<?php


namespace Symfony\Upgrade\Util;


use Symfony\CS\Tokenizer\Tokens;

class ArrayTokenUtil
{

    /**
     * @param Tokens|$tokens
     * @param int $start
     * @param null $end
     * @return array
     */
    public static function getNextArrayTokenRange($tokens, $start = 0, $end = null)
    {
        $startTokenIndex = $tokens->getNextTokenOfKind($start, [[T_ARRAY], '[']);
        if (!$startTokenIndex) {
            return [];
        }

        $endTokenIndex = self::getArrayEndIndex($tokens, $startTokenIndex);
        return range($startTokenIndex, $endTokenIndex);
    }

    /**
     * arrayのキーを示すtokenの配列を返します。
     * @param Tokens|$tokens
     * @param int $start
     * @param null $end
     * @return array
     */
    public static function getArrayKeyTokens($tokens, $start = 0, $end = null)
    {

        if (($tokens[$start]->isGivenKind([T_ARRAY]) || $tokens[$start]->getContent() === '[') === false) {
            $start = $tokens->getNextTokenOfKind($start, [[T_ARRAY], '[']);
        }

        // `array`の場合
        if ($tokens[$start]->getId() == T_ARRAY) {
            return self::findArrayKeyTokens($tokens, $start + 2, $end);
        }
        // `[` でもなければ終了
        elseif ($tokens[$start]->getContent() === '[') {
            return self::findArrayKeyTokens($tokens, $start + 1, $end);
        }

        return [];
    }

    /**
     * @param Tokens|$tokens
     * @param $start
     * @param $end
     * @return array
     */
    private static function findArrayKeyTokens($tokens, $start, $end)
    {

        $originalEnd = $end;
        $nestedArrayStartIndex = $tokens->getNextTokenOfKind($start, [[T_ARRAY], '[']);
        if ($nestedArrayStartIndex) {
            $end = $nestedArrayStartIndex;
        }

        $keyTokens = null;
        $result = [];

        do {
            $keyTokens = $tokens->findSequence([
                [T_CONSTANT_ENCAPSED_STRING],
                [T_DOUBLE_ARROW]
            ], $start, $end);
            if ($keyTokens) {
                $keyIndexes = array_keys($keyTokens);
                $result[$keyIndexes[0]] = $keyTokens[$keyIndexes[0]];
                $start = end($keyIndexes) + 1;
            }
        } while ($keyTokens);

        if ($nestedArrayStartIndex) {
            $result += self::findArrayKeyTokens($tokens, self::getArrayEndIndex($tokens, $nestedArrayStartIndex), $originalEnd);
        }
        return $result;
    }

    /**
     * @param Tokens|$tokens
     * @param $arrayStartIndex
     * @return int
     */
    private static function getArrayEndIndex($tokens, $arrayStartIndex) {
        $arrayStartToken = $tokens[$arrayStartIndex];
        if ($arrayStartToken->getId() == T_ARRAY) {
            return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $arrayStartIndex + 1);
        }
        return $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, $arrayStartIndex);
    }
}