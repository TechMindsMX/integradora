CREATE TABLE `flpmu_mandatos_mutuos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `integradoIdE` int(11) DEFAULT NULL,
  `integradoIdR` int(11) DEFAULT NULL,
  `cuenta` varchar(45) DEFAULT NULL,
  `expirationDate` bigint(20) DEFAULT NULL,
  `payments` int(11) DEFAULT NULL,
  `jsonTabla` text,
  `totalAmount` float DEFAULT NULL,
  `interes` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
