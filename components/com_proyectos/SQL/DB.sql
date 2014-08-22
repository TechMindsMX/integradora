ALTER TABLE `integradb`.`flpmu_integrado_users` 
ADD COLUMN `integrado_principal` BIT(1) NULL AFTER `integrado_id`;

ALTER TABLE `integradb`.`flpmu_integrado_users` 
ADD COLUMN `integrado_permission_level` INT(10) NULL DEFAULT 0 AFTER `integrado_principal`;

ALTER TABLE `integradb`.`flpmu_integrado_users` 
CHANGE COLUMN `integrado_id` `integrado_id` INT(10) NOT NULL ;

ALTER TABLE `integradb`.`flpmu_integrado_users` 
DROP PRIMARY KEY;
