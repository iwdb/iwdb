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

ALTER TABLE `prefix_scans` ADD `bed_eisen` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` ADD `bed_stahl` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` ADD `bed_vv4a` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` ADD `bed_chemie` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` ADD `bed_eis` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` ADD `bed_wasser` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` ADD `bed_energie` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` ADD `bed_bev` int(11) NOT NULL DEFAULT '0';

ALTER TABLE `prefix_spieler` ADD `einmaurer` INT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` ADD `gesperrt` INT( 1 ) NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` ADD `umode` INT( 1 ) NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `prefix_scans_geb` (
  `coords` varchar(9) NOT NULL,
  `geb_id` int(11) NOT NULL,
  `geb_anz` int(11) NOT NULL,
  `time` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='DB-Gebäudescans' ;

ALTER TABLE `prefix_scans` ADD `geblink` VARCHAR( 120 ) NOT NULL AFTER `geolink`;
ALTER TABLE `prefix_scans` ADD `schifflink` VARCHAR( 120 ) NOT NULL AFTER `geblink`;

-- 03.12.2013 masel : Tabellenoptimierungen

ALTER TABLE `prefix_scans_geb` CHANGE `geb_id` `geb_id_iw` INT NOT NULL COMMENT 'Gebäude IW-ID';
ALTER TABLE `prefix_scans_geb` CHANGE `geb_anz` `geb_anz` INT UNSIGNED NOT NULL COMMENT 'Gebäudeanzahl';
ALTER TABLE `prefix_scans_geb` CHANGE `time` `time` INT UNSIGNED NOT NULL COMMENT 'Unixzeit';

ALTER TABLE `prefix_scans` CHANGE `bed_bev` `bed_bev` INT UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `prefix_spieler` CHANGE `einmaurer` `einmaurer` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` CHANGE `gesperrt` `gesperrt` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `prefix_spieler` CHANGE `umode` `umode` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';

-- 18.03.2014 masel: Punktelogtabelle angepasst
ALTER TABLE  `prefix_punktelog` DROP  `id` ;
ALTER TABLE  `prefix_punktelog` ADD PRIMARY KEY (  `user` ,  `date` ) COMMENT  '';

-- 19.03.2014 masel: Tabellenanpassungen

