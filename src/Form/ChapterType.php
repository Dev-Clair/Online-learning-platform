<?php

namespace App\Form;

use App\Entity\Chapter;
use App\Entity\Courses;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('courses', EntityType::class, [
                'mapped' => true,
                'class' => Courses::class,
                'choice_label' => function (Courses $courses) {
                    return sprintf('%s', $courses->getTitle());
                },
                'placeholder' => '-- Select Course --',
                'attr' => ['class' => 'form-select']
            ]);
        // ->add('user', EntityType::class, [
        //     'mapped' => true,
        //     'class' => User::class,
        //     'choice_label' => function (User $user) {
        //         if (in_array('ROLE_INSTRUCTOR', $user->getRoles())) {
        //             return sprintf('%s %s', $user->getFirstname(), $user->getLastname());
        //         }
        //     },
        //     'placeholder' => '-- Click to Select Instructor --',
        //     'attr' => ['class' => 'form-select']
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapter::class,
        ]);
    }
}
