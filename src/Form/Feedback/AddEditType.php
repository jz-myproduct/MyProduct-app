<?php

namespace App\Form\Feedback;

use App\Entity\Feature;
use App\Entity\FeatureState;
use App\Entity\Feedback;
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
    /** @var Feature */
    private $featuresChoices;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, ['label' => 'Popis'])
            ->add('source', TextType::class, ['required' => false, 'label' => 'Kontakt'])
            ->add('save', SubmitType::class, ['label' => 'Uložit']);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
