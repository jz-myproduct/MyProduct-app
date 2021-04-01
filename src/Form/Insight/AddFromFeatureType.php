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
                'label' => 'Popis'
            ])
            ->add('source', TextType::class, [
                'label' => 'Kontakt',
                'required' => false
            ])
            ->add('weight', ChoiceType::class, [
                'choices' => $weights,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'label' => 'Feature je'
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