ALTER TABLE  `prefix_allianzstatus` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_allianzstatus` ADD UNIQUE  `name_allianz` (  `name` ,  `allianz` );
ALTER TABLE  `prefix_allianzstatus` CHANGE  `name`  `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_allianzstatus` ADD  `comment` VARCHAR( 250 ) NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_bbcodes` CHANGE  `isregex`  `isregex` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_bestellung` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Ziel';
ALTER TABLE  `prefix_bestellung` CHANGE  `team`  `team` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Lieferant';
ALTER TABLE  `prefix_bestellung` CHANGE  `coords_gal`  `coords_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `coords_sys`  `coords_sys` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `coords_planet`  `coords_planet` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `project`  `project` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_bestellung` CHANGE  `text`  `text` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_bestellung` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung` CHANGE  `prio`  `prio` SMALLINT( 6 ) NOT NULL DEFAULT  '1';

ALTER TABLE  `prefix_bestellung` CHANGE  `time_created`  `time_created` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_bestellung_projekt` CHANGE  `name`  `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_bestellung_projekt` CHANGE  `prio`  `prio` SMALLINT( 6 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung_projekt` CHANGE  `schiff`  `schiff` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `team`  `team` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `coords_gal`  `coords_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `coords_sys`  `coords_sys` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `coords_planet`  `coords_planet` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `project`  `project` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `text`  `text` VARCHAR( 254 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `time_created`  `time_created` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung_schiffe` CHANGE  `erledigt`  `erledigt` TINYINT( 1 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_bestellung_schiffe_pos` CHANGE  `bestellung_id`  `bestellung_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung_schiffe_pos` CHANGE  `schiffstyp_id`  `schiffstyp_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung_schiffe_pos` CHANGE  `menge`  `menge` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_bestellung_schiffe_pos` CHANGE  `offen`  `offen` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_def` CHANGE  `id`  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_def` CHANGE  `id_iw`  `id_iw` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_def` CHANGE  `abk`  `abk` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_fremdsondierung` CHANGE  `koords_to`  `koords_to` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_fremdsondierung` CHANGE  `name_to`  `name_to` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_fremdsondierung` CHANGE  `allianz_to`  `allianz_to` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_fremdsondierung` CHANGE  `koords_from`  `koords_from` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_fremdsondierung` CHANGE  `name_from`  `name_from` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_fremdsondierung` CHANGE  `allianz_from`  `allianz_from` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_fremdsondierung` CHANGE  `sondierung_art`  `sondierung_art` ENUM(  'schiffe',  'gebaeude' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'schiffe' COMMENT  'Schiffe oder Gebäude';
ALTER TABLE  `prefix_fremdsondierung` CHANGE  `timestamp`  `timestamp` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Zeitstempel Sondierung';
ALTER TABLE  `prefix_fremdsondierung` CHANGE  `erfolgreich`  `erfolgreich` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '0=fail,1=success';

ALTER TABLE  `prefix_gebaeude` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_gebaeude` CHANGE  `inactive`  `inactive` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_gebaeude` CHANGE  `dauer`  `dauer` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_gebaeude` CHANGE  `info`  `info` VARCHAR( 2000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude` CHANGE  `n_building`  `n_building` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude` CHANGE  `n_research`  `n_research` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude` CHANGE  `n_kolotyp`  `n_kolotyp` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude` CHANGE  `n_planityp`  `n_planityp` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude` CHANGE  `e_research`  `e_research` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude` CHANGE  `e_building`  `e_building` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude` CHANGE  `zerstoert`  `zerstoert` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude` CHANGE  `kostet`  `kostet` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude` CHANGE  `id_iw`  `id_iw` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `coords_gal`  `coords_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `coords_sys`  `coords_sys` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `coords_planet`  `coords_planet` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `kolo_typ`  `kolo_typ` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `category`  `category` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `building`  `building` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `count`  `count` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_gebaeude_spieler` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_gebbaukosten` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_gebbaukosten` CHANGE  `dauer`  `dauer` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_group` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_group` CHANGE  `parent_id`  `parent_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_group` CHANGE  `name`  `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_group_user` CHANGE  `group_id`  `group_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_group_user` CHANGE  `user_id`  `user_id` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

DROP TABLE prefix_group_sort;

ALTER TABLE  `prefix_incomings` CHANGE  `koords_to`  `koords_to` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Zielcoords';
ALTER TABLE  `prefix_incomings` CHANGE  `name_to`  `name_to` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Zielspieler';
ALTER TABLE  `prefix_incomings` CHANGE  `allianz_to`  `allianz_to` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Zielallianz';
ALTER TABLE  `prefix_incomings` CHANGE  `koords_from`  `koords_from` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Angreiferkoords';
ALTER TABLE  `prefix_incomings` CHANGE  `name_from`  `name_from` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Angreiferspieler';
ALTER TABLE  `prefix_incomings` CHANGE  `art`  `art` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Sondierung/Att';
ALTER TABLE  `prefix_incomings` CHANGE  `arrivaltime`  `arrivaltime` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Unixzeitstempel der Ankunft der Sondierung/Att';
ALTER TABLE  `prefix_incomings` CHANGE  `listedtime`  `listedtime` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Unixzeitstempel des Eintrags';

ALTER TABLE  `prefix_kasse_incoming` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_kasse_outgoing` CHANGE  `payedfrom`  `payedfrom` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_kasse_outgoing` CHANGE  `payedto`  `payedto` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_kb` CHANGE  `ID_KB`  `ID_KB` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb` CHANGE  `verteidiger`  `verteidiger` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_kb` CHANGE  `verteidiger_ally`  `verteidiger_ally` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_kb` CHANGE  `planet_name`  `planet_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_kb` CHANGE  `koords_gal`  `koords_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb` CHANGE  `koords_sol`  `koords_sol` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb` CHANGE  `koords_pla`  `koords_pla` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_kb_bomb` CHANGE  `ID_KB`  `ID_KB` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_bomb` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_bomb` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_kb_bomb` CHANGE  `trefferchance`  `trefferchance` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '100';
ALTER TABLE  `prefix_kb_bomb` CHANGE  `basis`  `basis` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_bomb` CHANGE  `bev`  `bev` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';


ALTER TABLE  `prefix_kb_bomb_geb` CHANGE  `ID_KB`  `ID_KB` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_bomb_geb` CHANGE  `ID_IW_GEB`  `ID_IW_GEB` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_bomb_geb` CHANGE  `anzahl`  `anzahl` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_kb_def` CHANGE  `ID_KB`  `ID_KB` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_def` CHANGE  `ID_IW_DEF`  `ID_IW_DEF` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_def` CHANGE  `anz_start`  `anz_start` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_def` CHANGE  `anz_verlust`  `anz_verlust` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_kb_flotten` CHANGE  `ID_FLOTTE`  `ID_FLOTTE` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_kb_flotten` CHANGE  `ID_KB`  `ID_KB` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_flotten` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_flotten` CHANGE  `art`  `art` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_flotten` CHANGE  `name`  `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_kb_flotten` CHANGE  `ally`  `ally` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_kb_flotten` CHANGE  `planet_name`  `planet_name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_kb_flotten` CHANGE  `koords_string`  `koords_string` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_kb_flotten_schiffe` CHANGE  `ID_FLOTTE`  `ID_FLOTTE` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_flotten_schiffe` CHANGE  `ID_IW_SCHIFF`  `ID_IW_SCHIFF` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_flotten_schiffe` CHANGE  `anz_start`  `anz_start` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_flotten_schiffe` CHANGE  `anz_verlust`  `anz_verlust` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_kb_pluenderung` CHANGE  `ID_KB`  `ID_KB` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_pluenderung` CHANGE  `ID_IW_RESS`  `ID_IW_RESS` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_pluenderung` CHANGE  `anzahl`  `anzahl` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_kb_verluste` CHANGE  `ID_KB`  `ID_KB` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_verluste` CHANGE  `ID_IW_RESS`  `ID_IW_RESS` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_verluste` CHANGE  `seite`  `seite` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_kb_verluste` CHANGE  `anzahl`  `anzahl` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_lager` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_lager` CHANGE  `coords_gal`  `coords_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `coords_sys`  `coords_sys` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `coords_planet`  `coords_planet` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `eisen`  `eisen` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `eisen_bunker`  `eisen_bunker` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `stahl`  `stahl` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `stahl_bunker`  `stahl_bunker` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `vv4a`  `vv4a` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `vv4a_bunker`  `vv4a_bunker` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `chem`  `chem` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `chem_lager`  `chem_lager` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `chem_bunker`  `chem_bunker` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `eis`  `eis` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `eis_lager`  `eis_lager` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `eis_bunker`  `eis_bunker` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `wasser`  `wasser` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `wasser_bunker`  `wasser_bunker` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `energie`  `energie` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `energie_lager`  `energie_lager` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `energie_bunker`  `energie_bunker` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `bev_a`  `bev_a` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `bev_g`  `bev_g` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `bev_w`  `bev_w` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `eisen_soll`  `eisen_soll` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `stahl_soll`  `stahl_soll` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `vv4a_soll`  `vv4a_soll` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `chem_soll`  `chem_soll` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `eis_soll`  `eis_soll` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `wasser_soll`  `wasser_soll` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `energie_soll`  `energie_soll` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_lager` CHANGE  `eisen_baukosten`  `eisen_baukosten` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `stahl_baukosten`  `stahl_baukosten` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `vv4a_baukosten`  `vv4a_baukosten` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `chemie_baukosten`  `chemie_baukosten` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `eis_baukosten`  `eis_baukosten` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `wasser_baukosten`  `wasser_baukosten` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `energie_baukosten`  `energie_baukosten` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lager` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_lieferung` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lieferung` CHANGE  `coords_from_gal`  `coords_from_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lieferung` CHANGE  `coords_from_sys`  `coords_from_sys` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lieferung` CHANGE  `coords_from_planet`  `coords_from_planet` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lieferung` CHANGE  `coords_to_gal`  `coords_to_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lieferung` CHANGE  `coords_to_sys`  `coords_to_sys` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lieferung` CHANGE  `coords_to_planet`  `coords_to_planet` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_lieferung` CHANGE  `user_from`  `user_from` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `prefix_lieferung` CHANGE  `user_to`  `user_to` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE  `prefix_lieferung` CHANGE  `schiffe`  `schiffe` VARCHAR( 2000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE  `prefix_merkmale` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE  `prefix_order_comment` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_order_comment` CHANGE  `order_id`  `order_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_order_comment` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_order_comment` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_order_comment` CHANGE  `text`  `text` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_params` CHANGE  `name`  `name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_params` CHANGE  `value`  `value` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_params` DROP `text`;

ALTER TABLE  `prefix_preset` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;

DROP TABLE prefix_parsemenu;

ALTER TABLE  `prefix_punktelog` CHANGE  `date`  `date` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_punktelog` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_research` CHANGE  `gameversion`  `gameversion` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '666';
ALTER TABLE  `prefix_research` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_ressuebersicht` CHANGE  `datum`  `datum` INT( 10 ) UNSIGNED NULL DEFAULT  '0';

ALTER TABLE  `prefix_research2user` CHANGE  `userid`  `userid` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_scans` CHANGE  `coords_gal`  `coords_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_scans` CHANGE  `coords_gal`  `coords_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_scans` CHANGE  `coords_planet`  `coords_planet` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_scans` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_scans` CHANGE  `reserviert`  `reserviert` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_scans` CHANGE  `rnb`  `rnb` VARCHAR( 5000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'raider-notizblock';
ALTER TABLE  `prefix_scans` CHANGE  `planet_farbe`  `planet_farbe` VARCHAR( 7 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_scans` CHANGE  `reserveraiduser`  `reserveraiduser` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_scans` CHANGE  `tteisen`  `tteisen` SMALLINT( 5 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_scans` CHANGE  `ttchemie`  `ttchemie` SMALLINT( 5 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_scans` CHANGE  `tteis`  `tteis` SMALLINT( 5 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_scans` CHANGE  `time_att`  `time_att` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_scans` CHANGE  `att`  `att` VARCHAR( 10000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_scans` CHANGE  `geolink`  `geolink` VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_scans` CHANGE  `geblink`  `geblink` VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_scans` CHANGE  `schifflink`  `schifflink` VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_scans_details` CHANGE  `coords`  `coords` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_scans_details` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_scans_details` CHANGE  `plan`  `plan` VARCHAR( 10000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_scans_details` CHANGE  `stat`  `stat` VARCHAR( 10000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_scans_geb` CHANGE  `coords`  `coords` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_scans_geb` CHANGE  `geb_id_iw`  `geb_id_iw` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Gebäude IW-ID';
ALTER TABLE  `prefix_scans_geb` CHANGE  `geb_anz`  `geb_anz` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Gebäudeanzahl';
ALTER TABLE  `prefix_scans_geb` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Unixzeit';

DROP TABLE prefix_scans_historie;

ALTER TABLE  `prefix_schiffe` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_schiffe` CHANGE  `schiff`  `schiff` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_schiffe` CHANGE  `anzahl`  `anzahl` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_schiffstyp` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_schiffstyp` CHANGE  `schiff`  `schiff` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_schiffstyp` CHANGE  `canLeaveGalaxy`  `canLeaveGalaxy` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_schiffstyp` CHANGE  `canBeTransported`  `canBeTransported` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_schiffstyp` CHANGE  `bestellbar`  `bestellbar` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_schiffstyp` CHANGE  `isTransporter`  `isTransporter` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_schiffstyp` CHANGE  `isCarrier`  `isCarrier` TINYINT( 1 ) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE  `prefix_sid` CHANGE  `userAgentHash`  `userAgentHash` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_sid` CHANGE  `date`  `date` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_sitterauftrag` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `date`  `date` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `date_b1`  `date_b1` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `date_b2`  `date_b2` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `ByUser`  `ByUser` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `planet`  `planet` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `bauschleife`  `bauschleife` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `schieben`  `schieben` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `irc`  `irc` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_sitterauftrag` CHANGE  `dauerauftrag`  `dauerauftrag` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_sitterlog` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_sitterlog` CHANGE  `sitterlogin`  `sitterlogin` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_sitterlog` CHANGE  `fromuser`  `fromuser` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_sitterlog` CHANGE  `date`  `date` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_sitterlog` CHANGE  `action`  `action` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_spieler` CHANGE  `name`  `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_spieler` CHANGE  `allianz`  `allianz` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_spieler` CHANGE  `status`  `status` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_spieler` CHANGE  `playerupdate_time`  `playerupdate_time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_spieler` CHANGE  `Hauptplanet`  `Hauptplanet` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  'Hauptplanet des Spielers';
ALTER TABLE  `prefix_spieler` CHANGE  `pos`  `pos` INT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `prefix_spieler` CHANGE  `gebp`  `gebp` INT UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_spieler` CHANGE  `fp`  `fp` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_spieler` CHANGE  `gesamtp`  `gesamtp` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_spieler` CHANGE  `diff`  `diff` INT( 11 ) NULL DEFAULT NULL;
ALTER TABLE  `prefix_spieler` CHANGE  `gebp_nodiff`  `gebp_nodiff` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_spieler` CHANGE  `fp_nodiff`  `fp_nodiff` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_spieler` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_spielerallychange` CHANGE  `name`  `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_spielerallychange` CHANGE  `fromally`  `fromally` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_spielerallychange` CHANGE  `toally`  `toally` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_spielerallychange` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_sysscans` CHANGE  `gal`  `gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_sysscans` CHANGE  `sys`  `sys` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_sysscans` CHANGE  `date`  `date` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_target` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_target` CHANGE  `name`  `name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_target` CHANGE  `coords_gal`  `coords_gal` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_target` CHANGE  `coords_sys`  `coords_sys` SMALLINT( 5 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_target` CHANGE  `coords_planet`  `coords_planet` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_transferliste` CHANGE  `zeitmarke`  `zeitmarke` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_transferliste` CHANGE  `buddler`  `buddler` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Absender';
ALTER TABLE  `prefix_transferliste` CHANGE  `fleeter`  `fleeter` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '' COMMENT  'Empfänger';

DROP TABLE `prefix_univ_link`;

ALTER TABLE  `prefix_user_research` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_user_research` CHANGE  `date`  `date` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_user_research` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `prefix_versand_auftrag` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_versand_auftrag` CHANGE  `time`  `time` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_versand_auftrag` CHANGE  `pos`  `pos` INT( 11 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_versand_auftrag` CHANGE  `reference`  `reference` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_versand_auftrag` CHANGE  `art`  `art` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

ALTER TABLE  `prefix_wronglogin` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE  `prefix_wronglogin` CHANGE  `user`  `user` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';
ALTER TABLE  `prefix_wronglogin` CHANGE  `date`  `date` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `prefix_wronglogin` CHANGE  `ip`  `ip` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '';

-- 25.04.2014 masel: change coords format back to signed

ALTER TABLE `prefix_bestellung` CHANGE `coords_gal` `coords_gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_bestellung` CHANGE `coords_sys` `coords_sys` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_bestellung` CHANGE `coords_planet` `coords_planet` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_bestellung_schiffe` CHANGE `coords_gal` `coords_gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_bestellung_schiffe` CHANGE `coords_sys` `coords_sys` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_bestellung_schiffe` CHANGE `coords_planet` `coords_planet` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_gebaeude_spieler` CHANGE `coords_gal` `coords_gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_gebaeude_spieler` CHANGE `coords_sys` `coords_sys` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_gebaeude_spieler` CHANGE `coords_planet` `coords_planet` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_kb` CHANGE `koords_gal` `koords_gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_kb` CHANGE `koords_sol` `koords_sol` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_kb` CHANGE `koords_pla` `koords_pla` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_lager` CHANGE `coords_gal` `coords_gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_lager` CHANGE `coords_sys` `coords_sys` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_lager` CHANGE `coords_planet` `coords_planet` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_lieferung` CHANGE `coords_from_gal` `coords_from_gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_lieferung` CHANGE `coords_from_sys` `coords_from_sys` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_lieferung` CHANGE `coords_from_planet` `coords_from_planet` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_lieferung` CHANGE `coords_to_gal` `coords_to_gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_lieferung` CHANGE `coords_to_sys` `coords_to_sys` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_lieferung` CHANGE `coords_to_planet` `coords_to_planet` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` CHANGE `coords_gal` `coords_gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` CHANGE `coords_sys` `coords_sys` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_scans` CHANGE `coords_planet` `coords_planet` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_sysscans` CHANGE `gal` `gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_sysscans` CHANGE `sys` `sys` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_target` CHANGE `coords_gal` `coords_gal` TINYINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_target` CHANGE `coords_sys` `coords_sys` SMALLINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_target` CHANGE `coords_planet` `coords_planet` TINYINT NOT NULL DEFAULT '0';
