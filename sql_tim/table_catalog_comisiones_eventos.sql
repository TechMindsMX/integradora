CREATE TABLE `flpmu_catalog_comisiones_eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `trigger` varchar(45) NOT NULL,
  `eventFullName` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
