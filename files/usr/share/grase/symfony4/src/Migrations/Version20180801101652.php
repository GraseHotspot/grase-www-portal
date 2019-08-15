<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migrate to role based access instead of bitmask Access level
 */
final class Version20180801101652 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE auth
          ADD role VARCHAR(25) NOT NULL,
          CHANGE username username VARCHAR(50) NOT NULL');
        $this->addSql('UPDATE auth SET role =  
            CASE
              WHEN accesslevel = 1 THEN \'ROLE_SUPERADMIN\'
              WHEN accesslevel = 2 THEN \'ROLE_ADMIN\'
              WHEN accesslevel = 4 THEN \'ROLE_USER\'
              ELSE \'ROLE_USER\' 
            END');

        $this->addSql('DROP INDEX password ON auth');
        $this->addSql('ALTER TABLE auth DROP accesslevel');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // When downgrading, everyone will become a superadmin
        $this->addSql('ALTER TABLE auth ADD accesslevel INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE auth DROP role, CHANGE username username VARCHAR(50) DEFAULT \'\'\'\' NOT NULL COLLATE latin1_swedish_ci');
        $this->addSql('CREATE INDEX password ON auth (password)');
    }
}
