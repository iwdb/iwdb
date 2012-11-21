<?php

/*
 * definition der Sounddateien (leer = global ausgeschaltet)
 * freie ogg Audioformat empfohlen unterstützt von Chrome, Firefox, Opera, Android, ...
 * nicht unterstützt von Apple und Microsoft, was solls ^^
 * ToDo: Einstellung in die Profileinstellung verschieben
*/
$soundfile_normal  = 'audio/auftrag.oga';
$soundfile_wichtig = 'audio/auftrag.oga';

global $sid, $db, $db_prefix, $db_tb_sitterauftrag;

//auslesen ob das Modul Sound haben darf
$sqlM = "SELECT sound FROM " . $db_prefix . "menu WHERE action = '" . $action . "';";
$resultM = $db->db_query($sqlM)
    or error(GENERAL_ERROR, 'Could not query sound setting.', '', __FILE__, __LINE__, $sqlM);
$rowM = $db->db_fetch_array($resultM);

$SitternoticeInModul = false;
if (!empty($rowM['sound'])) {
    $SitternoticeInModul = true;
}

if (empty($action) OR $action === 'memberlogin2') {
    //beim Login soll da abgespielt werden?
    $sqlP = "SELECT value FROM " . $db_prefix . "params WHERE name = 'sound_login' ";
    $resultP = $db->db_query($sqlP)
        or error(GENERAL_ERROR, 'Could not query sound setting.', '', __FILE__, __LINE__, $sqlP);
    $rowP = $db->db_fetch_array($resultP);
    if (!empty($rowP['value'])) {
        $SitternoticeInModul = true;
    }
}

if (!empty($user_id) AND ($user_id != 'guest') AND ($SitternoticeInModul)) {
    //was soll abgespielt werden?
    $sqlS = "SELECT sound FROM " . $db_prefix . "user WHERE id = '" . $user_id . "' ";
    $resultS = $db->db_query($sqlS)
        or error(GENERAL_ERROR, 'Could not query sound setting.', '', __FILE__, __LINE__, $sqlS);
    $rowS = $db->db_fetch_array($resultS);

    //0=ausgeschaltet, 1=fenster, 2=fenster mit sound, 3=fenster (blinkend), 4=fenster (blinkend) mit sound
    $sitternotice_setting = (int)$rowS['sound'];

    if (!empty($sitternotice_setting)) {
        if ($sitternotice_setting === 3 OR $sitternotice_setting === 4) { //blinkendes Fenster
            ?>
        <script type="text/javascript">
            <!--
            function changebgcolor(color) {
                if (document.getElementById("fadein1")) {
                    document.getElementById("fadein1").style.backgroundColor = color;
                }
                if (document.getElementById("fadein2")) {
                    document.getElementById("fadein2").style.backgroundColor = color;
                }
            }
            var bgColor = "#CCCCCC";

            function nerv_mich() {
                if (bgColor == "blue") {
                    bgColor = "red";
                } else if (bgColor == "red") {
                    bgColor = "yellow";
                } else {
                    bgColor = "blue";
                }
                changebgcolor(bgColor);
                window.setTimeout(nerv_mich, 1000);
            }
            window.setTimeout(nerv_mich, 500);

            //-->
        </script>
        <?php
        }

        // Anstehende Aufträge zählen
        $strAnzauftraege = "";
        $iSitterauftraege = 0;
        $iForschungsauftraege = 0;
        if (($user_adminsitten == SITTEN_BOTH) || ($user_adminsitten == SITTEN_ONLY_LOGINS)) {
            $sql = "SELECT count(*) AS anzahl FROM " . $db_tb_sitterauftrag .
                " WHERE date_b2 < " . CURRENT_UNIX_TIME;
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            $row = $db->db_fetch_array($result);
            $iSitterauftraege = $row['anzahl'];
            $db->db_free_result($result);

            $sql = "SELECT count(*) AS anzahl FROM " . $db_tb_sitterauftrag .
                " WHERE date_b2 < " . CURRENT_UNIX_TIME . " AND typ = 'Forschung'";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            $row = $db->db_fetch_array($result);
            $iForschungsauftraege = $row['anzahl'];
            $db->db_free_result($result);

            // Wurde gerade ein Auftrag erledigt? Falls ja, muss dieser von der
            // gerade ermittelten Zahl abgezogen werden.
            if ($action == "sitterliste") {
                $erledigt = getVar('erledigt');

                if (!empty($erledigt)) {
                    $iSitterauftraege = $iSitterauftraege - 1;
                    $iForschungsauftraege = $iForschungsauftraege - 1;
                }
            }

            if ($iSitterauftraege > 0) {
                $strAnzauftraege = " (" . $iSitterauftraege . " offen)";
            }
        }

        if ($iSitterauftraege > 0) {

            $soundfile = '';
            if ($sitternotice_setting === 2 OR $sitternotice_setting === 4) { //hat der user den Sound an?

                if ($iForschungsauftraege > 0) {
                    $soundfile = $soundfile_wichtig;
                } else {
                    $soundfile = $soundfile_normal;
                }

            }

            ?>

        <div id="fadein">
            <table id="blinker" class="bordercolor" style="color: red; width: 100%;">
                <tbody>
                <tr>
                    <td class="fadein" id="fadein1">
                        <?php
                        if (!empty($soundfile)) {
                            //autoplay des Sounds mit html5
                            ?>
                            <audio autoplay="autoplay">
                                <source src="<?php echo $soundfile;?>" type="audio/ogg">
                            </audio>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="fadein" id="fadein2">
                        <?php
                        if (($iSitterauftraege == 1) AND ($iForschungsauftraege == 0)) {
                            ?>
                            <a href="index.php?sid=<?php echo $sid;?>&action=sitterliste"><span
                                    style="font-size: 1.8rem; color: Cyan;">Es sind ein Auftrag offen!</span></a>
                            <?php
                        }
                        if (($iSitterauftraege == 1) AND ($iForschungsauftraege == 1)) {
                            ?>
                            <a href="index.php?sid=<?php echo $sid;?>&action=sitterliste"><span
                                    style="font-size: 1.8rem; color: Cyan;">Es sind ein Forschungsauftrag offen!</span></a>
                            <?php
                        }
                        if (($iSitterauftraege > 1) AND ($iForschungsauftraege == 0)) {
                            ?>
                            <a href="index.php?sid=<?php echo $sid;?>&action=sitterliste"><span
                                    style="font-size: 1.8rem; color: Cyan;">Es sind <?php echo $iSitterauftraege;?>
                                Aufträge offen!</span></a>
                            <?php
                        }
                        if (($iSitterauftraege > 1) AND ($iForschungsauftraege >= 1)) {
                            ?>
                            <a href="index.php?sid=<?php echo $sid;?>&action=sitterliste"><span
                                    style="font-size: 1.8rem; color: Cyan;">Es sind <?php echo $iSitterauftraege;?>
                                Aufträge offen!</span></a>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>

        </div>

        <?php
        }
    }
}