<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class UnitTestSetUpFixer extends ReturnTypeFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        // setUp、tearDownメソッドにvoidリターンを追加。
        // Add void return to setUp and tearDown methods
        $this->upsertReturnType($tokens, '....', 'setUp', 'void');
        $this->upsertReturnType($tokens, '...', 'tearDown', 'void');

        // 以下の非推奨のユニットテストメソッドを、更新されたメソッドに変更する。
        // Change deprecated unit test methods below to an updated method
        $this->renameChainMethods($tokens, 'assertContains', 'assertStringContainsString');
        $this->renameChainMethods($tokens, 'assertRegexp', 'assertMatchesRegularExpression');
        return $tokens->generateCode();
    }


    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return 'UnitTest setUp function requires void return type';
    }
}
