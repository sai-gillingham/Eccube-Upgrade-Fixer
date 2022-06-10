<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class RemoveFormatFromDateFormFixer extends \Symfony\CS\AbstractFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        // @todo: Fix if check parameter
//        $this->_fixStrictIfChecks($tokens);
        $this->_removeFormatFromDateTypeFormInput($tokens);
        return $tokens->generateCode();
    }

    /**
     * @param Tokens|Token[] $tokens
     * @param int $index
     * @return void
     */
    private function _removeFormatFromDateTypeFormInput(Tokens $tokens, int $index = 0) {
        $formInterfaceObject = $tokens->findSequence(
            [
                [T_OBJECT_OPERATOR],
                [T_STRING, 'add'],
                '(',
            ],
            $index
        );

        if ($formInterfaceObject == null) {
            return;
        }

        $formInterfaceTokens = array_keys($formInterfaceObject);
        $startParameterId = end($formInterfaceTokens);

        try {
            $endParameterId = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParameterId);
        } catch (\UnexpectedValueException $e) {
            $this->_removeFormatFromDateTypeFormInput($tokens, $startParameterId);
            return;
        }

        if ($this->_hasDateType($tokens, $startParameterId, $endParameterId) === true) {
            $this->_findAndReplaceFormatParameter($tokens, $startParameterId, $endParameterId);
        }
        $this->_removeFormatFromDateTypeFormInput($tokens, $endParameterId);
    }

    private function _hasDateType(Tokens $tokens, $startParameterId, $endParameterId) : bool {
        // Check if DateType Class Only
        for($i = $startParameterId; $i < $endParameterId; $i++ ) {
            if ($tokens[$i]->getContent() == 'DateType') {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Tokens|Token[] $tokens
     * @param int $startParameterId
     * @param int $endParameterId
     * @return void
     */
    private function _findAndReplaceFormatParameter(Tokens $tokens, int $startParameterId, int $endParameterId) {
        // DateTypeクラスのみかどうかをチェックする
        // Check if DateType Class Only
        $foundEndPoint = false;
        for($i = $startParameterId; $i < $endParameterId; $i++ ) {
            if ($tokens[$i]->getContent() == "'format'") {
                $y = 0;
                while($foundEndPoint === false) {
                    if($tokens[$i + $y]->getContent() == ",") {
                        $foundEndPoint = true;
                    }
                    $tokens[$i + $y]->setContent('');
                    $y++;
                }
                $x = 0;
                // 既存の空白を削除する
                // Remove Any Existing whitespace
                while(true) {
                    $x++;
                    if($tokens[$i - $x]->isWhitespace() === false) {
                        break;
                    } 
                    $tokens[$i - $x]->setContent('');
                }
            }
        }
    }



    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return "Remove date parameter from DateType::class";
    }
}
