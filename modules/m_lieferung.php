<?php
/*****************************************************************************/
/* m_lieferung.php                                                           */
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
/* Lieferuebersicht                                                          */
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
$modulname  = "m_lieferung";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Lieferung";

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
$moduldesc = "Zeigt Informationen zu anfliegenden Lieferungen an";

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

// Parameter ermitteln
$params = array(
	'view' => getVar('view'),
	'order' => getVar('order'),
	'orderd' => getVar('orderd'),
	'expand' => getVar('expand'),
	'filter_team' => getVar('filter_team'),
	'user_from' => getVar('user_from'),
	'user_to' => getVar('user_to'),
);

// Parameter validieren
if (empty($params['view']))
	$params['view'] = 'lieferung';
if (empty($params['order'])) 
	$params['order'] = 'time';
if ($params['orderd'] != 'asc' && $params['orderd'] != 'desc')
	$params['orderd'] = 'asc';
if (empty($params['filter_team']))
	$params['filter_team'] = $user_buddlerfrom;

debug_var("params", $params);

// Stammdaten abfragen
$config = array();

// Spieler und Teams abfragen
$users = array();
$users['(Alle)'] = '(Alle)';
$teams = array();
$teams['(Alle)'] = '(Alle)';
$sql = "SELECT * FROM " . $db_tb_user . " ORDER BY id";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	$users[$row['id']] = $row['id'];
	if (!empty($row['buddlerfrom']))
		$teams[$row['buddlerfrom']] = $row['buddlerfrom'];
}
$config['users'] = $users;
$config['teams'] = $teams;

// Abkürzungen fuer Schiffe abfragen
$sql = "SELECT * FROM $db_tb_schiffstyp";
debug_var("sql", $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
	$config['schiffstyp_abk'][$row['schiff']] = $row['abk'];

// Abfrage ausfuehren
$sql = "SELECT *,";
$sql .= " (SELECT planetenname FROM $db_tb_scans WHERE $db_tb_lieferung.coords_from_gal=$db_tb_scans.coords_gal AND $db_tb_lieferung.coords_from_sys=$db_tb_scans.coords_sys AND $db_tb_lieferung.coords_from_planet=$db_tb_scans.coords_planet) AS 'planet_from',";
$sql .= " (SELECT planetenname FROM $db_tb_scans WHERE $db_tb_lieferung.coords_to_gal=$db_tb_scans.coords_gal AND $db_tb_lieferung.coords_to_sys=$db_tb_scans.coords_sys AND $db_tb_lieferung.coords_to_planet=$db_tb_scans.coords_planet) AS 'planet_to'";
$sql .= " FROM $db_tb_lieferung";
$sql .= " WHERE time>UNIX_TIMESTAMP() AND user_to<>user_from";
if (isset($params['filter_team'])) {
	if ($params['filter_team'] != '(Alle)')
	{
		$sql .= " AND ((SELECT buddlerfrom FROM $db_tb_user WHERE $db_tb_user.id=$db_tb_lieferung.user_from)=";
		$sql .= "'" . $params['filter_team'] . "'";
		$sql .= " OR (SELECT buddlerfrom FROM $db_tb_user WHERE $db_tb_user.id=$db_tb_lieferung.user_to)=";
		$sql .= "'" . $params['filter_team'] . "'";
		$sql .= ")";
	}
}
if (isset($params['user_from']) && !empty($params['user_from']) && $params['user_from'] != '(Alle)')
{
	$sql .= " AND $db_tb_lieferung.user_from=";
	$sql .= "'" . $params['user_from'] . "'";
}
if (isset($params['user_to']) && !empty($params['user_to']) && $params['user_to'] != '(Alle)')
{
	$sql .= " AND $db_tb_lieferung.user_to=";
	$sql .= "'" . $params['user_to'] . "'";
}
debug_var("sql", $sql);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);

