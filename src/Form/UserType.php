<?php

namespace App\Form;

use App\Entity\UserAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                options: [
                    'label' => 'Nom',
                    'required' => true
                ]
            )
            ->add(
                'firstName',
                options: [
                    'label' => 'Prénom',
                    'required' => true
                ]
            )
            ->add(
                'email',
                options: [
                    'required' => true
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                options: [
                    'label' => 'Password',
                    'required' => true
                ]
            )
            ->add('roles', ChoiceType::class, options: [
                'label' => 'Roles',
                'required' => false,
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => true,
                ])
            ->add('Enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserAccount::class,
        ]);
    }
}
