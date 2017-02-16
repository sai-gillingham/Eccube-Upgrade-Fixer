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

        $builder
            ->add('class_name1', 'entity', array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '規格1を選択',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('class_name2', 'entity', array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '規格2を選択',
                'required' => false,
            ));
    }

}