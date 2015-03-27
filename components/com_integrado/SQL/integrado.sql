DROP TABLE IF EXISTS `integradb`.`_temp_flpmu_integrado`;

CREATE TABLE `integradb`.`_temp_flpmu_integrado` (
 `integradoId` int(10) NOT NULL,
 `status` tinyint(4) DEFAULT '0',
 `pers_juridica` tinyint(4) DEFAULT '0',
 UNIQUE INDEX `idIntegrados_UNIQUE` ( `integradoId` ),
 PRIMARY KEY  ( `integradoId` )
)
ENGINE = InnoDB
CHARACTER SET = utf8
ROW_FORMAT = COMPACT;

INSERT INTO `integradb`.`_temp_flpmu_integrado`(`integradoId`,
                                                 `pers_juridica`,
                                                 `status`)
   SELECT `integradoId`, `pers_juridica`, `status`
     FROM `integradb`.`flpmu_integrado`;

DROP TABLE `integradb`.`flpmu_integrado`;

ALTER TABLE `integradb`.`_temp_flpmu_integrado` RENAME `flpmu_integrado`;