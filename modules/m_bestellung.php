<?php
/*****************************************************************************/
/* m_bestellung.php                                                          */
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
/* fuer die IWDB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Author: [GILDE]Thella (mailto:icewars@thella.de)                          */
/* Version: 0.1                                                              */
/* Date: xx/xx/xxxx                                                          */
/*                                                                           */
/* Originally written by [GILDE]xerex.                                       */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschaftsprojekt von */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete           */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

define('DEBUG_LEVEL', 0);

include_once("includes/debug.php");

// -> Abfrage ob dieses Modul ueber die index.php aufgerufen wurde. 
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") { 
	echo "Hacking attempt...!!"; 
	exit; 
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig fuer die Benennung der zugehÃÂ¶rigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung fuer 
//    eine Installation ueber das Menue
//
$modulname  = "m_bestellung";

//****************************************************************************
//
// -> Menuetitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Bestellung";

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
$moduldesc = "Bestellsystem zur Koordination von Logistikauftr&auml;gen im Buddler-Fleeter-System.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase() {
	global $db, $db_prefix, $db_tb_iwdbtabellen;
/*
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
*/
	echo "<br>Installation: Datenbank&auml;nderungen = <b>OK</b><br>";
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
	  // Weitere Wiederholungen fuer weitere Menue-EintrÃÂ¤ge, z.B.
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
	global $db, $db_tb_bestellung, $db_tb_iwdbtabellen;

	$sqlscript = array(
	  "DROP TABLE " . $db_tb_bestellung,
	  "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE `name`='bestellung'",
	);

