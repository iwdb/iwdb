<?php
/*****************************************************************************
 * admin_einstellungen.php                                                   *
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

if ($user_status != "admin" && $user_status != "hc") {
    die('Hacking attempt...');
}

//****************************************************************************

doc_title("Admin Einstellungen");

global $db, $db_tb_params, $db_tb_menu;

/*****************************************************************************/
/* Fade-in Teil                                                              */
/*****************************************************************************/

if (GetVar('BS')) {

    $sound_standard = (int)GetVar('sound_standard');
    $sound_login    = (int)GetVar('sound_login');

    $aSqlData = array('name' => 'sound_standard', 'value' => $sound_standard);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'sound_login', 'value' => $sound_login);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $sqlM = "ALTER TABLE `{$db_tb_menu}` CHANGE `sound` `sound` TINYINT( 1 ) DEFAULT '" . $sound_standard . "'";
    $resultM = $db->db_query($sqlM);

    $db->db_update($db_tb_menu, array('sound' => 0));

    $sound_menu = GetVar('sound_menu');
    foreach ($sound_menu as $menuid) {
        $sqlM = "UPDATE `{$db_tb_menu}` SET sound = 1 WHERE id = " . $menuid . ";";
        $resultM = $db->db_query($sqlM);
    }

}

$menu_sel = array();
$menu_not = array();

//auslesen aller Menüpunkte, um eine Liste zu erstellen, wo der Sound abgespielt werden soll
$sqlM = "SELECT `action`,`sound`,`id` FROM `{$db_tb_menu}`;";
$resultM = $db->db_query($sqlM);

while ($rowM = $db->db_fetch_array($resultM)) {

    if (!empty($rowM['action'])) {

        //action auslesen
        $action = $rowM['action'];

        //ist action nicht leer, entschieden wo es hin soll:
        if (!empty($action) AND empty($rowM['sound'])) {
            $menu_not[$action]['name'] = $action;
            $menu_not[$action]['id']   = $rowM['id'];
        }
        if (!empty($action) AND !empty($rowM['sound'])) {
            $menu_sel[$action]['name'] = $action;
            $menu_sel[$action]['id']   = $rowM['id'];
        }
    }
}

//auslesen des standards
$sqlP = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'sound_standard';";
$resultP = $db->db_query($sqlP);
$rowP = $db->db_fetch_array($resultP);

if (!empty($rowP['value'])) {
    $sel_sel = 'checked="checked"';
    $sel_not = '';
} else {
    $sel_not = 'checked="checked"';
    $sel_sel = '';
}

$sqlP = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'sound_login';";
$resultP = $db->db_query($sqlP);
$rowP = $db->db_fetch_array($resultP);

$sel_login = '';
if (!empty($rowP['value'])) {
    $sel_login = 'checked="checked"';
}

?>
<br>
<form method="POST" action="index.php?action=admin&uaction=einstellungen&send=sound" enctype="multipart/form-data">
    <table class="table_format" style="width: 95%;">
        <tr>
            <th colspan="2" class="titlebg">
                <b>Sitterbenachrichtigung:</b>
            </th>
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
                    <?php endforeach;  foreach ($menu_not as $menu): ?>
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
                echo "<input type='radio' name='sound_standard' id='sound_on' value='1' $sel_sel><label for='sound_on'>Sound eingeschaltet</label>";
                echo "<input type='radio' name='sound_standard' id='sound_off' value='0' $sel_not><label for='sound_off'>Sound ausgeschaltet</label>";
                ?></td>
        </tr>
        <tr>
            <td colspan="2" class="titlebg center">
                <input type="submit" value="Fadeineinstellungen ändern" name="BS">
            </td>
        </tr>
    </table>
</form>
<br><br>
<?php

/*******************************************************************************/
/* 'Bericht einfügen für' Teil                                                 */
/*******************************************************************************/
if (GetVar('BE')) {

    $bericht_fuer_rang   = GetVar('bericht_fuer_rang');
    $bericht_fuer_sitter = (int)GetVar('bericht_fuer_sitter');

    $aSqlData = array('name' => 'bericht_fuer_rang', 'value' => $bericht_fuer_rang);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'bericht_fuer_sitter', 'value' => $bericht_fuer_sitter);
    $db->db_insertupdate($db_tb_params, $aSqlData);

}

