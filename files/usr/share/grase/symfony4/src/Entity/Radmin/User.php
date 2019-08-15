<?php
// src/AppBundle/Entity/User.php
namespace App\Entity\Radmin;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;

//use Avanzu\AdminThemeBundle\Model\UserInterface as ThemeUser;

/**
 * @ORM\Table(name="auth")
 * @ORM\Entity(repositoryClass="App\Entity\Radmin\UserRepository")
 */
class User implements UserInterface, EncoderAwareInterface, \Serializable //, ThemeUser
{
    /**
     * Column(type="integer")

     * GeneratedValue(strategy="AUTO")
     */


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

    public function __construct()
    {
        $this->isActive = true;
        // may not be needed, see section on salt below
// $this->salt = md5(uniqid(null, true));
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEncoderName()
    {
        if (strlen($this->password) == 49) {
            return 'sha1salted';
        }

        return null; // use the default encoder
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return substr($this->password, 0, 9);
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return [$this->role];
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            //$this->id,
            $this->username,
            $this->password,
            // see section on salt below
            //$this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
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
     * @return integer
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
     * @param integer $accessLevel
     *
     * @return User
     */
    public function setAccessLevel($accessLevel)
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    public function getAvatar()
    {
        return null;
    }
    public function getName()
    {
        return $this->username;
    }
    public function getMemberSince()
    {
        return null;
    }
    public function isOnline()
    {
        return true;
    }
    public function getIdentifier()
    {
        return null;
    }
    public function getTitle()
    {
        return $this->username;
    }
}
