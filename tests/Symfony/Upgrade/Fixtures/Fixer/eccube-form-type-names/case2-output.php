<?php

namespace Eccube\Controller\Admin\Order;

use Eccube\Form\Type\Front\CustomerLoginType;
use Eccube\Form\Type\Admin\OrderType;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class EditController extends AbstractController
{
    public function index(Application $app, Request $request, $id = null) {

        $TargetOrder = array();

        $builder = $app['form.factory']->createBuilder();
        $builder = $app['form.factory']->createBuilder(OrderType::class);
        $builder = $app['form.factory']->createBuilder(OrderType::class, $TargetOrder);

        $builder = $app['form.factory']->createNamedBuilder('', CustomerLoginType::class);

        $form = $app['form.factory']->createBuilder('form')
            ->add('file', 'file')
            ->add('create_file', 'text')
            ->getForm();
    }

}