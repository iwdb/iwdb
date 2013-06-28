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

doc_title('Kampfbasen');

// aktuelle Spielerauswahl ermitteln
$params['playerSelection'] = getVar('playerSelection');

// Auswahlarray zusammenbauen
$playerSelectionOptions = array();
$playerSelectionOptions['(Alle)'] = '(Alle)';
$playerSelectionOptions += getAllyAccTypesSelect() + getAllyTeamsSelect();

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
		    AND $db_tb_schiffe.schiff=7) AS 'numKBalpha',
		 (SELECT $db_tb_schiffe.anzahl
		  FROM $db_tb_schiffe
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
		    AND $db_tb_schiffe.schiff=72) AS 'numKBbeta',
		 (SELECT $db_tb_schiffe.anzahl
		  FROM $db_tb_schiffe
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
		    AND $db_tb_schiffe.schiff=100) AS 'numKBgamma',
		 
		 (SELECT COUNT($db_tb_scans.coords)
		  FROM $db_tb_scans
		  WHERE $db_tb_scans.user=$db_tb_user.id
		    AND $db_tb_scans.objekt='Kampfbasis') AS 'base'";
$sql .= " FROM $db_tb_user";
$sql .= " WHERE " . sqlPlayerSelection($params['playerSelection']);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);

// Spielerauswahl Dropdown erstellen
echo "<div class='playerSelectionbox'>";
echo "Auswahl: ";
echo makeField(
    array(
         "type"   => 'select',
         "values" => $playerSelectionOptions,
         "value"  => $params['playerSelection'],
         "onchange" => "location.href='index.php?action=m_kampfbasen&amp;playerSelection='+this.options[this.selectedIndex].value",
    ), 'playerSelection'
);
echo '</div><br>';

?>
<table class='tablesorter-blue' {sortlist: [[0,0]]}>
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
				KBs<br>aufgestellt
			</th>
			<th>
                <abbr title="KB Alpha / KB Beta / KB Gamma">KBs<br>im Acc</abbr>
			</th>
			<th>
				Diff Soll
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
	while ($row = $db->db_fetch_array($result)) {
	?>
	
		<tr>
			<td>
				<?php echo $row['user']; ?>
			</td>
			<td>
				<?php echo $row['typ']; ?>
			</td>
			<td>
				<?php
				if (!empty($row['research']) OR (!empty($row['count']))) {
                    echo "<span class='doc_green'>erforscht</span>";
				} else {
                    echo "<span class='doc_red'>nicht erforscht</span>";
				}
				?>
			</td>
			<td>
				<?php
				if (!empty($row['count'])) {
					echo "Stufe " . $row['count'];
				} else if (!empty($row['research'])) {
					echo "<span class='doc_red'>Keine</span>";
				} else {
					echo "<span class='doc_red'>-</span>";
				}
				?>
			</td>
			<td>
				<?php
                $abbrstring = $row['base'] . ' von ' . ($row['count'] + 2);
                echo "<abbr title='$abbrstring'>";
                echo $row['base'] . "/" . ($row['count'] + 2);
                echo '</abbr>';
				?>
			</td>
			<td>
                <?php
                $abbrstring = '';
                if (!empty($row['numKBalpha'])) {
                    $numKBalpha = $row['numKBalpha'];
                    $abbrstring .= $row['numKBalpha'] . 'x KB Alpha, ';
                } else {
                    $numKBalpha = 0;
                }
                if (!empty($row['numKBbeta'])) {
                    $numKBbeta = $row['numKBbeta'];
                    $abbrstring .= $row['numKBbeta'] . 'x KB Beta, ';
                } else {
                    $numKBbeta = 0;
                }
                if (!empty($row['numKBgamma'])) {
                    $numKBgamma = $row['numKBgamma'];
                    $abbrstring .= $row['numKBgamma'] . 'x KB Gamma, ';
                } else {
                    $numKBgamma = 0;
                }
                if (!empty($abbrstring)) {
                    $abbrstring = substr($abbrstring, 0, -2) . " = " . ($numKBalpha + $numKBbeta + $numKBgamma) . ' gesamt';
                } else {
                    $abbrstring = 'keine';
                }
                echo "<abbr title='$abbrstring'>";
                echo $numKBalpha . '/' . $numKBbeta . '/' . $numKBgamma;
                echo '</abbr>';
                ?>
			</td>
			<td>
				<?php
                $diffSoll = (($numKBalpha + $numKBbeta + $numKBgamma) + $row['base'] - ($row['count'] + 2));
                if ($diffSoll < 0) {
                    echo "<span class='doc_red'>$diffSoll</span>";
                } else {
                    echo $diffSoll;
                }
                ?>
			</td>
		</tr>
	
	<?php
	}
	?>
	</tbody>
</table>
<br>