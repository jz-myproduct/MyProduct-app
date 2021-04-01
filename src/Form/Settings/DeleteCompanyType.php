<?php


namespace App\Form\Settings;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteCompanyType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Delete company',
                'attr' => ['class' => 'btn-danger']
            ]);
    }

}