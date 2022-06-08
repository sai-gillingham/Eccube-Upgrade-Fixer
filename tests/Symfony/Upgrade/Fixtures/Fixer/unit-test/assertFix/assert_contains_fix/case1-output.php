<?php

class TestClass
{
    public function _testFunction() {
        $this->assertStringContainsString("Yes", "Yes");
        self::assertStringContainsString("Yes", "Yes");
    }
}
