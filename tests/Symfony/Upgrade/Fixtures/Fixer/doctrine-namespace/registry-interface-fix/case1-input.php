<?php

use Symfony\Bridge\Doctrine\RegistryInterface;

class ServiceClass
{
    public function _testFunction($constraint)
    {
        new RegistryInterface();
    }
}
