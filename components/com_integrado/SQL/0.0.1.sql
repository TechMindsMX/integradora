CREATE TABLE `flpmu_integrado_permisos` (
  `view_component` varchar(50) CHARACTER SET latin1 NOT NULL,
  `min_to_view` varchar(255) NOT NULL,
  `min_to_edit` varchar(255) NOT NULL,
  UNIQUE KEY `view_component_UNIQUE` (`view_component`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;