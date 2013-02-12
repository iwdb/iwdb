<?php
/*****************************************************************************
 * m_bestellung.php                                                          *
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
 *        Originally written by [GILDE]xerex.                                *
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
// -> Name des Moduls, ist notwendig fuer die Benennung der zugehörigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Dateinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname = "m_bestellung";

//****************************************************************************
//
// -> Titel des Moduls
//
$modultitle = "Ressbestellung";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul ueber die Navigation 
//    ausfuehren darf. Moegliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "Bestellsystem zur Koordination von Logistikaufträgen im Buddler-Fleeter-System.";

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
            "CREATE TABLE `" . $db_prefix . "bestellung` (" .
            "`id` int(11) NOT NULL auto_increment," .
            "`user` varchar(30) default NULL," .
            "`team` varchar(30) default NULL," .
            "`coords_gal` tinyint(4) NOT NULL," .
            "`coords_sys` int(11) NOT NULL," .
            "`coords_planet` tinyint(4) NOT NULL," .
            "`text` varchar(254) NOT NULL," .
            "`time` int(12) default NULL," .
            "`eisen` int(7) default 0," .
            "`stahl` int(7) default 0," .
            "`chemie` int(7) default 0," .
            "`vv4a` int(7) default 0," .
            "`eis` int(7) default 0," .
            "`wasser` int(7) default 0," .
            "`energie` int(7) default 0," .
            "`credits` int(7) default 0," .
            "`volk` int(7) default 0," .
            "`schiff` varchar(50) default NULL," .
            "`anzahl` int(7) default 1," .
            "`prio` int(4) NOT NULL default '1'," .
            "`taeglich` bit NOT NULL default 0," .
            "PRIMARY KEY  (`id`)" .
            ") COMMENT='Bestellsystem' AUTO_INCREMENT=1",
        );

        foreach ($sqlscript as $sql) {
            echo "<br>" . $sql;
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
    global $modulstatus;

    $menu            = getVar('menu');
    $submenu         = getVar('submenu');
    $menuetitel      = "Bestellung #ress"; // -> Menütitel in der Navigation, #ress wird gegen die Anzahl der Bestellungen ersetzt
    $actionparamters = "";

    insertMenuItem($menu, $submenu, $menuetitel, $modulstatus, $actionparamters);
    //
    // Weitere Wiederholungen fuer weitere Menue-Einträge, z.B.
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
    /*
     global $db, $db_tb_bestellung;

     $sqlscript = array(
       "DROP TABLE " . $db_tb_bestellung,
     );


	foreach ($sqlscript as $sql) {
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

	echo "<br>Deinstallation: Datenbankänderungen = <b>OK</b><br>";
*/
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
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

// Parameter ermitteln
$params = array(
    'view'       => ensureValue(getVar('view'), array('bestellung'), 'bestellung'),
    'order'      => ensureValue(getVar('order'), array('user', 'coords', 'team', 'text', 'prio', 'time', 'menge', 'offen'), 'prio'),
    'orderd'     => ensureSortDirection(getVar('orderd')),
    'edit'       => (int)getVar('edit'),
    'delete'     => (int)getVar('delete'),
    'expand'     => (int)getVar('expand'),
    'filter_who' => getVar('filter_who'),
);

if (empty($params['filter_who'])) {
    if (!empty($user_buddlerfrom)) {
        $params['filter_who'] = '(Team) ' . $user_buddlerfrom;
    } else {
        $params['filter_who'] = $user_sitterlogin;
    }
} else {
    $params['filter_who'] = $db->escape($params['filter_who']);
}

debug_var("params", $params);

// Stammdaten abfragen
$config = array();

// Ressourcen
$config['ress'] = array(
    "eisen"   => "Eisen",
    "stahl"   => "Stahl",
    "vv4a"    => "VV4A",
    "chemie"  => "Chemie",
    "eis"     => "Eis",
    "wasser"  => "Wasser",
    "energie" => "Energie",
    "volk"    => "Bevölkerung",
    "credits" => "Credits"
);

