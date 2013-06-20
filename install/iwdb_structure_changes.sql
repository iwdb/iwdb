-- 24.05.2013 masel: add missing unique index
ALTER TABLE  `prefix_gebaeude` ADD UNIQUE (`name`);

-- 25.05.2013 masel Tabellenoptimierung
ALTER TABLE  `prefix_bestellung` CHANGE  `id`  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_bestellung` CHANGE  `user`  `user` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  'Ziel';
ALTER TABLE  `prefix_bestellung` CHANGE  `team`  `team` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  'Lieferant';
ALTER TABLE  `prefix_bestellung` CHANGE  `coords_sys`  `coords_sys` SMALLINT NOT NULL;
ALTER TABLE  `prefix_bestellung` CHANGE  `project`  `project` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `prefix_bestellung` CHANGE  `text`  `text` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `prefix_bestellung` CHANGE  `time`  `time` INT UNSIGNED NOT NULL;
ALTER TABLE  `prefix_bestellung` CHANGE  `eisen`  `eisen` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `stahl`  `stahl` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `chemie`  `chemie` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `vv4a`  `vv4a` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `eis`  `eis` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `wasser`  `wasser` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `energie`  `energie` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `credits`  `credits` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `volk`  `volk` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `offen_eisen`  `offen_eisen` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `offen_stahl`  `offen_stahl` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `offen_chemie`  `offen_chemie` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `offen_vv4a`  `offen_vv4a` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `offen_eis`  `offen_eis` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `offen_wasser`  `offen_wasser` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `offen_energie`  `offen_energie` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `offen_volk`  `offen_volk` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `offen_credits`  `offen_credits` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `taeglich`  `taeglich` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `time_created`  `time_created` INT UNSIGNED NOT NULL;
ALTER TABLE  `prefix_bestellung` CHANGE  `erledigt`  `erledigt` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` DROP `schiff`;
ALTER TABLE  `prefix_bestellung` DROP `anzahl`;

-- 05.06.2013 patsch : Plani- und Gebbilder standardmäßig anzeigen
ALTER TABLE `prefix_user` CHANGE `planibilder` `planibilder` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1';
ALTER TABLE `prefix_user` CHANGE `gebbilder` `gebbilder` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1';

-- 09.06.2013 patsch : merging highscore- und spielertabelle
ALTER TABLE `prefix_spieler` ADD `pos` int(12) DEFAULT NULL;
ALTER TABLE `prefix_spieler` ADD `gebp` int(12) NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` ADD `fp` int(12) NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` ADD `gesamtp` int(12) NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` ADD `ptag` float NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` ADD `diff` int(12) DEFAULT NULL;
ALTER TABLE `prefix_spieler` ADD `gebp_nodiff` int(12) NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` ADD `fp_nodiff` int(12) NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` ADD `time` int(12) NOT NULL DEFAULT '0';

DROP TABLE `prefix_highscore`;

-- 11.06.2013 masel : #209 IP-Wechsel erlauben
ALTER TABLE  `prefix_sid` CHANGE  `ip`  `ipHash` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE  `prefix_sid` ADD  `userAgentHash` VARCHAR( 100 ) NOT NULL  DEFAULT '' AFTER  `ipHash` ;

ALTER TABLE  `prefix_user` ADD `allow_ip_change` TINYINT( 1 ) UNSIGNED NOT NULL  DEFAULT '0' AFTER  `email`;

-- 13.06.2013 masel : #213
ALTER TABLE  `prefix_spieler` CHANGE  `dabeiseit`  `dabeiseit` INT( 10 ) UNSIGNED NULL DEFAULT NULL ;

-- 18.06.2013 patsch : automatisches Setzen des Lagersollwertes im Profil ausschalten
ALTER TABLE `prefix_user` ADD `autlager` TINYINT( 1 ) NOT NULL DEFAULT '1';

