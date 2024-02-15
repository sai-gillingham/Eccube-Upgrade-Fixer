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
            'EncoderFactoryInterfaceクラスの取得先を変更します',
            [new CodeSample("use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface")],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if ($this->isHasContainerInterface($tokens)) {
            $this->fixServiceInterface($tokens);

            file_put_contents($file, $tokens->generateCode());
        }
        
        //return $tokens->generateCode();
    }

    private function fixServiceInterface(Tokens $tokens)
    {
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
            $useTokenIndexes = array_keys($useTokens);

            $newContent1 = new Token([T_STRING, 'PasswordHasher']);
            $newContent2 = new Token([T_STRING, 'Hasher']);
            $newContent3 = new Token([T_STRING, 'UserPasswordHasherInterface']);
            $newContent4 = new Token([T_WHITESPACE, ' ']);
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
        $fqcn = ['Symfony','Component', 'Security', 'Core','Encoder','EncoderFactoryInterface'];
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }else{
            return true;
        }
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}