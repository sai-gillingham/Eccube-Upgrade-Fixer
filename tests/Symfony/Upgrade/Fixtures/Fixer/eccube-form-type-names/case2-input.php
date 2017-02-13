<?php

namespace Eccube\Controller\Admin\Order;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class EditController extends AbstractController
{
    public function index(Application $app, Request $request, $id = null) {

        $TargetOrder = array();

        $builder = $app['form.factory']->createBuilder();
        $builder = $app['form.factory']->createBuilder('order');
        $builder = $app['form.factory']->createBuilder('order', $TargetOrder);

        $builder = $app['form.factory']->createNamedBuilder('', 'customer_login');

        $form = $app['form.factory']->createBuilder('form')
            ->add('file', 'file')
            ->add('create_file', 'text')
            ->getForm();
    }

}