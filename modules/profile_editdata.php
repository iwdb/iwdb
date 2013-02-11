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

doc_title('Profil von ' . $id);

$editprofile = getVar('editprofile');
if (!empty($editprofile) AND (($id === $user_id) OR ($user_status === "admin"))) {
    $userd['newpassword']     = getVar('newpassword');
    $userd['newpasswordwdhl'] = getVar('newpasswordwdhl');
    $userd['email']        = getVar('email');
    $userd['allianz']      = getVar('allianz');
    $userd['squad']        = getVar('squad');

    $userd['grav_von'] = (float)getVar('grav_von');
    $userd['grav_bis'] = (float)getVar('grav_bis');
    list($userd['grav_von'], $userd['grav_bis']) = sortValuesInc($userd['grav_von'], $userd['grav_bis']);

    $userd['gal_start'] = (int)getVar('gal_start');
    $userd['gal_end']   = (int)getVar('gal_end');
    list($userd['gal_start'], $userd['gal_end']) = sortValuesInc($userd['gal_start'], $userd['gal_end']);

    $userd['sys_start'] = (int)getVar('sys_start');
    $userd['sys_end']   = (int)getVar('sys_end');
    list($userd['sys_start'], $userd['sys_end']) = sortValuesInc($userd['sys_start'], $userd['sys_end']);

    $userd['preset'] = getVar('preset');
    //sitten
    $userd['adminsitten']    = (int)getVar('adminsitten');
    $userd['sitten']         = (bool)getVar('sitten');
    $userd['sitterpwd']      = getVar('sitterpwd');
    $userd['sitterpwdwdhl']  = getVar('sitterpwdwdhl');
    $userd['sitterskin']     = (int)getVar('sitterskin');
    $userd['sittercomment']  = getVar('sittercomment');
    $userd['sound']          = (int)getVar('sound');
    $userd['peitschen']      = (bool)getVar('peitschen');
    $userd['ikea']           = getVar('ikea');
    $userd['genbauschleife'] = (bool)getVar('genbauschleife');
    $userd['genmaurer']      = (bool)getVar('genmaurer');
    $userd['gengebmod']      = (float)getVar('gengebmod');
    $userd['iwsa']           = (bool)getVar('iwsa');
    //sonstiges
    $userd['budflesol']     = getVar('budflesol');
    $userd['buddlerfrom']   = getVar('buddlerfrom');
    $userd['color']         = getVar('color');
    $userd['staatsform']    = getVar('staatsform');
    $userd['NewUniXmlTime'] = strtotime(getVar('NewUniXmlTime')) ? strtotime(getVar('NewUniXmlTime')) : null;
    $userd['planibilder']   = (bool)getVar('planibilder');
    $userd['gebbilder']     = (bool)getVar('gebbilder');
    $userd['sitterpunkte']  = (int)getVar('sitterpunkte');
    $userd['geopunkte']     = (int)getVar('geopunkte');
    $userd['syspunkte']     = (int)getVar('syspunkte');
    $userd['status']        = getVar('status');
    $userd['gesperrt']      = (bool)getVar('gesperrt');
    $userd['menu_default']  = getVar('menu_default');
    $userd['uniprop']       = (bool)getVar('uniprop');

    if ($user_status != "admin") {
        unset($userd['status']);
        unset($userd['adminsitten']);
        unset($userd['sitterpunkte']);
        unset($userd['geopunkte']);
        unset($userd['syspunkte']);
        unset($userd['sittercomment']);
        unset($userd['allianz']);
    }

    if ($user_status === "admin") {
        $userd['id'] = $id;
    }

    // Testet ob das Passwort sicher ist
    if (!empty($userd['newpassword'])) {
        $alert = secure_password($userd['newpassword']);

        if (!empty($alert)) {
            echo "<br><div class='system_error'>" . $alert . "</div>";
            unset($userd['newpassword']);
            unset($userd['newpasswordwdhl']);
        } else {
            if ($userd['newpassword'] != $userd['newpasswordwdhl']) {
                echo "<br><div class='system_error'>Passwörter stimmen nicht überein! Passwort zurückgesetzt.</div>";
                unset($userd['newpassword']);
                unset($userd['newpasswordwdhl']);
            } else {
                unset($userd['newpasswordwdhl']);
                $userd['password'] = md5($userd['newpassword']);
            }
        }
    } else {
        unset($userd['newpassword']);
        unset($userd['newpasswordwdhl']);
    }

    if ($userd['sitterpwd'] !== $userd['sitterpwdwdhl']) {
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

    $result = $db->db_update($db_tb_user, $userd, "WHERE `id`='{$id}';")
        or error(GENERAL_ERROR, 'Could not update user information.', '', __FILE__, __LINE__);
    echo "<div class='system_notification'>Userdaten aktualisiert.</div>";

    $sql = "SELECT t1.* FROM " . $db_tb_sitterauftrag . " as t1 LEFT JOIN " . $db_tb_sitterauftrag . " as t2 ON t1.id = t2.refid WHERE t2.refid is null AND t1.user='" . $id . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        if ($row['typ'] == "Gebaeude") {
            dates($row['id'], $id);
        }
    }
}

