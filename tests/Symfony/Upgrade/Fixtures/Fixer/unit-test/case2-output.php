<?php

abstract class TestClass
{
    public function testChain○○○○○○○○()
    {
        $this->assertStringContainsString('string', 'string');
        $this->assertMatchesRegularExpression('regex', 'string');
    }

    public function testChain☓☓☓☓☓☓☓() {
        $this->assertSame([], []);
    }

    public function testChainYYYYYYYYYYYYYYY() {
        $this->assertSame("ab", "ab");
    }

    public function testConst○○○○○○○○() {
        self::assertStringContainsString('string', 'string');
        self::assertMatchesRegularExpression('regex', 'string');
    }

    public function testConst☓☓☓☓☓☓☓() {
        self::assertSame([], []);
    }

    public function testConstYYYYYYYYYYYYYYY() {
        self::assertSame("ab", "ab");
    }
}
