CREATE TABLE `flpmu_mandatos_comisiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(45) NOT NULL,
  `type` varchar(45) NOT NULL,
  `rate` float DEFAULT NULL,
  `frequencyTimes` int(11) DEFAULT NULL,
  `monto` float DEFAULT NULL,
  `trigger` varchar(45) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
