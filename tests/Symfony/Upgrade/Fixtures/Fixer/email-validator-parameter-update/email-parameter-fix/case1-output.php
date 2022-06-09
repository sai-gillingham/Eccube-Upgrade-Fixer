<?php

class ServiceClass
{
    public function _testFunction()
    {
        new Email(null, null, $this->eccubeConfig['eccube_rfc_email_check'] ? 'strict' : null);
    }
}
