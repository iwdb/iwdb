-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 17. Mai 2012 um 22:45
-- Server Version: 5.5.24
-- PHP-Version: 5.3.13-1~dotdeb.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `iwdb`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_allianzstatus`
--

CREATE TABLE IF NOT EXISTS `prefix_allianzstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `allianz` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Status der eigenen Allianz zu anderen' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung`
--

CREATE TABLE IF NOT EXISTS `prefix_bestellung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(30) DEFAULT NULL,
  `team` varchar(30) DEFAULT NULL,
  `coords_gal` tinyint(4) NOT NULL,
  `coords_sys` int(11) NOT NULL,
  `coords_planet` tinyint(4) NOT NULL,
  `project` varchar(30) NOT NULL,
  `text` varchar(254) NOT NULL,
  `time` int(12) DEFAULT NULL,
  `eisen` int(7) DEFAULT '0',
  `stahl` int(7) DEFAULT '0',
  `chemie` int(7) DEFAULT '0',
  `vv4a` int(7) DEFAULT '0',
  `eis` int(7) DEFAULT '0',
  `wasser` int(7) DEFAULT '0',
  `energie` int(7) DEFAULT '0',
  `credits` int(7) DEFAULT '0',
  `volk` int(7) DEFAULT '0',
  `offen_eisen` int(11) NOT NULL,
  `offen_stahl` int(11) NOT NULL,
  `offen_chemie` int(11) NOT NULL,
  `offen_vv4a` int(11) NOT NULL,
  `offen_eis` int(11) NOT NULL,
  `offen_wasser` int(11) NOT NULL,
  `offen_energie` int(11) NOT NULL,
  `offen_credits` int(11) NOT NULL,
  `schiff` varchar(50) DEFAULT NULL,
  `anzahl` int(7) DEFAULT '1',
  `prio` int(4) NOT NULL DEFAULT '1',
  `taeglich` bit(1) NOT NULL DEFAULT b'0',
  `time_created` int(12) NOT NULL,
  `erledigt` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Bestellsystem' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung_projekt`
--

