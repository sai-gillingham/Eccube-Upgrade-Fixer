<?php

class TestClass
{
    public function _testFunction() {
        $this->assertMatchesRegularExpression('/[^a-z0-9 ]/i', "Yes");
        self::assertMatchesRegularExpression('/[^a-z0-9 ]/i', "Yes");
    }
}
