<?php


namespace App\Entity;

use App\Entity\Radius\Group;
use App\Entity\Radius\User;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserData
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $username;

    /**
     * @var string
     */
    public $comment;


    /**
     * @var string
     */
    public $password;


    /** @var Group */
    public $primaryGroup;


    /**
     * @var \DateTime|null
     */
    //public $expiry;

    /**
     * Create a UpdateUserData from an existing User entity
     * @param User $user
     * @return UpdateUserData
     */
    public static function fromUser(User $user): self
    {
        $updateUserData = new self();
        $updateUserData->username = $user->getUsername();
        //$updateUserData->password = $user->getPassword();
        $updateUserData->comment = $user->getComment();
        $updateUserData->primaryGroup = $user->getPrimaryGroup();
        //$updateUserData->expiry = $user->getExpiry();

        return $updateUserData;
    }

    /**
     * Write data back to a User entity with the updated data
     * @param User $user
     * @param ObjectManager $em
     */
    public function updateUser(User $user, ObjectManager $em)
    {
        $user->setComment($this->comment);
        $user->setPrimaryGroup($this->primaryGroup);
        // @TODO update the rest
        $em->persist($user);
        $em->flush();
    }
}
