<?php
/*****************************************************************************
 * m_gebaeudeuebersicht.php                                                  *
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
 * Autor: [GILDE]Thella (icewars@thella.de)                                  *
 *                                                                           *
 * Entwicklerforum/Repo:                                              *
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
$modulname = "m_gebaeudeuebersicht";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Gebäudeübersicht";

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
$moduldesc = "Zeigt die Gebäudeübersicht an";

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
    global $modultitle, $modulstatus, $_POST;

    $actionparameters = "";
    insertMenuItem($_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparameters);
    //
    // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
    //
    // 	insertMenuItem( $_POST['menu'], ($_POST['submenu']+1), "Titel2", "hc", "&weissichnichtwas=1" ); 
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
Global $db_tb_user, $db_tb_gebaeude_spieler, $db_tb_gebaeude, $db_tb_scans;

// Titelzeile
doc_title('Gebäudeübersicht');

//Gebäudedaten holen
$sql = "SELECT `name`, `id`, `bild` FROM `{$db_tb_gebaeude}`;";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $buildings[$row['name']] = array(
        "id"   => $row['id'],
        "bild" => $row['bild']
    );
}

// aktuelle Spielerauswahl ermitteln
$params['playerSelection'] = getVar('playerSelection');

// Auswahlarray zusammenbauen
$playerSelectionOptions = array();
$playerSelectionOptions['(Alle)'] = '(Alle)';
$playerSelectionOptions += getAllyAccTypesSelect() + getAllyTeamsSelect() + getAllyAccs();

// Abfrage ausführen
$sql = "SELECT $db_tb_gebaeude_spieler.coords_gal,
		 $db_tb_gebaeude_spieler.coords_sys,
		 $db_tb_gebaeude_spieler.coords_planet,
		 $db_tb_gebaeude_spieler.kolo_typ,
		 $db_tb_gebaeude_spieler.building,
		 $db_tb_gebaeude_spieler.count,
		 $db_tb_gebaeude_spieler.time,
		(SELECT user FROM $db_tb_scans
		 WHERE $db_tb_scans.coords_gal=$db_tb_gebaeude_spieler.coords_gal
		   AND $db_tb_scans.coords_sys=$db_tb_gebaeude_spieler.coords_sys
		   AND $db_tb_scans.coords_planet=$db_tb_gebaeude_spieler.coords_planet) AS 'user',
		(SELECT planetenname FROM $db_tb_scans
		 WHERE $db_tb_scans.coords_gal=$db_tb_gebaeude_spieler.coords_gal
		   AND $db_tb_scans.coords_sys=$db_tb_gebaeude_spieler.coords_sys
		   AND $db_tb_scans.coords_planet=$db_tb_gebaeude_spieler.coords_planet) AS 'planet',
		(SELECT category FROM $db_tb_gebaeude
		 WHERE $db_tb_gebaeude.name=$db_tb_gebaeude_spieler.building) AS 'category',
		(SELECT inactive FROM $db_tb_gebaeude
		 WHERE $db_tb_gebaeude.name=$db_tb_gebaeude_spieler.building) AS 'inactive'";
$sql .= " FROM $db_tb_gebaeude_spieler";
$sql .= ",$db_tb_user";
$sql .= " WHERE $db_tb_user.id=user AND $db_tb_gebaeude_spieler.count!='0' AND $db_tb_gebaeude_spieler.kolo_typ='Kolonie'";
$sql .= " AND " . sqlPlayerSelection($params['playerSelection']);
if (!$user_fremdesitten) {
    $sql .= " AND " . $db_tb_user . ".allianz='" . $user_allianz . "'";
}
$sql .= " ORDER BY category,user,coords_gal,coords_sys,coords_planet";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);

// Abfrage auswerten
$categories           = array();
$categories_buildings = array();
$data                 = array();
while ($row = $db->db_fetch_array($result)) {
    if (!$row['inactive']) {
        $categories[$row['category']]                                                                                          = true;
        $categories_buildings[$row['category']][$row['building']]                                                              = true;
        $data[$row['category']][$row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet']]['user']           = $row['user'];
        $data[$row['category']][$row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet']]['planet']         = $row['planet'];
        $data[$row['category']][$row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet']]['time']           = $row['time'];
        $data[$row['category']][$row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet']][$row['building']] = $row['count'];
    }
}

// Spielerauswahl Dropdown erstellen
echo "Auswahl: ";
echo makeField(
    array(
         "type"   => 'select',
         "values" => $playerSelectionOptions,
         "value"  => $params['playerSelection'],
         "onchange" => "location.href='index.php?action=m_gebaeudeuebersicht&amp;playerSelection='+this.options[this.selectedIndex].value",
    ), 'playerSelection'
);
echo '<br>';

foreach ($categories as $category => $value) {
	echo "<br>";
	?>
	<table class="tablesorter-blue" style="width: 95%;">
		<?php
		$count = count($categories_buildings[$category]);
		?>
		<caption><?php echo $category ?></caption>
		<thead>
			<tr>
				<th>
					<b>Spieler</b>
				</th>
				<th>
					<b>Koords</b>
				</th>
				<th>
					<b>Planet</b>
				</th>
				<?php
				foreach ($categories_buildings[$category] as $building => $value) {
					?>
					<th>
						<?php
						if (isset($buildings[$building])) {
							$image = $buildings[$building]['bild'];
							$id    = $buildings[$building]['id'];
						} else {
							$image = 'blank';
							$id    = 0;
						}
                        echo "<abbr title='$building'>";
                        echo "<a href='index.php?action=m_building&show_building=" . $id . "'>";
                        echo "<img src='" . GEBAEUDE_BILDER_PATH . $image . ".jpg' width='50' height='50' alt='" . $building . "'>";
                        echo "</a>";
                        echo "</abbr>";
						?>
					</th>
				<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($data[$category] as $coords => $planet_buildings) {
			$color = getScanAgeColor($planet_buildings['time']);
			?>
			
				<tr>
					<td style='background-color: <?php echo $color ?>'>
						<?php
						echo $planet_buildings['user'];
						?>
					</td>
					<td>
						<?php
						echo $coords;
						?>
					</td>
					<td>
						<?php
						echo $planet_buildings['planet'];
						?>
					</td>
					<?php
					foreach ($categories_buildings[$category] as $building => $value) {
						?>
						<td>
							<?php
							if (isset($planet_buildings[$building])) {
								echo $planet_buildings[$building];
							} else {
							    echo "";
							}
							?>
						</td>
					<?php
					}
					?>
				</tr>
			
		<?php
		}
		?>
	</tbody>
	</table>
    <br>
<?php
}
?>