CREATE TABLE IF NOT EXISTS `prefix_bestellung_projekt` (
  `name` varchar(30) NOT NULL,
  `prio` int(11) NOT NULL,
  `schiff` int(1) NOT NULL,
  PRIMARY KEY (`name`,`schiff`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung_schiffe`
--

CREATE TABLE IF NOT EXISTS `prefix_bestellung_schiffe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(30) DEFAULT NULL,
  `team` varchar(30) DEFAULT NULL,
  `coords_gal` tinyint(4) NOT NULL,
  `coords_sys` int(11) NOT NULL,
  `coords_planet` tinyint(4) NOT NULL,
  `project` varchar(30) NOT NULL,
  `text` varchar(254) NOT NULL,
  `time` int(12) DEFAULT NULL,
  `time_created` int(12) NOT NULL,
  `erledigt` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Bestellsystem' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung_schiffe_pos`
--

CREATE TABLE IF NOT EXISTS `prefix_bestellung_schiffe_pos` (
  `bestellung_id` int(11) NOT NULL,
  `schiffstyp_id` int(11) NOT NULL,
  `menge` int(11) NOT NULL,
  `offen` int(11) NOT NULL,
  PRIMARY KEY (`bestellung_id`,`schiffstyp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_building2building`
--

CREATE TABLE IF NOT EXISTS `prefix_building2building` (
  `bOld` int(10) unsigned NOT NULL DEFAULT '0',
  `bNew` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bOld`,`bNew`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gebäude bOld ermöglicht Gebäude bNew';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_building2research`
--

CREATE TABLE IF NOT EXISTS `prefix_building2research` (
  `bId` int(10) unsigned NOT NULL DEFAULT '0',
  `rId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bId`,`rId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gebäude bId ermöglicht Forschung rId';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_def`
--

CREATE TABLE IF NOT EXISTS `prefix_def` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `eingebaut` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `id_iw` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `abk` varchar(30) NOT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_gebaeude`
--

CREATE TABLE IF NOT EXISTS `prefix_gebaeude` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `category` varchar(50) NOT NULL DEFAULT '',
  `idcat` int(5) NOT NULL DEFAULT '0',
  `inactive` char(1) NOT NULL DEFAULT '',
  `dauer` int(7) NOT NULL DEFAULT '0',
  `bild` varchar(40) NOT NULL DEFAULT '',
  `info` text NOT NULL,
  `n_building` text NOT NULL,
  `n_research` text NOT NULL,
  `n_kolotyp` text NOT NULL,
  `n_planityp` text NOT NULL,
  `e_research` text NOT NULL,
  `e_building` text NOT NULL,
  `zerstoert` text NOT NULL,
  `bringt` varchar(200) NOT NULL DEFAULT '',
  `Kosten` varchar(200) NOT NULL DEFAULT '',
  `Punkte` int(5) NOT NULL DEFAULT '0',
  `MaximaleAnzahl` int(3) NOT NULL DEFAULT '0',
  `typ` varchar(5) NOT NULL DEFAULT '',
  `kostet` varchar(200) NOT NULL,
  `id_iw` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gebäudekurzform' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_gebaeude_spieler`
--

CREATE TABLE IF NOT EXISTS `prefix_gebaeude_spieler` (
  `coords_gal` tinyint(4) NOT NULL,
  `coords_sys` smallint(6) NOT NULL,
  `coords_planet` tinyint(4) NOT NULL,
  `kolo_typ` varchar(20) NOT NULL,
  `user` varchar(30) NOT NULL,
  `category` varchar(100) NOT NULL,
  `building` varchar(200) NOT NULL,
  `count` smallint(6) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`coords_gal`,`coords_sys`,`coords_planet`,`category`,`building`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gebäudeuebersicht';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_group`
--

CREATE TABLE IF NOT EXISTS `prefix_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_group_sort`
--

CREATE TABLE IF NOT EXISTS `prefix_group_sort` (
  `group_id` int(11) NOT NULL,
  `module` varchar(30) NOT NULL,
  `user_id` varchar(30) NOT NULL,
  `sort` int(11) NOT NULL,
  `selected` int(11) NOT NULL,
  `collapsed` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`module`,`user_id`,`sort`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_group_user`
--

CREATE TABLE IF NOT EXISTS `prefix_group_user` (
  `group_id` int(11) NOT NULL,
  `user_id` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_highscore`
--

CREATE TABLE IF NOT EXISTS `prefix_highscore` (
  `name` varchar(30) NOT NULL DEFAULT '',
  `allianz` varchar(50) DEFAULT NULL,
  `pos` int(12) DEFAULT NULL,
  `gebp` int(12) NOT NULL DEFAULT '0',
  `fp` int(12) NOT NULL DEFAULT '0',
  `gesamtp` int(12) NOT NULL DEFAULT '0',
  `ptag` float NOT NULL DEFAULT '0',
  `diff` int(12) DEFAULT NULL,
  `dabei_seit` int(12) NOT NULL DEFAULT '0',
  `gebp_nodiff` int(12) NOT NULL DEFAULT '0',
  `fp_nodiff` int(12) NOT NULL DEFAULT '0',
  `time` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_iwdbtabellen`
--

CREATE TABLE IF NOT EXISTS `prefix_iwdbtabellen` (
  `name` varchar(40) NOT NULL DEFAULT '',
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabellennamen, die in der DB verwendet werden.';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kasse_content`
--

CREATE TABLE IF NOT EXISTS `prefix_kasse_content` (
  `amount` decimal(22,2) NOT NULL DEFAULT '0.00',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  `time_of_insert` date NOT NULL DEFAULT '0000-00-00',
  UNIQUE KEY `allianz` (`allianz`,`time_of_insert`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kasse_incoming`
--

CREATE TABLE IF NOT EXISTS `prefix_kasse_incoming` (
  `user` varchar(30) NOT NULL DEFAULT '',
  `amount` decimal(22,2) NOT NULL DEFAULT '0.00',
  `time_of_insert` date NOT NULL DEFAULT '0000-00-00',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  UNIQUE KEY `user` (`user`,`time_of_insert`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kasse_outgoing`
--

CREATE TABLE IF NOT EXISTS `prefix_kasse_outgoing` (
  `payedfrom` varchar(30) NOT NULL DEFAULT '',
  `payedto` varchar(30) NOT NULL DEFAULT '',
  `amount` bigint(20) unsigned NOT NULL DEFAULT '0',
  `time_of_pay` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  UNIQUE KEY `payedfrom` (`payedfrom`,`payedto`,`amount`,`time_of_pay`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb`
--

CREATE TABLE IF NOT EXISTS `prefix_kb` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(100) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL,
  `verteidiger` varchar(60) NOT NULL DEFAULT '',
  `verteidiger_ally` varchar(255) NOT NULL DEFAULT '',
  `planet_name` varchar(60) NOT NULL DEFAULT '',
  `koords_gal` int(11) NOT NULL DEFAULT '0',
  `koords_sol` int(11) NOT NULL DEFAULT '0',
  `koords_pla` int(11) NOT NULL DEFAULT '0',
  `typ` varchar(60) NOT NULL DEFAULT '',
  `resultat` varchar(60) NOT NULL DEFAULT '',
  KEY `koords_gal` (`koords_gal`,`koords_sol`,`koords_pla`),
  KEY `verteidiger_ally` (`verteidiger_ally`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_bomb`
--

CREATE TABLE IF NOT EXISTS `prefix_kb_bomb` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL,
  `user` varchar(30) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_bomb_geb`
--

CREATE TABLE IF NOT EXISTS `prefix_kb_bomb_geb` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `ID_IW_GEB` int(11) NOT NULL DEFAULT '0',
  `anzahl` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_def`
--

CREATE TABLE IF NOT EXISTS `prefix_kb_def` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `ID_IW_DEF` int(11) NOT NULL DEFAULT '0',
  `anz_start` int(11) NOT NULL DEFAULT '0',
  `anz_verlust` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_flotten`
--

CREATE TABLE IF NOT EXISTS `prefix_kb_flotten` (
  `ID_FLOTTE` int(11) NOT NULL AUTO_INCREMENT,
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL,
  `art` int(11) NOT NULL DEFAULT '0',
  `name` varchar(60) NOT NULL DEFAULT '',
  `ally` varchar(60) NOT NULL DEFAULT '',
  `planet_name` varchar(60) NOT NULL DEFAULT '',
  `koords_string` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID_FLOTTE`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_flotten_schiffe`
--

CREATE TABLE IF NOT EXISTS `prefix_kb_flotten_schiffe` (
  `ID_FLOTTE` int(11) NOT NULL,
  `ID_IW_SCHIFF` int(11) NOT NULL DEFAULT '0',
  `anz_start` int(11) NOT NULL DEFAULT '0',
  `anz_verlust` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID_FLOTTE`,`ID_IW_SCHIFF`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_pluenderung`
--

CREATE TABLE IF NOT EXISTS `prefix_kb_pluenderung` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `ID_IW_RESS` int(11) NOT NULL DEFAULT '0',
  `anzahl` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_verluste`
--

CREATE TABLE IF NOT EXISTS `prefix_kb_verluste` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `ID_IW_RESS` int(11) NOT NULL DEFAULT '0',
  `seite` int(11) NOT NULL DEFAULT '0',
  `anzahl` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_lager`
--

CREATE TABLE IF NOT EXISTS `prefix_lager` (
  `user` varchar(30) NOT NULL DEFAULT '',
  `coords_gal` tinyint(4) NOT NULL DEFAULT '0',
  `coords_sys` smallint(6) NOT NULL DEFAULT '0',
  `coords_planet` tinyint(4) NOT NULL DEFAULT '0',
  `kolo_typ` varchar(20) NOT NULL DEFAULT '',
  `eisen` float NOT NULL DEFAULT '0',
  `eisen_prod` float NOT NULL DEFAULT '0',
  `eisen_bunker` float NOT NULL DEFAULT '0',
  `stahl` float NOT NULL DEFAULT '0',
  `stahl_prod` float NOT NULL DEFAULT '0',
  `stahl_bunker` float NOT NULL DEFAULT '0',
  `vv4a` float NOT NULL DEFAULT '0',
  `vv4a_prod` float NOT NULL DEFAULT '0',
  `vv4a_bunker` float NOT NULL DEFAULT '0',
  `chem` float NOT NULL DEFAULT '0',
  `chem_prod` float NOT NULL DEFAULT '0',
  `chem_lager` float NOT NULL DEFAULT '0',
  `chem_bunker` float NOT NULL DEFAULT '0',
  `eis` float NOT NULL DEFAULT '0',
  `eis_prod` float NOT NULL DEFAULT '0',
  `eis_lager` float NOT NULL DEFAULT '0',
  `eis_bunker` float NOT NULL DEFAULT '0',
  `wasser` float NOT NULL DEFAULT '0',
  `wasser_prod` float NOT NULL DEFAULT '0',
  `wasser_bunker` float NOT NULL DEFAULT '0',
  `energie` float NOT NULL DEFAULT '0',
  `energie_prod` float NOT NULL DEFAULT '0',
  `energie_lager` float NOT NULL DEFAULT '0',
  `energie_bunker` float NOT NULL DEFAULT '0',
  `fp` float DEFAULT NULL,
  `fp_b` float DEFAULT NULL,
  `fp_m1` float DEFAULT NULL,
  `fp_m2` float DEFAULT NULL,
  `credits` float DEFAULT NULL,
  `bev_a` float DEFAULT NULL,
  `bev_g` float DEFAULT NULL,
  `bev_q` float DEFAULT NULL,
  `bev_w` float DEFAULT NULL,
  `zufr` float DEFAULT NULL,
  `zufr_w` float DEFAULT NULL,
  `eisen_soll` float DEFAULT NULL,
  `stahl_soll` float DEFAULT NULL,
  `vv4a_soll` float DEFAULT NULL,
  `chem_soll` float DEFAULT NULL,
  `eis_soll` float DEFAULT NULL,
  `wasser_soll` float DEFAULT NULL,
  `energie_soll` float DEFAULT NULL,
  `eisen_baukosten` float NOT NULL,
  `stahl_baukosten` float NOT NULL,
  `vv4a_baukosten` float NOT NULL,
  `chemie_baukosten` float NOT NULL,
  `eis_baukosten` float NOT NULL,
  `wasser_baukosten` float NOT NULL,
  `energie_baukosten` float NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
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

CREATE TABLE IF NOT EXISTS `prefix_lieferung` (
  `time` int(11) NOT NULL DEFAULT '0',
  `coords_from_gal` tinyint(4) NOT NULL DEFAULT '0',
  `coords_from_sys` smallint(6) NOT NULL DEFAULT '0',
  `coords_from_planet` tinyint(4) NOT NULL DEFAULT '0',
  `coords_to_gal` tinyint(4) NOT NULL DEFAULT '0',
  `coords_to_sys` smallint(6) NOT NULL DEFAULT '0',
  `coords_to_planet` tinyint(4) NOT NULL DEFAULT '0',
  `user_from` varchar(30) DEFAULT NULL,
  `user_to` varchar(30) DEFAULT NULL,
  `eisen` float DEFAULT NULL,
  `stahl` float DEFAULT NULL,
  `vv4a` float DEFAULT NULL,
  `chem` float DEFAULT NULL,
  `eis` float DEFAULT NULL,
  `wasser` float DEFAULT NULL,
  `energie` float DEFAULT NULL,
  `art` varchar(255) DEFAULT NULL,
  `schiffe` text,
  PRIMARY KEY (`time`,`coords_from_gal`,`coords_from_sys`,`coords_from_planet`,`coords_to_gal`,`coords_to_sys`,`coords_to_planet`),
  KEY `coords_to_gal` (`coords_to_gal`,`coords_to_sys`,`coords_to_planet`,`art`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Lieferung';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_menu`
--

CREATE TABLE IF NOT EXISTS `prefix_menu` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Menüstruktur' AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_merkmale`
--

CREATE TABLE IF NOT EXISTS `prefix_merkmale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merkmal` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `merkmal` (`merkmal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Planetenmerkmale zur Suche' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_order_comment`
--

CREATE TABLE IF NOT EXISTS `prefix_order_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `time` int(12) NOT NULL,
  `user` varchar(30) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_params`
--

CREATE TABLE IF NOT EXISTS `prefix_params` (
  `name` varchar(80) NOT NULL DEFAULT '',
  `value` varchar(80) NOT NULL DEFAULT '',
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_parsemenu`
--

CREATE TABLE IF NOT EXISTS `prefix_parsemenu` (
  `ersetze` varchar(100) NOT NULL DEFAULT '',
  `durch` text NOT NULL,
  `varorstr` char(3) NOT NULL DEFAULT 'str'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_parser`
--

CREATE TABLE IF NOT EXISTS `prefix_parser` (
  `modulename` varchar(30) NOT NULL DEFAULT '',
  `recognizer` varchar(200) NOT NULL DEFAULT '',
  `message` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`modulename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Parser-Phrasen und Zuordnungen zu Parsermodulen';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_preset`
--

CREATE TABLE IF NOT EXISTS `prefix_preset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Einstellungen für die Suche' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_punktelog`
--

CREATE TABLE IF NOT EXISTS `prefix_punktelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(30) NOT NULL DEFAULT '',
  `date` int(12) NOT NULL DEFAULT '0',
  `gebp` int(12) NOT NULL DEFAULT '0',
  `fp` int(12) NOT NULL DEFAULT '0',
  `gesamtp` int(12) NOT NULL DEFAULT '0',
  `ptag` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Punktenachverfolgung' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_raidview`
--

CREATE TABLE IF NOT EXISTS `prefix_raidview` (
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `coords` (`coords`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Raidberichte' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research`
--

CREATE TABLE IF NOT EXISTS `prefix_research` (
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
  `gameversion` varchar(10) NOT NULL DEFAULT '10.1',
  `reingestellt` varchar(50) DEFAULT NULL,
  `FPakt` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschungsinformation fuer Forschung Id' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2building`
--

CREATE TABLE IF NOT EXISTS `prefix_research2building` (
  `rId` int(10) unsigned NOT NULL DEFAULT '0',
  `bId` int(10) unsigned NOT NULL DEFAULT '0',
  `lvl` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rId`,`bId`,`lvl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschung rId ermöglicht Gebäude(stufe) bId';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2prototype`
--

CREATE TABLE IF NOT EXISTS `prefix_research2prototype` (
  `rid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschung rId ermöglicht Prototyp pId';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2research`
--

CREATE TABLE IF NOT EXISTS `prefix_research2research` (
  `rOld` int(10) unsigned NOT NULL DEFAULT '0',
  `rNew` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rOld`,`rNew`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschung rOld ermöglicht Forschung rNew';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2user`
--

CREATE TABLE IF NOT EXISTS `prefix_research2user` (
  `rid` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` varchar(30) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='bereits erforschte Forschungen des Benutzers';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_researchfield`
--

CREATE TABLE IF NOT EXISTS `prefix_researchfield` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschungsfelder' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_ressuebersicht`
--

CREATE TABLE IF NOT EXISTS `prefix_ressuebersicht` (
  `user` varchar(50) NOT NULL DEFAULT '',
  `datum` int(11) DEFAULT NULL,
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

CREATE TABLE IF NOT EXISTS `prefix_scans` (
  `coords` varchar(11) NOT NULL DEFAULT '',
  `coords_gal` tinyint(4) NOT NULL DEFAULT '0',
  `coords_sys` smallint(6) NOT NULL DEFAULT '0',
  `coords_planet` tinyint(4) NOT NULL DEFAULT '0',
  `user` varchar(30) NOT NULL DEFAULT '',
  `allianz` varchar(50) NOT NULL DEFAULT '',
  `planetenname` varchar(30) NOT NULL DEFAULT '',
  `punkte` int(12) NOT NULL DEFAULT '0',
  `typ` varchar(20) NOT NULL DEFAULT '',
  `objekt` varchar(20) NOT NULL DEFAULT '',
  `eisengehalt` float NOT NULL DEFAULT '0',
  `chemievorkommen` float NOT NULL DEFAULT '0',
  `eisdichte` float NOT NULL DEFAULT '0',
  `lebensbedingungen` float NOT NULL DEFAULT '0',
  `gravitation` float NOT NULL DEFAULT '0',
  `besonderheiten` text NOT NULL,
  `fmod` float NOT NULL,
  `kgmod` float NOT NULL,
  `dgmod` float NOT NULL,
  `ksmod` float NOT NULL,
  `dsmod` float NOT NULL,
  `eisen` int(12) NOT NULL DEFAULT '0',
  `stahl` int(12) NOT NULL DEFAULT '0',
  `vv4a` int(12) NOT NULL DEFAULT '0',
  `chemie` int(12) NOT NULL DEFAULT '0',
  `eis` int(12) NOT NULL DEFAULT '0',
  `wasser` int(12) NOT NULL DEFAULT '0',
  `energie` int(12) NOT NULL DEFAULT '0',
  `plan` text NOT NULL,
  `stat` text NOT NULL,
  `def` text NOT NULL,
  `geb` text NOT NULL,
  `time` int(12) NOT NULL DEFAULT '0',
  `reserviert` varchar(30) NOT NULL DEFAULT '',
  `bevoelkerungsanzahl` bigint(20) NOT NULL DEFAULT '0',
  `lager_chemie` int(11) NOT NULL DEFAULT '0',
  `lager_eis` int(11) NOT NULL DEFAULT '0',
  `lager_energie` int(11) NOT NULL DEFAULT '0',
  `tteisen` float NOT NULL DEFAULT '0',
  `ttchemie` float NOT NULL DEFAULT '0',
  `tteis` float NOT NULL DEFAULT '0',
  `rnb` text NOT NULL COMMENT 'raider-notizblock',
  `x11` int(12) DEFAULT NULL,
  `terminus` int(12) DEFAULT NULL,
  `x13` int(12) DEFAULT NULL,
  `fehlscantime` int(12) DEFAULT NULL,
  `reserveraid` int(12) DEFAULT NULL,
  `reserveraiduser` varchar(30) DEFAULT NULL,
  `gebscantime` int(11) NOT NULL DEFAULT '0',
  `schiffscantime` int(11) NOT NULL DEFAULT '0',
  `geoscantime` int(11) NOT NULL DEFAULT '0',
  `reset_timestamp` int(11) NOT NULL DEFAULT '0',
  `plaid` int(11) DEFAULT NULL,
  `sondierung` int(11) DEFAULT NULL,
  `sondierunguser` varchar(30) DEFAULT NULL,
  `angriff` int(11) DEFAULT NULL,
  `angriffuser` varchar(30) DEFAULT NULL,
  `planet_farbe` varchar(7) NOT NULL,
  `sortierung` int(2) NOT NULL DEFAULT '99',
  PRIMARY KEY (`coords`),
  KEY `user` (`user`(4)),
  KEY `scans_coords_gal` (`coords_gal`),
  KEY `scans_coords_sys` (`coords_sys`),
  KEY `coords_gal` (`coords_gal`,`coords_sys`),
  KEY `coords_gal_2` (`coords_gal`,`coords_sys`,`coords_planet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_scans_historie`
--

CREATE TABLE IF NOT EXISTS `prefix_scans_historie` (
  `coords` varchar(11) NOT NULL DEFAULT '',
  `time` int(12) NOT NULL DEFAULT '0',
  `coords_gal` tinyint(4) NOT NULL DEFAULT '0',
  `coords_sys` smallint(6) NOT NULL DEFAULT '0',
  `coords_planet` tinyint(4) NOT NULL DEFAULT '0',
  `user` varchar(30) NOT NULL DEFAULT '',
  `allianz` varchar(30) NOT NULL DEFAULT '',
  `punkte` int(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`coords`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Punktehistorie der Scans';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_schiffe`
--

CREATE TABLE IF NOT EXISTS `prefix_schiffe` (
  `user` varchar(30) NOT NULL DEFAULT '',
  `schiff` int(11) unsigned NOT NULL DEFAULT '0',
  `anzahl` int(7) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_schiffstyp`
--

CREATE TABLE IF NOT EXISTS `prefix_schiffstyp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schiff` varchar(80) NOT NULL,
  `abk` varchar(50) NOT NULL DEFAULT '',
  `typ` varchar(50) NOT NULL DEFAULT '',
  `bild` varchar(50) NOT NULL DEFAULT '',
  `id_iw` int(11) DEFAULT NULL,
  `kosten_eisen` int(11) NOT NULL,
  `kosten_stahl` int(11) NOT NULL,
  `kosten_vv4a` int(11) NOT NULL,
  `kosten_chemie` int(11) NOT NULL,
  `kosten_eis` int(11) NOT NULL,
  `kosten_wasser` int(11) NOT NULL,
  `kosten_energie` int(11) NOT NULL,
  `angriff` int(11) NOT NULL,
  `waffenklasse` varchar(30) NOT NULL,
  `verteidigung` int(11) NOT NULL,
  `panzerung_kinetisch` int(11) NOT NULL,
  `panzerung_elektrisch` int(11) NOT NULL,
  `panzerung_gravimetrisch` int(11) NOT NULL,
  `dauer` int(11) NOT NULL,
  `bestellbar` int(1) NOT NULL,
  `klasse1` int(11) NOT NULL,
  `klasse2` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sid`
--

CREATE TABLE IF NOT EXISTS `prefix_sid` (
  `sid` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(50) NOT NULL DEFAULT '',
  `date` int(10) unsigned NOT NULL,
  `id` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`sid`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sitterauftrag`
--

CREATE TABLE IF NOT EXISTS `prefix_sitterauftrag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(12) NOT NULL DEFAULT '0',
  `date_b1` int(12) NOT NULL DEFAULT '0',
  `date_b2` int(12) NOT NULL DEFAULT '0',
  `user` varchar(30) NOT NULL DEFAULT '',
  `ByUser` varchar(30) NOT NULL DEFAULT '',
  `planet` varchar(30) NOT NULL DEFAULT '',
  `auftrag` text NOT NULL,
  `bauid` int(5) NOT NULL DEFAULT '0',
  `bauschleife` char(1) NOT NULL DEFAULT '',
  `schieben` char(1) NOT NULL,
  `schiffanz` varchar(15) NOT NULL DEFAULT '0',
  `typ` varchar(20) NOT NULL DEFAULT '',
  `refid` int(11) NOT NULL DEFAULT '0',
  `irc` char(1) NOT NULL DEFAULT '0',
  `resid` int(11) NOT NULL DEFAULT '0',
  `dauerauftrag` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sitterlog`
--

CREATE TABLE IF NOT EXISTS `prefix_sitterlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitterlogin` varchar(30) NOT NULL DEFAULT '',
  `fromuser` varchar(30) NOT NULL DEFAULT '',
  `date` int(12) NOT NULL DEFAULT '0',
  `action` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sysscans`
--

CREATE TABLE IF NOT EXISTS `prefix_sysscans` (
  `id` varchar(7) NOT NULL DEFAULT '',
  `gal` tinyint(4) NOT NULL DEFAULT '0',
  `sys` smallint(6) NOT NULL DEFAULT '0',
  `objekt` varchar(20) NOT NULL DEFAULT '',
  `date` varchar(11) NOT NULL DEFAULT '',
  `nebula` varchar(10) NOT NULL DEFAULT '',
  KEY `idx_sg_search` (`gal`,`objekt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_target`
--

CREATE TABLE IF NOT EXISTS `prefix_target` (
  `user` varchar(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `coords_gal` tinyint(4) NOT NULL,
  `coords_sys` smallint(6) NOT NULL,
  `coords_planet` tinyint(4) NOT NULL,
  PRIMARY KEY (`user`,`name`,`coords_gal`,`coords_sys`,`coords_planet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_transferliste`
--

CREATE TABLE IF NOT EXISTS `prefix_transferliste` (
  `zeitmarke` int(11) NOT NULL,
  `buddler` varchar(50) NOT NULL,
  `fleeter` varchar(50) NOT NULL,
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
-- Tabellenstruktur für Tabelle `prefix_univ_link`
--

CREATE TABLE IF NOT EXISTS `prefix_univ_link` (
  `user` varchar(30) NOT NULL,
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`user`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_user`
--

CREATE TABLE IF NOT EXISTS `prefix_user` (
  `id` varchar(30) NOT NULL DEFAULT '',
  `staatsform` int(1) NOT NULL DEFAULT '0',
  `password` varchar(40) NOT NULL DEFAULT 'de28f02c69edd288390997757d55543e',
  `email` varchar(160) NOT NULL DEFAULT '',
  `status` varchar(10) NOT NULL DEFAULT '',
  `rules` char(1) NOT NULL DEFAULT '0',
  `logindate` int(11) NOT NULL DEFAULT '0',
  `allianz` varchar(30) NOT NULL DEFAULT '',
  `grav_von` float NOT NULL DEFAULT '0',
  `grav_bis` float NOT NULL DEFAULT '0',
  `gal_start` char(2) NOT NULL DEFAULT '0',
  `gal_end` char(2) NOT NULL DEFAULT '0',
  `sys_start` char(3) NOT NULL DEFAULT '0',
  `sys_end` char(3) NOT NULL DEFAULT '0',
  `preset` int(11) NOT NULL DEFAULT '0',
  `planibilder` char(1) NOT NULL DEFAULT '',
  `gebbilder` char(1) NOT NULL DEFAULT '',
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
  `budflesol` varchar(10) NOT NULL DEFAULT '',
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
  PRIMARY KEY (`id`),
  KEY `ikea` (`ikea`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_user_research`
--

CREATE TABLE IF NOT EXISTS `prefix_user_research` (
  `user` varchar(30) NOT NULL DEFAULT '',
  `rId` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(12) unsigned NOT NULL DEFAULT '0',
  `time` int(12) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Aktuelle Forschungen der User';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_wronglogin`
--

CREATE TABLE IF NOT EXISTS `prefix_wronglogin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(30) NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_spieler`
--

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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_spielerallychange`
--

CREATE TABLE IF NOT EXISTS `prefix_spielerallychange` (
  `name` varchar(50) NOT NULL,
  `fromally` varchar(50) NOT NULL,
  `toally` varchar(50) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;