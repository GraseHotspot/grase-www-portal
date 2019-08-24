<?php


namespace App\Util;

use App\Entity\AuditLog;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Class AuditLogHandler
 * Shamelessly copied from https://nehalist.io/logging-events-to-database-in-symfony/
 */
class AuditLogHandler extends AbstractProcessingHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * MonologDBHandler constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Called when writing to our database
     * @param array $record
     */
    protected function write(array $record)
    {
        $logEntry = new AuditLog();
        $logEntry->setMessage($record['message']);
        $logEntry->setLevel($record['level']);
        $logEntry->setLevelName($record['level_name']);
        $logEntry->setExtra($record['extra']);
        $logEntry->setContext($record['context']);

        if (!empty($record['extra']['token']['username'])) {
            $logEntry->setUsername($record['extra']['token']['username']);
        }

        $this->em->persist($logEntry);
        $this->em->flush();
    }
}