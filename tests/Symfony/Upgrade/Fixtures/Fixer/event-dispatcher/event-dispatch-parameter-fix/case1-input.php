<?php

class ServiceClass
{
    public function _testFunction() {
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CUSTOMER_INDEX_INITIALIZE, new stdClass());
    }

    public function _testFunctionAlreadyFixed() {
        $this->eventDispatcher->dispatch(new stdClass(), EccubeEvents::ADMIN_CUSTOMER_INDEX_INITIALIZE);
    }
}
