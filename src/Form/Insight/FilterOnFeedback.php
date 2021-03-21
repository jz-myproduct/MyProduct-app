<?php


namespace App\Form\Insight;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterOnFeedback extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tags = $options['tags'] ? $options['tags'] : null;

        $builder
            ->add('fulltext', TextType::class, [
                'label' => 'Název nebo popis',
                'required' => false
            ])
            ->add('tags', ChoiceType::class, [
                'label' => 'Tags',
                'choices' => $tags,
                'expanded' => true,
                'multiple' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Filtrovat']) ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tags' => null
        ]);
    }

}