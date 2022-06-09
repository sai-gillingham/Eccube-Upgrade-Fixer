<?php

use Doctrine\Persistence\ManagerRegistry;

class ServiceClass
{
    public function _testFunction($constraint)
    {
        new ManagerRegistry();
    }
}
