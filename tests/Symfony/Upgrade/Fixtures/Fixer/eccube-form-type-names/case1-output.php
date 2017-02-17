<?php

namespace Umpirsky\UpgradeBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
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

        $builder->add(
            $builder
                ->create('company_kana', TextType::class, array(
                    'label' => '会社名(フリガナ)',
                    'required' => false,
                    'constraints' => array(
                        new Assert\Regex(array(
                            'pattern' => "/^[ァ-ヶｦ-ﾟー]+$/u",
                        )),
                        new Assert\Length(array(
                            'max' => $config['stext_len'],
                        )),
                    ),
                ))
                ->addEventSubscriber(new \Eccube\EventListener\ConvertKanaListener('CV'))
        );
    }
}
