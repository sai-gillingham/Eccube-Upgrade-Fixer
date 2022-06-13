<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

abstract class ReturnTypeFixer extends RenameFixer
{
    public function upsertReturnType(Tokens $tokens, string $class, string $function, string $returnType, int $indexFrom = 0)
    {
        // Update return type
        /** @var Token[] * */
        $matchedTokens = $tokens->findSequence([
            [T_FUNCTION],
            [T_STRING, $function],
            '(',
            ')'
        ],
            $indexFrom
        );

        if ($matchedTokens == null) {
            return;
        }
        
        $useTokenIndexes = array_keys($matchedTokens);
        

        $isExistsAlready = false;

        for($tokenAttempts = 1; $tokenAttempts < 15; $tokenAttempts++) {
            /** @var $tokens Token[]|Tokens */
            if ($tokens[end($useTokenIndexes) + $tokenAttempts]->getContent() == ':') {
                $isExistsAlready = true;
            } elseif ($tokens[end($useTokenIndexes) + $tokenAttempts]->getContent() == '{') {
                break;
            }
        }

        if ($isExistsAlready === false) {
            $tokens->insertAt(
                end($useTokenIndexes) + 1,
                [
                    new Token([T_WHITESPACE, ' ']),
                    new Token(':'),
                    new Token([T_WHITESPACE, ' ']),
                    new Token([T_STRING, $returnType]),
                ],
            );
        }
        $this->upsertReturnType($tokens, $class, $function, $returnType, end($useTokenIndexes));
    }
}
