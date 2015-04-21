﻿--liquibase formatted sql

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

--changeset lutek:32
ALTER TABLE `flpmu_integrado_comisiones` ADD INDEX `integradoId` (`integradoId` ASC);
--rollback ALTER TABLE `flpmu_integrado_comisiones` DROP INDEX `integradoId`;

--changeset ricardolyon:33
ALTER TABLE `flpmu_ordenes_compra` CHANGE COLUMN `numOrden` `numOrden` INT (11);
ALTER TABLE `flpmu_ordenes_venta` CHANGE COLUMN `numOrden` `numOrden` INT (11);
--rollback ALTER TABLE `flpmu_ordenes_compra` CHANGE COLUMN `numOrden` `numOrden` BIGINT(20);
--rollback ALTER TABLE `flpmu_ordenes_venta` CHANGE COLUMN `numOrden` `numOrden` VARCHAR(255);

--changeset ricardolyon:34
INSERT INTO flpmu_extensions (extension_id, name, type, element, folder, client_id, enabled, access, protected, manifest_cache, params, custom_data, system_data, checked_out, checked_out_time, ordering, state) VALUES (10085, 'Conciliacion', 'component', 'com_conciliacionadmin', '', 1, 1, 1, 0, '{"name":"Conciliacion Admiin","type":"component","creationDate":"2014-10-17","author":"ismael","copyright":"Copyright (C) 2014. Todos los derechos reservados.","authorEmail":"aguilar_2001@hotmail.com","authorUrl":"http:\\/\\/","version":"1.0.0","description":"Movimientos registrados por STP","group":""}', '{}', '', '', 0, now(), 0, 0);
--rollback DELETE FROM flpmu_extensions WHERE extension_id = 10085;


--changeset lutek:35
ALTER TABLE flpmu_auth_mutuo ADD COLUMN integradoId INT (11) AFTER idOrden;
ALTER TABLE flpmu_auth_odc ADD COLUMN integradoId INT (11) AFTER idOrden;
ALTER TABLE flpmu_auth_odd ADD COLUMN integradoId INT (11) AFTER idOrden;
ALTER TABLE flpmu_auth_odv ADD COLUMN integradoId INT (11) AFTER idOrden;
ALTER TABLE flpmu_auth_odr ADD COLUMN integradoId INT (11) AFTER idOrden;
--rollback ALTER TABLE flpmu_auth_mutuo DROP COLUMN integradoId;
--rollback ALTER TABLE flpmu_auth_odc DROP COLUMN integradoId;
--rollback ALTER TABLE flpmu_auth_odd DROP COLUMN integradoId;
--rollback ALTER TABLE flpmu_auth_odv DROP COLUMN integradoId;
--rollback ALTER TABLE flpmu_auth_odr DROP COLUMN integradoId;

--changeset lutek:36
ALTER TABLE flpmu_ordenes_prestamo ADD COLUMN integradoIdA INT (11) AFTER tipo_movimiento;
ALTER TABLE flpmu_ordenes_prestamo ADD COLUMN integradoIdD INT (11) AFTER a_rfc;
--rollback ALTER TABLE flpmu_ordenes_prestamo DROP COLUMN integradoIdA;
--rollback ALTER TABLE flpmu_ordenes_prestamo DROP COLUMN integradoIdD;

--changeset lutek:37
UPDATE flpmu_catalog_tipoperiodos SET periodosAnio = 24, multiplicador = 15, nombreCiclo = 'D' WHERE IdTipo = 2;
--rollback UPDATE flpmu_catalog_tipoperiodos SET periodosAnio = 104, multiplicador = NULL, nombreCiclo = NULL WHERE IdTipo=2;

--changeset ricardolyon:38
CREATE TABLE `flpmu_txs_banco_timone_relation` (
  `id_txs_banco` INT,
  `id_txs_timone` INT,
  FOREIGN KEY (`id_txs_banco`) REFERENCES `flpmu_txs_banco_integrado`(id),
  FOREIGN KEY (`id_txs_timone`) REFERENCES `flpmu_txs_timone_mandato`(id)
);
--rollback DROP TABLE `flpmu_txs_banco_timone_relation`;

--changeset ricardolyon:39
CREATE TABLE `flpmu_txs_mandatos` (
  `id` INT NOT NULL,
  `amount` FLOAT,
  `orderType` VARCHAR(10),
  `idOrden` INT(11),
  FOREIGN KEY (`id`) REFERENCES `flpmu_txs_timone_mandato`(id)
);
--rollback DROP TABLE `flpmu_txs_mandatos`;

