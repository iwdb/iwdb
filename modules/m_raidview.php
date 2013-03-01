<?php
/*****************************************************************************
 * m_raidview.php                                                            *
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
$modulname = "m_raidview";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Raid-Statistik";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation
//    ausfuehren darf. Mögliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc =
    "In der Raid-Statistik werden die Raids der Member erfasst und statistisch aufbereitet.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//

function workInstallDatabase()
{
    /*
    global $db, $db_prefix;

      $sqlscript = array(
            "CREATE TABLE IF NOT EXISTS " . $db_prefix . "raidview ( " .
          " id INT(11) NOT NULL auto_increment, " .
          " coords VARCHAR(11) NOT NULL DEFAULT '', " .
          " date  INT(12) NOT NULL DEFAULT '0', " .
          " eisen INT(11) NOT NULL, " .
          " stahl INT(11) NOT NULL, " .
          " vv4a INT(11) NOT NULL, " .
          " chemie INT(11) NOT NULL, " .
          " eis INT(11) NOT NULL, " .
          " link VARCHAR(90) NOT NULL, " .
          " wasser INT(11) NOT NULL, " .
          " energie INT(11) NOT NULL, " .
          " `geraided` VARCHAR( 30 ) NOT NULL " .
          " user VARCHAR(20) NOT NULL, " .
          " PRIMARY KEY  (`id`), " .
          " UNIQUE KEY `coords` (`coords`,`date`)" .
            " ) COMMENT='Raidberichte';",
      );

    foreach ($sqlscript as $sql) {
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    echo "<br>Installation: Datenbankänderungen = <b>OK</b><br>";
    */
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modultitle, $modulstatus;

    $menu    = getVar('menu');
    $submenu = getVar('submenu');

    $actionparamters = "";
    insertMenuItem($menu, $submenu, $modultitle, $modulstatus, $actionparamters);
    //
    // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
    //
    // 	insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
    //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed
