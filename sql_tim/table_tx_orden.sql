CREATE TABLE `flpmu_tx_orden` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idTx` int(11) DEFAULT NULL,
  `idOrden` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `paid` int(11) DEFAULT NULL,
  `remainder` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
