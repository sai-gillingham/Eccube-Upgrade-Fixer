<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class SessionFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Converts simple usages of `array_push($x, $y);` to `$x[] = $y;`.',
            [new CodeSample("<?php\narray_push(\$x, \$y);\n")],
            null,
            'Risky when the function `array_push` is overridden.'
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if ($this->isHasContainerInterface($tokens)) {
            var_dump("見つけた");
            $this->fixUseClass($tokens);
            $this->fixArgument($tokens);
            $this->fixSetting($tokens);

            file_put_contents($file, $tokens->generateCode());
            //var_dump($tokens->generateCode());
        }
        
        //return $tokens->generateCode();
    }

    private function fixUseClass(Tokens $tokens)
    {
        // セッションクラスを書き換える Symfony\Component\HttpFoundation\RequestStack;

        // セッションinterface引数を見つけてRequestStackに変更する

        //　$this->sessionを見つけて$requestStack->getSession();と定義する

        // FindsequenceでEncoderFactoryInterfaceクラスを使っているかを判断
        $useTokens = $tokens->findSequence([
            [T_USE],
            [T_STRING, 'Symfony'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Component'],
            [T_NS_SEPARATOR],
            [T_STRING, 'HttpFoundation'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Session'],
            [T_NS_SEPARATOR],
            [T_STRING, 'SessionInterface'],
        ]);

        if ($useTokens) {
            // RequestStackがある場合はクラス書き換えは行わない
            $targetTokens = $tokens->findSequence([
                [T_USE],
                [T_STRING, 'Symfony'],
                [T_NS_SEPARATOR],
                [T_STRING, 'Component'],
                [T_NS_SEPARATOR],
                [T_STRING, 'HttpFoundation'],
                [T_NS_SEPARATOR],
                [T_STRING, 'RequestStack'],
            ]);
            if($targetTokens){
                return;
            }

            // RequestStackが無い場合は書き換える
            $useTokenIndexes = array_keys($useTokens);
            $newContent1 = new Token([T_STRING, 'RequestStack']);

            $tokens[$useTokenIndexes[7]] = $newContent1;

            $tokens->clearTokenAndMergeSurroundingWhitespace($useTokenIndexes[8]);
            $tokens->clearTokenAndMergeSurroundingWhitespace($useTokenIndexes[9]);

        }
    }

    private function fixArgument(Tokens $tokens)
    {
        $targetTokens = $tokens->findSequence([
            [T_USE],
            [T_STRING, 'Symfony'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Component'],
            [T_NS_SEPARATOR],
            [T_STRING, 'HttpFoundation'],
            [T_NS_SEPARATOR],
            [T_STRING, 'RequestStack'],
        ]);
        if($targetTokens){
            return;
        }

        $useTokens = $tokens->findSequence([
            [T_STRING, 'SessionInterface'],
            [T_VARIABLE, '$session']
        ]);

        if ($useTokens) {
            $useTokenIndexes = array_keys($useTokens);
            $newContent1 = new Token([T_STRING, 'RequestStack']);
            $newContent2 = new Token([T_VARIABLE, '$requestStack']);

            $tokens[$useTokenIndexes[0]] = $newContent1;
            $tokens[$useTokenIndexes[1]] = $newContent2;
        }
    }

    private function fixSetting(Tokens $tokens)
    {
        var_dump("定義を修正");
        $useTokens = $tokens->findSequence([
            [T_VARIABLE, '$this'],
            [T_OBJECT_OPERATOR],
            [T_STRING, 'session']
        ]);

        if ($useTokens) {
            $useTokenIndexes = array_keys($useTokens);

            $tokens[$useTokenIndexes[1]];

            for($i = 1; $i <= 20; $i ++){
                if($tokens[$useTokenIndexes[2] + $i]->getContent() == '$session'){
                    $newContent1 = new Token([T_VARIABLE, '$requestStack']);
                    $tokens[$useTokenIndexes[2] + $i] = $newContent1;

                    $tokens->insertAt(
                        $useTokenIndexes[2] + $i + 1,
                        array_merge(
                            [
                                new Token([T_OBJECT_OPERATOR, '->']),
                                new Token([T_STRING, 'getSession()']),
                            ]
                        )
                    );

                    break;
                }
            }

            
        }
    }

    private function isHasContainerInterface($tokens)
    {
        // ネームスペースを区切って見つけたいクラスを発掘する
        var_dump("セッションinterfaceを使用しているかを判定");
        $fqcn = ['Symfony','Component', 'HttpFoundation', 'Session','SessionInterface'];
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }else{
            return true;
        }

        var_dump("見つけた!");
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}