// Spieler und Teams abfragen
$users = array();
$teams = array();

$config['filter_who']['(Alle)'] = '(Alle)';

$sql = "SELECT * FROM " . $db_tb_user;
if (!$user_fremdesitten) {
    $sql .= " WHERE allianz='" . $user_allianz . "'";
}
debug_var('sql', $sql);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $users[$row['id']] = $row['id'];
    if (!empty($row['buddlerfrom'])) {
        $teams['(Team) ' . $row['buddlerfrom']] = '(Team) ' . $row['buddlerfrom'];
    }
}
$config['users'] = $users;
//add teams and users to selectarray
$config['filter_who'] = $config['filter_who'] + $teams + $users;

// Planeten des Spielers abfragen
$config['planeten'] = array();

$sql = "SELECT coords, planetenname FROM " . $db_tb_scans . " WHERE user='" . $user_sitterlogin . "' ORDER BY sortierung";
debug_var('sql', $sql);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $config['planeten'][$row['coords']] = $row['coords'] . " " . $row['planetenname'];
}
$config['planeten'][] = "(anderer)";

// Projekte abfragen
$sql = "SELECT name, prio FROM " . $db_tb_bestellung_projekt . " WHERE schiff=0 ORDER BY prio ASC";
debug_var("sql", $sql);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $config['projects'][$row['name']]      = $row['name'] . ($row['prio'] < 999 ? " (Priorität " . $row['prio'] . ")" : "");
    $config['projects_prio'][$row['name']] = $row['prio'];
}

// Daten löschen
if (!empty($params['delete'])) {
    $sql = "DELETE FROM " . $db_tb_bestellung . " WHERE id=" . $params['delete'];
    debug_var('sql', $sql);
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $results[]        = "<div class='system_notification'>Datensatz gelöscht.</div>";
    $params['delete'] = '';
    $params['edit']   = '';
}

// Button abfragen
$button_edit = getVar("button_edit");
$button_add  = getVar("button_add");

// Edit-Daten belegen
if (!empty($button_edit) || !empty($button_add)) {
    $edit = array(
        'user'          => $db->escape(getVar('user')),
        'planet'        => $db->escape(getVar('planet')),
        'coords_gal'    => (int)getVar('coords_gal'),
        'coords_sys'    => (int)getVar('coords_sys'),
        'coords_planet' => (int)getVar('coords_planet'),
        'team'          => $db->escape(getVar('team')),
        'project'       => $db->escape(getVar('project')),
        'text'          => $db->escape(getVar('text')),
        'time'          => parseTime(getVar('time')),
        'eisen'         => (int)getVar('eisen'),
        'stahl'         => (int)getVar('stahl'),
        'chemie'        => (int)getVar('chemie'),
        'vv4a'          => (int)getVar('vv4a'),
        'eis'           => (int)getVar('eis'),
        'wasser'        => (int)getVar('wasser'),
        'energie'       => (int)getVar('energie'),
        'volk'          => (int)getVar('volk'),
        'credits'       => (int)getVar('credits'),
        'schiff'        => (int)getVar('schiff'),
        'anzahl'        => (int)getVar('anzahl'),
    );
} else {
    $edit = array(
        'user'          => $user_sitterlogin,
        'planet'        => '',
        'coords_gal'    => '',
        'coords_sys'    => '',
        'coords_planet' => '',
        'team'          => '(Team) ' . $user_buddlerfrom,
        'project'       => '(Keins)',
        'text'          => '',
        'time'          => CURRENT_UNIX_TIME,
        'eisen'         => '',
        'stahl'         => '',
        'chemie'        => '',
        'vv4a'          => '',
        'eis'           => '',
        'wasser'        => '',
        'energie'       => '',
        'volk'          => '',
        'credits'       => '',
    );
}
debug_var("edit", $edit);

// Planetenkoordinatenfelder ergänzen wenn leer
if (empty($edit['coords_gal']) AND empty($edit['coords_sys']) AND empty($edit['coords_planet']) AND !empty($edit['planet'])) {
    $coords_tokens         = explode(":", $edit['planet']);
    $edit['coords_gal']    = (int)$coords_tokens[0];
    $edit['coords_sys']    = (int)$coords_tokens[1];
    $edit['coords_planet'] = (int)$coords_tokens[2];
    unset($coords_tokens);
}

