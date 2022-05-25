<?php

namespace Symfony\Upgrade\Fixer;

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
            ->setContent($new)
        ;

        $this->renameNewStatements($tokens, $old, $new);
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
            ->setContent($new)
        ;

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
            ->setContent($new)
        ;

        $this->renameConstants($tokens, $className, $old, $new);
    }

    protected function renameChainMethods(Tokens &$tokens, $old, $new)
    {
        // Change Swift_Message class
        $subjectFunctionUpdate = $tokens->findSequence([
            [T_OBJECT_OPERATOR],
            [T_STRING, $old],
        ]);

        if ($subjectFunctionUpdate === null) {
            return;
        }

        if ($subjectFunctionUpdate) {
            $useTokenIndexes = array_keys($subjectFunctionUpdate);
            $tokens[end($useTokenIndexes)]->setContent($new);
        }
        $this->renameChainMethods($tokens, $old, $new);
    }
}
