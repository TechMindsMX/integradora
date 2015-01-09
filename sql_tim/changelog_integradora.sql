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
--rollback DROP TABLE `flpmu_catalog_order_status`;

--changeset lutek:2
DROP TABLE IF EXISTS `flpmu_mandatos_mutuos`;
CREATE TABLE `flpmu_mandatos_mutuos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `integradoIdE` int(11) DEFAULT NULL,
  `integradoIdR` int(11) DEFAULT NULL,
  `cuenta` int(11) DEFAULT NULL,
  `expirationDate` int(11) DEFAULT NULL,
  `payments` int(11) DEFAULT NULL,
  `jsonTabla` text,
  `totalAmount` float DEFAULT NULL,
  `interes` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `integradb`.`flpmu_mandatos_mutuos`
CHANGE COLUMN `expirationDate` `paymentPeriod` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `payments` `quantityPayments` INT(11) NULL DEFAULT NULL ;
--rollback DROP TABLE `flpmu_mandatos_mutuos`;

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
DROP TABLE IF EXISTS `flpmu_catalog_tipoperiodos`;
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
DROP TABLE IF EXISTS `flpmu_integrado_timone`;
CREATE TABLE `integradb`.`flpmu_integrado_timone` (
  `integradoId` INT NOT NULL,
  `timOneId` INT NULL,
  `account` BIGINT NULL,
  PRIMARY KEY (`integradoId`));
--rollback DROP TABLE `integradb`.`flpmu_integrado_timone`;

--changeset ricardolyon:7
DROP TABLE IF EXISTS `flpmu_reportes_balance`;
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

--changeset nestor:8
DROP TABLE IF EXISTS `flpmu_ordenes_prestamo`;
CREATE TABLE IF NOT EXISTS `flpmu_ordenes_prestamo` (
`id` int(11) NOT NULL,
  `fecha_elaboracion` BIGINT NOT NULL,
  `fecha_deposito` BIGINT NOT NULL,
  `tasa` FLOAT NOT NULL,
  `tipo_movimiento` VARCHAR(45) NULL,
  `acreedor` VARCHAR(45) NULL,
  `a_rfc` VARCHAR(45) NULL,
  `deudor` VARCHAR(45) NULL,
  `d_rfc` VARCHAR(45) NULL,
  `capital` FLOAT NOT NULL,
  `intereses` FLOAT NOT NULL,
  `iva_intereses` FLOAT NOT NULL,
  PRIMARY KEY (`id`)
);
ALTER TABLE `flpmu_ordenes_prestamo` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--rollback DROP TABLE `flpmu_ordenes_prestamo`;

--changeset ricardolyon:9
DROP TABLE IF EXISTS `integradb`.`flpmu_auth_mutuo`;
CREATE TABLE `integradb`.`flpmu_auth_mutuo` (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  idOrden INT NOT NULL,
  userId INT NOT NULL,
  authDate BIGINT NOT NULL
  );
--rollback DROP TABLE `integradb`.`flpmu_auth_mutuo`;

--changeset ricardolyon:10
ALTER TABLE `integradb`.`flpmu_reportes_balance` CHANGE COLUMN `integradoId` `integradoId` INT NOT NULL;
--rollback ALTER  TABLE `integradb`.`flpmu_reportes_balance` CHANGE COLUMN `integradoId` `integradoId` VARCHAR(45) NOT NULL;

--changeset ricardolyon:11
DROP TABLE IF EXISTS `integradb`.`flpmu_integrado_params`;
CREATE TABLE `integradb`.`flpmu_integrado_params` (
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `integradoId` INT NOT NULL,
  `params` VARCHAR(255) NULL
);
--rollback DROP TABLE `integradb`.`flpmu_integrado_params`;


--changeset lutek:12
ALTER TABLE `flpmu_ordenes_prestamo` ADD `idMutuo` INT NOT NULL AFTER `id`;
--rollback ALTER TABLE `flpmu_ordenes_prestamo` DROP `idMutuo`;

--changeset lutek:13
CREATE TABLE `flpmu_bitacora_status_mutuo` (
  `idOrden` INT NULL,
  `userId` INT NULL,
  `changeDate` BIGINT NULL,
  `pastStatus` INT NULL,
  `newStatus` INT NULL,
  `result` INT NULL);
--rollback DROP TABLE `flpmu_bitacora_status_mutuo`;

--changeset lutek:14
ALTER TABLE  `flpmu_ordenes_prestamo` ADD  `numOrden` INT NOT NULL AFTER  `idMutuo`;
ALTER TABLE  `flpmu_ordenes_prestamo` ADD  `status` INT NOT NULL
--rollback ALTER TABLE `flpmu_ordenes_prestamo` DROP `numOrden`, DROP `status`;

--changeset lutek:15
ALTER TABLE `integradb`.`flpmu_ordenes_prestamo`
CHANGE COLUMN `numOrden` `numOrden` VARCHAR(50) NULL ,
CHANGE COLUMN `tipo_movimiento` `tipo_movimiento` VARCHAR(100) NULL ,
CHANGE COLUMN `acreedor` `acreedor` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `a_rfc` `a_rfc` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `deudor` `deudor` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `d_rfc` `d_rfc` VARCHAR(100) NULL DEFAULT NULL ;
--rollback ALTER TABLE `integradb`.`flpmu_ordenes_prestamo` CHANGE COLUMN `numOrden` `numOrden` INT(10) NULL ,CHANGE COLUMN `tipo_movimiento` `tipo_movimiento` VARCHAR(45) NULL ,CHANGE COLUMN `acreedor` `acreedor` VARCHAR(45) NULL DEFAULT NULL ,CHANGE COLUMN `a_rfc` `a_rfc` VARCHAR(45) NULL DEFAULT NULL ,CHANGE COLUMN `deudor` `deudor` VARCHAR(45) NULL DEFAULT NULL ,CHANGE COLUMN `d_rfc` `d_rfc` VARCHAR(45) NULL DEFAULT NULL ;

--changeset lutek:16
ALTER TABLE `integradb`.`flpmu_catalog_tipoperiodos`
ADD COLUMN `multiplicador` INT(11) NULL AFTER `periodosAnio`,
ADD COLUMN `nombreCiclo` VARCHAR(45) NULL AFTER `multiplicador`;
UPDATE flpmu_catalog_tipoperiodos SET nombreCiclo = 'M' WHERE idTipo IN (3,4,5,6);
UPDATE flpmu_catalog_tipoperiodos SET nombreCiclo = 'Y' WHERE idTipo = 7;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 1 WHERE idTipo = 3;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 2 WHERE idTipo = 4;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 3 WHERE idTipo = 5;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 6 WHERE idTipo = 6;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 1 WHERE idTipo = 7;
--rollback ALTER TABLE `integradb`.`flpmu_catalog_tipoperiodos` DROP COLUMN `nombreCiclo`, DROP COLUMN `multiplicador`;