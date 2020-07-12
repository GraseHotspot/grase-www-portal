<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to move to more modern radius table structure.
 *
 * A number of fields have changed in radacct with Freeradius 3 (and earlier)
 */
final class Version20191228075920 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX AcctUniqueId_2 ON radacct');
        $this->addSql('DROP INDEX acctuniqueid ON radacct');
        $this->addSql('
            ALTER TABLE radacct
                ADD acctupdatetime DATETIME DEFAULT NULL AFTER acctstarttime,
                ADD acctinterval INT DEFAULT NULL AFTER acctstoptime,
                DROP AcctStartDelay,
                DROP AcctStopDelay,
                DROP xascendsessionsvrkey,
                CHANGE NASPortId NASPortId VARCHAR(32) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX acctuniqueid ON radacct (acctuniqueid)');
        $this->addSql('DROP INDEX usergroup ON radusergroup');
        $this->addSql('DROP INDEX usergrouppri ON radusergroup');
        $this->addSql('
            ALTER TABLE radusergroup
                ADD id int(11) unsigned AUTO_INCREMENT NOT NULL,
                DROP PRIMARY KEY,
                ADD PRIMARY KEY (id)
                ');
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
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            ALTER TABLE radacct
                ADD AcctStartDelay INT DEFAULT NULL,
                ADD AcctStopDelay INT DEFAULT NULL,
                ADD xascendsessionsvrkey VARCHAR(10) CHARACTER SET latin1 DEFAULT \'NULL\' COLLATE `latin1_swedish_ci`,
                DROP acctupdatetime,
                DROP acctinterval
                ');
        $this->addSql('CREATE UNIQUE INDEX AcctUniqueId_2 ON radacct (AcctUniqueId)');
        $this->addSql('CREATE INDEX AcctUniqueId ON radacct (AcctUniqueId)');

        $this->addSql('ALTER TABLE radusergroup MODIFY id BIGINT NOT NULL');
        $this->addSql('ALTER TABLE radusergroup DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE radusergroup DROP id');
        $this->addSql('CREATE UNIQUE INDEX usergroup ON radusergroup (UserName, priority, GroupName)');
        $this->addSql('CREATE UNIQUE INDEX usergrouppri ON radusergroup (UserName, priority)');
        $this->addSql('ALTER TABLE radusergroup ADD PRIMARY KEY (UserName, GroupName)');
    }
}
