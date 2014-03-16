<?php
/*****************************************************************************
 * m_allychange.php                                                          *
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

if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig fuer die Benennung der zugehuerigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung fuer 
//    eine Installation ueber das Menue
//
$modulname = "m_allychange";

//****************************************************************************
//
// -> Menuetitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Allianzwechsler";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul ueber die Navigation 
//    ausfuehren darf. Muegliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "zeigt Allianzwechsel der Spieler";

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
    $menuetitel       = "Allianzwechsler";
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
// Dieser Abschnitt wird nur ausgefuehrt wenn das Modul mit dem Parameter 
// "install" aufgerufen wurde. Beispiel des Aufrufs: 
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" natuerlich deinen Server angeben und default 
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
    // ausgefuehrt werden.
    return;
}

//***************************hier gehts los***************************************
global $db, $db_tb_spieler;

// Titelzeile
echo '<abbr title="Hier kann man sehen, welche Spieler in letzter Zeit die Allianz gewechselt haben">';
doc_title($modultitle);
echo '</abbr><br>';

//Daten von
$sql_updated = "SELECT MAX(`playerupdate_time`) AS updated FROM `{$db_tb_spieler}`;";
$result = $db->db_query($sql_updated);
$playerdata = $db->db_fetch_array($result);
$playerupdatetime = $playerdata['updated'];
if (empty($playerdata)) {
    doc_message('keine Daten vorhanden');
} else {
    echo '<div class="textsmall">Daten von ' . strftime(CONFIG_DATETIMEFORMAT, $playerupdatetime) . '</div><br>';
    ?>
    <table class="tablesorter-blue" style="width: 80%;">
        <thead>
            <tr>
                <th>Spieler</th>
                <th>von Allianz</th>
                <th>zu Allianz</th>
                <th>Zeitpunkt</th>
            </tr>
		</thead>
		<tbody>
        <?php
        // letzten 50 Allywechsel abfragen
        $sql = "SELECT `name`, `fromally`, `toally`, `time` FROM `{$db_tb_spielerallychange}` ORDER BY `time` DESC LIMIT 0,50";
        $result = $db->db_query($sql);

        // Abfrage auswerten
        while ($row = $db->db_fetch_array($result)) {
            echo '<tr>';
            echo ' <td>';
            echo '  <a href="index.php?action=showgalaxy&amp;user=' . urlencode($row['name']) . '&amp;exact=1">' . $row['name'] . '</a>';
            echo ' </td>';
            echo ' <td>';
            echo '  <a href="index.php?action=m_allystats&allianz=' . $row['fromally'] . '">' . $row['fromally'] . '</a>';
            echo ' </td>';
            echo ' <td>';
            echo '  <a href="index.php?action=m_allystats&allianz=' . $row['toally'] . '">' . $row['toally'] . '</a>';
            echo ' </td>';
            echo ' <td>';
            echo strftime(CONFIG_DATETIMEFORMAT, $row['time']);
            echo ' </td>';
            echo '</tr>';
        }
        ?>
		</tbody>
    </table>
<?php
}
?>