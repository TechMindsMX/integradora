ALTER TABLE `integradb`.`flpmu_bitacora_status_odc` 
ADD COLUMN `result` BIT(1) NOT NULL AFTER `newStatus`;

ALTER TABLE `integradb`.`flpmu_bitacora_status_odd` 
ADD COLUMN `result` BIT(1) NOT NULL AFTER `newStatus`;

ALTER TABLE `integradb`.`flpmu_bitacora_status_odr` 
ADD COLUMN `result` BIT(1) NOT NULL AFTER `newStatus`;

ALTER TABLE `integradb`.`flpmu_bitacora_status_odv` 
ADD COLUMN `result` BIT(1) NOT NULL AFTER `newStatus`;

ALTER TABLE `integradb`.`flpmu_ordenes_venta` 
CHANGE COLUMN `created` `createdDate` BIGINT(20) NULL DEFAULT NULL ,
CHANGE COLUMN `payment` `paymentDate` BIGINT(20) NULL DEFAULT NULL ;
