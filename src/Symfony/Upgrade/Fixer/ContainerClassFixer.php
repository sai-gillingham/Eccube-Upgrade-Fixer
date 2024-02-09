<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class ContainerClassFixer extends AbstractFixer
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
            $this->fixServiceInterface($tokens);

            //var_dump($tokens->generateCode());
            file_put_contents($file, $tokens->generateCode());
        }
        
        //return $tokens->generateCode();
    }

    private function fixServiceInterface(Tokens $tokens)
    {

        /*
         *['Symfony','Component', 'DependencyInjection', 'ContainerInterface'];
         *Symfony\Component\DependencyInjection\ContainerInterface; Psr\Container\ContainerInterface;
         * Silex\ServiceProviderInterface -> Pimple\ServiceProviderInterface に変更
         */
        $useTokens = $tokens->findSequence([
            [T_USE],
            [T_STRING, 'Symfony'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Component'],
            [T_NS_SEPARATOR],
            [T_STRING, 'DependencyInjection'],
            [T_NS_SEPARATOR],
            [T_STRING, 'ContainerInterface']
        ]);

        
        if ($useTokens) {
            // $useTokensの一個目の名前空間の削除
            $useTokenIndexes = array_keys($useTokens);

            // スライドしてきた一個目の名前空間をPsrに変更
            // 二個目をContainerに変
            $newContent1 = new Token([T_STRING, 'Psr']);
            $newContent2 = new Token([T_STRING, 'Container']);

            $tokens->clearTokenAndMergeSurroundingWhitespace($useTokenIndexes[1]);
            $tokens->clearTokenAndMergeSurroundingWhitespace($useTokenIndexes[2]);
            $tokens[$useTokenIndexes[3]] = $newContent1;
            $tokens[$useTokenIndexes[5]] = $newContent2;

        }
    }

    /**
     * @param Tokens|$tokens
     */
    private function fixShare($tokens)
    {
        $currentIndex = 0;
        $matchedTokens = null;
        do {
            $matchedTokens = $tokens->findSequence([
                [T_VARIABLE],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'share'],
                '('
            ], $currentIndex);
            if ($matchedTokens) {
                $matchedIndexes = array_keys($matchedTokens);
                $blockEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, end($matchedIndexes));
                $tokens->clearRange($matchedIndexes[0], end($matchedIndexes));
                $tokens[$blockEnd]->clear();
                $currentIndex = $blockEnd + 1;
            }
        } while ($matchedTokens);
    }

    /**
     * @param Tokens|$tokens
     */
    private function fixExtend($tokens)
    {
        $currentIndex = 0;
        $matchedTokens = null;
        do {
            $matchedTokens = $tokens->findSequence([
                [T_VARIABLE],
                '[',
                [T_CONSTANT_ENCAPSED_STRING],
                ']',
                '=',
                [T_VARIABLE],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'extend']
            ], $currentIndex);
            if ($matchedTokens) {
                $matchedIndexes = array_keys($matchedTokens);
                $assignmentTokenIndex = $matchedIndexes[4];
                $tokens->clearRange($matchedIndexes[0], $assignmentTokenIndex);
                $tokens->removeTrailingWhitespace($assignmentTokenIndex);
                $currentIndex = end($matchedIndexes) + 1;
            }
        } while ($matchedTokens);
    }

    private function isHasContainerInterface($tokens)
    {
        // ネームスペースを区切って見つけたいクラスを発掘する
        $fqcn = ['Symfony','Component', 'DependencyInjection', 'ContainerInterface'];
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }else{
            return true;
        }

        var_dump("見つけた!");

        return null !== $tokens->findSequence([
                [T_CLASS],
                [T_STRING],
                [T_IMPLEMENTS],
                [T_STRING, array_pop($fqcn)],
            ]);
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}