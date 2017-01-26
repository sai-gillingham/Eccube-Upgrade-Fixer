<?php


namespace Symfony\Upgrade\Fixer;


use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class FormChoiceTypeArrayFixer extends FormTypeFixer
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

        if ($this->isFormType($tokens)) {
            $currentIndex = 0;

            do {
                $matchedTokens = $tokens->findSequence([
                    [T_CONSTANT_ENCAPSED_STRING, '\'choices\''],
                    [T_DOUBLE_ARROW],
                ], $currentIndex);
                if ($matchedTokens) {
                    $matchedIndexes = array_keys($matchedTokens);
                    $matchedIndex = $matchedIndexes[count($matchedIndexes)-1];
                    $startIndex = $tokens->getNextMeaningfulToken($matchedIndex);
                    /** @var Token $t */
                    $t = $tokens[$startIndex];
                    switch ($t->getId()) {
                        case T_ARRAY:
                            $endIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex + 1);
                            $currentIndex = $this->addArrayFlip($tokens, $startIndex, $endIndex);
                            break;
                        case T_VARIABLE:
                            $currentIndex = $this->addArrayFlip($tokens, $startIndex, $startIndex + 1);
                            break;
                        default:
                            $currentIndex = $startIndex+1;
                            break;
                    }
                }
            } while ($matchedTokens);
        }

        return $tokens->generateCode();
    }

    private function addArrayFlip($tokens, $startIndex, $endIndex) {
        $tokens->insertAt($endIndex, new Token(')'));
        $tokens->insertAt($startIndex, [
            new Token([T_STRING, 'array_flip']),
            new Token('(')
        ]);
        return $endIndex + 3;
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
        return "`'choices' => array(...)` -> `'choices' => array_flip(array(...))`";
    }
}