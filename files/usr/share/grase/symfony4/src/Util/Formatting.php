<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class Formatting
 * Utilities for formatting numbers/curency etc in a locale proper way
 */
class Formatting
{
    /** @var RequestStack */
    private $requestStack;

    /**
     * Formatting constructor.
     *
     * @param RequestStack $requestStack
     */
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
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB'];

        if (!isset($bytes)) {
            return '';
        } // Unlimited needs to display as blank

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $numberFormatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
        // TODO we may be able to use the new international formatter to do this instead of all this work to get the locale

        return $numberFormatter->format(round($bytes, $precision)) . ' ' . $units[$pow];
    }
}
