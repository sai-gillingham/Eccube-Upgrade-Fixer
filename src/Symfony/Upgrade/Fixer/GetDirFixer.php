<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class GetDirFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'containerクラスのgetParameterメソッドを変更しEccubeConfigを経由して取得します',
            [new CodeSample('$templateDir = $container->getParameter("eccube_theme_front_dir")')],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // ファイル名を確認、**Controller.phpの場合は処理しない
        // controllerにおいてコンテナからgetParameter()は実行することが無い前提です
        $filename = $file->getFilename();
        if(str_contains($filename, 'Controller.php')){
            return;
        }

        $flag = false;
        while ($this->isHasContainerInterface($tokens)) {
            $this->fixServiceInterface($tokens);
            $flag = true;
        }

        if($flag){
            file_put_contents($file, $tokens->generateCode());
        }
    }

    private function fixServiceInterface(Tokens $tokens)
    {
        //getParameterをgetに　変更
        //引数としてEccubeConfigクラスを設定　追加
        //矢印を追加　追加
        //getメソッドを追加　追加
        $useTokens = $tokens->findSequence([
            [T_VARIABLE],
            [T_OBJECT_OPERATOR],
            [T_STRING, 'getParameter']
        ]);

        
        if ($useTokens) {
            $useTokenIndexes = array_keys($useTokens);

            // 3個目をContainerに変更
            $changeContent1 = new Token([T_STRING, 'get']);
            
            $tokens[$useTokenIndexes[2]] = $changeContent1;

            // 追加トークンを列挙
            $tokens->insertAt(
                $useTokenIndexes[2] + 1,
                array_merge(
                    [
                        new Token([T_STRING, '(']),
                        new Token([T_CLASS, '\Eccube\Common\EccubeConfig::class']),
                        new Token([T_STRING, ')']),
                        new Token([T_OBJECT_OPERATOR, '->']),
                        new Token([T_STRING, 'get']),
                    ]
                )
            );
        }
    }

    private function isHasContainerInterface($tokens)
    {
        return null !== $tokens->findSequence([
                [T_VARIABLE],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'getParameter']
            ]);
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}