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

CREATE TABLE IF NOT EXISTS `prefix_kb_verluste` (
  `ID_KB` int(11) NOT NULL DEFAULT '0',
  `ID_IW_RESS` int(11) NOT NULL DEFAULT '0',
  `seite` int(11) NOT NULL DEFAULT '0',
  `anzahl` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