// Felder belegen
$fields = $edit;
unset($fields['planet']);

// Edit-Daten modifizieren
if (!empty($button_edit)) {
    $db->db_update($db_tb_bestellung, $fields, "WHERE `id`=" . $params['edit'])
        or error(GENERAL_ERROR, 'Could not update ress order.', '', __FILE__, __LINE__, $sql);

    $results[] = "<div class='system_notification'>Datensatz aktualisiert.</div>";
}

// Edit-Daten hinzufügen
if (!empty($button_add)) {
    $sql = "SELECT count(*) AS Anzahl FROM `{$db_tb_bestellung}` WHERE `coords_gal`=" . $fields['coords_gal'] . " AND `coords_planet`=" . $fields['coords_planet'] . " AND `coords_sys`=" . $fields['coords_sys'];
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query order information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    if ($row['Anzahl']>0) {
        $results[] = "<div class='system_notification'>Pro Planet kann nur eine Bestellung hinzugefügt werden.</div>";
    } else {
        $fields['time_created'] = CURRENT_UNIX_TIME;

        $db->db_insert($db_tb_bestellung, $fields)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

        $params['edit'] = $db->db_insert_id();

        $results[] = "<div class='system_notification'>Datensatz hinzugefügt.</div>";
    }
}

// Daten der Bestellung zum editieren abrufen
if (empty($button_edit) AND empty($button_add) AND (!empty($params['edit']))) {
    $sql = "SELECT * FROM `{$db_tb_bestellung}` WHERE `id`=" . $params['edit'];
    debug_var('sql', $sql);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    if ($row = $db->db_fetch_array($result)) {
        foreach ($row as $name => $value) {
            $edit[$name] = $value;
        }
    }
}
$edit['time'] = strftime("%d.%m.%Y %H:%M", $edit['time']);

//Planetenauswahlbox einstellen
if (empty($edit['planet'])) {
    if ((($edit['coords_gal']) !== '') AND ($edit['coords_sys'] !== '') AND ($edit['coords_planet']) !== '') {
        //Koordinatenauswahlfelder gefüllt

        if (isset($config['planeten'][$edit['coords_gal'] . ':' . $edit['coords_sys'] . ':' . $edit['coords_planet']])) {
            //Planet als Planet des Spielers bekannt -> diesen einstellen
            $edit['planet'] = $config['planeten'][$edit['coords_gal'] . ':' . $edit['coords_sys'] . ':' . $edit['coords_planet']];
        } else {
            //sonst '(anderer)'
            $edit['planet'] = '(anderer)';
        }
    } else {
        //Koordinatenauswahlfelder nicht gefüllt -> erster Planet des Spielers
        reset($config['planeten']);
        $edit['planet'] = key($config['planeten']);
    }
}
//Planetenkoordinaten füllen
if (($edit['planet'] !== '(anderer)') AND ((($edit['coords_gal']) === '') OR ($edit['coords_sys'] === '') OR ($edit['coords_planet']) === '')) {
    list($edit['coords_gal'], $edit['coords_sys'], $edit['coords_planet']) = explode(':', $edit['planet']);
}

// Tabellen-Daten abfragen
$data = array();

// Bestellungen abfragen
$sql = "SELECT *,
		 (SELECT `$db_tb_bestellung_projekt`.`prio` FROM `$db_tb_bestellung_projekt` WHERE `$db_tb_bestellung_projekt`.`name`=`$db_tb_bestellung`.`project` AND `$db_tb_bestellung_projekt`.`schiff`=0) AS prio
	 FROM $db_tb_bestellung";
