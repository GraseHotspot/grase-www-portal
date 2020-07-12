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
     * @param SettingsUtils $settingsUtils
     */
    public function __construct(SettingsUtils $settingsUtils)
    {
        $this->settingsUtils = $settingsUtils;
        $this->systemUtils = new SystemUtils();
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
            // TODO Update network data

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'grase.user.save_success.%username%',
                    ['%username%' => $user->getUsername()]
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
