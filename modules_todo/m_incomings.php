<?php
/*****************************************************************************
 * m_incomings.php                                                           *
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
$modulname = "m_incomings";

//****************************************************************************
//
// -> Titel des Moduls
//
$modultitle = "Incomings";

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
$moduldesc = "Anzeige der Incomings (Sondierung/Angriff) auf die eigene Allianz";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    global $db, $db_prefix;

    $sqlscript = array(
        "CREATE TABLE IF NOT EXISTS `{$db_prefix}incomings` (
        `koords_to` varchar(11) NOT NULL,
        `name_to` varchar(50) NOT NULL,
        `allianz_to` varchar(50) NOT NULL,
        `koords_from` varchar(11) NOT NULL,
        `name_from` varchar(50) NOT NULL,
        `allianz_from` varchar(50) NOT NULL,
        `art` varchar(100) NOT NULL COMMENT 'Angriff oder Sondierung',
        `timestamp` int(10) unsigned NOT NULL COMMENT 'Zeitstempel Sondierung',
        PRIMARY KEY (`timestamp`,`koords_to`, `koords_from` , `art`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Tabelle für Incomings';
        ",
    );
    foreach ($sqlscript as $sql) {
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    echo "<div class='system_success'>Installation: Datenbankänderungen = <b>OK</b></div>";

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
    $menuetitel       = "Incomings #incomings"; // -> Menütitel in der Navigation, #incomings wird gegen die Anzahl ersetzt
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
// for the configuration file
//
function workInstallConfigString()
{
    /*  global $config_gameversion;
      return
        "\$v04 = \" <div class=\\\"doc_lightred\\\">(V " . $config_gameversion . ")</div>\";";
    */
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase()
{
    global $db, $db_tb_incomings;

    $sqlscript = array(
        "DROP TABLE " . $db_tb_incomings . ";",
    );

    foreach ($sqlscript as $sql) {
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    echo "<div class='system_success'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";

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

    echo "<h2>Installationsarbeiten am Modul " . $modulname . " (" . $_REQUEST['was'] . ")</h2>\n";

    include("./includes/menu_fn.php");

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

global $db, $db_prefix, $db_tb_incomings, $db_tb_user;

// Titelzeile
doc_title('Incomings');
echo "Anzeige der laufenden Sondierungen und Angriffe auf uns";
echo " 	 <br />\n";
echo " 	 <br />\n";

//Löschen der Einträge in der Tabelle incomings, es sollen nur aktuelle Sondierungen und Angriffe eingetragen sein
//ToDo : evtl Trennung Sondierung und Angriffe, damit die Sondierungen früher entfernt sind
$sql = "DELETE FROM " . $db_tb_incomings . " WHERE timestamp<" . (CURRENT_UNIX_TIME - 20 * 60);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not delete incomings information.', '', __FILE__, __LINE__, $sql);

$sql = "SELECT * FROM " . $db_tb_incomings . " WHERE art = 'Sondierung (Schiffe/Def/Ress)' OR art = 'Sondierung (Gebäude/Ress)' ORDER BY timestamp ASC";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);

$data = array();

//Tabelle für die Sondierungen	
?>
<table class='table_hovertable' style='width:95%'>
	<caption>Sondierungen</caption>
	<thead>
		<tr>
			<th>
				Opfer
			</th>
			<th>
				Zielplanet
			</th>
			<th>
				Pösewicht
			</th>
			<th>
				Ausgangsplanet
			</th>
			<th>
				Zeitpunkt
			</th>
			<th>
				Art der Sondierung
			</th>
			<th>
				gesaved
			</th>
			<th>
				recalled
			</th>
		</tr>
	</thead>
	
	<?php
	while ($row = $db->db_fetch_array($result)) {
	?>
	<tbody>
		<tr>
			<td>
				<?php 
				$sitterlogin=CreateSitterlogin($row['name_to']);
				echo "<a href='$sitterlogin' target='_blank'><img src='bilder/user-login.gif' alt='L' title='Einloggen'></a>";
				echo "&emsp;" . $row['name_to'];
				?>
			</td>
			<td>
				<?php 
				$objekt = GetObjectByCoords($row['koords_to']);
				if ($objekt == 'Kolonie') {
					echo "<img src='bilder/kolo.png'>";
				} else if ($objekt == 'Sammelbasis') {
					echo "<img src='bilder/ress_basis.png'>";
				} else if ($objekt == 'Artefaktbasis') {
					echo "<img src='bilder/artefakt_basis.png'>";
				} else if ($objekt == 'Kampfbasis') {
					echo "<img src='bilder/kampf_basis.png'>";
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
					echo "<img src='bilder/kolo.png'>";
				} else if ($objekt == 'Sammelbasis') {
					echo "<img src='bilder/ress_basis.png'>";
				} else if ($objekt == 'Artefaktbasis') {
					echo "<img src='bilder/artefakt_basis.png'>";
				} else if ($objekt == 'Kampfbasis') {
					echo "<img src='bilder/kampf_basis.png'>";
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
				echo $row['art'];
				?>
			</td>
			<td>
				<?php 
				echo "<label><input type='checkbox' id='saved' value='1'";
				if ($row['gesaved'] == "1")
				{
					echo 'checked="checked"';
				}
				echo "'>";
				?></label>
			</td>
			<td>
				<?php 
				echo "<label><input type='checkbox' id='recalled' value='1'";
				if ($row['recalled'] == "1")
				{
					echo 'checked="checked"';
				}
				echo "'>";
				?></label>
			</td>
		</tr>
	</tbody>
	<?php
	}
	?>
</table>

<?php 
echo " 	 <br />\n";
echo " 	 <br />\n";

$sql = "SELECT * FROM " . $db_tb_incomings . " WHERE art = 'Angriff' ORDER BY timestamp DESC";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);

$data = array();

//Tabelle für die Angriffe	
?>
<table class='table_hovertable' style='width:95%'>
	<caption>Angriffe</caption>
	<thead>
		<tr>
			<th>
				Opfer
			</th>
			<th>
				Zielplanet
			</th>
			<th>
				Pösewicht
			</th>
			<th>
				Ausgangsplanet
			</th>
			<th>
				Zeitpunkt
			</th>
			<th>
				gesaved
			</th>
			<th>
				recalled
			</th>
		</tr>
	</thead>
	
	<?php
	while ($row = $db->db_fetch_array($result)) {
	?>
	<tbody>
		<tr>
			<td>
				<?php 
				$sitterlogin=CreateSitterlogin($row['name_to']);
				echo "<a href='$sitterlogin' target='_blank'><img src='bilder/user-login.gif' alt='L' title='Einloggen'></a>";
				echo "&emsp;" . $row['name_to'];
				?>
			</td>
			<td>
				<?php 
				$objekt = GetObjectByCoords($row['koords_to']);
				if ($objekt == 'Kolonie') {
					echo "<img src='bilder/kolo.png'>";
				} else if ($objekt == 'Sammelbasis') {
					echo "<img src='bilder/ress_basis.png'>";
				} else if ($objekt == 'Artefaktbasis') {
					echo "<img src='bilder/artefakt_basis.png'>";
				} else if ($objekt == 'Kampfbasis') {
					echo "<img src='bilder/kampf_basis.png'>";
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
					echo "<img src='bilder/kolo.png'>";
				} else if ($objekt == 'Sammelbasis') {
					echo "<img src='bilder/ress_basis.png'>";
				} else if ($objekt == 'Artefaktbasis') {
					echo "<img src='bilder/artefakt_basis.png'>";
				} else if ($objekt == 'Kampfbasis') {
					echo "<img src='bilder/kampf_basis.png'>";
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
				echo "<label><input type='checkbox' id='saved' value='1'";
				if ($row['gesaved'] == "1")
				{
					echo 'checked="checked"';
				}
				echo "'>";
				?></label>
			</td>
			<td>
				<?php 
				echo "<label><input type='checkbox' id='recalled' value='1'";
				if ($row['recalled'] == "1")
				{
					echo 'checked="checked"';
				}
				echo "'>";
				?></label>
			</td>
		</tr>
	</tbody>
	<?php
	}
	?>
</table>


<table class='borderless'>
	<tr>
		<td>
			<?php
			echo "<img src='bilder/kolo.png'> = Kolonie";
			?>
		</td>
		<td>
			<?php
			echo "<img src='bilder/ress_basis.png'> = Ressourcensammelbasis";
			?>
		</td>
		<td>
			<?php
			echo "<img src='bilder/artefakt_basis.png'> = Artefaktsammelbasis";
			?>
		</td>
		<td>
			<?php
			echo "<img src='bilder/kampf_basis.png'> = Kampfbasis";
			?>
		</td>
	</tr>
</table>

<script src="javascript/jquery-1.9.0.min.js"></script>
<script>
function performAjaxUpdate() {
    $.ajax({
    	url: 'm_incomings_update.php',
        method: POST,
        data: {
        	val: $(this).val()
        },
        success: function() {
            alert("Status erfolgreich geändert!");
        }
    });
    return false;
}
        
jQuery(":checkbox").change(function(){
	$(":checkbox").change(performAjaxUpdate);
});
</script>


<?php 
/**
 * Sitterlogin generieren
 *
 */
function CreateSitterlogin($user) {
	
	global $db, $db_tb_user;
	
	$sql = "SELECT `sitterpwd` FROM `{$db_tb_user}` WHERE `id` = '$user';";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
      
    $url = 'http://icewars.de/index.php?action=login&name=' . urlencode($user);
    $url .= '&pswd=' . $row['sitterpwd'];
    $url .= '&serverskin=1';
    $url .= '&serverskin_typ=3';
    $url .= '&sitter=1&ismd5=1&submit=1';
    
    return $url;
   	}
 ?>