<?php
/*****************************************************************************
 * m_kbsuche.php                                                             *
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
$modulname = "m_kbsuche";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "KB Suche";

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
$moduldesc = "Suche nach bestimmten KBs";

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
    $menuetitel       = "KB Suche";
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

doc_title('KB Suche');

global $db, $db_tb_raidview, $db_tb_spieler;

if (!isset($_POST['namen']) OR empty($_POST['namen'])) {
		$_POST['namen']='';
}

echo 	"<form method=\"POST\" action=\"index.php?action=" . $modulname .
		"&sid=" . $sid . "\" enctype=\"multipart/form-data\" onsubmit=\"return $(this).validate(jQueryFormLang);\">\n";
?>
	<table class='tablesorter-blue center' id='suche' style='width:60%'>
		<thead>
			<tr>
				<th data-sorter="false">
				</th>
				<th data-sorter="false">
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<b>Spieler</b>
				</td>
				<td>
					<input type="name" id="namen" name="namen" value="<?php echo $_POST['namen']; ?>">
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="2">
					<input type="submit" value="Suchen" id="suchen">
				</th>
			</tr>
		</tfoot>
	</table>
</form>	
<br>
<br>
	
<?php

$sql = "SELECT `coords`, `date`, `link`, `geraided`, `user`  FROM `{$db_tb_raidview}` WHERE (`geraided`='" . $_POST['namen'] ."' AND `user`!='')";
$result = $db->db_query($sql)
       or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

?>
<table data-sortlist='[[0,1]]' class='tablesorter-blue' id='ergebnisse' style='width:95%'>
	<thead>
		<tr>
			<th class="sorter-attr-unixtime">
				<b>Datum</b>
			</th>
			<th>
				<b>Koords</b>
			</th>
			<th>
				<b>Opfer</b>
			</th>
			<th>
				<b>Allianz</b>
			</th>
			<th>
				<b>Übeltäter</b>
			</th>
			<th>
				<b>Link</b>
			</th>
		</tr>
	</thead>
	</tbody>
	<?php				

	while ($row = $db->db_fetch_array($result)) {
	
		?>
		<tr>
			<td data-unixtime='<?php echo $row['date'] ?>'>
				<?php
				echo strftime(CONFIG_DATETIMEFORMAT, $row['date']);
				?>
			</td>
			<td>
				<?php
				echo $row['coords'];
				?>
			</td>
			<td>
				<?php
				echo $row['geraided'];
				?>
			</td>
			<td>
				<?php
				echo getAllianceByUser($row['geraided']);
				?>
			</td>
			<td>
				<?php
				echo $row['user'];
				?>
			</td>
			<td>
				<?php
				echo "<a title='Link zum externen Kampfbericht' href='" . $row['link'] . "' target='_blank'><img src='".BILDER_PATH."kampf_basis.png'/></a>";
				?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>	