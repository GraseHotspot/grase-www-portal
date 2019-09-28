<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Convert tables to InnoDB and correct UTF encoding
 */
class Version20160221065707 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE radcheck
  MODIFY `UserName` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  MODIFY `Attribute` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  MODIFY `op` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'==\',
  MODIFY `Value` varchar(253) COLLATE utf8_unicode_ci NOT NULL,
  ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci');

        $this->addSql('ALTER TABLE radusergroup
  MODIFY `UserName` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
  MODIFY `GroupName` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
  ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci');

        $this->addSql('ALTER TABLE groups
  MODIFY `GroupName` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  MODIFY `GroupLabel` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  MODIFY `Expiry` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  MODIFY `ExpireAfter` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  MODIFY `Comment` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci');

        $this->addSql('ALTER TABLE radusercomment
  DROP KEY `usercomment`,
  MODIFY `UserName` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'\',
  MODIFY `Comment` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE radcheck
  MODIFY `UserName` varchar(64) CHARACTER SET utf8 NOT NULL,
  MODIFY `Attribute` varchar(64) CHARACTER SET utf8 NOT NULL,
  MODIFY `op` char(2) CHARACTER SET utf8 NOT NULL DEFAULT \'==\',
  MODIFY `Value` varchar(253) CHARACTER SET utf8 NOT NULL,
  ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');

        $this->addSql('ALTER TABLE radusergroup
  MODIFY `UserName` varchar(64) NOT NULL DEFAULT \'\',
  MODIFY `GroupName` varchar(64) NOT NULL DEFAULT \'\',
  ENGINE=MyISAM DEFAULT CHARSET=utf8');

        $this->addSql('ALTER TABLE groups
  MODIFY `GroupName` varchar(64) NOT NULL,
  MODIFY `GroupLabel` varchar(64) NOT NULL,
  MODIFY `Expiry` varchar(100) DEFAULT NULL,
  MODIFY `ExpireAfter` varchar(100) DEFAULT NULL,
  MODIFY `Comment` varchar(300) DEFAULT NULL,
  ENGINE=MyISAM DEFAULT CHARSET=latin1');

        $this->addSql('ALTER TABLE radusercomment
  MODIFY `UserName` varchar(64) NOT NULL DEFAULT \'\',
  MODIFY `Comment` varchar(256) NOT NULL DEFAULT \'\',
  ENGINE=MyISAM DEFAULT CHARSET=utf8');
    }
}
