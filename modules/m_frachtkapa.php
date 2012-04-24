<?php
/*****************************************************************************/
/* m_frachtkapa.php                                                          */
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
/* f�r die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspr�nglichen DB ist ein Gemeinschaftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens daf�r eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul �ber die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!"; 
	exit; 
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig f�r die Benennung der zugeh�rigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung f�r 
//    eine Installation �ber das Men�
//
$modulname  = "m_frachtkapa";

//****************************************************************************
//
// -> Men�titel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Frachtkapazitäten";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul �ber die Navigation 
//    ausf�hren darf. M�gliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = 
  "Das Frachtkapazitäten-Modul dient zur Berechnung der notwendigen" .
  " Transporteranzahl für eine gegebene Menge Ressourcen";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase() {
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
    echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgef�hrt wenn das Modul mit dem Parameter 
// "install" aufgerufen wurde. Beispiel des Aufrufs: 
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" nat�rlich deinen Server angeben und default 
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
  // ausgef�hrt werden. 
  return;
}

if (!@include("./config/".$modulname.".cfg.php")) { 
	die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

$ressies = array( 
  "eisen" => "Eisen", 
  "stahl" => "Stahl", 
  "vv4a" => "VV4A", 
  "chemie" => "Chemische Elemente",
  "eis" => "Eis",
  "wasser" => "Wasser",
  "energie" => "Energie"
);

foreach( $ressies as $key => $value) {
  $temp   = getVar($key);
  ${$key} = empty($temp) ? 0 : $temp;
}

$klasse1 = $eisen + (2 * $stahl) + (3 * $chemie) + (4 * $vv4a);
$klasse2 = $energie + (2 * $eis) + (2 * $wasser);

$class1ships = array(
  "Systrans(en)" =>  5000,
  "Gorgol(s)" =>    20000,
  "Kamel(e)" =>     75000,
  "Flughund(e)" => 400000
);

$class2ships = array(
  "Lurch(e)" =>            2000,
  "Eisbär(en)" =>    10000,
  "Waschbär(en)" =>  50000,
  "Seepferdchen" =>      250000
);

echo "<div class='doc_title'>Frachtkapazitätenberechnung</div>\n";
echo "<form method=\"POST\" action=\"index.php?action=" . $modulname .
     "&sid=" . $sid . "\" enctype=\"multipart/form-data\">\n";
     
echo " <table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 80%;\">\n";
echo "  <tr>\n";
echo "   <td colspan=\"2\" class=\"titlebg\"><b>Eingabe:</b></td>\n";
echo "  </tr>\n"; 

foreach( $ressies as $key => $title) {
  echo "  <tr>\n";
  echo "   <td class=\"windowbg2\" style=\"width: 200px;\">" . $title . ":</td>\n";
  echo "   <td class=\"windowbg1\"><input type=\"text\" size=\"17\" name=\"" . $key . 
       "\" value=\"" . ${$key} . "\"></td>\n";
  echo "  </tr>\n";
}

echo "  <tr>\n";
echo "   <td colspan=\"2\" class=\"windowbg2\" align=\"center\"><input type=\"submit\" style=\"width: 120px;\" value=\"Berechnen\"></td>\n";
echo "  </tr>\n";
echo " </table>\n";
echo "</form>\n";
echo "<br>\n";

echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 80%;\">\n";
echo " <tr>\n";
echo "  <td colspan=\"3\" class=\"titlebg\"><b>Zu transportierende Ressourcen</b></td>\n";
echo " </tr>\n"; 

foreach( $ressies as $key => $title) {
  echo " <tr>\n";
  echo "  <td class=\"windowbg2\" style=\"width: 200px;\">" . $title . ":</td>\n";
  echo "  <td class=\"windowbg1\" colspan=\"2\">" . ${$key} . "</td>\n";
  echo " </tr>\n";
}

echo " <tr>\n";
echo "  <td colspan=\"3\" class=\"titlebg\"><b>Benötigte Frachtkapazität</b></td>\n";
echo " </tr>\n"; 
echo " <tr>\n";
echo "  <td class=\"windowbg2\">Klasse 1:</td>\n";
echo "  <td class=\"windowbg1\" colspan=\"2\">" . $klasse1 . "</td>\n";
echo " </tr>\n"; 
echo " <tr>\n";
echo "  <td class=\"windowbg2\">Klasse 2:</td>\n";
echo "  <td class=\"windowbg1\" colspan=\"2\">" . $klasse2 . "</td>\n";
echo " </tr>\n"; 
echo " <tr>\n";
echo "  <td colspan=\"3\" class=\"titlebg\"><b>Benötigte Transen für Klasse 1</b></td>\n";
echo " </tr>\n"; 

$t1 = "Entweder";
foreach($class1ships as $name => $divisor) {
  echo " <tr>\n";
  echo "  <td class=\"windowbg2\">" . $t1 . "</td>\n";
  echo "  <td class=\"windowbg1\">" . ceil($klasse1 / $divisor) . "</td>\n";
  echo "  <td class=\"windowbg2\">" . $name . "</td>\n";  
  echo " </tr>\n";
  $t1 = "Oder";
} 

echo " <tr>\n";
echo "  <td colspan=\"3\" class=\"titlebg\"><b>Benötigte Transen für Klasse 2</b></td>\n";
echo " </tr>\n"; 

$t1 = "Entweder";
foreach($class2ships as $name => $divisor) {
  echo " <tr>\n";
  echo "  <td class=\"windowbg2\">" . $t1 . "</td>\n";
  echo "  <td class=\"windowbg1\">" . ceil($klasse2 / $divisor) . "</td>\n";
  echo "  <td class=\"windowbg2\">" . $name . "</td>\n";  
  echo " </tr>\n";
  $t1 = "Oder";
} 

echo "</table>\n";
?>