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

-- Tabellenoptimierung und Ergänzung by masel
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
ALTER TABLE `prefix_scans` ADD INDEX (  `typ` );
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

-- bbcodes my basel :)
CREATE TABLE IF NOT EXISTS `prefix_bbcodes` (
  `isregex` tinyint(1) NOT NULL DEFAULT '0',
  `bbcode` varchar(100) NOT NULL DEFAULT '',
  `htmlcode` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`bbcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='bbcode Übersetzungstabelle';

-- fix #93
ALTER TABLE `prefix_kb_bomb` ADD `trefferchance` INT UNSIGNED NOT NULL;
ALTER TABLE `prefix_kb_bomb` ADD `basis` BOOLEAN NOT NULL;

-- #96
ALTER TABLE `prefix_spielerallychange` ADD PRIMARY KEY (  `name` ,  `fromally` ,  `toally` ,  `time` );

-- Mac: umsetzung des Sondierungsparsers (Fremdsondierungen)
CREATE TABLE IF NOT EXISTS `prefix_fremdsondierung` (
   `koords_to` varchar(11) NOT NULL,
   `name_to` varchar(50) NOT NULL,
   `allianz_to` varchar(50) NOT NULL,
   `koords_from` varchar(11) NOT NULL,
   `name_from` varchar(50) NOT NULL,
   `allianz_from` varchar(50) NOT NULL,
   `sondierung_art` ENUM( 'schiffe', 'gebaeude' ) NOT NULL COMMENT 'Schiffe oder Gebäude',
   `timestamp` int(10) unsigned NOT NULL COMMENT 'Zeitstempel Sondierung',
   `erfolgreich` int(1) DEFAULT '0' COMMENT '0=fail,1=success',
PRIMARY KEY (`timestamp`,`koords_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabelle eingegangener Sondierungen';

-- Patsch: Alternative für das Anzeigen der Sondierungen und Angriffe
CREATE TABLE IF NOT EXISTS `prefix_incomings` (
  `koords_to` varchar(11) NOT NULL,
  `name_to` varchar(50) NOT NULL,
  `allianz_to` varchar(50) NOT NULL,
  `koords_from` varchar(11) NOT NULL,
  `name_from` varchar(50) NOT NULL,
  `allianz_from` varchar(50) NOT NULL,
  `art` varchar(100) NOT NULL COMMENT 'Angriff oder Sondierung',
  `timestamp` int(10) unsigned NOT NULL COMMENT 'Zeitstempel Sondierung',
  PRIMARY KEY (`timestamp`,`koords_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabelle für Incomings';

ALTER TABLE `prefix_kasse_incoming` CHANGE `time_of_insert` `time_of_insert` DATETIME NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `prefix_kasse_content` CHANGE `time_of_insert` `time_of_insert` DATETIME NOT NULL DEFAULT '0000-00-00';

-- masel: fix #105
ALTER TABLE `prefix_kb_bomb` ADD  `bev` INT UNSIGNED NOT NULL;

-- masel: 13.01.2013 #119
ALTER TABLE `prefix_user` ADD  `NewUniXmlTime` INT UNSIGNED NULL DEFAULT NULL COMMENT  'Timestamp der nächsten Unixml Parsemöglichkeit';

-- masel: 14.01.2013
ALTER TABLE `prefix_spieler` ADD `Hauptplanet` VARCHAR( 11 ) NULL DEFAULT NULL COMMENT 'Hauptplanet des Spielers', ADD INDEX ( `Hauptplanet` );

-- masel: 16.01.2013 old parser removed
DROP TABLE `prefix_parser`;

-- masel: missing Index and removing double aktuellnews entry (here because order is important);
DELETE FROM `prefix_params` WHERE `name` = 'aktuellnews' LIMIT 1;
ALTER TABLE `prefix_params` ADD PRIMARY KEY ( `name` );

-- masel: 24.01. fehlender Eintrag für noch offene Bevölkerung
ALTER TABLE  `prefix_bestellung` ADD  `offen_volk` INT NOT NULL AFTER  `offen_energie`;

-- masel 24.01 nicht länger benötigt, Tabellen werden automatisch gesucht
DROP TABLE prefix_iwdbtabellen;
