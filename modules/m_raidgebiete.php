<?php
/*****************************************************************************
 * m_raidgebiete.php                                                         *
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
$modulname = "m_raidgebiete";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Raidgebiete";

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
$moduldesc = "Zeigt Infos über das Hasiversum, welcher Raider wann wo raidet";

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
    $menuetitel       = "Raidgebiete";
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

doc_title('Aufteilung des Hasiversums für die Raider');

global $db, $db_tb_user, $db_tb_raidgebiet;

$kw = date('W',time());

$sql = "DELETE FROM `{$db_tb_raidgebiet}` WHERE `kw`<'".($kw-2)."'";
$result = $db->db_query($sql);

?>
<table class='tablesorter-blue'>
	<thead>
		<tr class='center'>
			<th data-sorter="false" colspan="13">
				<b>letzte Kalenderwoche : <?php echo $kw-1; ?></b>
			</th>
		</tr>
		<tr>
			<th data-sorter="false">
				<b>Art</b>
			</th>
			<th data-sorter="false">
				<b>Gal 1</b>
			</th>
			<th data-sorter="false">
				<b>Gal 2</b>
			</th>
			<th data-sorter="false">
				<b>Gal 3</b>
			</th>
			<th data-sorter="false">
				<b>Gal 4</b>
			</th>
			<th data-sorter="false">
				<b>Gal 5</b>
			</th>
			<th data-sorter="false">
				<b>Gal 6</b>
			</th>
			<th data-sorter="false">
				<b>Gal 7</b>
			</th>
			<th data-sorter="false">
				<b>Gal 8</b>
			</th>
			<th data-sorter="false">
				<b>Gal 9</b>
			</th>
			<<th data-sorter="false">
				<b>Gal 10</b>
			</th>
			<th data-sorter="false">
				<b>Gal 11</b>
			</th>
			<th data-sorter="false">
				<b>Gal 12</b>
			</th>
		</tr>
	</thead>
	<tbody>
		
		<?php
		$sql_prevkw = "SELECT * FROM `{$db_tb_raidgebiet}` WHERE `kw`='" . ($kw-1) . "'";
		$result_prevkw = $db->db_query($sql_prevkw);
		$row_prevkw = $db->db_fetch_array($result_prevkw);
		?>
		<tr>
			<td style="background-color: #CAE1FF">
				<b>normales Raiden</b>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g1'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g2'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g3'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g4'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g5'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g6'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g7'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g8'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g9'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g10'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g11'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['1g12'];
				?>
			</td>
		</tr>
		<tr>
			<td style="background-color: #CAE1FF">
				<b>Inaktivenfarming</b>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g1'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g2'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g3'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g4'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g5'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g6'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g7'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g8'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g9'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g10'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g11'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['2g12'];
				?>
			</td>
		</tr>
		<tr>
			<td style="background-color: #CAE1FF">
				<b>KB-Raids</b>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g1'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g2'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g3'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g4'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g5'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g6'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g7'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g8'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g9'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g10'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g11'];
				?>
			</td>
			<td>
				<?php
				echo $row_prevkw['3g12'];
				?>
			</td>
		</tr>
	</tbody>
</table>

<table class='tablesorter-blue'>
	<thead>
		<tr class='center'>
			<th data-sorter="false" colspan="13">
				<b>aktuelle Kalenderwoche : <?php echo $kw; ?></b>
			</th>
		</tr>
		<tr>
			<th data-sorter="false">
				<b>Art</b>
			</th>
			<th data-sorter="false">
				<b>Gal 1</b>
			</th>
			<th data-sorter="false">
				<b>Gal 2</b>
			</th>
			<th data-sorter="false">
				<b>Gal 3</b>
			</th>
			<th data-sorter="false">
				<b>Gal 4</b>
			</th>
			<th data-sorter="false">
				<b>Gal 5</b>
			</th>
			<th data-sorter="false">
				<b>Gal 6</b>
			</th>
			<th data-sorter="false">
				<b>Gal 7</b>
			</th>
			<th data-sorter="false">
				<b>Gal 8</b>
			</th>
			<th data-sorter="false">
				<b>Gal 9</b>
			</th>
			<th data-sorter="false">
				<b>Gal 10</b>
			</th>
			<th data-sorter="false">
				<b>Gal 11</b>
			</th>
			<th data-sorter="false">
				<b>Gal 12</b>
			</th>
		</tr>
	</thead>
	<tbody>
		
		<?php
		$sql_aktkw = "SELECT * FROM `{$db_tb_raidgebiet}` WHERE `kw`='" . ($kw) . "'";
		$result_aktkw = $db->db_query($sql_aktkw);
		$row_aktkw = $db->db_fetch_array($result_aktkw);
		?>
		<tr>
			<td style="background-color: #CAE1FF">
				<b>normales Raiden</b>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g1'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g2'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g3'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g4'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g5'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g6'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g7'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g8'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g9'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g10'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g11'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['1g12'];
				?>
			</td>
		</tr>
		<tr>
			<td style="background-color: #CAE1FF">
				<b>Inaktivenfarming</b>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g1'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g2'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g3'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g4'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g5'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g6'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g7'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g8'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g9'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g10'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g11'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['2g12'];
				?>
			</td>
		</tr>
		<tr>
			<td style="background-color: #CAE1FF">
				<b>KB-Raids</b>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g1'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g2'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g3'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g4'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g5'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g6'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g7'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g8'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g9'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g10'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g11'];
				?>
			</td>
			<td>
				<?php
				echo $row_aktkw['3g12'];
				?>
			</td>
		</tr>
	</tbody>
</table>

<form method="POST" action="index.php?action=m_raidgebiete" enctype="multipart/form-data" id='raidgebiet'>

<?php
$sql_nextkw = "SELECT * FROM `{$db_tb_raidgebiet}` WHERE `kw`='" . ($kw+1) . "'";
$result_nextkw = $db->db_query($sql_nextkw);
$row_nextkw = $db->db_fetch_array($result_nextkw);
?>

<table class='tablesorter-blue'>
	<thead>
		<tr class='center'>
			<th data-sorter="false" colspan="13">
				<b>nächste Kalenderwoche : <?php echo $kw+1; ?></b>
			</th>
		</tr>
		<tr>
			<th data-sorter="false">
				<b>Art</b>
			</th>
			<th data-sorter="false">
				<b>Gal 1</b>
			</th>
			<th data-sorter="false">
				<b>Gal 2</b>
			</th>
			<th data-sorter="false">
				<b>Gal 3</b>
			</th>
			<th data-sorter="false">
				<b>Gal 4</b>
			</th>
			<th data-sorter="false">
				<b>Gal 5</b>
			</th>
			<th data-sorter="false">
				<b>Gal 6</b>
			</th>
			<th data-sorter="false">
				<b>Gal 7</b>
			</th>
			<th data-sorter="false">
				<b>Gal 8</b>
			</th>
			<th data-sorter="false">
				<b>Gal 9</b>
			</th>
			<th data-sorter="false">
				<b>Gal 10</b>
			</th>
			<th data-sorter="false">
				<b>Gal 11</b>
			</th>
			<th data-sorter="false">
				<b>Gal 12</b>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td style="background-color: #CAE1FF">
				<b>normales Raiden</b>
			</td>
			<td>
				<input type="name" name="g_1_1" value="<?php echo $row_nextkw['1g1']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_2" value="<?php echo $row_nextkw['1g2']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_3" value="<?php echo $row_nextkw['1g3']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_4" value="<?php echo $row_nextkw['1g4']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_5" value="<?php echo $row_nextkw['1g5']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_6" value="<?php echo $row_nextkw['1g6']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_7" value="<?php echo $row_nextkw['1g7']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_8" value="<?php echo $row_nextkw['1g8']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_9" value="<?php echo $row_nextkw['1g9']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_10" value="<?php echo $row_nextkw['1g10']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_11" value="<?php echo $row_nextkw['1g11']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_1_12" value="<?php echo $row_nextkw['1g12']; ?>" style="width: 5em;">
			</td>
		</tr>
		<tr>
			<td style="background-color: #CAE1FF">
				<b>Inaktivenfarming</b>
			</td>
			<td>
				<input type="name" name="g_2_1" value="<?php echo $row_nextkw['2g1']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_2" value="<?php echo $row_nextkw['2g2']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_3" value="<?php echo $row_nextkw['2g3']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_4" value="<?php echo $row_nextkw['2g4']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_5" value="<?php echo $row_nextkw['2g5']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_6" value="<?php echo $row_nextkw['2g6']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_7" value="<?php echo $row_nextkw['2g7']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_8" value="<?php echo $row_nextkw['2g8']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_9" value="<?php echo $row_nextkw['2g9']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_10" value="<?php echo $row_nextkw['2g10']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_11" value="<?php echo $row_nextkw['2g11']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_2_12" value="<?php echo $row_nextkw['2g12']; ?>" style="width: 5em;">
			</td>
		</tr>
		<tr>
			<td style="background-color: #CAE1FF">
				<b>KB-Raids</b>
			</td>
			<td>
				<input type="name" name="g_3_1" value="<?php echo $row_nextkw['3g1']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_2" value="<?php echo $row_nextkw['3g2']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_3" value="<?php echo $row_nextkw['3g3']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_4" value="<?php echo $row_nextkw['3g4']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_5" value="<?php echo $row_nextkw['3g5']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_6" value="<?php echo $row_nextkw['3g6']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_7" value="<?php echo $row_nextkw['3g7']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_8" value="<?php echo $row_nextkw['3g8']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_9" value="<?php echo $row_nextkw['3g9']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_10" value="<?php echo $row_nextkw['3g10']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_11" value="<?php echo $row_nextkw['3g11']; ?>" style="width: 5em;">
			</td>
			<td>
				<input type="name" name="g_3_12" value="<?php echo $row_nextkw['3g12']; ?>" style="width: 5em;">
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr class='center'>
			<th colspan="13">
				<input type="submit" value="aktualisieren" name="aktualisieren">
			</th>
		</tr>
	</tfoot>
</table>
</form>
<?php
if (GetVar('aktualisieren')) {
	$g_1_1	= GetVar('g_1_1');
	$g_1_2 	= GetVar('g_1_2');
	$g_1_3 	= GetVar('g_1_3');
	$g_1_4 	= GetVar('g_1_4');
	$g_1_5 	= GetVar('g_1_5');
	$g_1_6 	= GetVar('g_1_6');
	$g_1_7 	= GetVar('g_1_7');
	$g_1_8 	= GetVar('g_1_8');
	$g_1_9 	= GetVar('g_1_9');
	$g_1_10 = GetVar('g_1_10');
	$g_1_11 = GetVar('g_1_11');
	$g_1_12 = GetVar('g_1_12');
	$g_2_1	= GetVar('g_2_1');
	$g_2_2 	= GetVar('g_2_2');
	$g_2_3 	= GetVar('g_2_3');
	$g_2_4 	= GetVar('g_2_4');
	$g_2_5 	= GetVar('g_2_5');
	$g_2_6 	= GetVar('g_2_6');
	$g_2_7 	= GetVar('g_2_7');
	$g_2_8 	= GetVar('g_2_8');
	$g_2_9 	= GetVar('g_2_9');
	$g_2_10 = GetVar('g_2_10');
	$g_2_11 = GetVar('g_2_11');
	$g_2_12 = GetVar('g_2_12');
	$g_3_1	= GetVar('g_3_1');
	$g_3_2 	= GetVar('g_3_2');
	$g_3_3 	= GetVar('g_3_3');
	$g_3_4 	= GetVar('g_3_4');
	$g_3_5 	= GetVar('g_3_5');
	$g_3_6 	= GetVar('g_3_6');
	$g_3_7 	= GetVar('g_3_7');
	$g_3_8 	= GetVar('g_3_8');
	$g_3_9 	= GetVar('g_3_9');
	$g_3_10 = GetVar('g_3_10');
	$g_3_11 = GetVar('g_3_11');
	$g_3_12 = GetVar('g_3_12');
	
	$data = array(
		'kw'	=>	$kw+1,
		'1g1'	=>	$g_1_1,
		'1g2'	=>	$g_1_2,
		'1g3'	=>	$g_1_3,
		'1g4'	=>	$g_1_4,
		'1g5'	=>	$g_1_5,
		'1g6'	=>	$g_1_6,
		'1g7'	=>	$g_1_7,
		'1g8'	=>	$g_1_8,
		'1g9'	=>	$g_1_9,
		'1g10'	=>	$g_1_10,
		'1g11'	=>	$g_1_11,
		'1g12'	=>	$g_1_12,
		'2g1'	=>	$g_2_1,
		'2g2'	=>	$g_2_2,
		'2g3'	=>	$g_2_3,
		'2g4'	=>	$g_2_4,
		'2g5'	=>	$g_2_5,
		'2g6'	=>	$g_2_6,
		'2g7'	=>	$g_2_7,
		'2g8'	=>	$g_2_8,
		'2g9'	=>	$g_2_9,
		'2g10'	=>	$g_2_10,
		'2g11'	=>	$g_2_11,
		'2g12'	=>	$g_2_12,
		'3g1'	=>	$g_3_1,
		'3g2'	=>	$g_3_2,
		'3g3'	=>	$g_3_3,
		'3g4'	=>	$g_3_4,
		'3g5'	=>	$g_3_5,
		'3g6'	=>	$g_3_6,
		'3g7'	=>	$g_3_7,
		'3g8'	=>	$g_3_8,
		'3g9'	=>	$g_3_9,
		'3g10'	=>	$g_3_10,
		'3g11'	=>	$g_3_11,
		'3g12'	=>	$g_3_12
	);
	$db->db_insertupdate($db_tb_raidgebiet, $data);
}
?>