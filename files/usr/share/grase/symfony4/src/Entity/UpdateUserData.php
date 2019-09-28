<?php


namespace App\Entity;

use App\Entity\Radius\Check;
use App\Entity\Radius\Group;
use App\Entity\Radius\User;
use App\Entity\Radius\UserGroup;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UpdateUserData
{
    /**
     * @var string
     *
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

    public $dataLimit = ['dataLimitDropdown' => 'inherit', 'dataLimitCustom' => null];
    public $timeLimit = ['timeLimitDropdown' => 'inherit', 'timeLimitCustom' => null];

    /**
     * @var \DateTime|null
     */
    //public $expiry;

    /**
     * Create a UpdateUserData from an existing User entity
     * @param User $user
     *
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
        // Existing users don't use the dropdowns
        $updateUserData->dataLimit['dataLimitDropdown'] = null;
        $updateUserData->timeLimit['timeLimitDropdown'] = null;
        if ($user->getDataLimit() !== null) {
            $updateUserData->dataLimit['dataLimitCustom'] = $user->getDataLimitMebibyte();
        }
        if ($user->getTimeLimitSeconds() !== null) {
            $updateUserData->timeLimit['timeLimitCustom'] = $user->getTimeLimitMinutes();
        }

        return $updateUserData;
    }

    /**
     * @Assert\Callback
     */
    public function validateFields(ExecutionContextInterface $context)
    {
        if (null !== $this->dataLimit['dataLimitDropdown'] && null !== $this->dataLimit['dataLimitCustom']) {
            $context->addViolation("grase.form.user.datalimit.both_selected_error");
        }
        if (null !== $this->timeLimit['timeLimitDropdown'] && null !== $this->timeLimit['timeLimitCustom']) {
            $context->addViolation("grase.form.user.timelimit.both_selected_error");
        }
    }

    /**
     * Write data back to a User entity with the updated data
     * @param User          $user
     * @param ObjectManager $em
     */
    public function updateUser(User $user, ObjectManager $em)
    {

        $user->setComment($this->comment);
        $this->setPrimaryGroup($user, $em, $this->primaryGroup);

        $this->setDataLimit($user, $em, $this->dataLimitToBytes());
        $this->setTimeLimit($user, $em, $this->timeLimitToSeconds());
        // @TODO update the rest
        $em->persist($user);
        $em->flush();
    }



    private function dataLimitToBytes()
    {
        if ($this->dataLimit['dataLimitDropdown'] === 'inherit') {
            return null;
        }
        if ($this->dataLimit['dataLimitDropdown'] !== null) {
            return $this->dataLimit['dataLimitDropdown'];
        }

        // Custom Data Limit is in MiBs and we want Bytes
        if (null !== $this->dataLimit['dataLimitCustom']) {
            return (int) $this->dataLimit['dataLimitCustom'] * 1024 * 1024;
        }

        return null;
    }

    private function setDataLimit(User $user, ObjectManager $em, $bytes)
    {
        $dataLimitCheck = $user->getDataLimitCheck();
        if (null === $bytes && $dataLimitCheck) {
            // We just need to remove the check
            $em->remove($dataLimitCheck);

            return;
        }
        if (null === $bytes) {
            // We don't have a check already, so nothing to do;
            return;
        }
        if (!$dataLimitCheck) {
            // We need to create a check
            $dataLimitCheck = new Check();
            $dataLimitCheck->setAttribute('Max-Octets');
            $dataLimitCheck->setUser($user);
            $dataLimitCheck->setOp(':=');
        }
        // Just set the check we have
        $dataLimitCheck->setValue((string) $bytes);
        $em->persist($dataLimitCheck);
    }

    private function timeLimitToSeconds()
    {
        if ($this->timeLimit['timeLimitDropdown'] === 'inherit') {
            return null;
        }
        if ($this->timeLimit['timeLimitDropdown'] !== null) {
            return $this->timeLimit['timeLimitDropdown'];
        }

        // Custom Data Limit is in Minutes and we want Seconds
        if ($this->timeLimit['timeLimitCustom'] !== null) {
            return (int) $this->timeLimit['timeLimitCustom'] * 60;
        }

        return null;
    }

    private function setTimeLimit(User $user, ObjectManager $em, $seconds)
    {
        $timeLimitCheck = $user->getTimeLimitCheck();
        if (null === $seconds && $timeLimitCheck) {
            // We just need to remove the check
            $em->remove($timeLimitCheck);

            return;
        }
        if (null === $seconds) {
            // We don't have a check already, so nothing to do;
            return;
        }
        if (!$timeLimitCheck) {
            // We need to create a check
            $timeLimitCheck = new Check();
            $timeLimitCheck->setAttribute('Max-All-Session');
            $timeLimitCheck->setUser($user);
            $timeLimitCheck->setOp(':=');
        }
        // Just set the check we have
        $timeLimitCheck->setValue((string) $seconds);
        $em->persist($timeLimitCheck);
    }

    private function setPrimaryGroup(User $user, ObjectManager $em, $group)
    {
        $primaryUserGroup = $user->getPrimaryGroup();
        if (!$primaryUserGroup) {
            $primaryUserGroup = new UserGroup();
            $primaryUserGroup->setUser($user);
        }
        $primaryUserGroup->setGroup($group);
        $em->persist($primaryUserGroup);
    }
}
