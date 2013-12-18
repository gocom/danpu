-- 2013-12-14T15:41:43+01:00 - mysql:dbname=abc;host=localhost
SET AUTOCOMMIT = 0;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;

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
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;
COMMIT;
SET AUTOCOMMIT = 1;

-- Completed on: 2013-12-14T15:41:43+01:00
