<?php
/*****************************************************************************/
/* m_tranportschiffe.php                                                     */
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
/* Transportschiffe                                                          */
/* für die IWDB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/

/*****************************************************************************/
/*Dieses Modul dient als Vorlage zum Erstellen von eigenen Zusatzmodulen     */
/*für die Iw DB: Icewars geoscan and sitter database                         */
/*---------------------------------------------------------------------------*/
/*Diese Erweiterung der ursprünglichen DB ist ein Gemeinschafftsprojekt von  */
/*IW-Spielern.                                                               */
/*Bei Problemen kannst du dich an das eigens dafür eingerichtete             */
/*Entwiklerforum wenden:                                                     */
/*                                                                           */
/*        https://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                                                                           */
/*****************************************************************************/

define('DEBUG_LEVEL', 0);

include_once("includes/debug.php");

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
$modulname  = "m_transportschiffe";

//****************************************************************************
//
// -> MenÃ¼titel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Transportschiffe";

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
$moduldesc = "Zeigt Bedarfsinfos zu Transen an";

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

	echo "<div class='system_notification'>Installation: Datenbank&auml;nderungen = <b>OK</b></div>";
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
	  // Weitere Wiederholungen fÃ¼r weitere Menü-Einträge, z.B.
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

	echo "<div class='system_notification'>Deinstallation: Datenbank&auml;nderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgefÃ¼hrt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs:
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" natÃ¼rlich deinen Server angeben und default
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
  // ausgefÃ¼hrt werden.
  return;
}

