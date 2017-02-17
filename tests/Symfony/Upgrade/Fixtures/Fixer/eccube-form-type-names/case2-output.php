<?php

namespace Eccube\Controller\Admin\Order;

use Eccube\Form\Type\Admin\ProductClassType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('file', FileType::class)
            ->add('create_file', TextType::class)
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

        $builder
            ->add('product_classes', CollectionType::class, array(
                'entry_type' => ProductClassType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'data' => $ProductClasses,
            ));
    }

}