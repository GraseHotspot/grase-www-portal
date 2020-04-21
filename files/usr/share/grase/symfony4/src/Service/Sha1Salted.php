<?php

namespace App\Service;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * Class to allow us to use our sha1salted password hashes. This should only be used for authenticating a user, and then
 * we should upgrade them to something better
 *
 * @deprecated
 */
class Sha1Salted implements PasswordEncoderInterface
{
    /**
     * @param string $raw
     * @param null   $salt
     *
     * @return string
     */
    public function encodePassword($raw, $salt = null)
    {
        $saltLength = 9;
        if (null === $salt) {
            $salt = substr(md5(uniqid(rand(), true)), 0, $saltLength);
        } else {
            $salt = substr($salt, 0, $saltLength);
        }

        return $salt . sha1($salt . $raw);
    }

    /**
     * @param string      $encoded
     * @param string      $raw
     * @param string|null $salt
     *
     * @return bool
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $encoded);
    }

    /**
     * Anytime we have a sha1salted password, it needs to be rehashed with something better
     *
     * @param string $encoded
     *
     * @return bool
     */
    public function needsRehash(string $encoded)
    {
        return true;
    }
}
