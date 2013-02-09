<?php
/*****************************************************************************
 * m_sitterschleife.php                                                      *
 *****************************************************************************
 * Copyright (c) 2007 Quaki  - All Rights Reserved                           *
 * 16.03.08 Quaki auf user_db /user_iw umgestellt                            *
 * 30.03.08 Quaki offene Aufträge anzeigen                                   *
 * 14.04.08 Quaki Sitterauftrag erstellen eingebaut                          *
 * 24.04.08 Quaki Ikea anzeigen !!!                                          *
 * 28.04.08 Quaki Aufruf aus Sitterliste                                     *
 * 20.05.08 Quaki Startseite einlesen und auf Angriffe/Sondierung parsen     *
 * 24.05.08 Quaki Forschungsleerlauf/Bauleerlauf suchen und Auftrag anlegen  *
 * 02.06.08 Quaki time() -> $config_date                                     *
 * 13.09.08 Quaki newscan eingebaut                                             *
 * 04.10.08 Quaki Zeilenumbruch vor anderen Allianzen                         *
 * 07.10.08 Quaki U-Mod nicht in Sitterschleife anzeigen                     *
 * 20.05.09 Quaki Umbruch nach 20 Accounts                                     *
 * 26.05.09 Quaki Immer mit letztem Fleeter anfangen                         *
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
 * Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  *
 * IW-Spielern.                                                              *
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
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehoerigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname = "m_sitterschleife";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Sitterschleife";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation 
//    ausfuehren darf. Mögliche Werte:
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "admin";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "Dieses Modul dient dem Sitten in Schleife";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Function workInstallMenu is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modultitle, $modulstatus;

    $menu    = getVar('menu');
    $submenu = getVar('submenu');

    $actionparameters = "";
    insertMenuItem($menu, $submenu, $modultitle, $modulstatus, $actionparameters);
    //
    // Weitere Wiederholungen für weitere Menue-Eintraege, z.B.
    //
    //    insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
    //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file
//
function workInstallConfigString()
{
    return "";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase()
{
    echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgefuehrt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs: 
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" natürlich deinen Server angeben und default 
// durch den Dateinamen des Moduls ersetzen.
//
if (!empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
    if ($user_status != "admin") {
        die('Hacking attempt...');
    }

    echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")</div>\n";

    if (!@include("./includes/menu_fn.php")) {
        die("Cannot load menu functions");
    }

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgefuehrt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

if (($user_adminsitten != SITTEN_BOTH) && ($user_adminsitten != SITTEN_ONLY_LOGINS)) {
    die('Hacking attempt...');
}

echo '<style type="text/css">';
echo '.doc_menu {display:none;}';
echo '#iwdb_logo  {display:none;}';
echo '#iwdb_notices  {display:none;}';
echo '#next_sitterorders_table  {display:none;}';
echo '</style>';

doc_title($modultitle);

$r        = getvar('r');
$sub_back = getVar('sub_back');
$val_back = getVar('val_back');
$sub_forw = getVar('sub_forw');
$val_forw = getVar('val_forw');
$sub_akt  = getVar('sub_akt');
$val_akt  = getVar('val_akt');
if ($sub_back == 'zurück') {
    $r = $val_back;
}
if ($sub_forw == 'vorwärts') {
    $r = $val_forw;
}
if ($sub_akt == 'speichern') {
    $r = $val_akt;
}
$akt_user = getVar('akt_user');

$sitterlogin  = getVar('sitterlogin');
$sInhalt      = getVar('text');
$sitterlogins = array();
$offens       = array();
$AllyEnde     = 0;
$akt          = 0;

if ($sub_back == 'zurück' || $sub_forw == 'vorwärts' || $sub_akt == 'speichern') {
    if (!empty($akt_user) && !empty($sInhalt)) {
        // Account Startseite auf Forschungsleerlauf / Bauleerlauf etc durchsuchen
        $sitterschleife   = true;
        $selectedusername = $akt_user;
        include_once "newscan.php";
    }
}

// nur eigene Allianz
$sql = "SELECT sitterlogin, sitten, budflesol FROM $db_tb_user";
$sql .= " WHERE sitterpwd <> '' AND allianz = '{$user_allianz}' ORDER BY sitterlogin ASC;";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$budflesol = 0;
while ($row = $db->db_fetch_array($result)) {
    $sql = "SELECT t1.* FROM " . $db_tb_sitterauftrag . " as t1 LEFT JOIN " . $db_tb_sitterauftrag . " as t2 ON t1.id = t2.refid WHERE t2.refid is null AND t1.date_b2 <= " . $config_date;
    $sql .= " AND t1.user = '" . $row['sitterlogin'] . "' ";
    $result_auftrag = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row_auftrag = $db->db_fetch_array($result_auftrag);

    $offen = false;
    if ($row['sitterlogin'] == $row_auftrag['user']) {
        $offen = true;
    }

    $sitterlogins[] = $row['sitterlogin'];
    $offens[]       = $offen;
    if ($sitterlogin == $row['sitterlogin']) {
        $r = $akt;
    }
    if ($row['budflesol'] == 'Fleeter') {
        $budflesol = $akt;
    }
    $AllyEnde++;
    $akt++;
}

$forw = 0;
$akt  = 0;
$back = 0;

if (is_numeric($r)) {
    $akt = $r;
} else {
    $akt = $budflesol;
    $r   = $budflesol;
}

if ($akt == 0) {
    $back = count($sitterlogins) - 1;
}
if ($akt > 0) {
    $back = $akt - 1;
}
if ($akt == 0) {
    $forw = 1;
}
if ($akt < count($sitterlogins) - 1) {
    $forw = $akt + 1;
}

echo "<form method='POST' action='index.php?action=" . $modulname . "&amp;sid=" . $sid . "' enctype='multipart/form-data'>\n";
$j = 0;
for ($i = 0; $i < count($sitterlogins); $i++) {
    $offen = ($offens[$i] ? '_offen' : '');
    if ($i < $AllyEnde) {
        $class = 'button1' . $offen;
    } else {
        $class = 'button2' . $offen;
    }
    if ($i == $AllyEnde) {
        echo '<br><br>';
        $j = 0;
    }
    echo "<input type='button' class='$class' name='r' value='" . substr($sitterlogins[$i], 0, 5) . "' onClick='self.location.href=\"index.php?action=$modulname&amp;sid=$sid&amp;r=$i\"'> ";
    $j++;
    if ($j % 20 == 0) {
        echo "<br><br>";
    }
}
echo "<br><br>";

echo "<input type='submit' value='zurück' name='sub_back'>";
echo "<input type='hidden' value='$back' name='val_back'>";

if (isset($sitterlogins[$akt])) {
    echo "<input type='hidden' value='" . urlencode($sitterlogins[$akt]) . "' name='akt_user'>";
    echo " <b><big>&nbsp;$sitterlogins[$akt]&nbsp;";
    // Ikea/Peitschen nachlesen
    $sql = "SELECT ikea, peitschen FROM $db_tb_user WHERE sitterlogin = '" . $sitterlogins[$akt] . "'";
    $resultiw = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $rowiw = $db->db_fetch_array($resultiw);
    if ($rowiw['ikea'] == 'L') {
        echo "<font color=red>Ikea</font>";
    }
    if ($rowiw['ikea'] == 'M') {
        echo "<font color=red>Ikea</font>";
    }
    if ($rowiw['peitschen'] == 1) {
        echo "<font color=green>Peitschen</font>";
    }
    echo "&nbsp;</big></b> ";
}

echo "<input type='submit' value='speichern' name='sub_akt'>";
echo "<input type='hidden' value='$akt' name='val_akt'>";
echo "&nbsp;&nbsp;";
echo "<input type='submit' value='vorwärts' name='sub_forw'>";
echo "<input type='hidden' value='$forw' name='val_forw'>";

if ($user_adminsitten == SITTEN_BOTH || $user_status == 'admin') {
    echo "&nbsp;&nbsp;";
    echo "<a href='index.php?action=sitterauftrag&amp;sitterid=" . urlencode($sitterlogins[$akt]) .
        "&amp;sid=$sid' target=_new><img src='./bilder/file_new_s.gif' alt='Sitterauftrag erstellen' title='Sitterauftrag erstellen'></a>";
}

// offene Aufträge des aktuellen Accounts
unset($sitterlogin_akt);
if (isset($sitterlogins[$akt])) {
    $sitterlogin_akt = $sitterlogins[$akt];
}
if (isset($sitterlogins[$akt])) {
    include("sitterliste.php");
}

echo "<textarea name='text' cols='100' rows='2' style='border:2px black solid'></textarea>";
echo '</form>';
echo "<div class='textsmall'><small>Auf der Seite des Accounts alles markieren (Strg-A), alles kopieren (Strg-C) und oben ins Feld einfügen.</div>";
if (isset($sitterlogin_akt)) {
    echo "<iframe src='index.php?action=sitterlogins&amp;sitterlogin=" . urlencode($sitterlogin_akt) . "&amp;sid=" . $sid . "' width='100%' height='3500px' id='Account' name='SitterAuftrag'></iframe>";
}

