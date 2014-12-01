CREATE TABLE `flpmu_ordenes_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proyecto` int(11) DEFAULT NULL,
  `proveedor` int(11) DEFAULT NULL,
  `integradoId` int(11) DEFAULT NULL,
  `numOrden` bigint(20) DEFAULT NULL,
  `createdDate` bigint(20) DEFAULT NULL,
  `paymentDate` bigint(20) DEFAULT NULL,
  `paymentMethod` int(11) DEFAULT NULL,
  `totalAmount` float DEFAULT '0',
  `urlXML` varchar(255) DEFAULT NULL,
  `observaciones` text,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `flpmu_ordenes_deposito` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `integradoId` int(11) NOT NULL,
  `numOrden` int(11) NOT NULL,
  `createdDate` bigint(20) NOT NULL,
  `paymentDate` bigint(20) NOT NULL,
  `totalAmount` int(11) NOT NULL,
  `paymentMethod` int(11) NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `flpmu_ordenes_venta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `integradoId` int(11) DEFAULT NULL,
  `numOrden` varchar(255) DEFAULT NULL,
  `projectId` int(11) DEFAULT NULL,
  `projectId2` int(11) DEFAULT NULL,
  `clientId` int(100) DEFAULT NULL,
  `account` varchar(100) DEFAULT NULL,
  `paymentMethod` varchar(100) DEFAULT NULL,
  `conditions` varchar(100) DEFAULT NULL,
  `placeIssue` varchar(100) DEFAULT NULL,
  `productos` text,
  `created` bigint(20) DEFAULT NULL,
  `payment` bigint(20) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
