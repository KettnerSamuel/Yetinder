<?php

namespace App\Form;

use App\Entity\Yetties;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class YetiFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', options: ['required' => true])
            ->add('appearance', options: ['required' => true])
            ->add('color', choiceType::class, [
                'choices' => [
                    'White' => 'White',
                    'Brown' => 'Brown',
                    'Black' => 'Black',
                    'Green' => 'Green',
                    'Red' => 'Red',
                    'Blue' => 'Blue',
                    'Yellow' => 'Yellow',
                    'Purple' => 'Purple',
                    'Orange' => 'Orange',
                    'Pink' => 'Pink',
                ],
                'multiple' => false,
                'required' => true
            ])
            ->add('weight', NumberType::class, [
                'attr' => [
                    'pattern' => '^\d{1,4}$',
                    'inputmode' => 'numeric'
                ],
                'required' => false
            ])
            ->add('height', NumberType::class, [
                'attr' => [
                    'pattern' => '^\d{1,4}$',
                    'inputmode' => 'numeric'
                ],
                'required' => false
            ])
            ->add('image_path', FileType::class, [
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/*',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image',
                ])
            ]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Yetties::class,
        ]);
    }
}
