<?php

namespace App\Util;

use App\Repository\SettingRepository;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class Formatting
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Modified from https://stackoverflow.com/a/2510459/682931
     *
     * @param int|null $bytes
     * @param int      $precision
     *
     * @return string
     */
    public function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB');

        if (!isset($bytes)) {
            return "";
        } // Unlimited needs to display as blank

        $bytes = max($bytes, 0);
        $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow   = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $numberFormatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);

        return $numberFormatter->format(round($bytes, $precision)).' '.$units[$pow];
    }
}
