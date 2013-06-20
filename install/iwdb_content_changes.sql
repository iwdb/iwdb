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

TRUNCATE `prefix_schiffstyp`;
INSERT INTO `prefix_schiffstyp` (`id`, `schiff`, `abk`, `typ`, `bild`, `id_iw`, `kosten_eisen`, `kosten_stahl`, `kosten_vv4a`, `kosten_chemie`, `kosten_eis`, `kosten_wasser`, `kosten_energie`, `kosten_bev`, `kosten_creds`, `GeschwindigkeitSol`, `GeschwindigkeitGal`, `canLeaveGalaxy`, `canBeTransported`, `VerbrauchChemie`, `VerbrauchEnergie`, `angriff`, `waffenklasse`, `verteidigung`, `panzerung_kinetisch`, `panzerung_elektrisch`, `panzerung_gravimetrisch`, `Schilde`, `accuracy`, `mobility`, `numEscort`, `EscortBonusAtt`, `EscortBonusDef`, `werftTyp`, `dauer`, `bestellbar`, `isTransporter`, `klasse1`, `klasse2`, `bev`, `isCarrier`, `shipKapa1`, `shipKapa2`, `shipKapa3`, `aktualisiert`) VALUES
(1, 'Sonde X11', 'Sonde X11', '6. Sonden/Carrier', 'x11_k', 13, 0, 10, 0, 25, 0, 0, 40, 0, NULL, 10000, 60000, 0, 1, 2, 0, 0, 'keine', 4, 100, 100, 100, 0, 0, 500, 0, 1, 1, 'kleine', 360, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360139577),
(2, 'Terminus Sonde', 'Terminus Sonde', '6. Sonden/Carrier', '', 24, 0, 16, 0, 50, 10, 0, 40, 1, NULL, 11000, 75000, 1, 1, 2, 0, 0, 'keine', 5, 100, 100, 100, 0, 0, 500, 0, 1, 1, 'kleine', 720, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360139709),
(4, 'Lurch (Systemtransporter Klasse 2)', 'Lurch', '1. Frachter', 'lurch_k', 11, 0, 1250, 0, 300, 50, 50, 800, 15, 75, 400, 3900, 0, 0, 10, 0, 0, 'keine', 100, 100, 100, 100, 0, 0, 30, 0, 1, 1, 'kleine', 21600, 1, 1, 0, 2000, 0, 0, 0, 0, 0, 1360139554),
(5, 'Gorgol 9 (Hyperraumtransporter Klasse 1)', 'Gorgol', '1. Frachter', 'gorgol9_k', 15, 0, 2300, 0, 750, 0, 0, 2000, 5, NULL, 400, 3000, 1, 0, 10, 0, 0, 'keine', 200, 100, 100, 100, 0, 0, 40, 0, 1, 1, 'mittlere', 36000, 1, 1, 20000, 0, 0, 0, 0, 0, 0, 1360139598),
(6, 'Eisbär (Hyperraumtransporter Klasse 2)', 'Eisbär', '1. Frachter', '', 17, 0, 2500, 0, 1250, 100, 100, 1250, 5, NULL, 400, 3000, 1, 0, 15, 0, 0, 'keine', 200, 100, 100, 100, 0, 0, 50, 0, 1, 1, 'mittlere', 28800, 1, 1, 0, 10000, 0, 0, 0, 0, 0, 1360139619),
(7, 'Kampfbasis Alpha', 'KB Alpha', '2. Zivile Schiffe', '', 54, 10000, 7500, 750, 5900, 0, 0, 5000, 150, NULL, 450, 3600, 1, 0, 35, 0, 0, 'keine', 500, 100, 100, 100, 0, 0, 30, 0, 1, 1, 'mittlere', 28800, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141259),
(8, 'Atombomber', 'Atombomber', '3. Jäger &amp; Co', '', 21, 1000, 1750, 650, 750, 0, 0, 500, 1, NULL, 350, 0, 0, 1, 15, 0, 10, 'kinetisch', 45, 100, 75, 50, 0, 35, 25, 0, 1, 1, 'kleine', 7200, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360139683),
(9, 'Slayer', 'Slayer', '4. Korvetten/Zerstörer', 'slayer_k', 40, 0, 2000, 0, 900, 0, 0, 2000, 35, NULL, 300, 3000, 0, 1, 20, 0, 35, 'kinetisch', 100, 75, 75, 75, 0, 75, 75, 0, 1, 1, 'kleine', 18000, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141130),
(10, 'Vendeta', 'Vendeta', '4. Korvetten/Zerstörer', 'vendetta_k', 41, 0, 3000, 900, 1000, 0, 0, 2800, 50, NULL, 420, 4500, 1, 0, 20, 0, 55, 'elektrisch', 120, 100, 100, 100, 0, 100, 75, 0, 1, 1, 'mittlere', 21600, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141139),
(12, 'Hitman', 'Hitman', '4. Korvetten/Zerstörer', 'hitman_k', 44, 4000, 5000, 1500, 2000, 0, 0, 4000, 50, NULL, 550, 4000, 1, 0, 30, 0, 120, 'kinetisch', 280, 100, 100, 80, 0, 75, 55, 5, 1.05, 1.05, 'mittlere', 43200, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141164),
(13, 'Big Daddy', 'Big Daddy', '5. Schlachtschiffe/DN', 'bigdaddy_k', 48, 8000, 10000, 4900, 7500, 0, 0, 12000, 160, NULL, 400, 4900, 1, 0, 50, 0, 600, 'kinetisch', 980, 100, 100, 100, 0, 50, 40, 10, 1.1, 1.2, 'große', 72000, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141198),
(14, 'Scout', 'Scout', '2. Zivile Schiffe', 'scout_k', 8, 100, 250, 0, 250, 0, 0, 500, 5, NULL, 450, 1800, 0, 0, 15, 0, 2, 'kinetisch', 20, 75, 75, 50, 0, 45, 50, 0, 1, 1, 'kleine', 5040, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360139484),
(16, 'Crux (Systemtransporter Kolonisten)', 'Crux', '1. Frachter', 'crux_k', 10, 0, 1200, 0, 500, 0, 150, 750, 15, 50, 400, 3000, 0, 0, 10, 0, 0, 'keine', 100, 100, 100, 100, 0, 0, 30, 0, 1, 1, 'kleine', 21600, 1, 1, 0, 0, 500, 0, 0, 0, 0, 1360139529),
(18, 'Robominer V1', 'Robominer', '2. Zivile Schiffe', '', 55, 10000, 7500, 750, 5900, 0, 0, 10000, 150, NULL, 450, 3600, 1, 0, 35, 0, 0, 'keine', 500, 100, 100, 100, 0, 0, 30, 0, 1, 1, 'mittlere', 36000, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141292),
(62, 'Sheep', 'Sheep', '3. Jäger &amp; Co', 'sheep_k', 22, 100, 400, 50, 450, 0, 0, 600, 1, NULL, 350, 0, 0, 1, 7, 0, 10, 'kinetisch', 35, 75, 75, 50, 0, 75, 50, 0, 1, 1, 'kleine', 2880, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360139692),
(63, 'Lionheart', 'Lionheart', '4. Korvetten/Zerstörer', 'lionheart_k', 27, 0, 1550, 0, 850, 0, 0, 800, 10, NULL, 350, 4000, 0, 1, 20, 0, 20, 'kinetisch', 40, 75, 75, 75, 0, 65, 65, 0, 1, 1, 'kleine', 9000, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360139748),
(64, 'Kamel Z-98 (Hyperraumtransporter Klasse 1)', 'Kamel', '1. Frachter', '', 59, 0, 6500, 1200, 3550, 0, 0, 5000, 25, NULL, 500, 4500, 1, 0, 20, 0, 0, 'keine', 10, 50, 50, 50, 0, 0, 5, 0, 1, 1, 'große', 43200, 1, 1, 75000, 0, 0, 0, 0, 0, 0, 1360141323),
(65, 'Shark', 'Shark', '3. Jäger &amp; Co', 'shark_k', 23, 150, 500, 150, 550, 0, 0, 800, 1, NULL, 450, 0, 0, 1, 5, 0, 12, 'elektrisch', 40, 100, 100, 50, 0, 80, 85, 0, 1, 1, 'kleine', 3600, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360139700),
(66, 'INS-03A (interstellares Kolonieschiff)', 'INS-03A', '2. Zivile Schiffe', '', 18, 100000, 25000, 10000, 25000, 5000, 5000, 30000, 50, 10000, 300, 2000, 1, 0, 25, 0, 0, 'keine', 790, 100, 100, 100, 0, 0, 15, 0, 1, 1, 'mittlere', 172800, 1, 1, 10000, 1000, 500, 0, 0, 0, 0, 1360139629),
(67, 'Succubus', 'Succubus', '4. Korvetten/Zerstörer', '', 46, 9000, 15500, 6550, 12000, 0, 0, 10000, 100, NULL, 670, 6000, 1, 0, 35, 0, 360, 'elektrisch', 500, 100, 100, 100, 800, 75, 65, 5, 1.05, 1.05, 'mittlere', 54000, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141182),
(68, 'Kolpor (Hyperraumtransporter Kolonisten)', 'Kolpor', '1. Frachter', '', 16, 0, 2000, 0, 500, 0, 0, 2000, 5, NULL, 400, 3800, 1, 0, 10, 0, 0, 'keine', 200, 100, 100, 100, 0, 0, 50, 0, 1, 1, 'mittlere', 28800, 1, 1, 0, 0, 1000, 0, 0, 0, 0, 1360139609),
(70, 'Waschbär (Hyperraumtransporter Klasse 2)', 'Waschbär', '1. Frachter', '', 60, 0, 6000, 1000, 6000, 100, 100, 3000, 25, NULL, 500, 4300, 1, 0, 25, 0, 0, 'keine', 25, 75, 75, 75, 0, 0, 10, 0, 1, 1, 'große', 43200, 1, 1, 0, 50000, 0, 0, 0, 0, 0, 1360141332),
(71, 'Kronk', 'Kronk', '5. Schlachtschiffe/DN', '', 49, 20000, 20500, 9150, 19900, 0, 0, 29850, 300, NULL, 450, 5700, 1, 0, 70, 100, 1000, 'elektrisch', 1050, 150, 150, 120, 1400, 50, 45, 10, 1.1, 1.2, 'große', 86400, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141208),
(72, 'Kampfbasis Beta', 'KB Beta', '2. Zivile Schiffe', '', 61, 50000, 45500, 5750, 30000, 0, 0, 25000, 300, NULL, 500, 4250, 1, 0, 45, 0, 0, 'keine', 1500, 100, 100, 100, 0, 0, 25, 0, 1, 1, 'mittlere', 43200, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141340),
(73, 'Manta', 'Manta', '3. Jäger &amp; Co', 'manta_k', 31, 300, 700, 250, 700, 0, 0, 1100, 1, NULL, 600, 0, 0, 1, 6, 0, 15, 'elektrisch', 45, 100, 100, 75, 0, 85, 110, 0, 1, 1, 'kleine', 4320, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360140042),
(74, 'X12 (Carrier)', 'X12', '6. Sonden/Carrier', '', 53, 20000, 5800, 4565, 8000, 0, 0, 35000, 150, 1500, 600, 4900, 1, 0, 300, 0, 0, 'keine', 3500, 100, 100, 100, 540, 0, 15, 0, 1, 1, 'mittlere', 86400, 1, 0, 0, 0, 0, 1, 100, 100, 0, 1360141249),
(75, 'Hunter', 'Hunter', '4. Korvetten/Zerstörer', 'hunter_k', 14, 0, 1850, 500, 1000, 0, 0, 1000, 25, NULL, 450, 3500, 1, 0, 15, 0, 30, 'elektrisch', 70, 100, 100, 75, 0, 100, 80, 0, 1, 1, 'kleine', 10800, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360139587),
(76, 'Zeus', 'Zeus', '5. Schlachtschiffe/DN', '', 51, 25000, 40000, 15000, 25000, 0, 0, 40000, 500, NULL, 200, 5600, 1, 0, 170, 250, 2580, 'gravimetrisch', 1950, 95, 200, 130, 2000, 35, 30, 20, 1.15, 1.3, 'Dreadnought', 126000, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141225),
(77, 'Stormbringer', 'Stormbringer', '3. Jäger &amp; Co', '', 34, 2000, 2100, 1200, 1000, 0, 0, 1000, 2, NULL, 400, 0, 0, 1, 17, 0, 15, 'kinetisch', 40, 100, 100, 75, 0, 40, 30, 0, 1, 1, 'kleine', 10800, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360140070),
(78, 'Sonde X13', 'Sonde X13', '6. Sonden/Carrier', '', 64, 0, 25, 5, 100, 0, 0, 100, 0, NULL, 13000, 85000, 1, 1, 1, 1, 0, 'keine', 0, 100, 100, 100, 0, 0, 500, 0, 1, 1, 'kleine', 900, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141365),
(79, 'Victim', 'Victim', '4. Korvetten/Zerstörer', '', 37, 0, 3100, 750, 2100, 0, 0, 1500, 30, NULL, 500, 3900, 1, 0, 25, 0, 45, 'elektrisch', 90, 100, 100, 100, 50, 140, 75, 0, 1, 1, 'kleine', 10800, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141101),
(80, 'Nova', 'Nova', '3. Jäger &amp; Co', '', 35, 3000, 2800, 1800, 1500, 0, 0, 1500, 3, NULL, 450, 0, 0, 1, 20, 0, 20, 'kinetisch', 35, 80, 100, 100, 0, 50, 30, 0, 1, 1, 'kleine', 12600, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360140289),
(81, 'Terraformer Alpha', 'Terraformer', '2. Zivile Schiffe', '', 25, 0, 1500, 0, 1500, 100, 100, 1500, 0, 1500, 300, 4000, 0, 0, 15, 0, 0, 'keine', 75, 100, 100, 100, 0, 0, 25, 0, 1, 1, 'kleine', 18000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360139719),
(82, 'Eraser', 'Eraser', '4. Korvetten/Zerstörer', '', 39, 0, 2300, 870, 4000, 0, 0, 3150, 25, NULL, 630, 4900, 1, 0, 45, 55, 25, 'gravimetrisch', 25, 80, 80, 80, 0, 55, 60, 0, 1, 1, 'kleine', 14400, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1360141120),
(83, 'Widowmaker', 'Widowmaker', '4. Korvetten/Zerstörer', '', 43, 0, 3600, 1000, 3000, 0, 0, 3350, 80, NULL, 360, 6700, 1, 0, 60, 75, 45, 'elektrisch', 100, 60, 60, 50, 0, 100, 85, 0, 1, 1, 'mittlere', 28800, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141156),
(84, 'Sirius X300', 'Sirius X300', '3. Jäger &amp; Co', 'x300_k', 30, 500, 1500, 250, 700, 0, 0, 1500, 1, NULL, 0, 0, 0, 1, 0, 0, 45, 'kinetisch', 25, 80, 80, 80, 0, 120, 90, 0, 1, 1, 'kleine', 4680, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360140032),
(85, 'Nightcrawler', 'Nightcrawler', '3. Jäger &amp; Co', 'nc_k', 33, 1500, 1300, 250, 1000, 0, 0, 1000, 1, NULL, 500, 0, 0, 1, 10, 5, 35, 'elektrisch', 15, 60, 60, 60, 0, 90, 65, 0, 1, 1, 'kleine', 4320, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360140062),
(86, 'Mule (Carrier)', 'Mule (Carrier)', '6. Sonden/Carrier', '', 28, 35000, 18000, 9000, 12000, 0, 0, 40000, 250, 2000, 350, 4000, 1, 0, 350, 100, 0, 'keine', 4000, 100, 100, 100, 1200, 0, 25, 0, 1, 1, 'große', 100800, 0, 0, 0, 0, 0, 1, 100, 80, 0, 1360139757),
(88, 'Silent Sorrow', 'Silent Sorrow', '4. Korvetten/Zerstörer', '', 45, 5000, 4500, 850, 27500, 0, 0, 40000, 65, NULL, 590, 5000, 1, 0, 90, 30, 130, 'elektrisch', 400, 100, 120, 100, 0, 95, 70, 0, 1, 1, 'mittlere', 68400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141172),
(91, 'Nepomuk', 'Nepomuk', '3. Jäger &amp; Co', '', 36, 4000, 1000, 3250, 3500, 0, 0, 4000, 5, NULL, 250, 0, 0, 1, 20, 5, 5, 'kinetisch', 15, 75, 100, 100, 0, 40, 20, 0, 1, 1, 'kleine', 14400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360140875),
(92, 'Flughund (Hyperraumtransporter Klasse 1)', 'Flughund', '1. Frachter', '', 70, 85000, 50000, 15000, 30000, 0, 0, 50000, 120, NULL, 550, 4700, 1, 0, 50, 90, 0, 'keine', 35, 40, 50, 40, 0, 0, 5, 0, 1, 1, 'Dreadnought', 86400, 0, 1, 400000, 0, 0, 0, 0, 0, 0, 1360141404),
(99, 'Seepferdchen (Hyperraumtransporter Klasse 2)', 'Seepferdchen', '1. Frachter', '', 71, 75000, 50000, 7500, 50000, 1500, 1500, 75000, 50, NULL, 500, 4500, 1, 0, 60, 110, 0, 'keine', 50, 95, 75, 55, 0, 0, 5, 0, 1, 1, 'Dreadnought', 86400, 0, 1, 0, 250000, 0, 0, 0, 0, 0, 1360141413),
(100, 'Kampfbasis Gamma', 'KB Gamma', '2. Zivile Schiffe', '', 67, 190000, 176000, 65000, 210000, 0, 0, 200000, 900, NULL, 430, 3600, 1, 0, 120, 45, 0, 'keine', 5500, 100, 100, 100, 0, 0, 10, 0, 1, 1, 'Dreadnought', 57600, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141379),
(101, 'Crawler', 'Crawler', '4. Korvetten/Zerstörer', '', 42, 0, 6200, 2600, 4500, 50, 0, 2000, 75, 500, 800, 8000, 1, 0, 25, 50, 85, 'elektrisch', 120, 80, 150, 80, 250, 120, 80, 0, 1, 1, 'mittlere', 30600, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141148),
(102, 'Gargoil (Carrier)', 'Gargoil (Carrier)', '6. Sonden/Carrier', '', 57, 55000, 75000, 27500, 45000, 0, 0, 50000, 450, 2000, 200, 5000, 1, 0, 50, 350, 0, 'keine', 5000, 100, 100, 100, 2500, 0, 0, 0, 1, 1, 'Dreadnought', 172800, 0, 0, 0, 0, 0, 1, 300, 0, 300, 1360141305),
(103, 'Gatling', 'Gatling', '4. Korvetten/Zerstörer', '', 38, 0, 3400, 850, 2500, 0, 0, 2000, 30, NULL, 750, 5900, 1, 0, 25, 20, 65, 'elektrisch', 85, 75, 120, 100, 65, 150, 80, 0, 1, 1, 'kleine', 14400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141111),
(157, 'Systrans (Systemtransporter Klasse 1)', 'Systrans', '1. Frachter', '', 9, 0, 1000, 0, 500, 0, 0, 500, 15, NULL, 370, 3900, 0, 0, 10, 0, 0, 'keine', 100, 100, 100, 100, 0, 0, 25, 0, 1, 1, 'kleine', 10800, 1, 1, 5000, 0, 0, 0, 0, 0, 0, 1360139505),
(158, 'KISS-01 (Systemkolonieschiff)', 'KISS-01', '2. Zivile Schiffe', '', 12, 30000, 15000, 0, 15000, 1000, 1000, 25000, 50, 5000, 450, 3000, 0, 0, 15, 0, 0, 'keine', 350, 100, 100, 100, 0, 0, 15, 0, 1, 1, 'kleine', 172800, 1, 1, 10000, 1000, 150, 0, 0, 0, 0, 1360139566),
(159, 'Artefaktsammelbasis Alpha', 'AB Alpha', '2. Zivile Schiffe', '', 72, 25000, 20500, 1000, 15000, 0, 0, 25000, 500, NULL, 300, 2800, 1, 0, 55, 0, 0, 'keine', 280, 100, 100, 100, 0, 0, 0, 0, 1, 1, 'mittlere', 86400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141421),
(160, 'Pflaumenmus (Carrier)', 'Pflaumenmus', '6. Sonden/Carrier', '', 20, 10000, 5000, 0, 5000, 0, 0, 15000, 50, 1000, 520, 3500, 1, 0, 230, 0, 0, 'keine', 3000, 100, 100, 100, 0, 0, 20, 0, 1, 1, 'mittlere', 64800, 1, 0, 0, 0, 0, 1, 50, 40, 0, 1360139672),
(169, 'Weihnachtsmannschlitten (Transporter)', 'Weihnachtsmannschlitten', 'alte Schiffe', '', 63, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 400, 3700, 1, 0, 0, 0, 0, 'keine', 4000, 100, 100, 100, 0, 0, 160, 0, 1, 1, '', 0, 0, 1, 2000000, 2000000, 0, 0, 0, 0, 0, 1360141355),
(237, 'Settlers Delight', 'Settlers Delight', '1. Frachter', '', 76, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 400, 4000, 0, 0, 5, 0, 0, 'keine', 1000, 100, 100, 100, 0, 0, 500, 0, 1, 1, '', 0, 0, 1, 5000, 2000, 200, 0, 0, 0, 0, 1360141450),
(300, 'Artefaktsammelbasis Beta', 'AB Beta', '2. Zivile Schiffe', '', 73, 150000, 175000, 28000, 90000, 0, 0, 100000, 500, NULL, 300, 2900, 1, 0, 140, 120, 0, 'keine', 580, 100, 100, 100, 0, 0, 0, 0, 1, 1, 'Dreadnought', 172800, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141435),
(161, 'Raketentransporter (mit Plutoniumdrachenantrieb)', 'Raketen-Transe', 'alte Schiffe', '', 69, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1000, 12000, 1, 0, 5000, 5000, 0, 'keine', 10000, 200, 200, 200, 5700, 0, 120, 0, 1, 1, '', 0, 0, 1, 250000, 250000, 0, 0, 0, 0, 0, 1360141391),
(301, 'Rosa-Plüschhasen-Spezialschiff', 'Rosa-Plüschhasen-Spezialschiff', 'admin', '', 26, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2000, 15000, 1, 0, 10, 10, 250, 'elektrisch', 550, 100, 100, 100, 1025, 100, 100, 0, 1, 1, '', 0, 0, 1, 10000, 5000, 500, 0, 0, 0, 0, 1360139738),
(302, 'Nimbus BP-1729', 'Nimbus BP-1729', 'admin', '', 29, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4800, 85000, 1, 0, 0, 120, 360, 'unbekannt', 980, 150, 200, 200, 2500, 120, 120, 0, 1, 1, 'Dreadnought', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360140013),
(303, 'Sirius XPi', 'Sirius XPi', 'admin', '', 47, 15000, 19250, 6150, 12300, 0, 0, 9500, 80, NULL, 890, 8000, 1, 0, 35, 45, 450, 'gravimetrisch', 400, 80, 120, 120, 790, 65, 75, 5, 1.1, 1.1, 'mittlere', 72000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141190),
(304, 'Quasal', 'Quasal', 'admin', '', 50, 20000, 25500, 10500, 20400, 0, 0, 15000, 310, NULL, 380, 6000, 1, 0, 70, 200, 1450, 'gravimetrisch', 950, 120, 110, 100, 1000, 45, 35, 10, 1.1, 1.2, 'große', 108000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141217),
(305, 'Tempest', 'Tempest', 'admin', '', 52, 50000, 65000, 22000, 35000, 0, 0, 59000, 750, NULL, 180, 5450, 1, 0, 200, 275, 3800, 'gravimetrisch', 2900, 95, 220, 130, 3500, 30, 20, 20, 1.15, 1.3, 'Dreadnought', 162000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141238),
(306, 'TAG Vario Kreuzer', 'TAG Vario Kreuzer', 'admin', '', 58, 15000, 17000, 5890, 11000, 0, 0, 12000, 75, NULL, 700, 6800, 1, 0, 40, 40, 420, 'elektrisch', 300, 100, 100, 100, 650, 100, 70, 0, 1, 1, 'mittlere', 64800, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141314),
(307, 'Kampfhuhn', 'Kampfhuhn', 'alte Schiffe', '', 77, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 500, 5000, 1, 0, 10, 10, 0, 'keine', 100, 100, 100, 100, 0, 100, 500, 0, 1, 1, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360141458),
(309, 'Downbringer', 'Downbringer', 'alte Schiffe', '', 32, 450, 1200, 750, 800, 0, 0, 1300, 2, NULL, 850, 0, 0, 1, 9, 5, 25, 'elektrisch', 60, 100, 130, 100, 30, 150, 145, 0, 1, 1, 'kleine', 5040, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1360140052),
(310, 'Stopfente', 'Stopfente', 'admin', '', NULL, 10, 10, 5, 5, 0, 2, 5, 0, NULL, 400, 3000, 1, 0, 1, 0, 1, 'kinetisch', 2, 100, 100, 100, 0, 100, 100, 0, 1, 1, 'kleine', 360, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1368435726),
(311, 'SuperStopfente', 'Superstopfente', 'admin', '', NULL, 10, 10, 5, 5, 0, 2, 5, 0, NULL, 400, 3000, 1, 0, 1, 0, 1, 'kinetisch', 2000, 100, 100, 100, 0, 100, 500, 0, 1, 1, 'kleine', 360, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1368435737);

