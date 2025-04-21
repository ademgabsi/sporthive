<?php

namespace App\Form;

use App\Entity\Assurance;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssuranceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Duree', null, [
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('typeDeCouverture', null, [
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('dateDebut', null, [
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('Statut', ChoiceType::class, [
                'choices' => [
                    'Active' => 'Active',
                    'En attente' => 'En attente',
                    'Expirée' => 'Expirée'
                ],
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('Conditions', null, [
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Assurance::class,
        ]);
    }
}
