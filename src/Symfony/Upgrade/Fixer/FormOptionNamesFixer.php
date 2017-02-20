<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;
use Symfony\Upgrade\Util\ArrayTokenUtil;

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

                    $arrayKeyTokens = ArrayTokenUtil::getArrayKeyTokens($tokens, end($setDefaultStartTokenIndexes), $setDefaultEndIndex);
                    foreach($arrayKeyTokens as $index => $token) {
                        $filedName = preg_replace('/\'(.*)\'/', '\1', $token->getContent());
                        if (isset(self::$FIELD_NAMES[$filedName])) {
                            $this->replaceOldNameToNewName($filedName, self::$FIELD_NAMES[$filedName], $tokens, $token, $index);
                        }
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
            $this->fixOptionNames($tokens, $fieldNameTokens);
        }

        return $tokens->generateCode();
    }

    public function getDescription()
    {
        return 'Options precision and virtual was renamed to scale and inherit_data.';
    }

    private function fixOptionNames(Tokens $tokens, $fieldNameTokens, $start = 0, $end = null)
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

        $addBlockStartIndex = $matchedTokenIndexes[2];
        $addBlockEndIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $addBlockStartIndex);

        $currentIndex = $addBlockStartIndex;

        while ($currentIndex < $addBlockEndIndex) {
            $arrayRange = ArrayTokenUtil::getNextArrayTokenRange($tokens, $currentIndex, $addBlockEndIndex);

            if (!$arrayRange) {
                break;
            }

            $arrayKeyTokens = ArrayTokenUtil::getArrayKeyTokens($tokens, array_shift($arrayRange), end($arrayRange));

            foreach($arrayKeyTokens as $index => $token) {
                $filedName = preg_replace('/\'(.*)\'/', '\1', $token->getContent());
                if (key_exists($filedName, self::$FIELD_NAMES)) {
                    $this->replaceOldNameToNewName($filedName, self::$FIELD_NAMES[$filedName], $tokens, $token, $index);
                }
            }

            $currentIndex = end($arrayRange) + 1;
        }

        $this->fixOptionNames($tokens, $fieldNameTokens, $currentIndex);
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
