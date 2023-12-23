<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'attr' => ['class' => 'form-control']
            ])
            ->add('address', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Enter your address here',
                    'class' => 'form-control',
                    'style' => ['height' => '150px']
                ]
            ])
            ->add('country', CountryType::class, [
                'mapped' => false,
                'placeholder' => '-- Click to Select Country --',
                'attr' => ['class' => 'form-select']
            ])
            ->add('user', EntityType::class, [
                'mapped' => false,
                'class' => 'App\Entity\User',
                'choice_label' => 'email',
                'placeholder' => '-- Click to Select User Email --',
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}
