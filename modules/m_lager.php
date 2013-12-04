<?php
/*****************************************************************************
 * m_lager.php                                                               *
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
$modulname = "m_lager";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Lager";

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
$moduldesc = "Lagerübersicht zur Koordination von Logistikaufträgen im Buddler-Fleeter-System.";

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
    //
    // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
    //
    // 	insertMenuItem( $_POST['menu'], ($_POST['submenu']+1), "Titel2", "hc", "&weissichnichtwas=1" );
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

    echo "<br>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")<br><br>\n";

    require_once "./includes/menu_fn.php";

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

//verschiedene genutzte globale Variablen
global $db, $db_tb_scans, $db_tb_target, $db_tb_lieferung, $db_tb_lager, $db_tb_user, $user_sitterlogin;

// Parameter ermitteln
$params = array(
    'view'              => getVar('view'),
    'order'             => getVar('order'),
    'orderd'            => ensureSortDirection(getVar('orderd')),
    'edit'              => getVar('edit'),
    'delete'            => getVar('delete'),
    'expand'            => getVar('expand'),
    'playerSelection'   => getVar('playerSelection'),
    'forecast'          => getVar('forecast'),
    'advanced_forecast' => getVar('advanced_forecast'),
    'basen'             => getVar('basen'),
    'ress'              => getVar('ress'),
    'rote_lager'        => getVar('rote_lager'),
    'minimal'           => getVar('minimal'),
    'maximal'           => getVar('maximal'),
    'galaxie_min'       => getVar('galaxie_min'),
    'galaxie_max'       => getVar('galaxie_max'),
);

$numeisen=1;
$numstahl=2;
$numvv4a=3;
$numchem=4;
$numeis=5;
$numwasser=6;
$numenergie=7;

// Parameter validieren
if (empty($params['view'])) {
    $params['view'] = 'lager';
}
if (empty($params['order'])) {
    $params['order'] = 'user';
}
if (!empty($params['edit'])) {
    $params['expand'] = $params['edit'];
}
if (empty($params['playerSelection'])) {
    if (!empty($user_buddlerfrom)) {
        $params['playerSelection'] = '(Team) ' . $user_buddlerfrom;
    } else {
        //$params['playerSelection'] = $user_sitterlogin;
		$params['playerSelection'] = '(Alle)';
    }
} else {
    $params['playerSelection'] = $db->escape($params['playerSelection']);
}

// Zum Spiel weiterleiten
$universum       = getVar('universum');
$flotteversenden = getVar('flotteversenden');
if (!empty($universum) || !empty($flotteversenden)) {
    $name = 'Automatische Zielliste vom ' . strftime(CONFIG_DATETIMEFORMAT, CURRENT_UNIX_TIME);

    $sql = "DELETE FROM " . $db_tb_target . " WHERE user='" . $user_sitterlogin . "' AND name LIKE 'Automatische Zielliste%'";
    debug_var("sql", $sql);
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $index = 0;
    do {
        $current = getVar("target_" . $index++);
        if (!empty($current) && getVar("mark_" . $current)) {
            $coords = explode(":", $current);
            debug_var("coords", $coords);
            $sql = "INSERT INTO " . $db_tb_target . "(`user`,`name`,`coords_gal`,`coords_sys`,`coords_planet`)
				VALUES ('" . $user_sitterlogin . "','" . $name . "'," . $coords[0] . "," . $coords[1] . "," . $coords[2] . ")";

            debug_var("sql", $sql);
            $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }
    } while (!empty($current));
    $results[] = "<div class='system_notification'>Zielliste gespeichert.</div><br>";
    $redirect  = 'game.php?name=' . $name;
    if (!empty($universum)) {
        $redirect .= '&view=universum';
    } else {
        $redirect .= '&view=fleet_send';
    }
}

// Spieler und Teams abfragen
$playerSelectionOptions = array();
$playerSelectionOptions['(Alle)'] = '(Alle)';
$playerSelectionOptions += getAllyAccTypesSelect() + getAllyTeamsSelect() + getAllyAccs();

// Planeten des Spielers abfragen
$sql = "SELECT * FROM " . $db_tb_scans . " WHERE user='" . $user_sitterlogin . "'";
debug_var('sql', $sql);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $planets['key'] = 'value';
}

// Ressourcen
$resses_name = array('eisen'   => 'Eisen',
                     'stahl'   => 'Stahl',
                     'vv4a'    => 'VV4A',
                     'chem'    => 'Chemie',
                     'eis'     => 'Eis',
                     'wasser'  => 'Wasser',
                     'energie' => 'Energie'
);
$resses      = array('eisen', 'stahl', 'vv4a', 'chem', 'eis', 'wasser', 'energie');

$acc_typ_topgroups = array_keys ( $aSpieltypen );
foreach ($acc_typ_topgroups as $key => $group) {
    if (!is_array($aSpieltypen[$group])) {
        $acc_typ_topgroups[$key] = $aSpieltypen[$group];
    }
}

// Delete-Schlüssel aufbauen
$delete_keys_explode = explode(":", $params['delete']);
if (count($delete_keys_explode) == 3) {
    $delete_keys = array(
        'coords_gal'    => $delete_keys_explode[0],
        'coords_sys'    => $delete_keys_explode[1],
        'coords_planet' => $delete_keys_explode[2],
    );
}

// Daten löschen
if (isset($params['delete']) && $params['delete'] != '') {
    $sql = "DELETE FROM " . $db_tb_lager;
    $sql .= " WHERE ";
    foreach ($delete_keys as $name => $value) {
        $delete_tokens[] = $name . "=" . $value;
    }
    $sql .= implode($delete_tokens, " AND ");
    debug_var('sql', $sql);
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $results[]        = "<div class='system_notification'>Datensatz gelöscht.</div><br>";
    $params['delete'] = '';
    $params['edit']   = '';
}

// Filter festlegen
if (!empty($params['playerSelection'])) {
    $filters['playerSelection'] = $params['playerSelection'];
}

// Button abfragen
$button_edit = getVar("button_edit");
$button_add  = getVar("button_add");

// Edit-Daten belegen
$edit = array();
if (!empty($button_edit) || !empty($button_add)) {
    foreach ($resses as $ress) {
        $edit[$ress . '_soll'] = (int)filter_number(getVar($ress . '_soll'));
    }
    foreach ($resses as $ress) {
        $edit[$ress . '_sichtbar'] = getVar($ress . '_sichtbar');
    }
} else {
    foreach ($resses as $ress) {
        $edit[$ress . '_soll'] = '';
    }
    foreach ($resses as $ress) {
        $edit[$ress . '_sichtbar'] = 1;
    }
}

// Edit-Schlüssel aufbauen
$edit_keys_explode = explode(":", $params['edit']);
if (count($edit_keys_explode) == 3) {
    $edit_keys = array(
        'coords_gal'    => $edit_keys_explode[0],
        'coords_sys'    => $edit_keys_explode[1],
        'coords_planet' => $edit_keys_explode[2],
    );
}

// Edit-Felder belegen
$fields = array();
foreach ($edit as $key => $value) {
    $fields[$key] = (is_numeric($value) ? $value : (empty($value) ? 'NULL' : "'" . $value . "'"));
}

// Edit-Daten modifizieren
if (!empty($button_edit)) {
    $sql = "UPDATE " . $db_tb_lager . " SET ";
    foreach ($fields as $name => $value) {
        $tokens[] = $name . "=" . $value;
    }
    $sql .= implode($tokens, ",");
    $sql .= " WHERE ";
    foreach ($edit_keys as $name => $value) {
        $key_tokens[] = $name . "=" . $value;
    }
    $sql .= implode($key_tokens, " AND ");
    debug_var('sql', $sql);
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $results[] = "<div class='system_notification'>Datensatz aktualisiert.</div><br>";
}

// Edit-Daten abfragen
if (!empty($params['edit'])) {
    $sql = "SELECT * FROM " . $db_tb_lager;
    $sql .= " WHERE ";
    foreach ($edit_keys as $name => $value) {
        $key_tokens[] = $name . "=" . $value;
    }
    $sql .= implode($key_tokens, " AND ");
    debug_var('sql', $sql);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    if ($row = $db->db_fetch_array($result)) {
        foreach ($row as $name => $value) {
            $edit[$name] = $value;
        }
    }
}

// Tabellen-Daten abfragen
$data = array();
$sql  = "SELECT ";
$sql .= $db_tb_lager . ".user";
$sql .= "," . $db_tb_lager . ".coords_gal";
$sql .= "," . $db_tb_lager . ".coords_sys";
$sql .= "," . $db_tb_lager . ".coords_planet";
$sql .= "," . $db_tb_lager . ".kolo_typ";
foreach ($resses as $ress) {
    $sql .= "," . $db_tb_lager . "." . $ress;
    $sql .= "," . $db_tb_lager . "." . $ress . "_sichtbar";
    //Forecast
    if ($params['forecast'] || ($params['advanced_forecast'])) {
        $sql .= " + (" . $db_tb_lager . "." . $ress . "_prod * ";
        //Advanced Forecast
        if ($params['advanced_forecast']) {
            $sql .= "(" . $params['forecast'] . "+ (" . CURRENT_UNIX_TIME . " - " . $db_tb_lager . ".time) / 60 / 60)";
        } else {
            $sql .= $params['forecast'];
        }
        $sql .= ") AS " . $ress;
    }

    $sql .= ",$db_tb_lager.{$ress}_soll";
    $sql .= ",(SELECT SUM($db_tb_lieferung.$ress) FROM $db_tb_lieferung WHERE ";
    $sql .= " $db_tb_lager.coords_gal = $db_tb_lieferung.coords_to_gal";
    $sql .= " AND $db_tb_lager.coords_sys = $db_tb_lieferung.coords_to_sys";
    $sql .= " AND $db_tb_lager.coords_planet = $db_tb_lieferung.coords_to_planet";
    $sql .= " AND $db_tb_lieferung.time >= $db_tb_lager.time";
    $sql .= " AND ($db_tb_lieferung.art='Transport' OR $db_tb_lieferung.art='Übergabe' OR $db_tb_lieferung.art='Ressourcenhandel' OR $db_tb_lieferung.art='Ressourcenhandel (ok)'))";
    $sql .= " AS {$ress}_transfer";
    $sql .= ",(SELECT SUM($db_tb_lieferung.$ress) FROM $db_tb_lieferung WHERE ";
    $sql .= " $db_tb_lager.coords_gal = $db_tb_lieferung.coords_to_gal";
    $sql .= " AND $db_tb_lager.coords_sys = $db_tb_lieferung.coords_to_sys";
    $sql .= " AND $db_tb_lager.coords_planet = $db_tb_lieferung.coords_to_planet";
    $sql .= " AND $db_tb_lieferung.time >= $db_tb_lager.time";
    $sql .= " AND $db_tb_lieferung.art='Stationieren') AS {$ress}_stat";
    $sql .= ",IFNULL((SELECT SUM($db_tb_lieferung.$ress) FROM $db_tb_lieferung WHERE ";
    $sql .= " $db_tb_lager.coords_gal = $db_tb_lieferung.coords_to_gal";
    $sql .= " AND $db_tb_lager.coords_sys = $db_tb_lieferung.coords_to_sys";
    $sql .= " AND $db_tb_lager.coords_planet = $db_tb_lieferung.coords_to_planet";
    $sql .= " AND $db_tb_lieferung.time >= $db_tb_lager.time";
    $sql .= " AND ($db_tb_lieferung.art = 'Transport' OR $db_tb_lieferung.art='Übergabe' OR $db_tb_lieferung.art='Ressourcenhandel' OR $db_tb_lieferung.art='Ressourcenhandel (ok)')),0)";
    $sql .= "+IFNULL((SELECT SUM($db_tb_lieferung.$ress) FROM $db_tb_lieferung WHERE ";
    $sql .= " $db_tb_lager.coords_gal = $db_tb_lieferung.coords_to_gal";
    $sql .= " AND $db_tb_lager.coords_sys = $db_tb_lieferung.coords_to_sys";
    $sql .= " AND $db_tb_lager.coords_planet = $db_tb_lieferung.coords_to_planet";
    $sql .= " AND $db_tb_lieferung.time >= $db_tb_lager.time";
    $sql .= " AND $db_tb_lieferung.art = 'Stationieren'),0)";
    $sql .= "+$db_tb_lager.$ress";
    if ($params['forecast'] || ($params['advanced_forecast'])) {
        $sql .= "-(" . $ress . "_prod * -1 * ";
        //Advanced Forecast
        if ($params['advanced_forecast']) {
            $sql .= "(" . $params['forecast'] . "+ (" . CURRENT_UNIX_TIME . " - " . $db_tb_lager . ".time) / 60 / 60)";
        } else {
            $sql .= $params['forecast'];
        }
        $sql .= ")";
    }
    $sql .= " AS {$ress}_total";
    if ($ress == 'chem' || $ress == 'eis' || $ress == 'energie') {
        $sql .= ",$db_tb_lager.{$ress}_lager";
    }
    $sql .= ",$db_tb_lager.{$ress}_prod";
}
$sql .= "," . $db_tb_lager . ".eis_lager AS wasser_lager";
$sql .= "," . $db_tb_lager . ".time";
$sql .= "," . $db_tb_scans . ".planetenname";
$sql .= "," . $db_tb_scans . ".sortierung";
$sql .= "," . $db_tb_user . ".buddlerfrom";
$sql .= "," . $db_tb_user . ".budflesol";
//Advanced Forecast
if ($params['forecast'] && $params['advanced_forecast']) {
    $sql .= ",((" . CURRENT_UNIX_TIME . " - " . $db_tb_lager . ".time) / 60 / 60) AS advanced_forecast";
} else {
    $sql .= ",0 AS advanced_forecast";
}

$sql .= " FROM " . $db_tb_lager;
$sql .= " LEFT JOIN " . $db_tb_scans;
$sql .= " ON $db_tb_lager.coords_gal = $db_tb_scans.coords_gal";
$sql .= " AND $db_tb_lager.coords_sys = $db_tb_scans.coords_sys";
$sql .= " AND $db_tb_lager.coords_planet = $db_tb_scans.coords_planet";
$sql .= " LEFT JOIN " . $db_tb_user;
$sql .= " ON $db_tb_lager.user = $db_tb_user.id";
if (!empty($params['basen'])) {
    $sql .= " WHERE ($db_tb_lager.kolo_typ='Kolonie' OR $db_tb_lager.kolo_typ='Sammelbasis' OR $db_tb_lager.kolo_typ='Kampfbasis'  OR $db_tb_lager.kolo_typ='Artefaktbasis')";
} else {
    $sql .= " WHERE $db_tb_lager.kolo_typ='Kolonie'";
}
$sql .= " AND " . sqlPlayerSelection($params['playerSelection']);
$sql .= " AND $db_tb_user.sitten = '1' AND $db_tb_user.sitterpwd IS NOT NULL AND $db_tb_user.sitterpwd != ''";

//Minimale und maximale Ressourcenbestände
if (isset($params['ress']) && !empty($params['minimal'])) {
    $sql .= " AND $db_tb_lager." . $params['ress'] . " > '" . $params['minimal'] . "'";
}
if (isset($params['ress']) && !empty($params['maximal'])) {
    $sql .= " AND $db_tb_lager." . $params['ress'] . " < '" . $params['maximal'] . "'";
}
//Rote Lager anzeigen
if (isset($params['ress']) && $params['rote_lager']) {
    $sql .= " AND ";
    $sql .= "((IFNULL((SELECT SUM(" . $db_tb_lieferung . "." . $params['ress'] . ") FROM " . $db_tb_lieferung . " WHERE ";
    $sql .= " $db_tb_lager.coords_gal = $db_tb_lieferung.coords_to_gal";
    $sql .= " AND $db_tb_lager.coords_sys = $db_tb_lieferung.coords_to_sys";
    $sql .= " AND $db_tb_lager.coords_planet=$db_tb_lieferung.coords_to_planet";
    $sql .= " AND $db_tb_lieferung.time >= $db_tb_lager.time";
    $sql .= " AND ($db_tb_lieferung.art='Transport' OR $db_tb_lieferung.art='Übergabe' OR $db_tb_lieferung.art='Ressourcenhandel' OR $db_tb_lieferung.art='Ressourcenhandel (ok)')),0)";
    $sql .= "+IFNULL((SELECT SUM($db_tb_lieferung." . $params['ress'] . ") FROM $db_tb_lieferung WHERE ";
    $sql .= " $db_tb_lager.coords_gal = $db_tb_lieferung.coords_to_gal";
    $sql .= " AND $db_tb_lager.coords_sys = $db_tb_lieferung.coords_to_sys";
    $sql .= " AND $db_tb_lager.coords_planet = $db_tb_lieferung.coords_to_planet";
    $sql .= " AND $db_tb_lieferung.time >= $db_tb_lager.time";
    $sql .= " AND $db_tb_lieferung.art = 'Stationieren'),0)";
    $sql .= "+$db_tb_lager." . $params['ress'];
    if ($params['forecast'] || ($params['advanced_forecast'])) {
        $sql .= "+ ($db_tb_lager." . $params['ress'] . "_prod * ";
        //Advanced Forecast
        if ($params['advanced_forecast']) {
            $sql .= "(" . $params['forecast'] . "+ (" . CURRENT_UNIX_TIME . " - $db_tb_lager.time) / 60 / 60)";
        } else {
            $sql .= $params['forecast'];
        }
        $sql .= ")";
    }
    $sql .= ") * 100) ";
    $sql .= " / ($db_tb_lager." . $params['ress'] . "_soll ) < 90";
}

//Galaxie filtern
if (is_numeric($params['galaxie_min'])) {
    $sql .= " AND " . $db_tb_lager . ".coords_gal >= " . $params['galaxie_min'];
}
if (is_numeric($params['galaxie_max'])) {
    $sql .= " AND $db_tb_lager.coords_gal <= " . $params['galaxie_max'];
}
if (!$user_fremdesitten) {
    $sql .= " AND $db_tb_user.allianz='$user_allianz'";
}
$sql .= " GROUP BY ";
$sql .= "$db_tb_lager.user";
$sql .= ",$db_tb_lager.coords_gal";
$sql .= ",$db_tb_lager.coords_sys";
$sql .= ",$db_tb_lager.coords_planet";
$sql .= ",$db_tb_lager.kolo_typ";
foreach ($resses as $ress) {
    $sql .= ",$db_tb_lager.$ress";
    $sql .= ",$db_tb_lager.{$ress}_soll";
}
$sql .= ",$db_tb_lager.time";
$sql .= ",$db_tb_scans.planetenname";
$sql .= ",$db_tb_user.buddlerfrom";
$sql .= ",$db_tb_user.budflesol";

//$sql .= " ORDER BY ";
//$sql .= $db_tb_lager . ".user";
//$sql .= "," . $db_tb_lager . ".coords_gal";
//$sql .= "," . $db_tb_lager . ".coords_sys";
//$sql .= "," . $db_tb_lager . ".coords_planet";
//debug_var('sql', $sql);

$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $key        = $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'];
    $expanded   = $params['expand'] == $key;

    $acc_typ_hierarchy = array_get_value_recursive_up($row['budflesol'], $aSpieltypen);

    $data[$key] = array(
        'user'              => $row['user'],
        'team'              => $row['buddlerfrom'],
        'typ'               => array_search($acc_typ_hierarchy[0], $acc_typ_topgroups),
        'coords'            => $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'],
        'name'              => $row['planetenname'],
        'sortierung'        => $row['sortierung'],
        'objekttyp'         => $row['kolo_typ'],
        'eisen'             => $row['eisen'],
        'stahl'             => $row['stahl'],
        'vv4a'              => $row['vv4a'],
        'chem'              => $row['chem'],
        'eis'               => $row['eis'],
        'wasser'            => $row['wasser'],
        'energie'           => $row['energie'],
        'time'              => $row['time'],
		'eisen_soll'        => $row['eisen_soll']=lagersoll($row['user'], $numeisen, $row['coords_gal'], $row['coords_sys'], $row['coords_planet'], $row['eisen_prod'], $row['eisen_soll'], 0),
        'stahl_soll'        => $row['stahl_soll']=lagersoll($row['user'], $numstahl, $row['coords_gal'], $row['coords_sys'], $row['coords_planet'], $row['stahl_prod'], $row['stahl_soll'], 0),
        'vv4a_soll'         => $row['vv4a_soll']=lagersoll($row['user'], $numvv4a, $row['coords_gal'], $row['coords_sys'], $row['coords_planet'], $row['vv4a_prod'], $row['vv4a_soll'], 0),
		'chem_soll'         => $row['chem_soll']=lagersoll($row['user'], $numchem, $row['coords_gal'], $row['coords_sys'], $row['coords_planet'], $row['chem_prod'], $row['chem_soll'], $row['chem_lager']),
        'eis_soll'          => $row['eis_soll']=lagersoll($row['user'], $numeis, $row['coords_gal'], $row['coords_sys'], $row['coords_planet'], $row['eis_prod'], $row['eis_soll'], $row['eis_lager']),
        'wasser_soll'       => $row['wasser_soll']=lagersoll($row['user'], $numwasser, $row['coords_gal'], $row['coords_sys'], $row['coords_planet'], $row['wasser_prod'], $row['wasser_soll'], $row['wasser_lager']),
        'energie_soll'      => $row['energie_soll']=lagersoll($row['user'], $numenergie, $row['coords_gal'], $row['coords_sys'], $row['coords_planet'], $row['energie_prod'], $row['energie_soll'], $row['energie_lager']),
        'eisen_soll_diff'   => $row['eisen_total'] - $row['eisen_soll'],
        'stahl_soll_diff'   => $row['stahl_total'] - $row['stahl_soll'],
        'vv4a_soll_diff'    => $row['vv4a_total'] - $row['vv4a_soll'],
        'chem_soll_diff'    => $row['chem_total'] - $row['chem_soll'],
        'eis_soll_diff'     => $row['eis_total'] - $row['eis_soll'],
        'wasser_soll_diff'  => $row['wasser_total'] - $row['wasser_soll'],
        'energie_soll_diff' => $row['energie_total'] - $row['energie_soll'],
        'eisen_total'       => $row['eisen_total'],
        'stahl_total'       => $row['stahl_total'],
        'vv4a_total'        => $row['vv4a_total'],
        'chem_total'        => $row['chem_total'],
        'eis_total'         => $row['eis_total'],
        'wasser_total'      => $row['wasser_total'],
        'energie_total'     => $row['energie_total'],
        'user_style'        => 'background-color: ' . getScanAgeColor($row['time']) . ';',
        'eisen_style'       => 'background-color: ' . make_color($row, 'eisen') . '; text-align: right;',
        'stahl_style'       => 'background-color: ' . make_color($row, 'stahl') . '; text-align: right;',
        'vv4a_style'        => 'background-color: ' . make_color($row, 'vv4a') . '; text-align: right;',
        'chem_style'        => 'background-color: ' . make_color($row, 'chem') . '; text-align: right;',
        'eis_style'         => 'background-color: ' . make_color($row, 'eis') . '; text-align: right;',
        'wasser_style'      => 'background-color: ' . make_color($row, 'wasser') . '; text-align: right;',
        'energie_style'     => 'background-color: ' . make_color($row, 'energie') . '; text-align: right;',
    );

    // Expand-Daten abfragen
    if ($expanded) {
        $expand_data = array();
        $expand_data['transfer']   = array(
            'coords'        => $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'],
            'user'          => $row['user'],
            'team'          => $row['buddlerfrom'],
            'name'          => $row['planetenname'],
            'art'           => 'Lieferung',
            'eisen'         => $row['eisen_transfer'],
            'stahl'         => $row['stahl_transfer'],
            'vv4a'          => $row['vv4a_transfer'],
            'chem'          => $row['chem_transfer'],
            'eis'           => $row['eis_transfer'],
            'wasser'        => $row['wasser_transfer'],
            'energie'       => $row['energie_transfer'],
            'user_style'    => 'background-color: ' . getScanAgeColor($row['time']) . ';',
            'eisen_style'   => "text-align: right;",
            'stahl_style'   => "text-align: right;",
            'vv4a_style'    => "text-align: right;",
            'chem_style'    => "text-align: right;",
            'eis_style'     => "text-align: right;",
            'wasser_style'  => "text-align: right;",
            'energie_style' => "text-align: right;",
        );
        $expand_data['stat']       = array(
            'coords'        => $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'],
            'user'          => $row['user'],
            'team'          => $row['buddlerfrom'],
            'name'          => $row['planetenname'],
            'art'           => 'Stationieren',
            'eisen'         => $row['eisen_stat'],
            'stahl'         => $row['stahl_stat'],
            'vv4a'          => $row['vv4a_stat'],
            'chem'          => $row['chem_stat'],
            'eis'           => $row['eis_stat'],
            'wasser'        => $row['wasser_stat'],
            'energie'       => $row['energie_stat'],
            'user_style'    => 'background-color: ' . getScanAgeColor($row['time']),
            'eisen_style'   => "text-align: right;",
            'stahl_style'   => "text-align: right;",
            'vv4a_style'    => "text-align: right;",
            'chem_style'    => "text-align: right;",
            'eis_style'     => "text-align: right;",
            'wasser_style'  => "text-align: right;",
            'energie_style' => "text-align: right;",
        );
        $expand_data['total']      = array(
            'coords'        => $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'],
            'user'          => $row['user'],
            'team'          => $row['buddlerfrom'],
            'name'          => $row['planetenname'],
            'art'           => 'Gesamt',
            'eisen'         => $row['eisen_total'],
            'stahl'         => $row['stahl_total'],
            'vv4a'          => $row['vv4a_total'],
            'chem'          => $row['chem_total'],
            'eis'           => $row['eis_total'],
            'wasser'        => $row['wasser_total'],
            'energie'       => $row['energie_total'],
            'user_style'    => 'background-color: ' . getScanAgeColor($row['time']) . ';',
            'eisen_style'   => "text-align: right;",
            'stahl_style'   => "text-align: right;",
            'vv4a_style'    => "text-align: right;",
            'chem_style'    => "text-align: right;",
            'eis_style'     => "text-align: right;",
            'wasser_style'  => "text-align: right;",
            'energie_style' => "text-align: right;",
        );
        $expand_data['soll']       = array(
            'coords'        => $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'],
            'user'          => $row['user'],
            'team'          => $row['buddlerfrom'],
            'name'          => $row['planetenname'],
            'art'           => 'Soll',
            'eisen'         => $row['eisen_soll'],
            'stahl'         => $row['stahl_soll'],
            'vv4a'          => $row['vv4a_soll'],
            'chem'          => $row['chem_soll'],
            'eis'           => $row['eis_soll'],
            'wasser'        => $row['wasser_soll'],
            'energie'       => $row['energie_soll'],
            'user_style'    => 'background-color: ' . getScanAgeColor($row['time']) . ';',
            'eisen_style'   => "text-align: right;",
            'stahl_style'   => "text-align: right;",
            'vv4a_style'    => "text-align: right;",
            'chem_style'    => "text-align: right;",
            'eis_style'     => "text-align: right;",
            'wasser_style'  => "text-align: right;",
            'energie_style' => "text-align: right;",
        );
        $expand_data['soll_diff']  = array(
            'coords'        => $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'],
            'user'          => $row['user'],
            'team'          => $row['buddlerfrom'],
            'name'          => $row['planetenname'],
            'art'           => 'Differenz',
            'eisen'         => $row['eisen_soll'] != '' ? $data[$key]['eisen_soll_diff'] : '',
            'stahl'         => $row['stahl_soll'] != '' ? $data[$key]['stahl_soll_diff'] : '',
            'vv4a'          => $row['vv4a_soll'] != '' ? $data[$key]['vv4a_soll_diff'] : '',
            'chem'          => $row['chem_soll'] != '' ? $data[$key]['chem_soll_diff'] : '',
            'eis'           => $row['eis_soll'] != '' ? $data[$key]['eis_soll_diff'] : '',
            'wasser'        => $row['wasser_soll'] != '' ? $data[$key]['wasser_soll_diff'] : '',
            'energie'       => $row['energie_soll'] != '' ? $data[$key]['energie_soll_diff'] : '',
            'user_style'    => 'background-color: ' . getScanAgeColor($row['time']) . ';',
            'eisen_style'   => "color: " . ($data[$key]['eisen_soll_diff'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'stahl_style'   => "color: " . ($data[$key]['stahl_soll_diff'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'vv4a_style'    => "color: " . ($data[$key]['vv4a_soll_diff'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'chem_style'    => "color: " . ($data[$key]['chem_soll_diff'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'eis_style'     => "color: " . ($data[$key]['eis_soll_diff'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'wasser_style'  => "color: " . ($data[$key]['wasser_soll_diff'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'energie_style' => "color: " . ($data[$key]['energie_soll_diff'] < 0 ? 'red' : 'green') . "; text-align: right;",
        );
        $expand_data['lager']      = array(
            'coords'        => $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'],
            'user'          => $row['user'],
            'team'          => $row['buddlerfrom'],
            'name'          => $row['planetenname'],
            'art'           => 'Lager',
            'eisen'         => '---',
            'stahl'         => '---',
            'vv4a'          => '---',
            'chem'          => $row['chem_lager'],
            'eis'           => $row['eis_lager'],
            'wasser'        => $row['wasser_lager'],
            'energie'       => $row['energie_lager'],
            'user_style'    => 'background-color: ' . getScanAgeColor($row['time']) . ';',
            'eisen_style'   => "text-align: right;",
            'stahl_style'   => "text-align: right;",
            'vv4a_style'    => "text-align: right;",
            'chem_style'    => "text-align: right;",
            'eis_style'     => "text-align: right;",
            'wasser_style'  => "text-align: right;",
            'energie_style' => "text-align: right;",
        );
        $expand_data['prod']       = array(
            'coords'        => $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'],
            'user'          => $row['user'],
            'team'          => $row['buddlerfrom'],
            'name'          => $row['planetenname'],
            'art'           => 'Tagesproduktion',
            'eisen'         => $row['eisen_prod'] * 24,
            'stahl'         => $row['stahl_prod'] * 24,
            'vv4a'          => $row['vv4a_prod'] * 24,
            'chem'          => $row['chem_prod'] * 24,
            'eis'           => $row['eis_prod'] * 24,
            'wasser'        => $row['wasser_prod'] * 24,
            'energie'       => $row['energie_prod'] * 24,
            'user_style'    => 'background-color: ' . getScanAgeColor($row['time']) . ';',
            'eisen_style'   => "color: " . ($row['eisen_prod'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'stahl_style'   => "color: " . ($row['stahl_prod'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'vv4a_style'    => "color: " . ($row['vv4a_prod'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'chem_style'    => "color: " . ($row['chem_prod'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'eis_style'     => "color: " . ($row['eis_prod'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'wasser_style'  => "color: " . ($row['wasser_prod'] < 0 ? 'red' : 'green') . "; text-align: right;",
            'energie_style' => "color: " . ($row['energie_prod'] < 0 ? 'red' : 'green') . "; text-align: right;",
        );
        $expand_data['empty_prod'] = array(
            'coords'        => $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'],
            'user'          => $row['user'],
            'team'          => $row['buddlerfrom'],
            'name'          => $row['planetenname'],
            'art'           => 'Lager leer in h',
            'eisen'         => $row['eisen_prod'] < 0 ? (-($row['eisen_total'] - ($row['eisen_prod'] * ($params['forecast'] + $row['advanced_forecast']))) / $row['eisen_prod'] - $params['forecast']) : 0,
            'stahl'         => $row['stahl_prod'] < 0 ? (-($row['stahl_total'] + ($row['stahl_prod'] * ($params['forecast'] + $row['advanced_forecast']))) / $row['stahl_prod'] - $params['forecast']) : 0,
            'vv4a'          => $row['vv4a_prod'] < 0 ? (-($row['vv4a_total'] + ($row['vv4a_prod'] * ($params['forecast'] + $row['advanced_forecast']))) / $row['vv4a_prod'] - $params['forecast']) : 0,
            'chem'          => $row['chem_prod'] < 0 ? (-($row['chem_total'] + ($row['chem_prod'] * ($params['forecast'] + $row['advanced_forecast']))) / $row['chem_prod'] - $params['forecast']) : 0,
            'eis'           => $row['eis_prod'] < 0 ? (-($row['eis_total'] - ($row['eis_prod'] * ($params['forecast'] + $row['advanced_forecast']))) / $row['eis_prod'] - $params['forecast']) : 0,
            'wasser'        => $row['wasser_prod'] < 0 ? (-($row['wasser_total'] + ($row['wasser_prod'] * ($params['forecast'] + $row['advanced_forecast']))) / $row['wasser_prod'] - $params['forecast']) : 0,
            'energie'       => $row['energie_prod'] < 0 ? (-($row['energie_total'] + ($row['energie_prod'] * ($params['forecast'] + $row['advanced_forecast']))) / $row['energie_prod'] - $params['forecast']) : 0,
            'user_style'    => 'background-color: ' . getScanAgeColor($row['time']) . ';',
            'eisen_style'   => "text-align: right;",
            'stahl_style'   => "text-align: right;",
            'vv4a_style'    => "text-align: right;",
            'chem_style'    => "text-align: right;",
            'eis_style'     => "text-align: right;",
            'wasser_style'  => "text-align: right;",
            'energie_style' => "text-align: right;",
        );
    }
}

// Ansichten definieren
$views = array(
    'lager' => array(
        'title'   => 'Lager',
        'columns' => array(
            'user'      => 'Spieler',
            'team'      => 'Team',
            'typ'       => 'Typ',
            'coords'    => 'Koords',
            'name'      => 'Planet',
            'objekttyp' => 'Objekttyp',
            'eisen'     => 'Eisen',
            'stahl'     => 'Stahl',
            'vv4a'      => 'VV4A',
            'chem'      => 'Chemie',
            'eis'       => 'Eis',
            'wasser'    => 'Wasser',
            'energie'   => 'Energie',
        ),
        'key'     => 'coords',
        'group'   => array(
            'typ' => array(
                'title' => 'Typ',
                'sum'   => array(
                    'eisen',
                    'stahl',
                    'vv4a',
                    'chem',
                    'eis',
                    'wasser',
                    'energie',
                ),
            ),
        ),
        'grow'    => 'name',
        'edit'    => array(
            'eisen_soll'       => array(
                'title' => 'Eisen',
                'desc'  => 'Wieviel Eisen soll im Lager sein?',
                'type'  => 'text',
                'value' => number_format((float)$edit['eisen_soll'], 0, ",", "."),
                'style' => 'width: 10em;',
            ),
            'stahl_soll'       => array(
                'title' => 'Stahl',
                'desc'  => 'Wieviel Stahl soll im Lager sein?',
                'type'  => 'text',
                'value' => number_format((float)$edit['stahl_soll'], 0, ",", "."),
                'style' => 'width: 10em;',
            ),
            'vv4a_soll'        => array(
                'title' => 'VV4A',
                'desc'  => 'Wieviel VV4A soll im Lager sein?',
                'type'  => 'text',
                'value' => number_format((float)$edit['vv4a_soll'], 0, ",", "."),
                'style' => 'width: 10em;',
            ),
            'chem_soll'        => array(
                'title' => 'Chemie',
                'desc'  => 'Wieviel Chemie soll im Lager sein?',
                'type'  => 'text',
                'value' => number_format((float)$edit['chem_soll'], 0, ",", "."),
                'style' => 'width: 10em;',
            ),
            'eis_soll'         => array(
                'title' => 'Eis',
                'desc'  => 'Wieviel Eis soll im Lager sein?',
                'type'  => 'text',
                'value' => number_format((float)$edit['eis_soll'], 0, ",", "."),
                'style' => 'width: 10em;',
            ),
            'wasser_soll'      => array(
                'title' => 'Wasser',
                'desc'  => 'Wieviel Wasser soll im Lager sein?',
                'type'  => 'text',
                'value' => number_format((float)$edit['wasser_soll'], 0, ",", "."),
                'style' => 'width: 10em;',
            ),
            'energie_soll'     => array(
                'title' => 'Energie',
                'desc'  => 'Wieviel Energie soll im Lager sein?',
                'type'  => 'text',
                'value' => number_format((float)$edit['energie_soll'], 0, ",", "."),
                'style' => 'width: 10em;',
            ),
            'eisen_sichtbar'   => array(
                'title' => 'Eisen sichtbar',
                'desc'  => 'Sollen andere Spieler den Bedarf liefern?',
                'type'  => 'checkbox',
                'value' => $edit['eisen_sichtbar'],
            ),
            'stahl_sichtbar'   => array(
                'title' => 'Stahl sichtbar',
                'desc'  => 'Sollen andere Spieler den Bedarf liefern?',
                'type'  => 'checkbox',
                'value' => $edit['stahl_sichtbar'],
            ),
            'vv4a_sichtbar'    => array(
                'title' => 'VV4A sichtbar',
                'desc'  => 'Sollen andere Spieler den Bedarf liefern?',
                'type'  => 'checkbox',
                'value' => $edit['vv4a_sichtbar'],
            ),
            'chem_sichtbar'    => array(
                'title' => 'Chemie sichtbar',
                'desc'  => 'Sollen andere Spieler den Bedarf liefern?',
                'type'  => 'checkbox',
                'value' => $edit['chem_sichtbar'],
            ),
            'eis_sichtbar'     => array(
                'title' => 'Eis sichtbar',
                'desc'  => 'Sollen andere Spieler den Bedarf liefern?',
                'type'  => 'checkbox',
                'value' => $edit['eis_sichtbar'],
            ),
            'wasser_sichtbar'  => array(
                'title' => 'Wasser sichtbar',
                'desc'  => 'Sollen andere Spieler den Bedarf liefern?',
                'type'  => 'checkbox',
                'value' => $edit['wasser_sichtbar'],
            ),
            'energie_sichtbar' => array(
                'title' => 'Energie sichtbar',
                'desc'  => 'Sollen andere Spieler den Bedarf liefern?',
                'type'  => 'checkbox',
                'value' => $edit['energie_sichtbar'],
            ),
        ),
        'expand'  => array(
            'title'   => 'Details',
            'columns' => array(
                'user'    => 'Spieler',
                'team'    => 'Team',
                'coords'  => 'Koords',
                'name'    => 'Planet',
                'art'     => 'Art',
                'eisen'   => 'Eisen',
                'stahl'   => 'Stahl',
                'vv4a'    => 'VV4A',
                'chem'    => 'Chemie',
                'eis'     => 'Eis',
                'wasser'  => 'Wasser',
                'energie' => 'Energie',
            ),
        ),
    ),
);

// Aktuelle Ansicht auswählen
$view   = $views[$params['view']];
$expand = $view['expand'];

// Daten sortieren
usort($data, "sort_data_cmp");

// Daten gruppieren
foreach ($view['group'] as $groupkey => $grouptitle) {
    foreach ($data as $row) {
        $group_data[$groupkey][$row[$groupkey]][$row[$view['key']]] = $row;
    }
    if (isset($group_data[$groupkey])) {
        ksort($group_data[$groupkey]);
    }
}
if (!isset($group_data)) {
    $group_data = array();
}

// Titelzeile ausgeben
doc_title($view['title']);

// Ergebnisse ausgeben
if (isset($results)) {
    foreach ($results as $result) {
        echo $result;
    }
}

// Weiterleitung aktiv?
if (isset($redirect)) {
    echo 'Weiterleitung zu <a href="' . $redirect . '">Zielseite</a> ...';
    echo '<script> this.location = "' . $redirect . '"; </script>';
    exit;
}

// Team Dropdown
$basen             = $params['basen'];
$rote_lager        = $params['rote_lager'];
$advanced_forecast = $params['advanced_forecast'];
unset($params['basen']);
unset($params['rote_lager']);
unset($params['advanced_forecast']);
echo "<form method='POST' action='" . makeurl(array()) . "' enctype='multipart/form-data'><p align='center'>";
$params['basen']             = $basen;
$params['rote_lager']        = $rote_lager;
$params['advanced_forecast'] = $advanced_forecast;
// Auswahl Dropdown
echo "Auswahl: ";
echo makeField(
    array(
         "type"   => 'select',
         "values" => $playerSelectionOptions,
         "value"  => $params['playerSelection'],
    ), 'playerSelection'
);
echo ' Vorhersage: ';
echo ' <input type="text" name="forecast" size="3" value="' . $params['forecast'] . '"/>';
echo ' Stunden ';
echo ' <input type="checkbox" name="basen" value="1" ' . ($params['basen'] ? ' checked ' : '') . '/>';
echo ' mit Basen';
// Minimal-Maximal Ressourcen einstellen
echo '<br>Nach Ressourcen filtern:';
echo makeField(array("type" => 'select', "values" => $resses_name, "value" => $params['ress']), 'ress');
echo ' Minimal: ';
echo '<input type="text" name="minimal" size="6" value="' . $params['minimal'] . '"/>';
echo ' Maximal: ';
echo '<input type="text" name="maximal" size="6" value="' . $params['maximal'] . '"/>';
echo ' <input type="checkbox" name="rote_lager" value="1" ' . ($params['rote_lager'] ? ' checked ' : '') . '/>';
echo ' rote Lager ';
echo ' <input type="checkbox" name="advanced_forecast" value="1" ' . ($params['advanced_forecast'] ? ' checked ' : '') . '/>';
echo ' Normalisierung der Einlesezeiten';
echo ' <input type="submit" name="submit" value="anzeigen"/>';
echo "</form>\n";

//Aktionsschaltflaechen
echo '<div>';
echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p>' . "\n";
echo '<div class= "right" style="width: 100%; margin-bottom: 6px">';
echo '<input type="submit" name="flotteversenden" value="Flotte versenden"/>';
echo '</div>';

//Überschriften ausgeben
echo '<table  class="table_format" id="lagertabelle">';
start_row("titlebg top");
foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
    if (!isset($view['group'][$viewcolumnkey]) && !isset($filters[$viewcolumnkey])) {
        $extra = 'nowrap valign=top';
        if (isset($view['grow']) && $view['grow'] == $viewcolumnkey) {
            $extra .= ' width="100%"';
        } else {
            $extra .= ' width="0%"';
        }
        next_cell("titlebg", $extra);
        $orderkey = $viewcolumnkey;
        if (isset($view['sortcolumns'][$orderkey])) {
            $orderkey = $view['sortcolumns'][$orderkey];
        }
        echo makelink(
            array(
                 'order'  => $orderkey,
                 'orderd' => 'asc'
            ),
            "<img src='".BILDER_PATH."asc.gif'>"
        );
        echo '<b>' . $viewcolumnname . '</b>';
        echo makelink(
            array(
                 'order'  => $orderkey,
                 'orderd' => 'desc'
            ),
            "<img src='".BILDER_PATH."desc.gif'>"
        );
    }
}
if (isset($view['edit'])) {
    next_cell("titlebg top");
    echo '&nbsp;';
}
next_cell("titlebg top");

//Initialisiere Summenzeilen
$summe             = array();
$summe_ueberschuss = array();
$summe_bedarf      = array();
$summe_diff        = array();
foreach ($views['lager']['columns'] as $viewcolumnkey => $value) {
    $summe[$viewcolumnkey]             = '0';
    $summe_ueberschuss[$viewcolumnkey] = '0';
    $summe_bedarf[$viewcolumnkey]      = '0';
    $summe_diff[$viewcolumnkey]        = '0';
}

//Daten ausgeben
$targetindex = 0;
foreach ($group_data as $groupkey => $group) {
    foreach ($group as $groupvalue => $grouprows) {
        $user_before = '';
        $border      = '';

        //Gruppenüberschrift
        next_row('windowbg2', 'nowrap valign=top colspan=' . (count($view['columns']) + 2));
        echo "<b>" . format_value(null, $groupkey, $groupvalue) . "</b>";

        //Zeilen
        foreach ($grouprows as $row) {
            $key      = $row[$view['key']];
            $expanded = $params['expand'] == $key;
            //Expand-Image
            if ($user_before !== $row['user'] AND ($params['order'] === 'user')) {
                if (!empty($user_before)) {
                    $border = ' border-top:2px black solid;';
                }
                $user_before = $row['user'];
            } else {
                $border = '';
            }
            next_row('windowbg1 middle', 'style="' . $border . '"');
            echo makelink(
                array('expand' => ($expanded ? '' : $key), 'edit' => ''),
                '<img src="bilder/' . ($expanded ? 'point' : 'plus') . '.gif" alt="' . ($expanded ? 'zuklappen' : 'erweitern') . '">'
            );
            foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
                if (!isset($view['group'][$viewcolumnkey]) && !isset($filters[$viewcolumnkey])) {
                    if (isset($row[$viewcolumnkey . '_style'])) {
                        $style = $row[$viewcolumnkey . '_style'];
                    } else {
                        $style = "background-color: white;";
                    }
                    next_cell("windowbg1", 'nowrap valign=top style="' . $style . $border . '"');
                    echo format_value($row, $viewcolumnkey, $row[$viewcolumnkey]);
                    if (is_numeric($row[$viewcolumnkey]) && $viewcolumnkey != "name") {
                        $summe[$viewcolumnkey] = $summe[$viewcolumnkey] + $row[$viewcolumnkey];
                        if ($row[$viewcolumnkey . '_soll'] < $row[$viewcolumnkey . '_total'] && $row[$viewcolumnkey . '_soll'] > "0") {
                            $summe_ueberschuss[$viewcolumnkey] = $summe_ueberschuss[$viewcolumnkey] + ($row[$viewcolumnkey . '_total'] - $row[$viewcolumnkey . '_soll']);
                        }
                        if ($row[$viewcolumnkey . '_soll'] > $row[$viewcolumnkey . '_total'] && $row[$viewcolumnkey . '_soll'] > "0") {
                            $summe_bedarf[$viewcolumnkey] = $summe_bedarf[$viewcolumnkey] - ($row[$viewcolumnkey . '_soll'] - $row[$viewcolumnkey . '_total']);
                        }
                        if ($row[$viewcolumnkey . '_soll'] > "0") {
                            $summe_diff[$viewcolumnkey] = $summe_diff[$viewcolumnkey] + $row[$viewcolumnkey . '_soll_diff'];
                        }
                    }
                }
            }
            // Editbuttons ausgeben
            if (isset($view['edit'])) {
                next_cell("windowbg1 top", 'style="' . $border . '"');
                if (!isset($row['allow_edit']) || $row['allow_edit']) {
                    echo makelink(
                        array('edit' => $key),
                        "<img src='".BILDER_PATH."file_edit_s.gif' alt='bearbeiten'>"
                    );
                }
                if (!isset($row['allow_delete']) || $row['can_delete']) {
                    //echo makelink(array('delete' => $key), "<img src='".BILDER_PATH."file_delete_s.gif' onclick=\"return confirm('Datensatz wirklich löschen?')\" alt='löschen'>");
                }
            }
            //Markierung-Checkbox
            next_cell('windowbg1', 'nowrap valign=center style="background-color: white;' . $border . '"');
            echo '<input type="hidden" name="target_' . $targetindex++ . '" value="' . $key . '">';
            echo '<input type="checkbox" name="mark_' . $key . '" value="1" checked>';
            // Expandbereich ausgeben
            if (isset($expand) && $params['expand'] == $key) {
                next_row('titlebg', 'colspan=' . (count($view['columns']) + 2));
                echo "<b>" . $expand['title'] . "</b>";
                next_row('windowbg2', '');
                foreach ($expand['columns'] as $expandcolumnkey => $expandcolumnname) {
                    if (!isset($filters[$expandcolumnkey])) {
                        next_cell("windowbg2 top");
                        echo $expandcolumnname;
                    }
                }
                if (isset($view['edit'])) {
                    next_cell("windowbg2 top");
                    echo '&nbsp;';
                }
                next_cell("windowbg2 top");
                echo '&nbsp;';
                foreach ($expand_data as $expand_row) {
                    next_row('windowbg1', 'nowrap valign=center style="background-color: white;"');
                    foreach ($expand['columns'] as $expandcolumnkey => $expandcolumnname) {
                        if (!isset($filters[$expandcolumnkey])) {
                            if (isset($expand_row[$expandcolumnkey . '_style'])) {
                                $style = $expand_row[$expandcolumnkey . '_style'];
                            } else {
                                $style = "background-color: white;";
                            }
                            next_cell("windowbg1", "nowrap valign=top style='" . $style . "'");
                            echo format_value($expand_row, $expandcolumnkey, $expand_row[$expandcolumnkey], true);
                        }
                    }
                    if (isset($view['edit'])) {
                        next_cell("windowbg1 top");
                        echo '&nbsp;';
                    }
                    next_cell('windowbg1', '');
                }
                next_row('windowbg2', 'colspan=' . (count($view['columns']) + 2));
                echo "&nbsp;";
            }
        }
    }
}

//Summenzeile anzeigen
next_row('windowbg2', 'nowrap valign=top colspan=' . (count($view['columns']) + 2));
echo '<b>Summe</b>';
next_row('windowbg1', 'nowrap valign=top style="background-color: white;"');
echo '&nbsp;';
next_cell("windowbg1 top");
echo '<b>Lagerbestände</b>';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
foreach ($resses as $ress_einzeln) {
    next_cell("windowbg1 right top");
    echo format_value($summe, $ress_einzeln, $summe[$ress_einzeln]);
}
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell('windowbg1 top', 'style="background-color: white;"');
echo '&nbsp;';
next_row('windowbg1 top', 'style="background-color: white;"');
echo '&nbsp;';
next_cell("windowbg1 top");
echo '<b>Überschuss</b>';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
foreach ($resses as $ress_einzeln) {
    next_cell("windowbg1 right top");
    echo format_value($summe_ueberschuss, $ress_einzeln, $summe_ueberschuss[$ress_einzeln]);
}
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell('windowbg1 top', 'style="background-color: white;"');
echo '&nbsp;';
next_row('windowbg1 top', 'style="background-color: white;"');
echo '&nbsp;';
next_cell("windowbg1 top");
echo '<b>Bedarf</b>';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
foreach ($resses as $ress_einzeln) {
    next_cell("windowbg1 right top");
    echo format_value($summe_bedarf, $ress_einzeln, $summe_bedarf[$ress_einzeln]);
}
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell('windowbg1 top', 'style="background-color: white;"');
echo '&nbsp;';
next_row('windowbg1 top');
echo '&nbsp;';
next_cell("windowbg1 top");
echo '<b>Gesamt</b>';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell("windowbg1 top");
echo '&nbsp;';
foreach ($resses as $ress_einzeln) {
    next_cell("windowbg1 right top");
    echo format_value($summe_diff, $ress_einzeln, $summe_diff[$ress_einzeln]);
}
next_cell("windowbg1 top");
echo '&nbsp;';
next_cell('windowbg1 top', 'style="background-color: white;"');
echo '&nbsp;';
end_table();
echo '</form>';
echo '</div>';

// Legende ausgeben
echo "
<br>
<table class='table_format' style='width: 90%;'>
<tr style='white-space: nowrap;'>
<td style='width: 30px; background-color: green;'></td>
<td class='windowbg1' style='width: 70px;'>optimal</td>
<td style='width: 30px; background-color: yellow;'></td>
<td class='windowbg1' style='width: 70px;'>zu viel</td>
<td style='width: 30px; background-color: red;'></td>
<td class='windowbg1' style='width: 70px;'>zu wenig</td>
<td style='width: 30px; background-color: orange;'></td>
<td class='windowbg1' style='width: 70px;'>Lagerüberlauf</td>
<td style='width: 30px; background-color: fuchsia;'></td>
<td class='windowbg1' style='width: 70px;'>unbefriedigt</td>
<td style='width: 30px; background-color: white;'></td>
<td class='windowbg1' style='width: 70px;'>egal</td>
</tr>
</table>
";

// Maske ausgeben
if (!empty($params['edit'])) {
    echo '<br>';
    echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p>' . "\n";
    start_table();
    next_row("titlebg", 'nowrap valign=top colspan=2');
    echo "<b>" . $view['title'];
    echo " bearbeiten";
    echo '<input type="hidden" name="edit" value="' . $params['edit'] . '">' . "\n";
    echo "</b>";
    foreach ($view['edit'] as $key => $field) {
        next_row("windowbg2 top", "style='width:30%'");
        echo $field['title'];
        if (isset($field['desc'])) {
            echo '<br><i>' . $field['desc'] . '</i>';
        }
        next_cell('windowbg1', 'style="width: 100%;"');
        if (is_array($field['type'])) {
            $first = true;
            foreach ($field['type'] as $typekey => $type) {
                if (!$first) {
                    echo '&nbsp;';
                }
                echo makeField($type, $typekey);
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

function make_color($row, $key)
{
    global $user_sitterlogin;
    if (isset($row[$key . '_sichtbar']) && !$row[$key . '_sichtbar']) {
        if (isset($row['user']) && $user_sitterlogin != $row['user']) {
            return 'white';
        }
    }
    $ist      = $row[$key];
    $transfer = $row[$key . '_transfer'];
//	$transfer = $transfer + ($row[$key . "_prod"] * $params['forecast']);
    $stat  = $row[$key . '_stat'];
    $soll  = $row[$key . '_soll'];
    $lager = isset($row[$key . '_lager']) ? $row[$key . '_lager'] : 0;
//	if ($ist + $transfer - ($row[$key . "_prod"] * $params['forecast'] && !empty($params['forecast'])) < 0) {
    if ($ist + $transfer < 0) {
        return 'fuchsia';
    }
    if (!empty($lager)) {
        if (($ist == $lager) OR ($ist + $transfer > $lager)) {
            return 'orange';
        }
    }
    if (empty($soll)) {
        return 'white';
    } else {
        $x = ($ist + $transfer + $stat) * 100 / $soll;
    }
    if ($x < 90) {
        return 'red';
    } elseif ($x >= 90 && $x <= 120) {
        return 'lime';
    } else {
        return 'yellow';
    }
}

function format_value($row, $key, $value, $expand = false)
{
    global $acc_typ_topgroups;

    if ($row == null && $key == 'typ') {
		return $acc_typ_topgroups[$value];
    }
    if ($key == 'user' && !$expand) {
        return $value . "<br>(" . make_duration($row['time']) . ")";
    }
    if (is_numeric($value)) {
        if ($key == 'eisen') {
            return "<b>" . number_format($value, 0, ",", ".") . "</b>" . (!empty($row['eisen_soll']) ? "<br>(" . number_format($row['eisen_soll_diff'], 0, ",", ".") . ")" : "");
        } elseif ($key == 'stahl') {
            return "<b>" . number_format($value, 0, ",", ".") . "</b>" . (!empty($row['stahl_soll']) ? "<br>(" . number_format($row['stahl_soll_diff'], 0, ",", ".") . ")" : "");
        } elseif ($key == 'vv4a') {
            return "<b>" . number_format($value, 0, ",", ".") . "</b>" . (!empty($row['vv4a_soll']) ? "<br>(" . number_format($row['vv4a_soll_diff'], 0, ",", ".") . ")" : "");
        } elseif ($key == 'chem') {
            return "<b>" . number_format($value, 0, ",", ".") . "</b>" . (!empty($row['chem_soll']) ? "<br>(" . number_format($row['chem_soll_diff'], 0, ",", ".") . ")" : "");
        } elseif ($key == 'eis') {
            return "<b>" . number_format($value, 0, ",", ".") . "</b>" . (!empty($row['eis_soll']) ? "<br>(" . number_format($row['eis_soll_diff'], 0, ",", ".") . ")" : "");
        } elseif ($key == 'wasser') {
            return "<b>" . number_format($value, 0, ",", ".") . "</b>" . (!empty($row['wasser_soll']) ? "<br>(" . number_format($row['wasser_soll_diff'], 0, ",", ".") . ")" : "");
        } elseif ($key == 'energie') {
            return "<b>" . number_format($value, 0, ",", ".") . "</b>" . (!empty($row['energie_soll']) ? "<br>(" . number_format($row['energie_soll_diff'], 0, ",", ".") . ")" : "");
        } else {
            return number_format($value, 0, ",", ".");
        }
    } else {
        return $value;
    }
}

function make_duration($time)
{
    $diff = CURRENT_UNIX_TIME - $time;
    if ($diff < 300) {
        return 'gerade eben';
    }

    $days = intval($diff / DAY);
    $diff -= $days * DAY;
    $hours = intval($diff / HOUR);
    $diff -= $hours * HOUR;
    $mins = intval($diff / MINUTE);


    return ($days > 1 ? $days . " Tage " : ($days > 0 ? $days . " Tag " : ""))
        . str_pad($hours, 2, '0', STR_PAD_LEFT)
        . ":" . str_pad($mins, 2, '0', STR_PAD_LEFT) . 'h';

}

//****************************************************************************
function sort_coords_cmp($a, $b)
{
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

    return $result;
}

//
// Vergleichsfunktion für das sortieren
function sort_data_cmp($a, $b)
{
    global $params;

    if ($params['order'] === 'coords') {
        $result = sort_coords_cmp($a, $b);
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

    if ($params['orderd'] == 'desc') { //Sortierrichtung umdrehen
        $result *= -1;
    }

    if (($result == 0) AND ($params['order'] == 'user')) { //bei Sortierung nach Username Untersortierung nach Planetensortierung (nicht beeinflusst von der Hauptsortierrichtung)
        if ($a['sortierung'] < $b['sortierung']) {
            $result = -1;
        } elseif ($a['sortierung'] > $b['sortierung']) {
            $result = 1;
        } else {
            $result = sort_coords_cmp($a, $b); //ist die Sortierreihenfolge gleich dann nach Koordinaten sortieren
        }
    }

    return $result;
}

// ****************************************************************************
//
// Erzeugt einen Modul-Link.
function makelink($newparams, $content)
{
    return '<a href="' . makeurl($newparams) . '">' . $content . '</a>';
}

// ****************************************************************************
//
// Erzeugt eine Modul-URL.
function makeurl($newparams)
{
    global $modulname, $params;

    $url = 'index.php?action=' . $modulname;
    if (is_array($newparams)) {
        $mergeparams = array_merge($params, $newparams);
    } else {
        $mergeparams = $params;
    }
    foreach ($mergeparams as $paramkey => $paramvalue) {
        $url .= '&' . $paramkey . '=' . $paramvalue;
    }

    return $url;
}
// ****************************************************************************
//
// Update lager_soll.
function lagersoll($name, $ressart, $gal, $sys, $plan, $prod, $soll, $lager) {
	
	global $db, $db_tb_params, $db_tb_lager, $db_tb_user, $db_tb_scans;
	
	$sql = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour';");
    $row = $db->db_fetch_array($sql);
	
	switch ($ressart) {
		
		case '1':
			$sql = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_eisen';");
			$row = $db->db_fetch_array($sql);
			$h_bedarf_eisen=$row['value'];
			$bedarf=0;
			if ($prod<0) {
				$bedarf=$row['value']*abs($prod);
			}
			if ($prod=='0') {
				$sql = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'max_eisen';");
				$row = $db->db_fetch_array($sql);
				$bedarf=$row['value'];
			}
			$sql = $db->db_query("SELECT `bed_eisen` FROM `{$db_tb_scans}` WHERE `coords`='" . $gal . ":" . $sys . ":" . $plan . "';");
			$row = $db->db_fetch_array($sql);
			$soll = $bedarf + ($row['bed_eisen']/24*$h_bedarf_eisen);
			$SQLdata = array (
				'eisen_soll' => $soll
			);
			$db->db_update($db_tb_lager, $SQLdata, "WHERE (`coords_gal`=" . $gal . " AND `coords_sys`=" . $sys . " AND `coords_planet`=" . $plan .")")
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
			break;
		
		case '2':
			$sql = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_stahl';");
			$row = $db->db_fetch_array($sql);
			$h_bedarf_stahl=$row['value'];
			$bedarf=0;
			if ($prod<0) {
				$bedarf=$row['value']*abs($prod);
			}
			$sql = $db->db_query("SELECT `bed_stahl` FROM `{$db_tb_scans}` WHERE `coords`='" . $gal . ":" . $sys . ":" . $plan . "';");
			$row = $db->db_fetch_array($sql);
			$soll = $bedarf + ($row['bed_stahl']/24*$h_bedarf_stahl);
			$SQLdata = array (
				'stahl_soll' => $soll
			);
			$db->db_update($db_tb_lager, $SQLdata, "WHERE (`coords_gal`=" . $gal . " AND `coords_sys`=" . $sys . " AND `coords_planet`=" . $plan .")")
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
			break;
		
		case '3':
			$sql = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_vv4a';");
			$row = $db->db_fetch_array($sql);
			$h_bedarf_vv4a=$row['value'];
			$bedarf=0;
			if ($prod<0) {
				$bedarf=$row['value']*abs($prod);
			}
			$sql = $db->db_query("SELECT `bed_vv4a` FROM `{$db_tb_scans}` WHERE `coords`='" . $gal . ":" . $sys . ":" . $plan . "';");
			$row = $db->db_fetch_array($sql);
			$soll = $bedarf + ($row['bed_vv4a']/24*$h_bedarf_vv4a);
			$SQLdata = array (
				'vv4a_soll' => $soll
			);
			$db->db_update($db_tb_lager, $SQLdata, "WHERE (`coords_gal`=" . $gal . " AND `coords_sys`=" . $sys . " AND `coords_planet`=" . $plan .")")
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
			break;
		
		case '4':
			$sql = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_chemie';");
			$row = $db->db_fetch_array($sql);
			$h_bedarf_chemie=$row['value'];
			$bedarf=0;
			if ($prod<0) {
				$bedarf=$row['value']*abs($prod);
			}
			$sql = $db->db_query("SELECT `bed_chemie` FROM `{$db_tb_scans}` WHERE `coords`='" . $gal . ":" . $sys . ":" . $plan . "';");
			$row = $db->db_fetch_array($sql);
			$soll = $bedarf + ($row['bed_chemie']/24*$h_bedarf_chemie);
			if ($soll>$lager) {
				$soll=$lager;
			}
			$SQLdata = array (
				'chem_soll' => $soll
			);
			$db->db_update($db_tb_lager, $SQLdata, "WHERE (`coords_gal`=" . $gal . " AND `coords_sys`=" . $sys . " AND `coords_planet`=" . $plan .")")
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
			break;
		
		case '5':
			$sql = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_eis';");
			$row = $db->db_fetch_array($sql);
			$h_bedarf_eis=$row['value'];
			$bedarf=0;
			if ($prod<0) {
				$bedarf=$row['value']*abs($prod);
			}
			if ($prod=='0') {
				$bedarf=$lager/5;
			}
			$sql = $db->db_query("SELECT `bed_eis` FROM `{$db_tb_scans}` WHERE `coords`='" . $gal . ":" . $sys . ":" . $plan . "';");
			$row = $db->db_fetch_array($sql);
			$soll = $bedarf + ($row['bed_eis']/24*$h_bedarf_eis);
			if ($soll>$lager) {
				$soll=$lager;
			}
			$SQLdata = array (
				'eis_soll' => $soll
			);
			$db->db_update($db_tb_lager, $SQLdata, "WHERE (`coords_gal`=" . $gal . " AND `coords_sys`=" . $sys . " AND `coords_planet`=" . $plan .")")
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
			break;
		
		case '6':
			$sql = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_wasser';");
			$row = $db->db_fetch_array($sql);
			$h_bedarf_wasser=$row['value'];
			$bedarf=0;
			if ($prod<0) {
				$bedarf=$row['value']*abs($prod);
			}
			if ($prod=='0') {
				$bedarf=$lager/5;
			}
			$sql = $db->db_query("SELECT `bed_wasser` FROM `{$db_tb_scans}` WHERE `coords`='" . $gal . ":" . $sys . ":" . $plan . "';");
			$row = $db->db_fetch_array($sql);
			$soll = $bedarf + ($row['bed_wasser']/24*$h_bedarf_wasser);
			if ($soll>$lager) {
				$soll=$lager;
			}
			$SQLdata = array (
				'wasser_soll' => $soll
			);
			$db->db_update($db_tb_lager, $SQLdata, "WHERE (`coords_gal`=" . $gal . " AND `coords_sys`=" . $sys . " AND `coords_planet`=" . $plan .")")
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
			break;
		
		case '7':
			$sql = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'hour_energie';");
			$row = $db->db_fetch_array($sql);
			$h_bedarf_energie=$row['value'];
			$bedarf=0;
			if ($prod<0) {
				$bedarf=$row['value']*abs($prod);
			}
			if ($prod=='0') {
				$bedarf=$lager/5;
			}
			$sql = $db->db_query("SELECT `bed_energie` FROM `{$db_tb_scans}` WHERE `coords`='" . $gal . ":" . $sys . ":" . $plan . "';");
			$row = $db->db_fetch_array($sql);
			$soll = $bedarf + ($row['bed_energie']/24*$h_bedarf_energie);
			if ($soll>$lager) {
				$soll=$lager;
			}
			$SQLdata = array (
				'energie_soll' => $soll
			);
			$db->db_update($db_tb_lager, $SQLdata, "WHERE (`coords_gal`=" . $gal . " AND `coords_sys`=" . $sys . " AND `coords_planet`=" . $plan .")")
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
			break;
	}
	return $soll;
}
?>