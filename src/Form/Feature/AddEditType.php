<?php

namespace App\Form\Feature;

use App\Entity\Feature;
use App\Entity\FeatureState;
use App\Entity\FeatureTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddEditType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    private $tags;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tags = $options['tags'] ? $options['tags']->toArray() : null;
        $states = $options['states'] ?? null;

        $builder
            ->add('name', TextType::class, ['label' => 'Jméno'])
            ->add('description', TextareaType::class, ['required' => false, 'label' => 'Popis'])
            ->add('state', ChoiceType::class, [
               'choices' => $states,
               'choice_value' => 'id',
               'choice_label' => 'name',
               'label' => 'Stav'
            ]);

        if($tags){
            $builder->add('tags', EntityType::class, [
                'class' => FeatureTag::class,
                'choices' => $tags,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true
            ]);
        }

        $builder->add('save', SubmitType::class, ['label' => 'Uložit']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tags' => null,
            'states' => null
        ]);
    }
}
