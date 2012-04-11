<?php
/*****************************************************************************/
/* m_default.php                                                             */
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
/* Dieses Modul dient als Vorlage zum Erstellen von eigenen Zusatzmodulen    */
/* für die Iw DB: Icewars geoscan and sitter database                        */
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
$modulname  = "m_colors";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Farbtabelle";

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
$moduldesc =
  "Das Colors-Modul bietet eine Anzeige s&auml;mtlicher in Icewars f&uuml;r " . 
  "die farbige Markierung von Links wie den Planetennamen relevanten " . 
  "Hexadezimal-Farbcodes aus der man diese einfach rauskopieren kann.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//
function workInstallDatabase() {
//	global $db, $db_tb_user;
//
//  $sql ="ALTER TABLE `" . $db_tb_user . "`" .
//	  " ADD `notice` text NOT NULL AFTER `titel`;";
//
//  $result = $db->db_query($sql)
//	  or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

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
  return "";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//
function workUninstallDatabase() {
//	global $db, $db_tb_user;
//
//  $sql ="ALTER TABLE `" . $db_tb_user . "`" .
//	  " DROP COLUMN `notice`;";
//
//  $result = $db->db_query($sql)
//    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    echo "<div class='system_notification'>Deinstallation: Datenbank&auml;nderungen = <b>OK</b></div>";
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
// -> Und hier beginnt das eigentliche Modul


$limit = getVar('limit');
if( empty($limit) ) { 
  $limit = 20;
}

echo "<div class='doc_big_black' style='text-align:center'>Nachfolgend alle Farbcodes, " .
     "die Ihr f&uuml;r die Ordnung Eurer Links<br>und Planis ingame verwenden " .
     "k&ouml;nnt.</div>\n";
echo "<br>\n";
echo "Einfach kopieren und an der gew&uuml;schten Stelle z.B. bei der ".
     "Benennung von Planetennamen einfügen, das wars!<br><br>\n";
echo "\n";
echo "<SCRIPT LANGUAGE=\"JavaScript\">\n";
echo "clr=new Array('00','20','40','60','80','a0','c0','ff');\n";
echo "for (i=0;i<8;i++) {\n";
echo "document.write(\"<table border=1 cellpadding=8>\");\n";
echo "for (j=0;j<8;j++) {\n";
echo "document.write(\"<tr>\");\n";
echo "for (k=0;k<8;k++) {\n";
echo "document.write('<td bgcolor=\"#'+clr[i]+clr[j]+clr[k]+'\">');\n";
echo "document.write('<tt><font color=\"#'+clr[7-i]+clr[7-j]+clr[7-k]+'\">#');\n";
echo "document.write(clr[i]+clr[j]+clr[k]+' </font></tt></td>'); }\n";
echo "document.write(\"</tr>\"); }\n";
echo "document.write(\"</table><br>\"); }\n";
echo "// end -->\n";
echo "</script>\n";
echo "\n";
echo "<!-- Script-Ende -->\n";

?>