<?php

namespace App\Form;

use App\Entity\Chapter;
use App\Entity\Lesson;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('contents', TextType::class)
            ->add('duration', TimeType::class)
            ->add('chapter', EntityType::class, [
                'mapped' => false,
                'class' => Chapter::class,
                'choice_label' => function (Chapter $chapter) {
                    return sprintf('%s', $chapter->getTitle());
                },
                'placeholder' => '-- Select Chapter --',
                'attr' => ['class' => 'form-select']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