//auslesen rang
$sqlP = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'bericht_fuer_rang';";
$resultP = $db->db_query($sqlP);
$rowP = $db->db_fetch_array($resultP);

$sel_all = '';
$sel_mv = '';
$sel_hc = '';
$sel_admin = '';
if (!empty($rowP['value'])) {
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
$sqlP = "SELECT value FROM `{$db_tb_params}` WHERE name = 'bericht_fuer_sitter' ";
$resultP = $db->db_query($sqlP);
$rowP = $db->db_fetch_array($resultP);

$sitval0 = '';
$sitval1 = '';
$sitval2 = '';
$sitval3 = '';

if (!empty($rowP['value'])) {
    ${'sitval' . $rowP['value']} = 'selected';
} else {
    $sitval0 = 'selected';
}

?>

<form method="POST" action="index.php?action=admin&uaction=einstellungen&send=bericht" enctype="multipart/form-data">
    <table class="table_format" style="width: 95%;">
        <tr>
            <th colspan="2" class="titlebg">
                <b>'Bericht einfügen für':</b>
            </th>
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
            <td colspan="2" class="titlebg center">
                <input type="submit" value="'Bericht einfügen für' ändern" name="BE">
            </td>
        </tr>
    </table>
</form>
<br><br>
<?php

/*******************************************************************************/
/* automatisches Credsbestellen Teil, gibt es nur bei vorhandenem Bestellmodul */
/*******************************************************************************/
if (isset($db_tb_bestellung)) {

    if (GetVar('automatic_creds_order_change')) {

        $automatic_creds_order           = GetVar('automatic_creds_order');
        $automatic_creds_order_minvalue  = filter_int(GetVar('automatic_creds_order_minvalue'), false, 0); //no default, Minimum 0
        $automatic_creds_order_minpayout = filter_int(GetVar('automatic_creds_order_minpayout'), '', 0); //default '', Minimum 0

        if (($automatic_creds_order === 'true') AND (!empty($automatic_creds_order_minvalue))) {

            $aSqlData = array('name' => 'automatic_creds_order', 'value' => 'true');
            $db->db_insertupdate($db_tb_params, $aSqlData);

            $aSqlData = array('name' => 'automatic_creds_order_minvalue', 'value' => $automatic_creds_order_minvalue);
            $db->db_insertupdate($db_tb_params, $aSqlData);

            $aSqlData = array('name' => 'automatic_creds_order_minpayout', 'value' => $automatic_creds_order_minpayout);
            $db->db_insertupdate($db_tb_params, $aSqlData);

        } else {

            $aSqlData = array('name' => 'automatic_creds_order', 'value' => 'false');
            $db->db_insertupdate($db_tb_params, $aSqlData);

        }

    }

    //Status der automatischen Credsbestellung aus der DB holen
    $sth = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'automatic_creds_order';");
    $row = $db->db_fetch_array($sth);

    $sel_automatic_creds_order       = '';
    $automatic_creds_order_minvalue  = '';
    $automatic_creds_order_minpayout = '';
    if (!empty($row)) {
        if ($row['value'] === 'true') { //Eintrag für automatische Bestellung vorhanden und aktiv
            $sel_automatic_creds_order = 'checked="checked"';
        }

        $sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'automatic_creds_order_minvalue';";
        $sth = $db->db_query($sql);

        $row = $db->db_fetch_array($sth);

        $automatic_creds_order_minvalue = number_format((float)$row['value'], 0, ',', '.');

        $sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'automatic_creds_order_minpayout';";
        $sth = $db->db_query($sql);

        $row = $db->db_fetch_array($sth);

        $automatic_creds_order_minpayout = number_format((float)$row['value'], 0, ',', '.');
    }

    ?>
    <form method="POST" action="index.php?action=admin&uaction=einstellungen" enctype="multipart/form-data">
        <table class="table_format" style="width: 95%;">
            <tr>
                <th colspan="2" class="titlebg">
                    <b>Automatische Creditsbestellung:</b>
                </th>
            </tr>
            <tr>
                <td class="windowbg2" style="width:40%;">
                    automatisch Credits bestellen?:<br>
                    <i>Sollen automatisch Credits bei unterschreiten<br> der Mindestmenge bestellt werden?</i>
                </td>
                <td class="windowbg1" style="width: 60%">
                    <input type="checkbox" name="automatic_creds_order" <?php echo $sel_automatic_creds_order;?> value="true">
                </td>
            </tr>
            <tr>
                <td class="windowbg2" style="width:40%;">
                    Mindestmenge:<br>
                    <i>Mindestmenge der vorhandenen Credits</i>
                </td>
                <td class="windowbg1">
                    <input type="text" name="automatic_creds_order_minvalue" value="<?php echo $automatic_creds_order_minvalue;?>" style="width:10em;">&nbsp;Credits
                </td>
            </tr>
            <tr>
                <td class="windowbg2" style="width:40%;">
                    Mindestauszahlungsmenge:<br>
                    <i>Kleinste Menge auszuzahlender Credits</i>
                </td>
                <td class="windowbg1">
                    <input type="text" name="automatic_creds_order_minpayout" value="<?php echo $automatic_creds_order_minpayout;?>" style="width:10em;">&nbsp;Credits
                </td>
            </tr>
            <tr>
                <td colspan="2" class="titlebg center">
                    <input type="submit" value="ändern" name="automatic_creds_order_change">
                </td>
            </tr>
        </table>
    </form>
    <br><br>
<?php
}

/*****************************************************************************/
/* Lagerbedarfsbelegung                                                      */
/*****************************************************************************/

if (GetVar('stunden_change')) {
	$hour_eisen = filter_int(GetVar('stunden_eisen'), '', 0); //default '', Minimum 0
	$hour_stahl = filter_int(GetVar('stunden_stahl'), '', 0);
	$hour_vv4a = filter_int(GetVar('stunden_vv4a'), '', 0);
	$hour_chemie = filter_int(GetVar('stunden_chemie'), '', 0);
	$hour_eis = filter_int(GetVar('stunden_eis'), '', 0);
	$hour_wasser = filter_int(GetVar('stunden_wasser'), '', 0);
	$hour_energie = filter_int(GetVar('stunden_energie'), '', 0);
	$max_eisen = filter_int(GetVar('lager_eisen'), '', 0);

    $aSqlData = array('name' => 'hour_eisen', 'value' => $hour_eisen);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'hour_stahl', 'value' => $hour_stahl);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'hour_vv4a', 'value' => $hour_vv4a);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'hour_chemie', 'value' => $hour_chemie);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'hour_eis', 'value' => $hour_eis);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'hour_wasser', 'value' => $hour_wasser);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'hour_energie', 'value' => $hour_energie);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'max_eisen', 'value' => $max_eisen);
    $db->db_insertupdate($db_tb_params, $aSqlData);

}

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_eisen';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$hour_eisen = $row['value'];

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_stahl';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$hour_stahl = $row['value'];

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_vv4a';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$hour_vv4a = $row['value'];

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_chemie';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$hour_chemie = $row['value'];

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_eis';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$hour_eis = $row['value'];

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_wasser';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$hour_wasser = $row['value'];

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_energie';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$hour_energie = $row['value'];

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'max_eisen';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$max_eisen = $row['value'];

