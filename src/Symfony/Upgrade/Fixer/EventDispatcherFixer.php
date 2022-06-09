<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use UnexpectedValueException;

class EventDispatcherFixer extends AbstractFixer
{
    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content): string
    {
        $tokens = Tokens::fromCode($content);

        $this->_swapDispatchParameters($tokens);

        return $tokens->generateCode();
    }

    private function _swapDispatchParameters(Tokens &$tokens, int $startingIndex = 0)
    {
        $foundTokens = $tokens->findSequence([
                [T_VARIABLE, '$this'],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'eventDispatcher'],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'dispatch'],
                '('
            ],
            $startingIndex
        );

        if ($foundTokens == null) {
            return;
        }

        $useTokenIndexes = array_keys($foundTokens);
        try {
            $blockEndToken = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, end($useTokenIndexes), true);
        } catch (UnexpectedValueException $exception) {
            // @todo : ここに不正な構文の警告を追加する。
            // @todo: Add Bad syntax warning here.

            return;
        }

        // @todo: 既に切り替わったパラメータを無視する
        // @todo: Ignore already switched parameters

        $oneParameters = [];
        $twoParameters = [];
        $isFirstParameter = true;

        for($y = end($useTokenIndexes) + 1; $y < $blockEndToken; $y++) {
            /** @var $token Token */
            $token = $tokens[$y];

            if ($token->getContent() == ',') {
                $isFirstParameter = false;
                $token->setContent('');
                continue;
            }
            if ($isFirstParameter) {
                $oneParameters[] = clone $token;
            } else {
                $twoParameters[] = clone $token;
            }
            $token->setContent('');
        }

        $tokens->insertAt(end($useTokenIndexes) + 1,
            [...$twoParameters, ...[new Token([T_STRING, ','])], ...$oneParameters, ...[new Token([T_STRING, ')'])]]
        );

        $this->_swapDispatchParameters($tokens, $blockEndToken);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return "Switch EC-CUBE Event dispatcher parameters";
    }
}
