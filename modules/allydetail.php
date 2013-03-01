<?php
/*****************************************************************************
 * allydetail.php                                                            *
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
 * gehört zu m_allystats.php                                                 *
 *****************************************************************************
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
global $db, $db_tb_scans, $db_tb_allianzstatus, $db_tb_spieler;
global $config_map_galaxy_min, $config_map_galaxy_max, $config_map_system_max;

//Einstellungen
$show_full_map = true;
$rangewidth = 10;

function getColor($anzahl)
{

    if ($anzahl['Kolonie'] == 0 AND $anzahl['Sammelbasis'] == 0 AND $anzahl['Kampfbasis'] == 0) {
        return '#00FF00';
    } else if ($anzahl['Kolonie'] == 0 AND ($anzahl['Sammelbasis'] != 0 OR $anzahl['Kampfbasis'] != 0)) {
        return '#00FFCC';
    }

    $i = (int)(60 * sqrt(0.5 * $anzahl['Kolonie']));
    $i += 150;
    if ($i >= 512) {
        return '#FF0000';
    }
    $rot   = ($i < 256) ? $i : 255;
    $gruen = ($i < 256) ? 255 : 255 - ($i - 256);

    return ("#" . str_pad(dechex($rot), 2, "0", STR_PAD_LEFT) . str_pad(dechex($gruen), 2, "0", STR_PAD_LEFT) . "00");
}


//settings überprüfen und entsprechend setzen
$allianz    = getVar('allianz') ? getVar('allianz') : '';

$alliances['alle'] = 'alle';
$alliances['Solo'] = 'Solo';

$sql = "SELECT DISTINCT allianz FROM " . $db_tb_spieler . " WHERE allianz != '' ORDER BY allianz ASC;";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $alliances[urlencode($row['allianz'])] = $row['allianz'];
}

//Spielertabelle nicht mit Scanstabelle sync (zum testen)
$sql = "SELECT DISTINCT allianz FROM " . $db_tb_scans . " WHERE allianz != '' ORDER BY allianz ASC;";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $alliances[urlencode($row['allianz'])] = $row['allianz'];
}

doc_title('Allianz-Statistik');

echo 'Allianz: ';
echo makeField(
    array(
         "type"   => 'select',
         "values" => $alliances,
         "value"  => urlencode($allianz),
         "onchange" => "location.href='index.php?action=allydetail&amp;allianz='+this.options[this.selectedIndex].value",
    ), 'allianz'
);
echo "<br><br>\n";

//wenn Allianz angegeben, ausgabe der Allianzspieler

if (!empty($allianz)) {
    if (in_array($allianz, $alliances)) {
        if (($allianz === 'Solo')) {
            $allianz = '';
        } elseif ($allianz === 'alle') {
            $allianz = '%';
        } else {
            $allianz = $db->escape($allianz);
        }

        $sql = "SELECT count(name) as gesamt,
        SUM(IF(`staatsform` IS NULL OR `staatsform` = '', 1,0)) AS unbekannt,
        SUM(IF(`staatsform` = 'Barbar', 1,0)) AS Barbaren,
        SUM(IF(`staatsform` = 'Diktator', 1,0))  AS Diktatoren,
        SUM(IF(`staatsform` = 'Demokrat', 1,0))  AS Demokraten,
        SUM(IF(`staatsform` = 'Kommunist', 1,0)) AS Kommunisten,
        SUM(IF(`staatsform` = 'Monarch', 1,0))  AS Monarchen
        FROM " . $db_tb_spieler . "
        WHERE allianz LIKE '$allianz';";

        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $allianzSpieler = $db->db_fetch_array($result);

        $bgcolor = '';
        if ($allianz !== '%' AND $allianz !== '') { //'%' sind alle Spieler, '' sind Solos -> Status macht irgendwie keinen Sinn...
            $sql = "SELECT `status` FROM " . $db_tb_allianzstatus . " WHERE allianz like '$allianz';";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            $row = $db->db_fetch_array($result);
            if (!empty($row['status'])) {
                $status  = $row['status'];
                $bgcolor = 'background-color:' . $config_allianzstatus[$row['status']] . ';';
                if ($status == 'own') {
                    $status = 'eigene ally';
                }
            } else {
                $status = 'keiner';
            }
        } else {
            $status  = '-';
        }


        $allianzgals = array();
        if ($show_full_map) {
            $galamin = $config_map_galaxy_min;
            $galamax = $config_map_galaxy_max;
        } else {
            //auslesen, von welcher bis welcher Gala die ally vertreten ist
            $sql = "SELECT min(coords_gal) as galmin, max(coords_gal) as galmax FROM " . $db_tb_scans . " WHERE allianz like '$allianz' AND user!=''";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            $row    = $db->db_fetch_array($result);
            $galamin = $row['galmin'];
            $galamax = $row['galmax'];
        }

        //variablen initialisieren
        $steinis           = 0;
        $chaosgalasteinies = 0;
        $eisis             = 0;
        $gasis             = 0;
        $astros            = 0;
        $Kampfbasen        = 0;
        $Sammelbasen       = 0;
        $Artefaktbasen     = 0;

        $planicount = Array(); //hier wird krams für die karte reingeschrieben
        for ($gala = $galamin; $gala <= $galamax; $gala++) {
            for ($range = 1; $range <= ceil($config_map_system_max / $rangewidth); $range++) {
                $planicount[$gala][$range]['Kolonie']       = 0;
                $planicount[$gala][$range]['Sammelbasis']   = 0;
                $planicount[$gala][$range]['Kampfbasis']    = 0;
                $planicount[$gala][$range]['Artefaktbasis'] = 0;
                $planicount[$gala][$range]['Raumstation']   = 0;
            }
        }

        //alle planis der ally aus der db holen und verarbeiten:
        $sql = "SELECT coords, coords_gal, coords_sys, coords_planet, user, allianz, planetenname, typ, objekt, gebscantime, schiffscantime, geoscantime FROM " . $db_tb_scans . " WHERE allianz LIKE '$allianz' AND user!='' ORDER BY user, coords_gal, coords_sys, coords_planet ASC";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        $i = 0;
        while ($row = $db->db_fetch_array($result)) {
            $i++;

            if ($row['objekt'] == "Kolonie" AND $row['typ'] == "Eisplanet") {
                $eisis++;
            }
            if ($row['objekt'] == "Kolonie" AND $row['typ'] == "Steinklumpen") {
                ($row['coords_gal'] % 4 == 0) ? $chaosgalasteinies++ : $steinis++;
            }
            if ($row['objekt'] == "Kolonie" AND $row['typ'] == "Gasgigant") {
                $gasis++;
            }
            if ($row['objekt'] == "Kolonie" AND $row['typ'] == "Asteroid") {
                $astros++;
            }
            if ($row['objekt'] == "Sammelbasis") {
                $Sammelbasen++;
            }
            if ($row['objekt'] == "Kampfbasis") {
                $Kampfbasen++;
            }
            if ($row['objekt'] == "Artefaktbasis") {
                $Artefaktbasen++;
            }

            $PlaniHtmlString = "";
            switch ($row['typ']) {
                case "Steinklumpen" :
                    $PlaniHtmlString = "<span class='doc_black'><b>S</b></span>";
                    break;
                case "Eisplanet" :
                    $PlaniHtmlString = "<span class='doc_blue'><b>E</b></span>";

                    if ($row['objekt'] == "Kolonie") {
                        $spezialplanie[$row['coords_gal']]['Eisplanet']['user'][]   = $row['user'];
                        $spezialplanie[$row['coords_gal']]['Eisplanet']['coords'][] = " <a href='index.php?action=showplanet&amp;coords=" . $row['coords'] . "&amp;ansicht=auto'>" . $row['coords'] . "</a>";
                    }
                    break;
                case "Asteroid" :
                    $PlaniHtmlString = "<span class='doc_red'><b>A</b></span>";

                    if ($row['objekt'] == "Kolonie") {
                        $spezialplanie[$row['coords_gal']]['Asteroid']['user'][]   = $row['user'];
                        $spezialplanie[$row['coords_gal']]['Asteroid']['coords'][] = " <a href='index.php?action=showplanet&amp;coords=" . $row['coords'] . "&amp;ansicht=auto'>" . $row['coords'] . "</a>";
                    }
                    break;
                case "Gasgigant" :
                    $PlaniHtmlString = "<span class='doc_green'><b>G</b></span>";

                    if ($row['objekt'] == "Kolonie") {
                        $spezialplanie[$row['coords_gal']]['Gasgigant']['user'][]   = $row['user'];
                        $spezialplanie[$row['coords_gal']]['Gasgigant']['coords'][] = " <a href='index.php?action=showplanet&amp;coords=" . $row['coords'] . "&amp;ansicht=auto'>" . $row['coords'] . "</a>";
                    }
                    break;
                default :
                    $PlaniHtmlString = "<span class='doc_grey'><b>N</b></span>";
            }

            $PlaniHtmlString .= " <a href='index.php?action=showplanet&amp;coords=" . $row['coords'] . "&amp;ansicht=auto'>" . $row['coords'] . "</a>";
            if ($row['objekt'] == "Kolonie") {
                $PlaniHtmlString .= "</span>";
            }

            $player[$row['user']][$row['objekt']][$row['coords']]['coordshtml'] = $PlaniHtmlString;

            if (!empty($row['gebscantime'])) {
                $player[$row['user']][$row['objekt']][$row['coords']]['gebscantime'] = $row['gebscantime'];
            }
            if (!empty($row['schiffscantime'])) {
                $player[$row['user']][$row['objekt']][$row['coords']]['schiffscantime'] = $row['schiffscantime'];
            }
            if (!empty($row['geoscantime'])) {
                $player[$row['user']][$row['objekt']][$row['coords']]['geoscantime'] = $row['geoscantime'];
            }

            if ($row['coords_sys'] > $config_map_system_max) {     //dafür sorgen, dass zu weit hinten liegende systeme in das letzte Intervall gesteckt werden (dann ist $config_map_system_max falsch eingestellt)
                $row['coords_sys'] = $config_map_system_max;
            }

            $inrange = (int)ceil($row['coords_sys'] / $rangewidth);
            if (isset($planicount[(int)$row['coords_gal']][$inrange])) {
                $planicount[$row['coords_gal']][$inrange][$row['objekt']]++;
            }
        }

        $plancount = $eisis + $astros + $gasis + $steinis + $chaosgalasteinies;
        if (!empty($allianzSpieler['gesamt'])) {
            $planimem  = $plancount / $allianzSpieler['gesamt']; //planies pro member
        } else {
            $planimem = null;
        }



        //ausgabe
        //allgemeines

        start_table();
        start_row("titlebg center", "style='width:95%' colspan='4'");
        echo "<b>Allgemeine Informationen</b>";
        next_row("windowbg2 left", "style='width:25%'");

        echo "Allianz-Tag";
        next_cell("windowbg1 left", "style='width:25%'");
        if (empty($allianz)) {
            echo '<i>Solo</i>';
        } elseif ($allianz == '%') {
            echo '<i>alle</i>';
        } else {
            echo $allianz;
        }

        next_cell("windowbg2 left", "style='width:25%'");
        echo "Kolonien";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $plancount;

        next_row("windowbg2 left", "style='width:25%'");
        echo "diplomatischer Status";
        next_cell("windowbg1 left", "style='width:25%; $bgcolor'");
        if ($status === 'keiner') {
            echo "<i>keiner</i>";
        } else {
            echo $status;
        }

        next_cell("windowbg2 left", "style='width:25%'");
        echo "Kolonisierte Steinklumpen";
        next_cell("windowbg1 left", "style='width:25%'");
        echo ($steinis + $chaosgalasteinies) . " (" . $steinis . "+" . $chaosgalasteinies . ")";

        next_row("windowbg2 left", "style='width:25%'");
        echo "Mitgliederzahl";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $allianzSpieler['gesamt'];

        next_cell("windowbg2 left", "style='width:25%'");
        echo "Kolonisierte Asteroiden";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $astros;

        next_row("windowbg2 left", "style='width:25%'");
        echo "unbekannte Staatsform";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $allianzSpieler['unbekannt'];

        next_cell("windowbg2 left", "style='width:25%'");
        echo "Kolonisierte Gasriesen";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $gasis;

        next_row("windowbg2 left", "style='width:25%'");
        echo "Barbaren";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $allianzSpieler['Barbaren'];

        next_cell("windowbg2 left", "style='width:25%'");
        echo "Kolonisierte Eisplaneten";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $eisis;

        next_row("windowbg2 left", "style='width:25%'");
        echo "Diktatoren";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $allianzSpieler['Diktatoren'];

        next_cell("windowbg2 left", "style='width:25%'");
        echo "Planeten pro Mitglied";
        next_cell("windowbg1 left", "style='width:25%'");
        echo number_format($planimem, 2, ",", ".");

        next_row("windowbg2 left", "style='width:25%'");
        echo "Monarchen";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $allianzSpieler['Monarchen'];

        next_cell("windowbg2 left", "style='width:25%'");
        echo "Sammelbasen";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $Sammelbasen;

        next_row("windowbg2 left", "style='width:25%'");
        echo "Kommunisten";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $allianzSpieler['Kommunisten'];

        next_cell("windowbg2 left", "style='width:25%'");
        echo "Kampfbasen";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $Kampfbasen;

        next_row("windowbg2 left", "style='width:25%'");
        echo "Demokraten";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $allianzSpieler['Demokraten'];

        next_cell("windowbg2 left", "style='width:25%'");
        echo "Artefaktbasen";
        next_cell("windowbg1 left", "style='width:25%'");
        echo $Artefaktbasen;

        end_row();
        end_table();
        echo "<br>";

        //Karte der Verteilung
        start_table();
        start_row("titlebg center", "style='width:95%' colspan='" . (ceil($config_map_system_max / $rangewidth) + 1) . "'");
        echo "<b>Verteilungskarte</b>";
        next_row("windowbg2 left", "style='width:5%'");
        echo " ";
        $width = floor(95 / ($config_map_system_max / $rangewidth));
        for ($range = 1; $range <= ceil($config_map_system_max / $rangewidth) - 1; $range++) {
            next_cell("windowbg2 center", "style='width:$width%'");
            echo $range * $rangewidth - $rangewidth + 1 . "-" . $range * $rangewidth;
        }
        next_cell("windowbg2 center", "style='width:$width%'"); //letzte Spalte
        echo ceil($config_map_system_max / $rangewidth) * $rangewidth - $rangewidth + 1 . "-$config_map_system_max";

        foreach ($planicount as $gala => $pcount) {
            if ($gala > 0) {

                next_row("windowbg2 left");
                echo "<a href='index.php?action=karte&amp;galaxy=$gala'>G $gala</a>\n";
                $range = 1;
                foreach ($planicount[$gala] as $inrange => $count) {
                    $color = getColor($planicount[$gala][$inrange]);

                    next_cell("windowbg1 center", "style='background-color:" . $color . "'");
                    echo "<a href='index.php?action=showgalaxy&amp;sys_start=" . ($range * $rangewidth - $rangewidth + 1) .
                        "&amp;sys_end=" . ($range * $rangewidth) . "&amp;gal_start=" . $gala .
                        "&amp;gal_end=" . $gala . "' >" . $planicount[$gala][$inrange]['Kolonie'] . "/" . $planicount[$gala][$inrange]['Sammelbasis'] . "/" . $planicount[$gala][$inrange]['Kampfbasis'] . "</a>\n";
                    $range++;
                }
            }

        }
        end_row();
        end_table();

        echo "<b>Kolonien/Sammelbasen/Kampfbasen</b><br>";

        //planiliste
        echo "<br>";

        if (!empty($allianz) and $allianz !== '%' AND !empty($player)) {

            start_table();
            start_row("titlebg center", "style='width:95%' colspan='4'");
            echo "<b>Spieler</b>";

            next_row("windowbg2 center", "style='width:15%'");
            echo "Username";

            next_cell("windowbg2 center", "style='width:40%'");
            echo "Kolonien";

            next_cell("windowbg2 center", "style='width:20%'");
            echo "Sammelbasen";

            next_cell("windowbg2 center", "style='width:20%'");
            echo "Kampfbasen";

            foreach ($player as $playername => $planis) {
                next_row("windowbg3 center", "style='width:22%'");

                $sql = "SELECT `staatsform`,`allianzrang`,`Hauptplanet`,`acctype`,`status`,`ges_pkt` FROM `{$db_tb_spieler}` WHERE `name` = '" . $playername . "';";
                $result = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
                $row = $db->db_fetch_array($result);

                echo '<b>' . $playername . '</b>';
                if (!empty($row['acctype']) and ($row['acctype'] === "IWBP")) {
                    echo "&nbsp;<img src='".BILDER_PATH."iwdb/pingu2.png' alt='IWBP' title='IceWars besoffener Pinguin Account (IWBPA)'>";
                } elseif (!empty($row['acctype']) and ($row['acctype'] === "Admin")) {
                    echo "&nbsp;<img src='".BILDER_PATH."iwdb/admin_hase.png' alt='Admin' title='Admin Account'>";
                }

                if (!empty($row['allianzrang']) and ($row['allianzrang'] != "")) {
                    echo " (" . $row['allianzrang'] . ")";
                }
                echo "<br>";

                if (!empty($row['staatsform'])) {
                    echo $row['staatsform'] . "<br>";
                }

                if (!empty($row['ges_pkt'])) {
                    echo number_format($row['ges_pkt'], 0, ',', '.') . " Punkte<br>";
                }

                if (!empty($row['status'])) {
                    echo "<span style='color:red'>" . $row['status'] . "</span>";
                }

                next_cell("windowbg1 left");
                if (!empty($player[$playername]['Kolonie'])) {
                    start_table(100, 0, 4, 1);
                    //var_dump($player[$playername]['Kolonie']);
                    foreach ($player[$playername]['Kolonie'] as $coords => $planidata) {
                        start_row("windowbg1 left", "style='width:50%';");
                        if (!empty($row['Hauptplanet']) AND ($row['Hauptplanet'] == $coords)) {
                            echo '<b>' . $planidata['coordshtml'] . ' </b> (HP)';
                        } else {
                            echo $planidata['coordshtml'];
                        }
                        next_cell("windowbg1 left", "style='width:50%';");
                        if (!empty($planidata['geoscantime'])) {
                            echo  "<abbr title='Geoscan von " . strftime(CONFIG_DATETIMEFORMAT, $planidata['geoscantime']) . "'><img src='bilder/scann_geo.png' class='middle'><span class='middle'>&thinsp;".makeShortDuration($planidata['geoscantime']).'</span></abbr>';
                        }
                        if (!empty($planidata['schiffscantime'])) {
                            echo "<abbr title='Schiffscan von " . strftime(CONFIG_DATETIMEFORMAT, $planidata['schiffscantime']) . "'><img src='bilder/scann_schiff.png' class='middle'><span class='middle'>&thinsp;".makeShortDuration($planidata['schiffscantime']).'</span></abbr>';
                        }
                        if (!empty($planidata['gebscantime'])) {
                            echo "<abbr title='Gebäudescan von " . strftime(CONFIG_DATETIMEFORMAT, $planidata['gebscantime']) . "'><img src='bilder/scann_geb.png' class='middle'><span class='middle'>&thinsp;".makeShortDuration($planidata['schiffscantime']).'</span></abbr>';
                        }

                        end_row();
                    }
                    end_table();
                } else {
                    echo '-';
                }
                next_cell("windowbg1 left");
                if (!empty($player[$playername]['Sammelbasis'])) {
                    start_table(100);
                    foreach ($player[$playername]['Sammelbasis'] as $planidata) {
                        start_row("windowbg1 left", "style='width:100%'");
                        echo $planidata['coordshtml'];
                        end_row();
                    }
                    end_table();
                } else {
                    echo "-";
                }
                next_cell("windowbg1 left");
                if (!empty($player[$playername]['Kampfbasis'])) {
                    start_table(100);
                    foreach ($player[$playername]['Kampfbasis'] as $planidata) {
                        start_row("windowbg1 left", "style='width:100%'");
                        echo $planidata['coordshtml'];
                        end_row();
                    }
                    end_table();
                } else {
                    echo "-";
                }
            }
            end_row();
            end_table();

            echo "<span class='doc_black'><b>S=Steinklumpen</b></span>, <span class='doc_blue'><b>E=Eisplanet</b></span>, <span class='doc_green'><b>G=Gasriese</b></span>, <span class='doc_red'><b>A=Asteroid</b></span>, <span class='doc_grey'><b>N=Nichts</b></span><br><br>";

            //spezialplaniübersicht
            if (!empty($spezialplanie)) {
                start_table();
                start_row("titlebg center", "style='width:95%' colspan='4'");
                echo '<b>Spezialplanetenübersicht</b>';
                next_row("windowbg2 center", "style='width:13%'");
                echo 'Galaxie';
                next_cell("windowbg2 center", "style='width:29%'");
                echo '<span class="doc_green">Gasriesen</span>';
                next_cell("windowbg2 center", "style='width:29%'");
                echo '<span class="doc_red">Asteroiden</span>';
                next_cell("windowbg2 center", "style='width:29%'");
                echo '<span class="doc_blue">Eisplaneten</span>';
                foreach ($spezialplanie as $gala => $plaar) {
                    next_row("windowbg3 center", "style='width:13%''");
                    echo "Gala $gala";
                    next_cell("windowbg1 left", "style='width:29%'");
                    if (isset($spezialplanie[$gala]['Gasgigant'])) {
                        foreach ($spezialplanie[$gala]['Gasgigant']['coords'] as $id => $pla) {
                            echo $spezialplanie[$gala]['Gasgigant']['coords'][$id] . " (" . $spezialplanie[$gala]['Gasgigant']['user'][$id] . ")<br>";
                        }
                    }
                    next_cell("windowbg1 left", "style='width:29%'");
                    if (isset($spezialplanie[$gala]['Asteroid'])) {
                        foreach ($spezialplanie[$gala]['Asteroid']['coords'] as $id => $pla) {
                            echo $spezialplanie[$gala]['Asteroid']['coords'][$id] . " (" . $spezialplanie[$gala]['Asteroid']['user'][$id] . ")<br>";
                        }
                    }
                    next_cell("windowbg1 left", "style='width:29%'");
                    if (isset($spezialplanie[$gala]['Eisplanet'])) {
                        foreach ($spezialplanie[$gala]['Eisplanet']['coords'] as $id => $pla) {
                            echo $spezialplanie[$gala]['Eisplanet']['coords'][$id] . " (" . $spezialplanie[$gala]['Eisplanet']['user'][$id] . ")<br>";
                        }
                    }
                }
                end_row();
                end_table();
            }
        }
    } else {
        doc_message('Allianz wurde nicht gefunden');
        redirect("index.php?action=m_allystats", "< zurück zur Allianzliste");
    }

}