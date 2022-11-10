-- ----------------------------------------------
-- ORACLE LAUNCHER DB SETUP
-- ----------------------------------------------

DROP TABLE IF EXISTS `avatars_list`;
CREATE TABLE IF NOT EXISTS `avatars_list` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(250) NOT NULL DEFAULT 'Unknown' COLLATE 'utf8_general_ci',
	`url` VARCHAR(250) NOT NULL DEFAULT 'http://localhost/launcher/application/avatars/default.jpg' COLLATE 'utf8_general_ci',
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `url` (`url`) USING BTREE
) COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=0;
INSERT INTO `avatars_list` (`id`, `name`, `url`) VALUES (1, 'Default', 'http://localhost/launcher/application/avatars/default.jpg');

DROP TABLE IF EXISTS `characters_market`;
CREATE TABLE IF NOT EXISTS `characters_market` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`seller_account` INT(11) NOT NULL DEFAULT '0',
	`guid` INT(11) NOT NULL,
	`price_dp` INT(11) NOT NULL DEFAULT '0',
	`realm_id` INT(11) NOT NULL DEFAULT '1',
	`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`realm_id`, `guid`) USING BTREE,
	UNIQUE INDEX `id` (`id`) USING BTREE
)
COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `characters_market_logs`;
CREATE TABLE IF NOT EXISTS `characters_market_logs` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`buyer_id` INT(11) NOT NULL,
	`seller_id` INT(11) NOT NULL,
	`market_id` INT(11) NOT NULL,
	`character_guid` INT(11) NOT NULL,
	`price_dp` INT(11) NOT NULL,
	`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='latin1_swedish_ci' ENGINE=InnoDB AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
	`account_name` VARCHAR(255) NOT NULL COLLATE 'latin1_swedish_ci',
	`last_session_id` VARCHAR(255) NOT NULL COLLATE 'latin1_swedish_ci',
	`last_ip` VARCHAR(250) NOT NULL COLLATE 'latin1_swedish_ci',
	`last_seen` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`account_name`) USING BTREE
) COLLATE='latin1_swedish_ci' ENGINE=InnoDB;

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(250) NOT NULL DEFAULT 'New Article' COLLATE 'utf8_general_ci',
	`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`articleUrl` VARCHAR(250) NOT NULL COLLATE 'utf8_general_ci',
	`imageUrl` VARCHAR(250) NOT NULL DEFAULT '' COLLATE 'utf8_general_ci',
	`expansionID` INT(11) NOT NULL,
	PRIMARY KEY (`id`, `expansionID`) USING BTREE
) COLLATE='utf8_general_ci' ENGINE=InnoDB AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`subject` VARCHAR(250) NOT NULL DEFAULT '' COLLATE 'latin1_swedish_ci',
	`message` VARCHAR(250) NOT NULL DEFAULT '' COLLATE 'latin1_swedish_ci',
	`imageUrl` VARCHAR(250) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`redirectUrl` VARCHAR(1000) NOT NULL COLLATE 'latin1_swedish_ci',
	`accountID` INT(11) NOT NULL DEFAULT '0' COMMENT '0 = everyone',
	PRIMARY KEY (`id`) USING BTREE
) COLLATE='latin1_swedish_ci' ENGINE=InnoDB AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `notifications_read`;
CREATE TABLE IF NOT EXISTS `notifications_read` (
	`accountID` INT(11) NOT NULL,
	`notificationID` INT(11) NOT NULL,
	`dateRead` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`accountID`, `notificationID`) USING BTREE
) COLLATE='latin1_swedish_ci' ENGINE=InnoDB;

DROP TABLE IF EXISTS `realms`;
CREATE TABLE IF NOT EXISTS `realms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `realm_id` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(100) NOT NULL DEFAULT 'Dummy Realm Name',
  `SRP6` tinyint(1) NOT NULL DEFAULT 0,
  `char_db_mysql_hostname` varchar(100) NOT NULL DEFAULT 'localhost',
  `char_db_name` varchar(100) NOT NULL DEFAULT 'characters',
  `char_db_mysql_port` smallint(6) NOT NULL DEFAULT 3306,
  `char_db_mysql_user` varchar(100) NOT NULL DEFAULT 'root',
  `char_db_mysql_pass` varchar(100) NOT NULL DEFAULT 'ascent',
  `realmlist` varchar(100) NOT NULL DEFAULT 'logon.yourservername.com',
  `soap_hostname` varchar(100) NOT NULL DEFAULT 'localhost',
  `soap_uri` varchar(50) NOT NULL DEFAULT 'urn:TC',
  `soap_user` varchar(100) NOT NULL DEFAULT 'admin',
  `soap_pass` varchar(100) NOT NULL DEFAULT '12345',
  PRIMARY KEY (`realm_id`) USING BTREE,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

