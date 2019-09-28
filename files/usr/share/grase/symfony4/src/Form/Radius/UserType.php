<?php

namespace App\Form\Radius;

use App\Entity\Radius\Group;
use App\Entity\Radius\User;
use App\Entity\Radius\UserGroup;
use App\Entity\UpdateUserData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DomCrawler\Field\InputFormField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class UserType
 * Form for creating/editing Radius users
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disableEditUsername = true;
        $passwordPlaceholder = "grase.form.user.change_password_placeholder";
        $submitButtonLabel = "grase.form.user.submit.update";
        $passwordRequired = false;
        if ($options['create']) {
            $disableEditUsername = false;
            $passwordPlaceholder = "grase.form.user.password_placeholder";
            $submitButtonLabel = "grase.form.user.submit.create";
            $passwordRequired = true;
        }
        $builder
            ->add('username', TextType::class, [
                'disabled' => $disableEditUsername,
            ])

            ->add('password', TextType::class, [
                'attr' => ['placeholder' => $passwordPlaceholder],
                'data' => '',
                'required' => $passwordRequired,
            ])
            ->add('comment')
            ->add('primaryGroup', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'choice_value' => 'name',
                'label' => 'grase.form.user.group.label',
                'help' => 'grase.form.user.group.help',

            ]);
            /*->add(
                'expiry',
                null,
                [
                    'attr' => ['placeholder' => 'grase.form.NoExpiry'],
                    'label' => 'grase.form.expiry'
                ]
            )*/
        $builder->add('dataLimit', DataLimitType::class, [
            'label' => 'grase.form.user.datalimit.title',
            'create' => $options['create'],
        ]);
        $builder->add('timeLimit', TimeLimitType::class, [
            'label' => 'grase.form.user.timelimit.title',
            'create' => $options['create'],
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
                'create' => false,
            ]
        );

        $resolver->setRequired('create');
        $resolver->setAllowedTypes('create', 'boolean');
    }
}
