<?php
/*****************************************************************************/
/* m_projects.php                                                            */
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
// -> Name des Moduls, ist notwendig fuer die Benennung der zugehörigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation ueber das Menue
//
$modulname  = "m_projects";

//****************************************************************************
//
// -> Menuetitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Projekteverwaltung";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation 
//    ausfuehren darf. Mögliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "admin";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc = "Verwaltung von Projekten im Bestellsystem";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase() {
	global $db, $db_prefix, $db_tb_iwdbtabellen;
/*
	$sqlscript = array(
		"CREATE TABLE `" . $db_prefix . "bestellung_projekt" (" .
  		"`name` varchar(30) NOT NULL," .
  		"`prio` int(11) NOT NULL," .
		"PRIMARY KEY  (`name`)" .
		"),
		"INSERT INTO " . $db_tb_iwdbtabellen . " (`name`) VALUES ('bestellung_projekt')",
	);

	foreach ($sqlscript as $sql) {
		echo "<br>" . $sql;
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}
*/
	echo "<br>Installation: Datenbankänderungen = <b>OK</b><br>";
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
	  // Weitere Wiederholungen fuer weitere Menü-Einträge, z.B.
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
	global $db, $db_tb_bestellung_projekt, $db_tb_iwdbtabellen;

	$sqlscript = array(
	  "DROP TABLE " . $db_tb_bestellung_projekt,
	  "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE `name`='bestellung_projekt'",
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
// Dieser Abschnitt wird nur ausgeführt wenn das Modul mit dem Parameter 
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
	'hidden_name' => getVar('hidden_name'),
	'name' => getVar('name'),
	'prio' => getVar('prio'),
	'schiff' => getVar('schiff'),
	'edit' => getVar('edit'),
	'delete' => getVar('delete'),
);
	
debug_var("params", $params);

// Timestamp
$heute = getdate();

// Daten löschen
if (isset($params['delete']) && $params['delete'] != '') {
	$sql = "DELETE FROM " . $db_tb_bestellung_projekt . " WHERE name='" . $params['delete'] ."'";
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
		'name' => getVar('name'),
		'prio' => getVar('prio'),
		'schiff' => getVar('schiff'),
	));
} else {
	debug_var("edit", $edit = array(
		'name' => getVar('name'),
		'prio' => getVar('prio'),
		'schiff' => getVar('schiff'),
	));
}

if (empty($params['schiff'])) {
	$params['schiff']=0;	
}

// Edit-Daten modifizieren
if (!empty($button_edit)) {
	echo $params['hidden_name'];
	$sql = "UPDATE " . $db_tb_bestellung_projekt . " SET ";
	$sql .= "name='". $params['name'] ."', prio='". $params['prio'] ."', schiff=" . $params['schiff'];
	$sql .= " WHERE name='" . $params['hidden_name'] ."'";
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$results[] = "<div class='system_notification'>Datensatz aktualisiert.</div><br>";	
}

// Edit-Daten hinzufügen
if (!empty($button_add)) {
	$sql = "INSERT INTO " . $db_tb_bestellung_projekt . " (";
	$sql .= "name,prio,schiff";
	$sql .= ") VALUES (";
	$sql .= "'" . $params['name'] . "','" . $params['prio'] ."'," . $params['schiff'];
	$sql .= ");";
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$results[] = "<div class='system_notification'>Datensatz hinzugefuegt.</div><br>";	
}

// Edit-Daten abfragen

if (empty($button_edit) && empty($button_add) && $params['edit']) {
	$sql = "SELECT * FROM " . $db_tb_bestellung_projekt . " WHERE name='" . $params['edit'] ."'";
	debug_var('sql', $sql);
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	if ($row = $db->db_fetch_array($result))
		foreach ($row as $name => $value)
			$edit[$name] = $value;
			
}

