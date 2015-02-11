--liquibase formatted sql

--changeset ricardolyon:1
CREATE TABLE `flpmu_catalog_order_status` (
  `id` int(11) NOT NULL,
  `statusName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO flpmu_catalog_order_status (id, statusName) VALUES (1, 'Nueva');
INSERT INTO flpmu_catalog_order_status (id, statusName) VALUES (3, 'En autorización');
INSERT INTO flpmu_catalog_order_status (id, statusName) VALUES (5, 'Autorizada');
INSERT INTO flpmu_catalog_order_status (id, statusName) VALUES (8, 'Procesando');
INSERT INTO flpmu_catalog_order_status (id, statusName) VALUES (13, 'Pagada');
INSERT INTO flpmu_catalog_order_status (id, statusName) VALUES (21, 'Liquidada');
INSERT INTO flpmu_catalog_order_status (id, statusName) VALUES (34, 'Devuelta');
INSERT INTO flpmu_catalog_order_status (id, statusName) VALUES (55, 'Cancelada');
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
ALTER TABLE `flpmu_mandatos_mutuos`
CHANGE COLUMN `expirationDate` `paymentPeriod` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `payments` `quantityPayments` INT(11) NULL DEFAULT NULL ;
--rollback DROP TABLE `flpmu_mandatos_mutuos`;

--changeset lutek:3
ALTER TABLE `flpmu_mandatos_mutuos`
CHANGE COLUMN `cuenta` `idCuenta` INT(11) NULL DEFAULT NULL ,
ADD COLUMN `cuotaOcapital` INT(11) NULL AFTER `interes`;
--rollback ALTER TABLE flpmu_mandatos_mutuos CHANGE COLUMN `idCuenta` `cuenta` INT(11) NULL DEFAULT NULL;
--rollback ALTER TABLE flpmu_mandatos_mutuos DROP cuotaOcapital;

--changeset ricardolyon:4
ALTER TABLE flpmu_txs_timone_mandato CHANGE idTx idTx VARCHAR(255);
ALTER TABLE flpmu_txs_timone_mandato ADD CONSTRAINT unique_idTx UNIQUE (idTx);
ALTER TABLE `flpmu_catalog_order_status` CHANGE COLUMN `statusName` `name` VARCHAR(45) NOT NULL ,ADD UNIQUE INDEX `name_UNIQUE` (`name` ASC);
--rollback ALTER TABLE flpmu_txs_timone_mandato CHANGE idTx idTx INT(11);
--rollback ALTER TABLE flpmu_txs_timone_mandato DROP INDEX unique_idTx;
--rollback ALTER TABLE `flpmu_catalog_order_status` CHANGE COLUMN `name` `statusName` VARCHAR(45) NOT NULL ,DROP INDEX `name_UNIQUE`;

--changeset lutek:5
DROP TABLE IF EXISTS `flpmu_catalog_tipoperiodos`;
CREATE TABLE `flpmu_catalog_tipoperiodos` (
  `IdTipo` INT(11) NULL,
  `nombre` VARCHAR(45) NULL,
  `periodosAnio` INT(11) NULL);
ALTER TABLE `flpmu_mandatos_mutuos` ADD COLUMN `status` INT(11) NULL AFTER `cuotaOcapital`;

INSERT INTO `flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(2,'Quincenal',104);
INSERT INTO `flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(3,'Mensual',12);
INSERT INTO `flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(4,'Bimestral',6);
INSERT INTO `flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(5,'Trimestral',4);
INSERT INTO `flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(6,'Semestral',2);
INSERT INTO `flpmu_catalog_tipoperiodos` (`IdTipo`,`nombre`,`periodosAnio`)VALUES(7,'Anual',1);
--rollback DROP TABLE `flpmu_catalog_tipoperiodos`;
--rollback ALTER TABLE `flpmu_mandatos_mutuos` DROP `status`;

--changeset lutek:6
DROP TABLE IF EXISTS `flpmu_integrado_timone`;
CREATE TABLE `flpmu_integrado_timone` (
  `integradoId` INT NOT NULL,
  `timOneId` INT NULL,
  `account` BIGINT NULL,
  PRIMARY KEY (`integradoId`));
--rollback DROP TABLE `flpmu_integrado_timone`;

--changeset ricardolyon:7
DROP TABLE IF EXISTS `flpmu_reportes_balance`;
CREATE TABLE `flpmu_reportes_balance` (
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
--rollback DROP TABLE `flpmu_reportes_balance`;

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
DROP TABLE IF EXISTS `flpmu_auth_mutuo`;
CREATE TABLE `flpmu_auth_mutuo` (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  idOrden INT NOT NULL,
  userId INT NOT NULL,
  authDate BIGINT NOT NULL
  );
--rollback DROP TABLE `flpmu_auth_mutuo`;

--changeset ricardolyon:10
ALTER TABLE `flpmu_reportes_balance` CHANGE COLUMN `integradoId` `integradoId` INT NOT NULL;
--rollback ALTER  TABLE `flpmu_reportes_balance` CHANGE COLUMN `integradoId` `integradoId` VARCHAR(45) NOT NULL;

--changeset ricardolyon:11
DROP TABLE IF EXISTS `flpmu_integrado_params`;
CREATE TABLE `flpmu_integrado_params` (
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `integradoId` INT NOT NULL,
  `params` VARCHAR(255) NULL
);
--rollback DROP TABLE `flpmu_integrado_params`;


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
ALTER TABLE `flpmu_ordenes_prestamo`
CHANGE COLUMN `numOrden` `numOrden` VARCHAR(50) NULL ,
CHANGE COLUMN `tipo_movimiento` `tipo_movimiento` VARCHAR(100) NULL ,
CHANGE COLUMN `acreedor` `acreedor` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `a_rfc` `a_rfc` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `deudor` `deudor` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `d_rfc` `d_rfc` VARCHAR(100) NULL DEFAULT NULL ;
--rollback ALTER TABLE `flpmu_ordenes_prestamo` CHANGE COLUMN `numOrden` `numOrden` INT(10) NULL ,CHANGE COLUMN `tipo_movimiento` `tipo_movimiento` VARCHAR(45) NULL ,CHANGE COLUMN `acreedor` `acreedor` VARCHAR(45) NULL DEFAULT NULL ,CHANGE COLUMN `a_rfc` `a_rfc` VARCHAR(45) NULL DEFAULT NULL ,CHANGE COLUMN `deudor` `deudor` VARCHAR(45) NULL DEFAULT NULL ,CHANGE COLUMN `d_rfc` `d_rfc` VARCHAR(45) NULL DEFAULT NULL ;

--changeset lutek:16
ALTER TABLE `flpmu_catalog_tipoperiodos`
ADD COLUMN `multiplicador` INT(11) NULL AFTER `periodosAnio`,
ADD COLUMN `nombreCiclo` VARCHAR(45) NULL AFTER `multiplicador`;
UPDATE flpmu_catalog_tipoperiodos SET nombreCiclo = 'M' WHERE idTipo IN (3,4,5,6);
UPDATE flpmu_catalog_tipoperiodos SET nombreCiclo = 'Y' WHERE idTipo = 7;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 1 WHERE idTipo = 3;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 2 WHERE idTipo = 4;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 3 WHERE idTipo = 5;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 6 WHERE idTipo = 6;
UPDATE flpmu_catalog_tipoperiodos SET multiplicador = 1 WHERE idTipo = 7;
--rollback ALTER TABLE `flpmu_catalog_tipoperiodos` DROP COLUMN `nombreCiclo`, DROP COLUMN `multiplicador`;

--changeset ricardolyon:17
INSERT INTO flpmu_menu (id, menutype, title, alias, note, path, link, type, published, parent_id, level, component_id, checked_out, checked_out_time, browserNav, access, img, template_style_id, params, lft, rgt, home, language, client_id) VALUES (204, 'mainmenu', 'Integrado', 'integrado', '', 'integrado', 'index.php?option=com_integrado', 'url', 1, 1, 1, 0, 0, null, 0, 1, '', 0, '{"menu-anchor_title":"","menu-anchor_css":"","menu_image":"","menu_text":1}', 179, 180, 0, '*', 0);
--rollback DELETE FROM flpmu_menu WHERE id = 204;

--changeset lutek:18
ALTER TABLE  `flpmu_bitacora_status_mutuo` ADD  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE  `flpmu_bitacora_status_odc` CHANGE  `id`  `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE  `flpmu_bitacora_status_odd` CHANGE  `id`  `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE  `flpmu_bitacora_status_odr` CHANGE  `id`  `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE  `flpmu_bitacora_status_odv` CHANGE  `id`  `id` INT( 11 ) NOT NULL AUTO_INCREMENT;
--rollback ALTER TABLE `flpmu_bitacora_status_mutuo` DROP `id`;
--rollback ALTER TABLE `flpmu_bitacora_status_odc` CHANGE  `id`  `id` INT( 11 ) NOT NULL;
--rollback ALTER TABLE `flpmu_bitacora_status_odd` CHANGE  `id`  `id` INT( 11 ) NOT NULL;
--rollback ALTER TABLE `flpmu_bitacora_status_odr` CHANGE  `id`  `id` INT( 11 ) NOT NULL;
--rollback ALTER TABLE `flpmu_bitacora_status_odv` CHANGE  `id`  `id` INT( 11 ) NOT NULL;

--changeset ricardolyon:19
CREATE TABLE `flpmu_integrado_verificacion_solicitud` (
  `integradoId` INT NOT NULL,
`datos_personales` VARCHAR(1024) NOT NULL,
`datos_empresa` VARCHAR(1024) NOT NULL,
  `datos_bancarios` TEXT NOT NULL,
`instrumentos` VARCHAR(1024) NOT NULL
);
--rollback DROP TABLE `flpmu_integrado_verificacion_solicitud`;

--changeset lutek:20
ALTER TABLE  `flpmu_integrado_params` CHANGE  `integradoId`  `integrado_id` INT( 11 ) NOT NULL
--rollback ALTER TABLE `flpmu_integrado_params` CHANGE `integrado_id` `integradoId` INT(11) NOT NULL

--changeset lutek:21
ALTER TABLE  `flpmu_integrado_timone` CHANGE  `timOneId`  `timoneUuid` VARCHAR( 50 ) NULL DEFAULT NULL ,
CHANGE  `account`  `stpClabe` BIGINT( 20 ) NULL DEFAULT NULL
--rollback ALTER TABLE  `flpmu_integrado_timone` CHANGE  `timoneUuid`  `timOneId` INT( 11 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , CHANGE  `stpClabe`  `account` BIGINT( 20 ) NULL DEFAULT NULL

--changeset ricardolyon:22
CREATE TABLE `flpmu_catalog_permission_levels` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(1024) NOT NULL
);
INSERT INTO `flpmu_catalog_permission_levels` (id, name) VALUES (1, 'Consulta');
INSERT INTO `flpmu_catalog_permission_levels` (id, name) VALUES (2, 'Operaciones');
INSERT INTO `flpmu_catalog_permission_levels` (id, name) VALUES (3, 'Autorizador');
INSERT INTO `flpmu_catalog_permission_levels` (id, name) VALUES (4, 'Full');

--rollback DROP TABLE `flpmu_catalog_permission_levels`;

--changeset lutek:23
CREATE TABLE `flpmu_catalogo_ivas` (
  `valor` int(11) NOT NULL,
  `leyenda` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO  `flpmu_catalogo_ivas` (`valor` ,`leyenda`) VALUES ('0', '0%'), ('11', '11%'), ('16', '16%');

--rollback DROP TABLE `flpmu_catalogo_ivas`;

--changeset ricardolyon:24
UPDATE flpmu_menu SET access = 2 WHERE id IN (193,195,204);
--rollback UPDATE flpmu_menu SET access = 1 WHERE id IN (193,195,204);

--changeset lutek:25
ALTER TABLE `flpmu_catalogo_ivas` ADD PRIMARY KEY (`valor`), ADD UNIQUE INDEX `valor_UNIQUE` (`valor` ASC);
UPDATE `flpmu_catalogo_ivas` SET  `leyenda` = '0', `valor` = 1  WHERE `valor` = 0;
UPDATE `flpmu_catalogo_ivas` SET  `leyenda` = '11', `valor` = 2 WHERE `valor` = 11;
UPDATE `flpmu_catalogo_ivas` SET  `leyenda` = '16', `valor` = 3 WHERE `valor` = 16;
ALTER TABLE `flpmu_catalogo_ivas` CHANGE COLUMN `leyenda` `leyenda` FLOAT (11) NOT NULL ;
--rollback UPDATE `flpmu_catalogo_ivas` SET  `leyenda` = '0%', `valor` = 0 WHERE `valor` = 1;
--rollback UPDATE `flpmu_catalogo_ivas` SET  `leyenda` = '11%', `valor` = 11 WHERE `valor` = 2;
--rollback UPDATE `flpmu_catalogo_ivas` SET  `leyenda` = '16%', `valor` = 16 WHERE `valor` = 3;
--rollback ALTER TABLE `flpmu_catalogo_ivas` CHANGE COLUMN `leyenda` `leyenda` VARCHAR(255) NOT NULL ;

--changeset ricardolyon:26
ALTER TABLE `flpmu_integrado_clientes_proveedor` ADD COLUMN `bancos` VARCHAR(255);
--rollback ALTER TABLE `flpmu_integrado_clientes_proveedor` DROP COLUMN `bancos`;

--changeset lutek:27
ALTER TABLE `flpmu_integrado` ADD COLUMN `createdDate` BIGINT (20);
--rollback ALTER  TABLE `flpmu_integrado` DROP COLUMN `createdDate`;

--changeset lutek:28
ALTER TABLE `flpmu_integrado_verificacion_solicitud` ADD COLUMN `params` varchar (20);
--rollback ALTER  TABLE `flpmu_integrado_verificacion_solicitud` DROP COLUMN `params`;

--changeset ricardolyon:29
ALTER TABLE `flpmu_ordenes_deposito` CHANGE `totalAmount` `totalAmount` FLOAT (11) NOT NULL;
--rollback ALTER TABLE `flpmu_ordenes_deposito` CHANGE `totalAmount` `totalAmount` INT;

--changeset lutek:30
ALTER TABLE `flpmu_ordenes_compra` ADD COLUMN `bankId` INT (11);
--rollback ALTER TABLE `flpmu_ordenes_compra` DROP COLUMN `bankId`;

--changeset ricardolyon:31
INSERT INTO flpmu_extensions (extension_id, name, type, element, folder, client_id, enabled, access, protected, manifest_cache, params, custom_data, system_data, checked_out, checked_out_time, ordering, state) VALUES (10082, 'System - Integralib', 'plugin', 'integralib', 'system', 0, 1, 1, 0, '{"name":"System - Integralib","type":"plugin","creationDate":"February 2015","author":"Ricardo Lyon","copyright":"","authorEmail":"ricardolyon@gmail.com","authorUrl":"","version":"1.0.0","description":"Simple plugin to register custom library.","group":""}', '{}', '', '', 0, '2015-02-10', 0, 0);
--rollback DELETE FROM flpmu_extensions WHERE extension_id = 10082;