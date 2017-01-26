<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Eccube\Form\Type\TelType;
use Eccube\Form\Type\KanaType;
use Eccube\Form\Type\NameType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', NameType::class)
            ->add('kana', KanaType::class)
            ->add('tel', TelType::class)
        ;
    }
}