// Abfrage auswerten
$data = array();
while ($row = $db->db_fetch_array($result)) {
	// Daten
	$data[] = array_merge($row, array(
		"key" => $row['time'] . $row['coords_from_gal'] . ':' . $row['coords_from_sys'] . ':' . $row['coords_from_planet'] .
			  $row['coords_to_gal'] . ':' . $row['coords_to_sys'] . ':' . $row['coords_to_planet'],
		"coords_from" => $row['coords_from_gal'] . ':' . $row['coords_from_sys'] . ':' . $row['coords_from_planet'],
		"coords_to" => $row['coords_to_gal'] . ':' . $row['coords_to_sys'] . ':' . $row['coords_to_planet'],
		"eisen_style" => "text-align: right;",
		"stahl_style" => "text-align: right;",
		"vv4a_style" => "text-align: right;",
		"chem_style" => "text-align: right;",
		"eis_style" => "text-align: right;",
		"wasser_style" => "text-align: right;",
		"energie_style" => "text-align: right;",
	));
}

// Daten sortieren
usort($data, "sort_data_cmp");

// Ansichten definieren
$views = array(
	'lieferung' => array(
		'title' => 'Lieferungen',
		'headers' => array(
			'Lieferung' => 2,
			'Start' => 2,
			'Ziel' => 2,
			'Transport' => 8,
		),
		'columns' => array(
			'art' => 'Art',
			'time' => 'Zeit',
			'user_from' => 'Absender',
			'coords_from' => 'Start',
			'user_to' => 'Empfänger',
			'coords_to' => 'Ziel',
			'eisen' => 'Eisen',
			'stahl' => 'Stahl',
			'vv4a' => 'VV4A',
			'chem' => 'Chemie',
			'eis' => 'Eis',
			'wasser' => 'Wasser',
			'energie' => 'Energie',
			'schiffe' => 'Schiffe',
		),
		'sums' => array(
			'eisen' => 'Eisen',
			'stahl' => 'Stahl',
			'vv4a' => 'VV4A',
			'chem' => 'Chemie',
			'eis' => 'Eis',
			'wasser' => 'Wasser',
			'energie' => 'Energie',
		),
		'key' => 'key',
		'expand' => array(
			'title' => 'Planeten',
			'columns' => array(
				'coords' => 'Koords',
				'name' => 'Name',
				'eisen' => 'Eisen',
				'stahl' => 'Stahl',
				'vv4a' => 'VV4A',
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
echo ' Start: ';
echo makefield(array("type" => 'select', "values" => $config['users'], "value" => $params['user_from']), 'user_from');
echo ' Ziel: ';
echo makefield(array("type" => 'select', "values" => $config['users'], "value" => $params['user_to']), 'user_to');
echo ' <input type="submit" name="submit" value="anzeigen"/>';
echo "</form><br><br>\n";

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
next_row('windowbg2', 'colspan=' . (count($view['columns']) + 3));
echo "<b>Summe</b>";
next_row('windowbg1', 'nowrap valign=top style="background-color: white;"');
foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
	if (isset($row[$viewcolumnkey . '_style']))
		$style = $row[$viewcolumnkey . '_style'];
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

//****************************************************************************
//
// Formatiert die Datenwerte
function format_value($row, $key, $value, $expand = false) {
	global $config;

	if ($key == 'eisen' ||
	    $key == 'stahl' ||
	    $key == 'vv4a' ||
	    $key == 'chem' ||
	    $key == 'eis' ||
	    $key == 'wasser' ||
	    $key == 'energie')
	{
		return number_format($value, 0, "", ".");
	}
	else if ($key == 'time')
	{
		return strftime("%d.%m.%Y %H:%M", $value);
	}
	else if ($key == 'schiffe')
	{
		foreach ($config['schiffstyp_abk'] as $schiff => $abk)
			$value = str_replace($schiff, $abk, $value);
		return $row['art'] == 'Übergabe' ? $value : '';
	}
	else
		return $value;
}

//****************************************************************************
//
// Vergleichsfunktion fuer das Sortieren
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