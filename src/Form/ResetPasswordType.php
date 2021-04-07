<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('new_password', RepeatedType::class, [
            'type' => PasswordType::class,
            'label' => 'Votre nouveau mot de passe',
            'required' => true,
            'invalid_message' => 'Le mot de passe de confirmation doit être identique au mot de passe.',
            'first_options' => ['label' => 'Votre nouveau mot de passe',
            'attr'=> ['placeholder' => 'Merci de saisir votre nouveau mot de passe']],
            'second_options' => ['label' => 'Confirmez votre nouveau mot de passe',
            'attr'=> ['placeholder' => 'Merci de saisir votre nouveau mot de passe']]
            ])
        ->add('submit', SubmitType::class, [
            'label' => 'Mettre à jour',
            'attr' => [
                'class' => 'btn-block btn-info'
            ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