--changeset ricardolyon:40
ALTER TABLE `flpmu_txs_timone_mandato` DROP COLUMN idOrden;
ALTER TABLE `flpmu_txs_timone_mandato` DROP COLUMN tipoOrden;
--rollback ALTER TABLE `flpmu_txs_timone_mandato` ADD COLUMN idOrden INT;
--rollback ALTER TABLE `flpmu_txs_timone_mandato` ADD COLUMN tipoOrden VARCHAR(45);

--changeset ricardolyon:41
CREATE TABLE `flpmu_ordenes_odv_odc_relation` (
  `id_odv` INT NOT NULL,
  `id_odc` INT NOT NULL,
  FOREIGN KEY (`id_odv`) REFERENCES `flpmu_ordenes_venta`(id),
  FOREIGN KEY (`id_odc`) REFERENCES `flpmu_ordenes_compra`(id)
);
--rollback DROP TABLE `flpmu_ordenes_odv_odc_relation`;

--changeset ricardolyon:42
INSERT INTO flpmu_extensions (extension_id, name, type, element, folder, client_id, enabled, access, protected, manifest_cache, params, custom_data, system_data, checked_out, checked_out_time, ordering, state) VALUES ('10088', 'Integradoraservices', 'component', 'com_integradoraservices', '', '0', '1', '1', '0', '{\"name\":\"Integradoraservices\",\"type\":\"component\",\"creationDate\":\"Enero 2015\",\"author\":\"Ricardo Lyon\",\"copyright\":\"\",\"authorEmail\":\"ricardolyon@gmail.com\",\"authorUrl\":\"\",\"version\":\"0.0.1\",\"description\":\"Componente de servicios para Integradora\",\"group\":\"\",\"filename\":\"integradoraservices\"}', '{}', '', '', '0', now(), '0', '0');
--rollback DELETE FROM flpmu_extensions WHERE extension_id = 10088;

--changeset ricardolyon:43
UPDATE `flpmu_modules` SET `published`='0' WHERE `id`='101';
CREATE TABLE `flpmu_catalog_payment_methods` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tag` varchar(25) NOT NULL,
  `published` BOOLEAN
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT `flpmu_catalog_payment_methods` SET `tag` = 'LBL_SPEI', `published` = 1;
INSERT `flpmu_catalog_payment_methods` SET `tag` = 'LBL_DEPOSIT', `published` = 1;
INSERT `flpmu_catalog_payment_methods` SET `tag` = 'LBL_CHEQUE', `published` = 0;
--rollback UPDATE `flpmu_modules` SET `published`='1' WHERE `id`='101';
--rollback DROP TABLE `flpmu_catalog_payment_methods`;

--changeset ricardolyon:44
CREATE TABLE `flpmu_txs_liquidacion_saldo` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `amount` FLOAT NOT NULL,
  `integradoId` INT(11) NOT NULL,
  `date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--rollback DROP TABLE `flpmu_txs_liquidacion_saldo`;

--changeset lutek:45
ALTER TABLE `flpmu_facturas_comisiones` ADD COLUMN createdDate BIGINT;
--rollback ALTER TABLE `flpmu_facturas_comisiones` DROP COLUMN createdDate ;

--changeset ricardolyon:46
UPDATE `flpmu_extensions` SET `params`='{\"public_key\":\"6LdyTwUTAAAAAHR1ksTclf7Hg9ZWJpwBv5dHf7NF\",\"private_key\":\"6LdyTwUTAAAAAKylbEIE3sGrqx7zPbOVYnCFDU5P\",\"theme\":\"clean\"}' WHERE `extension_id`='439';
UPDATE `flpmu_extensions` SET `params`='{\"allowUserRegistration\":\"1\",\"new_usertype\":\"2\",\"guest_usergroup\":\"9\",\"sendpassword\":\"0\",\"useractivation\":\"1\",\"mail_to_admin\":\"0\",\"captcha\":\"recaptcha\",\"frontend_userparams\":\"0\",\"site_language\":\"0\",\"change_login_name\":\"0\",\"reset_count\":\"5\",\"reset_time\":\"1\",\"minimum_length\":\"10\",\"minimum_integers\":\"1\",\"minimum_symbols\":\"1\",\"minimum_uppercase\":\"1\",\"save_history\":\"0\",\"history_limit\":5,\"mailSubjectPrefix\":\"\",\"mailBodySuffix\":\"\"}' WHERE `extension_id`='25';
--rollback UPDATE `flpmu_extensions` SET `params`='{\"public_key\":\"\",\"private_key\":\"\",\"theme\":\"clean\"}' WHERE `extension_id`='439';
--rollback UPDATE `flpmu_extensions` SET `params`='{\"allowUserRegistration\":\"1\",\"new_usertype\":\"2\",\"guest_usergroup\":\"9\",\"sendpassword\":\"1\",\"useractivation\":\"1\",\"mail_to_admin\":\"0\",\"captcha\":\"\",\"frontend_userparams\":\"1\",\"site_language\":\"0\",\"change_login_name\":\"0\",\"reset_count\":\"10\",\"reset_time\":\"1\",\"minimum_length\":\"4\",\"minimum_integers\":\"0\",\"minimum_symbols\":\"0\",\"minimum_uppercase\":\"0\",\"save_history\":\"0\",\"history_limit\":5,\"mailSubjectPrefix\":\"\",\"mailBodySuffix\":\"\"}' WHERE `extension_id`='25';

