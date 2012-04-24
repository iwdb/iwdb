-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 22. Apr 2012 um 12:07
-- Server Version: 5.5.16
-- PHP-Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Datenbank: `iwdb`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `iwdb_transport_einstellungen`
--
-- Erzeugt am: 21. Apr 2012 um 22:11
-- Aktualisiert am: 21. Apr 2012 um 22:11
--

DROP TABLE IF EXISTS `iwdb_transport_einstellungen`;
CREATE TABLE IF NOT EXISTS `iwdb_transport_einstellungen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_allianzstatus`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 22. Apr 2012 um 08:14
--

DROP TABLE IF EXISTS `prefix_allianzstatus`;
CREATE TABLE IF NOT EXISTS `prefix_allianzstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `allianz` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Status der eigenen Allianz zu anderen' AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `prefix_allianzstatus`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellen`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_bestellen`;
CREATE TABLE IF NOT EXISTS `prefix_bestellen` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(30) NOT NULL DEFAULT '',
  `coords` varchar(30) NOT NULL DEFAULT '',
  `coords2` varchar(30) NOT NULL DEFAULT '',
  `squad` varchar(10) NOT NULL DEFAULT '',
  `typ` int(1) NOT NULL DEFAULT '0',
  `schiff` varchar(20) NOT NULL DEFAULT '',
  `menge` int(3) NOT NULL DEFAULT '0',
  `bs` int(1) NOT NULL DEFAULT '0',
  `transport` int(1) NOT NULL DEFAULT '0',
  `angenommen` varchar(50) NOT NULL DEFAULT '',
  `eisen` int(11) NOT NULL DEFAULT '0',
  `stahl` int(11) NOT NULL DEFAULT '0',
  `vv4a` int(11) NOT NULL DEFAULT '0',
  `chemie` int(11) NOT NULL DEFAULT '0',
  `eis` int(11) NOT NULL DEFAULT '0',
  `wasser` int(11) NOT NULL DEFAULT '0',
  `energie` int(11) NOT NULL DEFAULT '0',
  `bevoelkerung` int(11) NOT NULL DEFAULT '0',
  `order_time` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_bestellung`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_bestellung_projekt`;
CREATE TABLE IF NOT EXISTS `prefix_bestellung_projekt` (
  `name` varchar(30) NOT NULL,
  `prio` int(11) NOT NULL,
  `schiff` int(1) NOT NULL,
  PRIMARY KEY (`name`,`schiff`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `prefix_bestellung_projekt`
--

INSERT INTO `prefix_bestellung_projekt` (`name`, `prio`, `schiff`) VALUES
('(Keins)', 999, 0),
('(Keins)', 999, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_bestellung_schiffe`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_bestellung_schiffe`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_bestellung_schiffe_pos`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_building2building`;
CREATE TABLE IF NOT EXISTS `prefix_building2building` (
  `bOld` int(10) unsigned NOT NULL DEFAULT '0',
  `bNew` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bOld`,`bNew`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gebaeude bOld ermoeglicht Gebaeude bNew';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_building2research`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_building2research`;