if (!@include("./config/".$modulname.".cfg.php")) {
	die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

//****************************************************************************

// Parameter ermitteln
$params = array(
	'view' => getVar('view'),
	'order' => getVar('order'),
	'orderd' => getVar('orderd'),
	'expand' => getVar('expand'),
	'filter_team' => getVar('filter_team'),
	'heimatgalaxy' => getVar('heimatgalaxy'),
	'heimatgalaxy_abdeckung_system_klasse1' => getVar('heimatgalaxy_abdeckung_system_klasse1'),
	'heimatgalaxy_abdeckung_system_klasse2' => getVar('heimatgalaxy_abdeckung_system_klasse2'),
	'heimatgalaxy_abdeckung_hyperraum_klasse1' => getVar('heimatgalaxy_abdeckung_hyperraum_klasse1'),
	'heimatgalaxy_abdeckung_hyperraum_klasse2' => getVar('heimatgalaxy_abdeckung_hyperraum_klasse2'),
	'sonstige_abdeckung_system_klasse1' => getVar('sonstige_abdeckung_system_klasse1'),
	'sonstige_abdeckung_system_klasse2' => getVar('sonstige_abdeckung_system_klasse2'),
	'sonstige_abdeckung_hyperraum_klasse1' => getVar('sonstige_abdeckung_hyperraum_klasse1'),
	'sonstige_abdeckung_hyperraum_klasse2' => getVar('sonstige_abdeckung_hyperraum_klasse2'),
	'expand' => getVar('expand'),
	'soll' => getVar('soll'),
);

// Parameter validieren
if (empty($params['view']))
	$params['view'] = 'transportschiffe';
if (empty($params['order'])) 
	$params['order'] = 'user';
if ($params['orderd'] != 'asc' && $params['orderd'] != 'desc')
	$params['orderd'] = 'asc';
if (empty($params['filter_team']))
	$params['filter_team'] = $user_buddlerfrom;
if (!is_numeric($params['heimatgalaxy']))
	$params['heimatgalaxy'] = $user_allianz == 'KEINE' ? 17 : 3;
if (!is_numeric($params['heimatgalaxy_abdeckung_system_klasse1']))
	$params['heimatgalaxy_abdeckung_system_klasse1'] = 100;
if (!is_numeric($params['heimatgalaxy_abdeckung_system_klasse2']))
	$params['heimatgalaxy_abdeckung_system_klasse2'] = 100;
if (!is_numeric($params['heimatgalaxy_abdeckung_hyperraum_klasse1']))
	$params['heimatgalaxy_abdeckung_hyperraum_klasse1'] = 100;
if (!is_numeric($params['heimatgalaxy_abdeckung_hyperraum_klasse2']))
	$params['heimatgalaxy_abdeckung_hyperraum_klasse2'] = 100;
if (!is_numeric($params['sonstige_abdeckung_system_klasse1']))
	$params['sonstige_abdeckung_system_klasse1'] = 0;
if (!is_numeric($params['sonstige_abdeckung_system_klasse2']))
	$params['sonstige_abdeckung_system_klasse2'] = 0;
if (!is_numeric($params['sonstige_abdeckung_hyperraum_klasse1']))
	$params['sonstige_abdeckung_hyperraum_klasse1'] = 200;
if (!is_numeric($params['sonstige_abdeckung_hyperraum_klasse2']))
	$params['sonstige_abdeckung_hyperraum_klasse2'] = 200;

debug_var("params", $params);

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

// Abfrage ausführen
$sql = "SELECT  $db_tb_user.id AS 'user',
		  $db_tb_user.budflesol AS 'typ',
		  $db_tb_user.lastshipscan AS 'lastshipscan',
	 	 (SELECT eisen
		  FROM $db_tb_ressuebersicht
		  WHERE $db_tb_ressuebersicht.user=$db_tb_user.id) AS 'eisen',
	 	 (SELECT stahl
		  FROM $db_tb_ressuebersicht
		  WHERE $db_tb_ressuebersicht.user=$db_tb_user.id) AS 'stahl',
	 	 (SELECT vv4a
		  FROM $db_tb_ressuebersicht
		  WHERE $db_tb_ressuebersicht.user=$db_tb_user.id) AS 'vv4a',
	 	 (SELECT chem
		  FROM $db_tb_ressuebersicht
		  WHERE $db_tb_ressuebersicht.user=$db_tb_user.id) AS 'chem',
	 	 (SELECT eis
		  FROM $db_tb_ressuebersicht
		  WHERE $db_tb_ressuebersicht.user=$db_tb_user.id) AS 'eis',
	 	 (SELECT wasser
		  FROM $db_tb_ressuebersicht
		  WHERE $db_tb_ressuebersicht.user=$db_tb_user.id) AS 'wasser',
	 	 (SELECT energie
		  FROM $db_tb_ressuebersicht
		  WHERE $db_tb_ressuebersicht.user=$db_tb_user.id) AS 'energie',
		 (SELECT SUM($db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Systrans%') AS 'systrans',
		 (SELECT SUM($db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Lurch%') AS 'lurch',
		 (SELECT SUM($db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Gorgol%') AS 'gorgol',
		 (SELECT SUM($db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Eis%') AS 'eisbaer',
		 (SELECT SUM($db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Kamel%') AS 'kamel',
		 (SELECT SUM($db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Wasch%') AS 'waschbaer',
		 (SELECT SUM($db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Flughund%') AS 'flughund',
		 (SELECT SUM($db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Seepferdchen%') AS 'seepferd',
		 (SELECT SUM($db_tb_schiffstyp.klasse1 * $db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Systrans%') AS 'transporter_system_klasse1',
		 (SELECT SUM($db_tb_schiffstyp.klasse2 * $db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff LIKE '%Lurch%') AS 'transporter_system_klasse2',
		 (SELECT SUM($db_tb_schiffstyp.klasse1 * $db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff NOT LIKE '%Systrans%'
		    AND $db_tb_schiffstyp.schiff NOT LIKE '%Settlers Delight%') AS 'transporter_hyperraum_klasse1',
		 (SELECT SUM($db_tb_schiffstyp.klasse2 * $db_tb_schiffe.anzahl)
                FROM $db_tb_schiffe, $db_tb_schiffstyp
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
                  AND $db_tb_schiffstyp.id=$db_tb_schiffe.schiff
		    AND $db_tb_schiffstyp.schiff NOT LIKE '%Lurch%'
		    AND $db_tb_schiffstyp.schiff NOT LIKE '%Settlers Delight%') AS 'transporter_hyperraum_klasse2',
	        (SELECT datum
       	  FROM $db_tb_ressuebersicht
            	  WHERE $db_tb_ressuebersicht.user=$db_tb_user.id) AS 'datum' 
";
$sql .= " FROM $db_tb_user";
if (isset($params['filter_team'])) {
	if ($params['filter_team'] == '(Nur Fleeter)')
		$sql .= " WHERE " . $db_tb_user . ".budflesol='Fleeter'";
	elseif ($params['filter_team'] == '(Nur Cash Cows)')
		$sql .= " WHERE " . $db_tb_user . ".budflesol='Cash Cow'";
	elseif ($params['filter_team'] == '(Nur Buddler)')
		$sql .= " WHERE " . $db_tb_user . ".budflesol='Buddler'";
	elseif ($params['filter_team'] != '(Alle)')
		$sql .= " WHERE " . $db_tb_user . ".buddlerfrom='" . $params['filter_team'] . "'";
}
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);

// Abfrage auswerten
$data = array();
while ($row = $db->db_fetch_array($result)) {
	// Drecks-Solos raus
	if ($row['typ'] == 'Solo')
		continue;
	// Werte zuruecksetzen
	$produktion_system_klasse1 = 0;
	$produktion_system_klasse2 = 0;
	$produktion_hyperraum_klasse1 = 0;
	$produktion_hyperraum_klasse2 = 0;
	$abdeckung_system_klasse1 = 0;
	$abdeckung_system_klasse2 = 0;
	$abdeckung_hyperraum_klasse1 = 0;
	$abdeckung_hyperraum_klasse2 = 0;
	$expand = array();
	// Planeten
	$sql_detail = "SELECT *,";
	$sql_detail .= " (SELECT planetenname FROM $db_tb_scans WHERE $db_tb_scans.coords_gal=$db_tb_lager.coords_gal";
	$sql_detail .= " AND $db_tb_scans.coords_sys=$db_tb_lager.coords_sys";
	$sql_detail .= " AND $db_tb_scans.coords_planet=$db_tb_lager.coords_planet) AS 'planetenname'";
	$sql_detail .= " FROM $db_tb_lager WHERE user='" . $row['user'] . "'";
	$result_detail = $db->db_query($sql_detail)
		or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql_detail);
	while ($row_detail = $db->db_fetch_array($result_detail)) {
		// Alle positiven Produktions-Summen benoetigen Transen-Kapa
		if ($row_detail['eisen_prod'] > 0)
		{
			if ($row_detail["coords_gal"] == $params["heimatgalaxy"])
				$produktion_system_klasse1 += $row_detail['eisen_prod'] * 24;
			else
				$produktion_hyperraum_klasse1 += $row_detail['eisen_prod'] * 24;
		}
		if ($row_detail['stahl_prod'] > 0)
		{
			if ($row_detail["coords_gal"] == $params["heimatgalaxy"])
				$produktion_system_klasse1 += $row_detail['stahl_prod'] * 24 * 2;
			else
				$produktion_hyperraum_klasse1 += $row_detail['stahl_prod'] * 24 * 2;
		}
		if ($row_detail['chem_prod'] > 0)
		{
			if ($row_detail["coords_gal"] == $params["heimatgalaxy"])
				$produktion_system_klasse1 += $row_detail['chem_prod'] * 24 * 3;
			else
				$produktion_hyperraum_klasse1 += $row_detail['chem_prod'] * 24 * 3;
		}
		if ($row_detail['vv4a_prod'] > 0)
		{
			if ($row_detail["coords_gal"] == $params["heimatgalaxy"])
				$produktion_system_klasse1 += $row_detail['vv4a_prod'] * 24 * 4;
			else
				$produktion_hyperraum_klasse1 += $row_detail['vv4a_prod'] * 24 * 4;
		}
		// Klasse 2
		if ($row_detail['eis_prod'] > 0)
		{
			if ($row_detail["coords_gal"] == $params["heimatgalaxy"])
				$produktion_system_klasse2 += $row_detail['eis_prod'] * 24 * 2;
			else
				$produktion_hyperraum_klasse2 += $row_detail['eis_prod'] * 24 * 2;
		}
		if ($row_detail['wasser_prod'] > 0)
		{
			if ($row_detail["coords_gal"] == $params["heimatgalaxy"])
				$produktion_system_klasse2 += $row_detail['wasser_prod'] * 24 * 2;
			else
				$produktion_hyperraum_klasse2 += $row_detail['wasser_prod'] * 24 * 2;
		}
		if ($row_detail['energie_prod'] > 0)
		{
			if ($row_detail["coords_gal"] == $params["heimatgalaxy"])
				$produktion_system_klasse2 += $row_detail['energie_prod'] * 24;
			else
				$produktion_hyperraum_klasse2 += $row_detail['energie_prod'] * 24;
		}
		$expand[] = array(
			"coords" => $row_detail['coords_gal'] . ":" . $row_detail['coords_sys'] . ":" . $row_detail['coords_planet'],
			"name" => $row_detail['planetenname'],
			"systrans" => 0,
			"lurch" => 0,
			"gorgol" => 0,
			"eisbaer" => 0,
			"kamel" => 0,
			"waschbaer" => 0,
			"flughund" => 0,
			"seepferd" => 0,
			"produktion_system_klasse1" => 0,
			"produktion_system_klasse2" => 0,
			"transporter_system_klasse1" => 0,
			"transporter_system_klasse2" => 0,
			"abdeckung_system_klasse1" => 0,
			"abdeckung_system_klasse2" => 0,
			"produktion_hyperraum_klasse1" => 0,
			"produktion_hyperraum_klasse2" => 0,
			"transporter_hyperraum_klasse1" => 0,
			"transporter_hyperraum_klasse2" => 0,
			"abdeckung_hyperraum_klasse1" => 0,
			"abdeckung_hyperraum_klasse2" => 0,
		);
	}
	// Abfragen anfliegender Übergaben
	$sql_detail = "SELECT * FROM $db_tb_lieferung WHERE user_to<>user_from AND user_to='" . $row['user'] . "' AND time > " . time() . " AND art='&Uuml;bergabe'";
	$result_detail = $db->db_query($sql_detail)
		or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql_detail);
	while ($row_detail = $db->db_fetch_array($result_detail)) {
		$tokens = split('<br>', $row_detail['schiffe']);
		foreach ($tokens as $pos)
		{
			$tokens = split(' ', $pos);
			$anzahl = $tokens[0];
			$schiff = implode(' ', array_slice($tokens, 1));
			if (strpos($schiff, 'Systrans') !== FALSE)
			{
				$row['systrans'] += $anzahl;
				$row['transporter_system_klasse1'] += $anzahl * 5000;
			}
			elseif (strpos($schiff, 'Lurch') !== FALSE)
			{
				$row['lurch'] += $anzahl;
				$row['transporter_system_klasse2'] += $anzahl * 2000;
			}
			elseif (strpos($schiff, 'Gorgol') !== FALSE)
			{
				$row['gorgol'] += $anzahl;
				$row['transporter_hyperraum_klasse1'] += $anzahl * 20000;
			}
			elseif (strpos($schiff, 'Eisb') !== FALSE)
			{
				$row['eisbaer'] += $anzahl;
				$row['transporter_hyperraum_klasse2'] += $anzahl * 10000;
			}
			elseif (strpos($schiff, 'Kamel') !== FALSE)
			{
				$row['kamel'] += $anzahl;
				$row['transporter_hyperraum_klasse1'] += $anzahl * 75000;
			}
			elseif (strpos($schiff, 'Waschb') !== FALSE)
			{
				$row['waschbaer'] += $anzahl;
				$row['transporter_hyperraum_klasse2'] += $anzahl * 50000;
			}
			elseif (strpos($schiff, 'Flughund') !== FALSE)
			{
				$row['flughund'] += $anzahl;
				$row['transporter_hyperraum_klasse1'] += $anzahl * 400000;
			}
			elseif (strpos($schiff, 'Seepferd') !== FALSE)
			{
				$row['seepferd'] += $anzahl;
				$row['transporter_hyperraum_klasse2'] += $anzahl * 250000;
			}
		}
	}
	// Abdeckung berechnen
	$abdeckung_system_klasse1 = $produktion_system_klasse1 > 0 ? $row['transporter_system_klasse1'] * 100 / $produktion_system_klasse1 : "";
	$abdeckung_system_klasse2 = $produktion_system_klasse2 > 0 ? $row['transporter_system_klasse2'] * 100 / $produktion_system_klasse2 : "";
	$abdeckung_hyperraum_klasse1 = $produktion_hyperraum_klasse1 > 0 ? $row['transporter_hyperraum_klasse1'] * 100 / $produktion_hyperraum_klasse1 : "";
	$abdeckung_hyperraum_klasse2 = $produktion_hyperraum_klasse2 > 0 ? $row['transporter_hyperraum_klasse2'] * 100 / $produktion_hyperraum_klasse2 : "";
	// Differenz zum Soll berechnen
	$diff_system_klasse1 = $row['transporter_system_klasse1'] - ($produktion_system_klasse1 * ($params['heimatgalaxy_abdeckung_system_klasse1'] / 100));
	$diff_system_klasse2 = $row['transporter_system_klasse2'] - ($produktion_system_klasse2 * ($params['heimatgalaxy_abdeckung_system_klasse2'] / 100));
	$diff_hyperraum_klasse1 = $row['transporter_hyperraum_klasse1'] - ($produktion_system_klasse1 * ($params['heimatgalaxy_abdeckung_hyperraum_klasse1'] / 100)) - ($produktion_hyperraum_klasse1 * ($params['sonstige_abdeckung_hyperraum_klasse1'] / 100));
	$diff_hyperraum_klasse2 = $row['transporter_hyperraum_klasse2'] - ($produktion_system_klasse2 * ($params['heimatgalaxy_abdeckung_hyperraum_klasse2'] / 100)) - ($produktion_hyperraum_klasse2 * ($params['sonstige_abdeckung_hyperraum_klasse2'] / 100));
	// Scan-Alter berechnen
	$scanage = min($row['datum'], $row['lastshipscan']);
	$scanagehours = (time() - $scanage) / (60 * 60);
	if ($scanagehours < 24)
		$agecolor = "#00FF00";
	else if ($scanagehours < 96)
		$agecolor = "yellow";
	else
		$agecolor = "red";
	// Daten
	$data[$row['user']] = array(
		"user" => $row['user'],
		"typ" => $row['typ'],
		"systrans" =>  $params['soll'] ? floor($diff_system_klasse1 / 5000) : $row['systrans'],
		"lurch" => $params['soll'] ? floor($diff_system_klasse2 / 2000) : $row['lurch'],
		"gorgol" => $params['soll'] ? floor($diff_hyperraum_klasse1 / 20000) : $row['gorgol'],
		"eisbaer" => $params['soll'] ? floor($diff_hyperraum_klasse2 / 10000) : $row['eisbaer'],
		"kamel" => $params['soll'] ? floor($diff_hyperraum_klasse1 / 75000) : $row['kamel'],
		"waschbaer" => $params['soll'] ? floor($diff_hyperraum_klasse2 / 50000) : $row['waschbaer'],
		"flughund" => $params['soll'] ? floor($diff_hyperraum_klasse1 / 400000) : $row['flughund'],
		"seepferd" => $params['soll'] ? floor($diff_hyperraum_klasse2 / 250000) : $row['seepferd'],
		"produktion_system_klasse1" => $produktion_system_klasse1,
		"produktion_system_klasse2" => $produktion_system_klasse2,
		"transporter_system_klasse1" => $row["transporter_system_klasse1"],
		"transporter_system_klasse2" => $row["transporter_system_klasse2"],
		"abdeckung_system_klasse1" => $abdeckung_system_klasse1,
		"abdeckung_system_klasse2" => $abdeckung_system_klasse2,
		"produktion_hyperraum_klasse1" => $produktion_hyperraum_klasse1,
		"produktion_hyperraum_klasse2" => $produktion_hyperraum_klasse2,
		"transporter_hyperraum_klasse1" => $row["transporter_hyperraum_klasse1"],
		"transporter_hyperraum_klasse2" => $row["transporter_hyperraum_klasse2"],
		"abdeckung_hyperraum_klasse1" => $abdeckung_hyperraum_klasse1,
		"abdeckung_hyperraum_klasse2" => $abdeckung_hyperraum_klasse2,
		"user_style" => "background-color: $agecolor;",
		"produktion_system_klasse1_style" => "text-align: right;",
		"produktion_system_klasse2_style" => "text-align: right;",
		"transporter_system_klasse1_style" => "text-align: right;",
		"transporter_system_klasse2_style" => "text-align: right;",
		"abdeckung_system_klasse1_style" => "text-align: right;",
		"abdeckung_system_klasse2_style" => "text-align: right;",
		"produktion_hyperraum_klasse1_style" => "text-align: right;",
		"produktion_hyperraum_klasse2_style" => "text-align: right;",
		"transporter_hyperraum_klasse1_style" => "text-align: right;",
		"transporter_hyperraum_klasse2_style" => "text-align: right;",
		"abdeckung_hyperraum_klasse1_style" => "text-align: right;",
		"abdeckung_hyperraum_klasse2_style" => "text-align: right;",
		"expand" => $expand,
	);
}

// Daten sortieren
usort($data, "sort_data_cmp");

// Ansichten definieren
$views = array(
	'transportschiffe' => array(
		'title' => 'Transportschiffe',
		'headers' => array(
			'Spieler' => 2,
			'Schiffe' => 8,
			'Produktion (System)' => 2,
			'Transporter (System)' => 2,
			'Abdeckung (System)' => 2,
			'Produktion (Hyperraum)' => 2,
			'Transporter (Hyperraum)' => 2,
			'Abdeckung (Hyperraum)' => 2,
		),
		'columns' => array(
			'user' => 'Spieler',
			'typ' => 'Typ',
			'systrans' => 'S',
			'lurch' => 'L',
			'gorgol' => 'G',
			'eisbaer' => 'E',
			'kamel' => 'K',
			'waschbaer' => 'W',
			'flughund' => 'F',
			'seepferd' => 'S',
			'produktion_system_klasse1' => 'Klasse 1',
			'produktion_system_klasse2' => 'Klasse 2',
			'transporter_system_klasse1' => 'Klasse 1',
			'transporter_system_klasse2' => 'Klasse 2',
			'abdeckung_system_klasse1' => 'Klasse 1',
			'abdeckung_system_klasse2' => 'Klasse 2',
			'produktion_hyperraum_klasse1' => 'Klasse 1',
			'produktion_hyperraum_klasse2' => 'Klasse 2',
			'transporter_hyperraum_klasse1' => 'Klasse 1',
			'transporter_hyperraum_klasse2' => 'Klasse 2',
			'abdeckung_hyperraum_klasse1' => 'Klasse 1',
			'abdeckung_hyperraum_klasse2' => 'Klasse 2',
		),
		'sums' => array(
			'systrans' => 'S',
			'lurch' => 'L',
			'gorgol' => 'G',
			'eisbaer' => 'E',
			'kamel' => 'K',
			'waschbaer' => 'W',
			'flughund' => 'F',
			'seepferd' => 'S',
			'produktion_system_klasse1' => 'Klasse 1',
			'produktion_system_klasse2' => 'Klasse 2',
			'transporter_system_klasse1' => 'Klasse 1',
			'transporter_system_klasse2' => 'Klasse 2',
			'abdeckung_system_klasse1' => 'Klasse 1',
			'abdeckung_system_klasse2' => 'Klasse 2',
			'produktion_hyperraum_klasse1' => 'Klasse 1',
			'produktion_hyperraum_klasse2' => 'Klasse 2',
			'transporter_hyperraum_klasse1' => 'Klasse 1',
			'transporter_hyperraum_klasse2' => 'Klasse 2',
			'abdeckung_hyperraum_klasse1' => 'Klasse 1',
			'abdeckung_hyperraum_klasse2' => 'Klasse 2',
			"systrans_style" => "text-align: right;",
			"lurch_style" => "text-align: right;",
			"gorgol_style" => "text-align: right;",
			"eisbaer_style" => "text-align: right;",
			"kamel_style" => "text-align: right;",
			"waschbaer_style" => "text-align: right;",
			"flughund_style" => "text-align: right;",
			"seepferd_style" => "text-align: right;",
			"produktion_system_klasse1_style" => "text-align: right;",
			"produktion_system_klasse2_style" => "text-align: right;",
			"transporter_system_klasse1_style" => "text-align: right;",
			"transporter_system_klasse2_style" => "text-align: right;",
			"abdeckung_system_klasse1_style" => "text-align: right;",
			"abdeckung_system_klasse2_style" => "text-align: right;",
			"produktion_hyperraum_klasse1_style" => "text-align: right;",
			"produktion_hyperraum_klasse2_style" => "text-align: right;",
			"transporter_hyperraum_klasse1_style" => "text-align: right;",
			"transporter_hyperraum_klasse2_style" => "text-align: right;",
			"abdeckung_hyperraum_klasse1_style" => "text-align: right;",
			"abdeckung_hyperraum_klasse2_style" => "text-align: right;",
		),
		'key' => 'user',
		'expand' => array(
			'title' => 'Planeten',
			'columns' => array(
				'coords' => 'Koords',
				'name' => 'Name',
				'systrans' => 'S',
				'lurch' => 'L',
				'gorgol' => 'G',
				'eisbaer' => 'E',
				'kamel' => 'K',
				'waschbaer' => 'W',
				'flughund' => 'F',
				'seepferd' => 'S',
				'produktion_system_klasse1' => 'Klasse 1',
				'produktion_system_klasse2' => 'Klasse 2',
				'transporter_system_klasse1' => 'Klasse 1',
				'transporter_system_klasse2' => 'Klasse 2',
				'abdeckung_system_klasse1' => 'Klasse 1',
				'abdeckung_system_klasse2' => 'Klasse 2',
				'produktion_hyperraum_klasse1' => 'Klasse 1',
				'produktion_hyperraum_klasse2' => 'Klasse 2',
				'transporter_hyperraum_klasse1' => 'Klasse 1',
				'transporter_hyperraum_klasse2' => 'Klasse 2',
				'abdeckung_hyperraum_klasse1' => 'Klasse 1',
				'abdeckung_hyperraum_klasse2' => 'Klasse 2',
			),
		),
	),
);

// Aktuelle Ansicht auswählen
$view = $views[$params['view']];
$expand = $view['expand'];

// Titelzeile ausgeben
echo "<div class='doc_title'>" . $view['title'] . "</div><br>\n";

// Ergebnisse ausgeben
if (isset($results))
	foreach ($results as $result)
		echo $result;

// Team Dropdown
echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p align=\"center\">';
echo 'Team: ';
echo makefield(array("type" => 'select', "values" => $config['teams'], "value" => $params['filter_team']), 'filter_team');
echo '<input type="hidden" name="soll" value="0"/>';
echo ' <input type="submit" name="submit" value="anzeigen"/>';
echo "</form>\n";

// Soll-Berechnung
echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p align=\"center\">';
echo '<table>';
echo '<tr>';
echo '<td>';
echo 'Heimatgalaxy: ';
echo '</td>';
echo '<td>';
echo makefield(array("type" => 'text', "value" => $params['heimatgalaxy'], "size" => 2), 'heimatgalaxy');
echo '</td>';
echo '<td>';
echo ' Systemtransporter: ';
echo '</td>';
echo '<td>';
echo ' Klasse 1: ';
echo '</td>';
echo '<td>';
echo makefield(array("type" => 'text', "value" => $params['heimatgalaxy_abdeckung_system_klasse1'], "size" => 3), 'heimatgalaxy_abdeckung_system_klasse1');
echo '%';
echo '</td>';
echo '<td>';
echo ' Klasse 2: ';
echo '</td>';
echo '<td>';
echo makefield(array("type" => 'text', "value" => $params['heimatgalaxy_abdeckung_system_klasse2'], "size" => 3), 'heimatgalaxy_abdeckung_system_klasse2');
echo '%';
echo '</td>';
echo '<td>';
echo '&nbsp;';
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<td>';
echo '&nbsp;';
echo '</td>';
echo '<td>';
echo '&nbsp;';
echo '</td>';
echo '<td>';
echo ' Hyperraumtransporter: ';
echo '</td>';
echo '<td>';
echo ' Klasse 1: ';
echo '</td>';
echo '<td>';
echo makefield(array("type" => 'text', "value" => $params['heimatgalaxy_abdeckung_hyperraum_klasse1'], "size" => 3), 'heimatgalaxy_abdeckung_hyperraum_klasse1');
echo '%';
echo '</td>';
echo '<td>';
echo ' Klasse 2: ';
echo '</td>';
echo '<td>';
echo makefield(array("type" => 'text', "value" => $params['heimatgalaxy_abdeckung_hyperraum_klasse2'], "size" => 3), 'heimatgalaxy_abdeckung_hyperraum_klasse2');
echo '%';
echo '</td>';
echo '<td>';
echo '&nbsp;';
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<td>';
echo 'Sonstige Galaxien: ';
echo '</td>';
echo '<td>';
echo '&nbsp;';
echo '</td>';
echo '<td>';
echo ' Systemtransporter ';
echo '</td>';
echo '<td>';
echo ' Klasse 1: ';
echo '</td>';
echo '<td>';
echo makefield(array("type" => 'text', "value" => $params['sonstige_abdeckung_system_klasse1'], "size" => 3), 'sonstige_abdeckung_system_klasse1');
echo '%';
echo '</td>';
echo '<td>';
echo ' Klasse 2: ';
echo '</td>';
echo '<td>';
echo makefield(array("type" => 'text', "value" => $params['sonstige_abdeckung_system_klasse2'], "size" => 3), 'sonstige_abdeckung_system_klasse2');
echo '%';
echo '</td>';
echo '<td>';
echo '&nbsp;';
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<td>';
echo '&nbsp;';
echo '</td>';
echo '<td>';
echo '&nbsp;';
echo '</td>';
echo '<td>';
echo ' Hyperraumtransporter: ';
echo '</td>';
echo '<td>';
echo ' Klasse 1: ';
echo '</td>';
echo '<td>';
echo makefield(array("type" => 'text', "value" => $params['sonstige_abdeckung_hyperraum_klasse1'], "size" => 3), 'sonstige_abdeckung_hyperraum_klasse1');
echo '%';
echo '</td>';
echo '<td>';
echo ' Klasse 2: ';
echo '</td>';
echo '<td>';
echo makefield(array("type" => 'text', "value" => $params['sonstige_abdeckung_hyperraum_klasse2'], "size" => 3), 'sonstige_abdeckung_hyperraum_klasse2');
echo '%';
echo '</td>';
echo '<td>';
echo '<input type="submit" name="submit" value="berechnen"/>';
echo '</td>';
echo '</tr>';
echo '</table>';
echo '<input type="hidden" name="soll" value="1"/>';
echo "</form>\n";

if ($params['soll'])
{
	echo 'Berechnete Differenz der ben&ouml;tigten Transportschiffe:<br><br>';
}

// Daten ausgeben
start_table(100);
start_row("titlebg", "nowrap valign=top");
foreach ($view['headers'] as $headercolumnname => $headercolumnspan) {
	next_cell("titlebg", "nowrap colspan=$headercolumnspan valign=top");
	echo "<b>" . $headercolumnname . "</b>";
}
next_cell("titlebg", 'nowrap valign=top');
echo '&nbsp;';
start_row("windowbg2", "nowrap valign=top");
foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
	next_cell("windowbg2", "nowrap valign=top");
	$orderkey = $viewcolumnkey;
	if (isset($view['sortcolumns'][$orderkey]))
		$orderkey = $view['sortcolumns'][$orderkey];
		echo makelink(
		array(
			'order' => $orderkey,
			'orderd' => 'asc'
		),
		"<img src=\"./bilder/asc.gif\" border=\"0\">");
	echo $viewcolumnname;
	echo makelink(
		array(
			'order' => $orderkey,
			'orderd' => 'desc'
		),
		"<img src=\"./bilder/desc.gif\" border=\"0\">");
}
next_cell("windowbg2");
$index = 0;
foreach ($data as $row) {
	$key = $row[$view['key']];
	$expanded = $params['expand'] == $key;
	next_row('windowbg1', 'nowrap valign=top style="background-color: white;"');
	echo makelink(
		array('expand' => ($expanded ? '' : $key)),
		'<img src="bilder/' . ($expanded ? 'point' : 'plus') . '.gif" border="0" alt="' . ($expanded ? 'zuklappen' : 'erweitern') . '">'
	);
	foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
		if (isset($row[$viewcolumnkey . '_style']))
			$style = $row[$viewcolumnkey . '_style'];
		else
			$style = "background-color: white;";
		next_cell("windowbg1", 'nowrap valign=top style="' . $style . '"');
		echo format_value($row, $viewcolumnkey, $row[$viewcolumnkey]);
		// Summe bilden
		if (isset($view['sums']) && isset($view['sums'][$viewcolumnkey]))
			if (isset($sums[$viewcolumnkey]))
				$sums[$viewcolumnkey] += $row[$viewcolumnkey];
			else
				$sums[$viewcolumnkey] = $row[$viewcolumnkey];
	}
	// Editbuttons ausgeben
	if (isset($view['edit'])) {
		next_cell("windowbg1", 'nowrap valign=top');
		if (!isset($row['allow_edit']) || $row['allow_edit'])
			echo makelink(
				array('edit' => $key),
				"<img src=\"bilder/file_edit_s.gif\" border=\"0\" alt=\"bearbeiten\">"
			);
		if (!isset($row['allow_delete']) || $row['can_delete'])
			echo makelink(
				array('delete' => $key),
				"<img src=\"bilder/file_delete_s.gif\" border=\"0\" onclick=\"return confirmlink(this, 'Datensatz wirklich loeschen?')\" alt=\"loeschen\">"
			);
	}
	// Markierbuttons ausgeben
	next_cell("windowbg1", 'nowrap valign=top');
	//echo "<input type=\"checkbox\" name=\"mark_" . $index++ . "\" value=\"" . $key . "\"";
	//if (getVar("mark_all"))
	//	echo " checked";
	//echo ">";
	// Expandbereich ausgeben
	if (isset($expand) && $params['expand'] == $key && isset($row['expand']) && count($row['expand'])) {
		next_row('titlebg', 'colspan=' . (count($view['columns']) + 3));
		echo "<b>" . $expand['title'] . "</b>";
		next_row('windowbg2', '');
		foreach ($expand['columns'] as $expandcolumnkey => $expandcolumnname) {
			next_cell("windowbg2", "nowrap valign=top");
			echo $expandcolumnname;
		}
		if (isset($view['edit'])) {
			next_cell("windowbg2", 'nowrap valign=top');
			echo '&nbsp;';
		}
		next_cell("windowbg2");
		echo '&nbsp;';
		foreach ($row['expand'] as $expand_row) {
			next_row('windowbg1', 'nowrap valign=center style="background-color: white;"');
			foreach ($expand['columns'] as $expandcolumnkey => $expandcolumnname) {
				if (isset($expand_row[$expandcolumnkey . '_style']))
					$style = $expand_row[$expandcolumnkey . '_style'];
				else
					$style = "background-color: white;";
				next_cell("windowbg1", 'nowrap valign=top style="' . $style . '"');
				echo format_value($expand_row, $expandcolumnkey, $expand_row[$expandcolumnkey], true);
			}
			if (isset($view['edit'])) {
				next_cell("windowbg1", 'nowrap valign=top');
				echo '&nbsp;';
			}
			next_cell("windowbg1");
			echo '&nbsp;';
		}
		next_row('windowbg2', 'colspan=' . (count($view['columns']) + 3));
		echo "&nbsp;";
	}
}
next_row('windowbg2', 'colspan=' . (count($view['columns']) + 3));
echo "<b>Summe</b>";
next_row('windowbg1', 'nowrap valign=top style="background-color: white;"');
foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
	if (isset($view['sums'][$viewcolumnkey . '_style']))
		$style = $view['sums'][$viewcolumnkey . '_style'];
	else
		$style = "background-color: white;";
	next_cell("windowbg1", 'nowrap valign=top style="' . $style . '"');
	if (isset($sums) && isset($view['sums']) && isset($view['sums'][$viewcolumnkey]))
		echo format_value($sums, $viewcolumnkey, $sums[$viewcolumnkey]);
	else
		echo "&nbsp;";

}
next_cell("windowbg1", 'nowrap valign=top');
end_table();

// Legende ausgeben
echo '<br><table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="">';
echo '<tr nowrap>';
echo '<td style="background-color: white;" nowrap>Ress/Kolo- oder Schiffs&uuml;bersicht</td>';
echo '<td style="width: 30; background-color: #00FF00;"></td>';
echo '<td class="windowbg1">von heute</td>';
echo '<td style="width: 30; background-color: yellow;"></td>';
echo '<td class="windowbg1">&gt;24h</td>';
echo '<td style="width: 30; background-color: red;"></td>';
echo '<td class="windowbg1">&gt;96h</td>';
echo '<td style="width: 10; background-color: white;" align="center">S</td>';
echo '<td class="windowbg1">Systrans</td>';
echo '<td style="width: 10; background-color: white;" align="center">L</td>';
echo '<td class="windowbg1">Lurch</td>';
echo '<td style="width: 10; background-color: white;" align="center">G</td>';
echo '<td class="windowbg1">Gorgol</td>';
echo '<td style="width: 10; background-color: white;" align="center">E</td>';
echo '<td class="windowbg1">Eisb&auml;r</td>';
echo '<td style="width: 10; background-color: white;" align="center">K</td>';
echo '<td class="windowbg1">Kamel</td>';
echo '<td style="width: 10; background-color: white;" align="center">W</td>';
echo '<td class="windowbg1">Waschb&auml;r</td>';
echo '<td style="width: 10; background-color: white;" align="center">F</td>';
echo '<td class="windowbg1">Flughund</td>';
echo '<td style="width: 10; background-color: white;" align="center">S</td>';
echo '<td class="windowbg1">Seepferdchen</td>';
echo '</tr>';
echo '</table>';

//****************************************************************************
//
// Formatiert die Datenwerte
function format_value($row, $key, $value, $expand = false) {
	global $params;

	if ($key == 'eisen' ||
	    $key == 'stahl' ||
	    $key == 'vv4a' ||
	    $key == 'chemie' ||
	    $key == 'eis' ||
	    $key == 'wasser' ||
	    $key == 'energie' ||
	    $key == 'systrans' ||
	    $key == 'lurch' ||
	    $key == 'gorgol' ||
           $key == 'eisbaer' ||
           $key == 'kamel' ||
           $key == 'waschbaer' ||
           $key == 'flughund' ||
	    $key == 'seepferd')
	{
		if ($value < 0)
			$color = "red";
		else
			$color = "green";
		$value = "<span style=\"color: $color\">" . number_format($value, 0, "", ".") . "</span>";
		return $value;
	}
	else if ($key == 'produktion_system_klasse1' ||
	    	  $key == 'produktion_system_klasse2' ||
		  $key == 'produktion_hyperraum_klasse1' ||
	    	  $key == 'produktion_hyperraum_klasse2' ||
		  $key == 'transporter_system_klasse1' ||
		  $key == 'transporter_system_klasse2' ||
		  $key == 'transporter_hyperraum_klasse1' ||
		  $key == 'transporter_hyperraum_klasse2')
	{
		return number_format($value, 0, "", ".");
	}
	else if ($key == 'abdeckung_system_klasse1')
	{
		if ($row['produktion_system_klasse1'] <= 0)
			$value = 0;
		else
			$value = $row['transporter_system_klasse1'] * 100 / $row['produktion_system_klasse1'];
		if ($value < 100)
			$color = "red";
		else
			$color = "green";
		return "<span style=\"color: $color\">" . number_format($value, 0, "", ".") . "%</span>";	
	}
	else if ($key == 'abdeckung_system_klasse2')
	{
		if ($row['produktion_system_klasse2'] <= 0)
			$value = 0;
		else
			$value = $row['transporter_system_klasse2'] * 100 / $row['produktion_system_klasse2'];
		if ($value < 100)
			$color = "red";
		else
			$color = "green";
		return "<span style=\"color: $color\">" . number_format($value, 0, "", ".") . "%</span>";	
	}
	else if ($key == 'abdeckung_hyperraum_klasse1')
	{
		if ($row['produktion_hyperraum_klasse1'] <= 0)
			$value = 0;
		else
			$value = $row['transporter_hyperraum_klasse1'] * 100 / $row['produktion_hyperraum_klasse1'];
		if ($value < 100)
			$color = "red";
		else
			$color = "green";
		return "<span style=\"color: $color\">" . number_format($value, 0, "", ".") . "%</span>";	
	}
	else if ($key == 'abdeckung_hyperraum_klasse2')
	{
		if ($row['produktion_hyperraum_klasse2'] <= 0)
			$value = 0;
		else
			$value = $row['transporter_hyperraum_klasse2'] * 100 / $row['produktion_hyperraum_klasse2'];
		if ($value < 100)
			$color = "red";
		else
			$color = "green";
		return "<span style=\"color: $color\">" . number_format($value, 0, "", ".") . "%</span>";	
	}
	else
		return $value;
}

//****************************************************************************
//
// Vergleichsfunktion fuer das sortieren
function sort_data_cmp($a, $b) {
	global $params;

	if ($params['order'] == 'coords') {
		$coordsA = explode(':', $a['coords']);
		$coordsB = explode(':', $b['coords']);
		$result = 0;
		if ($coordsA[0] < $coordsB[0])
			$result = -1;
		elseif ($coordsA[0] > $coordsB[0])
			$result = 1;
		if ($result == 0 && ($coordsA[1] < $coordsB[1]))
			$result = -1;
		elseif ($result == 0 && ($coordsA[1] > $coordsB[1]))
			$result = 1;
		if ($result == 0 && ($coordsA[2] < $coordsB[2]))
			$result = -1;
		elseif ($result == 0 && ($coordsA[2] > $coordsB[2]))
			$result = 1;
	} else {
		$valA = strtoupper($a[$params['order']]);
		$valB = strtoupper($b[$params['order']]);
		if ($valA < $valB)
			$result = -1;
		elseif ($valA > $valB)
			$result = 1;
		else
			$result = 0;
	}
	if ($params['orderd'] == 'desc')
		$result *= -1;

	return $result;
}

// ****************************************************************************
//
// Erstellt ein Formularfeld.
function makefield($field, $key) {
	switch ($field['type']) {
	case 'text':
		$html = '<input type="text" name="' . $key . '" value="' . $field['value'] . '"';
		if (isset($field['style']))
			$html .= ' style="' . $field['style'] . '"';
		if (isset($field['size']))
			$html .= ' size="' . $field['size'] . '"';
		$html .= '>';
		break;
	case 'select':
		$html = '<select name="' . $key . '">';
		foreach ($field['values'] as $key => $value) {
			$html .= '<option value="' . $key . '"';
			if (isset($field['value']) && $field['value'] == $key)
				$html .= ' selected';
			$html .= '>' . $value . '</option>';
		}
		$html .= '</select>';
		break;
	case 'area':
		$html = '<textarea name="' . $key . '" rows="' . $field['rows'] . '" cols="' . $field['cols'] . '">';
		$html .= $field['value'];
		$html .= '</textarea>';
		break;
	case 'checkbox':
		$html = '<input type="checkbox" name="' . $key . '" value="1"';
		if ($field['value'])
			$html .= ' checked';
		if (isset($field['style']))
			$html .= ' style="' . $field['style'] . '"';
		$html .= '>';
		break;
	}
	return $html;
}

// ****************************************************************************
//
// Erzeugt einen Modul-Link.
function makelink($newparams, $content) {
	return '<a href="' . makeurl($newparams) . '">' . $content . '</a>';
}

// ****************************************************************************
//
// Erzeugt eine Modul-URL.
function makeurl($newparams) {
	global $modulname, $sid, $params;

	$url = 'index.php?action=' . $modulname;
	$url .= '&amp;sid=' . $sid;
	$mergeparams = array_merge($params, $newparams);
	foreach ($mergeparams as $paramkey => $paramvalue)
		$url .= '&amp;' . $paramkey . '=' . $paramvalue;
	return $url;
}
?> 