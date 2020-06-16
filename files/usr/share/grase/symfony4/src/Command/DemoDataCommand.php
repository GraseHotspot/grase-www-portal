<?php

namespace App\Command;

use App\Entity\Radius\Check;
use App\Entity\Radius\Group;
use App\Entity\Radius\User;
use App\Entity\Setting;
use App\Util\SettingsUtils;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Command to take a normal database, and destroy it to create a database for demo purposes
 * Class DemoDataCommand
 */
class DemoDataCommand extends Command
{
    protected static $defaultName = 'grase:demodata';

    /** @var TranslatorInterface */
    private $translator;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var SettingsUtils */
    private $settingsUtils;

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var array Array of prepared userQueries for us to execute */
    private $renameUserQueries = [];

    /**
     * DemoData Command Constructor
     *
     * @param TranslatorInterface    $translator
     * @param EntityManagerInterface $entityManager
     * @param SettingsUtils          $settingsUtils
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, SettingsUtils $settingsUtils, UserPasswordEncoderInterface $encoder)
    {
        parent::__construct();
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->settingsUtils = $settingsUtils;
        $this->setDescription('Cleans up the data for use in demos');
        $this->encoder = $encoder;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // For all users, change the password
        /** @var \Doctrine\ORM\QueryBuilder $queryBuilder */
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder->update(Check::class, 'c')
            ->set('c.value', ':value')
            ->where('c.attribute = :attribute')
            ->setParameter('value', 'DEMOPASSWORD')
            ->setParameter('attribute', 'Cleartext-Password')
            ->getQuery();

        $output->writeln('Reset password: ' . $query->execute());

        // Change all the comments
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $query = $queryBuilder->update(User::class, 'u')
            ->set('u.comment', ':comment')
            ->where('u.comment IS NOT NULL')
            ->setParameter('comment', 'Demo Comment')
            ->getQuery();

        $output->writeln('Change comments: ' . $query->execute());

        /**
         * For all MAC addresses, modify them (lots of places). This needs to be SQL as Username is a primary key that
         * should never change
         */
        $this->entityManager->createQueryBuilder();

        /**
         * Some more tables we could update, or just truncate are:
         *  * radreply
         *  * radpostauth
         */

        /** @var Connection $db */
        $db = $this->entityManager->getConnection();
        $this->renameUserQueries['updateUserQuery'] = $db->prepare(
            'UPDATE users SET UserName = :newName WHERE UserName = :oldName'
        );
        $this->renameUserQueries['updateCheckQuery'] = $db->prepare(
            'UPDATE radcheck SET UserName = :newName WHERE UserName = :oldName'
        );
        $this->renameUserQueries['updateRadReplyQuery'] = $db->prepare(
            'UPDATE radreply SET UserName = :newName WHERE UserName = :oldName'
        );
        $this->renameUserQueries['updateRadUserGroupQuery'] = $db->prepare(
            'UPDATE radusergroup SET UserName = :newName WHERE UserName = :oldName'
        );
        $this->renameUserQueries['updateGroupQuery'] = $db->prepare(
            'UPDATE radusergroup SET UserName = :newName WHERE UserName = :oldName'
        );
        $this->renameUserQueries['updateRadpostauthQuery'] = $db->prepare(
            'UPDATE radpostauth SET UserName = :newName WHERE UserName = :oldName'
        );
        $this->renameUserQueries['updateRadacctQuery'] = $db->prepare(
            'UPDATE radacct SET UserName = :newName WHERE UserName = :oldName'
        );
        $this->renameUserQueries['updateMtotacctQuery'] = $db->prepare(
            'UPDATE mtotacct SET UserName = :newName WHERE UserName = :oldName'
        );
        $this->renameUserQueries['updateBatchQuery'] = $db->prepare(
            'UPDATE batch SET UserName = :newName WHERE UserName = :oldName'
        );

        $macUsers = $this->entityManager->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.username LIKE \'__-__-__-__-__-__\'')
            ->getQuery()
            ->execute();

        $mtotacctMacUsers = $db->query(
            'SELECT DISTINCT UserName from mtotacct WHERE UserName LIKE \'__-__-__-__-__-__\''
        )->fetchAll();

        $db->beginTransaction();
        $db->query('SET FOREIGN_KEY_CHECKS=0');

        foreach (array_column($mtotacctMacUsers, 'UserName') as $mac) {
            if (substr($mac, 0, 2) === 'XX') {
                continue;
            }
            $newUsername = preg_replace('/(..)-(..)-(..)-(..)-(..)-(..)/', 'XX-$1-XX-$6-XX-$3', $mac);
            $this->renameUser($output, $mac, $newUsername);
        }

        /** @var User $macUser */
        foreach ($macUsers as $macUser) {
            $oldUsername = $macUser->getUsername();
            if (substr($oldUsername, 0, 2) === 'XX') {
                continue;
            }
            $newUsername = preg_replace('/(..)-(..)-(..)-(..)-(..)-(..)/', 'XX-$1-XX-$6-XX-$3', $oldUsername);
            $this->renameUser($output, $oldUsername, $newUsername);
        }

        /** @var Group $autoCreateGroup */
        $autoCreateGroup = $this->entityManager->getRepository(Group::class)->findOneBy(
            ['name' => $this->settingsUtils->getSettingValue(Setting::AUTO_CREATE_GROUP)]
        );
        foreach ($autoCreateGroup->getUsergroups() as $userGroupMapping) {
            $oldUsername = $userGroupMapping->getUser()->getUsername();
            if (strlen($oldUsername) === 10) {
                continue;
            }
            // Just need to make it unique
            $newUsername = substr(hash('adler32', $oldUsername), 2, 6)
                . substr(hash('crc32', $oldUsername), 4, 4);
            $this->renameUser($output, $oldUsername, $newUsername);
        }

        $db->query('SET FOREIGN_KEY_CHECKS=1');
        $db->commit();

        // Truncate the adminlog to protect the innocent
        $db->query('TRUNCATE adminlog');

        // Create a new guest user for the demo
        $guestUser = new App\Entity\Radmin\User();
        $guestUser->setUsername('guest');
        $guestUser->setPassword($this->encoder->encodePassword($user, 'guest'));
        $this->entityManager->persist($guestUser);
        $this->entityManager->flush();

        // TODO set setting 'demosite' to true

        // TODO Clear audit log
    }

    /**
     * Executes the prepared statements to rename a user. Must happen in a dodgy transaction due to FKs as we're
     * updating primary keys which we shouldn't be doing
     *
     * @param OutputInterface $output
     * @param string          $oldUsername
     * @param string          $newUsername
     */
    private function renameUser($output, $oldUsername, $newUsername)
    {
        $output->writeln("Renaming $oldUsername to $newUsername");
        foreach ($this->renameUserQueries as $renameQuery) {
            $renameQuery->execute([':oldName' => $oldUsername, ':newName' => $newUsername]);
        }
    }
}