CREATE TABLE IF NOT EXISTS `prefix_building2research` (
  `bId` int(10) unsigned NOT NULL DEFAULT '0',
  `rId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`bId`,`rId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Gebaeude bId ermoeglicht Forschung rId';

--
-- Daten für Tabelle `prefix_building2research`
--

INSERT INTO `prefix_building2research` (`bId`, `rId`) VALUES
(41, 21),
(41, 24),
(41, 29),
(41, 34),
(41, 75),
(41, 378),
(42, 45),
(43, 164),
(56, 17),
(56, 28),
(56, 30),
(56, 65),
(56, 267),
(56, 347),
(56, 348),
(56, 352),
(89, 79);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_def`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 19:06
--

DROP TABLE IF EXISTS `prefix_def`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `prefix_def`
--

INSERT INTO `prefix_def` (`id`, `eingebaut`, `id_iw`, `name`, `abk`, `typ`, `kosten_eisen`, `kosten_stahl`, `kosten_vv4a`, `kosten_chemie`, `kosten_energie`, `dauer`, `verbrauch_chem`, `verbrauch_energie`, `angriff`, `waffenklasse`, `verteidigung`, `schilde`, `genauigkeit`, `eff_sonden`, `eff_zivile`, `eff_jaeger`, `eff_bomber`, `eff_korvetten`, `eff_zerstoerer`, `eff_kreuzer`, `eff_ss`, `eff_dn`, `eff_spezielle`) VALUES
(1, 1, 1, 'SDI Raketensystem', 'Rak', 'planetare Def', 200, 200, 0, 100, 200, 3600, 0, 0, 10, 'kinetisch', 40, 0, 65, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100),
(2, 1, 2, 'SDI Atomraketen', 'ARak', 'planetare Def', 300, 300, 0, 150, 300, 3600, 0, 0, 15, 'kinetisch', 45, 0, 80, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100),
(3, 1, 3, 'Raketensatellit', 'RakSat', 'orbitale Def', 300, 200, 0, 100, 300, 3600, 0, 0, 25, 'kinetisch', 30, 0, 60, 100, 100, 130, 130, 100, 100, 80, 50, 50, 100),
(4, 1, 4, 'Gausskanonensatellit', 'Gauss', 'orbitale Def', 700, 400, 0, 200, 400, 3600, 0, 0, 25, 'kinetisch', 135, 0, 85, 100, 100, 150, 150, 120, 100, 100, 60, 60, 100),
(5, 1, 5, 'SD01 Gatling', 'SD01', 'aktive Sondenverteidigung', 1000, 1250, 0, 1500, 1000, 7200, 5, 0, 0, 'keine', 0, 0, 0, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100),
(6, 1, 6, 'SDI Plasmalaser', 'Plasma', 'planetare Def', 3500, 1000, 400, 1000, 2000, 7200, 10, 10, 300, 'elektrisch', 750, 0, 80, 100, 100, 25, 25, 70, 100, 100, 120, 130, 100),
(7, 1, 7, 'SDI Gravitonbeam', 'Grav', 'planetare Def', 4000, 2400, 1000, 0, 10000, 10800, 25, 125, 480, 'gravimetrisch', 1200, 500, 50, 100, 100, 40, 40, 70, 90, 100, 130, 150, 100),
(8, 0, 8, 'SDI Stellarkonverter', 'Stellar', 'planetare Def', 15000, 7000, 4000, 5000, 25000, 14400, 60, 125, 2500, 'gravimetrisch', 1250, 750, 40, 100, 100, 20, 20, 50, 70, 100, 140, 150, 100),
(9, 1, 9, 'LaserSat', 'Laser', 'orbitale Def', 900, 500, 0, 500, 1000, 7200, 0, 0, 35, 'elektrisch', 80, 0, 85, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100),
(10, 1, 10, 'PulslaserSat', 'Puls', 'orbitale Def', 1300, 750, 0, 500, 1100, 7200, 10, 10, 55, 'elektrisch', 125, 0, 140, 100, 100, 150, 150, 120, 100, 80, 50, 50, 100),
(11, 0, 11, 'Fusiontorpedowerfer (Sat)', 'Fusion', 'orbitale Def', 2500, 1500, 500, 2500, 2000, 7200, 20, 20, 1050, 'kinetisch', 400, 100, 80, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100),
(12, 1, 12, 'MassdriverSat', 'Mass', 'orbitale Def', 5000, 2000, 1000, 3000, 10000, 10800, 30, 30, 450, 'kinetisch', 550, 150, 90, 100, 100, 150, 150, 130, 110, 100, 70, 50, 100),
(13, 1, 13, 'SD02 Pulslaser', 'SD02', 'aktive Sondenverteidigung', 2000, 2000, 100, 2000, 2000, 10800, 10, 5, 0, 'keine', 0, 0, 0, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100),
(15, 1, 15, 'Stopfentenwerfer', 'Stopfi', 'planetare Def', 200, 200, 0, 0, 100, 3600, 0, 0, 1, 'keine', 100, 0, 50, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_gebaeude`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 22. Apr 2012 um 10:05
--

DROP TABLE IF EXISTS `prefix_gebaeude`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Gebäudekurzform' AUTO_INCREMENT=251 ;

--
-- Daten für Tabelle `prefix_gebaeude`
--

INSERT INTO `prefix_gebaeude` (`id`, `name`, `category`, `idcat`, `inactive`, `dauer`, `bild`, `info`, `n_building`, `n_research`, `n_kolotyp`, `n_planityp`, `e_research`, `e_building`, `zerstoert`, `bringt`, `Kosten`, `Punkte`, `MaximaleAnzahl`, `typ`, `kostet`, `id_iw`) VALUES
(1, 'kleine Eisenmine', ' 5. Förderungsanlagen', 2, '', 10800, 'kl_eisenmine', 'Eine kleine Eisenmine.\r\nNett anzuschaun.\r\nEin echtes Kunstwerk.\r\nToll, nicht wahr?\r\nUnd das beste ist, sie produziert auch noch Eisen.\r\nDas ist erst toll.\r\nUnd zwar 20 Eisen / Stunde.\r\nDas ist aber jetzt wirklich echt toll.\r\nToll.\r\nSollte man bauen.\r\nKann nie schaden.\r\nJapp. ', '', '', 'Kolonie', '', '', 'kleines Stahlwerk br\r\ngroßes Stahlwerk br\r\nkleiner Stahlkomplex', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+20,00 Eisen', 'Eisen: 100 Stahl: 50 Energie: 50 Bevölkerung: 10', 6, 0, 'norma', '-1 Energie', 26),
(2, 'kleine Chemiefabrik', ' 5. Förderungsanlagen', 16, '', 10800, 'kl_chemiefabrik', 'Die kleine Chemiefabrik produziert chemische Elemente. Einen Teil davon kann man rauchen, einen Teil davon kann man benutzen um irgendwelche Wege zu teeren und gewisse andere Teile kommen bei der Herstellung von Tiefkühlpizza zum Einsatz. Das alles wird hier aber zusammengefasst zu chemischen Elementen, welche verbrannt (nat&uuml;rlich in Kraftwerken, wo denn sonst) oder als Treibstoff f&uuml;r Raumschiffe benutzt werden.', '', 'chemische Reaktionen', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+20,00 chem. Elemente', 'Eisen: 150 Stahl: 50 Energie: 50 Bevölkerung: 10', 6, 0, 'norma', '-5 Energie', 29),
(3, 'große Chemiefabrik', ' 5. Förderungsanlagen', 17, '', 28800, 'gr_chem_fabrik', 'Im großen Kochtopf blubbert die Blubbermasse massentauglich vor sich her. Jetzt noch ein bisschen Oregano, ein wenig Basilikum und das Ganze mit Sodiumbenzanol abschmecken - köstlich!', '', 'chemische Ungezieferbekämpfung', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+80,00 chem. Elemente', 'Eisen: 750 Stahl: 300 chem. Elemente: 100 Energie: 300 Bevölkerung: 5', 21, 0, 'norma', '-25 Energie', 92),
(4, 'große Eisenmine', ' 5. Förderungsanlagen', 3, '', 28800, 'gr_eisenmine', 'Der Lärm von Hunderten von Bohrern ist Ohrenbetäubend. Die Löcher, die da gebohrt werden, sind groß wie Häuser. So, aber genug vom letzten Zahnarzt-Besuch, hier soll ja was über die große Eisenmine stehen. Nunja... sie ist groß. Und da wird Eisen abgebaut. Wurde schon erwähnt, dass sie groß ist? Sehr groß?', '', 'Robotik', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+80,00 Eisen', 'Eisen: 500 Stahl: 300 chem. Elemente: 50 Energie: 300 BevÃ¶lkerung: 5', 17, 0, 'norma', '-5 Energie', 91),
(5, 'kleines Haus', ' 2. Bevölkerung', 20, '', 900, 'kl_haus', 'Dies ist ein Haus. Da passen Leute hinein. Welche sich dann da langweilen und sich dadurch vermehren. Quasi *langweil*sprutz*plopp* schon sind 3 Leute da. Je mehr Leute da sind, desto schneller geht das. Man sollte beim Bau bedenken, dass die Leute die sich vermehren auch Wasser trinken, weil sie komischerweise was gegen das Wieder-Verschwinden haben. Es gilt: 50 Leute trinken pro Stunde 1 Wasser. Da sollte man Vorsorge treffen. Zum Beispiel für diese Art von Haus mindestens +1 Wasser/Stunde bereitstellen. Siehe auch Tauchsieder.', '', '', 'Kolonie', 'Steinklumpen', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+50 Platz für Bevölkerung', ' Eisen: 50 Stahl: 5 Energie: 5', 3, 0, 'norma', '-5 Energie', 19),
(6, 'Zelt', ' 2. Bevölkerung', 10, '', 1800, 'zelt', 'Ein paar Stangen und eine Plane. Mehr ist das nicht. Leicht aufgebaut bringt es schnell echtes Camper-Feeling.', '', '', 'Kolonie', 'Steinklumpen', '', '', '', '+10 Platz für Bevölkerung, +10 Bevölkerung', 'Eisen: 2', 0, 0, 'norma', '', 18),
(7, 'mittleres Haus', ' 2. Bevölkerung', 30, '', 1800, 'mi_haus', 'Dies ist ein mittleres Haus. Da passen noch mehr Leute hinein. Welche sich dann da zwar auch langweilen, sich aber umsomehr vermehren. Weil eben mehr Platz da ist. Quasi *viel-auswahl*sprutz^2*plopp^3* schon sind 6 Leute da. Je mehr Leute da sind desto schneller geht das. Man sollte beim Bau bedenken das die Leute die sich vermehren auch Wasser trinken, weil sie komischerweise was gegen das wieder verschwinden haben. Es gilt: 50 Leute trinken pro Stunde 1 Wasser. Da sollte man Vorsorge treffen. Zum Beispiel für diese Art von Haus mindestens +3 Wasser/Stunde bereitstellen. Siehe auch Tauchsieder.', '', 'Städtebau', 'Kolonie', 'Steinklumpen', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+150 Platz für Bevölkerung', 'Eisen: 100 Stahl: 20 Energie: 40', 5, 0, 'norma', '-10 Energie', 20),
(8, 'gro&szlig;es Haus', ' 2. Bev&ouml;lkerung', 40, '', 3600, 'gr_haus', 'Dies ist ein großes Haus. Da passen super viele Leute rein. Statt langeweile, eine einzige große Hausparty. Es *sprutzt* und *ploppt* geradezu in allen Ecken des Hauses. Denn auch hier gilt, Je mehr Leute da sind desto schneller geht das. Man sollte beim Bau bedenken das die Leute die sich vermehren auch Wasser trinken, weil sie komischerweise was gegen das wieder verschwinden haben. Es gilt: 50 Leute trinken pro Stunde 1 Wasser. Da sollte man Vorsorge treffen. Zb für diese Art von Haus mindestens +10 Wasser/Stunde bereitstellen. Siehe auch Tauchsieder.', '', 'menschliches Zusammenleben', 'Kolonie', 'Steinklumpen', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+500 Platz für Bevölkerung', 'Eisen: 1.000 Stahl: 200 Wasser: 20 Energie: 400', 21, 0, 'norma', '-15 Energie', 21),
(9, 'Villenkomplex', ' 2. Bevölkerung', 50, '', 7200, 'villenkomplex', 'Als ihre Forscher verbesserte Bautechnik erforscht haben hat einer von ihnen zufällig ein kleines Modell mit ein paar kleinen Häusern einem Springbrunnen und ein paar Gardinen dran mitgebracht. So ist man auf die Idee gekommen einen Villenkomplex zu entwickeln. Da *sprutzt* und *ploppt* es zwar nicht besser als in einem großen Haus, aber die Leute sind zumindest zufriedener.', '', 'verbesserte Bautechnik', 'Kolonie', 'Steinklumpen', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+500 Platz für Bevölkerung +0.05 Zufriedenheit', 'Eisen: 2.500 Stahl: 1.000 VV4A: 100 Wasser: 100 Energie: 1.200 Credits: 500', 56, 0, 'norma', '-5 chem. Elemente, -40 Energie', 127),
(10, 'Siedlungskomplex', ' 2. Bevölkerung', 60, '', 7200, 'siedlungskomplex', 'Das nonplusultra der Wohnraumbeschaffung. Da passen echt mega viele Leute rein. Kein Wunder das hier vor lauter *sprutzen* und *ploppen* die Wände mit vv4a gedämmt werden müssen. Sonst würden sich bald die Nachbarn beschweren und vor Gericht ziehen. Nicht auszudenken, was solche Prozesse kosten könnten.... Ach ja, Wasser brauchen die Leute komischerweise immer noch. Für dieses Haus sogar 40 pro Stunde.', '', 'Irgendwer sollte mal wieder aufräumen. Ach, ich zieh einfach um. Wohin bloß?', 'Kolonie', 'Steinklumpen', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+2.000 Platz für Bevölkerung', 'Eisen: 5.000 Stahl: 1.200 VV4A: 100 Energie: 2.500', 79, 0, 'norma', '', 113),
(11, 'Taverne (Zum volltrunkenen Raumfahrer)', ' 4. Freizeit', 50, '', 3600, 'taverne', 'Ein Muss für jede Zivilisation die dem Babyzeitalter -nur-auf-den-eigenen-Planeten-beschränkt- entfliehen möchte und in die jugendliche Sauf-ära der -prä-wir-können-ins-All-fliegen- Epoche einsaufen, ähhh eintauchen möchte. Hier gibt es alles was einen auf das zukünftige Leben im All vorbereitet und das beste ist, es macht sogar noch Spaß. Wenn ihr diese Ausbildung hier schafft, seid ihr nicht nur Raumfahrer, nein ihr seid auch noch sauffest und somit jeglichem Schrecken im All gewappned.', 'Stadtzentrum', 'Raumfahrt', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+0.2 Zufriedenheit', 'Eisen: 200 Stahl: 200 chem. Elemente: 30 Energie: 400 BevÃ¶lkerung: 5', 11, 10, 'norma', '-5 chem. Elemente, -10 Energie', 95),
(12, 'Park', ' 4. Freizeit', 30, '', 3600, 'park', 'Eine wundervolle Idylle ist dieser Park, die Vögel zwitschern lieblich und das Sonnenlicht spiegelt sich romantisch in dem achtlos weggeworfenen Müll wieder, was ein ganz besonderes Licht auf die zerbrochenden Bänke, die Grafittis und die verkohlten Stämme wirft. Ach *schmacht* ist das was ganz besonderes sich hier zu erholen?', '', 'Städtebau', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+0.1 Zufriedenheit', 'Eisen: 100 Stahl: 100 Wasser: 200 Energie: 10 BevÃ¶lkerung: 1', 14, 10, 'norma', '-5 Energie', 24),
(13, 'kleines Stahlwerk', ' 5a. Wandlungsgebs', 5, '', 10800, 'kl_stahlwerk', 'Stahlwerk, klein, oliv, eckig, produziert Stahl, verbraucht Eisen und Energie wenn es Stahl produziert, stufenlose Einstellung der produzierten Stahlmenge einstellbar unter Umwandlung, wichtiges Gebäude, Voraussetzung für ein Forschungslabor, wegtreten.', 'kleine Eisenmine', '', 'Kolonie', '', '', 'kleines Forschungslabor br\r\nVV4A Walzwerk br\r\ngroßes Forschungslabor br\r\ngroßes VV4A-Walzwerk br\r\nArea 42 (unterirdischer Forschungskomplex)', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+20,00 Produktionskapazität für Stahl', 'Eisen: 150 Stahl: 50 Energie: 50 Bevölkerung: 10', 6, 0, 'norma', '', 27),
(14, 'großes Stahlwerk', ' 5a. Wandlungsgebs', 6, '', 28800, 'gr_stahlwerk', 'Ist an sich das gleiche, wie ein kleines Stahlwerk. Allerdings größer. Und besser. Und produziert mehr Stahl. Und überhaupt. Hört sich eh viel besser an als kleines Stahlwerk.', 'kleine Eisenmine', 'VV4A', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+80,00 Produktionskapazität für Stahl', 'Eisen: 750 Stahl: 750 VV4A: 20 chem. Elemente: 50 Energie: 300 Bevölkerung: 5', 30, 0, 'norma', '', 110),
(15, 'Tauchsieder MkIV', ' 5a. Wandlungsgebs', 26, '', 14400, 'tauchsieder_mkiv', 'Ein Tauchsieder ist in der Lage 20 Einheiten schmutziges dreckiges unreines Eis in 10 Einheiten kristallklares sauberes Wasser pro Stunde umzuwandeln. Verbruzzelt dabei Strom das gute Ding. Und man kann (und muss) einstellen, wieviel Eis in Wasser umgewandelt wird. Siehe auch Umwandlung. Achja falls kein Eis da ist, kann auch kein Wasser produziert werden. PS: Die Entwicklungsstufen 1-3 hatten einen kleinen Fehler, welcher aus Stahl Wasser produzierte. Da dies natürlich nicht sein kann, wurde dann zum Glück die Version 4 eingeführt.', '', '', 'Kolonie', '', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+10,00 Produktionskapazität für Wasser', 'Eisen: 250 Stahl: 100 Eis: 10 Energie: 50', 9, 0, 'norma', '', 31),
(16, 'Design Eiscrusher der Sirius Corp', ' 5. Förderungsanlagen', 21, '', 14400, 'design_eiscrusher', 'Der Eiscrusher ist eine gewaltige Maschine, die das Eis aus dem Planeteninneren hervorholt und handlich aufbereitet. Tjo, produziert eben Eis das Ding, was soll''s sonst machen ? Kaffee kochen ? Pinguine produzieren ? Haha.', '', '', 'Kolonie', '', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+17,74 Eis', 'Eisen: 150 Stahl: 150 Energie: 150', 9, 0, 'norma', '-1 Energie', 30),
(17, 'Eiscrusher der Sirius Corp, Typ Glace la mine', ' 5. Förderungsanlagen', 22, '', 28800, 'gr_eiscrusher', 'Er ist groß. Er ist mächtig. Er ist von der Sirius Corp. Und er buddelt Eis ! Was will man von einem Gebäude eigentlich noch mehr?', '', 'VV4A', 'Kolonie', '', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+70,95 Eis', '	Eisen: 750 Stahl: 750 chem. Elemente: 100 Energie: 750', 30, 0, 'norma', '-5 Energie', 93),
(18, 'Wasserwerk', ' 5a. Wandlungsgebs', 27, '', 28800, 'wasserwerk', 'Auf der letzten 11 Tägigen Tagung (besser unter dem Titel "selbstklebende Teelichter - Die Zukunft ? Eine kurze Analayse" bekannt) unserer Forscher wurde festgestellt, dass, wenn man vier Tauchsieder nebeinander stellt und das ganze mit etwas Klebeband und Kaugummi zusammenpappt, das Ganze nur noch 8 Stunden zum Aufbau braucht, statt wie bisher 16. Da unsere Forscher jedoch so von dieser Zeitdilatation fasziniert waren, haben sie vergessen das Patent darauf anzumelden. Und jetzt hat das die Sirius Corporation. Und deswegen ist das Ding auch teurer als vier Tauchsieder. Tja, Pech gehabt.', '', 'VV4A', 'Kolonie', '', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+40,00 Produktionskapazität für Wasser', 'Eisen: 1.250 Stahl: 500 VV4A: 50 Eis: 50 Energie: 500', 32, 0, 'norma', '', 111),
(19, 'VV4A Walzwerk', ' 5a. Wandlungsgebs', 11, '', 10800, 'vv4a_werk', 'Sie wissen nicht, was sie mit den vielen Bewohnern machen sollen die sinnlos auf ihrem Planeten herumlungern? Es stört sie, dass die auch noch Geld dafür verlangen nichts zu tun? Sie haben zu viel Stahl und Energie? Dann ist dieses Gebäude genau das Richtige für sie! Beschäftigt 100 Leute. Vebraucht Stahl und Energie. Ach ja, nebenbei wird dabei auch noch VV4A produziert. Weiß zwar keiner was das ist, aber irgendwas werden sie damit schon anzufangen wissen.', 'kleines Stahlwerk', 'VV4A', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+20,00 Produktionskapazität für VV4A', 'Eisen: 3.000 Stahl: 1.200 chem. Elemente: 500 Wasser: 100 Energie: 1.000 Bevölkerung: 100', 64, 0, 'norma', '', 28),
(20, 'großes VV4A-Walzwerk', ' 5a. Wandlungsgebs', 12, '', 28800, 'vv4a_werk_gross', 'Waffeln, wir brauchen mehr Waffeln! Viel mehr sogar! Zum Glück haben die Forscher, in einem Anfall von Heißhunger auf Süßigkeiten, eine viel größere Waffelpresse erfunden. Die hab ich ihnen dann weggenommen um selbst Waffeln zu haben. Und sie beauftragt, lieber Stahl zu pressen als Waffelteig.', 'kleines Stahlwerk', 'Fusionstechnologie', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+80,00 Produktionskapazität für VV4A ', 'Eisen: 15.000 Stahl: 6.000 chem. Elemente: 2.500 Wasser: 500 Energie: 5.000 Bevölkerung: 200', 252, 0, '', '', 151),
(21, 'Solarplatten', ' 5b. Eneprodde', 41, '', 3600, 'solarplatten', 'Eine Solarplatte. Praktisch, quadratisch, gut. Und produziert auch noch Strom.', '', '', 'Kolonie', '', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+20,00 Energie ', '', 4, 0, '', '', 32),
(22, 'Kraftwerk (Solar)', ' 5b. Eneprodde', 44, '', 10800, 'kw_solar', 'Die große Ausführung der Solarplatten. Das Ding produziert eindeutig mehr Energie und ist auch sehr effektiv darin. Das beste ist allerdings, dass sich keiner aufregt, wenn so ein Ding irgendwo aufgebaut wird.', '', 'Verbesserte Nutzung der Solarenergie', 'Kolonie', '', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+50,00 Energie', 'Eisen: 500 Stahl: 600 chem. Elemente: 500 Bevölkerung: 5', 28, 0, 'norma', '', 33),
(23, 'Kraftwerk (Solar) (orbital)', ' 5b. Eneprodde', 45, '', 86400, 'kw_orb_sol', 'Der Kompromiss in Sachen Ozonloch. Es sitzt, passt, wackelt und hat Luft, und erfüllt seinen Job sogar noch besser. Und nebenbei bekommen wir sogar noch bißerl Energie dafür und die ökos sind auch zufrieden.', 'Kontrollzentrum mit Startrampe', 'Mikrowellen', 'Kolonie', '', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+1.200,00 Energie', 'Eisen: 10.000 Stahl: 5.000 chem. Elemente: 2.000', 188, 5, 'norma', '', 36),
(24, 'Kraftwerk (Atom)', ' 5b. Eneprodde', 43, '', 43200, 'kw_atom', 'Das Atomkraftwerk, ein Wunder der modernen Technik. Es ist sauber (solange niemand den Müll raus bringt) effektiv (solange es geht) und ab und zu liefert es ein schönes Feuerwerk mit strahlenden Leuten. Komischerweise sind die Leute dagegen so ein Ding zu haben. Wirklich sehr komisch.', '', 'Atomkraft', 'Kolonie', '', '', 'Grabbeltisch mit radioaktiven Müll br\r\nGrabbeltisch mit radioaktiven Blob', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+400,00 Energie -0.2 Zufriedenheit', 'Eisen: 1.050 Stahl: 750 chem. Elemente: 200 Bevölkerung: 50', 34, 0, 'norma', '-20 chem. Elemente, -5 Wasser', 35),
(26, 'Kraftwerk (Brennstoffzellen)', ' 5b. Eneprodde', 46, '', 36000, 'brennstoffzelle', 'Brennstoffzelle. Klein. Handlich. Umweltfreundlich. Und vor allem: Sie produziert Energie. Außerdem kriegt jeder Mitarbeiter Gratisgetränke. Wenn das nicht mal ein Grund ist dieses Teil zu bauen. Also ich würde es machen. Aber ich will dir da nicht reinreden...', '', 'alternative Energiekonzepte', 'Kolonie', '', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+950,00 Energie, +5 Wasser', '	Eisen: 15.000 Stahl: 6.500 VV4A: 500 chem. Elemente: 5.000 Bevölkerung: 250', 278, 0, 'norma', '-150 chem. Elemente', 131),
(27, 'Kraftwerk (Fusionsreaktor)', ' 5b. Eneprodde', 47, '', 43200, 'fusikw', 'Laut unseren Wissenschaftlern lässt der Fusionsreaktor Atomkerne verschmelzen und gewinnt damit Energie, ungefähr so wie unsere Sonne im Kleinformat! Ach ja, das Ding schont die Ressourcen und die Umwelt, sagten sie, diese Wissenschaftler... Was sie aber nicht gesagt haben, dass sie das Ding an das andere Stadtende bauen und die ganze Belegschaft mit dem Auto zur Arbeit fährt. Auf Firmenkosten! Unerhört dieser Spritverbrauch!', '', 'Fusionstechnologie', 'Kolonie', '', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+6.500,00 Energie', 'Eisen: 100.000 Stahl: 50.000 VV4A: 25.000 chem. Elemente: 50.000 Energie: 50.000 Credits: 50.000 Bevölkerung: 1.000', 2133, 0, 'norma', '-1000 chem. Elemente', 159),
(28, '24h Pizzadienst', ' 4. Freizeit', 70, '', 3600, '24pizza', 'Schon wieder nichts zu essen daheim? Wieder mal zu faul gewesen, einkaufen zu gehen und den Tag lieber mit wichtigeren Dingen wie Pinguinweitwurf zugebracht? Zum Glück gibt es den guten alten Pizza-Lieferservice. Wo hab ich bloß die Nummer nur...', '', '24h Pizzadienst', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+0.5 Zufriedenheit', 'Eisen: 2.400 Stahl: 3.950 VV4A: 200 chem. Elemente: 5.000 Eis: 500 Wasser: 500 Energie: 5.000 Credits: 500 Bevölkerung: 25', 0, 10, 'norma', '-100 chem. Elemente, -165 Energie', 156),
(29, 'Waffelstand', ' 4. Freizeit', 40, '', 3600, 'waffelstand', 'Endlich haben die Forscher die Lösung auf die Frage gefunden, wozu Studenten gut sind: Zum Waffeln verkaufen. Um allerdings die Studenten an den Waffelstand zu kriegen muss zunächst aus Chemie und Wasser Bier gebraut werden. So sind die Studenten glücklich, die Bürger glücklich und wir um etwas Wasser, Chemie und Energie ärmer. Aber das ist ein kleiner Preis dafür, die Zufriedenheit in den kleinen Gesichtchen der waffelmampfenden Bürger zu sehen. Sagt zumindest meine Mama. Und Mama hat immer Recht.', '', 'perfekte Waffeln dank Waffeleisen', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+0.3 Zufriedenheit', 'Eisen: 100 Stahl: 500 chem. Elemente: 100 Wasser: 10 Energie: 250 Bevölkerung: 1', 18, 10, 'norma', '-5 chem. Elemente, -2 Wasser, -5 Energie', 25),
(30, 'Chemielager', ' 6. Lager & Bunker', 10, '', 10800, 'chem_lager', 'Das Chemielager lagert chemische Elemente. Mehr nicht. Aber das kann es sehr gut.', '', 'Lagerbau', '', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', 'Lagerplatz für chem. Elemente', 'Stufengebäude', 0, 0, '', '', 118),
(31, 'Teenie Disko', ' 4. Freizeit', 60, '', 3600, 'teeniedisse', 'Dr. Sommer wurde gefragt und was kam dabei raus? Die Leute wollen Spannung, Spiel, Spaß und ne Gehaltserhöhung. Stattdessen haben sie diese formschöne Teenie Disko gekriegt. Sieht toll aus mit den ganzen funkelnden Lichtern und der lauten Musik. Außerdem macht sie die Leute glücklich. Was will man eigentlich mehr? Okay, ich weiß, Spannung, Spiel, Spaß und ne Gehaltserhöhung. Aber wenn man das nicht kriegt ist man auch mit ner Teenie Disko zufrieden.', '', 'Fragen an Dr. Sommer', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+0.4 Zufriedenheit', 'Eisen: 1.000 Stahl: 1.800 chem. Elemente: 500 Energie: 2.200 Credits: 1.000 Bevölkerung: 10', 55, 10, 'norma', '-50 chem. Elemente, -50 Energie', 112),
(33, 'kleiner chemischer Fabrikkomplex', ' 5. Förderungsanlagen', 18, '', 36000, 'chem_komplex', 'Die Jungs im Labor haben eine Pflanze gefunden, die tolle Eigenschaften hat: Man muss nur einen Zapfhahn dran befestigen und aufdrehen, schon fließt die Blubbermasse in Strömen. Leider müssen wir dafür viel Geld für Schutzzäune und Wachen ausgeben, um uns die Pflanzenschützer vom Hals zu halten.', '', 'Wir brauchen extra Käse', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+160 Chemie', 'Eisen: 5.000 Stahl: 3.000 VV4A: 1.500 chem. Elemente: 1.000 Energie: 7.500 Bevölkerung: 25', 159, 0, '', '-50 Energie', 161),
(35, 'Mondbergwerk', ' 5. Förderungsanlagen', 31, '', 10800, 'mondbergwerk', 'Der Mond. "Ein kleiner Schritt für mich, ein großer Schritt für das Hasiversum" haben sie gesagt. Und nun? Ein Krater hier, ein Krater da, alles voller Staub... Laangweilig! Vielleicht ist es im Mondinnern ja interessanter. Ich bin mal kurz graben...', 'Kontrollzentrum mit Startrampe br\r\nVoraussetzungen Planeteneigenschaften Mond', 'extraterrestrischer Bergbau', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+40,00 Eisen, +40,00 chem. Elemente, +22,38 Eis', 'Eisen: 1.500 Stahl: 3.000 chem. Elemente: 500 Energie: 1.000 Bevölkerung: 50', 80, 10, 'norma', '-20 Energie', 94),
(36, 'orbitales Habitat', ' 2. Bevölkerung', 70, '', 3600, 'orb_habitat', '', 'Kontrollzentrum mit Startrampe', 'Wie benutze ich eine Toilette bei Schwerelosigkeit?', 'Kolonie', 'Steinklumpen', '', '', 'Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+5.000 Platz für Bevölkerung ', 'Eisen: 5.000 Stahl: 1.000 Energie: 5.000', 73, 0, '', '', 180),
(37, 'kleines Forschungslabor', ' 3. Forschung', 20, '', 23040, 'kl_forschlab', 'Ein Forschungslabor.<br>Man stelle sich einen Keller mit dicken Mauern vor, wo viele Wissenschaftler auf kleinen Raum gefangen *ähm* freiwillig untergebracht sind und durch einen kleinen Schlitz unter der Tür mit Pizza versorgt werden.<br>Dort erfinden sie dann die (un-)nützlichsten Sachen die man garantiert bis dahin noch nie gebraucht hat. Meistens nachher auch nicht. Aber sicherheitshalber werden sie erfunden.<br><br>Eine weitere Eigenschaft das Forschungslabors ist es, das viele Köche den Brei verderben, da man nicht unendlich Leute einsperren *hust* zusammenbringen kann, ohne das die sich auf die Füße treten und dann anfangen Unsinn zu machen, wie etwa Tetris spielen oder im Internet gewissen Seiten suchen, anstatt ordentlich mit vollem Elan zu arbeiten.', 'kleines Stahlwerk', '', 'Kolonie', '', '', 'Universität', 'fremde Spielerinteraktion (Bomben, etc) br\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+32,04 Forschungspunkte', '', 22, 0, 'globa', '-20 Energie', 14),
(38, 'großes Forschungslabor', ' 3. Forschung', 40, '', 21600, 'forschungskomplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 40),
(39, 'orbitaler Forschungskomplex', ' 3. Forschung', 45, '', 21600, 'orb_lab', 'orb. Forschungskomplex: Endlich ist jemand auf die kluge Idee gekommen unsere Forscher in den Weltraum zu schicken. Dies hat zwei große Vorteile. Erstens können die Forscher nun herumschweben und sich den Planeten von oben anschauen, was sie sehr glücklich und zufrieden macht. Und zweitens nervt nicht mehr jede Woche ein entlaufener Forscher unsere Kolonisten. Ach ja, außerdem kann hier im Weltall auch viel mehr und toller geforscht werden. Deswegen bringt das orbitale Forschungslabor auch 100 FP. Doch auch hier gilt: Aber einer gewissen Menge werden die Labs teurer. Abhilfe schafft die Erforschung neuer Labortypen.', 'Kontrollzentrum mit Startrampe', 'Zero G Forschung', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+142,24 Forschungspunkte', '', 428, 0, 'globa', '-15 chem. Elemente, -150 Energie', 155),
(40, 'Area 42 (unterirdischer Forschungskomplex)', ' 3. Forschung', 60, '', 21600, 'u_forschkomplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 173),
(41, 'Universität', ' 3. Forschung', 10, '', 36000, 'uni', '<br>Die Universität. Ein Hort des Wissens über geile Studentenpartys, unzähliger Langzeitstudenten die auf vorhergenannten Partys rumhängen, Massen von Profs die nur ihr eigenes Zeug machen, eine ständig überfüllte Mensa und...mhh viel gibt es dann nicht mehr, achja ab und zu wird noch was Wichtiges erfunden. Ab und zu zwischen 2 Partys (meistens auch auf) wird tatsächlich was erfunden. Zumindest werden ein paar wichtige lebensphilosophische Fragen diskutiert. Jeder sollte eine Universität haben, schon alleine wegen der Partys.', '(kleines Forschungslabor)', '(Computer)<br>', 'Kolonie<br>', '', '(Was mache ich heute Abend?) (Internet) (Wo finde ich die besten Pornoseiten?) (Wo bekomme ich was zu essen her?) (Was zum Teufel ist das hier auf meinem Teller?)<br>', '(planetarer Supercomputer)<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+8\\% Forschung', '-100 Energie', 96, 1, 'norma', '', 13),
(42, 'planetarer Supercomputer', ' 3. Forschung', 30, '', 72000, 'supercomp', 'Dank des Internets, können wir nun mit jedem auf dem Planeten jederzeit in Kontakt treten. Leider besteht das ganze noch nur aus kleinen instabilen Servern, weswegen unsere Techniker dieses Schmuckstück entworfen haben. Der planetare Supercomputer ist in der Lage, den gesamten digitalen Verkehr des Planeten zu verwalten. Und vor allem unsere Wissenschaftler dürften von ihm begeistert sein, da sie nun zumindest Kontakt nach außen zu anderen Forschungslaboren erhalten.', 'Universität', 'Internet', 'Kolonie', '', 'semiintelligente KI Technik', '', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+13% Forschung ', 'Eisen: 10.000 Stahl: 10.000 chem. Elemente: 5.000 Wasser: 1.000 Energie: 15.000 Credits: 500 BevÃ¶lkerung: 500', 297, 1, 'norma', '-200 Energie', 17),
(43, 'Quantencomputer', ' 3. Forschung', 50, '', 86400, 'quanten_comp', '', '', 'totale Wissensvernetzung durch bessere Waffentechnologie', 'Kolonie', '', 'Verstehen der Zusammenhänge', '', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+18% Forschung', '', 18956, 1, 'norma', '-1600 chem. Elemente, -200 Wasser, -8900 Energie', 172),
(44, 'Interstellarer Bibokomplex', ' 3. Forschung', 70, '', 86400, 'inter_bibo', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 130),
(45, 'großes Chemielager (Kampfbasis)', ' 6. Lager & Bunker', 40, '', 10800, 'chemlager_gross', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 165),
(46, 'Eis & Wasserlager', ' 6. Lager & Bunker', 20, '', 10800, 'eis_wass_lager', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 119),
(47, 'Energielager', ' 6. Lager & Bunker', 30, '', 10800, 'energie_lager', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 120),
(48, 'großes Energielager (Kampfbasis)', ' 6. Lager & Bunker', 50, '', 10800, 'energielager_gross', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 166),
(49, 'Eisenbunker', ' 6. Lager & Bunker', 110, '', 10800, 'eisenbunker', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 121),
(50, 'Stahlbunker', ' 6. Lager & Bunker', 120, '', 10800, 'stahlbunker', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 137),
(51, 'VV4A-Bunker', ' 6. Lager & Bunker', 130, '', 10800, 'vv4a_bunker', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 138),
(52, 'Chemiebunker', ' 6. Lager & Bunker', 140, '', 10800, 'chemiebunker', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 122),
(53, 'Eis &amp; Wasserbunker', ' 6. Lager &amp; Bunker', 150, '', 10800, 'eis_wass_bunker', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 123),
(54, 'Energiebunker', ' 6. Lager & Bunker', 160, '', 10800, 'energiebunker', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 124),
(55, 'Bev&ouml;lkerungsbunker', ' 6. Lager & Bunker', 170, '', 10800, 'bevbunker', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 167),
(56, 'Kontrollzentrum mit Startrampe', ' 7. Raumfahrt', 10, '', 36000, 'kontrollzentrum_mit_startrampe', 'Vorgestern ist mein Wasserboiler im Keller explodiert, man war das ein Bumm! Wo das Ding hingeflogen ist, weiß ich nicht, eigentlich ist mir das auch ziemlich egal weil es regnet und ich ein großes Loch im Dach habe. Vorhin hat mich dann aber einer dieser Heinis angerufen, die in der Sternwarte arbeiten. Sie meinten ein Objekt, dass die Form eines Wasserboilers hätte, umkreist unseren Planeten. Zumindest haben wir damit zwei Sachen rausgefunden! Erstens: Den Wasserboiler im Keller niemals als Dampfkochtopf benutzen! Zweitens: Wie man mehr oder weniger sinnvolle Dinge in den Orbit schießt!', '', 'Raumfahrt', 'Kolonie', '', 'extraterrestrischer Bergbau\r\nextraterrestrisches Siedeln', 'orbitales Teleskop\r\nkleine planetare Werft\r\nFlottenkontrollzentrum\r\nMondbergwerk\r\nFlottenscanner', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', 'ermöglicht den Bau orbitaler Gebäude, +10 SolSichtweite', '', 158, 1, 'norma', '-200 Energie', 15),
(57, 'Flottenkontrollzentrum', ' 7. Raumfahrt', 60, '', 28800, 'fl_kontr_zentr', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 54),
(58, 'kleine planetare Werft', ' 7. Raumfahrt', 20, '', 28800, 'kl_plan_werft', 'Eine Schraube hier, eine Feder dort, dann noch mal mit Klebeband die Lecks abdichten - und fertig ist das Raumschiff. Sieht nicht toll aus, wackelt ein wenig, aber was Besseres gibt&#039;s halt nicht mit Second-Hand-Teilen und Klebeband. Mit Betonung auf Letzterem. Eigentlich besteht es ja nur noch aus Klebeband... aber es scheint zu halten und die sollen sich nicht beschweren, die Piloten. Das passt schon. Blöd nur, dass das Ding am Boden ist, mit all&#039; der blöden Schwerkraft. Da muss man halt auch noch in eine große Schleuder investieren, um das Meisterwerk in den Weltraum zu schießen. Vielleicht hätte man doch die Werft gleich dort bauen sollen...', 'Kontrollzentrum mit Startrampe', 'Raumfahrt', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+1 kleine planetare Werft', '', 106, 20, 'norma', '-40 Energie', 41),
(59, 'kleine orbitale Werft', ' 7. Raumfahrt', 30, '', 36000, 'kl_orb_werft', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 42),
(60, 'mittlere planetare Werft', ' 7. Raumfahrt', 50, '', 28800, 'mitt_plan_werft', '', 'Kontrollzentrum mit Startrampe', 'große Schiffsrümpfe', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+1 mittlere planetare Werft', '', 348, 10, 'norma', '-20 chem. Elemente, -100 Energie', 43),
(61, 'mittlere orbitale Werft', ' 7. Raumfahrt', 70, '', 54000, 'mitt_orb_werft', '', 'Kontrollzentrum mit Startrampe', 'Miniaturisierung', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+1 mittlere orbitale Werft', '', 684, 10, 'norma', '-50 chem. Elemente, -150 Energie', 115),
(62, 'große Werft', ' 7. Raumfahrt', 80, '', 86400, 'gr_werft', '<br>', '(Kontrollzentrum mit Startrampe)', '(sehr große Schiffsrümpfe)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+1 grosse orbitale Werft', '-280 chem. Elemente, -750 Energie', 1906, 5, 'norma', '', 146),
(64, 'kleiner Werftkomplex', ' 7. Raumfahrt', 100, '', 86400, 'orb_werftkomplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 147),
(65, 'Sternwarte', ' 1. Beobachtung', 10, '', 14400, 'sternenwarte', '<br>Die Sternwarte. Hier sitzen in der Nacht jede Menge von Leuten und suchen das All nach Außerirdischen ab, die dann vorbeikommen und wilde sexy Sachen *ähm* ja. Jedenfalls Leute *ähm* ich muss mal kurz *hust* weg. Zur Sternenwarte. Komme wirklich gleich wieder. Wirklich.', '', '(Astronomie)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+4 Sichtweite in der selben Galaxie', '-15 Energie', 0, 10, 'norma', '', 37),
(66, 'Flottenscanner', ' 1. Beobachtung', 60, '', 21600, 'flottenscanner', '<br>Was macht ein Flottenscanner? Hängt im Orbit rum und scannt. Und was scannt er? Flotten (sagt ja schon der Name, oder?). Ihr wollt noch mehr wissen? Gut. Das Ding ist 40 mal 100 Meter lang, hat eine Höhe von 10 Metern und innen drin sitzen 50 Leute und starren den ganzen Tag auf grünlich schimmernde Monitore und warten darauf, dass irgendwas passiert. Und wenn was passiert dann laufen sie schreiend durch die Station. Sieht ziemlich lustig aus.', '(Kontrollzentrum mit Startrampe)', '(Raumfahrt)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+ 01:00:00 Flottenerkennung', '-20 chem. Elemente, -25 Energie', 65, 30, 'norma', '', 143),
(67, 'orbitales Teleskop', ' 1. Beobachtung', 20, '', 14400, 'orb_teleskop', 'Sieht aus wie ein großes Rohr mit zwei Flügeln dran. Ist aber eigentlich was gaaanz nützliches. Mit dem Ding kann man nämlich die Nachbarn noch besser beobachten als mit der Sternwarte. Und dank dem Internet kann man das ganze live... aber lassen wir das.', 'Kontrollzentrum mit Startrampe', 'visuelle Optik', 'Kolonie', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)<br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+5 Sichtweite in der selben Galaxie', 'Eisen: 1.500 Stahl: 750 chem. Elemente: 650 Wasser: 10 Energie: 500', 42, 10, 'norma', '-15 chem. Elemente', 38),
(68, 'orbitales Langstreckenteleskop', ' 1. Beobachtung', 30, '', 14400, 'orb_lang_teleskop', '<br>Nun ja, war ja nur eine Frage der Zeit bis sich irgendein Freak *ähh* Wissenschaftler daran macht das orbitale Teleskop zu tunen. Es ist jetzt tiefer gelegt, hat mehr Flügel und ein längeres Rohr. Außerdem frisst es mehr Sprit. Aber na ja, zumindest können wir damit jetzt noch weiter sehen. Das ist doch schonmal was.', '(Kontrollzentrum mit Startrampe)', '(Hyperraumscanner)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+9 Sichtweite in der selben Galaxie', '-25 chem. Elemente', 36, 10, 'norma', '', 39),
(69, 'orbitales Tiefenraumteleskop', ' 1. Beobachtung', 40, '', 28800, 'orb_tiefentele', '<br>', '(Kontrollzentrum mit Startrampe)', '(Verbesserte Beobachtung)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+13 Sichtweite in der selben Galaxie', '-50 chem. Elemente', 99, 10, 'norma', '', 116),
(70, 'orbitaler Galaxienscanner', ' 1. Beobachtung', 50, '', 43200, 'orb_gal_scanner', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 117),
(71, 'planetare Verteidigungsstellung', ' 9. Verteidigung', 10, '', 27000, 'plan_vertei_stellung', '<br>', '', '(militärische Grundlagen)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+10 planetare Verteidigungsplätze', '-30 Energie', 29, 100, 'norma', '', 89),
(72, 'orbitale Verteidigungskoordination', ' 9. Verteidigung', 20, '', 27000, 'orb_vertei_koordi', '<br>', '(Kontrollzentrum mit Startrampe)', '(orbitale Verteidigung)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+10 orbitale Verteidigungsplätze', '-20 chem. Elemente', 39, 100, 'norma', '', 90),
(73, 'Panzerungsupdate Planetar', ' 9. Verteidigung', 30, '', 43200, 'panzer_update_plan', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 144),
(74, 'Panzerungsupdate Orbital', ' 9. Verteidigung', 40, '', 43200, 'panzer_update_orb', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 145),
(75, 'lunare Verteidigungsstation', ' 9. Verteidigung', 60, '', 36000, 'mond_defence', '<br>Dies ist ein Testgebäude. Wir werden ihnen jetzt ein paar einfache Aufgaben stellen. Wenn sie diese gut genug erfüllen gibt es ein kleines Geschenk. Frage 1: Hat ihr Planet einen Mond? Frage 2: Brauchen sie mehr Verteidigungsplätze? Frage 3: Brauchen sie SEHR VIEL mehr Verteidigungsplätze? Frage 4: Sie sind sich also sicher das sie mehr Verteidigungsplätze brauchen? Wenn sie alle diese Fragen mit ja beantwortet haben, sollte sie als letztes noch dieses Gebäude bauen. Wenn sie das alles gemacht haben, belohnen wir sie dafür mit 100 orbitalen und 100 planetaren Verteidigungsplätzen. Sind wir nicht großzügig?', '(Kontrollzentrum mit Startrampe)<br><br>Voraussetzungen Planeteneigenschaften Mond', '(Neue Verbundstoffe)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', 'Bringt 100 orbitale und 100 planetare Verteidigungspositionen', 'Eisen: 5.000 Stahl: 5.000 chem. Elemente: 12.000 Energie: 5.000 Bevölkerung: 130', 210, 1, 'norma', '', 169),
(76, 'planetares Alpha Schild', ' 9. Verteidigung', 50, '', 259200, 'plan_alphaschild', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 162),
(77, 'Abbaugerät Eisen', ' 5. Förderungsanlagen', 90, '', 43200, 'abbau_eisen', '<br>', '', '(Robotermining)<br>', 'Sammelbasis<br>', '', '<br>', '<br>', '<br>', '+ Eisen, +50 chem. Elemente, +15 Eis, 0 \\% Abbau Dichte Eisen pro Tag', ': 5.000 : 5.000 : 1.000 : 5.000 : 5.000', 0, 1, 'norma', '', 133),
(78, 'Abbaugerät chem. Elemente', ' 5. Förderungsanlagen', 91, '', 43200, 'abbau_chemie', '<br>', '', '(Robotermining)<br>', 'Sammelbasis<br>', '', '<br>', '<br>', '<br>', '+ chem. Elemente, +50 Eisen, +15 Eis, 0 \\% Abbau Dichte chem. Elemente pro Tag', ': 5.000 : 5.000 : 1.000 : 5.000 : 5.000', 0, 1, 'norma', '', 134),
(79, 'Abbaugerät Eis', ' 5. Förderungsanlagen', 92, '', 43200, 'abbau_eis', '<br>', '', '(Robotermining)<br>', 'Sammelbasis<br>', '', '<br>', '<br>', '<br>', '+ Eis, +50 Eisen, +50 chem. Elemente, 0 \\% Abbau Dichte Eis pro Tag', ': 10.000 : 10.000 : 2.500 : 10.000 : 10.000', 0, 1, 'norma', '', 135),
(80, 'Bevölkerungszentrum', '10. Wirtschaft & Verwaltung', 1, '', 54000, 'bev_zentrum', '<br>Das Bevölkerungszentrum ist ein Versammlungsort für Menschen die zuviel Geld haben und sich blödsinnige Sachen kaufen wollen. Hier findet man alles was das Herz begehrt und garantiert unnütz ist. Kitsch in Reinform. Was für ein Paradies. (Nur inklusive nervender Zeitungsaboverkäufer und Garantiert-Gratis-Proben Verteiler.)', '(Stadtzentrum)', '(menschliches Zusammenleben)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+50\\% Steuereinkommen', 'Eisen: 5.000 Stahl: 1.000 Wasser: 500 Energie: 1.000 Credits: 500', 84, 1, 'norma', '', 23),
(81, 'Stadtzentrum', ' 4. Freizeit', 10, '', 28800, 'stadtzentrum', '<br>Ein Ort der Idylle, der Ruhe und der inneren Besinnung auf die gesellschaftlichen Werte des Friedens, der Barmhertzigkeit und der Nächstenliebe. Dies wird durch den 3 Meter hohen Zaun mit Stacheldraht, den 2 Selbstschussanlagen, einem kleinen Minenfeld und dem privaten Wachdienst gewährleistet. Gut, dass es solche Orte noch gibt.', '', '(Stadtzentren)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+0.5 Zufriedenheit', 'Eisen: 2000 Stahl: 250 Wasser: 20 Energie: 500', 0, 1, 'norma', '', 22),
(82, 'SpacePort', '10. Wirtschaft & Verwaltung', 5, '', 64800, 'spaceport', '<br>Der SpacePort, unendliche Weiten! Eigentlich isses ja nur eine Haltestelle für den interplanetaren Bummelbus, aber &quot;SpacePort&quot; klingt nun mal viel cooler - weil Englisch klingt immer viel besser als Moepisch. Die Haltestelle wird exklusiv gebaut inklusive einem Rasta-Hippie, der mit seinem Hund und seiner Gitarre rumhängt und in der Zeit bis zur Ankunft des Busses den Leuten das Geld aus der Tasche zieht. Das wiederum bringt Steuereinnahmen, denn von der Weisheit des Hasengottes erfüllt, haben Sie als gütiger Herrscher natürlich eine Rasta-Hippie-mit-Hund-an-Haltestellen-Steuer erfunden. Und da sage noch einer, die seien zu nichts gut!', '', '(Handel)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+75\\% Steuereinkommen', '-100 Energie', 122, 1, 'norma', '', 55),
(83, 'Hauptquartier', '10. Wirtschaft & Verwaltung', 6, '', 7200, 'hauptquartier', '<br>', '', '(Interstellares Vordringen)<br>', 'Kolonie<br>', '', '<br>', '(Bauarbeitersiedlung aus Containern und einem Plumpsklo) (Hilfskiste Eisen auspacken) (Hilfskiste Stahl auspacken) (Hilfskiste chem. Elemente auspacken) (Hilfskiste Eis auspacken) (Hilfskiste Wasser auspacken) (Hilfskiste Energie auspacken) (Hilfskiste Settlers Delight auspacken) (Hilfskiste Scout auspacken)<br>', '<br>', 'bestimmt diesen Planeten zum neuen Hauptplaneten', 'Eisen: 20.000 Stahl: 20.000 VV4A: 20.000 chem. Elemente: 20.000 Eis: 20.000 Wasser: 20.000 Energie: 20.000 Credits: 20.000', 0, 1, 'norma', '', 61),
(84, 'Kolonisationszentrum', '10. Wirtschaft & Verwaltung', 2, '', 64800, 'kol_zentrum', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 51),
(85, 'Kommerztempel', '10. Wirtschaft & Verwaltung', 14, '', 28800, 'kommerztempel', '<br>Hier kann man viele verschiedene Sachen kaufen. Zum Beispiel Schuhe. Und Essen. Und was zu trinken. Und Hasenbibeln. Also quasi alles was das Herz begehrt. Außerdem: Wo hat man sonst schon Gelegenheit sich in endlosen Schlangen einzureihen und sich an der Kasse von einer Verkäuferin vollmaulen lassen, die einem dadurch den Eindruck vermittelt froh sein zu dürfen, dass man überhaupt hier und bei ihr bezahlen darf. Das vollkommene Paradies eben. Deswegen bringt der Kommerztempel ja auch 100\\% Steuerbonus.', '', '(Staatsform)<br>', 'Kolonie<br><br> Dieses Gebäude benötigt weitere komplexe Voraussetzungen', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+100\\% Steuereinkommen', 'Eisen: 5.000 Stahl: 2.000 chem. Elemente: 6.000 Energie: 6.000 Credits: 5.000 Bevölkerung: 200', 128, 1, 'norma', '', 149),
(86, 'Handelsgesellschaft', '10. Wirtschaft & Verwaltung', 3, '', 43200, 'handelsgesellschaft', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 53),
(87, 'kleine Kommunikationsanlage', '10. Wirtschaft & Verwaltung', 7, '', 32400, 'kl_kommanlage', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 62),
(88, 'gro&szlig;e Kommunikationsanlage', '10. Wirtschaft & Verwaltung', 9, '', 54000, 'gr_komm_anlage', '<br>', '', '(interstellare Kommunikation)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', 'ermöglicht Kommunikation mit eignen Flotten', '-150 Energie', 1185, 1, 'norma', '', 63),
(89, 'imperiale Steuerbehörde', '10. Wirtschaft & Verwaltung', 10, '', 64800, 'imp_steuer', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 109),
(90, 'Niederlassung der Sirius Corp', '10. Wirtschaft &amp; Verwaltung', 15, '', 43200, 'sirius_corp', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 153),
(91, 'Bank', '10. Wirtschaft &amp; Verwaltung', 18, '', 21600, 'bank', '<br>', '', '(Bankwesen)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', 'Ermöglicht das überweisen von Credits an andere Spieler, +10\\% Steuereinkommen', '-35 Energie', 153, 1, 'norma', '', 171),
(92, 'Robominerzentrale', '10. Wirtschaft &amp; Verwaltung', 12, '', 36000, 'robozentrale', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 126),
(93, 'Roboterminenkomplex', '10. Wirtschaft &amp; Verwaltung', 11, '', 172800, 'robokomplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 114),
(94, 'Kampfbasenverwaltung', '10. Wirtschaft &amp; Verwaltung', 13, '', 43200, 'kampfbasenkontrolle', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 128),
(95, '5 Sterne Pizzalokal', '10. Wirtschaft &amp; Verwaltung', 16, '', 64800, '5sterne_pizzalokal', '<br>', '', '(Wieso kostet das so viel?)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+100\\% Steuereinkommen', '-30 chem. Elemente, -30 Wasser, -250 Energie', 610, 1, 'norma', '', 157),
(96, 'Katzeundmausabwehrstockproduktionsfabrik', '10. Wirtschaft &amp; Verwaltung', 17, '', 43200, 'katzundmaus', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 158),
(97, 'Terraforming Lebensbedingungen', ' 8. Spezielle Aktionen', 25, '', 3600, 'terraform_leben', 'Verbessert die Lebensbedingungen.<br>\r\nDie Kosten für das Terraforming berechnen sich anhand den aktuellen Lebensbedingungen, d.h. die unten angegebenen Kosten sind nur die Initialkosten für 0%. Alle höheren Stufen sind teurer. Auf der Bauseite des Planeten werden die aktuellen Kosten angezeigt.', '', 'Terraforming Lebensbedingungen', 'Kolonie', '', '', '', '', '+5% Lebensbedingungen ', 'Stufengebäude', 0, 1, '', '', 106),
(98, 'Recyclinganlage', '10. Wirtschaft &amp; Verwaltung', 26, '', 21600, 'recycler', '<br>', '', '(Recycling)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+5\\% Recycling beim Abbruch, Abriss &amp; Verschrottung von Gebäuden, Schiffen &amp; Defanlagen', '-10 chem. Elemente, -100 Energie', 63, 5, 'norma', '', 104),
(99, 'orbitaler Recycler', '10. Wirtschaft &amp; Verwaltung', 27, '', 64800, 'orb_recycler', '<br>', '(Kontrollzentrum mit Startrampe)', '(Recycling)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+3\\% (bis zu) Recycling von Eisen, Stahl, Eis aus im Kampf zerstörten Schiffen', '-200 chem. Elemente', 1141, 5, 'norma', '', 105),
(100, 'orbitaler Lift', ' 7. Raumfahrt', 110, '', 172800, 'orb_lift', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 163),
(101, 'passiver Basensonnensystemscanner', ' 1. Beobachtung', 70, '', 36000, 'p_base_scanner', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 136),
(102, 'orbitale Basenverteidigungskontrolle', ' 9. Verteidigung', 70, '', 21600, 'orb_basen_def_control', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 129),
(104, 'Jumpgate', '10. Wirtschaft &amp; Verwaltung', 50, '', 43200, 'jumpgate', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 214),
(105, 'Statue', ' 4. Freizeit', 20, '', 3600, 'statue', '<br>', '', '(Statuen)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', 'völlig sinnloses Gebäude', 'Eisen: 250 Stahl: 50 VV4A: 10 chem. Elemente: 5 Energie: 100 Credits: 250', 8, 0, 'norma', '', 125),
(106, 'Handelsgesellschaft 4-6', '10. Wirtschaft &amp; Verwaltung', 4, '', 86400, 'handelsgesellschaft', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(107, 'kleine Kommunikationsanlage Stufe 2', '10. Wirtschaft &amp; Verwaltung', 8, '', 36000, 'kl_kommanlage', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(109, 'Destillerie', ' 4. Freizeit', 90, '', 3600, 'destille', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 175),
(110, 'kleiner Stahlkomplex', ' 5a. Wandlungsgebs', 7, '', 36000, 'kl_stahlkomplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 176),
(111, 'Asteroidenwohnanlagen', ' 2. Bev&ouml;lkerung', 90, '', 14400, 'asteroidenwohnanlage', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 178),
(112, 'Asteroidenminen', ' 5. F&ouml;rderungsanlagen', 1, '', 28800, 'asteroidenmine', '<br>', '(Kontrollzentrum mit Startrampe)<br><br>Voraussetzungen Planeteneigenschaften Asteroidengürtel', '(Asteroidenmining)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', '+160,00 Eisen', '-50 Energie', 124, 25, 'norma', '', 170),
(113, 'Goldmine', ' 5. F&ouml;rderungsanlagen', 8, '', 43200, 'goldmine', '<br>', 'Voraussetzungen Planeteneigenschaften Gold', '(Robotik)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+50\\% Steuereinkommen', 'Eisen: 25.000 Stahl: 10.000 chem. Elemente: 10.000 Eis: 500 Wasser: 1.500 Energie: 25.000 Credits: 10.000 Bevölkerung: 250', 441, 1, 'norma', '', 152),
(115, 'Gasriesenwohndingens', ' 2. Bev&ouml;lkerung', 100, '', 14400, 'gasgiganten_wohnanlage', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 179),
(117, 'DN Werft', ' 7. Raumfahrt', 90, '', 129600, 'dn_werft', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 148);
INSERT INTO `prefix_gebaeude` (`id`, `name`, `category`, `idcat`, `inactive`, `dauer`, `bild`, `info`, `n_building`, `n_research`, `n_kolotyp`, `n_planityp`, `e_research`, `e_building`, `zerstoert`, `bringt`, `Kosten`, `Punkte`, `MaximaleAnzahl`, `typ`, `kostet`, `id_iw`) VALUES
(118, 'Hilfskiste chem. Elemente auspacken', '11. Imperiale Hilfsg&uuml;ter', 3, '', 1800, 'hilfskiste_chem', '<br>In dieser Schachtel sind alle möglichen Chemikalien. Wir hoffen, das Ding fliegt nicht irgendwann in die Luft.', '(Hauptquartier)', '(chemische Reaktionen)<br>', 'Kolonie<br>', '', '<br>', '<br>', '<br>', '+100 chem. Elemente', '', 0, 1, 'norma', '', 197),
(121, 'Hilfskiste Scout auspacken', '11. Imperiale Hilfsg&uuml;ter', 9, '', 3600, 'hilfskiste_scout', '<br>In dieser Kiste versteckt sich ein Scout, der das Universum auskundschaften kann, also zum Beispiel Geoscans, etc.', '(Hauptquartier)', '(chemische Reaktionen)<br>', 'Kolonie<br>', '', '<br>', '<br>', '<br>', '+1 Scout', 'Eisen: 10 Stahl: 10 chem. Elemente: 20 Energie: 40', 0, 1, 'norma', '', 203),
(122, 'Hilfskiste Eisen auspacken', '11. Imperiale Hilfsg&uuml;ter', 1, '', 1800, 'hilfskiste_eisen', '<br>Ein wenig Eisen für alle Fälle. Kann ja nie schaden, ein bisschen im Keller zu haben.', '(Hauptquartier)', '<br>', 'Kolonie<br>', '', '<br>', '<br>', '<br>', '+500 Eisen', '', 0, 1, 'norma', '', 195),
(123, 'Bauarbeitersiedlung aus Containern und einem Plumpsklo', '10. Wirtschaft & Verwaltung', 0, '', 900, 'plumpsklo', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 194),
(124, 'Hilfskiste Stahl auspacken', '11. Imperiale Hilfsgüter', 2, '', 1800, 'hilfskiste_stahl', '<br>Ein bisschen Stahlvorrat zum Angeben.', '(Hauptquartier)', '<br>', 'Kolonie<br>', '', '<br>', '<br>', '<br>', '+250 Stahl', '', 0, 1, 'norma', '', 196),
(125, 'Hilfskiste Eis auspacken', '11. Imperiale Hilfsgüter', 4, '', 1800, 'hilfskiste_eis', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 198),
(126, 'Hilfskiste Wasser auspacken', '11. Imperiale Hilfsgüter', 5, '', 1800, 'hilfskiste_wasser', '<br>Wasser-Vorratspack für beispielsweise lange Wüstentouren. Pro Stunde kann Ihr Körper etwa einen Liter Wasser verwerten. Nehmen Sie deshalb stets ausreichend Wasser mit in die Wüste. Pro Tag in der Wüste sind 8 Liter Wasser einzuplanen pro Person. Halten Sie das Elektrolyt-Gleichgewicht durch Verwendung von natrium-, kalium und magnesiumreichen Wassers.', '(Hauptquartier)', '<br>', 'Kolonie<br>', '', '<br>', '<br>', '<br>', '+500 Wasser', '', 0, 1, 'norma', '', 199),
(127, 'Hilfskiste Energie auspacken', '11. Imperiale Hilfsgüter', 6, '', 1800, 'hilfskiste_energie', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 200),
(130, 'große Werften wieder hochschaffen', ' 8. Spezielle Aktionen', 8, '', 86400, 'werfthochscha_gr', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(131, 'bürokratischer Verwaltungskomplex', '10. Wirtschaft & Verwaltung', 19, '', 86400, 'buerokrat_verwalt_kompl', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(132, 'Universumsichtkontrolleinrichtung', ' 8. Spezielle Aktionen', 1, '', 86400, 'univ_kontroll_einrichtung', '<br>', '(Hauptquartier)', '(unlogische Informationsspeicherung)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.) ', 'ermöglicht die Universumsicht als XML zu exportieren', ': 75.000 : 29.000 : 5.000 : 23.500 : 50.000 : 20.000 : 500', 1157, 1, 'norma', '', 227),
(133, 'Klonzentrum', ' 2. Bevölkerung', 120, '', 28800, 'klonzentrum', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 168),
(137, 'Massdriver', ' 8. Spezielle Aktionen', 10, '', 43200, 'massdriver', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 228),
(139, 'Alle Werften wieder hochschaffen', ' 8. Spezielle Aktionen', 2, '', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 226),
(142, 'Ein Loch', '10. Wirtschaft &amp; Verwaltung', 31, '', 43200, '', '<br>', '', '(Wir bauen das größte Loch das es gibt)<br>', 'Kolonie<br>', '', '<br>', '<br>', '<br>', 'Wir baggern ein großes Loch', '', 0, 0, 'norma', '', 207),
(143, 'wirres Löffelmonument', '10. Wirtschaft & Verwaltung', 30, '', 43200, 'loeffelmonument', '<br>Ich weiß zwar nicht, was das Ding kann oder welchen Sinn es hat... um ehrlich zu sein, ist es sogar ziemlich hässlich. Aber das macht nichts, denn die Leute mögen es. Sie hatten so ein komisches Wort dafür: Kunst.', '', '(Löffel, überall Löffel)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+5 Zufriedenheit', '-400 Energie', 2498, 1, 'norma', '', 218),
(144, 'kleine wirre Blumenrabatte zum Quälen von Mitmenschen', ' 9. Verteidigung', 80, '', 43200, 'blumenrabatte', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 204),
(145, 'Pfadfindercamp', ' 4. Freizeit', 110, '', 10800, 'pfadfindercamp', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 206),
(146, 'Eisschmelzanlage AlphaEins', ' 5a. Wandlungsgebs', 28, '', 43200, 'eis_alpha_eins', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 259),
(147, 'Icecrusher V3b', ' 5. Förderungsanlagen', 23, '', 43200, 'crusher_v3b', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 217),
(148, 'kleiner Eisenminenkomplex', ' 5. Förderungsanlagen', 4, '', 36000, 'eisen_komplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 210),
(149, 'brennende Irrenanstalt', ' 9. Verteidigung', 90, '', 86400, 'irrenanstalt', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 209),
(150, 'geheimes Vulkanlabor', ' 3. Forschung', 80, '', 28800, 'vulkanlabor', '<br>Bringt 300 FP', '(Ein Berg)', '(Müllentsorgung in einem Vulkan)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+528,70 Forschungspunkte', '-250 chem. Elemente, -1300 Energie', 3855, 0, 'globa', '', 211),
(151, 'Kraftwerk (Fernwärme)', ' 5b. Eneprodde', 48, '', 54000, 'kw_fernwaerme', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(152, 'Kraftwerk (Fernwärmekomplex)', ' 5b. Eneprodde', 49, '', 7200, '', '<br>', 'Voraussetzungen Planeteneigenschaften instabiler Kern', '(Heißer öhm *hust* Rauch)<br>', 'Kolonie<br>', '', '<br>', '<br>', 'fremde Spielerinteraktion (Bomben, etc)<br><br>Sonstiges (Katastrophen, Massdriver, Streiks, usw.)', '+75000,00 Energie', '-200 chem. Elemente, -500 Wasser', 13925, 5, 'norma', '', 258),
(153, 'Ein Berg', '10. Wirtschaft & Verwaltung', 32, '', 43200, 'ein_berg', '<br>', '', '(Wohin mit dem Dreck)<br>', 'Kolonie<br>', '', '<br>', '<br>', '<br>', 'Wir bauen einen großen Berg', '', 0, 0, 'norma', '', 208),
(154, 'Feuchtbiotop', '10. Wirtschaft & Verwaltung', 45, '', 43200, 'feuchtbiotop', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 205),
(155, 'Icecrusher V3a', ' 4. Freizeit', 100, '', 3600, 'crusher_v3a', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 216),
(156, 'Nebelmaschine', ' 9. Verteidigung', 100, '', 259200, 'nebelmaschine', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 215),
(157, 'Villenkomplex V2', ' 2. Bevölkerung', 52, '', 7200, 'villenkomplex_v2', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 212),
(158, 'Eis & Wasserlager V2', ' 6. Lager & Bunker', 25, '', 10800, 'eiswasser_lager_v2', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 255),
(159, 'Chemielager V2', ' 6. Lager & Bunker', 15, '', 10800, 'chem_lager_v2', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 256),
(160, 'Energielager V2', ' 6. Lager & Bunker', 32, '', 10800, 'energie_lager_v2', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 257),
(161, 'planetares Beta Schild', ' 9. Verteidigung', 55, '', 172800, 'plan_betaschild', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 213),
(162, 'Der Komplex', ' 8. Spezielle Aktionen', 15, '', 3600, 'komplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 219),
(163, 'kleines Forschungslabor Stufe 2', ' 3. Forschung', 20, '', 23040, 'kl_forschlab', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(164, 'kleines Forschungslabor Stufe 3', ' 3. Forschung', 20, '', 36000, 'kl_forschlab', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(228, 'Artefaktbunker', ' 6. Lager & Bunker', 180, '', 10800, 'artefaktbunker', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 280),
(184, 'prähistorische Ausgrabung', '12. Artefakte', 10, '', 0, 'praehist_ausgrab', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 260),
(186, 'Artefaktsammelbasencenter', '12. Artefakte', 30, '', 0, 'artefaktbasencenter', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 262),
(189, 'orbitaler Forschungskomplex Stufe 2', ' 3. Forschung', 50, '', 25920, 'orb_lab', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(190, 'orbitaler Forschungskomplex Stufe 3', ' 3. Forschung', 51, '', 30240, 'orb_lab', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(191, 'Area 42 (unterirdischer Forschungskomplex) Stufe 2', ' 3. Forschung', 61, '', 32400, 'u_forschkomplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(192, 'Area 42 (unterirdischer Forschungskomplex) Stufe 3', ' 3. Forschung', 62, '', 38880, 'u_forschkomplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(199, 'Hilfskiste Settlers Delight auspacken', '11. Imperiale Hilfsgüter', 10, '', 3600, 'hilfskiste_systrans', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 201),
(203, 'Koloniezentrale', '13. Sonstiges', 10, '1', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 60),
(201, 'gr. Forschungslabor Stufe 2', ' 3. Forschung', 41, '', 30240, 'forschungskomplex', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(204, 'Techteam zur Erhöhung der Chemiedichte', '13. Sonstiges', 20, '1', 360, '', 'Dieses Gebäude kann nicht mehr gebaut werden, wenn eines der folgenden Gebäude gebaut wurde:<br>\r\nTechteam zur Erhöhung der Eisdichte<br>\r\nTechteam zur Erhöhung der Eisendichte', '', '', 'Kolonie<br>\r\nDieses Gebäude benötigt weitere komplexe Voraussetzungen', '', '', '', '', 'Fügt 2 Prozentpunkte zum Vorkommen chem. Elemente auf diesem Planeten hinzu. Dadurch wird mehr chem. Elemente pro Abbauanlage produziert. ', '', 0, 1, '', '', 283),
(205, 'Techteam zur Erhöhung der Eisendichte', '13. Sonstiges', 30, '1', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 281),
(206, 'tolle Baumhüttenanlage (mit extratollem Ausblick)', '13. Sonstiges', 40, '1', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 58),
(207, 'Sandhügel mit Strand', '13. Sonstiges', 50, '1', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(208, '2 Zimmer Höhlenanlage mit Südblick', '13. Sonstiges', 60, '1', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(209, 'Techteam zur Erhöhung der Eisdichte', '13. Sonstiges', 70, '1', 360, '', 'Dieses Gebäude kann nicht mehr gebaut werden, wenn eines der folgenden Gebäude gebaut wurde:<br>\r\nTechteam zur Erhöhung der Chemiedichte<br>\r\nTechteam zur Erhöhung der Eisendichte', '', '', 'Kolonie<br>Dieses Gebäude benötigt weitere komplexe Voraussetzungen', '', '', '', '', 'Fügt 24 Prozentpunkte zum Vorkommen Eis auf diesem Planeten hinzu. Dadurch wird mehr Eis pro Abbauanlage produziert. Wenn dieses Gebäude gebaut werden soll, dann muss dies innerhalb von 14 Tagen nach', '', 0, 1, '', '', 282),
(229, 'Strukturelle Integritätsverstärkung mittels Phasenfluxdehydrierung', ' 9. Verteidigung', 110, '', 86400, 'strukturell_usw', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 154),
(230, 'Kampfbasen Panzerung (mit Postits)', ' 9. Verteidigung', 120, '', 36000, '', '', '', 'orbitale Dockingsysteme', 'Kampfbasis', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)<br>\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', 'Erhöht die Trefferpunkte der Kampfbasis um 10%', 'Stufengebäude', 0, 0, '', '', 290),
(231, 'Artefaktbasen Panzerung (mit kleinen englischen Keksen)', ' 9. Verteidigung', 130, '', 36000, '', '', '', 'orbitale Dockingsysteme', 'Artefaktbasis', '', '', '', 'fremde Spielerinteraktion (Bomben, etc)<br>\r\nSonstiges (Katastrophen, Massdriver, Streiks, usw.)', 'Erhöht die Trefferpunkte der Artefaktbasis um 10%', 'Stufengebäude', 0, 0, '', '', 291),
(232, 'grosses Chemielager (Artefaktbasis)', ' 6. Lager & Bunker', 190, '', 10800, 'chemlager_gross', '', '', 'orbitale Dockingsysteme', 'Artefaktbasis', '', '', '', '', '', 'Stufengebäude', 0, 0, '', '', 292),
(233, 'grosses Energielager (Artefaktbasis)', ' 6. Lager & Bunker', 200, '', 10800, 'energielager_gross', '', '', 'orbitale Dockingsysteme', 'Artefaktbasis', '', '', '', '', '', 'Stufengebäude', 0, 0, '', '', 293),
(234, 'Biosphäre', ' 2. Bevölkerung', 80, '', 14400, 'biosphaere', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 160),
(236, 'große prähistorische Ausgrabung', '12. Artefakte', 80, '', 172800, 'gr_praehist_ausgrab', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 261),
(242, 'Asteroidenwohnplattform', ' 2. Bevölkerung', 111, '', 10800, 'asteroidenwohnplatform', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 182),
(243, 'Schönheitssalon', ' 4. Freizeit', 81, '', 3600, 'schoenheit', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 174),
(244, 'Fernwärmekraftwerk', ' 5b. Eneprodde', 50, '', 54000, 'kw_fernwaerme', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 220),
(245, 'Grabbeltisch mit radioaktiven Müll', ' 4. Freizeit', 51, '', 3600, '', '', 'Kraftwerk (Atom)', 'Kostenlose Radioaktive Müllentsorgung mittels Schlussverkauf und Grabbeltischen', 'Kolonie<br>Dieses Gebäude benötigt weitere komplexe Voraussetzungen', '', '', '', '', '+0,2 Zufriedenheit', 'Eisen: 500 Stahl: 200 Energie: 200', 15, 5, '', '', 312),
(246, 'Grabbeltisch mit radioaktiven Blob', ' 4. Freizeit', 52, '', 3600, '', '', 'Kraftwerk (Atom)', 'Kostenlose Radioaktive Müllentsorgung mittels Schlussverkauf und Grabbeltischen', 'Kolonie<br>Dieses Gebäude benötigt weitere komplexe Voraussetzungen', '', '', '', '', '+0,2 Zufriedenheit, +1 Eis', 'Eisen: 500 Stahl: 200 Energie: 200', 15, 5, '', '', 318),
(247, 'Strukturelle Integritätsverstärkung', ' 9. Verteidigung', 0, '', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(248, 'großes Chemielager ', ' 6. Lager & Bunker', 0, '', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(249, 'großes Energielager ', ' 6. Lager & Bunker', 0, '', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 0),
(250, 'grosses Eis & Wasserlager (Artefaktbasis)', ' 6. Lager & Bunker', 210, '', 10800, '', '', '', 'orbitale Dockingsysteme', 'Artefaktbasis', '', '', '', '', '', 'Stufengebäude', 0, 0, '', '', 294);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_gebaeude_spieler`
--
-- Erzeugt am: 22. Apr 2012 um 10:06
-- Aktualisiert am: 22. Apr 2012 um 10:06
--

DROP TABLE IF EXISTS `prefix_gebaeude_spieler`;
CREATE TABLE IF NOT EXISTS `prefix_gebaeude_spieler` (
  `coords_gal` tinyint(4) NOT NULL,
  `coords_sys` smallint(6) NOT NULL,
  `coords_planet` tinyint(4) NOT NULL,
  `kolo_typ` varchar(20) CHARACTER SET latin1 NOT NULL,
  `user` varchar(30) CHARACTER SET latin1 NOT NULL,
  `category` varchar(100) CHARACTER SET latin1 NOT NULL,
  `building` varchar(200) CHARACTER SET latin1 NOT NULL,
  `count` smallint(6) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`coords_gal`,`coords_sys`,`coords_planet`,`category`,`building`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Gebaeudeuebersicht';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_group`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_group`;
CREATE TABLE IF NOT EXISTS `prefix_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `prefix_group`
--

INSERT INTO `prefix_group` (`id`, `parent_id`, `name`) VALUES
(1, 0, '(Alle)');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_group_sort`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_group_sort`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_group_user`;
CREATE TABLE IF NOT EXISTS `prefix_group_user` (
  `group_id` int(11) NOT NULL,
  `user_id` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_highscore`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_highscore`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 19:06
--

DROP TABLE IF EXISTS `prefix_iwdbtabellen`;
CREATE TABLE IF NOT EXISTS `prefix_iwdbtabellen` (
  `name` varchar(40) NOT NULL DEFAULT '',
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabellennamen, die in der DB verwendet werden.';

--
-- Daten für Tabelle `prefix_iwdbtabellen`
--

INSERT INTO `prefix_iwdbtabellen` (`name`) VALUES
('allianzstatus'),
('bestellen'),
('bestellung'),
('bestellung_projekt'),
('bestellung_schiffe'),
('bestellung_schiffe_pos'),
('building2building'),
('building2research'),
('def'),
('gebaeude'),
('gebaeude_spieler'),
('group'),
('group_sort'),
('group_user'),
('highscore'),
('kasse_content'),
('kasse_incoming'),
('kasse_outgoing'),
('kb'),
('kb_bomb'),
('kb_bomb_geb'),
('kb_flotten'),
('kb_flotten_schiffe'),
('lager'),
('lieferung'),
('mails'),
('menu'),
('merkmale'),
('parser'),
('preset'),
('punktelog'),
('raid'),
('raidview'),
('research'),
('research2building'),
('research2prototype'),
('research2research'),
('research2user'),
('researchfield'),
('ressuebersicht'),
('scans'),
('scans_historie'),
('schiffe'),
('schiffstyp'),
('sid'),
('sitterauftrag'),
('sitterlog'),
('spielerinfo'),
('sysscans'),
('target'),
('transferliste'),
('univ_link'),
('user'),
('user_research'),
('versand_auftrag'),
('wronglogin');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kasse_content`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kasse_content`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kasse_incoming`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kasse_outgoing`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kb`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kb_bomb`;
CREATE TABLE IF NOT EXISTS `prefix_kb_bomb` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL,
  `user` varchar(30) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_bomb_geb`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kb_bomb_geb`;
CREATE TABLE IF NOT EXISTS `prefix_kb_bomb_geb` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `ID_IW_GEB` int(11) NOT NULL DEFAULT '0',
  `anzahl` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_kb_def`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kb_def`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kb_flotten`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kb_flotten_schiffe`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_kb_pluenderung`;
CREATE TABLE IF NOT EXISTS `prefix_kb_pluenderung` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `ID_IW_RESS` int(11) NOT NULL DEFAULT '0',
  `anzahl` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_lager`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_lager`;
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
  `eisen_sichtbar` tinyint(1) NOT NULL default '1',
  `stahl_sichtbar` tinyint(1) NOT NULL default '1',
  `chem_sichtbar` tinyint(1) NOT NULL default '1',
  `vv4a_sichtbar` tinyint(1) NOT NULL default '1',
  `eis_sichtbar` tinyint(1) NOT NULL default '1',
  `wasser_sichtbar` tinyint(1) NOT NULL default '1',
  `energie_sichtbar` tinyint(1) NOT NULL default '1',
  PRIMARY KEY (`coords_gal`,`coords_sys`,`coords_planet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Lagerübersicht';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_lieferung`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_lieferung`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 22:16
--

DROP TABLE IF EXISTS `prefix_menu`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Menüstruktur' AUTO_INCREMENT=25 ;

--
-- Daten für Tabelle `prefix_menu`
--

INSERT INTO `prefix_menu` (`id`, `menu`, `submenu`, `active`, `title`, `status`, `action`, `extlink`, `sittertyp`, `sound`) VALUES
(1, 1, 0, 1, 'Daten', '', '', 'n', 0, 0),
(2, 1, 1, 1, 'neuer Bericht', '', 'newscan', 'n', 0, 0),
(3, 11, 0, 1, 'Allianz', '', '', '', 0, 0),
(4, 11, 1, 1, 'Spieler', '', 'members', 'n', 0, 0),
(5, 11, 2, 1, 'Planeten', '', 'planeten', 'n', 0, 0),
(6, 11, 3, 1, 'Schiffe', '', 'schiffe', 'n', 0, 0),
(7, 6, 0, 1, 'Universum', '', '', '', 0, 0),
(8, 6, 1, 1, 'Planet suchen', '', 'searchdb', 'n', 0, 0),
(9, 6, 2, 1, 'Karte', '', 'karte', 'n', 0, 0),
(10, 2, 0, 1, 'Sitting', '', '', '', 0, 0),
(11, 2, 4, 1, 'meine Aufträge', '', 'sitterauftrag', 'n', 1, 0),
(12, 2, 3, 1, 'Sitterhistorie', '', 'sitterhistory', 'n', 1, 0),
(13, 2, 2, 1, 'Sitterlogins', '', 'sitterlogins', 'n', 1, 0),
(14, 2, 1, 1, 'Sitteraufträge #sitter', '', 'sitterliste', 'n', 1, 0),
(15, 7, 0, 1, 'Angriffe', '', '', '', 0, 0),
(16, 14, 2, 1, 'Menüverwaltung', 'admin', 'admin_menue', 'n', 0, 0),
(17, 7, 1, 1, 'laufende Angriffe #angriffe', '', 'm_raid&view=overview&allistatus=own&angriff=1', 'n', 0, 0),
(18, 13, 0, 1, 'Tools', '', '', '', 0, 0),
(19, 12, 1, 1, 'Highscore', '', 'showhighscore', 'n', 0, 1),
(20, 14, 0, 1, 'Administration', 'admin', '', 'n', 0, 0),
(21, 12, 0, 1, 'Statistik', '', '', '', 0, 0),
(22, 3, 0, 1, 'Bestellung', '', '', '', 1, 0),
(23, 6, 3, 1, 'Planeten ohne Scans', '', 'showgalaxy&withoutscan=1', 'n', 0, 0),
(24, 6, 4, 1, 'Reservierte Planeten', '', 'showgalaxy&reserv=1&ansicht=geologisch  	', 'n', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_merkmale`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_merkmale`;
CREATE TABLE IF NOT EXISTS `prefix_merkmale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merkmal` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `merkmal` (`merkmal`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Planetenmerkmale zur Suche' AUTO_INCREMENT=12 ;

--
-- Daten für Tabelle `prefix_merkmale`
--

INSERT INTO `prefix_merkmale` (`id`, `merkmal`) VALUES
(1, 'Mond'),
(2, 'alte Ruinen'),
(3, 'Ureinwohner'),
(4, 'Asterioidengürtel'),
(5, 'Gold'),
(6, 'planetarer Ring'),
(7, 'instabliler Kern'),
(8, 'Natürliche Quelle'),
(9, 'wenig Rohstoffe'),
(10, 'toxisch'),
(11, 'radioaktiv');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_order_comment`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_order_comment`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_params`;
CREATE TABLE IF NOT EXISTS `prefix_params` (
  `name` varchar(80) NOT NULL DEFAULT '',
  `value` varchar(80) NOT NULL DEFAULT '',
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `prefix_params`
--

INSERT INTO `prefix_params` (`name`, `value`, `text`) VALUES
('gesperrt', '0', ''),
('aktuellnews', '0', ''),
('sound_login', '1', ''),
('sound_standart', '1', ''),
('sound_global', '4', ''),
('bericht_fuer_sitter', '1', ''),
('bericht_fuer_rang', 'all', ''),
('aktuellnews', '0', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_parsemenu`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_parsemenu`;
CREATE TABLE IF NOT EXISTS `prefix_parsemenu` (
  `ersetze` varchar(100) NOT NULL DEFAULT '',
  `durch` text NOT NULL,
  `varorstr` char(3) NOT NULL DEFAULT 'str'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `prefix_parsemenu`
--

INSERT INTO `prefix_parsemenu` (`ersetze`, `durch`, `varorstr`) VALUES
('M~B', 'anznachrichten', 'var'),
('N~S', 'anznews', 'var'),
('#', 'anzauftrag', 'var'),
('M~B', 'anznachrichten', 'var'),
('N~S', 'anznews', 'var'),
('#', 'anzauftrag', 'var');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_parser`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 19:06
--

DROP TABLE IF EXISTS `prefix_parser`;
CREATE TABLE IF NOT EXISTS `prefix_parser` (
  `modulename` varchar(30) NOT NULL DEFAULT '',
  `recognizer` varchar(200) NOT NULL DEFAULT '',
  `message` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`modulename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Parser-Phrasen und Zuordnungen zu Parsermodulen';

--
-- Daten für Tabelle `prefix_parser`
--

INSERT INTO `prefix_parser` (`modulename`, `recognizer`, `message`) VALUES
('schiffsuebersicht', 'Schiffsübersicht', 'Schiffsübersicht'),
('members', 'Name Rang GebP FP', 'Mitgliederliste'),
('raid', 'Kampf auf dem Planeten', 'Raid-Bericht'),
('transferliste', 'Transport angekommen', 'Transportbericht'),
('unixml', '?xml ', 'UniXML'),
('research', 'Forschungsinfo:', 'Forschungsbericht'),
('researchoverview', 'Erforschte Forschungen', 'Forschungsliste'),
('kasse', 'Standardbeitrag', 'Allianzkasse'),
('resskolo', 'Ressourcenkoloübersicht', 'Ressourcenkoloübersicht'),
('eigene_flotten', 'Eigene Flotten', 'Eigene Flotten'),
('fremde_flotten', 'Fremde Flotten', 'Fremde Flotten'),
('fehlscan', 'Sondierung fehlgeschlagen', 'Fehlgeschlagene Sondierung'),
('highscore', 'Letzte Aktualisierung', 'Highscore'),
('sbxml', 'http://www.icewars.de/portal/kb/de/sb.php', 'SB-XML'),
('gebaeudeuebersicht', '__deaktiviert__Gebäudeübersicht', 'Gebäudeübersicht'),
('research2', 'Die Forscher haben schon', 'Forschungen'),
('production', 'Ressourcenkoloübersicht', 'Produktionsübersicht');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_preset`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_preset`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Einstellungen für die Suche' AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `prefix_preset`
--

INSERT INTO `prefix_preset` (`id`, `name`, `fromuser`, `typ`, `objekt`, `user`, `exact`, `allianz`, `planetenname`, `merkmal`, `eisengehalt`, `chemievorkommen`, `eisdichte`, `lebensbedingungen`, `order1`, `order1_d`, `order2`, `order2_d`, `order3`, `order3_d`, `grav_von`, `grav_bis`, `gal_start`, `gal_end`, `sys_start`, `sys_end`, `max`, `ansicht`, `kgmod`, `dgmod`, `ksmod`, `dsmod`, `fmod`) VALUES
(1, 'leer', '', '%', '%', '', '', '', '', '%', 0, 0, 0, 0, '', 'ASC', '', 'ASC', '', 'ASC', '', '', '', '', '', '', '', 'auto', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_punktelog`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 22. Apr 2012 um 07:54
--

DROP TABLE IF EXISTS `prefix_punktelog`;
CREATE TABLE IF NOT EXISTS `prefix_punktelog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(30) NOT NULL DEFAULT '',
  `date` int(12) NOT NULL DEFAULT '0',
  `gebp` int(12) NOT NULL DEFAULT '0',
  `fp` int(12) NOT NULL DEFAULT '0',
  `gesamtp` int(12) NOT NULL DEFAULT '0',
  `ptag` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Punktenachverfolgung' AUTO_INCREMENT=1;

--
-- Daten für Tabelle `prefix_punktelog`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_raidview`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_raidview`;
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
  `summe` int(11) NOT NULL DEFAULT '0' COMMENT 'um sortieren zu kÃƒÂ¶nnen',
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 19:06
--

DROP TABLE IF EXISTS `prefix_research`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Forschungsinformation fuer Forschung Id' AUTO_INCREMENT=382 ;

--
-- Daten für Tabelle `prefix_research`
--

INSERT INTO `prefix_research` (`ID`, `name`, `description`, `FP`, `gebiet`, `highscore`, `addcost`, `geblevels`, `declarations`, `defense`, `objects`, `genetics`, `rlevel`, `gameversion`, `reingestellt`, `FPakt`, `time`) VALUES
(1, 'Bautechnik', 'Gestern hat jemand angefangen einen 2-stöckigen Stall zu bauen, angeblich weil der alte zu klein ist. Dabei hat der Steine verwendet, statt wie anständige Leute Lehm und Stroh zu benutzen. Irre, was manchen so einfällt, nur um die Freundin zu beeindrucken. Hoffentlich macht das keine Schule, sonst wächst uns das am Ende noch über den Kopf.', 250, 3, 0, '', '', '', '', '', '', 1, '11.74', 'Balian', 30, 1305120628),
(2, 'chemische Reaktionen', 'Die vielen bunten Fläschchen, die man in der Nähe vom bunten Hügel finden kann, geben eine schöne Blubbermasse wenn man alles was drin ist zusammenkippt. Man könnte ja versuchen, das auch in einer kleinen Chemiefabrik herzustellen.', 100, 2, 0, '', '', '', '', '', '', 1, '11.74', 'Balian', 12, 1305120628),
(3, 'Astronomie', 'Irgendein Blödmann hat behauptet dein Planet wäre gar keine Scheibe und die funkelnden Dinger da oben wären andere Planeten, die, genau wie dein Planet, im Universum auf &quot;Umlaufbahnen&quot; umherkreisen. Hahahaha der wird schön blöd gucken, wenn dein Teleskop erstmal fertig ist und er sieht, dass es dort rein gar nichts zu sehen gibt!', 100, 4, 0, '', '', '', '', '', '', 1, '11.74', 'Balian', 12, 1305120628),
(26, 'Basiswissen', 'Ist halt Basiswissen. Ohne das lassen sich einige Gebäude einfach nicht bauen.<br />\nzuerst erforscht von', 0, 14, 0, '', '', '', '', '', '', 1, '0.696', 'Corran', 0, 0),
(4, 'Atomkraft', 'Du brauchst Strom, du brauchst Strom, du brauchst Strom? Na dann teil doch das Atom!', 500, 4, 0, 'Eisen: 500', '', '', '', '', '', 1, '11.74', 'Balian', 60, 1305120628),
(5, 'Atomraketen', '', 5000, 5, 0, '', '', '', 'SDI Atomraketen', '', '', 6, '11.74', 'Balian', 1100, 1305120628),
(6, 'Verbesserte Nutzung der Solarenergie', '', 500, 4, 0, '', '', '', '', '', '', 1, '11.74', 'Balian', 60, 1305120628),
(8, 'Raketentechnik', '', 500, 5, 0, '', '', '', '', '', '', 1, '10.1', 'Labasu', 60, 1305120628),
(9, 'militärische Grundlagen', 'Das Einmaleins der Planetenverteidigung ist eine komplizierte, materialintensive aber unbedingt notwendige Investition. Zumindest haben sich die Forscher mit diesen Argumenten Unmengen an Forschungsgeldern ergaunert. Aber jetzt sitzen die schon seit 3 Wochen am Strand und bauen maßstabsgetreue Sandburgen. Nicht zu fassen...', 500, 5, 0, '', '', '', 'SDI Raketensystem<br />\nStopfentenwerfer', '', '', 2, '10.1', 'Labasu', 60, 1305120628),
(10, 'Lagerbau', 'Wo kommen die ganzen Ressourcen her? Der See nebenan leuchtet schon grünlich, da passen keine chemischen Elemente mehr rein. Im Sandkasten vom Kinderspielplatz ist aber noch Platz. Oder ich baue Lager. Mal sehn.', 250, 3, 0, '', '', '', '', '', '', 2, '11.74', 'Major', 30, 1305120628),
(11, 'Bunkerbau', '', 2000, 5, 0, '', '', '', '', '', '', 3, '11.74', 'Major', 621, 1308638687),
(12, 'Städtebau', 'Endlich kann man nicht nur sehen, welches Bier der Nachbar trinkt und riechen, was er gerade kocht, nein, man kann nachts auch hören, was er gerade macht. Und wenn man ihm im Park begegnet, grinst er immer so schön. Warum nur?', 480, 7, 0, '', '', '', '', '', '', 2, '11.74', 'Siver', 58, 1305120628),
(13, 'Stadtzentren', 'Hm, jetzt werden die Städte langsam zu groß, zu viele Nachbarn, zu weite Entfernungen. Bauen wir einfach ein Zentrum, dann haben wir nicht mehr so weite Wege beim Einkaufen. Ach was, besser noch: Lasst uns nach den Sternen greifen!', 2000, 7, 0, '', '', '', '', '', '', 3, '11.74', 'Siver', 240, 1305120628),
(14, 'Race into Space', 'Den Leuten gehts zu gut, die Städte und Straßen werden immer voller. Bis jetzt lief es prima, aber seit dem letzten Geschwindigkeitsdowngrade zu Gunsten der Sicherheit ist hier der Teufel los. Das Volk von Rasern schreit Zeter und Mordio. Aber auch dafür gibt es &#039;ne Lösung: Ab ins All - freie Fahrt für freie Bürger!', 4000, 6, 0, '', '', '', '', '', '', 3, '11.74', 'Siver', 880, 1305120628),
(15, 'menschliches Zusammenleben', 'Wir bauen jetzt ganz große Häuser in ganz großen Städten, wo alles ganz toll ist. Aber irgendwie ist das doof, wenn einem immer jemand auf die Füße tritt. Auf dem Mars ist bestimmt alles noch viel toller.', 8000, 7, 0, '', '', '', '', '', '', 4, '11.74', 'Vrelid', 1760, 1305120628),
(16, 'Staatsform', 'Ne ne ne, so geht das nicht mehr. Hier macht ja jeder was er will. Was für ein Chaos. Jetzt sind mal alle still und setzen sich, damit wir das friedlich ausdiskutieren können. Und wenn das nicht klappt, dann probieren wirs eben anders. *Knüppel schwing* Wie, das kann man nicht so einfach ändern? Ok, dann eben gleich der Knüppel...', 6000, 7, 0, '', '', '', '', '', '', 5, '11.74', 'Vrelid', 2400, 1309462116),
(18, 'Computer', 'Neu neu neu! Rappelkiste in der es fiept und piept und manchmal auch rattert! Der Spaß für die ganze Familie für läppische 5000 Forschungspunkte! (Was sind eigentlich Forschungspunkte? Müssen das unbedingt mal jemanden an der Universität fragen...)', 5000, 8, 0, '', '', '', '', '', '', 4, '11.74', 'Vrelid', 1100, 1305120628),
(309, 'Eine kurze Abhandlung über den Nutzen von unter dem Bett gefundenen alten Dingen, speziell jener die man nie dort gesucht hat und nun überrascht feststellt, das man im Besitz derselben ist', 'Eine kurze Abhandlung über den Nutzen von unter dem Bett gefundenen alten Dingen, speziell jener, die man nie dort gesucht hat und von denen man nun überrascht feststellt, dass man im Besitz derselben ist', 100000, 12, 0, '', '', '', '', '', '', 12, '11.74', 'Shar', 58000, 1317374712),
(19, 'chemischer Antrieb', 'Man muss höllisch aufpassen, dass die Katze nichts von der Blubbermasse trinkt; die geht sonst ab wie eine Rakete.', 1000, 2, 0, '', '', '', '', '', '', 4, '11.74', 'Vrelid', 220, 1305120628),
(20, 'Handel', 'Es reicht! Vor lauter Eisenstapeln kommt man kaum noch vom Wohnzimmer in die Küche. Und die Straßen sind auch schon zugemüllt. Außerdem hört man gar nicht mehr, was die Nachbarn so treiben. Das Zeug muss schleunigst weg. Redet mal jemandem ein, daß er den Mist unbedingt kaufen will.', 5000, 9, 0, '', 'Handelsgesellschaft Stufe 1<br />\nHandelsgesellschaft Stufe 2', '', '', '', '', 4, '11.74', 'Vrelid', 2000, 1309462116),
(21, 'Internet', 'Das Internet, unendliche Weiten, unerschöpflicher Fundus des Wissens. Man findet alles, besonders Sachen, die man gar nicht finden will. Aber es ist trotzdem prima, weil die User im Internet alle ehrliche und vertrauenswürdige Menschen sind, mit denen man super die Zeit totschlagen kann. Wer braucht da noch das &quot;real life&quot;?', 5000, 8, 0, '', '', '', '', '', '', 5, '11.74', 'Vrelid', 1430, 1308772906),
(22, 'Verbrennungstechnik', 'Der Müll stinkt und sieht nicht schön aus? Verbrennen wir ihn doch einfach! Gut, das stinkt auch und schön aussehen tut es auch nicht, aber immerhin könnte etwas Energie dabei abfallen.', 250, 2, 0, 'chem. Elemente: 100', '', '', '', '', '', 1, '10.1', 'Vrelid', 30, 1305120628),
(23, 'Raumfahrt', 'Ein bisschen Kerosin hier, ein bisschen Stahl dort, fertig ist das Raumschiff. Denke ich jedenfalls, so schwer kann das ja nicht sein. Achja, einen Schiffsdesigner brauch ich auch. Ich glaub ich nehm den Quotenhippie, der immer in der Taverne abhängt.', 4800, 10, 0, 'Eisen: 500 chem. Elemente: 100 Energie: 500', '', '', '', '', '', 5, '11.74', 'Vrelid', 1056, 1305120628),
(24, 'Wo bekomme ich was zu essen her?', '', 4800, 12, 0, '', '', '', '', '', '', 6, '11.74', 'Vrelid', 1349, 1308772906),
(25, 'VV4A', 'Beim Waffelfres- äh, Waffelessen sind Forscher auf eine megageniale Idee gekommen. Wenn man unter hohem Druck und Hitze tolle Lebensmittel noch viel toller machen kann, warum eigentlich nicht tolle Ressourcen auch zusammenbacken? Gesagt getan, heraus kam das Wundermaterial VV4A. Was man damit alles machen kann!! Mindestens größere Waffeleisen. *freu*', 60000, 3, 0, '', '', '', '', '', '', 14, '11.74', 'Vrelid', 32401, 1309884201),
(29, 'Wo finde ich die besten Pornoseiten?', '', 9600, 12, 0, '', '', '', '', '', '', 6, '11.74', 'Corran', 3317, 1308772906),
(31, 'visuelle Optik', 'Hmmm, irgendwie laufen sie um mich herum nur noch gegen Wände und fallen über schlecht postierte Frauendamentaschen. Kann es sein, daß das Volk dekandent und einfach dumm geworden ist? Einer von der schlauen Sorte meinte, wenn man ein Stück Glas auf der Nase hat, wird es besser. Na ob das wirklich gegen &#039;nen Knick in der Optik hilft?', 5000, 3, 0, '', '', '', '', '', '', 6, '11.74', 'Corran', 1650, 1317374897),
(32, 'stellare Kommunikation', '', 8000, 10, 0, '', 'Flottenkontrollzentrum Stufe 1<br />\nFlottenkontrollzentrum Stufe 2<br />\nkleine Kommunikationsanlage Stufe 1<br />\nkleine Kommunikationsanlage Stufe 2', '', '', '', '', 6, '11.74', 'Corran', 3281, 1308772861),
(33, 'Raumschiffchassis', '', 6000, 10, 0, '', '', '', '', '', '', 6, '11.74', 'Corran', 2280, 1310641818),
(34, 'Was mache ich heute Abend?', '', 480, 12, 0, '', '', '', '', '', '', 5, '10.1', 'Corran', 106, 1305120628),
(35, 'Robotik', '', 30000, 3, 0, '', '', '', '', '', '', 7, '11.74', 'Corran', 25500, 1307038267),
(36, 'Scout', '', 9600, 13, 0, 'Eisen: 150 Stahl: 375 chem. Elemente: 375 Energie: 750', '', '', '', '', '', 7, '10.1', 'Corran', 4608, 1317374897),
(37, 'Eistransport', '', 9600, 10, 0, 'Eis: 500', '', '', '', '', '', 7, '11.74', 'Corran', 3168, 1317374897),
(361, 'Steigerung der chem. Elemente Transfergeschwindigkeit auf Basen', '', 5000, 3, 0, '', '', '', '', '', '', 22, '11.74', 'Shar', 0, 0),
(39, 'Lionheart (Korvette)', '', 15000, 13, 0, 'Stahl: 2.325 chem. Elemente: 1.275 Energie: 1.200', '', '', '', '', '', 7, '11.74', 'Corran', 7200, 1317374897),
(40, 'Slayer (Zerstörer)', '', 50000, 13, 0, 'Stahl: 3.000 chem. Elemente: 1.350 Energie: 3.000', '', '', '', '', '', 7, '11.74', 'Corran', 21501, 1324415304),
(42, 'interstellare Kommunikation', '', 12000, 10, 0, '', 'Kolonisationszentrum Stufe 3<br />\nHandelsgesellschaft Stufe 3<br />\nHandelsgesellschaft Stufe 4', '', '', '', '', 7, '11.74', 'Corran', 9120, 1309170792),
(318, 'Artefaktsammelbasis Beta', '', 5000000, 13, 0, ': 225000 : 262500 : 42000 : 135000 : 150000', '', '', '', '', '', 29, '10.1', 'Shar', 4250000, 1321739681),
(43, 'Sonde X11', '', 1920, 13, 0, 'Stahl: 15 chem. Elemente: 38 Energie: 60', '', '', '', '', '', 7, '11.74', 'Corran', 423, 1305120628),
(44, 'Interstellares Vordringen', 'Seit Hinz und Kunz mit Orbitalhüpfern Wettrennen zum Mond und wieder zurück machen dürfen, laufen die Naturschützer Sturm. Das Ozonloch wird größer, die Mondfliege ist am Rand des Aussterbens, Hasen werden unfruchtbar. Laber Rharbarber. Jetzt haben sie Geld gesammelt und eine neue Arche gebaut, um &quot;ein neues Paradies&quot; zu suchen. Sollen sie halt, die Spinner.', 60000, 6, 0, '', '', '', '', '', '', 10, '11.74', 'Corran', 33000, 1309462116),
(45, 'semiintelligente KI Technik', '', 70000, 8, 0, '', '', '', '', '', '', 7, '11.74', 'Corran', 28701, 1309170711),
(46, 'Crux (Systemtransporter Kolonisten)', '', 16000, 13, 0, 'Stahl: 1.800 chem. Elemente: 750 Wasser: 225 Energie: 1.125 Credits: 75', '', '', '', '', '', 8, '11.74', 'Corran', 7680, 1317374630),
(47, 'Lurch (Systemtransporter Klasse 2)', '', 16000, 13, 0, 'Stahl: 1.875 chem. Elemente: 450 Eis: 75 Wasser: 75 Energie: 1.200 Credits: 113', '', '', '', '', '', 8, '11.74', 'Corran', 5280, 1317374630),
(49, 'Kolonie auf felsigen Planeten', '', 32000, 11, 0, '', '', '', '', '', '', 8, '11.74', 'Corran', 13121, 1309170711),
(50, 'verbesserte Bautechnik', '', 35000, 3, 0, '', '', '', '', '', '', 8, '11.74', 'Corran', 14000, 1309462116),
(51, 'Ionenantrieb', '', 72000, 10, 0, 'chem. Elemente: 5.000', '', '', '', '', '', 12, '11.74', 'Corran', 34560, 1317374897),
(364, 'Du bist ruhig!', '', 1136, 12, 0, '', '', '', '', '', '', 31, '11.74', 'Shar', 495, 1305120628),
(53, 'Gorgol 9 (Hyperraumtransporter Klasse1)', '', 60000, 13, 0, 'Stahl: 3.450 chem. Elemente: 1.125 Energie: 3.000', '', '', '', '', '', 17, '11.74', 'Corran', 34800, 1317374897),
(54, 'Kolpor (Hyperraumtransporter Kolonisten)', '', 75000, 13, 0, 'Stahl: 3.000 chem. Elemente: 750 Energie: 3.000', '', '', '', '', '', 17, '11.74', 'Corran', 54750, 1317374803),
(55, 'Genetik', 'Spritzen, Strahlung, Pillen - Schnee von gestern! Jetzt gehts richtig zur Sache, wir können uns alles basteln, was wir wollen. Und dieser Dr. Moreau macht einen wirklich seriösen Eindruck. Wie schön.', 1250000, 6, 0, 'Credits: 100.000', '', '', '', '', 'Meister des Buddelns<br />\nMeister der Umwandlung<br />\nBau auf Bau auf Bau auf Bau auf<br />\nIch steh auf billig<br />\nIch bau gerne Schiffe<br />\nJetzt neu, Jetzt BILLIG, Schiffe!!<br />\nDont jump around und ich bin platt<br />\nIch will noch mehr Zeit<br />\nUnd noch viel mehr Zeit<br />\nWissen ist Nacht<br />\nSchifflebauen The Next Gen<br />\nSchifflebauen V2<br />\nSchifflebauen (mist mir gehen die Namen aus)<br />\nSchifflebauen Quick and Dirty<br />\nBaugruppe Stroand<br />\nBaugruppe Loook<br />\nBaugruppe Resorasch<br />\nBaugruppe Guck<br />\nBaugruppe Loager<br />\nBaugruppe Bedfor<br />\nBaugruppe Skon<br />\nBaugruppe Bumms<br />\nEinführung von Montageanleitungen<br />\nIKEA Club Card<br />\nIKEA Master Club Card<br />\nIKEA Platin Goldkettchen Club Card<br />\nSonnenschein, ohh du mein Sonnenschein<br />\nänderfix<br />\nArtefakte sind mein Leben', 12, '11.74', 'Corran', 750000, 1313658803),
(56, 'Terminus Sonde', '', 145000, 13, 0, 'Stahl: 24 chem. Elemente: 75 Eis: 15 Energie: 60', '', '', '', '', '', 13, '11.74', 'Corran', 84100, 1317374630),
(317, 'Artefaktsammelbasis Alpha', '', 150000, 13, 0, 'Eisen: 37.500 Stahl: 30.750 VV4A: 1.500 chem. Elemente: 22.500 Energie: 37.500', '', '', '', '', '', 14, '11.74', 'Shar', 106500, 1321223568),
(57, 'Kampfbasis Alpha', '', 50000, 13, 0, 'Eisen: 15.000 Stahl: 11.250 VV4A: 1.125 chem. Elemente: 8.850 Energie: 7.500', '', '', '', '', '', 17, '11.74', 'Corran', 42000, 1313658714),
(58, 'Bürokratie', '', 48000, 9, 0, '', 'Kolonisationszentrum Stufe 1<br />\nKolonisationszentrum Stufe 2', '', '', '', '', 9, '11.74', 'Corran', 19680, 1309170711),
(59, 'Eisbär (Hyperraumtransporter Klasse 2)', '', 50000, 13, 0, 'Stahl: 3.750 chem. Elemente: 1.875 Eis: 150 Wasser: 150 Energie: 1.875', '', '', '', '', '', 17, '11.74', 'Corran', 35000, 1313658803),
(311, 'große Schiffsrümpfe', '', 60000, 10, 0, '', '', '', '', '', '', 16, '11.74', 'Shar', 37800, 1310641703),
(60, 'Fängt mal wer den Müll wieder ein? Er rennt schon wieder weg', '', 30000, 12, 0, 'Eisen: 1 Stahl: 1 VV4A: 1 chem. Elemente: 1 Eis: 1 Wasser: 1 Energie: 1 Credits: 1.000', '', '', '', '', '', 9, '11.74', 'Corran', 9900, 1317374803),
(61, 'Bankwesen', '', 50000, 9, 0, 'Credits: 20.000', '', '', '', '', '', 10, '11.74', 'Corran', 24000, 1317374897),
(62, 'Recycling', 'Schon wieder so ne neue Partei, die angeblich für die Rechte der Wälder und Seen und wat weiß ich alles eintritt. Haben die Rechte? Naja nach H.M.Eiserer schon lange nicht mehr. Aber lieber sie umhegen und umpflegen ihren Wald und alles drumherum, statt mir auf die Nerven zu gehen und mir dauernd vorzuwerfen, die Gesellschaft würde zuviel von den natürlichen Rohstoffen verbrauchen. Alles Humbug.', 60000, 3, 0, '', '', '', '', '', '', 10, '11.74', 'Corran', 28800, 1317374897),
(63, 'chemische Ungezieferbekämpfung', 'Ratten, Ratten, nichts als Ratten, ein paar Krabbeltiere, ein paar Kriechtiere... Langsam kommen die kleinen Chemiefabriken nicht mehr mit der lustigen Blubbermasse nach. Man sollte wirklich mal über ein paar größere nachdenken.', 75000, 2, 0, 'chem. Elemente: 5000', '', '', '', '', '', 10, '11.74', 'Corran', 40500, 1309884334),
(64, 'INS-03A (interstellares Kolonieschiff)', '', 100000, 13, 0, 'Eisen: 150.000 Stahl: 37.500 VV4A: 15.000 chem. Elemente: 37.500 Eis: 7.500 Wasser: 7.500 Energie: 45.000 Credits: 15.000', '', '', '', '', '', 17, '11.74', 'Corran', 86000, 1312708057),
(316, 'KISS-01 (Systemkolonieschiff)', '', 40000, 13, 0, 'Eisen: 45.000 Stahl: 22.500 chem. Elemente: 22.500 Eis: 1.500 Wasser: 1.500 Energie: 37.500 Credits: 7.500', '', '', '', '', '', 9, '11.74', 'Shar', 20000, 1313658714),
(65, 'orbitale Verteidigung', '', 3000, 5, 0, '', '', '', 'Raketensatellit', '', '', 6, '11.74', 'Corran', 1230, 1308772813),
(66, 'Hyperraumtheorien', '', 30000, 10, 0, '', '', '', '', '', '', 11, '11.74', 'Corran', 19800, 1309171160),
(67, 'chemischer Hyperraumantrieb', '', 48000, 10, 0, '', '', '', '', '', '', 12, '11.74', 'Corran', 25920, 1309884334),
(68, 'Gibt es auch was anderes zu essen?', '', 30000, 12, 0, '', '', '', '', '', '', 12, '11.74', 'Corran', 16500, 1309462172),
(69, 'Hyperraumscanner', '', 12000, 10, 0, '', '', '', '', '', '', 12, '11.74', 'Corran', 7680, 1309884334),
(70, 'Gausskanone', '', 30000, 5, 0, '', '', '', 'Gausskanonensatellit<br />\nSD01 Gatling', '', '', 11, '11.74', 'Corran', 22500, 1309462300),
(71, 'Cryogenic', 'Wenn man irgendwo neue Zeltlager anlegen will, muss man schon &#039;ne Menge Leute mitschicken. Aber wenn man zu viele Menschen/Fellknäuel/Glibbermasse auf kleinem Raum zusammenpfercht, fressen die sich gegenseitig auf. Und das läuft dann der ganzen Aktion zuwider. Etwas Kälte wird den Bewegungsdrang bestimmt bremsen. Obwohl... Wozu eigentlich nur halbe Sachen machen?', 72000, 3, 0, '', '', '', '', '', '', 1, '11.74', 'Corran', 34560, 1317374897),
(72, 'Hitman (Kreuzer)', '', 100000, 13, 0, 'Eisen: 6.000 Stahl: 7.500 VV4A: 2.250 chem. Elemente: 3.000 Energie: 6.000', '', '', '', '', '', 17, '11.74', 'Corran', 53000, 1324415304),
(73, 'Sheep (Jäger)', '', 25000, 13, 0, 'Eisen: 150 Stahl: 600 VV4A: 75 chem. Elemente: 675 Energie: 900', '', '', '', '', '', 18, '11.74', 'Corran', 17500, 1313658500),
(74, 'perfekte Waffeln dank Waffeleisen', 'Der Motor der Gesellschaft, die Antriebskraft der Wissenschaft: Eine deftige Brotzeit. Da Brot aber lange nicht so gut schmeckt, vor allem nicht mit Kirschen und Sahne, sind Waffeln viel toller. Und noch bessere Waffeln machen uns unbesiegbar, muahahaha. Und dick.', 40000, 3, 0, 'chem. Elemente: 1.500 Wasser: 10', '', '', '', '', '', 13, '11.74', 'Corran', 21600, 1309884201),
(75, 'Was zum Teufel ist das hier auf meinem Teller?', '', 120000, 12, 0, '', '', '', '', '', '', 5, '10.1', 'Corran', 44400, 1305120628),
(319, 'Irgendwer sollte mal wieder aufräumen. Ach, ich zieh einfach um. Wohin bloß?', '', 125000, 12, 0, '', '', '', '', '', '', 0, '10.1', 'Shar', 46250, 1305120628),
(76, 'unlogische Informationsspeicherung', '', 40000, 10, 0, '', '', '', '', '', '', 11, '11.74', 'Corran', 21600, 1309884334),
(77, 'Antrag auf imperiale Besteuerung', 'Keine Sorge. Bis die den Antrag bearbeitet haben, ist die Runde zu Ende.', 60000, 9, 0, 'Credits: 10.000', '', '', '', '', '', 8, '11.74', 'Corran', 45000, 1309462050),
(362, 'Steigerung der Lagerkapazität von Basen', '', 5000, 3, 0, '', '', '', '', '', '', 22, '11.74', 'Shar', 0, 0),
(79, 'Aufnahme in die Zivilisierten Welten', 'Es ist nicht leicht, in den Club der ziliversierten Welten reinzukommen, doch unsere Wissenschaftler haben gemeint, daß es a) zivilisiert heißt und b) die Chancen ganz ordentlich sind. Wer 421 Fernsehsender (davon 151 TV-Shops und immerhin 87 Sender für Erwachsene), Modezeitschriften für Hunde, Kaugummis mit Bratwurstgeschmack und diverse enteignete Naturvölker vorweisen kann, der ist gut dabei. Aber wir sollten noch ein paar Tierarten ausrotten. Sicher ist sicher.', 100000, 6, 0, '', '', '', '', '', '', 9, '11.74', 'Corran', 0, 0),
(324, 'Statuen', '', 10000, 3, 0, 'Eisen: 20 Stahl: 20 VV4A: 10 Credits: 100', '', '', '', '', '', 10, '11.74', 'Blutmade', 7000, 1313658583),
(80, 'Miniaturisierung', '', 100000, 4, 0, '', '', '', '', '', '', 17, '11.74', 'Corran', 73000, 1310641779),
(81, 'Ballistische Panzerung', 'Ich hab ein neues Hobby: Mit einem Bolzenschussgerät kleine, verängstigte Forscher jagen. Leider haben die auch schon wieder ein Mittel dagegen entwickelt. *grml*', 60000, 5, 0, 'VV4A: 100', '', '', '', '', '', 15, '11.74', 'Corran', 31800, 1312046236),
(82, 'planetare Flugmanöver', '', 70000, 10, 0, '', '', '', '', '', '', 15, '11.74', 'Corran', 33600, 1317374897),
(84, 'Das Wissen der 10000 Welten', 'Was macht die Biene mit dem Blümchen? Was heißt ?moep? auf Denglish? Wie gewinne ich mehr Energie? Fragen, die die Welt nicht braucht, oder doch?', 135000, 7, 0, '', 'Flottenkontrollzentrum Stufe 3<br />\nFlottenkontrollzentrum Stufe 4', '', '', '', '', 10, '11.74', 'Corran', 85050, 1310641703),
(86, 'Wieso leuchtet Katzenurin im Dunkeln?', '', 135000, 12, 0, '', '', '', '', '', '', 10, '11.74', 'Corran', 98550, 1310641703),
(87, 'Atombomber', '', 40000, 13, 0, 'Eisen: 1.500 Stahl: 2.625 VV4A: 975 chem. Elemente: 1.125 Energie: 750', '', '', '', '', '', 18, '11.74', 'Corran', 18800, 1305120628),
(88, 'orbitale Dockingsysteme', '', 72000, 10, 0, '', '', '', '', '', '', 16, '11.74', 'Corran', 50400, 1313658714),
(89, 'Universalübersetzer', 'Soso, Sie wollen ein Konto eröffnen, mit Schiffen und sonstigem Zeugs handeln? Die Wurzelzwerge haben Sie wohl wieder über&#039;s Ohr gehauen? Selbst schuld, muahararar! Dann füllen Sie mal dieses Formular aus, in 5facher Ausfertigung und das Ganze dann bitte auch noch auf Denglisch und Moepisch.', 75000, 7, 0, '', '', '', '', '', '', 14, '11.74', 'Corran', 47250, 1312046487),
(90, 'Fragen an Dr. Sommer', 'Das Runde muss ja eigentlich ins Eckige. Aber das klappt irgendwie alles gar nicht und überhaupt. Mal schauen ob die tollen Hefte und Filmchen weiterhelfen.', 20000, 12, 0, '', '', '', '', '', '', 11, '11.74', 'Corran', 14600, 1310641742),
(94, 'kleine Lebenserhaltungssysteme', '', 85000, 10, 0, '', '', '', '', '', '', 17, '11.74', 'Corran', 49300, 1317374897),
(95, 'Verbesserte Beobachtung', '', 100000, 4, 0, '', '', '', '', '', '', 11, '11.74', 'Corran', 53000, 1324415304),
(96, 'Lasertechnik', '', 150000, 4, 0, '', '', '', 'LaserSat', '', '', 11, '11.74', 'Corran', 75000, 1313658803),
(98, 'Neue Verbundstoffe', 'Juhu, endlich auf dem Mond. Aber hier fliegt ja alles weg! Der ist also doch nicht aus Käse, wie die doofen Wissenschaftler gesagt haben. Wenn wir hier was bauen wollen brauchen wir definitiv bessere Klebstoffe. *kleb*', 200000, 2, 0, 'Eisen: 10.000 Stahl: 5.000 VV4A: 5.000', '', '', '', '', '', 11, '11.74', 'Corran', 146000, 1310641742),
(99, 'Photonenantrieb', '', 150000, 4, 0, '', '', '', '', '', '', 11, '11.74', 'Corran', 87000, 1317374897),
(100, 'Bürokratisierung der Bürokraten', '', 500000, 9, 0, 'Credits: 25.000', 'Kolonisationszentrum Stufe 4<br />\nKolonisationszentrum Stufe 5<br />\nKolonisationszentrum Stufe 6', '', '', '', '', 15, '11.74', 'Corran', 250000, 1313658803),
(102, 'Kampfbasis Beta', '', 585000, 13, 0, 'Eisen: 75.000 Stahl: 68.250 VV4A: 8.625 chem. Elemente: 45.000 Energie: 37.500', '', '', '', '', '', 21, '11.74', 'Corran', 585000, 1317374803),
(103, 'Shark (Jäger)', '', 110000, 13, 0, 'Eisen: 225 Stahl: 750 VV4A: 225 chem. Elemente: 825 Energie: 1.200', '', '', '', '', '', 19, '11.74', 'Corran', 73700, 1320590801),
(104, 'energetische Panzerung', '', 175000, 4, 0, 'VV4A: 5.000', '', '', '', '', '', 12, '11.74', 'Corran', 119000, 1317374897),
(105, 'Plasmalaser', '', 150000, 5, 0, '', '', '', 'SDI Plasmalaser', '', '', 17, '10.1', 'Corran', 110700, 1312708109),
(330, 'Wie schütze ich mich vor dem wilden Fellknäuel?', '', 616500, 12, 0, '', '', '', '', '', '', 21, '11.74', 'Corran', 363735, 1316358978),
(106, 'alternative Energiekonzepte', '', 50000, 4, 0, 'chem. Elemente: 25.000', '', '', '', '', '', 18, '11.74', 'Corran', 24000, 1317374803),
(107, 'Robotermining', '', 150000, 3, 0, '', 'Robominerzentrale Stufe 1<br />\nRobominerzentrale Stufe 2<br />\nRobominerzentrale Stufe 3', '', '', '', '', 12, '11.74', 'Corran', 94500, 1312046487),
(108, 'Zero G Forschung', '', 300000, 4, 0, '', '', '', '', '', '', 15, '11.74', 'Corran', 219000, 1311327412),
(110, 'Kolonie auf Eisplaneten', '', 500000, 11, 0, 'Eis: 25.000', '', '', '', '', '', 11, '11.74', 'Corran', 325000, 1321739681),
(111, 'Hyperraumphotonenantrieb', '', 200000, 4, 0, '', '', '', '', '', '', 12, '11.74', 'Corran', 140000, 1313658714),
(112, 'Imperiale Gedanken', 'Mein Psychiater meinte gestern, ich wäre etwas Besonderes (kurz bevor er aus dem Fenster gesprungen ist). Das fand ich super! Ich hab mich so sehr darüber gefreut, dass ich spontan beschlossen habe alle meine Schiffe starten zu lassen um auch all meinen Nachbarn klarzumachen, wie besonders ich bin!', 450000, 6, 0, '', '', '', '', '', '', 16, '11.74', 'Corran', 324000, 1312708020),
(113, 'Stormbringer', '', 1800000, 13, 0, 'Eisen: 3.000 Stahl: 3.150 VV4A: 1.800 chem. Elemente: 1.500 Energie: 1.500', '', '', '', '', '', 21, '10.1', 'Corran', 939600, 1333007241),
(115, 'Hunter (Korvette)', '', 60000, 13, 0, 'Stahl: 2.775 VV4A: 750 chem. Elemente: 1.500 Energie: 1.500', '', '', '', '', '', 13, '11.74', 'Corran', 60000, 1312993660),
(116, 'Vendeta (Zerstörer)', '', 120000, 13, 0, 'Stahl: 4.500 VV4A: 1.350 chem. Elemente: 1.500 Energie: 4.200', '', '', '', '', '', 13, '11.74', 'Corran', 56400, 1303681165),
(118, 'Genetik V2', 'Das diese Forscher schon wieder so seltsam gucken, wie der letzte... Egal, hauptsache die kriegen das Fell wieder weg.', 8500000, 6, 0, '', '', '', '', '', 'Meister der kleinen Werften<br />\nMeister der mittleren Werften<br />\nMeister der großen Werften<br />\nIch will Spaß und geb Gas<br />\nSchildpfuscherei<br />\nSchifflebauen V43<br />\nSchifflebauen V44<br />\nSchifflebauen V45<br />\nSchildeSchildeSchilde<br />\nHardSchilds<br />\nIch bin Platt V2.0', 28, '11.74', 'Corran', 18700000, 1317374630),
(119, 'Wiedergewinnung von 0 Genetikpunkt(en)(mittels eines Pümpels)', 'Hiermit kann man verlorene Genetikpunkte wiedergewinnen. Man kann die Forschung sooft ausführen wie man will, man braucht nur mindestens einen verlorenen Genetikpunkt. Es gibt allerdings was zu beachten, die Forschungskosten steigen mit der Anzahl der besiedelten Planeten und wie oft man die Forschung bereits erforscht hat.', 1, 6, 0, '', '', '', '', '', '', 13, '11.74', 'Corran', 0, 0),
(120, 'Roboterminer V1', '', 75000, 13, 0, 'Eisen: 15.000 Stahl: 11.250 VV4A: 1.125 chem. Elemente: 8.850 Energie: 15.000', '', '', '', '', '', 13, '11.74', 'Corran', 47250, 1324415304),
(121, 'X12 (Carrier)', '', 1350000, 13, 0, 'Eisen: 30.000 Stahl: 8.700 VV4A: 6.848 chem. Elemente: 12.000 Energie: 52.500 Credits: 2.250', '', '', '', '', '', 23, '11.74', 'Corran', 855000, 1305120628),
(122, 'Big Daddy (Schlachtschiff)', '', 250000, 13, 0, 'Eisen: 12.000 Stahl: 15.000 VV4A: 7.350 chem. Elemente: 11.250 Energie: 18.000', '', '', '', '', '', 18, '11.74', 'Corran', 170000, 1317374672),
(123, 'Waschbär (Hyperraumtransporter Klasse 2)', '', 706500, 13, 0, 'Stahl: 9.000 VV4A: 1.500 chem. Elemente: 9.000 Eis: 150 Wasser: 150 Energie: 4.500', '', '', '', '', '', 21, '11.74', 'Corran', 459225, 1321739681),
(124, 'Kamel Z-98 (Hyperraumtransporter Klasse 1)', '', 699880, 13, 0, 'Stahl: 9.750 VV4A: 1.800 chem. Elemente: 5.325 Energie: 7.500', '', '', '', '', '', 19, '11.74', 'Corran', 475919, 1317374803),
(125, 'Asteroidenmining', '', 364000, 9, 0, '', '', '', '', '', '', 13, '11.74', 'Corran', 200201, 1321739681),
(360, 'Biosphären', '', 250000, 11, 0, '', '', '', '', '', '', 19, '11.74', 'Shar', 92500, 1305120628),
(127, 'Terraformer Alpha', '', 25000, 13, 0, 'Stahl: 2.250 chem. Elemente: 2.250 Eis: 150 Wasser: 150 Energie: 2.250 Credits: 2.250', '', '', '', '', '', 10, '11.74', 'Corran', 13250, 1324415304),
(128, '24h Pizzadienst', '', 150000, 9, 0, '', '', '', '', '', '', 17, '10.1', 'Corran', 81000, 1313658583),
(129, 'Manta (Jäger)', '', 895000, 13, 0, 'Eisen: 450 Stahl: 1.050 VV4A: 375 chem. Elemente: 1.050 Energie: 1.650', '', '', '', '', '', 22, '10.1', 'Corran', 386640, 1333007241),
(130, 'Victim', '', 855000, 13, 0, 'Stahl: 4.650 VV4A: 1.125 chem. Elemente: 3.150 Energie: 2.250', '', '', '', '', '', 23, '11.74', 'Corran', 855000, 1317374803),
(358, 'Schiffhandel', '', 110000, 9, 0, 'Credits: 20.000', '', '', '', '', '', 8, '11.74', 'Shar', 58300, 1312046102),
(135, 'Alpha Class Schilde', '', 900000, 5, 0, '', '', '', '', '', '', 22, '11.74', 'Corran', 531000, 1316359141),
(137, 'Wir brauchen extra Käse', '', 945000, 2, 0, 'chem. Elemente: 50.000 Credits: 35.000', '', '', '', '', '', 19, '10.1', 'Corran', 382725, 1321739681),
(138, 'Gegensätze ziehen sich an ?', '', 75000, 4, 0, '', '', '', '', '', '', 19, '10.1', 'Corran', 47250, 1313658533),
(334, 'Warum bleibt der Käse am Deckel kleben?', '', 360000, 12, 0, '', '', '', '', '', '', 18, '10.1', 'Corran', 226800, 1313658714),
(139, 'Magnetische Beschleunigung', '', 500000, 4, 0, '', '', '', '', '', '', 19, '10.1', 'Corran', 265500, 1316359141),
(140, 'Fusionstechnologie', '', 675000, 4, 0, '', 'Flottenkontrollzentrum Stufe 5<br />\nFlottenkontrollzentrum Stufe 6', '', '', '', '', 20, '11.74', 'Corran', 567000, 1313658760),
(142, 'Die Macht der Verführung', '', 640000, 7, 0, '', '', '', '', '', '', 22, '10.1', 'Corran', 362880, 1324415304),
(144, 'Impuls Vektor Triebwerk', '', 780000, 3, 0, '', '', '', '', '', '', 20, '10.1', 'Corran', 512460, 1317374897),
(145, 'Pulslasertechnik', '', 550000, 5, 0, '', '', '', 'PulslaserSat', '', '', 21, '10.1', 'Corran', 287100, 1317374712),
(146, 'Succubus (Kreuzer)', '', 1530000, 13, 0, 'Eisen: 13.500 Stahl: 23.250 VV4A: 9.825 chem. Elemente: 18.000 Energie: 15.000', '', '', '', '', '', 23, '11.74', 'Corran', 969000, 1303681165),
(147, 'Die Macht des Seins', 'Sein oder nicht sein, das ist hier die frage. Bin ich oder weiß ich das ich nichts bin, bzw wenn ich weiß das ich bin, weiß ich dann etwas? Fragen über fragen. Aber wenn wir eins wissen, ist es das wir nichts wissen. Jedenfalls wird das so behauptet, daher sollten wir wohl flugs was sein, um zu wissen, das wir, öhm, das wir wenigstens was besseres sind als die anderen. haha', 850000, 6, 0, '', '', '', '', '', '', 23, '11.74', 'Corran', 586500, 1316359061),
(148, 'verbesserte Massdrivertechnologie', '', 1300000, 4, 0, '', 'Massdriver Stufe 2', '', '', '', '', 24, '10.1', 'Corran', 481000, 1305120628),
(149, 'ökologische Raumschiffantriebe', 'Reduziert den Verbrauch der Schiffe bei chemischen Elementen um 50%', 4500000, 10, 0, 'chem. Elemente: 250.000', '', '', '', '', '', 24, '10.1', 'Corran', 3021750, 1321739681),
(150, 'verbesserte Jägerstarttechnologie', '', 350000, 10, 0, '', '', '', '', '', '', 21, '10.1', 'Corran', 198450, 1324415304),
(151, 'Fast Tracking Systeme', '', 560000, 10, 0, '', '', '', '', '', '', 21, '10.1', 'Corran', 267120, 1324415304),
(355, 'Mikrowellen', '', 25000, 4, 0, '', '', '', '', '', '', 7, '11.74', 'Shar', 5500, 1305120628),
(356, 'Mir ist langweilig, was nun?', '', 807500, 12, 0, '', '', '', '', '', '', 24, '11.74', 'Shar', 549100, 1317374712),
(357, 'Out of Ice?', '', 80000, 3, 0, '', '', '', '', '', '', 1, '10.1', 'Shar', 29600, 1305120628),
(154, 'Nova', '', 13000000, 13, 0, 'Eisen: 4.500 Stahl: 4.200 VV4A: 2.700 chem. Elemente: 2.250 Energie: 2.250', '', '', '', '', '', 39, '10.1', 'Corran', 7020000, 1333007241),
(155, 'Nepomuk', '', 20000000, 13, 0, 'Eisen: 6.000 Stahl: 1.500 VV4A: 4.875 chem. Elemente: 5.250 Energie: 6.000', '', '', '', '', '', 42, '10.1', 'Corran', 21600000, 1303673681),
(156, 'Photonenblaster', 'Wenn eine hochtechnologisierte Barbarei eine simple Lampe entwickelt und diese freudig &quot;Photonenblaster&quot; nennt - DAS ist Selbstbewusstsein!', 293250, 5, 0, '', '', '', '', '', '', 24, '11.74', 'Corran', 170085, 1317374712),
(158, 'Leben auf ganz kleinem Raum', '', 850000, 11, 0, '', 'Kolonisationszentrum Stufe 7<br />\nKolonisationszentrum Stufe 8', '', '', '', '', 21, '10.1', 'Corran', 520200, 1317374897),
(359, 'Systrans (Systemtransporter Klasse 1)', '', 9600, 13, 0, 'Stahl: 1.500 chem. Elemente: 750 Energie: 750', '', '', '', '', '', 7, '11.74', 'Shar', 3552, 1312708205),
(161, 'Kolonie auf Gasgiganten', '', 2000000, 11, 0, '', '', '', '', '', '', 28, '10.1', 'Corran', 495001, 1321739681),
(345, 'Mule (Tarnträger)', '', 1400000, 13, 0, 'Eisen: 52.500 Stahl: 27.000 VV4A: 13.500 chem. Elemente: 18.000 Energie: 60.000 Credits: 3.000', '', '', '', '', '', 35, '10.1', 'Corran', 938000, 1303681107),
(162, 'Kolonie auf Asteroiden', '', 2000000, 11, 0, '', '', '', '', '', '', 28, '10.1', 'Corran', 495001, 1321739681),
(344, 'Wie benutze ich eine Toilette bei Schwerelosigkeit?', '', 555555, 12, 0, '', '', '', '', '', '', 27, '10.1', 'Corran', 670556, 1317374630),
(163, 'Wir brauchen mehr Partys', '', 1300000, 12, 0, ': 75.400', '', '', '', '', '', 25, '10.1', 'Corran', 806650, 1317374897),
(164, 'Verstehen der Zusammenhänge', 'Meine Forscher sagten mir, sie verstehen endlich alle Zusammenhänge. Sie müssen nur noch herausfinden was die Antwort auf die Frage nach dem Leben, dem Universum und dem ganzen Rest ist.', 750000, 6, 0, '', '', '', '', '', '', 34, '11.74', 'Corran', 540000, 1320590801),
(166, 'Tarnung ist alles', '', 2000000, 4, 0, '', '', '', '', '', '', 25, '10.1', 'Corran', 1156000, 1324415304),
(343, 'Tarnung von großen Sachen mittels einer Decke', '', 1800000, 4, 0, '', '', '', '', '', '', 34, '10.1', 'Corran', 1530000, 1321739681),
(167, 'Wir sind blöd', '', 85, 12, 0, '', '', '', '', '', '', 26, '11.74', 'Corran', 37, 1305120628),
(169, 'Nightcrawler (Tarnjäger)', '', 450000, 13, 0, 'Eisen: 2.250 Stahl: 1.950 VV4A: 375 chem. Elemente: 1.500 Energie: 1.500', '', '', '', '', '', 26, '10.1', 'Corran', 382500, 1321739681),
(170, 'Eraser (Tarnkorvette)', '', 650000, 13, 0, 'Stahl: 3.450 VV4A: 1.305 chem. Elemente: 6.000 Energie: 4.725', '', '', '', '', '', 26, '10.1', 'Corran', 552500, 1321739681),
(172, 'Schöner bomben mit Napalmin', '', 8500000, 5, 0, '', '', '', '', '', '', 26, '11.74', 'Corran', 0, 0),
(173, 'Wieso ist der Alk schon wieder alle ?', 'War das eine Party, lauter blonde Häschen am Abend und einen dicken Kater am Morgen. Wir sollten schnellstmöglich für Nachschub sorgen, der Abend kommt schnell *G*<br />\nzuerst erforscht von Lekket am 27.07.2006 07:05', 500000, 12, 5000, '', '', '', '', '', '', 26, '0.696', 'Corran', 276250, 1321739681),
(174, '10001 lustige Partygründe', '', 1000001, 12, 0, 'chem. Elemente: 50.505', '', '', '', '', '', 26, '10.1', 'Corran', 820001, 1317374897),
(178, 'Du vielleicht, ich nicht', '', 21250, 12, 0, '', '', '', '', '', '', 27, '11.74', 'Corran', 9250, 1305120628),
(179, 'Stimmt gar nicht', '', 850, 12, 0, '', '', '', '', '', '', 27, '11.74', 'Corran', 370, 1305120628),
(180, 'Stimmt wohl', '', 8395, 12, 0, '', '', '', '', '', '', 28, '11.74', 'Corran', 3655, 1305120628),
(183, 'Sonde X13', '', 850000, 13, 0, 'Stahl: 38 VV4A: 8 chem. Elemente: 150 Energie: 150', '', '', '', '', '', 29, '11.74', 'Corran', 578000, 1324415304),
(185, 'Du bist doof', '', 9427, 12, 0, '', '', '', '', '', '', 29, '11.74', 'Corran', 4104, 1305120628),
(186, 'Ihr seid doch blöd euch so zu streiten', '', 100, 12, 0, '', '', '', '', '', '', 29, '11.74', 'Corran', 37, 1305120628),
(187, 'Selber doof', '', 4891, 12, 0, '', '', '', '', '', '', 30, '11.74', 'Corran', 7965, 1316359100),
(188, 'Von dir lasse ich mir nix sagen', '', 10, 12, 0, '', '', '', '', '', '', 30, '11.74', 'Corran', 5, 1305120628),
(189, 'Selber blöd', '', 12439, 12, 0, '', '', '', '', '', '', 30, '11.74', 'Corran', 0, 0),
(191, 'Wir brauchen bessere Forscher', '', 1700000, 9, 0, '', '', '', '', '', '', 33, '11.74', 'Corran', 1829881, 1317374759),
(192, 'Du bist doch nur inkompetent', '', 19550, 12, 0, '', '', '', '', '', '', 31, '11.74', 'Corran', 33275, 1316359100),
(363, 'Pft!', '', 50000, 12, 0, '', '', '', '', '', '', 32, '11.74', 'Shar', 90850, 1316358940),
(195, 'Widowmaker (Tarnzerstörer)', '', 970000, 13, 0, 'Stahl: 5.400 VV4A: 1.500 chem. Elemente: 4.500 Energie: 5.025', '', '', '', '', '', 35, '10.1', 'Corran', 601400, 1303681107),
(197, 'Betrachten von Fliegen', '', 666279, 4, 0, 'chem. Elemente: 50.000', '', '', '', '', '', 36, '10.1', 'Corran', 353128, 1324415304),
(198, 'Neue Ideen braucht das Land ähm Universum', '', 1200000, 7, 0, 'Credits: 100.000', '', '', '', '', '', 36, '10.1', 'Corran', 444000, 1305120628),
(199, 'Follow the pink rabbit', '', 3139156, 6, 0, '', '', '', '', '', '', 36, '11.74', 'Corran', 1977669, 1324415304),
(200, 'Silent Sorrow (Tarnkreuzer)', '', 2400000, 13, 0, 'Eisen: 7.500 Stahl: 6.750 VV4A: 1.275 chem. Elemente: 41.250 Energie: 60.000', '', '', '', '', '', 36, '10.1', 'Corran', 1840321, 1303681107),
(201, 'Warum fliegen Fliegen immer um Blumen rum ?', '', 1500012, 12, 0, '', '', '', '', '', '', 37, '10.1', 'Corran', 555005, 1305120628),
(202, 'Warum fliegen Fliegen um einen Monitor wo Essensreste drumherum stehen ?', '', 750000, 12, 0, 'chem. Elemente: 1.500.000', '', '', '', '', '', 37, '10.1', 'Corran', 352500, 1305120628),
(203, 'In die Disco', '', 2900000, 12, 0, '', '', '', '', '', '', 37, '10.1', 'Corran', 1087500, 1329735501),
(204, 'Löffel, überall Löffel', '', 1600000, 4, 0, '', '', '', '', '', '', 37, '10.1', 'Corran', 592000, 1305120628),
(205, 'Nimm die blaue Pille', '', 1650000, 2, 0, 'chem. Elemente: 500.000', '', '', '', '', '', 37, '11.74', 'Corran', 814000, 1305120628),
(208, 'Burn Baby Burn', '', 4000000, 12, 40000, '', '', '', '', '', '', 38, '0.696', 'Corran', 1740000, 1333007241),
(209, 'Coole Accessoires', '', 4800000, 9, 0, '', '', '', '', '', '', 38, '10.1', 'Corran', 1776000, 1305120628),
(211, 'Sinn und Zweck von blauen Pillen', '', 2345678, 2, 0, 'chem. Elemente: 1.000.000', '', '', '', '', '', 38, '11.74', 'Corran', 1102469, 1305120628),
(212, 'Crush the Ice, Baby *hust*', '', 5560000, 2, 55600, '', '', '', '', '', '', 40, '0.696', 'Corran', 2613200, 1305120628),
(213, 'Alles klar', '', 5000000, 7, 0, '', '', '', '', '', '', 39, '10.1', 'Corran', 3100000, 1305120628),
(214, 'keine warme Milch mehr für die Forscher', '', 8000000, 8, 0, '', '', '', '', '', '', 38, '10.1', 'Corran', 3000000, 1329735501),
(215, 'Blumen sind Böse', '', 3000000, 7, 0, 'Wasser: 50.000', '', '', '', '', '', 38, '10.1', 'Corran', 1350000, 1329735501),
(217, 'Erfindung der Spül&quot;maschiene&quot;', '', 2000123, 9, 20001, '', '', '', '', '', '', 40, '0.696', 'Corran', 0, 0),
(219, 'Wir sind toll', '', 2000000, 3, 0, 'chem. Elemente: 2.000.000', '', '', '', '', '', 38, '10.1', 'Corran', 2625000, 1321223568),
(221, 'Bessere Buddelmethoden', '', 15000000, 3, 0, '', '', '', '', '', '', 42, '10.1', 'Corran', 7650000, 1333007241),
(222, '12 nützliche Verwendungsmöglichkeiten für Hämmer', '', 1500000, 3, 0, '', '', '', '', '', '', 39, '10.1', 'Corran', 705000, 1305120628),
(223, 'Einen geilen Schlitten', '', 5600000, 10, 0, '', '', '', '', '', '', 39, '10.1', 'Corran', 2436000, 1333007241),
(224, 'Icecrusher', '', 5000000, 9, 50000, '', '', '', '', '', '', 39, '0.696', 'Corran', 1800000, 1333007241),
(225, 'Die Nebelmaschiene', '', 4850000, 9, 48500, '', '', '', '', '', '', 1, '0.696', 'Corran', 0, 0),
(226, 'Die Discokugel', '', 1000000, 9, 0, '', '', '', '', '', '', 39, '10.1', 'Corran', 370000, 1305120628),
(374, 'Die Nebelmaschine', '', 4850000, 9, 0, '', '', '', '', '', '', 39, '10.1', 'Shar', 2291625, 1333007241),
(229, 'Müllentsorgung in einem Vulkan', '', 15000000, 2, 0, '', '', '', '', '', '', 40, '10.1', 'Corran', 6525000, 1333007241),
(230, 'Mystische Zeremonien', '', 10000000, 8, 0, '', '', '', '', '', '', 40, '10.1', 'Corran', 5700000, 1305120628),
(232, 'Alternative Realitäten', '', 2321232, 10, 0, '', '', '', '', '', '', 39, '10.1', 'Corran', 0, 0),
(234, 'Der Flughund', '', 3500000, 13, 35000, '', '', '', '', '', '', 39, '0.696', 'Corran', 2345000, 1303673681),
(235, 'Wohin mit dem Dreck', '', 3800000, 3, 0, '', '', '', '', '', '', 39, '10.1', 'Corran', 5985000, 1321223568),
(236, 'Erfinden eines Gerätes um die Leute, die in das Loch gefallen sind, wieder rauszuholen', '', 1600000, 4, 0, '', '', '', '', '', '', 39, '10.1', 'Corran', 2520000, 1321223568),
(237, 'Projekt Chaos', '', 18000000, 7, 0, '', '', '', '', '', '', 41, '10.1', 'Corran', 11160000, 1305120628),
(238, 'Chaotische Krümmung des Raums', '', 10000000, 10, 0, '', '', '', '', '', '', 40, '10.1', 'Corran', 5700000, 1305120628),
(241, 'Bunte Farben', '', 9000000, 2, 0, '', '', '', '', '', '', 41, '10.1', 'Corran', 6090300, 1303830739),
(242, 'Sirius X300', '', 12000000, 13, 0, 'Eisen: 750 Stahl: 2.250 VV4A: 375 chem. Elemente: 1.050 Energie: 2.250', '', '', '', '', '', 40, '10.1', 'Corran', 12000000, 1303673681),
(243, 'Wir bauen da was tolles rein', '', 2900000, 3, 0, '', '', '', '', '', '', 40, '10.1', 'Corran', 1363000, 1305120628),
(244, 'Die Gartenkralle', '', 1000000, 5, 0, '', '', '', '', '', '', 40, '10.1', 'Corran', 450000, 1329735501),
(246, 'Als schützenswertes Biotop erklären', '', 3000000, 9, 30000, '', '', '', '', '', '', 40, '0.696', 'Corran', 1305000, 1333007241),
(248, 'Kampfbasis Gamma', '', 11250000, 13, 0, 'Eisen: 285.000 Stahl: 264.000 VV4A: 97.500 chem. Elemente: 315.000 Energie: 300.000', '', '', '', '', '', 41, '11.74', 'Corran', 8100000, 1333007241),
(249, 'Das Ende', '', 5000000, 6, 0, '', '', '', '', '', '', 42, '10.1', 'Corran', 5450000, 1303681107),
(253, 'Crawler', '', 25000000, 13, 0, 'Stahl: 9.300 VV4A: 3.900 chem. Elemente: 6.750 Eis: 75 Energie: 3.000 Credits: 750', '', '', '', '', '', 43, '10.1', 'Corran', 30000000, 1303673681),
(255, 'Kleine Figuren aus Stein', '', 12000000, 9, 120000, '', '', '', '', '', '', 41, '0.696', 'Corran', 5220000, 1333007241),
(257, 'Abwehr von verrückten Tabletopspielern', '', 9000000, 5, 90000, '', '', '', '', '', '', 42, '0.696', 'Corran', 6390000, 1303681107),
(258, 'Gargoil', '', 15000000, 13, 150000, '', '', '', '', '', '', 42, '0.696', 'Corran', 15000000, 1303681107),
(259, 'Gatling', '', 6750000, 13, 0, 'Stahl: 5.100 VV4A: 1.275 chem. Elemente: 3.750 Energie: 3.000', '', '', '', '', '', 43, '11.74', 'Corran', 10800000, 1303681107),
(262, 'Reduzierung der Schiffbaudauer um 1%', '', 20000000, 9, 0, '', '', '', '', '', '', 43, '10.1', 'Corran', 0, 0),
(263, 'Reduzierung der Schiffbaukosten um 1%', '', 20000000, 9, 0, '', '', '', '', '', '', 43, '10.1', 'Corran', 0, 0),
(264, 'Reduzierung der Gebbaukosten um 1%', '', 20000000, 9, 0, '', '', '', '', '', '', 43, '10.1', 'Corran', 0, 0),
(265, 'Reduzierung der Gebbaudauer um 1%', '', 20000000, 9, 0, '', '', '', '', '', '', 43, '10.1', 'Corran', 0, 0),
(266, 'Total geheime Forschung', '', 4000000, 8, 40000, '', '', '', '', '', '', 45, '0.696', 'Corran', 0, 0),
(314, 'sehr große Schiffsrümpfe', '', 500000, 10, 0, '', '', '', '', '', '', 17, '11.74', 'Shar', 290000, 1317374897),
(310, 'Suche nach neuen alten Sachen', '', 75000, 9, 0, '', '', '', '', '', '', 13, '10.1', 'Shar', 51000, 1317374897),
(322, 'alternative Ordnungsmethoden', '', 123456, 9, 0, '', '', '', '', '', '', 14, '11.74', 'Blutmade', 77778, 1312046102),
(325, 'verbesserte Reflexionseigenschaften', '', 140000, 4, 0, '', '', '', '', '', '', 10, '11.74', 'Blutmade', 86800, 1312708205),
(326, 'Warum dauert das so lange?', '', 145000, 12, 0, '', '', '', '', '', '', 18, '10.1', 'Blutmade', 97875, 1313658714),
(346, 'Das habe ich bestellt??', '', 450000, 12, 0, '', 'Handelsgesellschaft Stufe 5<br />\nHandelsgesellschaft Stufe 6', '', '', '', '', 18, '10.1', 'Shar', 162000, 1329735501),
(347, 'extraterrestrischer Bergbau', '', 10000, 10, 0, '', '', '', '', '', '', 6, '11.74', 'Shar', 6500, 1307038267),
(348, 'extraterrestrisches Siedeln', '', 25000, 11, 0, '', '', '', '', '', '', 7, '11.74', 'Shar', 5500, 1305120628),
(349, 'Gravitonuntersuchungen', '', 1190000, 4, 0, '', '', '', '', '', '', 24, '11.74', 'Shar', 809200, 1317374897),
(350, 'große Massdriver', '', 550000, 10, 0, '', 'Massdriver Stufe 1', '', '', '', '', 20, '10.1', 'Shar', 361350, 1317374897),
(351, 'Pflaumenmus (kleiner Carrier)', '', 75000, 13, 0, 'Eisen: 15.000 Stahl: 7.500 chem. Elemente: 7.500 Energie: 22.500 Credits: 1.500', '', '', '', '', '', 17, '11.74', 'Shar', 51000, 1317374897),
(352, 'Grundlagen Terraformen', '', 30000, 11, 0, '', '', '', '', '', '', 9, '11.74', 'Shar', 9900, 1317374897),
(353, 'Terraforming Lebensbedingungen', '', 25000, 11, 0, '', '', '', '', '', '', 10, '11.74', 'Shar', 12000, 1317374803),
(354, 'Human Gnome Project', 'Paar Spritzen hier, bissl Strahlung dort und am Ende noch paar Pillen zur Beruhigung. Und keine Sorge, das dritte Auge muss da sein! Und auch der Haarverlust. Jaha, auch die Sache da unten...', 45000, 6, 0, '', '', '', '', '', 'Meister der Bunker<br />\nMeister der Neugier<br />\nMeister des Ikea<br />\nIch will es hübsch<br />\nMeister der Peitschen<br />\nSchifflebauen<br />\nDer Einmaurer<br />\nLehrling von Ikea<br />\nIch will mehr Zeit<br />\nResearch the Apokalypse NOW', 8, '11.74', 'Shar', 18450, 1308772959),
(333, 'Katze- und Maustest', '', 630000, 4, 0, '', '', '', '', '', '', 20, '10.1', 'Corran', 396900, 1315255592),
(337, 'Wieso kostet das so viel?', '', 340000, 12, 0, 'Credits: 100.000', '', '', '', '', '', 18, '10.1', 'Corran', 162180, 1324415304),
(339, 'totale Wissensvernetzung durch bessere Waffentechnologie', '', 2040000, 5, 0, '', '', '', '', '', '', 25, '11.74', 'Corran', 1672800, 1317374759),
(340, 'Gravitonenstrahl', '', 1250000, 5, 0, '', '', '', 'SDI Gravitonbeam', '', '', 25, '10.1', 'Corran', 871250, 1317374759),
(341, 'Größe macht doch was aus', '', 2975000, 5, 0, '', '', '', '', '', '', 28, '11.74', 'Corran', 6545001, 1317374630),
(342, 'Kapier ich nicht, lasst uns was anderes machen. Was tolles, mit Spaß und was zum Spielen! Und einer überraschung...', 'Kapier ich nicht, lasst uns was anderes machen. Was tolles, mit Spaß und zum Spielen.', 750000, 12, 0, '', '', '', '', '', '', 35, '11.74', 'Corran', 487500, 1321739681),
(365, 'Zeus (Dreadnought)', '', 4000000, 13, 0, 'Eisen: 37.500 Stahl: 60.000 VV4A: 22.500 chem. Elemente: 37.500 Energie: 60.000', '', '', '', '', '', 29, '10.1', 'Corran', 1480000, 1305120628),
(366, 'Also eigentlich haben wir den Photonenblaster schon erforscht. Damals hatten wir ihn Taschenlampe genannt. Wieso haben wir ihn dann nochmal erforscht?', '', 42500, 12, 0, '', '', '', '', '', '', 25, '11.74', 'Sahnetoertchen', 0, 0),
(367, 'Wieso haben alle einen Partner, nur ich nicht? *nöl*', '', 915000, 12, 0, '', '', '', '', '', '', 27, '10.1', 'Shar', 1104444, 1317374630),
(372, 'Wiedergewinnung von 8 Genetikpunkt(en)(mittels eines Pümpels)', 'Hiermit kann man verlorene Genetikpunkte wiedergewinnen. Man kann die Forschung sooft ausführen wie man will, man braucht nur mindestens einen verlorenen Genetikpunkt. Es gibt allerdings was zu beachten, die Forschungskosten steigen mit der Anzahl der besiedelten Planeten und wie oft man die Forschung bereits erforscht hat.', 1, 6, 0, '', '', '', '', '', '', 1, '10.1', 'Blutmade', 0, 0),
(370, 'Erfindung der Spül&quot;maschine&quot;', '', 2000123, 9, 0, 'chem. Elemente: 1.500.000 Credits: 250.000', '', '', '', '', '', 40, '10.1', 'Shar', 0, 0),
(368, 'Wir bauen das größte Loch das es gibt', '', 120, 3, 0, 'Eisen: 1.000.000 Stahl: 1.000.000 VV4A: 500.000 chem. Elemente: 2.000.000', '', '', '', '', '', 37, '10.1', 'Shar', 57, 1305120628),
(369, 'Thermonukleare Plasmafusion von Quarks auf Basis von ionisierten Primärspaltneutronen', '', 1400000, 4, 0, '', '', '', '', '', '', 37, '10.1', 'Shar', 661500, 1324415304),
(371, 'Das Loch, die Pfadfinder und eine neunschwänzige Lederpeitsche mit Stahlkugeln an den Spitzen', '', 4125000, 3, 0, '', '', '', '', '', '', 41, '11.74', 'Shar', 3135000, 1305120628),
(373, 'Werbung für quellgesundes Wasser aus quellgesundem Methaneis', '', 1900000, 9, 0, '', '', '', '', '', '', 39, '10.1', 'Shar', 703000, 1305120628),
(375, 'Heißer öhm *hust* Rauch', '', 5600000, 2, 0, 'chem. Elemente: 1.500.000', '', '', '', '', '', 39, '10.1', 'Shar', 3192000, 1305120628),
(376, 'Bessere Waffen gegen revoltierende Roboter mit Atomwaffen', '', 20000, 5, 0, '', '', '', '', '', '', 8, '11.74', 'Fiedler', 11201, 1309170663),
(377, 'Wir zielen mit großen Waffen auf kleine Dinge', '', 800, 5, 0, '', '', '', 'SDI Raketensystem<br />\nStopfentenwerfer', '', '', 2, '11.74', 'Fiedler', 0, 0),
(378, 'Was lebt in meinem Kühlschrank?', '', 120000, 12, 0, '', '', '', '', '', '', 13, '11.74', 'Fiedler', 87600, 1310641703),
(379, 'Ich verstehe die Formulare nicht, ich ziehe einfach um', '', 75000, 12, 0, '', '', '', '', '', '', 9, '11.74', 'Starflyer', 47250, 1310641818),
(380, 'Ich, als absoluter Prospieler brauche defintiv die 10% mehr von der wichtigen Ressource Energie, damit ich weiterhin vorne mitpowern kann. Solange ich dies nicht habe, haben die anderen einen uneinholbaren Vorsprung. UNEINHOLBAR !!111elf *fuchtel*', '', 1, 9, 0, 'Eis: 5', '', '', '', '', '', 7, '11.74', 'Fiedler', 0, 0),
(381, 'Kostenlose Radioaktive Müllentsorgung mittels Schlussverkauf und Grabbeltischen', '', 50000, 3, 0, '', '', '', '', '', '', 1, '11.74', 'Fiedler', 24000, 1317374897);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2building`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 22:10
--

DROP TABLE IF EXISTS `prefix_research2building`;
CREATE TABLE IF NOT EXISTS `prefix_research2building` (
  `rId` int(10) unsigned NOT NULL DEFAULT '0',
  `bId` int(10) unsigned NOT NULL DEFAULT '0',
  `lvl` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rId`,`bId`,`lvl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschung rId ermoeglicht Gebaeude(stufe) bId';

--
-- Daten für Tabelle `prefix_research2building`
--

INSERT INTO `prefix_research2building` (`rId`, `bId`, `lvl`) VALUES
(1, 1, 0),
(1, 5, 0),
(1, 6, 0),
(1, 13, 0),
(1, 37, 0),
(1, 122, 0),
(1, 124, 0),
(1, 125, 0),
(1, 126, 0),
(1, 127, 0),
(2, 2, 0),
(2, 118, 0),
(2, 121, 0),
(2, 199, 0),
(3, 65, 0),
(4, 24, 0),
(6, 22, 0),
(7, 23, 0),
(9, 71, 0),
(10, 30, 0),
(10, 46, 0),
(10, 47, 0),
(11, 49, 0),
(11, 50, 0),
(11, 52, 0),
(11, 53, 0),
(11, 54, 0),
(11, 55, 0),
(12, 7, 0),
(12, 12, 0),
(13, 81, 0),
(15, 8, 0),
(15, 80, 0),
(16, 85, 0),
(18, 41, 0),
(20, 82, 0),
(20, 86, 0),
(20, 86, 1),
(20, 86, 2),
(21, 42, 0),
(22, 25, 0),
(23, 11, 0),
(23, 56, 0),
(23, 58, 0),
(23, 66, 0),
(25, 14, 0),
(25, 17, 0),
(25, 18, 0),
(25, 19, 0),
(25, 51, 0),
(25, 73, 0),
(25, 74, 0),
(25, 228, 0),
(26, 1, 0),
(26, 5, 0),
(26, 6, 0),
(26, 13, 0),
(26, 37, 0),
(26, 122, 0),
(26, 124, 0),
(26, 125, 0),
(26, 126, 0),
(26, 127, 0),
(27, 23, 0),
(30, 35, 0),
(31, 67, 0),
(32, 57, 0),
(32, 57, 1),
(32, 57, 2),
(32, 87, 0),
(32, 87, 1),
(32, 87, 2),
(35, 4, 0),
(35, 113, 0),
(41, 60, 0),
(42, 84, 3),
(42, 86, 3),
(42, 86, 4),
(42, 88, 0),
(44, 83, 0),
(45, 38, 0),
(50, 9, 0),
(50, 59, 0),
(50, 139, 0),
(52, 64, 0),
(57, 101, 0),
(58, 84, 0),
(58, 84, 1),
(58, 84, 2),
(61, 91, 0),
(62, 98, 0),
(62, 99, 0),
(63, 3, 0),
(65, 72, 0),
(69, 68, 0),
(69, 70, 0),
(74, 29, 0),
(76, 132, 0),
(77, 89, 0),
(78, 90, 0),
(80, 61, 0),
(83, 105, 0),
(84, 44, 0),
(84, 57, 3),
(84, 57, 4),
(88, 94, 0),
(88, 102, 0),
(88, 230, 0),
(88, 231, 0),
(88, 232, 0),
(88, 233, 0),
(88, 247, 0),
(88, 248, 0),
(88, 249, 0),
(88, 250, 0),
(90, 31, 0),
(91, 221, 0),
(91, 222, 0),
(91, 223, 0),
(95, 69, 0),
(97, 10, 0),
(98, 75, 0),
(100, 84, 4),
(100, 84, 5),
(100, 84, 6),
(106, 26, 0),
(107, 77, 0),
(107, 78, 0),
(107, 79, 0),
(107, 92, 0),
(107, 92, 1),
(107, 92, 2),
(107, 92, 3),
(107, 93, 0),
(108, 39, 0),
(109, 108, 0),
(114, 131, 0),
(125, 112, 0),
(126, 97, 0),
(128, 28, 0),
(133, 95, 0),
(134, 86, 5),
(134, 86, 6),
(135, 76, 0),
(137, 241, 0),
(139, 100, 0),
(140, 20, 0),
(140, 27, 0),
(140, 57, 5),
(140, 57, 6),
(141, 96, 0),
(143, 137, 0),
(143, 137, 1),
(148, 137, 2),
(158, 84, 7),
(158, 84, 8),
(159, 43, 0),
(173, 109, 0),
(175, 36, 0),
(175, 111, 0),
(175, 115, 0),
(175, 134, 0),
(176, 116, 0),
(177, 133, 0),
(181, 110, 0),
(181, 117, 0),
(181, 135, 0),
(181, 225, 0),
(191, 40, 0),
(204, 143, 0),
(206, 142, 0),
(212, 147, 0),
(213, 149, 0),
(215, 144, 0),
(217, 145, 0),
(220, 148, 0),
(224, 155, 0),
(225, 156, 0),
(228, 146, 0),
(229, 150, 0),
(231, 151, 0),
(231, 152, 0),
(235, 153, 0),
(238, 104, 0),
(239, 162, 0),
(241, 161, 0),
(243, 157, 0),
(245, 86, 7),
(246, 154, 0),
(252, 158, 0),
(252, 159, 0),
(252, 160, 0),
(266, 226, 0),
(267, 35, 0),
(304, 60, 0),
(307, 84, 3),
(307, 86, 3),
(307, 86, 4),
(307, 88, 0),
(310, 184, 0),
(310, 186, 0),
(310, 236, 0),
(311, 60, 0),
(314, 62, 0),
(315, 64, 0),
(319, 10, 0),
(321, 90, 0),
(324, 105, 0),
(327, 234, 0),
(333, 96, 0),
(335, 137, 0),
(335, 137, 1),
(336, 97, 0),
(337, 95, 0),
(338, 86, 5),
(338, 86, 6),
(339, 43, 0),
(341, 110, 0),
(341, 117, 0),
(344, 36, 0),
(344, 111, 0),
(344, 115, 0),
(344, 242, 0),
(346, 86, 5),
(346, 86, 6),
(347, 35, 0),
(350, 137, 0),
(350, 137, 1),
(353, 97, 0),
(354, 64, 0),
(355, 23, 0),
(358, 90, 0),
(360, 234, 0),
(367, 243, 0),
(368, 142, 0),
(370, 145, 0),
(371, 148, 0),
(373, 146, 0),
(374, 156, 0),
(375, 152, 0),
(375, 244, 0),
(377, 71, 0),
(379, 10, 0),
(381, 245, 0),
(381, 246, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2prototype`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_research2prototype`;
CREATE TABLE IF NOT EXISTS `prefix_research2prototype` (
  `rid` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschung rId ermoeglicht Prototyp pId';

--
-- Daten für Tabelle `prefix_research2prototype`
--

INSERT INTO `prefix_research2prototype` (`rid`, `pid`) VALUES
(36, 14),
(38, 301),
(39, 63),
(40, 9),
(43, 1),
(46, 16),
(47, 4),
(48, 304),
(53, 5),
(54, 68),
(56, 2),
(57, 7),
(59, 6),
(64, 66),
(72, 12),
(73, 62),
(87, 8),
(93, 305),
(102, 72),
(103, 65),
(113, 77),
(115, 75),
(116, 10),
(120, 18),
(121, 74),
(122, 13),
(123, 70),
(124, 64),
(127, 81),
(129, 73),
(130, 79),
(136, 71),
(146, 67),
(154, 80),
(155, 91),
(168, 76),
(169, 85),
(170, 82),
(183, 78),
(194, 86),
(195, 83),
(200, 88),
(234, 92),
(242, 84),
(248, 100),
(251, 99),
(253, 101),
(258, 102),
(259, 103),
(312, 157),
(313, 160),
(316, 158),
(317, 159),
(318, 300),
(345, 86),
(351, 160),
(359, 157),
(365, 76);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2research`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 19:06
--

DROP TABLE IF EXISTS `prefix_research2research`;
CREATE TABLE IF NOT EXISTS `prefix_research2research` (
  `rOld` int(10) unsigned NOT NULL DEFAULT '0',
  `rNew` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rOld`,`rNew`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Forschung rOld ermoeglicht Forschung rNew';

--
-- Daten für Tabelle `prefix_research2research`
--

INSERT INTO `prefix_research2research` (`rOld`, `rNew`) VALUES
(1, 10),
(1, 12),
(2, 19),
(2, 377),
(3, 14),
(4, 5),
(5, 376),
(6, 355),
(8, 9),
(10, 11),
(11, 25),
(12, 13),
(12, 14),
(13, 15),
(14, 15),
(14, 18),
(14, 19),
(14, 20),
(15, 16),
(15, 348),
(18, 21),
(19, 23),
(21, 29),
(23, 5),
(23, 24),
(23, 31),
(23, 32),
(23, 33),
(23, 65),
(23, 347),
(23, 348),
(24, 355),
(25, 81),
(25, 82),
(29, 44),
(29, 45),
(29, 348),
(29, 380),
(31, 43),
(32, 42),
(33, 36),
(33, 37),
(33, 39),
(33, 40),
(33, 311),
(33, 359),
(35, 50),
(35, 376),
(37, 46),
(37, 47),
(37, 316),
(39, 115),
(40, 116),
(41, 91),
(41, 93),
(42, 77),
(42, 358),
(43, 56),
(44, 66),
(44, 70),
(44, 76),
(45, 51),
(45, 354),
(46, 54),
(47, 59),
(49, 58),
(49, 316),
(50, 60),
(51, 56),
(51, 57),
(53, 124),
(55, 118),
(55, 119),
(56, 183),
(57, 102),
(58, 44),
(58, 61),
(59, 123),
(60, 62),
(60, 63),
(60, 309),
(66, 51),
(66, 67),
(66, 69),
(67, 42),
(67, 53),
(67, 57),
(67, 59),
(67, 71),
(67, 72),
(67, 311),
(67, 381),
(68, 74),
(68, 378),
(70, 72),
(70, 73),
(71, 54),
(71, 56),
(71, 64),
(72, 146),
(73, 103),
(74, 25),
(75, 319),
(76, 68),
(76, 309),
(77, 79),
(77, 379),
(79, 84),
(79, 86),
(79, 324),
(79, 325),
(79, 378),
(80, 106),
(81, 57),
(81, 311),
(82, 87),
(82, 88),
(84, 89),
(84, 90),
(84, 110),
(84, 314),
(86, 98),
(86, 99),
(87, 113),
(88, 57),
(89, 100),
(89, 112),
(90, 55),
(90, 100),
(90, 322),
(94, 73),
(94, 87),
(96, 103),
(96, 104),
(96, 105),
(97, 101),
(98, 88),
(98, 104),
(98, 107),
(98, 108),
(98, 314),
(98, 360),
(99, 103),
(99, 111),
(100, 112),
(102, 248),
(102, 361),
(102, 362),
(103, 129),
(104, 103),
(104, 115),
(104, 116),
(105, 135),
(106, 360),
(107, 120),
(107, 125),
(108, 314),
(110, 360),
(111, 64),
(111, 115),
(111, 116),
(111, 120),
(111, 121),
(111, 122),
(111, 123),
(111, 124),
(112, 105),
(112, 128),
(113, 154),
(113, 155),
(116, 130),
(116, 253),
(117, 126),
(122, 136),
(123, 251),
(124, 142),
(124, 234),
(128, 326),
(128, 334),
(128, 337),
(128, 346),
(129, 242),
(130, 259),
(135, 121),
(135, 130),
(135, 146),
(135, 147),
(135, 349),
(138, 140),
(138, 333),
(139, 135),
(139, 144),
(139, 350),
(140, 102),
(140, 113),
(140, 123),
(140, 135),
(140, 145),
(140, 146),
(140, 147),
(140, 158),
(141, 152),
(142, 146),
(144, 129),
(144, 149),
(144, 150),
(145, 129),
(145, 130),
(145, 349),
(147, 148),
(147, 149),
(147, 156),
(147, 158),
(147, 349),
(147, 356),
(150, 121),
(151, 129),
(151, 130),
(153, 159),
(153, 160),
(156, 166),
(156, 366),
(158, 161),
(158, 162),
(160, 136),
(160, 168),
(163, 173),
(163, 174),
(164, 342),
(166, 169),
(166, 170),
(166, 343),
(167, 178),
(167, 179),
(171, 194),
(172, 154),
(172, 155),
(174, 177),
(174, 344),
(174, 367),
(176, 181),
(176, 182),
(178, 180),
(179, 180),
(180, 185),
(180, 186),
(181, 168),
(182, 183),
(182, 184),
(182, 318),
(185, 187),
(186, 188),
(186, 189),
(187, 191),
(188, 192),
(189, 364),
(190, 193),
(191, 164),
(191, 343),
(192, 363),
(195, 200),
(197, 201),
(197, 202),
(198, 368),
(198, 369),
(199, 203),
(199, 204),
(199, 205),
(201, 215),
(201, 216),
(202, 218),
(202, 370),
(203, 208),
(203, 209),
(204, 210),
(205, 211),
(205, 212),
(206, 220),
(208, 213),
(209, 223),
(209, 224),
(209, 226),
(209, 374),
(210, 233),
(211, 232),
(211, 375),
(214, 229),
(214, 230),
(215, 154),
(215, 222),
(216, 227),
(216, 228),
(216, 373),
(217, 220),
(218, 234),
(219, 235),
(219, 236),
(221, 253),
(222, 237),
(222, 238),
(223, 242),
(224, 212),
(226, 230),
(226, 241),
(227, 245),
(227, 246),
(227, 247),
(228, 217),
(229, 241),
(230, 237),
(230, 255),
(232, 238),
(232, 239),
(233, 240),
(235, 229),
(235, 243),
(236, 243),
(236, 244),
(237, 221),
(237, 249),
(238, 248),
(238, 249),
(240, 250),
(241, 155),
(241, 249),
(243, 252),
(244, 249),
(247, 251),
(249, 262),
(249, 263),
(249, 264),
(249, 265),
(250, 254),
(254, 256),
(255, 257),
(255, 258),
(256, 260),
(256, 261),
(257, 259),
(260, 266),
(304, 305),
(304, 306),
(307, 308),
(309, 310),
(310, 317),
(311, 53),
(311, 54),
(311, 59),
(311, 64),
(311, 72),
(311, 80),
(311, 94),
(311, 314),
(311, 351),
(314, 122),
(316, 64),
(317, 318),
(320, 336),
(322, 108),
(325, 95),
(325, 96),
(326, 124),
(326, 139),
(330, 135),
(330, 142),
(330, 147),
(333, 151),
(333, 330),
(334, 137),
(334, 138),
(339, 172),
(340, 136),
(340, 365),
(341, 164),
(341, 365),
(342, 197),
(342, 198),
(342, 199),
(343, 195),
(343, 345),
(344, 161),
(344, 162),
(345, 155),
(345, 200),
(347, 35),
(348, 49),
(349, 339),
(349, 340),
(350, 148),
(352, 127),
(352, 353),
(354, 55),
(354, 352),
(356, 163),
(356, 164),
(358, 80),
(359, 53),
(363, 191),
(364, 363),
(366, 167),
(367, 118),
(367, 182),
(367, 341),
(368, 219),
(368, 221),
(368, 371),
(369, 213),
(369, 214),
(370, 371),
(373, 370),
(376, 70),
(377, 14),
(378, 89),
(378, 322);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_research2user`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_research2user`;
CREATE TABLE IF NOT EXISTS `prefix_research2user` (
  `rid` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` varchar(30) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='bereits erforschte Forschungen des Benutzers';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_researchfield`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 22:10
--

DROP TABLE IF EXISTS `prefix_researchfield`;
CREATE TABLE IF NOT EXISTS `prefix_researchfield` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Forschungsfelder' AUTO_INCREMENT=17 ;

--
-- Daten für Tabelle `prefix_researchfield`
--

INSERT INTO `prefix_researchfield` (`id`, `name`) VALUES
(1, 'noch unbekannt'),
(2, 'Chemie'),
(3, 'Industrie'),
(4, 'Physik'),
(5, 'Militär'),
(6, 'Evolution'),
(7, 'Ethik'),
(8, 'Informatik'),
(9, 'Wirtschaft'),
(10, 'Raumfahrt'),
(11, 'Kolonisation'),
(12, 'Unifragen'),
(13, 'Prototypen'),
(14, '---'),
(15, ''),
(16, 'noch unbekannt');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_ressuebersicht`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_ressuebersicht`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_scans`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_scans_historie`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_schiffe`;
CREATE TABLE IF NOT EXISTS `prefix_schiffe` (
  `user` varchar(30) NOT NULL DEFAULT '',
  `schiff` int(11) unsigned NOT NULL DEFAULT '0',
  `anzahl` int(7) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_schiffstyp`
--
-- Erzeugt am: 21. Apr 2012 um 22:27
-- Aktualisiert am: 21. Apr 2012 um 22:27
--

DROP TABLE IF EXISTS `prefix_schiffstyp`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=306 ;

--
-- Daten für Tabelle `prefix_schiffstyp`
--

INSERT INTO `prefix_schiffstyp` (`id`, `schiff`, `abk`, `typ`, `bild`, `id_iw`, `kosten_eisen`, `kosten_stahl`, `kosten_vv4a`, `kosten_chemie`, `kosten_eis`, `kosten_wasser`, `kosten_energie`, `angriff`, `waffenklasse`, `verteidigung`, `panzerung_kinetisch`, `panzerung_elektrisch`, `panzerung_gravimetrisch`, `dauer`, `bestellbar`, `klasse1`, `klasse2`) VALUES
(1, 'Sonde X11', 'Sonde X11', '6. Sonden/Carrier', 'x11_k', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(2, 'Terminus Sonde', 'Terminus Sonde', '6. Sonden/Carrier', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(4, 'Lurch (Systemtransporter Klasse 2)', 'Lurch', '1. Frachter', 'lurch_k', 11, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 2000),
(5, 'Gorgol 9 (Hyperraumtransporter Klasse 1)', 'Gorgol', '1. Frachter', 'gorgol9_k', 15, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 20000, 0),
(6, 'Eisbär (Hyperraumtransporter Klasse 2)', 'Eisb&auml;r', '1. Frachter', '', 17, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 10000),
(7, 'Kampfbasis Alpha', 'KB Alpha', '2. Zivile Schiffe', '', 54, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(8, 'Atombomber', 'Atombomber', '3. J&auml;ger &amp; Co', '', 21, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(9, 'Slayer', 'Slayer', '4. Korvetten/Zerst&ouml;rer', 'slayer_k', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(10, 'Vendeta', 'Vendeta', '4. Korvetten/Zerst&ouml;rer', 'vendetta_k', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(12, 'Hitman', 'Hitman', '4. Korvetten/Zerst&ouml;rer', 'hitman_k', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(13, 'Big Daddy', 'Big Daddy', '5. Schlachtschiffe/DN', 'bigdaddy_k', 48, 8000, 10000, 4900, 7500, 0, 0, 12000, 600, 'kinetisch', 980, 100, 100, 100, 72000, 0, 0, 0),
(14, 'Scout', 'Scout', '2. Zivile Schiffe', 'scout_k', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(16, 'Crux (Systemtransporter Kolonisten)', 'Crux', '1. Frachter', 'crux_k', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(18, 'Robominer V1', 'Robominer', '2. Zivile Schiffe', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(62, 'Sheep', 'Sheep', '3. J&auml;ger &amp; Co', 'sheep_k', 22, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(63, 'Lionheart', 'Lionheart', '4. Korvetten/Zerst&ouml;rer', 'lionheart_k', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(64, 'Kamel Z-98 (Hyperraumtransporter Klasse 1)', 'Kamel', '1. Frachter', '', 59, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 75000, 0),
(65, 'Shark', 'Shark', '3. J&auml;ger &amp; Co', 'shark_k', 23, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(66, 'INS-03A (interstellares Kolonieschiff)', 'INS-03A', '2. Zivile Schiffe', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(67, 'Succubus', 'Succubus', '4. Korvetten/Zerst&ouml;rer', '', 46, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(68, 'Kolpor (Hyperraumtransporter Kolonisten)', 'Kolpor', '1. Frachter', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(70, 'Waschbär (Hyperraumtransporter Klasse 2)', 'Waschb&auml;r', '1. Frachter', '', 60, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 50000),
(71, 'Kronk', 'Kronk', '5. Schlachtschiffe/DN', '', 49, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(72, 'Kampfbasis Beta', 'KB Beta', '2. Zivile Schiffe', '', 61, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(73, 'Manta', 'Manta', '3. J&auml;ger &amp; Co', 'manta_k', 31, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(74, 'X12 (Carrier)', 'X12', '6. Sonden/Carrier', '', 53, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(75, 'Hunter', 'Hunter', '4. Korvetten/Zerst&ouml;rer', 'hunter_k', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(76, 'Zeus', 'Zeus', '5. Schlachtschiffe/DN', '', 51, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(77, 'Stormbringer', 'Stormbringer', '3. J&auml;ger &amp; Co', '', 34, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(78, 'Sonde X13', 'Sonde X13', '6. Sonden/Carrier', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(79, 'Victim', 'Victim', '4. Korvetten/Zerst&ouml;rer', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(80, 'Nova', 'Nova', '3. J&auml;ger &amp; Co', '', 35, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(81, 'Terraformer Alpha', 'Terraformer', '2. Zivile Schiffe', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(82, 'Eraser', 'Eraser', '4. Korvetten/Zerst&ouml;rer', '', 39, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(83, 'Widowmaker', 'Widowmaker', '4. Korvetten/Zerst&ouml;rer', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(84, 'Sirius X300', 'Sirius X300', '3. J&auml;ger &amp; Co', 'x300_k', 30, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(85, 'Nightcrawler', 'Nightcrawler', '3. J&auml;ger &amp; Co', 'nc_k', 33, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(86, 'Mule (Carrier)', 'Mule (Carrier)', '6. Sonden/Carrier', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(88, 'Silent Sorrow', 'Silent Sorrow', '4. Korvetten/Zerst&ouml;rer', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(91, 'Nepomuk', 'Nepomuk', '3. J&auml;ger &amp; Co', '', 36, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(92, 'Flughund (Hyperraumtransporter Klasse 1)', 'Flughund', '1. Frachter', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 400000, 0),
(99, 'Seepferdchen (Hyperraumtransporter Klasse 2)', 'Seepferdchen', '1. Frachter', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 250000),
(100, 'Kampfbasis Gamma', 'KB Gamma', '2. Zivile Schiffe', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(101, 'Crawler', 'Crawler', '4. Korvetten/Zerst&ouml;rer', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(102, 'Gargoil (Carrier)', 'Gargoil (Carrier)', '6. Sonden/Carrier', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(103, 'Gatling', 'Gatling', '4. Korvetten/Zerst&ouml;rer', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(157, 'Systrans (Systemtransporter Klasse 1)', 'Systrans', '1. Frachter', '', 9, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 5000, 0),
(158, 'KISS-01 (Systemkolonieschiff)', 'KISS-01', '2. Zivile Schiffe', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(159, 'Artefaktsammelbasis Alpha', 'AB Alpha', '2. Zivile Schiffe', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(160, 'Pflaumenmus (Carrier)', 'Pflaumenmus', '6. Sonden/Carrier', '', 20, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(169, 'Weihnachtsmannschlitten (Transporter)', 'Weihnachtsmannschlitten', '1. Frachter', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(237, 'Settlers Delight', 'Settlers Delight', '1. Frachter', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 5000, 2000),
(300, 'Artefaktsammelbasis Beta', 'AB Beta', '2. Zivile Schiffe', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0),
(161, 'Raketentransporter (mit Plutoniumdrachenantrieb und zwei extra grossen Eiern)', 'Raketen-Transe', '1. Frachter', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 'keine', 10000, 200, 200, 200, 0, 0, 250000, 250000);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sid`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 22. Apr 2012 um 09:17
--

DROP TABLE IF EXISTS `prefix_sid`;
CREATE TABLE IF NOT EXISTS `prefix_sid` (
  `sid` varchar(50) NOT NULL DEFAULT '',
  `ip` varchar(20) NOT NULL DEFAULT '',
  `date` varchar(11) NOT NULL DEFAULT '',
  `id` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `prefix_sid`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sitterauftrag`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_sitterauftrag`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_sitterlog`;
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
-- Tabellenstruktur für Tabelle `prefix_spielerinfo`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_spielerinfo`;
CREATE TABLE IF NOT EXISTS `prefix_spielerinfo` (
  `user` varchar(30) NOT NULL DEFAULT '',
  `dabei_seit` int(12) DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_sysscans`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_sysscans`;
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
-- Tabellenstruktur für Tabelle `prefix_transferliste`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_transferliste`;
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
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_univ_link`;
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
-- Erzeugt am: 21. Apr 2012 um 18:55
-- Aktualisiert am: 22. Apr 2012 um 09:17
--

DROP TABLE IF EXISTS `prefix_user`;
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
  `dabei` varchar(11) NOT NULL DEFAULT '',
  `titel` varchar(150) NOT NULL DEFAULT '',
  `userlink` char(1) NOT NULL DEFAULT '',
  `lastshipscan` varchar(11) NOT NULL DEFAULT '',
  `menu_default` varchar(20) NOT NULL DEFAULT 'default',
  `gesperrt` tinyint(1) NOT NULL DEFAULT '0',
  `color` varchar(7) NOT NULL DEFAULT '0',
  `ikea` char(1) NOT NULL DEFAULT '',
  `sound` tinyint(1) NOT NULL DEFAULT '0',
  `squad` varchar(30) NOT NULL DEFAULT '',
  `switch` int(11) NOT NULL DEFAULT '0' COMMENT 'zum speichern der einstellungen bei m_produktion',
  `lastsitterlogin` int(11) NOT NULL DEFAULT '0',
  `lastsitteruser` varchar(30) NOT NULL DEFAULT '',
  `lastsitterloggedin` int(11) NOT NULL DEFAULT '0',
  `fremdesitten` int(1) NOT NULL DEFAULT '0' COMMENT 'Darf fremde Allianzen sitten',
  `vonfremdesitten` int(1) NOT NULL DEFAULT '0' COMMENT 'Darf von fremden Allianzen gesittet werden',
  `iwsa` char(1) NOT NULL,
  `lasttransport` varchar(11) DEFAULT NULL,
  `uniprop` int(11) NOT NULL DEFAULT '1',
  `dauersitten` int(11) NOT NULL DEFAULT '0' COMMENT 'Anzahl Sekunden bis Login faellig',
  `dauersittentext` varchar(255) NOT NULL COMMENT 'Kommentar zum Dauersitten',
  `dauersittenlast` int(11) DEFAULT NULL COMMENT 'Timestamp der letzten Dauersitten-Erledigung',
  PRIMARY KEY (`id`),
  KEY `ikea` (`ikea`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `prefix_user`
--

INSERT INTO `prefix_user` (`id`, `staatsform`, `password`, `email`, `status`, `rules`, `logindate`, `allianz`, `grav_von`, `grav_bis`, `gal_start`, `gal_end`, `sys_start`, `sys_end`, `preset`, `planibilder`, `gebbilder`, `geopunkte`, `syspunkte`, `sitterlogin`, `sitterpwd`, `sitterskin`, `sittercomment`, `sitten`, `adminsitten`, `newspermission`, `mailpermission`, `sitterpunkte`, `gebaeude`, `peitschen`, `gengebmod`, `genbauschleife`, `genmaurer`, `budflesol`, `buddlerfrom`, `rang`, `gebp`, `fp`, `gesamtp`, `ptag`, `dabei`, `titel`, `userlink`, `lastshipscan`, `menu_default`, `gesperrt`, `color`, `ikea`, `sound`, `squad`, `switch`, `lastsitterlogin`, `lastsitteruser`, `lastsitterloggedin`, `fremdesitten`, `vonfremdesitten`, `iwsa`, `lasttransport`, `uniprop`, `dauersitten`, `dauersittentext`, `dauersittenlast`) VALUES
('admin', 0, '21232f297a57a5a743894a0e4a801fc3', '', 'admin', '1', 0, '', 0, 0, '0', '0', '0', '0', 0, '', '', 0, 0, '', NULL, 0, '', '', '', 1, 1, 0, '', '', 1, '', '', '', '', '', 0, 0, 0, 0, '', '', '', '', 'default', 0, '0', '', 0, '', 0, 0, '', 0, 0, 0, '', NULL, 1, 0, '', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_user_research`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_user_research`;
CREATE TABLE IF NOT EXISTS `prefix_user_research` (
  `user` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `rId` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(12) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Aktuelle Forschungen der User';

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_versand_auftrag`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_versand_auftrag`;
CREATE TABLE IF NOT EXISTS `prefix_versand_auftrag` (
  `user` varchar(30) NOT NULL,
  `time` int(11) NOT NULL,
  `pos` int(11) NOT NULL,
  `reference` varchar(30) NOT NULL,
  `art` varchar(20) NOT NULL,
  PRIMARY KEY (`user`,`time`,`pos`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `prefix_wronglogin`
--
-- Erzeugt am: 21. Apr 2012 um 18:54
-- Aktualisiert am: 21. Apr 2012 um 18:54
--

DROP TABLE IF EXISTS `prefix_wronglogin`;
CREATE TABLE IF NOT EXISTS `prefix_wronglogin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(30) NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

