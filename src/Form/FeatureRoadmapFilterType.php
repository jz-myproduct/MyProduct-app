<?php


namespace App\Form;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatureRoadmapFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $tagChoices = $options['tagChoices'] ? $options['tagChoices'] : null;
        $currentTagChoices = $options['currentTagChoices'] ? $options['currentTagChoices'] : null;

        $builder
            ->add('tags', ChoiceType::class, [
                'choices' => $tagChoices,
                'expanded' => true,
                'multiple' => true,
                'data' => $currentTagChoices
            ])
            ->add('save', SubmitType::class, ['label' => 'Filtrovat']) ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'currentTagChoices' => null,
            'tagChoices' => null
        ]);
    }

}