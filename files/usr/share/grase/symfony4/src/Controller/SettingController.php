<?php

namespace App\Controller;

use App\Data\NetworkSettingsData;
use App\Entity\Setting;
use App\Form\NetworkSettings;
use App\Form\SettingType;
use App\Util\SettingsUtils;
use App\Util\SystemUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    /**
     * Show all the settings in a table so we can see the "hidden" settings.
     *
     * @Route("/advanced", name="advanced")
     *
     * @IsGranted("ROLE_SUPERADMIN")
     *
     * @return Response
     */
    public function advancedSettingsAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');

        return $this->render('advancedSettings.html.twig');
    }

    /**
     * "Advanced" edit any setting
     *
     * @Route("/advanced/{setting}/edit", name="advanced_edit", options={"expose"= true})
     *
     * @IsGranted("ROLE_SUPERADMIN")
     *
     * @param Setting $setting
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editAdvancedSettingAction(Setting $setting, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');

        $form = $this->createForm(SettingType::class, $setting);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $setting = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($setting);
            $em->flush();

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'grase.advancedSettings.edit.save_success.%setting%',
                    ['%setting%' => $setting->getName()]
                )
            );

            return $this->redirectToRoute('grase_settings_advanced');
        }

        return $this->render(
            'editAdvancedSetting.html.twig',
            [
                'settingForm' => $form->createView(),
            ]
        );
    }
}
