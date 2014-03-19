-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 19. Mrz 2014 um 19:33
-- Server Version: 10.0.8-MariaDB
-- PHP-Version: 5.5.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_allianzstatus`
--

CREATE TABLE `prefix_allianzstatus` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(10) NOT NULL DEFAULT '',
  `comment` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_allianz` (`name`,`allianz`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Status der eigenen Allianz zu anderen';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bbcodes`
--

CREATE TABLE `prefix_bbcodes` (
  `isregex` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `bbcode` varchar(100) NOT NULL DEFAULT '',
  `htmlcode` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`bbcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='bbcode Übersetzungstabelle';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung`
--

CREATE TABLE `prefix_bestellung` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL DEFAULT '' COMMENT 'Ziel',
  `team` varchar(100) NOT NULL DEFAULT '' COMMENT 'Lieferant',
  `coords_gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `coords_sys` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coords_planet` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `project` varchar(100) NOT NULL DEFAULT '',
  `text` varchar(1000) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `eisen` int(10) unsigned NOT NULL DEFAULT '0',
  `stahl` int(10) unsigned NOT NULL DEFAULT '0',
  `chemie` int(10) unsigned NOT NULL DEFAULT '0',
  `vv4a` int(10) unsigned NOT NULL DEFAULT '0',
  `eis` int(10) unsigned NOT NULL DEFAULT '0',
  `wasser` int(10) unsigned NOT NULL DEFAULT '0',
  `energie` int(10) unsigned NOT NULL DEFAULT '0',
  `credits` int(10) unsigned NOT NULL DEFAULT '0',
  `volk` int(10) unsigned NOT NULL DEFAULT '0',
  `offen_eisen` int(10) unsigned NOT NULL DEFAULT '0',
  `offen_stahl` int(10) unsigned NOT NULL DEFAULT '0',
  `offen_chemie` int(10) unsigned NOT NULL DEFAULT '0',
  `offen_vv4a` int(10) unsigned NOT NULL DEFAULT '0',
  `offen_eis` int(10) unsigned NOT NULL DEFAULT '0',
  `offen_wasser` int(10) unsigned NOT NULL DEFAULT '0',
  `offen_energie` int(10) unsigned NOT NULL DEFAULT '0',
  `offen_volk` int(10) unsigned NOT NULL DEFAULT '0',
  `offen_credits` int(10) unsigned NOT NULL DEFAULT '0',
  `prio` smallint(6) NOT NULL DEFAULT '1',
  `taeglich` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `time_created` int(10) unsigned NOT NULL DEFAULT '0',
  `erledigt` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Bestellsystem';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung_projekt`
--

CREATE TABLE `prefix_bestellung_projekt` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `prio` smallint(6) NOT NULL DEFAULT '0',
  `schiff` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`,`schiff`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung_schiffe`
--

CREATE TABLE `prefix_bestellung_schiffe` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL DEFAULT '',
  `team` varchar(50) NOT NULL DEFAULT '',
  `coords_gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `coords_sys` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coords_planet` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `project` varchar(50) NOT NULL DEFAULT '',
  `text` varchar(254) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `time_created` int(10) unsigned NOT NULL DEFAULT '0',
  `erledigt` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Bestellsystem';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung_schiffe_pos`
--

CREATE TABLE `prefix_bestellung_schiffe_pos` (
  `bestellung_id` int(10) unsigned NOT NULL DEFAULT '0',
  `schiffstyp_id` int(10) unsigned NOT NULL DEFAULT '0',
  `menge` int(10) unsigned NOT NULL DEFAULT '0',
  `offen` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bestellung_id`,`schiffstyp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_building2building`
--

CREATE TABLE `prefix_building2building` (
  `bOld` int(10) unsigned NOT NULL DEFAULT '0',
  `bNew` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bOld`,`bNew`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gebäude bOld ermöglicht Gebäude bNew';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_building2research`
--

CREATE TABLE `prefix_building2research` (
  `bId` int(10) unsigned NOT NULL DEFAULT '0',
  `rId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bId`,`rId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gebäude bId ermöglicht Forschung rId';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_def`
--

CREATE TABLE `prefix_def` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eingebaut` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `id_iw` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `abk` varchar(30) NOT NULL DEFAULT '',
  `typ` varchar(50) NOT NULL DEFAULT '',
  `kosten_eisen` smallint(5) unsigned NOT NULL DEFAULT '0',
  `kosten_stahl` smallint(5) unsigned NOT NULL DEFAULT '0',
  `kosten_vv4a` smallint(5) unsigned NOT NULL DEFAULT '0',
  `kosten_chemie` smallint(5) unsigned NOT NULL DEFAULT '0',
  `kosten_energie` smallint(5) unsigned NOT NULL DEFAULT '0',
  `dauer` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `verbrauch_chem` smallint(5) unsigned NOT NULL DEFAULT '0',
  `verbrauch_energie` smallint(5) unsigned NOT NULL DEFAULT '0',
  `angriff` smallint(5) unsigned NOT NULL DEFAULT '0',
  `waffenklasse` varchar(50) NOT NULL DEFAULT '',
  `verteidigung` smallint(5) unsigned NOT NULL DEFAULT '0',
  `schilde` smallint(5) unsigned NOT NULL DEFAULT '0',
  `genauigkeit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_sonden` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_zivile` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_jaeger` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_bomber` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_korvetten` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_zerstoerer` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_kreuzer` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_ss` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_dn` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eff_spezielle` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_fremdsondierung`
--

CREATE TABLE `prefix_fremdsondierung` (
  `koords_to` varchar(10) NOT NULL DEFAULT '',
  `name_to` varchar(50) NOT NULL DEFAULT '',
  `allianz_to` varchar(50) NOT NULL DEFAULT '',
  `koords_from` varchar(10) NOT NULL DEFAULT '',
  `name_from` varchar(50) NOT NULL DEFAULT '',
  `allianz_from` varchar(50) NOT NULL DEFAULT '',
  `sondierung_art` enum('schiffe','gebaeude') NOT NULL DEFAULT 'schiffe' COMMENT 'Schiffe oder Gebäude',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Zeitstempel Sondierung',
  `erfolgreich` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=fail,1=success',
  PRIMARY KEY (`timestamp`,`koords_to`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabelle eingegangener Sondierungen';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_gebaeude`
--

CREATE TABLE `prefix_gebaeude` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `category` varchar(50) NOT NULL DEFAULT '',
  `idcat` int(5) NOT NULL DEFAULT '0',
  `inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dauer` int(10) unsigned NOT NULL DEFAULT '0',
  `bild` varchar(40) NOT NULL DEFAULT '',
  `info` varchar(2000) NOT NULL DEFAULT '',
  `n_building` varchar(250) NOT NULL DEFAULT '',
  `n_research` varchar(250) NOT NULL DEFAULT '',
  `n_kolotyp` varchar(250) NOT NULL DEFAULT '',
  `n_planityp` varchar(30) NOT NULL DEFAULT '',
  `e_research` varchar(250) NOT NULL DEFAULT '',
  `e_building` varchar(1000) NOT NULL DEFAULT '',
  `zerstoert` varchar(250) NOT NULL DEFAULT '',
  `bringt` varchar(200) NOT NULL DEFAULT '',
  `Kosten` varchar(200) NOT NULL DEFAULT '',
  `Punkte` int(5) NOT NULL DEFAULT '0',
  `MaximaleAnzahl` int(3) NOT NULL DEFAULT '0',
  `typ` varchar(10) NOT NULL DEFAULT '',
  `kostet` varchar(200) NOT NULL DEFAULT '',
  `id_iw` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Gebäudekurzform';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_gebaeude_spieler`
--

CREATE TABLE `prefix_gebaeude_spieler` (
  `coords_gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `coords_sys` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coords_planet` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kolo_typ` varchar(20) NOT NULL DEFAULT '',
  `user` varchar(50) NOT NULL DEFAULT '',
  `category` varchar(100) NOT NULL DEFAULT '',
  `building` varchar(200) NOT NULL DEFAULT '',
  `count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`coords_gal`,`coords_sys`,`coords_planet`,`category`,`building`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gebäudeuebersicht';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_gebbaukosten`
--

CREATE TABLE `prefix_gebbaukosten` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `dauer` int(10) unsigned NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Gebäudebaukosten einiger Gebäude für Ressbedarfsrechnung';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_group`
--

CREATE TABLE `prefix_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_group_user`
--

CREATE TABLE `prefix_group_user` (
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_incomings`
--

CREATE TABLE `prefix_incomings` (
  `koords_to` varchar(10) NOT NULL DEFAULT '' COMMENT 'Zielcoords',
  `name_to` varchar(50) NOT NULL DEFAULT '' COMMENT 'Zielspieler',
  `allianz_to` varchar(50) NOT NULL DEFAULT '' COMMENT 'Zielallianz',
  `koords_from` varchar(10) NOT NULL DEFAULT '' COMMENT 'Angreiferkoords',
  `name_from` varchar(50) NOT NULL DEFAULT '' COMMENT 'Angreiferspieler',
  `allianz_from` varchar(50) DEFAULT NULL COMMENT 'Angreiferallianz',
  `art` varchar(100) NOT NULL DEFAULT '' COMMENT 'Sondierung/Att',
  `arrivaltime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Unixzeitstempel der Ankunft der Sondierung/Att',
  `listedtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Unixzeitstempel des Eintrags',
  `saved` tinyint(1) NOT NULL DEFAULT '0',
  `savedUpdateTime` int(10) unsigned DEFAULT NULL COMMENT 'Unixzeitstempel des Saveflug der Schiffe',
  `recalled` tinyint(1) NOT NULL DEFAULT '0',
  `recalledUpdateTime` int(10) unsigned DEFAULT NULL COMMENT 'Unixzeitstempel des Recall der Schiffe',
  PRIMARY KEY (`arrivaltime`,`koords_to`,`koords_from`,`art`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabelle für Incomings';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kasse_content`
--

CREATE TABLE `prefix_kasse_content` (
  `amount` decimal(22,2) NOT NULL DEFAULT '0.00',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  `time_of_insert` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `allianz` (`allianz`,`time_of_insert`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kasse_incoming`
--

CREATE TABLE `prefix_kasse_incoming` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `amount` decimal(22,2) NOT NULL DEFAULT '0.00',
  `time_of_insert` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  UNIQUE KEY `user` (`user`,`time_of_insert`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kasse_outgoing`
--

CREATE TABLE `prefix_kasse_outgoing` (
  `payedfrom` varchar(50) NOT NULL DEFAULT '',
  `payedto` varchar(50) NOT NULL DEFAULT '',
  `amount` bigint(20) unsigned NOT NULL DEFAULT '0',
  `time_of_pay` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  UNIQUE KEY `payedfrom` (`payedfrom`,`payedto`,`amount`,`time_of_pay`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb`
--

CREATE TABLE `prefix_kb` (
  `ID_KB` int(10) unsigned NOT NULL DEFAULT '0',
  `hash` varchar(100) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `verteidiger` varchar(50) NOT NULL DEFAULT '',
  `verteidiger_ally` varchar(50) NOT NULL DEFAULT '',
  `planet_name` varchar(50) NOT NULL DEFAULT '',
  `koords_gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `koords_sol` smallint(5) unsigned NOT NULL DEFAULT '0',
  `koords_pla` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `typ` varchar(60) NOT NULL DEFAULT '',
  `resultat` varchar(60) NOT NULL DEFAULT '',
  KEY `koords_gal` (`koords_gal`,`koords_sol`,`koords_pla`),
  KEY `verteidiger_ally` (`verteidiger_ally`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_bomb`
--

CREATE TABLE `prefix_kb_bomb` (
  `ID_KB` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `user` varchar(50) NOT NULL DEFAULT '',
  `trefferchance` tinyint(3) unsigned NOT NULL DEFAULT '100',
  `basis` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `bev` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_bomb_geb`
--

CREATE TABLE `prefix_kb_bomb_geb` (
  `ID_KB` int(10) unsigned NOT NULL DEFAULT '0',
  `ID_IW_GEB` int(10) unsigned NOT NULL DEFAULT '0',
  `anzahl` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_def`
--

CREATE TABLE `prefix_kb_def` (
  `ID_KB` int(10) unsigned NOT NULL DEFAULT '0',
  `ID_IW_DEF` int(10) unsigned NOT NULL DEFAULT '0',
  `anz_start` int(10) unsigned NOT NULL DEFAULT '0',
  `anz_verlust` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_flotten`
--

CREATE TABLE `prefix_kb_flotten` (
  `ID_FLOTTE` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_KB` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `art` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `ally` varchar(50) NOT NULL DEFAULT '',
  `planet_name` varchar(50) NOT NULL DEFAULT '',
  `koords_string` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID_FLOTTE`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_flotten_schiffe`
--

CREATE TABLE `prefix_kb_flotten_schiffe` (
  `ID_FLOTTE` int(10) unsigned NOT NULL DEFAULT '0',
  `ID_IW_SCHIFF` int(10) unsigned NOT NULL DEFAULT '0',
  `anz_start` int(10) unsigned NOT NULL DEFAULT '0',
  `anz_verlust` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_FLOTTE`,`ID_IW_SCHIFF`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_pluenderung`
--

CREATE TABLE `prefix_kb_pluenderung` (
  `ID_KB` int(10) unsigned NOT NULL DEFAULT '0',
  `ID_IW_RESS` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `anzahl` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_verluste`
--

CREATE TABLE `prefix_kb_verluste` (
  `ID_KB` int(10) unsigned NOT NULL DEFAULT '0',
  `ID_IW_RESS` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `seite` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `anzahl` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_lager`
--

CREATE TABLE `prefix_lager` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `coords_gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `coords_sys` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coords_planet` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `kolo_typ` varchar(20) NOT NULL DEFAULT '',
  `eisen` int(10) unsigned NOT NULL DEFAULT '0',
  `eisen_prod` float NOT NULL DEFAULT '0',
  `eisen_bunker` int(10) unsigned NOT NULL DEFAULT '0',
  `stahl` int(10) unsigned NOT NULL DEFAULT '0',
  `stahl_prod` float NOT NULL DEFAULT '0',
  `stahl_bunker` int(10) unsigned NOT NULL DEFAULT '0',
  `vv4a` int(10) unsigned NOT NULL DEFAULT '0',
  `vv4a_prod` float NOT NULL DEFAULT '0',
  `vv4a_bunker` int(10) unsigned NOT NULL DEFAULT '0',
  `chem` int(10) unsigned NOT NULL DEFAULT '0',
  `chem_prod` float NOT NULL DEFAULT '0',
  `chem_lager` int(10) unsigned NOT NULL DEFAULT '0',
  `chem_bunker` int(10) unsigned NOT NULL DEFAULT '0',
  `eis` int(10) unsigned NOT NULL DEFAULT '0',
  `eis_prod` float NOT NULL DEFAULT '0',
  `eis_lager` int(10) unsigned NOT NULL DEFAULT '0',
  `eis_bunker` int(10) unsigned NOT NULL DEFAULT '0',
  `wasser` int(10) unsigned NOT NULL DEFAULT '0',
  `wasser_prod` float NOT NULL DEFAULT '0',
  `wasser_bunker` int(10) unsigned NOT NULL DEFAULT '0',
  `energie` int(10) unsigned NOT NULL DEFAULT '0',
  `energie_prod` float NOT NULL DEFAULT '0',
  `energie_lager` int(10) unsigned NOT NULL DEFAULT '0',
  `energie_bunker` int(10) unsigned NOT NULL DEFAULT '0',
  `fp` float DEFAULT NULL,
  `fp_b` float DEFAULT NULL,
  `fp_m1` float DEFAULT NULL,
  `fp_m2` float DEFAULT NULL,
  `credits` float DEFAULT NULL,
  `bev_a` int(10) unsigned DEFAULT NULL,
  `bev_g` int(10) unsigned DEFAULT NULL,
  `bev_q` float DEFAULT NULL,
  `bev_w` int(10) unsigned DEFAULT NULL,
  `zufr` float DEFAULT NULL,
  `zufr_w` float DEFAULT NULL,
  `eisen_soll` int(10) unsigned DEFAULT NULL,
  `stahl_soll` int(10) unsigned DEFAULT NULL,
  `vv4a_soll` int(10) unsigned DEFAULT NULL,
  `chem_soll` int(10) unsigned DEFAULT NULL,
  `eis_soll` int(10) unsigned DEFAULT NULL,
  `wasser_soll` int(10) unsigned DEFAULT NULL,
  `energie_soll` int(10) unsigned DEFAULT NULL,
  `eisen_baukosten` int(10) unsigned NOT NULL DEFAULT '0',
  `stahl_baukosten` int(10) unsigned NOT NULL DEFAULT '0',
  `vv4a_baukosten` int(10) unsigned NOT NULL DEFAULT '0',
  `chemie_baukosten` int(10) unsigned NOT NULL DEFAULT '0',
  `eis_baukosten` int(10) unsigned NOT NULL DEFAULT '0',
  `wasser_baukosten` int(10) unsigned NOT NULL DEFAULT '0',
  `energie_baukosten` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `eisen_sichtbar` tinyint(1) NOT NULL DEFAULT '1',
  `stahl_sichtbar` tinyint(1) NOT NULL DEFAULT '1',
  `chem_sichtbar` tinyint(1) NOT NULL DEFAULT '1',
  `vv4a_sichtbar` tinyint(1) NOT NULL DEFAULT '1',
  `eis_sichtbar` tinyint(1) NOT NULL DEFAULT '1',
  `wasser_sichtbar` tinyint(1) NOT NULL DEFAULT '1',
  `energie_sichtbar` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`coords_gal`,`coords_sys`,`coords_planet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Lagerübersicht';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_lieferung`
--

CREATE TABLE `prefix_lieferung` (
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `coords_from_gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `coords_from_sys` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coords_from_planet` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `coords_to_gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `coords_to_sys` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coords_to_planet` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_from` varchar(50) DEFAULT NULL,
  `user_to` varchar(50) DEFAULT NULL,
  `eisen` int(10) unsigned NOT NULL DEFAULT '0',
  `stahl` int(10) unsigned NOT NULL DEFAULT '0',
  `vv4a` int(10) unsigned NOT NULL DEFAULT '0',
  `chem` int(10) unsigned NOT NULL DEFAULT '0',
  `eis` int(10) unsigned NOT NULL DEFAULT '0',
  `wasser` int(10) unsigned NOT NULL DEFAULT '0',
  `energie` int(10) unsigned NOT NULL DEFAULT '0',
  `volk` int(10) unsigned NOT NULL DEFAULT '0',
  `art` varchar(255) DEFAULT NULL,
  `schiffe` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`time`,`coords_from_gal`,`coords_from_sys`,`coords_from_planet`,`coords_to_gal`,`coords_to_sys`,`coords_to_planet`),
  KEY `coords_to_gal` (`coords_to_gal`,`coords_to_sys`,`coords_to_planet`,`art`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Lieferung';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_menu`
--

CREATE TABLE `prefix_menu` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `menu` tinyint(4) NOT NULL DEFAULT '0',
  `submenu` tinyint(4) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `title` varchar(100) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT '',
  `action` varchar(200) NOT NULL DEFAULT '',
  `extlink` char(1) NOT NULL DEFAULT 'n',
  `sittertyp` tinyint(4) NOT NULL DEFAULT '0',
  `sound` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Menüstruktur';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_merkmale`
--

CREATE TABLE `prefix_merkmale` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `merkmal` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `merkmal` (`merkmal`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Planetenmerkmale zur Suche';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_order_comment`
--

CREATE TABLE `prefix_order_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `user` varchar(50) NOT NULL DEFAULT '',
  `text` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_params`
--

CREATE TABLE `prefix_params` (
  `name` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_preset`
--

CREATE TABLE `prefix_preset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `fromuser` varchar(30) NOT NULL DEFAULT '',
  `typ` varchar(20) NOT NULL DEFAULT '',
  `objekt` varchar(20) NOT NULL DEFAULT '',
  `user` varchar(30) NOT NULL DEFAULT '',
  `exact` char(1) NOT NULL DEFAULT '',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  `planetenname` varchar(50) NOT NULL DEFAULT '',
  `merkmal` varchar(30) NOT NULL DEFAULT '',
  `eisengehalt` float NOT NULL DEFAULT '0',
  `chemievorkommen` float NOT NULL DEFAULT '0',
  `eisdichte` float NOT NULL DEFAULT '0',
  `lebensbedingungen` float NOT NULL DEFAULT '0',
  `order1` varchar(20) NOT NULL DEFAULT '',
  `order1_d` varchar(20) NOT NULL DEFAULT '',
  `order2` varchar(20) NOT NULL DEFAULT '',
  `order2_d` varchar(20) NOT NULL DEFAULT '',
  `order3` varchar(20) NOT NULL DEFAULT '',
  `order3_d` varchar(20) NOT NULL DEFAULT '',
  `grav_von` varchar(5) NOT NULL DEFAULT 'x',
  `grav_bis` varchar(5) NOT NULL DEFAULT 'x',
  `gal_start` char(3) NOT NULL DEFAULT 'x',
  `gal_end` char(3) NOT NULL DEFAULT 'x',
  `sys_start` char(3) NOT NULL DEFAULT 'x',
  `sys_end` char(3) NOT NULL DEFAULT 'x',
  `max` varchar(6) NOT NULL DEFAULT '',
  `ansicht` varchar(20) NOT NULL DEFAULT '',
  `kgmod` varchar(5) NOT NULL DEFAULT '',
  `dgmod` varchar(5) NOT NULL DEFAULT '',
  `ksmod` varchar(5) NOT NULL DEFAULT '',
  `dsmod` varchar(5) DEFAULT NULL,
  `fmod` varchar(5) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Einstellungen für die Suche';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_punktelog`
--

CREATE TABLE `prefix_punktelog` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `gebp` int(12) NOT NULL DEFAULT '0',
  `fp` int(12) NOT NULL DEFAULT '0',
  `gesamtp` int(12) NOT NULL DEFAULT '0',
  `ptag` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Punktenachverfolgung';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_raidview`
--

CREATE TABLE `prefix_raidview` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coords` varchar(11) NOT NULL DEFAULT '',
  `date` int(12) NOT NULL DEFAULT '0',
  `eisen` int(11) NOT NULL DEFAULT '0',
  `stahl` int(11) NOT NULL DEFAULT '0',
  `vv4a` int(11) NOT NULL DEFAULT '0',
  `chemie` int(11) NOT NULL DEFAULT '0',
  `eis` int(11) NOT NULL DEFAULT '0',
  `link` varchar(120) NOT NULL DEFAULT '',
  `wasser` int(11) NOT NULL DEFAULT '0',
  `energie` int(11) NOT NULL DEFAULT '0',
  `geraided` varchar(30) NOT NULL DEFAULT '',
  `user` varchar(20) NOT NULL DEFAULT '',
  `summe` int(11) NOT NULL DEFAULT '0' COMMENT 'um sortieren zu können',
  `v_eisen` int(11) NOT NULL,
  `v_stahl` int(11) NOT NULL,
  `v_vv4a` int(11) NOT NULL,
  `v_chem` int(11) NOT NULL,
  `v_eis` int(11) NOT NULL,
  `v_wasser` int(11) NOT NULL,
  `v_energie` int(11) NOT NULL,
  `g_eisen` int(11) NOT NULL,
  `g_stahl` int(11) NOT NULL,
  `g_vv4a` int(11) NOT NULL,
  `g_chem` int(11) NOT NULL,
  `g_eis` int(11) NOT NULL,
  `g_wasser` int(11) NOT NULL,
  `g_energie` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Raidberichte';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research`
--

CREATE TABLE `prefix_research` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `description` text,
  `FP` int(10) unsigned NOT NULL DEFAULT '0',
  `gebiet` int(10) unsigned NOT NULL DEFAULT '0',
  `highscore` int(10) unsigned NOT NULL DEFAULT '0',
  `addcost` text,
  `geblevels` text,
  `declarations` text,
  `defense` text,
  `objects` text,
  `genetics` text,
  `rlevel` int(10) unsigned NOT NULL DEFAULT '0',
  `gameversion` varchar(10) NOT NULL DEFAULT '666',
  `reingestellt` varchar(50) DEFAULT NULL,
  `FPakt` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Forschungsinformation fuer Forschung Id';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2building`
--

CREATE TABLE `prefix_research2building` (
  `rId` int(10) unsigned NOT NULL DEFAULT '0',
  `bId` int(10) unsigned NOT NULL DEFAULT '0',
  `lvl` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rId`,`bId`,`lvl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschung rId ermöglicht Gebäude(stufe) bId';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2prototype`
--

CREATE TABLE `prefix_research2prototype` (
  `rid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschung rId ermöglicht Prototyp pId';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2research`
--

CREATE TABLE `prefix_research2research` (
  `rOld` int(10) unsigned NOT NULL DEFAULT '0',
  `rNew` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rOld`,`rNew`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschung rOld ermöglicht Forschung rNew';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2user`
--

CREATE TABLE `prefix_research2user` (
  `rid` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`rid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='bereits erforschte Forschungen des Benutzers';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_researchfield`
--

CREATE TABLE `prefix_researchfield` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Forschungsfelder';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_ressuebersicht`
--

CREATE TABLE `prefix_ressuebersicht` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `datum` int(10) unsigned DEFAULT '0',
  `eisen` float DEFAULT NULL,
  `stahl` float DEFAULT NULL,
  `vv4a` float DEFAULT NULL,
  `chem` float DEFAULT NULL,
  `eis` float DEFAULT NULL,
  `wasser` float DEFAULT NULL,
  `energie` float DEFAULT NULL,
  `fp_ph` float DEFAULT NULL,
  `credits` float DEFAULT NULL,
  `bev_a` float DEFAULT NULL,
  `bev_g` float DEFAULT NULL,
  `bev_q` float DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_scans`
--

CREATE TABLE `prefix_scans` (
  `coords` varchar(11) NOT NULL DEFAULT '',
  `coords_gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `coords_sys` smallint(6) NOT NULL DEFAULT '0',
  `coords_planet` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user` varchar(50) NOT NULL DEFAULT '',
  `userchange_time` int(10) unsigned DEFAULT NULL,
  `allianz` varchar(50) NOT NULL DEFAULT '',
  `planetenname` varchar(50) NOT NULL DEFAULT '',
  `punkte` int(10) unsigned NOT NULL DEFAULT '0',
  `typ` enum('','Nichts','Steinklumpen','Asteroid','Gasgigant','Eisplanet') NOT NULL DEFAULT '',
  `typchange_time` int(10) unsigned DEFAULT NULL,
  `objekt` enum('---','Artefaktbasis','Kampfbasis','Kolonie','Raumstation','Sammelbasis') NOT NULL DEFAULT '---',
  `objektchange_time` int(10) unsigned DEFAULT NULL,
  `nebel` enum('','blau','gelb','gruen','rot','violett') NOT NULL DEFAULT '',
  `eisengehalt` float DEFAULT NULL,
  `chemievorkommen` float DEFAULT NULL,
  `eisdichte` float DEFAULT NULL,
  `lebensbedingungen` float DEFAULT NULL,
  `gravitation` float DEFAULT NULL,
  `besonderheiten` text,
  `fmod` float DEFAULT NULL,
  `kgmod` float DEFAULT NULL,
  `dgmod` float DEFAULT NULL,
  `ksmod` float DEFAULT NULL,
  `dsmod` float DEFAULT NULL,
  `eisen` int(10) unsigned DEFAULT NULL,
  `stahl` int(10) unsigned DEFAULT NULL,
  `vv4a` int(10) unsigned DEFAULT NULL,
  `chemie` int(10) unsigned DEFAULT NULL,
  `eis` int(10) unsigned DEFAULT NULL,
  `wasser` int(10) unsigned DEFAULT NULL,
  `energie` int(10) unsigned DEFAULT NULL,
  `plan` text,
  `stat` text,
  `def` text,
  `geb` text,
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `reserviert` varchar(50) NOT NULL DEFAULT '',
  `bevoelkerungsanzahl` bigint(20) unsigned DEFAULT NULL,
  `lager_chemie` int(10) unsigned DEFAULT NULL,
  `lager_eis` int(10) unsigned DEFAULT NULL,
  `lager_energie` int(10) unsigned DEFAULT NULL,
  `tteisen` smallint(5) unsigned DEFAULT NULL,
  `ttchemie` smallint(5) unsigned DEFAULT NULL,
  `tteis` smallint(5) unsigned DEFAULT NULL,
  `rnb` varchar(5000) NOT NULL DEFAULT '' COMMENT 'raider-notizblock',
  `x11` int(10) unsigned DEFAULT NULL,
  `terminus` int(10) unsigned DEFAULT NULL,
  `x13` int(10) unsigned DEFAULT NULL,
  `fehlscantime` int(10) unsigned DEFAULT NULL,
  `reserveraid` int(10) unsigned DEFAULT NULL,
  `reserveraiduser` varchar(50) NOT NULL DEFAULT '',
  `gebscantime` int(10) unsigned DEFAULT NULL,
  `schiffscantime` int(10) unsigned DEFAULT NULL,
  `geoscantime` int(10) unsigned DEFAULT NULL,
  `reset_timestamp` int(10) unsigned DEFAULT NULL,
  `plaid` int(10) unsigned DEFAULT NULL,
  `sondierung` int(10) unsigned DEFAULT NULL,
  `sondierunguser` varchar(50) DEFAULT NULL,
  `angriff` int(10) unsigned DEFAULT NULL,
  `angriffuser` varchar(50) DEFAULT NULL,
  `planet_farbe` varchar(7) NOT NULL DEFAULT '',
  `sortierung` int(2) NOT NULL DEFAULT '99',
  `planet_pic` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `astro_pic` tinyint(3) unsigned DEFAULT NULL,
  `shadow_pic` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bg_pic` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `time_att` int(10) unsigned NOT NULL DEFAULT '0',
  `att` varchar(10000) NOT NULL DEFAULT '',
  `geolink` varchar(120) NOT NULL DEFAULT '',
  `geblink` varchar(120) NOT NULL DEFAULT '',
  `schifflink` varchar(120) NOT NULL DEFAULT '',
  `bed_eisen` int(10) unsigned NOT NULL DEFAULT '0',
  `bed_stahl` int(10) unsigned NOT NULL DEFAULT '0',
  `bed_vv4a` int(10) unsigned NOT NULL DEFAULT '0',
  `bed_chemie` int(10) unsigned NOT NULL DEFAULT '0',
  `bed_eis` int(10) unsigned NOT NULL DEFAULT '0',
  `bed_wasser` int(10) unsigned NOT NULL DEFAULT '0',
  `bed_energie` int(10) unsigned NOT NULL DEFAULT '0',
  `bed_bev` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`coords`),
  UNIQUE KEY `scans_coords_2` (`coords_gal`,`coords_sys`,`coords_planet`),
  KEY `scans_coords_gal` (`coords_gal`),
  KEY `scans_coords_sys` (`coords_sys`),
  KEY `scans_user` (`user`),
  KEY `scans_coords_pla` (`coords_planet`),
  KEY `time` (`time`),
  KEY `typchange_time` (`typchange_time`),
  KEY `userchange_time` (`userchange_time`),
  KEY `objektchange_time` (`objektchange_time`),
  KEY `typ` (`typ`),
  KEY `time_2` (`time`),
  KEY `typ_2` (`typ`),
  KEY `typchange_time_2` (`typchange_time`),
  KEY `userchange_time_2` (`userchange_time`),
  KEY `objektchange_time_2` (`objektchange_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_scans_details`
--

CREATE TABLE `prefix_scans_details` (
  `coords` varchar(10) NOT NULL DEFAULT '',
  `art` char(1) NOT NULL DEFAULT 'S',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `plan` varchar(10000) NOT NULL DEFAULT '',
  `stat` varchar(10000) NOT NULL DEFAULT '',
  `def` text NOT NULL,
  PRIMARY KEY (`coords`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_scans_geb`
--

CREATE TABLE `prefix_scans_geb` (
  `coords` varchar(10) NOT NULL DEFAULT '',
  `geb_id_iw` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Gebäude IW-ID',
  `geb_anz` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Gebäudeanzahl',
  `time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Unixzeit'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='DB-Gebäudescans';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_schiffe`
--

CREATE TABLE `prefix_schiffe` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `schiff` int(10) unsigned NOT NULL DEFAULT '0',
  `anzahl` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_schiffstyp`
--

CREATE TABLE `prefix_schiffstyp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `schiff` varchar(100) NOT NULL DEFAULT '',
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
  `canLeaveGalaxy` tinyint(1) unsigned DEFAULT NULL,
  `canBeTransported` tinyint(1) unsigned DEFAULT NULL,
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
  `bestellbar` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isTransporter` tinyint(1) unsigned DEFAULT NULL,
  `klasse1` int(10) unsigned DEFAULT NULL,
  `klasse2` int(10) unsigned DEFAULT NULL,
  `bev` int(10) unsigned DEFAULT NULL,
  `isCarrier` tinyint(1) unsigned DEFAULT NULL,
  `shipKapa1` smallint(5) unsigned DEFAULT NULL,
  `shipKapa2` smallint(5) unsigned DEFAULT NULL,
  `shipKapa3` smallint(5) unsigned DEFAULT NULL,
  `aktualisiert` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schiff` (`schiff`),
  UNIQUE KEY `id_iw` (`id_iw`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sid`
--

CREATE TABLE `prefix_sid` (
  `sid` varchar(50) NOT NULL DEFAULT '',
  `ipHash` varchar(100) DEFAULT NULL,
  `userAgentHash` varchar(100) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `id` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`sid`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sitterauftrag`
--

CREATE TABLE `prefix_sitterauftrag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `date_b1` int(10) unsigned NOT NULL DEFAULT '0',
  `date_b2` int(10) unsigned NOT NULL DEFAULT '0',
  `user` varchar(50) NOT NULL DEFAULT '',
  `ByUser` varchar(50) NOT NULL DEFAULT '',
  `planet` varchar(50) NOT NULL DEFAULT '',
  `auftrag` text NOT NULL,
  `bauid` int(5) NOT NULL DEFAULT '0',
  `bauschleife` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `schieben` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `schiffanz` varchar(15) NOT NULL DEFAULT '0',
  `typ` varchar(20) NOT NULL DEFAULT '',
  `refid` int(11) NOT NULL DEFAULT '0',
  `irc` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `resid` int(11) NOT NULL DEFAULT '0',
  `dauerauftrag` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sitterlog`
--

CREATE TABLE `prefix_sitterlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sitterlogin` varchar(50) NOT NULL DEFAULT '',
  `fromuser` varchar(50) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `action` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_spieler`
--

CREATE TABLE `prefix_spieler` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  `allianzrang` varchar(50) DEFAULT NULL,
  `exallianz` varchar(50) DEFAULT NULL,
  `allychange_time` int(10) unsigned DEFAULT NULL,
  `staatsform` varchar(50) DEFAULT NULL,
  `acctype` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT '',
  `dabeiseit` int(10) unsigned DEFAULT NULL,
  `playerupdate_time` int(10) unsigned NOT NULL DEFAULT '0',
  `geb_pkt` int(10) unsigned DEFAULT NULL,
  `forsch_pkt` int(10) unsigned DEFAULT NULL,
  `ges_pkt` int(10) unsigned DEFAULT NULL,
  `pktupdate_time` int(10) unsigned DEFAULT NULL,
  `Hauptplanet` varchar(10) DEFAULT NULL COMMENT 'Hauptplanet des Spielers',
  `pos` int(10) unsigned DEFAULT NULL,
  `gebp` int(10) unsigned NOT NULL DEFAULT '0',
  `fp` int(10) unsigned NOT NULL DEFAULT '0',
  `gesamtp` int(10) unsigned NOT NULL DEFAULT '0',
  `ptag` float NOT NULL DEFAULT '0',
  `diff` int(11) DEFAULT NULL,
  `gebp_nodiff` int(10) unsigned NOT NULL DEFAULT '0',
  `fp_nodiff` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `einmaurer` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `gesperrt` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `umode` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`),
  KEY `allychange_time` (`allychange_time`),
  KEY `Hauptplanet` (`Hauptplanet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabelle aller Spieler';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_spielerallychange`
--

CREATE TABLE `prefix_spielerallychange` (
  `name` varchar(50) NOT NULL DEFAULT '',
  `fromally` varchar(50) NOT NULL DEFAULT '',
  `toally` varchar(50) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`,`fromally`,`toally`,`time`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sysscans`
--

CREATE TABLE `prefix_sysscans` (
  `id` varchar(7) NOT NULL DEFAULT '',
  `gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sys` smallint(5) unsigned NOT NULL DEFAULT '0',
  `objekt` varchar(20) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `nebula` enum('','blau','gelb','gruen','rot','violett') NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_target`
--

CREATE TABLE `prefix_target` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `coords_gal` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `coords_sys` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coords_planet` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`,`name`,`coords_gal`,`coords_sys`,`coords_planet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_transferliste`
--

CREATE TABLE `prefix_transferliste` (
  `zeitmarke` int(10) unsigned NOT NULL DEFAULT '0',
  `buddler` varchar(50) NOT NULL DEFAULT '' COMMENT 'Absender',
  `fleeter` varchar(50) NOT NULL DEFAULT '' COMMENT 'Empfänger',
  `eisen` int(11) DEFAULT '0',
  `stahl` int(11) DEFAULT '0',
  `vv4a` int(11) DEFAULT '0',
  `chem` int(11) DEFAULT '0',
  `eis` int(11) DEFAULT '0',
  `wasser` int(11) DEFAULT '0',
  `energie` int(11) DEFAULT '0',
  `volk` int(11) DEFAULT '0',
  PRIMARY KEY (`zeitmarke`,`buddler`,`fleeter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_user`
--

CREATE TABLE `prefix_user` (
  `id` varchar(30) NOT NULL DEFAULT '',
  `staatsform` int(1) NOT NULL DEFAULT '0',
  `password` varchar(40) NOT NULL DEFAULT '36dd80d60af38df8614b87f74059e5da' COMMENT 'Icewars2013',
  `email` varchar(160) NOT NULL DEFAULT '',
  `allow_ip_change` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status` varchar(10) NOT NULL DEFAULT '',
  `rules` char(1) NOT NULL DEFAULT '0',
  `logindate` int(11) NOT NULL DEFAULT '0',
  `allianz` varchar(30) NOT NULL DEFAULT '',
  `grav_von` float NOT NULL DEFAULT '0.5',
  `grav_bis` float NOT NULL DEFAULT '2',
  `gal_start` char(2) NOT NULL DEFAULT '0',
  `gal_end` char(2) NOT NULL DEFAULT '0',
  `sys_start` char(3) NOT NULL DEFAULT '0',
  `sys_end` char(3) NOT NULL DEFAULT '0',
  `preset` int(11) NOT NULL DEFAULT '0',
  `planibilder` char(1) NOT NULL DEFAULT '1',
  `gebbilder` char(1) NOT NULL DEFAULT '1',
  `geopunkte` int(11) NOT NULL DEFAULT '0',
  `syspunkte` int(11) NOT NULL DEFAULT '0',
  `sitterlogin` varchar(30) NOT NULL DEFAULT '',
  `sitterpwd` varchar(90) DEFAULT NULL,
  `sitterskin` tinyint(4) NOT NULL DEFAULT '0',
  `sittercomment` text NOT NULL,
  `sitten` char(1) NOT NULL DEFAULT '',
  `adminsitten` char(1) NOT NULL DEFAULT '',
  `newspermission` tinyint(1) NOT NULL DEFAULT '1',
  `mailpermission` tinyint(1) NOT NULL DEFAULT '1',
  `sitterpunkte` int(11) NOT NULL DEFAULT '0',
  `gebaeude` text NOT NULL,
  `peitschen` char(1) NOT NULL DEFAULT '',
  `gengebmod` float NOT NULL DEFAULT '1',
  `genbauschleife` char(1) NOT NULL DEFAULT '',
  `genmaurer` char(1) NOT NULL DEFAULT '',
  `budflesol` varchar(50) NOT NULL DEFAULT '',
  `buddlerfrom` varchar(30) NOT NULL DEFAULT '',
  `rang` varchar(20) NOT NULL DEFAULT '',
  `gebp` int(12) NOT NULL DEFAULT '0',
  `fp` int(12) NOT NULL DEFAULT '0',
  `gesamtp` int(12) NOT NULL DEFAULT '0',
  `ptag` float NOT NULL DEFAULT '0',
  `dabei` int(11) NOT NULL,
  `titel` varchar(150) NOT NULL DEFAULT '',
  `userlink` char(1) NOT NULL DEFAULT '',
  `lastshipscan` varchar(11) NOT NULL DEFAULT '',
  `menu_default` varchar(20) NOT NULL DEFAULT 'default',
  `gesperrt` tinyint(1) NOT NULL DEFAULT '0',
  `color` varchar(7) NOT NULL DEFAULT '0',
  `ikea` char(1) NOT NULL DEFAULT '',
  `sound` tinyint(1) NOT NULL DEFAULT '0',
  `squad` varchar(30) NOT NULL DEFAULT '',
  `switch` int(11) NOT NULL DEFAULT '0' COMMENT 'zum Speichern der Einstellungen bei m_produktion',
  `lastsitterlogin` int(11) NOT NULL DEFAULT '0',
  `lastsitteruser` varchar(30) NOT NULL DEFAULT '',
  `lastsitterloggedin` int(11) NOT NULL DEFAULT '0',
  `fremdesitten` int(1) NOT NULL DEFAULT '0' COMMENT 'Darf fremde Allianzen sitten',
  `vonfremdesitten` int(1) NOT NULL DEFAULT '0' COMMENT 'Darf von fremden Allianzen gesittet werden',
  `iwsa` char(1) NOT NULL,
  `lasttransport` varchar(11) DEFAULT NULL,
  `uniprop` int(11) NOT NULL DEFAULT '1',
  `dauersitten` int(11) NOT NULL DEFAULT '0' COMMENT 'Anzahl Sekunden bis Login fällig',
  `dauersittentext` varchar(255) NOT NULL COMMENT 'Kommentar zum Dauersitten',
  `dauersittenlast` int(11) DEFAULT NULL COMMENT 'Timestamp der letzten Dauersitten-Erledigung',
  `NewUniXmlTime` int(10) unsigned DEFAULT NULL COMMENT 'Timestamp der nächsten Unixml Parsemöglichkeit',
  PRIMARY KEY (`id`),
  KEY `ikea` (`ikea`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_user_research`
--

CREATE TABLE `prefix_user_research` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `rId` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Aktuelle Forschungen der User';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_versand_auftrag`
--

CREATE TABLE `prefix_versand_auftrag` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `pos` int(11) NOT NULL DEFAULT '0',
  `reference` int(10) unsigned NOT NULL DEFAULT '0',
  `art` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`user`,`time`,`pos`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_wronglogin`
--

CREATE TABLE `prefix_wronglogin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
