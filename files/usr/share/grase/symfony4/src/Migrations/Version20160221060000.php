<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Move all radmin tables to radius
 */
class Version20160221060000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // @TODO check we are upgrading from a supported DB version
        $this->addSql('ALTER TABLE radmin.adminlog RENAME radius.adminlog');
        $this->addSql('ALTER TABLE radmin.auth RENAME radius.auth');
        $this->addSql('ALTER TABLE radmin.batch RENAME radius.batch');
        $this->addSql('ALTER TABLE radmin.batches RENAME radius.batches');
        $this->addSql('ALTER TABLE radmin.groups RENAME radius.groups');
        $this->addSql('ALTER TABLE radmin.settings RENAME radius.settings');
        $this->addSql('ALTER TABLE radmin.templates RENAME radius.templates');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE radius.adminlog RENAME radmin.adminlog');
        $this->addSql('ALTER TABLE radius.auth RENAME radmin.auth');
        $this->addSql('ALTER TABLE radius.batch RENAME radmin.batch');
        $this->addSql('ALTER TABLE radius.batches RENAME radmin.batches');
        $this->addSql('ALTER TABLE radius.groups RENAME radmin.groups');
        $this->addSql('ALTER TABLE radius.settings RENAME radmin.settings');
        $this->addSql('ALTER TABLE radius.templates RENAME radmin.templates');
    }
}
