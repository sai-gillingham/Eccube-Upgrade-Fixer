<?php


namespace Symfony\Upgrade\Fixer;


use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class AppRequestFixer extends AbstractFixer
{

    /**
     * Fixes a file.
     *
     * @param \SplFileInfo $file A \SplFileInfo instance
     * @param string $content The file content
     *
     * @return string The fixed file content
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $matchedTokens = null;
        $currentIndex = 0;

        do {
            $matchedTokens = $tokens->findSequence([
                [T_VARIABLE, '$app'],
                '[',
                [T_CONSTANT_ENCAPSED_STRING, '\'request\''],
                ']'
            ], $currentIndex);

            if ($matchedTokens) {
                $matchedIndexes = array_keys($matchedTokens);
                $tokens[$matchedIndexes[2]]->setContent('\'request_stack\'');
                $tokens->insertAt(end($matchedIndexes) + 1, [
                    new Token([T_OBJECT_OPERATOR, '->']),
                    new Token([T_STRING, 'getCurrentRequest']),
                    new Token('('),
                    new Token(')'),
                ]);
                $currentIndex = end($matchedIndexes) + 1;
            }
        } while ($matchedTokens);

        return $tokens->generateCode();
    }

    /**
     * Returns the description of the fixer.
     *
     * A short one-line description of what the fixer does.
     *
     * @return string The description of the fixer
     */
    public function getDescription()
    {
        return 'Fix $app["request"] -> $app["request"]->getCurrentRequest().';
    }
}