CREATE TABLE `flpmu_bitacora_status_odv` (
  `id` int(11) NOT NULL,
  `idOrden` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `changeDate` bigint(20) NOT NULL,
  `pastStatus` int(11) NOT NULL,
  `newStatus` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `flpmu_bitacora_status_odd` (
  `id` int(11) NOT NULL,
  `idOrden` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `changeDate` bigint(20) NOT NULL,
  `pastStatus` int(11) NOT NULL,
  `newStatus` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `flpmu_bitacora_status_odc` (
  `id` int(11) NOT NULL,
  `idOrden` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `changeDate` bigint(20) NOT NULL,
  `pastStatus` int(11) NOT NULL,
  `newStatus` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `flpmu_bitacora_status_odr` (
  `id` int(11) NOT NULL,
  `idOrden` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `changeDate` bigint(20) NOT NULL,
  `pastStatus` int(11) NOT NULL,
  `newStatus` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

