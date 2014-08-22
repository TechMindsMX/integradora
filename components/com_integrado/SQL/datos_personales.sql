DROP TABLE IF EXISTS `integradb`.`_temp_flpmu_integrado_datos_personales`;

CREATE TABLE `integradb`.`_temp_flpmu_integrado_datos_personales` (
 `integrado_id` int(10) NOT NULL,
 `nacionalidad` varchar(45) DEFAULT NULL,
 `sexo` varchar(45) DEFAULT NULL,
 `fecha_nacimiento` date DEFAULT NULL,
 `rfc` varchar(45) DEFAULT NULL,
 `calle` varchar(45) DEFAULT NULL,
 `num_exterior` varchar(45) DEFAULT NULL,
 `num_interior` varchar(45) DEFAULT NULL,
 `cod_postal` varchar(5) DEFAULT NULL,
 `curp` varchar(18) DEFAULT NULL,
 `email` varchar(100) DEFAULT NULL,
 `tel_fijo` varchar(10) DEFAULT NULL,
 `tel_fijo_extension` varchar(10) DEFAULT NULL,
 `tel_movil` varchar(13) DEFAULT NULL,
 `nom_comercial` varchar(100) DEFAULT NULL,
 `url_identificacion` varchar(255) DEFAULT NULL,
 `url_rfc` varchar(255) DEFAULT NULL,
 `url_comprobante_domicilio` varchar(255) DEFAULT NULL
)
ENGINE = InnoDB
CHARACTER SET = utf8
ROW_FORMAT = COMPACT;

INSERT INTO `integradb`.`_temp_flpmu_integrado_datos_personales`(
               `calle`,
               `cod_postal`,
               `fecha_nacimiento`,
               `integrado_id`,
               `nacionalidad`,
               `nom_comercial`,
               `num_exterior`,
               `num_interior`,
               `rfc`,
               `sexo`,
               `tel_fijo`,
               `tel_fijo_extension`,
               `tel_movil`,
               `url_comprobante_domicilio`,
               `url_identificacion`,
               `url_rfc`)
   SELECT `calle`,
          `cod_postal`,
          `fecha_nacimiento`,
          `integrado_id`,
          `nacionalidad`,
          `nom_comercial`,
          `num_exterior`,
          `num_interior`,
          `rfc`,
          `sexo`,
          `tel_fijo`,
          `tel_fijo_extension`,
          `tel_movil`,
          `url_comprobante_domicilio`,
          `url_identificacion`,
          `url_rfc`
     FROM `integradb`.`flpmu_integrado_datos_personales`;

DROP TABLE `integradb`.`flpmu_integrado_datos_personales`;

ALTER TABLE `integradb`.`_temp_flpmu_integrado_datos_personales` RENAME `flpmu_integrado_datos_personales`;