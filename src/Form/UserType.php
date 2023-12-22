<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Profile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                EmailType::class
            )
            ->add(
                'password',
                PasswordType::class
            )
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                        'Instructor' => 'ROLE_INSTRUCTOR',
                        'Student' => 'ROLE_STUDENT',
                    ],
                    'multiple' => true,
                    'expanded' => true
                ]
            )
            ->add(
                'userProfile',
                EntityType::class,
                [
                    'mapped' => false,
                    'class' => Profile::class,
                    // 'choice_label' => 'userProfile',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
