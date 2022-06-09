<?php

use Doctrine\Persistence\ManagerRegistry as RegistryInterface;

class ServiceClass
{
    public function _testFunction($constraint)
    {
        new RegistryInterface();
    }
}
