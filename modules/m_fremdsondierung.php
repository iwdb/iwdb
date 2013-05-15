<?php
/*****************************************************************************
 * m_fremdsondierung.php                                                     *
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
$modulname = "m_fremdsondierung";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Fremdsondierung";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation 
//    ausführen darf. Mögliche Werte:
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "admin";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc = "Anzeige der Sondierungen (Schiffe/Gebs) auf die eigene Allianz";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    /*
    global $db, $db_prefix;

    $sqlscript = array(
        "CREATE TABLE " . $db_prefix . "neuername
        (
        );",
    );
    foreach($sqlscript as $sql) {
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    */
    echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modultitle, $modulstatus;

    $menu    = getVar('menu');
    $submenu = getVar('submenu');

    $actionparamters = "";
    insertMenuItem($menu, $submenu, $modultitle, $modulstatus, $actionparamters);
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
function workInstallConfigString()
{
    /*  global $config_gameversion;
      return
        "\$v04 = ' <div class=\\'doc_lightred\\'>(V " . $config_gameversion . ")</div>';";
    */
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase()
{
    /*
    global $db, $db_tb_neuername;

    $sqlscript = array(
        "DROP TABLE " . $db_tb_neuername . ";",
    );

    foreach($sqlscript as $sql) {
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }

    echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
    */
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
//
// -> Und hier beginnt das eigentliche Modul

// Titelzeile
doc_title('Fremdsondierung');
echo "Anzeige der Sondierungen auf uns in den letzten 14 Tagen";
echo " 	 <br />\n";
echo " 	 <br />\n";

$sql = "SELECT * FROM " . $db_tb_fremdsondierung . " WHERE timestamp >" . (CURRENT_UNIX_TIME - 14 * DAY) . " ORDER BY timestamp DESC";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

$data = array();

?>
<table class='tablesorter'>
	<thead>
		<tr>
			<th>
				<b>Opfer</b>
			</th>
			<th>
				<b>Zielplanet</b>
			</th>
			<th>
				<b>Pösewicht</b>
			</th>
			<th>
				<b>Ausgangsplanet</b>
			</th>
			<th>
				<b>Zeitpunkt</b>
			</th>
			<th>
				<b>Art</b>
			</th>
			<th>
				<b>erfolgreich?</b>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
	while ($row = $db->db_fetch_array($result)) {
	?>
	
		<tr>
			<td>
				<?php echo $row['name_to']; ?>
			</td>
			<td>
				<?php
				$objekt = GetObjectByCoords($row['koords_to']);
				if ($objekt == 'Kolonie') {
					echo "<img src='".BILDER_PATH."kolo.png'>";
				} else if ($objekt == 'Sammelbasis') {
					echo "<img src='".BILDER_PATH."ress_basis.png'>";
				} else if ($objekt == 'Artefaktbasis') {
					echo "<img src='".BILDER_PATH."artefakt_basis.png'>";
				} else if ($objekt == 'Kampfbasis') {
					echo "<img src='".BILDER_PATH."kampf_basis.png'>";
				}
				echo $row['koords_to'];
				?>
			</td>
			<td>
				<?php
				if (!empty($row['allianz_from'])) {
					echo ($row['name_from'] . " [" . $row['allianz_from'] . "]");
				} else {
					echo $row['name_from'];
				}
				?>
			</td>
			<td>
				<?php
				$objekt = GetObjectByCoords($row['koords_from']);
				if ($objekt == 'Kolonie') {
					echo "<img src='".BILDER_PATH."kolo.png'>";
				} else if ($objekt == 'Sammelbasis') {
					echo "<img src='".BILDER_PATH."ress_basis.png'>";
				} else if ($objekt == 'Artefaktbasis') {
					echo "<img src='".BILDER_PATH."artefakt_basis.png'>";
				} else if ($objekt == 'Kampfbasis') {
					echo "<img src='".BILDER_PATH."kampf_basis.png'>";
				}
				echo $row['koords_from'];
				?>
			</td>
			<td>
				<?php
				echo strftime(CONFIG_DATETIMEFORMAT, $row['timestamp']);
				?>
			</td>
			<td>
				<?php
				if ($row['sondierung_art']=='schiffe')
					echo "Schiffe";
				else if ($row['sondierung_art']=='gebaeude')
					echo "Gebäude";
				?>
			</td>
			<td>
				<?php
				if ($row['erfolgreich'] == '0') {
					echo "nein";
				} else {
					echo "ja";
				}
				?>
			</td>
		</tr>
	
	<?php
	}
	?>
	</tbody>
</table>

<table class='borderless'>
	<tr>
		<td>
			<?php
			echo "<img src='".BILDER_PATH."kolo.png'> = Kolonie";
			?>
		</td>
		<td>
			<?php
			echo "<img src='".BILDER_PATH."ress_basis.png'> = Ressourcensammelbasis";
			?>
		</td>
		<td>
			<?php
			echo "<img src='".BILDER_PATH."artefakt_basis.png'> = Artefaktsammelbasis";
			?>
		</td>
		<td>
			<?php
			echo "<img src='".BILDER_PATH."kampf_basis.png'> = Kampfbasis";
			?>
		</td>
	</tr>
</table>