<?php

namespace App\Form;

use App\Entity\Assurance;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Date', null, [
                'widget' => 'single_text',
            ])
            ->add('Description')
            ->add('Etat')
            ->add('Montantreclame', IntegerType::class)
            ->add('Montantrembourse', IntegerType::class)
            ->add('Documents')
            ->add('assurance', EntityType::class, [
                'class' => Assurance::class,
                'choice_label' => 'id',
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