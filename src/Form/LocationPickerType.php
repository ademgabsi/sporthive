<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationPickerType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr'] = array_merge($view->vars['attr'], [
            'data-latitude' => $options['default_latitude'],
            'data-longitude' => $options['default_longitude'],
            'data-zoom' => $options['default_zoom'],
            'data-map-type' => $options['map_type'],
            'data-enable-search' => $options['enable_search'] ? 'true' : 'false',
            'data-draggable-marker' => $options['draggable_marker'] ? 'true' : 'false',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'default_latitude' => 36.8065,  // Default to Tunisia
            'default_longitude' => 10.1815,
            'default_zoom' => 13,
            'map_type' => 'standard',       // standard, satellite, terrain
            'enable_search' => true,
            'draggable_marker' => true,
            'attr' => [
                'class' => 'location-picker-field',
            ],
        ]);
    }

    public function getParent()
    {
        return \Symfony\Component\Form\Extension\Core\Type\TextType::class;
    }
}
