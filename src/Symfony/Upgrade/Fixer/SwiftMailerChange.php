<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class SwiftMailerChange extends AbstractFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        if ($file->getFilename() == 'MailMagazineService.php') {
            // Add Use Statements
            $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mailer', 'MailerInterface']);
            $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mime', 'Address']);
            $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mime', 'Email']);

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

            // Replace "from" array type with Address class instance
            $this->_replaceArrayWithAddressObject($tokens);

            // Replace Swift mailer functions with Symfony mailer functions
            $this->_replaceChainFunction($tokens, 'setSubject', 'subject');
            $this->_replaceChainFunction($tokens, 'setFrom', 'from');
            $this->_replaceChainFunction($tokens, 'setTo', 'to');
            $this->_replaceChainFunction($tokens, 'setReplyTo', 'replyTo');
            $this->_replaceChainFunction($tokens, 'setReturnPath', 'returnPath');
            $this->_replaceChainFunction($tokens, 'setBody', 'text');
            $this->_replaceChainFunction($tokens, 'addPart', 'html');

            // Replace comments
//            $this->_replacePHPDOCComment($tokens);
        }
        return $tokens->generateCode();
    }


    private function _replaceChainFunction(Tokens &$tokens, string $from, string $to)
    {
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

    private function _replaceArrayWithAddressObject(Tokens &$tokens) {
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
                new Token([T_STRING,')'])
            ]);
        }
    }

    private function _replacePHPDOCComment(Tokens &$tokens) {
        var_dump("1");

        $commentsToken = $tokens->findSequence([
            [T_DOC_COMMENT, '/*']
        ]);
        var_dump("2");
        var_dump($commentsToken);

        if($commentsToken) {
            $commentsIndex = array_keys($commentsToken);
            /** @var Token[] $tokens */
            $tokens[end($commentsIndex) - 1]->setContent('');
            $tokens[end($commentsIndex)]->setContent('MailerInterface');
        }
        var_dump("3");


        $commentsToken = $tokens->findSequence([
            [T_STRING, '@param'],
            [T_WHITESPACE, ' '],
            [T_NS_SEPARATOR],
            [T_STRING, 'Swift_Message']
        ]);

        var_dump($commentsToken);

        if($commentsToken) {
            $commentsIndex = array_keys($commentsToken);
            /** @var Token[] $tokens */
            $tokens[end($commentsIndex) - 1]->setContent('');
            $tokens[end($commentsIndex)]->setContent('MailerInterface');
        }
    }


    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        // TODO: Implement getDescription() method.
    }
}
