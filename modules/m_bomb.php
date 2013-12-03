<?php
/*****************************************************************************
 * m_bomb.php                                                                *
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
 * Autor: Patsch                                                             *
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

define('MAX_BOMB_LINES', 20);

include_once('./includes/debug.php');

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
$modulname = "m_bomb";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Bombardements";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation 
//    ausführen darf. Mögliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "Bombardements zeigt die letzten Bombenangriffe und Statistiken an.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    //nothing here
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modulstatus;

    $menu             = getVar('menu');
    $submenu          = getVar('submenu');
    $menuetitel       = "Bombardements";
    $actionparameters = "";

    insertMenuItem($menu, $submenu, $menuetitel, $modulstatus, $actionparameters);
    //
    // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
    //
    // 	insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
    //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file.
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
    //nothing here
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgeführt wenn das Modul mit dem Parameter 
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
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// Hauptprogramm

Global $db, $db_tb_allianzstatus, $db_tb_gebaeude;

$results = array();

$config = array();
$config['allistatus_own'] = array();
$config['allistatus_krieg'] = array();

// Allianzen im Status 'Krieg' abfragen
$sql = "SELECT * FROM " . $db_tb_allianzstatus;
$sql .= " WHERE status='Krieg'";

//if ($user_status <> 'admin')
$sql .= " AND name='" . $user_allianz . "'";

$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $config['allistatus_krieg'][$row['allianz']] = $row['allianz'];
}


// Allianzen im Status 'own' abfragen
$sql = "SELECT * FROM " . $db_tb_allianzstatus;
$sql .= " WHERE status='own'";

//if ($user_status <> 'admin')
$sql .= " AND name='" . $user_allianz . "'";

$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $config['allistatus_own'][$row['allianz']] = $row['allianz'];
}

// Gebaeude abfragen
$sql = "SELECT * FROM " . $db_tb_gebaeude;
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    if (!empty($row['id_iw'])) {
        $config['gebaeude'][$row['id_iw']] = $row;
    }
}


// Daten abfragen
$data = array(
    'own'   => query_bombs($config['allistatus_own']),
    'krieg' => query_bombs($config['allistatus_krieg']),
);

function query_bombs($allis)
{
    global $config, $db, $db_tb_kb_bomb, $db_tb_kb, $db_tb_kb_bomb_geb;

    $data = array(
        'total' => array(
            'bauzeit' => 0,
        ),
    );
    $sql = "SELECT " .
        $db_tb_kb_bomb . ".*," .
        $db_tb_kb . ".*" .
        " FROM " . $db_tb_kb_bomb .
        " INNER JOIN " . $db_tb_kb . " ON " . $db_tb_kb . ".ID_KB=" . $db_tb_kb_bomb . ".ID_KB" .
        " WHERE (" . $db_tb_kb . ".verteidiger_ally IN ('" . implode("','", $allis) . "') AND " . $db_tb_kb . ".time>'1385170574')" .
        //" WHERE " . $db_tb_kb . ".verteidiger_ally IN ('" . implode("','", $allis) . "')" .
        " ORDER BY " . $db_tb_kb_bomb . ".time DESC";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        $bauzeit = 0;
        $urls    = array();
        $sqlinner = "SELECT " .
            $db_tb_kb . ".*" .
            "  FROM " . $db_tb_kb .
            " WHERE " . $db_tb_kb . ".koords_gal=" . $row['koords_gal'] .
            "   AND " . $db_tb_kb . ".koords_sol=" . $row['koords_sol'] .
            "   AND " . $db_tb_kb . ".koords_pla=" . $row['koords_pla'] .
            "   AND " . $db_tb_kb . ".time>" . ($row['time'] - 1 * 60 * 1000) .
            "   AND " . $db_tb_kb . ".time<" . ($row['time'] + 1 * 60 * 1000) .
            " ORDER BY " . $db_tb_kb . ".time ASC";

        $resultinner = $db->db_query($sqlinner)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlinner);
        while ($rowinner = $db->db_fetch_array($resultinner)) {
            $urls[] = 'http://www.icewars.de/portal/kb/de/kb.php?id=' . $rowinner['ID_KB'] . '&md_hash=' . $rowinner['hash'];
        }
        $gebs = array();
        $sqlinner = "SELECT " .
            $db_tb_kb_bomb_geb . ".*" .
            "  FROM " . $db_tb_kb_bomb_geb .
            " WHERE " . $db_tb_kb_bomb_geb . ".ID_KB=" . $row['ID_KB'];
        $resultinner = $db->db_query($sqlinner)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlinner);
        while ($rowinner = $db->db_fetch_array($resultinner)) {
            if (!isset($config['gebaeude'][$rowinner['ID_IW_GEB']])) {
                $results[] = "Unbekannte Gebäude ID '" . $rowinner['ID_IW_GEB'] . "'";
            } else {
                $gebs[] = array(
                    "anzahl" => $rowinner['anzahl'],
                    "info"   => @$config['gebaeude'][$rowinner['ID_IW_GEB']]
                );
                if (empty($config['gebaeude'][$rowinner['ID_IW_GEB']]['dauer'])) {
                    $results[] = "Unbekannte Bauzeit für Gebäude ID '" . $rowinner['ID_IW_GEB'] . "'";
                } else {
                    $bauzeit += $config['gebaeude'][$rowinner['ID_IW_GEB']]['dauer'] * $rowinner['anzahl'];
                }
            }
        }
        $data[] = array_merge(
            array(
                 'url'     => 'http://www.icewars.de/portal/kb/de/kb.php?id=' . $row['ID_KB'] . '&md_hash=' . $row['hash'],
                 'urls'    => $urls,
                 'gebs'    => $gebs,
                 'bauzeit' => $bauzeit,
            ), $row
        );
        if (!isset($data['total']['by_attacker'][$row['user']])) {
            $data['total']['by_attacker'][$row['user']] = array(
                'user'    => $row['user'],
                'anzahl'  => 0,
                'bauzeit' => 0
            );
        }
        $data['total']['by_attacker'][$row['user']]['anzahl']++;
        $data['total']['by_attacker'][$row['user']]['bauzeit'] += $bauzeit;
        $data['total']['bauzeit'] += $bauzeit;
    }

    return $data;
}

// Titelzeile ausgeben
doc_title($modultitle);

echo "<form>";
echo "Zusammenfassen: ";
echo "<input type='text' size='3' value='60'> Sekunden";
echo "</form>";

// Ergebnisse ausgeben
foreach ($results as $result) {
    echo($result);
}

start_table();
start_row("titlebg", "colspan='3'");
echo "<b>Angreifer von uns</b>";
next_cell("titlebg", "colspan='3'");
echo "<b>Angreifer auf uns</b>";
next_row("windowbg2", "nowrap");
echo "Name";
next_cell("windowbg2", "nowrap");
echo "Anzahl";
next_cell("windowbg2", "nowrap");
echo "Bauzeit";
next_cell("windowbg2", "nowrap");
echo "Name";
next_cell("windowbg2", "nowrap");
echo "Anzahl";
next_cell("windowbg2", "nowrap");
echo "Bauzeit";

$total_krieg_by_attacker = array();
if (!empty($data['krieg']['total']['by_attacker']) AND (is_array($data['krieg']['total']['by_attacker']))) {
    $total_krieg_by_attacker = array_values($data['krieg']['total']['by_attacker']);
    usort($total_krieg_by_attacker, "cmp_attack");
}

$total_own_by_attacker = array();
if (!empty($data['own']['total']['by_attacker']) AND (is_array($data['own']['total']['by_attacker']))) {
    $total_own_by_attacker = array_values($data['own']['total']['by_attacker']);
    usort($total_own_by_attacker, "cmp_attack");
}

$count = max(count($total_krieg_by_attacker), count($total_own_by_attacker));
for ($index = 0; $index < $count; $index++) {
    next_row("windowbg1", "nowrap");
    if (isset($total_krieg_by_attacker[$index])) {
        $attacker = $total_krieg_by_attacker[$index];
        echo $attacker['user'];
        next_cell("windowbg1", "nowrap");
        echo $attacker['anzahl'];
        next_cell("windowbg1", "nowrap");
        echo makeduration($attacker['bauzeit']);
    } else {
        next_cell("windowbg1", "nowrap");
        next_cell("windowbg1", "nowrap");
    }
    if (isset($total_own_by_attacker[$index])) {
        $attacker = $total_own_by_attacker[$index];
        next_cell("windowbg1", "nowrap");
        echo $attacker['user'];
        next_cell("windowbg1", "nowrap");
        echo $attacker['anzahl'];
        next_cell("windowbg1", "nowrap");
        echo makeduration($attacker['bauzeit']);
    } else {
        next_cell("windowbg1", "nowrap");
        next_cell("windowbg1", "nowrap");
        next_cell("windowbg1", "nowrap");
    }
}
end_table();

echo "<br>";

start_table();
start_row("titlebg", "colspan='6'");
echo "<b>Bombenangriffe von uns</b>";
next_cell("titlebg", "colspan='6'");
echo "<b>Bombenangriffe auf uns</b>";
next_row("windowbg2", "nowrap");
echo "Zeit";
next_cell("windowbg2", "nowrap");
echo "Angreifer";
next_cell("windowbg2", "nowrap");
echo "Verteidiger";
next_cell("windowbg2", "nowrap");
echo "Planet";
next_cell("windowbg2", "nowrap");
echo "Bauzeit";
next_cell("windowbg2", "nowrap");
echo "KB";
next_cell("windowbg2", "nowrap");
echo "Zeit";
next_cell("windowbg2", "nowrap");
echo "Angreifer";
next_cell("windowbg2", "nowrap");
echo "Verteidiger";
next_cell("windowbg2", "nowrap");
echo "Planet";
next_cell("windowbg2", "nowrap");
echo "Bauzeit";
next_cell("windowbg2", "nowrap");
echo "KB";
$count = max(count($data['krieg']), count($data['own']));
for ($index = 0; $index < $count - 1; $index++) {
    if (isset($data['krieg'][$index])) {
        $att = $data['krieg'][$index];
        next_row("windowbg1", "nowrap");
        echo makesince($att["time"]);
        next_cell("windowbg1", "nowrap");
        echo $att["user"];
        next_cell("windowbg1", "nowrap");
        if (!empty($att['verteidiger_ally'])) {
            echo "[" . $att['verteidiger_ally'] . "]";
        }
        echo $att["verteidiger"];
        next_cell("windowbg1", "nowrap");
        echo $att["koords_gal"] . ":" . $att["koords_sol"] . ":" . $att["koords_pla"];
        next_cell("windowbg1", "nowrap align='right'");
        echo makeduration($att['bauzeit']);
        next_cell("windowbg1", "nowrap");
        for ($urlindex = 0; $urlindex < count($att['urls']); $urlindex++) {
            echo " <a href='" . $att['urls'][$urlindex] . "' target='" . $modultitle . "_kb' style='" . ($att['urls'][$urlindex] == $att['url'] ? 'color : red' : '') . "'>" . ($urlindex + 1) . "</a>";
        }
    } else {
        next_row("windowbg1");
        next_cell("windowbg1");
        next_cell("windowbg1");
        next_cell("windowbg1");
        next_cell("windowbg1");
        next_cell("windowbg1");
    }
    if (isset($data['own'][$index])) {
        $att = $data['own'][$index];
        next_cell("windowbg1", "nowrap");
        echo makesince($att["time"]);
        next_cell("windowbg1", "nowrap");
        echo $att["user"];
        next_cell("windowbg1", "nowrap");
        if (!empty($att['verteidiger_ally'])) {
            echo "[" . $att['verteidiger_ally'] . "]";
        }
        echo $att["verteidiger"];
        next_cell("windowbg1", "nowrap");
        echo $att["koords_gal"] . ":" . $att["koords_sol"] . ":" . $att["koords_pla"];
        next_cell("windowbg1", "nowrap align='right'");
        echo makeduration($att['bauzeit']);
        next_cell("windowbg1", "nowrap");
        for ($urlindex = 0; $urlindex < count($att['urls']); $urlindex++) {
            echo " <a href='" . $att['urls'][$urlindex] . "' target='" . $modultitle . "_kb' style='" . ($att['urls'][$urlindex] == $att['url'] ? 'color : red' : '') . "'>" . ($urlindex + 1) . "</a>";
        }
    } else {
        next_cell("windowbg1");
        next_cell("windowbg1");
        next_cell("windowbg1");
        next_cell("windowbg1");
        next_cell("windowbg1");
        next_cell("windowbg1");
    }
}
next_row("windowbg2", "nowrap colspan='4'");
echo (count($data['krieg']) - 1) . " Bombadierungen";
next_cell("windowbg2", "nowrap align='right'");
echo makeduration($data['krieg']['total']['bauzeit']);
next_cell("windowbg2");
next_cell("windowbg2", "nowrap colspan='4'");
echo (count($data['own']) - 1) . " Bombadierungen";
next_cell("windowbg2", "nowrap align='right'");
echo makeduration($data['own']['total']['bauzeit']);
next_cell("windowbg2");
end_table();

//****************************************************************************
//
// Vergleicht zwei Angriffe
function cmp_attack($a, $b)
{
    if ($a['bauzeit'] == $b['bauzeit']) {
        return 0;
    } else {
        return $a['bauzeit'] > $b['bauzeit'] ? -1 : 1;
    }
}

//****************************************************************************
//
// Dauer formatieren
function makeduration($seconds)
{
    $days  = floor($seconds / 60 / 60 / 24);
    $hours = $seconds / 60 / 60 % 24;
    $mins  = $seconds / 60 % 60;

    return sprintf("%02dd %02d:%02d", $days, $hours, $mins);
}

//****************************************************************************
//
// Zeit seit jetzt formatieren
function makesince($time)
{
    if (empty($time)) {
        return '---';
    }
    $duration = time() - $time;
    if ($duration > 2 * 24 * 60 * 60) {
        return round($duration / (24 * 60 * 60)) . "d";
    } elseif ($duration > 60 * 60) {
        $hours = round($duration / (60 * 60));

        return $hours == 1 ? "1 Stunde" : $hours . "h";
    } else {
        $minutes = round($duration / 60);

        return $minutes == 1 ? "1 Minute" : $minutes . "min";
    }
}