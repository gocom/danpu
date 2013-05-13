-- 2013-05-13T16:24:50+02:00 - @localhost

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
