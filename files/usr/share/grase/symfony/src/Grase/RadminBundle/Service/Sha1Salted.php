<?php
namespace Grase\RadminBundle\Service;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class Sha1Salted implements PasswordEncoderInterface
{

    public function encodePassword($raw, $salt = null)
    {
        $SALT_LENGTH = 9;
        if ($salt === null) {
            $salt = substr(md5(uniqid(rand(), true)), 0, $SALT_LENGTH);
        } else {
            $salt = substr($salt, 0, $SALT_LENGTH);
        }

        return $salt . sha1($salt . $raw);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        //var_dump($encoded);
        //exit(1);
        return $encoded === $this->encodePassword($raw, $encoded);
    }

}
