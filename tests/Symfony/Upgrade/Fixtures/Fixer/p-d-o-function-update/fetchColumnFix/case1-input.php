<?php

class ServiceClass
{
    public function _testFunction() {
        $this->fetchColumn("select nextval('dtb_member_id_seq')");
    }
}
