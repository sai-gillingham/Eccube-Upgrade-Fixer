<?php

class ServiceClass
{
    public function _testFunction($constraint)
    {
        if ($constraint->mode===Email::VALIDATION_MODE_STRICT) {
            $baseEmailValidator = new BaseEmailValidator(Email::VALIDATION_MODE_STRICT);
        }
    }
}
