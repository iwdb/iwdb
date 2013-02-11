<?php
/*****************************************************************************
 * m_raid.php                                                                 *
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
 * Autor: [GILDE]Thella (icewars@thella.de)                                  *
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

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname = "m_raid";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Ziele suchen";

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
// -> Beschreibung des Moduls, wie es in der Menü-Uebersicht angezeigt wird.
//
$moduldesc = "Dieses Modul hilft beim pösen Klauen von Lollis.";

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
    global $modultitle, $modulstatus, $_POST;

    $actionparamters = "";
    insertMenuItem($_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparamters);
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

$max_results = 1000;

$results = array();
$params  = array();

// Seitenparameter definieren
$defaults = array(
    'view'                 => 'overview',
    'gal_start'            => $user_gal_start,
    'gal_end'              => $user_gal_end,
    'sys_start'            => $user_sys_start,
    'sys_end'              => $user_sys_end,
    'order'                => 'coords',
    'orderd'               => 'asc',
    'edit'                 => '',
    'delete'               => '',
    'expand'               => '',
    'objekt'               => 'Alle',
    'user'                 => '',
    'alli'                 => '',
    'scans'                => 'Alle',
    'no_noob'              => '1',
    'inaktiv'              => '',
    'def_min'              => '',
    'def_max'              => '',
    'scan_schiff_age_min'  => '',
    'scan_schiff_age_max'  => '',
    'scan_geb_age_min'     => '',
    'scan_geb_age_max'     => '',
    'scan_failure_age_min' => '',
    'scan_failure_age_max' => '',
    'allistatus'           => '',
    'angriff'              => '',
    'no_angriff'           => '',
    'sondierung'           => '',
    'no_sondierung'        => '',
    'no_reservierung'      => '',
    'reservierung_user'    => '',
    'reservierung_foreign' => '',
    'rating_min'           => '',
    'rating_max'           => '',
    'ressource'            => 'Alle',
    'ress_min'             => '',
    'sg_start'             => '',
    'sg_end'               => ''
);

// Seitenparameter ermitteln
foreach ($defaults as $key => $value) {
    $params[$key] = getVar($key);
}

// Seitenparameter validieren
if (is_array($params['allistatus'])) {
    $params['allistatus'] = implode(",", $params['allistatus']);
}
if (!is_numeric($params['gal_start']) && !empty($params['gal_start'])) {
    $params['gal_start'] = $defaults['gal_start'];
}
if (!is_numeric($params['gal_end']) && !empty($params['gal_end'])) {
    $params['gal_end'] = $defaults['gal_end'];
}
if (!is_numeric($params['sys_start']) && !empty($params['sys_start'])) {
    $params['sys_start'] = $defaults['sys_start'];
}
if (!is_numeric($params['sys_end']) && !empty($params['sys_end'])) {
    $params['sys_end'] = $defaults['sys_end'];
}
if (empty($params['order'])) {
    $params['order'] = $defaults['order'];
}
if ($params['orderd'] != 'asc' && $params['orderd'] != 'desc') {
    $params['orderd'] = $defaults['orderd'];
}
if (empty($params['objekt'])) {
    $params['objekt'] = $defaults['objekt'];
}
if (empty($params['allistatus'])) {
    $params['allistatus'] = $defaults['allistatus'];
}
debug_var("Parameter", $params);

// Zum Spiel weiterleiten
$universum       = getVar('universum');
$flotteversenden = getVar('flotteversenden');
if (!empty($universum) || !empty($flotteversenden)) {
    $name = 'Automatische Zielliste vom ' . strftime(CONFIG_DATETIMEFORMAT, CURRENT_UNIX_TIME);

    $sql = "DELETE FROM " . $db_tb_target . " WHERE user='" . $user_sitterlogin . "' AND name LIKE 'Automatische Zielliste%'";
    debug_var("sql", $sql);
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $index = 1;
    do {
        $current = getVar("target_" . $index++);
        if (!empty($current) && getVar("mark_" . $current)) {
            debug_var("coords", $coords = explode(":", $current));
            debug_var(
                "sql", $sql = "
				INSERT INTO " . $db_tb_target . "(`user`,`name`,`coords_gal`,`coords_sys`,`coords_planet`)
				VALUES ('" . $user_sitterlogin . "','" . $name . "'," . $coords[0] . "," . $coords[1] . "," . $coords[2] . ")"
            );
            $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }
    } while (!empty($current));
    $results[] = "<div class='system_notification'>Zielliste gespeichert.</div><br>";
    $redirect  = 'game.php?sid=' . $sid . '&name=' . $name;
    if (!empty($universum)) {
        $redirect .= '&view=universum';
    } else {
        $redirect .= '&view=fleet_send';
    }
}

// Fehlscan speichern
$fehlscan = getVar('fehlscan');
if (!empty($fehlscan)) {
    $x11_all      = getVar('x11_all');
    $terminus_all = getVar('terminus_all');
    $x13_all      = getVar('x13_all');
    $index        = 0;
    while ($index++ < $fehlscan) {
        $coords_gal    = getVar('coords_gal_' . $index);
        $coords_sys    = getVar('coords_sys_' . $index);
        $coords_planet = getVar('coords_planet_' . $index);
        $time          = getVar('time_' . $index);
        $x11           = getVar('x11_' . $index);
        $terminus      = getVar('terminus_' . $index);
        $x13           = getVar('x13_' . $index);
        if (empty($x11)) {
            $x11 = $x11_all;
        }
        if (empty($terminus)) {
            $terminus = $terminus_all;
        }
        if (empty($x13)) {
            $x13 = $x13_all;
        }
        if (!empty($x11) || !empty($terminus) || !empty($x13)) {
            $sql = "UPDATE " . $db_tb_scans;
            if (!empty($x11)) {
                $sql .= " SET x11=" . $x11 . ",terminus=NULL,x13=NULL";
            } elseif (!empty($terminus)) {
                $sql .= " SET terminus=" . $terminus . ",x11=NULL,x13=NULL";
            } elseif (!empty($x13)) {
                $sql .= " SET x13=" . $x13 . ",x11=NULL,terminus=NULL";
            }
            $sql .= ",fehlscantime=" . $time;
            $sql .= " WHERE coords_gal=" . $coords_gal;
            $sql .= " AND coords_sys=" . $coords_sys;
            $sql .= " AND coords_planet=" . $coords_planet;
            debug_var("sql", $sql);
            $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }
        $results[] = "<div class='system_notification'>Fehlscan auf " . $coords_gal . ":" . $coords_sys . ":" . $coords_planet . " gespeichert.</div><br>";
    }
    $results[] = "<div class='system_notification'>Datensatz gespeichert.</div><br>";
}

// Stammdaten abfragen
$config = array();

// Spieler abfragen
$config['users'] = array();

$sql = "SELECT * FROM " . $db_tb_user;
debug_var('sql', $sql);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $config['users'][$row['id']] = $row['id'];
}

// Allianzstatus abfragen
$config['allistatus'] = array();
$config['statusalli'] = array();

$sql = "SELECT status,allianz FROM " . $db_tb_allianzstatus . " WHERE name='" . $user_allianz . "'";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $config['allistatus'][$row['allianz']]  = $row['status'];
    $config['statusalli'][$row['status']][] = $row['allianz'];
}

// Schiffstypen
$config['schiffsangriff'] = array(
    // Jäger
    'Sheep'                          => 10,
    'Shark'                          => 12,
    'Manta'                          => 15,
    'Downbringer'                    => 25,
    'Sirius X300'                    => 45,
    'Nightcrawler'                   => 35,
    // Bomber
    'Atombomber'                     => 10,
    'Stormbringer'                   => 15,
    'Nova'                           => 20,
    'Nepomuk'                        => 5,
    // Korvette
    'Lionheart'                      => 20,
    'Hunter'                         => 30,
    'Victim'                         => 45,
    'Gatling'                        => 65,
    'Eraser'                         => 25,
    // Zerstörer
    'Slayer'                         => 35,
    'Vendeta'                        => 55,
    'Crawler'                        => 85,
    'Widowmaker'                     => 45,
    // Kreuzer
    'Hitman'                         => 120,
    'Succubus'                       => 360,
    'Sirius XPi'                     => 450,
    'TAG Vario Kreuzer'              => 420,
    'Silent Sorrow'                  => 130,
    // Schlachtschiff
    'Big Daddy'                      => 600,
    'Kronk'                          => 1000,
    'Quasal'                         => 1450,
    // Dreadnoughts
    'Rentier Kampftransporter'       => 3000,
    'Zeus'                           => 2580,
    'Tempest'                        => 3800,
    'Rosa-Plüschhasen-Spezialschiff' => 250,
    'Nimbus BP-1729'                 => 360,
);

// Button abfragen
$button_edit = getVar("button_edit");
$button_add  = getVar("button_add");

// Edit-Daten belegen
$edit['reserveraiduser']  = getVar('reserveraiduser');
$edit['reserveraidhours'] = getVar('reserveraidhours');

// Edit-Daten validieren
if (empty($edit['reserveraiduser'])) {
    $edit['reserveraiduser'] = $user_sitterlogin;
}
if (empty($edit['reserveraidhours'])) {
    $edit['reserveraidhours'] = '24';
}

$edit['reserveraiduntil'] = strftime("%d.%m.%Y %H:%M", (CURRENT_UNIX_TIME + ($edit['reserveraidhours'] * HOUR)));

// Edit-Daten löschen
if (isset($params['delete']) && !empty($params['delete'])) {
    $explode = explode(":", $params['delete']);
    $sql     = "UPDATE " . $db_tb_scans;
    $sql .= " SET reserveraid=NULL, reserveraiduser=NULL";
    $sql .= " WHERE ";
    $sql .= "coords_gal=" . $explode[0] . " AND coords_sys=" . $explode[1] . " AND coords_planet=" . $explode[2];
    debug_var('sql', $sql);
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $results[]        = "<div class='system_notification'>Datensatz gelöscht.</div><br>";
    $params['delete'] = '';
    $params['edit']   = '';
}

// Edit-Daten modifizieren
if (!empty($button_edit)) {
    $explode = explode(":", $params['edit']);
    $sql     = "UPDATE " . $db_tb_scans . " SET ";
    $sql .= "reserveraiduser='" . $edit['reserveraiduser'] . "',";
    $sql .= "reserveraid=" . (CURRENT_UNIX_TIME + ($edit['reserveraidhours'] * HOUR));
    $sql .= " WHERE ";
    $sql .= "coords_gal=" . $explode[0] . " AND coords_sys=" . $explode[1] . " AND coords_planet=" . $explode[2];
    debug_var('sql', $sql);
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $results[] = "<div class='system_notification'>Datensatz aktualisiert.</div><br>";
}

// Edit-Daten abfragen
if (empty($button_edit) && empty($button_add) && !empty($params['edit'])) {
    $explode = explode(":", $params['edit']);
    $sql     = "SELECT ";
    $sql .= "reserveraiduser,reserveraid";
    $sql .= " FROM " . $db_tb_scans;
    $sql .= " WHERE ";
    $sql .= "coords_gal=" . $explode[0] . " AND coords_sys=" . $explode[1] . " AND coords_planet=" . $explode[2];
    debug_var('sql', $sql);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    if ($row = $db->db_fetch_array($result)) {
        if (!empty($row['reserveraiduser'])) {
            $edit['reserveraiduser']  = $row['reserveraiduser'];
            $edit['reserveraid']      = $row['reserveraid'];
            $edit['reserveraidhours'] = round(($row['reserveraid'] - CURRENT_UNIX_TIME) / HOUR);
            $edit['reserveraiduntil'] = strftime("%d.%m.%Y %H:%M", $edit['reserveraid']);
        }
    }
}

// Edit-Bereich definieren
$editview = array(
    'reserveraiduser'  => array(
        'title'  => 'Spieler',
        'desc'   => 'Für welchen Spieler soll das Ziel reserviert werden?',
        'type'   => 'select',
        'values' => $config['users'],
        'value'  => $edit['reserveraiduser'],
    ),
    'reserveraidhours' => array(
        'title' => 'Stunden',
        'desc'  => 'Wieviele Stunden soll das Ziel reserviert werden?',
        'type'  => 'text',
        'value' => $edit['reserveraidhours'],
        'style' => 'width: 70px;',
    ),
    'reserveraiduntil' => array(
        'title' => 'Reserviert bis',
        'desc'  => 'Wie lange ist das Ziel reserviert?',
        'type'  => 'label',
        'value' => $edit['reserveraiduntil'],
        'style' => 'width: 110;',
    ),
);

// Ansichten defininieren
$views = array(
    'overview' => array(
        'title'       => 'Übersicht',
        'columns'     => array(
            'objekttyp'   => '',
            'coords'      => 'Koords',
            'tsonden'     => 'T',
            'x13sonden'   => 'X13',
            'planetentyp' => '',
            'user'        => 'Spieler',
            'allianz'     => 'Allianz',
            'last_scan'   => 'Scan',
            'last_raid'   => 'Raid',
            'inaktiv'     => 'Inaktiv',
            'eisen'       => 'FE',
            'stahl'       => 'St',
            'vv4a'        => 'V4',
            'chemie'      => 'CE',
            'eis'         => 'Ei',
            'wasser'      => 'HO',
            'energie'     => 'En',
            'deff_pla'    => 'Deff',
            'deff_schiff' => 'Schiff',
            'rating'      => 'Rating',
        ),
        'edit'        => $editview,
        'edittitle'   => 'Reservierung',
        'deletetitle' => 'Reservierung',
        'key'         => 'coords'
    )
);

// Sicht auswählen
if (!empty($params['view'])) {
    $view = $views[$params['view']];
}

// Titelzeile ausgeben
doc_title($modultitle);

// Ergebnisse ausgeben
foreach ($results as $result) {
    echo $result;
}

// Weiterleitung aktiv?
if (isset($redirect)) {
    echo 'Weiterleitung zu <a href="' . $redirect . '">Zielseite</a> ...';
    echo '<script> this.location = "' . $redirect . '"; </script>';
    exit;
}

// Suchmaske oder Daten ausgeben?
if (empty($params['view'])) {
    // Form beginnen
    start_form($modulname . "&amp;sid=" . $sid);
    // Tabelle beginnen
    start_table();
    // Bereich
    start_row("titlebg", "colspan='2'");
    echo "<b>Bereich:</b>";
    next_row("windowbg2", "style='width:30%;'");
    echo "Galaxie:<br>\n";
    next_cell("windowbg1");
    echo "von&nbsp;";
    echo "<input type='text' name='gal_start' value='" . $defaults['gal_start'] . "' style='width: 5em' maxlength='2'>";
    echo " bis&nbsp;";
    echo "<input type='text' name='gal_end' value='" . $defaults['gal_end'] . "' style='width: 5em' maxlength='2'>";
    next_row("windowbg2");
    echo "System:<br>\n";
    next_cell("windowbg1");
    echo "von&nbsp;";
    echo "<input type='text' name='sys_start' value='" . $defaults['sys_start'] . "' style='width: 5em' maxlength='3'>";
    echo " bis&nbsp;";
    echo "<input type='text' name='sys_end' value='" . $defaults['sys_end'] . "' style='width: 5em' maxlength='3'>";
    next_row("windowbg2");
    echo "Entfernung zum Stargate (Systeme):<br>\n";
    next_cell("windowbg1");
    echo "von&nbsp;";
    echo "<input type='text' name='sg_start' value='" . $defaults['sg_start'] . "' style='width: 5em' maxlength='3'>";
    echo " bis&nbsp;";
    echo "<input type='text' name='sg_end' value='" . $defaults['sg_end'] . "' style='width: 5em' maxlength='3'>";
    // Eigenschaften
    next_row("titlebg", "colspan='2'");
    echo '<b>Eigenschaften:';
    next_row("windowbg2");
    echo "Objekttyp:<br>\n";
    next_cell("windowbg1");
    echo "<select name='objekt'>\n";
    echo "<option value='Alle'>Alle</option>\n";
    echo "<option value='unbewohnt'>unbewohnt</option>\n";
    echo "<option value='bewohnt' selected='selected'>bewohnt</option>\n";
    echo "<option value='Kolonie'>Kolonie</option>\n";
    echo "<option value='Steinklumpen'>Steinklumpen</option>\n";
    echo "<option value='Asteroid'>Asteroid</option>\n";
    echo "<option value='Gasgigant'>Gasgigant</option>\n";
    echo "<option value='Eisplanet'>Eisplanet</option>\n";
    echo "<option value='Spezialplanet'>Spezialplanet</option>\n";
    echo "<option value='Basen'>alle Basen</option>\n";
    echo "<option value='Kampfbasis'>Kampfbasis</option>\n";
    echo "<option value='Sammelbasis'>Sammelbasis</option>\n";
    echo "<option value='Artefaktbasis'>Artefaktbasis</option>\n";
    echo "</select>\n";
    next_row("windowbg2");
    echo "Spielername (mehrere mit ; getrennt):<br>\n";
    echo "<i>Planeten eines bestimmten Spielers suchen</i>\n";
    next_cell("windowbg1");
    echo "<input type='text' name='user' style='width: 20em'>";
    next_row("windowbg2");
    echo "Allianzen (mehrere mit ; getrennt):<br>\n";
    echo "<i>Planeten einer bestimmten Allianz suchen</i>";
    next_cell("windowbg1");
    echo "<input type='text' name='alli' style='width: 20em'>";
    next_row("windowbg2");
    echo "Allianzstatus:<br>\n";
    next_cell("windowbg1");
    echo "<select name='allistatus[]' size='" . (count($config['statusalli']) + 1) . "' multiple='multiple'>";
    echo "<option " . (isset($config['statusalli']['Krieg']) ? "" : " selected='selected'") . " value='(kein)'>(kein)</option>";
    foreach ($config['statusalli'] as $key => $value) {
        echo "<option" . ($key == 'Krieg' ? " selected='selected'" : "") . " value='" . $key . "'>" . $key . "</option>";
    }
    echo "</select>";
    next_row("windowbg2");
    echo "Schiffsscan:<br>\n";
    next_cell("windowbg1");
    echo "von <input type='text' name='scan_schiff_age_min' style='width: 5em' maxlength='5'> bis&nbsp;<input type='text' name='scan_schiff_age_max' style='width: 5em' maxlength='5'> Stunden<br>";
    next_row("windowbg2");
    echo "Gebäudescan:<br>\n";
    next_cell("windowbg1");
    echo "von <input type='text' name='scan_geb_age_min'  style='width: 5em' maxlength='5'> bis&nbsp;<input type='text' name='scan_geb_age_max'  style='width: 5em' maxlength='5'> Stunden<br>";
    next_row("windowbg2");
    echo "Fehlgeschlagene Scans:<br>\n";
    next_cell("windowbg1");
    echo "von <input type='text' name='scan_failure_age_min'  style='width: 5em' maxlength='5'> bis&nbsp;<input type='text' name='scan_failure_age_max'  style='width: 5em' maxlength='5'> Stunden<br>";
    next_row("windowbg2");
    echo "Noobschutz:<br>\n";
    next_cell("windowbg1");
    echo "<input type='checkbox' name='no_noob' value='1' checked='checked'> Kein Noobschutz";

    next_row("windowbg2");
    echo "Inaktivität:<br>\n";
    next_cell("windowbg1");
    echo "seit <input type='text' name='inaktiv' style='width: 5em' maxlength='5'> Tagen <br>";

    next_row("windowbg2");
    echo "Verteidigung:<br>\n";
    next_cell("windowbg1");
    echo "von <input type='text' name='def_min' style='width: 5em' maxlength='5'> bis&nbsp;<input type='text' name='def_max' style='width: 5em' maxlength='5'>";
    next_row("windowbg2");
    echo "Rating:<br>\n";
    next_cell("windowbg1");
    echo "von <input type='text' name='rating_min' style='width: 5em' maxlength='5'> bis&nbsp;<input type='text' name='rating_max' style='width: 5em' maxlength='5'>";
    next_row("windowbg2");
    echo "Ressource:<br>\n";
    next_cell("windowbg1");
    echo "<select name='ressource'>\n";
    echo "<option value='Alle'>Alle</option>\n";
    echo "<option value='eisen'>Eisen</option>\n";
    echo "<option value='stahl'>Stahl</option>\n";
    echo "<option value='chemie'>Chemie</option>\n";
    echo "<option value='vv4a'>VV4A</option>\n";
    echo "<option value='eis'>Eis</option>\n";
    echo "<option value='wasser'>Wasser</option>\n";
    echo "<option value='energie'>Energie</option>\n";
    echo "</select>\n";
    echo "von <input type='text' name='ress_min' style='width: 10em' maxlength='10'>";
    next_row("windowbg2");
    echo "Status:<br>\n";
    next_cell("windowbg1");
    echo "<input type='checkbox' name='no_angriff' value='1' checked='checked'> Keine Angriffe";
    echo "<input type='checkbox' name='angriff' value='1'> Nur Angriffe<br>";
    echo "<input type='checkbox' name='no_sondierung' value='1' checked='checked''> Keine Sondierungen";
    echo "<input type='checkbox' name='sondierung' value='1'> Nur Sondierungen<br>";
    echo "<input type='checkbox' name='no_reservierung' value='1' checked='checked'> Keine Reservierungen";
    echo "<input type='checkbox' name='reservierung_user' value='1'> Eigene Reservierungen";
    echo "<input type='checkbox' name='reservierung_foreign' value='1'> Fremde Reservierungen<br>";
    next_row("titlebg", "colspan='2'");
    echo "<b>Ausgabe:</b>";
    next_row("windowbg2", "style='width:20%;'");
    echo "Ansicht:\n";
    next_cell("windowbg1");
    echo "<select name='view'>";
    // Ansichten auflisten
    foreach ($views as $viewkey => $view) {
        echo "<option value='" . $viewkey . "'>" . $view['title'] . "</option>\n";
    }
    echo "</select>";
    // Schaltflächen
    next_row("titlebg center", "colspan='2'");
    echo "<input type='submit' value='suchen' name='searchtargets' class='submit'>\n";
    // Tabelle beenden
    end_table();
    // Form beenden
    end_form();
} else {
    $data = array();
    // SQL-Abfrage aufbauen
    $sql_select = "SELECT ";
    $sql_select .= $db_tb_scans . ".coords";
    $sql_select .= "," . $db_tb_scans . ".coords_gal";
    $sql_select .= "," . $db_tb_scans . ".coords_sys";
    $sql_select .= "," . $db_tb_scans . ".coords_planet";
    $sql_select .= "," . $db_tb_scans . ".user";
    $sql_select .= "," . $db_tb_scans . ".allianz";
    $sql_select .= "," . $db_tb_scans . ".typ";
    $sql_select .= "," . $db_tb_scans . ".objekt";
    $sql_select .= "," . $db_tb_scans . ".schiffscantime";
    $sql_select .= "," . $db_tb_scans . ".gebscantime";
    $sql_select .= "," . $db_tb_scans . ".geoscantime";
    $sql_select .= "," . $db_tb_scans . ".fehlscantime";
    $sql_select .= "," . $db_tb_scans . ".time";
    $sql_select .= "," . $db_tb_scans . ".x11";
    $sql_select .= "," . $db_tb_scans . ".terminus";
    $sql_select .= "," . $db_tb_scans . ".x13";
    $sql_select .= "," . $db_tb_scans . ".eisen";
    $sql_select .= "," . $db_tb_scans . ".stahl";
    $sql_select .= "," . $db_tb_scans . ".vv4a";
    $sql_select .= "," . $db_tb_scans . ".chemie";
    $sql_select .= "," . $db_tb_scans . ".eis";
    $sql_select .= "," . $db_tb_scans . ".wasser";
    $sql_select .= "," . $db_tb_scans . ".energie";
    $sql_select .= "," . $db_tb_scans . ".geb";
    $sql_select .= "," . $db_tb_scans . ".def";
    $sql_select .= "," . $db_tb_scans . ".plan";
    $sql_select .= "," . $db_tb_scans . ".stat";
    $sql_select .= "," . $db_tb_scans . ".reserveraid";
    $sql_select .= "," . $db_tb_scans . ".reserveraiduser";
    $sql_select .= "," . $db_tb_scans . ".rnb";
    $sql_from = " FROM " . $db_tb_scans;
    // LEFT JOIN auf Spielerinfo
    $sql_select .= "," . $db_tb_highscore . ".pos";
    $sql_select .= "," . $db_tb_highscore . ".gebp_nodiff";
    $sql_select .= "," . $db_tb_highscore . ".dabei_seit";
    $sql_from .= " LEFT JOIN " . $db_tb_highscore . " ON " . $db_tb_scans . ".user=" . $db_tb_highscore . ".name";
    if (!empty($db_tb_raidview)) {
        $sql_select .= ",(SELECT date FROM " . $db_tb_raidview . " WHERE " . $db_tb_raidview . ".coords=" . $db_tb_scans . ".coords ORDER BY date DESC LIMIT 1) AS last_raid";
        $sql_select .= ",(SELECT link FROM " . $db_tb_raidview . " WHERE " . $db_tb_raidview . ".coords=" . $db_tb_scans . ".coords ORDER BY date DESC LIMIT 1) AS last_link";
    }
    // Filter
    $where = array();

    // Galaxie
    if (!empty($params['gal_start'])) {
        array_push($where, $db_tb_scans . '.coords_gal>=' . $params['gal_start']);
    }
    if (!empty($params['gal_end'])) {
        array_push($where, $db_tb_scans . '.coords_gal<=' . $params['gal_end']);
    }

    // System
    if (!empty($params['sys_start'])) {
        array_push($where, $db_tb_scans . '.coords_sys>=' . $params['sys_start']);
    }
    if (!empty($params['sys_end'])) {
        array_push($where, $db_tb_scans . '.coords_sys<=' . $params['sys_end']);
    }

    // SG-Nähe
    $sql_sg = "(SELECT MIN(ABS(" . $db_tb_scans . ".coords_sys-" . "$db_tb_sysscans" . ".sys)) FROM " . $db_tb_sysscans .
        " WHERE " . $db_tb_sysscans . ".gal=" . "$db_tb_scans" . ".coords_gal" .
        " AND " . $db_tb_sysscans . ".objekt='Stargate')";
    if (!empty($params['sg_start'])) {
        array_push($where, $sql_sg . '>=' . $params['sg_start']);
    }
    if (!empty($params['sg_end'])) {
        array_push($where, $sql_sg . '<=' . $params['sg_end']);
    }

    // Objekttyp
    switch ($params['objekt']) {
        case 'unbewohnt':
            array_push($where, $db_tb_scans . ".user=''");
            break;
        case 'bewohnt':
            array_push($where, $db_tb_scans . ".user<>''");
            break;
        case 'Kolonie':
            array_push($where, $db_tb_scans . ".objekt='Kolonie'");
            break;
        case 'Steinklumpen':
            //array_push($where, $db_tb_scans . ".objekt='Kolonie'");
            array_push($where, $db_tb_scans . ".typ='Steinklumpen'");
            break;
        case 'Asteroid':
            //array_push($where, $db_tb_scans . ".objekt='Kolonie'");
            array_push($where, $db_tb_scans . ".typ='Asteroid'");
            break;
        case 'Gasgigant':
            //array_push($where, $db_tb_scans . ".objekt='Kolonie'");
            array_push($where, $db_tb_scans . ".typ='Gasgigant'");
            break;
        case 'Eisplanet':
            //array_push($where, $db_tb_scans . ".objekt='Kolonie'");
            array_push($where, $db_tb_scans . ".typ='Eisplanet'");
            break;
        case 'Spezialplanet':
            //array_push($where, $db_tb_scans . ".objekt='Kolonie'");
            array_push($where, "(" . $db_tb_scans . ".typ='Asteroid' OR " . $db_tb_scans . ".typ='Gasgigant' OR " . $db_tb_scans . ".typ='Eisplanet')");
            break;
        case 'Basen':
            array_push($where, "(" . $db_tb_scans . ".objekt='Kampfbasis' OR " . $db_tb_scans . ".objekt='Sammelbasis' OR " . $db_tb_scans . ".objekt='Artefaktbasis')");
            break;
        case 'Kampfbasis':
            array_push($where, $db_tb_scans . ".objekt='Kampfbasis'");
            break;
        case 'Sammelbasis':
            array_push($where, $db_tb_scans . ".objekt='Sammelbasis'");
            break;
        case 'Artefaktbasis':
            array_push($where, $db_tb_scans . ".objekt='Artefaktbasis'");
            break;
    }

    // Spielername
    if (!empty($params['user'])) {
        $where[$db_tb_scans . '.user'] = explode(';', $params['user']);
    }

    // Allianzen
    if (!empty($params['alli'])) {
        $where[$db_tb_scans . '.allianz'] = explode(';', $params['alli']);
    }

    // Allianzstatus
    if (strstr($params['allistatus'], "(kein)") == false) {
        foreach ($config_allianzstatus as $allistatus => $allicolor) {
            if (strstr($params['allistatus'], $allistatus) != false) {
                foreach ($config['allistatus'] as $allianz => $configstatus) {
                    if ($allistatus == $configstatus) {
                        $allifilter[] = $allianz;
                    }
                }
            }
        }
        if (isset($allifilter)) {
            array_push($where, $db_tb_scans . ".allianz IN ('" . implode("','", $allifilter) . "')");
        }
    } else {
        foreach ($config_allianzstatus as $allistatus => $allicolor) {
            if (strstr($params['allistatus'], $allistatus) == false) {
                foreach ($config['allistatus'] as $allianz => $configstatus) {
                    if ($allistatus == $configstatus) {
                        $allifilter[] = $allianz;
                    }
                }
            }
        }
        if (isset($allifilter)) {
            array_push($where, $db_tb_scans . ".allianz NOT IN ('" . implode("','", $allifilter) . "')");
        }
    }

    // Schiffsscan
    if (!empty($params['scan_schiff_age_min'])) {
        $time = CURRENT_UNIX_TIME - ($params['scan_schiff_age_min'] + 1) * HOUR;
        array_push($where, $db_tb_scans . ".schiffscantime<" . $time);
    }
    if (!empty($params['scan_schiff_age_max'])) {
        $time = CURRENT_UNIX_TIME - ($params['scan_schiff_age_max'] + 1) * HOUR;
        array_push($where, $db_tb_scans . ".schiffscantime>" . $time);
    }

    // Gebscan
    if (!empty($params['scan_geb_age_min'])) {
        $time = CURRENT_UNIX_TIME - ($params['scan_geb_age_min'] + 1) * HOUR;
        array_push($where, $db_tb_scans . ".gebscantime<" . $time);
    }
    if (!empty($params['scan_geb_age_max'])) {
        $time = CURRENT_UNIX_TIME - ($params['scan_geb_age_max'] + 1) * HOUR;
        array_push($where, $db_tb_scans . ".gebscantime>" . $time);
    }

    // Fehlgeschlagene Scans
    if (!empty($params['scan_failure_age_min'])) {
        $time = CURRENT_UNIX_TIME - ($params['scan_failure_age_min'] + 1) * HOUR;
        array_push($where, $db_tb_scans . ".fehlscantime<" . $time);
    }
    if (!empty($params['scan_failure_age_max'])) {
        $time = CURRENT_UNIX_TIME - ($params['scan_failure_age_max'] + 1) * HOUR;
        array_push($where, $db_tb_scans . ".fehlscantime>" . $time);
    }

    // Noobstatus
    // copper will 22 Tage :)
    if (!empty($params['no_noob'])) {
        $time = CURRENT_UNIX_TIME - 22 * DAY;
        array_push($where, "(" . $db_tb_highscore . ".dabei_seit<" . $time . " OR " . $db_tb_highscore . ".dabei_seit IS NULL)");
    }
    // Inaktiv
    if (!empty($params['inaktiv'])) {
        $time = CURRENT_UNIX_TIME - ($params['inaktiv']) * DAY;
        array_push($where, "(" . $db_tb_highscore . ".gebp_nodiff<" . $time . " AND " . $db_tb_highscore . ".gebp_nodiff IS NOT NULL)");
    }

    // Angriff
    if (!empty($params['angriff'])) {
        array_push($where, $db_tb_scans . ".angriff>" . (CURRENT_UNIX_TIME - 15 * MINUTE));
    }
    if (!empty($params['no_angriff'])) {
        array_push($where, "(" . $db_tb_scans . ".angriff<" . CURRENT_UNIX_TIME . " OR " . $db_tb_scans . ".angriff IS NULL)");
    }

    // Sondierung
    if (!empty($params['sondierung'])) {
        array_push($where, $db_tb_scans . ".sondierung>" . CURRENT_UNIX_TIME);
    }
    if (!empty($params['no_sondierung'])) {
        array_push($where, "(" . $db_tb_scans . ".sondierung<" . CURRENT_UNIX_TIME . " OR " . $db_tb_scans . ".sondierung IS NULL)");
    }

    // Ressourcen Min
    if ($params['ressource'] != "Alle" AND $params['ress_min'] > 0) {
        array_push($where, $db_tb_scans . "." . $params['ressource'] . ">" . $params['ress_min']);
    }

    // Reservierung
    if (!empty($params['no_reservierung'])) {
        array_push($where, "(" . $db_tb_scans . ".reserveraid<" . CURRENT_UNIX_TIME . " OR " . $db_tb_scans . ".reserveraid IS NULL)");
    }
    if (!empty($params['reservierung_user']) && !empty($params['reservierung_foreign'])) {
        array_push($where, "(" . $db_tb_scans . ".reserveraid>" . CURRENT_UNIX_TIME . ")");
    } elseif (!empty($params['reservierung_user'])) {
        array_push($where, "(" . $db_tb_scans . ".reserveraid>" . CURRENT_UNIX_TIME . " AND " . $db_tb_scans . ".reserveraiduser='" . $user_sitterlogin . "')");
    } elseif (!empty($params['reservierung_foreign'])) {
        array_push($where, "(" . $db_tb_scans . ".reserveraid>" . CURRENT_UNIX_TIME . " AND " . $db_tb_scans . ".reserveraiduser<>'" . $user_sitterlogin . "')");
    }

    // WHERE-Clause aufbauen
    $first     = true;
    $sql_where = "";
    foreach ($where as $key => $clause) {
        if (is_array($clause)) {
            $sql_token = "";
            foreach ($clause as $clause_token) {
                if (!empty($sql_token)) {
                    $sql_token .= " OR ";
                }
                $sql_token .= $key . " LIKE '" . $clause_token . "'";
            }
            $sql_token = "(" . $sql_token . ")";
        } else {
            $sql_token = $clause;
        }
        if ($first) {
            $sql_where .= " WHERE ";
            $first = false;
        } else {
            $sql_where .= " AND ";
        }
        $sql_where .= $sql_token;
    }
    $sql_order = "";
    // SQL ausführen
    $count = 0;
    $sql   = $sql_select . $sql_from . $sql_where . $sql_order;
    debug_var("sql", $sql);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    debug_var("Ergebnisse in der DB", $db->db_num_rows($result));
    while ($row = $db->db_fetch_array($result)) {
        unset($sd_01);
        unset($sd_02);
        unset($sd_x11);
        unset($sd_terminus);
        unset($sd_x13);
        unset($grav);
        unset($plasma);
        unset($arak);
        unset($rak);
        unset($pulssat);
        unset($lasersat);
        unset($gauss);
        unset($raksat);
        unset($sd01);
        unset($sd02);
        unset($ress);
        unset($gesamt);
        unset($comment);
        if (!empty($row['rnb'])) {
            $comment = "<div style='color: blue'>" . $row['rnb'] . "</div>";
        } else {
            $comment = "";
        }
        // Reservierter Raid
        if ($row['reserveraid'] > CURRENT_UNIX_TIME) {
            $comment .= "<div style='color: #808080'>Reserviert von " . $row['reserveraiduser'] . " bis " . strftime(CONFIG_DATETIMEFORMAT, $row['reserveraid']) . "</div>";
            $text_color = "color: #808080;";
        } else {
            $text_color = "";
        }
        // Angriff
        $angriff_time = "";
        $angriff_from = "";
        if (!empty($db_tb_lieferung)) {
            $sql_angriff = "SELECT art, user_from, time, schiffe FROM " . $db_tb_lieferung . " WHERE";
            $sql_angriff .= " coords_to_gal=" . $row['coords_gal'] . " AND";
            $sql_angriff .= " coords_to_sys=" . $row['coords_sys'] . " AND";
            $sql_angriff .= " coords_to_planet=" . $row['coords_planet'] . " AND";
            $sql_angriff .= " (art='Angriff' OR art='Sondierung' OR art='Sondierung (Schiffe/Def/Ress)' OR art='Sondierung (Gebäude/Ress)') AND";
            $sql_angriff .= " time>" . (CURRENT_UNIX_TIME - 15 * MINUTE);
            $sql_angriff .= " ORDER BY time DESC";
            $result_angriff = $db->db_query($sql_angriff)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            while ($row_angriff = $db->db_fetch_array($result_angriff)) {
                if ($row_angriff['art'] == 'Angriff') {
                    $comment .= "<div style='color: red'>Angriff von " . $row_angriff['user_from'] . " bis " . strftime(CONFIG_DATETIMEFORMAT, $row_angriff['time']) . "</div>";
                    $text_color = "color: red;";
                } else {
                    $comment .= "<div style='color: #CC6600'>Sondierung von " . $row_angriff['user_from'] . " bis " . strftime(CONFIG_DATETIMEFORMAT, $row_angriff['time']) . " mit " . $row_angriff['schiffe'] . "</div>";
                    if (empty($text_color) || $text_color = "color: #808080;") {
                        $text_color = "color: #CC6600";
                    }
                }
            }
        }
        // Datensatz ist aufgeklappt?
        $expanded = $params['expand'] == $key;
        // Letzte Scantime
        $last_scan = null;
        if (($row['geoscantime'] > $last_scan && !empty($row['geoscantime'])) || empty($last_scan)) {
            $last_scan = $row['geoscantime'];
        }
        if (($row['schiffscantime'] > $last_scan && !empty($row['schiffscantime'])) || empty($last_scan)) {
            $last_scan = $row['schiffscantime'];
        }
        if (($row['gebscantime'] > $last_scan && !empty($row['gebscantime'])) || empty($last_scan)) {
            $last_scan = $row['gebscantime'];
        }
        if (($row['fehlscantime'] > $last_scan && !empty($row['fehlscantime'])) || empty($last_scan)) {
            $last_scan = $row['fehlscantime'];
        }
        // Verteidigungs-Objekte parsen
        $lines   = explode("\n", $row['def']);
        $lines   = array_merge($lines, explode("\n", $row['plan']));
        $lines   = array_merge($lines, explode("\n", $row['stat']));
        $lines   = array_merge($lines, explode("\n", $row['geb']));
        $mode    = 'block';
        $objects = array();
        foreach ($lines as $line) {
            if ($mode == 'object') {
                $objekt = $line;
            } elseif ($mode == 'value') {
                $objects[$objekt] = $line;
            }
            if (strpos($line, 'scan_object')) {
                $mode = 'object';
            } elseif (strpos($line, 'scan_value')) {
                $mode = 'value';
            } else {
                $mode = 'block';
            }
        }
        $sd_01     = isset($objects['SD01 Gatling']) ? $objects['SD01 Gatling'] : 0;
        $sd_02     = isset($objects['SD02 Pulslaser']) ? $objects['SD02 Pulslaser'] : 0;
        $tsonden   = ceil(($sd_01 / 1.2 + $sd_02 * 2.5 / 1.2 + 10));
        $x13sonden = ceil(($sd_01 / 2 + $sd_02 * 2.5 / 2 + 8));
        // Fehlgeschlagene Scans
        if ($last_scan == $row['fehlscantime']) {
            // Mit welchem Sondentyp schlug es fehl?
            if (!empty($row['terminus'])) {
                $tsonden   = ">" . $row['terminus'];
                $x13sonden = ceil((($row['terminus'] - 10) * 0.6) + 8);
            }
            if (!empty($row['x13'])) {
                $x13sonden = ">" . $row['x13'];
                $tsonden   = ceil((($row['x13'] - 8) * 1.67) + 10);
            }
        }
        // Verteidigungsanlagen
        $deff_pla = null;
        $anlagen  = "";
        if (!empty($row['schiffscantime'])) {
            $grav = isset($objects['SDI Gravitonbeam']) ? $objects['SDI Gravitonbeam'] : 0;
            if (!empty($grav)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $grav . " Grav";
            }
            $plasma = isset($objects['SDI Plasmalaser']) ? $objects['SDI Plasmalaser'] : 0;
            if (!empty($plasma)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $plasma . " Plasma";
            }
            $arak = isset($objects['SDI Atomraketen']) ? $objects['SDI Atomraketen'] : 0;
            if (!empty($arak)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $arak . " ARak";
            }
            $rak = isset($objects['SDI Raketensystem']) ? $objects['SDI Raketensystem'] : 0;
            if (!empty($rak)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $rak . " Rak";
            }
            $pulssat = isset($objects['PulslaserSat']) ? $objects['PulslaserSat'] : 0;
            if (!empty($pulssat)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $pulssat . " PulsSat";
            }
            $lasersat = isset($objects['LaserSat']) ? $objects['LaserSat'] : 0;
            if (!empty($lasersat)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $lasersat . " LaserSat";
            }
            $gauss = isset($objects['Gausskanonensatellit']) ? $objects['Gausskanonensatellit'] : 0;
            if (!empty($gauss)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $gauss . " Gauss";
            }
            $raksat = isset($objects['Raketensatellit']) ? $objects['Raketensatellit'] : 0;
            if (!empty($raksat)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $raksat . " RakSat";
            }
            $sd01 = isset($objects['SD01 Gatling']) ? $objects['SD01 Gatling'] : 0;
            if (!empty($sd01)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $sd01 . " SD01";
            }
            $sd02 = isset($objects['SD02 Pulslaser']) ? $objects['SD02 Pulslaser'] : 0;
            if (!empty($sd02)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $sd02 . " SD02";
            }
            $stopfi = isset($objects['Stopfentenwerfer']) ? $objects['Stopfentenwerfer'] : 0;
            if (!empty($stopfi)) {
                $anlagen .= (!empty($anlagen) ? " " : "") . $stopfi . " Stopfi";
            }
            $deff_pla = $grav * 480 + $plasma * 300 + $arak * 15 + $rak * 10 + $pulssat * 55 + $lasersat * 35 + $gauss * 25 + $raksat * 25 + $stopfi * 1;
        }
        // Werften
        $klorw   = isset($objects['kleine orbitale Werft']) ? $objects['kleine orbitale Werft'] : ($row['geb'] == "" ? '---' : 0);
        $klplw   = isset($objects['kleine planetare Werft']) ? $objects['kleine planetare Werft'] : ($row['geb'] == "" ? '---' : 0);
        $miorw   = isset($objects['mittlere orbitale Werft']) ? $objects['mittlere orbitale Werft'] : ($row['geb'] == "" ? '---' : 0);
        $miplw   = isset($objects['mittlere planetare Werft']) ? $objects['mittlere planetare Werft'] : ($row['geb'] == "" ? '---' : 0);
        $grw     = isset($objects['große Werft']) ? $objects['große Werft'] : ($row['geb'] == "" ? '---' : 0);
        $dnw     = isset($objects['DN Werft']) ? $objects['DN Werft'] : ($row['geb'] == "" ? '---' : 0);
        $werften = $klorw + $klplw + $miorw + $miplw + $grw + $dnw;
        // Sichtweite
        $gala    = isset($objects['orbitaler Galaxienscanner']) ? $objects['orbitaler Galaxienscanner'] : ($row['geb'] == "" ? '---' : 0);
        $flotten = isset($objects['Flottenscanner']) ? $objects['Flottenscanner'] : ($row['geb'] == "" ? '---' : 0);
        // Ziele
        $koze = isset($objects['Kolonisationszentrum']) ? $objects['Kolonisationszentrum'] : ($row['geb'] == "" ? '---' : 0);
        $rmk  = isset($objects['Roboterminenkomplex']) ? $objects['Roboterminenkomplex'] : ($row['geb'] == "" ? '---' : 0);
        // Verteidigung
        $pla_pu = isset($objects['Panzerungsupdate Planetar']) ? $objects['Panzerungsupdate Planetar'] : ($row['geb'] == "" ? '---' : 0);
        $orb_pu = isset($objects['Panzerungsupdate Orbital']) ? $objects['Panzerungsupdate Orbital'] : ($row['geb'] == "" ? '---' : 0);
        $alpha  = isset($objects['planetares Alpha Schild']) ? $objects['planetares Alpha Schild'] : ($row['geb'] == "" ? '---' : 0);
        $beta   = isset($objects['planetares Beta Schild']) ? $objects['planetares Beta Schild'] : ($row['geb'] == "" ? '---' : 0);
        // Inaktiv
        $inaktiv = isset($row['gebp_nodiff']) ? $row['gebp_nodiff'] : "---";
        // Schiffe
        $deff_schiff = null;
        $deff_gesamt = null;
        $schiffe     = "";
        if (!empty($row['schiffscantime'])) {
            $deff_schiff = 0;
            foreach ($config['schiffsangriff'] as $name => $angriff) {
                if (isset($objects[$name]) && !empty($angriff)) {
                    $deff_schiff += $objects[$name] * $angriff;
                    $schiffe .= (!empty($schiffe) ? " " : "") . makeAmount($objects[$name]) . " " . $name;
                }
            }
            // Verteidigung
            $deff_gesamt = $deff_pla + $deff_schiff;
        }
        // Verteidigungsfilter
        if (!empty($params['def_min']) AND (is_null($deff_gesamt) OR ($deff_gesamt < $params['def_min']))) {
            continue;
        }
        if (!empty($params['def_min']) AND (is_null($deff_gesamt) OR ($deff_gesamt > $params['def_min']))) {
            continue;
        }
        //ToDo: Ressgewichtung einstellbar machen
        // Ressourcen zählen
        $ress = null;
        if (!empty($row['schiffscantime']) OR !empty($row['gebscantime'])) {
            $ress = $row['eisen'] + $row['stahl'] * 2 + $row['chemie'] + $row['vv4a'] * 4 + $row['eis'] * 2 + $row['wasser'] * 2 + $row['energie'] * 0.1;
        }

        $rating = $ress;
        if (!empty($deff_gesamt) AND !is_null($ress)) {
            $rating /= $deff_gesamt;
        }
        if (!empty($rating)) {
            $rating = round($rating);
        }
        // Rating Filter
        if (!empty($params['rating_min']) AND (is_null($rating) OR ($rating < $params['rating_min']))) {
            continue;
        }
        if (!empty($params['rating_max']) AND (is_null($rating) OR ($rating > $params['rating_max']))) {
            continue;
        }
        // Hintergrund-Farbe festlegen
        $allianz_background_color = "background-color: white;";
        if (isset($config['allistatus'][$row['allianz']])) {
            if (isset($config_allianzstatus[$config['allistatus'][$row['allianz']]])) {
                $allianz_background_color = "background-color: " . $config_allianzstatus[$config['allistatus'][$row['allianz']]] . ";";
            }
        }

        $data[$row['coords']] = array(
            'coords'            => $row['coords'],
            'gal'               => $row['coords_gal'],
            'sys'               => $row['coords_sys'],
            'pla'               => $row['coords_planet'],
            'user'              => $row['user'],
            'allianz'           => $row['allianz'],
            'pos'               => $row['pos'],
            'planetentyp'       => $row['typ'],
            'objekttyp'         => $row['objekt'],
            'dabei_seit'        => $row['dabei_seit'],
            'last_scan'         => $last_scan,
            'fehlscantime'      => $row['fehlscantime'],
            'schiffscantime'    => $row['schiffscantime'],
            'gebscantime'       => $row['gebscantime'],
            'geoscantime'       => $row['geoscantime'],
            'last_raid'         => $row['last_raid'],
            'last_link'         => $row['last_link'],
            'inaktiv'           => $inaktiv,
            'sd_01'             => $sd_01,
            'sd_02'             => $sd_02,
            'eisen'             => $row['eisen'],
            'stahl'             => $row['stahl'],
            'vv4a'              => $row['vv4a'],
            'chemie'            => $row['chemie'],
            'eis'               => $row['eis'],
            'wasser'            => $row['wasser'],
            'energie'           => $row['energie'],
            'ress'              => $ress,
            'deff_schiff'       => $deff_schiff,
            'deff_pla'          => $deff_pla,
            'deff_gesamt'       => $deff_gesamt,
            'rating'            => $rating,
            'geb'               => $row['geb'],
            'plan'              => $row['plan'],
            'stat'              => $row['stat'],
            'def'               => $row['def'],
            'klplw'             => $klplw,
            'klorw'             => $klorw,
            'miplw'             => $miplw,
            'miorw'             => $miorw,
            'grw'               => $grw,
            'dnw'               => $dnw,
            'werften'           => $werften,
            'gala'              => $gala,
            'flotten'           => $flotten,
            'koze'              => $koze,
            'rmk'               => $rmk,
            'orb_pu'            => $orb_pu,
            'pla_pu'            => $pla_pu,
            'alpha'             => $alpha,
            'beta'              => $beta,
            'schiffe'           => $schiffe,
            'anlagen'           => $anlagen,
            'comment'           => $comment,
            'tsonden'           => $tsonden,
            'x13sonden'         => $x13sonden,
            'allianz_style'     => $allianz_background_color,
            'dabei_seit_style'  => "text-align: right; " . $text_color,
            'last_scan_style'   => "text-align: right; " . $text_color,
            'last_raid_style'   => "text-align: right; " . $text_color,
            'inaktiv_style'     => "text-align: right; " . $text_color,
            'sd_01_style'       => "text-align: right; " . $text_color,
            'sd_02_style'       => "text-align: right; " . $text_color,
            'tsonden_style'     => "text-align: right; " . $text_color,
            'x13sonden_style'   => "text-align: right; " . $text_color,
            'eisen_style'       => "text-align: right; " . $text_color,
            'stahl_style'       => "text-align: right; " . $text_color,
            'vv4a_style'        => "text-align: right; " . $text_color,
            'chemie_style'      => "text-align: right; " . $text_color,
            'eis_style'         => "text-align: right; " . $text_color,
            'wasser_style'      => "text-align: right; " . $text_color,
            'energie_style'     => "text-align: right; " . $text_color,
            'ress_style'        => "text-align: right; " . $text_color,
            'deff_schiff_style' => "text-align: right; " . $text_color,
            'deff_pla_style'    => "text-align: right; " . $text_color,
            'gesamt_style'      => "text-align: right; " . $text_color,
            'rating_style'      => "text-align: right; ",
            'row_style'         => $text_color,
        );
    }
    debug_var("Ergebnisse nach Filterung", count($data));

    usort($data, "sort_data_cmp");
    // Daten ausgeben
    start_table();
    start_row("titlebg top");
    foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
        if (!isset($view['group'][$viewcolumnkey]) && !isset($filters[$viewcolumnkey])) {
            next_cell("titlebg top");
            $orderkey = $viewcolumnkey;
            if (isset($view['sortcolumns'][$orderkey])) {
                $orderkey = $view['sortcolumns'][$orderkey];
            }
            echo makelink(
                array(
                     'order'  => $orderkey,
                     'orderd' => 'asc'
                ),
                "<img src='./bilder/asc.gif'>"
            );
            echo '<b>' . $viewcolumnname . '</b>';
            echo makelink(
                array(
                     'order'  => $orderkey,
                     'orderd' => 'desc'
                ),
                "<img src='./bilder/desc.gif'>"
            );
        }
    }
    if (isset($view['edit'])) {
        next_cell("titlebg top");
        echo '&nbsp;';
    }
    next_cell("titlebg top");
    echo '&nbsp;';
    echo '<form method="POST">';
    $index           = 0;
    $to_much_results = false;
    foreach ($data as $row) {

        //ToDo: besser lösen (Limit der von der DB gelieferten Ergebnisse)
        if ($index >= $max_results) {
            $to_much_results = true;
            break;
        }

        $key      = $row[$view['key']];
        $expanded = $params['expand'] == $key;
        $index++;

        echo '<input type="hidden" name="target_' . $index . '" value="' . $key . '"/>';
        if (isset($row['row_style'])) {
            next_row("windowbg1", 'nowrap valign=center style="' . $row['row_style'] . '"');
        } else {
            next_row("windowbg1", 'nowrap valign=center');
        }
        // Schaltfläche zum auf-/zuklappen
        echo "<a href=\"javascript:Collapse('" . $key . "');\"><img src='bilder/plus.gif' alt='' id='collapse_" . $key . "'></a>";
        foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
            if (isset($row[$viewcolumnkey . '_style'])) {
                next_cell("windowbg1", 'nowrap valign=center style="' . $row[$viewcolumnkey . '_style'] . '"');
            } elseif (isset($row['row_style'])) {
                next_cell("windowbg1", 'nowrap valign=center style="' . $row['row_style'] . '"');
            } else {
                next_cell("windowbg1", 'nowrap valign=center');
            }
            echo format_value($row, $viewcolumnkey, $row[$viewcolumnkey]);
        }
        // Editbuttons ausgeben
        if (isset($view['edit'])) {
            if (isset($row['row_style'])) {
                next_cell("windowbg1", 'nowrap valign=center style="' . $row['row_style'] . '"');
            } else {
                next_cell("windowbg1", 'nowrap valign=center');
            }
            if (!isset($row['allow_edit']) || $row['allow_edit']) {
                echo makelink(
                    array('edit' => $key),
                    "<img src='bilder/file_edit_s.gif' alt='bearbeiten'>"
                );
            }
            if (!isset($row['allow_delete']) || $row['allow_delete']) {
                echo makelink(
                    array('delete' => $key),
                    "<img src='bilder/file_delete_s.gif' onclick='return confirmlink(this, '" .
                        (isset($view['deletetitle']) ? $view['deletetitle'] : 'Datensatz') . " wirklich löschen?')' alt='loeschen'>"
                );
            }
        }
        // Markierbuttons ausgeben
        next_cell("windowbg1 top");
        echo '<input type="checkbox" name="mark_' . $key . '" ';
        if (getVar("mark_all")) {
            echo 'value=true checked';
        }
        echo '>';
        // Kommentarbereich ausgeben
        if (!empty($row['comment'])) {
            next_row("", "style='border-width: 0; margin: 0; padding: 4px; background-color: white;'");
            echo "";
            next_cell("windowbg1 center", "style='border-width: 0; margin: 0; padding: 4px; background-color: white;'", count($view['columns']) + 1);
            echo "";
            start_table();
            echo $row['comment'];
            end_table();
            next_cell("windowbg1", "");
            echo "";
        }
        // Expandbereich ausgeben

        echo "<tr id='row_" . $key . "' style='display: none; border-width: 0; margin: 0; padding: 4; background-color: white;'>";
        echo "<td colspan='23'>";
        start_table();
        if (!empty($row['schiffscantime']) OR !empty($row['gebscantime'])) {
            start_row("titlebg", "nowrap valign=top", 2);
            echo '<b>auf Lager:</b>';
            next_row("windowbg2", "style='width: 20%'");
            echo 'Eisen:';
            next_cell("windowbg1");
            echo number_format((float)$row['eisen'], 0, ",", '.');
            next_row("windowbg2", "style='width: 20%'");
            echo 'Stahl:';
            next_cell("windowbg1");
            echo number_format((float)$row['stahl'], 0, ",", '.');
            next_row("windowbg2", "style='width: 20%'");
            echo 'VV4A:';
            next_cell("windowbg1");
            echo number_format((float)$row['vv4a'], 0, ",", '.');
            next_row("windowbg2", "style='width: 20%'");
            echo 'Chemie:';
            next_cell("windowbg1");
            echo number_format((float)$row['chemie'], 0, ",", '.');
            next_row("windowbg2", "style='width: 20%'");
            echo 'Eis:';
            next_cell("windowbg1");
            echo number_format((float)$row['eis'], 0, ",", '.');
            next_row("windowbg2", "style='width: 20%'");
            echo 'Wasser:';
            next_cell("windowbg1");
            echo number_format((float)$row['wasser'], 0, ",", '.');
            next_row("windowbg2", "style='width: 20%'");
            echo 'Energie:';
            next_cell("windowbg1");
            echo number_format((float)$row['energie'], 0, ",", '.');
            start_row("titlebg", "nowrap valign=top", 2);
            echo '<b>benötigte Frachtkapazität:</b>';
            next_row("windowbg2", "style='width: 20%'");
            echo 'Klasse 1:';
            next_cell("windowbg1");

            $kapazitaet = $row['eisen'] + $row['stahl'] * 2 + $row['chemie'] * 3 + $row['vv4a'] * 4;
            echo number_format((float)$kapazitaet, 0, ",", '.');
            echo " (" . number_format((float)ceil($kapazitaet / 5000), 0, ",", '.') . " Systrans";
            echo ", " . number_format((float)ceil($kapazitaet / 20000), 0, ",", '.') . " Gorgol";
            echo ", " . number_format((float)ceil($kapazitaet / 75000), 0, ",", '.') . " Kamel";
            echo ", " . number_format((float)ceil($kapazitaet / 400000), 0, ",", '.') . " Flughund)";
            next_row("windowbg2", "style='width: 20%'");
            echo 'Klasse 2:';
            next_cell("windowbg1");
            $kapazitaet = $row['eis'] * 2 + $row['wasser'] * 2 + $row['energie'];
            echo number_format((float)$kapazitaet, 0, ",", '.');
            echo " (" . number_format((float)ceil($kapazitaet / 2000), 0, ",", '.') . " Lurch";
            echo ", " . number_format((float)ceil($kapazitaet / 10000), 0, ",", '.') . " Eisbär";
            echo ", " . number_format((float)ceil($kapazitaet / 50000), 0, ",", '.') . " Waschbär";
            echo ", " . number_format((float)ceil($kapazitaet / 250000), 0, ",", '.') . " Seepferdchen)";
        }
        if (!empty($row['geb'])) {
            start_row("titlebg", "nowrap valign=top", 2);
            echo '<b>Gebäude:</b>';
            next_row("windowbg1", "", 2);
            echo $row['geb'];
        }
        if (!empty($row['plan'])) {
            start_row("titlebg", "nowrap valign=top", 2);
            echo '<b>planetare Flotte:</b>';
            next_row("windowbg1", "", 2);
            echo $row['plan'];
        }
        if (!empty($row['stat'])) {
            start_row("titlebg", "nowrap valign=top", 2);
            echo '<b>stationierte Flotte:</b>';
            next_row("windowbg1", "", 2);
            echo $row['stat'];
        }
        if (!empty($row['def'])) {
            start_row("titlebg", "nowrap valign=top", 2);
            echo '<b>Verteidigung:</b>';
            next_row("windowbg1", "", 2);
            echo $row['def'];
        }
        end_table();
        echo "</td>";
        echo "</tr>";
    }
    end_table();
    debug_var("Ausgaben", $index);

    if ($to_much_results) {
        echo "<br><div class='system_notification'>Es wurden nur die ersten {$max_results} Ergebnisse angezeigt. Bitte die Suche weiter einschränken.</div><br>";
    }
    echo '<table border="0" cellpadding="2" cellspacing="1" style="width: 100%;">';
    echo '<tr><td class="right">';
    echo makelink(array('mark_all' => true), "Alle auswählen");
    echo ' / ';
    echo makelink(array('mark_all' => false), "Auswahl entfernen");
    echo '</td>';
    echo '</tr>';
    echo '<tr><td class="right">';
    echo '<input type="submit" value="Universum" name="universum"> ';
    echo '<input type="submit" value="Flotte versenden" name="flotteversenden">';
    echo '</td>';
    echo '</table>';
    echo '</form>';
    // Legende ausgeben
    echo '<br><table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">';
    echo '<tr>';
    echo '<td style="width: 30; background-color: #C4F493;"></td>';
    echo '<td class="windowbg1" style="width: 70;">own</td>';
    echo '<td style="width: 30; background-color: #E6F6A5;"></td>';
    echo '<td class="windowbg1" style="width: 70;">wing</td>';
    echo '<td style="width: 30; background-color: #7C9CF1;"></td>';
    echo '<td class="windowbg1" style="width: 70;">NAP</td>';
    echo '<td style="width: 30; background-color: #8DADF2;"></td>';
    echo '<td class="windowbg1" style="width: 70;">iNAP</td>';
    echo '<td style="width: 30; background-color: #4A71D5;"></td>';
    echo '<td class="windowbg1" style="width: 70;">VB</td>';
    echo '<td style="width: 30; background-color: #E84528;"></td>';
    echo '<td class="windowbg1" style="width: 70;">Krieg</td>';
    echo '<td style="width: 30; background-color: #CCBB11;"></td>';
    echo '<td class="windowbg1" style="width: 70;">noraid</td>';
    echo '<td class="windowbg1" style="width: 70; color: #808080;">Reserviert</td>';
    echo '<td class="windowbg1" style="width: 70; color: #FF0000;">Angriff</td>';
    echo '</tr>';
    echo '</table>';
    // Maske ausgeben
    if (isset($params['edit']) && !empty($params['edit'])) {
        echo '<br>';
        echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p>' . "\n";
        start_table();
        next_row("titlebg", 'nowrap valign=top colspan=2');
        if (!isset($view['edittitle'])) {
            echo "<b>" . $view['title'];
        } else {
            echo "<b>" . $view['edittitle'];
        }
        echo " bearbeiten";
        echo '<input type="hidden" name="edit" value="' . $params['edit'] . '">' . "\n";
        echo "</b>";
        foreach ($view['edit'] as $key => $field) {
            next_row("windowbg2 top");
            echo $field['title'];
            if (isset($field['desc'])) {
                echo '<br><i>' . $field['desc'] . '</i>';
            }
            next_cell('windowbg1', 'style="width: 100%;"');
            if (is_array($field['type'])) {
                $first = true;
                foreach ($field['type'] as $key => $field) {
                    if (!$first) {
                        echo '&nbsp;';
                    }
                    echo makeField($field, $key);
                    $first = false;
                }
            } else {
                echo makeField($field, $key);
            }
        }
        next_row('titlebg center', 'colspan=2');
        echo '<input type="submit" value="speichern" name="button_edit"> ';
        end_table();
        echo '</form>';
    }
}

//****************************************************************************
//
// Vergleichsfunktion für das sortieren
function sort_data_cmp($a, $b)
{
    global $params;

    if ($params['order'] == 'coords') {
        $coordsA = explode(':', $a['coords']);
        $coordsB = explode(':', $b['coords']);
        $result  = 0;
        if ($coordsA[0] < $coordsB[0]) {
            $result = -1;
        } elseif ($coordsA[0] > $coordsB[0]) {
            $result = 1;
        }
        if ($result == 0 && ($coordsA[1] < $coordsB[1])) {
            $result = -1;
        } elseif ($result == 0 && ($coordsA[1] > $coordsB[1])) {
            $result = 1;
        }
        if ($result == 0 && ($coordsA[2] < $coordsB[2])) {
            $result = -1;
        } elseif ($result == 0 && ($coordsA[2] > $coordsB[2])) {
            $result = 1;
        }
    } else {
        $valA = strtoupper($a[$params['order']]);
        $valB = strtoupper($b[$params['order']]);
        if ($valA < $valB) {
            $result = -1;
        } elseif ($valA > $valB) {
            $result = 1;
        } else {
            $result = 0;
        }
    }
    if ($params['orderd'] == 'desc') {
        $result *= -1;
    }

    return $result;
}

//****************************************************************************
//
// Spalte formatieren
function format_value($row, $name, $value)
{
    global $view, $sid, $params;
    if ($value == '---') {
        return $value;
    }
    switch ($name) {
        case 'user':
            return makelink(
                array(
                     "view" => $params['view'],
                     "user" => $value,
                     "sid"  => $sid,
                ), $value, true
            );
        case 'coords':
        case 'gal':
        case 'sys':
        case 'pla':
            return "<a href='index.php?action=showplanet&amp;coords=" . $row['coords'] . "&amp;ansicht=auto&amp;sid=" . $sid . "'>" . $value . "</a>";
        case 'planetentyp':
            if ($value == "Steinklumpen") {
                return "<a href='' title='Steinklumpen'>S</a>";
            } elseif ($value == 'Asteroid') {
                return "<a href='' title='Steinklumpen'>A</a>";
            } elseif ($value == 'Gasgigant') {
                return "<a href='' title='Steinklumpen'>G</a>";
            } elseif ($value == 'Eisplanet') {
                return "<a href='' title='Steinklumpen'>E</a>";
            } else {
                return $value;
            }
            break;
        case 'objekttyp':
            if ($value == 'Kolonie') {
                return "<alt title='Kolonie'><img src='bilder/kolo.png'></a>";
            } elseif ($value == 'Sammelbasis') {
                return "<alt title='Sammelbasis'><img src='bilder/ress_basis.png'></a>";
            } elseif ($value == 'Kampfbasis') {
                return "<alt title='Kampfbasis'><img src='bilder/kampf_basis.png'></a>";
            } elseif ($value == 'Artefaktbasis') {
                return "<alt title='Artefaktbasis'><img src='bilder/artefakt_basis.png'></a>";
            }
            break;
        case 'eisen':
            return "<alt title='" . number_format((float)$value, 0, ",", '.') . " Eisen'>" . makeAmount($value) . "</a>";
        case 'stahl':
            return "<alt title='" . number_format((float)$value, 0, ",", '.') . " Stahl'>" . makeAmount($value) . "</a>";
        case 'vv4a':
            return "<alt title='" . number_format((float)$value, 0, ",", '.') . " VV4A'>" . makeAmount($value) . "</a>";
        case 'chemie':
            return "<alt title='" . number_format((float)$value, 0, ",", '.') . " Chemie'>" . makeAmount($value) . "</a>";
        case 'eis':
            return "<alt title='" . number_format((float)$value, 0, ",", '.') . " Eis'>" . makeAmount($value) . "</a>";
        case 'wasser':
            return "<alt title='" . number_format((float)$value, 0, ",", '.') . " Wasser'>" . makeAmount($value) . "</a>";
        case 'energie':
            return "<alt title='" . number_format((float)$value, 0, ",", '.') . " Energie'>" . makeAmount($value) . "</a>";
        case 'ress':
            /*$title = "";
            if (!empty($row['eisen']))
                $title .= (!empty($title) ? " " : "") . "Eisen: " . makeAmount($row['eisen']);
            if (!empty($row['stahl']))
                $title .= (!empty($title) ? " " : "") . "Stahl: " . makeAmount($row['stahl']);
            if (!empty($row['vv4a']))
                $title .= (!empty($title) ? " " : "") . "VV4A: " . makeAmount($row['vv4a']);
            if (!empty($row['chemie']))
                $title .= (!empty($title) ? " " : "") . "Chemie: " . makeAmount($row['chemie']);
            if (!empty($row['wasser']))
                $title .= (!empty($title) ? " " : "") . "Wasser: " . makeAmount($row['wasser']);
            if (!empty($row['energie']))
                $title .= (!empty($title) ? " " : "") . "Energie: " . makeAmount($row['energie']);
            return "<alt title='" . $title . "'>" . number_format((float)$value, 0, ',', '.') . "</a>";*/
            return makeAmount($value);
        //case 'punkte':
        case 'deff_gesamt':
            return number_format((float)$value, 0, ',', '.');
        case 'deff_schiff':
            if (is_null($value)) {
                return '';
            }

            return "<alt title='" . $row['schiffe'] . "'>" . number_format((float)$value, 0, ',', '.') . "</a>";
        case 'deff_pla':
            if (is_null($value)) {
                return '';
            }

            return "<alt title='" . $row['anlagen'] . "'>" . number_format((float)$value, 0, ',', '.') . "</a>";
        case 'tsonden':
        case 'x13sonden':
            if (substr($value, 0, 1) == ">") {
                return '<span class="ranking_red">' . $value . '</span>';
            } else {
                return '<span class="ranking_green">' . $value . '</span>';
            }
        case 'inaktiv':
            $diff = CURRENT_UNIX_TIME - $value;
            if ($diff > 2 * DAY) {
                return '<span class="ranking_red">' . makeduration($value) . '</span>';
            } elseif ($diff > DAY) {
                return '<span class="ranking_yellow">' . makeduration($value) . '</span>';
            } else {
                return '<span class="ranking_green">' . makeduration($value) . '</span>';
            }
        case 'dabei_seit':
            $diff = CURRENT_UNIX_TIME - $value;
            if (($diff > DAY * 20) && ($diff <= DAY * 21)) {
                return '<span class="ranking_yellow">' . makeduration($value) . '</span>';
            } elseif ($diff > DAY * 21) {
                return '<span class="ranking_green">' . makeduration($value) . '</span>';
            } else {
                return '<span class="ranking_red">' . makeduration($value) . '</span>';
            }
        case 'last_scan':
            $result = "<table width='100%'><tr><td nowrap width='100%'>";
            if (!empty($row['geoscantime'])) {
                $result .= "<alt title='Geoscan vor " . makeduration($row['geoscantime']) . "'><img src='bilder/scann_geo.png'></alt> ";
            }
            if (!empty($row['schiffscantime'])) {
                $result .= "<alt title='Schiffscan vor " . makeduration($row['schiffscantime']) . "'><img src='bilder/scann_schiff.png'></alt> ";
            }
            if (!empty($row['gebscantime'])) {
                $result .= "<alt title='Gebäudescan vor " . makeduration($row['gebscantime']) . "'><img src='bilder/scann_geb.png'></alt> ";
            }
            $result .= '</td>';

            if(!empty($row['last_scan'])) {
                if ($row['last_scan'] == $row['fehlscantime']) {
                    $result .= '<td nowrap><span class="ranking_red">' . makeduration($row['last_scan']) . '</span></td>';
                } else {
                    $result .= '<td nowrap><span class="ranking_green">' . makeduration($row['last_scan']) . '</span></td>';
                }
            }
            $result .= "</td></tr></table>";

            return $result;
        case 'last_raid':
            if (!empty($row['last_link'])) {
                return "<a href='" . $row['last_link'] . "' target='raid'>" . makeduration($value) . "</a>";
            } else {
                return "";
            }
        case 'rating':
            if (is_null($value)) {
                return '';
            } elseif ($value < 100) {
                $result = '<span class="ranking_red">';
            } elseif ($value >= 100 && $value < 999) {
                $result = '<span class="ranking_yellow">';
            } else {
                $result = '<span class="ranking_green">';
            }
            $result .= number_format((float)$value, 0, ',', '.') . '%';
            $result .= '</span>';

            return $result;
        default:
            return $value;
    }
}

