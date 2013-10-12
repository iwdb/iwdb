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

INSERT INTO `prefix_gebbaukosten` (`id`, `name`, `dauer`, `kosten_eisen`, `kosten_stahl`, `kosten_vv4a`, `kosten_chemie`, `kosten_eis`, `kosten_wasser`, `kosten_energie`, `kosten_bev`, `kosten_creds`) VALUES
(1, 'Icecrusher V3b', 43200, 25000, 25000, 17500, 25000, 0, 0, 35000, 50, 0),
(2, 'große Chemiefabrik', 28800, 750, 300, 0, 100, 0, 0, 300, 5, 0),
(3, 'kleiner chemischer Fabrikkomplex', 36000, 5000, 3000, 1500, 1000, 0, 0, 7500, 25, 0),
(4, 'geheimes Vulkanlabor', 28800, 75000, 100000, 75000, 100000, 0, 0, 125000, 590, 0),
(5, 'kleiner Eisenminenkomplex', 36000, 10000, 13000, 7500, 17500, 5, 20, 20000, 120, 2500),
(6, 'VV4A Walzwerk', 10800, 3000, 1200, 0, 500, 0, 100, 1000, 100, 0),
(7, 'Eiscrusher der Sirius Corp, Typ Glace la mine', 28800, 750, 750, 0, 100, 0, 0, 750, 0, 0),
(8, 'kleiner Stahlkomplex', 36000, 5000, 7500, 5000, 7500, 0, 0, 10000, 50, 1000),
(9, 'Mondbergwerk', 10800, 1500, 3000, 0, 500, 0, 0, 1000, 50, 0),
(10, 'Area 42 (unterirdischer Forschungskomplex)', 21600, 15000, 19000, 12000, 25000, 0, 0, 25000, 200, 25000),
(11, 'Eisschmelzanlage AlphaEins', 43200, 15000, 15000, 10000, 15000, 1500, 0, 25000, 75, 0),
(12, 'kleines Forschungslabor', 14400, 1000, 250, 0, 0, 0, 10, 500, 50, 0),
(13, 'große Eisenmine', 28800, 500, 300, 0, 50, 0, 0, 300, 5, 0),
(14, 'orbitaler Forschungskomplex', 21600, 7500, 10000, 5000, 10000, 0, 100, 20000, 170, 5000),
(15, 'großes Stahlwerk', 28800, 750, 750, 20, 50, 0, 0, 300, 5, 0),
(16, 'Wasserwerk', 28800, 1250, 500, 50, 0, 50, 0, 500, 0, 0),
(17, 'großes Forschungslabor', 21600, 3000, 1250, 0, 1000, 0, 100, 2000, 100, 500),
(18, 'großes VV4A-Walzwerk', 28800, 15000, 6000, 0, 2500, 0, 500, 5000, 200, 0);

UPDATE `prefix_schiffstyp` SET `schiff`='bürokratischer Formularbomber', `abk`='Formularbomber' WHERE `prefix_schiffstyp`.`id_iw`='21';

DELETE FROM `prefix_params` WHERE `name` = 'hour';

INSERT INTO `prefix_params` (`name` ,`value` ,`text`) VALUES 
	('hour_eisen', '36', ''),
	('hour_stahl', '36', ''),
	('hour_vv4a', '36', ''),
	('hour_chemie', '36', ''),
	('hour_eis', '36', ''),
	('hour_wasser', '36', ''),
	('hour_energie', '36', '');