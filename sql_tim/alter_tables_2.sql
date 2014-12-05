ALTER TABLE `integradb`.`flpmu_ordenes_venta` 
ADD COLUMN `totalAmount` FLOAT NULL AFTER `paymentDate`,
ADD COLUMN `urlXML` VARCHAR(255) NULL AFTER `totalAmount`;
