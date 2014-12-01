CREATE TABLE `flpmu_ordenes_retiro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `integradoId` int(11) NOT NULL,
  `numOrden` int(11) NOT NULL,
  `createdDate` bigint(20) NOT NULL,
  `paymentDate` bigint(20) NOT NULL,
  `paymentMethod` int(11) NOT NULL,
  `amount` float NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
