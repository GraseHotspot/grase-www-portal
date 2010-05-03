# MySQL Navigator Xport
# Database: radmin
# root@localhost

# CREATE DATABASE radmin;
# USE radmin;

#
# Table structure for table 'auth'
#

# DROP TABLE IF EXISTS auth;
CREATE TABLE IF NOT EXISTS `auth` (
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(60) NOT NULL,
  PRIMARY KEY (`username`),
  KEY `password` (`password`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

#
# Table structure for table 'settings'
#

# DROP TABLE IF EXISTS settings;
CREATE TABLE IF NOT EXISTS  `settings` (
  `setting` varchar(20) NOT NULL,
  `value` varchar(1000) NOT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Settings for RAFI interface';

INSERT INTO `settings` SET
`setting` ='DBVersion',
`value` ='1.0';

#
# Table structure for table 'adminlog'

CREATE TABLE IF NOT EXISTS  `adminlog` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `timestamp` DATETIME NOT NULL,
    `username` VARCHAR(100) NULL,
    `ipaddress` VARCHAR(16) NULL,
    `action` TEXT(1000) NOT NULL,
    PRIMARY KEY `id` (`id`)
) ENGINE=innoDB COMMENT ='Log of Admin/Usermin Actions';
