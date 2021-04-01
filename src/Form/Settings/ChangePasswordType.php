<?php


namespace App\Form\Settings;


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
                'label' => 'Current password',
                'required' => true
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Your new password and confirmation password do not match.',
                'required' => true,
                'first_options' => ['label' => 'New password'],
                'second_options' => ['label' => 'New password confirmation']
            ])
            ->add('save', SubmitType::class, ['label' => 'Change password']);
    }


}