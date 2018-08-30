<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 20/02/2018
 * Time: 11:38
 */

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('username')
            ->add('firstName', null, array('label'=>'Nom','attr'=>['placeholder'=>'Nom de l\'utilisateur']))
            ->add('lastName', null, array('label'=>'Prénom','attr'=>['placeholder'=>'Prénom de l\'utilisateur']))
            ->add('phone', null, array('label'=>'Numéro de téléphone','attr'=>['placeholder'=>'Numéro de téléphone']))
            ->add('password', PasswordType::class, array('label'=>'Mot de passe', 'attr'=>['placeholder'=>'Mot de passe']))
            ->add('email',null, array('label'=>'Adresse e-mail', 'attr'=>['placeholder'=>'Adresse e-mail']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_user';
    }
}