// Ansichten definieren
$views = array(
	'projekte' => array(
		'title' => 'Projekte',
		'columns' => array(
			'name' => 'Projektname',
			'prio' => 'Priorität',
			'schiff' => 'Schiffbau',
		),
		'key' => 'name',
		'edit' => array(
			'name' => array(
				'title' => 'Projektname',
				'desc' => 'Wie heißt das Projekt?',
				'type' => 'text',
				'values' => '',
				'value' => $edit['name'],
			),
			'prio' => array(
				'title' => 'Priorität',
				'desc' => 'Welche Priorität soll es haben [0-999]?',
				'type' => 'text',
				'values' => '',
				'value' => $edit['prio'],
			),
			'schiff' => array(
				'title' => 'Schiffbau',
				'desc' => 'Ist dies ein Schiffbau-Projekt?',
				'type' => 'checkbox',
				'values' => '',
				'value' => $edit['schiff'],
			),
			'hidden_name' => array(
				'title' => '',
				'desc' => '',
				'type' => 'hidden',
				'values' => '',
				'value' => $edit['name'],
			),
		),		
	),
);

// Daten abfragen
$data = array();
$sql = "SELECT * FROM " . $db_tb_bestellung_projekt . " ORDER BY schiff,prio";
debug_var("sql", $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	$data[] = array(
		'name' => $row['name'],
		'prio' => $row['prio'],
		'schiff' => ($row['schiff'] ? 'Ja' : 'Nein'),
	);
}

// Aktuelle Ansicht auswählen
$view = $views['projekte'];

// Titelzeile ausgeben
echo "<div class='doc_title'>Projekteverwaltung</div><br>\n";

// Daten ausgeben
start_table();
start_row("titlebg", "nowrap valign=top");

foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
	next_cell("titlebg", "nowrap valign=top");
	$orderkey = $viewcolumnkey;
	if (isset($view['sortcolumns'][$orderkey]))
		$orderkey = $view['sortcolumns'][$orderkey];
	echo '<b>' . $viewcolumnname . '</b>';
}
if (isset($view['edit'])) {
	next_cell("titlebg", 'nowrap valign=top');
	echo '&nbsp;';
}
foreach ($data as $row) {
	$key = $row[$view['key']];
	next_row('windowbg1', 'nowrap valign=top style="background-color: white;"');
	foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
		next_cell("windowbg1", 'nowrap valign=top style="background-color: white;"');
		echo $row[$viewcolumnkey];
	}
	// Editbuttons ausgeben
	if (isset($view['edit'])) {
		next_cell("windowbg1", 'nowrap valign=top');
		if ((!isset($row['allow_edit']) || $row['allow_edit']) && $key!="(Keins)")
			echo makelink(
				array('edit' => $key),
				"<img src=\"bilder/file_edit_s.gif\" border=\"0\" alt=\"bearbeiten\">"
			);
		if ((!isset($row['allow_delete']) || $row['can_delete']) && $key!="(Keins)")
			echo makelink(
				array('delete' => $key),
				"<img src=\"bilder/file_delete_s.gif\" border=\"0\" onclick=\"return confirmlink(this, 'Datensatz wirklich löschen?')\" alt=\"löschen\">"
			);
	}
}
end_table();

// Maske ausgeben
echo '<br>';
echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p>' . "\n";
start_table();
next_row("titlebg", 'nowrap valign=top colspan=2');
echo "<b>Projekt";
if (isset($params['edit']) && $params['edit']) {
	echo " bearbeiten/hinzufügen";
	echo '<input type="hidden" name="edit" value="' . $params['edit'] . '">' . "\n";
	// echo '<input type="hidden" name="list_team" value="'.$list_team.'" />' . "\n";
} else
	echo " hinzufügen";
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
		echo "";
}

next_row('titlebg', 'align=center colspan=2');
if (isset($params['edit']) && $params['edit'])
	echo '<input type="submit" value="speichern" name="button_edit" class="submit"> ';
echo '<input type="submit" value="hinzufügen" name="button_add" class="submit">';
end_table();
echo '</form>';


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
	case 'hidden':
		$html = '<input type="hidden" name="' . $key . '" value="' . $field['value'] . '"';
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