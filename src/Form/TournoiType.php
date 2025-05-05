<?php

namespace App\Form;

use App\Entity\Competition;
use App\Entity\Tournoi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TournoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('date_debut', null, [
                'widget' => 'single_text'
            ])
            ->add('date_fin', null, [
                'widget' => 'single_text'
            ])
            ->add('lieu')
            ->add('competition', EntityType::class, [
                'class' => Competition::class,
'choice_label' => 'Nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournoi::class,
        ]);
    }
}