if (isset($params['filter_who']) && $params['filter_who'] != '(Alle)') {
    if (strpos($params['filter_who'], '(Team) ') === 0) { //suchen nach einem Team
        $sql .= " WHERE (`{$db_tb_bestellung}`.`team`='" . $params['filter_who'] . "' OR `{$db_tb_bestellung}`.`team` IS NULL OR `{$db_tb_bestellung}`.`team`='(Alle)')";
    } else { //suchen nach einem einzelnen Spieler
        $sql .= " WHERE `{$db_tb_bestellung}`.`user`='" . $params['filter_who'] . "'";
    }
    if (!$user_fremdesitten) {
        $sql .= " AND (SELECT `allianz` FROM `{$db_tb_user}` WHERE `{$db_tb_user}`.`id`=`{$db_tb_bestellung}`.`user`) = '" . $user_allianz . "'";
    }
} elseif (!$user_fremdesitten) {
    $sql .= " WHERE (SELECT `allianz` FROM `{$db_tb_user}` WHERE `{$db_tb_user}`.`id`=`{$db_tb_bestellung}`.`user`) = '" . $user_allianz . "'";
}
$sql .= " ORDER BY `prio` DESC, `$db_tb_bestellung`.`time` DESC, `$db_tb_bestellung`.`user` ASC, `$db_tb_bestellung`.`coords_gal` ASC, `$db_tb_bestellung`.`coords_sys` ASC, `$db_tb_bestellung`.`coords_planet` ASC;";

debug_var("sql", $sql);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {

    // Koordinaten
    $coords = $row['coords_gal'] . ":" . $row['coords_sys'] . ":" . $row['coords_planet'];

    // Projekt und Bemerkung
    if (!empty($row['project']) && $row['project'] != "(Keins)") {
        $text = "<b>" . $row['project'] . "</b><br>" . $row['text'];
    } else {
        $text = $row['text'];
    }

    // Grunddaten
    $data[$row['id']] = array(
        'id'     => $row['id'],
        'user'   => $row['user'],
        'coords' => $coords,
        'team'   => $row['team'],
        'text'   => $text,
        'prio'   => $row['prio'],
        'time'   => strftime("%d.%m.%Y %H:%M", $row['time']),
        'menge'  => makeresstable($row, '', '', true),
        'sort'   => $row['prio'] . "-" . $row['time'],
    );

    // Offene Mengen
    foreach ($config['ress'] as $key => $caption) {
        $data[$row['id']]['offen'][$key] = $row[$key];
    }

    // Lieferungen abfragen
    if (!isset($lieferungen[$coords])) {
        $sql_lieferung =
            "SELECT *,
				(SELECT $db_tb_user.`buddlerfrom` FROM $db_tb_user WHERE $db_tb_user.`id`=$db_tb_lieferung.`user_from`) AS team
			FROM $db_tb_lieferung
			WHERE $db_tb_lieferung.`coords_to_gal`=" . $row['coords_gal'] . "
			AND $db_tb_lieferung.`coords_to_sys`=" . $row['coords_sys'] . "
			AND $db_tb_lieferung.`coords_to_planet`=" . $row['coords_planet'] . "
			AND $db_tb_lieferung.`art`='Transport'
			AND $db_tb_lieferung.`time`>" . $row['time_created'] . "
			AND $db_tb_lieferung.`user_from`<>$db_tb_lieferung.`user_to`
			ORDER BY $db_tb_lieferung.`time`";
        debug_var("sql_lieferung", $sql_lieferung);
        $result_lieferung = $db->db_query($sql_lieferung)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        while ($row_lieferung = $db->db_fetch_array($result_lieferung)) {
            $coords_from                = $row_lieferung['coords_from_gal'] . ":" . $row_lieferung['coords_from_sys'] . ":" . $row_lieferung['coords_from_planet'];
            $key                        = $coords_from . "-" . $row_lieferung['time'];
            $lieferungen[$coords][$key] = array(
                'user'   => $row_lieferung['user_from'],
                'coords' => $coords_from,
                'team'   => $row_lieferung['team'],
                'art'    => $row_lieferung['art'],
                'time'   => strftime("%d.%m.%Y %H:%M", $row_lieferung['time'])
            );
            debug_var("lieferungen[$coords][$key]", $lieferungen[$coords][$key]);

            foreach ($config['ress'] as $ress => $caption) {
                if ($ress == "credits") {
                    $lieferungen[$coords][$key][$ress]           = 0;
                    $lieferungen[$coords][$key][$ress . '_frei'] = 0;
                } else {
                    $lieferungen[$coords][$key][$ress]           = $row_lieferung[($ress == "chemie" ? "chem" : $ress)];
                    $lieferungen[$coords][$key][$ress . '_frei'] = $row_lieferung[($ress == "chemie" ? "chem" : $ress)];
                }
            }
        }
    }
}

