--
-- 13.05. : Neue Schiffstypen der Admins (Sauron ist Schuld, der will nur Plex bomben :)
--

INSERT INTO `prefix_schiffstyp` (`id`, `schiff`, `abk`, `typ`, `bild`, `id_iw`, `kosten_eisen`, `kosten_stahl`, `kosten_vv4a`, `kosten_chemie`, `kosten_eis`, `kosten_wasser`, `kosten_energie`, `kosten_bev`, `GeschwindigkeitSol`, `GeschwindigkeitGal`, `canLeaveGalaxy`, `canBeTransported`, `VerbrauchChemie`, `VerbrauchEnergie`, `angriff`, `waffenklasse`, `verteidigung`, `panzerung_kinetisch`, `panzerung_elektrisch`, `panzerung_gravimetrisch`, `Schilde`, `accuracy`, `mobility`, `numEscort`, `EscortBonusAtt`, `EscortBonusDef`, `werftTyp`, `dauer`, `bestellbar`, `isTransporter`, `klasse1`, `klasse2`, `bev`, `isCarrier`, `shipKapa1`, `shipKapa2`, `shipKapa3`, `aktualisiert`) VALUES
(310, 'Stopfente', 'Stopfente', 'admin', '', NULL, 10, 10, 5, 5, 0, 2, 5, 0, 400, 3000, 1, 0, 1, 0, 1, 'kinetisch', 2, 100, 100, 100, 0, 100, 100, 0, 1, 1, 'kleine', 360, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1368435726),
(311, 'SuperStopfente', 'Superstopfente', 'admin', '', NULL, 10, 10, 5, 5, 0, 2, 5, 0, 400, 3000, 1, 0, 1, 0, 1, 'kinetisch', 2000, 100, 100, 100, 0, 100, 500, 0, 1, 1, 'kleine', 360, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1368435737);


--
-- 02.06. : IW-ID und neues Geb
--
UPDATE `prefix_gebaeude` SET `id_iw`='57' WHERE `prefix_gebaeude`.`id` =163;
UPDATE `prefix_gebaeude` SET `id_iw`='59' WHERE `prefix_gebaeude`.`id` =162;

INSERT INTO `prefix_gebaeude` (`name`, `category`, `idcat`, `inactive`, `dauer`, `bild`, `info`, `n_building`, `n_research`, `n_kolotyp`, `n_planityp`, `e_research`, `e_building`, `zerstoert`, `bringt`, `Kosten`, `Punkte`, `MaximaleAnzahl`, `typ`, `kostet`, `id_iw`) VALUES
('tolle Baumhüttenanlage (mit extratollem Ausblick)', ' 2. Bevölkerung', 150, '1', 0, '', '', '', '', '', '', '', '', '', '', '', 0, 0, '', '', 58);


--
-- 05.06. : Standardmäßig Plani- und Gebbilder anzeigen
--
UPDATE `prefix_user` SET `planibilder`='1',`gebbilder`='1';

--
-- 17.06. : Einfügen der Stundenvorlage für Lagerbelegung
--
INSERT INTO `prefix_params` (`name` ,`value` ,`text`) VALUES
('hour', '24', '');