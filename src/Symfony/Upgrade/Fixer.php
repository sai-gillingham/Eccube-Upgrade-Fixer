<?php

namespace Symfony\Upgrade;

use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Upgrade\Fixer\Iterator\FixerIterator;
use PhpCsFixer\Fixer\FixerInterface;

class Fixer
{
    const VERSION = '0.1.5-eccube-4.2.0-alpha2';

    private $fixers = [];
    private $finder;
    private $errorsManager;
    private $stopwatch;

    public function __construct(
        \Traversable $finder,
        ErrorsManager $errorsManager = null,
        Stopwatch $stopwatch = null
    ) {
        $this->finder = $finder;
        $this->errorsManager = $errorsManager;
        $this->stopwatch = $stopwatch;
    }

    public function registerBuiltInFixers()
    {
        foreach (new FixerIterator() as $fixer) {
            $this->addFixer($fixer);
        }
    }

    public function addFixer(FixerInterface $fixer)
    {
        $this->fixers[] = $fixer;
    }

    public function getFixers()
    {
        return $this->fixers;
    }

    public function fix($dryRun = false)
    {
        // ここがアップデートの本丸
        $changed = [];

        if ($this->stopwatch) {
            $this->stopwatch->openSection();
        }

        foreach ($this->finder as $file) {
            // 各ファイルの判定を実施している
            if ($file->isDir() || $file->isLink()) {
                // リンクやディレクトリは無視する
                continue;
            }
            if ($this->stopwatch) {
                $this->stopwatch->start($this->getFileRelativePathname($file));
            }

            if ($fixInfo = $this->fixFile($file, $dryRun)) {
                $changed[$this->getFileRelativePathname($file)] = $fixInfo;
            }else{
                var_dump("書き換え対象ではない");
            }

            if ($this->stopwatch) {
                $this->stopwatch->stop($this->getFileRelativePathname($file));
            }
        }

        if ($this->stopwatch) {
            $this->stopwatch->stopSection('fixFile');
        }

        return $changed;
    }

    private function fixFile(\SplFileInfo $file, $dryRun)
    {
        var_dump($file->getRealpath()."判定開始");
        $new = $old = file_get_contents($file->getRealpath());

        $appliedFixers = [];

        Tokens::clearCache();
        $tokens = Tokens::fromCode($new);

        try {
            foreach ($this->fixers as $fixer) {
                if (!$fixer->supports($file)) {
                    continue;
                }


                $newest = $fixer->applyFix($file, $tokens);
                var_dump("変更後がどうなるかのデータは取得");

                if ($newest !== $new) {
                    $appliedFixers[] = $fixer->getName();
                }
                $new = $newest;
            }
        } catch (\Exception $e) {
            var_dump("エラーが出た");
            if ($this->errorsManager) {
                $this->errorsManager->report(ErrorsManager::ERROR_TYPE_EXCEPTION, $this->getFileRelativePathname($file), $e->__toString());
            }
            return;
        }

        // 書き換え対象と判定された場合は @todo 各自でやらせる
        if ($new !== $old) {
            // ドライランでない限り

            if (!$dryRun) {
                // 書き換えを実行する
                file_put_contents($file->getRealpath(), $new);
            }
        }
        
        // 配列が返される
        return $appliedFixers;
    }

    private function getFileRelativePathname(\SplFileInfo $file)
    {
        if ($file instanceof FinderSplFileInfo) {
            return $file->getRelativePathname();
        }

        return $file->getPathname();
    }
}
