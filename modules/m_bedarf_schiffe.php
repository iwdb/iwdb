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
$modultitle = "Ressbedarf Schiffe";

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
    $menuetitel       = "Ressbedarf Schiffe";
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

$sql_klplanw = "SELECT schiff FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='kleine' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_klplanw = $db->db_query($sql_klplanw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_klplanw);

$sql_klorw = "SELECT schiff FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='kleine' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_klorw = $db->db_query($sql_klorw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_klorw);

$sql_miorw = "SELECT schiff FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='mittlere' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_miorw = $db->db_query($sql_miorw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_miw);

$sql_miw = "SELECT schiff FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='mittlere' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_miplanw = $db->db_query($sql_miw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_miw);
		
$sql_grw = "SELECT schiff FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='große' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_grw = $db->db_query($sql_grw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_grw);
		
$sql_dnw = "SELECT schiff FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`werftTyp`='Dreadnought' AND `{$db_tb_schiffstyp}`.`typ`!='admin' ORDER BY `{$db_tb_schiffstyp}`.`schiff` ASC";
$result_dnw = $db->db_query($sql_dnw)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_dnw);
		
?>

<STYLE type="text/css">
	OPTION.red{color:red}
	OPTION.green{color:green}
</STYLE>
<script>
	    jQuery(function ($) {
    $('form').validatr();
    });
