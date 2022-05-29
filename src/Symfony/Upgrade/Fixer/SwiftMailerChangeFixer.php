<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use UnexpectedValueException;

class SwiftMailerChangeFixer extends AbstractFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        if ($file->getFilename() == 'MailMagazineService.php') {
            // Useステートメントの追加
            // Add Use Statements
            $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mailer', 'MailerInterface']);
            $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mime', 'Address']);
            $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mime', 'Email']);

            // Swift_MessageクラスをEmailクラスに変更する
            // Change Swift_Message class to Email Class
            $swiftMessageClass = $tokens->findSequence([
                [T_NEW],
                [T_NS_SEPARATOR],
                [T_STRING, 'Swift_Message'],
                '(',
                ')'
            ]);
            if ($swiftMessageClass) {
                $useTokenIndexes = array_keys($swiftMessageClass);
                $tokens[end($useTokenIndexes) - 3]->setContent('');
                $tokens[end($useTokenIndexes) - 2]->setContent('Email');
            }
            // 配列のfrom型をAddressクラスのインスタンスに置き換える
            // Replace "from" array type with Address class instance
            $this->_replaceArrayWithAddressObject($tokens);
            // Swiftのメーラー関数をSymfonyのメーラー関数に置き換える
            // Replace Swift mailer functions with Symfony mailer functions
            $this->_replaceChainFunction($tokens, 'setSubject', 'subject');
            $this->_replaceChainFunction($tokens, 'setFrom', 'from');
            $this->_replaceChainFunction($tokens, 'setTo', 'to');
            $this->_replaceChainFunction($tokens, 'setReplyTo', 'replyTo');
            $this->_replaceChainFunction($tokens, 'setReturnPath', 'returnPath');
            $this->_replaceChainFunction($tokens, 'setBody', 'text');
            $this->_replaceChainFunction($tokens, 'addPart', 'html');
            // Swift_Messageの型への参照をすべてMailInterfaceに置き換える
            // Replace any reference to type of Swift_Message to MailInterface
            $this->_parameterTypeFix($tokens);
        }
        return $tokens->generateCode();
    }


    private function _replaceChainFunction(Tokens &$tokens, string $from, string $to)
    {
        // Swift_Message クラスを変更する
        // Change Swift_Message class
        $subjectFunctionUpdate = $tokens->findSequence([
            [T_OBJECT_OPERATOR],
            [T_STRING, $from],
        ]);
        if ($subjectFunctionUpdate) {
            $useTokenIndexes = array_keys($subjectFunctionUpdate);
            $tokens[end($useTokenIndexes)]->setContent($to);
        }
    }

    private function _replaceArrayWithAddressObject(Tokens &$tokens)
    {
        $arrayStart = $tokens->findSequence([
            [T_OBJECT_OPERATOR],
            [T_STRING, 'setFrom'],
            '(',
            '['
        ]);

        if ($arrayStart) {
            $arrayStartIndex = array_keys($arrayStart);
            $endToken = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_SQUARE_BRACE, end($arrayStartIndex));
            $startTokenIndex = end($arrayStartIndex);
            $email = [];
            $emailName = [];
            $isNameNow = false;
            $tokens[$endToken]->setContent('');
            for ($y = $startTokenIndex; $y <= $endToken + 1; $y++) {
                /** @var $tokens Token[] */
                if ($tokens[$y]->equals([T_DOUBLE_ARROW, '=>'])) {
                    $isNameNow = true;
                    $tokens[$y]->setContent('');
                    continue;
                }
                if ($isNameNow) {
                    $emailName[] = $tokens[$y]->getContent();
                } else {
                    $email[] = $tokens[$y]->getContent();
                };
                $tokens[$y]->setContent('');
            }
            array_shift($email);
            $emailString = implode('', $email);
            $emailName = implode('', $emailName);
            $tokens->insertAt($startTokenIndex, [
                new Token([T_NEW, 'new']),
                new Token([T_WHITESPACE, ' ']),
                new Token([T_STRING, 'Address']),
                new Token([T_STRING, '(']),
                new Token([T_STRING, $emailString]),
                new Token([T_STRING, ',']),
                new Token([T_STRING, $emailName]),
                new Token([T_STRING, ')'])
            ]);
        }
    }

    private function _parameterTypeFix(Tokens &$tokens, int $parameterIndex = 0)
    {
        $arrayStart = $tokens->findSequence(
            [
                '('
            ],
            $parameterIndex
        );

        if ($arrayStart == null) {
            return;
        }

        $arrayStartIndex = array_keys($arrayStart);
        try {
            $endToken = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, end($arrayStartIndex));
        } catch (UnexpectedValueException $exception) {
            echo "NO END";
            $this->_parameterTypeFix($tokens, end($arrayStartIndex) + 1);
        }

        for ($y = end($arrayStartIndex); $y < $endToken; $y++) {
            /** @var Token[] $tokens */
            if ($tokens[$y]->getContent() == 'Swift_Mailer') {
                $tokens[$y - 1]->setContent('');
                $tokens[$y]->setContent('MailerInterface');
            }
        }

        $this->_parameterTypeFix($tokens, end($arrayStartIndex) + 1);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return 'Update from \Swift_Mailer to Symfony 5 MailerInterface';
    }
}
