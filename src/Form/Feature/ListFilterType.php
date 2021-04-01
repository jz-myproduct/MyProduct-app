<?php

namespace App\Form\Feature;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $stateChoices = $options['stateChoices'] ? $options['stateChoices'] : null;
        $tagChoices = $options['tagChoices'] ? $options['tagChoices'] : null;

        $builder
            ->add('fulltext', TextType::class, [
                'label' => 'Name or description',
                'required' => false
            ])
            ->add('state', ChoiceType::class, [
                'choices' => $stateChoices,
                'label' => 'State'
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags',
                'choices' => $tagChoices,
                'expanded' => true,
                'multiple' => true,
                'label_attr' => [
                    'class' => 'checkbox-inline'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['class' => 'btn-outline-primary']
            ]) ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'stateChoices' => null,
            'tagChoices' => null,
        ]);
    }



}