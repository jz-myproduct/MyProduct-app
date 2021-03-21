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
                'label' => 'Stav',
                'required' => false,
                'choices' => [
                    'Oboje' => null,
                    'Nový' => 1,
                    'Přečtený' => 0
                ],
                'empty_data' => null
            ])
            ->add('fulltext', TextType::class, [
                'label' => 'Hledat feedback',
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Filtrovat']) ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }

}