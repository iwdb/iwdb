<?php
/*****************************************************************************/
/* m_artefaktbasen.php                                                       */
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
/* Kampfbasen                                                             */
/* für die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Author: Patsch                            */
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
$modulname  = "m_artefaktbasen";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Artefaktbasen";

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
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "Zeigt Informationen zu Artefaktbasen und Artefaktbasenverwaltungen an";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase() {
	global $db, $db_prefix, $db_tb_iwdbtabellen;

	$sqlscript = array(
	);

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

	$sqlscript = array(
	);

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
echo "<div class='doc_title'>$modultitle</div>\n";

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
$sql = "SELECT  $db_tb_user.id AS 'user',
		  $db_tb_user.budflesol AS 'typ',
	 	 
		 (SELECT $db_tb_research2user.userid
		  FROM $db_tb_research2user
		  WHERE $db_tb_research2user.userid=$db_tb_user.id
		    AND $db_tb_research2user.rid=219) AS 'research',
		 
		 (SELECT DISTINCT MAX($db_tb_gebaeude_spieler.count)
		  FROM $db_tb_gebaeude_spieler
		  WHERE $db_tb_gebaeude_spieler.user=$db_tb_user.id
		    AND $db_tb_gebaeude_spieler.building='Artefaktsammelbasencenter' HAVING MAX($db_tb_gebaeude_spieler.count)) AS 'count',
		 
		 (SELECT COUNT($db_tb_scans.coords)
		  FROM $db_tb_scans
		  WHERE $db_tb_scans.user=$db_tb_user.id
		    AND $db_tb_scans.objekt='Artefaktbasis') AS 'base'";
$sql .= " FROM $db_tb_user";
if (isset($params['team'])) {
	if ($params['team'] == '(Nur Fleeter)')
		$sql .= " WHERE " . $db_tb_user . ".budflesol='Fleeter'";
	elseif ($params['team'] == '(Nur Cash Cows)')
		$sql .= " WHERE " . $db_tb_user . ".budflesol='Cash Cow'";
	elseif ($params['team'] == '(Nur Buddler)')
		$sql .= " WHERE " . $db_tb_user . ".budflesol='Buddler'";
	elseif ($params['team'] != '(Alle)')
		$sql .= " WHERE " . $db_tb_user . ".buddlerfrom='" . $params['team'] . "'";
}
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);

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

start_table();
start_row("titlebg", "nowrap style=\"width:0%\" align=\"center\" colspan=\"8\"");
echo "<b>Artefaktsammelbasen</b>";
start_row("windowbg2", "nowrap style=\"width:0%\" align=\"center\"");
echo "Spieler";
next_cell("windowbg2", "nowrap style=\"width:0%\" align=\"center\"");
echo "Typ";
next_cell("windowbg2", "nowrap style=\"width:0%\" align=\"center\"");
echo "Suche nach neuen alten Sachen";
next_cell("windowbg2", "nowrap style=\"width:0%\" align=\"center\"");
echo "Artefaktsammelbasencenter";
next_cell("windowbg2", "nowrap style=\"width:0%\" align=\"center\"");
echo "Artefaktsammelbasis";


// Abfrage auswerten
while ($row = $db->db_fetch_array($result)) {
	start_row("windowbg1", "nowrap style=\"width:0%\" align=\"left\"");
	echo $row['user'];
	next_cell("windowbg1", "nowrap style=\"width:0%\" align=\"left\"");
	echo $row['typ'];
	next_cell("windowbg1", "nowrap style=\"width:0%\" align=\"left\"");
	if (!empty($row['research']))
		echo "erforscht";
	else
		echo "-";
	next_cell("windowbg1", "nowrap style=\"width:0%\" align=\"left\"");
	if (!empty($row['count']))
		echo "Stufe " . $row['count'];
	else if (!empty($row['research']))
		echo "Keine";
	else
		echo "-";
	next_cell("windowbg1", "nowrap style=\"width:0%\" align=\"left\"");
	//echo $row['base'] . "/" . $row['count'];
	
	if (!empty($row['count']))  {
		echo $row['base'] . "/" . $row['count'];
	} else
		echo "--";
	
	end_row();
}
end_table();


?>