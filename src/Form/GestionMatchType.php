<?php

namespace App\Form;

use App\Entity\GestionMatch;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GestionMatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('type', null, [
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('description', null, [
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('date', null, [
                'widget' => 'single_text',
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('heure', TimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('qrCode', HiddenType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GestionMatch::class,
        ]);
    }
}
