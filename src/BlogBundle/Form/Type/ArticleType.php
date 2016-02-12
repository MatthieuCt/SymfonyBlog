<?php

namespace BlogBundle\Form\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('content', TextType::class)
            ->add('category', EntityType::class, array(
                'class' => 'BlogBundle:Category',
                'choice_label' => 'name',
            ))
            ->add('tags', EntityType::class, array(
                'class' => 'BlogBundle:Tag',
                'choice_label' => 'name',
            ))
            ->add('date', DateTimeType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
        ;
    }
}
