CREATE TABLE IF NOT EXISTS `#__magebridge_importer_products` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `attributeset_id` INT(4) NOT NULL DEFAULT 0,
    `product_type` VARCHAR(20) NOT NULL DEFAULT 0,
    `status` VARCHAR(20) NOT NULL DEFAULT 0,
    `created` datetime NOT NULL default '0000-00-00 00:00:00',
    `created_by` INT(11) NOT NULL DEFAULT 0,
    `modified` datetime NOT NULL default '0000-00-00 00:00:00',
    `modified_by` INT(11) NOT NULL DEFAULT 0,
    `params` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__magebridge_importer_product_values` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `product_id` INT(11) NOT NULL DEFAULT 0,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `value` TEXT NOT NULL DEFAULT '',
    `timestamp` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__magebridge_importer_profiles` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `label` VARCHAR(255) NOT NULL DEFAULT '',
    `exclude_fields` text NOT NULL,
    `include_fields` text NOT NULL,
    `params` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__magebridge_importer_fieldsets` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `profile_id` INT(11) NOT NULL DEFAULT 0,
    `name` INT(11) NOT NULL DEFAULT 0,
    `label` VARCHAR(255) NOT NULL DEFAULT '',
    `description` TEXT NOT NULL DEFAULT '',
    `params` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__magebridge_importer_fields` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `profile_id` INT(11) NOT NULL DEFAULT 0,
    `fieldset_id` INT(11) NOT NULL DEFAULT 0,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `label` VARCHAR(255) NOT NULL DEFAULT '',
    `default_value` VARCHAR(255) NOT NULL DEFAULT '',
    `description` TEXT NOT NULL DEFAULT '',
    `params` text NOT NULL,
    PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;

