<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class UnitTestFixer extends ReturnTypeFixer
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
