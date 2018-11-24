-- 2018-11-24T15:45:28+01:00 - mysql:dbname=abc;host=localhost

-- Table structure for table `types`

DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `option` enum('0','1','2') DEFAULT NULL,
  `character` set('a','b','c','d') DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `coords` float(4,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table `types`

LOCK TABLES `types` WRITE;
INSERT INTO `types` VALUES (1,'0','a,d','512.89',89.12);
UNLOCK TABLES;

-- Completed on: 2018-11-24T15:45:28+01:00
