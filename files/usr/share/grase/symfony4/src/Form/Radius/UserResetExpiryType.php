<?php

namespace App\Form\Radius;

use App\Entity\UpdateUserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Form for resetting the expiry of a radius user
 */
class UserResetExpiryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $submitButtonLabel = "grase.form.user.submit.reset_expiry";
        $builder
            ->add('expiry', DateTimeType::class, [
                'disabled' => true,
                'widget' => 'single_text',
                'label' => 'grase.form.user.reset_expiry'
            ]);
        $builder
            ->add('save', SubmitType::class, ['label' => $submitButtonLabel]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UpdateUserData::class,
            ]
        );
    }
}
