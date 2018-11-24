-- 2013-12-14T15:45:28+01:00 - mysql:dbname=abc;host=localhost

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

-- Trigger structure `test_table_delete`

DROP TRIGGER IF EXISTS `test_table_delete`;
DELIMITER //
CREATE TRIGGER `test_table_delete` BEFORE DELETE ON `test_table` FOR EACH ROW
delete from test_table_1 where test_table_1.id = OLD.id
//
DELIMITER ;

-- Completed on: 2013-12-14T15:45:28+01:00
