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
    }

}