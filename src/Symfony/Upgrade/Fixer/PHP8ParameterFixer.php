<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use UnexpectedValueException;

class PHP8ParameterFixer extends AbstractFixer
{
    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content): string
    {
        $tokens = Tokens::fromCode($content);
        $this->_removeShift_MailerFromIndexFunc($tokens);
        return $tokens->generateCode();
    }

    /**
     * メールマガジンプラグインで、オプションのパラメータよりも無効な必須パラメータが存在する場合の特殊な例です。
     * Special instance for mail magazine plugin where invalid required parameter exists over optional parameter
     * @param Tokens|Token[] $tokens
     * @return void
     */
    private function _removeShift_MailerFromIndexFunc(Tokens $tokens, int $index = 0) {
        $findSwiftMailerInstanceToken = $tokens->findSequence([
            [T_PUBLIC],
            [T_FUNCTION],
            [T_STRING, 'index'],
            '('
        ], $index);
        
        if ($findSwiftMailerInstanceToken === null) {
            return;
        }

        $useTokenIndexes = array_keys($findSwiftMailerInstanceToken);
        try {
            $blockEndToken = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, end($useTokenIndexes), true);
        } catch (UnexpectedValueException $exception) {
            // @todo : ここに不正な構文の警告を追加する。
            // @todo: Add Bad syntax warning here.
            $this->_removeShift_MailerFromIndexFunc($tokens, end($useTokenIndexes));
            return;
        }
        
        $targetID = -1;
        for($i = $useTokenIndexes[0]; $i < $blockEndToken; $i++) {
            if($tokens[$i]->getContent() === 'Swift_Mailer') {
                $targetID = $i;
                break;
            }
        }
        if ($targetID == -1) {
            $this->_removeShift_MailerFromIndexFunc($tokens, end($useTokenIndexes));
            return;
        }
        
        $xx = 0;
        while(true) {
            $xx++;
            if($tokens[$targetID - $xx]->getContent() === ',') {
                $tokens[$targetID - $xx]->setContent('');
                break;
            }
            $tokens[$targetID - $xx]->setContent('');
        }
        $xx = 0;
        while(true) {
            $xx++;
            if($tokens[$targetID + $xx]->getContent() === ')') {
                break;
            }
            $tokens[$targetID + $xx]->setContent('');
        }
        $tokens[$targetID]->setContent('');
        $this->_removeShift_MailerFromIndexFunc($tokens, $blockEndToken);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return "Fixes for php 8";
    }
}
