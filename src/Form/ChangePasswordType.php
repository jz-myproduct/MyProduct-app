<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Současné heslo',
                'required' => true
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Zadané heslo se neshoduje',
                'required' => true,
                'first_options' => ['label' => 'Nové heslo'],
                'second_options' => ['label' => 'Nové heslo znova']
            ])
            ->add('save', SubmitType::class, ['label' => 'Změnit heslo']);
    }


}