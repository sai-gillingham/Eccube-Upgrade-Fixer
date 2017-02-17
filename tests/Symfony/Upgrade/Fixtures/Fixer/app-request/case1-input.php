<?php

namespace Eccube\Controller\Admin\Order;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class EditController extends AbstractController
{
    public function index(Application $app) {
        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
        }
    }
}