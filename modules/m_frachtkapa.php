<?php
/*****************************************************************************
 * m_frachtkappa.php                                                         *
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
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname = "m_frachtkapa";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Frachtkapazitäten";

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
$moduldesc =
    "Das Frachtkapazitäten-Modul dient zur Berechnung der notwendigen" .
        " Transporteranzahl für eine gegebene Menge Ressourcen";

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
    $menuetitel       = "Frachtkapazitäten";
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
function workInstallConfigString()
{
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
if (!empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
    if ($user_status != "admin") {
        die('Hacking attempt...');
    }

    echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")</div>\n";

    require_once './includes/menu_fn.php';

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

doc_title('Frachtkapazitätenberechnung');
?>
<table>
	<tr>
		<td>
			<form name="transcalc" action="">

				<table id="transcalc_input_table" class="table_format" style="width: 80%;">
					<tr>
						<td colspan="2" class="titlebg"><b>Eingabe:</b></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Eisen:</td>
						<td class="windowbg1"><input type="text" size="17" id="eisen" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Stahl:</td>
						<td class="windowbg1"><input type="text" size="17" id="stahl" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">VV4A:</td>
						<td class="windowbg1"><input type="text" size="17" id="vv4a" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">chem. Elemente:</td>
						<td class="windowbg1"><input type="text" size="17" id="chemie" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Eis:</td>
						<td class="windowbg1"><input type="text" size="17" id="eis" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Wasser:</td>
						<td class="windowbg1"><input type="text" size="17" id="wasser" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Energie:</td>
						<td class="windowbg1"><input type="text" size="17" id="energie" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td colspan="2" class="titlebg"><b>Vorhandene Transen für Klasse 1</b></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Systransen:</td>
						<td class="windowbg1"><input type="text" size="17" id="systransen_vorhanden" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Gorgols:</td>
						<td class="windowbg1"><input type="text" size="17" id="gorgols_vorhanden" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Kamele:</td>
						<td class="windowbg1"><input type="text" size="17" id="kamele_vorhanden" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Flughunde:</td>
						<td class="windowbg1"><input type="text" size="17" id="flughunde_vorhanden" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td colspan="2" class="titlebg"><b>Vorhandene Transen für Klasse 2</b></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Lurche:</td>
						<td class="windowbg1"><input type="text" size="17" id="luche_vorhanden" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Eisbären:</td>
						<td class="windowbg1"><input type="text" size="17" id="eisbaeren_vorhanden" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Waschbären:</td>
						<td class="windowbg1"><input type="text" size="17" id="waschbaeren_vorhanden" value="0" pattern="\d*"></td>
					</tr>
					<tr>
						<td class="windowbg2" style="width: 200px;">Seepferdchen:</td>
						<td class="windowbg1"><input type="text" size="17" id="seepferdchen_vorhanden" value="0" pattern="\d*"></td>
					</tr>
				</table>
				<br>
			<input type="reset" class="center">
			</form>
		</td>
		<td>
		<br>
			<table id="transcalc_output_table" class="table_format" style="width: 80%;">
				<tr>
					<td colspan="3" class="titlebg"><b>Benötigte Frachtkapazität</b></td>
				</tr>
				<tr>
					<td class="windowbg2">Klasse 1:</td>
					<td class="windowbg1" id="class1kappatext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg1"></td>
				</tr>
				<tr>
					<td class="windowbg2">Klasse 2:</td>
					<td class="windowbg1" id="class2kappatext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg1"></td>
				</tr>
				<tr>
					<td colspan="3" class="titlebg"><b>Benötigte Transen für Klasse 1</b></td>
				</tr>
				<tr>
					<td class="windowbg2">entweder</td>
					<td class="windowbg1" id="systranstext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg2">Systrans(en)</td>
				</tr>
				<tr>
					<td class="windowbg2">oder</td>
					<td class="windowbg1" id="gorgoltext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg2">Gorgol(s)</td>
				</tr>
				<tr>
					<td class="windowbg2">oder</td>
					<td class="windowbg1" id="kameltext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg2">Kamel(e)</td>
				</tr>
				<tr>
					<td class="windowbg2">oder</td>
					<td class="windowbg1" id="flughundtext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg2">Flughund(e)</td>
				</tr>
				<tr>
					<td colspan="3" class="titlebg"><b>Benötigte Transen für Klasse 2</b></td>
				</tr>
				<tr>
					<td class="windowbg2">entweder</td>
					<td class="windowbg1" id="lurchtext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg2">Lurch(e)</td>
				</tr>
				<tr>
					<td class="windowbg2">oder</td>
					<td class="windowbg1" id="eisbaertext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg2">Eisbär(en)</td>
				</tr>
				<tr>
					<td class="windowbg2">oder</td>
					<td class="windowbg1" id="waschbaertext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg2">Waschbär(en)</td>
				</tr>
				<tr>
					<td class="windowbg2">oder</td>
					<td class="windowbg1" id="seepferdchentext" style="width: 200px;">&nbsp;</td>
					<td class="windowbg2">Seepferdchen</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script src="javascript/frachtkappa.js"></script>