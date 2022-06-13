<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class UnitTestFixer extends ReturnTypeFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        /** @var Tokens|Token[] $tokens */
        $tokens = Tokens::fromCode($content);

        // setUp、tearDownメソッドにvoidリターンを追加。
        // Add void return to setUp and tearDown methods
        $this->upsertReturnType($tokens, '....', 'setUp', 'void');
        $this->upsertReturnType($tokens, '...', 'tearDown', 'void');

        $this->renameFunctionAccessType($tokens, 'setUp', T_PUBLIC, T_PROTECTED);
        $this->renameFunctionAccessType($tokens, 'tearDown', T_PUBLIC, T_PROTECTED);

        // 以下の非推奨のユニットテストメソッドを、更新されたメソッドに変更する。
        // Change deprecated unit test methods below to an updated method
        $this->renameChainMethods($tokens, 'assertContains', 'assertStringContainsString');
        $this->renameConstants($tokens, 'self', 'assertContains', 'assertStringContainsString');

        $this->renameChainMethods($tokens, 'assertRegexp', 'assertMatchesRegularExpression');
        $this->renameConstants($tokens, 'self', 'assertRegexp', 'assertMatchesRegularExpression');

        $this->renameChainMethods($tokens, 'assertArraySubset', 'assertSame');
        $this->renameConstants($tokens, 'self', 'assertArraySubset', 'assertSame');

        $this->renameChainMethods($tokens, 'assertEquals', 'assertSame');
        $this->renameConstants($tokens, 'self', 'assertEquals', 'assertSame');

        // MailCollector function change
        $this->_getMailCollectorToGetMailerMessage($tokens);
        // Login process UT update
        $this->_UTLoginProcessUpdate($tokens);
        
//        var_dump(T_STRING);
        $this->renameArrayValue($tokens, '\'tags\'', '', T_STRING,"null", "[]");
        $this->renameArrayValue($tokens, '\'images\'', '', T_STRING, "null", "[]");
        $this->renameArrayValue($tokens, '\'add_images\'', '', T_STRING , "null", "[]");
        $this->renameArrayValue($tokens, '\'delete_images\'', '', T_STRING, "null", "[]");
        $this->renameArrayValue($tokens, '\'Category\'', '', T_STRING, "null", "[]");
        
        return $tokens->generateCode();
    }

    /**
     * @return void
     * @var Tokens|Token[] $tokens
     */
    private function _getMailCollectorToGetMailerMessage(Tokens $tokens, int $index = 0)
    {
        // @todo: Get getMailCollector Line int
        $mailCollectorTokens = $tokens->findSequence(
            [
                '=',
                [T_VARIABLE, '$this'],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'getMailCollector'],
                '('
            ],
            $index
        );

        if ($mailCollectorTokens == null) {
//            var_dump("NOPE");
            return;
        }

        $matchedIndexes = array_keys($mailCollectorTokens);
//        var_dump("YEP");
//        var_dump($matchedIndexes);

        // Get Previous token which *should* be the variable name (or the plugin itself would crash)
        $mailCollectorName = $tokens[$matchedIndexes[0] - 2]->getContent();

        // Get End of function parameter
        $endToken = $tokens->findBlockEnd($tokens::BLOCK_TYPE_PARENTHESIS_BRACE, end($matchedIndexes));

        // First Line Clear
        for ($y = $matchedIndexes[0] - 2; $y < $endToken + 2; $y++) {
            $tokens[$y]->setContent('');
        }

        // Second Line
        $secondLineTokens = $tokens->findSequence([
            '=',
            [T_VARIABLE, $mailCollectorName],
            [T_OBJECT_OPERATOR],
            [T_STRING, 'getMessages'],
            '(',
            ')',
            ';'
        ], $index);

        if ($secondLineTokens == null) {
//            var_dump("STOP1");
            $this->_getMailCollectorToGetMailerMessage($tokens, $endToken);
            return;
        }

        $matchedIndexes = array_keys($secondLineTokens);
