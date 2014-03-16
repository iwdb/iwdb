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

global $db, $db_tb_scans, $user_sitterlogin, $user_status, $db_tb_allianzstatus, $user_planibilder, $db_tb_spieler;

$ansicht = ensureValue(getVar('ansicht'), array("auto", "taktisch", "geologisch", "beide"), "auto");
$coords = filter_coords(getVar('coords'));
if (empty($coords)) {
    exit('Hacking attempt...');
}

doc_title('Planet');

//alle Planieinformationen holen
$sql_planie = "SELECT * FROM `{$db_tb_scans}` WHERE `coords`='" . $coords . "'";
$result_planie = $db->db_query($sql_planie);
$row_planie = $db->db_fetch_array($result_planie);

//Planetenreservierung aktualisieren
$reserveplanie = getVar('reserveplanie');
if ((!empty($reserveplanie)) AND (empty($row_planie['reserviert']) OR ($row_planie['reserviert'] === $user_sitterlogin) OR ($user_status === "admin"))) {
    $reserve_to = (getVar('reservieren')) ? $user_sitterlogin : "";

    if ($reserve_to !== $row_planie['reserviert']) {
        $result = $db->db_update($db_tb_scans, array('reserviert' => $reserve_to), "WHERE coords='" . $coords . "'");

        echo "<div class='system_notification'>Planetenreservierung geändert.</div>";
        $row_planie['reserviert'] = $reserve_to;
    }
}

$submitnotice = getVar('submitnotice');
if (!empty($submitnotice)) {
    //Notizen eintragen

    $notice = getVar('notice');

    $result = $db->db_update($db_tb_scans, array('rnb' => $notice), "WHERE coords='" . $coords . "'");

    $sql_name = "SELECT `user` FROM `{$db_tb_scans}` WHERE `coords`='" . $coords . "'";
    $result_name = $db->db_query($sql_name);
    $row_name = $db->db_fetch_array($result_name);
    $name     = $row_name['user'];

    $data = array(
        'gesperrt' => getVar('gesperrt'),
        'umode'    => getVar('umode'),
		'einmaurer'=> getVar('einmaurer')
    );
    $result = $db->db_update($db_tb_spieler, $data, "WHERE `name`='" . $name . "'");

} else {
    //Notizen aufrufen

    $sql = "SELECT `rnb` FROM `{$db_tb_scans}` WHERE `coords`='" . $coords . "'";
    $result = $db->db_query($sql);
    $result = $db->db_fetch_array($result);
    $notice = $result["rnb"];

    $sql_name = "SELECT `user` FROM `{$db_tb_scans}` WHERE `coords`='" . $coords . "'";
    $result_name = $db->db_query($sql_name);
    $name = $result_name['user'];

    $sql_spieler = "SELECT `gesperrt`, `umode`, `einmaurer` FROM `{$db_tb_spieler}` WHERE `name`='" . $name . "'";
    $result_spieler = $db->db_query($sql_spieler);
    $result_spieler = $db->db_fetch_array($result_spieler);
}

//Allianzstatus holen
$sql_allystatus = "SELECT `status` FROM `{$db_tb_allianzstatus}` WHERE `allianz` LIKE '" . $row_planie['allianz'] . "'";
$result_allystatus = $db->db_query($sql_allystatus);
$row_allystatus = $db->db_fetch_array($result_allystatus);
if (isset($config_allianzstatus[$row_allystatus['status']])) {
    $color = $config_allianzstatus[$row_allystatus['status']];
} else {
    $color = "white";
}

//Spielerstatus holen
$sql_spieler = "SELECT `umode`, `gesperrt`, `einmaurer` FROM `{$db_tb_spieler}` WHERE `name`='" . $row_planie['user'] . "'";
$result_spieler = $db->db_query($sql_spieler);
$row_spieler = $db->db_fetch_array($result_spieler);

$rating = rating($row_planie);

