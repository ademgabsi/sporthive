<?php
namespace App\Form;

use App\Entity\Joueur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Equipe;

class JoueurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime',
                'label' => 'Date de naissance',
                'attr' => [
                    'class' => 'datepicker'
                ]
            ])
            ->add('photo', FileType::class, [
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
            ->add('equipe', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Joueur::class,
        ]);
    }
}
