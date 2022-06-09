<?php

class ServiceClass
{
    public function _testFunction($constraint)
    {
        if ($constraint->strict) {
            $baseEmailValidator = new BaseEmailValidator($constraint->strict);
        }
    }
}
