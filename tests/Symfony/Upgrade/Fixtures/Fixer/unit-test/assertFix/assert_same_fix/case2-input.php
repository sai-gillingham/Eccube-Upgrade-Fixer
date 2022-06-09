<?php

class TestClass
{
    public function _testFunction() {
        $this->assertEquals(new stdClass(), new stdClass());
        self::assertEquals(new stdClass(), new stdClass());
    }


}
