<?php

namespace App\Form\PortalFeature;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AddEditFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $states = $options['states'] ? $options['states'] : null;

        $builder
            ->add('display', CheckboxType::class, [
                'label' => 'Display on portal',
                'required' => false
            ])
            ->add('state', ChoiceType::class, [
                'choices' => $states,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'label' => 'Section'
            ])
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('description', TextareaType::class, ['required' => false, 'label' => 'Description'])
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/x-png',
                            'image/pjpeg'
                        ],
                        'mimeTypesMessage' => 'Unsupported file format',
                    ])
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Save']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'states' => null
        ]);
    }
}
