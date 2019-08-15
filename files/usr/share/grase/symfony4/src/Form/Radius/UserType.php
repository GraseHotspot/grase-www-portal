<?php

namespace App\Form\Radius;

use App\Entity\Radius\Group;
use App\Entity\Radius\User;
use App\Entity\Radius\UserGroup;
use App\Entity\UpdateUserData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DomCrawler\Field\InputFormField;
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
        $disableEditUsername = true;
        if ($options['create']) {
            $disableEditUsername = false;
        }
        $builder
            ->add('username', null, [
                'disabled' => $disableEditUsername,
            ])
            ->add('comment')
            ->add('password', null, [
                'attr' => ['placeholder' => "Change the password"],
                'data' => '',
            ])
            ->add('primaryGroup', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'choice_value' => 'name',

            ])
            /*->add(
                'expiry',
                null,
                [
                    'attr' => ['placeholder' => 'grase.form.NoExpiry'],
                    'label' => 'grase.form.expiry'
                ]
            )*/
            ->add('save', SubmitType::class, ['label' => "Update Details"]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UpdateUserData::class,
                'create' => false,
            ]
        );

        $resolver->setRequired('create');
        $resolver->setAllowedTypes('create', 'boolean');
    }
}
