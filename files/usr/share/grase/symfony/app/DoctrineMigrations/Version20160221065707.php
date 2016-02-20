<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Convert tables to InnoDB and correct UTF encoding
 */
class Version20160221065707 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE radius.radcheck
  MODIFY `UserName` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  MODIFY `Attribute` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  MODIFY `op` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT \'==\',
  MODIFY `Value` varchar(253) COLLATE utf8_unicode_ci NOT NULL,

 ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_unicode_ci');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE radius.radcheck
  MODIFY `UserName` varchar(64) CHARACTER SET utf8 NOT NULL,
  MODIFY `Attribute` varchar(64) CHARACTER SET utf8 NOT NULL,
  MODIFY `op` char(2) CHARACTER SET utf8 NOT NULL DEFAULT \'==\',
  MODIFY `Value` varchar(253) CHARACTER SET utf8 NOT NULL,
ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');


    }
}
