<?php

class TestClass
{
    protected function setUp() : void
    {

    }

    protected function tearDown() : void
    {

    }

    public function _test()
    {
        $Message = new stdClass();
        $Message->getBody();
    }
}
