<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class, array('label' => 'Гарчиг'))
            ->add('bodys')
            ->add('picture', FileType::class,array('label' => 'Зураг') )
            ->add('types')
            ->add('status',ChoiceType::class, [
                'choices' => [
                    'Тийм' => 1,
                    'Үгүй' => 0,
                ],
                'multiple' => false,
                'label' => 'Хэрэглэгчдэд харагдах эсэх'])
//            ->add('views')
            ->add('date', DateType::class,[
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('comments',TextType::class, array('label' => 'Товч тайлбар'))
            ->add('sort',NumberType::class, array('label' => 'Эрэмбэ'))
//            ->add('who')
//            ->add('createdDate')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
