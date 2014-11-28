CREATE TABLE `integradb`.`flpmu_ordenes_compra` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `proyecto` INT NULL,
  `proveedor` INT NULL,
  `integradoId` INT NULL,
  `numOrden` BIGINT NULL,
  `createdDate` BIGINT NULL,
  `paymentDate` BIGINT NULL,
  `paymentMethod` INT NULL,
  `factura` TEXT NULL,
  `observaciones` TEXT NULL,
  `status` INT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE  `flpmu_ordenes_venta` CHANGE  `numOdv`  `numOrden` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
