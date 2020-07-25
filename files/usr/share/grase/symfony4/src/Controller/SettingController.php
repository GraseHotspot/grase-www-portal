<?php

namespace App\Controller;

use App\Data\NetworkSettingsData;
use App\Form\NetworkSettings;
use App\Util\SettingsUtils;
use App\Util\SystemUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Settings routes
 *
 * @Route("/settings", name="grase_settings_")
 */
class SettingController extends AbstractController
{
    /**
     * @var SettingsUtils
     */
    private $settingsUtils;

    /** @var SystemUtils */
    private $systemUtils;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param SettingsUtils       $settingsUtils
     * @param TranslatorInterface $translator
     */
    public function __construct(SettingsUtils $settingsUtils, TranslatorInterface $translator)
    {
        $this->settingsUtils = $settingsUtils;
        $this->systemUtils = new SystemUtils();
        $this->translator = $translator;
    }

    /**
     * @Route("/network", name="network")
     *
     * @IsGranted("ROLE_SUPERADMIN")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function networkSettingsAction(Request $request)
    {
        $networkSettingsData = new NetworkSettingsData($this->settingsUtils);
        $form = $this->createForm(NetworkSettings::class, $networkSettingsData, [
            'lan_nics' => $this->systemUtils->getPotentialLanNetworkInterfaces(),
            'wan_nics' => $this->systemUtils->getPotentialWanNetworkInterfaces(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Update network data
            $networkSettingsData->save();

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'grase.network-settings.save_success'
                )
            );

            return $this->redirectToRoute('grase_settings_network');
        }

        return $this->render(
            'network_settings.html.twig',
            [
                'networkSettingsForm' => $form->createView(),
            ]
        );
    }
}
