<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'mapped' => false,
                'required' => true,
                'placeholder' => 'Enter your email',
                'class' => ['form-control']
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'placeholder' => 'Enter new password',
                'class' => ['form-control']
            ])
            ->add('confirm password', PasswordType::class, [
                'mapped' => false,
                'required' => true,
                'placeholder' => 'Re-enter password to confirm',
                'class' => ['form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
