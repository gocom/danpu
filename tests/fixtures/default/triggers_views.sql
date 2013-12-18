-- 2013-12-14T15:45:28+01:00 - mysql:dbname=abc;host=localhost

-- Table structure for table `organization`

DROP TABLE IF EXISTS `organization`;
CREATE TABLE `organization` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `zip_code` varchar(32) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `phone` varchar(125) NOT NULL,
  `website` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_idx` (`email`),
  KEY `name_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table `organization`

LOCK TABLES `organization` WRITE;
INSERT INTO `organization` VALUES (1,'GitHub','','','San Francisco','','','','http://github.com','github@example.com');
UNLOCK TABLES;

-- Table structure for table `person`

DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `organization` int(12) NOT NULL DEFAULT '0',
  `login` varchar(64) NOT NULL,
  `pass` varchar(126) NOT NULL,
  `email` varchar(254) NOT NULL,
  `pub_session_id` varchar(126) NOT NULL,
  `prv_session_id` varchar(126) NOT NULL,
  `access_token` text NOT NULL,
  `last_access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reset_token` varchar(126) NOT NULL,
  `user_group` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_idx` (`name`),
  UNIQUE KEY `email_idx` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table `person`

LOCK TABLES `person` WRITE;
INSERT INTO `person` VALUES (1,'John Doe',1,'john','','john.doe@example.com','','','','0000-00-00 00:00:00','',2);
UNLOCK TABLES;

-- Table structure for table `privs`

DROP TABLE IF EXISTS `privs`;
CREATE TABLE `privs` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `user_group` int(12) NOT NULL,
  `event` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_idx` (`user_group`),
  KEY `event_idx` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table `privs`

LOCK TABLES `privs` WRITE;
INSERT INTO `privs` VALUES (1,2,'delete_profile');
INSERT INTO `privs` VALUES (2,2,'edit_profile');
UNLOCK TABLES;

-- Table structure for table `test_table`

DROP TABLE IF EXISTS `test_table`;
CREATE TABLE `test_table` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `hello` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name_idx` (`hello`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `test_table`

LOCK TABLES `test_table` WRITE;
UNLOCK TABLES;

-- Table structure for table `test_table_1`

DROP TABLE IF EXISTS `test_table_1`;
CREATE TABLE `test_table_1` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `hello` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name_idx` (`hello`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `test_table_1`

LOCK TABLES `test_table_1` WRITE;
UNLOCK TABLES;

-- Table structure for table `test_table_2`

DROP TABLE IF EXISTS `test_table_2`;
CREATE TABLE `test_table_2` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `test_table_2`

LOCK TABLES `test_table_2` WRITE;
UNLOCK TABLES;

-- Table structure for table `test_table_3`

DROP TABLE IF EXISTS `test_table_3`;
CREATE TABLE `test_table_3` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table `test_table_3`

LOCK TABLES `test_table_3` WRITE;
UNLOCK TABLES;

-- Table structure for table `user_groups`

DROP TABLE IF EXISTS `user_groups`;
CREATE TABLE `user_groups` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table `user_groups`

LOCK TABLES `user_groups` WRITE;
INSERT INTO `user_groups` VALUES (1,'Developer');
INSERT INTO `user_groups` VALUES (2,'Staff');
UNLOCK TABLES;

-- Structure for view `organization_view`

DROP VIEW IF EXISTS `organization_view`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `organization_view` AS select `organization`.`id` AS `id`,`organization`.`name` AS `name`,`organization`.`website` AS `website` from `organization`;

-- Trigger structure `user_group_delete`

DROP TRIGGER IF EXISTS `user_group_delete`;
DELIMITER //
CREATE TRIGGER `user_group_delete` BEFORE DELETE ON `user_groups` FOR EACH ROW
delete from privs where privs.user_group = OLD.id
//
DELIMITER ;

-- Completed on: 2013-12-14T15:45:28+01:00