?>
<form method="POST" action="index.php?action=admin&uaction=einstellungen" enctype="multipart/form-data">
	<table class="table_format" style="width: 95%;">
		<thead>
			<tr class='center'>
				<th data-sorter="false" colspan="2">
					<b>Voreinstellung Stunden für Lagergrundbedarf</b>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="windowbg2" style="width:40%; background-color: #ADD8E6">
					<b>für Eisen</b>
				</td>
				<td class="left">
					<input type="number" name="stunden_eisen" min="1" max="400" step="1" value="<?php echo $hour_eisen ?>">
				</td>
			</tr>
			<tr>
				<td class="windowbg2" style="width:40%; background-color: #ADD8E6">
					<b>für Stahl</b>
				</td>
				<td class="left">
					<input type="number" name="stunden_stahl" min="1" max="400" step="1" value="<?php echo $hour_stahl ?>">
				</td>
			</tr>
			<tr>
				<td class="windowbg2" style="width:40%; background-color: #ADD8E6">
					<b>für VV4A</b>
				</td>
				<td class="left">
					<input type="number" name="stunden_vv4a" min="1" max="400" step="1" value="<?php echo $hour_vv4a ?>">
				</td>
			</tr>
			<tr>
				<td class="windowbg2" style="width:40%; background-color: #ADD8E6">
					<b>für Chemie</b>
				</td>
				<td class="left">
					<input type="number" name="stunden_chemie" min="1" max="400" step="1" value="<?php echo $hour_chemie ?>">
				</td>
			</tr>
			<tr>
				<td class="windowbg2" style="width:40%; background-color: #ADD8E6">
					<b>für Eis</b>
				</td>
				<td class="left">
					<input type="number" name="stunden_eis" min="1" max="400" step="1" value="<?php echo $hour_eis ?>">
				</td>
			</tr>
			<tr>
				<td class="windowbg2" style="width:40%; background-color: #ADD8E6">
					<b>für Wasser</b>
				</td>
				<td class="left">
					<input type="number" name="stunden_wasser" min="1" max="400" step="1" value="<?php echo $hour_wasser ?>">
				</td>
			</tr>
			<tr>
				<td class="windowbg2" style="width:40%; background-color: #ADD8E6">
					<b>für Energie</b>
				</td>
				<td class="left">
					<input type="number" name="stunden_energie" min="1" max="400" step="1" value="<?php echo $hour_energie ?>">
				</td>
			</tr>
			<tr>
				<td class="windowbg2" style="width:40%; background-color: #ADD8E6">
					<b>Eisengrundbedarf wenn nichts vorhanden</b>
				</td>
				<td class="left">
					<input type="number" name="lager_eisen" value="<?php echo $max_eisen ?>">
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr class='center'>
				<th colspan="2">
					<input type="submit" value="ändern" name="stunden_change">
				</th>
			</tr>
		</tfoot>
	</table>
