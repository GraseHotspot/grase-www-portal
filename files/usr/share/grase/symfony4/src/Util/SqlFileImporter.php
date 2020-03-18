<?php

namespace App\Util;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Special class for importing V3 backups (SQL dumps) into the Grase database
 */
class SqlFileImporter
{
    /** @var Connection */
    protected $connection;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface        $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->connection = $entityManager->getConnection();
        $this->logger = $logger;
    }

    /**
     * Takes a SQL file and imports it into our database
     *
     * @param string $filename
     *
     * @return bool
     *
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function importSqlFile($filename)
    {
        $query = '';

        $this->connection->beginTransaction();

        foreach ($this->readFile($filename) as $line) {
            // Skip line if it's a mysqldump comment or empty
            if (substr($line, 0, 2) === '--' || trim($line) === '') {
                continue;
            }

            // Check for CREATE DATABASE and USE lines and skip them
            // e.g. CREATE DATABASE IF NOT EXISTS `radius`;
            // e.g. USE `radius`;
            if (substr($line, 0, 15) === 'CREATE DATABASE' || substr($line, 0, 5) === 'USE `') {
                continue;
            }

            // Add line to current query
            $query .= $line;

            // Check if we're at the end of a query
            if (substr(trim($line), -1, 1) === ';') {
                try {
                    // Perform the Query
                    $this->connection->exec($query);
                } catch (DBALException $e) {
                    $this->logger->error(
                        "Query failed while importing $filename",
                        ['message' => $e->getMessage(), 'code' => $e->getCode()]
                    );
                    $this->connection->rollBack();

                    return false;
                }

                // Query was successful, clear $query for next round
                $query = '';
            }
        }
        $this->connection->commit();

        return true;
    }

    /**
     * Confirm that the provided backup file appears to be either a radius or radmin backup file
     * We basically do the same as the actual importSqlFile logic, but just checking for some
     * matching DROP TABLE lines
     *
     * @param $filename
     *
     * @return bool
     */
    public function confirmBackupFile($filename)
    {
        /**
         * Check for the following lines, they should be near the top
         * DROP TABLE IF EXISTS `mtotacct`;
         * DROP TABLE IF EXISTS `adminlog`;
         */
        $searchQueries = [
            'DROP TABLE IF EXISTS `mtotacct`;',
            'DROP TABLE IF EXISTS `adminlog`;',
        ];

        $lineCount = 0;

        $query = '';

        foreach ($this->readFile($filename) as $line) {
            // Skip line if it's a mysqldump comment or empty
            if (substr($line, 0, 2) === '--' || trim($line) === '') {
                continue;
            }

            // Add line to current query
            $query .= $line;
            $lineCount++;

            // Check if we're at the end of a query
            if (substr(trim($line), -1, 1) === ';') {
                // Check the command is one of the ones we're looking for, then we can exit
                if (in_array($query, $searchQueries)) {
                    return true;
                }
                $query = '';
            }

            // Don't allow us to read the entire file looking for something that should be at the top
            if ($lineCount >= 100) {
                return false;
            }
        }

        return false;
    }

    /**
     * Erase all tables in the Grase hotspot database to make room for the import
     *
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function eraseDatabase()
    {
        $tables = $this->connection->query('SHOW TABLES')->fetchAll(FetchMode::COLUMN);
        $this->connection->beginTransaction();

        $this->connection->exec('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            // TODO? Show which tables we've deleted?
            $this->connection->exec("DROP TABLE ${table}");
        }

        $this->connection->exec('SET FOREIGN_KEY_CHECKS=1;');

        $this->connection->commit();
    }

    /**
     * Read a SQL file (optionally gzipped) line by line in an efficient way
     *
     * @param $filename
     *
     * @return \Generator
     */
    private function readFile($filename)
    {
        $handle = gzopen($filename, 'r');

        while (!gzeof($handle)) {
            yield trim(gzgets($handle));
        }

        gzclose($handle);
    }
}
