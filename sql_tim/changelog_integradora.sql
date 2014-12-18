--liquibase formatted sql

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

--changeset lutek:5
CREATE TABLE `integradb`.`flpmu_catalog_tipoperiodos` (
  `IdTipo` INT(11) NULL,
  `nombre` VARCHAR(45) NULL,
  `periodosAnio` INT(11) NULL);
ALTER TABLE `integradb`.`flpmu_mandatos_mutuos` ADD COLUMN `status` INT(11) NULL AFTER `cuotaOcapital`;

INSERT INTO `integradb`.`flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(2,'Quincenal',104);
INSERT INTO `integradb`.`flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(3,'Mensual',12);
INSERT INTO `integradb`.`flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(4,'Bimestral',6);
INSERT INTO `integradb`.`flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(5,'Trimestral',4);
INSERT INTO `integradb`.`flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(6,'Semestral',2);
INSERT INTO `integradb`.`flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(7,'Anual',1);
--rollback DROP TABLE `integradb`.`flpmu_catalog_tipoperiodos`;
--rollback ALTER TABLE `integradb`.`flpmu_mandatos_mutuos` DROP `status`;

--changeset lutek:6
CREATE TABLE `integradb`.`flpmu_integrado_timone` (
  `integradoId` INT NOT NULL,
  `timOneId` INT NULL,
  `account` BIGINT NULL,
  PRIMARY KEY (`integradoId`));
--rollback DROP TABLE `integradb`.`flpmu_integrado_timone`;

--changeset ricardolyon:7
CREATE TABLE `integradb`.`flpmu_reportes_balance` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `integradoId` VARCHAR(45) NOT NULL,
  `year` INT(4) NOT NULL,
  `pasivo` FLOAT NOT NULL,
  `pasivo_iva` FLOAT NOT NULL,
  `activo_banco` FLOAT NOT NULL,
  `depositos` FLOAT NOT NULL,
  `retiros` FLOAT NOT NULL,
  `createdDate` BIGINT NOT NULL,
  PRIMARY KEY (`id`));
--rollback DROP TABLE `integradb`.`flpmu_reportes_balance`;