<?php

class TestClass
{
    public function _test()
    {
        $this->client->loginUser(
                new Customer(), 'customer');
    }

    public function _test_2()
    {
        $this->client->loginUser(
                new Admin(), 'admin');
    }

    public function _test_3()
    {
        $this->client->loginUser(
                $this->admin, 'admin');
    }

    public function _test_4()
    {
        $this->client->loginUser(
                $this->customer, 'customer');
    }
}
