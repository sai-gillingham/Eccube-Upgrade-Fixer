<?php

class TestClass
{
    public function _testFunction() {
        $this->assertSame(new stdClass(), new stdClass());
        self::assertSame(new stdClass(), new stdClass());
    }


}
