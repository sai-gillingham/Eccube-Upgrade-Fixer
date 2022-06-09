<?php

class TestClass
{
    public function _testFunction() {
        $this->assertContains("Yes", "Yes");
        self::assertContains("Yes", "Yes");
    }
}