// Offene Mengen berechnen
foreach ($data as $id_bestellung => $bestellung) {
    $coords = $bestellung['coords'];
    if (isset($lieferungen[$coords])) {
        foreach ($lieferungen[$coords] as $id_lieferung => $lieferung) {
            $verwendet = false;
            foreach ($config['ress'] as $key => $caption) {
                if (!empty($data[$id_bestellung]['offen'][$key]) && !empty($lieferungen[$coords][$id_lieferung][$key . '_frei'])) {
                    // Offene Bestellmenge größer als freie Liefermenge
                    if ($data[$id_bestellung]['offen'][$key] > $lieferungen[$coords][$id_lieferung][$key . '_frei']) {
                        // Offene Bestellmenge um freie Liefermenge verringern
                        $data[$id_bestellung]['offen'][$key] -= $lieferungen[$coords][$id_lieferung][$key . '_frei'];
                        // Freie Liefermenge auf 0 setzen
                        $lieferungen[$coords][$id_lieferung][$key . '_frei'] = 0;
                        // Offene Bestellmenge kleiner als freie Liefermenge
                    } elseif ($data[$id_bestellung]['offen'][$key] <= $lieferungen[$coords][$id_lieferung][$key . '_frei']) {
                        // Freie Liefermenge um offene Bestellmenge verringern
                        $lieferungen[$coords][$id_lieferung][$key . '_frei'] -= $data[$id_bestellung]['offen'][$key];
                        // Offene Bestellmenge auf 0 setzen
                        $data[$id_bestellung]['offen'][$key] = 0;
                    }
                    $verwendet = true;
                }
            }
            if ($verwendet) {
                debug_var("lieferung", $lieferungen[$coords]);
                $data[$id_bestellung]['expand'][] = array(
                    'user'   => $lieferung['user'],
                    'coords' => $lieferung['coords'],
                    'team'   => $lieferung['team'],
                    'art'    => $lieferung['art'],
                    'blank'  => " ",
                    'time'   => $lieferung['time'],
                    'menge'  => makeresstable($lieferung, '', '', true),
                    'offen'  => makeresstable($data[$id_bestellung]['offen'], '', ''),
                );
            }
        }
    }
    $orderdata = array(
        'offen_eisen'   => $data[$id_bestellung]['offen']['eisen'],
        'offen_stahl'   => $data[$id_bestellung]['offen']['stahl'],
        'offen_chemie'  => $data[$id_bestellung]['offen']['chemie'],
        'offen_vv4a'    => $data[$id_bestellung]['offen']['vv4a'],
        'offen_eis'     => $data[$id_bestellung]['offen']['eis'],
        'offen_wasser'  => $data[$id_bestellung]['offen']['wasser'],
        'offen_energie' => $data[$id_bestellung]['offen']['energie'],
        'offen_volk'    => $data[$id_bestellung]['offen']['volk'],
        'offen_credits' => $data[$id_bestellung]['offen']['credits'],
    );

    // Markiere vollständig erledigte Bestellungen
    $complete = true;
    foreach ($config['ress'] as $ress => $caption) {
        if ($data[$id_bestellung]['offen'][$ress] > 0) {
            $complete = false;
            break;
        }
    }
    if ($complete) {
        $orderdata = $orderdata + array('erledigt' => 1);
    }

    $db->db_update($db_tb_bestellung, $orderdata, "WHERE `id`=" . $id_bestellung)
        or error(GENERAL_ERROR, 'Could not update ress order.', '', __FILE__, __LINE__, $sql);

    // Mengen formatieren
    $data[$id_bestellung]['offen'] = makeresstable($data[$id_bestellung]['offen'], '', '');
}

