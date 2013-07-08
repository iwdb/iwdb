<?php
/*****************************************************************************
 * m_robotermining.php                                                       *
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
$modulname = "m_robotermining";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Robotermining";

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
$moduldesc = "Zeigt Informationen zu Ressourcensammelbasen und Roboterminenkomplexen an";

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
doc_title('Robotermining');

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
		    AND $db_tb_research2user.rid=38) AS 'research',
		
		 (SELECT DISTINCT MAX($db_tb_gebaeude_spieler.count)
		  FROM $db_tb_gebaeude_spieler
		  WHERE $db_tb_gebaeude_spieler.user=$db_tb_user.id
		    AND $db_tb_gebaeude_spieler.building='Robominerzentrale' HAVING MAX($db_tb_gebaeude_spieler.count)) AS 'numRMZ',
			
		 (SELECT COUNT($db_tb_scans.coords)
		  FROM $db_tb_scans
		  WHERE $db_tb_scans.user=$db_tb_user.id
		    AND $db_tb_scans.objekt='Sammelbasis') AS 'base',
		 
		 (SELECT $db_tb_schiffe.anzahl
		  FROM $db_tb_schiffe
		  WHERE $db_tb_schiffe.user=$db_tb_user.id
		    AND $db_tb_schiffe.schiff=18) AS 'numRB',
		 
		 (SELECT SUM($db_tb_lager.eisen_prod)
		  FROM $db_tb_lager
		  WHERE $db_tb_lager.user=$db_tb_user.id
		    AND $db_tb_lager.kolo_typ='Sammelbasis') AS 'eisen',
		 
		 (SELECT SUM($db_tb_lager.chem_prod)
		  FROM $db_tb_lager
		  WHERE $db_tb_lager.user=$db_tb_user.id
		    AND $db_tb_lager.kolo_typ='Sammelbasis') AS 'chemie',
		 
		 (SELECT SUM($db_tb_lager.eis_prod)
		  FROM $db_tb_lager
		  WHERE $db_tb_lager.user=$db_tb_user.id
		    AND $db_tb_lager.kolo_typ='Sammelbasis') AS 'eis'";

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
         "onchange" => "location.href='index.php?action=m_robotermining&amp;playerSelection='+this.options[this.selectedIndex].value",
    ), 'playerSelection'
);
echo '</div><br>';

?>
<table data-sortlist="[[0,0]]" class='tablesorter-blue'>
    <thead>
    <tr>
        <th>
            Spieler
        </th>
        <th>
            Typ
        </th>
        <th>
            Robotermining
        </th>
        <th>
            Robominerzentrale<br>Stufe max
        </th>
        <th>
            Sammelbasen<br>aufgestellt
        </th>
        <th>
            Sammelbasen<br>im Acc
        </th>
        <th>
            Eisen /h
        </th>
        <th>
            Chemie /h
        </th>
        <th>
            Eis /h
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
                if (!empty($row['research'])) {
                    echo "<span class='doc_green'>erforscht</span>";
                } elseif (!empty($row['numRMZ'])) {
                    echo "<span class='doc_green'>erforscht</span>";
                } elseif (!empty($row['base'])) {
                    echo "<span class='doc_green'>erforscht</span>";
                } elseif (!empty($row['eisen']) OR !empty($row['chemie']) OR !empty($row['eis'])) {
                    echo "<span class='doc_green'>erforscht</span>";
                } else {
                    echo "<span class='doc_red'>nicht erforscht</span>";
                }
                ?>
            </td>
            <td>
                <?php
                if (!empty($row['numRMZ'])) {
                    if ($row['numRMZ'] < 3) {
                        echo "<span class='doc_red'>" . $row['numRMZ'] . "</span>";
                    } else {
                        echo "<span class='doc_green'>" . $row['numRMZ'] . "</span>";
                    }
                } elseif (!empty($row['eisen']) OR !empty($row['chemie']) OR !empty($row['eis'])) {
                    echo "<span class='doc_red'>?</span>";
                } else if (!empty($row['research'])) {
                    echo "<span class='doc_red'>Keine</span>";
                } else {
                    echo "<span class='doc_red'>-</span>";
                }
                ?>
            </td>
            <td>
                <?php
                if (!empty($row['base'])) {
                    if ($row['base'] < 3) {
                        echo "<span class='doc_red'>" . $row['base'] . "</span>";
                    } else {
                        echo "<span class='doc_green'>" . $row['base'] . "</span>";
                    }
                } elseif (!empty($row['eisen']) OR !empty($row['chemie']) OR !empty($row['eis'])) {
                    echo "<span class='doc_red'>?</span>";
                } else {
                    echo "<span class='doc_red'>-</span>";
                }
                ?>
            </td>
            <td>
                <?php
                if (!empty($row['numRB'])) {
                    echo $row['numRB'];
                } else {
                    echo "<span class='doc_red'>-</span>";
                }
                ?>
            </td>
            <td>
                <?php
                if (!empty($row['eisen'])) {
                    echo number_format($row['eisen'], 0, "", ".");
                } else {
                    echo "-";
                }
                ?>
            </td>
            <td>
                <?php
                if (!empty($row['chemie'])) {
                    echo number_format($row['chemie'], 0, "", ".");
                } else {
                    echo "-";
                }
                ?>
            </td>
            <td>
                <?php
                if (!empty($row['eis'])) {
                    echo number_format($row['eis'], 0, "", ".");
                } else {
                    echo "-";
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