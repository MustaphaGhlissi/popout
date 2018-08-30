<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 01/03/2018
 * Time: 17:22
 */

namespace App\Form;

use App\Entity\Event;
use App\Entity\Offer;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, array('label'=>'Descrpition','attr'=>['placeholder'=>'Entrez ici une description']))
            ->add('event', EntityType::class,
                array(
                    'label'=>'Evènement',
                    'class' => Event::class,
                    'choice_label' => 'name',
                    'placeholder'=>"-- Sélectionner un évènement --"
                ))
            ->add('startDate', TextType::class, array('label'=>'Date de début','attr'=>['placeholder'=>'Date de début']))
            ->add('startTime', TextType::class, array('label'=>'Heure de début','attr'=>['placeholder'=>'Heure de début']))
            ->add('endDate',TextType::class, array('label'=>'Date d\'expiration', 'attr'=>['placeholder'=>'Date d\'expiration']))
            ->add('endTime',TextType::class, array('label'=>'Heure d\'expiration', 'attr'=>['placeholder'=>'Heure d\'expiration']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Offer::class,
        ));
    }

    public function getBlockPrefix()
    {
        return 'form_offer';
    }
}