// Daten sortieren
usort($data, "sort_data_cmp");

// Ansichten definieren
$views = array(
    'bestellung' => array(
        'title'   => 'Ressourcen Bestellungen',
        'columns' => array(
            'user'   => 'Spieler',
            'coords' => 'Koords',
            'team'   => 'Lieferant',
            'text'   => 'Text',
            'prio'   => 'Prio',
            'time'   => 'Zeit',
            'menge'  => 'Menge',
            'offen'  => 'Offen',
        ),
        'key'     => 'id',
        'edit'    => array(
            'user'    => array(
                'title'  => 'Spieler',
                'desc'   => 'Welcher Spieler soll beliefert werden?',
                'type'   => 'select',
                'values' => $config['users'],
                'value'  => $edit['user'],
            ),
            'planet'  => array(
                'id'       => 'planetcoords_select',
                'title'    => 'Planet',
                'desc'     => 'Welcher Planet soll beliefert werden?',
                'type'     => 'select',
                'values'   => $config['planeten'],
                'value'    => $edit['planet'],
                'onchange' => 'updateCoordsInput()'
            ),
            'coords'  => array(
                'title' => 'Koordinaten',
                'desc'  => 'Falls anderer Planet.',
                'type'  => array(
                    'coords_gal'    => array(
                        'id'    => 'coords_gal_input',
                        'type'  => 'number',
                        'min'   => $config_map_galaxy_min,
                        'max'   => $config_map_galaxy_max,
                        'style' => 'width: 5em',
                        'value' => $edit['coords_gal'],
                        'onchange' => 'updateCoordsSelect()'
                    ),
                    'coords_sys'    => array(
                        'id'    => 'coords_sys_input',
                        'type'  => 'number',
                        'min'   => $config_map_system_min,
                        'max'   => $config_map_system_max,
                        'style' => 'width: 5em',
                        'value' => $edit['coords_sys'],
                        'onchange' => 'updateCoordsSelect()'
                    ),
                    'coords_planet' => array(
                        'id'    => 'coords_planet_input',
                        'type'  => 'number',
                        'min'   => '1',
                        'style' => 'width: 5em',
                        'value' => $edit['coords_planet'],
                        'onchange' => 'updateCoordsSelect()'
                    ),
                ),
            ),
            'team'    => array(
                'title'  => 'Lieferant',
                'desc'   => 'Wer soll liefern?',
                'type'   => 'select',
                'values' => $config['filter_who'],
                'value'  => $edit['team'],
            ),
            'project' => array(
                'title'  => 'Projekt',
                'desc'   => 'Für welches Projekt ist die Lieferung?',
                'type'   => 'select',
                'values' => $config['projects'],
                'value'  => $edit['project'],
            ),
            'text'    => array(
                'title' => 'Text',
                'desc'  => 'Bemerkung für diese Bestellung.',
                'type'  => 'area',
                'rows'  => 5,
                'cols'  => 80,
                'value' => $edit['text'],
            ),
            'time'    => array(
                'title' => 'Zeit',
                'desc'  => 'Wann soll die Lieferung ankommen?',
                'type'  => 'text',
                'value' => $edit['time'],
                'style' => 'width: 10em;',
            ),
            'eisen'   => array(
                'title' => 'Eisen',
                'type'  => 'text',
                'value' => $edit['eisen'],
                'style' => 'width: 10em;',
            ),
            'stahl'   => array(
                'title' => 'Stahl',
                'type'  => 'text',
                'value' => $edit['stahl'],
                'style' => 'width: 10em;',
            ),
            'chemie'  => array(
                'title' => 'Chemie',
                'desc'  => 'Auf Lager achten!',
                'type'  => 'text',
                'value' => $edit['chemie'],
                'style' => 'width: 10em;',
            ),
            'vv4a'    => array(
                'title' => 'VV4A',
                'type'  => 'text',
                'value' => $edit['vv4a'],
                'style' => 'width: 10em;',
            ),
            'eis'     => array(
                'title' => 'Eis',
                'desc'  => 'Auf Lager achten!',
                'type'  => 'text',
                'value' => $edit['eis'],
                'style' => 'width: 10em;',
            ),
            'wasser'  => array(
                'title' => 'Wasser',
                'desc'  => 'Auf Lager achten!',
                'type'  => 'text',
                'value' => $edit['wasser'],
                'style' => 'width: 10em;',
            ),
            'energie' => array(
                'title' => 'Energie',
                'desc'  => 'Auf Lager achten!',
                'type'  => 'text',
                'value' => $edit['energie'],
                'style' => 'width: 10em;',
            ),
            'volk'    => array(
                'title' => 'Bevölkerung',
                'desc'  => '',
                'type'  => 'text',
                'value' => $edit['volk'],
                'style' => 'width: 10em;',
            ),
            'credits' => array(
                'title' => 'Credits',
                'desc'  => '',
                'type'  => 'text',
                'value' => $edit['credits'],
                'style' => 'width: 10em;',
            ),
        ),
        'expand'  => array(
            'title'   => 'Lieferungen',
            'columns' => array(
                'user'   => 'Spieler',
                'coords' => 'Koords',
                'team'   => 'Team',
                'art'    => 'Art',
                'blank'  => " ",
                'time'   => 'Ankunft',
                'menge'  => 'Menge',
                'offen'  => 'Offen',
            ),
        ),
    ),
);

