<?php

namespace App\Form;
use App\Form\Type\LocationPickerType;
use App\Entity\Terrain;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\{
    NotBlank,
    Positive,
    Image
};
class TerrainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
               'label' => 'Nom du terrain',
                'attr' => ['placeholder' => 'Ex: Terrain Olympique'],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire'])

            ]        ])
            ->add('typeSurface', TextType::class, [
                'label' => 'Type de surface',
                'required' => true,
            ])
            ->add('localisation', LocationPickerType::class, [
                'label' => 'Localisation sur la carte',
                'required' => true,
                'default_latitude' => 36.8065,
                'default_longitude' => 10.1815,
                'default_zoom' => 13,
                'enable_search' => true,
                'draggable_marker' => true,
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
