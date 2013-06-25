<?php
/*****************************************************************************
 * m_bestellung_schiffe.php                                                  *
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
// -> Das m_ als Beginn des Dateinamens des Moduls ist Bedingung für 
//    eine Installation über das Menue
//
$modulname = "m_bestellung_schiffe";

//****************************************************************************
//
// -> Titel des Moduls
//
$modultitle = "Schiffsbestellung";

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
$moduldesc = "Bestellsystem zur Koordination von Schiffsbestellungen.";

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
          "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "bestellung_schiffe` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `user` varchar(30) DEFAULT NULL,
              `team` varchar(30) DEFAULT NULL,
              `coords_gal` tinyint(4) NOT NULL,
              `coords_sys` int(11) NOT NULL,
              `coords_planet` tinyint(4) NOT NULL,
              `project` varchar(30) NOT NULL,
              `text` varchar(254) NOT NULL,
              `time` int(12) DEFAULT NULL,
              `time_created` int(12) NOT NULL,
              `erledigt` int(1) NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`)
              ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Bestellsystem' AUTO_INCREMENT=1",
          "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "bestellung_schiffe_pos` (
              `bestellung_id` int(11) NOT NULL,
              `schiffstyp_id` int(11) NOT NULL,
              `menge` int(11) NOT NULL,
              `offen` int(11) NOT NULL,
              PRIMARY KEY (`bestellung_id`,`schiffstyp_id`)
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8;",
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
    $menuetitel      = "Schiffe #schiffe"; // -> Menütitel in der Navigation, #schiffe wird gegen die Anzahl der Bestellungen ersetzt
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
          "DROP TABLE " . $db_tb_bestellung_pos,
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

//genutzte globale Variablen
global $db, $db_tb_scans, $db_tb_user, $db_tb_bestellung_projekt, $db_tb_schiffstyp, $db_tb_bestellung_schiffe_pos, $db_tb_bestellung_schiffe, $db_tb_lieferung, $db_tb_sitterlog;
global $config_map_galaxy_min, $config_map_galaxy_max, $config_map_system_min, $config_map_system_max, $user_sitterlogin;

// Parameter ermitteln
$params = array(
    'view'            => getVar('view'),
    'order'           => getVar('order'),
    'orderd'          => ensureSortDirection(getVar('orderd')),
    'edit'            => getVar('edit'),
    'delete'          => getVar('delete'),
    'expand'          => getVar('expand'),
    'playerSelection' => getVar('playerSelection'),
);

// Parameter validieren
if (empty($params['view'])) {
    $params['view'] = 'bestellung';
}
if (empty($params['order'])) {
    $params['order'] = 'sort';
}
if (empty($params['playerSelection'])) {
    $params['playerSelection'] = '(Alle)';
} else {
    $params['playerSelection'] = $db->escape($params['playerSelection']);
}

debug_var("params", $params);

// Stammdaten abfragen
$config = array();

// Teams und Spieler abfragen
$playerSelectionOptions = array();
$playerSelectionOptions['(Alle)'] = '(Alle)';
$playerSelectionOptions += getAllyTeamsSelect() + getAllyAccs();

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
$sql = "SELECT name, prio FROM " . $db_tb_bestellung_projekt . " WHERE schiff=1 ORDER BY prio ASC";
debug_var("sql", $sql);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $config['projects'][$row['name']]      = $row['name'] . ($row['prio'] < 999 ? " (Priorität " . $row['prio'] . ")" : "");
    $config['projects_prio'][$row['name']] = $row['prio'];
}

// Schiffstypen abfragen
$schiffstypen = array();

$sql = "SELECT * FROM " . $db_tb_schiffstyp . " WHERE bestellbar=1 ORDER BY typ, abk";
debug_var('sql', $sql);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $schiffstypen[$row['schiff']] = array(
        'id'  => $row['id'],
        'abk' => $row['abk'],
        'typ' => $row['typ'],
    );
}
$config['schiffstypen'] = $schiffstypen;

// Daten löschen
if (!empty($params['delete'])) {
    
	$sql_user = "SELECT user FROM " . $db_tb_bestellung_schiffe . " WHERE id=" . $params['delete'];
	$result_user = $db->db_query($sql_user)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql_user);
	$row_user = $db->db_fetch_array($result_user);
	$name = $row_user['user'];
	
	$sql = "DELETE FROM `" . $db_tb_bestellung_schiffe_pos . "` WHERE `bestellung_id`=" . $params['delete'];
    debug_var('sql', $sql);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $sql = "DELETE FROM " . $db_tb_bestellung_schiffe . " WHERE id=" . $params['delete'];
    debug_var('sql', $sql);
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $logtext = "<font color='#FF0000'><b>Schiffbestellung gelöscht von " . $user_sitterlogin . "</b></font>";
	$sql = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin, fromuser, date, action) VALUES ('" . $name . "', '" . $user_sitterlogin . "', '" . CURRENT_UNIX_TIME . "', '" . $logtext . "')";
	debug_var('sql', $sql);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		
	$results[]        = "<div class='system_notification'>Datensatz gelöscht.</div><br>";
    $params['delete'] = '';
    $params['edit']   = '';
}

// Button abfragen
$button_edit = (bool)getVar("button_edit");
$button_add  = (bool)getVar("button_add");

// Edit-Daten belegen
if ($button_edit OR $button_add) {
    $edit = array(
        'user'          => $db->escape(getVar('user')),
        'planet'        => $db->escape(getVar('planet')),
        'coords_gal'    => (int)getVar('coords_gal'),
        'coords_sys'    => (int)getVar('coords_sys'),
        'coords_planet' => (int)getVar('coords_planet'),
        'team'          => $db->escape(getVar('team')),
        'project'       => $db->escape(getVar('project')),
        'text'          => $db->escape(getVar('text')),
        'time'          => parsetime(getVar('time')),
    );
} else {
    $edit = array(
        'user'          => $user_sitterlogin,
        'planet'        => '',
        'coords_gal'    => '',
        'coords_sys'    => '',
        'coords_planet' => '',
        'team'          => '(Alle)',
        'project'       => '(Keins)',
        'text'          => '',
        'time'          => CURRENT_UNIX_TIME,
    );
}
foreach ($config['schiffstypen'] as $schiffstyp) {
    $edit['schiff_' . $schiffstyp['id']] = getVar('schiff_' . $schiffstyp['id']);
    debug_var("edit[schiff_" . $schiffstyp['id'] . "]", $edit['schiff_' . $schiffstyp['id']]);
}

// Planetenkoordinatenfelder ergänzen
if (!empty($edit['planet']) AND empty($edit['coords_gal']) AND empty($edit['coords_gal']) AND empty($edit['coords_gal'])) {
    $coords_tokens         = explode(":", $edit['planet']);
    $edit['coords_gal']    = (int)$coords_tokens[0];
    $edit['coords_sys']    = (int)$coords_tokens[1];
    $edit['coords_planet'] = (int)$coords_tokens[2];
}

// Felder belegen
$fields = array();
foreach ($edit as $key => $value) {
    if (strncmp($key, "schiff_", 7) != 0) {
        $fields[$key] = $value;
    }
}
unset($fields['planet']);

// Edit-Daten modifizieren
if ($button_edit) {
    $db->db_update($db_tb_bestellung_schiffe, $fields, "WHERE `id`=" . $params['edit'])
        or error(GENERAL_ERROR, 'Could not update ship order.', '', __FILE__, __LINE__, $sql);

    $results[] = "<div class='system_notification'>Datensatz aktualisiert.</div><br>";
}

// Edit-Daten hinzufügen
$doppelbelegung = "false";
if (!empty($button_add)) {
    $sql = "SELECT * FROM `" . $db_tb_bestellung_schiffe . "` WHERE coords_gal=" . $fields['coords_gal'] . " AND coords_planet=" . $fields['coords_planet'] . " AND coords_sys=" . $fields['coords_sys'] . ";";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    if ($row = $db->db_fetch_array($result)) {
        $results[]      = "<div class='system_notification'>Pro Planet kann nur eine Bestellung hinzugefügt werden.</div><br>";
        $doppelbelegung = "true";
    } else {
        $fields['time_created'] = CURRENT_UNIX_TIME;
        $db->db_insert($db_tb_bestellung_schiffe, $fields)
            or error(GENERAL_ERROR, 'Could not insert order information.', '', __FILE__, __LINE__, $sql);

        $params['edit'] = $db->db_insert_id();

        $results[] = "<div class='system_notification'>Datensatz hinzugefügt.</div><br>";
    }
}

// Edit-Daten hinzufügen/modifizeren
if (($button_add OR $button_edit) && $doppelbelegung != "true") {
    $sql = "DELETE FROM " . $db_tb_bestellung_schiffe_pos . " WHERE bestellung_id=" . $params['edit'];
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    foreach ($config['schiffstypen'] as $schiffstyp) {
        $menge = $edit['schiff_' . $schiffstyp['id']];
        if (!empty($menge)) {

            $sqldata = array(
                'bestellung_id' => (int)$params['edit'],
                'schiffstyp_id' => (int)$schiffstyp['id'],
                'menge'         => (int)$menge,
                'offen'         => (int)$menge
            );

            $db->db_insert($db_tb_bestellung_schiffe_pos, $sqldata)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

        }
    }
}

// Edit-Daten abfragen
if (!$button_edit AND !$button_add AND is_numeric($params['edit'])) {
    $sql = "SELECT * FROM " . $db_tb_bestellung_schiffe . " WHERE id=" . $params['edit'];
    debug_var('sql', $sql);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    if ($row = $db->db_fetch_array($result)) {
        foreach ($row as $name => $value) {
            $edit[$name] = $value;
        }
    }
    $sql = "SELECT * FROM " . $db_tb_bestellung_schiffe_pos . " WHERE bestellung_id=" . $params['edit'];
    debug_var('sql', $sql);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        $edit['schiff_' . $row['schiffstyp_id']] = $row['menge'];
        debug_var('edit[schiff_' . $row['schiffstyp_id'] . ']', $edit['schiff_' . $row['schiffstyp_id']]);
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
if ((!empty($edit['planet'])) AND ($edit['planet'] !== '(anderer)') AND ((($edit['coords_gal']) === '') OR ($edit['coords_sys'] === '') OR ($edit['coords_planet']) === '')) {
    list($edit['coords_gal'], $edit['coords_sys'], $edit['coords_planet']) = explode(':', $edit['planet']);
}

// Tabellen-Daten abfragen
$data = array();

// Bestellungen abfragen
$sql = "SELECT *,
            (SELECT `{$db_tb_bestellung_projekt}`.`prio` FROM `{$db_tb_bestellung_projekt}` WHERE `{$db_tb_bestellung_projekt}`.`name`=`{$db_tb_bestellung_schiffe}`.`project` AND `{$db_tb_bestellung_projekt}`.`schiff`=1) AS prio FROM `{$db_tb_bestellung_schiffe}`";
if (isset($params['playerSelection']) && $params['playerSelection'] != '(Alle)') {
    if (strpos($params['playerSelection'], '(Team) ') === 0) { //suchen nach einem Team
        $sql .= " WHERE (" . $db_tb_bestellung_schiffe . ".team='" . $params['playerSelection'] . "' OR " . $db_tb_bestellung_schiffe . ".team IS NULL" . " OR " . $db_tb_bestellung_schiffe . ".team='(Alle)')";
    } else { //suchen nach einem einzelnen Spieler
        $sql .= " WHERE (" . $db_tb_bestellung_schiffe . ".user='" . $params['playerSelection'] . "' OR " . $db_tb_bestellung_schiffe . ".team IS NULL" . " OR " . $db_tb_bestellung_schiffe . ".team='(Alle)')";
    }
    if (!$user_fremdesitten) {
        $sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_bestellung_schiffe . ".user) = '" . $user_allianz . "'";
    }
} elseif (!$user_fremdesitten) {
    $sql .= " WHERE (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_bestellung_schiffe . ".user) = '" . $user_allianz . "'";
}
$sql .= " ORDER BY `prio` DESC, `$db_tb_bestellung_schiffe`.`time` DESC, `$db_tb_bestellung_schiffe`.`user` ASC, `$db_tb_bestellung_schiffe`.`coords_gal` ASC, `$db_tb_bestellung_schiffe`.`coords_sys` ASC, `$db_tb_bestellung_schiffe`.`coords_planet` ASC;";

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
        'sort'   => $row['prio'] . "-" . $row['time'],
    );
    // Positionsdaten
    $sql_pos = "SELECT $db_tb_bestellung_schiffe_pos.*, $db_tb_schiffstyp.schiff
		FROM $db_tb_bestellung_schiffe_pos, $db_tb_schiffstyp
		WHERE bestellung_id=" . $row['id'] . " AND $db_tb_bestellung_schiffe_pos.schiffstyp_id=$db_tb_schiffstyp.id";
    debug_var("sql_pos", $sql_pos);
    $result_pos = $db->db_query($sql_pos)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row_pos = $db->db_fetch_array($result_pos)) {
        $data[$row['id']]['pos'][$row_pos['schiff']]   = $row_pos['menge'];
        $data[$row['id']]['offen'][$row_pos['schiff']] = $row_pos['menge'];
    }
    if (!empty($data[$row['id']]['pos'])) {
        $data[$row['id']]['menge'] = makeschifftable($data[$row['id']]['pos'], true);
    } else {
        $data[$row['id']]['menge'] = "";
    }
    // Lieferungen abfragen
    if (!isset($lieferungen[$coords])) {

        $sql_lieferung =
            "SELECT *,
				(SELECT `{$db_tb_user}`.`buddlerfrom` FROM `{$db_tb_user}` WHERE `{$db_tb_user}`.`id`=`{$db_tb_lieferung}`.`user_from`) AS team
			FROM $db_tb_lieferung
			WHERE $db_tb_lieferung.`coords_to_gal`=" . $row['coords_gal'] . "
			AND $db_tb_lieferung.`coords_to_sys`=" . $row['coords_sys'] . "
			AND $db_tb_lieferung.`coords_to_planet`=" . $row['coords_planet'] . "
			AND ($db_tb_lieferung.`art`='Übergabe' OR $db_tb_lieferung.`art`='Übergabe (tr Schiffe)')
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
                'time'   => strftime("%d.%m.%Y %H:%M", $row_lieferung['time']),
            );
            foreach (explode("<br>", $row_lieferung['schiffe']) as $line) {
                if (preg_match("/(\d+)\s(.*)/", $line, $match) > 0) {
                    $lieferungen[$coords][$key]['menge'][$match[2]] = $match[1];
                    $lieferungen[$coords][$key]['frei'][$match[2]]  = $match[1];
                }
            }
            debug_var("lieferungen[$coords][$key]", $lieferungen[$coords][$key]);
        }
    }
}

// Offene Mengen berechnen
foreach ($data as $id_bestellung => $bestellung) {
    $coords = $bestellung['coords'];
    if (isset($lieferungen[$coords])) {
        foreach ($lieferungen[$coords] as $id_lieferung => $lieferung) {
            $verwendet = false;
            if (isset($lieferung['frei'])) {
                foreach ($lieferung['frei'] as $key => $menge) {
                    // Sind noch offene Positionen vorhanden?
                    if (!empty($data[$id_bestellung]['offen'][$key])) {
                        // Offene Bestellmenge grösser als freie Liefermenge
                        if ($data[$id_bestellung]['offen'][$key] > $menge) {
                            // Offene Bestellmenge um freie Liefermenge verringern
                            $data[$id_bestellung]['offen'][$key] -= $menge;
                            // Freie Liefermenge auf 0 setzen
                            $lieferungen[$coords][$id_lieferung]['frei'][$key] = 0;
                            // Offene Bestellmenge kleiner als freie Liefermenge
                        } elseif ($data[$id_bestellung]['offen'][$key] <= $menge) {
                            // Freie Liefermenge um offene Bestellmenge verringern
                            $lieferungen[$coords][$id_lieferung]['frei'][$key] -= $data[$id_bestellung]['offen'][$key];
                            // Offene Bestellmenge auf 0 setzen
                            $data[$id_bestellung]['offen'][$key] = 0;
                        }
                        $verwendet = true;
                    }
                    if (isset($data[$id_bestellung]['offen'][$key])) {
                        $offen = intval($data[$id_bestellung]['offen'][$key]);
                    } else {
                        $offen = 0;
                    }

                    $sql = "UPDATE `{$db_tb_bestellung_schiffe_pos}` SET offen=" . $offen .
                        " WHERE `bestellung_id`=" . $id_bestellung .
                        "   AND `schiffstyp_id`=(SELECT `id` FROM $db_tb_schiffstyp WHERE schiff='" . $key . "')";
                    debug_var("sql", $sql);
                    $db->db_query($sql)
                        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
                }
                if ($verwendet) {
                    $data[$id_bestellung]['expand'][] = array(
                        'user'   => $lieferung['user'],
                        'coords' => $lieferung['coords'],
                        'team'   => $lieferung['team'],
                        'art'    => $lieferung['art'],
                        'blank'  => " ",
                        'time'   => $lieferung['time'],
                        'menge'  => makeschifftable($lieferung['menge'], true),
                        'offen'  => makeschifftable($data[$id_bestellung]['offen']),
                    );
                }
            }
        }
    }
    // Markiere vollständig erledigte Bestellungen
    $kontrollsumme = 0;
    if (!empty($data[$id_bestellung]['offen'])) {
        foreach ($data[$id_bestellung]['offen'] as $key => $menge) {
            $kontrollsumme += $menge;
        }
    }

    $sql_erledigt = "
		UPDATE " . $db_tb_bestellung_schiffe . "
		SET " . $db_tb_bestellung_schiffe . ".erledigt=" . ($kontrollsumme ? '0' : '1') . "
		WHERE " . $db_tb_bestellung_schiffe . ".id=" . $id_bestellung;
    debug_var("sql_erledigt", $sql_erledigt);
    $db->db_query($sql_erledigt)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    // Mengen formatieren
    if (!empty($data[$id_bestellung]['offen'])) {
        $data[$id_bestellung]['offen'] = makeschifftable($data[$id_bestellung]['offen']);
    } else {
        $data[$id_bestellung]['offen'] = "";
    }
}

// Daten sortieren
usort($data, "sort_data_cmp");

// Ansichten definieren
$views = array(
    'bestellung' => array(
        'title'   => 'Schiffe Bestellungen',
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
                'values' => getAllyAccs(),
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
                'values' => $playerSelectionOptions,
                'value'  => $params['playerSelection'],
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
                'style' => 'width: 200;',
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

$typ = '';
foreach ($config['schiffstypen'] as $schiffstyp) {
    if ($schiffstyp['typ'] != $typ) {
        $views['bestellung']['edit'][$schiffstyp['typ']] = array(
            'title'   => $schiffstyp['typ'],
            'type'    => 'label',
            'colspan' => 2
        );
        $typ                                             = $schiffstyp['typ'];
    }
    $views['bestellung']['edit']['schiff_' . $schiffstyp['id']] = array(
        'title' => $schiffstyp['abk'],
        'desc'  => 'Anzahl angeben',
        'type'  => 'number',
        'min'   => '0',
        'max'   => '1000000',
        'value' => $edit['schiff_' . $schiffstyp['id']],
        'style' => 'width: 10em;'
    );
}

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

// Auswahl Dropdown
echo "Lieferant: ";
echo makeField(
    array(
         "type"   => 'select',
         "values" => $playerSelectionOptions,
         "value"  => $params['playerSelection'],
         "onchange" => "location.href='index.php?action=m_bestellung_schiffe&amp;playerSelection='+this.options[this.selectedIndex].value",
    ), 'playerSelection'
);
echo '<br><br>';

// Daten ausgeben
start_form("m_flotte_versenden", array("nobody" => 1, "art" => "bestellung_schiffe"));
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
        "<img src='".BILDER_PATH."asc.gif'>"
    );
    echo '&nbsp;<b>' . $viewcolumnname . '</b>&nbsp;';
    echo makelink(
        array(
             'order'  => $orderkey,
             'orderd' => 'desc'
        ),
        "<img src='".BILDER_PATH."desc.gif'>"
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
                "<img src='".BILDER_PATH."file_edit_s.gif' alt='bearbeiten'>"
            );
        }
        if (!isset($row['allow_delete']) || $row['can_delete']) {
            echo makelink(
                array('delete' => $key),
                "<img src='".BILDER_PATH."file_delete_s.gif' onclick=\"return confirmlink(this, 'Datensatz wirklich löschen?')\" alt='löschen'>"
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
            next_cell("windowbg1");
            echo '&nbsp;';
        }
        next_row('windowbg2', 'colspan=' . (count($view['columns']) + 3));
        echo "&nbsp;";
    }
}
end_table();
start_table(100, 0, 4, 1, "");
next_row("", "align=\"right\"");
echo makelink(array('mark_all' => true), "Alle ausw&auml;hlen");
echo " / ";
echo makelink(array('mark_all' => false), "Auswahl entfernen");
next_row("", "align=\"right\"");
echo "<input type=\"submit\" value=\"Flotte versenden\" name=\"flotte_versenden\" class=\"submit\">";
end_table();
end_form();

$sql = "SELECT schiffstyp_id,SUM(offen) AS maxanz FROM " . $db_tb_bestellung_schiffe_pos . " WHERE offen!='' GROUP BY schiffstyp_id";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

$data = array();
?>
<table class='tablesorter' style='width: 80%;'>
	<thead>
		<tr>
			<th>
				<b>Schiffstyp</b>
			</th>
			<th>
				<b>Summe (offene Bestellungen)</b>
			</th>
		</tr>
	</thead>
	<tbody>
	
	<?php
	while ($row = $db->db_fetch_array($result)) {
	?>
		<tr>
			<td>
				<?php
				$name=GetNameByID($row['schiffstyp_id']);
				echo $name;
				?>
			</td>
			<td>
				<?php
				echo $row['maxanz'];
				?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
<?php

// Maske ausgeben
echo '<br>';
echo '<form method="POST" action="' . makeurl(array()) . '" enctype="multipart/form-data"><p>' . "\n";
start_table();
next_row("titlebg top", 'colspan=2');
echo "<b>" . $view['title'];
if (isset($params['edit']) && is_numeric($params['edit'])) {
    echo " bearbeiten/hinzufügen";
    echo '<input type="hidden" name="edit" value="' . $params['edit'] . '">' . "\n";
    // echo '<input type="hidden" name="list_team" value="'.$list_team.'" />' . "\n";
} else {
    echo " hinzufügen";
}
echo "</b>";
foreach ($view['edit'] as $key => $field) {
    if ($field['type'] == 'label') {
        next_row('titlebg top', '', isset($field['colspan']) ? $field['colspan'] : 1);
        echo $field['title'];
    } else {
        next_row("windowbg2 top", "style='width:25%;'");
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
}

next_row('titlebg center', 'colspan=2');
if (isset($params['edit']) && is_numeric($params['edit'])) {
    echo '<input type="submit" value="speichern" name="button_edit"> ';
}
echo '<input type="submit" value="hinzufügen" name="button_add">';
end_table();
echo '</form>';

?>
    <script type="text/javascript" src="javascript/bestellung.js"></script>
<?php
function makeschifftable($row, $nocolor = false)
{
    global $config;
    $html = '<table class="table_format_noborder" style="width:100%">';
    foreach ($row as $typ => $menge) {
        $html .= '<tr>';
        $html .= "<td nowrap width='30%'>";
        if (!$nocolor) {
            $html .= '<span class="';
            if ($menge > 0) {
                $html .= 'ranking_red';
            } else {
                $html .= 'ranking_green';
            }
            $html .= '">';
        }
        $html .= number_format($menge, 0, ',', '.');
        if (!$nocolor) {
            $html .= '</span>';
        }
        $html .= '</td>';
        $html .= '<td nowrap>';
        if (!$nocolor) {
            $html .= '<span class="';
            if ($menge > 0) {
                $html .= 'ranking_red';
            } else {
                $html .= 'ranking_green';
            }
            $html .= '">';
        }
        if (isset($config['schiffstypen'][$typ]) && !empty($config['schiffstypen'][$typ]['abk'])) {
            $html .= $config['schiffstypen'][$typ]['abk'];
        } else {
            $html .= $typ;
        }
        if (!$nocolor) {
            $html .= '</span>';
        }
        $html .= '</td>';
        $html .= '</tr>';
    }
    $html .= "</table>";

    return $html;
}

function makeresscol($row, $prefix_out, $prefix_cmp, $nocolor, $name, $title)
{
    $html  = "";
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
    $mergeparams = array_merge($params, $newparams);
    foreach ($mergeparams as $paramkey => $paramvalue) {
        $url .= '&' . $paramkey . '=' . $paramvalue;
    }

    return $url;
}

// **************************************************************************
//
// Gibt den Schiffsnamen zu einer Schiffs-ID zurück
function GetNameByID($id) {
	
	global $db, $db_tb_schiffstyp;
	
	$sql = "SELECT `schiff` FROM `{$db_tb_schiffstyp}` WHERE `id` = '$id';";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    return $row['schiff'];
}