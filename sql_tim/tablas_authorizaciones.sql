CREATE TABLE `flpmu_auth_odc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idOrden` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `authDate` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `flpmu_auth_odd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idOrden` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `authDate` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `flpmu_auth_odr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idOrden` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `authDate` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `flpmu_auth_odv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idOrden` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `authDate` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

