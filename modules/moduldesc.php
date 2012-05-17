<?php
/*****************************************************************************/
/* moduldesc.php                                                             */
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

$modulary["m_default"]["name"] = "m_default";
$modulary["m_default"]["titel"] = "Defaultmodul";
$modulary["m_default"]["desc"] =  "Das Default-Modul dient als Vorlage f&uuml;r die anderen Module und hat keine Funktion";

$modulary["m_universe"]["name"] = "m_universe";
$modulary["m_universe"]["titel"] = "zeige Universum";
$modulary["m_universe"]["desc"] =  "Dieses Modul f&uuml;gt eine &Uuml;bersicht f&uuml;r das bekannte Universum hinzu.";

$modulary["m_transferliste"]["name"] = "m_transferliste";
$modulary["m_transferliste"]["titel"] = "Transfer-Statistik";
$modulary["m_transferliste"]["desc"] =  "In der Buddler-Statistik werden die an die Fleeter transferierten Rohstoffe erfasst und statistisch aufbereitet.";

$modulary["m_techtree"]["name"] = "m_techtree";
$modulary["m_techtree"]["titel"] = "graph. Techtree";
$modulary["m_techtree"]["desc"] =  "Ein graphischer Technoligiebaum, der jede Evolutionsstufe als &Uuml;bersicht mit Forschungsverkn&uuml;pfungen und Informationen anzeigt. Besonders Wert gelegt wurde auf die optische Integration in die AlliDB und schnelle Ladezeiten.";

$modulary["m_sprengung"]["name"] = "m_sprengung";
$modulary["m_sprengung"]["titel"] = "zeige Sprengungen";
$modulary["m_sprengung"]["desc"] =  "Zeigt an wann Planeten vorraussichtlich gesprengt werden";

$modulary["m_ress"]["name"] = "m_ress";
$modulary["m_ress"]["titel"] = "Produktion";
$modulary["m_ress"]["desc"] =  "Dieses Modul dient zur Anzeige der Ressproduktion der Spieler in der Allianz. Dabei wird anhand der Kolo-/Ress&uuml;bersicht der Tagesbedarf bzw. Tagesoutput errechnet.";

$modulary["m_research"]["name"] = "m_research";
$modulary["m_research"]["titel"] = "dyn. Techtree";
$modulary["m_research"]["desc"] =  "Das Forschungsmodul erlaubt die Darstellung und das Navigieren innerhalb des bereits bekannten Forschungsbaumes. Die notwendigen Forschungen und Gebäude werden ebenfalls dargestellt.";

$modulary["m_colors"]["name"] = "m_colors";
$modulary["m_colors"]["titel"] = "Farbtabelle";
$modulary["m_colors"]["desc"] = "Das Colors-Modul bietet eine Anzeige sämtlicher in Icewars f&uuml;r die farbige Markierung von Links wie den Planetennamen relevanten Hexadezimal-Farbcodes aus der man diese einfach rauskopieren kann.";

$modulary["m_raidview"]["name"] = "m_raidview";
$modulary["m_raidview"]["titel"] = "Raid-Statistik";
$modulary["m_raidview"]["desc"] = "Durch dieses Modul werden eine Raid&uuml;bersicht der Allianzmitglieder sowie detaillierte Raidhistories jedes Mitgliedes eingef&uuml;gt.";

$modulary["m_polkarte"]["name"] = "m_polkarte";
$modulary["m_polkarte"]["titel"] = "politische Karte";
$modulary["m_polkarte"]["desc"] = "Anzeige einer Universumskarte mit allen Allianzbeziehungen";

$modulary["m_lieferung"]["name"] = "m_lieferung";
$modulary["m_lieferung"]["titel"] = "Lieferung";
$modulary["m_lieferung"]["desc"] =  "Zeigt Informationen zu anfliegenden Lieferungen an";

$modulary["m_kbparser1"]["name"] = "m_kbparser1";
$modulary["m_kbparser1"]["titel"] = "KBParser2";
$modulary["m_kbparser1"]["desc"] =  "KBParser mit einer vielfältigen Ausgabe im BBCode";

$modulary["m_kbparser"]["name"] = "m_kbparser";
$modulary["m_kbparser"]["titel"] = "KBParser";
$modulary["m_kbparser"]["desc"] =  "Ausgabe der Kampfberichte im BBCode";

$modulary["m_kasse"]["name"] = "m_kasse";
$modulary["m_kasse"]["titel"] = "Allianzkasse";
$modulary["m_kasse"]["desc"] = "Das Allianzkassenmodul dient zur Speicherung und &uuml;bersichtlichen Anzeige von Daten aus der Allianzkasse";

