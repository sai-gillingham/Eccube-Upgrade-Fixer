<?php

class TestClass
{
    public function _testFunction() {
        $this->assertArraySubset([], []);
        self::assertArraySubset([], []);
    }
}
