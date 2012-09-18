-- Tabellenstruktur `prefix_sid` geändert by masel 25.04. 1:00
ALTER TABLE  `prefix_sid` ENGINE = MEMORY;
ALTER TABLE  `prefix_sid` CHANGE  `date`  `date` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE  `prefix_sid` CHANGE  `ip`  `ip` VARCHAR( 50 ) CHARACTER SET utf8;

-- Tabellenstruktur `prefix_research_user` geändert by patsch 08.05. 21:00
ALTER TABLE  `prefix_user_research` ADD `time` INT( 13 ) UNSIGNED NOT NULL;

-- Tabelle `iwdb_transport_einstellungen` entfernen, wird nicht benötigt ... by patsch 17.05. 15:00
DROP TABLE IF EXISTS `iwdb_transport_einstellungen`;

-- fixing collations to standard utf8 (utf8_general_ci) by masel 17.05.2012 22:33
ALTER TABLE  `prefix_gebaeude_spieler` DEFAULT CHARACTER SET utf8;
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `kolo_typ`  `kolo_typ` VARCHAR( 20 ) CHARACTER SET utf8 NOT NULL;
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `user`  `user` VARCHAR( 30 ) CHARACTER SET utf8 NOT NULL;
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `category`  `category` VARCHAR( 100 ) CHARACTER SET utf8 NOT NULL;
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `building`  `building` VARCHAR( 200 ) CHARACTER SET utf8 NOT NULL;

ALTER TABLE  `prefix_user_research` DEFAULT CHARACTER SET utf8;
ALTER TABLE  `prefix_user_research` CHANGE  `user`  `user` VARCHAR( 30 ) CHARACTER SET utf8 NOT NULL DEFAULT  '';

DROP TABLE IF EXISTS `prefix_bestellen`;
DROP TABLE IF EXISTS `prefix_versand_auftrag`;
DROP TABLE IF EXISTS `prefix_spielerinfo`;

