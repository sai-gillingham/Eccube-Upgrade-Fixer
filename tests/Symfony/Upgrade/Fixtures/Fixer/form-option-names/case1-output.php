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
            'placeholder' => '-',
            'tel01_options' => array(
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                    new Assert\Length(array('max' => $this->config['tel_len'], 'min' => $this->config['tel_len_min'])),
                ),
            ),
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'form.name'))
            ->add('price1', 'text', array(
                'label' => 'form.price1',
                'scale' => 3,
            ))
            ->add('price2', 'text', array(
                'scale' => 3,
            ))
            ->add('discount', 'integer', [
                'label' => 'form.email',
                'inherit_data' => true,
            ])
            ->add('password', 'password')
            ->add('device', 'entity', array(
                'class' => 'Eccube\Entity\Master\DeviceType',
                'choice_label' => 'id',
                'placeholder' => '指定なし',
            ))
            ->add('device', 'entity', array(
            ))
            ->add('device', 'entity', array())
            ->add('Shippings', 'collection', array(
                'entry_type' => 'shipping',
            ))
        ;

        $builder
            ->add('line_max', TextType::class, array(
                'label' => '表示行数',
                'data' => '50',
                'constraints' => array(
                    new Assert\Type(array('type' => 'numeric', 'message' => 'form.type.numeric.invalid')),
                    new Assert\NotBlank(),
                ),
            ));
    }
}
