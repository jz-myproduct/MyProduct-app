<?php


namespace App\Form\Feature;


use App\Entity\FeatureState;
use App\Entity\FeatureTag;
use Doctrine\ORM\EntityManagerInterface;
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
        $currentStateChoice = $options['currentStateChoice'] ? $options['currentStateChoice'] : null;

        $tagChoices = $options['tagChoices'] ? $options['tagChoices'] : null;
        $currentTagChoices = $options['currentTagChoices'] ? $options['currentTagChoices'] : null;

        $fulltext = $options['fulltext'] ? $options['fulltext'] : null;

        $builder
            ->add('fulltext', TextType::class, [
                'label' => 'Název nebo popis',
                'required' => false,
                'data' => $fulltext
            ])
            ->add('state', ChoiceType::class, [
                'choices' => $stateChoices,
                'label' => 'Stav',
                'data' => $currentStateChoice
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags',
                'choices' => $tagChoices,
                'expanded' => true,
                'multiple' => true,
                'data' => $currentTagChoices,
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
            'currentStateChoice' => null,
            'stateChoices' => null,
            'currentTagChoices' => null,
            'tagChoices' => null,
            'fulltext' => null
        ]);
    }



}