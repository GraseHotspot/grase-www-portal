<?php

declare(strict_types=1);

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
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER DATABASE CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');

        $this->addSql('ALTER TABLE radcheck
  MODIFY `id` INT AUTO_INCREMENT NOT NULL,
  MODIFY `UserName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY `Attribute` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY `op` VARCHAR(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'==\',
  MODIFY `Value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');

        $this->addSql('ALTER TABLE radusergroup
  MODIFY `UserName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY `GroupName` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');

        $this->addSql('ALTER TABLE groups
  MODIFY `GroupName` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY `GroupLabel` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY `Expiry` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  MODIFY `ExpireAfter` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  MODIFY `Comment` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');

        $this->addSql('ALTER TABLE radusercomment
  DROP KEY `usercomment`,
  MODIFY `UserName` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  CHANGE `Comment` `comment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');

        $this->addSql('ALTER TABLE auth
              CHANGE password password VARCHAR(60) CHARACTER SET utf8mb4_unicode_ci NOT NULl,
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE batch
              CHANGE `UserName` `UserName` VARCHAR(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE batches
              CHANGE `createdBy` `createdBy` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              CHANGE `Comment` `Comment` varchar(300) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE mtotacct
              CHANGE `UserName` `UserName` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `NASIPAddress` `NASIPAddress` varchar(15) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE nas
              CHANGE `nasname` `nasname` varchar(128) CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              CHANGE `shortname` `shortname` varchar(32) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `type` `type` varchar(30) CHARACTER SET utf8mb4_unicode_ci DEFAULT \'other\',
              CHANGE `secret` `secret` varchar(60) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'secret\',
              CHANGE `community` `community` varchar(50) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `description` `description` varchar(200) CHARACTER SET utf8mb4_unicode_ci DEFAULT \'RADIUS Client\',
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE radacct
              CHANGE `AcctSessionId` `AcctSessionId` varchar(32) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `AcctUniqueId` `AcctUniqueId` varchar(32) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `UserName` `UserName` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `Groupname` `Groupname` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `Realm` `Realm` varchar(64) CHARACTER SET utf8mb4_unicode_ci DEFAULT \'\',
              CHANGE `NASIPAddress` `NASIPAddress` varchar(15) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `NASPortId` `NASPortId` varchar(32) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `NASPortType` `NASPortType` varchar(32) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `AcctAuthentic` `AcctAuthentic` varchar(32) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `ConnectInfo_start` `ConnectInfo_start` varchar(50) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `ConnectInfo_stop` `ConnectInfo_stop` varchar(50) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `CalledStationId` `CalledStationId` varchar(50) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `CallingStationId` `CallingStationId` varchar(50) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `AcctTerminateCause` `AcctTerminateCause` varchar(32) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `ServiceType` `ServiceType` varchar(32) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `FramedProtocol` `FramedProtocol` varchar(32) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `FramedIPAddress` `FramedIPAddress` varchar(15) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE radgroupcheck
              CHANGE `GroupName` `GroupName` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `Attribute` `Attribute` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `op` `op` char(2) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'==\',
              CHANGE `Value` `Value` varchar(253) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE radgroupreply
              CHANGE `GroupName` `GroupName` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `Attribute` `Attribute` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `op` `op` char(2) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'==\',
              CHANGE `Value` `Value` varchar(253) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE radpostauth
              CHANGE `username` `username` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `pass` `pass` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `reply` `reply` varchar(32) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `authdate` `authdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              CHANGE `ServiceType` `ServiceType` varchar(32) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `FramedIPAddress` `FramedIPAddress` varchar(15) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              CHANGE `CallingStationId` `CallingStationId` varchar(50) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE radreply
              CHANGE `GroupName` `GroupName` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `Attribute` `Attribute` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              CHANGE `op` `op` char(2) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'==\',
              CHANGE `Value` `Value` varchar(253) CHARACTER SET utf8mb4_unicode_ci NOT NULL DEFAULT \'\',
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE settings
              CHANGE `setting` `setting` varchar(30) CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              CHANGE `value` `value` varchar(2000) CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE templates
              CHANGE `tpl` `tpl` text CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE vouchers
              CHANGE `VoucherName` `VoucherName` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              CHANGE `VoucherLabel` `VoucherLabel` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              CHANGE `VoucherPrice` `VoucherPrice` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              CHANGE `VoucherGroup` `VoucherGroup` varchar(64) CHARACTER SET utf8mb4_unicode_ci NOT NULL,
              CHANGE `Description` `Description` varchar(300) CHARACTER SET utf8mb4_unicode_ci DEFAULT NULL,
              ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
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
  CHANGE `comment` `Comment` varchar(300) DEFAULT NULL,
  ENGINE=MyISAM DEFAULT CHARSET=latin1');

        $this->addSql('ALTER TABLE radusercomment
  MODIFY `UserName` varchar(64) NOT NULL DEFAULT \'\',
  MODIFY `Comment` varchar(256) NOT NULL DEFAULT \'\',
  ENGINE=MyISAM DEFAULT CHARSET=utf8');
    }
}
