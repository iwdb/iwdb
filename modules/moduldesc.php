<?php
/*****************************************************************************/
/* moduldesc.php                                                           */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* ========================================================================= */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* ========================================================================= */
/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */
/*****************************************************************************/
/* This program is free software; you can redistribute it and/or modify it   */
/* under the terms of the GNU General Public License as published by the     */
/* Free Software Foundation; either version 2 of the License, or (at your    */
/* option) any later version.                                                */
/*                                                                           */
/* This program is distributed in the hope that it will be useful, but       */
/* WITHOUT ANY WARRANTY; without even the implied warranty of                */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General */
/* Public License for more details.                                          */
/*                                                                           */
/* The GNU GPL can be found in LICENSE in this directory                     */
/*****************************************************************************/

/*****************************************************************************/
/* moduldesc.php                                                             */
/* Hier werden die Beschreibungen für die Module gespeichert                 */
/*****************************************************************************/
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!";
	exit;
}

$modulary["m_colors"]["name"] = "m_colors";
$modulary["m_colors"]["titel"] = "Farbtabelle";
$modulary["m_colors"]["desc"] = "Das Colors-Modul bietet eine Anzeige s&auml;mtlicher in Icewars f&uuml;r die farbige Markierung von Links wie den Planetennamen relevanten Hexadezimal-Farbcodes aus der man diese einfach rauskopieren kann.";

$modulary["m_default"]["name"] = "m_default";
$modulary["m_default"]["titel"] = "Defaultmodul";
$modulary["m_default"]["desc"] =  "Das Default-Modul dient als Vorlage f&uuml;r die anderen Module und hat keine Funktion";

$modulary["m_effektiv"]["name"] = "m_effektiv";
$modulary["m_effektiv"]["titel"] = "Effektivit&auml;tsmodul";
$modulary["m_effektiv"]["desc"] =  "Modul zum anzeigen der Schiffsklasseneffektivit&auml;ten, beruhend auf der Schiffklasseneffektivit&auml;t ingame";

$modulary["m_frachtkapa"]["name"] = "m_frachtkapa";
$modulary["m_frachtkapa"]["titel"] = "Frachtkapazit&auml;ten";
$modulary["m_frachtkapa"]["desc"] = "Das Frachtkapazit&auml;ten-Modul dient zur Berechnung der notwendigen Transporteranzahl f&uuml;r eine gegebene Menge Ressourcen";

$modulary["m_polkarte"]["name"] = "m_polkarte";
$modulary["m_polkarte"]["titel"] = "politische Karte";
$modulary["m_polkarte"]["desc"] = "Anzeige einer Universumskarte mit allen Allianzstati";

$modulary["m_research"]["name"] = "m_research";
$modulary["m_research"]["titel"] = "dyn. Techtree";
$modulary["m_research"]["desc"] = "Das Forschungsmodul erlaubt die Darstellung und das Navigieren innerhalb des bereits bekannten Forschungsbaumes. Die notwendigen Forschungen und Geb&auml;ude werden ebenfalls dargestellt.";

$modulary["m_ress"]["name"] = "m_ress";
$modulary["m_ress"]["titel"] = "Produktion";
$modulary["m_ress"]["desc"] = "Dieses Modul dient zur Anzeige der Ressproduktion der Spieler in der Allianz. Dabei wird anhand der Kolo-/Ress&uuml;bersicht der Tagesbedarf bzw. Tagesoutput errechnet.";

$modulary["m_techtree"]["name"] = "m_techtree";
$modulary["m_techtree"]["titel"] = "graph. Techtree";
$modulary["m_techtree"]["desc"] = "Ein graphsicher Technologiebaum, der jede Evolutionsstufe als &Uuml;bersicht mit Forschungsverkn&uuml;pfungen und Informationen anzeigt. Besonders Wert gelegt wurde auf die optische Integration in die AlliDB und schnelle Ladezeiten.";

$modulary["m_transferliste"]["name"] = "m_transferliste";
$modulary["m_transferliste"]["titel"] = "Transfer-Statistik";
$modulary["m_transferliste"]["desc"] = "In der Buddler-Statistik werden die an die Fleeter transferierten Rohstoffe erfasst und statistisch aufbereitet.";

$modulary["m_universe"]["name"] = "m_universe";
$modulary["m_universe"]["titel"] = "zeige Universum";
$modulary["m_universe"]["desc"] = "Dieses Modul f&uuml;gt eine &Uuml;bersicht f&uuml;r das bekannte Universum hinzu.";

$modulary["m_raidview"]["name"] = "m_raidview";
$modulary["m_raidview"]["titel"] = "Raid-Statistik";
$modulary["m_raidview"]["desc"] = "Durch dieses Modul werden eine Raid&uuml;bersicht der Allianzmitglieder sowie detaillierte Raidhistories jedes Mitgliedes eingef&uuml;gt.";

$modulary["m_galastats"]["name"] = "m_galastats";
$modulary["m_galastats"]["titel"] = "Galastatistiken";
$modulary["m_galastats"]["desc"] = "Das Galastatistiken-modul berechnet eine Highscore für Kolonien, Plannipunkte und Kampfbasen für jede Galaxie und für die gesamte Sichtweite";

$modulary["m_kasse"]["name"] = "m_kasse";
$modulary["m_kasse"]["titel"] = "Allianzkasse";
$modulary["m_kasse"]["desc"] = "Das Allianzkassenmodul dient zur Speicherung und &uuml;bersichtlichen Anzeige von Daten aus der Allianzkasse";

$modulary["m_building"]["name"] = "m_building";
$modulary["m_building"]["titel"] = "Gebaeudeanzeige";
$modulary["m_building"]["desc"] = "Ermoeglicht das Anzeigen der Gebaeude. <br> Dieses Modul braucht eine Installation des dynamischen Techtrees!";

global $modulary;

?>
