CREATE TABLE `flpmu_facturas_comisiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `integradoId` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `urlFactura` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
