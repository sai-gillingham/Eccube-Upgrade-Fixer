<?php

class TestClass
{
    public function _testFunction() {
        $this->assertSame([], []);
        self::assertSame([], []);
    }
}
