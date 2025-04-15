<?php

namespace App\Form;

use App\Entity\Terrain;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TerrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du terrain',
                'required' => true,
            ])
            ->add('typeSurface', TextType::class, [
                'label' => 'Type de surface',
                'required' => true,
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Localisation',
                'required' => true,
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (en DT)',
                'required' => true,
            ])
            ->add('imageTer', FileType::class, [
                'label' => 'Image du terrain',
                'mapped' => false,
                'required' => false,
            ])
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'Propriétaire',
                'choice_label' => 'id', 
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Terrain::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
