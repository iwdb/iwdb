<?php
/*****************************************************************************/
/* m_bestellung_schiffe.php                                                  */
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
/* Bestellungen                                                              */
/* für die IWDB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Author: [GILDE]Thella (mailto:icewars@thella.de)                          */
/* Version: 0.1                                                              */
/* Date: xx/xx/xxxx                                                          */
/*                                                                           */
/* Originally written by [GILDE]xerex.                                       */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
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
// -> Das m_ als Beginn des Dateinamens des Moduls ist Bedingung für 
//    eine Installation über das Menue
//
$modulname  = "m_bestellung_schiffe";

//****************************************************************************
//
// -> Menuetitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Schiffe #schiffe";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul ueber die Navigation 
//    ausfuehren darf. Moegliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "Bestellsystem zur Koordination von Logistikaufträgen im Buddler-Fleeter-System.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase() {
/*
	global $db, $db_prefix, $db_tb_iwdbtabellen;

	$sqlscript = array(
		"CREATE TABLE `" . $db_prefix . "bestellung` (" .
		"`id` int(11) NOT NULL auto_increment," .
		"`user` varchar(30) default NULL," .
		"`team` varchar(30) default NULL," .
		"`coords_gal` tinyint(4) NOT NULL," .
		"`coords_sys` int(11) NOT NULL," .
		"`coords_planet` tinyint(4) NOT NULL," .
		"`text` varchar(254) NOT NULL," .
		"`time` int(12) default NULL," .
		"`eisen` int(7) default 0," .
		"`stahl` int(7) default 0," .
		"`chemie` int(7) default 0," .
		"`vv4a` int(7) default 0," .
		"`eis` int(7) default 0," .
		"`wasser` int(7) default 0," .
		"`energie` int(7) default 0," .
		"`credits` int(7) default 0," .
		"`volk` int(7) default 0," .
		"`schiff` varchar(50) default NULL," .
		"`anzahl` int(7) default 1," . 
		"`prio` int(4) NOT NULL default '1'," .
		"`taeglich` bit NOT NULL default 0," .
		"PRIMARY KEY  (`id`)" .
		") COMMENT='Bestellsystem' AUTO_INCREMENT=1",
		"INSERT INTO " . $db_tb_iwdbtabellen . " (`name`) VALUES ('bestellung')",
	);

	foreach ($sqlscript as $sql) {
		echo "<br>" . $sql;
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

	echo "<br>Installation: Datenbankänderungen = <b>OK</b><br>";
*/
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
  	insertMenuItem( $_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparamters );
	  //
	  // Weitere Wiederholungen fuer weitere Menue-Einträge, z.B.
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
  return "";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//

