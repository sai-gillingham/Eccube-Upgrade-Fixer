<?php

abstract class TestClass
{
    public function testChain○○○○○○○○()
    {
        $this->assertContains('string', 'string');
        $this->assertRegexp('regex', 'string');
    }

    public function testChain☓☓☓☓☓☓☓() {
        $this->assertArraySubset([], []);
    }

    public function testChainYYYYYYYYYYYYYYY() {
        $this->assertEquals("ab", "ab");
    }

    public function testConst○○○○○○○○() {
        self::assertContains('string', 'string');
        self::assertRegexp('regex', 'string');
    }

    public function testConst☓☓☓☓☓☓☓() {
        self::assertArraySubset([], []);
    }

    public function testConstYYYYYYYYYYYYYYY() {
        self::assertEquals("ab", "ab");
    }
}
