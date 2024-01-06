<?php

namespace App\Form;

use App\Entity\Courses;
use App\Entity\Reviews;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('review', TextareaType::class);
        // ->add('course', EntityType::class, [
        //     'mapped' => true,
        //     'class' => Courses::class,
        //     'choice_label' => function (Courses $courses) {
        //         return sprintf('%s', $courses->getTitle());
        //     },
        //     'placeholder' => '-- Select Course --',
        //     'attr' => ['class' => 'form-select']
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reviews::class,
        ]);
    }
}
