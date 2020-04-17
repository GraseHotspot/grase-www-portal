<?php

namespace App\Entity\Radmin;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="auth")
 * @ORM\Entity(repositoryClass="App\Entity\Radmin\UserRepository")
 */
class User implements UserInterface, EncoderAwareInterface, \Serializable //, ThemeUser
{
    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @ORM\Id
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25)
     */
    private $role;

    private $isActive;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->isActive = true;
    }

    /**
     * Get the Radmin Users username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get password encoder (lets us use existing sha1salted hashes until we upgrade them)
     *
     * @return string|null
     */
    public function getEncoderName()
    {
        if (strlen($this->password) === 49) {
            return 'sha1salted';
        }

        return null; // use the default encoder
    }

    /**
     * @return false|string|null
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return substr($this->password, 0, 9);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return [$this->role];
    }

    /**
     * @param string $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([
            //$this->id,
            $this->username,
            $this->password,
            // see section on salt below
            //$this->salt,
        ]);
    }

    /**
     * @see \Serializable::unserialize()
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            //$this->id,
            $this->username,
            $this->password,
            // see section on salt below
            //$this->salt
            ) = unserialize($serialized);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set accessLevel
     *
     * @param int $accessLevel
     *
     * @return User
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->username;
    }
}