//****************************************************************************
//
// Zeilenfarbe ermitteln
function row_color($row)
{
    global $config;

    $allicolor = $config['allicolor'];

    if (isset($row['allianz'])) {
        if (isset($allicolor[$row['allianz']])) {
            return $allicolor[$row['allianz']];
        }
    }

    return 'white';
}

// ****************************************************************************
//
// Erzeugt einen Modul-Link.
function makelink($newparams, $content, $nomerge = false)
{
    return '<a href="' . makeurl($newparams, $nomerge) . '">' . $content . '</a>';
}

// ****************************************************************************
//
// Erzeugt eine Modul-URL.
function makeurl($newparams, $nomerge = false)
{
    global $modulname, $sid, $params;

    $url = 'index.php?action=' . $modulname;
    $url .= '&amp;sid=' . $sid;
    if ($nomerge) {
        $mergeparams = $newparams;
    } elseif (is_array($newparams)) {
        $mergeparams = array_merge($params, $newparams);
    } else {
        $mergeparams = $params;
    }
    foreach ($mergeparams as $paramkey => $paramvalue) {
        $url .= '&amp;' . $paramkey . '=' . $paramvalue;
    }

    return $url;
}

//****************************************************************************
//
// Dauer formatieren
function makeduration($time)
{
    if (empty($time)) {
        return '---';
    }
    $duration = CURRENT_UNIX_TIME - $time;
    if ($duration > 2 * DAY) {
        return round($duration / DAY) . "d";
    } elseif ($duration > HOUR) {
        $hours = round($duration / HOUR);

        return $hours == 1 ? "1 Stunde" : $hours . "h";
    } else {
        $minutes = round($duration / MINUTE);

        return $minutes == 1 ? "1 Minute" : $minutes . "min";
    }
}

//****************************************************************************
//
// Menge formatieren
function makeAmount($amount)
{
    if (is_null($amount) or $amount === '') {
        return '';
    } else {
        if ($amount > 1000) {
            return number_format(round($amount / 1000), 0, ",", '.') . "k";
        } else {
            return number_format((float)$amount, 0, ",", '.');
        }
    }
}