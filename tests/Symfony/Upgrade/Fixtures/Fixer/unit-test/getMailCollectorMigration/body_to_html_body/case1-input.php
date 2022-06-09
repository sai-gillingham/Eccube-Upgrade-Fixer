<?php

class TestClass
{
    public function _test() {
        $mailCollector = $this->getMailCollector(false);
        $Messages = $mailCollector->getMessages();
        $Message = $Messages[0];
        $Message->getBody();
    }
}