DROP TABLE `prefix_schiffstyp`;
CREATE TABLE IF NOT EXISTS `prefix_schiffstyp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schiff` varchar(80) NOT NULL,
  `abk` varchar(50) NOT NULL DEFAULT '',
  `typ` varchar(50) NOT NULL DEFAULT '',
  `bild` varchar(50) NOT NULL DEFAULT '',
  `id_iw` smallint(5) unsigned DEFAULT NULL,
  `kosten_eisen` int(10) unsigned DEFAULT NULL,
  `kosten_stahl` int(10) unsigned DEFAULT NULL,
  `kosten_vv4a` int(10) unsigned DEFAULT NULL,
  `kosten_chemie` int(10) unsigned DEFAULT NULL,
  `kosten_eis` int(10) unsigned DEFAULT NULL,
  `kosten_wasser` int(10) unsigned DEFAULT NULL,
  `kosten_energie` int(10) unsigned DEFAULT NULL,
  `kosten_bev` int(10) unsigned DEFAULT NULL,
  `kosten_creds` int(10) unsigned DEFAULT NULL,
  `GeschwindigkeitSol` mediumint(8) unsigned DEFAULT NULL,
  `GeschwindigkeitGal` mediumint(8) unsigned DEFAULT NULL,
  `canLeaveGalaxy` tinyint(1) DEFAULT NULL,
  `canBeTransported` tinyint(1) DEFAULT NULL,
  `VerbrauchChemie` smallint(5) unsigned DEFAULT NULL,
  `VerbrauchEnergie` smallint(5) unsigned DEFAULT NULL,
  `angriff` smallint(5) unsigned DEFAULT NULL,
  `waffenklasse` varchar(30) DEFAULT NULL,
  `verteidigung` smallint(5) unsigned DEFAULT NULL,
  `panzerung_kinetisch` smallint(5) unsigned DEFAULT NULL,
  `panzerung_elektrisch` smallint(5) unsigned DEFAULT NULL,
  `panzerung_gravimetrisch` smallint(5) unsigned DEFAULT NULL,
  `Schilde` smallint(5) unsigned DEFAULT NULL,
  `accuracy` smallint(5) unsigned DEFAULT NULL,
  `mobility` smallint(5) unsigned DEFAULT NULL,
  `numEscort` smallint(5) unsigned DEFAULT NULL,
  `EscortBonusAtt` float NOT NULL DEFAULT '1',
  `EscortBonusDef` float NOT NULL DEFAULT '1',
  `werftTyp` varchar(50) DEFAULT NULL,
  `dauer` int(10) unsigned DEFAULT NULL,
  `bestellbar` tinyint(1) NOT NULL DEFAULT '0',
  `isTransporter` tinyint(1) DEFAULT NULL,
  `klasse1` int(10) unsigned DEFAULT NULL,
  `klasse2` int(10) unsigned DEFAULT NULL,
  `bev` int(10) unsigned DEFAULT NULL,
  `isCarrier` tinyint(1) DEFAULT NULL,
  `shipKapa1` smallint(5) unsigned DEFAULT NULL,
  `shipKapa2` smallint(5) unsigned DEFAULT NULL,
  `shipKapa3` smallint(5) unsigned DEFAULT NULL,
  `aktualisiert` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schiff` (`schiff`),
  UNIQUE KEY `id_iw` (`id_iw`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=312 ;

CREATE TABLE IF NOT EXISTS `prefix_gebbaukosten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `dauer` int(7) NOT NULL DEFAULT '0',
  `kosten_eisen` int(10) unsigned DEFAULT NULL,
  `kosten_stahl` int(10) unsigned DEFAULT NULL,
  `kosten_vv4a` int(10) unsigned DEFAULT NULL,
  `kosten_chemie` int(10) unsigned DEFAULT NULL,
  `kosten_eis` int(10) unsigned DEFAULT NULL,
  `kosten_wasser` int(10) unsigned DEFAULT NULL,
  `kosten_energie` int(10) unsigned DEFAULT NULL,
  `kosten_bev` int(10) unsigned DEFAULT NULL,
  `kosten_creds` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Gebäudebaukosten einiger Gebäude für Ressbedarfsrechnung' AUTO_INCREMENT=21 ;
