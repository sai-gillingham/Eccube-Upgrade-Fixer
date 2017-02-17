<?php

namespace Eccube\Controller\Admin\Order;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class EditController extends AbstractController
{
    public function index(Application $app) {
        if ('POST' === $app['request_stack']->getCurrentRequest()->getMethod()) {
            $form->handleRequest($app['request_stack']->getCurrentRequest());
        }
    }
}