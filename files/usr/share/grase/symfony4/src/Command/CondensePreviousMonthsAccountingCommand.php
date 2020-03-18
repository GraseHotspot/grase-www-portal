<?php

namespace App\Command;

use App\Entity\Radius\Check;
use App\Util\GraseConsoleStyle;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Cron task to compact radius accounting rows (radacct) into a summary table (mtotacct)
 */
class CondensePreviousMonthsAccountingCommand extends Command
{
    protected static $defaultName = 'grase:cron:condensePreviousMonthsAccounting';

    /** @var EntityManagerInterface */
    private $em;

    /** @var Logger */
    private $auditLogger;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslatorInterface */
    private $translator;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Logger                 $auditLogger
     * @param LoggerInterface        $logger
     * @param TranslatorInterface    $translator
     */
    public function __construct(EntityManagerInterface $entityManager, Logger $auditLogger, LoggerInterface $logger, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->em = $entityManager;
        $this->auditLogger = $auditLogger;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Condense Previous Months Accounting');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new GraseConsoleStyle($input, $output);

        // Find the months present in radacct
        /** @var \DateTime[] $accountingMonths */
        $accountingMonths = $this->getAccountingMonths();

        // For each month that has accounting data, older than 2 months
        foreach ($accountingMonths as $startAccountingMonth) {
            $endAccountingMonth = (clone $startAccountingMonth)->modify('first day of next month');

            // Setup mtotaccttmp
            $this->setupAndCleanTempTable();

            // summarise radacct rows into mtotaccttmp for this accounting month
            $tempAccountingRows = $this->populateTempAccountingTable($startAccountingMonth, $endAccountingMonth);
            if (0 === $tempAccountingRows) {
                // If there wasn't anything for that month, continue (but we should never get here, we only process months with data)
                // @ TODO log this? Throw an exception
                continue;
            }

            // Update the radchecks from the now summarised data
            $this->updateCheckMaxAllSession($startAccountingMonth);
            $this->updateCheckMaxOctets($startAccountingMonth);

            // Move the summarised data from the temp table to the monthly archive table
            $usersSummarised = $this->moveTempAccountingToArchive($startAccountingMonth);

            $this->auditLogger->info(
                'grase.cron.audit.condensePreviousMonths',
                ['month' => $startAccountingMonth->format('Y-m'), 'condensedUsers' => $usersSummarised]
            );
            $io->success(
                $this->translator->trans(
                    'grase.cron.output.condensePreviousMonths',
                    ['month' => $startAccountingMonth->format('Y-m'), 'condensedUsers' => $usersSummarised]
                )
            );

            // Cleanup the summarised radacct rows for the month, they have all been summarised successfully now
            $deletedRadacctRows = $this->removeAccountingData($startAccountingMonth, $endAccountingMonth);

            $this->auditLogger->info(
                'grase.cron.audit.condensePreviousMonths.cleanup',
                ['month' => $startAccountingMonth->format('Y-m'), 'deletedRows' => $deletedRadacctRows]
            );
            $io->note(
                $this->translator->trans(
                    'grase.cron.output.condensePreviousMonths.cleanup',
                    ['month' => $startAccountingMonth->format('Y-m'), 'deletedRows' => $deletedRadacctRows]
                )
            );

            // Cleanup
            $this->setupAndCleanTempTable();
        }
    }

