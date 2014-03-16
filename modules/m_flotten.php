<?php
/*****************************************************************************
 * m_flotten.php                                                             *
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
 * Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  *
 * IW-Spielern.                                                              *
 *                                                                           *
 * Autor: Patsch                                                             *
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

if (!defined('DEBUG_LEVEL'))
	define('DEBUG_LEVEL', 0);

include_once('./includes/debug.php');

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
$modulname  = "m_flotten";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Flotten";

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
$moduldesc = "Berechnet Flottenanzahl eines Gegners.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    //nothing here
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modulstatus;

    $menu             = getVar('menu');
    $submenu          = getVar('submenu');
    $menuetitel       = "Flotten";
    $actionparameters = "";

    insertMenuItem($menu, $submenu, $menuetitel, $modulstatus, $actionparameters);
    //
    // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
    //
    // 	insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
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
function workUninstallDatabase()
{
    //nothing here
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
//
// Hauptprogramm

$results = array();

// Seitenparameter definieren
debug_var("defaults", $defaults = array());

// Seitenparameter ermitteln
foreach ($defaults as $key => $value)
	$params[$key] = getVar($key);

// Seitenparameter validieren

// Stammdaten abfragen
$config = array();

// Titelzeile ausgeben
doc_title($modultitle);

// Ergebnisse ausgeben
foreach ($results as $result)
	echo($result);

// Schiffstypen abfragen
$sql = "SELECT * FROM " . $db_tb_schiffstyp;
$result = $db->db_query($sql);
while ($row = $db->db_fetch_array($result))
	$config['schiffstyp'][$row['id_iw']] = $row;

// Schiffe die angezeigt werden sollen
$config['schiffe'] = array(
	'9' => array('abk' => 'S', 'name' => 'Systrans'),
	'15' => array('abk' => 'G', 'name' => 'Gorgol'),
	'59' => array('abk' => 'K', 'name' => 'Kamel'),
	'11' => array('abk' => 'L', 'name' => 'Lurch'),
	'17' => array('abk' => 'E', 'name' => 'Eisbär'),
	'60' => array('abk' => 'W', 'name' => 'Waschbär'),
	'31' => array('abk' => 'M', 'name' => 'Manta'),
	'34' => array('abk' => 'S', 'name' => 'Stormbringer'),
	'49' => array('abk' => 'K', 'name' => 'Kronk'),
	'51' => array('abk' => 'Z', 'name' => 'Zeus'),
);
$config['schiffe_cat'] = array(
	'Transen 1' => array('9','15','59'),
	'Transen 2' => array('11','17','60'),
	'Evo 5' => array('31','34','49','51'),
);

// Allianzen im Status 'Krieg' abfragen
$sql = "SELECT * FROM " . $db_tb_allianzstatus .
	" WHERE status='Krieg'";
		


//if ($user_status <> 'admin')
	$sql .= " AND name='" . $user_allianz . "'";



$result = $db->db_query($sql);
while ($row = $db->db_fetch_array($result))
	$config['allistatus_krieg'][$row['allianz']] = $row['allianz'];

// Angriffe mit Flotten abfragen
if (isset($config['allistatus_krieg'])) {
	debug_var("sql", $sql = "SELECT " .
		$db_tb_kb_flotten . ".*," .
		$db_tb_kb . ".*" .
		" FROM " . $db_tb_kb_flotten .
		" INNER JOIN " . $db_tb_kb . " ON " . $db_tb_kb . ".ID_KB=" . $db_tb_kb_flotten . ".ID_KB" .
		" WHERE (" . $db_tb_kb_flotten . ".ally IN ('" . implode("','", $config['allistatus_krieg']) . "') AND " . $db_tb_kb . ".time>'1385170574')" .
		//" WHERE " . $db_tb_kb_flotten . ".ally IN ('" . implode("','", $config['allistatus_krieg']) . "')" .
		" ORDER BY " . $db_tb_kb . ".time DESC");
	$result = $db->db_query($sql);
	while ($row = $db->db_fetch_array($result)) {
		$gegner = array();
		if (!empty($row['verteidiger']) && $row['name'] != $row['verteidiger'])
			$gegner[] = array('ally' => $row['verteidiger_ally'], 'name' => $row['verteidiger']);
		debug_var("sqlinner", $sqlinner = "SELECT " .
			$db_tb_kb_flotten . ".*" .
			" FROM " . $db_tb_kb_flotten .
			" WHERE " . $db_tb_kb_flotten . ".ID_KB=" . $row['ID_KB'] .
			" AND " . $db_tb_kb_flotten . ".name<>'" . $row['name'] . "'" .
			" AND " . $db_tb_kb_flotten . ".name<>'" . $row['verteidiger'] . "'");
		$resultinner = $db->db_query($sqlinner);
		while ($rowinner = $db->db_fetch_array($resultinner))
			$gegner[] = array('ally' => $rowinner['ally'], 'name' => $rowinner['name']);		
		$schiffe = array();
		debug_var("sqlinner", $sqlinner = "SELECT " .
			$db_tb_kb_flotten_schiffe . ".*," .
			$db_tb_kb_flotten_schiffe . ".anz_start-" . $db_tb_kb_flotten_schiffe . ".anz_verlust as anz_verbleibend" .
			" FROM " . $db_tb_kb_flotten_schiffe .
			" WHERE " . $db_tb_kb_flotten_schiffe . ".ID_FLOTTE=" . $row['ID_FLOTTE'] .
			"   AND " . $db_tb_kb_flotten_schiffe . ".anz_start-" . $db_tb_kb_flotten_schiffe . ".anz_verlust>0");
		$resultinner = $db->db_query($sqlinner);
		while ($rowinner = $db->db_fetch_array($resultinner))
			if (isset($config['schiffe'][$rowinner['ID_IW_SCHIFF']]))
				$schiffe[$rowinner['ID_IW_SCHIFF']] = $rowinner;
			else if (isset($config['schiffstyp'][$rowinner['ID_IW_SCHIFF']]))
				$schiffe['sonstige'][] = $rowinner;
		if (count($schiffe) > 0)
			$data['flotten_krieg'][] = array_merge(array(
					'url' => 'http://www.icewars.de/portal/kb/de/kb.php?id=' . $row['ID_KB'] . '&md_hash=' . $row['hash'],
					'gegner' => $gegner,
					'schiffe' => $schiffe,
				), $row);
	}
}

start_table();
start_row("titlebg", "colspan=\"4\"");
echo "<b>Verbleibende Flotten</b>";
foreach ($config['schiffe_cat'] as $caption => $cat)
{
	next_cell("titlebg", "colspan=\"" . count($cat) . "\"");
	echo "<b>$caption</b>";
}
next_cell("titlebg", "nowrap");
echo "<b>Sonstige</b>";
next_cell("titlebg", "nowrap");
echo "<b>KB</b>";
next_row("windowbg2", "nowrap");
echo "Zeit";
next_cell("windowbg2", "nowrap");
echo "Name";
next_cell("windowbg2", "nowrap");
echo "Gegner";
next_cell("windowbg2", "nowrap");
echo "Planet";
foreach ($config['schiffe_cat'] as $cat)
	foreach ($cat as $id) {
		next_cell("windowbg2", "nowrap align=\"center\"");
		echo "<span title=\"" . $config['schiffe'][$id]['name'] . "\">";
		echo $config['schiffe'][$id]['abk'];
		echo "</span>";
	}
next_cell("windowbg2", "nowrap");
echo "&nbsp;";
next_cell("windowbg2", "nowrap");
echo "&nbsp;";
if (isset($data['flotten_krieg'])) {
	foreach ($data['flotten_krieg'] as $att) {
		next_row("windowbg1", "nowrap valign=\"top\"");
		echo makeduration($att['time']);
		next_cell("windowbg1", "nowrap valign=\"top\"");
		if (!empty($att['ally']))
			echo "[" . $att['ally'] . "]";
		echo $att['name'];
		next_cell("windowbg1", "nowrap valign=\"top\"");
//		if (empty($att['verteidiger']) || $att['verteidiger'] == $att['name'])
//		{
			if (count($att['gegner']) > 0)
				foreach ($att['gegner'] as $gegner)
				{
					if (!empty($gegner['ally']))
						echo "[" . $gegner['ally'] . "]";
					echo $gegner['name'];
					echo "<br>";
				}
//		}	
//		else
//		{
//			if (!empty($att['verteidiger_ally']))
//				echo "[" . $att['verteidiger_ally'] . "]";
//			echo $att['verteidiger'];
//		}
		next_cell("windowbg1", "nowrap valign=\"top\"");
		echo $att["koords_gal"] . ":" . $att["koords_sol"] . ":" . $att["koords_pla"];
		foreach ($config['schiffe_cat'] as $cat)
			foreach ($cat as $id) {
				next_cell("windowbg1", "nowrap valign=\"top\" align=\"right\"");
				if (empty($att['schiffe'][$id]))
					echo "&nbsp;";
				else {
					$title = $att['schiffe'][$id]['anz_verbleibend'] . "/" . $att['schiffe'][$id]['anz_start'] . " " . $config['schiffe'][$id]['name'];
					echo "<span title=\"" . $title . "\">";
					echo number_format($att['schiffe'][$id]['anz_verbleibend'], 0, ",", ".");
					echo "</span>";
				}
			}
		next_cell("windowbg1", "valign=\"top\"");
		if (isset($att['schiffe']['sonstige']))
			for ($index = 0; $index < count($att['schiffe']['sonstige']); $index++) {
				$schiffe = $att['schiffe']['sonstige'][$index];
				if (!empty($config['schiffstyp'][$schiffe['ID_IW_SCHIFF']]))
				{
					$schiffstyp = $config['schiffstyp'][$schiffe['ID_IW_SCHIFF']];
					if ($index)
						echo " ";
					echo number_format($schiffe['anz_verbleibend'], 0, ",", ".") . " " .$schiffstyp['abk'];
				}
			}
		next_cell("windowbg1", "nowrap valign=\"top\"");
		echo "<a href=\"" . $att["url"] . "\" target=\"" . $modultitle . "_kb\">KB</a>";
	}
}
end_table();

//****************************************************************************
//
// Dauer formatieren
function makeduration($time) {
	if (empty($time))
		return '---';
	$duration = time() - $time;
	if ($duration > 2 * 24 * 60 * 60)
		return round($duration / (24 * 60 * 60)) . "d";
	elseif ($duration > 60 * 60) {
		$hours = round($duration / (60 * 60));
		return $hours == 1 ? "1 Stunde" : $hours . "h";
	} else {
		$minutes = round($duration / 60);
		return $minutes == 1 ? "1 Minute" : $minutes . "min";
	}
}