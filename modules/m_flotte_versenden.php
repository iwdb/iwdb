<?php
/*****************************************************************************/
/* m_flotte_versenden.php                                                    */
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
/* Flotte versenden                                                          */
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

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig fuer die Benennung der zugehÃÂ¶rigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung fuer 
//    eine Installation ueber das Menue
//
$modulname  = "m_flotte_versenden";

//****************************************************************************
//
// -> Menuetitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Flotte versenden";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul ueber die Navigation 
//    ausfuehren darf. Moegliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "admin";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc = "Flotten versenden aus dem Ziel-/Bestell-/Lagersystem.";

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
function workInstallMenu() {
    global $modultitle, $modulstatus, $_POST;

    $actionparameters = "";
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
function workUninstallDatabase()
{
    //nothing here
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

  require_once './includes/menu_fn.php';

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

$content = "http://icewars.de";

// Seitenparameter festlegen
$defaults = array(
	"time" => "",
	"art" => "",
	"pos" => 1
);

// Seitenparameter ermitteln
foreach ($defaults as $key => $default) {
	$value = getVar($key);
	$params[$key] = empty($value) ? $default : $value;
}

// Frame-Hoehe festlegen
if ($params["art"] == "bestellung")
	$top_height = 80;
else
	$top_height = 40;

// Welche Sicht?
switch (getVar("view")) {

// Oberes Menue
case "top":
	echo '<html>';
	echo '<body style="color: #ffffff">';
	echo '<body bgcolor="#111111">';
	
	// Auftragsliste abrufen
    $auftraege = array();
    $sql = "SELECT * FROM $db_tb_versand_auftrag WHERE user='" . $user_sitterlogin . "' AND time=" . $params['time'];
	debug_var("sql", $sql);
    $result = $db->db_query($sql);
	while ($row = $db->db_fetch_array($result)) {
		$auftraege[$row['pos']] = array(
			"pos" => $row['pos'],
			"reference" => $row['reference'],
			"art" => $row['art'],
		);
	}
	foreach ($auftraege as $pos => $auftrag) {
		// Schiff-Bestellungen lesen 
		if ($auftrag['art'] == "bestellung_schiffe") {
			debug_var("sql", $sql = "SELECT *,
				(SELECT allianz FROM $db_tb_scans WHERE $db_tb_scans.`coords_gal`=$db_tb_bestellung_schiffe.`coords_gal` AND
                             $db_tb_scans.`coords_sys`=$db_tb_bestellung_schiffe.`coords_sys` AND
			        $db_tb_scans.`coords_planet`=$db_tb_bestellung_schiffe.`coords_planet`) AS allianz,
				(SELECT planetenname FROM $db_tb_scans WHERE $db_tb_scans.`coords_gal`=$db_tb_bestellung_schiffe.`coords_gal` AND
                             $db_tb_scans.`coords_sys`=$db_tb_bestellung_schiffe.`coords_sys` AND
			        $db_tb_scans.`coords_planet`=$db_tb_bestellung_schiffe.`coords_planet`) AS planet
				FROM $db_tb_bestellung_schiffe WHERE id=" . $auftrag['reference']);
			$result = $db->db_query($sql);
			if ($row = $db->db_fetch_array($result)) {
				$auftraege[$pos] = array_merge($auftraege[$pos], array(
					"coords_gal" => $row['coords_gal'],
					"coords_sys" => $row['coords_sys'],
					"coords_planet" => $row['coords_planet'],
					"user" => $row['user'],
					"planet" => $row['planet'],
					"allianz" => $row['allianz'],
					"pos" => array(),
				));
				debug_var("sql_pos", $sql_pos = "SELECT $db_tb_bestellung_schiffe_pos.*,
					$db_tb_schiffstyp.`schiff`,
					$db_tb_schiffstyp.`abk`,
					$db_tb_schiffstyp.`id_iw` 
					FROM $db_tb_bestellung_schiffe_pos,
					     $db_tb_schiffstyp
					WHERE $db_tb_bestellung_schiffe_pos.`bestellung_id`=" . $row["id"] . "
					  AND $db_tb_bestellung_schiffe_pos.`schiffstyp_id`=$db_tb_schiffstyp.`id`");
				$result_pos = $db->db_query($sql_pos);
				while ($row_pos = $db->db_fetch_array($result_pos)) {
					$auftraege[$pos]['pos'][] = array(
						"name" => empty($row_pos['abk']) ? $row_pos['schiff'] : $row_pos['abk'],
						"menge" => $row_pos['offen'],
						"id_iw" => $row_pos['id_iw'],
					);
				}
			}
		// Ressourcen-Bestellungen lesen
		} else if ($auftrag['art'] == "bestellung") {
			debug_var("sql", $sql = "SELECT *,
				(SELECT allianz FROM $db_tb_scans WHERE $db_tb_scans.`coords_gal`=$db_tb_bestellung.`coords_gal` AND
                             $db_tb_scans.`coords_sys`=$db_tb_bestellung.`coords_sys` AND
			        $db_tb_scans.`coords_planet`=$db_tb_bestellung.`coords_planet`) AS allianz,
				(SELECT planetenname FROM $db_tb_scans WHERE $db_tb_scans.`coords_gal`=$db_tb_bestellung.`coords_gal` AND
                             $db_tb_scans.`coords_sys`=$db_tb_bestellung.`coords_sys` AND
			        $db_tb_scans.`coords_planet`=$db_tb_bestellung.`coords_planet`) AS planet
				FROM $db_tb_bestellung WHERE id=" . $auftrag['reference']);
			$result = $db->db_query($sql);
			if ($row = $db->db_fetch_array($result)) {
				$auftraege[$pos] = array_merge($auftraege[$pos], array(
					"coords_gal" => $row['coords_gal'],
					"coords_sys" => $row['coords_sys'],
					"coords_planet" => $row['coords_planet'],
					"user" => $row['user'],
					"planet" => $row['planet'],
					"allianz" => $row['allianz'],
					"eisen" => $row['offen_eisen'],
					"stahl" => $row['offen_stahl'],
					"vv4a" => $row['offen_vv4a'],
					"chemie" => $row['offen_chemie'],
					"eis" => $row['offen_eis'],
					"wasser" => $row['offen_wasser'],
					"energie" => $row['offen_energie'],
				));
			}
		}
	}

	// Ausgabe
	echo "<table width=100%>";
	//start_table(100);
	start_row("", "width=\"20%\" nowrap");

	// Vorheriger Auftrag
	if ($params['pos'] > 1) {
		$auftrag = $auftraege[$params['pos'] - 1];
		echo "<a href=\"" . url($modulname, array("time" => $params['time'], "pos" => 1, "nobody" => 1, "art" => $params['art'])) . "\" target=\"_top\" style=\"color: #ffffff; text-decoration: underline\">"; 
		echo "&lt;|";
		echo "</a> ";
		echo "<a href=\"" . url($modulname, array("time" => $params['time'], "pos" => $params['pos'] - 1, "nobody" => 1, "art" => $params['art'])) . "\" target=\"_top\" style=\"color: #ffffff; text-decoration: underline\">"; 
		echo "&lt;&lt;";
		echo $auftrag['coords_gal'] . ":" . $auftrag['coords_sys'] . ":" . $auftrag['coords_planet'];
		echo " ";
		if (!empty($auftrag['allianz'])) {
			echo "[";
			echo $auftrag['allianz'];
			echo "] ";
		}
		echo $auftrag['user'];
		echo "</a> ";
	}

	// Aktueller Auftrag
	next_cell("", "width=60% nowrap");
	if (isset($auftraege[$params['pos']])) {
		$auftrag = $auftraege[$params['pos']];
		echo "<a href=\"http://sandkasten.icewars.de/game/index.php?action=flotten_send&gal=" . $auftrag['coords_gal'] . "&sol=" . $auftrag['coords_sys'] . "&pla=" . $auftrag['coords_planet'] . "\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">";
		echo $auftrag['coords_gal'] . ":" . $auftrag['coords_sys'] . ":" . $auftrag['coords_planet'];
		echo " (" . $auftrag['planet'] . ")";
		echo "</a> ";
		if (!empty($auftrag['allianz'])) {
			echo "[";
			echo "<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=alli&tag=" . $auftrag['allianz'] . "\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">";
			echo $auftrag['allianz'];
			echo "</a>";
			echo "] ";
		}
		echo "<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=user&name=" . $auftrag['user'] . "\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">";
		echo $auftrag['user'];
		echo "</a> ";
	} else {
		echo "Kein Auftrag";
	}

	// Nächster Auftrag
	next_cell("", "width=\"20%\" align=\"right\" nowrap");
	if ($params['pos'] < count($auftraege)) {
		$auftrag = $auftraege[$params['pos'] + 1];
		echo "<a href=\"" . url($modulname, array("time" => $params['time'], "pos" => $params['pos'] + 1, "nobody" => 1, "art" => $params['art'])) . "\" target=\"_top\" style=\"color: #ffffff; text-decoration: underline;\">"; 
		echo $auftrag['coords_gal'] . ":" . $auftrag['coords_sys'] . ":" . $auftrag['coords_planet'];
		echo " ";
		if (!empty($auftrag['allianz'])) {
			echo "[";
			echo $auftrag['allianz'];
			echo "] ";
		}
		echo $auftrag['user'];
		echo " &gt;&gt;</a> ";
		echo "<a href=\"" . url($modulname, array("time" => $params['time'], "pos" => count($auftraege), "nobody" => 1, "art" => $params['art'])) . "\" target=\"_top\" style=\"color: #ffffff; text-decoration: underline\">"; 
		echo "&gt;|";
		echo "</a>";
	}
	next_cell();
	echo "<a href=\"" . url("newscan") . "\" target=\"_top\" style=\"color: #ffffff; text-decoration: underline\">X</a>";

	// 2. Zeile
	next_row();
	next_cell();

	// Schiffs-Bestellung
	if ($params['art'] == "bestellung_schiffe") {
		if (isset($auftraege[$params['pos']])) {
			$auftrag = $auftraege[$params['pos']];
			if (isset($auftrag['pos']) && is_array($auftrag['pos'])) {
				foreach ($auftrag['pos'] as $pos) {
					echo $pos['menge'];
					echo " ";
					if (!empty($pos['id_iw']))
						echo "<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=schiff&id=" . $pos['id_iw'] . "\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">";
					echo $pos['name'];
					if (!empty($pos['id_iw']))
						echo "</a>";
					echo " ";
				}
			}
		}
	} else if ($params['art'] == "bestellung") {
		if (isset($auftraege[$params['pos']])) {
			$auftrag = $auftraege[$params['pos']];
			echo "<form name=\"ress\">";
			start_table(0, 0, 0, 0, "");
			start_row("", "nowrap");
			echo "<img src=\"bilder/eisen.png\">";
			echo " Eisen ";
			next_cell("", "nowrap");
			echo "<input name=\"eisen_active\" type=\"checkbox\"";
			if (!empty($auftrag['eisen']))
				echo " checked>";
			else
				echo ">";
			echo "<input name=\"eisen\" type=\"text\" value=\"" . $auftrag['eisen'] . "\" size=\"8\">";
			next_cell("", "nowrap");
			echo "&nbsp;<img src=\"bilder/stahl.png\">";
			echo " Stahl ";
			next_cell("", "nowrap");
			echo "<input name=\"stahl_active\" type=\"checkbox\"";
			if (!empty($auftrag['stahl']))
				echo " checked>";
			else
				echo ">";
			echo "<input name=\"stahl\" type=\"text\" value=\"" . $auftrag['stahl'] . "\" size=\"8\">";
			next_cell("", "nowrap");
			echo "&nbsp;<img src=\"bilder/vv4a.png\">";
			echo " VV4A ";
			next_cell("", "nowrap");
			echo "<input name=\"vv4a_active\" type=\"checkbox\"";
			if (!empty($auftrag['vv4a']))
				echo " checked>";
			else
				echo ">";
			echo "<input name=\"vv4a\" type=\"text\" value=\"" . $auftrag['vv4a'] . "\" size=\"8\">";
			next_cell("", "nowrap");
			echo "&nbsp;<img src=\"bilder/chemie.png\">";
			echo " Chemie ";
			next_cell("", "nowrap");
			echo "<input name=\"chemie_active\" type=\"checkbox\"";
			if (!empty($auftrag['chemie']))
				echo " checked>";
			else
				echo ">";
			echo "<input name=\"chemie\" type=\"text\" value=\"" . $auftrag['chemie'] . "\" size=\"8\">";
			
			next_cell("", "nowrap");
			echo "&nbsp;<span id=\"systrans\"></span> ";
			next_cell("", "nowrap");
			echo "&nbsp;<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=schiff&id=9\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">Systrans</a>";
			next_cell("", "nowrap");
			echo "&nbsp;<span id=\"gorgol\"></span> ";
			next_cell("", "nowrap");
			echo "&nbsp;<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=schiff&id=15\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">Gorgol</a>";
			next_cell("", "nowrap");
			echo "&nbsp;<span id=\"kamel\"></span> ";
			next_cell("", "nowrap");
			echo "&nbsp;<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=schiff&id=59\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">Kamel</a>";
			next_cell("", "nowrap");
			echo "&nbsp;<span id=\"flughund\"></span> ";
			next_cell("", "nowrap");
			echo "&nbsp;<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=schiff&id=70\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">Flughund</a>";
			end_row();

			start_row("", "nowrap");
			echo "<img src=\"bilder/eis.png\">";
			echo " Eis ";
			next_cell("", "nowrap");
			echo "<input name=\"eis_active\" type=\"checkbox\"";
			if (!empty($auftrag['eis']))
				echo " checked>";
			else
				echo ">";
			echo "<input name=\"eis\" type=\"text\" value=\"" . $auftrag['eis'] . "\" size=\"8\">";
			next_cell("", "nowrap");
			echo "&nbsp;<img src=\"bilder/wasser.png\">";
			echo " Wasser ";
			next_cell("", "nowrap");
			echo "<input name=\"wasser_active\" type=\"checkbox\"";
			if (!empty($auftrag['wasser']))
				echo " checked>";
			else
				echo ">";
			echo "<input name=\"wasser\" type=\"text\" value=\"" . $auftrag['wasser'] . "\" size=\"8\">";			
			next_cell("", "nowrap");
			echo "&nbsp;<img src=\"bilder/energie.png\">";
			echo " Energie ";
			next_cell("", "nowrap");
			echo "<input name=\"energie_active\" type=\"checkbox\"";
			if (!empty($auftrag['energie']))
				echo " checked>";
			else
				echo ">";
			echo "<input name=\"energie\" type=\"text\" value=\"" . $auftrag['energie'] . "\" size=\"8\">";			

			next_cell("", "nowrap");
			next_cell("", "nowrap");
			next_cell("", "nowrap");
			echo "<span id=\"lurch\"></span> ";
			next_cell("", "nowrap");
			echo "&nbsp;<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=schiff&id=11\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">Lurch</a>";
			next_cell("", "nowrap");
			echo "&nbsp;<span id=\"eisbaer\"></span> ";
			next_cell("", "nowrap");
			echo "&nbsp;<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=schiff&id=17\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">Eisb&auml;r</a>";
			next_cell("", "nowrap");
			echo "&nbsp;<span id=\"waschbaer\"></span> ";
			next_cell("", "nowrap");
			echo "&nbsp;<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=schiff&id=60\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">Waschb&auml;r</a>";
			next_cell("", "nowrap");
			echo "&nbsp;<span id=\"seepferdchen\"></span> ";
			next_cell("", "nowrap");
			echo "&nbsp;<a href=\"http://sandkasten.icewars.de/game/index.php?action=info&typ=schiff&id=71\" target=\"content\" style=\"color: #ffffff; text-decoration: underline\">Seepferdchen</a>";
			end_table();
			end_form();

			echo '
<script language="JavaScript" type="text/javascript"><!--
function calc() {
	var kapa1 = 0;
	var kapa2 = 0;
	if (document.ress.eisen_active.checked)
		kapa1 += document.ress.eisen.value;
	if (document.ress.stahl_active.checked)
		kapa1 += (document.ress.stahl.value * 2);
	if (document.ress.chemie_active.checked)
		kapa1 += (document.ress.chemie.value * 3);
	if (document.ress.vv4a_active.checked)
		kapa1 += (document.ress.vv4a.value * 4);
	if (document.ress.eis_active.checked)
		kapa2 += (document.ress.eis.value * 2);
	if (document.ress.wasser_active.checked)
		kapa2 += (document.ress.wasser.value * 2);
	if (document.ress.energie_active.checked)
		kapa2 += document.ress.energie.value;
	document.getElementById("systrans").innerHTML = Math.ceil(kapa1 / 5000);
	document.getElementById("gorgol").innerHTML = Math.ceil(kapa1 / 20000);
	document.getElementById("kamel").innerHTML = Math.ceil(kapa1 / 75000);
	document.getElementById("flughund").innerHTML = Math.ceil(kapa1 / 400000);
	document.getElementById("lurch").innerHTML = Math.ceil(kapa2 / 2000);
	document.getElementById("eisbaer").innerHTML = Math.ceil(kapa2 / 10000);
	document.getElementById("waschbaer").innerHTML = Math.ceil(kapa2 / 50000);
	document.getElementById("seepferdchen").innerHTML = Math.ceil(kapa2 / 250000);
	window.setTimeout("calc()", 250);
}
window.setTimeout("calc()", 250);
// --></script>';
		}
	}

	next_cell();
	end_table();

	echo "</body>";
	echo "</html>";
	break;

// Frameset
default:
	// Auftragsliste schreiben
	if (empty($params['time'])) {
		$params['time'] = time();
		$index = 0;
		do {
			debug_var("current", $current = getVar("mark_" . $index++));
			if (!empty($current)) {
				debug_var("sql", $sql = "INSERT INTO $db_tb_versand_auftrag (`user`,`time`,`pos`,`reference`,`art`) VALUES ('" . $user_sitterlogin . "'," . $params['time'] . "," . $index . "," . $current . ",'" . $params['art'] . "')");
				$db->db_query($sql);
			}
		} while (!empty($current));
	}
	// Aktuellen Auftrag ermitteln
	if (!empty($params['pos'])) {
		$sql = "SELECT * FROM $db_tb_versand_auftrag WHERE `user`='" . $user_sitterlogin .  "' AND time=" . $params['time'] . " AND pos=" . $params['pos'];
		$result = $db->db_query($sql);
		if ($row = $db->db_fetch_array($result)) {
			// Schiff-Bestellung lesen
			if ($params['art'] == "bestellung_schiffe") {
				$sql = "SELECT * FROM $db_tb_bestellung_schiffe WHERE `id`=" . $row['reference'];
				$result = $db->db_query($sql);
				if ($row = $db->db_fetch_array($result))
					// URL aufbauen
					$content = "http://sandkasten.icewars.de/game/index.php?action=flotten_send&gal=" . $row['coords_gal'] . "&sol=" . $row['coords_sys'] . "&pla=" . $row['coords_planet'];
			// Ressourcen-Bestellung lesen
			} else if ($params['art'] == "bestellung") {
				$sql = "SELECT * FROM $db_tb_bestellung WHERE `id`=" . $row['reference'];
				$result = $db->db_query($sql);
				if ($row = $db->db_fetch_array($result))
					// URL aufbauen
					$content = "http://sandkasten.icewars.de/game/index.php?action=flotten_send&gal=" . $row['coords_gal'] . "&sol=" . $row['coords_sys'] . "&pla=" . $row['coords_planet'];
			}
		}
	}
	// Frameset ausgeben
	echo "<frameset rows=\"" . $top_height . ",*\" frameborder=\"YES\" border=\"0\" framespacing=\"0\">";
	echo "<frame src=\"" . url($modulname, array("nobody" => 1, "view" => "top", "time" => $params['time'], "pos" => $params['pos'], "art" => $params['art'])) . "\" name=\"top\" scrolling=\"NO\" noresize>";
	echo "<frame src=\"" . $content . "\" name=\"content\" scrolling=\"YES\">";
	echo "</frameset>";
	echo "<noframes><body>Die Seite verwendet Frames.</body></noframes>";
}