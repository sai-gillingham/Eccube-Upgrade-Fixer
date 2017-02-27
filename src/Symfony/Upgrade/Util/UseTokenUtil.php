<?php


namespace Symfony\Upgrade\Util;


use Symfony\CS\Tokenizer\Tokens;

class UseTokenUtil
{
    public static function getClassNameMap(Tokens $tokens)
    {
        $result = [];
        $currentIndex = 0;
        $arrayOfTokens = $tokens->toArray();

        while ($useStartTokenIndex = $tokens->getNextTokenOfKind($currentIndex, [[T_USE]])) {
            $useEndTokenIndex = $tokens->getNextTokenOfKind($useStartTokenIndex, [';']);
            $useTokens = array_slice($arrayOfTokens, $useStartTokenIndex + 1, $useEndTokenIndex - $useStartTokenIndex - 1);
            $className = null;
            $fqcn = [];
            $alias = false;
            foreach ($useTokens as $token) {
                switch ($token->getId()) {
                    case T_STRING:
                        if ($alias) {
                            $className = $token->getContent();
                        } else {
                            $fqcn[] = $token->getContent();
                        }
                        break;
                    case T_AS:
                        $alias = true;
                        break;
                    default:
                }
            }
            if ($alias === false) {
                $className = end($fqcn);
            }
            $result[$className] = $fqcn;
            $currentIndex = $useStartTokenIndex;
        }
        return $result;
    }

}