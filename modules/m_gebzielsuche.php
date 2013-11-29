<?php
/*****************************************************************************
 * m_gebzielsuche.php                                                        *
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
$modulname = "m_gebzielsuche";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Gebäudezielsuche";

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
$moduldesc = "Zeigt kriegsrelevante Gebäude des Gegners an";

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
    $menuetitel       = "Gebäudezielsuche";
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

    require_once './includes/menu_fn.php';

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************

// Titelzeile
doc_title($modultitle);

global $db, $db_tb_scans, $db_tb_scans_geb, $db_tb_allianzstatus, $db_tb_spieler;

$sql_gegner = "SELECT `allianz` FROM `{$db_tb_allianzstatus}` WHERE `status`='Krieg'";
$result_gegner = $db->db_query($sql_gegner);
$row_gegner = $db->db_fetch_array($result_gegner);

$sql = "SELECT `coords`, `user`, `allianz` FROM `{$db_tb_scans}` WHERE (`allianz`='" . $row_gegner['allianz'] . "' AND `objekt`='Kolonie')";
$result = $db->db_query($sql);

?>

<table data-sortlist='[[0,0]]'class="tablesorter-blue">
	<thead>
		<tr>
			<th>
				<b>Koords</b>
			</th>
			<th>
				<b>Spieler</b>
			</th>
			<th>
				<abbr title="Flottenscanner">
				<?php
				echo "<img src='".GEBAEUDE_BILDER_PATH."flottenscanner.jpg'>";
				?>
				</abbr>
			</th>
			<th>
				<abbr title="Galascanner">
				<?php
				echo "<img src='".GEBAEUDE_BILDER_PATH."orb_gal_scanner.jpg'>";
				?>
			</th>
			<th>
				<abbr title="PU orbital">
				<?php
				echo "<img src='".GEBAEUDE_BILDER_PATH."panzer_update_orb.jpg'>";
				?>
				</abbr>
			</th>
			<th>
				<abbr title="PU planetar">
				<?php
				echo "<img src='".GEBAEUDE_BILDER_PATH."panzer_update_plan.jpg'>";
				?>
				</abbr>
			</th>
			<th>
				<abbr title="Alpha Schild">
				<?php
				echo "<img src='".GEBAEUDE_BILDER_PATH."plan_alphaschild.jpg'>";
				?>
				</abbr>
			</th>
			<th>
				<abbr title="DN Werft">
				<?php
				echo "<img src='".GEBAEUDE_BILDER_PATH."dn_werft.jpg'>";
				?>
				</abbr>
			</th>
			<th>
				<abbr title="große Werft">
				<?php
				echo "<img src='".GEBAEUDE_BILDER_PATH."gr_werft.jpg'>";
				?>
				</abbr>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		while ($row = $db->db_fetch_array($result)) {
		?>
			<tr>
				<td>
					<?php
					echo $row['coords'];
					?>
				</td>
				<td>
					<?php
					$sql_einmaurer = "SELECT `einmaurer`, `staatsform` FROM `{$db_tb_spieler}` WHERE `name`='".$row['user']."'";
					$result_einmaurer = $db->db_query($sql_einmaurer);
					$row_einmaurer = $db->db_fetch_array($result_einmaurer);
					echo $row['user'];
					if ($row_einmaurer['einmaurer']=='1') {
						?>
						<abbr title="Einmaurer">
						<?php
						echo '  <img src="'.BILDER_PATH.'icon_einmaurer.png">';
					}
					if ($row_einmaurer['staatsform']=='Kommunist') {
						?>
						<abbr title="Kommunist">
						<?php
						echo '  <img src="'.BILDER_PATH.'icon_fleeter.png">';
					}
					?>
				</td>
				<td>
					<?php
						$sql_geb1 = "SELECT `geb_anz` FROM `{$db_tb_scans_geb}` WHERE (`coords`='".$row['coords']."' AND `geb_id`='143')";
						$result_geb1 = $db->db_query($sql_geb1)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_geb1);
						$row_geb1 = $db->db_fetch_array($result_geb1);
						echo $row_geb1['geb_anz'];
					?>
				</td>
				<td>
					<?php
						$sql_geb2 = "SELECT `geb_anz` FROM `{$db_tb_scans_geb}` WHERE (`coords`='".$row['coords']."' AND `geb_id`='117')";
						$result_geb2 = $db->db_query($sql_geb2)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_geb2);
						$row_geb2 = $db->db_fetch_array($result_geb2);
						echo $row_geb2['geb_anz'];
					?>
				</td>
				<td>
					<?php
						$sql_geb3 = "SELECT `geb_anz` FROM `{$db_tb_scans_geb}` WHERE (`coords`='".$row['coords']."' AND `geb_id`='145')";
						$result_geb3 = $db->db_query($sql_geb3)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_geb3);
						$row_geb3 = $db->db_fetch_array($result_geb3);
						echo $row_geb3['geb_anz'];
					?>
				</td>
				<td>
					<?php
						$sql_geb4 = "SELECT `geb_anz` FROM `{$db_tb_scans_geb}` WHERE (`coords`='".$row['coords']."' AND `geb_id`='144')";
						$result_geb4 = $db->db_query($sql_geb4)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_geb4);
						$row_geb4 = $db->db_fetch_array($result_geb4);
						echo $row_geb4['geb_anz'];
					?>
				</td>
				<td>
					<?php
						$sql_geb5 = "SELECT `geb_anz` FROM `{$db_tb_scans_geb}` WHERE (`coords`='".$row['coords']."' AND `geb_id`='162')";
						$result_geb5 = $db->db_query($sql_geb5)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_geb5);
						$row_geb5 = $db->db_fetch_array($result_geb5);
						echo $row_geb5['geb_anz'];
					?>
				</td>
				<td>
					<?php
						$sql_geb6 = "SELECT `geb_anz` FROM `{$db_tb_scans_geb}` WHERE (`coords`='".$row['coords']."' AND `geb_id`='148')";
						$result_geb6 = $db->db_query($sql_geb6)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_geb6);
						$row_geb6 = $db->db_fetch_array($result_geb6);
						echo $row_geb6['geb_anz'];
					?>
				</td>
				<td>
					<?php
						$sql_geb7 = "SELECT `geb_anz` FROM `{$db_tb_scans_geb}` WHERE (`coords`='".$row['coords']."' AND `geb_id`='146')";
						$result_geb7 = $db->db_query($sql_geb7)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_geb7);
						$row_geb7 = $db->db_fetch_array($result_geb7);
						echo $row_geb7['geb_anz'];
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
</table>