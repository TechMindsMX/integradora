DROP TABLE IF EXISTS `integradb`.`_temp_flpmu_integrado`;

CREATE TABLE `integradb`.`_temp_flpmu_integrado` (
 `integrado_id` int(10) NOT NULL,
 `status` tinyint(4) DEFAULT '0',
 `pers_juridica` tinyint(4) DEFAULT '0',
 UNIQUE INDEX `idIntegrados_UNIQUE` ( `integrado_id` ),
 PRIMARY KEY  ( `integrado_id` )
)
ENGINE = InnoDB
CHARACTER SET = utf8
ROW_FORMAT = COMPACT;

INSERT INTO `integradb`.`_temp_flpmu_integrado`(`integrado_id`,
                                                 `pers_juridica`,
                                                 `status`)
   SELECT `integrado_id`, `pers_juridica`, `status`
     FROM `integradb`.`flpmu_integrado`;

DROP TABLE `integradb`.`flpmu_integrado`;

ALTER TABLE `integradb`.`_temp_flpmu_integrado` RENAME `flpmu_integrado`;