INSERT INTO `realms` (`id`, `realm_id`, `enabled`, `name`, `SRP6`, `char_db_mysql_hostname`, `char_db_name`, `char_db_mysql_port`, `char_db_mysql_user`, `char_db_mysql_pass`, `realmlist`, `soap_hostname`, `soap_uri`, `soap_user`, `soap_pass`) VALUES
	(1, 1, 1, 'TrinityCore Wotlk 3.3.5', 1, 'localhost', 'characters', 3306, 'root', 'ascent', 'logon.yourservername.com', 'localhost', 'urn:TC', 'admin', '12345'),
	(2, 2, 0, 'AzerothCore Wotlk 3.3.5', 0, 'localhost', 'ac_characters', 3306, 'root', 'ascent', 'logon.yourservername.com', 'localhost', 'urn:AC', 'admin', '12345'),
	(3, 3, 0, 'CMangos Classic 1.0.1', 0, 'localhost', 'cmc_characters', 3306, 'root', 'ascent', 'logon.yourservername.com', 'localhost', 'urn:MaNGOS', 'admin', '12345'),
	(4, 4, 0, 'Skyfire Mop 5.4.8', 0, 'localhost', 'sf_characters', 3306, 'root', 'ascent', 'logon.yourservername.com', 'localhost', 'urn:SF', 'admin', '12345'),
	(5, 5, 0, 'TrinityCore Cata 4.3.4', 0, 'localhost', 'tcc_characters', 3306, 'root', 'ascent', 'logon.yourservername.com', 'localhost', 'urn:TC', 'admin', '12345');

DROP TABLE IF EXISTS `soap_logs`;
CREATE TABLE IF NOT EXISTS `soap_logs` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`account_id` INT(11) NOT NULL DEFAULT '0',
	`account_name` VARCHAR(250) NOT NULL DEFAULT 'Unknown' COLLATE 'latin1_swedish_ci',
	`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`realm_id` INT(11) NOT NULL DEFAULT '0',
	`command` VARCHAR(1000) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	PRIMARY KEY (`id`) USING BTREE
) COLLATE='latin1_swedish_ci' ENGINE=InnoDB;

DROP TABLE IF EXISTS `user_avatars`;
CREATE TABLE IF NOT EXISTS `user_avatars` (
	`account_id` INT(11) NOT NULL,
	`avatar_url` VARCHAR(250) NOT NULL COLLATE 'latin1_swedish_ci',
	UNIQUE INDEX `account_id` (`account_id`) USING BTREE
) COLLATE='latin1_swedish_ci' ENGINE=InnoDB;

DROP TABLE IF EXISTS `shop_list`;
CREATE TABLE IF NOT EXISTS `shop_list` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(250) NOT NULL COLLATE 'latin1_swedish_ci',
	`description` VARCHAR(255) NOT NULL COLLATE 'latin1_swedish_ci',
	`img_url` VARCHAR(250) NOT NULL COLLATE 'latin1_swedish_ci',
	`price_dp` INT(11) NOT NULL DEFAULT '0',
	`price_vp` INT(11) NOT NULL DEFAULT '0',
	`category` INT(11) NOT NULL DEFAULT '1' COMMENT '1 = service,\r\n2 = bundle,\r\n3 = item,\r\n4 = mount,\r\n5 = pet',
	`soap_command` VARCHAR(10000) NOT NULL COMMENT '{PLAYER} = player name\r\n{ACCOUNT} = account name' COLLATE 'latin1_swedish_ci',
	`realm_id` TINYINT(4) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`) USING BTREE
) COLLATE='latin1_swedish_ci' ENGINE=InnoDB AUTO_INCREMENT=0;

