<?php

namespace App\Form;

use App\Data\NetworkSettingsData;
use App\Util\GraseUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form for Network Settings
 */
class NetworkSettings extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $lanNicChoices = array_combine(array_keys($options['lan_nics']), array_keys($options['lan_nics']));
        $wanNicChoices = array_combine(array_keys($options['wan_nics']), array_keys($options['wan_nics']));

        $builder
            ->add('lanIpAddress', TextType::class, [
                'label' => 'grase.form.network-settings.lan-ip-address',
            ])
            ->add('lanNetworkMask', TextType::class, [
                'label' => 'grase.form.network-settings.lan-netmask',
            ])
            ->add('lanNetworkInterface', ChoiceType::class, [
                'label'   => 'grase.form.network-settings.lan-nic',
                'choices' => $lanNicChoices,
            ])
            ->add('wanNetworkInterface', ChoiceType::class, [
                'label'   => 'grase.form.network-settings.wan-nic',
                'choices' => $wanNicChoices,
            ])
            ->add('dnsServers', CollectionType::class, [
                'entry_type'     => TextType::class,
                'entry_options'  => ['label' => false, 'required' => false],
                'label'          => 'grase.form.network-settings.dns-servers',
                'allow_add'      => true,
                'allow_delete'   => true,
                'delete_empty'   => true,
                'error_bubbling' => false,
            ])
            ->add('bogusNxDomains', CollectionType::class, [
                'entry_type'    => TextType::class,
                'entry_options' => ['label' => false],
                'label'         => 'grase.form.network-settings.bogus-nx-domains',
                'allow_add'     => true,
                'allow_delete'  => true,
                'delete_empty'  => true,
            ])
            ->add('submit', SubmitType::class)
            ;

        // Do some magic to ensure we accept both mask formats
        $builder->get('lanNetworkMask')
            ->addModelTransformer(new CallbackTransformer(
                function ($value) {
                    return $value;
                },
                \Closure::fromCallable([GraseUtil::class, 'transformSubnetMask'])
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => NetworkSettingsData::class,
                'lan_nics'   => [],
                'wan_nics'   => [],
            ]
        );
    }
}
