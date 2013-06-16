<?php
/*****************************************************************************
 * m_bedarf_schiffe.php                                                      *
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
$modulname = "m_bedarf_schiffe";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Bedarf Schiffe";

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
$moduldesc = "Berechnet den Ressbedarf zu bauender Schiffe";

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
    $menuetitel       = "Bedarf Schiffe";
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

    if (!@include("./includes/menu_fn.php")) {
        die("Cannot load menu functions");
    }

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************

doc_title('Ressbedarf Schiffe');

global $id, $db, $db_tb_schiffstyp;
$sitterlogin = getVar('sitterlogin');

$sql_klorw = "SELECT schiff, kosten_eisen, kosten_stahl, kosten_vv4a, kosten_chemie, kosten_eis, kosten_wasser, kosten_energie, kosten_bev, dauer FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='kleine' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_klorw = $db->db_query($sql_klorw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_klorw);
$num = mysql_num_rows($result_klorw);

$sql_klplanw = "SELECT schiff, kosten_eisen, kosten_stahl, kosten_vv4a, kosten_chemie, kosten_eis, kosten_wasser, kosten_energie, kosten_bev, dauer FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='kleine' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_klplanw = $db->db_query($sql_klplanw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_klplanw);
$num = mysql_num_rows($result_klplanw);

$sql_miplanw = "SELECT schiff, kosten_eisen, kosten_stahl, kosten_vv4a, kosten_chemie, kosten_eis, kosten_wasser, kosten_energie, kosten_bev, dauer FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='mittlere' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_miplanw = $db->db_query($sql_miplanw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_miplanw);
$num = mysql_num_rows($result_miplanw);

$sql_miorw = "SELECT schiff, kosten_eisen, kosten_stahl, kosten_vv4a, kosten_chemie, kosten_eis, kosten_wasser, kosten_energie, kosten_bev, dauer FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='mittlere' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_miorw = $db->db_query($sql_miorw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_miorw);
$num = mysql_num_rows($result_miorw);
		
$sql_grw = "SELECT schiff, kosten_eisen, kosten_stahl, kosten_vv4a, kosten_chemie, kosten_eis, kosten_wasser, kosten_energie, kosten_bev, dauer FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='große' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_grw = $db->db_query($sql_grw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_grw);
$num = mysql_num_rows($result_grw);
		
$sql_dnw = "SELECT schiff, kosten_eisen, kosten_stahl, kosten_vv4a, kosten_chemie, kosten_eis, kosten_wasser, kosten_energie, kosten_bev, dauer FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='Dreadnought' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_dnw = $db->db_query($sql_dnw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_dnw);
$num = mysql_num_rows($result_dnw);
		
?>
<div id='container'>
	<?php
	echo "<form method=\"POST\" action=\"index.php?action=" . $modulname .
     "&sid=" . $sid . "\" enctype=\"multipart/form-data\">\n";
	?>
	<form method="POST" action="index.php?action=bedarf_schiffe" enctype="multipart/form-data">
		<table id='belegung'>
			<thead>
				<tr>
					<th>
						<b>Werft</b>
					</th>
					<th>
						<b>Anzahl</b>
					</th>
					<th>
						<b>Schiffe</b>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<b>kleine planetare Werft</b>
					</td>
					<td>
						<input type="number" name="anzahl_klplanw" min="0" max="30" step="1" value="" placeholder="0-30">
					</td>
					<td>
						<select name='schiffe_klplanw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_klplanw = mysql_fetch_assoc($result_klplanw)) {
								echo' <option value="' . $row_klplanw["schiff"] . '">' . $row_klplanw["schiff"] . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>kleine orbitale Werft</b>
					</td>
					<td>
						<input type="number" name="anzahl_klorw" min="0" max="40" step="1" placeholder="0-40">
					</td>
					<td>
						<select name='schiffe_klow'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_klorw = mysql_fetch_assoc($result_klorw)) {
								echo' <option value="' . $row_klorw["schiff"] . '">' . $row_klorw["schiff"] . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>mittlere planetare Werft</b>
					</td>
					<td>
						<input type="number" name="anzahl_miplanw" min="0" max="15" step="1" placeholder="0-15">
					</td>
					<td>
						<select name='schiffe_miplanw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_miplanw = mysql_fetch_assoc($result_miplanw)) {
								echo' <option value="' . $row_miplanw["schiff"] . '">' . $row_miplanw["schiff"] . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>mittlere orbitale Werft</b>
					</td>
					<td>
						<input type="number" name="anzahl_miorbw" min="0" max="15" step="1" placeholder="0-15">
					</td>
					<td>
						<select name='schiffe_miorw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_miorw = mysql_fetch_assoc($result_miorw)) {
								echo' <option value="' . $row_miorw["schiff"] . '">' . $row_miorw["schiff"] . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>große Werft</b>
					</td>
					<td>
						<input type="number" name="anzahl_grw" min="0" max="30" step="1" placeholder="0-6">
					</td>
					<td>
						<select name='schiffe_grw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_grw = mysql_fetch_assoc($result_grw)) {
								echo' <option value="' . $row_grw["schiff"] . '">' . $row_grw["schiff"] . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>DN Werft</b>
					</td>
					<td>
						<input type="number" name="anzahl_dn" min="0" max="30" step="1" placeholder="0-3">
					</td>
					<td>
						<select name='schiffe_dnw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_dnw = mysql_fetch_assoc($result_dnw)) {
								echo' <option value="' . $row_dnw["schiff"] . '">' . $row_dnw["schiff"] . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr class="center">
					<td colspan="3">
						<input type="submit" value="Berechnen" id="berechnen">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	
	<table id='ressbedarf'>
		<thead>
			<tr>
				<th>
				</th>
				<th>
					<b>Eisen</b>
				</th>
				<th>
					<b>Stahl</b>
				</th>
				<th>
					<b>VV4A</b>
				</th>
				<th>
					<b>Chemie</b>
				</th>
				<th>
					<b>Eis</b>
				</th>
				<th>
					<b>Wasser</b>
				</th>
				<th>
					<b>Energie</b>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<b>Ress /h</b>
				</td>
				<td>
					<?php
					echo (ceil(($row_klplanw["kosten_eisen"]/$row_klplanw["dauer"]*60*60)+($row_klorw["kosten_eisen"]/$row_klorw["dauer"]*60*60)+($row_miorw["kosten_eisen"]/$row_miorw["dauer"]*60*60)+($row_miplanw["kosten_eisen"]/$row_miplanw["dauer"]*60*60)+($row_grw["kosten_eisen"]/$row_grw["dauer"]*60*60)+($row_dnw["kosten_eisen"]/$row_dnw["dauer"]*60*60)));
					?>
				</td>
				<td>
					<?php
					echo (ceil(($row_klplanw["kosten_stahl"]/$row_klplanw["dauer"]*60*60)+($row_klorw["kosten_stahl"]/$row_klorw["dauer"]*60*60)+($row_miorw["kosten_stahl"]/$row_miorw["dauer"]*60*60)+($row_miplanw["kosten_stahl"]/$row_miplanw["dauer"]*60*60)+($row_grw["kosten_stahl"]/$row_grw["dauer"]*60*60)+($row_dnw["kosten_stahl"]/$row_dnw["dauer"]*60*60)));
					?>
				</td>
				<td>
					<?php
					echo (ceil(($row_klplanw["kosten_vv4a"]/$row_klplanw["dauer"]*60*60)+($row_klorw["kosten_vv4a"]/$row_klorw["dauer"]*60*60)+($row_miorw["kosten_vv4a"]/$row_miorw["dauer"]*60*60)+($row_miplanw["kosten_vv4a"]/$row_miplanw["dauer"]*60*60)+($row_grw["kosten_vv4a"]/$row_grw["dauer"]*60*60)+($row_dnw["kosten_vv4a"]/$row_dnw["dauer"]*60*60)));
					?>
				</td>
				<td>
					<?php
					echo (ceil(($row_klplanw["kosten_chemie"]/$row_klplanw["dauer"]*60*60)+($row_klorw["kosten_chemie"]/$row_klorw["dauer"]*60*60)+($row_miorw["kosten_chemie"]/$row_miorw["dauer"]*60*60)+($row_miplanw["kosten_chemie"]/$row_miplanw["dauer"]*60*60)+($row_grw["kosten_chemie"]/$row_grw["dauer"]*60*60)+($row_dnw["kosten_chemie"]/$row_dnw["dauer"]*60*60)));
					?>
				</td>
				<td>
					<?php
					echo (ceil(($row_klplanw["kosten_eis"]/$row_klplanw["dauer"]*60*60)+($row_klorw["kosten_eis"]/$row_klorw["dauer"]*60*60)+($row_miorw["kosten_eis"]/$row_miorw["dauer"]*60*60)+($row_miplanw["kosten_eis"]/$row_miplanw["dauer"]*60*60)+($row_grw["kosten_eis"]/$row_grw["dauer"]*60*60)+($row_dnw["kosten_eis"]/$row_dnw["dauer"]*60*60)));
					?>
				</td>
				<td>
					<?php
					echo (ceil(($row_klplanw["kosten_wasser"]/$row_klplanw["dauer"]*60*60)+($row_klorw["kosten_wasser"]/$row_klorw["dauer"]*60*60)+($row_miorw["kosten_wasser"]/$row_miorw["dauer"]*60*60)+($row_miplanw["kosten_wasser"]/$row_miplanw["dauer"]*60*60)+($row_grw["kosten_wasser"]/$row_grw["dauer"]*60*60)+($row_dnw["kosten_wasser"]/$row_dnw["dauer"]*60*60)));
					?>
				</td>
				<td>
					<?php
					echo (ceil(($row_klplanw["kosten_energie"]/$row_klplanw["dauer"]*60*60)+($row_klorw["kosten_energie"]/$row_klorw["dauer"]*60*60)+($row_miorw["kosten_energie"]/$row_miorw["dauer"]*60*60)+($row_miplanw["kosten_energie"]/$row_miplanw["dauer"]*60*60)+($row_grw["kosten_energie"]/$row_grw["dauer"]*60*60)+($row_dnw["kosten_energie"]/$row_dnw["dauer"]*60*60)));
					?>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='7'>
					<b> max Bevölkerung für 1x komplett Bauen</b>
				</td>
				<td>
					<?php
					echo ($row_klplanw["kosten_bev"]+$row_klorw["kosten_bev"]+$row_miplanw["kosten_bev"]+$row_miorw["kosten_bev"]+$row_grw["kosten_bev"]+$row_dnw["kosten_bev"]);
					?>
				</td>
			</tr>
		</tfoot>
	</table>				
</div>