if (empty($row_planie['dgmod'])) {
    $eisen_effektiv    = null;
    $chemie_effektiv   = null;
    $eis_effektiv      = null;
    $ttchemie_effektiv = null;
    $tteisen_effektiv  = null;
    $tteis_effektiv    = null;
} else {
    $eisen_effektiv    = $row_planie['eisengehalt'] / $row_planie['dgmod'];
    $tteisen_effektiv  = $row_planie['tteisen'] / $row_planie['dgmod'];
    $chemie_effektiv   = $row_planie['chemievorkommen'] / $row_planie['dgmod'];
    $ttchemie_effektiv = $row_planie['ttchemie'] / $row_planie['dgmod'];
    $eis_effektiv      = $row_planie['eisdichte'] / $row_planie['dgmod'];
    $tteis_effektiv    = $row_planie['tteis'] / $row_planie['dgmod'];
}

?>
<br>
<table class="table_format bordercolor left" style="width: 80%;">
<?php
if ($user_planibilder) {
    $path = "bilder/planeten/200x200/";
    switch ($row_planie['typ']) {
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
    if ($row_planie['objekt'] === "Schwarzes Loch") {
        $path = BILDER_PATH . 'planeten/200x200/schwarzesloch.jpg';
    }
    ?>
    <tr>
        <td colspan="2" class="center"><img src="<?php echo $path; ?>" alt="<?php echo $row_planie['typ'] ?>"></td>
    </tr>
<?php
}
?>
<tr>
    <td colspan="2" class="titlebg"><b>Daten:</b></td>
</tr>
<tr>
    <td class="windowbg2" style="width: 20%;">Koordinaten:</td>
    <td class="windowbg1"><?php echo $row_planie['coords']; ?></td>
</tr>
<tr>
    <td class="windowbg2">letztes Update:</td>
    <td class="windowbg1"><?php echo (empty($row_planie['time'])) ? "/" : round((CURRENT_UNIX_TIME - $row_planie['time']) / DAY) . " Tage"; ?></td>
</tr>
<?php
if ((($ansicht === "auto") && ($row_planie['objekt'] !== "---")) || ($ansicht === "taktisch") || ($ansicht === "beide")) {
    ?>
    <tr>
        <td class="windowbg2">Spielername:</td>
        <td class="windowbg1"><?php echo $row_planie['user']; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Allianz:</td>
        <td class="windowbg1" style=" background-color: <?php echo $color; ?>;"><a
                href="index.php?action=showgalaxy&allianz=<?php echo $row_planie['allianz']; ?>"><?php echo $row_planie['allianz'];
                echo ((empty($row_status['status'])) || ($row_status['status'] === 'own')) ? "" : " (" . $row_status['status'] . ")"; ?></a>
        </td>
    </tr>
    <tr>
        <td class="windowbg2">Planetennamen:</td>
        <td class="windowbg1"><?php echo $row_planie['planetenname']; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Spieler im UMode :</td>
        <?php
        if ($row_spieler['umode']) {
            $color = "#FF3030";
        } else {
            $color = "#FFFFFF";
        }
        ?>
        <td style="background-color:<?php echo $color; ?>">
            <?php
            if ($row_spieler['umode']) {
                echo "ja";
            } else {
                echo "nein";
            }
            ?>
        </td>
    </tr>
    <tr>
        <?php
        if ($row_spieler['gesperrt']) {
            $color = "#FF3030";
        } else {
            $color = "#FFFFFF";
        }
        ?>
        <td class="windowbg2">Spieler gesperrt :</td>
        <td style="background-color:<?php echo $color; ?>">
            <?php
            if ($row_spieler['gesperrt']) {
                echo "ja";
            } else {
                echo "nein";
            }
            ?>
        </td>
    </tr>
	<tr>
        <?php
        if ($row_spieler['einmaurer']) {
            $color = "#D2691E";
        } else {
            $color = "#FFFFFF";
        }
        ?>
        <td class="windowbg2">Spieler ist Einmaurer :</td>
        <td style="background-color:<?php echo $color; ?>">
            <?php
            if ($row_spieler['einmaurer']) {
                echo "ja";
            } else {
                echo "nein";
            }
            ?>
        </td>
    </tr>
		
<?php
}
?>
<tr>
    <td colspan="2" class="titlebg"><b>Eigenschaften:</b></td>
</tr>
<tr>
    <td class="windowbg2">Planetentyp:</td>
    <td class="windowbg1"><?php echo $row_planie['typ']; ?></td>
</tr>
<tr>
    <td class="windowbg2">Objekttyp:</td>
    <td class="windowbg1"><?php echo $row_planie['objekt']; ?></td>
</tr>
<tr>
    <td colspan="2" class="titlebg"><b>Notizen:</b></td>
</tr>
<tr>
    <td class="windowbg2"><i>Hier bitte jegliche Informationen über diesen Planeten, sei es Raids, Uhrzeiten, Absprachen, Tipps für Raider eingeben.</i></td>
    <td class="windowbg1">
        <form method='POST'>
            <input type="hidden" name="action" value="showplanet">
            <input type="hidden" name="coords" value="<?php echo $coords; ?>">
            <input type="hidden" name="ansicht" value="auto">
            <table class='table_format center' style='width: 100%;'>
                <tr>
                    <td class='windowbg2'>
                        <textarea name='notice' rows='10' style='width: 99%;'><?php echo $notice; ?></textarea>
                    </td>
                </tr>
                <?php
                    if ($row_spieler) {
                ?>
                <tr>
                    <td>Umode setzen :
                        <?php
                        echo "<input type='checkbox' name='umode' value='1'";
                        if ($row_spieler['umode']) {
                            echo ' checked="checked"';
                        }
                        echo "'>";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>gesperrt setzen :
                        <?php
                        echo "<input type='checkbox' name='gesperrt' value='1'";
                        if ($row_spieler['gesperrt']) {
                            echo ' checked="checked"';
                        }
                        echo "'>";
                        ?>
                    </td>
                </tr>
				<tr>
                    <td>Einmaurer setzen :
                        <?php
                        echo "<input type='checkbox' name='einmaurer' value='1'";
                        if ($row_spieler['einmaurer']) {
                            echo ' checked="checked"';
                        }
                        echo "'>";
                        ?>
                    </td>
                </tr>
                <?php
                    }
                ?>
                <tr>
                    <td class='titlebg'>
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
if (((($ansicht === "auto")) || ($ansicht === "geologisch") || ($ansicht === "beide")) AND !empty($row_planie['geoscantime'])) {
    ?>
    <tr>
        <td colspan="2" class="titlebg"><b>Geologie:</b></td>
    </tr>
    <tr>
        <td class="windowbg2">Gravitation:</td>
        <td class="windowbg1"><?php echo $row_planie['gravitation']; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Forschungsmod.:</td>
        <td class="windowbg1"><?php echo ($row_planie['fmod'] < 100) ? "<div class='doc_red'>" . $row_planie['fmod'] . " %</div>" : $row_planie['fmod'] . ' %'; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Gebäudekostenmod.:</td>
        <td class="windowbg1"><?php echo ($row_planie['kgmod'] > 1) ? "<div class='doc_red'>" . $row_planie['kgmod'] . "</div>" : $row_planie['kgmod']; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Gebäudedauermod.:</td>
        <td class="windowbg1"><?php echo ($row_planie['dgmod'] > 1) ? "<div class='doc_red'>" . $row_planie['dgmod'] . "</div>" : $row_planie['dgmod']; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Schiffskostenmod.:</td>
        <td class="windowbg1"><?php echo ($row_planie['ksmod'] > 1) ? "<div class='doc_red'>" . $row_planie['ksmod'] . "</div>" : $row_planie['ksmod']; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Schiffsdauermod.:</td>
        <td class="windowbg1"><?php echo ($row_planie['dsmod'] > 1) ? "<div class='doc_red'>" . $row_planie['dsmod'] . "</div>" : $row_planie['dsmod']; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Planetengröße:</td>
        <td class="windowbg1"><?php echo number_format($row_planie['bevoelkerungsanzahl'], 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg"><b>Ressourcen:</b></td>
    </tr>
    <tr>
        <td class="windowbg2">Eisengehalt:</td>
        <td class="windowbg1"><?php echo ($row_planie['eisengehalt'] > 100) ? "<b>" . $row_planie['eisengehalt'] . " %</b>" : $row_planie['eisengehalt'] . ' %';
            echo " (effektiv ";
            echo ($eisen_effektiv) ? "<b>" . round($eisen_effektiv, 1) . "%</b>)" : round($eisen_effektiv, 1) . "%)";
            echo ", mit TechTeam ";
            echo ($row_planie['tteisen'] > 100) ? "<b>" . $row_planie['tteisen'] . "%</b>" : $row_planie['tteisen'] . "%";
            echo " (effektiv ";
            echo ($tteisen_effektiv) ? "<b>" . round($tteisen_effektiv, 1) . "%</b>)" : round($tteisen_effektiv, 1) . "%)"; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Chemievorkommen:</td>
        <td class="windowbg1"><?php echo ($row_planie['chemievorkommen'] > 100) ? "<b>" . $row_planie['chemievorkommen'] . " %</b>" : $row_planie['chemievorkommen'] . ' %';
            echo " (effektiv ";
            echo ($chemie_effektiv) ? "<b>" . round($chemie_effektiv, 1) . "%</b>)" : round($chemie_effektiv, 1) . "%)";
            echo ", mit TechTeam ";
            echo ($row_planie['ttchemie'] > 100) ? "<b>" . $row_planie['ttchemie'] . "%</b>" : $row_planie['ttchemie'] . "%";
            echo " (effektiv ";
            echo ($ttchemie_effektiv) ? "<b>" . round($ttchemie_effektiv, 1) . "%</b>)" : round($ttchemie_effektiv, 1) . "%)"; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Eisdichte:</td>
        <td class="windowbg1"><?php echo ($row_planie['eisdichte'] > 30) ? "<b>" . $row_planie['eisdichte'] . " %</b>" : $row_planie['eisdichte'] . ' %';
            echo " (effektiv ";
            echo ($eis_effektiv) ? "<b>" . round($eis_effektiv, 1) . "%</b>)" : round($eis_effektiv, 1) . "%)";
            echo ", mit TechTeam ";
            echo ($row_planie['tteis'] > 30) ? "<b>" . $row_planie['tteis'] . "%</b>" : $row_planie['tteis'] . "%";
            echo " (effektiv ";
            echo ($tteis_effektiv) ? "<b>" . round($tteis_effektiv, 1) . "%</b>)" : round($tteis_effektiv, 1) . "%)"; ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Lebensbedingungen:</td>
        <td class="windowbg1"><?php echo ($row_planie['lebensbedingungen'] > 100) ? "<b>" . $row_planie['lebensbedingungen'] . " %</b>" : $row_planie['lebensbedingungen'] . ' %'; ?></td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg"><b>Besonderheiten:</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2"><?php echo !empty($row_planie['besonderheiten']) ? str_replace(", ", "<br>", $row_planie['besonderheiten']) : "keine *moep*"; ?></td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg"><b>Sprengung:</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2">
            <?php
            $reset_timestamp_first = (($row_planie['geoscantime'] + $row_planie['reset_timestamp']) - DAY); //vorverlegen des Sprengdatums wegen +-24h
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
    <tr>
        <td colspan="2" class="titlebg"><b>Geolink</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2">
            <?php
            echo $row_planie['geolink'];
            ?>
        </td>
    </tr>
	
<?php
}
if ($row_planie['objekt'] === "---") {
    ?>
    <tr>
        <td colspan="2" class="titlebg"><b>Rating:</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2 center">
            <b><?php echo(!empty($row_planie['geoscantime']) ? "<div class='bigtext'>" . $rating . "</div>" : "<div class='system_warning'>Kein Rating berechenbar, neuer Geoscan erforderlich</div>"); ?></b>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg"><b>Reservieren:</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2 center">
            <form method="POST">
                <input type="hidden" name="action" value="showplanet">
                <input type="hidden" name="coords" value="<?php echo $row_planie['coords']; ?>">
                <?php
                if (empty($row_planie['reserviert'])) {
                    echo "Diesen Planeten für dich reservieren? <input type='checkbox' name='reservieren'>
                        <input type='submit' value='speichern' name='reserveplanie' class='submit'>";
                } elseif ($row_planie['reserviert'] === $user_sitterlogin) {
                    echo "Diesen Planeten für dich reservieren? <input type='checkbox' name='reservieren' checked>
                        <input type='submit' value='speichern' name='reserveplanie' class='submit'>";
                } else {
                    echo "Dieser Planet ist für " . $row_planie['reserviert'] . " reserviert. Bitte besiedel ihn nicht.";
                    if ($user_status === "admin") {
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
if (((($ansicht === "auto") && ($row_planie['objekt'] !== "---")) || ($ansicht === "taktisch") || ($ansicht === "beide")) AND (!empty($row_planie['schiffscantime']) OR !empty($row_planie['gebscantime']))) {
    $class1 = $row_planie['eisen'] + 2 * $row_planie['stahl'] + 4 * $row_planie['vv4a'] + 3 * $row_planie['chemie'];
    $class2 = 2 * $row_planie['eis'] + 2 * $row_planie['wasser'] + $row_planie['energie'];
    ?>
    <tr>
        <td colspan="2" class="titlebg"><b>auf Lager:</b></td>
    </tr>
    <tr>
        <td class="windowbg2">Eisen:</td>
        <td class="windowbg1"><?php echo number_format($row_planie['eisen'], 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Stahl:</td>
        <td class="windowbg1"><?php echo number_format($row_planie['stahl'], 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td class="windowbg2">VV4A:</td>
        <td class="windowbg1"><?php echo number_format($row_planie['vv4a'], 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Chemie:</td>
        <td class="windowbg1"><?php echo number_format($row_planie['chemie'], 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Eis:</td>
        <td class="windowbg1"><?php echo number_format($row_planie['eis'], 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Wasser:</td>
        <td class="windowbg1"><?php echo number_format($row_planie['wasser'], 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Energie:</td>
        <td class="windowbg1"><?php echo number_format($row_planie['energie'], 0, ',', '.'); ?></td>
    <tr>
        <td colspan="2" class="titlebg"><b>benötigte Frachtkapazität:</b></td>
    </tr>
    <tr>
        <td class="windowbg2">Klasse 1:</td>
        <td class="windowbg1"><?php echo number_format($class1, 0, ',', '.'); ?></td>
    </tr>
    <tr>
        <td class="windowbg2">Klasse 2:</td>
        <td class="windowbg1"><?php echo number_format($class2, 0, ',', '.'); ?></td>
    </tr>

    <?php
    if (!empty($row_planie['lager_chemie']) AND !empty($row_planie['lager_eis']) AND !empty($row_planie['lager_energie'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>Lagerkapazität:</b></td>
        </tr>
        <tr>
            <td class="windowbg2">Lager Chemie:</td>
            <td class="windowbg1"><?php echo number_format($row_planie['lager_chemie'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td class="windowbg2">Lager Eis:</td>
            <td class="windowbg1"><?php echo number_format($row_planie['lager_eis'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td class="windowbg2">Lager Energie:</td>
            <td class="windowbg1"><?php echo number_format($row_planie['lager_energie'], 0, ',', '.'); ?></td>
        </tr>

    <?php
    }

    if (!empty($row_planie['geb'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>Gebäude:</b></td>
        </tr>
        <tr>
            <td class="windowbg1" colspan="2"><?php echo $row_planie['geb']; ?></td>
        </tr>
    <?php
    }

    if (!empty($row_planie['plan'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>planetare Flotte:</b></td>
        </tr>
        <tr>
            <td class="windowbg1" colspan="2"><?php echo $row_planie['plan']; ?></td>
        </tr>
    <?php
    }

    if (!empty($row_planie['stat'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>stationierte Flotte:</b></td>
        </tr>
        <tr>
            <td class="windowbg1" colspan="2"><?php echo $row_planie['stat']; ?></td>
        </tr>
    <?php
    }

    if (!empty($row_planie['def'])) {
        ?>
        <tr>
            <td colspan="2" class="titlebg"><b>Verteidigung:</b></td>
        </tr>
        <tr>
            <td class="windowbg1" colspan="2"><?php echo $row_planie['def']; ?></td>
        </tr>
    <?php
    }
	?>
	<tr>
        <td colspan="2" class="titlebg"><b>Schiffsondierungslink</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2">
            <?php
            echo $row_planie['schifflink'];
            ?>
        </td>
    </tr>
	<tr>
        <td colspan="2" class="titlebg"><b>Gebäudesondierungslink</b></td>
    </tr>
    <tr>
        <td colspan="2" class="windowbg2">
            <?php
            echo $row_planie['geblink'];
            ?>
        </td>
    </tr>
<?php
}
?>
</table>
<br>