$modulary["m_gebaeudeuebersicht"]["name"] = "m_gebaeudeuebersicht";
$modulary["m_gebaeudeuebersicht"]["titel"] = "Gebäude&uuml;bersicht";
$modulary["m_gebaeudeuebersicht"]["desc"] =  "Zeigt die Gebäde&uml;bersicht an";

$modulary["m_galastats"]["name"] = "m_galastats";
$modulary["m_galastats"]["titel"] = "Galastatistiken";
$modulary["m_galastats"]["desc"] = "Das Galastatistiken-Modul berechnet eine Highscore f&uuml;r Kolonien, Planipunkte und Kampfbasen f&uuml;r jede Galaxie und f&uuml;r die gesamte Sichtweite";

$modulary["m_frachtkapa"]["name"] = "m_frachtkapa";
$modulary["m_frachtkapa"]["titel"] = "Frachtkapazitäten";
$modulary["m_frachtkapa"]["desc"] = "Das Frachtkapazitäten-Modul dient zur Berechnung der notwendigen Transporteranzahl f&uuml;r eine gegebene Menge Ressourcen";

$modulary["m_effektiv"]["name"] = "m_effektiv";
$modulary["m_effektiv"]["titel"] = "Effektivitätsmodul";
$modulary["m_effektiv"]["desc"] =  "Modul zum Anzeigen der Schiffsklasseneffektivitäten, beruhend auf der Schiffklasseneffektivität ingame";

$modulary["m_building"]["name"] = "m_building";
$modulary["m_building"]["titel"] = "Gebaeudeanzeige";
$modulary["m_building"]["desc"] = "Ermoeglicht das Anzeigen der Gebaeude. <br> Dieses Modul braucht eine Installation des dynamischen Techtrees!";

$modulary["m_bestellung_schiffe"]["name"] = "m_bestellung_schiffe";
$modulary["m_bestellung_schiffe"]["titel"] = "Schiffe #schiffe";
$modulary["m_bestellung_schiffe"]["desc"] =  "Bestellsystem f&uuml;r Schiffe zur Koordination von Logistikaufträgen im Buddler-Fleeter-System.";

$modulary["m_bestellung"]["name"] = "m_bestellung";
$modulary["m_bestellung"]["titel"] = "Bestellung #ress";
$modulary["m_bestellung"]["desc"] =  "Bestellsystem zur Koordination von Logistikaufträgen im Buddler-Fleeter-System.";

$modulary["m_allystats"]["name"] = "m_allystats";
$modulary["m_allystats"]["titel"] = "Allianzstatistiken";
$modulary["m_allystats"]["desc"] =  "Das Allianzstatistiken-Modul berechnet diverse Daten zu einer Allianz";

$modulary["m_lager"]["name"] = "m_lager";
$modulary["m_lager"]["titel"] = "Lager";
$modulary["m_lager"]["desc"] =  "Lager&uuml;bersicht zur Koordination von Logistikaufträgen im Buddler-Fleeter-System.";

$modulary["m_raid"]["name"] = "m_raid";
$modulary["m_raid"]["titel"] = "Ziele suchen";
$modulary["m_raid"]["desc"] =  "Dieses Modul hilft beim pösen Klauen von Lollis.";

$modulary["m_sitterschleife"]["name"] = "m_sitterschleife";
$modulary["m_sitterschleife"]["titel"] = "Sitterschleife";
$modulary["m_sitterschleife"]["desc"] =  "Dieses Modul dient dem Sitten in Schleife";

$modulary["m_projects"]["name"] = "m_projects";
$modulary["m_projects"]["titel"] = "Projekteverwaltung";
$modulary["m_projects"]["desc"] =  "Verwaltung von Projekten im Bestellsystem";

$modulary["m_research2"]["name"] = "m_research2";
$modulary["m_research2"]["titel"] = "Forschungsprognosen";
$modulary["m_research2"]["desc"] =  "Zeigt alle Forschungen und deren aktuelle Forschungspunkte an und ermittelt daraus die Zeiten, die der User mit seinen FP/h zur Erforschung benötigt.";

$modulary["m_forsch"]["name"] = "m_forsch";
$modulary["m_forsch"]["titel"] = "Forschungsübersicht";
$modulary["m_forsch"]["desc"] =  "Die Forschungsübersicht zeigt die aktuell laufenden Forschungen";

$modulary["m_ressxml_worker"]["name"] = "m_ressxml_worker";
$modulary["m_ressxml_worker"]["titel"] = "Ressourcen-XML Updater";
$modulary["m_ressxml_worker"]["desc"] =  "Ding zum Holen von Ressübersichtsdaten über XML-Übersichts-Links";

$modulary["m_sc"]["name"] = "m_sc";
$modulary["m_sc"]["titel"] = "Sondenkalkulator";
$modulary["m_sc"]["desc"] =  "Berechnet die benötigten Sonden anhand gegebener Sondendeff";

global $modulary;

?>
