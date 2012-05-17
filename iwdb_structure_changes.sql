-- Tabellenstruktur `prefix_sid` geändert by masel 25.04. 1:00
ALTER TABLE  `prefix_sid` ENGINE = MEMORY;
ALTER TABLE  `prefix_sid` CHANGE  `date`  `date` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE  `prefix_sid` CHANGE  `ip`  `ip` VARCHAR( 50 ) CHARACTER SET utf8;

-- Tabellenstruktur `prefix_research_user` geändert by patsch 08.05. 21:00
ALTER TABLE  `prefix_research_user` ADD `time` INT( 13 ) UNSIGNED NOT NULL;

-- Tabelle `iwdb_transport_einstellungen` entfernen, wird nicht benötigt ... by patsch 17.05. 15:00
DROP TABLE IF EXISTS `iwdb_transport_einstellungen`