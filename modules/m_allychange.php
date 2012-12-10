<?php
/*****************************************************************************/
/* m_allychange.php                                                           */
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

//direktes Aufrufen verhindern
if (basename($_SERVER['PHP_SELF']) != "index.php") {header('HTTP/1.1 404 not found');exit;};
if (!defined('IRA')) {header('HTTP/1.1 404 not found');exit;};
    
//****************************************************************************
//
// -> Name des Moduls, ist notwendig fuer die Benennung der zugehuerigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung fuer 
//    eine Installation ueber das Menue
//
$modulname  = "m_allychange";

//****************************************************************************
//
// -> Menuetitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Allianzwechsler";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul ueber die Navigation 
//    ausfuehren darf. Muegliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "zeigt Allianzwechsel der Spieler";

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

  echo "<div class='system_notification'>Installation: Datenbank&auml;nderungen = <b>OK</b></div>";*/
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
	  // Weitere Wiederholungen fuer weitere Menue-Eintraege, z.B.
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

    echo "<div class='system_notification'>Deinstallation: Datenbank&auml;nderungen = <b>OK</b></div>";*/
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

  echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname . 
	     " ("  . $_REQUEST['was'] . ")</div>\n";

  if (!@include("./includes/menu_fn.php")) 
	  die( "Cannot load menu functions" );

  // Wenn ein Modul administriert wird, soll der Rest nicht mehr 
  // ausgefuehrt werden. 
  return;
}

//***************************hier gehts los***************************************
global $db, $db_prefix;

echo "<div class='doc_title'>Allianzwechsler</div><br>";
echo "<div>Hier kann man sehen, welche Spieler in letzter Zeit die Ally gewechselt haben:</div><br>";

//Daten von
$sql_updated= "SELECT MAX(`playerupdate_time`) AS updated FROM `".$db_prefix."spieler`;";
$result = $db->db_query($sql_updated) or error(GENERAL_ERROR,'Could not query player information.', '',__FILE__, __LINE__, $sql_updated);
$playerdata = $db->db_fetch_array($result);
$playerupdatetime = $playerdata['updated'];
if (empty($playerdata)) {
    exit('<div class="textsmall">keine Daten vorhanden</div>');
}
echo '<div class="textsmall">Daten von '.date("d.m.Y H:i",$playerupdatetime).'</div><br>';
?>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
    <tr>
        <th class='windowbg2'>Spieler</th>
        <th class='windowbg2'>von Allianz</th>
        <th class='windowbg2'>zu Allianz</th>
        <th class='windowbg2'>Zeitpunkt</th>
    </tr>

    <?php
    // letzten 50 Allywechsel abfragen
    $sql = "SELECT name, fromally, toally, time FROM `" . $db_prefix . "spielerallychange` ORDER BY `time` DESC LIMIT 0,50";
    
    $result = $db->db_query($sql)
    	or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);
    
    // Abfrage auswerten
    while ($row = $db->db_fetch_array($result)) {
    	echo "<tr>\n";
    	echo "<td class='windowbg1' style='text-align: center;'>";
    	echo "<a href='index.php?action=showgalaxy&amp;user=".urlencode($row['name'])."&amp;exact=1'>".$row['name']."</a>";
    	echo "</td>\n";
    	echo "<td class='windowbg1' style='text-align: center;'>";
        echo "<a href='index.php?action=m_allystats&allianz=".$row['fromally']."'>" . $row['fromally'] . "</a>";
    	echo "</td>\n";
    	echo "<td class='windowbg1' style='text-align: center;'>";
        echo "<a href='index.php?action=m_allystats&allianz=".$row['toally']."'>" . $row['toally'] . "</a>";
    	echo "</td>\n";
    	echo "<td class='windowbg1' style='text-align: center;'>";
    	echo date('d.m.Y H:i', $row['time']);
    	echo "</td>\n";
    	echo "</tr>\n";
    }
echo "</table>";
?>

