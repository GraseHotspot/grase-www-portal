<?php

namespace App\Form\Radius;

use App\Entity\Radius\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('comment')
            ->add('password')
            ->add('primaryGroup')
            ->add(
                'expiry',
                null,
                [
                    'attr' => ['placeholder' => 'grase.form.NoExpiry'],
                    'label' => 'grase.form.expiry'
                ]
            )
            ->add('save', SubmitType::class);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class
            )
        );
    }
}
