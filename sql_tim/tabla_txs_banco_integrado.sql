DROP TABLE `integradb`.`flpmu_tx_orden`;

DROP TABLE `flpmu_conciliacion_banco_integrado`;

CREATE TABLE `flpmu_txs_banco_integrado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cuenta` varchar(45) DEFAULT NULL,
  `referencia` varchar(60) DEFAULT NULL,
  `date` varchar(45) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `integradoId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
