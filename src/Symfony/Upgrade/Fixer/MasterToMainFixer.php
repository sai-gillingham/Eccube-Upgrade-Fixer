<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class MasterToMainFixer extends AbstractFixer
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
        while ($this->isGetMainRequest($tokens)) {
            $this->fixRename($tokens);
        }
        //return $tokens->generateCode();
        file_put_contents($file, $tokens->generateCode());
    }

    private function fixRename(Tokens $tokens)
    {

        /*
         * Silex\ServiceProviderInterface -> Pimple\ServiceProviderInterface に変更
         */
        $useTokens = $tokens->findSequence([
            [T_STRING, 'getMasterRequest'],
        ]);
        
        if ($useTokens) {
            $useTokenIndexes = array_keys($useTokens);
            $tokens[$useTokenIndexes[0]] = new Token([T_STRING, 'getMainRequest']);
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

    private function isGetMainRequest($tokens)
    {
        return $tokens->findSequence([
            [T_STRING, 'getMasterRequest']
        ]);

    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}