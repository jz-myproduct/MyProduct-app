<?php

namespace App\Form;

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

class FeatureFormType extends AbstractType
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
        $this->tags = $options['tags'];

        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, array('required' => false))
            ->add('state', ChoiceType::class, [
               'choices' => $this->entityManager->getRepository(FeatureState::class)->findAll(),
               'choice_value' => 'id',
               'choice_label' => 'name'
            ]);

        if($this->tags){
            $builder->add('tags', EntityType::class, [
                'class' => FeatureTag::class,
                'choices' => $this->tags,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true
            ]);
        }

        $builder->add('save', SubmitType::class, ['label' => 'Update']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feature::class,
            'tags' => null
        ]);
    }
}
