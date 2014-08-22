DROP TABLE IF EXISTS `integradb`.`_temp_flpmu_integrado_instrumentos`;

CREATE TABLE `integradb`.`_temp_flpmu_integrado_instrumentos` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `integrado_id` int(11) NOT NULL,
 `instrum_type` tinyint(4) DEFAULT NULL,
 `instrum_fecha` date DEFAULT NULL,
 `instrum_notaria` int(3) DEFAULT NULL,
 `instrum_estado` varchar(45) DEFAULT NULL,
 `instrum_num_instrumento` int(10) DEFAULT NULL,
 `url_instrumento` varchar(255) DEFAULT NULL,
 `instrum_nom_notario` varchar(255) DEFAULT NULL,
 UNIQUE INDEX `id_UNIQUE` ( `id` ),
 PRIMARY KEY  ( `id` )
)
ENGINE = InnoDB
CHARACTER SET = utf8
AUTO_INCREMENT = 5
ROW_FORMAT = COMPACT;

INSERT INTO `integradb`.`_temp_flpmu_integrado_instrumentos`(
               `id`,
               `instrum_estado`,
               `instrum_fecha`,
               `instrum_nom_notario`,
               `instrum_notaria`,
               `instrum_num_instrumento`,
               `instrum_type`)
   SELECT `id`,
          `instrum_estado`,
          `instrum_fecha`,
          `instrum_nom_notario`,
          `instrum_notaria`,
          `instrum_num_instrumento`,
          `instrum_type`
     FROM `integradb`.`flpmu_integrado_instrumentos`;

DROP TABLE `integradb`.`flpmu_integrado_instrumentos`;

ALTER TABLE `integradb`.`_temp_flpmu_integrado_instrumentos` RENAME `flpmu_integrado_instrumentos`;