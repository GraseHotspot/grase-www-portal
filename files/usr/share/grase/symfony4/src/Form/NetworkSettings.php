<?php

namespace App\Form;

use App\Data\NetworkSettingsData;
use Symfony\Component\Form\AbstractType;
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
        dump($options['lan_nics']);
        $builder
            ->add('lanIpAddress', TextType::class, [
                'label' => 'grase.form.network-settings.lan-ip-address',
            ])
            ->add('lanNetworkMask', TextType::class, [
                'label' => 'grase.form.network-settings.lan-netmask',
            ])
            ->add('lanNetworkInterface', ChoiceType::class, [
                'label'   => 'grase.form.network-settings.lan-nic',
                'choices' => $options['lan_nics'],
            ])
            ->add('wanNetworkInterface', ChoiceType::class, [
                'label'   => 'grase.form.network-settings.wan-nic',
                'choices' => $options['wan_nics'],
            ])
            ->add('dnsServers', CollectionType::class, [
                'entry_type'   => TextType::class,
                'label'        => 'grase.form.network-settings.dns-servers',
                'allow_add'    => true,
                'allow_delete' => true,
                'delete_empty' => true,
            ])
            ->add('bogusNxDomains', CollectionType::class, [
                'entry_type'   => TextType::class,
                'label'        => 'grase.form.network-settings.bogus-nx-domains',
                'allow_add'    => true,
                'allow_delete' => true,
                'delete_empty' => true,
            ])
            ->add('submit', SubmitType::class)
            ;
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
