<?php
/*****************************************************************************/
/* m_gebaeudeuebersicht.php                                                  */
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
/* Gebäudeübersicht                                                        */
/* für die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Author: [RoC]Thella (mailto:icewars@thella.de)                            */
/* Version: 0.x                                                              */
/* Date: xx/xx/xxxx                                                          */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  */
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
$modulname  = "m_gebaeudeuebersicht";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Gebäudeübersicht";

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
$moduldesc = "Zeigt die Gebäudeübersicht an";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//

function workInstallDatabase() {
	global $db, $db_prefix, $db_tb_iwdbtabellen;
/*
	$sqlscript = array(
		"CREATE TABLE `" . $db_prefix . "gebaeude_spieler` (" .
		"`coords_gal` tinyint(4) NOT NULL," .
		"`coords_sys` smallint(6) NOT NULL," .
		"`coords_planet` tinyint(4) NOT NULL," .
		"`kolo_typ` varchar(20) NOT NULL," .
		"`user` varchar(30) NOT NULL," .
		"`category` varchar(100) NOT NULL," .
		"`building` varchar(200) NOT NULL," .
		"`count` smallint(6) NOT NULL," .
		"`time` int(11) NOT NULL," .
		"PRIMARY KEY (`coords_gal`,`coords_sys`,`coords_planet`,`category`,`building`)" .
		") COMMENT='Gebaeudeuebersicht'",
		"INSERT INTO " . $db_tb_iwdbtabellen . " (`name`) VALUES ('gebaeude_spieler')",
		"INSERT INTO " . $db_tb_parser . " (`modulename`,`recognizer`,`message`) VALUES ('gebaeudeuebersicht','Gebäude&uuml;bersicht','Gebäude&uuml;bersicht')",
	);*/

	foreach ($sqlscript as $sql) {
		echo "<br>" . $sql;
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

	echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
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
	  // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
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
	global $db, $db_tb_gebaeude_spieler, $db_tb_iwdbtabellen;
/*
	$sqlscript = array(
		"DROP TABLE " . $db_tb_gebaeude_spieler,
		"DELETE FROM " . $db_tb_iwdbtabellen . " WHERE `name`='gebaeude_spieler'",
		"DELETE FROM " . $db_tb_parser . " WHERE `modulename`='gebaeudeuebersicht'",
	);*/

	foreach ($sqlscript as $sql) {
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

	echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgeführt wenn das Modul mit dem Parameter 
// "install" aufgerufen wurde. Beispiel des Aufrufs: 
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" natürlich deinen Server angeben und default 
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
  // ausgeführt werden. 
  return;
}

if (!@include("./config/".$modulname.".cfg.php")) { 
	die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

//****************************************************************************

// Titelzeile
echo "<div class='doc_title'>Gebäudeübersicht</div>\n";

// Stammdaten abfragen
$config = array();

$sql = "SELECT * FROM $db_tb_gebaeude";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	$buildings[$row['name']] = array(
		"id" => $row['id'],
		"bild" => $row['bild']
	);
}

// Spieler und Teams abfragen
$users = array();
$teams = array();
$teams['(Alle)'] = '(Alle)';
$teams['(Nur Fleeter)'] = '(Nur Fleeter)';
$teams['(Nur Cash Cows)'] = '(Nur Cash Cows)';
$teams['(Nur Buddler)'] = '(Nur Buddler)';
$sql = "SELECT * FROM " . $db_tb_user;
if (!$user_fremdesitten)
{
	$sql .= " WHERE allianz='" . $user_allianz . "'";
}
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	$users[$row['id']] = $row['id'];
	if (!empty($row['buddlerfrom']))
		$teams[$row['buddlerfrom']] = $row['buddlerfrom'];
}
$config['users'] = $users;
$config['teams'] = $teams;

// Parameter ermitteln
$params['team'] = getVar('team');

// Abfrage ausführen
$sql = "SELECT $db_tb_gebaeude_spieler.coords_gal,
		 $db_tb_gebaeude_spieler.coords_sys,
		 $db_tb_gebaeude_spieler.coords_planet,
		 $db_tb_gebaeude_spieler.kolo_typ,
		 $db_tb_gebaeude_spieler.building,
		 $db_tb_gebaeude_spieler.count,
		 $db_tb_gebaeude_spieler.time,
		(SELECT user FROM $db_tb_scans
		 WHERE $db_tb_scans.coords_gal=$db_tb_gebaeude_spieler.coords_gal
		   AND $db_tb_scans.coords_sys=$db_tb_gebaeude_spieler.coords_sys
		   AND $db_tb_scans.coords_planet=$db_tb_gebaeude_spieler.coords_planet) AS 'user',
		(SELECT planetenname FROM $db_tb_scans
		 WHERE $db_tb_scans.coords_gal=$db_tb_gebaeude_spieler.coords_gal
		   AND $db_tb_scans.coords_sys=$db_tb_gebaeude_spieler.coords_sys
		   AND $db_tb_scans.coords_planet=$db_tb_gebaeude_spieler.coords_planet) AS 'planet',
		(SELECT category FROM $db_tb_gebaeude
		 WHERE $db_tb_gebaeude.name=$db_tb_gebaeude_spieler.building) AS 'category',
		(SELECT inactive FROM $db_tb_gebaeude
		 WHERE $db_tb_gebaeude.name=$db_tb_gebaeude_spieler.building) AS 'inactive'";
$sql .= " FROM $db_tb_gebaeude_spieler";
$sql .= ",$db_tb_user";
$sql .= " WHERE $db_tb_user.id=user AND $db_tb_gebaeude_spieler.count!='0'";
if (isset($params['team'])) {
	if ($params['team'] == '(Nur Fleeter)')
		$sql .= " AND " . $db_tb_user . ".budflesol='Fleeter'";
	elseif ($params['team'] == '(Nur Cash Cows)')
		$sql .= " AND " . $db_tb_user . ".budflesol='Cash Cow'";
	elseif ($params['team'] == '(Nur Buddler)')
		$sql .= " AND " . $db_tb_user . ".budflesol='Buddler'";
	elseif ($params['team'] != '(Alle)')
		$sql .= " AND " . $db_tb_user . ".buddlerfrom='" . $params['team'] . "'";
}
if (!$user_fremdesitten)
{
	$sql .= " AND " . $db_tb_user . ".allianz='" . $user_allianz . "'";
}
$sql .= " ORDER BY category,user,coords_gal,coords_sys,coords_planet";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);

