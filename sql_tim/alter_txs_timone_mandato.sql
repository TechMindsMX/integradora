ALTER TABLE `integradb`.`flpmu_txs_timone_mandato` 
CHANGE COLUMN `idComision` `idComision` INT(11) NOT NULL AFTER `date`,
ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST,
ADD PRIMARY KEY (`id`);
