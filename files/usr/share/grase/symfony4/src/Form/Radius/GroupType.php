<?php

namespace App\Form\Radius;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GroupType
 * Form for editing/creating Radius Groups
 */
class GroupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add(
                'expiry',
                null,
                [
                    'attr'  => ['placeholder' => 'grase.form.NoExpiry'],
                    'label' => 'grase.form.expiry',
                ]
            )
            ->add('expireAfter')
            ->add('maxOctets', null, ['label' => 'grase.form.defaultDataLimit'])
            ->add('maxSeconds', null, ['label' => 'grase.form.defaultTimeLimit'])
            ->add('comment')
            ->add('save', SubmitType::class);

        $builder->get('maxOctets')
            ->addModelTransformer(
                new CallbackTransformer(
                    // Transform Octets to Megabytes
                    function ($octets) {
                        return $octets === null ? null : $octets / 1024 / 1024;
                    },
                    function ($megabytes) {
                        return $megabytes === null ? null : $megabytes * 1024 * 1024;
                    }
                )
            );

        $builder->get('maxSeconds')
            ->addModelTransformer(
                new CallbackTransformer(
                    // Transform Seconds to Minutes
                    function ($seconds) {
                        return $seconds === null ? null : $seconds / 60;
                    },
                    function ($minutes) {
                        return $minutes === null ? null : $minutes * 60;
                    }
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'App\Entity\Radius\Group',
            ]
        );
    }
}
