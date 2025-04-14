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

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('ville')
            ->add('Annee_fondation', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime', // <--- ici on précise que Symfony doit convertir en objet DateTime
            ])
            
            
            ->add('stade')
            ->add('logo', FileType::class, [
                'label' => 'Image Joueur',
                'mapped' => false, // Clé ! Ne lie pas directement à l'entité
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La photo est obligatoire.']),
                    new Assert\File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                    ]),
                ],
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
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
