<?php
/*****************************************************************************
 * m_allystats.php                                                           *
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
$modulname = "m_allystats";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Allianz-Statistiken";

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
$moduldesc =
    "Das Allianzstatistiken-modul, zeigt Statistiken aller bekannter Allianzen zusammen und einzeln im Detail an.";

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

    global $modultitle, $modulstatus;

    $menu    = getVar('menu');
    $submenu = getVar('submenu');

    $actionparameters = "";
    insertMenuItem($menu, $submenu, $modultitle, $modulstatus, $actionparameters);

}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed
// for the configuration file
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

/* if (!@include("./config/".$modulname.".cfg.php")) {
	die( "Error:<br \><b>Cannot load ".$modulname." - configuration!</b>");
}
 */
//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul
global $db, $db_tb_allianzstatus, $db_tb_scans, $sid;
global $config_map_default_galaxy, $config_map_galaxy_min, $config_map_galaxy_max;

$galamin   = getVar('galamin');
$galamax   = getVar('galamax');
$gesamtmin = getVar('gesamtmin');

$gesamtmax = getVar('gesamtmax');
$order     = getVar('order');
if ((!isset($order)) or ($order == "")) {
    $order = "Kolonien";
}
$order2 = getVar('order2');
if ((!isset($order2)) or ($order2 == "")) {
    $order2 = "Spieler";
}

$showfrom = getVar('showfrom');
$showto   = getVar('showto');
$DBdata   = false;

$galamin = (is_numeric($galamin)) ? $galamin : $config_map_galaxy_min;
$galamin = ($galamin < $config_map_galaxy_min) ? $config_map_galaxy_min : $galamin;
$galamin = ($galamin > $config_map_galaxy_max) ? $config_map_galaxy_max : $galamin;

$galamax = (is_numeric($galamax)) ? $galamax : $config_map_galaxy_max;
$galamax = ($galamax < $config_map_galaxy_min) ? $config_map_galaxy_min : $galamax;
$galamax = ($galamax > $config_map_galaxy_max) ? $config_map_galaxy_max : $galamax;

if ($galamin > $galamax) {
    $temp    = $galamin;
    $galamin = $galamax;
    $galamax = $temp;
}

$gesamtmin = (is_numeric($gesamtmin)) ? $gesamtmin : 1; //Start-Position der allys im Hasiversum, ab wann in der gesamtliste angezeigt werden.
$gesamtmax = (is_numeric($gesamtmax)) ? $gesamtmax : 100; //End-Position der allys im Hasiversum, die in der gesamtliste angezeigt werden.

$showfrom = (is_numeric($showfrom) && ($showfrom >= 1) && ($showfrom <= $config_map_galaxy_max)) ? $showfrom : ($config_map_default_galaxy - 3); //erste gala, die angezeigt wird
if ($showfrom <= 1) {
    $showfrom = 1;
}

$showto = (is_numeric($showto) && ($showto >= 1) && ($showto <= $config_map_galaxy_max)) ? $showto : ($config_map_default_galaxy + 3); //letzte gala, die angezeigt wird
if ($showto > $config_map_galaxy_max) {
    $showfrom = $config_map_galaxy_max;
}

$sql = "SELECT SQL_CACHE IF(`{$db_tb_scans}`.`allianz`='','<i>Solo</i>',`{$db_tb_scans}`.`allianz`) AS Allianz,
    COUNT( DISTINCT `user` ) AS 'Spieler',
    SUM(IF(`objekt` = 'Kolonie', 1,0)) AS Kolonien,
    SUM(IF(`objekt` = 'Kolonie' AND `typ` = 'Steinklumpen', 1,0)) AS KoloSteinklumpen,
    SUM(IF(`objekt` = 'Kolonie' AND `typ` = 'Gasgigant', 1,0)) AS KoloGasgiganten,
    SUM(IF(`objekt` = 'Kolonie' AND `typ` = 'Eisplanet', 1,0)) AS KoloEisplaneten,
    SUM(IF(`objekt` = 'Kolonie' AND `typ` = 'Asteroid', 1,0)) AS KoloAstroiden,
    SUM(IF(`objekt` = 'Kampfbasis', 1,0)) AS Kampfbasen,
    SUM(IF(`objekt` = 'Sammelbasis', 1,0)) AS Sammelbasen,
    `prefix_allianzstatus`.`status`
    FROM `{$db_tb_scans}` LEFT JOIN `{$db_tb_allianzstatus}`
     ON `{$db_tb_allianzstatus}`.`allianz` = `{$db_tb_scans}`.`allianz`
     WHERE `coords_gal` >= " . $galamin . " AND `coords_gal` <= " . $galamax . " AND `user` != ''
     GROUP BY Allianz WITH ROLLUP";

