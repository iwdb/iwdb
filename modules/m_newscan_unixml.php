<?php
/*****************************************************************************/
/* m_newscan_unixml.php                                                      */
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
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschaftsprojekt von */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                   https://github.com/iwdb/iwdb                            */
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
$modulname  = "m_newscan_unixml";

//****************************************************************************
//
// -> Men�titel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Uni-XML-Datei einlesen";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation 
//    ausfuehren darf. Mögliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Übersicht angezeigt wird.
//
$moduldesc =
  "Modul zum einfachen Einlesen der gesamten Uni-XML-Datei, die man mittels Universumskontrolleinrichtung erhält.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//
function workInstallDatabase() {
	/*	global $db, $db_prefix, $db_tb_iwdbtabellen;

	$sqlscript = array(
	"CREATE TABLE " . $db_prefix . "neuername
	(
	);",

	"INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
	" VALUES('neuername')"
	);
	foreach($sqlscript as $sql) {
	$result = $db->db_query($sql)
	or error(GENERAL_ERROR,
	'Could not query config information.', '',
	__FILE__, __LINE__, $sql);
	}
	echo "<div class='system_notification'>Installation: Datenbank&auml;nderungen = <b>OK</b></div>";
	*/}

	//****************************************************************************
	//
	// Function workUninstallDatabase is creating all menu entries needed for
	// installing this module. This function is called by the installation method
	// in the included file includes/menu_fn.php
	//
	function workInstallMenu() {
		global $modultitle, $modulstatus;

		$menu    = getVar('menu');
		$submenu = getVar('submenu');

		$actionparamters = "";
		insertMenuItem( $menu, $submenu, $modultitle, $modulstatus, $actionparameters );
		//
		// Weitere Wiederholungen für weitere Menü-Einträge, z.B.
		//
		// 	insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
		//
	}

	//****************************************************************************
	//
	// Function workInstallConfigString will return all the other contents needed
	// for the configuration file
	//
	function workInstallConfigString() {
		/*  global $config_gameversion;
		 return
		 "\$v04 = \" <div class=\\\"doc_lightred\\\">(V " . $config_gameversion . ")</div>\";";
		 */}

		//****************************************************************************
		//
		// Function workUninstallDatabase is creating all database entries needed for
		// removing this module.
		//
		function workUninstallDatabase() {
			/*  global $db, $db_tb_iwdbtabellen, $db_tb_neuername;

			$sqlscript = array(
			"DROP TABLE " . $db_tb_neuername . ";",
			"DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='neuername';"
			);

			foreach($sqlscript as $sql) {
			$result = $db->db_query($sql)
			or error(GENERAL_ERROR,
			'Could not query config information.', '',
			__FILE__, __LINE__, $sql);
			}
			echo "<div class='system_notification'>Deinstallation: Datenbank&auml;nderungen = <b>OK</b></div>";
			*/}

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

	?>
	<div class='system_notification'><strong>Wichtig:</strong> Das Einlesen der XML-Datei dauert <strong>sehr</strong> lange! Warte bitte solange, bis dir angezeigt wird, dass jede Menge Planeten aktualisiert wurden.</div>
	<form action="index.php" method="post" enctype="multipart/form-data" accept-charset="ISO-8859-1">
		<input type="hidden" name="action" value="m_newscan_unixml" />
		<input type="hidden" name="sid" value="<?=$sid?>" />
		<input name="xmlfile" type="file" size="50" maxlength="5000000" accept="text/html" /><br /><br />
		<input type="submit"  name="submit" value="einlesen" />
	</form>
	<?php
	
	$submit = getVar('submit');
	if(isset($submit) && $submit == "einlesen") {
		if(is_uploaded_file($_FILES['xmlfile']['tmp_name'])) {
			$content = file_get_contents($_FILES['xmlfile']['tmp_name']);
			$content = str_replace(array('<name/>','<allianz_tag/>'), array('<name></name>','<allianz_tag></allianz_tag>'), $content);
			$content = str_replace(" \t", " ", $content);
			$content = str_replace("\t", " ", $content);
			$content = str_replace("\r", "\n ", $content);
			$content = str_replace("\n \n", "\n", $content);
			$content = htmlentities($content, ENT_QUOTES);
			$content = explode("\n", $content);
			//print_r($content);
			echo "<div class='system_notification'>Parsing XML-Datei...</div>\n";
			include("./parser/s_unixml.php");
			$func = "parse_unixml";
			$func($content);
		}
		else {
			echo "<font color=\"#FF0000\"><b>Fehler beim Lesen der Datei!</b></font><br>\n";
		}
	}
?>