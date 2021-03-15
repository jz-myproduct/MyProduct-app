<?php


namespace App\Form;


use App\Entity\FeatureState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatureStateFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $choices = $options['choices'] ? $options['choices'] : null;
        $currentChoice = $options['currentChoice'] ? $options['currentChoice'] : null;

        $builder
            ->add('state', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'Stav',
                'data' => $currentChoice
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'currentChoice' => null,
            'choices' => null
        ]);
    }



}