ALTER TABLE `prefix_scans` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE `prefix_scans` CHANGE  `planetenname`  `planetenname` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE `prefix_scans` CHANGE  `typ`  `typ` ENUM('', 'Nichts', 'Steinklumpen', 'Asteroid', 'Gasgigant', 'Eisplanet' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE `prefix_scans` CHANGE  `objekt`  `objekt` ENUM('---', 'Artefaktbasis', 'Kampfbasis', 'Kolonie', 'Raumstation', 'Sammelbasis') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '---';
ALTER TABLE `prefix_scans` CHANGE  `eisengehalt`  `eisengehalt` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `chemievorkommen`  `chemievorkommen` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `eisdichte`  `eisdichte` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `lebensbedingungen`  `lebensbedingungen` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `gravitation`  `gravitation` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `besonderheiten`  `besonderheiten` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `fmod`  `fmod` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `kgmod`  `kgmod` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `dgmod`  `dgmod` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `ksmod`  `ksmod` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `dsmod`  `dsmod` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `eisen`  `eisen` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `stahl`  `stahl` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `vv4a`  `vv4a` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `chemie`  `chemie` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `eis`  `eis` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `wasser`  `wasser` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `energie`  `energie` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `plan`  `plan` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `stat`  `stat` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `def`  `def` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `geb`  `geb` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `time`  `time` INT UNSIGNED NOT NULL;
ALTER TABLE `prefix_scans` CHANGE  `reserviert`  `reserviert` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `prefix_scans` CHANGE  `bevoelkerungsanzahl`  `bevoelkerungsanzahl` BIGINT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `lager_chemie`  `lager_chemie` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `lager_eis`  `lager_eis` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `lager_energie`  `lager_energie` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `tteisen`  `tteisen` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `ttchemie`  `ttchemie` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `tteis`  `tteis` FLOAT NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `x11`  `x11` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `terminus`  `terminus` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `x13`  `x13` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `fehlscantime`  `fehlscantime` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `reserveraid`  `reserveraid` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `reserveraiduser`  `reserveraiduser` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `prefix_scans` CHANGE  `gebscantime`  `gebscantime` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `schiffscantime`  `schiffscantime` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `geoscantime`  `geoscantime` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `reset_timestamp`  `reset_timestamp` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `plaid`  `plaid` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `sondierung`  `sondierung` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `sondierunguser`  `sondierunguser` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `angriff`  `angriff` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` CHANGE  `angriffuser`  `angriffuser` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` ADD  `userchange_time` INT UNSIGNED NULL DEFAULT NULL AFTER  `user`;
ALTER TABLE `prefix_scans` ADD  `typchange_time` INT UNSIGNED NULL DEFAULT NULL AFTER  `typ`;
ALTER TABLE `prefix_scans` ADD  `objektchange_time` INT UNSIGNED NULL DEFAULT NULL AFTER  `objekt`;
ALTER TABLE `prefix_scans` ADD  `nebel` ENUM(  '',  'blau',  'gelb',  'gruen',  'rot',  'violett' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' AFTER `objektchange_time`;
ALTER TABLE `prefix_scans` ADD  `planet_pic` TINYINT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE `prefix_scans` ADD  `astro_pic` TINYINT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `prefix_scans` ADD  `shadow_pic` TINYINT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE `prefix_scans` ADD  `bg_pic` TINYINT UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE `prefix_scans` DROP INDEX `user`;
ALTER TABLE `prefix_scans` DROP INDEX `coords_gal_2` , ADD UNIQUE  `scans_coords_2` (  `coords_gal` ,  `coords_sys` ,  `coords_planet` );
ALTER TABLE `prefix_scans` DROP INDEX `coords_gal`;
ALTER TABLE `prefix_scans` ADD INDEX  `scans_user` (  `user` );
ALTER TABLE `prefix_scans` ADD INDEX `scans_coords_pla` (  `coords_planet` );
ALTER TABLE `prefix_scans` ADD INDEX (  `time` );
ALTER TABLE `prefix_scans` ADD INDEX (  `typ` )
ALTER TABLE `prefix_scans` ADD INDEX (  `typchange_time` );
ALTER TABLE `prefix_scans` ADD INDEX (  `userchange_time` );
ALTER TABLE `prefix_scans` ADD INDEX (  `objektchange_time` );

ALTER TABLE `prefix_sysscans` DROP INDEX idx_sg_search;
ALTER TABLE `prefix_sysscans` ADD PRIMARY KEY ( `id` );
UPDATE `prefix_sysscans` SET `nebula` = CASE `nebula` WHEN 'BLN' THEN 'blau' WHEN 'GEN' THEN 'gelb' WHEN 'GRN' THEN 'gruen' WHEN 'RON' THEN 'rot' WHEN 'VIN' THEN 'violett' END;
ALTER TABLE  `prefix_sysscans` CHANGE `nebula`  `nebula` ENUM(  '',  'blau',  'gelb',  'gruen',  'rot',  'violett' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

CREATE TABLE IF NOT EXISTS `prefix_spieler` (
  `name` varchar(50) NOT NULL,
  `allianz` varchar(50) NOT NULL,
  `allianzrang` varchar(50) DEFAULT NULL,
  `exallianz` varchar(50) DEFAULT NULL,
  `allychange_time` int(10) unsigned DEFAULT NULL,
  `staatsform` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `dabeiseit` int(10) unsigned NOT NULL,
  `playerupdate_time` int(10) unsigned NOT NULL,
  `geb_pkt` int(10) unsigned DEFAULT NULL,
  `forsch_pkt` int(10) unsigned DEFAULT NULL,
  `ges_pkt` int(10) unsigned DEFAULT NULL,
  `pktupdate_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `allychange_time` (`allychange_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabelle aller Spieler';

CREATE TABLE IF NOT EXISTS `prefix_spielerallychange` (
  `name` varchar(50) NOT NULL,
  `fromally` varchar(50) NOT NULL,
  `toally` varchar(50) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabelle aller Allianzänderungen';