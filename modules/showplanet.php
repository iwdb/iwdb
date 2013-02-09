<?php
/*****************************************************************************
 * showplanet.php                                                            *
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
global $sid, $db;

doc_title('Planet');

$ansicht = getVar('ansicht');
$ansicht = (empty($ansicht)) ? "auto" : $ansicht;
if (!isset($coords)) {
    $coords = getVar('coords');
}
$order = getVar('order');

if (!empty($coords)) {
    //alle Planieinformationen holen
    $sql = "SELECT * FROM " . $db_tb_scans . " WHERE coords='" . $coords . "' " . $order;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    //Planetenreservierung
    $editplanet  = getVar('reserveplanie');
    if ((!empty($editplanet)) AND ( empty($row['reserviert']) OR ($row['reserviert'] == $user_sitterlogin) OR ($user_status == "admin") )) {
        $reservieren = getVar('reservieren');
        $reservieren = (empty($reservieren)) ? "" : $user_sitterlogin;

        if ($reservieren !== $row['reserviert']) {
            $result = $db->db_update($db_tb_scans, array('reserviert' => $reservieren), "WHERE coords='" . $coords . "'")
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

            echo "<div class='system_notification'>Planetenreservierung geändert.</div>";
            $row['reserviert'] = $reservieren;
        }
    }

    //Notizen eintragen
    $submitnotice = getVar('submitnotice');
    if (!empty($submitnotice)) {
        $notice = getVar('notice');

        $result = $db->db_update($db_tb_scans, array('rnb' => $notice), "WHERE coords='" . $coords . "'")
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
    } else {
        //Notizen aufrufen
        $sql = "SELECT rnb FROM " . $db_tb_scans . " WHERE coords='" . $coords . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $result = $db->db_fetch_array($result);
        $notice = $result["rnb"];
    }

    //Allianzstatus holen
    $sql = "SELECT status FROM " . $db_tb_allianzstatus . " WHERE allianz LIKE '" . $row['allianz'] . "'";
    $result_status = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row_status = $db->db_fetch_array($result_status);
    if (isset($config_allianzstatus[$row_status['status']])) {
        $color = $config_allianzstatus[$row_status['status']];
    } else {
        $color = "white";
    }

    $rating = rating($row);

    if (empty($row['dgmod'])) {
        $eisen_effektiv    = null;
        $chemie_effektiv   = null;
        $eis_effektiv      = null;
        $ttchemie_effektiv = null;
        $tteisen_effektiv  = null;
        $tteis_effektiv    = null;
    } else {
        $eisen_effektiv    = $row['eisengehalt'] / $row['dgmod'];
        $tteisen_effektiv  = $row['tteisen'] / $row['dgmod'];
        $chemie_effektiv   = $row['chemievorkommen'] / $row['dgmod'];
        $ttchemie_effektiv = $row['ttchemie'] / $row['dgmod'];
        $eis_effektiv      = $row['eisdichte'] / $row['dgmod'];
        $tteis_effektiv    = $row['tteis'] / $row['dgmod'];
    }

}
?>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
<?php
if ($user_planibilder == "1") {
    $path = "bilder/planeten/200x200/";
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
        $path = 'bilder/planeten/200x200/schwarzesloch.jpg';
    }
    ?>
    <tr>
        <td colspan="2" align="center"><img src="<?php echo $path;?>" alt="<?php echo $row['typ']?>"></td>
    </tr>
<?php
}
?>
<tr>
    <td colspan="2" class="titlebg"><b>Daten:</b></td>
</tr>
<tr>
    <td class="windowbg2" style="width: 20%;">Koordinaten:</td>
    <td class="windowbg1"><?php echo $row['coords'];?></td>
</tr>
<tr>
    <td class="windowbg2">letztes Update:</td>
    <td class="windowbg1"><?php echo (empty($row['time'])) ? "/" : round((CURRENT_UNIX_TIME - $row['time']) / DAY) . " Tage"; ?></td>
</tr>
<?php
if ((($ansicht == "auto") && ($row['objekt'] != "---")) || ($ansicht == "taktisch") || ($ansicht == "beide")) {
    ?>
    <tr>
        <td class="windowbg2">Spielername:</td>
        <td class="windowbg1"><?php echo $row['user'];?></td>
    </tr>
    <tr>
        <td class="windowbg2">Allianz:</td>
        <td class="windowbg1" style=" background-color: <?php echo $color;?>;"><a
                href="index.php?action=showgalaxy&allianz=<?php echo $row['allianz'];?>&sid=<?php echo $sid;?>"><?php echo $row['allianz']; echo ((empty($row_status['status'])) || ($row_status['status'] == 'own')) ? "" : " (" . $row_status['status'] . ")";?></a>
        </td>
    </tr>
    <tr>
        <td class="windowbg2">Planetennamen:</td>
        <td class="windowbg1"><?php echo $row['planetenname'];?></td>
    </tr>
<?php
}
?>
<tr>
    <td colspan="2" class="titlebg"><b>Eigenschaften:</b></td>
</tr>
<tr>
    <td class="windowbg2">Planetentyp:</td>
    <td class="windowbg1"><?php echo $row['typ'];?></td>
</tr>
<tr>
    <td class="windowbg2">Objekttyp:</td>
    <td class="windowbg1"><?php echo $row['objekt'];?></td>
</tr>
<tr>
    <td colspan="2" class="titlebg"><b>Notizen:</b></td>
</tr>
<tr>
    <td class="windowbg2"><i>Hier bitte jegliche Informationen über diesen Planeten, sei es Raids, Uhrzeiten,
            Absprachen, Tipps für Raider eingeben.</i></td>
    <td class="windowbg1">

        <form method='POST' action='index.php?action=showplanet&coords=<?php echo $coords; ?>&sid=<?php echo $sid; ?>&ansicht=auto' enctype='multipart/form-data'>
        <table border='0' cellpadding='5' cellspacing='0' class='bordercolor' style='width: 80%;' align='center'>
            <tr>
                <td class='windowbg2' align='center'>
                    <textarea name='notice' rows='10' cols='80'><?php echo $notice; ?></textarea>
                </td>
            </tr>
            <tr>
                <td class='titlebg' align='center'>
                    <input type='submit' name='submitnotice' value='Speichern' class='submit'>
                    &nbsp;&nbsp;
                    <input type='reset' class='submit'>
                </td>
            </tr>
        </table>
        </form>
    </td>
</tr>
<?php
if (((($ansicht == "auto")) || ($ansicht == "geologisch") || ($ansicht == "beide")) AND !empty($row['geoscantime'])) {
    ?>
    <tr>
        <td colspan="2" class="titlebg"><b>Geologie:</b></td>
    </tr>
    <tr>
        <td class="windowbg2">Gravitation:</td>
        <td class="windowbg1"><?php echo $row['gravitation'];?></td>
    </tr>
    <tr>
        <td class="windowbg2">Forschungsmod.:</td>
        <td class="windowbg1"><?php echo ($row['fmod'] < 100) ? "<div class='doc_red'>" . $row['fmod'] . " %</div>" : $row['fmod'] . ' %';?></td>
    </tr>
    <tr>
        <td class="windowbg2">Gebäudekostenmod.:</td>
        <td class="windowbg1"><?php echo ($row['kgmod'] > 1) ? "<div class='doc_red'>" . $row['kgmod'] . "</div>" : $row['kgmod'];?></td>
    </tr>
    <tr>
        <td class="windowbg2">Gebäudedauermod.:</td>
        <td class="windowbg1"><?php echo ($row['dgmod'] > 1) ? "<div class='doc_red'>" . $row['dgmod'] . "</div>" : $row['dgmod'];?></td>
    </tr>
    <tr>
        <td class="windowbg2">Schiffskostenmod.:</td>
        <td class="windowbg1"><?php echo ($row['ksmod'] > 1) ? "<div class='doc_red'>" . $row['ksmod'] . "</div>" : $row['ksmod'];?></td>
    </tr>
    <tr>
        <td class="windowbg2">Schiffsdauermod.:</td>
        <td class="windowbg1"><?php echo ($row['dsmod'] > 1) ? "<div class='doc_red'>" . $row['dsmod'] . "</div>" : $row['dsmod'];?></td>
    </tr>
    <tr>
        <td class="windowbg2">Planetengröße:</td>
        <td class="windowbg1"><?php echo number_format($row['bevoelkerungsanzahl'], 0, ',', '.');?></td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg"><b>Ressourcen:</b></td>
    </tr>
    <tr>
        <td class="windowbg2">Eisengehalt:</td>
        <td class="windowbg1"><?php echo ($row['eisengehalt'] > 100) ? "<b>" . $row['eisengehalt'] . " %</b>" : $row['eisengehalt'] . ' %'; echo  " (effektiv ";  echo ($eisen_effektiv) ? "<b>" . round($eisen_effektiv, 1) . "%</b>)" : round($eisen_effektiv, 1) . "%)"; echo  ", mit TechTeam ";  echo ($row['tteisen'] > 100) ? "<b>" . $row['tteisen'] . "%</b>" : $row['tteisen'] . "%"; echo  " (effektiv ";  echo ($tteisen_effektiv) ? "<b>" . round($tteisen_effektiv, 1) . "%</b>)" : round($tteisen_effektiv, 1) . "%)";?></td>
    </tr>
    <tr>
        <td class="windowbg2">Chemievorkommen:</td>
        <td class="windowbg1"><?php echo ($row['chemievorkommen'] > 100) ? "<b>" . $row['chemievorkommen'] . " %</b>" : $row['chemievorkommen'] . ' %'; echo  " (effektiv ";  echo ($chemie_effektiv) ? "<b>" . round($chemie_effektiv, 1) . "%</b>)" : round($chemie_effektiv, 1) . "%)"; echo  ", mit TechTeam ";  echo ($row['ttchemie'] > 100) ? "<b>" . $row['ttchemie'] . "%</b>" : $row['ttchemie'] . "%"; echo  " (effektiv ";  echo ($ttchemie_effektiv) ? "<b>" . round($ttchemie_effektiv, 1) . "%</b>)" : round($ttchemie_effektiv, 1) . "%)";?></td>
    </tr>
    <tr>
        <td class="windowbg2">Eisdichte:</td>
        <td class="windowbg1"><?php echo ($row['eisdichte'] > 30) ? "<b>" . $row['eisdichte'] . " %</b>" : $row['eisdichte'] . ' %'; echo  " (effektiv ";  echo ($eis_effektiv) ? "<b>" . round($eis_effektiv, 1) . "%</b>)" : round($eis_effektiv, 1) . "%)"; echo  ", mit TechTeam ";  echo ($row['tteis'] > 30) ? "<b>" . $row['tteis'] . "%</b>" : $row['tteis'] . "%"; echo  " (effektiv ";  echo ($tteis_effektiv) ? "<b>" . round($tteis_effektiv, 1) . "%</b>)" : round($tteis_effektiv, 1) . "%)";?></td>
    </tr>
    <tr>
        <td class="windowbg2">Lebensbedingungen:</td>
        <td class="windowbg1"><?php echo ($row['lebensbedingungen'] > 100) ? "<b>" . $row['lebensbedingungen'] . " %</b>" : $row['lebensbedingungen'] . ' %';?></td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg"><b>Besonderheiten:</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2"><?php echo  !empty($row['besonderheiten']) ? str_replace(", ", "<br>", $row['besonderheiten']) : "keine *moep*";?></td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg"><b>Sprengung:</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2">
            <?php
            $reset_timestamp_first = (($row['geoscantime'] + $row['reset_timestamp']) - DAY);   //vorverlegen des Sprengdatums wegen +-24h
            if ($reset_timestamp_first > CURRENT_UNIX_TIME) {
                echo "Noch mindestens " . makeduration2(CURRENT_UNIX_TIME, $reset_timestamp_first) . " bis der Planet für etwas anderes tolles gesprengt wird.\n";
            } elseif (($reset_timestamp_first + 2 * DAY) > CURRENT_UNIX_TIME) { // 2 Tage Toleranz
                echo "Evl. seit " . makeduration2($reset_timestamp_first, CURRENT_UNIX_TIME) . " gesprengt.\n";
            } else {
                echo "Wahrscheinlich gesprengt!"; //alles was drüber ist, ist wohl weg
            }
            ?>
        </td>
    </tr>
<?php
}
if ($row['objekt'] == "---") {
    ?>
    <tr>
        <td colspan="2" class="titlebg"><b>Rating:</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2" align="center">
            <b><?php echo (!empty($rating) ? "<div class='doc_big_black'>" . $rating : "<div class='doc_red'>Kein Rating berechenbar, neuer Geoscan erforderlich");?>
                </div>
            </b></td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg"><b>Reservieren:</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2" align="center">
            <form method="POST" action="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&sid=<?php echo $sid;?>" enctype="multipart/form-data">
                <?php
                if (empty($row['reserviert'])) {
                    echo "Diesen Planeten für dich reservieren? <input type='checkbox' name='reservieren'>
                    <input type='submit' value='speichern' name='reserveplanie' class='submit'>";
                } elseif ((isset($user_sitterlogin)) AND ($row['reserviert'] === $user_sitterlogin)) {
                    echo "Diesen Planeten für dich reservieren? <input type='checkbox' name='reservieren' checked>
                    <input type='submit' value='speichern' name='reserveplanie' class='submit'>";
                } else {
                    echo "Dieser Planet ist für " . $row['reserviert'] . " reserviert. Bitte besiedel ihn nicht.";
                    if ((isset($user_status)) && ($user_status == "admin")) {
                        echo "<br>Ändern? <input type='checkbox' name='reservieren' checked>
                        <input type='submit' value='speichern' name='reserveplanie' class='submit'>";
                    }
                }
                ?>
            </form>
        </td>
    </tr>
<?php
}
if (((($ansicht == "auto") && ($row['objekt'] != "---")) || ($ansicht == "taktisch") || ($ansicht == "beide")) AND (!empty($row['schiffscantime']) OR !empty($row['gebscantime']))) {
    $class1 = $row['eisen'] + 2 * $row['stahl'] + 4 * $row['vv4a'] + 3 * $row['chemie'];
    $class2 = 2 * $row['eis'] + 2 * $row['wasser'] + $row['energie'];
    ?>
    <tr>
        <td colspan="2" class="titlebg"><b>auf Lager:</b></td>
    </tr>
    <tr>
        <td class="windowbg2">Eisen:</td>
        <td class="windowbg1"><?php echo number_format($row['eisen'], 0, ',', '.');?></td>
    </tr>
    <tr>
        <td class="windowbg2">Stahl:</td>
        <td class="windowbg1"><?php echo number_format($row['stahl'], 0, ',', '.');?></td>
    </tr>
    <tr>
        <td class="windowbg2">VV4A:</td>
        <td class="windowbg1"><?php echo number_format($row['vv4a'], 0, ',', '.');?></td>
    </tr>
    <tr>
        <td class="windowbg2">Chemie:</td>
        <td class="windowbg1"><?php echo number_format($row['chemie'], 0, ',', '.');?></td>
    </tr>
    <tr>
        <td class="windowbg2">Eis:</td>
        <td class="windowbg1"><?php echo number_format($row['eis'], 0, ',', '.');?></td>
    </tr>
    <tr>
        <td class="windowbg2">Wasser:</td>
        <td class="windowbg1"><?php echo number_format($row['wasser'], 0, ',', '.');?></td>
    </tr>
    <tr>
        <td class="windowbg2">Energie:</td>
        <td class="windowbg1"><?php echo number_format($row['energie'], 0, ',', '.');?></td>
    <tr>
        <td colspan="2" class="titlebg"><b>benötigte Frachtkapazität:</b></td>
    </tr>
    <tr>
        <td class="windowbg2">Klasse 1:</td>
        <td class="windowbg1"><?php echo number_format($class1, 0, ',', '.');?></td>
    </tr>
    <tr>
        <td class="windowbg2">Klasse 2:</td>
        <td class="windowbg1"><?php echo number_format($class2, 0, ',', '.');?></td>
    </tr>

    <?php
    if (!empty($row['lager_chemie']) AND !empty($row['lager_eis']) AND !empty($row['lager_energie'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>Lagerkapazität:</b></td>
        </tr>
        <tr>
            <td class="windowbg2">Lager Chemie:</td>
            <td class="windowbg1"><?php echo number_format($row['lager_chemie'], 0, ',', '.');?></td>
        </tr>
        <tr>
            <td class="windowbg2">Lager Eis:</td>
            <td class="windowbg1"><?php echo number_format($row['lager_eis'], 0, ',', '.');?></td>
        </tr>
        <tr>
            <td class="windowbg2">Lager Energie:</td>
            <td class="windowbg1"><?php echo number_format($row['lager_energie'], 0, ',', '.');?></td>
        </tr>

    <?php
    }

    if (!empty($row['geb'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>Gebäude:</b></td>
        </tr>
        <tr>
            <td class="windowbg1" colspan="2"><?php echo $row['geb'];?></td>
        </tr>
    <?php
    }

    if (!empty($row['plan'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>planetare Flotte:</b></td>
        </tr>
        <tr>
            <td class="windowbg1" colspan="2"><?php echo $row['plan'];?></td>
        </tr>
    <?php
    }

    if (!empty($row['stat'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>stationierte Flotte:</b></td>
        </tr>
        <tr>
            <td class="windowbg1" colspan="2"><?php echo $row['stat'];?></td>
        </tr>
    <?php
    }

    if (!empty($row['def'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>Verteidigung:</b></td>
        </tr>
        <tr>
            <td class="windowbg1" colspan="2"><?php echo $row['def'];?></td>
        </tr>

    <?php
    }

}
?>
</table>