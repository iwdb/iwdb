<?php
/*****************************************************************************/
/* m_allystats.php                                                           */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* ========================================================================= */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* ========================================================================= */
/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */
/*****************************************************************************/
/* This program is free software; you can redistribute it and/or modify it   */
/* under the terms of the GNU General Public License as published by the     */
/* Free Software Foundation; either version 2 of the License, or (at your    */
/* option) any later version.                                                */
/*                                                                           */
/* This program is distributed in the hope that it will be useful, but       */
/* WITHOUT ANY WARRANTY; without even the implied warranty of                */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General */
/* Public License for more details.                                          */
/*                                                                           */
/* The GNU GPL can be found in LICENSE in this directory                     */
/*****************************************************************************/

/*****************************************************************************/
/* Dieses Modul dient der Anzeige der Allianzen und Gedöns                   */
/* für die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iwdb.de.vu                                   */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
    echo "Hacking attempt...!!";
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
$modultitle = "Allianzstatistiken";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation
//    ausfuehren darf. Moegliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Übersicht angezeigt wird.
//
$moduldesc =
    "Das Allianzstatistiken-Modul berechnet diverse Daten zu einer Allianz";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//
function workInstallDatabase()
{

    echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>n/V (also OK)</b></div>";
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
    return "";

}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//
function workUninstallDatabase()
{
    echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>n/V (also OK)</b></div>";
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
    // ausgefuehrt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

function getColor($tuedel)
{

    if ($tuedel['Kolonie'] == 0 && $tuedel['Sammelbasis'] == 0 && $tuedel['Kampfbasis'] == 0 && $tuedel['Artefaktbasis'] == 0) {
        return "#00FF00";
    } else if ($tuedel['Kolonie'] == 0 && ($tuedel['Sammelbasis'] != 0 || $tuedel['Kampfbasis'] != 0 || $tuedel['Artefaktbasis'] != 0)) {
        return "#00FFCC";
    }

    $i = (int)(60 * sqrt(0.5 * $tuedel['Kolonie']));
    $i += 150;
    $rot   = ($i < 256) ? $i : 255;
    $gruen = ($i < 256) ? 255 : 254 - ($i - 256);

    return ("#" . str_pad(dechex($rot), 2, "0", STR_PAD_LEFT) . str_pad(dechex($gruen), 2, "0", STR_PAD_LEFT) . "00");
}

global $sid;

//settings überprüfen und entsprechend setzen
$allianz = getVar('allianz') ? getVar('allianz') : "";
$maxplannis = 300;
$range = 25;
$fleeterschnitt = getVar('fschnitt') ? (float)getVar('fschnitt') : 1.5;
$n = 5;


doc_title("Allianz-Statistiken");

//Eingabeform für die Suche:
echo "<div class='doc_centered'>\n";
echo "<form name='frm'>\n";
echo "<input type='hidden' name='sid' value='$sid'>\n";
echo "<input type='hidden' name='action' value='$modulname'>\n";
echo "<p>";
echo "Allianztag: <input type='text' name='allianz' value='$allianz' size='20'>&nbsp;\n";
echo "</p>\n<p>";
echo "Spieler mit dem <input type='text' name='fschnitt' value='$fleeterschnitt' size='5'> fachen des Allyschnitts für Fleeter halten\n";
echo "</p>\n<p>";
echo "<input type='submit' value='suchen'>";
echo "</p>\n<br>";
echo "</form>";
echo "</div>";

//wenn Allianz angegeben, Ausgabe
if ($allianz != "") {

    //Punkteschnitt der Allianz berechnen/prüfen ob es die Allianz gibt
    $sql = "SELECT count(DISTINCT user) as usercount, sum(punkte) as pktsum FROM " . $db_tb_scans . " WHERE allianz like '$allianz'";
    $result = $db->db_query($sql)
        or error(
        GENERAL_ERROR,
        'Could not query config information.', '',
        __FILE__, __LINE__, $sql
    );
    $row = $db->db_fetch_array($result);
    if ($row['usercount'] > 0) {
        $punkteschnitt = $row['pktsum'] / $row['usercount'];
        $usercount     = $row['usercount'];
    }

    if (isset($punkteschnitt)) {

        //ausrechnen, ab welchem Punkteschnitt pro Plani wir einen Spieler für nen Fleeter halten
        $fleeterschnitt = $punkteschnitt * $fleeterschnitt;

        //user mit überdurchschnittlichen Punkten aus der DB holen, die könnten ja Fleeter sein
        $sql = "SELECT user, sum(punkte) AS pktsum FROM " . $db_tb_scans . " WHERE allianz like '$allianz' GROUP BY user";
        $result = $db->db_query($sql)
            or error(
            GENERAL_ERROR,
            'Could not query config information.', '',
            __FILE__, __LINE__, $sql
        );
        while ($row = $db->db_fetch_array($result)) {
            if ($row['pktsum'] > $fleeterschnitt) {
                $fleeters[$row['user']] = number_format($row['pktsum'], 0, ",", ".");
            } else {
                $buddlers[$row['user']] = number_format($row['pktsum'], 0, ",", ".");
            }
        }

        $allianzgals = array();
        //auslesen, in welchen Galas die Allianz vertreten ist
        $sql = "SELECT DISTINCT coords_gal FROM " . $db_tb_scans . " WHERE allianz like '$allianz' ORDER BY coords_gal";
        $result = $db->db_query($sql)
            or error(
            GENERAL_ERROR,
            'Could not query config information.', '',
            __FILE__, __LINE__, $sql
        );
        $row    = $db->db_fetch_array($result);
        $galmin = $row['coords_gal'];
        $galmax = $galmin;
        while ($row = $db->db_fetch_array($result)) {
            $galmax = $row['coords_gal'];
        }

        //Variablen initialisieren
        $steinis   = 0;
        $sgsteinis = 0;
        $eisis     = 0;
        $gasis     = 0;
        $astros    = 0;
        $kb        = 0;
        $rb        = 0;
        $ab        = 0;
        $pkte      = 0;

        $plannicount = Array(); //hier wird Krams für die Karte reingeschrieben
        for ($i = $galmin; $i <= $galmax; $i++) {
            for ($j = 1; $j <= ceil($maxplannis / $range); $j++) {
                $plannicount[$i][$j]['Kolonie']       = 0;
                $plannicount[$i][$j]['Sammelbasis']   = 0;
                $plannicount[$i][$j]['Kampfbasis']    = 0;
                $plannicount[$i][$j]['Artefaktbasis'] = 0;
            }
        }

        //alle Planis der Allianz aus der DB holen und verarbeiten:
        $sql = "SELECT coords, coords_gal, coords_sys, coords_planet, user, allianz, planetenname, punkte, typ, objekt FROM " . $db_tb_scans . " WHERE allianz like '$allianz' ORDER BY user, coords_gal, coords_sys, coords_planet ASC";
        $result = $db->db_query($sql)
            or error(
            GENERAL_ERROR,
            'Could not query config information.', '',
            __FILE__, __LINE__, $sql
        );
        $i = 0;
        while ($row = $db->db_fetch_array($result)) {
            $i++;

            if ($row['objekt'] == "Kolonie" && $row['typ'] == "Eisplanet") {
                $eisis++;
            }
            if ($row['objekt'] == "Kolonie" && $row['typ'] == "Steinklumpen") {
                ($row['coords_gal'] % 4 == 0) ? $sgsteinis++ : $steinis++;
            }
            if ($row['objekt'] == "Kolonie" && $row['typ'] == "Gasgigant") {
                $gasis++;
            }
            if ($row['objekt'] == "Kolonie" && $row['typ'] == "Asteroid") {
                $astros++;
            }
            if ($row['objekt'] == "Sammelbasis") {
                $rb++;
            }
            if ($row['objekt'] == "Kampfbasis") {
                $kb++;
            }
            if ($row['objekt'] == "Artefaktbasis") {
                $ab++;
            }

            $plannistring = "";
            switch ($row['typ']) {
                case "Steinklumpen" :
                    $plannistring .= "<span class='doc_black'>S";
                    break;
                case "Eisplanet" :
                    $plannistring .= "<span class='doc_blue'>E";

                    if ($row['objekt'] == "Kolonie") {
                        $spezpla[$row['coords_gal']]['Eisplanet']['user'][]   = $row['user'];
                        $spezpla[$row['coords_gal']]['Eisplanet']['coords'][] = " <a href='index.php?action=showplanet&coords=" . $row['coords'] . "&ansicht=auto&sid=" . $sid . "'>" . $row['coords'] . "</a>";
                        $spezpla[$row['coords_gal']]['Eisplanet']['pkte'][]   = $row['punkte'];
                    }
                    break;
                case "Asteroid" :
                    $plannistring .= "<span class='doc_red'>A";

                    if ($row['objekt'] == "Kolonie") {
                        $spezpla[$row['coords_gal']]['Asteroid']['user'][]   = $row['user'];
                        $spezpla[$row['coords_gal']]['Asteroid']['coords'][] = " <a href='index.php?action=showplanet&coords=" . $row['coords'] . "&ansicht=auto&sid=" . $sid . "'>" . $row['coords'] . "</a>";
                        $spezpla[$row['coords_gal']]['Asteroid']['pkte'][]   = $row['punkte'];
                    }
                    break;
                case "Gasgigant" :
                    $plannistring .= "<span class='doc_green'>G";

                    if ($row['objekt'] == "Kolonie") {
                        $spezpla[$row['coords_gal']]['Gasgigant']['user'][]   = $row['user'];
                        $spezpla[$row['coords_gal']]['Gasgigant']['coords'][] = " <a href='index.php?action=showplanet&coords=" . $row['coords'] . "&ansicht=auto&sid=" . $sid . "'>" . $row['coords'] . "</a>";
                        $spezpla[$row['coords_gal']]['Gasgigant']['pkte'][]   = $row['punkte'];
                    }
                    break;
                default :
                    $plannistring .= "<span class='doc_black'>N";
            }

            $plannistring .= " <a href='index.php?action=showplanet&coords=" . $row['coords'] . "&ansicht=auto&sid=" . $sid . "'>" . $row['coords'] . "</a>";
            if ($row['objekt'] == "Kolonie") {
                $plannistring .= "</span>";
                $points[$row['user']][] = number_format($row['punkte'], 0, ",", ".");
            }

            if (isset($fleeters[$row['user']])) {
                $fleeter[$row['user']][$row['objekt']][] = $plannistring;

            } else {
                $buddler[$row['user']][$row['objekt']][] = $plannistring;
            }

            if ($row['coords_sys'] > 300) {
                $row['coords_sys'] = 300;
            } //dafür sorgen, dass gaaaaanz hinten liegende systeme in das letzte intervall gesteckt werden
            $inrange = ceil($row['coords_sys'] / $range);
            if (isset($plannicount[(int)$row['coords_gal']][$inrange])) {
                $plannicount[$row['coords_gal']][$inrange][$row['objekt']]++;
            }

            $pkte += $row['punkte'];
        }

        $plancount = $eisis + $astros + $gasis + $steinis + $sgsteinis;
        $pkteplan  = $pkte / $plancount;
        $pktemem   = $pkte / $usercount;
        $plannimem = $plancount / $usercount;

        //ausgabe
        //allgemeines
        start_table();
        start_row("titlebg", "style='width:95%' align='center' colspan='4'");
        echo "<b>Allgemeine Informationen</b>";
        next_row("windowbg2", "style='width:25%' align='left'");
        echo "Allianz";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "<b>$allianz</b>";
        next_cell("windowbg2", "style='width:25%' align='left'");
        echo "Planetenpunkte";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo number_format($pkte, 0, ",", ".");
        next_row("windowbg2", "style='width:25%' align='left'");
        echo "Mitgliederzahl";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo $usercount;
        next_cell("windowbg2", "style='width:25%' align='left'");
        echo "Punkte pro Mitglied";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo number_format($pktemem, 2, ",", ".");
        next_row("windowbg2", "style='width:25%' align='left'");
        echo "Planeten pro Mitglied";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo number_format($plannimem, 2, ",", ".");
        next_cell("windowbg2", "style='width:25%' align='left'");
        echo "Punkte pro Planet";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo number_format($pkteplan, 2, ",", ".");
        next_row("windowbg2", "style='width:25%' align='left'");
        echo "Kolonien";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "$plancount";
        next_cell("windowbg2", "style='width:25%' align='left'");
        echo "Kolonisierte Asteroiden";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "$astros";
        next_row("windowbg2", "style='width:25%' align='left'");
        echo "Steinklumpen in Startgalas";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "$steinis";
        next_cell("windowbg2", "style='width:25%' align='left'");
        echo "Kolonisierte Eisplaneten";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "$eisis";
        next_row("windowbg2", "style='width:25%' align='left'");
        echo "Steinklumpen in Chaosgalas";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "$sgsteinis";
        next_cell("windowbg2", "style='width:25%' align='left'");
        echo "Kolonisierte Gasgiganten";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "$gasis";
        next_row("windowbg2", "style='width:25%' align='left'");
        echo "Sammelbasen";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "$rb";
        next_cell("windowbg2", "style='width:25%' align='left'");
        echo "Kampfbasen";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "$kb";
        next_row("windowbg2", "style='width:25%' align='left'");
        echo "Artefaktbasen";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "$ab";
        next_cell("windowbg2", "style='width:25%' align='left'");
        echo "";
        next_cell("windowbg1", "style='width:25%' align='left'");
        echo "";
        end_row();
        end_table();
        echo "<br>";

        //Karte
        start_table();
        start_row("titlebg", "style='width:95%' align='center' colspan='" . (ceil($maxplannis / $range) + 1) . "'");
        echo "<b>Allianzkarte</b>";
        next_row("windowbg2", "style='width:5%' align='left'");
        echo " ";
        $width = floor(95 / ($maxplannis / $range));
        for ($j = 1; $j <= ceil($maxplannis / $range) - 1; $j++) {
            next_cell("windowbg2", "style='width:$width%' align='center'");
            echo $j * $range - $range + 1 . "-" . $j * $range;
        }
        next_cell("windowbg2", "style='width:$width%' align='center'");
        echo ceil($maxplannis / $range) * $range - $range + 1 . "-end";
        foreach ($plannicount as $gala => $pcount) {
            next_row("windowbg2", "align='left'");
            echo "G $gala";
            foreach ($plannicount[$gala] as $inrange => $count) {
                $color = getColor($plannicount[$gala][$inrange]);
                next_cell("windowbg1", "align='center' style='background-color:" . $color . "'");
                echo $plannicount[$gala][$inrange]['Kolonie'] . "/" . $plannicount[$gala][$inrange]['Sammelbasis'] . "/" . $plannicount[$gala][$inrange]['Kampfbasis'] . "/" . $plannicount[$gala][$inrange]['Artefaktbasis'];
            }
        }
        end_row();
        end_table();
        echo "<b>Kolonien/Sammelbasen/Kampfbasen/Artefaktbasen</b><br>";

        //Planiliste
        echo "<br>";
        start_table();
        start_row("titlebg", "style='width:95%' align='center' colspan='5'");
        echo "<b>Spieler mit hohem Punkteschnitt</b> (mögliche Fleeter)";
        next_row("windowbg2", "style='width:22%' align='center'");
        echo "Username";
        next_cell("windowbg2", "style='width:36%' align='center'");
        echo "Kolonien";
        next_cell("windowbg2", "style='width:21%' align='center'");
        echo "Ressbasen";
        next_cell("windowbg2", "style='width:21%' align='center'");
        echo "Kampfbasen";
        next_cell("windowbg2", "style='width:21%' align='center'");
        echo "Artefaktbasen";
        if (isset($fleeter)) {
            foreach ($fleeter as $username => $plannis) {
                next_row("windowbg3", "style='width:22%' align='center'");
                echo "<b>$username</b><br>" . $fleeters[$username] . " pkte";
                next_cell("windowbg1", "style='width:36%' align='left'");
                if (isset($fleeter[$username]['Kolonie'])) {
                    start_table(100);
                    foreach ($fleeter[$username]['Kolonie'] as $key => $planni) {
                        start_row("windowbg1", "style='width:55%' align='left'");
                        echo "$planni";
                        next_cell("windowbg1", "style='width:45%' align='right'");
                        echo $points[$username][$key] . " pkte";
                        end_row();
                    }
                    end_table();
                } else {
                    echo "-";
                }
                next_cell("windowbg1", "style='width:21%' align='left'");
                if (isset($fleeter[$username]['Sammelbasis'])) {
                    start_table(100);
                    foreach ($fleeter[$username]['Sammelbasis'] as $planni) {
                        start_row("windowbg1", "style='width:100%' align='left'");
                        echo "$planni<br>";
                        end_row();
                    }
                    end_table();
                } else {
                    echo "-";
                }
                next_cell("windowbg1", "style='width:21%' align='left'");
                if (isset($fleeter[$username]['Kampfbasis'])) {
                    start_table(100);
                    foreach ($fleeter[$username]['Kampfbasis'] as $planni) {
                        start_row("windowbg1", "style='width:100%' align='left'");
                        echo "$planni<br>";
                        end_row();
                    }
                    end_table();
                } else {
                    echo "-";
                }
                next_cell("windowbg1", "style='width:21%' align='left'");
                if (isset($fleeter[$username]['Artefaktbasis'])) {
                    start_table(100);
                    foreach ($fleeter[$username]['Artefaktbasis'] as $planni) {
                        start_row("windowbg1", "style='width:100%' align='left'");
                        echo "$planni<br>";
                        end_row();
                    }
                    end_table();
                } else {
                    echo "-";
                }
            }
            end_row();
        }
        start_row("titlebg", "style='width:95%' align='center' colspan='5'");
        echo "<b>weitere Spieler</b>";
        next_row("windowbg2", "style='width:22%' align='center'");
        echo "Username";
        next_cell("windowbg2", "style='width:36%' lign='center'");
        echo "Kolonien";
        next_cell("windowbg2", "style='width:21%' align='center'");
        echo "Ressbasen";
        next_cell("windowbg2", "style='width:21%' align='center'");
        echo "Kampfbasen";
        next_cell("windowbg2", "style='width:21%' align='center'");
        echo "Artefaktbasen";
        foreach ($buddler as $username => $plannis) {
            next_row("windowbg3", "style='width:22%' align='center'");
            echo "<b>$username</b><br>" . $buddlers[$username] . " pkte";
            next_cell("windowbg1", "style='width:36%' align='left'");
            if (isset($buddler[$username]['Kolonie'])) {
                start_table(100);
                foreach ($buddler[$username]['Kolonie'] as $key => $planni) {
                    start_row("windowbg1", "style='width:55%' align='left'");
                    echo "$planni";
                    next_cell("windowbg1", "style='width:45%' align='right'");
                    echo $points[$username][$key] . " pkte";
                    end_row();
                }
                end_table();
            } else {
                echo "-";
            }
            next_cell("windowbg1", "style='width:21%' align='left'");
            if (isset($buddler[$username]['Sammelbasis'])) {
                start_table(100);
                foreach ($buddler[$username]['Sammelbasis'] as $planni) {
                    start_row("windowbg1", "style='width:100%' align='left'");
                    echo "$planni";
                    end_row();
                }
                end_table();
            } else {
                echo "-";
            }
            next_cell("windowbg1", "style='width:21%' align='left'");
            if (isset($buddler[$username]['Kampfbasis'])) {
                start_table(100);
                foreach ($buddler[$username]['Kampfbasis'] as $planni) {
                    start_row("windowbg1", "style='width:100%' align='left'");
                    echo "$planni";
                    end_row();
                }
                end_table();

            } else {
                echo "-";
            }
            next_cell("windowbg1", "style='width:21%' align='left'");
            if (isset($buddler[$username]['Artefaktbasis'])) {
                start_table(100);
                foreach ($buddler[$username]['Artefaktbasis'] as $planni) {
                    start_row("windowbg1", "style='width:100%' align='left'");
                    echo "$planni";
                    end_row();
                }
                end_table();

            } else {
                echo "-";
            }
        }
        end_row();
        end_table();
        echo "<b>S=Steinklumpen, E=Eisplanet, G=Gasriese, A=Asteroid, N=Nichts/Spezialgalatyp</b><br>";
        echo "<br>";


        //Spezialplanniübersicht
        if (isset($spezpla)) {
            start_table();
            start_row("titlebg", "style='width:95%' align='center' colspan='10'");
            echo "<b>Spezialplanetenübersicht</b>";
            next_row("windowbg2", "style='width:13%' align='center'");
            echo "Galaxie";
            next_cell("windowbg2", "style='width:29%' align='center'");
            echo "Gasgiganten";
            next_cell("windowbg2", "style='width:29%' align='center'");
            echo "Asteroiden";
            next_cell("windowbg2", "style='width:29%' align='center'");
            echo "Eisplaneten";
            foreach ($spezpla as $gala => $plaar) {
                next_row("windowbg3", "style='width:13%' align='center'");
                echo "Gala $gala";
                next_cell("windowbg1", "style='width:29%' align='left'");
                if (isset($spezpla[$gala]['Gasgigant'])) {
                    foreach ($spezpla[$gala]['Gasgigant']['coords'] as $id => $pla) {
                        echo $spezpla[$gala]['Gasgigant']['coords'][$id] . " [" . $spezpla[$gala]['Gasgigant']['user'][$id] . "]<br>";
                    }
                }
                next_cell("windowbg1", "style='width:29%' align='left'");
                if (isset($spezpla[$gala]['Asteroid'])) {
                    foreach ($spezpla[$gala]['Asteroid']['coords'] as $id => $pla) {
                        echo $spezpla[$gala]['Asteroid']['coords'][$id] . " [" . $spezpla[$gala]['Asteroid']['user'][$id] . "]<br>";
                    }
                }
                next_cell("windowbg1", "style='width:29%' align='left'");
                if (isset($spezpla[$gala]['Eisplanet'])) {
                    foreach ($spezpla[$gala]['Eisplanet']['coords'] as $id => $pla) {
                        echo $spezpla[$gala]['Eisplanet']['coords'][$id] . " [" . $spezpla[$gala]['Eisplanet']['user'][$id] . "]<br>";
                    }
                }
            }
            end_row();
            end_table();
        }
    } else {
        echo "Allianz dummerweise net gefunden<br>\n";
    }
    echo "<a href='index.php?action=m_allystats&sid=" . $sid . "'>zurück zur Allianzliste</a>";

} else {
    start_table();
    start_row_only();
    $rownum = 1;
    $width  = (int)100 / $n;
    $sql    = "SELECT DISTINCT allianz FROM " . $db_tb_scans . " WHERE allianz != '' ORDER BY allianz";
    $result = $db->db_query($sql)
        or error(
        GENERAL_ERROR,
        'Could not query config information.', '',
        __FILE__, __LINE__, $sql
    );
    while ($row = $db->db_fetch_array($result)) {
        cell("windowbg1", "style='width:$width%'");
        action("m_allystats&allianz=" . $row['allianz'], $row['allianz']);
        end_cell();
        if ($rownum % $n == 0) {
            echo "</tr>";
            start_row_only();
        }
        $rownum++;
    }
    while ($rownum % $n != 1) {
        cell("windowbg1");
        echo " ";
        end_cell();
        $rownum++;
    }
    echo "</tr>";
    end_table();


}

?>
