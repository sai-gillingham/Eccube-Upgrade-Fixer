<?php


namespace Symfony\Upgrade\Fixer;


use Symfony\CS\Tokenizer\Tokens;

class ServiceProviderFixer extends AbstractFixer
{

    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        if ($this->isServiceProviderType($tokens)) {
            $this->fixShare($tokens);
            $this->fixExtend($tokens);
        }
        return $tokens->generateCode();
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

    private function isServiceProviderType($tokens)
    {
        $fqcn = ['Silex', 'ServiceProviderInterface'];
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }

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