/*
	foreach ($sqlscript as $sql) {
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

	echo "<br>Deinstallation: Datenbank&auml;nderungen = <b>OK</b><br>";
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
// Anstatt "Mein.Server" natuerlich deinen Server angeben und default 
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
  // ausgefuehrt werden. 
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
	$params['order'] = 'prio';
if ($params['orderd'] != 'asc' && $params['orderd'] != 'desc')
	$params['orderd'] = 'asc';
if (empty($params['filter_team']))
	$params['filter_team'] = $user_buddlerfrom;

debug_var("params", $params);

// Stammdaten abfragen
$config = array();

// Ressourcen
$config['ress'] = array("eisen" => "Eisen", "stahl" => "Stahl", "vv4a" => "VV4A", "chemie" => "Chemie", "eis" => "Eis", "wasser" => "Wasser", "energie" => "Energie", "credits" => "Credits");

// Spieler und Teams abfragen
$users = array();
$teams = array();
$teams['(Alle)'] = '(Alle)';
$sql = "SELECT * FROM " . $db_tb_user;
if (!$user_fremdesitten) {
	$sql .= " WHERE allianz='" . $user_allianz . "'";
}
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

// Schiffstypen abfragen
$schiffstypen = array();
$sql = "SELECT * FROM " . $db_tb_schiffstyp;
debug_var('sql', $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
	if (!empty($row['abk']))
		$schiffstypen[$row['abk']] = $row['abk'];
asort($schiffstypen);
$config['schiffstypen'] = $schiffstypen;

// Planeten des Spielers abfragen
$config['planeten'][] = "(Keiner)";
$sql = "SELECT * FROM " . $db_tb_scans . " WHERE user='" . $user_sitterlogin . "' ORDER BY sortierung";
debug_var('sql', $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
	$config['planeten'][$row['coords']] = $row['coords'] . " " . $row['planetenname'];

// Projekte abfragen
$sql = "SELECT * FROM " . $db_tb_bestellung_projekt . " WHERE schiff=0 ORDER BY prio ASC";
debug_var("sql", $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	$config['projects'][$row['name']] = $row['name'] . ($row['prio'] < 999 ? " (Priorit&auml;t " . $row['prio'] . ")" : "");
	$config['projects_prio'][$row['name']] = $row['prio'];
}

// Timestamp
$heute = getdate();

// Daten loeschen
if (isset($params['delete']) && $params['delete'] != '') {
	$sql = "DELETE FROM " . $db_tb_bestellung . " WHERE id=" . $params['delete'];
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$results[] = "<div class='system_notification'>Datensatz geloescht.</div><br>";
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
		'eisen' => getVar('eisen'),
		'stahl' => getVar('stahl'),
		'chemie' => getVar('chemie'),
		'vv4a' => getVar('vv4a'),
		'eis' => getVar('eis'),
		'wasser' => getVar('wasser'),
		'energie' => getVar('energie'),
		'credits' => getVar('credits'),
		'volk' => getVar('volk'),
		'schiff' => getVar('schiff'),
		'anzahl' => getVar('anzahl'),
	));
} else {
	debug_var("edit", $edit = array(
		'user' => $user_sitterlogin,
		'planet' => '',
		'coords_gal' => '',
		'coords_sys' => '',
		'coords_planet' => '',
		'team' => $user_buddlerfrom,
		'project' => '(Keins)',
		'text' => '',
		'time' => time(),
		'eisen' => '',
		'stahl' => '',
		'chemie' => '',
		'vv4a' => '',
		'eis' => '',
		'wasser' => '',
		'energie' => '',
		'credits' => '',
	));
}

// Planet suchen
if (!empty($edit['planet'])) {
	$coords_tokens = explode(":", $edit['planet']);
	$edit['coords_gal'] = $coords_tokens[0];
	$edit['coords_sys'] = $coords_tokens[1];
	$edit['coords_planet'] = $coords_tokens[2];
}

// Felder belegen
foreach ($edit as $key => $value)
	$fields[$key] = (is_numeric($value) ? $value : "'" . $value . "'");
unset($fields['planet']);

// Edit-Daten modifizieren
if (!empty($button_edit)) {
	$sql = "UPDATE " . $db_tb_bestellung . " SET ";
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
if (!empty($button_add)) {
	$sql = "SELECT * FROM " . $db_tb_bestellung . " WHERE coords_gal=" . $fields['coords_gal'] . " AND coords_planet=" . $fields['coords_planet'] . " AND coords_sys=" . $fields['coords_sys'];
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	if ($row = $db->db_fetch_array($result)) {
		$results[] = "<div class='system_notification'>Pro Planet kann nur eine Bestellung hinzugefuegt werden.</div><br>";			
	} else {
		$fields['time_created'] = time();
		$sql = "INSERT INTO " . $db_tb_bestellung . " (";
		$sql .= implode(array_keys($fields), ",");
		$sql .= ") VALUES (";
		$sql .= implode($fields, ",");
		$sql .= ");";
		debug_var('sql', $sql);
		$db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$sql = "SELECT MAX(ID) AS id FROM " . $db_tb_bestellung;
		debug_var('sql', $sql);
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		if ($row = $db->db_fetch_array($result))
			$params['edit'] = $row['id'];
		$results[] = "<div class='system_notification'>Datensatz hinzugefuegt.</div><br>";
	}
}

// Edit-Daten abfragen
if (empty($button_edit) && empty($button_add) && is_numeric($params['edit'])) {
	$sql = "SELECT * FROM " . $db_tb_bestellung . " WHERE id=" . $params['edit'];
	debug_var('sql', $sql);
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	if ($row = $db->db_fetch_array($result))
		foreach ($row as $name => $value)
			$edit[$name] = $value;
}
$edit['time'] = strftime("%d.%m.%Y %H:%M", $edit['time']);

// Tabellen-Daten abfragen
$data = array();

// Bestellungen abfragen
$sql = "SELECT *,
		 (SELECT $db_tb_bestellung_projekt.`prio` FROM $db_tb_bestellung_projekt WHERE $db_tb_bestellung_projekt.`name`=$db_tb_bestellung.`project` AND $db_tb_bestellung_projekt.`schiff`=0) AS prio
	 FROM $db_tb_bestellung";
if (isset($params['filter_team']) && $params['filter_team'] != '(Alle)')
{
	$sql .= " WHERE (" . $db_tb_bestellung . ".team='" . $params['filter_team'] . "' OR " . $db_tb_bestellung . ".team IS NULL" . " OR " . $db_tb_bestellung . ".team='(Alle)')";
	if (!$user_fremdesitten)
		$sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_bestellung . ".user) = '" . $user_allianz . "'";
}
else if (!$user_fremdesitten)
	$sql .= " WHERE (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_bestellung . ".user) = '" . $user_allianz . "'";
$sql .= " ORDER BY `prio`,$db_tb_bestellung.`time`,$db_tb_bestellung.`user`,$db_tb_bestellung.`coords_gal`,$db_tb_bestellung.`coords_sys`,$db_tb_bestellung.`coords_planet`";
debug_var("sql", $sql);
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
		'menge' => makeresstable($row, '', '', true),
		'sort' => $row['prio'] . "-" . $row['time'],
	);

	// Offene Mengen
	foreach ($config['ress'] as $key => $caption)
		$data[$row['id']]['offen'][$key] = $row[$key];

	// Lieferungen abfragen
	if (!isset($lieferungen[$coords])) {
		debug_var("sql_lieferung", $sql_lieferung = 
		      "SELECT *,
				(SELECT $db_tb_user.`buddlerfrom` FROM $db_tb_user WHERE $db_tb_user.`id`=$db_tb_lieferung.`user_from`) AS team
			FROM $db_tb_lieferung
			WHERE $db_tb_lieferung.`coords_to_gal`=" . $row['coords_gal'] . "
			AND $db_tb_lieferung.`coords_to_sys`=" . $row['coords_sys'] . "
			AND $db_tb_lieferung.`coords_to_planet`=" . $row['coords_planet'] . "
			AND $db_tb_lieferung.`art`='Transport'
			AND $db_tb_lieferung.`time`>" . $row['time_created'] . "
			AND $db_tb_lieferung.`user_from`<>$db_tb_lieferung.`user_to`
			ORDER BY $db_tb_lieferung.`time`");
		$result_lieferung = $db->db_query($sql_lieferung)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		while ($row_lieferung = $db->db_fetch_array($result_lieferung)) {
			$coords_from = $row_lieferung['coords_from_gal'] . ":" . $row_lieferung['coords_from_sys'] . ":" . $row_lieferung['coords_from_planet'];
			$key = $coords_from . "-" . $row_lieferung['time'];
			debug_var("lieferungen[$coords][$key]", $lieferungen[$coords][$key] = array(
				'user' => $row_lieferung['user_from'],
				'coords' => $coords_from,
				'team' => $row_lieferung['team'],
				'art' => $row_lieferung['art'],
				'time' => strftime("%d.%m.%Y %H:%M", $row_lieferung['time']),
			));
			foreach ($config['ress'] as $ress => $caption) {
				if ($ress == "credits")
				{
					$lieferungen[$coords][$key][$ress] = 0;
					$lieferungen[$coords][$key][$ress . '_frei'] = 0;
				}
				else
				{
					$lieferungen[$coords][$key][$ress] = $row_lieferung[($ress == "chemie" ? "chem" : $ress)];
					$lieferungen[$coords][$key][$ress . '_frei'] = $row_lieferung[($ress == "chemie" ? "chem" : $ress)];
				}
			}
		}
	}
}
// Offene Mengen zuruecksetzen
debug_var("sql", $sql = "UPDATE $db_tb_bestellung SET offen_eisen=eisen,offen_stahl=stahl,offen_chemie=chemie,offen_vv4a=vv4a,offen_eis=eis,offen_wasser=wasser,offen_energie=energie,offen_credits=credits");
$db->db_query($sql);
// Offene Mengen berechnen
foreach ($data as $id_bestellung => $bestellung) {
	$coords=$bestellung['coords'];
	if (isset($lieferungen[$coords])) {
		foreach ($lieferungen[$coords] as $id_lieferung => $lieferung) {
			$verwendet = false;
			foreach ($config['ress'] as $key => $caption) {
				if (!empty($data[$id_bestellung]['offen'][$key]) && !empty($lieferungen[$coords][$id_lieferung][$key . '_frei'])) {
					// Offene Bestellmenge groesser als freie Liefermenge
					if ($data[$id_bestellung]['offen'][$key] > $lieferungen[$coords][$id_lieferung][$key . '_frei']) {
						// Offene Bestellmenge um freie Liefermenge verringern
						$data[$id_bestellung]['offen'][$key] -= $lieferungen[$coords][$id_lieferung][$key . '_frei'];
						// Freie Liefermenge auf 0 setzen
						$lieferungen[$coords][$id_lieferung][$key . '_frei'] = 0;
					// Offene Bestellmenge kleiner als freie Liefermenge
					} elseif ($data[$id_bestellung]['offen'][$key] <= $lieferungen[$coords][$id_lieferung][$key . '_frei']) {
						// Freie Liefermenge um offene Bestellmenge verringern
						$lieferungen[$coords][$id_lieferung][$key . '_frei'] -= $data[$id_bestellung]['offen'][$key];
						// Offene Bestellmenge auf 0 setzen
						$data[$id_bestellung]['offen'][$key] = 0;
					}
					$verwendet = true;
				}
			}
			if ($verwendet) {
				debug_var("lieferung", $lieferungen[$coords]);
				$data[$id_bestellung]['expand'][] = array(
					'user' => $lieferung['user'],
					'coords' => $lieferung['coords'],
					'team' => $lieferung['team'],
					'art' => $lieferung['art'],
					'blank' => " ",
					'time' => $lieferung['time'],
					'menge' => makeresstable($lieferung, '', '', true),
					'offen' => makeresstable($data[$id_bestellung]['offen'], '', ''),
				);
			}
		}
	}
	debug_var("sql", $sql = "UPDATE $db_tb_bestellung SET offen_eisen=" .
		$data[$id_bestellung]['offen']['eisen'] . "," .
		"offen_stahl=" . $data[$id_bestellung]['offen']['stahl'] . "," .
		"offen_chemie=" . $data[$id_bestellung]['offen']['chemie'] . "," .
		"offen_vv4a=" . $data[$id_bestellung]['offen']['vv4a'] . "," .
		"offen_eis=" . $data[$id_bestellung]['offen']['eis'] . "," .
		"offen_wasser=" . $data[$id_bestellung]['offen']['wasser'] . "," .
		"offen_energie=" . $data[$id_bestellung]['offen']['energie'] . "," .
		"offen_credits=" . $data[$id_bestellung]['offen']['credits'] .
		" WHERE id=" . $id_bestellung);
	$db->db_query($sql);
	// Markiere vollstaendig erledigte Bestellungen
	$kontrollsumme=0;
	foreach ($config['ress'] as $ress => $caption) {
		$kontrollsumme+=$data[$id_bestellung]['offen'][$ress];	
	}
	if ($kontrollsumme=="0") {
		debug_var("sql_erledigt", $sql_erledigt = "
			UPDATE " . $db_tb_bestellung . " 
			SET " . $db_tb_bestellung . ".erledigt='1' 
			WHERE " . $db_tb_bestellung . ".id=" . $data[$id_bestellung]['id']);
		$result_lieferung = $db->db_query($sql_erledigt)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}
	// Mengen formatieren
	$data[$id_bestellung]['offen'] = makeresstable($data[$id_bestellung]['offen'], '', '');
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
				'desc' => 'Falls kein Planet ausgew&auml;hlt wird.',
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
				'desc' => 'Bemerkung f&uuml;r diese Bestellung.',
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
			'eisen' => array(
				'title' => 'Eisen',
				'type' => 'text',
				'value' => $edit['eisen'],
				'style' => 'width: 70;',
			),
			'stahl' => array(
				'title' => 'Stahl',
				'type' => 'text',
				'value' => $edit['stahl'],
				'style' => 'width: 70;',
			),
			'chemie' => array(
				'title' => 'Chemie',
				'desc' => 'Auf Lager achten!',
				'type' => 'text',
				'value' => $edit['chemie'],
				'style' => 'width: 70;',
			),
			'vv4a' => array(
				'title' => 'VV4A',
				'type' => 'text',
				'value' => $edit['vv4a'],
				'style' => 'width: 70;',
			),
			'eis' => array(
				'title' => 'Eis',
				'desc' => 'Auf Lager achten!',
				'type' => 'text',
				'value' => $edit['eis'],
				'style' => 'width: 70;',
			),
			'wasser' => array(
				'title' => 'Wasser',
				'desc' => 'Auf Lager achten!',
				'type' => 'text',
				'value' => $edit['wasser'],
				'style' => 'width: 70;',
			),
			'energie' => array(
				'title' => 'Energie',
				'desc' => 'Auf Lager achten!',
				'type' => 'text',
				'value' => $edit['energie'],
				'style' => 'width: 70;',
			),
			'credits' => array(
				'title' => 'Credits',
				'desc' => '',
				'type' => 'text',
				'value' => $edit['credits'],
				'style' => 'width: 70;',
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

// Aktuelle Ansicht auswaehlen
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
echo ' <input type="submit" name="submit" value="anzeigen"/>';
echo "</form><br><br>\n";

// Daten ausgeben
start_form("m_flotte_versenden", array("nobody" => 1, "art" => "bestellung"));
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
		"<img src=\"./bilder/asc.gif\" border=\"0\">");
	echo '<b>' . $viewcolumnname . '</b>';
	echo makelink(
		array(
			'order' => $orderkey,
			'orderd' => 'desc'
		),
		"<img src=\"./bilder/desc.gif\" border=\"0\">");
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
	echo "<input type=\"checkbox\" name=\"mark_" . $index++ . "\" value=\"" . $key . "\"";
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
		}
		next_cell("windowbg1");
		echo '&nbsp;';
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
	echo " bearbeiten/hinzufuegen";
	echo '<input type="hidden" name="edit" value="' . $params['edit'] . '">' . "\n";
	// echo '<input type="hidden" name="list_team" value="'.$list_team.'" />' . "\n";
} else
	echo " hinzufuegen";
echo "</b>";
foreach ($view['edit'] as $key => $field) {
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

next_row('titlebg', 'align=center colspan=2');
if (isset($params['edit']) && is_numeric($params['edit']))
	echo '<input type="submit" value="speichern" name="button_edit" class="submit"> ';
echo '<input type="submit" value="hinzuf&uuml;gen" name="button_add" class="submit">';
end_table();
echo '</form>';

function makeresstable($row, $prefix_out = '', $prefix_cmp = '', $nocolor = false) {
	$html = '<table width="100%">';
	$html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'eisen', 'Eisen');
	$html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'stahl', 'Stahl');
	$html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'chemie', 'Chemie');
	$html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'vv4a', 'VV4A');
	$html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'eis', 'Eis');
	$html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'wasser', 'Wasser');
	$html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'energie', 'Energie');
	$html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'credits', 'Credits');
	$html .= "</table>";
	return $html;
}

function makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, $name, $title) {
	$html = "";
	if (!isset($row[$prefix_cmp . $name]))
	{
		print_r($row);
	}
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
// Zeit einlesen.
function parsetime($text) {
	if (preg_match("/(\d+).(\d+).(\d+) (\d+):(\d+)/", $text, $match) > 0)
		return mktime($match[4], $match[5], 0, $match[2], $match[1], $match[3]);
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
	$url .= '&amp;sid=' . $sid;
	$mergeparams = array_merge($params, $newparams);
	foreach ($mergeparams as $paramkey => $paramvalue)
		$url .= '&amp;' . $paramkey . '=' . $paramvalue;
	return $url;
}
?>
