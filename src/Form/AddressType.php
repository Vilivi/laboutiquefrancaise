<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Quel nom souhaitez-vous donner à votre addresse?',
                'attr' => [ 'placeholder' => 'Nommez votre adresse']
            ])
            ->add('firstname',TextType::class, [
                'label' => 'Votre prénom',
                'attr' => [ 'placeholder' => 'Rentrez votre prénom']
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [ 'placeholder' => 'Rentrez votre nom']
            ])
            ->add('company', TextType::class, [
                'label' => 'Le nom de votre société',
                'required' => false,
                'attr' => [ 'placeholder' => '(facultatif) Rentrez le nom de votre société']
            ])
            ->add('address', TextType::class, [
                'label' => 'L\'adresse',
                'attr' => [ 'placeholder' => 'Rentrez l\'adresse']
            ])
            ->add('postal', TextType::class, [
                'label' => 'Le code postal',
                'attr' => [ 'placeholder' => 'Rentrez le code postal']
            ])
            ->add('city', TextType::class, [
                'label' => 'La ville',
                'attr' => [ 'placeholder' => 'Rentrez la ville']
            ])
            ->add('country', CountryType::class, [
                'label' => 'Le pays',
                'attr' => [ 'placeholder' => 'Rentrez le pays']
            ])
            ->add('phone', TelType::class, [
                'label' => 'Le numéro de téléphone',
                'attr' => [ 'placeholder' => 'Le numéro de téléphone']
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider', 
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