$groups = array();
$sql = "SELECT * FROM `$db_tb_group`;";
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
if (!empty($editprofile) && $user_status === "admin") {
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
    $sql = "SELECT * FROM `{$db_tb_group_user}` WHERE `{$db_tb_group_user}`.`user_id`='{$id}';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        $groups[$row["group_id"]]["selected"] = true;
    }
}

$sql = "SELECT * FROM `{$db_tb_user}` WHERE `id` = '{$id}';";
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

//auslesen aller Member
$alluser = array();
$sqlM = "SELECT `id` FROM `{$db_tb_user}`;";
$resultM = $db->db_query($sqlM)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
while ($rowM = $db->db_fetch_array($resultM)) {
    $alluser[$rowM['id']] = $rowM['id'];
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
<table class="table_format" style="width: 80%;">
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
        if ($user_status === "admin") {
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
        <input type="password" name="newpassword" autocomplete="off" value="" style="width: 25em">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Passwort Wiederholung:<br>
        <span style="font-style:italic;">Passwort zur Sicherheit wiederholen.</span>
    </td>
    <td class="windowbg1">
        <input type="password" name="newpasswordwdhl" autocomplete="off" value="" style="width: 25em">
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
        if ($user_status === "admin") {
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
        von
        <input type="number" min="0" max="12" step="0.1" name="grav_von" value="<?php echo $grav_von;?>" style="width: 5em" maxlength="3">
        bis
        <input type="number" min="0" max="12" step="0.1" name="grav_bis" value="<?php echo $grav_bis;?>" style="width: 5em" maxlength="3">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Galaxie:<br>
        <span style="font-style:italic;">Trage hier den Bereich der Galaxien ein, die du sehen kannst.</span>
    </td>
    <td class="windowbg1">
        von
        <input type="number" min="<?php echo $config_map_galaxy_min;?>" max="<?php echo $config_map_galaxy_max;?>" name="gal_start" value="<?php echo $gal_start;?>" style="width: 5em">
        bis
        <input type="number" min="<?php echo $config_map_galaxy_min;?>" max="<?php echo $config_map_galaxy_max;?>" name="gal_end" value="<?php echo $gal_end;?>" style="width: 5em">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        System:<br>
        <span style="font-style:italic;">Trage hier den Bereich der Systeme ein, die du sehen kannst.</span>
    </td>
    <td class="windowbg1">
        von
        <input type="number" min="<?php echo $config_map_system_min;?>" max="<?php echo $config_map_system_max;?>" name="sys_start" value="<?php echo $sys_start;?>" style="width: 5em">
        bis
        <input type="number" min="<?php echo $config_map_system_min;?>" max="<?php echo $config_map_system_max;?>" name="sys_end" value="<?php echo $sys_end;?>" style="width: 5em">
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
            $sql = "SELECT id, name FROM " . $db_tb_preset . " WHERE (fromuser = '" . $id . "' OR fromuser = '') ORDER BY fromuser, name";
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
        if ($user_status === "admin") {
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
        <select name="sitten">
            <option value="" <?php echo ($sitten) ? '' : 'selected';?>>nein</option>
            <option value="1" <?php echo ($sitten) ? 'selected' : '';?>>ja</option>
        </select>
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
        löschen?
        <input type="checkbox" name="deleteSitterpass" value="1">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Sitterpasswort Wiederholung:
    </td>
    <td class="windowbg1">
        <input type="password" name="sitterpwdwdhl" value="<?php echo $sitterpwdsp;?>" style="width: 25em">
        löschen?
        <input type="checkbox" name="deleteSitterpasswdh" value="1">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Serverskin:<br>
        <span style="font-style:italic;">Welchen Skin möchtest du beim Sitten verwenden?</span>
    </td>
    <td class="windowbg1">
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $skins,
                 "value"  => $sitterskin,
            ), 'sitterskin'
        );
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
        if ($user_status === "admin") {
            echo "<textarea name='sittercomment' id='sittercomment' rows='5' cols='50'>$sittercomment</textarea>\n";
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
        <select name="sound">
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
        <span style="font-style:italic;">Wenn du die Genetikoption hast, bitte auswählen.</span>
    </td>
    <td class="windowbg1">
        <select name="peitschen">
            <option value="" <?php echo ($peitschen) ? '' : 'selected';?>>nein</option>
            <option value="1" <?php echo ($peitschen) ? 'selected' : '';?>>ja</option>
        </select>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Ikea?:<br>
        <span style="font-style:italic;">Wenn du die Genetikoption hast, bitte auswählen.</span>
    </td>
    <td class="windowbg1">
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => array('' => 'kein Ikea', 'L' => 'Lehrling des IKEA', 'M' => 'Meister des IKEA'),
                 "value"  => $ikea,
            ), 'ikea'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Ich will mehr Zeit?:<br>
        <span style="font-style:italic;">Wenn du die Genetikoption hast, bitte auswählen.</span>
    </td>
    <td class="windowbg1">
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => array('' => 'nein', '1' => 'ja'),
                 "value"  => $genbauschleife,
            ), 'genbauschleife'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Der Einmaurer?:<br>
        <span style="font-style:italic;">Wenn du die Genetikoption hast, bitte auswählen.</span>
    </td>
    <td class="windowbg1">
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => array('' => 'nein', '1' => 'ja'),
                 "value"  => $genmaurer,
            ), 'genmaurer'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Bau auf Bau auf Bau auf Bau auf?:<br>
        <span style="font-style:italic;">Stelle hier deinen Gebäudebaudauermodifikator ein (Standard +-0%).</span>
    </td>
    <td class="windowbg1">
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => array('0.90' => '-10%', '0.95' => '-5%', '1.00' => '+-0%', '1.05' => '+5%', '1.10' => '+10%'),
                 "value"  => $gengebmod,
            ), 'gengebmod'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        IWSA/IWBP-Account?:<br>
  <span style="font-style:italic;">Wenn du einen solchen Account hast, bitte Haken setzen.
     Wichtig wegen FP!</span>
    </td>
    <td class="windowbg1">
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => array('' => 'nein', '1' => 'ja'),
                 "value"  => $iwsa,
            ), 'iwsa'
        );
        ?>
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
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $spieltyp,
                 "value"  => $budflesol,
            ), 'budflesol'
        );

        echo 'von';

        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => array('' => '---') + $alluser,
                 "value"  => $buddlerfrom,
            ), 'buddlerfrom'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Deine Farbe:<br>
        <span style="font-style:italic;">Sollest du Fleeter sein, trage hier eine Farbe für deine Buddler ein (Format: #RRGGBB)</span>
    </td>
    <td class="windowbg1">
        <input type="text" name="color" pattern="#?[0-9a-fA-F]{0,6}" size="8" maxlength="7" value="<?php echo $color;?>">
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Staatsform:<br>
        <span style="font-style:italic;">Deine Staatsform.</span>
    </td>
    <td class="windowbg1">
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $staatsformen,
                 "value"  => $staatsform,
            ), 'staatsform'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2" style="width:30%;">
        nächster Unixml Scan:<br>
        <span style="font-style:italic;">Nächster Zeitpunkt des UniXml Scans.</span>
    </td>
    <td class="windowbg1">
        <input type='text' name='NewUniXmlTime' value='<?php echo ($NewUniXmlTime ? strftime(CONFIG_DATETIMEFORMAT, $NewUniXmlTime) : ''); ?>' style='width: 15em;'>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Planetenbilder anzeigen?:<br>
        <span style="font-style:italic;">Sollen Bilder, den Planetentypen entsprechend in der Karte angezeigt werden?</span>
    </td>
    <td class="windowbg1">
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => array('' => 'nein', '1' => 'ja'),
                 "value"  => $planibilder,
            ), 'planibilder'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2">
        Gebäudebilder anzeigen?:<br>
        <span style="font-style:italic;">Sollen Gebäudebilder beim Erstellen eines Auftrages und bei "Gebäude ausblenden" angezeigt werden?</span>
    </td>
    <td class="windowbg1">
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => array('' => 'nein', '1' => 'ja'),
                 "value"  => $gebbilder,
            ), 'gebbilder'
        );
        ?>
    </td>
