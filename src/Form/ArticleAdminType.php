<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // on stocke les années à afficher dans le formulaire
        // pour le champ date de publication dans un tableau
        for($i=date('Y');$i>=1900; $i--){
            $years[] = $i;
        }
        $builder
            ->add('article', ArticleType::class, array(
                'data_class' => Article::class,
            ))
            ->add('user', EntityType::class, array(
                'class' => User::class,
                'choice_label' => 'username'
            ))
            ->add('date_publi', DateTimeType::class, array(
                'label'=>'Date de publication',
                'years'=>$years
            ))
        ;
    }

}
