<?php
/*****************************************************************************
 * profile_editdata.php                                                      *
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

$skins = array(
    "6" => "Standard Skin",
    "3" => "Text Skin",
    "0" => "User Skin"
);

$spieltyp = array(
    "Solo"     => "Solo",
    "Buddler"  => "Buddler",
    "Fleeter"  => "Fleeter",
    "Cash Cow" => "Cash Cow"
);

$adminsittens = array(
    SITTEN_DISABLED      => "Sitterbereich deaktiviert",
    SITTEN_ONLY_NEWTASKS => "kann Sitteraufträge erstellen, darf keine anderen sitten",
    SITTEN_ONLY_LOGINS   => "darf andere sitten, darf keine Sitteraufträge erstellen",
    SITTEN_BOTH          => "darf andere sitten, darf Sitteraufträge erstellen"
);

// Mögliche Menue-Stilarten werden aus der Dateisystem-Struktur gelesen.
$menustyles = array();
$fp6 = opendir("./menustyles");

$staatsformen = array('keine', 'Diktator', 'Monarch', 'Demokrat', 'Kommunist');

while ($datei1 = readdir($fp6)) {
    if (strstr($datei1, "menu_")) {
        $menuname = str_replace("menu_", "", $datei1);
        $menuname = str_replace(".php", "", $menuname);

        $menustyles[$menuname] = $menuname;
    }
}

closedir($fp6);

doc_title('Profil');

$id = getVar('id');
$edit = getVar('edit');

if (($sitterlogin <> "") && ($edit == "true") && (($sitterlogin == $user_sitterlogin) || ($user_status == "admin"))) {
    $userd['password']     = getVar('password');
    $userd['passwordwdhl'] = getVar('passwordwdhl');
    $userd['email']        = getVar('email');

    $userd['gal_start'] = getVar('gal_start');
    $userd['gal_end']   = getVar('gal_end');
    $userd['sys_start'] = getVar('sys_start');
    $userd['sys_end']   = getVar('sys_end');
    $userd['allianz']   = getVar('allianz');
    $userd['preset']    = getVar('preset');
    $userd['squad']     = getVar('squad');
    $userd['grav_von']  = getVar('grav_von');
    $userd['grav_bis']  = getVar('grav_bis');

    $userd['staatsform'] = getVar('staatsform');

    $userd['NewUniXmlTime'] = strtotime(getVar('NewUniXmlTime')) ? strtotime(getVar('NewUniXmlTime')) : null;

    $userd['grav_von'] = str_replace(",", ".", $userd['grav_von']);
    $userd['grav_bis'] = str_replace(",", ".", $userd['grav_bis']);

    $userd['geopunkte'] = getVar('geopunkte');
    $userd['syspunkte'] = getVar('syspunkte');

    $userd['sitterpwd']     = getVar('sitterpwd');
    $userd['sitterpwdwdhl'] = getVar('sitterpwdwdhl');

    $userd['sitterskin']     = getVar('sitterskin');
    $userd['sitterpunkte']   = getVar('sitterpunkte');
    $userd['sittercomment']  = getVar('sittercomment');
    $userd['sitten']         = getVar('sitten');
    $userd['adminsitten']    = getVar('adminsitten');
    $userd['genbauschleife'] = getVar('genbauschleife');
    $userd['genmaurer']      = getVar('genmaurer');
    $userd['iwsa']           = getVar('iwsa');
    $userd['gengebmod']      = getVar('gengebmod');
    $userd['gengebmod']      = str_replace(",", ".", $userd['gengebmod']);

    $userd['peitschen']   = getVar('peitschen');
    $userd['budflesol']   = getVar('budflesol');
    $userd['buddlerfrom'] = getVar('buddlerfrom');

    $userd['ikea'] = getVar('ikea');

    $userd['gesperrt'] = getVar('gesperrt');

    $userd['planibilder']  = getVar('planibilder');
    $userd['gebbilder']    = getVar('gebbilder');
    $userd['status']       = getVar('status');
    $userd['menu_default'] = getVar('menu_default');

    $userd['color'] = getVar('color');

    $userd['sound']   = getVar('sound');
    $userd['uniprop'] = getVar('uniprop');

    if ($user_status != "admin") {
        unset($userd['status']);
        unset($userd['adminsitten']);
        unset($userd['sitterpunkte']);
        unset($userd['geopunkte']);
        unset($userd['syspunkte']);
        unset($userd['sittercomment']);
        unset($userd['allianz']);
    }

    if ($user_status == "admin") {
        $userd['id'] = $id;
    }

    // Testet ob das Passwort sicher ist
    if (!empty($userd['password'])) {
        $alert = secure_password($userd['password']);

        if (!empty($alert)) {
            echo "<br><div class='system_error'>" . $alert . "</div>";
            unset($userd['password']);
            unset($userd['passwordwdhl']);
        } else {
            if ($userd['password'] != $userd['passwordwdhl']) {
                echo "<br><div class='system_error'>Passwörter stimmen nicht überein! Passwort zurückgesetzt.</div>";
                unset($userd['password']);
                unset($userd['passwordwdhl']);
            } else {
                unset($userd['passwordwdhl']);
                $userd['password'] = md5($userd['password']);
            }
        }
    } else {
        unset($userd['password']);
        unset($userd['passwordwdhl']);
    }

    if ($userd['sitterpwd'] != $userd['sitterpwdwdhl']) {
        echo "<br><div class='system_error'>Sitterpasswörter stimmen nicht überein! Passwort zurückgesetzt.</div>";
        unset($userd['sitterpwd']);
        unset($userd['sitterpwdwdhl']);
    } else {
        unset($userd['sitterpwdwdhl']);
    }

    if ($userd['sitterpwd'] == '' OR $userd['sitterpwd'] == '***') {
        unset($userd['sitterpwd']);
    } else {
        $userd['sitterpwd'] = MD5($userd['sitterpwd']);
    }

    if (getVar('deleteSitterpass') == '1' AND getVar('deleteSitterpasswdh') == '1') {
        $userd['sitterpwd'] = '';
        echo "<br><div class='system_notification'>Sitterpasswörter gelöscht.</div>";
    }

    $result = $db->db_update($db_tb_user, $userd, "WHERE sitterlogin='" . $sitterlogin . "'")
        or error(GENERAL_ERROR, 'Could not update user information.', '', __FILE__, __LINE__);
    echo "<div class='system_notification'>Userdaten aktualisiert.</div>";

    $sql = "SELECT t1.* FROM " . $db_tb_sitterauftrag . " as t1 LEFT JOIN " . $db_tb_sitterauftrag . " as t2 ON t1.id = t2.refid WHERE t2.refid is null AND t1.user='" . $sitterlogin . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        if ($row['typ'] == "Gebaeude") {
            dates($row['id'], $sitterlogin);
        }
    }
}

$groups = array();
$sql = "SELECT * FROM $db_tb_group";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $groups[$row["id"]] = array(
        "id"       => $row["id"],
        "name"     => $row["name"],
        "selected" => false,
    );
}
$selectedgroups = getVar("groups");
if (isset($selectedgroups) && is_array($selectedgroups)) {
    foreach ($selectedgroups as $selectedgroup) {
        $groups[$selectedgroup]["selected"] = true;
    }
}
if ($edit == true && $user_status == "admin") {
    $sql = "DELETE FROM $db_tb_group_user WHERE $db_tb_group_user.`user_id`='" . $id . "'";
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    if (isset($selectedgroups) && is_array($selectedgroups)) {
        foreach ($selectedgroups as $selectedgroup) {
            $sql = "INSERT INTO $db_tb_group_user (group_id,user_id) VALUES (" . $selectedgroup . ",'" . $id . "')";
            $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }
    }
} else {
    $sql = "SELECT * FROM $db_tb_group_user WHERE $db_tb_group_user.`user_id`='" . $sitterlogin . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        $groups[$row["group_id"]]["selected"] = true;
    }
}

$sql = "SELECT * FROM " . $db_tb_user . " WHERE sitterlogin = '" . $sitterlogin . "'";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);

foreach ($row as $key => $data) {
    ${$key} = $data;
}

if (!empty($sitterpwd)) {
    $sitterpwdsp = '***';
} else {
    $sitterpwdsp = '';
}

//auslesen aller Memebr
$alluser = array();
$sqlM = "SELECT id FROM " . $db_tb_user;
$resultM = $db->db_query($sqlM)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
while ($rowM = $db->db_fetch_array($resultM)) {
    $alluser[] = $rowM['id'];
}

$sel0 = '';
$sel1 = '';
$sel2 = '';
$sel3 = '';
$sel4 = '';

$asound = array();
$asound[0] = 'ausgeschaltet';
$asound[1] = 'fenster';
$asound[2] = 'fenster mit sound';
$asound[3] = 'fenster (blinkend)';
$asound[4] = 'fenster (blinkend) mit sound';

switch ($sound) {
    case '4':
        $sel4 = 'selected="selected"';
        break;
    case '3':
        $sel3 = 'selected="selected"';
        break;
    case '2':
        $sel2 = 'selected="selected"';
        break;
    case '1':
        $sel1 = 'selected="selected"';
        break;
    case '0':
        $sel0 = 'selected="selected"';
        break;
    default:
        $sel0 = 'selected="selected"';
}

?>
<br>
<form method="POST" action="index.php?action=profile&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
<tr>
    <td colspan="2" class="titlebg">
        <b>Daten:</b>
    </td>
</tr>
<tr>
    <td class="windowbg2" style="width:40%;">
        Username:<br>
        <span style="font-style:italic;">Dein Loginnick.</span>
    </td>
    <td class="windowbg1">
        <?php
        if ($user_status == "admin") {
            echo "<input type='text' name='id' value='$id' style='width: 25em'>\n";
        } else {
            echo "<input type='hidden' name='id' value='$id'>\n";
        }
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Passwort:<br>
        <span style="font-style:italic;">Dein Loginpasswort.</span>
    </td>
    <td class="windowbg1">
        <input type="password" name="password" value="" style="width: 25em">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Passwort Wiederholung:<br>
        <span style="font-style:italic;">Passwort zur Sicherheit wiederholen.</span>
    </td>
    <td class="windowbg1">
        <input type="password" name="passwordwdhl" value="" style="width: 25em">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        EMail:<br>
        <span style="font-style:italic;">An diese Adresse wird dein Passwort gesendet, wenn du es vergessen hast.</span>
    </td>
    <td class="windowbg1">
        <input type="email" name="email" value="<?php echo $email;?>" style="width: 25em">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Allianz:<br>
        <span style="font-style:italic;">Trage hier deine Allianz (<?php echo $config_allytag;?>) ein.</span>
    </td>
    <td class="windowbg1">
        <?php
        if ($user_status == "admin") {
            echo "<input type='text' name='allianz' value='$allianz' style='width: 25em'>\n";
        } else {
            echo $allianz;
        }
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Squad:<br>
        <span style="font-style:italic;">Gebe hier deinen Squadnamen an.</span>
    </td>
    <td class="windowbg1">
        <input type="text" name="squad" value="<?php echo $squad;?>" style="width: 25em">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Gravitation:<br>
        <span style="font-style:italic;">Trage hier den Bereich der Gravitation ein, die du besiedeln kannst.</span>
    </td>
    <td class="windowbg1">
        von <input type="text" name="grav_von" value="<?php echo $grav_von;?>" style="width: 5em" maxlength="3"> bis
        <input type="text" name="grav_bis" value="<?php echo $grav_bis;?>" style="width: 5em" maxlength="3">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Galaxie:<br>
        <span style="font-style:italic;">Trage hier den Bereich der Galaxien ein, die du sehen kannst.</span>
    </td>
    <td class="windowbg1">
        von <input type="text" name="gal_start" value="<?php echo $gal_start;?>" style="width: 5em"> bis
        <input type="text" name="gal_end" value="<?php echo $gal_end;?>" style="width: 5em">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        System:<br>
        <span style="font-style:italic;">Trage hier den Bereich der Systeme ein, die du sehen kannst.</span>
    </td>
    <td class="windowbg1">
        von <input type="text" name="sys_start" value="<?php echo $sys_start;?>" style="width: 5em"> bis
        <input type="text" name="sys_end" value="<?php echo $sys_end;?>" style="width: 5em">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Standardpreset:<br>
        <span style="font-style:italic;">Diese Voreinstellung wird bei "Planet suchen" standardmäßig geladen.</span>
    </td>
    <td class="windowbg1">
        <select name="preset" style="width: 100px;">
            <?php
            $sql = "SELECT id, name FROM " . $db_tb_preset . " WHERE (fromuser = '" . $user_sitterlogin . "' OR fromuser = '') ORDER BY fromuser, name";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            while ($row = $db->db_fetch_array($result)) {
                echo ($preset == $row['id']) ? "<option value='" . $row['id'] . "' selected>" . $row['name'] . "</option>\n" : "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>\n";
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td colspan="2" class="titlebg">
        <b>Sitten:</b>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Sittererstatus?:<br>
        <span style="font-style:italic;">Zeigt an, ob du andere sitten darfst und Sitteraufträge erstellen darfst. Kann nur von Admins geändert werden.</span>
    </td>
    <td class="windowbg1">
        <?php
        if ($user_status == "admin") {
            echo "<select name='adminsitten'>\n";
            foreach ($adminsittens as $key => $data) {
                echo ($adminsitten == $key) ? " <option value='" . $key . "' selected>" . $data . "</option>\n" : " <option value='" . $key . "'>" . $data . "</option>\n";
            }
            echo "</select>\n";
        } else {
            echo (isset($adminsittens[$adminsitten])) ? $adminsittens[$adminsitten] : '';
        }
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Sitten erlauben?:<br>
        <span style="font-style:italic;">Sollen andere deinen Account sitten können? (Aufträge kannst du auch wenn deaktiviert erstellen.)</span>
    </td>
    <td class="windowbg1">
        <input type="checkbox" name="sitten" value="1"<?php echo ($sitten) ? " checked" : "";?>>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Ingame Nick:<br>
        <span style="font-style:italic;">Dein Loginnick in Icewars.</span>
    </td>
    <td class="windowbg1">
        <input type="hidden" name="sitterlogin" value="<?php echo $sitterlogin;?>"><?php echo $sitterlogin;?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Sitterpasswort:<br>
        <span style="font-style:italic;">Dein Sitterpasswort in Icewars.</span>
    </td>
    <td class="windowbg1">
        <input type="password" name="sitterpwd" value="<?php echo $sitterpwdsp;?>" style="width: 25em">
        Null:
        <input type="checkbox" name="deleteSitterpass" value="1">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Sitterpasswort Wiederholung:
    </td>
    <td class="windowbg1">
        <input type="password" name="sitterpwdwdhl" value="<?php echo $sitterpwdsp;?>" style="width: 25em">
        Null:
        <input type="checkbox" name="deleteSitterpasswdh" value="1">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Serverskin:<br>
        <span style="font-style:italic;">Welchen Skin möchtest du beim Sitten verwenden?</span>
    </td>
    <td class="windowbg1">
        <select name="sitterskin">
            <?php
            foreach ($skins as $key => $data) {
                echo ($sitterskin == $key) ? " <option value='" . $key . "' selected>" . $data . "</option>\n" : " <option value='" . $key . "'>" . $data . "</option>\n";
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td class="windowbg2" style="width:30%;">
        Sitterpunkte:<br>
        <span style="font-style:italic;">So viele Punkte hast du schon fürs Sitten erhalten.</span>
    </td>
    <td class="windowbg1">
        <?php
        if ($user_status == "admin") {
            echo "<input type='text' name='sitterpunkte' value='$sitterpunkte' style='width: 5em'>\n";
        } else {
            echo $sitterpunkte;
        }
        $sql = "SELECT AVG(sitterpunkte) FROM " . $db_tb_user . " WHERE sitterpunkte <> 0";
        $result_avg = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row_avg = $db->db_fetch_array($result_avg);

        echo "Durchschnitt: " . round($row_avg['AVG(sitterpunkte)']);
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2" style="width:30%;">
        Sitterkommentar:<br>
        <span style="font-style:italic;">Der Admin kann hier einen Kommentar hinzufügen, der bei den Sitterlogins angezeigt wird (z.B. "im Urlaub").</span>
    </td>
    <td class="windowbg1">
        <?php
        if ($user_status == "admin") {
            echo "<textarea name='sittercomment' id='sittercomment' rows='3' cols='50'>$sittercomment</textarea>\n";
            echo bbcode_buttons('sittercomment');
        } else {
            echo $sittercomment;
        }
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Fadein: <br>
        <span style="font-style:italic;">Wie möchtest du bei Sitteraufträgen zusätzlich benachrichtigt werden?</span>
    </td>
    <td class="windowbg1">
        <select name="sound" size="1">
            <?php
            foreach ($asound as $key => $menu) {
                echo "<option value='$key' " . ${'sel' . $key} . ">" . $asound[$key] . "</option>";
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Meister der Peitschen?:<br>
        <span style="font-style:italic;">Wenn du die Genetikoption hast, bitte Haken setzen.</span>
    </td>
    <td class="windowbg1">
        <input type="checkbox" name="peitschen" value="1"<?php echo ($peitschen) ? " checked" : "";?>>
    </td>
</tr>
<?php
$nchecked = 'checked="checked"';
$lchecked = '';
$mchecked = '';
if ($ikea == 'L') {
    $nchecked = '';
    $lchecked = 'checked="checked"';
} elseif ($ikea == 'M') {
    $nchecked = '';
    $mchecked = 'checked="checked"';
}
?>
<tr>
    <td class="windowbg2">
        Ikea?:<br>
        <span style="font-style:italic;">Wenn du die Genetikoption hast, bitte auswählen setzen.</span>
    </td>
    <td class="windowbg1">
        <input type="radio" name="ikea" value="" <?php echo $nchecked;?>>kein Ikea&nbsp;
        <input type="radio" name="ikea" value="L" <?php echo $lchecked;?>>Lehrling&nbsp;
        <input type="radio" name="ikea" value="M" <?php echo $mchecked;?>>Meister&nbsp;
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Ich will mehr Zeit?:<br>
        <span style="font-style:italic;">Wenn du die Genetikoption hast, bitte Haken setzen.</span>
    </td>
    <td class="windowbg1">
        <input type="checkbox" name="genbauschleife" value="1"<?php echo ($genbauschleife) ? " checked" : "";?>>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Der Einmaurer?:<br>
        <span style="font-style:italic;">Wenn du die Genetikoption hast, bitte Haken setzen.</span>
    </td>
    <td class="windowbg1">
        <input type="checkbox" name="genmaurer" value="1"<?php echo ($genmaurer) ? " checked" : "";?>>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Bau auf Bau auf Bau auf Bau auf?:<br>
        <span style="font-style:italic;">Stelle hier deinen Gebäudebaudauermodifikator ein (Standard 1).</span>
    </td>
    <td class="windowbg1">
        <input type="text" name="gengebmod" value="<?php echo $gengebmod;?>" style="width: 5em">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        IWSA/IWBP-Account?:<br>
  <span style="font-style:italic;">Wenn du einen solchen Account hast, bitte Haken setzen.
     Wichtig wegen FP!</span>
    </td>
    <td class="windowbg1">
        <input type="checkbox" name="iwsa" value="1" <?php echo ($iwsa) ? " checked" : "";?>>
    </td>
</tr>
<tr>
    <td colspan="2" class="titlebg">
        <b>Sonstiges:</b>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Spieltyp:<br>
        <span style="font-style:italic;">Hier deinen Spieltyp eintragen. Wenn du Buddler bist, bitte noch das 2. Feld ausfüllen (ansonsten leer lassen).</span>
    </td>
    <td class="windowbg1">
        <select name="budflesol">
            <?php
            foreach ($spieltyp as $key => $data) {
                echo ($budflesol == $key) ? " <option value='" . $key . "' selected>" . $data . "</option>\n" : " <option value='" . $key . "'>" . $data . "</option>\n";
            }
            ?>
        </select>
        von
        <select name="buddlerfrom">
            <option value="">---</option>
            <?php
            foreach ($alluser as $auser) {
                echo ($buddlerfrom == $auser) ? " <option value='" . $auser . "' selected>" . $auser . "</option>\n" : " <option value='" . $auser . "'>" . $auser . "</option>\n";
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Deine Farbe:<br>
        <span style="font-style:italic;">Sollest du Fleeter sein, trage hier eine Farbe für deine Buddler ein (Format: #RRGGBB)</span>
    </td>
    <td class="windowbg1">
        <input type="text" name="color" size="8" maxlength="7" value="<?php echo $color;?>">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Staatsform:<br>
        <span style="font-style:italic;">Deine Staatsform.</span>
    </td>
    <td class="windowbg1">
        <select name="staatsform">
            <?php
            foreach ($staatsformen as $key => $data) {
                echo ($staatsform == $key) ? " <option value='" . $key . "' selected>" . $data . "</option>\n" : " <option value='" . $key . "'>" . $data . "</option>\n";
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td class="windowbg2" style="width:30%;">
        nächster Unixml Scan:<br>
        <span style="font-style:italic;">Nächster Zeitpunkt des UniXml Scans.</span>
    </td>
    <td class="windowbg1">
        <input type='text' name='NewUniXmlTime'
               value='<?php echo ($NewUniXmlTime ? date("d.m.Y H:i", $NewUniXmlTime) : ''); ?>' style='width: 15em;'>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Planetenbilder anzeigen?:<br>
        <span
            style="font-style:italic;">Sollen Bilder, den Planetentypen entsprechend in der Karte angezeigt werden?</span>
    </td>
    <td class="windowbg1">
        <input type="checkbox" name="planibilder" value="1"<?php echo ($planibilder) ? " checked" : "";?>>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Gebäudebilder anzeigen?:<br>
        <span style="font-style:italic;">Sollen Gebäudebilder beim Erstellen eines Auftrages und bei "Gebäude ausblenden" angezeigt werden?</span>
    </td>
    <td class="windowbg1">
        <input type="checkbox" name="gebbilder" value="1"<?php echo ($gebbilder) ? " checked" : "";?>>
    </td>
</tr>
<tr>
    <td class="windowbg2" style="width:30%;">
        Geoscanpunkte:<br>
        <span style="font-style:italic;">So viele GeoScans hast du schon eingestellt.</span>
    </td>
    <td class="windowbg1">
        <?php
        if ($user_status == "admin") {
            echo "<input type='text' name='geopunkte' value='$geopunkte' style='width: 5em;'>\n";
        } else {
            echo $geopunkte;
        }
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2" style="width:30%;">
        Systemscanpunkte:<br>
        <span style="font-style:italic;">So viele SystemScans hast du schon eingestellt.</span>
    </td>
    <td class="windowbg1">
        <?php
        if ($user_status == "admin") {
            echo "<input type='text' name='syspunkte' value='$syspunkte' style='width: 5em;'>\n";
        } else {
            echo $syspunkte;
        }
        ?>
    </td>
</tr>
<?php
if ($user_status == "admin") {
    ?>
    <tr>
        <td class="windowbg2">
            Status:<br>
            <span style="font-style:italic;">admin, HC, MV, SV, ...</span>
        </td>
        <td class="windowbg1">
            <input type="text" name="status" value="<?php echo $status;?>" style="width: 5em">
        </td>
    </tr>
    <tr>
        <td class="windowbg2">
            User sperren?:<br>
            <span style="font-style:italic;">Soll der User sich nicht mehr einloggen können?</span>
        </td>
        <td class="windowbg1">
            <input type="checkbox" name="gesperrt" value="1"<?php echo ($gesperrt) ? " checked" : "";?>>
        </td>
    </tr>
<?php
}
?>
<tr>
    <td class="windowbg2">
        Menü-Darstellung:
    </td>
    <td class="windowbg1">
        <select name="menu_default" style="width: 100">
            <?php
            foreach ($menustyles as $key => $data) {
                echo ($menu_default == $key)
                    ? "    <option value='" . $key . "' selected>" . $data . "</option>\n"
                    : "    <option value='" . $key . "'>" . $data . "</option>\n";
            }
            ?>
        </select>
    </td>
</tr>
<?php
if ($user_status == "admin") {
    ?>
    <tr>
        <td class="windowbg2">
            Gruppen:<br>
            <span style="font-style:italic;">Welchen Gruppen wird der User zugeordnet?</span>
        </td>
        <td class="windowbg1">
            <select name="groups[]" size="5" multiple="multiple">
                <?php
                foreach ($groups as $group) {
                    echo "<option value='";
                    echo $group["id"];
                    echo "'";
                    if ($group["selected"]) {
                        echo " selected>";
                    } else {
                        echo ">";
                    }
                    echo $group["name"];
                    echo "</option>";
                }
                ?>
            </select>
        </td>
    </tr>
<?php
}
?>
<tr>
    <td class="windowbg2">
        Proportionale Universumsansicht?:<br>
        <span
            style="font-style:italic;">Sollen alle Zeilenhöhen der Planeten in der Universumsansicht gleich hoch sein?</span>
    </td>
    <td class="windowbg1">
        <input type="checkbox" name="uniprop" value="1"<?php echo ($uniprop) ? " checked" : "";?>>
    </td>
</tr>
<tr>
    <td colspan="2" class="titlebg" align="center">
        <input type="hidden" name="edit" value="true"><input type="submit" value="speichern" name="B1" class="submit">
    </td>
</tr>
</table>
</form>
<?php
if (($user_status == "admin") && ($sitterlogin != $user_sitterlogin)) {
    ?>
    <br><br>
    <div class='doc_centered_blue'>Account löschen</div>
    <br>
    <a href="index.php?action=deluser&sitterlogin=<?php echo urlencode($sitterlogin);?>&sid=<?php echo $sid;?>"
       onclick="return confirmlink(this, 'Account wirklich loeschen?')">[jetzt
        löschen]</a>
<?php
}
?>
<br>
