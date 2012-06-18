<?php
/*****************************************************************************/
/* admin_allianzstatus.php                                                   */
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

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	exit("Hacking attempt...!!");
}

if ( $user_status != "admin" && $user_status != "hc" )
	die('Hacking attempt...');

doc_title("Admin Einstellungen");
echo "<br>\n";

/*****************************************************************************/
/* Fade-in Teil                                                              */
/*****************************************************************************/

$bs = GetVar('BS');
if ( !empty($bs) ) {

    $sound_standart = (int)GetVar('sound_standart');
    $sound_login = (int)GetVar('sound_login');

    $sqlP = "UPDATE ".$db_prefix."params SET value = '".$sound_standart."' WHERE name = 'sound_standart'";
    $resultP = $db->db_query($sqlP)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);

    $sqlP = "UPDATE ".$db_prefix."params SET value = '".$sound_login."' WHERE name = 'sound_login'";
    $resultP = $db->db_query($sqlP)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);

    $sqlM = "ALTER TABLE ".$db_prefix."menu CHANGE `sound` `sound` INT( 1 ) DEFAULT '".$sound_standart."'";
    $resultM = $db->db_query($sqlM)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlM);

    $sqlM = "UPDATE ".$db_prefix."menu SET sound = '0'";
    $resultM = $db->db_query($sqlM)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlM);

    $sound_menu = GetVar('sound_menu');
    foreach ($sound_menu as $menuid) {
        $sqlM = "UPDATE ".$db_prefix."menu SET sound = 1 WHERE id = ".$menuid.";";
        $resultM = $db->db_query($sqlM)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlM);
    }

}

global $db_prefix, $sid;

$menu_sel = array();
$menu_not = array();

//auslesen aller Menüpunkte, um eine Liste zu erstellen, wo der Sound abgespielt werden soll
$sqlM = "SELECT action,sound,id FROM ".$db_prefix."menu ";
$resultM = $db->db_query($sqlM)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlM);

while ($rowM = $db->db_fetch_array($resultM)) {

    if ( !empty($rowM['action']) ) {

        //action auslesen
        $action = $rowM['action'];

        //ist action nicht leer, entschieden wo es hin soll:
        if ( !empty($action) AND empty($rowM['sound']) ) {
            $menu_not[$action]['name'] = $action;
            $menu_not[$action]['id'] = $rowM['id'];
        }
        if ( !empty($action) AND !empty($rowM['sound']) ) {
            $menu_sel[$action]['name'] = $action;
            $menu_sel[$action]['id'] = $rowM['id'];
        }
    }
}

//auslesen des standards
$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'sound_standart' ";
$resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
$rowP = $db->db_fetch_array($resultP);

if ( !empty($rowP['value']) ) {
    $sel_sel = 'checked="checked"';
    $sel_not = '';
} else {
    $sel_not = 'checked="checked"';
    $sel_sel = '';
}

$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'sound_login' ";
$resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
$rowP = $db->db_fetch_array($resultP);

$sel_login = '';
if ( !empty($rowP['value']) ) {
    $sel_login = 'checked="checked"';
}

?>

<br>
<form method="POST" action="index.php?action=admin&uaction=einstellungen&send=sound&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
    <tr>
        <td colspan="2" class="titlebg">
        <b>Sitterbenachrichtigung:</b>
        </td>
    </tr>
    <tr>
        <td class="windowbg2" style="width:40%;">
        Benachrichtigung möglich:<br>
        <i>Hier wird das Fenster eingeblendet</i>
        </td>
        <td class="windowbg1">
        <select name="sound_menu[]" size="10" multiple="multiple">
            <?php foreach ($menu_sel as $menu): ?>
            <option value="<?php echo $menu['id']?>" selected><?php echo $menu['name']?></option>
            <?php endforeach; ?>
            <?php foreach ($menu_not as $menu): ?>
            <option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
            <?php endforeach; ?>
        </select>
        </td>
    </tr>
    <tr>
        <td class="windowbg2" style="width:40%;">
        Benachrichtigung auch beim Login:<br>
        </td>
        <td class="windowbg1"><?php
        echo "<input type='checkbox' name='sound_login' value='1' $sel_login>";
        ?></td>
    </tr>
    <tr>
        <td class="windowbg2" style="width:40%;">
        Standardeinstellung:<br>
        <i>Welche Einstellung sollen neu installierte Module haben?</i>
        </td>
        <td class="windowbg1"><?php
        echo "<input type='radio' name='sound_standart' id='sound_on' value='1' $sel_sel><label for='sound_on'>Sound eingeschaltet</label>";
        echo "<input type='radio' name='sound_standart' id='sound_off' value='0' $sel_not><label for='sound_off'>Sound ausgeschaltet</label>";
        ?></td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg" align="center">
        <input type="submit" value="Fadeineinstellungen ändern" name="BS">
        </td>
    </tr>
