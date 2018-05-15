<?php
/**
 * Created by PhpStorm.
 * User: Matthieu
 * Date: 11/05/2018
 * Time: 19:07
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label'=>'Titre de l\'article'))
            ->add('content', TextareaType::class, array('label'=>'Contenu de l\'article'))
            ->add('date_publi', DateTimeType::class, array('label'=>'Date de publication'))
            ->add('save', SubmitType::class, array('label' => 'enregistrer',
                                                                'attr' => ['class' => 'btn btn-primary']))
        ;
    }
}