<?php

namespace App\Form\Insight;

use App\Entity\FeatureState;
use App\Entity\Insight;
use App\Entity\InsightWeight;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddFromFeedbackType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $weights = $options['weights'] ? $options['weights'] : null;

        $builder
            ->add('weight', ChoiceType::class, [
                'choices' => $weights,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'label' => 'Feature je'
            ])
            ->add('save', SubmitType::class, ['label' => 'Uložit']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'weights' => null
        ]);
    }
}
