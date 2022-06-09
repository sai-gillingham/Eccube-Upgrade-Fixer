<?php

class TestClass
{
    public function _test()
    {
        self::$container->get('security.token_storage')->setToken(
            new UsernamePasswordToken(
                new Customer(), null, 'customer', []
            )
        );
    }

    public function _test_2()
    {
        self::$container->get('security.token_storage')->setToken(
            new UsernamePasswordToken(
                new Admin(), null, 'admin', []
            )
        );
    }

    public function _test_3()
    {
        self::$container->get('security.token_storage')->setToken(
            new UsernamePasswordToken(
                $this->admin, null, 'admin', []
            )
        );
    }

    public function _test_4()
    {
        self::$container->get('security.token_storage')->setToken(
            new UsernamePasswordToken(
                $this->customer, null, 'customer', []
            )
        );
    }
}