$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

$i = 0;
while ($rowGal = $db->db_fetch_array($result)) {
    if ((int)$rowGal['Spieler'] === 0) {//prevent division by zero warnings
        $rowGal['KpS'] = '';
    } else {
        $rowGal['KpS'] = sprintf("%5.2f", ((int)$rowGal['Kolonien'] / (int)$rowGal['Spieler']));
    }

    if (empty($rowGal['Allianz'])) { //Allianz '' enthällt alle Spieler
        $rowGal['Allianz'] = '<i>alle</i>';
        $rowGal['status']  = '';
    }

    $Allies[] = array(
        'Allytag'          => (string)$rowGal['Allianz'],
        'Spieler'          => (int)$rowGal['Spieler'],
        'status'           => (string)$rowGal['status'],
        'Kolonien'         => (int)$rowGal['Kolonien'],
        'KpS'              => (string)$rowGal['KpS'],
        'KoloSteinklumpen' => (int)$rowGal['KoloSteinklumpen'],
        'KoloGasgiganten'  => (int)$rowGal['KoloGasgiganten'],
        'KoloEisplaneten'  => (int)$rowGal['KoloEisplaneten'],
        'KoloAstroiden'    => (int)$rowGal['KoloAstroiden'],
        'Kampfbasen'       => (int)$rowGal['Kampfbasen'],
        'Sammelbasen'      => (int)$rowGal['Sammelbasen']
    );

    $i++;
}