INSERT INTO `shop_list` (`id`, `title`, `description`, `img_url`, `price_dp`, `price_vp`, `category`, `soap_command`, `realm_id`) VALUES (1, 'Warglaive of Azzinoth', 'Main hand Warglaive of Azzinoth', 'https://i.pinimg.com/originals/f5/f9/6a/f5f96ae070586b5e4c96e79339841523.png', 100, 1500, 3, 'send items {PLAYER} "Shop Receipt" "Thanks for your purchase(s)!" 32837:1', 1);
INSERT INTO `shop_list` (`id`, `title`, `description`, `img_url`, `price_dp`, `price_vp`, `category`, `soap_command`, `realm_id`) VALUES (2, 'Warglaive of Azzinoth', 'Off hand Warglaive of Azzinoth', 'https://cdna.artstation.com/p/assets/images/images/024/452/678/original/stian-s-sundby-200223-wg-gif-03.gif?1582478756', 90, 1500, 3, 'send items {PLAYER} "Shop Receipt" "Thanks for your purchase(s)!" 32838:1', 1);
INSERT INTO `shop_list` (`id`, `title`, `description`, `img_url`, `price_dp`, `price_vp`, `category`, `soap_command`, `realm_id`) VALUES (3, 'Illidan\'s Warglaives', 'Full set Warglaives of Azzinoth', 'https://buy-boost.com/static/data/product/376/Warglaives%20of%20Azzinoth%20Boost%201.jpg', 180, 2800, 2, 'send items {PLAYER} "Shop Receipt" "Thanks for your purchase(s)!" 32838:1 32837:1', 1);
INSERT INTO `shop_list` (`id`, `title`, `description`, `img_url`, `price_dp`, `price_vp`, `category`, `soap_command`, `realm_id`) VALUES (4, 'Faction Change', 'Change your character faction to Alliance or Horde', 'https://static.wikia.nocookie.net/wowpedia/images/a/a3/Faction_Change_service.jpg/revision/latest?cb=20180722014335', 100, 0, 1, 'character changefaction {PLAYER}', 1);
INSERT INTO `shop_list` (`id`, `title`, `description`, `img_url`, `price_dp`, `price_vp`, `category`, `soap_command`, `realm_id`) VALUES (5, 'Race Change', 'Change your character race to any race of your current faction', 'https://theworldofmmo.com/wp-content/uploads/2020/10/Save-30-on-Select-Game-Services-Race-Change-Faction.jpg', 100, 0, 1, 'character changerace {PLAYER}', 1);
INSERT INTO `shop_list` (`id`, `title`, `description`, `img_url`, `price_dp`, `price_vp`, `category`, `soap_command`, `realm_id`) VALUES (6, 'Appearance Change', 'Change your character appearance and name', 'https://d2skuhm0vrry40.cloudfront.net/2020/articles/2020-07-09-14-32/-1594301567485.jpg/EG11/resize/1200x-1/-1594301567485.jpg', 100, 0, 1, 'character customize {PLAYER}', 1);
INSERT INTO `shop_list` (`id`, `title`, `description`, `img_url`, `price_dp`, `price_vp`, `category`, `soap_command`, `realm_id`) VALUES (7, 'Swift Flying Broom', 'Enjoy flying with the Magic Broom!', 'https://wow.zamimg.com/uploads/screenshots/normal/147609-swift-flying-broom.jpg', 80, 0, 4, 'send items {PLAYER} "Shop Receipt" "Thanks for your purchase(s)!" 33182:1', 1);
INSERT INTO `shop_list` (`id`, `title`, `description`, `img_url`, `price_dp`, `price_vp`, `category`, `soap_command`, `realm_id`) VALUES (8, 'Phoenix Hatchling', 'Take care of a new companion, a pheonix!', 'https://static.wikia.nocookie.net/wowpedia/images/c/c2/Phoenix_Hatchling.jpg/revision/latest?cb=20080213095952', 25, 0, 5, 'send items {PLAYER} "Shop Receipt" "Thanks for your purchase(s)!" 35504:1', 1);
INSERT INTO `shop_list` (`id`, `title`, `description`, `img_url`, `price_dp`, `price_vp`, `category`, `soap_command`, `realm_id`) VALUES (9, 'Phoenix Hatchling on Realm 3', 'Take care of a new companion, a pheonix!', 'https://static.wikia.nocookie.net/wowpedia/images/c/c2/Phoenix_Hatchling.jpg/revision/latest?cb=20080213095952', 25, 0, 5, 'send items {PLAYER} "Shop Receipt" "Thanks for your purchase(s)!" 35504:1', 3);
