<?php

namespace App\Form;

use App\Entity\Feature;
use App\Entity\FeatureState;
use App\Entity\Feedback;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackFormType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /** @var Feature */
    private $featuresChoices;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->featuresChoices = $options['featureChoices'];
        dump($this->featuresChoices = $options['featureChoices']);

        $builder
            ->add('description', TextareaType::class)
            ->add('source', TextareaType::class, array('required' => false));

        if($this->featuresChoices)
        {
            $builder->add('feature', EntityType::class, [
                'class' => Feature::class,
                'choices' => $this->featuresChoices,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true
            ]);
        }

        $builder->add('save', SubmitType::class, ['label' => 'Create']);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
            'featureChoices' => null
        ]);
    }
}