if ($i === 0) {
    doc_message('Die werden noch gemei&szlig;elt. :(');
} else {

    foreach ($Allies as $key => $row) {
        $Allytag[$key]          = $row['Allytag'];
        $Spieler[$key]          = $row['Spieler'];
        $Kolonien[$key]         = $row['Kolonien'];
        $KpS[$key]              = $row['KpS'];
        $KoloSteinklumpen[$key] = $row['KoloSteinklumpen'];
        $KoloGasgiganten[$key]  = $row['KoloGasgiganten'];
        $KoloEisplaneten[$key]  = $row['KoloEisplaneten'];
        $KoloAstroiden[$key]    = $row['KoloAstroiden'];
        $Kampfbasen[$key]       = $row['Kampfbasen'];
        $Sammelbasen[$key]      = $row['Sammelbasen'];
    }

    doc_title("Allianz-Statistiken");

    switch ($order) {
        case "Allianz":
            $firstsort = $Allytag;
            break;
        case "Spieler":
            $firstsort = $Spieler;
            break;
        case "Kolonien":
            $firstsort = $Kolonien;
            break;
        case "KpS":
            $firstsort = $KpS;
            break;
        case "Steinies":
            $firstsort = $KoloSteinklumpen;
            break;
        case "Astis":
            $firstsort = $KoloAstroiden;
            break;
        case "Gasis":
            $firstsort = $KoloGasgiganten;
            break;
        case "Eisis":
            $firstsort = $KoloEisplaneten;
            break;
        case "KBs":
            $firstsort = $Kampfbasen;
            break;
        case "RBs":
            $firstsort = $Sammelbasen;
            break;
        default:
            $firstsort = $Kolonien;
    }

    switch ($order2) {
        case "Allianz":
            $secondsort = $Allytag;
            break;
        case "Spieler":
            $secondsort = $Spieler;
            break;
        case "Kolonien":
            $secondsort = $Kolonien;
            break;
        case "KpS":
            $secondsort = $KpS;
            break;
        case "Steinies":
            $secondsort = $KoloSteinklumpen;
            break;
        case "Astis":
            $secondsort = $KoloAstroiden;
            break;
        case "Gasis":
            $secondsort = $KoloGasgiganten;
            break;
        case "Eisis":
            $secondsort = $KoloEisplaneten;
            break;
        case "KBs":
            $secondsort = $Kampfbasen;
            break;
        case "RBs":
            $secondsort = $Sammelbasen;
            break;
        default:
            $secondsort = $Spieler;
    }
    array_multisort($firstsort, SORT_DESC, $secondsort, SORT_DESC, $Allies);

    echo "<div class='doc_centered'>\n";
    echo "<form name='frm'>\n";

    echo "<input type='hidden' name='action' value='$modulname'>\n";
    echo "<p>";
    echo "Statistiken anzeigen für Gala <input type='text' name='galamin' value='$galamin' size='4'>&nbsp;\n";
    echo "bis <input type='text' name='galamax' value='$galamax' size='4'>&nbsp;\n";
    echo "</p>\n<p>";
    echo "Hasiversumsliste von Ally <input type='text' name='gesamtmin' value='$gesamtmin' size='4'>&nbsp;\n";
    echo "bis <input type='text' name='gesamtmax' value='$gesamtmax' size='4'>&nbsp;\n";
    echo "</p>\n<p>";

    echo "1. Sortierung nach <select name='order' size=1>\n";

    echo "<option value='Allianz'\n";
    if ($order == "Allianz") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Ally Tag";

    echo "<option value='Spieler'";
    if ($order == "Spieler") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Spieler";

    echo "<option value='Kolonien'";
    if ($order == "Kolonien") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolonien";

    echo "<option value='KpS'";
    if ($order == "KpS") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolonien/Spieler";

    echo "<option value='Steinies'";
    if ($order == "Steinies") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolos auf Steinies";

    echo "<option value='Astis'";
    if ($order == "Astis") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolos auf Asties";

    echo "<option value='Gasis'";
    if ($order == "Gasis") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolos auf Gasis";

    echo "<option value='Eisis'";
    if ($order == "Eisis") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolos auf Eisis";

    echo "<option value='KBs'";
    if ($order == "KBs") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kampfbasen";

    echo "<option value='RBs'";
    if ($order == "RBs") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Ressbasen";
    echo "</select></p>\n<p>";

    echo "2. Sortierung nach <select name='order2' size=1>\n";

    echo "<option value='Allianz'\n";
    if ($order2 == "Allianz") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Ally Tag";

    echo "<option value='Spieler'";
    if ($order2 == "Spieler") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Spieler";

    echo "<option value='Kolonien'";
    if ($order2 == "Kolonien") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolonien";

    echo "<option value='KpS'";
    if ($order2 == "KpS") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolonien/Spieler";

    echo "<option value='Steinies'";
    if ($order2 == "Steinies") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolos auf Steinies";

    echo "<option value='Astis'";
    if ($order2 == "Astis") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolos auf Asties";

    echo "<option value='Gasis'";
    if ($order2 == "Gasis") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolos auf Gasis";

    echo "<option value='Eisis'";
    if ($order2 == "Eisis") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kolos auf Eisis";

    echo "<option value='KBs'";
    if ($order2 == "KBs") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Kampfbasen";

    echo "<option value='RBs'";
    if ($order2 == "RBs") {
        echo " selected='selected'";
    }
    echo ">\n";
    echo "Ressbasen";
    echo "</select></p>\n<p>";

    echo "<input type='submit' value='anzeigen'>";
    echo "</p>\n<br \>";
    echo "</form>";
    echo "</div>";

    start_table();
    start_row("titlebg center", "style='width:95%' colspan='11'");

    if (($galamin == $config_map_galaxy_min) and ($galamax == $config_map_galaxy_max)) {
        echo "  <b>Bekanntes Hasiversum</b>\n";
    } else if ($galamin == $galamax) {
        echo "  <b>Galaxie " . $galamin . "</b>\n";
    } else {
        echo "  <b>Galaxien " . $galamin . " bis " . $galamax . "</b>\n";
    }

    next_row("windowbg2 center", "style='width:5%'");
    echo "<b>Rang</b>";

    next_cell("windowbg2 center", "style='width:10%'");
    echo "<b>Allianz</b>";

    next_cell("windowbg2 center", "style='width:8%'");
    echo "<b>Spieler</b>";

    next_cell("windowbg2 center", "style='width:8%'");
    echo "<b>Kolonien</b>";

    next_cell("windowbg2 center", "style='width:8%'");
    echo "<b>Kolonien&nbsp;/<br \>Spieler</b>";

    next_cell("windowbg2 center", "style='width:8%'");
    echo "<b>Kolos&nbsp;auf<br \>Steinies</b>";

    next_cell("windowbg2 center", "style='width:8%'");
    echo "<b>Kolos&nbsp;auf Asties&nbsp;&nbsp;&nbsp;</b>";

    next_cell("windowbg2 center", "style='width:8%'");
    echo "<b>Kolos&nbsp;auf<br \>Gasis</b>";

    next_cell("windowbg2 center", "style='width:8%'");
    echo "<b>Kolos&nbsp;auf<br \>Eisis&nbsp;&nbsp;</b>";

    next_cell("windowbg2 center", "style='width:8%'");
    echo "<b>KB</b>";

    next_cell("windowbg2 center", "style='width:8%'");
    echo "<b>SB</b>";

    $i = 0;
    foreach ($Allies as $ally => $Alliestats) {
//	    echo "ally: $ally; allystats: $Alliestats<br />\n";

        $i++;
        if ($i >= $gesamtmin) {

            if ($Alliestats['Allytag'] == "<i>alle</i>") {
                $allylink = '<a href="index.php?action=allydetail&allianz=alle&amp;sid=' . $sid . '">' . $Alliestats['Allytag'] . '</a>';
            } elseif ($Alliestats['Allytag'] == "<i>Solo</i>") {
                $allylink = '<a href="index.php?action=allydetail&allianz=Solo&amp;sid=' . $sid . '">' . $Alliestats['Allytag'] . '</a>';
            } else {
                $allylink = '<a href="index.php?action=allydetail&allianz=' . $Alliestats['Allytag'] . '&amp;sid=' . $sid . '">' . $Alliestats['Allytag'] . '</a>';
            }


            if (!empty($Alliestats['status']) AND !empty($config_allianzstatus[$Alliestats['status']])) {
                $bgcolor = 'background-color:'.$config_allianzstatus[$Alliestats['status']].';';
            } else {
                $bgcolor = '';
            }
            if ($i % 2) {
                $style = "windowbg1 right";
                next_row("windowbg2 center", "style='width:5%; $bgcolor'");
            } else {
                $style = "windowbg3 right";
                next_row("windowbg2 center", "style='width:5%; $bgcolor'");
            }

            echo "$i.";

            next_cell($style, "style='width:10%; $bgcolor'");
            echo $allylink;

            next_cell($style, "style='width:8%; $bgcolor'");
            echo  $Alliestats['Spieler'];

            next_cell($style, "style='width:8%; $bgcolor'");
            echo  $Alliestats['Kolonien'];

            next_cell($style, "style='width:8%; $bgcolor'");
            echo  $Alliestats['KpS'];

            next_cell($style, "style='width:8%; $bgcolor'");
            echo  $Alliestats['KoloSteinklumpen'];

            next_cell($style, "style='width:8%; $bgcolor'");
            echo  $Alliestats['KoloAstroiden'];

            next_cell($style, "style='width:8%; $bgcolor'");
            echo  $Alliestats['KoloGasgiganten'];

            next_cell($style, "style='width:8%; $bgcolor'");
            echo  $Alliestats['KoloEisplaneten'];

            next_cell($style, "style='width:8%; $bgcolor'");
            echo  $Alliestats['Kampfbasen'];

            next_cell($style, "style='width:8%; $bgcolor'");
            echo  $Alliestats['Sammelbasen'];

        }

        if ($i == $gesamtmax) {
            break;
        }
    }

    end_row();
    end_table();
}