//        var_dump("YEP2");
//        var_dump($matchedIndexes);

        // Get Previous token which *should* be the variable name (or the plugin itself would crash)
        $secondLineVariableName = $tokens[$matchedIndexes[0] - 2]->getContent();

        for ($y = $matchedIndexes[0] - 2; $y < end($matchedIndexes) + 1; $y++) {
            $tokens[$y]->setContent('');
        }


        // @todo: Change $Messages[0] to $this->getMailerMessage();
        $thirdLineReplaceLine = $tokens->findSequence(
            [
                [T_VARIABLE, $secondLineVariableName]
            ],
            end($matchedIndexes) + 2
        );

        if ($thirdLineReplaceLine == null) {
            $this->_getMailCollectorToGetMailerMessage($tokens, end($matchedIndexes) + 2);
            return;
        }

        $matchedIndexes = array_keys($thirdLineReplaceLine);
//        var_dump("YEP3");
//        var_dump($matchedIndexes);

        $i = 0;
        while (true) {
            if ($tokens[end($matchedIndexes) + $i]->getContent() == ';') {
                break;
            }
            $tokens[end($matchedIndexes) + $i]->setContent('');
            $i++;
        }
        $tokens->insertAt(end($matchedIndexes),
            [
                new Token([T_VARIABLE, '$this']),
                new Token([T_OBJECT_OPERATOR, '->']),
                new Token([T_STRING, 'getMailerMessage()'])
            ]);

        $this->addUseStatement($tokens, ['Symfony', 'Component', 'Mime', 'RawMessage']);

        // Since previous checks have been made to confirm this file is a test file and email check are present we can
        // safe-fully assume getBody is associated with the MailInterface class and change the chain method
        // getBody() -> getHtmlBody() for the entire file is required.
        $this->renameChainMethods($tokens, 'getBody', 'getHtmlBody');

        $this->_getMailCollectorToGetMailerMessage($tokens, end($matchedIndexes));
    }

    /***
     * @param Tokens|Token[] $tokens
     * @return void
     */
    private function _UTLoginProcessUpdate(Tokens $tokens, int $index = 0)
    {
        //////////////////////////////////////
        // Login User Change
        // @todo: get the parameters inside setToken
        // @todo: 1 -> 1, 3 -> 2

        // @todo: Delete self::$container->get('security.token_storage')->setToken()
        // @todo: Replace with $this->client-loginUser() and add parameters from setToken;
        $difficultFind = $tokens->findSequence(
            [
                [T_STRING, 'self'],
                [T_DOUBLE_COLON],
                [T_VARIABLE, '$container'],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'get'],
                '(',

            ],
            $index
        );

        if ($difficultFind == null) {
            return;
        }

        $matchedIndexes = array_keys($difficultFind);

        // FindSequence functionality had trouble finding strings so find strings using another way
        if ($tokens[end($matchedIndexes) + 1]->getContent() !== "'security.token_storage'") {
            $this->_UTLoginProcessUpdate($tokens, end($matchedIndexes));
            return;
        }

        // Get User and firewall from UsernamePasswordToken
        $i = 0;
        $prepare = false;
        $parameterIndex = 0;
        $tempTokenArray = [];
        while (true) {
            $i++;
            if ($tokens[end($matchedIndexes) + $i]->getContent() == 'UsernamePasswordToken') {
                $prepare = true;
                continue;
            }
            if ($tokens[end($matchedIndexes) + $i]->getContent() == ',') {
                $parameterIndex++;
                continue;
            }
            if ($tokens[end($matchedIndexes) + $i]->getContent() == ';' && $prepare === true) {
                break;
            }
            if ($prepare === true) {
                $tempTokenArray[$parameterIndex][] = clone $tokens[end($matchedIndexes) + $i];
            }
        }

        // Delete the old code
        $x = 0;
        while (true) {
            if ($tokens[$matchedIndexes[0] + $x]->getContent() == ';') {
                $tokens[$matchedIndexes[0] + $x]->setContent('');
                break;
            }
            $tokens[$matchedIndexes[0] + $x]->setContent('');
            $x++;
        }
        $tokens->insertAt(
            $matchedIndexes[0],
            [
                new Token([T_VARIABLE, '$this']),
                new Token([T_OBJECT_OPERATOR, '->']),
                new Token([T_STRING, 'client']),
                new Token([T_OBJECT_OPERATOR, '->']),
                new Token([T_STRING, 'loginUser']),
                ...$tempTokenArray[0],
                new Token(','),
                ...$tempTokenArray[2],
                new Token(')'),
                new Token(';')
            ]
        );

        $this->_UTLoginProcessUpdate($tokens, end($matchedIndexes));
    }


    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'UnitTest setUp function requires void return type';
    }
}
