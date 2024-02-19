<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class RegistryFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'RegistryInterfaceクラスの取得先を変更します',
            [new CodeSample("use Symfony\Bridge\Doctrine\RegistryInterface")],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if ($this->isHasInterface($tokens)) {
            $this->fixUseClass($tokens);
        }
    }

    private function fixUseClass(Tokens $tokens)
    {
        // FindsequenceでRegistryInterfaceクラスを使っているかを判断
        $useTokens = $tokens->findSequence([
            [T_USE],
            [T_STRING, 'Symfony'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Bridge'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Doctrine'],
            [T_NS_SEPARATOR],
            [T_STRING, 'RegistryInterface'],
        ]);

        if ($useTokens) {
            // クラスの取得先を書きかえる
            $useTokenIndexes = array_keys($useTokens);
            $newContent1 = new Token([T_STRING, 'Doctrine']);
            $newContent2 = new Token([T_STRING, 'Persistence']);
            $newContent3 = new Token([T_STRING, 'ManagerRegistry']);
            $newContent4 = new Token([T_WHITESPACE, ' ']);
            $newContent5 = new Token([T_AS, 'as']);

            $tokens[$useTokenIndexes[1]] = $newContent1;
            $tokens[$useTokenIndexes[3]] = $newContent2;
            $tokens[$useTokenIndexes[5]] = $newContent3;
            $tokens[$useTokenIndexes[6]] = $newContent4;
            $tokens[$useTokenIndexes[7]] = $newContent5;

            $tokens->insertAt(
                $useTokenIndexes[7] + 1,
                array_merge(
                    [
                        new Token([T_WHITESPACE, ' ']),
                        new Token([T_STRING, 'RegistryInterface']),
                    ]
                )
            );

        }
    }

    
    private function isHasInterface($tokens)
    {
        // ネームスペースを区切って見つけたいクラスを発掘する
        $fqcn = ['Symfony','Bridge', 'Doctrine', 'RegistryInterface'];
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