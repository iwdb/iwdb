<?php
// http://www.icewars.de/portal/kb/de/kb.php?id=787974&md_hash=cce98800e55d87e7109105d4ef54dfde

/*****************************************************************************
 * m_kb.php                                                                   *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * Software Version: Iw DB 1.00                                              *
 * ========================================================================= *
 * Software Distributed by:    http://lauscher.riess.net/iwdb/               *
 * Support, News, Updates at:  http://lauscher.riess.net/iwdb/               *
 * ========================================================================= *
 * Copyright (c) 2007 Quaki  - All Rights Reserved                           *
 *                                                                           *
 * 07.10.08 Quaki [nobbc] vor Allytag eingebaut                              *
 * 13.10.08 Quaki Fake-Angriffe anzeigen an/aus                              *
 * 17.10.08 Quaki $fake durchreichen                                         *
 * 27.10.08 Quaki FC-Statistiken eingebaut                                   *
 * 28.10.08 Quaki FC-Details ein/ausklappen                                  *
 * 20.01.08 Quaki Anzeige fehlende Gebdsan eingebaut                         *
 * 20.01.08 Quaki Fake = rot wenn Flotte gefunden wurde                      *
 * 26.03.09 Quaki Nur Angriffe auf 1 Koord anzeigen                          *
 * 31.03.09 Quaki mysql_free ...                                             *
 * 05.04.09 Quaki Verlustanzeige aufgeteilt                                  *
 * 05.07.09 Quaki Fleeter anzeigen                                           *
 * 27.11.09 Quaki DB-Zugriff korrigiert                                      *
 * 02.12.09 Quaki für Forum aufbereitete KB markiert                         *
 * 02.12.09 Quaki fopen ausgelagert                                          *
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
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname = "m_kb";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Kriegs-Statistik";

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
$moduldesc =
    "Dieses Modul dient der Erstellung von Kriegs-Statistiken";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    global $db, $db_prefix, $db_tb_scans;

    $sqlscript = array(
        "CREATE TABLE IF NOT EXISTS " . $db_prefix . "kb_war ( 
     w_id int(11) NOT NULL auto_increment, 
     w_freund varchar(254) NOT NULL default '', 
     w_feind varchar(254) NOT NULL default '', 
     PRIMARY KEY  (w_id) 
     )",
        "CREATE TABLE IF NOT EXISTS " . $db_prefix . "kb_kb (
     w_id int(11) NOT NULL default '0',
     k_id int(11) NOT NULL auto_increment,
     k_typ char(1) NOT NULL default '',
     k_art char(1) NOT NULL default '',
     k_kb varchar(254) NOT NULL default '',
     k_time int(11) default NULL,
     k_opfer varchar(60) NOT NULL default '',
     k_ally varchar(30) NOT NULL default '',
     k_ort varchar(10) NOT NULL default '',
     k_attally varchar(100) NOT NULL default '',
     k_attspiel varchar(254) NOT NULL default '',
     k_sieg char(1) NOT NULL default '',
     k_mauer char(1) NOT NULL default '',
     k_atteisen int(11) NOT NULL default '0',
     k_attstahl int(11) NOT NULL default '0',
     k_attvv4a int(11) NOT NULL default '0',
     k_attchem int(11) NOT NULL default '0',
     k_attene int(11) NOT NULL default '0',
     k_attcred int(11) NOT NULL default '0',
     k_attbev int(11) NOT NULL default '0',
     k_atteis int(11) NOT NULL default '0',
     k_attwas int(11) NOT NULL default '0',
     k_defeisen int(11) NOT NULL default '0',
     k_defstahl int(11) NOT NULL default '0',
     k_defvv4a int(11) NOT NULL default '0',
     k_defchem int(11) NOT NULL default '0',
     k_defene int(11) NOT NULL default '0',
     k_defcred int(11) NOT NULL default '0',
     k_defbev int(11) NOT NULL default '0',
     k_defeis int(11) NOT NULL default '0',
     k_defwas int(11) NOT NULL default '0',
     k_plueisen int(11) NOT NULL default '0',
     k_plustahl int(11) NOT NULL default '0',
     k_pluvv4a int(11) NOT NULL default '0',
     k_pluchem int(11) NOT NULL default '0',
     k_plueis int(11) NOT NULL default '0',
     k_pluwas int(11) NOT NULL default '0',
     k_pluene int(11) NOT NULL default '0',
     k_bombev int(11) NOT NULL default '0',
     k_rauch  int(3) NOT NULL default '0',
     k_msg varchar(254) NOT NULL default '',
     k_forum CHAR(1) NOT NULL DEFAULT 'N',
     PRIMARY KEY (k_id),
     UNIQUE KEY k_kb (k_kb,w_id),
     KEY k_time (k_time)
    )",
        "CREATE TABLE IF NOT EXISTS " . $db_prefix . "kb_kaputt (
     w_id int(11) NOT NULL default '0',
     k_id int(11) NOT NULL default '0',
     v_typ char(1) NOT NULL default '',
     v_ally varchar(30) NOT NULL default '',
     v_name varchar(50) NOT NULL default '',
     v_art char(1) NOT NULL default '',
     v_bez varchar(100) NOT NULL default '',
     v_anzs int(11) NOT NULL default '0',
     v_anze int(11) NOT NULL default '0',
     v_klasse smallint(1) NOT NULL default '0',
     KEY w_id (w_id,k_id,v_ally,v_name,v_art)
    )",
        "ALTER TABLE " . $db_tb_scans . " " .
            "ADD `time_att` int(10) unsigned DEFAULT '0'," .
            "ADD `att` varchar(10000) NOT NULL DEFAULT ''",
    );
    foreach ($sqlscript as $sql) {
        $db->db_query($sql);
    }
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
    // Weitere Wiederholungen für weitere Menue-Einträge, z.B.
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
    return "DEFINE('SCAN_DETAILS_ANZ', 5);";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase()
{
    global $db, $db_tb_scans, $db_tb_kb_war, $db_tb_kb_kb, $db_tb_kb_kaputt;

    $sqlscript = array(
        "ALTER TABLE " . $db_tb_scans . " " .
            "DROP `time_att`," .
            "DROP `att` ",
        "DROP TABLE " . $db_tb_kb_war,
        "DROP TABLE " . $db_tb_kb_kb,
        "DROP TABLE " . $db_tb_kb_kaputt
    );

    foreach ($sqlscript as $sql) {
        $db->db_query($sql);
    }
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

    require_once './includes/menu_fn.php';

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr 
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul
?>
    <script type="text/javascript">
        function Collapse(what) {
            var collapseImage = document.getElementById("collapse_" + what);

            if (!document.getElementById("row_" + what)) return;
            var collapseRow = document.getElementById("row_" + what);

            if (!collapseImage)
                return;

            if (collapseRow.style.display == "") {
                collapseRow.style.display = "none";
                collapseImage.src = "bilder/plus.gif";
            } else {
                collapseRow.style.display = "";
                collapseImage.src = "bilder/minus.gif";
            }
        }
    </script>
<?php

global $db, $db_tb_kb_kb, $db_tb_kb_war, $db_tb_kb_kaputt;

echo "<link href='./css/m_kb.css' rel='stylesheet' type='text/css'>";

$BB           = false;
$aBB          = Array();
$aBB['gray']  = 'gray';
$aBB['red']   = '#990000';
$aBB['green'] = '#009900';

doc_title($modultitle);

$Ncoords         = getvar('Ncoords');
$KBD             = getvar('KBD');
$KBEM            = getvar('KBEM');
$KBF             = getvar('KBF');
$k_opfer         = getvar('k_opfer');
$a_id            = getvar('a_id');
$k_id            = getvar('k_id');
$k_kb            = getvar('k_kb');
$w_freund        = getvar('w_freund');
$w_feind         = getvar('w_feind');
$KBP             = getvar('KBP');
$A_War           = (getvar('A_War') == 'Auswahl' ? true : false);
$Edit_War        = (getvar('Edit_War') == 'Ändern' ? true : false);
$New_War         = (getvar('New_War') == 'Neuanlage' ? true : false);
$Delete_War      = (getvar('Delete_War') == 'Löschen' ? true : false);
$Recalculate_War = (getvar('Recalculate_War') == 'Neuberechnung' ? true : false);
$NI_War          = (getvar('NI_War') == 'Speichern' ? true : false);
$DN_War          = (getvar('DN_War') == 'NEIN' ? true : false);
$DJ_War          = (getvar('DJ_War') == 'ja ganz sicher' ? true : false);
$K_War           = (getvar('K_War') == 'KB einlesen' ? true : false);
$K_Long          = ((getvar('K_Long') == 'KB Komplett' || getvar('K_Long') == 'J') ? true : false);
$K_Short         = ((getvar('K_Short') == 'KB Übersicht' || getvar('K_Short') == 'J') ? true : false);
$K_Ver           = (getvar('K_Ver') == 'Verluste' ? true : false);
$K_Ver1          = (getvar('K_Ver1') == 'Ress/Atts/Deff' ? true : false);
$K_Ver2          = (getvar('K_Ver2') == 'Gebäude' ? true : false);
$K_Ver3          = (getvar('K_Ver3') == 'Schiffe' ? true : false);
$K_VerBB         = (getvar('K_VerBB') == 'Verluste BB-Code' ? true : false);
//$FC_Stat = (getvar('FC_Stat')  == 'FC Statistik' ? true : false);
$FC_StatS = (getvar('FC_StatS') == 'Summen' ? true : false);
$FC_Stat  = (getvar('FC_Stat') == 'EinzelKB' ? true : false);
$sKB      = (getvar('sKB') == 'KB' ? true : false);
$sCoord   = (getvar('sCoord') == 'zeigen' ? true : false);
$fake     = getvar('fake');

$atime       = '';
$wmsg        = '';
$hxml        = '';
$hatt        = '';
$hattSp      = '';
$rowS        = array();
$node_name   = array();
$kb_erfassen = true;
$kb_array    = array();
$anz_att     = 0;
$hopfer      = '';
init_kb_array();

//War Neuanlage/Änderung
if ($NI_War) {
    if (empty($w_freund) && empty($w_feind)) {
        $New_War = true;
        $wmsg  = "Bitte Freund/Feind eingeben!";
    } else {
        $h_freund = strtoupper(ltrim($w_freund));
        $h_feind  = strtoupper(ltrim($w_feind));
        if ($a_id == 0) {

            $SQLdata = array (
                'w_freund' => $h_freund,
                'w_feind' => $h_feind
            );
            $db->db_insert($db_tb_kb_war, $SQLdata);

            $a_id = $db->db_insert_id();

        } else {

            $SQLdata = array (
                'w_freund' => $h_freund,
                'w_feind' => $h_feind
            );
            $db->db_update($db_tb_kb_war, $SQLdata, "WHERE w_id = $a_id");

        }
    }
}

//Löschen Krieg  incl. Kampfberichte!
if ($DJ_War && $a_id > 0) {
    $sSQL = "DELETE FROM `" . $db_tb_kb_war . "` WHERE w_id = $a_id";
    $result = $db->db_query($sSQL);

    $sSQL = "DELETE FROM `" . $db_tb_kb_kb . "` WHERE w_id = $a_id";
    $result = $db->db_query($sSQL);

    $sSQL = "DELETE FROM `" . $db_tb_kb_kaputt . "` WHERE w_id = $a_id";
    $result = $db->db_query($sSQL);

    $a_id = 0;
}

//War-Auswahl einlesen
$sql = "SELECT * from `" . $db_tb_kb_war . "` order by w_id desc";
$select = $db->db_query($sql);

echo "<p><form method='POST' action='index.php?action=" . $modulname . "' enctype='multipart/form-data'>\n";
echo "<input type='hidden' name='action' value='m_kb'>";
echo "<table cellspacing='1' cellpadding='2' border='0'>";
echo "<tr><td nowrap><select style='width:575px;' name='a_id'>";
while ($rowW = $db->db_fetch_array($select)) {
    $w_freund = $rowW['w_freund'];
    $w_feind  = $rowW['w_feind'];
    $selected = '';
    if ($rowW['w_id'] == $a_id || $a_id == 0) {
        $selected = 'selected';
        $a_id     = $rowW['w_id'];
    }
    echo "<option $selected value='" . $rowW['w_id'] . "'>$w_freund <-> $w_feind</option>";
}
$db->db_free_result($select);
echo "</select></td></tr>";
echo "<td><input type='submit' Name='A_War' value='Auswahl'>&nbsp;";
if ($user_status == "admin") {
    echo "    <input type='submit' Name='Edit_War' value='Ändern'>&nbsp;";
    echo "    <input type='submit' Name='New_War' value='Neuanlage'>&nbsp;";
    echo "    <input type='submit' Name='Delete_War' value='Löschen'>&nbsp;";
    echo "    <input type='submit' Name='Recalculate_War' value='Neuberechnung'></td>";
}
echo "</tr>";
echo "</table>";
echo "</form></p>";

//Kriegname Neu/Ändern
if ($New_War || $Edit_War) {
    if ($New_War) {
        $a_id     = 0;
        $w_freund = '';
        $w_feind  = '';
    } else {
        $sql = "SELECT `w_freund`, `w_feind` FROM `{$db_tb_kb_war}` WHERE w_id = $a_id";
        $select = $db->db_query($sql);
        $rowW     = $db->db_fetch_array($select);
        $w_freund = $rowW['w_freund'];
        $w_feind  = $rowW['w_feind'];
    }
    echo "<p><form method='POST' action='index.php?action=" . $modulname . "' enctype='multipart/form-data'>\n";
    echo "<b>Allianztags mit ; trennen. Beispiel: Allianz A;Allianz B<b><br>";
    echo "<input type='hidden' name='action' value='m_kb'>";
    echo "<input name='a_id' value='$a_id' type='hidden'>";
    echo "<table cellspacing='1' cellpadding='2' border='0'>";
    echo "<tr><td class='wfreund'>Freund:</td><td class='wfreund'><input name='w_freund' value='$w_freund' type='text' size='100' maxlength='254'></td></tr>";
    echo "    <td class='wfeind'>Feind:</td><td class='wfeind'><input name='w_feind' value='$w_feind' type='text' size='100' maxlength='254'></td></tr>";
    echo "<tr><td colspan=2><input type='submit' name='NI_War' value='Speichern'></td></tr>";
    echo "</table>";
    echo "</form></p>";
    $kb_erfassen = false;
} else if ($KBD == 'J') {
    //EinzelKB Löschen
    delete_kb($a_id, $k_id);
} else if (isset($KBEM) && ($KBEM == 'J' || $KBEM == 'N')) {
    //Einmaurer Toggle
    $k_opfer = rawurldecode($k_opfer);
//  $k_opfer = htmlentities($k_opfer);

    if ($KBEM == 'J') {
        $SQLdata = array ('k_mauer' => 'X');
    } else {
        $SQLdata = array ('k_mauer' => ' ');
    }
    $db->db_update($db_tb_kb_kb, $SQLdata, "WHERE k_opfer = '$k_opfer' ");

} else if (isset($KBF) && ($KBF == 'J' || $KBF == 'N')) {
//Fake-Angriff Toggle
    if ($KBF == 'J') {
        $SQLdata = array ('k_art' => 'F');
    } else {
        $SQLdata = array ('k_art' => '');
    }
    $db->db_update($db_tb_kb_kb, $SQLdata, "WHERE w_id = $a_id AND k_id = $k_id ");
}
//Abfrage löschen Krieg  incl. Kampfberichte!
if ($Delete_War && $a_id > 0) {
    echo "<p><form method='POST' action='index.php?action=" . $modulname . "' enctype='multipart/form-data'>\n";
    echo "<b>Ehrlich echt den kompletten Krieg incl. aller KB löschen???<b><br>";
    echo "<input type='hidden' name='action' value='m_kb'>";
    echo "<table cellspacing='1' cellpadding='2' border='0'>";
    echo "<tr><td colspan=2><input type='submit' tabindex='1' Name='DN_War' value='NEIN'></td>";
    echo "    <td colspan=2><input type='submit' tabindex='2' Name='DJ_War' value='ja ganz sicher'></td></tr>";
    echo "</table>";
    echo "<input name='a_id' value=$a_id type='hidden'>";
    echo "</form></p><br><br>";
    $kb_erfassen = false;
}
if (!empty($wmsg)) {
    echo $wmsg;
}

// War-Auswahl wurde getroffen
if ($a_id > 0 && $kb_erfassen) {
    $w_freund = '';
    $w_feind  = '';
    $sql      = "SELECT * from `" . $db_tb_kb_war . "` where w_id = $a_id";
    $select = $db->db_query($sql);
    $rowW     = $db->db_fetch_array($select);
    $w_freund = $rowW["w_freund"];
    $w_feind  = $rowW["w_feind"];
    $a_freund = explode(';', $w_freund);
    $a_feind  = explode(';', $w_feind);

// KB einlesen  / alle anzeigen 
    $link_w_feind = "<a class='links' href='index.php?action=m_ziele";
    $link_w_feind .= "&gal_start=1&gal_end=20";
    $link_w_feind .= "&sys_start=1&sys_end=400";
    $link_w_feind .= "&alli=$w_feind";
    $link_w_feind .= "'>$w_feind</a>";

    echo "<span class='wfreund center'>&nbsp;&nbsp;$w_freund&nbsp;&nbsp;</span> <-> <span class='wfeind'>&nbsp;&nbsp;$link_w_feind&nbsp;&nbsp;</span>";
    echo "<p><form method='POST' action='index.php?action=" . $modulname . "' enctype='multipart/form-data'>\n";
    echo "<input type='hidden' name='action' value='m_kb'>";
    echo "<table cellspacing='1' cellpadding='2' border='0'>";
    echo "<tr><td><b>je Zeile 1 KB-Link, oder mit Ctrl-A, Ctrl-V mehrere komplette KB's</b></td></tr>";
    echo "<tr><td><textarea name='k_kb' rows='5' style='width:100%;'></textarea></td></tr>";
    echo "<tr><td><input type='submit' Name='K_War'   value='KB einlesen'>&nbsp;";
    echo "        <input type='submit' Name='K_Short' value='KB Übersicht'>&nbsp;";
    echo "        <input type='submit' Name='K_Long'  value='KB Komplett'>&nbsp;";
    echo "        <b>FC-Statisik:</b> ";
//  echo "        <input type='submit' Name='FC_Stat' value='FC Statistik'>&nbsp;";
    echo "        <input type='submit' Name='FC_StatS' value='Summen'>&nbsp;";
    echo "        <input type='submit' Name='FC_Stat' value='EinzelKB'>&nbsp;";
    echo "        <b>Verluste:</b> ";
    echo "        <input type='submit' Name='K_Ver1'  value='Ress/Atts/Deff'>&nbsp;";
    echo "        <input type='submit' Name='K_Ver2'  value='Gebäude'>&nbsp;";
    echo "        <input type='submit' Name='K_Ver3'  value='Schiffe'>&nbsp;";
//  echo "        <input type='submit' Name='K_Ver'   value='Verluste'>&nbsp;";
    if ($user_status == "admin") {
        echo "      <input type='submit' Name='K_VerBB' value='Verluste BB-Code'>&nbsp;";
    }
    echo "</td></tr>";
    echo "</table>";
    echo "<input name='a_id' value=$a_id type='hidden'>";
    echo "</form></p>";
}
if ($Recalculate_War) { //Neuberechnung
    $sql = "SELECT * FROM `$db_tb_kb_kb` ";
    $sql .= "WHERE w_id = $a_id ";
    $sql .= "ORDER BY k_time desc";
    echo $sql . '<br><br>';
    $select = $db->db_query($sql);
//  while ($row = $db->db_fetch_array($select)) {
// http://www.icewars.de/portal/kb/de/kb.php?id=621254&md_hash=89b20849a70af19071ad0c9532fe7db4
// http://www.icewars.de/portal/kb/de/kb.php?id=621253&md_hash=5a9c7bc001142a06899b4ef6018cb7a1
// http://www.icewars.de/portal/kb/de/kb.php?id=621252&md_hash=2b3ad8538fb34c7dc6d976cb8c30a9f5
    while ($row = $db->db_fetch_array($select)) {
        ;
        $k_id = $row['k_id'];
        $k_kb = $row['k_kb'];
//   echo $k_kb . '<br>';
        $sSQL = "DELETE FROM `" . $db_tb_kb_kaputt . "` WHERE w_id = $a_id AND k_id = $k_id";
//   echo $sSQL . '<br>';
        $result = $db->db_query($sSQL);
        kb_parsen($row['k_kb']);
//  echo $hxml;
    }
    echo '<br><br>Neuberechnung Ende <br><br>';
    $db->db_free_result($select);
}
if ($K_War && $a_id > 0) {
    kb_einlesen($k_kb);
//  echo $hxml;
}
// Verlustaufstellung für Gesamtkrieg
if ($a_id > 0 && $K_Ver) {
    $BB = false;
    verluste_anzeigen();
}
if ($a_id > 0 && $K_Ver1) {
    $BB = false;
    verluste_anzeigen_R();
    verluste_anzeigen_A();
    verluste_anzeigen_D();
}
if ($a_id > 0 && $K_Ver2) {
    $BB = false;
    verluste_anzeigen_G();
}
if ($a_id > 0 && $K_Ver3) {
    $BB = false;
    verluste_anzeigen_S();
}
// Verlustaufstellung für Gesamtkrieg als BB-Code für Forenpost
if ($a_id > 0 && $K_VerBB) {
    $BB = true;
    verluste_anzeigen();
}
// FC-Statistiken
if ($a_id > 0 && ($FC_Stat || $FC_StatS)) {
    echo "<span class='wfreund center'>&nbsp;&nbsp;$w_freund&nbsp;&nbsp;</span> <-> <span class='wfeind'>&nbsp;&nbsp;$link_w_feind&nbsp;&nbsp;</span>";
    echo "<p><table cellspacing='1' cellpadding='2' border='0' class='bordercolor' style='width:950px'>";
    $FCanz = 0;
    angriffe_jeFC('F'); // FC-Statistiken, Freunde
    angriffe_jeFC('E'); // FC-Statistiken, Feinde
    echo "</table></p>";
}
// KB-Parsen
if ($a_id > 0 && ($sKB)) {
//  for ($i = 0; $i < count($KBP); $i++) {
    $i = count($KBP) - 1;
    while ($i >= 0) {
        kb_parser($KBP[$i]);
        $i--;
    }
    echo "<br>";
    if (is_numeric($KBP[0])) {
        $K_Long  = false;
        $K_Short = false;
    } else {
        echo "<span class='doc_red'><b>ohne KB-Auswahl keine Aufbereitung :P</b></span><br><br>";
    }
}
// KB-Übersicht je Krieg
if ($a_id > 0 && ($K_Long || $K_Short)) {
    $KBP      = Array();
    $k_zeigen = '';
    if ($K_Long) {
        $k_zeigen = '&K_Long=J';
    }
    if ($K_Short) {
        $k_zeigen = '&K_Short=J';
    }

    $and_Ncoords = '';
    $abDatum     = " AND k_time >= ";
    $abDatum .= time() - 3 * $DAYS;
    if (!empty($Ncoords)) {
        $and_Ncoords = " AND k_ort = '$Ncoords' ";
        $abDatum     = '';
    }
    $sql = "SELECT * from `" . $db_tb_kb_kb . "` WHERE w_id = $a_id AND k_typ <> 'X' $and_Ncoords ";
    $sql .= "$abDatum order by k_time desc";

    $select = $db->db_query($sql);

    echo "<span class='wfreund center'>&nbsp;&nbsp;$w_freund&nbsp;&nbsp;</span> <-> <span class='wfeind'>&nbsp;&nbsp;$link_w_feind&nbsp;&nbsp;</span>";

    if (empty($Ncoords)) {
        echo "<div>Es werden nur die KB der letzten 3 Tage angezeigt!</div>";
    }
    echo "<p><form method='POST' action='index.php?action=" . $modulname . "&amp;a_id=$a_id" . $k_zeigen . "' enctype='multipart/form-data'>\n";
    echo "<input name='a_id' value=$a_id type='hidden'>";
//  if (!empty($Ncoords)) {
//    echo "<input name='Ncoords' value=$Ncoords type='hidden'>";
//      $fake = 'on';
//  }
    echo "alternativ alle Angriffe auf folgende Koordinaten <input type='text' name='Ncoords' value='$Ncoords' style='width: 50' size='10'> <input type='submit' name='sCoord' value='zeigen' style='CURSOR: hand;'>";

    echo "<table cellspacing='1' cellpadding='2' border='0' class='bordercolor' style='table-layout:fixed; overflow:hidden;'>";
    ausgabe_ueber($K_Long, 'E', 'J');

    $z = 0;
    while ($rowKB = $db->db_fetch_array($select)) {
        if ($fake != 'off' || $rowKB['k_art'] != 'F') {
            if ($rowKB['k_mauer'] == 'X') {
                $mclass  = 'maurer';
                $mtoggle = 'N';
            } else {
                $mclass  = '';
                $mtoggle = 'J';
            }
            if ($rowKB['k_sieg'] == '1') {
                $sclass = 'doc_green';
            } else {
                $sclass = 'doc_red';
            }
            $ftoggle = '';
            if ($rowKB['k_art'] == 'F') {
                $ftoggle = 'N';
            }
            if ($rowKB['k_art'] == '') {
                $ftoggle = 'J';
            }
            $hclass = '';
            ausgabe_zeile($rowKB, 'E', '', $K_Long, $rowKB['k_typ'], $ftoggle, $Ncoords);
        }
    }
    $db->db_free_result($select);

    // Summenzeilen aufbereiten
    if ($K_Long) {
        $sqlf = "  sum(k_atteisen) AS k_atteisen ";
        $sqlf .= ", sum(k_attstahl) as k_attstahl ";
        $sqlf .= ", sum(k_attvv4a)  as k_attvv4a  ";
        $sqlf .= ", sum(k_attchem)  as k_attchem  ";
        $sqlf .= ", sum(k_attene)   as k_attene   ";
        $sqlf .= ", sum(k_attcred)  as k_attcred  ";
        $sqlf .= ", sum(k_attbev)   as k_attbev   ";
        $sqlf .= ", sum(k_atteis)   as k_atteis   ";
        $sqlf .= ", sum(k_attwas)   as k_attwas   ";
        $sqlf .= ", sum(k_defeisen) as k_defeisen ";
        $sqlf .= ", sum(k_defstahl) as k_defstahl ";
        $sqlf .= ", sum(k_defvv4a)  as k_defvv4a  ";
        $sqlf .= ", sum(k_defchem)  as k_defchem  ";
        $sqlf .= ", sum(k_defene)   as k_defene   ";
        $sqlf .= ", sum(k_defcred)  as k_defcred  ";
        $sqlf .= ", sum(k_defbev)   as k_defbev   ";
        $sqlf .= ", sum(k_defeis)   as k_defeis   ";
        $sqlf .= ", sum(k_defwas)   as k_defwas   ";
        $sqlf .= ", sum(k_plueisen) as k_plueisen ";
        $sqlf .= ", sum(k_plustahl) as k_plustahl ";
        $sqlf .= ", sum(k_pluvv4a)  as k_pluvv4a  ";
        $sqlf .= ", sum(k_pluchem)  as k_pluchem  ";
        $sqlf .= ", sum(k_plueis)   as k_plueis   ";
        $sqlf .= ", sum(k_pluwas)   as k_pluwas   ";
        $sqlf .= ", sum(k_pluene)   as k_pluene   ";
        $sqlf .= ", sum(k_bombev)   as k_bombev   ";

        $sclass = '';
        ausgabe_ueber($K_Long, 'S', 'J');
        // Angriffe AUF Feinde  

        $sql = "SELECT k_ally, k_attally, ";
        $sql .= $sqlf;
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id AND k_typ = 'E' ";
        $sql .= "GROUP BY k_ally, k_attally ";
        $sql .= "ORDER BY k_ally, k_attally ";
        $select = $db->db_query($sql);
        while ($rowKB = $db->db_fetch_array($select)) {
            ausgabe_zeile($rowKB, 'SA', 'Freund-Summen je Verteidiger/Angreifer', $K_Long, 'E', '');
        }
        $db->db_free_result($select);

        // Angriffe AUF Feinde  Summe je Attally
        $sql = "SELECT k_attally, ";
        $sql .= $sqlf;
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id AND k_typ = 'E' ";
        $sql .= "GROUP BY k_attally ";
        $sql .= "ORDER BY k_attally ";
        $select = $db->db_query($sql);
        while ($rowKB = $db->db_fetch_array($select)) {
            ausgabe_zeile($rowKB, 'SJ', 'Freund-Summen je Angreifer', $K_Long, 'E', '');
        }
        $db->db_free_result($select);

        // Angriffe AUF Feinde  Gesamtsumme
        $sql = "SELECT ";
        $sql .= $sqlf;
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id AND k_typ = 'E' ";
        $select = $db->db_query($sql);
        while ($rowKB = $db->db_fetch_array($select)) {
            ausgabe_zeile($rowKB, 'SG', 'Freund-Gesamt-Summen', $K_Long, 'E', '');
        }
        $db->db_free_result($select);
        // Angriffe VOM Feinde
        $hclass = 'wfeind';
        $sql    = "SELECT k_ally, k_attally, ";
        $sql .= $sqlf;
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id AND k_typ = 'F' ";
        $sql .= "GROUP BY k_ally, k_attally ";
        $sql .= "ORDER BY k_ally, k_attally ";
        $select = $db->db_query($sql);
        while ($rowKB = $db->db_fetch_array($select)) {
            ausgabe_zeile($rowKB, 'SA', 'Feind-Summen je Verteidiger/Angreifer', $K_Long, 'F', '');
        }
        $db->db_free_result($select);
        // Angriffe VOM Feinde  Summe je Attally
        $sql = "SELECT k_attally, ";
        $sql .= $sqlf;
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id AND k_typ = 'F' ";
        $sql .= "GROUP BY k_attally ";
        $sql .= "ORDER BY k_attally ";
        $select = $db->db_query($sql);
        while ($rowKB = $db->db_fetch_array($select)) {
            ausgabe_zeile($rowKB, 'SJ', 'Feind-Summen je Angreifer', $K_Long, 'F', '');
        }
        $db->db_free_result($select);
        // Angriffe VOM Feinde  Gesamtsumme
        $sql = "SELECT ";
        $sql .= $sqlf;
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id AND k_typ = 'F' ";
        $select = $db->db_query($sql);
        while ($rowKB = $db->db_fetch_array($select)) {
            ausgabe_zeile($rowKB, 'SG', 'Feind-Gesamt-Summen', $K_Long, 'F', '');
        }
        $db->db_free_result($select);
    }
    echo "</table>";
    echo "</form></p>";
}
$db->db_query("COMMIT");

function kb_einlesen($text)
{
    if (!empty($text)) {
        $text = str_replace(" \t", " ", $text);
        $text = str_replace("\t", " ", $text);
        $text = str_replace("%", "\\%", $text);
        $text = str_replace("\r", "\n ", $text);
        $text = str_replace("\n \n", "\n", $text);
        $text = str_replace("&amp;", "&", $text);

//    $text .= "\n http://www.icewars.de/portal/kb/de/kb.php?id=556353&md_hash=fe94140bf64145a3f3da23cafb5e27ea \n";

        $k_kb = explode("\n", $text);
//    if ($k_kb[0] != $k_kb[1]) echo "ungleich! " . strlen($k_kb[0]) . " / " . strlen($k_kb[1]) . " <br>";
//    for ($i = 0; $i < strlen($k_kb[0]); $i++) {
//      echo $i . ' ' . substr($k_kb[0],$i,1) . ' ' . ord(substr($k_kb[0],$i,1)) . ' <-> ';
//      echo            substr($k_kb[1],$i,1) . ' ' . ord(substr($k_kb[1],$i,1)) . '<br>';
//    }
        foreach ($k_kb as $kb) {
            if (!(StrPos($kb, 'http://www.icewars.de/portal/kb/de/kb.php') === false)) {
                $file = trim($kb);
                kb_parsen($file);
            }
        }
    }
}

function kb_parsen($file)
{
    global $db;
    global $hxml;
    global $kb_array;
    global $a_id;
    global $k_id;
    global $k_kb;
    global $db_tb_kb_kb;
    global $db_tb_kb_kaputt;
    global $anz_att;
    global $hopfer;

    $anz_att = 0;
    $hopfer  = 0;
    $k_kb    = $file;
    $file .= '&typ=xml';
    init_kb_array();

    if (!(list($xml_parser, $fp) = new_xml_parser($file))) {
        die("could not open XML input");
    }
    $hxml = '<p><pre>';
    while ($data = fread($fp, 4096)) {
        if (!xml_parse($xml_parser, $data, feof($fp))) {
            die(sprintf(
                "XML error: %s at line %d\n",
                xml_error_string(xml_get_error_code($xml_parser)),
                xml_get_current_line_number($xml_parser)
            ));
        }
    }
    $hxml .= '</pre></p>';
    xml_parser_free($xml_parser);

    if (empty($kb_array['ATTALLY']) || empty($kb_array['TYP'])) {
        $kb_array['ERR'] = true;
        echo "<a href='$k_kb' target='_new'>$k_kb</a> <span class='doc_red'>abgelehnt, KB gehört nicht zu diesem Krieg!</span><br>";
        delete_kb($a_id, $k_id);
    }
    if (!$kb_array['ERR']) {
        $kb_array['MAUER'] = '';

        $sql = "SELECT * from `" . $db_tb_kb_kb . "` ";
        $sql .= "WHERE w_id = $a_id ";
//    $sql .= "  AND k_opfer = '" . htmlentities($kb_array['OPFER']) . "' ";
        $sql .= "  AND k_opfer = '" . $kb_array['OPFER'] . "' ";
        $sql .= "  AND k_mauer = 'X' ";
        $select = $db->db_query($sql);
        $rowM = $db->db_fetch_array($select);
        if ($db->db_errno() == 0) {
            if ($rowM['k_mauer'] == 'X') {
                $kb_array['MAUER'] = 'X';
            }
        } else {
            $kb_array['ERR'] = true;
            sql_fehler($sql);
            delete_kb($a_id, $k_id);
        }
    }
    if (!$kb_array['ERR']) {
        if ($anz_att <= 3 && $kb_array['ART'] == '') {
            $kb_array['ART'] = 'F';
        }
//    echo "ally: " . $kb_array['ALLY'] . "- typ: " . $kb_array['TYP'] . " " . $kb_array['OPFER'] . "<br>";

        $sSQL = "UPDATE `" . $db_tb_kb_kb . "` SET ";
        $sSQL .= " k_typ      = '" . $kb_array['TYP'] . "'";
        $sSQL .= ",k_art      = '" . $kb_array['ART'] . "'";
        $sSQL .= ",k_kb       = '" . $k_kb . "'";
        $sSQL .= ",k_time     = " . $kb_array['TIME'];
        //  $sSQL .= ",k_opfer    = '" . htmlentities($kb_array['OPFER']) . "'";
        $sSQL .= ",k_opfer    = '" . $kb_array['OPFER'] . "'";
        //  $sSQL .= ",k_ally     = '" . htmlentities($kb_array['ALLY']) . "'";
        $sSQL .= ",k_ally     = '" . $kb_array['ALLY'] . "'";
        $sSQL .= ",k_ort      = '" . $kb_array['ORT'] . "'";
        //  $sSQL .= ",k_attally  = '" . htmlentities($kb_array['ATTALLY']) . "'";
        $sSQL .= ",k_attally  = '" . $kb_array['ATTALLY'] . "'";
        //  $sSQL .= ",k_attspiel = '" . htmlentities($kb_array['ATTSPIEL']) . "'";
        $sSQL .= ",k_attspiel = '" . $kb_array['ATTSPIEL'] . "'";
        $sSQL .= ",k_sieg     = '" . $kb_array['SIEG'] . "'";
        $sSQL .= ",k_mauer    = '" . $kb_array['MAUER'] . "'";
        $sSQL .= ",k_atteisen = " . $kb_array['ATTEISEN'];
        $sSQL .= ",k_attstahl = " . $kb_array['ATTSTAHL'];
        $sSQL .= ",k_attvv4a  = " . $kb_array['ATTVV4A'];
        $sSQL .= ",k_attchem  = " . $kb_array['ATTCHEM'];
        $sSQL .= ",k_attene   = " . $kb_array['ATTENE'];
        $sSQL .= ",k_attcred  = " . $kb_array['ATTCRED'];
        $sSQL .= ",k_attbev   = " . $kb_array['ATTBEV'];
        $sSQL .= ",k_atteis   = " . $kb_array['ATTEIS'];
        $sSQL .= ",k_attwas   = " . $kb_array['ATTWAS'];
        $sSQL .= ",k_defeisen = " . $kb_array['DEFEISEN'];
        $sSQL .= ",k_defstahl = " . $kb_array['DEFSTAHL'];
        $sSQL .= ",k_defvv4a  = " . $kb_array['DEFVV4A'];
        $sSQL .= ",k_defchem  = " . $kb_array['DEFCHEM'];
        $sSQL .= ",k_defene   = " . $kb_array['DEFENE'];
        $sSQL .= ",k_defcred  = " . $kb_array['DEFCRED'];
        $sSQL .= ",k_defbev   = " . $kb_array['DEFBEV'];
        $sSQL .= ",k_defeis   = " . $kb_array['DEFEIS'];
        $sSQL .= ",k_defwas   = " . $kb_array['DEFWAS'];
        $sSQL .= ",k_plueisen = " . $kb_array['PLUEISEN'];
        $sSQL .= ",k_plustahl = " . $kb_array['PLUSTAHL'];
        $sSQL .= ",k_pluvv4a  = " . $kb_array['PLUVV4A'];
        $sSQL .= ",k_pluchem  = " . $kb_array['PLUCHEM'];
        $sSQL .= ",k_plueis   = " . $kb_array['PLUEIS'];
        $sSQL .= ",k_pluwas   = " . $kb_array['PLUWAS'];
        $sSQL .= ",k_pluene   = " . $kb_array['PLUENE'];
        $sSQL .= ",k_bombev   = " . $kb_array['BOMBEV'];
        $sSQL .= ",k_rauch    = " . $kb_array['RAUCH'];
        $sSQL .= ",k_msg      = '" . htmlentities($kb_array['MSG'], ENT_QUOTES) . "'";
        $sSQL .= " WHERE w_id = $a_id AND k_id = $k_id";
        $db->db_query($sSQL);

        $sSQL = "UPDATE `" . $db_tb_kb_kaputt . "` SET ";
        $sSQL .= " v_typ      = '" . $kb_array['TYP'] . "'";
        $sSQL .= " WHERE w_id = $a_id AND k_id = $k_id AND v_art = 'G'";
        $db->db_query($sSQL);

        echo "<a href='$k_kb' target='_new'>$k_kb</a> <span class='doc_green'>KB erfolgreich eingelesen</span><br>";
        fill_planatt();
        update_planatt();

    } else {
        delete_kb($a_id, $k_id);
    }
}

function startElement($parser, $name, $attribs)
{
    global $hxml;
    global $kb_array;
    global $node_name;
    global $anz_att;
    global $hopfer;

// echo "start: $name <br>";

    if ($name == 'KAMPF') {
        insert_iw_kb();
    }
// Aussenknoten merken 
    if ($name == 'PLA_DEF' || $name == 'FLOTTEN_ATT'
        || $name == 'PLUENDERUNG'
        || $name == 'PLANI_DATA'
        || $name == 'KAMPF_TYP'
        || $name == 'RESVERLUSTE'
        || $name == 'RESULTAT'
        || $name == 'TIMESTAMP'
        || $name == 'FLOTTEN_DEF'
        || $name == 'BOMBEN'
    ) {
        $node_name[0] = $name;
    }
    if ($node_name[0] == 'FLOTTEN_ATT' || $node_name[0] == 'FLOTTEN_DEF') {
        if ($name == 'USER' || $name == 'SCHIFFTYP' || $name == 'BLOEDSINN') {
            $node_name[1] = $name;
        }
    }
    if ($node_name[0] == 'RESVERLUSTE') {
        if ($name == 'ATT' || $name == 'DEF') {
            $node_name[1] = $name;
        }
    }
    if ($node_name[0] == 'PLA_DEF') {
        if ($name == 'USER' || $name == 'DEFENCETYP' || $name == 'SCHIFFTYP') {
            $node_name[1] = $name;
        }
    }
    if ($node_name[0] == 'BOMBEN') {
        if ($name == 'GEB') {
            $node_name[1] = $name;
        }
    }
    $hxml .= "&lt;<span style='color:#0000cc;'>$name</span>";
    if (count($attribs)) {
        foreach ($attribs as $k => $v) {
            $hxml .= " <span style='color:#009900;'>$k</span>='<span style='color:#990000;'>$v</span>'";
            if ($name == 'TIMESTAMP') {
                $kb_array['TIME'] = $v;
            }

            if ($node_name[0] == 'PLANI_DATA') {
                // Falls keine Planetendeff und nur stationierte Flotte:
                // Nur wenn Planetenbesitzer gleicher Typ wie Verteidiger... falls KB unter stationierter Flotte steht...
                if ($name == 'NAME' && !empty($v)) {
                    $hopfer = $v;
                }
//       echo $kb_array['ART'] . ' / ' . $kb_array['TYP'] . " / $hopfer <br>";
                if ($name == 'ALLIANZ_TAG' && !empty($v)) {
                    if ($kb_array['ART'] != 'S' || ($kb_array['ART'] == 'S' && (empty($kb_array['TYP']) || $kb_array['TYP'] == freund_feind($v)))) {
                        if ($kb_array['ART'] == 'S') {
                            $kb_array['ART'] = '';
                        }
                        if ($kb_array['ART'] == 'D') {
                            $kb_array['ART'] = '';
                        }
                        $kb_array['OPFER'] = $hopfer;
                        $kb_array['ALLY']  = strtoupper($v);
                        $kb_array['TYP']   = freund_feind($v);
                    }
                }
                if ($name == 'STRING') {
                    $kb_array['ORT'] = $v;
                }
            }
            if ($node_name[0] == 'FLOTTEN_ATT') {
                if ($node_name[1] == 'USER') {
                    if ($name == 'ALLIANZ_TAG' && empty($kb_array['ATTALLY'])) {
                        // Angreifer-Ally Freund oder Feind?
                        $h_art = freund_feind($v);
                        if ($h_art == 'F' || $h_art == 'E') {
                            $kb_array['ATTALLY'] = strtoupper($v);
                        }
                    }
                    if ($name == 'NAME') {
                        if (StrPos($kb_array['ATTSPIEL'], $v) === false) {
                            if (!empty($kb_array['ATTSPIEL'])) {
                                $kb_array['ATTSPIEL'] .= ';';
                            }
                            $kb_array['ATTSPIEL'] .= $v;
                        }
                    }
                }
            }
            if ($node_name[0] == 'RESVERLUSTE') {
                if ($node_name[1] == 'ATT') {
                    if ($name == 'ID') {
                        $kb_array['ID'] = $v;
                    }
                    if ($name == 'ANZAHL') {
                        if ($kb_array['ID'] == 1) {
                            $kb_array['ATTEISEN'] = $v;
                        }
                        if ($kb_array['ID'] == 2) {
                            $kb_array['ATTSTAHL'] = $v;
                        }
                        if ($kb_array['ID'] == 3) {
                            $kb_array['ATTVV4A'] = $v;
                        }
                        if ($kb_array['ID'] == 4) {
                            $kb_array['ATTEIS'] = $v;
                        }
                        if ($kb_array['ID'] == 5) {
                            $kb_array['ATTCHEM'] = $v;
                        }
                        if ($kb_array['ID'] == 6) {
                            $kb_array['ATTWAS'] = $v;
                        }
                        if ($kb_array['ID'] == 7) {
                            $kb_array['ATTENE'] = $v;
                        }
                        if ($kb_array['ID'] == 10) {
                            $kb_array['ATTCRED'] = $v;
                        }
                        if ($kb_array['ID'] == 11) {
                            $kb_array['ATTBEV'] = $v;
                        }
                    }
                }
                if ($node_name[1] == 'DEF') {
                    if ($name == 'ID') {
                        $kb_array['ID'] = $v;
                    }
                    if ($name == 'ANZAHL') {
                        if ($kb_array['ID'] == 1) {
                            $kb_array['DEFEISEN'] = $v;
                        }
                        if ($kb_array['ID'] == 2) {
                            $kb_array['DEFSTAHL'] = $v;
                        }
                        if ($kb_array['ID'] == 3) {
                            $kb_array['DEFVV4A'] = $v;
                        }
                        if ($kb_array['ID'] == 4) {
                            $kb_array['DEFEIS'] = $v;
                        }
                        if ($kb_array['ID'] == 5) {
                            $kb_array['DEFCHEM'] = $v;
                        }
                        if ($kb_array['ID'] == 6) {
                            $kb_array['DEFWAS'] = $v;
                        }
                        if ($kb_array['ID'] == 7) {
                            $kb_array['DEFENE'] = $v;
                        }
                        if ($kb_array['ID'] == 10) {
                            $kb_array['DEFCRED'] = $v;
                        }
                        if ($kb_array['ID'] == 11) {
                            $kb_array['DEFBEV'] = $v;
                        }
                    }
                }
            }
            if ($node_name[0] == 'PLUENDERUNG') {
                if ($name == 'ID') {
                    $kb_array['ID'] = $v;
                }
                if ($name == 'ANZAHL') {
                    if ($kb_array['ID'] == 1) {
                        $kb_array['PLUEISEN'] = $v;
                    }
                    if ($kb_array['ID'] == 2) {
                        $kb_array['PLUSTAHL'] = $v;
                    }
                    if ($kb_array['ID'] == 3) {
                        $kb_array['PLUVV4A'] = $v;
                    }
                    if ($kb_array['ID'] == 4) {
                        $kb_array['PLUEIS'] = $v;
                    }
                    if ($kb_array['ID'] == 5) {
                        $kb_array['PLUCHEM'] = $v;
                    }
                    if ($kb_array['ID'] == 6) {
                        $kb_array['PLUWAS'] = $v;
                    }
                    if ($kb_array['ID'] == 7) {
                        $kb_array['PLUENE'] = $v;
                    }
                }
            }
            if ($node_name[0] == 'PLA_DEF') {
                if ($node_name[1] == 'USER') {
                    $kb_array['ART'] = 'D';
                    if ($name == 'NAME') {
                        $kb_array['VNAME'] = $v;
                    }
                    if ($name == 'ALLIANZ_TAG') {
                        $kb_array['VALLY'] = strtoupper($v);
                        $kb_array['VTYP']  = freund_feind($v);
                    }
                }
                if ($node_name[1] == 'SCHIFFTYP') {
                    $kb_array['ART']  = 'D';
                    $kb_array['VART'] = 'D';
                    if ($name == 'NAME') {
                        $kb_array['VBEZ'] = $v;
                    }
                    if ($name == 'KLASSE') {
                        $kb_array['VKLASSE'] = $v;
                    }
                    if ($name == 'ANZAHL_START') {
                        $kb_array['VANZS'] = $v;
                    }
                    if ($name == 'ANZAHL_ENDE') {
                        $kb_array['VANZE'] = $v;
                        insert_iw_kaputt();
                    }
                }
                if ($node_name[1] == 'DEFENCETYP') {
                    $kb_array['ART']  = 'D';
                    $kb_array['VART'] = 'P';
                    if ($name == 'NAME') {
                        $kb_array['VBEZ'] = $v;
                    }
                    if ($name == 'ANZAHL_START') {
                        $kb_array['VANZS'] = $v;
                    }
                    if ($name == 'ANZAHL_ENDE') {
                        $kb_array['VKLASSE'] = 0;
                        $kb_array['VANZE']   = $v;
                        insert_iw_kaputt();
                    }
                }
            }
            if ($node_name[0] == 'BOMBEN') {
//        echo "bomben: (" . $kb_array['ART']. ")<br>";

                if ($kb_array['ART'] != 'P') {
                    $kb_array['ART'] = 'B';
                }
                if ($name == 'BASIS_ZERSTOERT' && $v == 1) {
                    $kb_array['ART'] = 'P';
                }
                if ($name == 'BEV_ZERSTOERT') {
                    $kb_array['BOMBEV'] = $v;
                }
                if ($name == 'BOMBENTREFFERCHANCE') {
                    $kb_array['RAUCH'] = $v;
                }
                if ($node_name[1] == 'GEB') {
                    $kb_array['VART'] = 'G';
                    if ($name == 'NAME') {
                        $kb_array['VBEZ'] = $v;
                    }
                    if ($name == 'ANZAHL') {
                        $kb_array['VANZS']   = $v;
                        $kb_array['VANZE']   = 0;
                        $kb_array['VKLASSE'] = 0;

                        if (!empty($kb_array['OPFER'])) {
                            $kb_array['VNAME'] = $kb_array['OPFER'];
                            $kb_array['VALLY']  = $kb_array['ALLY'];
                        }

                        insert_iw_kaputt();
                    }
                }
            }
            if ($node_name[0] == 'FLOTTEN_ATT') {
                if ($node_name[1] == 'USER') {
                    $kb_array['VART'] = 'A';
                    if ($name == 'NAME') {
                        $kb_array['VNAME'] = $v;
                    }
                    if ($name == 'ALLIANZ_TAG') {
                        $kb_array['VALLY'] = strtoupper($v);
                        $kb_array['VTYP']  = freund_feind($v);
                    }
                }
                if ($node_name[1] == 'SCHIFFTYP') {
                    if ($name == 'NAME') {
                        $kb_array['VBEZ'] = $v;
                    }
//          if ($name == 'NAME')         echo $v . '<br>';
                    if ($name == 'KLASSE') {
                        $kb_array['VKLASSE'] = $v;
                    }
                    if ($name == 'ANZAHL_START') {
                        $kb_array['VANZS'] = $v;
                    }
                    if ($name == 'ANZAHL_ENDE') {
                        $kb_array['VANZE'] = $v;
                        // Gesamtangriff nur 1 Schiff = Fake !
                        if ($kb_array['VANZS'] > 1) {
                            $anz_att += $kb_array['VANZS'];
                        }
                        if ($kb_array['VKLASSE'] == 2) {
                            $anz_att = 99;
                        }
                        insert_iw_kaputt();
                    }
                }
                if ($node_name[1] == 'BLOEDSINN') {
                    if ($name == 'MSG') {
                        $kb_array['MSG'] = $v;
                    }
                }
            }
            if ($node_name[0] == 'FLOTTEN_DEF') {
                if ($node_name[1] == 'USER') {
                    $kb_array['VART'] = 'D';
                    if ($name == 'NAME') {
                        $kb_array['VNAME'] = $v;
                    }
                    if ($name == 'ALLIANZ_TAG') {
                        $kb_array['VALLY'] = strtoupper($v);
                        $kb_array['VTYP']  = freund_feind($v);
                        // Stationierte Flotte, letzter Verteidiger zählt! Nur Freund/Feind!
//            echo "hallo: " . $kb_array['ART'] . " typ: " . $kb_array['VTYP'] . "<br>";
                        if (empty($kb_array['ART']) || empty($kb_array['ALLY']) && ($kb_array['VTYP'] == 'F' || $kb_array['VTYP'] == 'E')) {
                            $kb_array['ART']   = 'S';
                            $kb_array['ALLY']  = $kb_array['VALLY'];
                            $kb_array['TYP']   = $kb_array['VTYP'];
                            $kb_array['OPFER'] = $kb_array['VNAME'];
//                echo "ally: " . $kb_array['ALLY'] . "- typ: " . $kb_array['TYP'] . " " . $kb_array['OPFER' . "<br>";
                        }
                    }
                }
                if ($node_name[1] == 'SCHIFFTYP') {
                    if ($name == 'NAME') {
                        $kb_array['VBEZ'] = $v;
                    }
                    if ($name == 'KLASSE') {
                        $kb_array['VKLASSE'] = $v;
                    }
                    if ($name == 'ANZAHL_START') {
                        $kb_array['VANZS'] = $v;
                    }
                    if ($name == 'ANZAHL_ENDE') {
                        $kb_array['VANZE'] = $v;
//          echo $kb_array['ALLY'] . ' ' . $kb_array['OPFER'] . ' ' . $kb_array['TYP'] . '<br>';
                        insert_iw_kaputt();
                    }
                }
            }
            if ($node_name[0] == 'RESULTAT') {
                if ($name == 'ID') {
                    $kb_array['SIEG'] = $v;
                }
            }
        }
    }
    $hxml .= "&gt;";
}

function endElement($parser, $name)
{
    global $hxml;
    $hxml .= "&lt;/<span style='color:#0000cc;'>$name</span>&gt;";
}

function characterData($parser, $data)
{
    global $hxml;
    $hxml .= "<b>$data</b>";
}

function new_xml_parser($file)
{
    global $node_name;

    $node_name[0] = '';
    $node_name[1] = '';

    $xml_parser = xml_parser_create("UTF-8");
    xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 1);
    xml_set_element_handler($xml_parser, "startElement", "endElement");
    xml_set_character_data_handler($xml_parser, "characterData");

    $fp = @fopen($file, "r");

    if (!($fp)) {
        return false;
    }
    return array($xml_parser, $fp);
}

function freund_feind($v)
{
    global $a_freund;
    global $a_feind;

    $ff = '';
    for ($i = 0; $i < count($a_freund); $i++) {
        if ($a_freund[$i] == strtoupper($v)) {
            $ff = 'F';
        }
    }
    for ($i = 0; $i < count($a_feind); $i++) {
        if ($a_feind[$i] == strtoupper($v)) {
            $ff = 'E';
        }
    }

    return $ff;
}

function delete_kb($a_id, $k_id)
{
    global $db;
    global $db_tb_kb_kb;
    global $db_tb_kb_kaputt;
    global $Recalculate_War;

    if (!$Recalculate_War) {
        if ((is_numeric($a_id) && $a_id > 0) && (is_numeric($k_id) && $k_id > 0)) {
            $sSQL = "DELETE FROM `" . $db_tb_kb_kb . "` WHERE w_id = $a_id AND k_id = $k_id";
            $db->db_query($sSQL);

            $sSQL = "DELETE FROM `" . $db_tb_kb_kaputt . "` WHERE w_id = $a_id AND k_id = $k_id";
            $db->db_query($sSQL);
        }
    }
}

function insert_iw_kb()
{
    global $db;
    global $kb_array;
    global $a_id;
    global $k_id;
    global $k_kb;
    global $db_tb_kb_kb;
    global $Recalculate_War;

    if (!$Recalculate_War) {
        $strSQL   = "SELECT COUNT(*) AS 'count' FROM `" . $db_tb_kb_kb . "` WHERE k_kb='$k_kb';";
        $result = $db->db_query($strSQL);
        $row = $db->db_fetch_array($result);
        if (empty($row['count'])) {

            $aData = array(
                'w_id' => $a_id,
                'k_typ' => 'X',
                'k_kb' => $k_kb
            );
            $db->db_insertignore($db_tb_kb_kb, $aData);

            $k_id = $db->db_insert_id();
        } else {
            $kb_array['ERR'] = true;
            $k_id            = 0;
            echo "<a href='$k_kb' target='_new'>$k_kb</a> <span class='doc_red'>KB ist bereits vorhanden!</span><br>";
        }
    }
}

function insert_iw_kaputt()
{
    global $db;
    global $kb_array;
    global $a_id;
    global $k_id;
    global $db_tb_kb_kaputt;

//  if ($k_id > 0 && !$kb_array['ERR'] && ($kb_array['VTYP'] == 'E' || $kb_array['VTYP'] == 'F')) {
    if ($k_id > 0 && !$kb_array['ERR']) {
        $kb_array['VANZS'] = (is_numeric($kb_array['VANZS']) ? $kb_array['VANZS'] : 0);
        $kb_array['VANZE'] = (is_numeric($kb_array['VANZE']) ? $kb_array['VANZE'] : 0);
        if ($kb_array['VANZS'] > 0) {
            $sSQL = "INSERT INTO `" . $db_tb_kb_kaputt . "` VALUES ($a_id, $k_id, '" . $kb_array['VTYP'] . "' ";
            $sSQL .= ", '" . htmlspecialchars($kb_array['VALLY'], ENT_QUOTES, 'UTF-8') . "' ";
            $sSQL .= ", '" . htmlspecialchars($kb_array['VNAME'], ENT_QUOTES, 'UTF-8') . "' ";
            $sSQL .= ", '" . $kb_array['VART'] . "' ";
            $sSQL .= ", '" . htmlspecialchars($kb_array['VBEZ'], ENT_QUOTES, 'UTF-8') . "' ";
            $sSQL .= ", " . $kb_array['VANZS'];
            $sSQL .= ", " . $kb_array['VANZE'];
            $sSQL .= ", " . $kb_array['VKLASSE'];
            $sSQL .= " )";
            $db->db_query($sSQL);
        }
    }
}

function init_kb_array()
{
    global $kb_array;
    $kb_array['TYP']      = '';
    $kb_array['ART']      = '';
    $kb_array['TIME']     = 0;
    $kb_array['OPFER']    = '';
    $kb_array['ALLY']     = '';
    $kb_array['ORT']      = '';
    $kb_array['SIEG']     = '';
    $kb_array['MAUER']    = '';
    $kb_array['ATTALLY']  = '';
    $kb_array['ATTSPIEL'] = '';
    $kb_array['ID']       = '';
    $kb_array['ATTEISEN'] = 0;
    $kb_array['ATTSTAHL'] = 0;
    $kb_array['ATTVV4A']  = 0;
    $kb_array['ATTCHEM']  = 0;
    $kb_array['ATTENE']   = 0;
    $kb_array['ATTCRED']  = 0;
    $kb_array['ATTBEV']   = 0;
    $kb_array['ATTEIS']   = 0;
    $kb_array['ATTWAS']   = 0;
    $kb_array['DEFEISEN'] = 0;
    $kb_array['DEFSTAHL'] = 0;
    $kb_array['DEFVV4A']  = 0;
    $kb_array['DEFCHEM']  = 0;
    $kb_array['DEFENE']   = 0;
    $kb_array['DEFCRED']  = 0;
    $kb_array['DEFBEV']   = 0;
    $kb_array['DEFEIS']   = 0;
    $kb_array['DEFWAS']   = 0;
    $kb_array['PLUEISEN'] = 0;
    $kb_array['PLUSTAHL'] = 0;
    $kb_array['PLUVV4A']  = 0;
    $kb_array['PLUCHEM']  = 0;
    $kb_array['PLUENE']   = 0;
    $kb_array['PLUEIS']   = 0;
    $kb_array['PLUWAS']   = 0;
    $kb_array['BOMBEV']   = 0;
    $kb_array['VTYP']     = '';
    $kb_array['VALLY']    = '';
    $kb_array['VNAME']    = '';
    $kb_array['VART']     = '';
    $kb_array['VBEZ']     = '';
    $kb_array['VANZS']    = 0;
    $kb_array['VANZE']    = 0;
    $kb_array['VKLASSE']  = 0;
    $kb_array['ERR']      = false;
    $kb_array['RAUCH']    = 0;
    $kb_array['MSG']      = '';
}

function number($sAnz, $F = '')
{
    global $BB;
    global $aBB;

// if ($sAnz == 'SPACE') return '&nbsp;';

    $sPunkte = number_format($sAnz, 0, ',', '.');
//  if ($sAnz == 0) $sPunkte = "<span class='doc_black'>$sPunkte</span>";
    if (!$BB) {
        if ($F == 'F') { // Farbaufbereitung
            if ($sAnz >= 0) {
                $sPunkte = "<span class='doc_green'>$sPunkte</span>";
            }
            if ($sAnz < 0) {
                $sPunkte = "<span class='doc_red'>$sPunkte</span>";
            }
        }
        if ($F == 'R') { // Zahl > 0 Rot, sonst hellgrau
            if ($sAnz > 0) {
                $sPunkte = "<b><span class='doc_red'>$sPunkte</span></b>";
            }
            if ($sAnz <= 0) {
                $sPunkte = "<span class='doc_gray'>$sPunkte</span>";
            }
        }
        if ($F == 'G') { // Zahl > 0 Green, sonst hellgrau
            if ($sAnz > 0) {
                $sPunkte = "<b><span class='doc_green'>$sPunkte</span></b>";
            }
            if ($sAnz <= 0) {
                $sPunkte = "<span class='doc_gray'>$sPunkte</span>";
            }
        }
    } else {
        if ($F == 'F') { // Farbaufbereitung
            if ($sAnz >= 0) {
                $sPunkte = '[color=' . $aBB['green'] . ']' . $sPunkte . '[/color]';
            }
            if ($sAnz < 0) {
                $sPunkte = '[color=' . $aBB['red'] . ']' . $sPunkte . '[/color]';
            }
        }
        if ($F == 'R') { // Zahl > 0 Rot, sonst hellgrau
            if ($sAnz > 0) {
                $sPunkte = '[color=' . $aBB['red'] . ']' . $sPunkte . '[/color]';
            }
            if ($sAnz <= 0) {
                $sPunkte = '[color=' . $aBB['gray'] . ']' . $sPunkte . '[/color]';
            }
        }
        if ($F == 'G') { // Zahl > 0 Green, sonst hellgrau
            if ($sAnz > 0) {
                $sPunkte = '[color=' . $aBB['green'] . ']' . $sPunkte . '[/color]';
            }
            if ($sAnz <= 0) {
                $sPunkte = '[color=' . $aBB['gray'] . ']' . $sPunkte . '[/color]';
            }
        }
    }

    return $sPunkte;
}

function ausgabe_ueber($K_Long, $art, $fakeOff)
{
    global $fake;

    echo "<tr>";
    echo "    <td class='titlebg center' colspan=1 style='width:30px'>";
    echo "    <td class='titlebg center' colspan=4 style='width:70px'>";
    echo "    <td class='titlebg center' colspan=5 style='width:480px'>Verteidiger";
    echo "    <td class='menu'    style='width:1px;'>";
    echo "    <td class='titlebg center' colspan=2 style='width:250px'>Angreifer";
    if ($K_Long) {
        echo "    <td class='menu'    style='width:1px;'>";
        echo "    <td class='titlebg center' colspan=7 style='width:350px'>Geplünderte Rohstoffe";
        echo "    <td class='menu'    style='width:1px;'>";
        echo "    <td class='titlebg center' colspan=9 style='width:490px'>Angreifer Rohstoffverluste";
        echo "    <td class='menu'    style='width:1px;'>";
        echo "    <td class='titlebg center' colspan=9 style='width:490px'>Verteidiger Rohstoffverluste";
    }
    echo "</tr><tr>";
    if ($art == 'E') {
//   if ($user_status == "admin") {
        echo "    <td class='titlebg center' style='width:30px'><input type='submit' name='sKB' value='KB' style='CURSOR: hand;'>";
//   } else {
//      echo "    <td class='titlebg center' style='width:30px'>KB";
//   }
//    echo "    <td class='titlebg center' style='width:10px'>F";
        if ($fakeOff == 'J') {
            if ($fake != 'off') {
                echo "    <td class='titlebg center' style='width:30px'><input type='submit' name='fake' value='off' style='CURSOR: hand;' title='Fakeangriffe NICHT anzeigen'>";
            } else {
                echo "    <td class='titlebg center' style='width:30px'><input type='submit' name='fake' value='on' style='CURSOR: hand;' title='Fakeangriffe anzeigen'>";
            }
        } else {
            echo "    <td class='titlebg center' style='width:30px'>&nbsp;";
        }
        echo "    <td class='titlebg center' style='width:20px'>Del";
        echo "    <td class='titlebg center' style='width:20px'>EM";
        echo "    <td class='titlebg center' style='width:20px'>Art";
        echo "    <td class='titlebg center' >Datum"; // 160
        echo "    <td class='titlebg left'   >KB-Link"; // 40
    } else {
        echo "    <td colspan=7 class='titlebg'style='width:255px'>&nbsp;";
    }
    echo "    <td class='titlebg left'   style='width:90px'>Allianz";
    if ($art == 'E') {
        echo "    <td class='titlebg left'   style='width:130px'>Spieler";
        echo "    <td class='titlebg right'  style='width:60px'>System";
    } else {
        echo "    <td colspan=2 class='titlebg'style='width:190px'>&nbsp;";
    }
    echo "    <td class='menu' style='width:1px;'>";
    echo "    <td class='titlebg left'   style='width:100px'>Allianz";
    if ($art == 'E') {
        echo "    <td class='titlebg left'   style='width:150px'>Spieler";
    } else {
        echo "    <td class='titlebg'style='width:150px'>&nbsp;";
    }
    if ($K_Long) {
        // Plünderungen         
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:70px'>Eisen";
        echo "    <td class='titlebg right' style='width:70px'>Stahl";
        echo "    <td class='titlebg right' style='width:70px'>VV4A";
        echo "    <td class='titlebg right' style='width:70px'>Chemie";
        echo "    <td class='titlebg right' style='width:70px'>Eis";
        echo "    <td class='titlebg right' style='width:70px'>Wasser";
        echo "    <td class='titlebg right' style='width:70px'>Energie";
        // Angreiferverluste
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:70px'>Eisen";
        echo "    <td class='titlebg right' style='width:70px'>Stahl";
        echo "    <td class='titlebg right' style='width:70px'>VV4A";
        echo "    <td class='titlebg right' style='width:70px'>Chemie";
        echo "    <td class='titlebg right' style='width:70px'>Eis";
        echo "    <td class='titlebg right' style='width:70px'>Wasser";
        echo "    <td class='titlebg right' style='width:70px'>Energie";
        echo "    <td class='titlebg right' style='width:70px'>Credits";
        echo "    <td class='titlebg right' style='width:70px'>Bev";
        // Verteidigerverluste
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:70px'>Eisen";
        echo "    <td class='titlebg right' style='width:70px'>Stahl";
        echo "    <td class='titlebg right' style='width:70px'>VV4A";
        echo "    <td class='titlebg right' style='width:70px'>Chemie";
        echo "    <td class='titlebg right' style='width:70px'>Eis";
        echo "    <td class='titlebg right' style='width:70px'>Wasser";
        echo "    <td class='titlebg right' style='width:70px'>Energie";
        echo "    <td class='titlebg right' style='width:70px'>Credits";
        echo "    <td class='titlebg right' style='width:70px'>Bev";
    }
    echo "</tr>";
}

function ausgabe_zeile($rowKB, $art, $bez, $K_Long, $typ, $ftoggle, $coords = '')
{
    global $db;
    global $hclass;
    global $sclass;
    global $mclass;
    global $a_id;
    global $mtoggle;
    global $k_zeigen;
    global $z;
    global $fake;
    global $db_tb_scans, $db_tb_kb_kaputt, $db_tb_scans_user;

    $row_user['u_usertyp'] = '';
    if (!empty($db_tb_scans_user) && !empty($rowKB['k_opfer'])) {
        $sql = "SELECT * FROM $db_tb_scans_user WHERE u_user ='{$rowKB['k_opfer']}' ";
        $select = $db->db_query($sql);
        $row_user = $db->db_fetch_array($select);
    }

    $gebscan = '';
    if (isset($rowKB['k_art']) && $rowKB['k_art'] == 'B' && $typ == 'E') {
        $sql = "SELECT gebscantime FROM $db_tb_scans WHERE coords='" . $rowKB['k_ort'] . "' ";
        $select = $db->db_query($sql);
        $rowP = $db->db_fetch_array($select);
        $db->db_free_result($select);
        if ($rowKB['k_time'] > $rowP['gebscantime']) {
            $gebscan = '<small>GS</small>&nbsp;';
        }
    }

    $z++;
    $k_art = (isset($rowKB['k_art']) ? $rowKB['k_art'] : '');
    if ($typ == 'E') {
        if ($z % 2 == 0) {
            $hclass = 'wfreund';
        } else {
            $hclass = 'wfreund2';
        }

        if (isset($rowKB['k_art']) && $rowKB['k_art'] == 'F') {
            $hclass = 'wfreundf';
        }

        if ($k_art == 'F') { // bei eigenen Fakes prüfen ob dort Flotte stand Oo
            $sql = "SELECT sum(v_anze) AS anzahl FROM $db_tb_kb_kaputt ";
            $sql .= "WHERE k_id='" . $rowKB['k_id'] . "' ";
            $sql .= "AND v_art = 'D' ";
            $sql .= "AND v_klasse between '3' AND '9' ";

            $select = $db->db_query($sql);
            $rowF = $db->db_fetch_array($select);
            $db->db_free_result($select);
            if ($rowF['anzahl'] > 0) { // Deffschiffe gefunden
                $k_art = '<font color = "#cc0000">' . $rowKB['k_art'] . '</font>';
            }
        }
    }
    if ($typ == 'F') {
        if ($z % 2 == 0) {
            $hclass = 'wfeind';
        } else {
            $hclass = 'wfeind2';
        }

        if (isset($rowKB['k_art']) && $rowKB['k_art'] == 'F') {
            $hclass = 'wfeindf';
        }
    }
    if ($art == 'SG') {
        $hclass .= ' summen';
    }

    if (substr($art, 0, 1) == 'S') {
        echo "<tr>";
        echo "     <td colspan=7 class='$hclass'>$bez";
    } else {
        echo "<tr>";
        echo "    <td class='$hclass left'><input class='Check' name='KBP[]' value='" . $rowKB['k_id'] . "' type='checkbox'>";
        if ($rowKB['k_forum'] == 'J') {
            echo ' &#x2714;';
        }
        if (isset($ftoggle) && ($ftoggle == 'N' || $ftoggle == 'J')) {
            echo "    <td class='$hclass center'><a class='kb' href='index.php?action=m_kb&KBF=$ftoggle&fake=$fake&a_id=$a_id&k_id=" . $rowKB['k_id'] . "$k_zeigen' title='Toggle Fake'>F</a>";
        } else {
            echo "    <td class='$hclass center'>";
        }
        echo "    <td class='$hclass center'><a class='kb' href='index.php?action=m_kb&KBD=J&a_id=$a_id&fake=$fake&k_id=" . $rowKB['k_id'] . "$k_zeigen' title='Satz löschen'>X</a>";
        echo "    <td class='$hclass center'><a class='kb' href='index.php?action=m_kb&KBEM=$mtoggle&fake=$fake&a_id=$a_id&k_opfer=" . rawurlencode($rowKB['k_opfer']) . "$k_zeigen' title='Toggle Einmaurer'>M</a>";
        echo "    <td class='$hclass center'><b>$k_art</b>";
        echo "    <td class='$hclass'>" . date("d.m.y - H:i:s", $rowKB['k_time']);
        echo "    <td class='$hclass'><a class='kb'  href='" . $rowKB['k_kb'] . "' target=new'>KB-Link</a>";
    }
    if ($art == 'E' || $art == 'SA') {
        echo "    <td class='$hclass $sclass'>" . $rowKB['k_ally'];
    } else {
        echo "    <td class='$hclass $sclass'>&nbsp;";
    }
    if (substr($art, 0, 1) == 'S') {
        echo "    <td colspan=2 class='$hclass $sclass'>&nbsp;";
    } else {
        echo "    <td class='$hclass $mclass $sclass'>" . $rowKB['k_opfer'];
        echo show_user_typ($row_user['u_usertyp']);
//    echo "    <td class='$hclass $sclass right'>" . $rowKB['k_ort'];
        echo "    <td class='$hclass $sclass right'>$gebscan";
        echo '<a class="kb" href="index.php?action=showplanet&amp;coords=' . $rowKB['k_ort'] . '">' . $rowKB['k_ort'] . '</a>';
        echo '&nbsp;&nbsp;';
        echo     "<a href='index.php?action=m_kb&Ncoords=" . $rowKB['k_ort'] . "&fake=on&a_id=$a_id&k_id=" . $rowKB['k_id'] . "$k_zeigen' title='Nur die Angriffe auf diese Koordinate anzeigen'><img src='".BILDER_PATH."plus.gif' alt='' border='0'></a>\n";
    }
    echo "    <td class='menu'>";
    if ($art == 'E' || $art == 'SA' || $art == 'SJ') {
        echo "    <td class='$hclass $sclass'>" . $rowKB['k_attally'];
    } else {
        echo "    <td class='$hclass $sclass'>&nbsp;";
    }
    if (substr($art, 0, 1) == 'S') {
        echo "    <td class='$hclass $sclass'>&nbsp;";
    } else {
        echo "    <td class='$hclass $sclass'>" . $rowKB['k_attspiel'];
    }
    if ($K_Long) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_plueisen']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_plustahl']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_pluvv4a']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_pluchem']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_plueis']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_pluwas']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_pluene']);
        echo "    <td class='menu'>";
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_atteisen']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_attstahl']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_attvv4a']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_attchem']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_atteis']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_attwas']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_attene']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_attcred']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_attbev']);
        echo "    <td class='menu'>";
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_defeisen']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_defstahl']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_defvv4a']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_defchem']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_defeis']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_defwas']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_defene']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_defcred']);
        echo "    <td class='$hclass $sclass right'>" . number($rowKB['k_defbev']);
    }
    echo "</tr>";
}

function verluste_anzeigen()
{
    verluste_anzeigen_R();
    verluste_anzeigen_A();
    verluste_anzeigen_G();
    verluste_anzeigen_S();
    verluste_anzeigen_D();
}

function verluste_anzeigen_R()
{
    global $w_freund;
    global $w_feind;
    global $a_freund;
    global $a_feind;
    global $BB;

    if (!$BB) {
        echo "<span class='wfreund center'>&nbsp;&nbsp;$w_freund&nbsp;&nbsp;</span> <-> <span class='wfeind'>&nbsp;&nbsp;$w_feind&nbsp;&nbsp;</span>";
        echo "<p><table cellspacing='1' cellpadding='2' border='0' class='bordercolor' width=950>";
    } else {
        echo "<p>[quote][table]";
    }
    verluste_jeAlly_R($a_freund, $a_feind, 'F'); // Rohstoffe, Freunde
    verluste_jeAlly_R($a_feind, $a_freund, 'E'); // Rohstoffe, Feinde
    if (!$BB) {
        echo "</table></p>";
    } else {
        echo "[/table][/quote]</p><br><br>";
    }
}

function verluste_anzeigen_A()
{
    global $w_freund;
    global $w_feind;
    global $BB;

    if (!$BB) {
        echo "<span class='wfreund center'>&nbsp;&nbsp;$w_freund&nbsp;&nbsp;</span> <-> <span class='wfeind'>&nbsp;&nbsp;$w_feind&nbsp;&nbsp;</span>";
        echo "<p><table cellspacing='1' cellpadding='2' border='0' class='bordercolor' width=950>";
    } else {
        echo "<p>[quote][table]";
    }

    verluste_jeAlly_F('F'); // Fakes
    verluste_jeAlly_A('A'); // Angriffe
    verluste_jeAlly_A('V'); // Verteidigungen
    if (!$BB) {
        echo "</table></p>";
    } else {
        echo "[/table][/quote]</p><br><br>";
    }
}

function verluste_anzeigen_G()
{
    global $w_freund;
    global $w_feind;
    global $BB;

    if (!$BB) {
        echo "<span class='wfreund center'>&nbsp;&nbsp;$w_freund&nbsp;&nbsp;</span> <-> <span class='wfeind'>&nbsp;&nbsp;$w_feind&nbsp;&nbsp;</span>";
        echo "<p><table cellspacing='1' cellpadding='2' border='0' class='bordercolor' width=950>";
    } else {
        echo "<p>[quote][table]";
    }
    verluste_jeAlly_G(); // Gebäude, alle
    if (!$BB) {
        echo "</table></p>";
    } else {
        echo "[/table][/quote]</p><br><br>";
    }
}

function verluste_anzeigen_S()
{
    global $w_freund;
    global $w_feind;
    global $BB;

    if (!$BB) {
        echo "<span class='wfreund center'>&nbsp;&nbsp;$w_freund&nbsp;&nbsp;</span> <-> <span class='wfeind'>&nbsp;&nbsp;$w_feind&nbsp;&nbsp;</span>";
        echo "<p><table cellspacing='1' cellpadding='2' border='0' class='bordercolor' width=950>";
    } else {
        echo "<p>[quote][table]";
    }
    verluste_jeAlly_S(); // Schiffe, alle
    if (!$BB) {
        echo "</table></p>";
    } else {
        echo "[/table][/quote]</p><br><br>";
    }
}

function verluste_anzeigen_D()
{
    global $w_freund;
    global $w_feind;
    global $BB;

    if (!$BB) {
        echo "<span class='wfreund center'>&nbsp;&nbsp;$w_freund&nbsp;&nbsp;</span> <-> <span class='wfeind'>&nbsp;&nbsp;$w_feind&nbsp;&nbsp;</span>";
        echo "<p><table cellspacing='1' cellpadding='2' border='0' class='bordercolor' width=950>";
    } else {
        echo "<p>[quote][table]";
    }
    verluste_jeAlly_V(); // Verteidigungsanlagen, alle
    if (!$BB) {
        echo "</table></p>";
    } else {
        echo "[/table][/quote]</p><br><br>";
    }
}

function verluste_jeAlly_R($a_ally, $d_ally, $ff)
{
    global $db;
    global $a_id;
    global $db_tb_kb_kb;
    global $rowS;
    global $BB;

    $sqlA = "  sum(k_atteisen) AS k_atteisen ";
    $sqlA .= ", sum(k_attstahl) as k_attstahl ";
    $sqlA .= ", sum(k_attvv4a)  as k_attvv4a  ";
    $sqlA .= ", sum(k_attchem)  as k_attchem  ";
    $sqlA .= ", sum(k_attene)   as k_attene   ";
    $sqlA .= ", sum(k_attcred)  as k_attcred  ";
    $sqlA .= ", sum(k_attbev)   as k_attbev   ";
    $sqlA .= ", sum(k_atteis)   as k_atteis   ";
    $sqlA .= ", sum(k_attwas)   as k_attwas   ";

    $sqlD = "  sum(k_defeisen) as k_defeisen ";
    $sqlD .= ", sum(k_defstahl) as k_defstahl ";
    $sqlD .= ", sum(k_defvv4a)  as k_defvv4a  ";
    $sqlD .= ", sum(k_defchem)  as k_defchem  ";
    $sqlD .= ", sum(k_defene)   as k_defene   ";
    $sqlD .= ", sum(k_defcred)  as k_defcred  ";
    $sqlD .= ", sum(k_defbev)   as k_defbev   ";
    $sqlD .= ", sum(k_defeis)   as k_defeis   ";
    $sqlD .= ", sum(k_defwas)   as k_defwas   ";
    $sqlD .= ", sum(k_bombev)   as k_bombev   ";

    $sqlP = "  sum(k_plueisen) as k_plueisen ";
    $sqlP .= ", sum(k_plustahl) as k_plustahl ";
    $sqlP .= ", sum(k_pluvv4a)  as k_pluvv4a  ";
    $sqlP .= ", sum(k_pluchem)  as k_pluchem  ";
    $sqlP .= ", sum(k_plueis)   as k_plueis   ";
    $sqlP .= ", sum(k_pluwas)   as k_pluwas   ";
    $sqlP .= ", sum(k_pluene)   as k_pluene   ";

    $rowS['k_atteisen'] = 0;
    $rowS['k_attstahl'] = 0;
    $rowS['k_attvv4a']  = 0;
    $rowS['k_attchem']  = 0;
    $rowS['k_atteis']   = 0;
    $rowS['k_attwas']   = 0;
    $rowS['k_attene']   = 0;
    $rowS['k_attcred']  = 0;
    $rowS['k_attbev']   = 0;
    $rowS['k_bombev']   = 0;

    verluste_ueberR();

    $sd_ally = '(';
    for ($i = 0; $i < count($d_ally); $i++) {
        if (strlen($sd_ally) > 1) {
            $sd_ally .= ', ';
        }
        $sd_ally .= "'" . $d_ally[$i] . "'";
    }
    $sd_ally .= ')';

    for ($i = 0; $i < count($a_ally); $i++) {
        if ($i > 0) {
            if (!$BB) {
                echo "<tr></tr>";
            } else {
                echo "[tr][/tr]<br>";
            }
        }
        $sql = "SELECT ";
        $sql .= $sqlA;
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_attally = '" . $a_ally[$i] . "' ";
        $sql .= "AND k_ally IN $sd_ally ";
        $select = $db->db_query($sql);
        $rowA = $db->db_fetch_array($select);
        $db->db_free_result($select);

        $sql = "SELECT ";
        $sql .= $sqlD;
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_ally = '" . $a_ally[$i] . "' ";
        $sql .= "AND k_attally IN $sd_ally ";
        $select = $db->db_query($sql);
        $rowD = $db->db_fetch_array($select);
        $db->db_free_result($select);

        $sql = "SELECT ";
        $sql .= $sqlP;
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_attally = '" . $a_ally[$i] . "' ";
        $sql .= "AND k_ally IN $sd_ally ";
        $select = $db->db_query($sql);
        $rowP = $db->db_fetch_array($select);
        $db->db_free_result($select);

        verluste_zeile_R($rowA, $rowD, $rowP, $a_ally[$i], ' ', $ff);
    }
    verluste_zeile_R($rowS, $rowS, $rowS, 'Gesamt', 'S', $ff);
}

function verluste_ueberR()
{
    global $BB;

    if (!$BB) {
        echo "<tr>";
        echo "   <td class='titlebg left'>Allianz</td>";
        echo "   <td class='menu'    style='width:1px;'></td>";
        echo "   <td class='titlebg left'></td>";
        echo "   <td class='menu'    style='width:1px;'></td>";
        echo "   <td class='titlebg right'>Eisen</td>";
        echo "   <td class='titlebg right'>Stahl</td>";
        echo "   <td class='titlebg right'>VV4A</td>";
        echo "   <td class='titlebg right'>Chemie</td>";
        echo "   <td class='titlebg right'>Eis</td>";
        echo "   <td class='titlebg right'>Wasser</td>";
        echo "   <td class='titlebg right'>Energie</td>";
        echo "   <td class='titlebg right'>Credits</td>";
        echo "   <td class='titlebg right'>Bev</td>";
        echo "   <td class='menu'    style='width:1px;'></td>";
        echo "   <td class='titlebg right'>BombBev</td>";
        echo "</tr>";
    } else {
        echo "[tr]";
        echo "   [td][b]Allianz[/b][/td]";
        echo "   [td][/td]";
        echo "   [td][right][b]Eisen[/b][/right][/td]";
        echo "   [td][right][b]Stahl[/b][/right][/td]";
        echo "   [td][right][b]VV4A[/b][/right][/td]";
        echo "   [td][right][b]Chemie[/b][/right][/td]";
        echo "   [td][right][b]Eis[/b][/right][/td]";
        echo "   [td][right][b]Wasser[/b][/right][/td]";
        echo "   [td][right][b]Energie[/b][/right][/td]";
        echo "   [td][right][b]Credits[/b][/right][/td]";
        echo "   [td][right][b]Bev[/b][/right][/td]";
        echo "   [td][right][b]BombBev[/b][/right][/td]";
        echo "[/tr]<br>";
    }
}

function verluste_zeile_R($rowA, $rowD, $rowP, $ally, $art, $ff)
{
    global $hclass;
    global $rowS;
    global $z;
    global $BB;
    global $aBB;

    $z++;
    if (!$BB) {
        if ($ff == 'F') {
            if ($z % 2 == 0) {
                $hclass = 'wfreund';
            }
            if ($z % 2 != 0) {
                $hclass = 'wfreund2';
            }
        }
        if ($ff == 'E') {
            if ($z % 2 == 0) {
                $hclass = 'wfeind';
            }
            if ($z % 2 != 0) {
                $hclass = 'wfeind2';
            }
        }
    } else {
        $hclass = '';
    }

    if ($art != 'S') {
        if (!$BB) {
            $sclass = 'doc_red';
        } else {
            $sclass = '[color=' . $aBB['red'] . ']';
        }

        if (!$BB) {
            echo "<tr>";
            $hanf = "<td class='$hclass'>";
            $hend = "";
        } else {
            echo "[tr]";
            $hanf = "[td]";
            $hend = "[/td]";
        }
        echo $hanf . $ally . $hend;
        if (!$BB) {
            echo "    <td class='menu'></td>";
        }
        echo $hanf . 'Att-Verluste' . $hend;
        if (!$BB) {
            echo "    <td class='menu'></td>";
        }

        if (!$BB) {
            $hanf = "<td class='$hclass $sclass right'>";
        } else {
            $hanf = "[td][right] $sclass ";
            $hend = "[/color][/right][/td]";
        }
        echo $hanf . number($rowA['k_atteisen']) . $hend;
        echo $hanf . number($rowA['k_attstahl']) . $hend;
        echo $hanf . number($rowA['k_attvv4a']) . $hend;
        echo $hanf . number($rowA['k_attchem']) . $hend;
        echo $hanf . number($rowA['k_atteis']) . $hend;
        echo $hanf . number($rowA['k_attwas']) . $hend;
        echo $hanf . number($rowA['k_attene']) . $hend;
        echo $hanf . number($rowA['k_attcred']) . $hend;
        echo $hanf . number($rowA['k_attbev']) . $hend;
        if (!$BB) {
            echo "    <td class='menu'>";
        }
        echo $hanf . number(0) . $hend;
        if (!$BB) {
            echo "</tr>";
            echo "<tr>";
            $hanf = "<td class='$hclass'>";
            $hend = "";
        } else {
            echo "[/tr]<br>";
            echo "[tr]";
            $hanf = "[td]";
            $hend = "[/td]";
        }
        echo $hanf . $hend;
        if (!$BB) {
            echo "    <td class='menu'></td>";
        }
        echo $hanf . 'Def-Verluste' . $hend;
        if (!$BB) {
            echo "    <td class='menu'></td>";
            $hanf = "<td class='$hclass $sclass right'>";
        } else {
            $hanf = "[td][right] $sclass";
            $hend = "[/color][/right][/td]";
        }
        echo $hanf . number($rowD['k_defeisen']) . $hend;
        echo $hanf . number($rowD['k_defstahl']) . $hend;
        echo $hanf . number($rowD['k_defvv4a']) . $hend;
        echo $hanf . number($rowD['k_defchem']) . $hend;
        echo $hanf . number($rowD['k_defeis']) . $hend;
        echo $hanf . number($rowD['k_defwas']) . $hend;
        echo $hanf . number($rowD['k_defene']) . $hend;
        echo $hanf . number($rowD['k_defcred']) . $hend;
        echo $hanf . number($rowD['k_defbev']) . $hend;
        if (!$BB) {
            echo "    <td class='menu'>";
        }
        echo $hanf . number($rowD['k_bombev']) . $hend;
        if (!$BB) {
            echo "</tr>";
            $sclass = 'doc_green';
            echo "<tr>";
            $hanf = "<td class='$hclass'>";
            $hend = "";
        } else {
            echo "[/tr]<br>";
            $sclass = '[color=' . $aBB['green'] . ']';
            echo "[tr]";
            $hanf = "[td]";
            $hend = "[/td]";
        }
        echo $hanf . $hend;
        if (!$BB) {
            echo "    <td class='menu'></td>";
        }
        echo $hanf . 'Pl&uuml;nderungen' . $hend;
        if (!$BB) {
            echo "    <td class='menu'></td>";
        }
        if (!$BB) {
            $hanf = "<td class='$hclass $sclass right'>";
        } else {
            $hanf = "[td][right] $sclass";
            $hend = "[/color][/right][/td]";
        }
        echo $hanf . number($rowP['k_plueisen']) . $hend;
        echo $hanf . number($rowP['k_plustahl']) . $hend;
        echo $hanf . number($rowP['k_pluvv4a']) . $hend;
        echo $hanf . number($rowP['k_pluchem']) . $hend;
        echo $hanf . number($rowP['k_plueis']) . $hend;
        echo $hanf . number($rowP['k_pluwas']) . $hend;
        echo $hanf . number($rowP['k_pluene']) . $hend;
        echo $hanf . number(0) . $hend;
        echo $hanf . number(0) . $hend;
        if (!$BB) {
            echo "    <td class='menu'>";
        }
        echo $hanf . number(0) . $hend;
        if (!$BB) {
            echo "</tr>";
            $hclass .= ' summen';
            echo "<tr>";
            $hanf = "<td class='$hclass'>";
            $hend = "";
        } else {
            echo "[/tr]<br>";
            echo "[tr]";
            $hanf = "[td][b]";
            $hend = "[/b][/td]";
        }
        echo $hanf . $hend;
        if (!$BB) {
            echo "    <td class='menu'></td>";
        }
        echo $hanf . 'Summe' . $hend;
        if (!$BB) {
            echo "    <td class='menu'></td>";
        }
        if (!$BB) {
            $hanf = "<td class='$hclass right'>";
        } else {
            $hanf = "[td][right][b]";
            $hend = "[/b][/right][/td]";
        }
        echo $hanf . number(-$rowA['k_atteisen'] - $rowD['k_defeisen'] + $rowP['k_plueisen'], 'F') . $hend;
        echo $hanf . number(-$rowA['k_attstahl'] - $rowD['k_defstahl'] + $rowP['k_plustahl'], 'F') . $hend;
        echo $hanf . number(-$rowA['k_attvv4a'] - $rowD['k_defvv4a'] + $rowP['k_pluvv4a'], 'F') . $hend;
        echo $hanf . number(-$rowA['k_attchem'] - $rowD['k_defchem'] + $rowP['k_pluchem'], 'F') . $hend;
        echo $hanf . number(-$rowA['k_atteis'] - $rowD['k_defeis'] + $rowP['k_plueis'], 'F') . $hend;
        echo $hanf . number(-$rowA['k_attwas'] - $rowD['k_defwas'] + $rowP['k_pluwas'], 'F') . $hend;
        echo $hanf . number(-$rowA['k_attene'] - $rowD['k_defene'] + $rowP['k_pluene'], 'F') . $hend;
        echo $hanf . number(-$rowA['k_attcred'] - $rowD['k_defcred'], 'F') . $hend;
        echo $hanf . number(-$rowA['k_attbev'] - $rowD['k_defbev'], 'F') . $hend;
        if (!$BB) {
            echo "    <td class='menu'>";
        }
        echo $hanf . number(-$rowD['k_bombev'], 'F') . $hend;
        if (!$BB) {
            echo "</tr>";
        } else {
            echo "[/tr]<br>";
        }
        $rowS['k_atteisen'] += -$rowA['k_atteisen'] - $rowD['k_defeisen'] + $rowP['k_plueisen'];
        $rowS['k_attstahl'] += -$rowA['k_attstahl'] - $rowD['k_defstahl'] + $rowP['k_plustahl'];
        $rowS['k_attvv4a'] += -$rowA['k_attvv4a'] - $rowD['k_defvv4a'] + $rowP['k_pluvv4a'];
        $rowS['k_attchem'] += -$rowA['k_attchem'] - $rowD['k_defchem'] + $rowP['k_pluchem'];
        $rowS['k_atteis'] += -$rowA['k_atteis'] - $rowD['k_defeis'] + $rowP['k_plueis'];
        $rowS['k_attwas'] += -$rowA['k_attwas'] - $rowD['k_defwas'] + $rowP['k_pluwas'];
        $rowS['k_attene'] += -$rowA['k_attene'] - $rowD['k_defene'] + $rowP['k_pluene'];
        $rowS['k_attcred'] += -$rowA['k_attcred'] - $rowD['k_defcred'];
        $rowS['k_attbev'] += -$rowA['k_attbev'] - $rowD['k_defbev'];
        $rowS['k_bombev'] += -$rowD['k_bombev'];
    }
    if ($art == 'S') { // Rohstoff Summenezeile
        if (!$BB) {
            echo "<tr></tr>";
            echo "<tr>";
            $hclass .= ' summen';
            echo "<tr>";
            $hanf = "<td class='$hclass'>";
            $hend = "";
        } else {
            echo "[tr][/tr]<br>";
            echo "[tr]";
            $hanf = "[td][b]";
            $hend = "[/b][/td]";
        }

        echo $hanf . 'Gesamt' . $hend;
        if (!$BB) {
            echo "    <td class='menu'>";
        }
        echo $hanf . 'Summe' . $hend;
        if (!$BB) {
            echo "    <td class='menu'>";
        }
        if (!$BB) {
            $hanf = "<td class='$hclass right'>";
        } else {
            $hanf = "[td][right][b]";
            $hend = "[/b][/right][/td]";
        }
        echo $hanf . number($rowS['k_atteisen'], 'F') . $hend;
        echo $hanf . number($rowS['k_attstahl'], 'F') . $hend;
        echo $hanf . number($rowS['k_attvv4a'], 'F') . $hend;
        echo $hanf . number($rowS['k_attchem'], 'F') . $hend;
        echo $hanf . number($rowS['k_atteis'], 'F') . $hend;
        echo $hanf . number($rowS['k_attwas'], 'F') . $hend;
        echo $hanf . number($rowS['k_attene'], 'F') . $hend;
        echo $hanf . number($rowS['k_attcred'], 'F') . $hend;
        echo $hanf . number($rowS['k_attbev'], 'F') . $hend;
        if (!$BB) {
            echo "    <td class='menu'>";
        }
        echo $hanf . number($rowS['k_bombev'], 'F') . $hend;
        if (!$BB) {
            echo "</tr>";
        } else {
            echo "[/tr][tr][/tr][tr][/tr][tr][/tr][tr][/tr]<br>";
        }
    }
}

function verluste_jeAlly_G()
{
    global $a_id;
    global $a_freund;
    global $a_feind;
    global $db, $db_tb_kb_kaputt, $db_tb_kb_kb, $db_tb_gebaeude;
    global $z;
    global $BB;

    $ga_freund = array();
    $ga_feind  = array();

    $sfreund_ally = '(';
    for ($i = 0; $i < count($a_freund); $i++) {
        if (strlen($sfreund_ally) > 1) {
            $sfreund_ally .= ', ';
        }
        $sfreund_ally .= "'" . $a_freund[$i] . "'";
    }
    $sfreund_ally .= ')';
    $sfeind_ally = '(';
    for ($i = 0; $i < count($a_feind); $i++) {
        if (strlen($sfeind_ally) > 1) {
            $sfeind_ally .= ', ';
        }
        $sfeind_ally .= "'" . $a_feind[$i] . "'";
    }
    $sfeind_ally .= ')';

    if (!$BB) {
        echo "<tr>";
        echo "    <td class='titlebg left' style='width:290px;'>Gebäude";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_freund[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Bauzeit</b>";
        echo "    <td class='menu' style='width:1px;'>";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_feind[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Bauzeit</b>";
        echo "</tr>";
    } else {
        echo "[tr]";
        echo "    [td][b]Geb&auml;ude[/b][/td]";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "  [td][right][b]$a_freund[$i][/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "    [td][right][b]Bauzeit[/b][/right][/td]";
        echo "    [td] | [/td]";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "  [td][right][b]$a_feind[$i][/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "    [td][right][b]Bauzeit[/b][/right][/td]";
        echo "[/tr]<br>";
    }
    // alle betroffenen Gebäude einlesen
    $gezeit = 0;
    $gfzeit = 0;

    $sql = "SELECT v_bez, g.category, g.idcat ";
    $sql .= "FROM `$db_tb_kb_kaputt` k ";
    $sql .= "LEFT OUTER join `$db_tb_gebaeude` g ";
    $sql .= "ON v_bez = g.name ";
    $sql .= "WHERE w_id  = $a_id ";
    $sql .= "  AND v_art = 'G' ";
    $sql .= "GROUP BY g.category,g.idcat, v_bez ";
    $sql .= "ORDER BY g.category,g.idcat, v_bez ";

    $selectG = $db->db_query($sql);
    while ($rowG = $db->db_fetch_array($selectG)) {
        $hdauer = 0;
        $fzeit  = 0;
        $ezeit  = 0;

        $hbez = $rowG['v_bez'];
        $hcat = $rowG['category'];
        $sql  = "SELECT dauer ";
        $sql .= "FROM `$db_tb_gebaeude` ";
        $sql .= "WHERE name = '$hbez'";
        $selectD = $db->db_query($sql);
        if ($rowD = $db->db_fetch_array($selectD)) {
            $hdauer = $rowD['dauer'];
        }
        $db->db_free_result($selectD);
//    echo $sql . '<br>';
        $z++;
        if (!$BB) {
            echo "<tr>";
            echo "    <td class='menutop'>" . gebauede_farbig($hbez, $hcat);
//      echo "    <td class='menutop'>" . str_replace("&", "%", $rowG['v_bez']);
        } else {
            echo "[tr]";
            echo "    [td]" . gebauede_farbig($hbez, $hcat) . "[/td]";
        }
        if ($z % 2 == 0) {
            $hclass = 'wfreund';
        } else {
            $hclass = 'wfreund2';
        }
        $hGes = 0;
        for ($i = 0; $i < count($a_freund); $i++) {
            $sql = "SELECT sum(v_anzs) as s_anzs ";
            $sql .= "FROM `$db_tb_kb_kaputt` a, `$db_tb_kb_kb` b ";
            $sql .= "WHERE a.w_id = $a_id ";
            $sql .= "AND   b.w_id = $a_id ";
            $sql .= "AND   a.k_id = b.k_id ";
            $sql .= "AND v_ally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_attally IN $sfeind_ally ";
            $sql .= "AND v_bez  = '" . $rowG['v_bez'] . "' ";
            $sql .= "AND v_art  = 'G' ";
//      echo $sql;
            $selectA = $db->db_query($sql);
            $hAnz = 0;
            if ($rowA = $db->db_fetch_array($selectA)) {
                $hAnz = $rowA['s_anzs'];
            }
            $db->db_free_result($selectA);
            $hGes += $hAnz;
            $fzeit += ($hdauer * $hAnz);
            if (!isset($ga_freund[$i])) {
                $ga_freund[$i] = 0;
            }
            $ga_freund[$i] += ($hdauer * $hAnz);
            $gfzeit += ($hdauer * $hAnz);
            if (!$BB) {
                echo "    <td class='$hclass right'>" . number($hAnz, 'R');
            } else {
                echo "    [td][right] " . number($hAnz, 'R') . "[/right][/td]";
            }
        }
        // Freund-Summen je Gebäude
        if (!$BB) {
            echo "    <td class='menu'>";
        }
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hGes, 'R');
            echo "    <td class='$hclass right'>" . dauer_hhmm($fzeit);
            echo "    <td class='menu'>";
        } else {
            echo "    [td][right][b]" . number($hGes, 'R') . "[/b][/right][/td]";
            echo "    [td][right][b]" . dauer_hhmm($fzeit) . "[/b][/right][/td]";
            echo "    [td] | [/td]";
        }
        if ($z % 2 == 0) {
            $hclass = 'wfeind';
        }
        if ($z % 2 != 0) {
            $hclass = 'wfeind2';
        }
        $hGes = 0;
        for ($i = 0; $i < count($a_feind); $i++) {
            $sql = "SELECT sum(v_anzs) as s_anzs ";
            $sql .= "FROM `$db_tb_kb_kaputt` a, `$db_tb_kb_kb` b ";
            $sql .= "WHERE a.w_id = $a_id ";
            $sql .= "AND   b.w_id = $a_id ";
            $sql .= "AND   a.k_id = b.k_id ";
            $sql .= "AND v_ally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_attally IN $sfreund_ally ";
            $sql .= "AND v_bez  = '" . $rowG['v_bez'] . "' ";
            $sql .= "AND v_art  = 'G' ";
//      echo $sql . '<br>';
            $selectA = $db->db_query($sql);
            $hAnz = 0;
            if ($rowA = $db->db_fetch_array($selectA)) {
                $hAnz = $rowA['s_anzs'];
            }
            $db->db_free_result($selectA);
            $hGes += $hAnz;
            $ezeit += ($hdauer * $hAnz);
            if (!isset($ga_feind[$i])) {
                $ga_feind[$i] = 0;
            }
            $ga_feind[$i] += ($hdauer * $hAnz);
            $gezeit += ($hdauer * $hAnz);
            if (!$BB) {
                echo "    <td class='$hclass right'>" . number($hAnz, 'R');
            } else {
                echo "    [td][right] " . number($hAnz, 'R') . "[/right][/td]";
            }
        }
        // Feind-Summen je Gebäude
        if (!$BB) {
            echo "    <td class='menu'>";
        }
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hGes, 'R');
            echo "    <td class='$hclass right'>" . dauer_hhmm($ezeit);
            echo "</tr>";
        } else {
            echo "    [td][right][b]" . number($hGes, 'R') . "[/b][/right][/td]";
            echo "    [td][right][b]" . dauer_hhmm($ezeit) . "[/b][/right][/td]";
            echo "[/tr]<br>";
        }
    }
    $db->db_free_result($selectG);
    // Bauzeit-Summenzeile
    if (!$BB) {
        echo "<tr></tr><tr>";
        echo "    <td class='menutop'><b>Gesamt&nbsp;gebombte&nbsp;Bauzeit&nbsp;in&nbsp;hh:mm</b>";
    } else {
        echo "[tr][/tr]<br>[tr]";
        echo "    [td][b]Gesamt gebombte Bauzeit in hh:mm[/b][/td]";
    }

    if ($z % 2 == 0) {
        $hclass = 'wfreund';
    } else {
        $hclass = 'wfreund2';
    }

    for ($i = 0; $i < count($a_freund); $i++) {
        if (!isset($ga_freund[$i])) {
            $ga_freund[$i] = 0;
        }
        if (!$BB) {
            echo "    <td class='$hclass right'>" . dauer_hhmm($ga_freund[$i]);
        } else {
            echo "    [td]" . dauer_hhmm($ga_freund[$i]) . "[/td]";
        }
    }
    // Freund-Summen je Gebäude
    if (!$BB) {
        echo "    <td class='menu'>";
    }
    if (!$BB) {
        echo "    <td class='$hclass right'><b>" . dauer_hhmm($gfzeit) . '</b>';
        echo "    <td class='$hclass right'>";
        echo "    <td class='menu'>";
    } else {
        echo "    [td][right][b]" . dauer_hhmm($gfzeit) . "[/b][/right][/td]";
        echo "    [td][/td]";
        echo "    [td] | [/td]";
    }

    if ($z % 2 == 0) {
        $hclass = 'wfeind';
    } else {
        $hclass = 'wfeind2';
    }

    for ($i = 0; $i < count($a_feind); $i++) {
        if (!isset($ga_feind[$i])) {
            $ga_feind[$i] = 0;
        }
        if (!$BB) {
            echo "    <td class='$hclass right'>" . dauer_hhmm($ga_feind[$i]);
        } else {
            echo "    [td]" . dauer_hhmm($ga_feind[$i]) . "[/td]";
        }
    }
    // Feind-Summen je Gebäude
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'><b>" . dauer_hhmm($gezeit) . '</b>';
        echo "    <td class='$hclass right'>";
        echo "</tr>";
    } else {
        echo "    [td][right][b]" . dauer_hhmm($gezeit) . "[/b][/right][/td]";
        echo "    [td][/td]";
        echo "[/tr]<br>";
    }
}

function gebauede_farbig($v_bez, $hcat)
{
    global $BB;

    // Werften
    if (!$BB) {
        $v_bez = str_replace(" ", "&nbsp;", $v_bez);
    }
    if (strpos($hcat, 'Raumfahrt')) {
        if (!$BB) {
            $v_bez = "<span style='color:#dd0000; font-weight: bold;'>$v_bez</span>";
        } else {
            $v_bez = "[color=#dd0000]" . $v_bez . "[/color]";
        }
    }
    // Verteidigungsanlagen
    if (strpos($hcat, 'Verteidigung')) {
        if (!$BB) {
            $v_bez = "<b><span style='color:#C36200'>$v_bez</span></b>";
        } else {
            $v_bez = "[color=#c36200]" . $v_bez . "[/color]";
        }
    }
    // Sicht
    if (strpos($hcat, 'Beobachtung')) {
        if (!$BB) {
            $v_bez = "<b><span style='color:#FF5E28'>$v_bez</span></b>";
        } else {
            $v_bez = "[color=#ff5e28]" . $v_bez . "[/color]";
        }
    }
    if (strpos($hcat, 'Lager &amp; Bunker')) {
        if (!$BB) {
            $v_bez = "<b><span style='color:#872200'>$v_bez</span></b>";
        } else {
            $v_bez = "[color=#872200]" . $v_bez . "[/color]";
        }
    }
    // Zivieles schmerzhaftes Zeugs
    if (strpos($hcat, 'Wirtschaft &amp; Verwaltung')) {
        if (!$BB) {
            $v_bez = "<b><span style='color:#5A0087'>$v_bez</span></b>";
        } else {
            $v_bez = "[color=#5A0087]" . $v_bez . "[/color]";
        }
    }
    // Forschung
    if (strpos($hcat, 'Forschung')) {
        if (!$BB) {
            $v_bez = "<b><span style='color:#006938'>$v_bez</span></b>";
        } else {
            $v_bez = "[color=#006938]" . $v_bez . "[/color]";
        }
    }

    return $v_bez;
}

function verluste_jeAlly_S()
{
    global $a_id;
    global $a_freund;
    global $a_feind;
    global $db, $db_tb_kb_kaputt, $db_tb_kb_kb;
    global $z;
    global $BB;

    $sfreund_ally = '(';
    for ($i = 0; $i < count($a_freund); $i++) {
        if (strlen($sfreund_ally) > 1) {
            $sfreund_ally .= ', ';
        }
        $sfreund_ally .= "'" . $a_freund[$i] . "'";
    }
    $sfreund_ally .= ')';
    $sfeind_ally = '(';
    for ($i = 0; $i < count($a_feind); $i++) {
        if (strlen($sfeind_ally) > 1) {
            $sfeind_ally .= ', ';
        }
        $sfeind_ally .= "'" . $a_feind[$i] . "'";
    }
    $sfeind_ally .= ')';

    if (!$BB) {
        echo "<tr>";
        echo "    <td class='titlebg left' style='width:290px;' nowrap>Schiffe";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_freund[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "    <td class='menu' style='width:1px;'>";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_feind[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "</tr>";
    } else {
        echo "[tr]";
        echo "    [td][b]Schiffe[/b][/td]";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "  [td][right][b]" . $a_freund[$i] . "[/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "    [td] | [/td]";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "  [td][right][b]" . $a_feind[$i] . "[/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "[/tr]<br>";
    }

    // alle betroffenen Schiffe einlesen
    $sql = "SELECT v_klasse, v_bez, sum(v_anzs) - sum(v_anze) as s_anzs ";
    $sql .= "FROM `$db_tb_kb_kaputt` ";
    $sql .= "WHERE w_id  = $a_id ";
    $sql .= "  AND (v_art = 'A' OR v_art = 'D') ";
    $sql .= "GROUP BY v_klasse, v_bez ";
    $sql .= "ORDER BY v_klasse, v_bez ";
    $selectS = $db->db_query($sql);
    $hklasse = 99;
    while ($rowS = $db->db_fetch_array($selectS)) {
        $hbez = $rowS['v_bez'];
//    $hbez    = str_replace("&", "%", "$hbez");

        if ($rowS['s_anzs'] > 0) {
            if ($hklasse != 99 && $hklasse != $rowS['v_klasse']) {
                if (!$BB) {
                    echo "<tr></tr>";
                } else{
                    echo "[tr][/tr][tr][/tr][tr][/tr]<br>";
                }
            }
            $hklasse = $rowS['v_klasse'];
            $z++;
            if (!$BB) {
                echo "<tr>";
                $v_bez = str_replace("-", " ", $hbez);
                $v_bez = str_replace(" ", "&nbsp;", $v_bez);
                echo "    <td nowrap class='menutop'>" . $v_bez;
            } else {
                echo "[tr]";
                echo "    [td][b]" . $hbez . "[/b][/td]";
            }

            if ($z % 2 == 0) {
                $hclass = 'wfreund';
            } else {
                $hclass = 'wfreund2';
            }

            $hGes = 0;
            for ($i = 0; $i < count($a_freund); $i++) {
                $sql = "SELECT sum(v_anzs) - sum(v_anze) as s_anzs ";
                $sql .= "FROM `$db_tb_kb_kaputt` a, `$db_tb_kb_kb` b ";
                $sql .= "WHERE a.w_id = $a_id ";
                $sql .= "AND   b.w_id = $a_id ";
                $sql .= "AND   a.k_id = b.k_id ";
                $sql .= "AND v_ally = '" . $a_freund[$i] . "' ";
                $sql .= "AND ((k_attally IN $sfeind_ally ";
                $sql .= "AND   k_ally    IN $sfreund_ally) ";
                $sql .= " OR  (k_attally IN $sfreund_ally ";
                $sql .= "AND   k_ally    IN $sfeind_ally)) ";
                $sql .= "AND v_bez  = '" . $rowS['v_bez'] . "' ";
                $sql .= "AND (v_art = 'A' OR v_art = 'D') ";
                $selectA = $db->db_query($sql);
                $hAnz = 0;
                if ($rowA = $db->db_fetch_array($selectA)) {
                    $hAnz = $rowA['s_anzs'];
                }
                $db->db_free_result($selectA);
                $hGes += $hAnz;
                if (!$BB) {
                    echo "    <td class='$hclass right'>" . number($hAnz, 'R');
                } else {
                    echo "    [td][right] " . number($hAnz, 'R') . "[/right][/td]";
                }
            }
            // Freund-Summen je Schiffe
            if (!$BB) {
                echo "    <td class='menu'>";
            }
            if (!$BB) {
                echo "    <td class='$hclass right'>" . number($hGes, 'R');
                echo "    <td class='menu'>";
            } else {
                echo "    [td][right][b]" . number($hGes, 'R') . "[/b][/right][/td]";
                echo "    [td] | [/td]";
            }
            if ($z % 2 == 0) {
                $hclass = 'wfeind';
            }
            if ($z % 2 != 0) {
                $hclass = 'wfeind2';
            }
            $hGes = 0;
            for ($i = 0; $i < count($a_feind); $i++) {
                $sql = "SELECT sum(v_anzs) - sum(v_anze) as s_anzs ";
                $sql .= "FROM `$db_tb_kb_kaputt` a, `$db_tb_kb_kb` b ";
                $sql .= "WHERE a.w_id = $a_id ";
                $sql .= "AND   b.w_id = $a_id ";
                $sql .= "AND   a.k_id = b.k_id ";
                $sql .= "AND v_ally = '" . $a_feind[$i] . "' ";
                $sql .= "AND ((k_attally IN $sfeind_ally ";
                $sql .= "AND   k_ally    IN $sfreund_ally) ";
                $sql .= " OR  (k_attally IN $sfreund_ally ";
                $sql .= "AND   k_ally    IN $sfeind_ally)) ";
                $sql .= "AND v_bez  = '" . $rowS['v_bez'] . "' ";
                $sql .= "AND (v_art = 'A' OR v_art = 'D') ";
//      echo $sql . '<br>';
                $selectA = $db->db_query($sql);
                $hAnz = 0;
                if ($rowA = $db->db_fetch_array($selectA)) {
                    $hAnz = $rowA['s_anzs'];
                }
                $db->db_free_result($selectA);
                $hGes += $hAnz;
                if (!$BB) {
                    echo "    <td class='$hclass right'>" . number($hAnz, 'R');
                } else {
                    echo "    [td][right] " . number($hAnz, 'R') . "[/right][/td]";
                }
            }
            // Feind-Summen je Schiff
            if (!$BB) {
                echo "    <td class='menu'>";
            }
            if (!$BB) {
                echo "    <td class='$hclass right'>" . number($hGes, 'R');
                echo "</tr>";
            } else {
                echo "    [td][right][b]" . number($hGes, 'R') . "[/b][/right][/td]";
                echo "[/tr]<br>";
            }
        }
    }
    $db->db_free_result($selectS);
}

function verluste_jeAlly_V()
{
    global $db;
    global $a_id;
    global $a_freund;
    global $a_feind;
    global $db_tb_kb_kaputt;
    global $db_tb_kb_kb;
    global $z;
    global $BB;

    $sfreund_ally = '(';
    for ($i = 0; $i < count($a_freund); $i++) {
        if (strlen($sfreund_ally) > 1) {
            $sfreund_ally .= ', ';
        }
        $sfreund_ally .= "'" . $a_freund[$i] . "'";
    }
    $sfreund_ally .= ')';
    $sfeind_ally = '(';
    for ($i = 0; $i < count($a_feind); $i++) {
        if (strlen($sfeind_ally) > 1) {
            $sfeind_ally .= ', ';
        }
        $sfeind_ally .= "'" . $a_feind[$i] . "'";
    }
    $sfeind_ally .= ')';

    if (!$BB) {
        echo "<tr>";
        echo "    <td class='titlebg left' style='width:290px;'>Verteidigungsanlagen";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_freund[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "    <td class='menu' style='width:1px;'>";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_feind[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "</tr>";
    } else {
        echo "[tr]";
        echo "    [td][b]Verteidigungsanlagen[/b][/td]";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "  [td][right][b]" . $a_freund[$i] . "[/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "    [td] | [/td]";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "  [td][right][b]" . $a_feind[$i] . "[/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "[/tr]<br>";
    }
    // alle betroffenen Verteidigungsanlagen einlesen
    $sql = "SELECT v_bez, sum(v_anzs) - sum(v_anze) as s_anzs ";
    $sql .= "FROM `$db_tb_kb_kaputt` ";
    $sql .= "WHERE w_id  = $a_id ";
    $sql .= "  AND v_art = 'P' ";
    $sql .= "  AND v_typ in ('E','F') ";
    $sql .= "GROUP BY v_bez ";
    $sql .= "ORDER BY v_bez ";
    $selectS = $db->db_query($sql);
    while ($rowS = $db->db_fetch_array($selectS)) {
        if ($rowS['s_anzs'] > 0) {
            $z++;
            if (!$BB) {
                echo "<tr>";
                echo "    <td class='menutop'>" . $rowS['v_bez'];
            } else {
                echo "[tr]";
                echo "    [td][b]" . $rowS['v_bez'] . "[/b][/td]";
            }
            if ($z % 2 == 0) {
                $hclass = 'wfreund';
            } else {
                $hclass = 'wfreund2';
            }
            $hGes = 0;
            for ($i = 0; $i < count($a_freund); $i++) {
                $sql = "SELECT sum(v_anzs) - sum(v_anze) as s_anzs ";
                $sql .= "FROM `$db_tb_kb_kaputt` a, `$db_tb_kb_kb` b ";
                $sql .= "WHERE a.w_id = $a_id ";
                $sql .= "AND   b.w_id = $a_id ";
                $sql .= "AND   a.k_id = b.k_id ";
                $sql .= "AND v_ally = '" . $a_freund[$i] . "' ";
                $sql .= "AND k_attally IN $sfeind_ally ";
                $sql .= "AND v_bez  = '" . $rowS['v_bez'] . "' ";
                $sql .= "AND v_art = 'P' ";
                $selectA = $db->db_query($sql);
                $hAnz = 0;
                if ($rowA = $db->db_fetch_array($selectA)) {
                    $hAnz = $rowA['s_anzs'];
                }
                $db->db_free_result($selectA);
                $hGes += $hAnz;
                if (!$BB) {
                    echo "    <td class='$hclass right'>" . number($hAnz, 'R');
                } else {
                    echo "    [td][right] " . number($hAnz, 'R') . "[/right][/td]";
                }
            }
            // Freund-Summen je Verteidigungsanlage
            if (!$BB) {
                echo "    <td class='menu'>";
            }
            if (!$BB) {
                echo "    <td class='$hclass right'>" . number($hGes, 'R');
                echo "    <td class='menu'>";
            } else {
                echo "    [td][right][b]" . number($hGes, 'R') . "[/b][/right][/td]";
                echo "    [td] | [/td]";
            }

            if ($z % 2 == 0) {
                $hclass = 'wfeind';
            } else {
                $hclass = 'wfeind2';
            }

            $hGes = 0;
            for ($i = 0; $i < count($a_feind); $i++) {
                $sql = "SELECT sum(v_anzs) - sum(v_anze) as s_anzs ";
                $sql .= "FROM `$db_tb_kb_kaputt` a, `$db_tb_kb_kb` b ";
                $sql .= "WHERE a.w_id = $a_id ";
                $sql .= "AND   b.w_id = $a_id ";
                $sql .= "AND   a.k_id = b.k_id ";
                $sql .= "AND v_ally = '" . $a_feind[$i] . "' ";
                $sql .= "AND k_attally IN $sfreund_ally ";
                $sql .= "AND v_bez  = '" . $rowS['v_bez'] . "' ";
                $sql .= "AND v_art = 'P' ";

                $selectA = $db->db_query($sql);
                $hAnz = 0;
                if ($rowA = $db->db_fetch_array($selectA)) {
                    $hAnz = $rowA['s_anzs'];
                }
                $db->db_free_result($selectA);
                $hGes += $hAnz;
                if (!$BB) {
                    echo "    <td class='$hclass right'>" . number($hAnz, 'R');
                } else {
                    echo "    [td][right] " . number($hAnz, 'R') . "[/right][/td]";
                }
            }
            // Feind-Summen je Verteidigungsanlage
            if (!$BB) {
                echo "    <td class='menu'>";
                echo "    <td class='$hclass right'>" . number($hGes, 'R');
                echo "</tr>";
            } else {
                echo "    [td][right][b]" . number($hGes, 'R') . "[/b][/right][/td]";
                echo "[/tr]<br>";
            }
        }
    }
    $db->db_free_result($selectS);
}

function verluste_jeAlly_F($aart)
{
    global $a_id;
    global $a_freund;
    global $a_feind;
    global $db, $db_tb_kb_kb;
    global $z;
    global $BB;

    $hbez ='';

    $sfreund_ally = '(';
    for ($i = 0; $i < count($a_freund); $i++) {
        if (strlen($sfreund_ally) > 1) {
            $sfreund_ally .= ', ';
        }
        $sfreund_ally .= "'" . $a_freund[$i] . "'";
    }
    $sfreund_ally .= ')';
    $sfeind_ally = '(';
    for ($i = 0; $i < count($a_feind); $i++) {
        if (strlen($sfeind_ally) > 1) {
            $sfeind_ally .= ', ';
        }
        $sfeind_ally .= "'" . $a_feind[$i] . "'";
    }
    $sfeind_ally .= ')';

    $hfarbg='';
    if ($aart == 'F') {
        $hbez   = 'Fakes';
        $hfarbg = 'G';
    }

    if (!$BB) {
        echo "<tr>";
        echo "    <td class='titlebg left' style='width:290px;'>$hbez";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_freund[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "    <td class='menu' style='width:1px;'>";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_feind[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "</tr>";
    } else {
        echo "[tr]";
        echo "    [td][b]" . $hbez . "[/b][/td]";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "  [td][right][b]" . $a_freund[$i] . "[/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "    [td] | [/td]";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "  [td][right][b]" . $a_feind[$i] . "[/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "[/tr]<br>";
    }

    // Angriffe geflogen/Angegriffen worden
    if ($aart == 'F') {
        $hbez = 'Gesamt Fakes geflogen';
    }
    if (!$BB) {
        echo "<tr>";
        echo "    <td class='menutop'>$hbez";
    } else {
        echo "[tr]";
        echo "    [td][b]" . $hbez . "[/b][/td]";
    }
    // Fakes je FreundAlly einlesen
    $z++;

    if ($z % 2 == 0) {
        $hclass = 'wfreund';
    } else {
        $hclass = 'wfreund2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_freund); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art = 'F' ";
        $sql .= "AND k_attally = '" . $a_freund[$i] . "' ";
        $sql .= "AND k_ally IN $sfeind_ally ";
        $sql .= "GROUP BY k_ort, k_time ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }
    // Fake Freund-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "    <td class='menu'>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "    [td] | [/td]";
    }
    // Fakes je FeindAlly einlesen
    if ($z % 2 == 0) {
        $hclass = 'wfeind';
    }
    if ($z % 2 != 0) {
        $hclass = 'wfeind2';
    }
    $hGes = 0;
    for ($i = 0; $i < count($a_feind); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art = 'F' ";
        $sql .= "AND k_attally = '" . $a_feind[$i] . "' ";
        $sql .= "AND k_ally IN $sfreund_ally ";
        $sql .= "GROUP BY k_ort, k_time ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }
    // Fake Feind-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
    }
    if (!$BB) {
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "</tr>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "[/tr]";
    }
}

function verluste_jeAlly_A($aart)
{
    global $db;
    global $a_id;
    global $a_freund;
    global $a_feind;
    global $db_tb_kb_kb;
    global $z;
    global $BB;

    $sfreund_ally = '(';
    for ($i = 0; $i < count($a_freund); $i++) {
        if (strlen($sfreund_ally) > 1) {
            $sfreund_ally .= ', ';
        }
        $sfreund_ally .= "'" . $a_freund[$i] . "'";
    }
    $sfreund_ally .= ')';
    $sfeind_ally = '(';
    for ($i = 0; $i < count($a_feind); $i++) {
        if (strlen($sfeind_ally) > 1) {
            $sfeind_ally .= ', ';
        }
        $sfeind_ally .= "'" . $a_feind[$i] . "'";
    }
    $sfeind_ally .= ')';

    if ($aart == 'A') {
        $hbez   = 'Angriffe';
        $hfarbg = 'G';
    } else {
        $hbez   = 'Verteidigungen';
        $hfarbg = 'R';
    }
    if (!$BB) {
        echo "<tr>";
        echo "    <td class='titlebg left' style='width:290px;'>$hbez";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_freund[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "    <td class='menu' style='width:1px;'>";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "    <td class='titlebg right' style='width:80px;'>$a_feind[$i]";
        }
        echo "    <td class='menu' style='width:1px;'>";
        echo "    <td class='titlebg right' style='width:60px;'><b>Summe</b>";
        echo "</tr>";
    } else {
        echo "[tr]";
        echo "    [td][b]" . $hbez . "[/b][/td]";
        for ($i = 0; $i < count($a_freund); $i++) {
            echo "  [td][right][b]" . $a_freund[$i] . "[/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "    [td] | [/td]";
        for ($i = 0; $i < count($a_feind); $i++) {
            echo "  [td][right][b]" . $a_feind[$i] . "[/b][/right][/td]";
        }
        echo "    [td][right][b]Summe[/b][/right][/td]";
        echo "[/tr]<br>";
    }
    // Angriffe geflogen/Angegriffen worden
    if ($aart == 'A') {
        $hbez = 'Gesamt Angriffe geflogen';
    }
    if ($aart == 'V') {
        $hbez = 'Gesamt angegriffen worden';
    }
    if (!$BB) {
        echo "<tr>";
        echo "    <td class='menutop'>$hbez";
    } else {
        echo "[tr]";
        echo "    [td][b]" . $hbez . "[/b][/td]";
    }
    // Angriffe je FreundAlly einlesen
    $z++;

    if ($z % 2 == 0) {
        $hclass = 'wfreund';
    } else {
        $hclass = 'wfreund2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_freund); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art <> 'F' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_ally IN $sfeind_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_attally IN $sfeind_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }
    // Freund-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "    <td class='menu'>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "    [td] | [/td]";
    }
    // Angriffe je FeindAlly einlesen
    if ($z % 2 == 0) {
        $hclass = 'wfeind';
    } else {
        $hclass = 'wfeind2';
    }
    $hGes = 0;
    for ($i = 0; $i < count($a_feind); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art <> 'F' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_ally IN $sfreund_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_attally IN $sfreund_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }
    // Feind-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
    }
    if (!$BB) {
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "</tr><tr></tr>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "[/tr]<br>[tr][/tr][tr][/tr]<br>";
    }
    // gewonnene Angriffe
    if ($aart == 'A') {
        $hbez   = 'davon gewonnen';
        $hfarbg = 'G';
    } else {
        $hbez   = 'davon gewonnen';
        $hfarbg = 'G';
    }
    if (!$BB) {
        echo "<tr>";
        echo "    <td class='menutop'>$hbez";
    } else {
        echo "[tr]";
        echo "    [td][b]" . $hbez . "[/b][/td]";
    }
    // Angriffe je FreundAlly einlesen
    $z++;

    if ($z % 2 == 0) {
        $hclass = 'wfreund';
    } else {
        $hclass = 'wfreund2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_freund); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art <> 'F' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_ally IN $sfeind_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_attally IN $sfeind_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        if ($aart == 'A') {
            $sql .= "HAVING min(k_sieg) = '1' ";
        } else {
            $sql .= "HAVING min(k_sieg) = '2' ";
        }
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }
    // Freund-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "    <td class='menu'>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "    [td] | [/td]";
    }

    // Angriffe je FeindAlly einlesen

    if ($z % 2 == 0) {
        $hclass = 'wfeind';
    } else {
        $hclass = 'wfeind2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_feind); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art <> 'F' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_ally IN $sfreund_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_attally IN $sfreund_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        if ($aart == 'A') {
            $sql .= "HAVING min(k_sieg) = '1' ";
        } else {
            $sql .= "HAVING min(k_sieg) = '2' ";
        }
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }
    // Feind-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "</tr>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "[/tr]<br>";
    }
    // verlorene Angriffe
    if ($aart == 'A') {
        $hbez   = 'davon verloren';
        $hfarbr = 'R';
    } else {
        $hbez   = 'davon verloren';
        $hfarbr = 'R';
    }
    if (!$BB) {
        echo "<tr>";
        echo "    <td class='menutop'>$hbez";
    } else {
        echo "[tr]";
        echo "    [td][b]" . $hbez . "[/b][/td]";
    }
    // Angriffe je FreundAlly einlesen
    $z++;

    if ($z % 2 == 0) {
        $hclass = 'wfreund';
    } else {
        $hclass = 'wfreund2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_freund); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art <> 'F' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_ally IN $sfeind_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_attally IN $sfeind_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        if ($aart == 'A') {
            $sql .= "HAVING min(k_sieg) = '2' ";
        } else {
            $sql .= "HAVING min(k_sieg) = '1' ";
        }
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbr);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbr) . "[/right][/td]";
        }
    }

    // Freund-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbr);
        echo "    <td class='menu'>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbr) . "[/b][/right][/td]";
        echo "    [td] | [/td]";
    }

    // Angriffe je FeindAlly einlesen
    if ($z % 2 == 0) {
        $hclass = 'wfeind';
    } else {
        $hclass = 'wfeind2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_feind); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art <> 'F' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_ally IN $sfreund_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_attally IN $sfreund_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        if ($aart == 'A') {
            $sql .= "HAVING min(k_sieg) = '2' ";
        } else {
            $sql .= "HAVING min(k_sieg) = '1' ";
        }
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbr);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbr) . "[/right][/td]";
        }
    }

    // Feind-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbr);
        echo "</tr><tr></tr>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbr) . "[/b][/right][/td]";
        echo "[/tr]<br>[tr][/tr][tr][/tr]<br>";
    }
    // Angriffe Raids
    if ($aart == 'A') {
        $hbez   = 'dabei geplündert';
        $hfarbg = 'G';
    } else {
        $hbez   = 'dabei geplündert worden';
        $hfarbg = 'R';
    }
    if (!$BB) {
        echo "<tr>";
        echo "    <td class='menutop'>$hbez";
    } else {
        echo "[tr]";
        echo "    [td][b]" . $hbez . "[/b][/td]";
    }

    // Angriffe je FreundAlly einlesen
    $z++;

    if ($z % 2 == 0) {
        $hclass = 'wfreund';
    } else {
        $hclass = 'wfreund2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_freund); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art <> 'F' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_ally IN $sfeind_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_attally IN $sfeind_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        $sql .= "HAVING sum(k_plueisen + k_plustahl + k_pluvv4a + k_pluchem ";
        $sql .= "+ k_plueis + k_pluwas + k_pluene) > 0 ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }

    // Freund-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "    <td class='menu'>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "    [td] | [/td]";
    }

    // Angriffe je FeindAlly einlesen
    if ($z % 2 == 0) {
        $hclass = 'wfeind';
    } else {
        $hclass = 'wfeind2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_feind); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art <> 'F' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_ally IN $sfreund_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_attally IN $sfreund_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        $sql .= "HAVING sum(k_plueisen + k_plustahl + k_pluvv4a + k_pluchem ";
        $sql .= "+ k_plueis + k_pluwas + k_pluene) > 0 ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }
    // Feind-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "</tr>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "[/tr]<br>";
    }

    // Angriffe Bombs
    if ($aart == 'A') {
        $hbez = 'dabei gebombt';
    } else {
        $hbez = 'dabei gebombt worden';
    }
    if (!$BB) {
        echo "<tr>";
        echo "    <td class='menutop'>$hbez";
    } else {
        echo "[tr]";
        echo "    [td][b]" . $hbez . "[/b][/td]";
    }

    // Angriffe je FreundAlly einlesen
    $z++;
    if ($z % 2 == 0) {
        $hclass = 'wfreund';
    } else {
        $hclass = 'wfreund2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_freund); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art = 'B' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_ally IN $sfeind_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_attally IN $sfeind_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }

    // Freund-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "    <td class='menu'>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "    [td] | [/td]";
    }

    // Angriffe je FeindAlly einlesen
    if ($z % 2 == 0) {
        $hclass = 'wfeind';
    } else {
        $hclass = 'wfeind2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_feind); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art = 'B' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_ally IN $sfreund_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_attally IN $sfreund_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }

    // Feind-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "</tr>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "[/tr]<br>";
    }

    // Angriffe Basenplops
    if ($aart == 'A') {
        $hbez = 'dabei KB/Ressbase geplopt';
    } else {
        $hbez = 'dabei&nbsp;KB/Ressbase&nbsp;geplopt&nbsp;worden';
    }
    if (!$BB) {
        echo "<tr>";
        echo "    <td class='menutop'>$hbez";
    } else {
        echo "[tr]";
        echo "    [td][b]" . $hbez . "[/b][/td]";
    }

    // Angriffe je FreundAlly einlesen
    $z++;
    if ($z % 2 == 0) {
        $hclass = 'wfreund';
    } else {
        $hclass = 'wfreund2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_freund); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art = 'P' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_ally IN $sfeind_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_freund[$i] . "' ";
            $sql .= "AND k_attally IN $sfeind_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }

    // Freund-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "    <td class='menu'>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "    [td] | [/td]";
    }

    // Angriffe je FeindAlly einlesen
    if ($z % 2 == 0) {
        $hclass = 'wfeind';
    } else {
        $hclass = 'wfeind2';
    }

    $hGes = 0;
    for ($i = 0; $i < count($a_feind); $i++) {
        $sql = "SELECT count(*) as k_anz ";
        $sql .= ", k_ort, left(cast(k_time as char),7) as k_time ";
        $sql .= "FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id = $a_id ";
        $sql .= "AND k_art = 'P' ";
        if ($aart == 'A') {
            $sql .= "AND k_attally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_ally IN $sfreund_ally ";
        } else {
            $sql .= "AND k_ally = '" . $a_feind[$i] . "' ";
            $sql .= "AND k_attally IN $sfreund_ally ";
        }
        $sql .= "GROUP BY k_ort, k_time ";
        $hAnz = 0;
        $selectA = $db->db_query($sql);
        while ($rowA = $db->db_fetch_array($selectA)) {
            $hAnz += 1; // $rowA['k_anz'];
        }
        $db->db_free_result($selectA);
        $hGes += $hAnz;
        if (!$BB) {
            echo "    <td class='$hclass right'>" . number($hAnz, $hfarbg);
        } else {
            echo "    [td][right] " . number($hAnz, $hfarbg) . "[/right][/td]";
        }
    }

    // Feind-Summen
    if (!$BB) {
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>" . number($hGes, $hfarbg);
        echo "</tr>";
    } else {
        echo "    [td][right][b]" . number($hGes, $hfarbg) . "[/b][/right][/td]";
        echo "[/tr]<br>";
    }
}

function angriffe_jeFC($art)
{
    global $a_id;
    global $a_freund;
    global $a_feind;
    global $db, $db_tb_kb_kb;
    global $FCanz;

    $sfreund_ally = '(';
    for ($i = 0; $i < count($a_freund); $i++) {
        if (strlen($sfreund_ally) > 1) {
            $sfreund_ally .= ', ';
        }
        $sfreund_ally .= "'" . $a_freund[$i] . "'";
    }
    $sfreund_ally .= ')';

    $sfeind_ally = '(';
    for ($i = 0; $i < count($a_feind); $i++) {
        if (strlen($sfeind_ally) > 1) {
            $sfeind_ally .= ', ';
        }
        $sfeind_ally .= "'" . $a_feind[$i] . "'";
    }
    $sfeind_ally .= ')';

    echo "<tr>";
    echo "    <td class='titlebg left' style='width:20px;'>&nbsp;";
    echo "    <td class='menu' style='width:1px;'>";
    echo "    <td class='titlebg left' style='width:80px;'>Allianz";
    echo "    <td class='titlebg left' style='width:120px;'>FC";
    echo "    <td class='menu' style='width:1px;'>";
    echo "    <td class='titlebg right' style='width:80px;' ><b>Angriffe</b>";
    echo "    <td class='menu' style='width:1px;'>";
    echo "    <td class='titlebg right' style='width:80px;' ><b>Fakes</b>";
    echo "    <td class='titlebg right' style='width:80px;' ><b>Plops</b>";
    echo "    <td class='titlebg right' style='width:80px;' ><b>stationierte</b>";
    echo "    <td class='titlebg right' style='width:80px;' ><b>Bombs</b>";
    echo "    <td class='titlebg right' style='width:160px;' ><b>gebombte Bauzeit in HH:MM</b>";
    echo "</tr>";
    // Fakes je FreundAlly einlesen
    $hAngriff = 0;
    $hBomb    = 0;
    $hFake    = 0;
    $hPlopp   = 0;
    $hStat    = 0;
    $hSpieler = '';
    $hAlly    = '';
    $htime    = 99;
    $hort     = '';
    $hart     = '';
    $sRaids   = Array(0, 0, 0, 0, 0, 0, 0);
    if ($art == 'F') {
        $hclass = 'wfreund2';
    } else {
        $hclass = 'wfeind2';
    }
    $sql = "SELECT k_attspiel, k_attally, k_art, ";
    $sql .= "k_ort, left(cast(k_time as char),7) as k_time, ";
    $sql .= "sum(k_plueisen) as k_plueisen, sum(k_plustahl) as k_plustahl, ";
    $sql .= "sum(k_pluvv4a) as k_pluvv4a, sum(k_pluchem) as k_pluchem, ";
    $sql .= "sum(k_plueis) as k_plueis, sum(k_pluwas) as k_pluwas, ";
    $sql .= "sum(k_pluene) as k_pluene ";
    $sql .= "FROM `$db_tb_kb_kb` ";
    $sql .= "WHERE w_id = $a_id ";
    if ($art == 'F') {
        $sql .= "AND k_typ = 'E' "; // Angriff auf Enemi
        $sql .= "AND k_ally IN $sfeind_ally ";
        $sql .= "AND k_attally IN $sfreund_ally ";
    } else {
        $sql .= "AND k_typ = 'F' "; // Angriff auf Freund
        $sql .= "AND k_ally IN $sfreund_ally ";
        $sql .= "AND k_attally IN $sfeind_ally ";
    }
    $sql .= "GROUP BY  k_attally, k_attspiel, k_ort, k_time, k_art ";
    $sql .= "ORDER BY k_attally, k_attspiel, k_ort, k_time, k_art ";
    $selectA = $db->db_query($sql);
    while ($rowA = $db->db_fetch_array($selectA)) {
        if ($rowA['k_attspiel'] != $hSpieler) {
            if (!empty($hSpieler)) {
                $FCanz++;
                angriffe_jeFC_zeile($hSpieler, $hAlly, $hAngriff, $hBomb, $hFake, $hPlopp, $hStat, $hclass, $FCanz, $sRaids);
                //raids_jeFC_zeile($hSpieler,$hAlly,$sRaids,$hclass,$FCanz);
                if ($rowA['k_attally'] != $hAlly) {
                    if ($art == 'F') {
                        if ($hclass == 'wfreund') {
                            $hclass = 'wfreund2';
                        } else {
                            $hclass = 'wfreund';
                        }
                    } else {
                        if ($hclass == 'wfeind') {
                            $hclass = 'wfeind2';
                        } else {
                            $hclass = 'wfeind';
                        }
                    }
                }
            }
            $hAngriff = 0;
            $hBomb    = 0;
            $hFake    = 0;
            $hPlopp   = 0;
            $hStat    = 0;
            $hSpieler = trim($rowA['k_attspiel']);
            $hAlly    = trim($rowA['k_attally']);
            $sRaids   = Array(0, 0, 0, 0, 0, 0, 0);
        }
        if ($htime != $rowA['k_time'] || $hort != $rowA['k_ort'] || $hart != $rowA['k_art']) {
            $hAngriff++;
            if ($rowA['k_art'] == 'B') {
                $hBomb++;
            }
            if ($rowA['k_art'] == 'F') {
                $hFake++;
            }
            if ($rowA['k_art'] == 'P') {
                $hPlopp++;
            }
            if ($rowA['k_art'] == 'S') {
                $hStat++;
            }
        }
        $htime = $rowA['k_time'];
        $hort  = $rowA['k_ort'];
        $hart  = $rowA['k_art'];
        $sRaids['0'] += $rowA['k_plueisen'];
        $sRaids['1'] += $rowA['k_plustahl'];
        $sRaids['2'] += $rowA['k_pluvv4a'];
        $sRaids['3'] += $rowA['k_pluchem'];
        $sRaids['4'] += $rowA['k_plueis'];
        $sRaids['5'] += $rowA['k_pluwas'];
        $sRaids['6'] += $rowA['k_pluene'];
    }
    $db->db_free_result($selectA);
    // letzten Spieler ausgeben
    $FCanz++;
    angriffe_jeFC_zeile($hSpieler, $hAlly, $hAngriff, $hBomb, $hFake, $hPlopp, $hStat, $hclass, $FCanz, $sRaids);
//  raids_jeFC_zeile($hSpieler,$hAlly,$sRaids,$hclass,$FCanz);
}

function angriffe_jeFC_zeile($hSpieler, $hAlly, $hAngriff, $hBomb, $hFake, $hPlopp, $hStat, $hclass, $FCanz, $sRaids)
{
    global $a_id;
    global $db, $db_tb_kb_kaputt, $db_tb_kb_kb, $db_tb_gebaeude;
    global $FC_StatS;

    if ($hBomb > 0) {
        $sql = "SELECT sum((v_anzs - v_anze) * g.dauer) as bzeit ";
        $sql .= "FROM `$db_tb_kb_kaputt` k ";
        $sql .= "LEFT OUTER join `$db_tb_gebaeude` g ";
        $sql .= "ON v_bez = g.name ";
        $sql .= "WHERE w_id  = $a_id ";
        $sql .= "  AND v_art = 'G' ";
        $sql .= "  AND k_id in (SELECT k_id from $db_tb_kb_kb ";
        $sql .= "WHERE w_id  = $a_id ";
        $sql .= "  AND k_art = 'B' ";
        $sql .= "  AND k_attspiel = '$hSpieler' )";
        $selectG = $db->db_query($sql);
        $rowG = $db->db_fetch_array($selectG);
        $db->db_free_result($selectG);
    } else {
        $rowG['bzeit'] = 0;
    }
    echo "<tr>";

    if ($FC_StatS) {
        echo "    <td class='$hclass'>&nbsp;\n";
    } else {
        echo "    <td class='$hclass center'><a title='Alle Angriffe dieses FC anzeigen' href=\"javascript:Collapse('$FCanz');\"><img src='".BILDER_PATH."plus.gif' alt='' border='0' id='collapse_$FCanz'></a>\n";
    }
    echo "    <td class='menu'>";
    echo "    <td class='$hclass left'>" . $hAlly;
    echo "    <td class='$hclass left'>" . $hSpieler;
    echo "    <td class='menu'>";
    echo "    <td class='$hclass right'>" . number($hAngriff, 'G');
    echo "    <td class='menu'>";
    echo "    <td class='$hclass right'>" . number($hFake, 'G');
    echo "    <td class='$hclass right'>" . number($hPlopp, 'R');
    echo "    <td class='$hclass right'>" . number($hStat, 'G');
    echo "    <td class='$hclass right'>" . number($hBomb, 'R');
    echo "    <td class='$hclass right'>" . dauer_hhmm($rowG['bzeit']);
    echo "</tr>";

    if (!$FC_StatS) {
        echo "<tr id='row_$FCanz' style='display:none;'><td colspan=12>";
        angriffe_jeFC_coords($hSpieler, $sRaids, $hclass);
        echo "</td></tr>";
    }
}

function angriffe_jeFC_coords($hSpieler, $sRaids, $hclass)
{
    global $a_id;
    global $db, $db_tb_kb_kb;
    global $fake;
    global $mclass, $mtoggle, $sclass;
    global $modulname;
    global $k_zeigen;

    $K_Long   = false;
    $k_zeigen = '&FC_Stat=FC%20Statistik';
    $sql      = "SELECT * from `" . $db_tb_kb_kb . "` ";
    $sql .= " WHERE w_id = $a_id ";
    $sql .= " AND k_typ <> 'X' ";
    $sql .= " AND k_attspiel = '$hSpieler' ";
    $sql .= " ORDER BY k_time desc";
    $select = $db->db_query($sql);

    echo "<form method='POST' action='index.php?action=" . $modulname . "&amp;a_id=$a_id" . $k_zeigen . "' enctype='multipart/form-data'>\n";
    echo "<input name='a_id' value=$a_id type='hidden'>";
    echo "<table cellspacing='1' cellpadding='1' border='0' width='100%' overflow:hidden>";
    if ($sRaids['0'] + $sRaids['1'] + $sRaids['2'] + $sRaids['3'] + $sRaids['4'] + $sRaids['5'] + $sRaids['6'] > 0) {
        echo "<tr>";
        echo "    <td class='$hclass' colspan='5'>&nbsp;";
        echo "    <td class='$hclass left'><small>Raidsumme</small>";
        echo "    <td class='$hclass right' colspan='7'><small>";
        echo      "Eisen: <span class=doc_eisen>" . number($sRaids['0']) . "</span>&nbsp;";
        echo      "Stahl: <span class=doc_stahl>" . number($sRaids['1']) . "</span>&nbsp;";
        echo      "VV4A: <span class=doc_vv4a>" . number($sRaids['2']) . "</span>&nbsp;";
        echo      "Chem: <span class=doc_chemie>" . number($sRaids['3']) . "</span>&nbsp;";
        echo      "Eis: <span class=doc_eis>" . number($sRaids['4']) . "</span>&nbsp;";
        echo      "Wasser: <span class=doc_wasser>" . number($sRaids['5']) . "</span>&nbsp;";
        echo      "Ene: <span class=doc_ene>" . number($sRaids['6']) . "</span></small";
        echo "</tr>\n";
    }
    ausgabe_ueber($K_Long, 'E', 'N');

    while ($rowKB = $db->db_fetch_array($select)) {
        if ($fake != 'off' || $rowKB['k_art'] != 'F') {
            if ($rowKB['k_mauer'] == 'X') {
                $mclass  = 'maurer';
                $mtoggle = 'N';
            } else {
                $mclass  = '';
                $mtoggle = 'J';
            }
            if ($rowKB['k_sieg'] == '1') {
                $sclass = 'doc_green';
            } else {
                $sclass = 'doc_red';
            }
            $ftoggle = '';
            if ($rowKB['k_art'] == 'F') {
                $ftoggle = 'N';
            }
            if ($rowKB['k_art'] == '') {
                $ftoggle = 'J';
            }
            ausgabe_zeile($rowKB, 'E', '', $K_Long, $rowKB['k_typ'], $ftoggle, 'N');
        }
    }
    $db->db_free_result($select);
    echo "</table>";
    echo "</form>";
}

function raids_jeFC_zeile($hSpieler, $hAlly, $sRaids, $hclass, $FCanz)
{
//    $sRaids['0'] += $rowA['k_plueisen'];
//    $sRaids['1'] += $rowA['k_plustahl'];
//    $sRaids['2'] += $rowA['k_pluvv4a'];
//    $sRaids['3'] += $rowA['k_pluchem'];
//    $sRaids['4'] += $rowA['k_plueis'];
//    $sRaids['5'] += $rowA['k_pluwas'];
//    $sRaids['6'] += $rowA['k_pluene'];
    if ($sRaids['0'] + $sRaids['1'] + $sRaids['2'] + $sRaids['3'] + $sRaids['4'] + $sRaids['5'] + $sRaids['6'] > 0) {
        echo "<tr>";
        echo "    <td class='$hclass center'>&nbsp;";
        echo "    <td class='menu'>";
        echo "    <td class='$hclass left'>&nbsp;";
        echo "    <td class='$hclass left'><small>Raidsumme $hSpieler</small>";
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right'>&nbsp;";
        echo "    <td class='menu'>";
        echo "    <td class='$hclass right' colspan='5'><small>";
        echo      "Eisen: <span class=doc_eisen>" . number($sRaids['0']) . "</span>&nbsp;";
        echo      "Stahl: <span class=doc_stahl>" . number($sRaids['1']) . "</span>&nbsp;";
        echo      "VV4A: <span class=doc_vv4a>" . number($sRaids['2']) . "</span>&nbsp;";
        echo      "Chem: <span class=doc_chemie>" . number($sRaids['3']) . "</span>&nbsp;";
        echo      "Eis: <span class=doc_eis>" . number($sRaids['4']) . "</span>&nbsp;";
        echo      "Wasser: <span class=doc_wasser>" . number($sRaids['5']) . "</span>&nbsp;";
        echo      "Ene: <span class=doc_ene>" . number($sRaids['6']) . "</span></small";
        echo "</tr>\n";
    }
}

function kb_parser($k_id)
{
    if ($k_id > 0) {
        global $db, $db_tb_kb_kb, $db_tb_kb_kaputt, $db_tb_gebaeude;

        global $a_id;
        global $atime;
        global $BB;

        // Aufbereitung als BB-Code
        $BB = true;

        // betreffenden KB einlesen
        $sql = "SELECT * FROM `$db_tb_kb_kb` ";
        $sql .= "WHERE w_id  = $a_id ";
        $sql .= "  AND k_id  = $k_id ";
        $selectKB = $db->db_query($sql);
        $rowKB = $db->db_fetch_array($selectKB);
        $db->db_free_result($selectKB);

        // alle betroffenen Schiffe einlesen
        $sql = "SELECT v_klasse, v_bez, v_art, 1 as s_art ";
        $sql .= "FROM `$db_tb_kb_kaputt` ";
        $sql .= "WHERE w_id  = $a_id ";
        $sql .= "  AND k_id  = $k_id ";
        $sql .= "  AND (v_art = 'A' OR v_art = 'D') ";
        $sql .= " UNION ";
        $sql .= "SELECT v_klasse, v_bez, v_art, 2 as s_art ";
        $sql .= "FROM `$db_tb_kb_kaputt` ";
        $sql .= "WHERE w_id  = $a_id ";
        $sql .= "  AND k_id  = $k_id ";
        $sql .= "  AND v_art = 'P' ";
        $sql .= "GROUP BY v_klasse, v_bez, v_art ";
        $sql .= "ORDER BY s_art, v_klasse, v_bez, v_art ";
        $selectS = $db->db_query($sql);

        $vbez  = '';
        $hangr = "Angreifer";
        $hvert = "Verteidiger";
        $hwin  = '';
        $oally = '';
        if (!empty($rowKB['k_ally'])) {
            $oally = '[' . $rowKB['k_ally'] . '] ';
        }
        $aally = '';
        if (!empty($rowKB['k_attally'])) {
            $aally = '[' . $rowKB['k_attally'] . '] ';
        }

        $aspielA = explode(';', $rowKB['k_attspiel']);
//  if (isset ($aspielA[1])) $aspiel .= ' ...';
        $aspiel = $aspielA[0];
        for ($i = 1; $i < count($aspielA); $i++) {
            $aspiel .= ', ';
            $aspiel .= $aspielA[$i];
        }

        if ($rowKB['k_sieg'] == 1) {
            $hangr = "[color=green][b]Angreifer[/b][/color]";
            $hwin  = "[b]" . $rowKB['k_opfer'] . "[/b] wurde geschlagen von dem überlegenen [color=green][b]Aggressor[/b][/color]";
        }
        if ($rowKB['k_sieg'] == 2) {
            $hvert = "[color=red][b]Verteidiger[/b][/color]";
            $hwin  = "[color=red][b]" . $rowKB['k_opfer'] . "[/b][/color] war unerwartet überlegen und hat diese Raumschlacht gewonnen";
        }
        $htime = kb_parser_time($rowKB['k_time']);

        echo "<p>[quote][table]";
        echo "[tr][td colspan=2]Kampf auf [[b]" . $rowKB['k_ort'] . "[/b]] am [b]" . date("d.m.y", $rowKB['k_time']) . "[/b]";
        echo " um [b]" . $htime . "[/b] Uhr[/td][/tr]<br>";
        if ($rowKB['k_art'] == 'S') {
            echo "[tr][td]Stationierte Flotte von[/td]";
        } else {
            echo "[tr][td]Planetenbesitzer[/td]";
        }
        echo "[td]: [b][nobbc]" . $oally . "[/nobbc]" . $rowKB['k_opfer'] . "[/b][/td][/tr]<br>";
        echo "[tr][td]phieser Angreifer " . $atime . "war[/td]";
        echo "[td]: [b][nobbc]" . $aally . "[/nobbc]" . $aspiel . "[/b][/td][/tr]<br>";
        echo "[/table]<br>[table]";
        echo "[tr][td]" . $hwin . "[/td][/tr]<br>";
        if ($rowKB['k_art'] == 'P') {
            echo "[tr][td]Die Basis wurde geploppt, und machte [color=red][b]Plöpp[/b][/color]";
            if ($rowKB['k_attally'] == 'PLEX') {
                echo " (wie ein Plexbier)";
            }
            echo "[/td][/tr]<br>";
        }
        echo "[tr][td]&nbsp;[/td][/tr]<br>";
        echo "[tr][td]&nbsp;[/td][/tr]<br>";
        if (!empty($rowKB['k_msg'])) {
            echo "[tr][td][b][i][color=maroon]" . trim($rowKB['k_msg']) . "[/color][/i][/b][/td][/tr]<br>";
            echo "[tr][td]&nbsp;[/td][/tr]<br>";
            echo "[tr][td]&nbsp;[/td][/tr]<br>";
        }
        echo "[tr][td]KB-Link: [url]" . $rowKB['k_kb'] . "[/url][/td][/tr]<br>";
        echo "[tr][td]&nbsp;[/td][/tr]<br>";
        echo "[/table]<br>[table]";
        echo "[tr][td]&nbsp;[/td][td]&nbsp;|&nbsp;[/td]";
        echo "[td colspan=3][right]" . $hvert . "[/right][/td][td]&nbsp;|&nbsp;[/td][td colspan=3][right]" . $hangr . "[/right][/td][/tr]<br>";
        echo "[tr][td colspan=9][hr][/td][/tr]<br>";
        echo "[tr][td][b]Schiffe/Verteidigung[/b][/td][td]&nbsp;|&nbsp;[/td]";
        echo "[td][right][b]vorher[/b][/right][/td][td][right][b]zerst&ouml;rt[/b][/right][/td][td][right][b]Rest[/b][/right][/td]";
        echo "[td]&nbsp;|&nbsp;[/td]";
        echo "[td][right][b]vorher[/b][/right][/td][td][right][b]zerst&ouml;rt[/b][/right][/td][td][right][b]Rest[/b][/right][/td]";
        echo "[/tr]<br>";
        while ($rowS = $db->db_fetch_array($selectS)) {
            $hanzA = array(0, 0, 0);
            $hbez  = $rowS['v_bez'];

            if ($vbez != $hbez) {
                if ($rowS['v_art'] != 'P') {
                    $hanzA = kb_parser_bez($hbez, $a_id, $k_id, 'A');
                    $hanzD = kb_parser_bez($hbez, $a_id, $k_id, 'D');
                } else {
                    $hanzA[0] = 'SPACE';
                    $hanzA[1] = 'SPACE';
                    $hanzA[2] = 'SPACE';
                    $hanzD    = kb_parser_bez($hbez, $a_id, $k_id, 'P');
                }
                $abez = str_replace("(Hyperraumtransporter Klasse 1)", "&nbsp;", $hbez);
                $abez = str_replace("(Hyperraumtransporter Klasse 2)", "&nbsp;", $abez);
                $abez = str_replace("(kleiner Carrier)", "&nbsp;", $abez);
                $abez = str_replace("(Carrier)", "&nbsp;", $abez);
                $abez = str_replace("(Systransporter Klasse 1)", "&nbsp;", $abez);
                $abez = str_replace("(Systransporter Klasse 2)", "&nbsp;", $abez);
                echo "[tr][td]" . $abez . "[/td][td]&nbsp;|&nbsp;[/td]";
                echo "[td]" . ($hanzD[0] == 'SPACE' ? '&nbsp;' : '[right]' . number($hanzD[0]) . '[/right]') . "[/td]";
                echo "[td]" . ($hanzD[2] == 'SPACE' ? '&nbsp;' : '[right][b]' . number($hanzD[2], 'R') . '[/b][/right]') . "[/td]";
                echo "[td]" . ($hanzD[1] == 'SPACE' ? '&nbsp;' : '[right]' . number($hanzD[1], 'G') . '[/right]') . "[/td]";
                echo "[td]&nbsp;|&nbsp;[/td]";
                echo "[td]" . ($hanzA[0] == 'SPACE' ? '&nbsp;' : '[right]' . number($hanzA[0]) . '[/right]') . "[/td]";
                echo "[td]" . ($hanzA[2] == 'SPACE' ? '&nbsp;' : '[right][b]' . number($hanzA[2], 'R') . '[/b][/right]') . "[/td]";
                echo "[td]" . ($hanzA[1] == 'SPACE' ? '&nbsp;' : '[right]' . number($hanzA[1], 'G') . '[/right]') . "[/td]";
                echo "[/tr]<br>";
            }
            $vbez = $hbez;
        }
        $db->db_free_result($selectS);
        // Zerstörte und Geraidete Rohstoffe
        echo "[tr][td colspan=9][hr][/td][/tr]<br>";
        echo "[tr][td][b]Rohstoffe[/b][/td][td]&nbsp;|&nbsp;[/td]";
        echo "[td][right]&nbsp;[/right][/td][td][right][b]zerst&ouml;rt[/b][/right][/td][td]&nbsp;[/td]";
        echo "[td]&nbsp;|&nbsp;[/td]";

        echo "[td][right][b]raid[/b][/right][/td][td][right][b]zerst&ouml;rt[/b][/right][/td][td][right][b]+/-[/b][/right][/td]";
        echo "[/tr]<br>";
        kb_parser_res('Eisen', $rowKB['k_plueisen'], $rowKB['k_atteisen'], $rowKB['k_defeisen']);
        kb_parser_res('Stahl', $rowKB['k_plustahl'], $rowKB['k_attstahl'], $rowKB['k_defstahl']);
        kb_parser_res('VV4A', $rowKB['k_pluvv4a'], $rowKB['k_attvv4a'], $rowKB['k_defvv4a']);
        kb_parser_res('chem. Elemente', $rowKB['k_pluchem'], $rowKB['k_attchem'], $rowKB['k_defchem']);
        kb_parser_res('Eis', $rowKB['k_plueis'], $rowKB['k_atteis'], $rowKB['k_defeis']);
        kb_parser_res('Wasser', $rowKB['k_pluwas'], $rowKB['k_attwas'], $rowKB['k_defwas']);
        kb_parser_res('Energie', $rowKB['k_pluene'], $rowKB['k_attene'], $rowKB['k_defene']);

        // Gebombte Gebäude
        if ($rowKB['k_art'] == 'B') {
            echo "[tr][td colspan=9][hr][/td][/tr]";
            if ($rowKB['k_rauch'] > 0) {
                echo "[tr][td colspan=9][i]Die Bombentrefferchance lag bei [b]" . number($rowKB['k_rauch']) . "%[/b][/i][/td][/tr]";
            }
            echo "[tr][td][b]Geb&auml;ude[/b][/td][td]&nbsp;|&nbsp;[/td]";
            echo "[td colspan=2][right][b]zerstört[/b][/td]";
            echo "[td]&nbsp;[/td]";
            echo "[td]&nbsp;|&nbsp;[/td]";
            echo "[td colspan=3][right][b]Bauzeit in hh:mm[/b][/td]";
            echo "[/tr]<br>";

            $sql = "SELECT v_bez, v_anzs, g.category, g.idcat, g.dauer ";
            $sql .= "FROM `$db_tb_kb_kaputt` k ";
            $sql .= "LEFT OUTER join `$db_tb_gebaeude` g ";
            $sql .= "ON v_bez = g.name ";
            $sql .= "WHERE w_id  = $a_id ";
            $sql .= "  AND k_id  = $k_id ";
            $sql .= "  AND v_art = 'G' ";
            $sql .= "ORDER BY g.category, g.idcat, v_bez ";

            $selectG = $db->db_query($sql);
            $gzeit = 0;
            while ($rowG = $db->db_fetch_array($selectG)) {
                $hdauer = 0;
                if (isset($rowG['dauer'])) {
                    $hdauer = $rowG['dauer'];
                }
                $hbez = $rowG['v_bez'];
                $hcat = $rowG['category'];
                echo "[tr][td]" . gebauede_farbig($hbez, $hcat) . "[/td]";
                echo "[td]&nbsp;|&nbsp;[/td]";
                echo "[td colspan=2][right][b]" . number($rowG['v_anzs'], 'R') . "[/b][/right][/td]";
                echo "[td]&nbsp;[/td]";
                echo "[td]&nbsp;|&nbsp;[/td]";

                $bzeit = ($hdauer * $rowG['v_anzs']);
                $gzeit += $bzeit;
                echo "[td colspan=3][right]" . dauer_hhmm($bzeit) . "[/right][/td]";
                echo "[/tr]<br>";
            }
            $db->db_free_result($selectG);
            echo "[tr][td colspan=9][hr][/td][/tr]<br>";
            echo "[tr][td colspan=6]Gesamt zerst&ouml;rte Bauzeit[/td]";
            echo "[td colspan=3][right][b]" . dauer_hhmm($gzeit) . "[/b][/right][/td][/tr]<br>";
            echo "[tr][td colspan=9]" . number($rowKB['k_bombev'], 'R');
            if ($rowKB['k_attally'] == 'Kilrathy') {
                echo " Leute wurden von leeren Plexbierflaschen erschlagen[/td][/tr]<br>";
            } else {
                echo " Leute wurden von Bomben erschlagen[/td][/tr]<br>";
            }
        }
        echo "[/table][/quote]</p>";

        $sql = "UPDATE `$db_tb_kb_kb` SET k_forum = 'J' ";
        $sql .= "WHERE w_id  = $a_id ";
        $sql .= "  AND k_id  = $k_id ";
        $selectForum = $db->db_query($sql);
    }
}

function kb_parser_time($htime)
{
    global $atime;

    $hh    = date("H", $htime);
    $hcol  = 'gray';
    $atime = '';
    if ($hh > 6 && $hh <= 14) {
        $hcol  = 'green';
        $atime = 'am Morgen ';
    }
    if ($hh > 14 && $hh <= 20) {
        $hcol  = 'teal';
        $atime = 'am Nachmittag ';
    }
    if (($hh > 20 && $hh <= 24) || ($hh >= 0 && $hh <= 3)) {
        $hcol  = 'red';
        $atime = 'am Abend ';
    }
    if ($hh > 3 && $hh <= 6) {
        $hcol  = 'maroon';
        $atime = 'mitten in der Nacht ';
    }
    $htime = "[color=" . $hcol . "]" . date("H:i:s", $htime) . "[/color]";

    return $htime;
}

function kb_parser_res($hbez, $hplu, $hatt, $hdef)
{
    if ($hplu > 0 || $hatt > 0 || $hdef > 0) {
        echo "[tr][td]" . $hbez . "[/td][td]&nbsp;|&nbsp;[/td]";
        echo "[td]&nbsp;[/td][td]" . ($hdef == 0 ? '&nbsp;' : '[right][b]' . number($hdef, 'R') . '[/b][/right]') . "[/td][td]&nbsp;[/td]";
        echo "[td]&nbsp;|&nbsp;[/td]";
        echo "[td]" . ($hplu == 0 ? '&nbsp;' : '[right]' . number($hplu, 'G') . '[/right]') . "[/td][td]" . ($hatt == 0 ? '&nbsp;' : '[right][b]' . number($hatt, 'R') . '[/b][/right]') . "[/td][td]";
        if ($hplu > 0 && $hatt > 0) {
            echo '[right]' . number($hplu - $hatt, 'F') . '[/right]';
        } else {
            echo "&nbsp;";
        }
        echo "[/td][/tr]<br>";
    }
}

function kb_parser_bez($hbez, $a_id, $k_id, $hart)
{
    global $db, $db_tb_kb_kaputt;

    $sql = "SELECT sum(v_anzs) as v_anzs, sum(v_anze) as v_anze ";
    $sql .= "FROM `$db_tb_kb_kaputt` ";
    $sql .= "WHERE w_id  = $a_id ";
    $sql .= "  AND k_id  = $k_id ";
    $sql .= "  AND v_typ in ('E','F') ";
    $sql .= "  AND v_art = '$hart' ";
    $sql .= "  AND v_bez = '$hbez' ";
    $selectZ = $db->db_query($sql);
    $rowZ = $db->db_fetch_array($selectZ);
    $db->db_free_result($selectZ);
    if (!isset($rowZ['v_anzs'])) {
        $hAnz[0] = 'SPACE';
        $hAnz[1] = 'SPACE';
        $hAnz[2] = 'SPACE';
    } else {
        $hAnz[0] = $rowZ['v_anzs'];
        $hAnz[1] = $rowZ['v_anze'];
        $hAnz[2] = strval($rowZ['v_anzs'] - $rowZ['v_anze']);
    }

    return $hAnz;
}

function dauer_hhmm($zeit)
{
    $hh = intval($zeit / 3600);
    $mm = intval(($zeit - $hh * 3600) / 60);

    if ($zeit == 0) {
        return '-';
    }
    $mm    = ($mm < 10 ? "0" . $mm : $mm);
    $dauer = $hh . ":" . $mm;

    return $dauer;
}

function fill_planatt()
{
//RestDeff NACH Angriff aufbereiten
    global $db, $db_tb_kb_kaputt;
    global $hatt;
    global $a_id;
    global $k_id;
    global $kb_array;
    $hatt = '';

    $ha = "<td class='scan_object'>\n";
    $hm = "</td>\n<td class='scan_value'>\n";
    $he = "</td>\n</tr>\n";

    // Deffflotte-Planetenbesitzer
    $sql = "SELECT * FROM `$db_tb_kb_kaputt` ";
    $sql .= "WHERE w_id  = $a_id ";
    $sql .= "  AND k_id  = $k_id ";
    $sql .= "  AND v_anze > 0 ";
    $sql .= "  AND v_art = 'D' ";
//  $sql    .= "  AND v_name = '" . htmlentities($kb_array['OPFER']) . "'";
    $sql .= "  AND v_name = '" . $kb_array['OPFER'] . "'";
    $sql .= " ORDER BY v_klasse ";

    $select = $db->db_query($sql);
    while ($row = $db->db_fetch_array($select)) {
        $hclass = fill_schiffclass($row['v_bez']);
        $hatt .= $hclass . $ha . $row['v_bez'] . $hm . $row['v_anze'] . $he;
    }
    $db->db_free_result($select);
    // Plan-Deff
    $sql = "SELECT * FROM `$db_tb_kb_kaputt` ";
    $sql .= "WHERE w_id  = $a_id ";
    $sql .= "  AND k_id  = $k_id ";
    $sql .= "  AND v_anze > 0 ";
    $sql .= "  AND v_art = 'P' ";
//  $sql    .= " ORDER BY v_klasse ";

    $select = $db->db_query($sql);
    $hclass = "<tr class='scan_row1'> \n";
    while ($row = $db->db_fetch_array($select)) {
        $hatt .= $hclass . $ha . $row['v_bez'] . $hm . $row['v_anze'] . $he;
    }
    $db->db_free_result($select);
    // Stationierte Fremdflotten
    $sql = "SELECT * FROM `$db_tb_kb_kaputt` ";
    $sql .= "WHERE w_id  = $a_id ";
    $sql .= "  AND k_id  = $k_id ";
    $sql .= "  AND v_anze > 0 ";
    $sql .= "  AND v_art = 'D' ";
//  $sql    .= "  AND v_name <> '" . htmlentities($kb_array['OPFER']) . "'";
    $sql .= "  AND v_name <> '" . $kb_array['OPFER'] . "'";
    $sql .= " ORDER BY v_name, v_klasse ";
//  echo $sql . '<br>';
    $select = $db->db_query($sql);
    $hattSp = '';
    while ($row = $db->db_fetch_array($select)) {
        if ($hattSp != $row['v_name']) {
            $hatt .= "<tr class='scan_row2'> \n";
            $hatt .= "<td colspan='2' class='scan_title'>&nbsp;&nbsp;Stationierte Flotte von <a href='index.php?action=showgalaxy&user=" . urldecode($row['v_name']) . "'>" . $row['v_name'] . "</a>: </td> \n";
            $hatt .= "</tr> \n";
            $hattSp = $row['v_name'];
        }
        $hclass = fill_schiffclass($row['v_bez']);
        $hatt .= $hclass . $ha . $row['v_bez'] . $hm . $row['v_anze'] . $he;
    }
    $db->db_free_result($select);

}

function fill_schiffclass($hbez)
{
    global $db, $db_tb_schiffstyp;
    $sqlS = "SELECT typ from `" . $db_tb_schiffstyp . "` WHERE schiff = '$hbez'";
    $selectS = $db->db_query($sqlS);
    $rowS = $db->db_fetch_array($selectS);
    $db->db_free_result($selectS);

    $hclass = "<tr class='scan_row'> \n";
    if (substr($rowS['typ'], 0, 1) == 4 // Frachter
        || substr($rowS['typ'], 0, 1) == 5 // Sonden/Carrier
        || substr($rowS['typ'], 0, 1) == 6
    ) { // Sonstiges
        $hclass = "   <tr class='scan_row2'> \n";
    }
    if (substr($rowS['typ'], 0, 1) == 3) { // Kriegsschiffe/DN
        $hclass = "   <tr class='scan_row1'> \n";
    }

    return $hclass;
}

function update_planatt()
{
    global $hatt;
    global $db, $db_tb_scans;
    global $kb_array;

    $natt = "\n <table class='scan_table'> \n";
    $natt .= "\n <colgroup><col width='*'><col width='60'></colgroup> \n";
    $natt .= $hatt;
    $natt .= "\n </table> \n";
    $hatt = $natt;

    $sql = "SELECT coords, time, time_att, att FROM " . $db_tb_scans .
        " WHERE coords='" . $kb_array['ORT'] . "'";
    $result = $db->db_query($sql);
    $row = $db->db_fetch_array($result);
    $db->db_free_result($result);

    // neuer KB
    if ($kb_array['TIME'] >= $row['time_att']) {
        $htime = $row['time'];
        // Att-Time ist neuer als alle anderen Einlesezeiten
        if ($kb_array['TIME'] >= $row['time']) {
            $htime = $kb_array['TIME'];
        }

        $SQLdata = array(
            'time_att' => $kb_array['TIME'],
            'time'     => $htime,
            'att'      => $hatt
        );
        $db->db_update($db_tb_scans, $SQLdata, "WHERE coords='" . $row['coords'] . "'");
    }
}

function sql_fehler($sql)
{
    global $db;

    echo "<span class='doc_red'>$sql<br>Sql-Fehler: " . $db->db_errno() . ": " . $db->db_error() . "</span>";
}

function show_user_typ($usertyp = '')
{
    if ($usertyp == 'F') {
        return "&nbsp;&#x2708;";
    }

    return '';
}