<?php

namespace Streamlabs\Bundle\TwitchBundle\Forms;

use Streamlabs\Entities\UserToStreamer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class AddFavoriteStreamerType
 * @package Streamlabs\Bundle\TwitchBundle\Forms
 */
class AddFavoriteStreamerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('streamer_name', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Your favorite streamer\'s name',
                    'required' => true
                ),
                'label' => 'Twitch Streamer Name',

            ))
            ->add('submit', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-info pull-right'),
                'label' => 'Save',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UserToStreamer::class,
            'validation_groups' => array('add_fav_streamer'),
        ));
    }
}