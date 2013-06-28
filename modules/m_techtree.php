<?php
/*****************************************************************************
 * m_techtree.php                                                            *
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
$modulname = "m_techtree";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "graph. Techtree";

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
// -> Beschreibung des Moduls, wie es in der Menue-Übersicht angezeigt wird.
//
$moduldesc =
    "Ein graphsicher Technoligiebaum, der jede Evolutionsstufe als Übersicht mit Forschungsverknüpfungen und Informationen anzeigt. Besonders Wert gelegt wurde auf die optische Integration in die AlliDB und schnelle Ladezeiten.";

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

    $actionparamters = "";
    insertMenuItem($_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparamters);
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
    return "";
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
//
// -> Und hier beginnt das eigentliche Modul

// -> Nachsehen ob der dynamische Techtree installiert ist.
if (file_exists("./config/m_research.cfg.php")) {

    // Array aller Evos ggf. anpassen
    $evoArray    = array();
    $evoArray[0] = array('img' => 'evo0.png');
    $evoArray[1] = array('img' => 'evo1.png', 'keyResearch' => 'Race into Space');
    $evoArray[2] = array('img' => 'evo2.png', 'keyResearch' => 'Interstellares Vordringen');
    $evoArray[3] = array('img' => 'evo3.png', 'keyResearch' => 'Aufnahme in die zivilisierten Welten');
    $evoArray[4] = array('img' => 'evo4.png', 'keyResearch' => 'Imperiale Gedanken');
    $evoArray[5] = array('img' => 'evo5.png', 'keyResearch' => 'Die Macht des Seins');
    $evoArray[7] = array('img' => 'evo7.png', 'keyResearch' => 'Verstehen der Zusammenhänge');

    $selectEvo = null;

    if (isset($_GET['selectEvo'])) {
        $selectEvo    = (int)$_GET['selectEvo'];
        $selectEvoImg = '';
        if (array_key_exists($selectEvo, $evoArray)) {
            $selectEvoImg = $evoArray[$selectEvo]['img'];
        } else {
            $selectEvo = null;
        }
    }

    if (is_null($selectEvo)) {

        $selectEvo    = 0;
        $selectEvoImg = '';

        foreach ($evoArray as $evoNumber => $evoData) {
            // -> Nach der ID für die Schlüsselforschungen suchen.
            if (!isset($evoData['keyResearch'])) {
                $selectEvo    = $evoNumber;
                $selectEvoImg = $evoData['img'];
                continue;
            }

            $sql = "SELECT ID FROM " . $db_tb_research . " WHERE name='" . $evoData['keyResearch'] . "';";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            $result1    = $db->db_fetch_array($result);
            $researchID = $result1["ID"];

            // Wenn vorhanden, nachsehen ob der User diese Forschung schon hat.
            if (!empty($researchID)) {
                $sql = "SELECT rid FROM " . $db_tb_research2user . " WHERE rid=" . $researchID . " AND userid='" . $user_sitterlogin . "'";
                $result = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
                $result2 = $db->db_fetch_array($result);
                if (!empty($result2["rid"])) {
                    $selectEvo    = $evoNumber;
                    $selectEvoImg = $evoData['img'];
                }
            }
        }
    }


    doc_title('Techtrees für Icewars');

    echo "<div class='doc_big_black'>";
    if ($selectEvo > 0) {
        echo "<a href='index.php?action=m_techtree&selectEvo=" . ($selectEvo - 1) . "'><b>&lt;&lt;</b></a>\n"; // <<
    }

    for ($evo = 0; $evo <= 7; $evo++) {

        if (array_key_exists($evo, $evoArray)) { //gibt es die Evo überhaupt?
            echo "<a href='index.php?action=m_techtree&selectEvo=" . $evo . "'>";
            if ($evo === $selectEvo) {
                echo "<b>[" . $evo . "]</b>";
            } else {
                echo $evo;
            }
            echo "</a>\n";
        }
    }

    if ($selectEvo < 7) {
        echo "<a href='index.php?action=m_techtree&selectEvo=" . ($selectEvo + 1) . "'><b>&gt;&gt;</b></a>\n"; // >>
    }
    echo "</div>";

    echo "<img src='" . TECHTREE_BILDER_PATH . $selectEvoImg . "'>";
    echo "<br>";
    echo "Danke an H.G. Blob für die Grafiken der Techtrees. :)";
}
?>