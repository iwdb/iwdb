<?php
/*****************************************************************************/
/* m_sprengung.php                                                           */
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
/* Sprengungen                                                               */
/* für Iw DB: Icewars geoscan and sitter database                            */
/*---------------------------------------------------------------------------*/
/* Author: [RoC]Thella (mailto:icewars@thella.de)                            */
/* Version: 0.x                                                              */
/* Date: xx/xx/xxxx                                                          */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspr?hen DB ist ein Gemeinschaftsprojekt von       */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!"; 
	exit; 
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
$modulname  = "m_sprengung";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "zeige Sprengungen";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation
//    ausführen darf. Mögliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc = "zeigt an wann Planeten vorraussichtlich gesprengt werden";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase() {
	global $db, $db_prefix, $db_tb_iwdbtabellen;

/*	foreach ($sqlscript as $sql) {
		echo "<br>" . $sql;
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

  echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";*/
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu() {
    global $modultitle, $modulstatus, $_POST;
		
		$actionparamters = "";
  	insertMenuItem( $_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparameters );
	  //
	  // Weitere Wiederholungen f?tere Men?r?, z.B.
	  //
	  // 	insertMenuItem( $_POST['menu'], ($_POST['submenu']+1), "Titel2", "hc", "&weissichnichtwas=1" ); 
	  //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file.
//
function workInstallConfigString() {
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase() {
	global $db, $db_tb_scans_historie, $db_tb_iwdbtabellen;

/*	foreach ($sqlscript as $sql) {
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

    echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";*/
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgef?enn das Modul mit dem Parameter 
// "install" aufgerufen wurde. Beispiel des Aufrufs: 
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" nat? deinen Server angeben und default 
// durch den Dateinamen des Moduls ersetzen.
//
if( !empty($_REQUEST['was'])) {
  //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
  if ( $user_status != "admin" ) 
		die('Hacking attempt...');

  echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname . 
	     " ("  . $_REQUEST['was'] . ")</div>\n";

  if (!@include("./includes/menu_fn.php")) 
	  die( "Cannot load menu functions" );

  // Wenn ein Modul administriert wird, soll der Rest nicht mehr 
  // ausgef?erden. 
  return;
}

if (!@include("./config/".$modulname.".cfg.php")) { 
	die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

//****************************************************************************

// Seitenparameter ermitteln
$gal_start = getVar('gal_start');
if (empty($gal_start) || !is_numeric($gal_start))
	$gal_start = $user_gal_start;
$gal_end = getVar('gal_end');
if (empty($gal_end) || !is_numeric($gal_end))
	$gal_end = $user_gal_end;

$sys_start = getVar('sys_start');
if (empty($sys_start) || !is_numeric($sys_start) )
	$sys_start = $user_sys_start;
$sys_end = getVar('sys_end');
if (empty($sys_end) || !is_numeric($sys_end))
	$sys_end = $user_sys_end;

// gesprengte Planeten l?en
$spreng_zeit = getVar('spreng_zeit');
if (empty($spreng_zeit) || !is_numeric($spreng_zeit))
{
	$spreng_zeit = 48;
}
else
{
	// vorfristige Sprengungen sind verboten
	if ($spreng_zeit < 0)
	{
		$spreng_zeit = 48;
	}
	else
	{
		// SQL-Statement aufbauen
		$sql = "update " . $db_tb_scans;
		$sql = $sql . " set eisengehalt=0, chemievorkommen=0, eisdichte=0, lebensbedingungen=0, gravitation=0,";
		$sql = $sql . " besonderheiten=null, fmod=null, kgmod=null, dgmod=null, ksmod=null, dsmod=null,";
		$sql = $sql . " time=0, tteisen=0, ttchemie=0, tteis=0, reset_timestamp=null, geoscantime=null,";
		$sql = $sql . " reserviert='', rnb=''";
		$sql = $sql . " where from_unixtime(geoscantime + reset_timestamp + ". $spreng_zeit ." * 60 * 60) < now() and reset_timestamp is not null";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not delete geoscans.', '', __FILE__, __LINE__, $sql);
	}
}

// Titelzeile
echo "<div class='doc_title'>Sprengungen</div>\n";
echo "<br>\n";
echo "Hier könnt ihr sehen, wann die pösen Vorgonen die nächsten Planeten sprengen, um Platz für eine Hyperraum-Umgehungsstraße zu schaffen.";
echo "<br>\n";
echo "<form method=\"POST\" action=\"index.php?action=" . $modulname . "&amp;sid=" . $sid . "\" enctype=\"multipart/form-data\"><p align=\"center\">\n";
echo "  Galaxie von: <input type=\"text\" name=\"gal_start\" value=\"" . $gal_start . "\" style=\"width: 30\"> bis: <input type=\"text\" name=\"gal_end\" value=\"" . $gal_end . "\" style=\"width: 30\"><br><br>";
echo "  System von: <input type=\"text\" name=\"sys_start\" value=\"" . $sys_start . "\" style=\"width: 30\"> bis: <input type=\"text\" name=\"sys_end\" value=\"" . $sys_end . "\" style=\"width: 30\"><br><br>";
echo "  Alle Geoscans löschen, deren Sprengung mehr als <input type=\"test\" name=\"spreng_zeit\" value=\"" . $spreng_zeit . "\" style=\"width: 30\"> Stunden in der Vergangenheit liegt<br><br>";
echo "  <input type=\"submit\" value=\"los\" name=\"B1\" class=\"submit\"><br>";
echo "</form>\n<br><br>";


// Tabellen?hrift
echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 90%;\">\n";
echo "  <tr>\n";
echo "    <td class=\"titlebg\" align=\"center\" colspan=\"1\">\n";
echo "      Koords\n";
echo "    </td>\n";
echo "    <td class=\"titlebg\" align=\"center\" colspan=\"1\">\n";
echo "      Planetentyp\n";
echo "    </td>\n";
echo "    <td class=\"titlebg\" align=\"center\" colspan=\"1\">\n";
echo "      Objekttyp\n";
echo "    </td>\n";
echo "    <td class=\"titlebg\" align=\"center\" colspan=\"1\">\n";
echo "      Spieler\n";
echo "    </td>\n";
echo "    <td class=\"titlebg\" align=\"center\" colspan=\"1\">\n";
echo "      Allianz\n";
echo "    </td>\n";
echo "    <td class=\"titlebg\" align=\"center\" colspan=\"1\">\n";
echo "      Sprengung in ... (+/- 24h)\n";
echo "    </td>\n";
echo "  </tr>\n";

// Zwischenspeicher initialisieren
$data = array();

// SQL-Statement aufbauen
$sql_order = ' ORDER BY geoscantime + reset_timestamp, coords_gal, coords_sys, coords_planet DESC';
$sql_where = '';

if ($gal_start > 0)
	$sql_where .= ' coords_gal>=' . $gal_start;
if ($gal_end > 0)
{
	if ($sql_where != '')
		$sql_where .= ' AND ';
	$sql_where .= ' coords_gal<=' . $gal_end;
}
if ($sys_start > 0)
{
	if ($sql_where != '')
		$sql_where .= ' AND ';
	$sql_where .= ' coords_sys>=' . $sys_start;
}
if ($sys_end > 0)
{
	if ($sql_where != '')
		$sql_where .= ' AND ';
	$sql_where .= ' coords_sys<=' . $sys_end;
}
if ($sql_where != '')
	$sql_where .= ' AND ';
$sql_where .= " reset_timestamp>0 AND objekt<>'Kolonie' AND typ<>'Nichts' AND typ<>'Raumstation' AND objekt<>'Kampfbasis' AND objekt<>'Sammelbasis' AND objekt<>'Artefaktbasis'";
if ($sql_where != '')
	$sql_where = " WHERE " . $sql_where;

// Abfrage ausf?
$sql = "SELECT * FROM " . $db_tb_scans . $sql_where . $sql_order;
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);

// Abfrage auswerten
while ($row = $db->db_fetch_array($result)) {
	$color = 'white';
	echo "  <tr>\n";
	echo "    <td class=\"windowbg1\" align=\"center\" style=\"background-color: " . $color . "\" colspan=\"1\">\n";
	echo "      <a href=\"index.php?action=showplanet&amp;coords=" . $row['coords'] . "&amp;ansicht=auto&amp;sid=" . $sid . "\">\n";
	echo "      " . $row['coords'] . "\n";
	echo "      </a>\n";
	echo "    </td>\n";
	echo "    <td class=\"windowbg1\" align=\"center\" style=\"background-color: " . $color . "\" colspan=\"1\">\n";
	echo "      " . $row['typ'] . "\n";
	echo "    </td>\n";
	echo "    <td class=\"windowbg1\" align=\"center\" style=\"background-color: " . $color . "\" colspan=\"1\">\n";
	echo "      " . $row['objekt'] . "\n";
	echo "    </td>\n";
	echo "    <td class=\"windowbg1\" align=\"center\" style=\"background-color: " . $color . "\" colspan=\"1\">\n";
	echo "      " . $row['user'] . "\n";
	echo "    </td>\n";
	echo "    <td class=\"windowbg1\" align=\"center\" style=\"background-color: " . $color . "\" colspan=\"1\">\n";
	echo "      " . $row['allianz'] . "\n";
	echo "    </td>\n";
	echo "    <td class=\"windowbg1\" align=\"center\" style=\"background-color: " . $color . "\" colspan=\"1\">\n";
	if ($row['geoscantime'] + $row['reset_timestamp'] > time()) {
		echo "      " . makeduration($row['geoscantime'] + $row['reset_timestamp']) . "\n";
	} else {
		echo "      wurde gesprengt!";
	}
	echo "    </td>\n";
	echo "  </tr>\n";
}
echo "</table>";

function makeduration($time) {
	if (empty($time))
		return '---';
	$duration = $time - time();
	$text = "";
	$days = (int)($duration / (24 * 60 * 60));
	$duration -= $days * 24 * 60 * 60;
	$hours = (int)($duration / (60 * 60));
	$duration -= $hours * 60 * 60;
	$mins = (int)($duration / 60);
	$duration -= $mins * 60;
	$secs = $duration;
	if ($days)
		$text .= $days . " Tagen ";
	$text .= (($hours < 10) ? "0" . $hours : $hours) . ":";
	$text .= (($mins < 10) ? "0" . $mins : $mins) . ":";
	$text .= (($secs < 10) ? "0" . $secs : $secs);
	return $text;
}
?>