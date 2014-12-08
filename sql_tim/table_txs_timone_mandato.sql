CREATE TABLE `flpmu_txs_timone_mandato` (
  `idComision` int(11) NOT NULL,
  `idTx` int(11) NOT NULL,
  `idOrden` int(11) NOT NULL,
  `idIntegrado` int(11) NOT NULL,
  `tipoOrden` varchar(45) NOT NULL,
  `date` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
