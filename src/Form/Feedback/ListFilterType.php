<?php


namespace App\Form\Feedback;

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

        $builder
            ->add('isNew', ChoiceType::class, [
                'label' => 'State',
                'required' => false,
                'choices' => [
                    'All' => null,
                    'New' => 1,
                    'Processed' => 0
                ],
                'empty_data' => null
            ])
            ->add('fulltext', TextType::class, [
                'label' => 'Description or contact',
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Search',
                'attr' => ['class' => 'btn-outline-primary']
            ]) ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }

}