</tr>
<tr>
    <td class="windowbg2" style="width:30%;">
        Sitterpunkte:<br>
        <span style="font-style:italic;">So viele Punkte hast du schon fürs Sitten erhalten.</span>
    </td>
    <td class="windowbg1">
        <?php
        if ($user_status === "admin") {
            echo "<input type='text' pattern='[0-9]*' name='sitterpunkte' value='$sitterpunkte' style='width: 5em'>\n";
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
        Geoscanpunkte:<br>
        <span style="font-style:italic;">So viele GeoScans hast du schon eingestellt.</span>
    </td>
    <td class="windowbg1">
        <?php
        if ($user_status === "admin") {
            echo "<input type='text' pattern='[0-9]*' name='geopunkte' value='$geopunkte' style='width: 5em;'>\n";
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
        if ($user_status === "admin") {
            echo "<input type='text' pattern='[0-9]*' name='syspunkte' value='$syspunkte' style='width: 5em;'>\n";
        } else {
            echo $syspunkte;
        }
        ?>
    </td>
</tr>
<?php
if ($user_status === "admin") {
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
        <?php
        echo makeField(
            array(
                 "type"   => 'select',
                 "values" => $menustyles,
                 "value"  => $menu_default,
            ), 'menu_default'
        );
        ?>
    </td>
</tr>
<?php
if ($user_status === "admin") {
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
    <td colspan="2" class="titlebg center">
        <input type="submit" value="speichern" name="editprofile">
    </td>
</tr>
</table>
</form>
<?php
if (($user_status === "admin") && ($id !== $user_id)) {
    ?>
    <br><br>
    <div class='doc_centered_blue'>Account löschen</div>
    <br>
    <a href="index.php?action=deluser&sitterlogin=<?php echo urlencode($sitterlogin);?>&sid=<?php echo $sid;?>"
       onclick="return confirmlink(this, 'Account wirklich löschen?')">[jetzt löschen]</a>
<?php
}
?>
<br>