<<<<<<< HEAD
<?php

namespace App\Form;

use App\Entity\Assurance;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('Date', null, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('Description', null, [
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('Etat', ChoiceType::class, [
                'required' => false,
                'attr' => ['novalidate' => 'novalidate'],
                'choices' => [
                    'Active' => 'Active',
                    'En attente' => 'En attente',
                    'Expirée' => 'Expirée'
                ]
            ])
            ->add('Montantreclame', IntegerType::class, [
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
            ])
            ->add('Montantrembourse', IntegerType::class, [
                'required' => false,
                'attr' => ['novalidate' => 'novalidate']
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
                    'accept' => '.pdf'
                ]
            ])
            ->add('assurance', EntityType::class, [
                'class' => Assurance::class,
                'choice_label' => 'typeDeCouverture',
                'required' => false,
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
=======
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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Date', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime('now'), // Date d'aujourd'hui par défaut
                'label' => 'Date de réclamation'
            ])
            ->add('Description', TextareaType::class, [
                'label' => 'Description de la réclamation',
                'attr' => [
                    'placeholder' => 'Décrivez votre problème en détail...',
                    'rows' => 5
                ]
            ])
            ->add('Etat', TextType::class, [
                'data' => 'En cours', // État par défaut
                'label' => 'État de la réclamation',
                'attr' => [
                    'readonly' => true // Rend le champ en lecture seule pour les utilisateurs
                ]
            ])
            ->add('Montantreclame', IntegerType::class, [
                'label' => 'Montant réclamé (TND)',
                'attr' => [
                    'min' => 0,
                    'placeholder' => 'Entrez le montant réclamé'
                ]
            ])
            ->add('Montantrembourse', IntegerType::class, [
                'data' => 0, // Montant remboursé par défaut à 0
                'label' => 'Montant remboursé (TND)',
                'attr' => [
                    'readonly' => true, // Rend le champ en lecture seule pour les utilisateurs
                    'min' => 0
                ]
            ])
            ->add('Documents', TextType::class, [
                'label' => 'Documents justificatifs',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Références des documents joints'
                ]
            ])
            ->add('assurance', EntityType::class, [
                'class' => Assurance::class,
                'choice_label' => 'typeDeCouverture', 
                'label' => 'Votre assurance',
                'placeholder' => 'Sélectionnez votre type assurance'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
>>>>>>> gestionMatch
}