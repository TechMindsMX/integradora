INSERT INTO `flpmu_integrado` (`integradoId`, `status`, `pers_juridica`) VALUES ('1', '1', '1');

INSERT INTO `flpmu_integrado_users` (`integradoId`, `user_id`, `integrado_principal`, `integrado_permission_level`) VALUES ('1', '93', b'1', '3');

INSERT INTO `flpmu_integrado_datos_personales` (`integradoId`, `nombre_representante`, `nacionalidad`, `sexo`, `fecha_nacimiento`, `rfc`, `calle`, `num_exterior`, `num_interior`, `cod_postal`, `curp`, `email`, `tel_fijo`, `tel_fijo_extension`, `tel_movil`, `nom_comercial`, `url_identificacion`, `url_rfc`, `url_comprobante_domicilio`) VALUES ('1', 'Integradora', NULL, NULL, NULL, NULL, 'Tiburcio Montiel', '80', 'B3', '11810', NULL, NULL, NULL, NULL, NULL, 'Integradora de Emprendimiento culturales S.A. de C.V.', NULL, NULL, NULL);

INSERT INTO `flpmu_integrado_datos_empresa` (`integradoId`, `razon_social`, `rfc`, `calle`, `num_exterior`, `num_interior`, `cod_postal`, `tel_fijo`, `tel_fijo_extension`, `tel_fax`, `sitio_web`, `testimonio_1`, `testimonio_2`, `poder`, `reg_propiedad`, `url_rfc`) VALUES ('1', 'Integradora de Emprendimientos Culturales S.A. de C.V.', 'IEC121203FV8', 'Tiburcio Montiel', '80', 'B3', '11850', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `flpmu_integrado_datos_bancarios` (`datosBan_id` , `integradoId` , `banco_codigo` , `banco_cuenta` ,`banco_sucursal` ,`banco_clabe` ,`banco_file`) VALUES (NULL ,  '1',  '014',  '0141805663',  '0141',  '014180566389265712', NULL);

INSERT INTO  `integradb`.`flpmu_integrado_params` (`id` , `integradoId` , `params`) VALUES (NULL ,  '1',  '1');