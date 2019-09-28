<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\AuditLog;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Migration to get the new audit_log tables
 */
final class Version20190823234949 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'CREATE TABLE audit_log (
                    id INT AUTO_INCREMENT NOT NULL,
                    message LONGTEXT NOT NULL,
                    username VARCHAR(255),
                    context LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
                    level SMALLINT NOT NULL,
                    level_name VARCHAR(50) NOT NULL,
                    extra LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
                    created_at DATETIME NOT NULL,
                    PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );
    }

    /**
     * @param Schema $schema
     *
     * After we've created the new tables, migrate the data in (with a limit)
     */
    public function postUp(Schema $schema)
    {
        parent::postUp($schema);

        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        // TODO Limit the number of logs we migrate?
        // Migrate adminlog to audit_log
        $oldAdminLogs = $this->connection->query('
            SELECT * FROM (
                SELECT id,timestamp, username, ipaddress, action
                FROM adminlog
                WHERE action != "CRON"
                ORDER BY id
                DESC LIMIT 5000) AS ttable
            ORDER BY id')->fetchAll(FetchMode::ASSOCIATIVE);

        $this->write($translator->trans('grase.migrate.db.adminlogs', ['number' => count($oldAdminLogs)]));

        $batchSize = 500;
        $i = 0;
        $progressBar = new ProgressBar(new ConsoleOutput());
        foreach ($progressBar->iterate($oldAdminLogs) as $oldAdminLog) {
            $i++;
            $newAdminLog = new AuditLog();

            if ($oldAdminLog['timestamp'] && $oldAdminLog['timestamp'] !== "0000-00-00 00:00:00") {
                $newAdminLog->setCreatedAt(new \DateTime($oldAdminLog['timestamp']));
            }
            if ($oldAdminLog['username'] && $oldAdminLog['username'] !== 'Anon') {
                $newAdminLog->setUsername($oldAdminLog['username']);
            }
            $newAdminLog->setMessage($oldAdminLog['action']);
            $newAdminLog->setExtra(['ip' => $oldAdminLog['ipaddress']]);
            $newAdminLog->setLevel(Logger::INFO);
            $newAdminLog->setLevelName(Logger::getLevelName(Logger::INFO));
            $em->persist($newAdminLog);
            if (($i % $batchSize) === 0) {
                $em->flush();
                $em->clear(); // Detaches all objects from Doctrine!
                $this->write('.');
            }
        }
        $em->flush(); //Persist objects that did not make up an entire batch
        $em->clear();

        // TODO remove adminlog table?
    }

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE audit_log');
    }
}
