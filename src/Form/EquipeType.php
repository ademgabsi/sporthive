<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'équipe',
                'attr' => ['class' => 'form-control']
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => ['class' => 'form-control']
            ])
            ->add('annee_fondation', DateType::class, [
                'label' => 'Année de fondation',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('stade', TextType::class, [
                'label' => 'Nom du stade',
                'attr' => ['class' => 'form-control']
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo de l\'équipe',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG ou GIF)',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2Mo'
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => function(Utilisateur $utilisateur) {
                    return $utilisateur->getNom() . ' ' . $utilisateur->getPrenom() . ' (ID: ' . $utilisateur->getId() . ')';
                },
                'label' => 'Utilisateur',
                'placeholder' => 'Sélectionnez un utilisateur',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
