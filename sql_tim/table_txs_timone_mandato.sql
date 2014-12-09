DROP TABLE IF EXISTS `flpmu_txs_timone_mandato`;

CREATE TABLE `flpmu_txs_timone_mandato` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idTx` int(11) NOT NULL,
  `idOrden` int(11) DEFAULT NULL,
  `idIntegrado` int(11) NOT NULL,
  `date` bigint(20) NOT NULL,
  `tipoOrden` varchar(45) DEFAULT NULL,
  `idComision` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
