<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class EmailValidatorFixer extends RenameFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content): string
    {
        $tokens = Tokens::fromCode($content);
        // @todo: Fix if check parameter
        $this->_fixStrictIfChecks($tokens);
        $this->_fixBaseEmailValidatorParameters($tokens);
        return $tokens->generateCode();
    }

    private function _fixStrictIfChecks(Tokens $tokens, int $index = 0) {
        $strictIfTokens = $tokens->findSequence(
            [
                [T_IF],
                '(',
                [T_VARIABLE, '$constraint'],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'strict'],
            ],
            $index
        );
        if($strictIfTokens == null) {
            return;
        }
        $useTokenIndexes = array_keys($strictIfTokens);
        /** @var $tokens Tokens|Token[] */
        $tokens[end($useTokenIndexes)]->setContent('');
        $tokens->insertAt(
            end($useTokenIndexes),
            [
                new Token([T_STRING, 'mode']),
                new Token([T_IS_IDENTICAL, '===']),
                new Token([T_STRING, 'Email']),
                new Token([T_DOUBLE_COLON, '::']),
                new Token([T_STRING, 'VALIDATION_MODE_STRICT'])
            ]
        );
        $this->_fixStrictIfChecks($tokens, end($useTokenIndexes) + 1);
    }

    private function _fixBaseEmailValidatorParameters(Tokens $tokens, int $index = 0)
    {
        $parameterStartTokens = $tokens->findSequence(
            [
                [T_NEW],
                [T_STRING, 'BaseEmailValidator'],
                '(',
            ],
            $index
        );
        if ($parameterStartTokens == null) {
            return;
        }

        $useTokenIndexes = array_keys($parameterStartTokens);
        $endToken = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, end($useTokenIndexes));

        for ($y = end($useTokenIndexes); $y < $endToken; $y++) {
            $tokens[$y]->setContent('');
        }

        $tokens->insertAt(end($useTokenIndexes),
            [
                new Token([T_STRING, '(']),
                new Token([T_STRING, 'Email']),
                new Token([T_DOUBLE_COLON, '::']),
                new Token([T_STRING, 'VALIDATION_MODE_STRICT'])
            ]);

        $this->_fixBaseEmailValidatorParameters($tokens, $endToken);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return "Fix up strict validation changes to email. From an instance reference to CONSTANT reference";
    }
}
