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
            $addStartTokens = null;
            do {
                $addStartTokens = $tokens->findSequence([
                    [T_OBJECT_OPERATOR],
                    [T_STRING, 'add'],
                    '(',
                    [T_CONSTANT_ENCAPSED_STRING],
                    ',',
                    [T_CONSTANT_ENCAPSED_STRING]
                ], $currentIndex) ?: $tokens->findSequence([
                    [T_OBJECT_OPERATOR],
                    [T_STRING, 'add'],
                    '(',
                    [T_CONSTANT_ENCAPSED_STRING],
                    ',',
                    [T_STRING],
                    [T_DOUBLE_COLON],
                    [CT_CLASS_CONSTANT]
                ], $currentIndex);
                if ($addStartTokens) {
                    $addStartTokenIndexes = array_keys($addStartTokens);
                    $addEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $addStartTokenIndexes[2]);
                    $currentIndex = end($addStartTokenIndexes);

                    $typeToken = $addStartTokens[$addStartTokenIndexes[5]];

                    if ($typeToken->getContent() === "'entity'" || ($typeToken->getId() === T_STRING && $typeToken->getContent() === 'EntityType') ) {
                        continue;
                    }

                    $matchedTokens = $tokens->findSequence([
                        [T_CONSTANT_ENCAPSED_STRING, '\'choices\''],
                        [T_DOUBLE_ARROW],
                    ], $currentIndex, $addEndIndex);
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
                }
            } while ($addStartTokens);
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
        return "Flip choices in ChoiceType.";
    }
}