// Abfrage auswerten
$categories = array();
$categories_buildings = array();
$data = array();
while ($row = $db->db_fetch_array($result)) {
	if (!$row['inactive']) {
		$categories[$row['category']] = true;
		$categories_buildings[$row['category']][$row['building']] = true;
		$data[$row['category']][$row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet']]['user'] = $row['user'];
		$data[$row['category']][$row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet']]['planet'] = $row['planet'];
		$data[$row['category']][$row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet']]['time'] = $row['time'];
		$data[$row['category']][$row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet']][$row['building']] = $row['count'];
	}
}
// Auswahlfelder
echo "<form method=\"POST\" action=\"\" enctype=\"multipart/form-data\">";
echo "<p align=\"center\">";
echo "Team: ";
echo "<select name=\"team\">";
foreach ($config['teams'] as $team) {
	echo "<option value=\"$team\"";
	if ($team == $params['team'])
		echo " selected";
	echo ">$team</option>";
}
echo "</select>";
echo "</p>";
echo "<input type=\"submit\" name=\"submit\" value=\"anzeigen\"/>";
echo "</form>";

foreach ($categories as $category => $value) {
	echo "<br>";
	start_table();
	$count = count($categories_buildings[$category]);
	start_row("titlebg", "nowrap style=\"width:0%\" align=\"center\" colspan=\"" . ($count + 3) . "\"");
	echo "<b>" . $category . "</b>";
	next_row("windowbg2", "nowrap style=\"width:0%\" align=\"center\"");
	echo "Spieler";
	next_cell("windowbg2", "nowrap style=\"width:0%\" align=\"center\"");
	echo "Koords";
	next_cell("windowbg2", "nowrap style=\"width:100%\" align=\"center\"");
	echo "Planet";
	foreach ($categories_buildings[$category] as $building => $value) {
		next_cell("windowbg2", "style=\"width:60px\" align=\"center\"");
		if (isset($buildings[$building])) {
			$image = $buildings[$building]['bild'];
			$id = $buildings[$building]['id'];
		} else {
			$image = 'blank';
			$id = 0;
		}
		echo "<a href=\"index.php?action=m_building&show_building=" . $id . "\">";
		echo "<img src=\"bilder/gebs/" . $image . ".jpg\" border=\"0\" width=\"50\" height=\"50\" alt=\"" . $building . "\">";
		echo "</a>";
	}
	foreach ($data[$category] as $coords => $planet_buildings) {
		$color = scanAge($planet_buildings['time']);
		next_row("windowbg1", "nowrap style=\"width:0%; background-color: $color\" align=\"left\"");
		echo $planet_buildings['user'];
		next_cell("windowbg1", "nowrap style=\"width:0%\" align=\"left\"");
		echo $coords;
		next_cell("windowbg1", "nowrap style=\"width:0%\" align=\"left\"");
		echo $planet_buildings['planet'];
		foreach ($categories_buildings[$category] as $building => $value) {
			next_cell("windowbg1", "align=\"center\"");
			if (isset($planet_buildings[$building]) AND (($planet_buildings[$building])!="0")) {
				
					echo $planet_buildings[$building];
			}else
				echo "";
		}
	}
	end_row();
	end_table();
}
?>