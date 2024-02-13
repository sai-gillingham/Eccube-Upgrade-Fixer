<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class EncoderFactoryInterfaceFixer extends AbstractFixer
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
            $this->fixServiceInterface($tokens);

            file_put_contents($file, $tokens->generateCode());
        }
        
        //return $tokens->generateCode();
    }

    private function fixServiceInterface(Tokens $tokens)
    {
        var_dump("処理開始");
        // FindsequenceでEncoderFactoryInterfaceクラスを使っているかを判断
        $useTokens = $tokens->findSequence([
            [T_USE],
            [T_STRING, 'Symfony'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Component'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Security'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Core'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Encoder'],
            [T_NS_SEPARATOR],
            [T_STRING, 'EncoderFactoryInterface']
        ]);

        if ($useTokens) {
            var_dump("チェックポイント1");
            $useTokenIndexes = array_keys($useTokens);

            //use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
            //use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as EncoderFactoryInterface
            var_dump("チェックポイント2");
            $newContent1 = new Token([T_STRING, 'PasswordHacher']);
            var_dump("チェックポイント3");
            $newContent2 = new Token([T_STRING, 'Hacher']);
            var_dump("チェックポイント4");
            $newContent3 = new Token([T_STRING, 'UserPasswordHasherInterface']);
            var_dump("チェックポイント5");
            $newContent4 = new Token([T_WHITESPACE, ' ']);
            var_dump("チェックポイント6");
            $newContent5 = new Token([T_AS, 'as']);

            $tokens[$useTokenIndexes[5]] = $newContent1;
            $tokens[$useTokenIndexes[7]] = $newContent2;
            $tokens[$useTokenIndexes[9]] = $newContent3;
            $tokens[$useTokenIndexes[10]] = $newContent4;
            $tokens[$useTokenIndexes[11]] = $newContent5;

            $tokens->insertAt(
                $useTokenIndexes[11] + 1,
                array_merge(
                    [
                        new Token([T_WHITESPACE, ' ']),
                        new Token([T_STRING, 'EncoderFactoryInterface']),
                    ]
                )
            );

        }
    }

    private function isHasContainerInterface($tokens)
    {
        // ネームスペースを区切って見つけたいクラスを発掘する
        var_dump("エンコーダーを見つける");
        $fqcn = ['Symfony','Component', 'Security', 'Core','Encoder','EncoderFactoryInterface'];
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }else{
            return true;
        }

        var_dump("見つけた!");

        return null !== $tokens->findSequence([
                [T_USE],
                [T_STRING, 'Symfony'],
                [T_NS_SEPARATOR],
                [T_STRING, 'Component'],
                [T_NS_SEPARATOR],
                [T_STRING, 'Security'],
                [T_NS_SEPARATOR],
                [T_STRING, 'Core']
                [T_NS_SEPARATOR],
                [T_STRING, 'Encoder'],
                [T_NS_SEPARATOR],
                [T_STRING, 'EncoderFactoryInterface']
            ]);
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}