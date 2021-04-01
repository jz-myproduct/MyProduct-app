<?php

namespace App\Form\Insight;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddFromFeatureType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $weights = $options['weights'] ? $options['weights'] : null;

        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('source', TextType::class, [
                'label' => 'Contact',
                'required' => false
            ])
            ->add('weight', ChoiceType::class, [
                'choices' => $weights,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'label' => 'How important is this to you?'
            ])
            ->add('save', SubmitType::class, ['label' => 'Uložit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'weights' => null
        ]);
    }
}
