CREATE TABLE IF NOT EXISTS `#__facturasxcobrar` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`integradoid` INT(11)  NOT NULL ,
`status` INT(2)  NOT NULL ,
`url_xml` VARCHAR(255)  NOT NULL ,
`campo1` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