    /**
     * Update radcheck rows to reflect the now summarised accounting rows for Max-Octets (Data based limits)
     *
     * @param \DateTime $startAccountingMonth
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function updateCheckMaxOctets(\DateTime $startAccountingMonth)
    {
        // Query to update radcheck rows to reflect the now summarised accounting rows
        $updateCheckMaxOctetsSql = '
        UPDATE radcheck, mtotaccttmp
        SET
            radcheck.value = GREATEST(
                CAST(radcheck.value AS SIGNED INTEGER) - (mtotaccttmp.InputOctets + mtotaccttmp.OutputOctets),
                0)
        WHERE radcheck.Attribute = :maxOctets
        AND radcheck.UserName = mtotaccttmp.UserName
        AND mtotaccttmp.AcctDate = :acctDate';

        $updateCheckMaxOctetsQuery = $this->em->getConnection()->prepare($updateCheckMaxOctetsSql);

        // Select and total all radacct data for month into mtotaccttmp
        $updateCheckMaxOctetsQuery->execute(
            [
                'maxOctets' => Check::MAX_OCTETS,
                'acctDate'  => $startAccountingMonth->format('Y-m-d'),
            ]
        );

        $updatedRows = $updateCheckMaxOctetsQuery->rowCount();

        return $updatedRows;
    }

    /**
     * Update radcheck rows to reflect the now summarised accounting rows for Max-All-Sessions (time based limits)
     *
     * @param \DateTime $startAccountingMonth
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function updateCheckMaxAllSession(\DateTime $startAccountingMonth)
    {
        // Query to update radcheck rows to reflect the now summarised accounting rows
        $updateCheckMaxAllSessionSql = '
        UPDATE radcheck, mtotaccttmp
        SET
            radcheck.value = GREATEST(
                CAST(radcheck.value AS SIGNED INTEGER) - mtotaccttmp.ConnTotDuration,
                0)
        WHERE radcheck.Attribute = :maxAllSession
        AND radcheck.UserName = mtotaccttmp.UserName
        AND mtotaccttmp.AcctDate = :acctDate';

        $updateCheckMaxAllSessionQuery = $this->em->getConnection()->prepare($updateCheckMaxAllSessionSql);

        // Select and total all radacct data for month into mtotaccttmp
        $updateCheckMaxAllSessionQuery->execute(
            [
                'maxAllSession' => Check::MAX_ALL_SESSION,
                'acctDate'      => $startAccountingMonth->format('Y-m-d'),
            ]
        );

        $updatedRows = $updateCheckMaxAllSessionQuery->rowCount();

        return $updatedRows;
    }

    /**
     * Move the summarised accounting rows from the temp table into the permanent monthly archive
     *
     * @param \DateTime $startAccountingMonth
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function moveTempAccountingToArchive(\DateTime $startAccountingMonth)
    {
        // Move the mtotaccttmp details into mtotacct
        // @TODO should we be do an insert/update instead so we ensure only 1 line per user per month?
        $moveTempAccountingSql = 'INSERT INTO mtotacct (
                    UserName,
                    AcctDate,
                    ConnNum,
                    ConnTotDuration, 
                    ConnMaxDuration, 
                    ConnMinDuration, 
                    InputOctets, 
                    OutputOctets, 
                    NASIPAddress
                    )
                    SELECT 
                    LOWER(UserName) AS UserName,
                    AcctDate, 
                    ConnNum, 
                    ConnTotDuration, 
                    ConnMaxDuration, 
                    ConnMinDuration, 
                    InputOctets, 
                    OutputOctets, 
                    NASIPAddress
                    FROM 
                    mtotaccttmp
                    WHERE AcctDate = :acctDate';

        $moveTempAccountingQuery = $this->em->getConnection()->prepare($moveTempAccountingSql);

        $moveTempAccountingQuery->execute(
            [
                'acctDate' => $startAccountingMonth->format('Y-m-d'),
            ]
        );

        $updatedRows = $moveTempAccountingQuery->rowCount();

        return $updatedRows;
    }

    /**
     * Delete from the radacct table the rows we have just summarised into the monthly table
     *
     * @param \DateTime $startAccountingMonth
     * @param \DateTime $endAccountingMonth
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function removeAccountingData(\DateTime $startAccountingMonth, \DateTime $endAccountingMonth)
    {
        // Query to remove the accounting data from the radacct table that we've now summarised
        $deleteFromRadacctSql = 'DELETE FROM radacct
            WHERE AcctStopTime >= :startAccountingMonth
              AND AcctStopTime < :endAccountingMonth';

        $deleteFromRadacctQuery = $this->em->getConnection()->prepare($deleteFromRadacctSql);

        // Select and total all radacct data for month into mtotaccttmp
        $deleteFromRadacctQuery->execute(
            [
                'startAccountingMonth' => $startAccountingMonth->format('Y-m-d H:i:s'),
                'endAccountingMonth'   => $endAccountingMonth->format('Y-m-d H:i:s'),
            ]
        );

        $deletedRows = $deleteFromRadacctQuery->rowCount();

        return $deletedRows;
    }

    /**
     * Summarise the radius accounting rows for the selected month into the temp table so we can
     * process them for moving into the monthly archive table
     *
     * @param \DateTime $startAccountingMonth
     * @param \DateTime $endAccountingMonth
     *
     * @return int
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function populateTempAccountingTable(\DateTime $startAccountingMonth, \DateTime $endAccountingMonth)
    {
        // Query to Select and total all radacct data for month into mtotaccttmp
        $intoMtotaccttmpSql = 'INSERT INTO mtotaccttmp
                             (UserName,
                             AcctDate,
                             ConnNum,
                             ConnTotDuration,
                             ConnMaxDuration,
                             ConnMinDuration,
                             InputOctets,
                             OutputOctets,
                             NASIPAddress)
                             SELECT LOWER(UserName) AS UserName,
                             :acctDate,
                             COUNT(RadAcctId),
                             SUM(AcctSessionTime),
                             MAX(AcctSessionTime),
                             MIN(AcctSessionTime),
                             SUM(AcctInputOctets),
                             SUM(AcctOutputOctets),
                             NASIPAddress
                             FROM radacct
                             WHERE AcctStopTime >= :startAccountingMonth
                             AND AcctStopTime < :endAccountingMonth
                             GROUP BY UserName,NASIPAddress';
        $intoMtotaccttmpQuery = $this->em->getConnection()->prepare($intoMtotaccttmpSql);

        // Select and total all radacct data for month into mtotaccttmp
        $intoMtotaccttmpQuery->execute(
            [
                'acctDate'             => $startAccountingMonth->format('Y-m-d'),
                'startAccountingMonth' => $startAccountingMonth->format('Y-m-d H:i:s'),
                'endAccountingMonth'   => $endAccountingMonth->format('Y-m-d H:i:s'),
            ]
        );

        $insertedRows = $intoMtotaccttmpQuery->rowCount();

        if ($insertedRows) {
            // Log/audit
        }

        return $insertedRows;
    }

    /**
     * Ensure the mtotaccttmp table exists, and truncate it so we can start with a clean table before trying to
     * summarise the radius accounting rows for old months
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function setupAndCleanTempTable()
    {
        // Ensure the table exists
        $this->em->getConnection()->exec('CREATE TABLE IF NOT EXISTS mtotaccttmp LIKE mtotacct');

        // Truncate the table
        $this->em->getConnection()->exec('TRUNCATE mtotaccttmp');
    }

    /**
     * Get any month with radacct rows older than 2 months. We need the last 2 months to allow
     * rolling data limits (x Mb/month) to work correctly.
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getAccountingMonths()
    {
        // Find the months present in radacct
        // select DISTINCT EXTRACT(YEAR_MONTH FROM AcctStopTime) from radacct;
        // SELECT DISTINCT DATE_FORMAT(AcctStopTime, '%Y-%m') FROM radacct
        $query = $this->em->getConnection()
            ->query('SELECT DISTINCT DATE_FORMAT(AcctStopTime, \'%Y-%m\') AS AccountingMonths FROM radacct');
        $results = $query->fetchAll(FetchMode::COLUMN);

        $results[] = '2020-01';
        $results[] = '2019-11';

        // Turn the months into DateTime objects (1st of the month)
        $months = array_map(
            function ($monthString) {
                return new \DateTime("$monthString-01 00:00:00");
            },
            $results
        );

        // Filter out current month and last month (we keep upto 2 months at a time)
        return array_filter(
            $months,
            function ($month) {
                return $month < new \DateTime('midnight first day of last month');
            }
        );
    }
}
