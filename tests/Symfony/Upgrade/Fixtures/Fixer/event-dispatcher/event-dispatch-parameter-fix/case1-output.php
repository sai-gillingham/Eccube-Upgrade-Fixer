<?php

class ServiceClass
{
    public function _testFunction() {
        $this->eventDispatcher->dispatch( new stdClass(),EccubeEvents::ADMIN_CUSTOMER_INDEX_INITIALIZE);
    }

    public function _testFunctionAlreadyFixed() {
        $this->eventDispatcher->dispatch(new stdClass(), EccubeEvents::ADMIN_CUSTOMER_INDEX_INITIALIZE);
    }
}
