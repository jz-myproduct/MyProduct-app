<?php


namespace App\Form\Feature;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoadmapFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $tagChoices = $options['tagChoices'] ? $options['tagChoices'] : null;

        $builder
            ->add('fulltext', TextType::class, [
                'label' => 'Název nebo popis',
                'required' => false
            ])
            ->add('tags', ChoiceType::class, [
                'choices' => $tagChoices,
                'expanded' => true,
                'multiple' => true,
                'label_attr' => [
                    'class' => 'checkbox-inline'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Filtrovat',
                'attr' => ['class' => 'btn-outline-primary']
            ]) ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tagChoices' => null
        ]);
    }

}