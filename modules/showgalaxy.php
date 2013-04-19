<?php
/*****************************************************************************
 * showgalaxy.php                                                            *
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

global $db, $db_tb_preset, $user_gal_start, $user_gal_end, $user_sys_start, $user_sys_end, $user_status, $user_sitterlogin;

//Input filtering
$orderprio = array(
    "Koordinaten",
    "rating_normal",
    "rating_best_tt",
    "rating_eisen_tt",
    "rating_chemie_tt",
    "rating_eis_tt",
    "eisengehalt",
    "chemievorkommen",
    "eisdichte",
    "lebensbedingungen",
    "gravitation",
    "typ",
    "objekt",
    "user",
    "allianz",
    "kgmod",
    "dgmod",
    "ksmod",
    "dsmod",
    "fmod"
);

// get post and get vars //
$order1 = ensureValue(getVar('order1'), $orderprio, "Koordinaten");
$order2 = ensureValue(getVar('order2'), $orderprio, "Koordinaten");
$order3 = ensureValue(getVar('order3'), $orderprio, "Koordinaten");
$order1_d = ensureSortDirection(getVar('order1_d'));
$order2_d = ensureSortDirection(getVar('order2_d'));
$order3_d = ensureSortDirection(getVar('order3_d'));

$showWithoutScan = (bool)getVar('withoutscan');
$reserv = (bool)getVar('reserv');
$clean_reserv = (bool)getVar('clean_reserv');

$gal = (int)getVar('gal');
$sys = (int)getVar('sys');

$gal_start = (int)getVar('gal_start');
$gal_end = (int)getVar('gal_end');
$sys_start = (int)getVar('sys_start');
$sys_end = (int)getVar('sys_end');

$objekt_type = array(
    "%",
    "---",
    "bewohnt",
    "Kolonie",
    "%basis",
    "Kampfbasis",
    "Sammelbasis"
);
$objekt = ensureValue(getVar('objekt'), $objekt_type, "%");

$typ_type = array(
    "%",
    "Steinklumpen",
    "Asteroid",
    "Eisplanet",
    "Gasgigant",
    "Nichts"
);
$typ = ensureValue(getVar('typ'), $typ_type, "%");

$user = $db->escape(getVar('user'));
$allianz = $db->escape(getVar('allianz'));

$button = (bool)getVar('B1');

$eisengehalt = (int)getVar('eisengehalt');
$chemievorkommen = (int)getVar('chemievorkommen');
$eisdichte = (int)getVar('eisdichte');
$lebensbedingungen = (int)getVar('lebensbedingungen');

//Normalisierung der Modifikatoren
$kgmod = (float)str_replace(",", ".", getVar('kgmod'));
$kgmod = ($kgmod>10) ? $kgmod/100 : $kgmod;

$dgmod = (float)str_replace(",", ".", getVar('dgmod'));
$dgmod = ($dgmod>10) ? $dgmod/100 : $dgmod;

$ksmod = (float)str_replace(",", ".", getVar('ksmod'));
$ksmod = ($ksmod>10) ? $ksmod/100 : $ksmod;

$dsmod = (float)str_replace(",", ".", getVar('dsmod'));
$dsmod = ($dsmod>10) ? $dsmod/100 : $dsmod;

$fmod  = (float)str_replace(",", ".", getVar('fmod'));
$fmod = ($fmod<10) ? $fmod*100 : $fmod;

$grav_von = (float)str_replace(",", ".", getVar('grav_von'));
$grav_bis = (float)str_replace(",", ".", getVar('grav_bis'));

$max = (int)getVar('max');
$exact = (bool)getVar('exact');
$global_preset = (bool)getVar('global');

$newpreset = (bool)getVar('newpreset');
$presetname1 = $db->escape(getVar('presetname1'));
$presetname2 = $db->escape(getVar('presetname2'));

$ansichten = array(
    "auto",
    "geologisch",
    "taktisch",
    "beide"
);
$ansicht = ensureValue(getVar('ansicht'), $ansichten, "auto");

$techteams = array(
    "keinTT",
    "EisenTT",
    "ChemieTT",
    "EisTT"
);
$techteam = ensureValue(getVar('techteam'), $techteams, "keinTT");

$ratingmin = (int)getVar('ratingmin');
$ratingtypes = array(
    "rating_normal",
    "rating_best_tt",
    "rating_eisen_tt",
    "rating_chemie_tt",
    "rating_eis_tt",
);
$ratingtyp = ensureValue(getVar('ratingtyp'), $ratingtypes, "rating_normal");

$merkmale = array(
    "%",
    "Asteroidengürtel",
    "Gold",
    "instabiler Kern",
    "Mond",
    "planetarer Ring",
    "Natürliche Quelle",
    "radioaktiv",
    "wenig Rohstoffe",
    "alte Ruinen",
    "Ureinwohner",
    "toxisch"
);
$merkmal = ensureValue(getVar('merkmal'), $merkmale, "%");

//ungescannte Planeten anzeigen Modus?
if ($showWithoutScan AND ($button === false)) {

    $gal_start = $user_gal_start;
    $gal_end   = $user_gal_end;
    $sys_start = $user_sys_start;
    $sys_end   = $user_sys_end;

} else {

    $gal_start = (int)getVar('gal_start');
    $gal_end   = (int)getVar('gal_end');
    $sys_start = (int)getVar('sys_start');
    $sys_end   = (int)getVar('sys_end');

    if (!empty($gal)) {
        $gal_start = $gal;
        $gal_end   = $gal;
    }
    if (!empty($sys)) {
        $sys_start = $sys;
        $sys_end   = $sys;
    }

}

if ($newpreset) {
    doc_title('Preset hinzufügen');

    if (($user_status == "admin") AND ($global_preset)) {
        $fromuser = "";
    } else {
        $fromuser = $user_sitterlogin;
    }

    if (!empty($presetname2)) {
        $sql = "INSERT INTO " . $db_tb_preset . " (name, typ, objekt, user, exact, allianz, ";
        $sql .= "merkmal, eisengehalt, chemievorkommen, eisdichte, lebensbedingungen, order1, order1_d, order2, order2_d, order3, order3_d, grav_von, grav_bis, fmod, kgmod, dgmod, ksmod, dsmod, gal_start, gal_end, sys_start, sys_end, max, fromuser, ansicht) VALUES ('" . $presetname2 . "', '" . $typ . "', '" . $objekt . "', '" . $user . "', '" . $exact . "', '" . $allianz;
        $sql .= "', '" . $merkmal . "', '" . $eisengehalt . "', '" . $chemievorkommen . "', '" . $eisdichte . "', '" . $lebensbedingungen . "', '" . $order1 . "', '" . $order1_d . "', '" . $order2 . "', '" . $order2_d . "', '" . $order3 . "', '" . $order3_d . "', '" . $grav_von . "', '" . $grav_bis . "', '" . $fmod . "', '" . $kgmod . "', '" . $dgmod . "', '" . $ksmod . "', '" . $dsmod . "', '" . $gal_start . "', '" . $gal_end . "', '" . $sys_start . "', '" . $sys_end . "', '" . $max . "', '" . $fromuser . "', '" . $ansicht . "')";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        echo "<div class='system_notification'>Neues Preset '" . $presetname2 . "' hinzugefügt.</div>";
    } else {
        $sql = "SELECT fromuser, name FROM " . $db_tb_preset . " WHERE id = '" . $presetname1 . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result);

        if (($row['fromuser'] == $user_sitterlogin) || ($user_status == "admin")) {
            $sql = "UPDATE " . $db_tb_preset . " SET typ = '" . $typ . "', objekt = '" . $objekt . "', user = '" . $user . "', exact = '" . $exact . "', allianz = '" . $allianz;
            $sql .= "', merkmal = '" . $merkmal . "', eisengehalt = '" . $eisengehalt . "', chemievorkommen = '" . $chemievorkommen . "', eisdichte = '" . $eisdichte . "', lebensbedingungen = '" . $lebensbedingungen . "', order1 = '" . $order1 . "', order1_d = '" . $order1_d . "', order2 = '" . $order2 . "', order2_d = '" . $order2_d . "', order3 = '" . $order3 . "', order3_d = '" . $order3_d . "', gal_start = '" . $gal_start . "', gal_end = '" . $gal_end . "', sys_start = '" . $sys_start . "', sys_end = '" . $sys_end . "', max = '" . $max . "', fromuser = '" . $fromuser . "', ansicht = '" . $ansicht . "', kgmod = '" . $kgmod . "', dgmod = '" . $dgmod . "', ksmod = '" . $ksmod . "', dsmod = '" . $dsmod . "', dsmod = '" . $dsmod . "' WHERE id = '" . $presetname1 . "'";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            echo "<div class='system_notification'>Preset '" . $row['name'] . " (" . $row['fromuser'] . ")' aktualisiert.</div>";

        } else {
            echo "<div class='system_error'>Hack Attempt.</div>";
        }
    }

} else {

    if ($showWithoutScan) {
        doc_title('Planeten ohne aktuellen Geoscan');
    } else {
        doc_title('Galaxie');
    }

    if ($objekt <> "---") {
        $kgmod_von = "";
        $kgmod_bis = "";
        $dgmod_von = "";
        $dgmod_bis = "";
        $ksmod_von = "";
        $ksmod_bis = "";
        $dsmod_von = "";
        $dsmod_bis = "";
        $fmod_von = "";
        $fmod_bis = "";
    }

    // Sortierung //
    $order = "ORDER BY ";
    if ($order1 === 'Koordinaten') {
        $order .= "coords_gal " . $order1_d . ", coords_sys " . $order1_d . ", coords_planet " . $order1_d;
    } else {
        $order .= $order1 . " " . $order1_d;
    }
    if ($order2 !== $order1) {
        if ($order2 === 'Koordinaten') {
            $order .= ", coords_gal " . $order2_d . ", coords_sys " . $order2_d . ", coords_planet " . $order2_d;
        } else {
            $order .= ', ' . $order2 . " " . $order2_d;
        }
    }
    if (($order3 !== $order2) AND ($order3 !== $order1)) {
        if ($order3 === 'Koordinaten') {
            $order .= ", coords_gal " . $order3_d . ", coords_sys " . $order3_d . ", coords_planet " . $order3_d;
        } else {
            $order .= ', ' . $order3 . " " . $order3_d;
        }
    }

    // Planetentyp
    $where = "WHERE typ LIKE '" . $typ . "'";

    // Objekttyp //
    if ($objekt == "bewohnt") {
        $where .= " AND objekt <> '---'";
    } else {
        $where .= " AND objekt LIKE '" . $objekt . "'";
    }

    // Galaxy, System //
    if (!empty($gal_start)) {
        $where .= " AND coords_gal >= " . $gal_start;
    }
    if (!empty($gal_end)) {
        $where .= " AND coords_gal <= " . $gal_end;
    }

    if (!empty($sys_start)) {
        $where .= " AND coords_sys >= " . $sys_start;
    }
    if (!empty($sys_end)) {
        $where .= " AND coords_sys <= " . $sys_end;
    }

    // ungescannte Planies oder welche mit abgelaufenem Sprengdatum
    if ($showWithoutScan) {

        $nothingtypes = "(`typ` = 'Steinklumpen' OR `typ` = 'Asteroid' OR `typ` = 'Gasgigant' OR `typ` = 'Eisplanet')";
        $where .= " AND (`geoscantime` IS NULL OR (geoscantime+reset_timestamp) < " . CURRENT_UNIX_TIME . " ) AND objekt = '---' AND " . $nothingtypes;

    }

    // keine reservierte Planeten
    if ($reserv) {
        $where .= " AND reserviert <> ''";
    }

    // Planetenname
    if (!empty($planetenname)) {
        $where .= " AND planetenname LIKE '" . $planetenname . "'";
    }

    // Spielername
    $exact_mod = ($exact) ? "" : "%";
    if (!empty($user)) {
        $users = explode(";", $user);
        $where_user = "";
        foreach ($users as $user_name) {
            $where_user = (!empty($where_user)) ? $where_user . " OR user LIKE '" . $exact_mod . $user_name . $exact_mod . "'" : "user LIKE '" . $exact_mod . $user_name . $exact_mod . "'";
        }
        $where .= " AND (" . $where_user . ")";
        unset($where_user);
    }

    // Allianz
    if (!empty($allianz)) {
        $allianzen = explode(";", $allianz);
        $where_ally = "";
        foreach ($allianzen as $ally_name) {
            $where_ally = (!empty($where_ally)) ? $where_ally . " OR allianz LIKE '" . $ally_name . "'" : "allianz LIKE '" . $ally_name . "'";
        }
        $where .= " AND (" . $where_ally . ")";
        unset($where_ally);
    }

    // Merkmale
    if (!empty($merkmal) && $merkmal !== "%") {
        $where .= " AND besonderheiten LIKE '%" . $merkmal . "%'";
    }

    // Eisengehalt
    if (!empty($eisengehalt)) {
        if ($techteam == "EisenTT") {
            $where .= " AND tteisen >= " . $eisengehalt;
        } else {
            $where .= " AND eisengehalt >= " . $eisengehalt;
        }
    }

    // Chemiegehalt
    if (!empty($chemievorkommen)) {
        if ($techteam == "ChemieTT") {
            $where .= " AND ttchemie >= " . $chemievorkommen;
        } else {
            $where .= " AND chemievorkommen >= " . $chemievorkommen;
        }
    }

    // Eisgehalt
    if (!empty($eisdichte)) {
        if ($techteam == "EisTT") {
            $where .= " AND tteis >= " . $eisdichte;
        } else {
            $where .= " AND eisdichte >= " . $eisdichte;
        }
    }

    // Lebensbedingungen
    if (!empty($lebensbedingungen)) {
        $where .= " AND lebensbedingungen >= " . $lebensbedingungen;
    }

    // Gravitation
    if (!empty($grav_von)) {
        $where .= " AND gravitation >= " . $grav_von . "";
    }
    if (!empty($grav_bis)) {
        $where .= " AND gravitation <= " . $grav_bis . "";
    }

    // Modifikatoren
    if (!empty($kgmod)) {
        $where .= " AND kgmod <= " . $kgmod . "";
    }
    if (!empty($dgmod)) {
        $where .= " AND dgmod <= " . $dgmod . "";
    }
    if (!empty($ksmod)) {
        $where .= " AND ksmod <= " . $ksmod . "";
    }
    if (!empty($dsmod)) {
        $where .= " AND dsmod <= " . $dsmod . "";
    }
    if (!empty($fmod)) {
        $where .= " AND fpmod <= " . $fmod . "";
    }

    // maximale Anzahl
    if (!empty($max)) {
        $limit = " LIMIT " . $max;
    } else {
        $limit = " LIMIT 100";
    }

    // Rating
    $rating_normal = "";
    if (((strlen($ratingmin) > 0) AND (strlen(  $ratingtyp) == 0)) OR ($order1 == "rating_normal") OR ($order2 == "rating_normal") OR ($order3 == "rating_normal")) {
        $rating_normal = ", " . sqlRating("") . " AS rating_normal";
        if ((strlen($ratingmin) > 0) AND (strlen($ratingtyp) == 0)) {
            $where .= " AND " . sqlRating("") . " > " . $ratingmin . "";
        }
    }
    $rating_best_tt = "";
    if (((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_best_tt")) OR ($order1 == "rating_best_tt") OR ($order2 == "rating_best_tt") OR ($order3 == "rating_best_tt")) {
        $rating_best_tt = ", " . sqlRating("rating_best_tt") . " AS rating_best_tt";
        if ((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_best_tt")) {
            $where .= " AND " . sqlRating("rating_best_tt") . " > " . $ratingmin . "";
        }
    }
    $rating_eisen_tt = "";
    if (((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_eisen_tt")) OR ($order1 == "rating_eisen_tt") OR ($order2 == "rating_eisen_tt") OR ($order3 == "rating_eisen_tt")) {
        $rating_eisen_tt = ", " . sqlRating("rating_eisen_tt") . " AS rating_eisen_tt";
        if ((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_eisen_tt")) {
            $where .= " AND " . sqlRating("rating_eisen_tt") . " > " . $ratingmin . "";
        }
    }
    $rating_chemie_tt = "";
    if (((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_chemie_tt")) OR ($order1 == "rating_chemie_tt") OR ($order2 == "rating_chemie_tt") OR ($order3 == "rating_chemie_tt")) {
        $rating_chemie_tt = ", " . sqlRating("rating_chemie_tt") . " AS rating_chemie_tt";
        if ((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_chemie_tt")) {
            $where .= " AND " . sqlRating("rating_chemie_tt") . " > " . $ratingmin . "";
        }
    }
    $rating_eis_tt = "";
    if (((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_eis_tt")) OR ($order1 == "rating_eis_tt") OR ($order2 == "rating_eis_tt") OR ($order3 == "rating_eis_tt")) {
        $rating_eis_tt = ", " . sqlRating("rating_eis_tt") . " AS rating_eis_tt";
        if ((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_eis_tt")) {
            $where .= " AND " . sqlRating("rating_eis_tt") . " > " . $ratingmin . "";
        }
    }
    $rating = $rating_normal . $rating_best_tt . $rating_eisen_tt . $rating_chemie_tt . $rating_eis_tt;

    $sql = "SELECT *" . $rating . " FROM " . $db_tb_scans . " " . $where . " " . $order . $limit;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    if (($showWithoutScan === false) && ($gal_end == $gal_start) && ($sys_end == $sys_start) && (!empty($gal_end)) && (!empty($gal_start))) {      //einzelnes System
        ?>
        <form method="POST" action="index.php?action=showgalaxy" enctype="multipart/form-data">
            <p class="center"><?php
                if ($sys_start > 1) {
                    ?>
                    <a href="index.php?action=showgalaxy&sys_end=<?php echo ($sys_end - 1);?>&sys_start=<?php echo ($sys_end - 1);?>&gal_end=<?php echo $gal_end;?>&gal_start=<?php echo $gal_start;?>"><b>&laquo;</b></a>
                <?php
                }
                ?> Galaxie:
                <input type="number" name="gal" style="width: 5em" min="<?php echo $config_map_galaxy_min;?>" max="<?php echo $config_map_galaxy_max;?>" value="<?php echo $gal_start;?>">
                System:
                <input type="number" name="sys" style="width: 5em" min="<?php echo $config_map_system_min;?>" max="<?php echo $config_map_system_max;?>" value="<?php echo $sys_start;?>">
                <input type="submit" value="los" name="B1">
                <a href="index.php?action=showgalaxy&sys_end=<?php echo ($sys_end + 1);?>&sys_start=<?php echo ($sys_end + 1);?>&gal_end=<?php echo $gal_end;?>&gal_start=<?php echo $gal_start;?>"><b>&raquo;</b></a>
            </p>
        </form>
    <?php
    }

    if ($showWithoutScan) {
        ?>
        <form method="POST" action="index.php?action=showgalaxy&withoutscan=1" enctype="multipart/form-data">
            <table class='table_format_showgalaxy'>
                <tr>
                    <td>Galaxie von:
                        <input type="number" name="gal_start" style="width: 5em"
                               min="<?php echo $config_map_galaxy_min;?>" max="<?php echo $config_map_galaxy_max;?>" value="<?php echo $gal_start;?>">
                        bis: <input type="number" name="gal_end" style="width: 5em"
                               min="<?php echo $config_map_galaxy_min;?>" max="<?php echo $config_map_galaxy_max;?>" value="<?php echo $gal_end;?>">
                    </td>
                </tr>
                <tr>
                    <td>System von:
                        <input type="number" name="sys_start" style="width: 5em"
                               min="<?php echo $config_map_system_min;?>" max="<?php echo $config_map_system_max;?>" value="<?php echo $sys_start;?>">
                        bis:
                        <input type="number" name="sys_end" style="width: 5em"
                               min="<?php echo $config_map_system_min;?>" max="<?php echo $config_map_system_max;?>" value="<?php echo $sys_end;?>">
                    </td>
                </tr>
                <?php
                    echo '<tr><td class="center">Typ: ';
                    echo '<select name="typ">';
                    echo '<option value="%" selected>Alle</option>';
                    echo '<option value="Steinklumpen">Steinklumpen</option>';
                    echo '<option value="Asteroid">Asteroid</option>';
                    echo '<option value="Eisplanet">Eisplanet</option>';
                    echo '<option value="Gasgigant">Gasgigant</option>';
                    echo '<option value="Nichts">Nichts</option>';
                    echo '</select></td></tr>';
                ?>
                <tr>
                    <td class="center"><input type="submit" value="los" name="B1"></td>
                </tr>
            </table>
        </form>
        <br>
    <?php
    }

    if ($db->db_num_rows($result) == 0) {
        if (($showWithoutScan) AND (empty($gal_start)) AND (empty($gal_end)) AND (empty($sys_start)) AND (empty($sys_end))) {
            echo "<div class='system_error'>Bitte Bereich eingeben.</div>";
        } else {
            echo "<div class='system_notice'>Keine passenden Planeten gefunden.</div>";
        }
    } else {

        // Merkmale
        if (!empty($merkmal) && $merkmal !== "%") {
            echo "<div class='doc_centered'>Ausgabe von Planeten mit " . $merkmal . "</div>\n";
        }

        if (!empty($gal_start) && !empty($sys_start) && $gal_start == $gal_end && $sys_start == $sys_end) {
            $syssql = "SELECT date, nebula FROM " . $db_tb_sysscans . " WHERE gal=" . $gal_start . " AND sys=" . $sys_start;

            $result1 = $db->db_query($syssql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $syssql);
            $row1 = $db->db_fetch_array($result1);

            if (!empty($row1)) {
                $rtime = round((CURRENT_UNIX_TIME - $row1['date']) / DAY);

                if ($rtime == 0) {
                    doc_message('System zuletzt gescannt: heute');
                } else if ($rtime == 1) {
                    doc_message('System zuletzt gescannt: gestern');
                } else {
                    doc_message('System zuletzt gescannt: vor ' . $rtime . ' Tagen');
                }
                echo '<br>';

                switch ($row1['nebula']) {
                    case 'blau':
                        echo '<b>Blauer Nebel</b><br /><br />';
                        break;
                    case 'gelb':
                        echo '<b>Gelber Nebel</b><br /><br />';
                        break;
                    case 'gruen':
                        echo '<b>Grüner Nebel</b><br /><br />';
                        break;
                    case 'rot':
                        echo '<b>Roter Nebel</b><br /><br />';
                        break;
                    case 'violett':
                        echo '<b>Violetter Nebel</b><br /><br />';
                        break;
                }
            }
        }

        ?>
        <table class="table_format borderless" style="width: 95%;">
        <tr class="titlebg center bold">
            <?php
            if ($user_planibilder == "1") {
                ?>
                <td>&nbsp;</td>
                <?php
            }
            if ((($ansicht == "auto") && ($objekt != "---")) || ($ansicht == "taktisch") || ($ansicht == "beide")) {
                ?>
                <td>Koords</td>
                <td>Planetentyp</td>
                <td>Objekttyp</td>
                <td>
                    <a href="index.php?action=showgalaxy&user=<?php echo urlencode($row['user']);?>&order=user&orderd=desc"><img src="bilder/desc.gif"></a>Spieler-<br>name
                    <a href="index.php?action=showgalaxy&user=<?php echo urlencode($row['user']);?>&order=user&orderd=asc"><img src="bilder/asc.gif"></a>
                </td>
                <td>Allitag</td>
                <td>Planeten-<br>name</td>
                <td>letztes Update</td>
                <td>Scan / Raid</td>
            <?php
            }
            $order = getVar('order');
            $orderd = getVar('orderd');
            if ((($ansicht == "auto") && ($objekt == "---")) || ($ansicht == "geologisch") || ($ansicht == "beide")) {
                if ($ansicht != "beide") {
                    ?>
                    <td>Koordinaten</td>
                    <td>Planetentyp</td>
                <?php
                }
                ?>
                <td>Eisen-<br>gehalt</td>
                <td>Chemie-<br>vorkommen</td>
                <td>Eisdichte</td>
                <td>Lebens-<br>bedingungen</td>
                <?php
                if ((!empty($kgmod_von)) OR (!empty($dgmod_von)) OR (!empty($ksmod_von)) OR (!empty($dsmod_von)) OR (!empty($kgmod_bis)) OR (!empty($dgmod_bis)) OR (!empty($ksmod_bis)) OR (!empty($dsmod_bis)) OR (!empty($fmod_von)) OR (!empty($fmod_bis)) OR (!empty($kgmod)) OR (!empty($dgmod)) OR (!empty($ksmod)) OR (!empty($dsmod)) OR (!empty($fmod))) {
                    ?>
                    <td><abbr title="Gebäudebaukosten-Modifikation">kgmod</abbr></td>
                    <td><abbr title="Gebäudebaudauer-Modifikation">dgmod</abbr></td>
                    <td><abbr title="Schiffbaukosten-Modifikation">ksmod</abbr></td>
                    <td><abbr title="Schiffbaudauer-Modifikation">dsmod</abbr></td>
                <?php
                }

                if ((!empty($grav_von)) OR (!empty($grav_bis))) {
                    ?>
                    <td><abbr title="Gravitation">grav</abbr></td>
                <?php
                }

            }
            if ($reserv) {
                if ($clean_reserv == false) {
                    echo "<div style='font-weight: bold;'>[<a href='index.php?action=showgalaxy&reserv=1&clean_reserv=1&ansicht=geologisch'><span style='color:red;'>Leichen löschen</span></a>]</div>";
                } else {
                    $sql99 = "UPDATE `" . $db_tb_scans . "` SET `reserviert`=NULL WHERE `user`!=''";
                    $result99 = $db->db_query($sql99)
                        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql99);

                    doc_message('Leichen wurden gelöscht...');
                    echo 'Seite [<a href="index.php?action=showgalaxy&reserv=1&ansicht=geologisch">neu laden</a>], um Änderungen zu sehen!';
                }
                echo '<br><br>';

                ?>
                <td>reserviert</td>
            <?php
            }
            if (strlen($rating_normal) > 0) {
                ?>
                <td>Rating</td>
            <?php
            }
            if (strlen($rating_best_tt) > 0) {
                ?>
                <td>Rating<br>bestes<br>Techteam</td>
            <?php
            }
            if (strlen($rating_eisen_tt) > 0) {
                ?>
                <td>Rating<br>Techteam<br>Eisen</td>
            <?php
            }
            if (strlen($rating_chemie_tt) > 0) {
                ?>
                <td>Rating<br>Techteam<br>Chemie</td>
            <?php
            }
            if (strlen($rating_eis_tt) > 0) {
                ?>
                <td>Rating<br>Techteam<br>Eis</td>
            <?php
            }
            ?>
        </tr>
        <?php
        while ($row = $db->db_fetch_array($result)) {
            if (!empty($row['allianz'])) {
                $sql = "SELECT status FROM " . $db_tb_allianzstatus . " WHERE allianz LIKE '" . $row['allianz'] . "'";
                $result_status = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
                $row_status = $db->db_fetch_array($result_status);
                if (!empty($config_allianzstatus[$row_status['status']])) {
                    $color = $config_allianzstatus[$row_status['status']];
                } else {
                    $color      = "white";
                    $row_status = "";
                }
            } else {
                $color      = "white";
                $row_status = "";
            }
            if ($row['objekt'] == "Stargate") {
                $color = $config_color['Stargate'];
            }
            if ($row['objekt'] == "Schwarzes Loch") {
                $color = $config_color['SchwarzesLoch'];
            }
            if (!empty($row['reserviert'])) {
                $color = $config_color['reserviert'];
            }
            if (!isset($sys_bev)) {
                $sys_bev = "";
            }

            //einzelne Systeme unterteilt...
            if (($row['coords_sys'] <> $sys_bev) AND ($order1 === "Koordinaten")) {
                echo '<tr><td class="borderless"></td></tr>';
                $sys_bev = $row['coords_sys'];
            }

            ?>
            <tr class="windowbg2 center" style="background-color: <?php echo $color;?>;">
            <?php

            if ($user_planibilder) {
                $path = "bilder/planeten/40x40/";
                switch ($row['typ']) {
                    case "Steinklumpen":
                        $path .= "stein/st_" . str_pad(mt_rand(1, 53), 2, "0", STR_PAD_LEFT) . ".jpg";
                        break;
                    case "Eisplanet":
                        $path .= "eis/eis_" . str_pad(mt_rand(1, 34), 2, "0", STR_PAD_LEFT) . ".jpg";
                        break;
                    case "Gasgigant":
                        $path .= "gas/gas_" . str_pad(mt_rand(1, 30), 2, "0", STR_PAD_LEFT) . ".jpg";
                        break;
                    case "Asteroid":
                        $path .= "asteroiden/ast_" . str_pad(mt_rand(1, 45), 2, "0", STR_PAD_LEFT) . ".jpg";
                        break;
                    case "Nichts":
                        $path .= "nix/nix_" . str_pad(mt_rand(1, 4), 2, "0", STR_PAD_LEFT) . ".jpg";
                        break;
                    case "Sonne":
                        $path .= "sonne.jpg";
                        break;
                    default:
                        $path .= "bes/bes_" . str_pad(mt_rand(1, 20), 2, "0", STR_PAD_LEFT) . ".jpg";
                        break;
                }
                if ($row['objekt'] == "Schwarzes Loch") {
                    $path = BILDER_PATH.'planeten/40x40/schwarzesloch.jpg';
                }
                ?>
                <td><img src="<?php echo $path;?>" alt =""></td>
            <?php
            }
            if ((($ansicht == "auto") && ($objekt != "---")) || ($ansicht == "taktisch") || ($ansicht == "beide")) {
                ?>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['coords'];?></a>
                </td>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['typ'];?></a>
                </td>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['objekt'];?></a>
                </td>
                <td>
                    <a href="index.php?action=showgalaxy&user=<?php echo urlencode($row['user']);?>&exact=1"><?php echo $row['user'];?></a>
                </td>
                <td>
                    <a href="index.php?action=showgalaxy&allianz=<?php echo $row['allianz'];?>"><?php echo $row['allianz']; echo ((empty($row_status['status'])) || ($row_status['status'] == 'own')) ? "" : " (" . $row_status['status'] . ")";?></a>
                </td>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['planetenname'];?></a>
                </td>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>">
                        <?php
                        if (empty($row['time'])) {
                            echo "/";
                        } else {
                            $rtime = round((CURRENT_UNIX_TIME - $row['time']) / DAY);
                            if ($rtime > $config_geoscan_yellow && $rtime <= $config_geoscan_red) {
                                echo "<div class='doc_yellow'>" . $rtime . " Tage</div>";
                            } else if ($rtime > $config_geoscan_red) {
                                echo "<div class='doc_red'>" . $rtime . " Tage</div>";
                            } else {
                                echo $rtime . " Tage";
                            }
                        }
                        ?>
                    </a></td>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>">
                        <?php
                        if (!empty($row['geb'])) {
                            echo "Gebäude<br>";
                        }
                        if ((!empty($row['plan'])) OR (!empty($row['stat'])) OR (!empty($row['def']))) {
                            echo "Schiffe<br>";
                        }
                        if (($row['lager_chemie'] > 0) || ($row['lager_eis'] > 0) || ($row['lager_energie'] > 0)) {
                            echo "Koloinfo<br>";
                        }
                        if (($row['eisengehalt'] > 0) || ($row['chemievorkommen'] > 0) || ($row['eisdichte'] > 0)) {
                            echo "Geo<br>";
                            echo "(" . rating(0, $row['coords']) . ")";
                        }
                        ?>
                    </a>
                </td>
            <?php
            }
            if ((($ansicht == "auto") && ($objekt == "---")) || ($ansicht == "geologisch") || ($ansicht == "beide")) {
                if ($ansicht != "beide") {
                    ?>
                    <td>
                        <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['coords'];?></a>
                    </td>
                    <td>
                        <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['typ'];?></a>
                    </td>
                    <?php
                }
                ?>
                <td class="right">
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php
                        if ($row['eisengehalt'] > 100) {
                            echo "<b>" . $row['eisengehalt'] . "</b>";
                        } else {
                            echo $row['eisengehalt'];
                        }
                        if ($row['tteisen'] > 0) {
                            echo ", max.";
                            if ($row['tteisen'] > 130) {
                                echo "<b><font color='red'>" . $row['tteisen'] . "</font></b>";
                            } else if ($row['tteisen'] > 100) {
                                echo "<b>" . $row['tteisen'] . "</b>";
                            } else {
                                echo $row['tteisen'];
                            }
                        }
                        ?> </a></td>
                <td class="right">
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php
                        if ($row['chemievorkommen'] > 100) {
                            echo "<b>" . $row['chemievorkommen'] . "</b>";
                        } else {
                            echo $row['chemievorkommen'];
                        }
                        if ($row['ttchemie'] > 0) {
                            echo ", max.";
                            if ($row['ttchemie'] > 130) {
                                echo "<b><font color='red'>" . $row['ttchemie'] . "</font></b>";
                            } else if ($row['ttchemie'] > 100) {
                                echo "<b>" . $row['ttchemie'] . "</b>";
                            } else {
                                echo $row['ttchemie'];
                            }
                        }
                        ?> </a></td>
                <td class="right">
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php
                        if ($row['eisdichte'] > 30) {
                            echo "<b>" . $row['eisdichte'] . "</b>";
                        } else {
                            echo $row['eisdichte'];
                        }
                        if ($row['tteis'] > 0) {
                            echo ", max.";
                            if ($row['tteis'] > 100) {
                                echo "<b><font color='red'>" . $row['tteis'] . "</font></b>";
                            } else if ($row['tteis'] > 30) {
                                echo "<b>" . $row['tteis'] . "</b>";
                            } else {
                                echo $row['tteis'];
                            }
                        }
                        ?> </a></td>
                <td class="right">
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo ($row['lebensbedingungen'] > 100) ? "<b>" . $row['lebensbedingungen'] . "</b>" : $row['lebensbedingungen'];?></a>
                </td>
                <?php if (!empty($kgmod) OR !empty($dgmod) OR !empty($ksmod) OR !empty($dsmod)) {
                    ?>
                    <td>
                        <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['kgmod'];?></a>
                    </td>
                    <td>
                        <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['dgmod'];?></a>
                    </td>
                    <td>
                        <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['ksmod'];?></a>
                    </td>
                    <td>
                        <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['dsmod'];?></a>
                    </td>
                    <?php
                }
                if (!empty($grav_von) OR !empty($grav_bis)) {
                    ?>
                    <td>
                        <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['gravitation'];?></a>
                    </td>
                    <?php
                }
            }
            if ($reserv) {
                ?>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo $row['reserviert'];?></a>
                </td>
                <?php
            }
            if (!empty($rating_normal)) {
                ?>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo sprintf("%.2f", $row['rating_normal']);?></a>
                </td>
                <?php
            }
            if (!empty($rating_best_tt)) {
                ?>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo sprintf("%.2f", $row['rating_best_tt']);?></a>
                </td>
                <?php
            }
            if (!empty($rating_eisen_tt)) {
                ?>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo sprintf("%.2f", $row['rating_eisen_tt']);?></a>
                </td>
                <?php
            }
            if (!empty($rating_chemie_tt)) {
                ?>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo sprintf("%.2f", $row['rating_chemie_tt']);?></a>
                </td>
                <?php
            }
            if (!empty($rating_eis_tt)) {
                ?>
                <td>
                    <a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>"><?php echo sprintf("%.2f", $row['rating_eis_tt']);?></a>
                </td>
                <?php
            }
            ?>
            </tr>
        <?php
        }
    } ?>
    </table>
<?php }