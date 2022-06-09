<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class EmailValidatorParameterUpdateFixer extends AbstractFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $this->_fixEmailValidationParameter($tokens);
        return $tokens->generateCode();
    }

    private function _fixEmailValidationParameter(Tokens $tokens, int $indexFrom = 0)
    {
        $emailClassToken = $tokens->findSequence(
            [
                [T_NEW, 'new'],
                [T_STRING, 'Email'],
                '(',
                '['
            ],
            $indexFrom
        );

        if ($emailClassToken == null) {
            return;
        }

        $useTokenIndexes = array_keys($emailClassToken);
        $endArray = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, end($useTokenIndexes));

        for($y = end($useTokenIndexes); $y < $endArray + 1; $y++) {
            /** @var $tokens Tokens|Token[] */
            $tokens[$y]->setContent('');
        }

        $tokens->insertAt(end($useTokenIndexes),
            [
                new Token([T_STRING, 'null']),
                new Token([T_STRING, ', ']),
                new Token([T_STRING, 'null']),
                new Token([T_STRING, ', ']),
                new Token([T_STRING, '$this']),
                new Token([T_OBJECT_OPERATOR, '->']),
                new Token([T_STRING, 'eccubeConfig[\'eccube_rfc_email_check\']']),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_STRING, '?']),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_STRING, '\'strict\'']),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_STRING, ':']),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_STRING, 'null'])
            ]);
        $this->_fixEmailValidationParameter($tokens, $endArray);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return "Email validation parameter updated";
    }
}
