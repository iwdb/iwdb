<?php
/*****************************************************************************
 * m_bedarfsrechner.php                                                      *
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
$modulname = "m_bedarfsrechner";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Ressbedarfsrechner";

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
$moduldesc = "Berechnet den Ressbedarf zum Bau von Schiffen und Gebs";

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
    $menuetitel       = "Ressbedarfsrechner";
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

doc_title('Ressbedarfsrechner');

global $id, $db, $db_tb_schiffstyp, $db_tb_gebbaukosten;

?>
<style type="text/css">
	option.red{color:red}
	option.green{color:green}
</style>

<script>
$(document).ready(function() {
	$('form').validatr();
	$('#btn_reset').click(function(){
		$(':input').not(':button, :submit, :reset, :hidden').val('');
		$(':select').not(':button, :submit, :reset, :hidden').val('');
	});
});
</script>
<?php

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
		
$sql_geb = "SELECT name FROM `{$db_tb_gebbaukosten}` ORDER BY `{$db_tb_gebbaukosten}`.`name` ASC";
$result_geb = $db->db_query($sql_geb)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_geb);
		
	
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
	if (!isset($_POST['gen3']) OR empty($_POST['gen3'])) {
		$_POST['gen3']='1';
	}
	if (!isset($_POST['gen4']) OR empty($_POST['gen4'])) {
		$_POST['gen4']='1';
	}
	if (!isset($_POST['gebbau']) OR empty($_POST['gebbau'])) {
		$_POST['gebbau']='';
	}
	if (!isset($_POST['ber_hour']) OR empty($_POST['ber_hour'])) {
		$_POST['ber_hour']='36';
	}
	
	
	$selectedValue_klplanschiffe = $_POST['schiffe_klplanw'];
	$selectedValue_klorschiffe = $_POST['schiffe_klorw'];
	$selectedValue_miplanschiffe = $_POST['schiffe_miplanw'];
	$selectedValue_miorschiffe = $_POST['schiffe_miorw'];
	$selectedValue_grschiffe = $_POST['schiffe_grw'];
	$selectedValue_dnschiffe = $_POST['schiffe_dnw'];
	$selectedValue_staatsform = $_POST['staatsform'];
	$selectedValue_gen1 = $_POST['gen1'];
	$selectedValue_gen2 = $_POST['gen2'];
	$selectedValue_gen3 = $_POST['gen3'];
	$selectedValue_gen4 = $_POST['gen4'];
	$selectedValue_geb = $_POST['gebbau'];
		
?>

<div id='container'>
	<div  class='titlebg'>
		<b>Schiffsauswahl</b>
	</div>
	<br>
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
						<input type="number" name="anzahl_klplanw" min="0" max="30" step="1" value="<?php echo $_POST['anzahl_klplanw'] ?>" placeholder="0-30">
					</td>
					<td>
						<select name="schiffe_klplanw">
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_klplanw = $db->db_fetch_array($result_klplanw)) {
								echo' <option value="' . $row_klplanw["schiff"] . '"';
									if ($selectedValue_klplanschiffe==$row_klplanw["schiff"]) {
										echo ' selected="selected"';
									}
								echo '>' . $row_klplanw["schiff"] . '</option>';
								
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
						<input type="number" name="anzahl_klorw" min="0" max="40" step="1" value="<?php echo $_POST['anzahl_klorw'] ?>" placeholder="0-40">
					</td>
					<td>
						<select name='schiffe_klorw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_klorw = $db->db_fetch_array($result_klorw)) {
								echo' <option value="' . $row_klorw["schiff"] . '"';
									if ($selectedValue_klorschiffe==$row_klorw["schiff"]) {
										echo ' selected="selected"';
									}
								echo '>' . $row_klorw["schiff"] . '</option>';
								
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
						<input type="number" name="anzahl_miplanw" min="0" max="15" step="1" value="<?php echo $_POST['anzahl_miplanw'] ?>" placeholder="0-15">
					</td>
					<td>
						<select name='schiffe_miplanw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_miplanw = $db->db_fetch_array($result_miplanw)) {
								echo' <option value="' . $row_miplanw["schiff"] . '"';
									if ($selectedValue_miplanschiffe==$row_miplanw["schiff"]) {
										echo ' selected="selected"';
									}
								echo '>' . $row_miplanw["schiff"] . '</option>';
								
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
						<input type="number" name="anzahl_miorw" min="0" max="15" step="1" value="<?php echo $_POST['anzahl_miorw'] ?>" placeholder="0-15">
					</td>
					<td>
						<select name='schiffe_miorw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_miorw = $db->db_fetch_array($result_miorw)) {
								echo' <option value="' . $row_miorw["schiff"] . '"';
									if ($selectedValue_miorschiffe==$row_miorw["schiff"]) {
										echo ' selected="selected"';
									}
								echo '>' . $row_miorw["schiff"] . '</option>';
								
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
						<input type="number" name="anzahl_grw" min="0" max="30" step="1" value="<?php echo $_POST['anzahl_grw'] ?>" placeholder="0-6">
					</td>
					<td>
						<select name='schiffe_grw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_grw = $db->db_fetch_array($result_grw)) {
								echo' <option value="' . $row_grw["schiff"] . '"';
									if ($selectedValue_grschiffe==$row_grw["schiff"]) {
										echo ' selected="selected"';
									}
								echo '>' . $row_grw["schiff"] . '</option>';
								
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
						<input type="number" name="anzahl_dnw" min="0" max="30" step="1" value="<?php echo $_POST['anzahl_dnw'] ?>" placeholder="0-3">
					</td>
					<td>
						<select name='schiffe_dnw'>
							<option value="">--- Auswahl des Schiffes ---</option>
							<?php
							while ($row_dnw = $db->db_fetch_array($result_dnw)) {
								echo' <option value="' . $row_dnw["schiff"] . '"';
									if ($selectedValue_dnschiffe==$row_dnw["schiff"]) {
										echo ' selected="selected"';
									}
								echo '>' . $row_dnw["schiff"] . '</option>';
								
							}
							?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<br>
		
		<div  class='titlebg'>
		<b>Gebäudeauswahl</b>
		</div>
		<br>
		<table id='gebaude'>
			<thead>
				<tr>
					<th>
						<b>Gebäude</b>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select name='gebbau'>
							<option value="">--- Auswahl des Gebäudes ----</option>
							<?php
							while ($row_geb = $db->db_fetch_array($result_geb)) {
								echo' <option value="' . $row_geb["name"] . '"';
									if ($selectedValue_geb==$row_geb["name"]) {
										echo ' selected="selected"';
									}
								echo '>' . $row_geb["name"] . '</option>';
								
							}
							?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		
		<br>
		<br>
		<div  class='titlebg'>
		<b>Genetikauswahl</b>
		</div>
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
							<option value="Kommunist" class="green"
							<?php
								if ($selectedValue_staatsform == "Kommunist") {
									echo ' selected="selected"';
								}
							?>
							>Kommunist</option>
							<option value="andere" class="red"
							<?php
								if ($selectedValue_staatsform == "andere") {
									echo ' selected="selected"';
								}
							?>
							>andere</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>Ich bau gerne Schiffe</b>
					</td>
					<td>
						<select name='gen1'>
							
							<option value="1.2" class="red"
							<?php
								if ($selectedValue_gen1 == "1.2") {
									echo ' selected="selected"';
								}
							?>
							>+20%</option>
							<option value="1.1" class="red"
							<?php
								if ($selectedValue_gen1 == "1.1") {
									echo ' selected="selected"';
								}
							?>
							>+10%</option>
							<option value="1.0"
							<?php
								if ($selectedValue_gen1 == "1.0") {
									echo ' selected="selected"';
								}
							?>
							>+-0%</option>
							<option value="0.95" class="green"
							<?php
								if ($selectedValue_gen1 == "0.95") {
									echo ' selected="selected"';
								}
							?>
							>-5%</option>
							<option value="0.90" class="green"
							<?php
								if ($selectedValue_gen1 == "0.90") {
									echo ' selected="selected"';
								}
							?>
							>-10%</option>
							<option value="0.80" class="green"
							<?php
								if ($selectedValue_gen1 == "0.80") {
									echo ' selected="selected"';
								}
							?>
							>-20%</option>
							<option value="0.65" class="green"
							<?php
								if ($selectedValue_gen1 == "0.65") {
									echo ' selected="selected"';
								}
							?>
							>-35%</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>Jetzt neu, Jetzt BILLIG, Schiffe!!</b>
					</td>
					<td>
						<select name='gen2'>
							
							<option value="1.2" class="red"
							<?php
								if ($selectedValue_gen2 == "1.2") {
									echo ' selected="selected"';
								}
							?>
							>+20%</option>
							<option value="1.1" class="red"
							<?php
								if ($selectedValue_gen2 == "1.1") {
									echo ' selected="selected"';
								}
							?>
							>+10%</option>
							<option value="1.0"
							<?php
								if ($selectedValue_gen2 == "1.0") {
									echo ' selected="selected"';
								}
							?>
							>+-0%</option>
							<option value="0.90" class="green"
							<?php
								if ($selectedValue_gen2 == "0.90") {
									echo ' selected="selected"';
								}
							?>
							>-10%</option>
							<option value="0.8" class="green"
							<?php
								if ($selectedValue_gen2 == "0.80") {
									echo ' selected="selected"';
								}
							?>
							>-20%</option>
							<option value="0.7" class="green"
							<?php
								if ($selectedValue_gen2 == "0.70") {
									echo ' selected="selected"';
								}
							?>
							>-30%</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>Bau auf Bau auf Bau auf</b>
					</td>
					<td>
						<select name='gen3'>		
							<option value="1.1" class="red"
							<?php
								if ($selectedValue_gen2 == "1.1") {
									echo ' selected="selected"';
								}
							?>
							>+10%</option>
							<option value="1.05" class="red"
							<?php
								if ($selectedValue_gen2 == "1.05") {
									echo ' selected="selected"';
								}
							?>
							>+5%</option>
							<option value="1.00"
							<?php
								if ($selectedValue_gen2 == "1.00") {
									echo ' selected="selected"';
								}
							?>
							>+-0%</option>
							<option value="0.95" class="green"
							<?php
								if ($selectedValue_gen2 == "0.95") {
									echo ' selected="selected"';
								}
							?>
							>-5%</option>
							<option value="0.90" class="green"
							<?php
								if ($selectedValue_gen2 == "0.90") {
									echo ' selected="selected"';
								}
							?>
							>-10%</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<b>Ich steh auf billig</b>
					</td>
					<td>
						<select name='gen4'>		
							<option value="1.25" class="red"
							<?php
								if ($selectedValue_gen2 == "1.25") {
									echo ' selected="selected"';
								}
							?>
							>+25%</option>
							<option value="1.1" class="red"
							<?php
								if ($selectedValue_gen2 == "1.1") {
									echo ' selected="selected"';
								}
							?>
							>+10%</option>
							<option value="1.00"
							<?php
								if ($selectedValue_gen2 == "1.00") {
									echo ' selected="selected"';
								}
							?>
							>+-0%</option>
							<option value="0.90" class="green"
							<?php
								if ($selectedValue_gen2 == "0.90") {
									echo ' selected="selected"';
								}
							?>
							>-10%</option>
							<option value="0.80" class="green"
							<?php
								if ($selectedValue_gen2 == "0.80") {
									echo ' selected="selected"';
								}
							?>
							>-20%</option>
							<option value="0.70" class="green"
							<?php
								if ($selectedValue_gen2 == "0.70") {
									echo ' selected="selected"';
								}
							?>
							>-30%</option>
							<option value="0.60" class="green"
							<?php
								if ($selectedValue_gen2 == "0.60") {
									echo ' selected="selected"';
								}
							?>
							>-40%</option>
							<option value="0.50" class="green"
							<?php
								if ($selectedValue_gen2 == "0.50") {
									echo ' selected="selected"';
								}
							?>
							>-50%</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<br>
		
		<div  class='titlebg'>
		<b>Ausgabe für x Stunden</b>
		</div>
		<br>
		<table>
			<thead>
				<tr>
					<th class="center">
						<b> Anzahl der Stunden für die Berechnung</b>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="center">
						<input type="number" name="ber_hour" class="center" min="1" max="400" step="1" value="<?php echo $_POST['ber_hour'] ?>" placeholder="1-400">
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<br>
		<br>
		<table>
			<thead>
			</thead>
			<tbody>
				<tr class="center">
					<td colspan="2">
						<input type="submit" value="Berechne" id="berechnen">
					</td>
					<td colspan="2">
						<input type="button" value="Reset" id="btn_reset">
					</td>
				</tr>
			</tbody>
		</table>
		
	</form>
	
	<?php
	
	
	
	
	$sql1 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev, kosten_creds/dauer as creds FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_klplanw'] . "'";
	$result1 = $db->db_query($sql1)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql1);
	$row1 = $db->db_fetch_array($result1);
	
	$sql2 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev, kosten_creds/dauer as creds FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_klorw'] . "'";
	$result2 = $db->db_query($sql2)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql2);
	$row2 = $db->db_fetch_array($result2);
	
	$sql3 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev, kosten_creds/dauer as creds FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_miplanw'] . "'";
	$result3 = $db->db_query($sql3)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql3);
	$row3 = $db->db_fetch_array($result3);
	
	$sql4 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev, kosten_creds/dauer as creds FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_miorw'] . "'";
	$result4 = $db->db_query($sql4)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql4);
	$row4 = $db->db_fetch_array($result4);
	
	$sql5 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev, kosten_creds/dauer as creds FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_grw'] . "'";
	$result5 = $db->db_query($sql5)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql5);
	$row5 = $db->db_fetch_array($result5);
	
	$sql6 = "SELECT schiff, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev, kosten_creds/dauer as creds FROM `{$db_tb_schiffstyp}` WHERE `{$db_tb_schiffstyp}`.`schiff`='" . $_POST['schiffe_dnw'] . "'";
	$result6 = $db->db_query($sql6)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql6);
	$row6 = $db->db_fetch_array($result6);
	
	$sql7 = "SELECT name, dauer, kosten_eisen/dauer AS eisen, kosten_stahl/dauer AS stahl, kosten_vv4a/dauer AS vv4a, kosten_chemie/dauer AS chemie, kosten_eis/dauer AS eis, kosten_wasser/dauer as wasser, kosten_energie/dauer as energie, kosten_bev, kosten_creds/dauer as creds FROM `{$db_tb_gebbaukosten}` WHERE `{$db_tb_gebbaukosten}`.`name`='" . $_POST['gebbau'] . "'";
	$result7 = $db->db_query($sql7)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql7);
	$row7 = $db->db_fetch_array($result7);
	
	if ($_POST['staatsform']==="Kommunist") {
		$staatsform_dauer=0.90;
		$staatsform_kosten=0.95;
	}
	else {
		$staatsform_dauer=1.0;
		$staatsform_kosten=1.0;
	}
				
	$eisen_schiff =	($row1['eisen']*$_POST['anzahl_klplanw']*1.2)+
				($row2['eisen']*$_POST['anzahl_klorw'])+
				($row3['eisen']*$_POST['anzahl_miplanw']*1.2)+
				($row4['eisen']*$_POST['anzahl_miorw'])+
				($row5['eisen']*$_POST['anzahl_grw'])+
				($row6['eisen']*$_POST['anzahl_dnw']);
	$eisen_schiff = ceil($eisen_schiff*$staatsform_kosten*$_POST['gen2']*3600);
	$eisen_geb = ceil($row7['eisen']*$_POST['gen4']*3600);
	$eisen = $eisen_schiff+$eisen_geb;
	$stahl_schiff =	($row1['stahl']*$_POST['anzahl_klplanw']*1.2)+
				($row2['stahl']*$_POST['anzahl_klorw'])+
				($row3['stahl']*$_POST['anzahl_miplanw']*1.2)+
				($row4['stahl']*$_POST['anzahl_miorw'])+
				($row5['stahl']*$_POST['anzahl_grw'])+
				($row6['stahl']*$_POST['anzahl_dnw']);
	$stahl_schiff = ceil($stahl_schiff*$staatsform_kosten*$_POST['gen2']*3600);
	$stahl_geb = ceil($row7['stahl']*$_POST['gen4']*3600);
	$stahl = $stahl_schiff+$stahl_geb;
	$vv4a_schiff =		($row1['vv4a']*$_POST['anzahl_klplanw']*1.2)+
				($row2['vv4a']*$_POST['anzahl_klorw'])+
				($row3['vv4a']*$_POST['anzahl_miplanw']*1.2)+
				($row4['vv4a']*$_POST['anzahl_miorw'])+
				($row5['vv4a']*$_POST['anzahl_grw'])+
				($row6['vv4a']*$_POST['anzahl_dnw']);
	$vv4a_schiff = ceil($vv4a_schiff*$staatsform_kosten*$_POST['gen2']*3600);
	$vv4a_geb = ceil($row7['vv4a']*$_POST['gen4']*3600);
	$vv4a = $vv4a_schiff+$vv4a_geb;
	$chemie_schiff =	($row1['chemie']*$_POST['anzahl_klplanw']*1.2)+
				($row2['chemie']*$_POST['anzahl_klorw'])+
				($row3['chemie']*$_POST['anzahl_miplanw']*1.2)+
				($row4['chemie']*$_POST['anzahl_miorw'])+
				($row5['chemie']*$_POST['anzahl_grw'])+
				($row6['chemie']*$_POST['anzahl_dnw']);
	$chemie_schiff = ceil($chemie_schiff*$staatsform_kosten*$_POST['gen2']*3600);
	$chemie_geb = ceil($row7['chemie']*$_POST['gen4']*3600);
	$chemie = $chemie_schiff+$chemie_geb;
	$eis_schiff =		($row1['eis']*$_POST['anzahl_klplanw']*1.2)+
				($row2['eis']*$_POST['anzahl_klorw'])+
				($row3['eis']*$_POST['anzahl_miplanw']*1.2)+
				($row4['eis']*$_POST['anzahl_miorw'])+
				($row5['eis']*$_POST['anzahl_grw'])+
				($row6['eis']*$_POST['anzahl_dnw']);
	$eis_schiff = ceil($eis_schiff*$staatsform_kosten*$_POST['gen2']*3600);
	$eis_geb = ceil($row7['eis']*$_POST['gen4']*3600);
	$eis = $eis_schiff+$eis_geb;
	$wasser_schiff =	($row1['wasser']*$_POST['anzahl_klplanw']*1.2)+
				($row2['wasser']*$_POST['anzahl_klorw'])+
				($row3['wasser']*$_POST['anzahl_miplanw']*1.2)+
				($row4['wasser']*$_POST['anzahl_miorw'])+
				($row5['wasser']*$_POST['anzahl_grw'])+
				($row6['wasser']*$_POST['anzahl_dnw']);
	$wasser_schiff = ceil($wasser_schiff*$staatsform_kosten*$_POST['gen2']*3600);
	$wasser_geb = ceil($row7['wasser']*$_POST['gen4']*3600);
	$wasser = $wasser_schiff+$wasser_geb;
	$energie_schiff =	($row1['energie']*$_POST['anzahl_klplanw']*1.2)+
				($row2['energie']*$_POST['anzahl_klorw'])+
				($row3['energie']*$_POST['anzahl_miplanw']*1.2)+
				($row4['energie']*$_POST['anzahl_miorw'])+
				($row5['energie']*$_POST['anzahl_grw'])+
				($row6['energie']*$_POST['anzahl_dnw']);
	$energie_schiff = ceil($energie_schiff*$staatsform_kosten*$_POST['gen2']*3600);
	$energie_geb = ceil($row7['energie']*$_POST['gen4']*3600);
	$energie = $energie_schiff+$energie_geb;
	
	$creds_schiff =	($row1['creds']*$_POST['anzahl_klplanw']*1.2)+
				($row2['creds']*$_POST['anzahl_klorw'])+
				($row3['creds']*$_POST['anzahl_miplanw']*1.2)+
				($row4['creds']*$_POST['anzahl_miorw'])+
				($row5['creds']*$_POST['anzahl_grw'])+
				($row6['creds']*$_POST['anzahl_dnw']);
	$creds_schiff = ceil($creds_schiff*$staatsform_kosten*$_POST['gen2']*3600);
	$creds_geb = ceil($row7['creds']*$_POST['gen4']*3600);
	$creds = $creds_schiff+$creds_geb;
	
	$bev =		($row1['kosten_bev']*$_POST['anzahl_klplanw']+
				$row2['kosten_bev']*$_POST['anzahl_klorw']+
				$row3['kosten_bev']*$_POST['anzahl_miplanw']+
				$row4['kosten_bev']*$_POST['anzahl_miorw']+
				$row5['kosten_bev']*$_POST['anzahl_grw']+
				$row6['kosten_bev']*$_POST['anzahl_dnw']+
				$row7['kosten_bev']);
	
	
	?>
	<br>
	<br>
	<br>
	<br>
	
	<div  class='titlebg'>
		<b>Ausgabe Ressbedarf</b>
	</div>
	<br>
	<table class='table_format' id='ressbedarf' style='width: 80%'>
		<thead>
			<tr class='windowbg2'>
				<th>
				</th>
				<th style='width: 9%'>
					<b>Eisen</b>
				</th>
				<th style='width: 9%'>
					<b>Stahl</b>
				</th>
				<th style='width: 9%'>
					<b>VV4A</b>
				</th>
				<th style='width: 9%'>
					<b>Chemie</b>
				</th>
				<th style='width: 9%'>
					<b>Eis</b>
				</th>
				<th style='width: 9%'>
					<b>Wasser</b>
				</th>
				<th style='width: 9%'>
					<b>Energie</b>
				</th>
				<th style='width: 9%'>
					<b>Credits</b>
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
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $creds;
					?>
				</td>
			</tr>
			<tr>
				<td class='windowbg3'>
					<b>Ress /24h</b>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $eisen*24;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $stahl*24;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $vv4a*24;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $chemie*24;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $eis*24;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $wasser*24;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $energie*24;
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $creds*24;
					?>
				</td>
			</tr>
			<tr>
				<td class='windowbg3'>
					<b>Ress /<?php echo $_POST['ber_hour'] ?>h</b>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $eisen*$_POST['ber_hour'];
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $stahl*$_POST['ber_hour'];
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $vv4a*$_POST['ber_hour'];
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $chemie*$_POST['ber_hour'];
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $eis*$_POST['ber_hour'];
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $wasser*$_POST['ber_hour'];
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $energie*$_POST['ber_hour'];
					?>
				</td>
				<td class='right'>
					<?php
					echo "<font color='#FF0000'>", $creds*$_POST['ber_hour'];
					?>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='8'>
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