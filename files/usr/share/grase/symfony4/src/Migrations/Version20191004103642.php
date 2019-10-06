<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Bring in Foreign Key Constraints
 */
final class Version20191004103642 extends AbstractMigration
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
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'ALTER TABLE radcheck
            ADD CONSTRAINT FK_CF5F1897A11ACB1F FOREIGN KEY (UserName) REFERENCES users (username)'
        );
        $this->addSql(
            'ALTER TABLE radusergroup
            ADD CONSTRAINT FK_569F584FA11ACB1F FOREIGN KEY (UserName) REFERENCES users (username)'
        );
        $this->addSql(
            'ALTER TABLE radusergroup 
            ADD CONSTRAINT FK_569F584FB219A218 FOREIGN KEY (GroupName) REFERENCES `groups` (id)'
        );
        $this->addSql(
            'CREATE INDEX IDX_569F584FB219A218 ON radusergroup (GroupName)'
        );
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

        $this->addSql('ALTER TABLE radusergroup DROP INDEX IDX_569F584FB219A218');
        $this->addSql('ALTER TABLE radusergroup DROP FOREIGN KEY FK_569F584FB219A218');
        $this->addSql('ALTER TABLE radusergroup DROP FOREIGN KEY FK_569F584FA11ACB1F');
        $this->addSql('ALTER TABLE radcheck DROP FOREIGN KEY FK_CF5F1897A11ACB1F');

    }
}
