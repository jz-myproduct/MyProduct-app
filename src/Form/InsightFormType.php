<?php

namespace App\Form;

use App\Entity\FeatureState;
use App\Entity\Insight;
use App\Entity\InsightWeight;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InsightFormType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('weight', ChoiceType::class, [
                'choices' => $this->manager->getRepository(InsightWeight::class)->findAll(),
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
            'data_class' => Insight::class,
        ]);
    }
}