</form>
<br><br>
<?php
/*****************************************************************************/
/* DB-Sperre Teil                                                            */
/*****************************************************************************/

if (GetVar('iwdb_lock_change')) {

    $iwdb_locked      = ensureValue(GetVar('iwdb_locked'), array('true', 'false'), 'false');
    $iwdb_lock_reason = $db->escape(GetVar('iwdb_lock_reason'));

    $aSqlData = array('name' => 'gesperrt', 'value' => $iwdb_locked);
    $db->db_insertupdate($db_tb_params, $aSqlData);

    $aSqlData = array('name' => 'gesperrt_grund', 'value' => $iwdb_lock_reason);
    $db->db_insertupdate($db_tb_params, $aSqlData);

}

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'gesperrt';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$iwdb_locked = $row['value'];

$sql = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'gesperrt_grund';";
$result = $db->db_query($sql);
$row = $db->db_fetch_array($result);
$iwdb_lock_reason = $row['value'];

$sel_iwdb_locked = '';
if ($iwdb_locked === 'true') {
    $sel_iwdb_locked = 'checked="checked"';
}
?>
<form method="POST" action="index.php?action=admin&uaction=einstellungen&send=sperre" enctype="multipart/form-data">
    <table class="table_format" style="width: 95%;">
        <tr>
            <th colspan="3" class="titlebg" style="background-color: #ff0000">
                <b>IWDB-Sperre:</b>
            </th>
        </tr>
        <tr>
            <td class="windowbg2" style="width:40%; background-color: #ff8080">IWDB sperren?:</td>
            <td class="windowbg1" style="width: 6%">
                <input type="checkbox" name="iwdb_locked" <?php echo $sel_iwdb_locked;?> value="true">
            </td>
            <td class="windowbg1" style="width: 54%">
                Grund:&nbsp;&nbsp;<input type="text" name="iwdb_lock_reason" value="<?php echo $iwdb_lock_reason;?>" style="width: 80%">
            </td>
        </tr>
        <tr>
            <td colspan="3" class="titlebg center" style="background-color: #ff0000">
                <input type="submit" value="ändern" name="iwdb_lock_change">
            </td>
        </tr>
    </table>
</form>