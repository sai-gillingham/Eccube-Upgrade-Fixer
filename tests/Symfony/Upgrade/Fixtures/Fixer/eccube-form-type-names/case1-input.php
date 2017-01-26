<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'name')
            ->add('kana', 'kana')
            ->add('tel', 'tel')
        ;
    }
}
