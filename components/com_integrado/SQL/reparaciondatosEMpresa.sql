DROP TABLE IF EXISTS `integradb`.`_temp_flpmu_integrado_datos_empresa`;

CREATE TABLE `integradb`.`_temp_flpmu_integrado_datos_empresa` (
 `integradoId` int(10) NOT NULL,
 `razon_social` varchar(255) DEFAULT NULL,
 `rfc` varchar(45) DEFAULT NULL,
 `calle` varchar(45) DEFAULT NULL,
 `num_exterior` varchar(45) DEFAULT NULL,
 `num_interior` varchar(45) DEFAULT NULL,
 `cod_postal` varchar(5) DEFAULT NULL,
 `tel_fijo` varchar(10) DEFAULT NULL,
 `tel_fijo_extension` varchar(10) DEFAULT NULL,
 `tel_fax` varchar(10) DEFAULT NULL,
 `sitio_web` varchar(255) DEFAULT NULL,
 `testimonio_1` int(10) UNSIGNED DEFAULT NULL,
 `testimonio_2` int(10) UNSIGNED DEFAULT NULL,
 `poder` int(10) UNSIGNED DEFAULT NULL,
 `reg_propiedad` int(10) UNSIGNED DEFAULT NULL,
 `url_rfc` varchar(50) DEFAULT NULL
)
ENGINE = InnoDB
CHARACTER SET = utf8
ROW_FORMAT = COMPACT;

INSERT INTO `integradb`.`_temp_flpmu_integrado_datos_empresa`(
               `calle`,
               `cod_postal`,
               `integradoId`,
               `num_exterior`,
               `num_interior`,
               `poder`,
               `razon_social`,
               `reg_propiedad`,
               `rfc`,
               `sitio_web`,
               `tel_fax`,
               `tel_fijo`,
               `tel_fijo_extension`,
               `testimonio_1`,
               `testimonio_2`)
   SELECT `calle`,
          `cod_postal`,
          `integradoId`,
          `num_exterior`,
          `num_interior`,
          `poder`,
          `razon_social`,
          `reg_propiedad`,
          `rfc`,
          `sitio_web`,
          `tel_fax`,
          `tel_fijo`,
          `tel_fijo_extension`,
          `testimonio_1`,
          `testimonio_2`
     FROM `integradb`.`flpmu_integrado_datos_empresa`;

DROP TABLE `integradb`.`flpmu_integrado_datos_empresa`;

ALTER TABLE `integradb`.`_temp_flpmu_integrado_datos_empresa` RENAME `flpmu_integrado_datos_empresa`;