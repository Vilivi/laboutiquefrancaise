<?php 

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as TypeEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('string', TextType::class, [
                'label' => false, 
                'required' => false, 
                'attr' => [
                    'placeholder' => 'Votre recherche...',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('categories', TypeEntityType::class, [
                'label' => false, 
                'required' => false,
                'class' => Category::class,
                'multiple' => true, //pour sélectionner plusieurs valeurs
                'expanded' => true //pour avoir une vue en checkbox et sélectionner donc plusieurs valeurs
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer', 
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET', //pour que les personnes qui reçoivent le lien ait l'URL configuré avec les bons filtres
            'crsf_protection' => false //pour désactiver la protection puisqu'on est dans un formulaire de recherche
        ]);
    }

    public function getBlockPrefix()
    {
        // return parent::getBlockPrefix();
        //pour ne pas avoir le préfix de la classe (Search) quand on retournera un tableau:
        return "";
    }
}