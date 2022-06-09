<?php

class ServiceClass
{
    public function _testFunction()
    {
        new Email(['strict' => $this->eccubeConfig['eccube_rfc_email_check']]);
    }
}
