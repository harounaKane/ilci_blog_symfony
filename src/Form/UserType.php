<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', TextType::class, [
                "label" => "Prénom",
                "attr" => [
                    "placeholder" => "votre prénom"
                ]
            ])
            ->add('nom', TextType::class, [
                "label" => "Nom",
                "attr" => [
                    "placeholder" => "votre nom"
                ]
            ])
            ->add('sexe', ChoiceType::class, [
                "label" => "Civilité",
                "choices" => [
                    "Femme" => 'femme',
                    "Homme" => "homme"
                ],
                "expanded" => true,
                "multiple" => false
            ])
            ->add('login', TextType::class, [
                "label" => "Login",
                "attr" => [
                    "placeholder" => "votre login"
                ]
            ])
            ->add('mdp', RepeatedType::class, [
                "type" => PasswordType::class,
                "first_options" => [
                    "label" => "Mot de passe"
                ],
                "second_options" => [
                    "label" => "Confirmation"
                ]
            ])
            ->add("Rejoindre", SubmitType::class)

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
