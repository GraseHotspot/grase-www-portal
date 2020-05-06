<?php

namespace App\Util;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Override SymfonyStyle to add our timestamps
 */
class GraseConsoleStyle extends SymfonyStyle
{
    /**
     * Prepends a timestamp to all messages and then sends it to the parent to output the blocks
     *
     * {@inheritdoc}
     */
    public function block($messages, $type = null, $style = null, $prefix = ' ', $padding = false, $escape = true)
    {
        $messages = \is_array($messages) ? array_values($messages) : [$messages];
        $messages = array_map(function ($message) {
            return date('[Y-m-d H:i:s] ') . $message;
        }, $messages);
        parent::block($messages, $type, $style, $prefix, $padding, $escape);
    }

    /**
     * Prepends a timestamp and then forwards to parent text
     *
     * {@inheritdoc}
     */
    public function text($message)
    {
        $messages = \is_array($message) ? array_values($message) : [$message];
        $messages = array_map(function ($message) {
            return date('[Y-m-d H:i:s] ') . $message;
        }, $messages);
        parent::text($messages);
    }
}
