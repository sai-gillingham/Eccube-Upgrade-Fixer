<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

abstract class RenameFixer extends AbstractFixer
{
    protected function renameNewStatements(Tokens $tokens, $old, $new)
    {
        $matchedTokens = $tokens->findSequence([
            [T_NEW],
            [T_STRING, $old],
        ]);

        if (null === $matchedTokens) {
            return;
        }

        $matchedIndexes = array_keys($matchedTokens);

        $matchedTokens[$matchedIndexes[count($matchedIndexes) - 1]]
            ->setContent($new);

        $this->renameNewStatements($tokens, $old, $new);
    }

    protected function renameFunctionAccessType(Tokens $tokens, string $functionName, int $fromAccessType, int $toAccessType, int $index = 0) {
        $matchedTokens = $tokens->findSequence([
            [$fromAccessType],
            [T_FUNCTION],
            [T_STRING, $functionName]
        ], $index);

        var_dump("testpoint");
        if ($matchedTokens == null) {
            return;
        }


        $matchedIndexes = array_keys($matchedTokens);

        $matchedTokens[$matchedIndexes[0]]
            ->setContent('');

        $accessTranslateArray = [
            T_PUBLIC => 'public',
            T_PROTECTED => 'protected',
            T_PRIVATE => 'private'
        ];

        $tokens->insertAt($matchedIndexes[0], [
            new Token([$toAccessType, $accessTranslateArray[$toAccessType]])
        ]);
        $this->renameFunctionAccessType($tokens, $functionName, $fromAccessType, $toAccessType, end($matchedIndexes));
    }

    protected function renameMethodCalls(Tokens $tokens, $className, $old, $new)
    {
        $matchedTokens = $tokens->findSequence([
            [T_STRING, $className],
            [T_DOUBLE_COLON],
            [T_STRING, $old],
            '(',
            ')',
        ]);

        if (null === $matchedTokens) {
            return;
        }

        $matchedTokensIndexes = array_keys($matchedTokens);

        $matchedTokens[$matchedTokensIndexes[count($matchedTokensIndexes) - 3]]
            ->setContent($new);

        $this->renameMethodCalls($tokens, $className, $old, $new);
    }

    protected function renameConstants(Tokens $tokens, $className, $old, $new)
    {
        $matchedTokens = $tokens->findSequence([
            [T_STRING, $className],
            [T_DOUBLE_COLON],
            [T_STRING, $old],
        ]);

        if (null === $matchedTokens) {
            return;
        }

        $matchedTokensIndexes = array_keys($matchedTokens);

        $matchedTokens[$matchedTokensIndexes[count($matchedTokensIndexes) - 1]]
            ->setContent($new);

        $this->renameConstants($tokens, $className, $old, $new);
    }

    protected function renameChainMethods(Tokens &$tokens, $old, $new, $index = 0)
    {
        // Change Swift_Message class
        $subjectFunctionUpdate = $tokens->findSequence([
            [T_OBJECT_OPERATOR],
            [T_STRING, $old],
        ], $index);

        if ($subjectFunctionUpdate === null) {
            return;
        }

        if ($subjectFunctionUpdate) {
            $useTokenIndexes = array_keys($subjectFunctionUpdate);
            $tokens[end($useTokenIndexes)]->setContent($new);
        }
        $this->renameChainMethods($tokens, $old, $new, end($useTokenIndexes));
    }

    /**
     * @param Tokens|Token[] $tokens
     * @param string $keyName
     * @param string $associatedKey // TODO: Use Enclosed match type search to prevent mismatch code
     * @param int $oldValueTokenType
     * @param $oldValue
     * @param $newValue
     * @param int $index
     * @return void
     */
    protected function renameArrayValue(Tokens $tokens, string $keyName, string $associatedKey, int $oldValueTokenType, $oldValue, $newValue, int $index = 0) {
        $arrayValueKeyTokens = $tokens->findSequence([
            [T_CONSTANT_ENCAPSED_STRING, $keyName],
            [T_DOUBLE_ARROW, '=>'],
            [T_STRING, $oldValue]
        ], $index);

        if ($arrayValueKeyTokens == null) {
            return;
        }

        $arrayValueKeyTokenKeys = array_keys($arrayValueKeyTokens);
        $tokens[end($arrayValueKeyTokenKeys)]->setContent($newValue);
        $this->renameArrayValue($tokens, $keyName, $associatedKey, $oldValueTokenType, $oldValue, $newValue, end($arrayValueKeyTokenKeys));
    }


    protected function renameFunctionParameterTypes(Tokens &$tokens, string $old, string $new, int $index = 0)
    {
        $classParameterTypeTokens = $tokens->findSequence([
            '(',
            [T_STRING, $old]
        ], $index);

        if ($classParameterTypeTokens == null) {
            return;
        }

        $useTokenIndexes = array_keys($classParameterTypeTokens);

        /** @var $tokens Token[]|Tokens */
        $tokens[end($useTokenIndexes)]->setContent($new);

        $this->renameFunctionParameterTypes($tokens, $old, $new, end($useTokenIndexes));
    }
}
