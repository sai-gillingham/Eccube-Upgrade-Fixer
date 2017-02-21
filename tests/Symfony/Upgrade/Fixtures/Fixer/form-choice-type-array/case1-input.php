<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('gender', 'choice', array(
            'choices' => array('m' => 'Male', 'f' => 'Female')
        ));
        $arr = array('m' => 'Male', 'f' => 'Female');
        $builder->add('gender', 'choice', array(
            'choices' => $arr
        ));
        $builder->add('category_id', 'entity', array(
            'class' => 'Eccube\Entity\Category',
            'choice_label' => 'NameWithLevel',
            'choices' => $Categories,
            'placeholder' => '全ての商品',
            'required' => false,
            'label' => '商品カテゴリから選ぶ',
        ));
        $builder->add('category_id', EntityType::class, array(
            'class' => 'Eccube\Entity\Category',
            'choice_label' => 'NameWithLevel',
            'choices' => $Categories,
            'placeholder' => '全ての商品',
            'required' => false,
            'label' => '商品カテゴリから選ぶ',
        ));
    }

}