function workUninstallDatabase() {
/*
 	global $db, $db_tb_bestellung, $db_tb_iwdbtabellen;

	$sqlscript = array(
	  "DROP TABLE " . $db_tb_bestellung,
	  "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE `name`='bestellung'",
	);

	foreach ($sqlscript as $sql) {
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

	echo "<br>Deinstallation: Datenbankänderungen = <b>OK</b><br>";
*/
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgefuehrt wenn das Modul mit dem Parameter 
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

  echo "<br>Installationsarbeiten am Modul " . $modulname . 
	     " ("  . $_REQUEST['was'] . ")<br><br>\n";

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
//
// -> Und hier beginnt das eigentliche Modul

// Parameter ermitteln
$params = array(
	'view' => getVar('view'),
	'order' => getVar('order'),
	'orderd' => getVar('orderd'),
	'edit' => getVar('edit'),
	'delete' => getVar('delete'),
	'expand' => getVar('expand'),
	'filter_team' => getVar('filter_team'),
);

// Parameter validieren
if (empty($params['view']))
	$params['view'] = 'bestellung';
if (empty($params['order'])) 
	$params['order'] = 'sort';
if ($params['orderd'] != 'asc' && $params['orderd'] != 'desc')
	$params['orderd'] = 'asc';
if (empty($params['filter_team']))
	$params['filter_team'] = '(Alle)';

debug_var("params", $params);

// Stammdaten abfragen
$config = array();

// Spieler und Teams abfragen
$users = array();
$teams = array();
$teams['(Alle)'] = '(Alle)';
$sql = "SELECT * FROM " . $db_tb_user;
$sql .= " WHERE allianz='" . $user_allianz . "'";
debug_var('sql', $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	$users[$row['id']] = $row['id'];
	if (!empty($row['buddlerfrom']))
		$teams[$row['buddlerfrom']] = $row['buddlerfrom'];
}
$config['users'] = $users;
$config['teams'] = $teams;

// Planeten des Spielers abfragen
$config['planeten'][] = "(Keiner)";
$sql = "SELECT * FROM " . $db_tb_scans . " WHERE user='" . $user_sitterlogin . "' ORDER BY sortierung";
debug_var('sql', $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
	$config['planeten'][$row['coords']] = $row['coords'] . " " . $row['planetenname'];

// Projekte abfragen
$sql = "SELECT * FROM " . $db_tb_bestellung_projekt . " WHERE schiff=1 ORDER BY prio ASC";
debug_var("sql", $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	$config['projects'][$row['name']] = $row['name'] . ($row['prio'] < 999 ? " (Priorität " . $row['prio'] . ")" : "");
	$config['projects_prio'][$row['name']] = $row['prio'];
}

// Schiffstypen abfragen
$schiffstypen = array();
$sql = "SELECT * FROM " . $db_tb_schiffstyp . " WHERE bestellbar=1 ORDER BY typ, abk";
debug_var('sql', $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
	$schiffstypen[$row['schiff']] = array(
		'id' => $row['id'],
		'abk' => $row['abk'],
		'typ' => $row['typ'],
	);
$config['schiffstypen'] = $schiffstypen;

// Timestamp
$heute = getdate();

// Daten löschen
if (isset($params['delete']) && $params['delete'] != '') {
	debug_var('sql', $sql = "DELETE FROM " . $db_tb_bestellung_schiffe_pos . " WHERE bestellung_id=" . $params['delete']);
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$sql = "DELETE FROM " . $db_tb_bestellung_schiffe . " WHERE id=" . $params['delete'];
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$results[] = "<div class='system_notification'>Datensatz gel&ouml;scht.</div><br>";
	$params['delete'] = '';
	$params['edit'] = '';
}

// Button abfragen
$button_edit = getVar("button_edit");
$button_add = getVar("button_add");

// Edit-Daten belegen
if (!empty($button_edit) || !empty($button_add)) {
	debug_var("edit", $edit = array(
		'user' => getVar('user'),
		'planet' => getVar('planet'),
		'coords_gal' => getVar('coords_gal'),
		'coords_sys' => getVar('coords_sys'),
		'coords_planet' => getVar('coords_planet'),
		'team' => getVar('team'),
		'project' => getVar('project'),
		'text' => getVar('text'),
		'time' => parsetime(getVar('time')),
	));
} else {
	debug_var("edit", $edit = array(
		'user' => $user_sitterlogin,
		'planet' => '',
		'coords_gal' => '',
		'coords_sys' => '',
		'coords_planet' => '',
		'team' => '(Alle)',
		'project' => '(Keins)',
		'text' => '',
		'time' => time(),
	));
}
foreach ($config['schiffstypen'] as $schiffstyp)
	debug_var("edit[schiff_" . $schiffstyp['id'] . "]", $edit['schiff_' . $schiffstyp['id']] = getVar('schiff_' . $schiffstyp['id'])); 

// Planet suchen
if (!empty($edit['planet'])) {
	$coords_tokens = explode(":", $edit['planet']);
	$edit['coords_gal'] = $coords_tokens[0];
	$edit['coords_sys'] = $coords_tokens[1];
	$edit['coords_planet'] = $coords_tokens[2];
}

// Felder belegen
foreach ($edit as $key => $value)
	if (strncmp($key, "schiff_", 7) != 0)
		$fields[$key] = (is_numeric($value) ? $value : "'" . $value . "'");
unset($fields['planet']);

// Edit-Daten modifizieren
if (!empty($button_edit)) {
	$sql = "UPDATE " . $db_tb_bestellung_schiffe . " SET ";
	foreach ($fields as $name => $value)
		$tokens[] = $name . "=" . $value;
	$sql .= implode($tokens, ",");
	$sql .= " WHERE ID=" . $params['edit'];
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$results[] = "<div class='system_notification'>Datensatz aktualisiert.</div><br>";
}

// Edit-Daten hinzufuegen
$doppelbelegung="false";
if (!empty($button_add)) {
	$sql = "SELECT * FROM " . $db_tb_bestellung_schiffe . " WHERE coords_gal=" . $fields['coords_gal'] . " AND coords_planet=" . $fields['coords_planet'] . " AND coords_sys=" . $fields['coords_sys'];
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	if ($row = $db->db_fetch_array($result)) {
		$results[] = "<div class='system_notification'>Pro Planet kann nur eine Bestellung hinzugefügt werden.</div><br>";	
		$doppelbelegung="true";		
	} else {
		$fields['time_created'] = time();
		$sql = "INSERT INTO " . $db_tb_bestellung_schiffe . " (";
		$sql .= implode(array_keys($fields), ",");
		$sql .= ") VALUES (";
		$sql .= implode($fields, ",");
		$sql .= ");";
		debug_var('sql', $sql);
		$db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$sql = "SELECT MAX(ID) AS id FROM " . $db_tb_bestellung_schiffe;
		debug_var('sql', $sql);
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		if ($row = $db->db_fetch_array($result))
			$params['edit'] = $row['id'];
		$results[] = "<div class='system_notification'>Datensatz hinzugefügt.</div><br>";
	}
}

// Edit-Daten hinzufügen/modifizeren
if ((!empty($button_add) || !empty($button_edit)) && $doppelbelegung!="true") {
	debug_var('sql', $sql = "DELETE FROM " . $db_tb_bestellung_schiffe_pos . " WHERE bestellung_id=" . $params['edit']);
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	foreach ($config['schiffstypen'] as $schiffstyp) {
		$menge = $edit['schiff_' . $schiffstyp['id']];
		if (!empty($menge)) {
			debug_var('sql', $sql = "INSERT INTO " . $db_tb_bestellung_schiffe_pos . " (bestellung_id,schiffstyp_id,menge) VALUES (" .
				$params['edit'] . "," . $schiffstyp['id'] . "," . $menge . ")"
			);
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}
	}
}

// Edit-Daten abfragen
if (empty($button_edit) && empty($button_add) && is_numeric($params['edit'])) {
	debug_var('sql', $sql = "SELECT * FROM " . $db_tb_bestellung_schiffe . " WHERE id=" . $params['edit']);
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	if ($row = $db->db_fetch_array($result))
		foreach ($row as $name => $value)
			$edit[$name] = $value;
	debug_var('sql', $sql = "SELECT * FROM " . $db_tb_bestellung_schiffe_pos . " WHERE bestellung_id=" . $params['edit']);
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);	
	while ($row = $db->db_fetch_array($result))
		debug_var('edit[schiff_' . $row['schiffstyp_id'] . ']', $edit['schiff_' . $row['schiffstyp_id']] = $row['menge']);
}
$edit['time'] = strftime("%d.%m.%Y %H:%M", $edit['time']);

// Tabellen-Daten abfragen
$data = array();

// Bestellungen abfragen
$sql = "SELECT *, (SELECT $db_tb_bestellung_projekt.`prio` FROM $db_tb_bestellung_projekt WHERE $db_tb_bestellung_projekt.`name`=$db_tb_bestellung_schiffe.`project` AND $db_tb_bestellung_projekt.`schiff`=1) AS prio FROM $db_tb_bestellung_schiffe";
$sql .= " WHERE (SELECT allianz FROM $db_tb_user WHERE $db_tb_bestellung_schiffe.user=$db_tb_user.id)='" . $user_allianz . "'";
debug_var("sql", $sql .= " ORDER BY `prio`,$db_tb_bestellung_schiffe.`time`,$db_tb_bestellung_schiffe.`id` DESC");
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	// Koordinaten
	$coords = $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'];
	// Projekt und Bemerkung
	if (!empty($row['project']) && $row['project'] != "(Keins)")
		$text = "<b>" . $row['project'] . "</b><br>" . $row['text'];
	else
		$text = $row['text'];
	// Grunddaten	
	$data[$row['id']] = array(
		'id' => $row['id'],
		'user' => $row['user'],
		'coords' => $coords,
		'team' => $row['team'],
		'text' => $text,
		'prio' => $row['prio'],
		'time' => strftime("%d.%m.%Y %H:%M", $row['time']),
		'sort' => $row['prio'] . "-" . $row['time'],
	);
	// Positionsdaten
	debug_var("sql_pos", $sql_pos = "SELECT $db_tb_bestellung_schiffe_pos.*, $db_tb_schiffstyp.schiff 
		FROM $db_tb_bestellung_schiffe_pos, $db_tb_schiffstyp 
		WHERE bestellung_id=" . $row['id'] . " AND $db_tb_bestellung_schiffe_pos.schiffstyp_id=$db_tb_schiffstyp.id");
	$result_pos = $db->db_query($sql_pos)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while ($row_pos = $db->db_fetch_array($result_pos)) {
		$data[$row['id']]['pos'][$row_pos['schiff']] = $row_pos['menge'];
		$data[$row['id']]['offen'][$row_pos['schiff']] = $row_pos['menge'];
	}
	if (!empty($data[$row['id']]['pos']))
		$data[$row['id']]['menge'] = makeschifftable($data[$row['id']]['pos'], true);
	else
		$data[$row['id']]['menge'] = "";
	// Lieferungen abfragen
	if (!isset($lieferungen[$coords])) {
		debug_var("sql_lieferung", $sql_lieferung = 
		      "SELECT *,
				(SELECT $db_tb_user.`buddlerfrom` FROM $db_tb_user WHERE $db_tb_user.`id`=$db_tb_lieferung.`user_from`) AS team
			FROM $db_tb_lieferung
			WHERE $db_tb_lieferung.`coords_to_gal`=" . $row['coords_gal'] . "
			AND $db_tb_lieferung.`coords_to_sys`=" . $row['coords_sys'] . "
			AND $db_tb_lieferung.`coords_to_planet`=" . $row['coords_planet'] . "
			AND ($db_tb_lieferung.`art`='Übergabe' OR $db_tb_lieferung.`art`='Übergabe (tr Schiffe)')
			AND $db_tb_lieferung.`time`>" . $row['time_created'] . "
			AND $db_tb_lieferung.`user_from`<>$db_tb_lieferung.`user_to`
			ORDER BY $db_tb_lieferung.`time`");
		$result_lieferung = $db->db_query($sql_lieferung)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		while ($row_lieferung = $db->db_fetch_array($result_lieferung)) {
			$coords_from = $row_lieferung['coords_from_gal'] . ":" . $row_lieferung['coords_from_sys'] . ":" . $row_lieferung['coords_from_planet'];
			$key = $coords_from . "-" . $row_lieferung['time'];
			$lieferungen[$coords][$key] = array(
				'user' => $row_lieferung['user_from'],
				'coords' => $coords_from,
				'team' => $row_lieferung['team'],
				'art' => $row_lieferung['art'],
				'time' => strftime("%d.%m.%Y %H:%M", $row_lieferung['time']),
			);
			foreach (explode("<br>", $row_lieferung['schiffe']) as $line)
				if (preg_match("/(\d+)\s(.*)/", $line, $match) > 0) {
					$lieferungen[$coords][$key]['menge'][$match[2]] = $match[1];
					$lieferungen[$coords][$key]['frei'][$match[2]] = $match[1];
				}
			debug_var("lieferungen[$coords][$key]", $lieferungen[$coords][$key]);
		}
	}
}

// Offene Mengen zurücksetzen
debug_var("sql", $sql = "UPDATE $db_tb_bestellung_schiffe_pos SET offen=menge");
$db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
// Offene Mengen berechnen
foreach ($data as $id_bestellung => $bestellung) {
	$coords = $bestellung['coords'];
	if (isset($lieferungen[$coords])) {
		foreach ($lieferungen[$coords] as $id_lieferung => $lieferung) {
			$verwendet = false;
			if (isset($lieferung['frei'])) {
				foreach ($lieferung['frei'] as $key => $menge) {
					// Sind noch offene Positionen vorhanden?
					if (!empty($data[$id_bestellung]['offen'][$key])) {
						// Offene Bestellmenge grösser als freie Liefermenge
						if ($data[$id_bestellung]['offen'][$key] > $menge) {
							// Offene Bestellmenge um freie Liefermenge verringern
							$data[$id_bestellung]['offen'][$key] -= $menge;
							// Freie Liefermenge auf 0 setzen
							$lieferungen[$coords][$id_lieferung]['frei'][$key] = 0;
						// Offene Bestellmenge kleiner als freie Liefermenge
						} elseif ($data[$id_bestellung]['offen'][$key] <= $menge) {
							// Freie Liefermenge um offene Bestellmenge verringern
							$lieferungen[$coords][$id_lieferung]['frei'][$key] -= $data[$id_bestellung]['offen'][$key];
							// Offene Bestellmenge auf 0 setzen
							$data[$id_bestellung]['offen'][$key] = 0;				
						}
						$verwendet = true;
					}
					if (isset($data[$id_bestellung]['offen'][$key]))
						$offen = intval($data[$id_bestellung]['offen'][$key]);
					else
						$offen = 0;
					debug_var("sql", $sql = "UPDATE $db_tb_bestellung_schiffe_pos SET offen=" . $offen .
						" WHERE `bestellung_id`=" . $id_bestellung .
						"   AND `schiffstyp_id`=(SELECT `id` FROM $db_tb_schiffstyp WHERE schiff='" . $key . "')");
					$db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				}
				if ($verwendet) {
					$data[$id_bestellung]['expand'][] = array(
						'user' => $lieferung['user'],
						'coords' => $lieferung['coords'],
						'team' => $lieferung['team'],
						'art' => $lieferung['art'],
						'blank' => " ",
						'time' => $lieferung['time'],
						'menge' => makeschifftable($lieferung['menge'], true),
						'offen' => makeschifftable($data[$id_bestellung]['offen']),
					);
				}
			}
		}
	}
	// Markiere vollständig erledigte Bestellungen
	$kontrollsumme = 0;
	if (!empty($data[$id_bestellung]['offen'])) {
		foreach ($data[$id_bestellung]['offen'] as $key => $menge)
			$kontrollsumme += $menge;
	}
	debug_var("sql_erledigt", $sql_erledigt = "
		UPDATE " . $db_tb_bestellung_schiffe . " 
		SET " . $db_tb_bestellung_schiffe . ".erledigt=" . ($kontrollsumme ? '0' : '1') . " 
		WHERE " . $db_tb_bestellung_schiffe . ".id=" . $id_bestellung);
	$db->db_query($sql_erledigt)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	// Mengen formatieren
	if (!empty($data[$id_bestellung]['offen']))
		$data[$id_bestellung]['offen'] = makeschifftable($data[$id_bestellung]['offen']);
	else
		$data[$id_bestellung]['offen'] = "";
}

// Daten sortieren
usort($data, "sort_data_cmp");

// Ansichten definieren
$views = array(
	'bestellung' => array(
		'title' => 'Bestellungen',
		'columns' => array(
			'user' => 'Spieler',
			'coords' => 'Koords',
			'team' => 'Team',
			'text' => 'Text',
			'prio' => 'Prio',
			'time' => 'Zeit',
			'menge' => 'Menge',
			'offen' => 'Offen',
		),
		'key' => 'id',
		'edit' => array(
			'user' => array(
				'title' => 'Spieler',
				'desc' => 'Welcher Spieler soll beliefert werden?',
				'type' => 'select',
				'values' => $config['users'],
				'value' => $edit['user'],
			),
			'planet' => array(
				'title' => 'Planet',
				'desc' => 'Welcher Planet soll beliefert werden?',
				'type' => 'select',
				'values' => $config['planeten'],
				'value' => $edit['planet'],
			),
			'coords' => array(
				'title' => 'Koordinaten',
				'desc' => 'Falls kein Planet ausgewählt wird.',
				'type' => array(
					'coords_gal' => array(
						'type' => 'text',
						'style' => 'width: 30;',
						'value' => $edit['coords_gal'],
					),
					'coords_sys' => array(
						'type' => 'text',
						'style' => 'width: 30;',
						'value' => $edit['coords_sys'],
					),
					'coords_planet' => array(
						'type' => 'text',
						'style' => 'width: 30;',
						'value' => $edit['coords_planet'],
					),
				),
			),
			'team' => array(
				'title' => 'Team',
				'desc' => 'Welches Team soll liefern?',
				'type' => 'select',
				'values' => $config['teams'],
				'value' => $edit['team'],
			),
			'project' => array(
				'title' => 'Projekt',
				'desc' => 'F&uuml;r welches Projekt ist die Lieferung?',
				'type' => 'select',
				'values' => $config['projects'],
				'value' => $edit['project'],
			),
			'text' => array(
				'title' => 'Text',
				'desc' => 'Bemerkung für diese Bestellung.',
				'type' => 'area',
				'rows' => 5,
				'cols' => 80,
				'value' => $edit['text'],
			),
			'time' => array(
				'title' => 'Zeit',
				'desc' => 'Wann soll die Lieferung ankommen?',
				'type' => 'text',
				'value' => $edit['time'],
				'style' => 'width: 200;',
			),
		),		
		'expand' => array(
			'title' => 'Lieferungen',
			'columns' => array(
				'user' => 'Spieler',
				'coords' => 'Koords',
				'team' => 'Team',
				'art' => 'Art',
				'blank' => " ",
				'time' => 'Ankunft',
				'menge' => 'Menge',
				'offen' => 'Offen',
			),
		),
	),
);

$typ = '';
foreach ($config['schiffstypen'] as $schiffstyp) {
	if ($schiffstyp['typ'] != $typ) {
		$views['bestellung']['edit'][$schiffstyp['typ']] = array(
			'title' => $schiffstyp['typ'],
			'type' => 'label',
			'colspan' => 2);
		$typ = $schiffstyp['typ'];
	}
	$views['bestellung']['edit']['schiff_' . $schiffstyp['id']] = array(
		'title' => $schiffstyp['abk'],
		'desc' => 'Anzahl angeben',
		'type' => 'text',
		'value' => $edit['schiff_' . $schiffstyp['id']],
		'style' => 'width: 50;');
}

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
echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p align="center">';
echo 'Team: ';
echo makefield(array("type" => 'select', "values" => $config['teams'], "value" => $params['filter_team']), 'filter_team');
echo ' <input type="submit" name="submit" value="anzeigen"/>';
echo "</form><br><br>\n";

// Daten ausgeben
start_form("m_flotte_versenden", array("nobody" => 1, "art" => "bestellung_schiffe"));
start_table(100);
start_row("titlebg", "nowrap valign=top");
foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
	next_cell("titlebg", "nowrap valign=top");
	$orderkey = $viewcolumnkey;
	if (isset($view['sortcolumns'][$orderkey]))
		$orderkey = $view['sortcolumns'][$orderkey];
		echo makelink(
		array(
			'order' => $orderkey,
			'orderd' => 'asc'
		),
		"<img src='./bilder/asc.gif' border='0'>");
	echo '<b>' . $viewcolumnname . '</b>';
	echo makelink(
		array(
			'order' => $orderkey,
			'orderd' => 'desc'
		),
		"<img src='./bilder/desc.gif' border='0'>");
}
if (isset($view['edit'])) {
	next_cell("titlebg", 'nowrap valign=top');
	echo '&nbsp;';
}
next_cell("titlebg");
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
		if ($viewcolumnkey=="text") {
			next_cell("windowbg1", 'valign=top style="background-color: white;"');
		}
		else {
			next_cell("windowbg1", 'nowrap valign=top style="background-color: white;"');
		}
		echo $row[$viewcolumnkey];
	}
	// Editbuttons ausgeben
	if (isset($view['edit'])) {
		next_cell("windowbg1", 'nowrap valign=top');
		if (!isset($row['allow_edit']) || $row['allow_edit'])
			echo makelink(
				array('edit' => $key),
				"<img src='bilder/file_edit_s.gif' border='0' alt='bearbeiten'>"
			);
		if (!isset($row['allow_delete']) || $row['can_delete'])
			echo makelink(
				array('delete' => $key),
				"<img src='bilder/file_delete_s.gif' border='0' onclick=\"return confirmlink(this, 'Datensatz wirklich löschen?')\" alt='löschen'>"
			);
	}
	// Markierbuttons ausgeben
	next_cell("windowbg1", 'nowrap valign=top');
	echo "<input type='checkbox' name='mark_" . $index++ . "' value='" . $key . "'";
	if (getVar("mark_all"))
		echo " checked";
	echo ">";
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
				next_cell("windowbg1", "nowrap valign=top");
				echo $expand_row[$expandcolumnkey];
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
end_table();
end_form();

// Maske ausgeben
echo '<br>';
echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p>' . "\n";
start_table();
next_row("titlebg", 'nowrap valign=top colspan=2');
echo "<b>" . $view['title'];
if (isset($params['edit']) && is_numeric($params['edit'])) {
	echo " bearbeiten/hinzufügen";
	echo '<input type="hidden" name="edit" value="' . $params['edit'] . '">' . "\n";
	// echo '<input type="hidden" name="list_team" value="'.$list_team.'" />' . "\n";
} else
	echo " hinzuf&uuml;gen";
echo "</b>";
foreach ($view['edit'] as $key => $field) {
	if ($field['type'] == 'label') {
		next_row('titlebg', 'nowrap valign=top', isset($field['colspan']) ? $field['colspan'] : 1);
		echo $field['title'];
	} else {
		next_row('windowbg2', 'nowrap valign=top');
		echo $field['title'];
		if (isset($field['desc']))
			echo '<br><i>' . $field['desc'] . '</i>';
		next_cell('windowbg1', 'style="width: 100%;"');
		if (is_array($field['type'])) {
			$first = true;
			foreach ($field['type'] as $key => $field) {
				if (!$first)
					echo '&nbsp;';
				echo makefield($field, $key);
				$first = false;
			}
		} else
			echo makefield($field, $key);
	}
}

next_row('titlebg', 'align=center colspan=2');
if (isset($params['edit']) && is_numeric($params['edit']))
	echo '<input type="submit" value="speichern" name="button_edit" class="submit"> ';
echo '<input type="submit" value="hinzuf&uuml;gen" name="button_add" class="submit">';
end_table();
echo '</form>';

function makeschifftable($row, $nocolor = false) {
	global $config;
	$html = '<table width="100%">';
	foreach ($row as $typ => $menge) {
		$html .= '<tr>';
		$html .= "<td nowrap width='30%'>";
		if (!$nocolor) {
			$html .= '<span class="';
			if ($menge > 0)
				$html .= 'ranking_red';
			else
				$html .= 'ranking_green';
			$html .= '">';
		}
		$html .= number_format($menge, 0, ',', '.');
		if (!$nocolor)
			$html .= '</span>';
		$html .= '</td>';
		$html .= '<td nowrap>';
		if (!$nocolor) {
			$html .= '<span class="';
			if ($menge > 0)
				$html .= 'ranking_red';
			else
				$html .= 'ranking_green';
			$html .= '">';
		}
		if (isset($config['schiffstypen'][$typ]) && !empty($config['schiffstypen'][$typ]['abk']))
			$html .= $config['schiffstypen'][$typ]['abk'];
		else
			$html .= $typ;
		if (!$nocolor)
			$html .= '</span>';
		$html .= '</td>';
		$html .= '</tr>';
	}
	$html .= "</table>";
	return $html;
}

function makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, $name, $title) {
	$html = "";
	$cmp = $row[$prefix_cmp . $name];
	$value = $row[$prefix_out . $name];
	if ($cmp != 0) {
		$html = '<tr><td nowrap>' . $title . '</td><td nowrap align="right">';
		if (!$nocolor) {
			$html .= '<span class="';
			if ($value > 0)
				$html .= 'ranking_red';
			else
				$html .= 'ranking_green';
			$html .= '">';
		}
		$html .= number_format($value, 0, ',', '.');
		if (!$nocolor)
			$html .= '</span>';
		$html .= '</td></tr>';
	}
	return $html;
}

//****************************************************************************
//
// Vergleichsfunktion für das sortieren
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
// Zeit einlesen.
function parsetime($text) {
	if (preg_match("/(\d+).(\d+).(\d+) (\d+):(\d+)/", $text, $match) > 0) {
		$temptime=mktime($match[4], $match[5], 0, $match[2], $match[1], $match[3]);
		if ($temptime < time()){
			return time();
		}
		else {
			return mktime($match[4], $match[5], 0, $match[2], $match[1], $match[3]);
		}
	}
	else
		return time();
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
	$url .= '&sid=' . $sid;
	$mergeparams = array_merge($params, $newparams);
	foreach ($mergeparams as $paramkey => $paramvalue)
		$url .= '&' . $paramkey . '=' . $paramvalue;
	return $url;
}
?>
