﻿--liquibase formatted sql

--changeset ricardolyon:1
CREATE TABLE `flpmu_catalog_order_status` (
  `id` int(11) NOT NULL,
  `statusName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO integradb.flpmu_catalog_order_status (id, statusName) VALUES (1, 'Nueva');
INSERT INTO integradb.flpmu_catalog_order_status (id, statusName) VALUES (3, 'En autorización');
INSERT INTO integradb.flpmu_catalog_order_status (id, statusName) VALUES (5, 'Autorizada');
INSERT INTO integradb.flpmu_catalog_order_status (id, statusName) VALUES (8, 'Procesando');
INSERT INTO integradb.flpmu_catalog_order_status (id, statusName) VALUES (13, 'Pagada');
INSERT INTO integradb.flpmu_catalog_order_status (id, statusName) VALUES (21, 'Liquidada');
INSERT INTO integradb.flpmu_catalog_order_status (id, statusName) VALUES (34, 'Devuelta');
INSERT INTO integradb.flpmu_catalog_order_status (id, statusName) VALUES (55, 'Cancelada');

--changeset lutek:2
ALTER TABLE `integradb`.`flpmu_mandatos_mutuos` 
CHANGE COLUMN `expirationDate` `paymentPeriod` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `payments` `quantityPayments` INT(11) NULL DEFAULT NULL ;

--changeset lutek:3
ALTER TABLE `integradb`.`flpmu_mandatos_mutuos` 
CHANGE COLUMN `cuenta` `idCuenta` INT(11) NULL DEFAULT NULL ,
ADD COLUMN `cuotaOcapital` INT(11) NULL AFTER `interes`;
--rollback ALTER TABLE integradb.flpmu_mandatos_mutuos CHANGE COLUMN `idCuenta` `cuenta` INT(11) NULL DEFAULT NULL;
--rollback ALTER TABLE integradb.flpmu_mandatos_mutuos DROP cuotaOcapital;

--changeset ricardolyon:4
ALTER TABLE integradb.flpmu_txs_timone_mandato CHANGE idTx idTx VARCHAR(255);
ALTER TABLE integradb.flpmu_txs_timone_mandato ADD CONSTRAINT unique_idTx UNIQUE (idTx);
ALTER TABLE `integradb`.`flpmu_catalog_order_status` CHANGE COLUMN `statusName` `name` VARCHAR(45) NOT NULL ,ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC);
--rollback ALTER TABLE integradb.flpmu_txs_timone_mandato CHANGE idTx idTx INT(11);
--rollback ALTER TABLE integradb.flpmu_txs_timone_mandato DROP INDEX unique_idTx;
--rollback ALTER TABLE `integradb`.`flpmu_catalog_order_status` CHANGE COLUMN `name` `statusName` VARCHAR(45) NOT NULL ,DROP INDEX `name_UNIQUE`;