</table>
</form>
<br>

<?php

$be = GetVar('BE');
if ( !empty($be) ) {

    $bericht_fuer_rang = GetVar('bericht_fuer_rang');
    $bericht_fuer_sitter = (int)
    GetVar('bericht_fuer_sitter');

    $sqlP = "UPDATE ".$db_prefix."params SET value = '".$bericht_fuer_rang."' WHERE name = 'bericht_fuer_rang'";
    $resultP = $db->db_query($sqlP)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);

    $sqlP = "UPDATE ".$db_prefix."params SET value = '".$bericht_fuer_sitter."' WHERE name = 'bericht_fuer_sitter'";
    $resultP = $db->db_query($sqlP)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);

}

//auslesen rang
$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'bericht_fuer_rang' ";
$resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
$rowP = $db->db_fetch_array($resultP);

$sel_all='';
$sel_mv='';
$sel_hc='';
$sel_admin='';
if ( !empty($rowP['value']) ) {
    switch ($rowP['value']) {
        case 'alle':
            $sel_all = 'selected';
            break;
        case 'mv':
            $sel_mv = 'selected';
            break;
        case 'hc':
            $sel_hc = 'selected';
            break;
        case 'admin':
            $sel_admin = 'selected';
            break;
        default:
            $sel_admin = 'selected';
            break;
    }
} else {
    $sel_admin = 'selected';
}

//auslesen sitter
$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'bericht_fuer_sitter' ";
$resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
$rowP = $db->db_fetch_array($resultP);

$sitval0 = '';
$sitval1 = '';
$sitval2 = '';
$sitval3 = '';

if ( !empty($rowP['value']) ) {
   ${'sitval'.$rowP['value']} = 'selected';
} else {
   $sitval0 = 'selected';
}

?>

<form method="POST" action="index.php?action=admin&uaction=einstellungen&send=bericht&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
    <tr>
        <td colspan="2" class="titlebg">
        <b>'Bericht einfügen für':</b>
        </td>
    </tr>
    <tr>
        <td class="windowbg2" style="width:40%;">
        Bericht einfügen für:<br>
        <i>Wer darf das Fenster 'Bericht einfügen für' nutzen?</i>
        </td>
        <td class="windowbg1">
        Rang:<br>
        <select name="bericht_fuer_rang" size="1"><?php
            echo "\n";
            echo "            <option value='alle' $sel_all>Alle</option>\n";
            echo "            <option value='mv' $sel_mv>MV / HC und Admin</option>\n";
            echo "            <option value='hc' $sel_hc>HC und Admin</option>\n";
            echo "            <option value='admin' $sel_admin>Admin</option>\n";
        ?>
        </select>
        <br><br>
        Sittertyp:<br>
        <select name="bericht_fuer_sitter" size="1"><?php
            echo "\n";
            echo "            <option value='2' $sitval2>Sitterbereich deaktiviert</option>\n";
            echo "            <option value='0' $sitval0>kann Sitteraufträge erstellen, darf keine anderen sitten</option>\n";
            echo "            <option value='3' $sitval3>darf andere sitten, darf keine Sitteraufträge erstellen</option>\n";
            echo "            <option value='1' $sitval1>darf andere sitten, darf Sitteraufträge erstellen</option>\n";
        ?>
        </select>
    </td>
    </tr>
    <tr>
        <td colspan="2" class="titlebg" align="center">
        <input type="submit" value="'Bericht einfügen für' ändern" name="BE">
        </td>
    </tr>
</table>
</form>