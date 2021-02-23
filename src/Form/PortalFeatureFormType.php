<?php

namespace App\Form;

use App\Entity\FeatureState;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PortalFeatureFormType extends AbstractType
{
    private $states;
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
        $this->states = $options['states'];

        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, array('required' => false))
            ->add('state', ChoiceType::class, [
                'choices' => $this->manager->getRepository(PortalFeatureState::class)->findAll(),
                'choice_value' => 'id',
                'choice_label' => 'name'
            ])
            ->add('display', CheckboxType::class, [
                'label' => 'Zobrazit na portále',
                'required' => false
            ]);
        $builder->add('save', SubmitType::class, ['label' => 'Register'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PortalFeature::class,
        ]);
    }
}
