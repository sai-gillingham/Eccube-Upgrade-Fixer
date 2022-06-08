<?php

class TestClass
{
    public function _testFunction() {
        $this->assertRegexp('/[^a-z0-9 ]/i', "Yes");
        self::assertRegexp('/[^a-z0-9 ]/i', "Yes");
    }
}
