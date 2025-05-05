<?php

namespace App\Form;

use App\Entity\Competition;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter competition name',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('Type', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., Tournament, League, Championship',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('Date', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control datepicker'],
                'label_attr' => ['class' => 'form-label'],
                'html5' => true,
            ])
            ->add('Heure', TimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control timepicker'],
                'label_attr' => ['class' => 'form-label'],
                'html5' => true,
            ])
            ->add('Description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Enter competition details...',
                ],
                'label_attr' => ['class' => 'form-label'],
                'required' => false,
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'placeholder' => 'Select a user',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Competition::class,
            'attr' => ['class' => 'competition-form'],
        ]);
    }
}