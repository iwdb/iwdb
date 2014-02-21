<?php
/*****************************************************************************
 * config.php                                                                *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * ========================================================================= *
 * Copyright (c) 2004 Robert Riess - All Rights Reserved                     *
 *****************************************************************************
 * This program is free software; you can redistribute it and/or modify it   *
 * under the terms of the GNU General Public License as published by the     *
 * Free Software Foundation; either version 2 of the License, or (at your    *
 * option) any later version.                                                *
 *                                                                           *
 * This program is distributed in the hope that it will be useful, but       *
 * WITHOUT ANY WARRANTY; without even the implied warranty of                *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General *
 * Public License for more details.                                          *
 *                                                                           *
 * The GNU GPL can be found in LICENSE in this directory                     *
 *****************************************************************************
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

// ***************************************************************************
//  Ab hier sind Angaben die nicht unbedingt geändert werden müssen.
//
//allgemeine Pfadangaben
define('LOG_PATH', "log/");
define('BILDER_PATH', "bilder/");
define('GEBAEUDE_BILDER_PATH', BILDER_PATH."gebs/");
define('BANNER_PATH', BILDER_PATH."banner/");
define('MENUSTYLES_PATH', "menustyles/");
define('TECHTREE_BILDER_PATH', BILDER_PATH."techtree/");
//für evl aktuellere techtrees
//define('TECHTREE_BILDER_PATH', "//wuz.php-friends.de/techtree/");           //do not add http: or https:

//Pfad für den IWDB Standard-Banner, leer schaltet Banner für alle aus
$config_banner = BANNER_PATH."logo.png";

// Farben in der Karte der Allianzen
$config_allianzstatus            = array();
$config_allianzstatus['own']     = "#C4F493";
$config_allianzstatus['wing']    = "#E6F6A5";
$config_allianzstatus['NAP']     = "#7C9CF1";
$config_allianzstatus['iNAP']    = "#8DADF2";
$config_allianzstatus['VB']      = "#4A71D5";
$config_allianzstatus['iVB']     = "#4A71D5";
$config_allianzstatus['Krieg']   = "#E84528";
$config_allianzstatus['imKrieg'] = "#FFC080";
$config_allianzstatus['noraid']  = "#DD9911";

// Farben von Stargates, Schwarze Loecher, reservierten Planeten
$config_color                  = array();
$config_color['Stargate']      = "#A0BFCD";
$config_color['SchwarzesLoch'] = "#3F6778";
$config_color['reserviert']    = "#CCDCE3";
$config_color['first24h']      = "#00AACC";
$config_color['last24']        = "#00AACC"; //veraltet
$config_color['unscanned']     = "#4B4B00";
$config_color['scanoutdated']  = "#FF0000";

//ToDo: In die IWDB-Einstellungen verschieben?
$aSpieltypen = array(
    "Solo",
    "Allrounder",
    "Buddler" => array("Eisenbuddler", "Chembuddler", "Eisbuddler"),
    "Wandler" => array("Stahlwandler", "VV4A Wandler", "Wasser Wandler"),
    "Fleeter",
    "Cash Cow"
);

// Zeit, wie lange die SID aktuell bleibt (in Sekunden)
$config_sid_timeout = 1 * HOUR;

// zugelassene Zeichen für SIDs
$config_sid_string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890-_";

// Länge der SID
$config_sid_length = 20;

// zugelassene Zeichen fuer Passwoerter
$config_password_string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890-_&%\$!/()=?';

// Cookiename
$config_cookie_name = "iwdb";

// Zeit, wie lange das Cookie gültig ist in Sekunden
$config_cookie_timeout = 14 * DAY;

// Zeit, wie lange ein User als online angezeigt wird in Sekunden
$config_counter_timeout = 10 * MINUTE;

// Zeit, wie lange ein Username gesperrt wird nach x falschen Loginversuchen in Sekunden
$config_wronglogin_timeout = 6 * HOUR;

// nach wie vielen Loginversuchen soll der Username gesperrt werden
$config_wronglogins = 5;

// Zeitformat
$config_timeformat = "%d.%m.%Y %H:%M";  //veraltet
// Zeitformat im Sittertool
$config_sitter_timeformat = "%d.%m.%y %H:%M"; //veraltet

// Zeitformat
define("CONFIG_TIMEFORMAT", "%H:%M");
// Datumsformat
define("CONFIG_DATEFORMAT", "%d.%m.%Y");
// Datumszeitformat
define("CONFIG_DATETIMEFORMAT", "%d.%m.%Y %H:%M:%S");

// Zeitformat der Memberregistrierung in Icewars
$config_members_timeformat = "%d.%m.%y";
// Zeit, wie oft Sitterseiten neu geladen werden sollen in Sekunden
$config_refresh_timeout = 1 * MINUTE;
// Zeit, nach wie vielen Sekunden es wieder Punkte für einen gleichen Scan gibt
$config_scan_timeout = 1 * DAY;

// Punkte, die es für einen einfachen Login gibt
$config_sitterpunkte_login = 0.25;

// Punkte, die es für "Freundschaftssitten" gibt
$config_sitterpunkte_friend = 2;

// Punkte, die es für einen erledigten Auftrag gibt
$config_sitterpunkte_auftrag = 3;

// Punkte, die es gibt, wenn man etwas ohne Auftrag gemacht hat
$config_sitterpunkte_auftrag_frei = 1;

// Zeit, wie lange die Sitterlog gespeichert wird in Sekunden
$config_sitterlog_timeout = 30 * DAY;

// Zeit, die vergehen muss in Sekunden, bevor man neue Punkte bekommt, wenn man sich mehrmals beim gleichen User einloggt
$config_sitterpunkte_timeout = 15 * MINUTE;

// Zeit, wie lange ein Sitterlogin gesperrt ist in Sekunden, wenn sich jemand eingeloggt hat
$config_sitterlogin_timeout = 2 * MINUTE;

// wie viele Sitteraufträge in den nächsten x Sekunden sollen angezeigt werden
$config_sitterliste_timeout = 12 * HOUR;

// Zeit, wie lange Sitterauftrag in der Vergangenheit liegen darf (gewisse Toleranz ist hier zu empfehlen!)
$config_sitterauftrag_timeout = 2 * DAY;

// Zeit in Sekunden, bei Daueraufträgen, wie lange ein Auftrag nach Login als "erledigt" markiert werden soll
$config_dauer_timeout = 3 * HOUR;

//PHPIDS Einstellungen
$phpids_enabled         = true;     //aktiviert phpids, true für an (Standard), false für aus
$phpids_log_impact      = 1;        //logge Seitenaufrufe ab eingestellten impact, Standard 1, false für aus
$phpids_block_impact    = 1;        //blocke Seitenaufrufe ab eingestellten impact, Standard 1, false für aus