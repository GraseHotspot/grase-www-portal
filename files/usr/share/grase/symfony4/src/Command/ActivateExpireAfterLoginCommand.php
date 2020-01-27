<?php

namespace App\Command;

use App\Entity\Radius\Check;
use App\Entity\Radius\RadPostAuth;
use App\Entity\UpdateUserData;
use App\Util\GraseConsoleStyle;
use App\Util\SettingsUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This cron job activates a user's Expiry after the first login when they have an ExpireAfter
 * radcheck. This allows you to have a short expiry that only takes effect after first login.
 */
class ActivateExpireAfterLoginCommand extends Command
{
    protected static $defaultName = 'grase:cron:activateExpireAfterLogin';

    /**
     * @var SettingsUtils
     */
    private $settingsUtils;

    /** @var EntityManagerInterface */
    private $em;

    /** @var Logger */
    private $auditLogger;

    /** @var TranslatorInterface */
    private $translator;


    /**
     * ActivateExpireAfterLoginCommand constructor.
     *
     * @param SettingsUtils          $settingsUtils
     * @param EntityManagerInterface $entityManager
     * @param Logger                 $auditLogger
     * @param TranslatorInterface    $translator
     */
    public function __construct(SettingsUtils $settingsUtils, EntityManagerInterface $entityManager, Logger $auditLogger, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->settingsUtils = $settingsUtils;
        $this->em = $entityManager;
        $this->auditLogger = $auditLogger;
        $this->translator = $translator;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new GraseConsoleStyle($input, $output);

        $query = $this->em->createQueryBuilder()
            ->select('c', 'MIN(p.id)', 'MIN(p.authDate)')
            ->from(Check::class, 'c')
            ->join(RadPostAuth::class, 'p', Join::WITH, 'c.user = p.username')
            ->andWhere('p.reply = :accessAccept')
            ->setParameter('accessAccept', 'Access-Accept')
            ->andWhere('c.attribute = :expireAfter')
            ->setParameter('expireAfter', Check::GRASE_EXPIRE_AFTER)
            ->groupBy('c.user');

        foreach ($query->getQuery()->getResult() as $result) {
            /** @var Check $checkItem */
            $checkItem = $result[0];
            $firstLogin = strtotime($result[2]);
            $user = $checkItem->getUser();

            $userUpdateData = UpdateUserData::fromUser($user);
            $userUpdateData->expiry = new \DateTime("@" . strtotime($user->getExpireAfter(), $firstLogin));

            $this->auditLogger->info(
                'grase.cron.audit.activate.expireAfterLogin',
                ['user' => $user->getUsername(), 'expiry' => $userUpdateData->expiry->format('c')]
            );
            $io->success(
                $this->translator->trans(
                    'grase.cron.output.activate.expireAfterLogin',
                    [
                        'user' => $user->getUsername(),
                        'expiry' => $userUpdateData->expiry->format('c'),
                    ]
                )
            );

            $userUpdateData->updateUser($user, $this->em);

            // Now remove the Grase Expire After check item
            $this->em->remove($checkItem);
            $this->em->flush();
        }
    }
}