// Aktuelle Ansicht auswählen
$view   = $views[$params['view']];
$expand = $view['expand'];

// Titelzeile ausgeben
doc_title($view['title']);

// Ergebnisse ausgeben
if (isset($results)) {
    foreach ($results as $result) {
        echo $result;
    }
}

// Team Dropdown
echo "<form method='POST' action='" . makeurl(array()) . "' enctype='multipart/form-data'><p class='center'>";
echo 'Lieferant: ';
echo makeField(
    array(
         "type"   => 'select',
         "values" => $config['filter_who'],
         "value"  => $params['filter_who']
    ), 'filter_who'
);
echo "<input type='submit' name='submit' value='anzeigen'/>";
echo "</form><br><br>\n";

// Daten ausgeben
start_form("m_flotte_versenden", array("nobody" => 1, "art" => "bestellung"));
start_table(100);
start_row("titlebg top");
foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
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
if (isset($view['edit'])) {
    next_cell("titlebg top");
    echo '&nbsp;';
}
next_cell("titlebg");
$index = 0;
foreach ($data as $row) {
    $key      = $row[$view['key']];
    $expanded = $params['expand'] == $key;
    next_row('windowbg1 top', 'style="background-color: white;"');
    echo makelink(
        array('expand' => ($expanded ? '' : $key)),
        '<img src="bilder/' . ($expanded ? 'point' : 'plus') . '.gif" alt="' . ($expanded ? 'zuklappen' : 'erweitern') . '">'
    );
    foreach ($view['columns'] as $viewcolumnkey => $viewcolumnname) {
        if ($viewcolumnkey == "text") {
            next_cell("windowbg1 top", 'style="background-color: white;"');
        } else {
            next_cell("windowbg1 top", 'style="background-color: white;"');
        }
        echo $row[$viewcolumnkey];
    }
    // Editbuttons ausgeben
    if (isset($view['edit'])) {
        next_cell("windowbg1 top");
        if (!isset($row['allow_edit']) || $row['allow_edit']) {
            echo makelink(
                array('edit' => $key),
                "<img src='bilder/file_edit_s.gif' alt='bearbeiten'>"
            );
        }
        if (!isset($row['allow_delete']) || $row['can_delete']) {
            echo makelink(
                array('delete' => $key),
                "<img src='bilder/file_delete_s.gif' onclick=\"return confirmlink(this, 'Datensatz wirklich löschen?')\" alt='löschen'>"
            );
        }
    }
    // Markierbuttons ausgeben
    next_cell("windowbg1 top");
    echo "<input type='checkbox' name='mark_" . $index++ . "' value='" . $key . "'";
    if (getVar("mark_all")) {
        echo " checked";
    }
    echo ">";
    // Expandbereich ausgeben
    if (isset($expand) && $params['expand'] == $key && isset($row['expand']) && count($row['expand'])) {
        next_row('titlebg', 'colspan=' . (count($view['columns']) + 3));
        echo "<b>" . $expand['title'] . "</b>";
        next_row('windowbg2', '');
        foreach ($expand['columns'] as $expandcolumnkey => $expandcolumnname) {
            next_cell("windowbg2 top");
            echo $expandcolumnname;
        }
        if (isset($view['edit'])) {
            next_cell("windowbg2 top");
            echo '&nbsp;';
        }
        next_cell("windowbg2");
        echo '&nbsp;';
        foreach ($row['expand'] as $expand_row) {
            next_row('windowbg1 middle', 'style="background-color: white;"');
            foreach ($expand['columns'] as $expandcolumnkey => $expandcolumnname) {
                next_cell("windowbg1 top");
                echo $expand_row[$expandcolumnkey];
            }
            if (isset($view['edit'])) {
                next_cell("windowbg1 top");
                echo '&nbsp;';
            }
        }
        next_cell("windowbg1");
        echo '&nbsp;';
        next_row('windowbg2', 'colspan=' . (count($view['columns']) + 3));
        echo "&nbsp;";
    }
}
end_table();
end_form();

