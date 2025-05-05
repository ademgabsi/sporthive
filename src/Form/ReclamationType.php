<?php

namespace App\Form;

use App\Entity\Assurance;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Date', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime('now'),
                'label' => 'Date de réclamation',
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('Description', TextareaType::class, [
                'label' => 'Description de la réclamation',
                'attr' => [
                    'placeholder' => 'Décrivez votre problème en détail...',
                    'rows' => 5,
                    'novalidate' => 'novalidate'
                ]
            ])
            ->add('Etat', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'En cours',
                    'Active' => 'Active',
                    'En attente' => 'En attente',
                    'Expirée' => 'Expirée'
                ],
                'data' => 'En cours',
                'label' => 'État de la réclamation',
                'attr' => [
                    'readonly' => true,
                    'novalidate' => 'novalidate'
                ]
            ])
            ->add('Montantreclame', IntegerType::class, [
                'label' => 'Montant réclamé (TND)',
                'attr' => [
                    'min' => 0,
                    'placeholder' => 'Entrez le montant réclamé',
                    'novalidate' => 'novalidate'
                ]
            ])
            ->add('Montantrembourse', IntegerType::class, [
                'data' => 0,
                'label' => 'Montant remboursé (TND)',
                'attr' => [
                    'readonly' => true,
                    'min' => 0,
                    'novalidate' => 'novalidate'
                ]
            ])
            ->add('Documents', FileType::class, [
                'label' => 'Document justificatif (PDF)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un document PDF valide',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'accept' => '.pdf',
                    'novalidate' => 'novalidate'
                ]
            ])
            ->add('assurance', EntityType::class, [
                'class' => Assurance::class,
                'choice_label' => 'typeDeCouverture',
                'label' => 'Votre assurance',
                'placeholder' => 'Sélectionnez votre type assurance',
                'attr' => ['novalidate' => 'novalidate']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}