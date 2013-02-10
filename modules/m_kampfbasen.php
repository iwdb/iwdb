<?php
/*****************************************************************************
 * m_kampfbasen.php                                                          *
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
 * Entwicklerforum/Repo wenden:                                              *
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
$modulname = "m_kampfbasen";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Kampfbasen";

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
$moduldesc = "Zeigt Informationen zu Kampfbasen und Kampfbasenverwaltung an";

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

// Titelzeile
doc_title('Kampfbasen');

// Stammdaten abfragen
$config = array();

$sql = "SELECT * FROM $db_tb_gebaeude";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $buildings[$row['name']] = array(
        "id"   => $row['id'],
        "bild" => $row['bild']
    );
}

// Spieler und Teams abfragen
$users                    = array();
$teams                    = array();
$teams['(Alle)']          = '(Alle)';
$teams['(Nur Fleeter)']   = '(Nur Fleeter)';
$teams['(Nur Cash Cows)'] = '(Nur Cash Cows)';
$teams['(Nur Buddler)']   = '(Nur Buddler)';
$sql                      = "SELECT * FROM " . $db_tb_user;
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $users[$row['id']] = $row['id'];
    if (!empty($row['buddlerfrom'])) {
        $teams[$row['buddlerfrom']] = $row['buddlerfrom'];
    }
}
$config['users'] = $users;
$config['teams'] = $teams;

// Parameter ermitteln
$params['team'] = getVar('team');

// Abfrage ausführen
$sql = "SELECT  $db_tb_user.id AS 'user',
		  $db_tb_user.budflesol AS 'typ',
	 	 (SELECT $db_tb_research2user.userid
		  FROM $db_tb_research2user
		  WHERE $db_tb_research2user.userid=$db_tb_user.id
		    AND $db_tb_research2user.rid=36) AS 'research',
		 
		 (SELECT DISTINCT MAX($db_tb_gebaeude_spieler.count)
		  FROM $db_tb_gebaeude_spieler
		  WHERE $db_tb_gebaeude_spieler.user=$db_tb_user.id
		    AND $db_tb_gebaeude_spieler.building='Kampfbasenverwaltung' HAVING MAX($db_tb_gebaeude_spieler.count)) AS 'count',
		 
		 (SELECT $db_tb_schiffe.anzahl
		  FROM $db_tb_schiffe
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
		    AND $db_tb_schiffe.schiff=7) AS 'alpha',
		 (SELECT $db_tb_schiffe.anzahl
		  FROM $db_tb_schiffe
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
		    AND $db_tb_schiffe.schiff=72) AS 'beta',
		 (SELECT $db_tb_schiffe.anzahl
		  FROM $db_tb_schiffe
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
		    AND $db_tb_schiffe.schiff=100) AS 'gamma',
		 
		 (SELECT COUNT($db_tb_scans.coords)
		  FROM $db_tb_scans
		  WHERE $db_tb_scans.user=$db_tb_user.id
		    AND $db_tb_scans.objekt='Kampfbasis') AS 'base'";
$sql .= " FROM $db_tb_user";
if (isset($params['team'])) {
    if ($params['team'] == '(Nur Fleeter)') {
        $sql .= " WHERE " . $db_tb_user . ".budflesol='Fleeter'";
    } elseif ($params['team'] == '(Nur Cash Cows)') {
        $sql .= " WHERE " . $db_tb_user . ".budflesol='Cash Cow'";
    } elseif ($params['team'] == '(Nur Buddler)') {
        $sql .= " WHERE " . $db_tb_user . ".budflesol='Buddler'";
    } elseif ($params['team'] != '(Alle)') {
        $sql .= " WHERE " . $db_tb_user . ".buddlerfrom='" . $params['team'] . "'";
    }
}
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);

// Auswahlfelder
echo "<form method='POST' action='' enctype='multipart/form-data'>";
echo "<p align='center'>";
echo "Team: ";
echo "<select name='team'>";
foreach ($config['teams'] as $team) {
    echo "<option value='$team'";
    if ($team == $params['team']) {
        echo " selected";
    }
    echo ">$team</option>";
}
echo "</select>";
echo "</p>";
echo "<input type='submit' name='submit' value='anzeigen'/>";
echo "</form>";
echo "</p>";

?>
<table class="table_hovertable">
	<caption>Kampfbasen</caption>
	<thead>
		<tr>
			<th>
				Spieler
			</th>
			<th>
				Typ
			</th>
			<th>
				orbitale Dockingsysteme
			</th>
			<th>
				Kampfbasenverwaltung
			</th>
			<th>
				Kampfbasen
			</th>
			<th>
				# Basen
			</th>
			<th>
				Diff Soll
			</th>
		</tr>
	</thead>
	
	<?php
	while ($row = $db->db_fetch_array($result)) {
	?>
	<tbody>
		<tr>
			<td>
				<?php echo $row['user']; ?>
			</td>
			<td>
				<?php echo $row['typ']; ?>
			</td>
			<td>
				<?php
				if (!empty($row['research'])) {
					echo "erforscht";
				} else {
					echo "-";
				}
				?>
			</td>
			<td>
				<?php
				if (!empty($row['count'])) {
					echo "Stufe " . $row['count'];
				} else if (!empty($row['research'])) {
					echo "Keine";
				} else {
					echo "-";
				}
				?>
			</td>
			<td>
				<?php
				echo $row['base'] . "/" . ($row['count'] + 2);
				?>
			</td>
			<td>
				<?php
				if (!empty($row['alpha'])) {
					$one = $row['alpha'];
				} else {
					$one = 0;
				}
				if (!empty($row['beta'])) {
					$two = $row['beta'];
				} else {
					$two = 0;
				}
				if (!empty($row['gamma'])) {
					$three = $row['gamma'];
				} else {
					$three = 0;
				}

				echo $one . "/" . $two . "/" . $three;
				?>
			</td>
			<td>
				<?php
				echo (($one + $two + $three) + $row['base'] - ($row['count'] + 2));
				?>
			</td>
		</tr>
	</tbody>
	<?php
	}
	?>
</table>