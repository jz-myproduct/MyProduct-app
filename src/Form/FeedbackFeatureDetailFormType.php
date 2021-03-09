<?php

namespace App\Form;

use App\Entity\Feedback;
use App\Entity\Insight;
use App\Entity\InsightWeight;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackFeatureDetailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, ['label' => 'Popis'])
            ->add('source', TextareaType::class, ['required' => false, 'label' => 'Zdroj'])
            ->add('weights', CollectionType::class, [
                'entry_type' => InsightFormType::class,
                'entry_options' => ['label' => false]
            ])
            ->add('save', SubmitType::class, ['label' => 'Přidat']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
