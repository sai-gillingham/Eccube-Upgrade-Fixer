<?php

class ServiceClass
{
    public function _testFunction() {
        $this->fetchOne("select nextval('dtb_member_id_seq')");
    }
}