</script>

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
						<b>gültige Werte</b>
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
					<td class='center'>
						0-30
					</td>
					<td>
						<input type="number" name="anzahl_klplanw" min="0" max="30" step="1" value="0" placeholder="0-30">
					</td>
					<td>
						<select name="schiffe_klplanw">
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_klplanw = $db->db_fetch_array($result_klplanw)) {
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
					<td class='center'>
						0-40
					</td>
					<td>
						<input type="number" name="anzahl_klorw" min="0" max="40" step="1" value="0" placeholder="0-40">
					</td>
					<td>
						<select name='schiffe_klorw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_klorw = $db->db_fetch_array($result_klorw)) {
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
					<td class='center'>
						0-15
					</td>
					<td>
						<input type="number" name="anzahl_miplanw" min="0" max="15" step="1" value="0" placeholder="0-15">
					</td>
					<td>
						<select name='schiffe_miplanw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_miplanw = $db->db_fetch_array($result_miplanw)) {
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
					<td class='center'>
						0-15
					</td>
					<td>
						<input type="number" name="anzahl_miorw" min="0" max="15" step="1" value="0" placeholder="0-15">
					</td>
					<td>
						<select name='schiffe_miorw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_miorw = $db->db_fetch_array($result_miorw)) {
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
					<td class='center'>
						0-6
					</td>
					<td>
						<input type="number" name="anzahl_grw" min="0" max="30" step="1" value="0" placeholder="0-6">
					</td>
					<td>
						<select name='schiffe_grw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_grw = $db->db_fetch_array($result_grw)) {
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
					<td class='center'>
						0-3
					</td>
					<td>
						<input type="number" name="anzahl_dnw" min="0" max="30" step="1" value="0" placeholder="0-3">
					</td>
					<td>
						<select name='schiffe_dnw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_dnw = $db->db_fetch_array($result_dnw)) {
								echo' <option value="' . $row_dnw["schiff"] . '">' . $row_dnw["schiff"] . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<br>
		<table id='einstellungen'>
			<thead>
				<tr>
					<th>
						<b>Einstellung</b>
					</th>
					<th>
						<b>Werte</b>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<b>Staatsform</b>
					</td>
					<td>
						<select name='staatsform'>
							<option value="">--- Auswahl Staatsform ----</option>
							<option value="Kommunist" class="green">Kommunist</option>
							<option value="andere" class="red">andere</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>Ich bau gerne Schiffe</b>
					</td>
					<td>
						<select name='gen1'>
							<option value="">--- Auswahl Genetikoption 1 ----</option>
							<option value="1.2" class="red">+20%</option>
							<option value="1.1" class="red">+10%</option>
							<option value="1.0">+-0%</option>
							<option value="0.95" class="green">-5%</option>
							<option value="0.90" class="green">-10%</option>
							<option value="0.80" class="green">-20%</option>
							<option value="0.65" class="green">-35%</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>Jetzt neu, Jetzt BILLIG, Schiffe!!</b>
					</td>
					<td>
						<select name='gen2'>
							<option value="">--- Auswahl Genetikoption 2 ----</option>
							<option value="1.2" class="red">+20%</option>
							<option value="1.1" class="red">+10%</option>
							<option value="1.0">+-0%</option>
							<option value="0.90" class="green">-10%</option>
							<option value="0.80" class="green">-20%</option>
							<option value="0.70" class="green">-30%</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<table>
			<thead>
			</thead>
			<tbody>
				<tr class="center">
					<td colspan="3">
						<input type="submit" value="Berechne" id="berechnen">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	
	<?php
	
	if (!isset($_POST['schiffe_klplanw']) OR empty($_POST['schiffe_klplanw'])) {
		$_POST['schiffe_klplanw']='';
	}
	if (!isset($_POST['schiffe_klorw']) OR empty($_POST['schiffe_klorw'])) {
		$_POST['schiffe_klorw']='';
	}
	if (!isset($_POST['schiffe_miplanw']) OR empty($_POST['schiffe_miplanw'])) {
		$_POST['schiffe_miplanw']='';
	}
	if (!isset($_POST['schiffe_miorw']) OR empty($_POST['schiffe_miorw'])) {
		$_POST['schiffe_miorw']='';
	}
	if (!isset($_POST['schiffe_grw']) OR empty($_POST['schiffe_grw'])) {
		$_POST['schiffe_grw']='';
	}
	if (!isset($_POST['schiffe_dnw']) OR empty($_POST['schiffe_dnw'])) {
		$_POST['schiffe_dnw']='';
	}
	if (!isset($_POST['staatsform']) OR empty($_POST['staatsform'])) {
		$_POST['staatsform']='';
	}
	if (!isset($_POST['anzahl_klplanw']) OR empty($_POST['anzahl_klplanw'])) {
		$_POST['anzahl_klplanw']='0';
	}
	if (!isset($_POST['anzahl_klorw']) OR empty($_POST['anzahl_klorw'])) {
		$_POST['anzahl_klorw']='0';
	}
	if (!isset($_POST['anzahl_miplanw']) OR empty($_POST['anzahl_miplanw'])) {
		$_POST['anzahl_miplanw']='0';
	}
	if (!isset($_POST['anzahl_miorw']) OR empty($_POST['anzahl_miorw'])) {
		$_POST['anzahl_miorw']='0';
	}
	if (!isset($_POST['anzahl_grw']) OR empty($_POST['anzahl_grw'])) {
		$_POST['anzahl_grw']='0';
	}
	if (!isset($_POST['anzahl_dnw']) OR empty($_POST['anzahl_dnw'])) {
		$_POST['anzahl_dnw']='0';
	}
	if (!isset($_POST['gen1']) OR empty($_POST['gen1'])) {
		$_POST['gen1']='1';
	}
	if (!isset($_POST['gen2']) OR empty($_POST['gen2'])) {
		$_POST['gen2']='1';
	}
	
	
	$sql1 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_klplanw'] . "'";
	$result1 = $db->db_query($sql1)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql1);
	$row1 = $db->db_fetch_array($result1);
	
	$sql2 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_klorw'] . "'";
	$result2 = $db->db_query($sql2)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql2);
	$row2 = $db->db_fetch_array($result2);
	
	$sql3 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_miplanw'] . "'";
	$result3 = $db->db_query($sql3)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql3);
	$row3 = $db->db_fetch_array($result3);
	
	$sql4 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_miorw'] . "'";
	$result4 = $db->db_query($sql4)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql4);
	$row4 = $db->db_fetch_array($result4);
	
	$sql5 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_grw'] . "'";
	$result5 = $db->db_query($sql5)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql5);
	$row5 = $db->db_fetch_array($result5);
	
	$sql6 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_dnw'] . "'";
	$result6 = $db->db_query($sql6)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql6);
	$row6 = $db->db_fetch_array($result6);
	
	if ($_POST['staatsform']==="Kommunist") {
		$staatsform_dauer=0.90;
		$staatsform_kosten=0.95;
	}
	else {
		$staatsform_dauer=1.0;
		$staatsform_kosten=1.0;
	}
				
	$eisen =	($row1['eisen']*$_POST['anzahl_klplanw']*1.2)+
				($row2['eisen']*$_POST['anzahl_klorw'])+
				($row3['eisen']*$_POST['anzahl_miplanw']*1.2)+
				($row4['eisen']*$_POST['anzahl_miorw'])+
				($row5['eisen']*$_POST['anzahl_grw'])+
				($row6['eisen']*$_POST['anzahl_dnw']);
	$eisen = ceil($eisen*$staatsform_kosten*$_POST['gen2']*3600);
	$stahl =	($row1['stahl']*$_POST['anzahl_klplanw']*1.2)+
				($row2['stahl']*$_POST['anzahl_klorw'])+
				($row3['stahl']*$_POST['anzahl_miplanw']*1.2)+
				($row4['stahl']*$_POST['anzahl_miorw'])+
				($row5['stahl']*$_POST['anzahl_grw'])+
				($row6['stahl']*$_POST['anzahl_dnw']);
	$stahl = ceil($stahl*$staatsform_kosten*$_POST['gen2']*3600);
	$vv4a =		($row1['vv4a']*$_POST['anzahl_klplanw']*1.2)+
				($row2['vv4a']*$_POST['anzahl_klorw'])+
				($row3['vv4a']*$_POST['anzahl_miplanw']*1.2)+
				($row4['vv4a']*$_POST['anzahl_miorw'])+
				($row5['vv4a']*$_POST['anzahl_grw'])+
				($row6['vv4a']*$_POST['anzahl_dnw']);
	$vv4a = ceil($vv4a*$staatsform_kosten*$_POST['gen2']*3600);
	$chemie =	($row1['chemie']*$_POST['anzahl_klplanw']*1.2)+
				($row2['chemie']*$_POST['anzahl_klorw'])+
				($row3['chemie']*$_POST['anzahl_miplanw']*1.2)+
				($row4['chemie']*$_POST['anzahl_miorw'])+
				($row5['chemie']*$_POST['anzahl_grw'])+
				($row6['chemie']*$_POST['anzahl_dnw']);
	$chemie = ceil($chemie*$staatsform_kosten*$_POST['gen2']*3600);
	$eis =		($row1['eis']*$_POST['anzahl_klplanw']*1.2)+
				($row2['eis']*$_POST['anzahl_klorw'])+
				($row3['eis']*$_POST['anzahl_miplanw']*1.2)+
				($row4['eis']*$_POST['anzahl_miorw'])+
				($row5['eis']*$_POST['anzahl_grw'])+
				($row6['eis']*$_POST['anzahl_dnw']);
	$eis = ceil($eis*$staatsform_kosten*$_POST['gen2']*3600);
	$wasser =	($row1['wasser']*$_POST['anzahl_klplanw']*1.2)+
				($row2['wasser']*$_POST['anzahl_klorw'])+
				($row3['wasser']*$_POST['anzahl_miplanw']*1.2)+
				($row4['wasser']*$_POST['anzahl_miorw'])+
				($row5['wasser']*$_POST['anzahl_grw'])+
				($row6['wasser']*$_POST['anzahl_dnw']);
	$wasser = ceil($wasser*$staatsform_kosten*$_POST['gen2']*3600);
	$energie =	($row1['energie']*$_POST['anzahl_klplanw']*1.2)+
				($row2['energie']*$_POST['anzahl_klorw'])+
				($row3['energie']*$_POST['anzahl_miplanw']*1.2)+
				($row4['energie']*$_POST['anzahl_miorw'])+
				($row5['energie']*$_POST['anzahl_grw'])+
				($row6['energie']*$_POST['anzahl_dnw']);
	$energie = ceil($energie*$staatsform_kosten*$_POST['gen2']*3600);
	$bev =		($row1['kosten_bev']*$_POST['anzahl_klplanw']+
				$row2['kosten_bev']*$_POST['anzahl_klorw']+
				$row3['kosten_bev']*$_POST['anzahl_miplanw']+
				$row4['kosten_bev']*$_POST['anzahl_miorw']+
				$row5['kosten_bev']*$_POST['anzahl_grw']+
				$row6['kosten_bev']*$_POST['anzahl_dnw']);
	
	
	?>
	<br>
	<br>
	<br>
	<br>
	
	<table class='table_format' id='ressbedarf' style='width: 80%'>
		<thead>
			<tr class='windowbg2'>
				<th>
				</th>
				<th style='width: 13%'>
					<b>Eisen</b>
				</th>
				<th style='width: 13%'>
					<b>Stahl</b>
				</th>
				<th style='width: 13%'>
					<b>VV4A</b>
				</th>
				<th style='width: 13%'>
					<b>Chemie</b>
				</th>
				<th style='width: 13%'>
					<b>Eis</b>
				</th>
				<th style='width: 13%'>
					<b>Wasser</b>
				</th>
				<th style='width: 13%'>
					<b>Energie</b>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='windowbg3'>
					<b>Ress /h</b>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $eisen;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $stahl;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $vv4a;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $chemie;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $eis;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $wasser;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $energie;
					?>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='7'>
					<b> max Bevölkerung für 1x komplett Bauen</b>
				</td>
				<td class='center'>
					<?php
					echo "<font color='#008B00'>", $bev;
					?>
				</td>
			</tr>
		</tfoot>
	</table>				
</div>
<script src="javascript/validatr.min.js"></script>