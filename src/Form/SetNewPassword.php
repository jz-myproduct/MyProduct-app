<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class SetNewPassword extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Zadané heslo se neshoduje',
                'required' => true,
                'first_options' => ['label' => 'Nové heslo'],
                'second_options' => ['label' => 'Nové heslo znova'],
            ])
            ->add('save', SubmitType::class, ['label' => 'Změnit heslo']);
    }

}