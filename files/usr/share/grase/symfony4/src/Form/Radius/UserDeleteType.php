<?php

namespace App\Form\Radius;

use App\Entity\UpdateUserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form for resetting the expiry of a radius user
 */
class UserDeleteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $submitButtonLabel = 'grase.form.user.submit.delete';
        $builder
            ->add('username', HiddenType::class)
            ->add('delete', SubmitType::class, [
                'label' => $submitButtonLabel,
                /*'attr' => [
                    'onclick'=> "return confirm('Are you sure you want to delete this user?');",
                ],*/
            ]);
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
