<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use UnexpectedValueException;

class SwiftMailerChangeFixer extends AbstractFixer
{
    public bool $didUpdateMailer = false;
    public int $avoidAttemptTryCount = 6;

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $this->didUpdateMailer = false;
        $this->updateEmailClass($tokens);
        if ($this->didUpdateMailer === true) {
            // Useステートメントの追加
            // Add Use Statements
            $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mailer', 'MailerInterface']);
            $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mime', 'Address']);
            $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mime', 'Email']);
            // Swift_MessageクラスをEmailクラスに変更する
            // Change Swift_Message class to Email Class
            // 配列のfrom型をAddressクラスのインスタンスに置き換える
            // Replace "from" array type with Address class instance
            $this->_replaceArrayWithAddressObject($tokens);
            // Swiftのメーラー関数をSymfonyのメーラー関数に置き換える
            // Replace Swift mailer functions with Symfony mailer functions
            $this->_replaceChainFunction($tokens, 'setSubject', 'subject', 0, ['$sendHistory'] );
            $this->_replaceChainFunction($tokens, 'setFrom', 'from', 0, ['$sendHistory']);
            $this->_replaceChainFunction($tokens, 'setTo', 'to', 0, ['$sendHistory']);
            $this->_replaceChainFunction($tokens, 'setBcc', 'bcc', 0, ['$sendHistory']);
            $this->_replaceChainFunction($tokens, 'setReplyTo', 'replyTo', 0, ['$sendHistory']);
            $this->_replaceChainFunction($tokens, 'setReturnPath', 'returnPath', 0, ['$sendHistory']);
            $this->_replaceChainFunction($tokens, 'setBody', 'text', 0, ['$sendHistory']);
            $this->_replaceChainFunction($tokens, 'addPart', 'html', 0, ['$sendHistory']);
            // メール
            $this->_deleteChainFunction($tokens, 'setContentType');

            // Swift_Messageの型への参照をすべてMailInterfaceに置き換える
            // Replace any reference to type of Swift_Message to MailInterface
            $this->_parameterTypeFix($tokens);
        }
        return $tokens->generateCode();
    }

    private function updateEmailClass(Tokens $tokens, int $index = 0)
    {
        $swiftMessageClass = $tokens->findSequence(
            [
                [T_NEW],
                [T_NS_SEPARATOR],
                [T_STRING, 'Swift_Message'],
                '(',
                ')'
            ],
            $index
        );
        if ($swiftMessageClass == null) {
            return;
        }
        $useTokenIndexes = array_keys($swiftMessageClass);
        $tokens[end($useTokenIndexes) - 3]->setContent('');
        $tokens[end($useTokenIndexes) - 2]->setContent('Email');
        $this->didUpdateMailer = true;
        $this->updateEmailClass($tokens, end($useTokenIndexes));
    }


    private function _replaceChainFunction(Tokens &$tokens, string $from, string $to, $index = 0, array $avoidVariableNames = [])
    {
        // Swift_Message クラスを変更する
        // Change Swift_Message class
        $subjectFunctionUpdate = $tokens->findSequence(
            [
                [T_OBJECT_OPERATOR],
                [T_STRING, $from],
            ],
            $index);

        if ($subjectFunctionUpdate == null) {
            return;
        }

        $useTokenIndexes = array_keys($subjectFunctionUpdate);
        $stopReplace = false;

        // @todo: これを改良して、避けるべき正しいインスタンスタイプを見つけるようにしたいのですが、 今のところ、これは $avoidVariableNames の内容を持つトークンをすべて避けるようにします。
        // @todo: Want to improve this by finding the correct instancetype to avoid, but for now, this will avoid any with token with contents of $avoidVariableNames
        foreach($avoidVariableNames as $avoidVariableName) {
            for ($i = 0; $i < $this->avoidAttemptTryCount;  $i++) {
                /** @var Tokens|Token[] $tokens */
                if ($from == 'setBody') {
                    var_dump($tokens[end($useTokenIndexes) - $i]->getContent());
                }
                if ($tokens[end($useTokenIndexes) - $i]->getContent() == $avoidVariableName) {
                    $stopReplace = true;
                }
            }
        }
        if ($stopReplace === false) {
            $tokens[end($useTokenIndexes)]->setContent($to);
        }
        
        $this->_replaceChainFunction($tokens, $from, $to, end($useTokenIndexes), $avoidVariableNames);
    }

    /**
     * @param Tokens $tokens
     * @param string $from
     * @param int $index
     * @return void
     */
    private function _deleteChainFunction(Tokens &$tokens, string $from, int $index = 0)
    {
        $deleteChainFunction = $tokens->findSequence(
            [
                [T_OBJECT_OPERATOR],
                [T_STRING, $from],
                '('
            ],
            $index
        );

        if ($deleteChainFunction == null) {
            return;
        }

        $deleteFunctionToken = array_keys($deleteChainFunction);

        $methodEndToken = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, end($deleteFunctionToken));

        for ($i = $deleteFunctionToken[0]; $i < $methodEndToken + 1; $i++) {
            /** @var Tokens|Token[] $tokens */
            $tokens[$i]->setContent('');
        }
        $this->_deleteChainFunction($tokens, $from, $methodEndToken);
    }

    private function _replaceArrayWithAddressObject(Tokens &$tokens, int $index = 0)
    {
        $arrayStart = $tokens->findSequence(
            [
                [T_OBJECT_OPERATOR],
                [T_STRING, 'setFrom'],
                '(',
                '['
            ],
            $index
        );

        if ($arrayStart == null) {
            return;
        }

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
        $this->_replaceArrayWithAddressObject($tokens, $endToken);
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
        }

        if (isset($endToken)) {
            for ($y = end($arrayStartIndex); $y < $endToken; $y++) {
                /** @var Token[] $tokens */
                if ($tokens[$y]->getContent() == 'Swift_Mailer') {
                    $tokens[$y - 1]->setContent('');
                    $tokens[$y]->setContent('MailerInterface');
                }
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
