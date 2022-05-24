<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

abstract class ReturnTypeFixer extends AbstractFixer
{
    public function upsertReturnType(Tokens $tokens, string $class, string $function, string $returnType) {
        // Update return type
        /** @var Token[] **/
        $matchedTokens = $tokens->findSequence([
            [T_FUNCTION],
            [T_STRING, $function],
            '(',
            ')'
        ]);
        var_dump($matchedTokens);
        if ($matchedTokens) {
            $useTokenIndexes = array_keys($matchedTokens);
            $tokens->insertAt(
                end($useTokenIndexes) + 1,
                [
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_STRING, ':']),
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_STRING, $returnType]),
                ],
            );
        }

    }

}
