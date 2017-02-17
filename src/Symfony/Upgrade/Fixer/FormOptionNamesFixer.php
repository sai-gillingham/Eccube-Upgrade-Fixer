<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class FormOptionNamesFixer extends FormTypeFixer
{
    private static $FIELD_NAMES = [
        'precision' => 'scale',
        'virtual' => 'inherit_data',
        'property' => 'choice_label',
        'empty_value' => 'placeholder',
        'type' => 'entry_type',
        'empty_data' => null,
    ];

    /**
     * @param Tokens|$tokens
     */
    private function fixDefaultOptions($tokens)
    {
        if ($this->isFormType($tokens)) {
            $matchedTokens = $tokens->findSequence([
                [T_FUNCTION],
                [T_STRING, 'configureOptions'],
                '(',
                [T_STRING],
                [T_VARIABLE],
                ')',
                '{'
            ]) ?: $tokens->findSequence([
                [T_FUNCTION],
                [T_STRING, 'setDefaultOptions'],
                '(',
                [T_STRING],
                [T_VARIABLE],
                ')',
                '{'
            ]);
            if ($matchedTokens) {
                $matchedIndexes = array_keys($matchedTokens);
                $methodEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, end($matchedIndexes));

                $setDefaultStartTokens = $tokens->findSequence([
                    [T_STRING, 'setDefaults'],
                    '(',
                ], end($matchedIndexes), $methodEnd);

                if ($setDefaultStartTokens) {
                    $setDefaultStartTokenIndexes = array_keys($setDefaultStartTokens);
                    $setDefaultEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, end($setDefaultStartTokenIndexes));

                    $currentIndex = end($setDefaultStartTokenIndexes);
                    foreach (self::$FIELD_NAMES as $oldName => $newName) {
                        $oldFiledTokens = null;
                        do {
                            $oldFiledTokens = $tokens->findSequence([
                                [T_CONSTANT_ENCAPSED_STRING, '\''.$oldName.'\''],
                                [T_DOUBLE_ARROW]
                            ], $currentIndex, $setDefaultEndIndex);
                            if ($oldFiledTokens) {
                                $oldFiledTokenIndexes = array_keys($oldFiledTokens);
                                $this->replaceOldNameToNewName($oldName, $newName, $tokens, $tokens[$oldFiledTokenIndexes[0]], $oldFiledTokenIndexes[0]);
                                $currentIndex = end($oldFiledTokenIndexes) + 1;
                            }
                        } while ($oldFiledTokens);
                    }
                }
            }
        }

    }

    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $this->fixDefaultOptions($tokens);

        $fieldNameTokenSets = [
            [[T_CONSTANT_ENCAPSED_STRING]],
            [[T_STRING], [T_DOUBLE_COLON], [CT_CLASS_CONSTANT]],
        ];

        foreach ($fieldNameTokenSets as $fieldNameTokens) {
            foreach (self::$FIELD_NAMES as $oldName => $newName) {
                $this->fixOptionNames($tokens, $fieldNameTokens, $oldName, $newName);
            }
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Options precision and virtual was renamed to scale and inherit_data.';
    }

    private function fixOptionNames(Tokens $tokens, $fieldNameTokens, $oldName, $newName, $start = 0, $end = null)
    {
        $matchedTokens = $tokens->findSequence(array_merge(
            [
                [T_OBJECT_OPERATOR],
                [T_STRING, 'add'],
                '(',
                [T_CONSTANT_ENCAPSED_STRING],
                ',',
            ],
            $fieldNameTokens,
            [',']
        ), $start, $end);

        if (null === $matchedTokens) {
            return;
        }

        $matchedTokenIndexes = array_keys($matchedTokens);
        $isArray = $tokens->isArray(
            $index = $tokens->getNextMeaningfulToken(end($matchedTokenIndexes))
        );

        if (!$isArray) {
            return;
        }

        do {
            $index = $tokens->getNextMeaningfulToken($index);
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }

            if ("'$oldName'" === $token->getContent()) {
                $this->replaceOldNameToNewName($oldName, $newName, $tokens, $token, $index);
            }
        } while (!in_array($token->getContent(), [')', ']']));

        $this->fixOptionNames($tokens, $fieldNameTokens, $oldName, $newName, $index, $end);
    }

    private function replaceOldNameToNewName($oldName, $newName, $tokens, $token, $tokenIndex)
    {
        if (is_null($newName)) {
            $oldNameKeyValueToken = $tokens->findSequence([
                [T_CONSTANT_ENCAPSED_STRING, "'$oldName'"],
                [T_DOUBLE_ARROW],
                [T_STRING]
            ], $tokenIndex - 2);
            if ($oldNameKeyValueToken) {
                list($oldNameKeyIndex, , $oldNameValueIndex) = array_keys($oldNameKeyValueToken);
                $next = $tokens->getNextMeaningfulToken($oldNameValueIndex);
                if ($tokens[$next]->equals(',')) {
                    $oldNameValueIndex = $next;
                }
                $tokens->removeLeadingWhitespace($oldNameKeyIndex);
                $tokens->clearRange($oldNameKeyIndex, $oldNameValueIndex);
            }
        } else {
            $token->setContent("'$newName'");
        }
    }
}
