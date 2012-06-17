<?php
/*****************************************************************************/
/* m_techtree.php                                                             */
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
/* für die Iw DB: Icewars geoscan and sitter database  
 /*---------------------------------------------------------------------------*/
/* Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  
 /* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete  
 /* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (!defined('IRA'))
die('Hacking attempt...');

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
$modulname  = "m_techtree";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "graph. Techtree";

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
// -> Beschreibung des Moduls, wie es in der Menue-Übersicht angezeigt wird.
//
$moduldesc =
  "Ein graphsicher Technoligiebaum, der jede Evolutionsstufe als Übersicht mit Forschungsverknüpfungen und Informationen anzeigt. Besonders Wert gelegt wurde auf die optische Integration in die AlliDB und schnelle Ladezeiten.";

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
	insertMenuItem( $_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparamters );
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
//
// -> Und hier beginnt das eigentliche Modul

$lastevo="blobtree-evo0.png"; // -> Für EVO1

// -> Nachsehen ob der dynamische Techtree installiert ist.
if(file_exists("./config/m_research.cfg.php")){

	// -> Schlüsselforschungen zum Erreichen einer neuen Evostufe, kann sein das es im Laufe der Runde angepasst werden muss.
	$evo =array("blobtree-evo1.png"=>"Race into Space",
              "blobtree-evo2.png"=>"Interstellares Vordringen",
              "blobtree-evo3.png"=>"Aufnahme in die zivilisierten Welten",
              "blobtree-evo4.png"=>"Imperiale Gedanken",
              "blobtree-evo5.png"=>"Die Macht des Seins",
              "evo6neu.png"=>"Verstehen der Zusammenhänge",
              "blobtree-evo7.png"=>"Verstehen der Zusammenhänge");

	for($x=0;$x<sizeof($evo);$x++) {
		// -> Nach der ID für die Schlüsselforschungen suchen.
		$sql = "SELECT ID FROM " . $db_tb_research . " WHERE name='" . current($evo) . "'";
		$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
		__FILE__, __LINE__, $sql);
		$result1 = $db->db_fetch_array($result);
		$evo_id=$result1["ID"];
		// Wenn vorhanden, nachsehen ob der User diese Forschung schon hat.
		if(!empty($evo_id)){
			$sql = "SELECT * FROM " . $db_tb_research2user . " WHERE rid=" . $evo_id . " AND userid='" . $user_sitterlogin . "'";
			$result = $db->db_query($sql)
			or error(GENERAL_ERROR,
             'Could not query config information.', '',
			__FILE__, __LINE__, $sql);
			$result2 = $db->db_fetch_array($result);
			if(!empty($result2["rid"])){
				$lastevo=key($evo);
			}
		}
		next($evo);
	}
}


echo "<div class='doc_title'>";
echo "Nachfolgend eine Sammlung unterschiedlicher Techtrees</span>";
echo "</div>";
echo "<br>";
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function imgchange()
{
        var si =  document.frm.selbox.selectedIndex;
        document.img.src = document.frm.selbox.options[si].value;
}
//-->
</SCRIPT>
<?php
echo "<div class='doc_centered'>";
echo "<form name='frm'>";
echo "<SELECT NAME='selbox' size=1>";

if ($lastevo == "blobtree-evo0.png"){$sel=" selected";}else{$sel="";}
echo "<OPTION VALUE=\"bilder/techtree/blobtree-evo0.png\"".$sel.">";
echo "TreeEvo0";

if ($lastevo == "blobtree-evo1.png"){$sel=" selected";}else{$sel="";}
echo "<OPTION VALUE=\"bilder/techtree/blobtree-evo1.png\"".$sel.">";
echo "&nbsp; TreeEvo1";

if ($lastevo == "blobtree-evo2.png"){$sel=" selected";}else{$sel="";}
echo "<OPTION VALUE=\"bilder/techtree/blobtree-evo2.png\"".$sel.">";
echo "&nbsp;&nbsp; TreeEvo2";

if ($lastevo == "blobtree-evo3.png"){$sel=" selected";}else{$sel="";}
echo "<OPTION VALUE=\"bilder/techtree/blobtree-evo3.png\"".$sel.">";
echo "&nbsp;&nbsp;&nbsp; TreeEvo3";

if ($lastevo == "blobtree-evo4.png"){$sel=" selected";}else{$sel="";}
echo "<OPTION VALUE=\"bilder/techtree/blobtree-evo4.png\"".$sel.">";
echo "&nbsp;&nbsp;&nbsp;&nbsp; TreeEvo4";

if ($lastevo == "blobtree-evo5.png"){$sel=" selected";}else{$sel="";}
echo "<OPTION VALUE=\"bilder/techtree/blobtree-evo5.png\"".$sel.">";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TreeEvo5";

if ($lastevo == "evo6neu.png"){$sel=" selected";}else{$sel="";}
echo "<OPTION VALUE=\"bilder/techtree/evo6neu.png\"".$sel.">";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TreeEvo6";

if ($lastevo == "blobtree-evo7.png"){$sel=" selected";}else{$sel="";}
echo "<OPTION VALUE=\"bilder/techtree/blobtree-evo7.png\"".$sel.">";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TreeEvo7";

echo "</select>";
echo "<input type=\"button\" value=\"ansehen\" onClick=\"imgchange()\">";
echo "<br>";
//echo "wir danken H.G. Blob für die Grafiken der EVO 0, 1, 3, 4 und 5";
echo "<br><br>";
echo "<IMG SRC=\"bilder/techtree/".$lastevo."\"  NAME=\"img\">";
echo "</form>";
echo "</div>";
?>