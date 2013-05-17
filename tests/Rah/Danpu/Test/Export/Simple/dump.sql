-- 2013-05-13T16:24:50+02:00 - @localhost

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


-- Dumping data for table `test_table`

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