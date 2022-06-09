<?php

use Doctrine\Common\Persistence\ManagerRegistry;

class ServiceClass
{
    public function _testFunction($constraint)
    {
        new ManagerRegistry();
    }
}
