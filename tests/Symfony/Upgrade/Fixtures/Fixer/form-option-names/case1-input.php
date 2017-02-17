<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Eccube\Entity\MailTemplate',
            'empty_value' => '-',
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'form.name'))
            ->add('price1', 'text', array(
                'label' => 'form.price1',
                'precision' => 3,
            ))
            ->add('price2', 'text', array(
                'precision' => 3,
            ))
            ->add('discount', 'integer', [
                'label' => 'form.email',
                'virtual' => true,
            ])
            ->add('password', 'password')
            ->add('device', 'entity', array(
                'class' => 'Eccube\Entity\Master\DeviceType',
                'property' => 'id',
                'empty_value' => '指定なし',
                'empty_data' => null,
            ))
            ->add('device', 'entity', array(
                'empty_data' => null
            ))
            ->add('device', 'entity', array('empty_data' => null))
            ->add('Shippings', 'collection', array(
                'type' => 'shipping',
            ))
        ;
    }
}