INSERT INTO `prefix_gebbaukosten` (`id`, `name`, `dauer`, `kosten_eisen`, `kosten_stahl`, `kosten_vv4a`, `kosten_chemie`, `kosten_eis`, `kosten_wasser`, `kosten_energie`, `kosten_bev`, `kosten_creds`) VALUES
(1, 'kleine Chemiefabrik', 10800, 150, 50, 0, 0, 0, 0, 50, 10, 0),
(2, 'Icecrusher V3b', 43200, 25000, 25000, 17500, 25000, 0, 0, 35000, 50, 0),
(3, 'große Chemiefabrik', 28800, 750, 300, 0, 100, 0, 0, 300, 5, 0),
(4, 'kleiner chemischer Fabrikkomplex', 36000, 5000, 3000, 1500, 1000, 0, 0, 7500, 25, 0),
(5, 'geheimes Vulkanlabor', 28800, 75000, 100000, 75000, 100000, 0, 0, 125000, 590, 0),
(6, 'kleiner Eisenminenkomplex', 36000, 10000, 13000, 7500, 17500, 5, 20, 20000, 120, 2500),
(7, 'VV4A Walzwerk', 10800, 3000, 1200, 0, 500, 0, 100, 1000, 100, 0),
(8, 'Eiscrusher der Sirius Corp, Typ Glace la mine', 28800, 750, 750, 0, 100, 0, 0, 750, 0, 0),
(9, 'kleiner Stahlkomplex', 36000, 5000, 7500, 5000, 7500, 0, 0, 10000, 50, 1000),
(10, 'Mondbergwerk', 10800, 1500, 3000, 0, 500, 0, 0, 1000, 50, 0),
(11, 'Area 42 (unterirdischer Forschungskomplex)', 21600, 15000, 19000, 12000, 25000, 0, 0, 25000, 200, 25000),
(12, 'Eisschmelzanlage AlphaEins', 43200, 15000, 15000, 10000, 15000, 1500, 0, 25000, 75, 0),
(13, 'kleines Forschungslabor', 14400, 1000, 250, 0, 0, 0, 10, 500, 50, 0),
(14, 'kleine Eisenmine', 10800, 100, 50, 0, 0, 0, 0, 50, 10, 0),
(15, 'Design Eiscrusher der Sirius Corp', 14400, 150, 150, 0, 0, 0, 0, 150, 0, 0),
(16, 'Tauchsieder MkIV', 14400, 250, 100, 0, 0, 10, 0, 50, 0, 0),
(17, 'kleines Stahlwerk', 10800, 150, 50, 0, 0, 0, 0, 50, 10, 0),
(18, 'orbitaler Forschungskomplex', 21600, 7500, 10000, 5000, 10000, 0, 100, 20000, 170, 5000),
(19, 'großes Stahlwerk', 28800, 750, 750, 20, 50, 0, 0, 300, 5, 0),
(20, 'Wasserwerk', 28800, 1250, 500, 50, 0, 50, 0, 500, 0, 0);
