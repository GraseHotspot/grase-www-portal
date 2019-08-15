<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Adds the new Groups table and fills it
 * Moves radusercomment to users and fills in the blanks
 */
class Version20160221201937 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql(
            "ALTER TABLE groups
              DROP PRIMARY KEY,
              ADD id INT NOT NULL AUTO_INCREMENT FIRST,
              ADD PRIMARY KEY (`id`),
              ADD UNIQUE KEY (`GroupName`)"
        );
        $this->addSql(
            "INSERT INTO groups
    (GroupName, GroupLabel, Comment)
     SELECT
      DISTINCT GroupName, GroupName, 'Missing group recreated' FROM `radusergroup`
      ON DUPLICATE KEY UPDATE id=id"
        );

        $this->addSql(
            "UPDATE radgroupcheck AS t
        INNER JOIN groups AS g ON t.GroupName = g.GroupName
        SET t.GroupName = CAST(g.id AS CHAR)
        WHERE t.GroupName != CAST(g.id AS CHAR)"
        );

        $this->addSql(
            "UPDATE radgroupreply AS t
        INNER JOIN groups AS g ON t.GroupName = g.GroupName
        SET t.GroupName = CAST(g.id AS CHAR)
        WHERE t.GroupName != CAST(g.id AS CHAR)"
        );

        $this->addSql(
            "UPDATE radusergroup AS t
        INNER JOIN groups AS g ON t.GroupName = g.GroupName
        SET t.GroupName = CAST(g.id AS CHAR)
        WHERE t.GroupName != CAST(g.id AS CHAR)"
        );

        $this->addSql(
            "ALTER TABLE groups
          DROP KEY GroupName,
          DROP COLUMN GroupName,
          CHANGE GroupLabel name VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL,
          ADD UNIQUE name (name)
            "
        );

        $this->addSql(
            'ALTER TABLE radusercomment
  RENAME users,
  MODIFY `UserName` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\'
'
        );
        $this->addSql(
            'INSERT INTO users (UserName)
SELECT DISTINCT username FROM radcheck
ON DUPLICATE KEY UPDATE UserName=users.UserName'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql(
            'ALTER TABLE users
  RENAME radusercomment,
  MODIFY `UserName` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\'
'
        );
    }
}
