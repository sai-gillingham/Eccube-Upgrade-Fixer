<?php

namespace Eccube\Controller\Admin\Order;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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

        $builder
            ->add('class_name1', EntityType::class, array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '規格1を選択',
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('class_name2', EntityType::class, array(
                'class' => 'Eccube\Entity\ClassName',
                'property' => 'name',
                'empty_value' => '規格2を選択',
                'required' => false,
            ));
    }

}