// Maske ausgeben
echo '<br>';
echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p>' . "\n";
start_table();
next_row("titlebg top", 'colspan=2');
echo "<b>" . $view['title'];
if (isset($params['edit']) && is_numeric($params['edit'])) {
    echo " bearbeiten/hinzufügen";
    echo '<input type="hidden" name="edit" value="' . $params['edit'] . '">' . "\n";
} else {
    echo " hinzufügen";
}
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
if (!empty($params['edit'])) {
    echo '<input type="submit" value="ändern" name="button_edit"> ';
}
echo '<input type="submit" value="hinzufügen" name="button_add">';
end_table();
echo '</form>';
?>
    <script type="text/javascript" src="javascript/bestellung.js"></script>
<?php

function makeresstable($row, $prefix_out = '', $prefix_cmp = '', $nocolor = false)
{
    $html = '<table class="table_format_noborder" width="100%">';
    $html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'eisen', 'Eisen');
    $html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'stahl', 'Stahl');
    $html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'chemie', 'Chemie');
    $html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'vv4a', 'VV4A');
    $html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'eis', 'Eis');
    $html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'wasser', 'Wasser');
    $html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'energie', 'Energie');
    $html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'volk', 'Bevölkerung');
    $html .= makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, 'credits', 'Credits');
    $html .= "</table>";

    return $html;
}

function makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, $name, $title)
{
    $html = "";
    if (!isset($row[$prefix_cmp . $name])) {
        print_r($row);
    }
    $cmp   = $row[$prefix_cmp . $name];
    $value = $row[$prefix_out . $name];
    if ($cmp != 0) {
        $html = '<tr><td nowrap>' . $title . '</td><td class="right">';
        if (!$nocolor) {
            $html .= '<span class="';
            if ($value > 0) {
                $html .= 'ranking_red';
            } else {
                $html .= 'ranking_green';
            }
            $html .= '">';
        }
        $html .= number_format($value, 0, ',', '.');
        if (!$nocolor) {
            $html .= '</span>';
        }
        $html .= '</td></tr>';
    }

    return $html;
}

//****************************************************************************
//
// Vergleichsfunktion für das Sortieren
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
    global $modulname, $sid, $params;

    $url = 'index.php?action=' . $modulname;
    $url .= '&sid=' . $sid;
    $mergeparams = array_merge($params, $newparams);
    foreach ($mergeparams as $paramkey => $paramvalue) {
        $url .= '&' . $paramkey . '=' . $paramvalue;
    }

    return $url;
}