// for the configuration file
//
function workInstallConfigString()
{
    /*  global $config_gameversion;
      return
        "\$v04 = ' <font color=\\'#ff4466\\'>(V " . $config_gameversion . ")</font>';";
    */
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//

function workUninstallDatabase()
{
    //nothing here
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

    echo "<br>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")<br><br>\n";

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
global $db, $db_tb_raidview;

doc_title("Plünderungen");

$hauser = "dsjktvafkwefj vofjeriofjegiodfsghsd";
if (isset($_GET['user'])) {
    $hauser = $_GET['user'];
}

// TABELLE - RAIDHIGHSCORE START
function make_link($order, $ordered)
{
    global $sid;
    echo "<a href='index.php?action=m_raidview&order=" . $order . "&ordered=" . $ordered .
        "&sid=$sid'> <img src='bilder/" . $ordered . ".gif' alt='" . $ordered . "'> </a>";
}

// user aus Tabelle holen und gruppieren
$sql = "SELECT `user` FROM `{$db_tb_raidview}` GROUP BY `user`;";
$result2 = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

start_table();
start_row("titlebg center bold", "style='width:10%' colspan='3'");

echo "User";

next_cell("titlebg center bold", "style='width:10%'");

echo "Raids";

next_cell("titlebg center bold", "style='width:10%'");

echo "Eisen";

next_cell("titlebg center bold", "style='width:10%'");

echo "Stahl";

next_cell("titlebg center bold", "style='width:10%'");

echo "VV4A";

next_cell("titlebg center bold", "style='width:10%'");

echo "Chemie";

next_cell("titlebg center bold", "style='width:10%'");

echo "Eis";

next_cell("titlebg center bold", "style='width:10%'");

echo "Wasser";

next_cell("titlebg center bold", "style='width:10%'");

echo "Energie";


// jeder user eine tabelle
while ($row = $db->db_fetch_array($result2)) {
    $user = $row["user"];

// Ressourcen aus raidview holen und nach Datum sortieren
    $sql = "SELECT * FROM " . $db_tb_raidview . " WHERE user= '" . $user . "' ORDER BY date ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $count     = 0;
    $eisen     = 0;
    $stahl     = 0;
    $vv4a      = 0;
    $chem      = 0;
    $eis       = 0;
    $wasser    = 0;
    $energie   = 0;
    $volk      = 0;
    $v_eisen   = 0;
    $v_stahl   = 0;
    $v_vv4a    = 0;
    $v_chem    = 0;
    $v_eis     = 0;
    $v_wasser  = 0;
    $v_energie = 0;
    $g_eisen   = 0;
    $g_stahl   = 0;
    $g_vv4a    = 0;
    $g_chem    = 0;
    $g_eis     = 0;
    $g_wasser  = 0;
    $g_energie = 0;

// Addieren der Resswerte für Gesamtsumme und Schreiben der Zeilen
    while ($row = $db->db_fetch_array($result)) {
        $count++;
        $ruser    = $row['user'];
        $geraided = $row['geraided'];
        if (strnatcasecmp($ruser, $geraided)) {
            $eisen += $row['eisen'];
            $stahl += $row['stahl'];
            $vv4a += $row['vv4a'];
            $chem += $row['chemie'];
            $eis += $row['eis'];
            $wasser += $row['wasser'];
            $energie += $row['energie'];
            $v_eisen += $row['v_eisen'];
            $v_stahl += $row['v_stahl'];
            $v_vv4a += $row['v_vv4a'];
            $v_chem += $row['v_chem'];
            $v_eis += $row['v_eis'];
            $v_wasser += $row['v_wasser'];
            $v_energie += $row['v_energie'];

            $g_eisen += $row['g_eisen'];
            $g_stahl += $row['g_stahl'];
            $g_vv4a += $row['g_vv4a'];
            $g_chem += $row['g_chem'];
            $g_eis += $row['g_eis'];
            $g_wasser += $row['g_wasser'];
            $g_energie += $row['g_energie'];
        } else {
            $eisen -= $row['eisen'];
            $stahl -= $row['stahl'];
            $vv4a -= $row['vv4a'];
            $chem -= $row['chemie'];
            $eis -= $row['eis'];
            $wasser -= $row['wasser'];
            $energie -= $row['energie'];
            $v_eisen -= $row['v_eisen'];
            $v_stahl -= $row['v_stahl'];
            $v_vv4a -= $row['v_vv4a'];
            $v_chem -= $row['v_chem'];
            $v_eis -= $row['v_eis'];
            $v_wasser -= $row['v_wasser'];
            $v_energie -= $row['v_energie'];

            $g_eisen -= $row['g_eisen'];
            $g_stahl -= $row['g_stahl'];
            $g_vv4a -= $row['g_vv4a'];
            $g_chem -= $row['g_chem'];
            $g_eis -= $row['g_eis'];
            $g_wasser -= $row['g_wasser'];
            $g_energie -= $row['g_energie'];
        }
    }

    $order   = getVar('order');
    $ordered = getVar('ordered');

    if (empty($order)) {
        $order = 'user';
    }

    if (empty($ordered)) {
        $ordered = 'asc';
    }

    global $db, $db_tb_ressuebersicht;

//Tabelle mit Inhalten aus der Datenbank füttern
    next_row("windowbg1", "colspan='3'");
    if (isset($user) and isset($_GET['user']) and $_GET['user'] == $user) {

        echo "<a href='index.php?action=m_raidview&sid=$sid'><b>" . $user . "</b></a>";

    } else {

        echo "<a href='index.php?action=m_raidview&user=" . $user . "&sid=$sid'><b>" . $user . "</b></a>";

    }
    next_cell("windowbg1 right");
    echo "#:" . number_format($count, 0, ',', '.');
    next_cell("windowbg1 right");

    echo number_format($eisen, 0, ',', '.'), " <br> ", '<font color="red">', number_format($v_eisen, 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format(($eisen - $v_eisen), 0, ',', '.');
    next_cell("windowbg1 right");
    echo number_format($stahl, 0, ',', '.'), " <br> ", '<font color="red">', number_format($v_stahl, 0, ',', '.'), " <br> ", '<font color="green">', number_format(($stahl - $v_stahl), 0, ',', '.');
    next_cell("windowbg1 right", "style='width:10%'");
    echo number_format($vv4a, 0, ',', '.'), " <br> ", '<font color="red">', number_format($v_vv4a, 0, ',', '.'), " <br> ", '<font color="green">', number_format(($vv4a - $v_vv4a), 0, ',', '.');
    next_cell("windowbg1 right", "style='width:10%'");
    echo number_format($chem, 0, ',', '.'), " <br> ", '<font color="red">', number_format($v_chem, 0, ',', '.'), " <br> ", '<font color="green">', number_format(($chem - $v_chem), 0, ',', '.');
    next_cell("windowbg1 right", "style='width:10%'");
    echo number_format($eis, 0, ',', '.'), " <br> ", '<font color="red">', number_format($v_eis, 0, ',', '.'), " <br> ", '<font color="green">', number_format(($eis - $v_eis), 0, ',', '.');
    next_cell("windowbg1 right", "style='width:10%'");
    echo number_format($wasser, 0, ',', '.'), " <br> ", '<font color="red">', number_format($v_wasser, 0, ',', '.'), " <br> ", '<font color="green">', number_format(($wasser - $v_wasser), 0, ',', '.');
    next_cell("windowbg1 right", "style='width:10%'");
    echo number_format($energie, 0, ',', '.'), " <br> ", '<font color="red">', number_format($v_energie, 0, ',', '.'), " <br> ", '<font color="green">', number_format(($energie - $v_energie), 0, ',', '.');

    end_row();

    if ($user == $hauser) {

        if (isset($_GET['user'])) {
            // user aus Tabelle holen und gruppieren
            $user = $_GET['user'];
            # start_table();
            start_row("titlebg center", "style='width:95%' colspan='12'");
            echo "<b> Raidhistory von " . $user . "</b>";
            next_row("windowbg2 center", "style='width:1%'");
            echo "";
            next_cell("windowbg2 center", "style='width:10%'");
            echo "Zeit";
            next_cell("windowbg2 center", "style='width:10%'");
            echo "Opfer";
            next_cell("windowbg2 center", "style='width:9%'");
            echo "Koords";

            next_cell("windowbg2 center", "style='width:10%'");
            echo "Eisen";
            next_cell("windowbg2 center", "style='width:10%'");
            echo "Stahl";
            next_cell("windowbg2 center", "style='width:10%'");
            echo "VV4A";
            next_cell("windowbg2 center", "style='width:10%'");
            echo "Chemie";
            next_cell("windowbg2 center", "style='width:10%'");
            echo "Eis";
            next_cell("windowbg2 center", "style='width:10%'");
            echo "Wasser";
            next_cell("windowbg2 center", "style='width:10%'");
            echo "Energie";

            // Ressourcen aus raidview holen und nach Datum sortieren
            $sql = "SELECT * FROM " . $db_tb_raidview . " WHERE user= '" . $_GET['user'] . "' ORDER BY date ASC";
            $result3 = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            $count     = 0;
            $eisen     = 0;
            $stahl     = 0;
            $vv4a      = 0;
            $chem      = 0;
            $eis       = 0;
            $wasser    = 0;
            $energie   = 0;
            $volk      = 0;
            $v_eisen   = 0;
            $v_stahl   = 0;
            $v_vv4a    = 0;
            $v_chem    = 0;
            $v_eis     = 0;
            $v_wasser  = 0;
            $v_energie = 0;
            $g_eisen   = 0;
            $g_stahl   = 0;
            $g_vv4a    = 0;
            $g_chem    = 0;
            $g_eis     = 0;
            $g_wasser  = 0;
            $g_energie = 0;

            // addieren der Resswerte für Gesamtsumme und schreiben der Zeilen
            while ($row = $db->db_fetch_array($result3)) {
                $count++;
                #	$eisen   += $row['eisen'];
                #	$stahl   += $row['stahl'];
                #  	$vv4a    += $row['vv4a'];
                #  	$chem    += $row['chemie'];
                #  	$eis     += $row['eis'];
                #  	$wasser  += $row['wasser'];
                #  	$energie += $row['energie'];
                $row['link'] = str_replace('&', '&', $row['link']);
                $ruser       = $row['user'];
                $geraided    = trim($row['geraided']);
                $guser       = preg_replace('/\[.*\]/', '', $geraided);
                $guser       = trim($guser);
                if (strnatcasecmp($ruser, $geraided)) {
                    next_row("windowbg1 left", "style='width:1%'");
                    echo "<a title='Link zum externen Kampfbericht' href='" . $row['link'] . "'><img src='bilder/point.gif'/></a>";
                    next_cell("windowbg1 left", "style='width:12%'");
                    echo strftime(CONFIG_DATETIMEFORMAT, $row['date']);
                    next_cell("windowbg1 center", "style='width:10%'");
                    echo "<a href='index.php?action=showgalaxy&user=" . $guser . "&sid=" . $sid . "'>" . $row['geraided'] . "</a>";
                    next_cell("windowbg1 right", "style='width:9%'");
                    echo "<a href='index.php?action=showplanet&coords=" . $row['coords'] . "&ansicht=auto&sid=" . $sid . "'>" . $row['coords'] . "</a>";

                    next_cell("windowbg1 right", "style='width:8%'");
                    echo number_format($row['eisen'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_eisen'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_eisen'], 0, ',', '.');
                    next_cell("windowbg1 right", "style='width:8%'");
                    echo number_format($row['stahl'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_stahl'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_stahl'], 0, ',', '.');
                    next_cell("windowbg1 right", "style='width:8%'");
                    echo number_format($row['vv4a'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_vv4a'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_vv4a'], 0, ',', '.');
                    next_cell("windowbg1 right", "style='width:8%'");
                    echo number_format($row['chemie'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_chem'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_chem'], 0, ',', '.');
                    next_cell("windowbg1 right", "style='width:8%'");
                    echo number_format($row['eis'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_eis'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_eis'], 0, ',', '.');
                    next_cell("windowbg1 right", "style='width:8%'");
                    echo number_format($row['wasser'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_wasser'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_wasser'], 0, ',', '.');
                    next_cell("windowbg1 right", "style='width:8%'");
                    echo number_format($row['energie'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_energie'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_energie'], 0, ',', '.');

                } else {
                    echo "<font color=red>";
                    next_row("windowbg1 left", "style='width:1%'");
                    echo "<a title='Link zum externen Kampfbericht' href='" . $row['link'] . "'><img src='bilder/point.gif'/></a>";
                    next_cell("windowbg1 left", "style='width:12%'");
                    echo strftime(CONFIG_DATETIMEFORMAT, $row['date']);
                    next_cell("windowbg1 center", "style='width:10%'");
                    echo "<a href='index.php?action=showgalaxy&user=" . $guser . "&sid=" . $sid . "'>" . $row['geraided'] . "</a>";
                    next_cell("windowbg1 right", "style='width:9%'");
                    echo "<a href='index.php?action=showplanet&coords=" . $row['coords'] . "&ansicht=auto&sid=" . $sid . "'>" . $row['coords'] . "</a>";

                    next_cell("windowbg1 right", "style=color:#FF0000");
                    echo number_format($row['eisen'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_eisen'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_eisen'], 0, ',', '.');
                    next_cell("windowbg1 right", "style=color:#FF0000");
                    echo number_format($row['stahl'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_stahl'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_stahl'], 0, ',', '.');
                    next_cell("windowbg1 right", "style=color:#FF0000");
                    echo number_format($row['vv4a'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_vv4a'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_vv4a'], 0, ',', '.');
                    next_cell("windowbg1 right", "style=color:#FF0000");
                    echo number_format($row['chemie'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_chem'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_chem'], 0, ',', '.');
                    next_cell("windowbg1 right", "style=color:#FF0000");
                    echo number_format($row['eis'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_eis'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_eis'], 0, ',', '.');
                    next_cell("windowbg1 right", "style=color:#FF0000");
                    echo number_format($row['wasser'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_wasser'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_wasser'], 0, ',', '.');
                    next_cell("windowbg1 right", "style=color:#FF0000");
                    echo number_format($row['energie'], 0, ',', '.'), " <br> ", '<font color="red">', number_format($row['v_energie'], 0, ',', '.'), '</font>', " <br> ", '<font color="green">', number_format($row['g_energie'], 0, ',', '.');
                    echo "</font>";
                }
            }

            // Ausgabe der Gesamtsummen
            next_row("windowbg2 left", "style='width:10%' colspan='11'");
            echo "&nbsp;";
            end_row();
#      end_table();
        }
    }
}
// TABELLE - RAIDHIGHSCORE ENDE
end_table();
?>
<br>
<br>
<div style="color:black">Anzahl geraideter Ressourcen</div>
<div style="color:red">Ressourcenverluste z.B. durch Schiffe/Deff</div>
<div style="color:green">Anzahl effektiver Ressourcen</div>
<br>