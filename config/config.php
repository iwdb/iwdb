<?php
/*****************************************************************************/
/* config.php                                                                */
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
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschafftsprojekt    */
/* von IW-Spielern.                                                          */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete           */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

if (!defined('IRA'))
die('Hacking attempt...');

// Lade die Allianzdefinitionen. Wenn das nicht klappt, ist was falsch.
if (!@include("./config/" . $_SERVER["HTTP_HOST"] . "/configally.php")) {
	if (!@include("./config/configally.php")) { 
		die( "Error:<br><b>Allianz Konfiguration (configally.php) wurde nicht geladen!</b>");
	}  
} 
  
// ***************************************************************************
//  Ab hier sind Angaben die nicht unbedingt geändert werden muessen.
//

// Farben in der Karte der Allianzen (own - eigene Allianz/Wings, NAP, iNAP, VB, Krieg)
$config_allianzstatus = array();
$config_allianzstatus['own']   = "#C4F493";
$config_allianzstatus['wing']   = "#E6F6A5";
$config_allianzstatus['NAP']   = "#7C9CF1";
$config_allianzstatus['iNAP']  = "#8DADF2";
$config_allianzstatus['VB']    = "#4A71D5";
$config_allianzstatus['Krieg'] = "#E84528";
$config_allianzstatus['noraid'] = "#DD9911";

// Farben von Stargates, Schwarze Loecher, reservierten Planeten
$config_color = array();
$config_color['Stargate']      = "#A0BFCD";
$config_color['SchwarzesLoch'] = "#3F6778";
$config_color['reserviert']    = "#CCDCE3";
$config_color['last24'] = "00AACC";

// Tabellennamen - Definition des Einstiegsnamens
$db_tb_iwdbtabellen = $db_prefix . "iwdbtabellen";

// Die restlichen Tabellennamen werden aus der DB gelesen.
$sql = "SELECT name FROM " . $db_prefix . "iwdbtabellen";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 
           'Could not query config information.', '', 
           __FILE__, __LINE__, $sql);
           
while($row = $db->db_fetch_array($result)) {
  $tbname = "db_tb_" . $row['name'];
  ${$tbname} = $db_prefix . $row['name'];
}

// Basisdefinitionen fuer Zeitraueme.
$MINUTES = 60;
$HOURS   = 60 * $MINUTES;
$DAYS    = 24 * $HOURS;

// Das aktuelle Datum wird pro Skriptaufruf nur einmal geholt, +-x kann
// entsprechend hier geändert werden
$config_date = time();

// Zeit, wie lange die SID aktuell bleibt (in Sekunden)
$config_sid_timeout = 1 * $HOURS;

// zugelassene Zeichen fuer SIDs
$config_sid_string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890-_";

// Länge der SID
$config_sid_length = 20;

// zugelassene Zeichen fuer Passwoerter
$config_password_string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890-_&%\$�!/()=?';

// Cookiename
$config_cookie_name = "iwdb";

// Zeit, wie lange das Cookie gueltig ist in Sekunden
$config_cookie_timeout = 365 * $DAYS;

// Zeit, wie lange ein User als online angezeigt wird in Sekunden
$config_counter_timeout = 4 * $MINUTES;

// Zeit, wie lange ein Username gesperrt wird nach x falschen Loginversuchen in Sekunden
$config_wronglogin_timeout = 6 * $HOURS;

// nach wie vielen Loginversuchen soll der Username gesperrt werden
$config_wronglogins = 5;

// Zeitformat
$config_timeformat = "%d.%m.%Y %H:%M";
// Zeitformat der Memberregistrierung in Icewars
$config_members_timeformat = "%d.%m.%y";
// Zeit, wie oft Sitterseiten neu geladen werden sollen in Sekunden
$config_refresh_timeout = 1 * $MINUTES;
// Zeit, nach wie vielen Sekunden es wieder Punkte fuer einen gleichen Scan gibt
$config_scan_timeout = 1 * $DAYS;

// Punkte, die es fuer einen einfachen Login gibt
$config_sitterpunkte_login = 0.25;

// Punkte, die es fuer "Freundschaftssitten" gibt
$config_sitterpunkte_friend = 2;

// Punkte, die es fuer einen erledigten Auftrag gibt
$config_sitterpunkte_auftrag = 3;

// Punkte, die es gibt, wenn man etwas ohne Auftrag gemacht hat
$config_sitterpunkte_auftrag_frei = 1;

// Zeit, wie lange die Sitterlog gespeichert wird in Sekunden
$config_sitterlog_timeout = 30 * $DAYS;

// Zeit, die vergehen muss in Sekunden, bevor man neue Punkte bekommt, wenn man sich mehrmals beim gleichen User einloggt
$config_sitterpunkte_timeout = 15 * $MINUTES;

// Zeit, wie lange ein Sitterlogin gesperrt ist in Sekunden, wenn sich jemand eingeloggt hat
$config_sitterlogin_timeout = 2 * $MINUTES;

// wie viele Sitteraufträge in den nächsten x Sekunden sollen angezeigt werden
$config_sitterliste_timeout = 12 * $HOURS;

// Zeit, wie lange Sitterauftrag in der Vergangenheit liegen darf (gewisse Toleranz ist hier zu empfehlen!)
$config_sitterauftrag_timeout = 1 * $DAYS + 12 * $HOURS;

// Zeit in Sekunden, bei Daueraufträgen, wie lange ein Auftrag nach Login als "erledigt" markiert werden soll
$config_dauer_timeout = 3 * $HOURS;

// Zeitformat im Sittertool
$config_sitter_timeformat = "%d.%m.%y %H:%M";

// nicht ändern
define('SITTEN_DISABLED', 2);
define('SITTEN_ONLY_NEWTASKS', 0);
define('SITTEN_ONLY_LOGINS', 3);
define('SITTEN_BOTH', 1);
?>
