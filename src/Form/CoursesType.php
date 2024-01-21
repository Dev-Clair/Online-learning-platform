<?php

namespace App\Form;

use App\Entity\Courses;
use App\Entity\CoursesCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoursesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('duration', TextType::class)
            ->add('value', TextType::class)
            ->add(
                'category',
                EntityType::class,
                [
                    'mapped' => false,
                    'class' => CoursesCategory::class,
                    'choice_label' => function (CoursesCategory $coursesCategory) {
                        return $coursesCategory->getTitle();
                    },
                    'placeholder' => '-- Click to Select Category --',
                    'attr' => ['class' => 'form-select'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Courses::class,
        ]);
    }
}
