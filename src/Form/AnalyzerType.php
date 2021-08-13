<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnalyzerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Cpp файл',
                'attr' => ['accept' => '.cpp'],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'text/x-c++',
                            'text/x-c',
                        ],
                        'mimeTypesMessage' => 'Пожалуйста, загрузите валидный \'cpp\' файл!',
                    ]),
                    new NotBlank(),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Отправить',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
