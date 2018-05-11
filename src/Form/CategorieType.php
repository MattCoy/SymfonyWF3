<?php
/**
 * Created by PhpStorm.
 * User: Matthieu
 * Date: 11/05/2018
 * Time: 16:18
 */
//src/Form/CategorieType.php
namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'enregistrer'))
        ;
    }
}