--changeset ricardolyon:47
ALTER TABLE `flpmu_integrado` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `flpmu_auth_mutuo` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY flpmu_auth_mutuo_ibfk_1 (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_auth_odc` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY flpmu_auth_odc_ibfk_1 (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_auth_odd` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY flpmu_auth_odd_ibfk_1 (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_auth_odr` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY flpmu_auth_odr_ibfk_1 (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
--rollback ALTER TABLE `flpmu_auth_mutuo` DROP FOREIGN KEY `flpmu_auth_mutuo_ibfk_1`;
--rollback ALTER TABLE `flpmu_auth_mutuo` DROP INDEX flpmu_auth_mutuo_ibfk_1;
--rollback ALTER TABLE `flpmu_auth_mutuo` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_auth_odc` DROP FOREIGN KEY `flpmu_auth_odc_ibfk_1`;
--rollback ALTER TABLE `flpmu_auth_odc` DROP INDEX `flpmu_auth_odc_ibfk_1`;
--rollback ALTER TABLE `flpmu_auth_odc` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_auth_odd` DROP FOREIGN KEY `flpmu_auth_odd_ibfk_1`;
--rollback ALTER TABLE `flpmu_auth_odd` DROP INDEX `flpmu_auth_odd_ibfk_1`;
--rollback ALTER TABLE `flpmu_auth_odd` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_auth_odr` DROP FOREIGN KEY `flpmu_auth_odr_ibfk_1`;
--rollback ALTER TABLE `flpmu_auth_odr` DROP INDEX `flpmu_auth_odr_ibfk_1`;
--rollback ALTER TABLE `flpmu_auth_odr` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;

--changeset ricardolyon:48
ALTER TABLE `flpmu_auth_odv` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY flpmu_auth_odv_ibfk_1 (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_facturas_comisiones` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY flpmu_facturas_comisiones_ibfk_1 (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_clientes_proveedor` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_Id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_clientes_proveedor` COLLATE utf8_general_ci, CHANGE COLUMN `integradoIdCliente` `integradoIdCliente` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_comisiones` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
--rollback ALTER TABLE `flpmu_auth_odv` DROP FOREIGN KEY `flpmu_auth_odv_ibfk_1`;
--rollback ALTER TABLE `flpmu_auth_odv` DROP INDEX `flpmu_auth_odv_ibfk_1`;
--rollback ALTER TABLE `flpmu_auth_odv` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_facturas_comisiones` DROP FOREIGN KEY `flpmu_facturas_comisiones_ibfk_1`;
--rollback ALTER TABLE `flpmu_facturas_comisiones` DROP INDEX `flpmu_facturas_comisiones_ibfk_1`;
--rollback ALTER TABLE `flpmu_facturas_comisiones` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_clientes_proveedor` DROP FOREIGN KEY `flpmu_integrado_clientes_proveedor_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_clientes_proveedor` DROP FOREIGN KEY `flpmu_integrado_clientes_proveedor_ibfk_2`;
--rollback ALTER TABLE `flpmu_integrado_clientes_proveedor` CHANGE COLUMN `integradoId` `integrado_Id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_clientes_proveedor` CHANGE COLUMN `integradoIdCliente` `integradoIdCliente` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_comisiones` DROP FOREIGN KEY `flpmu_integrado_comisiones_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_comisiones` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;

--changeset ricardolyon:49
ALTER TABLE `flpmu_integrado_contacto` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_datos_bancarios` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_datos_empresa` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_datos_personales` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_instrumentos` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
--rollback ALTER TABLE `flpmu_integrado_contacto` DROP FOREIGN KEY `flpmu_integrado_contacto_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_contacto` CHANGE COLUMN `integradoId` `integrado_id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_datos_bancarios` DROP FOREIGN KEY `flpmu_integrado_datos_bancarios_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_datos_bancarios` CHANGE COLUMN `integradoId` `integrado_id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_datos_empresa` DROP FOREIGN KEY `flpmu_integrado_datos_empresa_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_datos_empresa` CHANGE COLUMN `integradoId` `integrado_id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_datos_personales` DROP FOREIGN KEY `flpmu_integrado_datos_personales_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_datos_personales` CHANGE COLUMN `integradoId` `integrado_id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_instrumentos` DROP FOREIGN KEY `flpmu_integrado_instrumentos_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_instrumentos` CHANGE COLUMN `integradoId` `integrado_id` INT(11) NOT NULL;

--changeset ricardolyon:50
ALTER TABLE `flpmu_integrado_params` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_products` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_proyectos` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_status_log` COLLATE utf8_general_ci, CHANGE COLUMN `status_log_integrado_id` `status_log_integrado_id` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`status_log_integrado_id`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_timone` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
--rollback ALTER TABLE `flpmu_integrado_params` DROP FOREIGN KEY `flpmu_integrado_params_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_params` CHANGE COLUMN `integradoId` `integrado_id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_products` DROP FOREIGN KEY `flpmu_integrado_products_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_products` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_proyectos` DROP FOREIGN KEY `flpmu_integrado_proyectos_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_proyectos` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_status_log` DROP FOREIGN KEY `flpmu_integrado_status_log_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_status_log` CHANGE COLUMN `status_log_integrado_id` `status_log_integrado_id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_timone` DROP FOREIGN KEY `flpmu_integrado_timone_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_timone` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;

--changeset ricardolyon:51
ALTER TABLE `flpmu_integrado_users` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_integrado_verificacion_solicitud` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_mandatos_clientes` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_mandatos_mutuos` COLLATE utf8_general_ci, CHANGE COLUMN `integradoIdE` `integradoIdE` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoIdE`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_mandatos_mutuos` COLLATE utf8_general_ci, CHANGE COLUMN `integradoIdR` `integradoIdR` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoIdR`) REFERENCES `flpmu_integrado` (`integradoId`);
--rollback ALTER TABLE `flpmu_integrado_users` DROP FOREIGN KEY `flpmu_integrado_users_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_users` CHANGE COLUMN `integradoId` `integrado_id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado_verificacion_solicitud` DROP FOREIGN KEY `flpmu_integrado_verificacion_solicitud_ibfk_1`;
--rollback ALTER TABLE `flpmu_integrado_verificacion_solicitud` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_mandatos_clientes` DROP FOREIGN KEY `flpmu_mandatos_clientes_ibfk_1`;
--rollback ALTER TABLE `flpmu_mandatos_clientes` CHANGE COLUMN `integradoId` `integrado_id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_mandatos_mutuos` DROP FOREIGN KEY `flpmu_mandatos_mutuos_ibfk_1`;
--rollback ALTER TABLE `flpmu_mandatos_mutuos` DROP FOREIGN KEY `flpmu_mandatos_mutuos_ibfk_2`;
--rollback ALTER TABLE `flpmu_mandatos_mutuos` CHANGE COLUMN `integradoIdE` `integradoIdE` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_mandatos_mutuos` CHANGE COLUMN `integradoIdR` `integradoIdR` INT(11) NOT NULL;

--changeset ricardolyon:52
ALTER TABLE `flpmu_mandatos_productos` COLLATE utf8_general_ci, CHANGE COLUMN `integrado_id` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_ordenes_compra` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_ordenes_deposito` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_ordenes_retiro` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_ordenes_venta` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
--rollback ALTER TABLE `flpmu_mandatos_productos` DROP FOREIGN KEY `flpmu_mandatos_productos_ibfk_1`;
--rollback ALTER TABLE `flpmu_mandatos_productos` CHANGE COLUMN `integradoId` `integrado_id` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_ordenes_compra` DROP FOREIGN KEY `flpmu_ordenes_compra_ibfk_1`;
--rollback ALTER TABLE `flpmu_ordenes_compra` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_ordenes_deposito` DROP FOREIGN KEY `flpmu_ordenes_deposito_ibfk_1`;
--rollback ALTER TABLE `flpmu_ordenes_deposito` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_ordenes_retiro` DROP FOREIGN KEY `flpmu_ordenes_retiro_ibfk_1`;
--rollback ALTER TABLE `flpmu_ordenes_retiro` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_ordenes_venta` DROP FOREIGN KEY `flpmu_ordenes_venta_ibfk_1`;
--rollback ALTER TABLE `flpmu_ordenes_venta` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;

--changeset ricardolyon:53
ALTER TABLE `flpmu_reportes_balance` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_txs_banco_integrado` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_txs_liquidacion_saldo` COLLATE utf8_general_ci, CHANGE COLUMN `integradoId` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
ALTER TABLE `flpmu_txs_timone_mandato` COLLATE utf8_general_ci, CHANGE COLUMN `idIntegrado` `integradoId` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, ADD FOREIGN KEY (`integradoId`) REFERENCES `flpmu_integrado` (`integradoId`);
--rollback ALTER TABLE `flpmu_reportes_balance` DROP FOREIGN KEY `flpmu_reportes_balance_ibfk_1`;
--rollback ALTER TABLE `flpmu_reportes_balance` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_txs_banco_integrado` DROP FOREIGN KEY `flpmu_txs_banco_integrado_ibfk_1`;
--rollback ALTER TABLE `flpmu_txs_banco_integrado` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_txs_liquidacion_saldo` DROP FOREIGN KEY `flpmu_txs_liquidacion_saldo_ibfk_1`;
--rollback ALTER TABLE `flpmu_txs_liquidacion_saldo` CHANGE COLUMN `integradoId` `integradoId` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_txs_timone_mandato` DROP FOREIGN KEY `flpmu_txs_timone_mandato_ibfk_1`;
--rollback ALTER TABLE `flpmu_txs_timone_mandato` CHANGE COLUMN `integradoId` `idIntegrado` INT(11) NOT NULL;
--rollback ALTER TABLE `flpmu_integrado` CHANGE COLUMN `integradoId` `integrado_id` INT(11) AUTO_INCREMENT NOT NULL;

--changeset ricardolyon:54
CREATE TABLE `flpmu_users_security_questions` (
  `user_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `answer` VARCHAR(255) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `flpmu_users`(id)

);
--rollback DROP TABLE `flpmu_users_security_questions`;

--changeset ricardolyon:55
CREATE TABLE `flpmu_security_questions` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `question` VARCHAR(255) NOT NULL
);
--rollback DROP TABLE `flpmu_security_questions`;

--changeset  ricardolyon:56
INSERT `flpmu_security_questions` SET `question` = '¿Cuál fue su apodo de la infancia?';
INSERT `flpmu_security_questions` SET `question` = '¿En qué ciudad se conocieron su cónyuge / pareja?';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál es el nombre de tu amigo favorito de la infancia?';
INSERT `flpmu_security_questions` SET `question` = '¿En qué calle vivía usted en el tercer grado?';
INSERT `flpmu_security_questions` SET `question` = '¿Cual es el mes del cumpleaños de su hermano mayor y el año? (Por ejemplo, Enero de 1900)';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál es el segundo nombre de su hijo mayor?';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál es el segundo nombre de su hermano mayor?';
INSERT `flpmu_security_questions` SET `question` = '¿A qué escuela fue usted al sexto grado?';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál fue su número de teléfono de la niñez incluyendo código de área? (Por ejemplo, 000-000-0000)';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál es el nombre y apellido de su primo mayor?';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál era el nombre de su primer animal de peluche?';
INSERT `flpmu_security_questions` SET `question` = '¿En qué ciudad o pueblo conocieron su madre y su padre?';
INSERT `flpmu_security_questions` SET `question` = '¿Dónde estabas cuando tuviste tu primer beso?';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál es el primer nombre del niño o niña que primero besaste?';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál fue el apellido de su maestra de tercer grado?';
INSERT `flpmu_security_questions` SET `question` = '¿En qué ciudad vive su hermano más cercano?';
INSERT `flpmu_security_questions` SET `question` = '¿Qué es el mes del cumpleaños de su hermano mayor y el año? (Por ejemplo, Enero de 1900)';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál es el apellido de soltera de su abuela materna?';
INSERT `flpmu_security_questions` SET `question` = '¿En qué ciudad o pueblo fue tu primer trabajo?';
INSERT `flpmu_security_questions` SET `question` = '¿Cuál es el nombre del lugar de la recepción de su boda?';
INSERT `flpmu_security_questions` SET `question` = 'Cuál es el nombre de una universidad a la que aplicó a pero no asistió?';
--rollback TRUNCATE `flpmu_security_questions`;