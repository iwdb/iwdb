<?php
/*****************************************************************************
 * searchdb.php                                                              *
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

$orderprio = array(
    "Koordinaten"       => "Koordinaten",
    "rating_normal"     => "Rating ohne Techteam",
    "rating_best_tt"    => "Rating bestes Techteam",
    "rating_eisen_tt"   => "Rating Techteam Eisen",
    "rating_chemie_tt"  => "Rating Techteam Chemie",
    "rating_eis_tt"     => "Rating Techteam Eis",
    "eisengehalt"       => "Eisen",
    "chemievorkommen"   => "Chemie",
    "eisdichte"         => "Eis",
    "lebensbedingungen" => "Lebensbedingungen",
    "gravitation"       => "Gravitation",
    "typ"               => "Planetentyp",
    "objekt"            => "Objekttyp",
    "user"              => "Spielername",
    "allianz"           => "Allianz",
    "kgmod"             => "Gebäudekosten",
    "dgmod"             => "Gebäudedauer",
    "ksmod"             => "Schiffskosten",
    "dsmod"             => "Schiffsdauer",
    "fmod"              => "Forschungsmultiplikator"
);

$ratingtypes = array(
    "rating_normal"    => "Rating ohne Techteam",
    "rating_best_tt"   => "Rating bestes Techteam",
    "rating_eisen_tt"  => "Rating Techteam Eisen",
    "rating_chemie_tt" => "Rating Techteam Chemie",
    "rating_eis_tt"    => "Rating Techteam Eis",
);

$orderpriod = array(
    "ASC"  => "aufsteigend",
    "DESC" => "absteigend"
);

$typ_type = array(
    "%"            => "Alle",
    "Steinklumpen" => "Steinklumpen",
    "Asteroid"     => "Asteroid",
    "Eisplanet"    => "Eisplanet",
    "Gasgigant"    => "Gasgigant",
    "Nichts"       => "Nichts"
);

$objekt_type = array(
    "%"           => "Alle",
    "---"         => "unbewohnt",
    "bewohnt"     => "bewohnt",
    "Kolonie"     => "Kolonie",
    "%basis"      => "alle Basen",
    "Kampfbasis"  => "Kampfbasis",
    "Sammelbasis" => "Sammelbasis"
);

$ansichten = array(
    "auto"       => "automatisch",
    "geologisch" => "geologisch",
    "taktisch"   => "taktisch",
    "beide"      => "geologisch und taktisch"
);

$techteams = array(
    "keinTT"         => "kein Techteam berücksichtigen",
    "EisenTT"  => "Techteam Eisen berücksichtigen",
    "ChemieTT" => "Techteam Chemie berücksichtigen",
    "EisTT"    => "Techteam Eis berücksichtigen"
);

$merkmale = array(
    "%"                 => "---",
    "Asteroidengürtel"  => "Astrogürtel",
    "Gold"              => "Gold",
    "instabiler Kern"   => "instabil",
    "Mond"              => "Mond",
    "planetarer Ring"   => "planetarer Ring",
    "Natürliche Quelle" => "Quelle",
    "radioaktiv"        => "radioaktiv",
    "wenig Rohstoffe"   => "Rohstoffmangel",
    "alte Ruinen"       => "Ruinen",
    "Ureinwohner"       => "Ureinwohner",
    "toxisch"           => "toxisch"
);

$sql = "SELECT * FROM " . $db_tb_user . " WHERE sitterlogin = '" . $user_sitterlogin . "'";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);

$gal_start = $row['gal_start']; $gal_end = $row['gal_end']; $sys_start = $row['sys_start']; $sys_end = $row['sys_end'];
$grav_von = $row['grav_von']; $grav_bis = $row['grav_bis'];

$preset = getVar('preset');
$preset = (empty($preset)) ? $row['preset'] : $preset;


if (!empty($preset)) {
    $sql = "SELECT * FROM " . $db_tb_preset . " WHERE id = '" . $preset . "'";
    $result = $db->db_query($sql);
    $row = $db->db_fetch_array($result);
    foreach ($row as $key => $data) {
        if ($data <> "x") {
            ${$key} = $data;
        }
    }
}

doc_title('Planet suchen');
?>
<table class="table_format" style="width: 80%;">
<tr>
    <td colspan="2" class="windowbg2 center"><br>

        <form method="POST" action="index.php?action=searchdb" enctype="multipart/form-data">
            <select name="preset" style="width: 100px;" onchange="this.form.submit();">
                <?php
                $sql = "SELECT id, name FROM " . $db_tb_preset . " WHERE (fromuser = '" . $user_sitterlogin . "' OR fromuser = '')";
                $result = $db->db_query($sql);
                while ($row = $db->db_fetch_array($result)) {
                    echo ($preset == $row['id']) ? "<option value='" . $row['id'] . "' selected>" . $row['name'] . "</option>\n" : "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>\n";
                }
                ?>
            </select>
        </form>
        <br>
    </td>
</tr>
<form method="POST" action="index.php?action=showgalaxy" enctype="multipart/form-data">
<tr>
    <td colspan="2" class="titlebg"><b>Bereich:</b></td>
</tr>
<tr>
    <td class="windowbg2" style="width: 40%;">Galaxie:<br>
        <i>In welchem Galaxiebereich sollen Planeten gesucht werden?</i></td>
    <td class="windowbg1">von <input type="text" name="gal_start" value="<?php echo ((isset($gal_start)) ? $gal_start : '')?>" style="width: 5em">
        bis <input type="text" name="gal_end" value="<?php echo ((isset($gal_end)) ? $gal_end : '')?>" style="width: 5em"></td>
</tr>
<tr>
    <td class="windowbg2">System:<br>
        <i>In welchem Systemberich sollen Planeten gesucht werden?</i></td>
    <td class="windowbg1">von <input type="text" name="sys_start" value="<?php echo ((isset($sys_start)) ? $sys_start : '')?>" style="width: 5em">
        bis <input type="text" name="sys_end" value="<?php echo ((isset($sys_end)) ? $sys_end : '')?>" style="width: 5em"></td>
</tr>
<tr>
    <td colspan="2" class="titlebg"><b>Eigenschaften:</b></td>
</tr>
<tr>
    <td class="windowbg2">Planetentyp:</td>
    <td class="windowbg1"><select name="typ" style="width: 15em">
            <?php
            $typ = (isset($typ)) ? $typ : '';
            foreach ($typ_type as $key => $data) {
                echo ($typ == $key) ? " <option value='" . $key . "' selected>" . $data . "</option>\n" : " <option value='" . $key . "'>" . $data . "</option>\n";
            }
            ?>
        </select></td>
</tr>
<tr>
    <td class="windowbg2">Objekttyp:</td>
    <td class="windowbg1"><select name="objekt" style="width: 15em">
            <?php
            $objekt = (isset($objekt)) ? $objekt : '';
            foreach ($objekt_type as $key => $data) {
                echo ($objekt == $key) ? " <option value='" . $key . "' selected>" . $data . "</option>\n" : " <option value='" . $key . "'>" . $data . "</option>\n";
            }
            ?>
        </select></td>
</tr>
<tr>
    <td class="windowbg2">Gravitation:<br>
        <i>Wie viel Gravitation soll der Planet mindestens und maximal haben?</i>
    </td>
    <td class="windowbg1">von <input type="text" name="grav_von"
                                     value="<?php echo ((isset($grav_von)) ? $grav_von : '')?>" style="width: 5em"
                                     maxlength="3"> bis <input type="text" name="grav_bis"
                                                               value="<?php echo ((isset($grav_bis)) ? $grav_bis : '')?>"
                                                               style="width: 5em"
                                                               maxlength="3">
        <?php
        $grav_von = str_replace(",", ".", $grav_von);
        $grav_bis = str_replace(",", ".", $grav_bis);
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">Spielername (mehrere mit ; getrennt):<br>
        <i>Planeten eines bestimmten Spielers suchen</i></td>
    <td class="windowbg1">
        <input type="text" name="user" value="<?php echo ((isset($user)) ? $user : '')?>" style="width: 15em"><br>
        <div><input type="checkbox" name="exact" class="middle" value="1" <?php echo (isset($exact) && $exact) ? "checked" : "";?>><span class="middle">exakte Suche?</span></div>
    </td>
</tr>
<tr>
    <td class="windowbg2">Allianzen (mehrere mit ; getrennt):<br>
        <i>Planeten einer bestimmten Allianz suchen</i></td>
    <td class="windowbg1"><input type="text" name="allianz" value="<?php echo ((isset($allianz)) ? $allianz : '');?>" style="width: 15em">
    </td>
</tr>
<tr>
    <td class="windowbg2">Planetenname:<br>
        <i>Nach Planetennamen suchen</i></td>
    <td class="windowbg1"><input type="text" name="planetenname" value="<?php echo ((isset($planetenname)) ? $planetenname : '');?>" style="width: 15em"></td>
</tr>
<tr>
    <td colspan="2" class="titlebg"><b>Modifikationen:</b><br>
        <i>Welche Modifikationen soll der Planet aufweisen?</i></td>
</tr>
<tr>
    <td class="windowbg2">&nbsp;</td>
    <td class="windowbg1">maximal</td>
</tr>
<tr>
    <td class="windowbg2">Gebäudekosten:</td>
    <td class="windowbg1">
        <input type="text" name="kgmod" value="<?php echo ((isset($kgmod)) ? $kgmod : '')?>" style="width: 15em" maxlength="5">
    </td>
</tr>
<tr>
    <td class="windowbg2">Gebäudedauer:</td>
    <td class="windowbg1">
        <input type="text" name="dgmod" value="<?php echo ((isset($dgmod)) ? $dgmod : '')?>" style="width: 15em" maxlength="5">
    </td>
</tr>
<tr>
    <td class="windowbg2">Schiffkosten:</td>
    <td class="windowbg1">
        <input type="text" name="ksmod" value="<?php echo ((isset($ksmod)) ? $ksmod : '')?>" style="width: 15em" maxlength="5">
    </td>
</tr>
<tr>
    <td class="windowbg2">Schiffdauer:</td>
    <td class="windowbg1">
        <input type="text" name="dsmod" value="<?php echo ((isset($dsmod)) ? $dsmod : '')?>" style="width: 15em" maxlength="5">
    </td>
</tr>
<tr>
   <td class="windowbg2">Forschung:</td>
   <td class="windowbg1">
      <input type="text" name="fmod_bis" value="<?php echo ((isset($fmod)) ? $fmod : '')?>" style="width: 15em" maxlength="5">
   </td>
 </tr>
 <tr>
<tr>
    <td colspan="2" class="titlebg"><b>Ressourcen (min):</b><br>
        <i>Welche Ressourcenwerte soll der Planet mindestens aufweisen?</i>
    </td>
</tr>
<tr>
    <td class="windowbg2">Eisengehalt:</td>
    <td class="windowbg1">
        <input type="text" name="eisengehalt" value="<?php echo ((isset($eisengehalt)) ? $eisengehalt : '')?>" style="width: 15em" maxlength="3">
    </td>
</tr>
<tr>
    <td class="windowbg2">Chemievorkommen:</td>
    <td class="windowbg1">
        <input type="text" name="chemievorkommen" value="<?php echo ((isset($chemievorkommen)) ? $chemievorkommen : '')?>" style="width: 15em" maxlength="3">
    </td>
</tr>
<tr>
    <td class="windowbg2">Eisdichte:</td>
    <td class="windowbg1">
        <input type="text" name="eisdichte" value="<?php echo ((isset($eisdichte)) ? $eisdichte : '')?>" style="width: 15em" maxlength="3">
    </td>
</tr>
<tr>
    <td class="windowbg2">Techteams:</td>
    <td class="windowbg1">
            <?php
            $techteam = (isset($techteam)) ? $techteam : 'keinTT';
            echo makeField(
                array(
                     "type"   => 'select',
                     "values" => $techteams,
                     "value"  => $techteam
                ), 'techteam'
            );
            ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">Lebensbedingungen:</td>
    <td class="windowbg1">
        <input type="text" name="lebensbedingungen" value="<?php echo ((isset($lebensbedingungen)) ? $lebensbedingungen : '')?>" style="width: 15em" maxlength="3">
    </td>
</tr>
<tr>
    <td class="windowbg2">Besonderheiten:</td>
    <td class="windowbg1">
        <?php
        $merkmal = (isset($merkmal)) ? $merkmal : '';
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $merkmale,
                 "value"  => $merkmal
            ), 'merkmal'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">Rating:</td>
    <td class="windowbg1">
        <input type="text" name="ratingmin" value="<?php echo ((isset($ratingmin)) ? $ratingmin : '')?>" style="width: 15em" maxlength="6">
        <?php
        $ratingtyp = (isset($ratingtyp)) ? $ratingtyp : 'rating_normal';
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $ratingtypes,
                 "value"  => $ratingtyp
            ), 'ratingtyp'
        );
        ?>
    </td>
</tr>
<tr>
    <td colspan="2" class="titlebg"><b>Sortierung:</b><br>
        <i>Nach was sollen die Suchergebnisse sortiert werden?</i></td>
</tr>
<tr>
    <td colspan="2" class="windowbg1 center">
        <?php
            $order1 = (isset($order1)) ? $order1 : "Koordinaten";
            echo makeField(
                array(
                     "type"   => 'select',
                     "values" => $orderprio,
                     "value"  => $order1
                ), 'order1'
            );
            $order1_d = (isset($order1_d)) ? $order1_d : "Koordinaten";
            echo makeField(
                array(
                     "type"   => 'select',
                     "values" => $orderpriod,
                     "value"  => $order1_d
                ), 'order1_d'
            );
        ?>
    </td>
</tr>
<tr>
    <td colspan="2" class="windowbg1 center">
        <?php
        $order2 = (isset($order2)) ? $order2 : "Koordinaten";
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $orderprio,
                 "value"  => $order2
            ), 'order2'
        );
        $order2_d = (isset($order2_d)) ? $order2_d : "Koordinaten";
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $orderpriod,
                 "value"  => $order2_d
            ), 'order2_d'
        );
        ?>
    </td>
</tr>
<tr>
    <td colspan="2" class="windowbg1 center">
        <?php
        $order3 = (isset($order3)) ? $order3 : "Koordinaten";
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $orderprio,
                 "value"  => $order3
            ), 'order3'
        );
        $order3_d = (isset($order3_d)) ? $order3_d : "Koordinaten";
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $orderpriod,
                 "value"  => $order3_d
            ), 'order3_d'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg1 center" colspan="2">maximale Ergebnisse:
        <input type="text" name="max" value="<?php echo ((isset($max)) ? $max : '100')?>" style="width: 15em" maxlength="4">
    </td>
</tr>
<tr>
    <td class="windowbg1 center" colspan="2">Ansicht:
        <?php
            $ansicht = (isset($ansicht)) ? $ansicht : "auto";
            echo makeField(
                array(
                     "type"   => 'select',
                     "values" => $ansichten,
                     "value"  => $ansicht
                ), 'ansicht'
            );
        ?>
    </td>
</tr>
<tr>
    <td colspan="2" class="titlebg center"><input type="submit" value="OK" name="B1"></td>
</tr>
<tr>
    <td colspan="2" class="titlebg center">als Preset speichern? <input type="checkbox" name="newpreset" value="1"> <?php
        if ($user_status == "admin") {
            if ((isset($fromuser)) && ($fromuser == "")) {
                echo "global? <input type='checkbox' name='global' value='1' checked>";
            } else {
                echo "global? <input type='checkbox' name='global' value='1'>";
            }
        }
        ?> <br>
        ändern: <select name="presetname1" style="width: 10em;">
            <?php
            if ($user_status === "admin") {
                $sql = "SELECT id, name FROM " . $db_tb_preset . " WHERE fromuser = '" . $user_sitterlogin . "' OR fromuser = '' ORDER BY fromuser, name";
            } else {
                $sql = "SELECT id, name FROM " . $db_tb_preset . " WHERE fromuser = '" . $user_sitterlogin . "'";
            }
            $result = $db->db_query($sql);
            while ($row = $db->db_fetch_array($result)) {
                echo ($preset == $row['id']) ? "<option value='" . $row['id'] . "' selected>" . $row['name'] . "</option>\n" : "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>\n";
            }
            ?>
        </select> oder neu: <input type="text" name="presetname2" value="" style="width: 15em"></td>
</tr>
</form>
</table>