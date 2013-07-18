<?php

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

/**
 *
 * @desc   ungültige Gebäude/Deffdaten eines bestimmten Zeitpunkts der Planettyp-, Objekttyp- oder username-Änderung löschen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db                 Datenbankhandle
 * @global string $db_tb_scans        Bezeichner der Tabelle mit Planetendaten
 *
 * @param int     $updatetime         Koordinaten
 */
function ResetPlaniedata($updatetime)
{
    global $db, $db_tb_scans;

    $updatetime = (int)$updatetime;

    $data = array(
        "eisen"           => null,
        "stahl"           => null,
        "vv4a"            => null,
        "chemie"          => null,
        "eis"             => null,
        "wasser"          => null,
        "energie"         => null,
        "plan"            => null,
        "def"             => null,
        "geb"             => null,
        "lager_chemie"    => null,
        "lager_eis"       => null,
        "lager_energie"   => null,
        "x11"             => null,
        "terminus"        => null,
        "x13"             => null,
        "fehlscantime"    => null,
        "reserveraid"     => null,
        "reserveraiduser" => null,
        "gebscantime"     => null
    );

    $db->db_update($db_tb_scans, $data, "WHERE `userchange_time` = {$updatetime} OR `typchange_time` = {$updatetime}  OR `objektchange_time` = {$updatetime};")
        or error(GENERAL_ERROR, 'DB ResetPlaniedata Fehler!', '', __FILE__, __LINE__, '');
}

/**
 *
 * @desc   ungültige Gebäude/Deffdaten eines bestimmten Planeten löschen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db              Datenbankhandle
 * @global string $db_tb_scans     Bezeichner der Tabelle mit Planetendaten
 *
 * @param string  $coords          Koordinaten
 */
function ResetPlaniedataByCoords($coords)
{
    global $db, $db_tb_scans;

    $coords = $db->escape($coords);

    $data = array(
        "eisen"           => null,
        "stahl"           => null,
        "vv4a"            => null,
        "chemie"          => null,
        "eis"             => null,
        "wasser"          => null,
        "energie"         => null,
        "plan"            => null,
        "def"             => null,
        "geb"             => null,
        "lager_chemie"    => null,
        "lager_eis"       => null,
        "lager_energie"   => null,
        "x11"             => null,
        "terminus"        => null,
        "x13"             => null,
        "fehlscantime"    => null,
        "reserveraid"     => null,
        "reserveraiduser" => null,
        "gebscantime"     => null
    );

    $db->db_update($db_tb_scans, $data, "WHERE `coords` = '{$coords}';")
        or error(GENERAL_ERROR, 'DB ResetPlaniedataByCoords Fehler!', '', __FILE__, __LINE__, '');
}

/**
 *
 * @desc   ungültige Geodaten löschen von Planeten einer bestimmten Typänderungszeit
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db                   Datenbankhandle
 * @global string $db_tb_scans          Bezeichner der Tabelle mit Planetendaten
 *
 * @param int     $typchange_time       Typänderungszeit
 */
function ResetGeodata($typchange_time)
{
    global $db, $db_tb_scans;

    $typchange_time = (int)$typchange_time;

    $data = array(
        "eisengehalt"       => null,
        "chemievorkommen"   => null,
        "eisdichte"         => null,
        "lebensbedingungen" => null,
        "gravitation"       => null,
        "besonderheiten"    => null,
        "fmod"              => null,
        "kgmod"             => null,
        "dgmod"             => null,
        "ksmod"             => null,
        "dsmod"             => null,
        "tteisen"           => null,
        "ttchemie"          => null,
        "tteis"             => null,
        "geoscantime"       => null,
        "reset_timestamp"   => null,
        "astro_pic"         => null
    );

    $db->db_update($db_tb_scans, $data, "WHERE `typchange_time`={$typchange_time};")
        or error(GENERAL_ERROR, 'DB ResetGeodata Fehler!', '', __FILE__, __LINE__, '');
}

/**
 *
 * @desc   ungültige Geodaten löschen eines bestimmten Planeten
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db                  Datenbankhandle
 * @global string $db_tb_scans         Bezeichner der Tabelle mit Planetendaten
 *
 * @param string  $coords              Koordinaten
 */
function ResetGeodataByCoords($coords)
{
    global $db, $db_tb_scans;

    $coords = $db->escape($coords);

    $data = array(
        "eisengehalt"       => null,
        "chemievorkommen"   => null,
        "eisdichte"         => null,
        "lebensbedingungen" => null,
        "gravitation"       => null,
        "besonderheiten"    => null,
        "fmod"              => null,
        "kgmod"             => null,
        "dgmod"             => null,
        "ksmod"             => null,
        "dsmod"             => null,
        "tteisen"           => null,
        "ttchemie"          => null,
        "tteis"             => null,
        "geoscantime"       => null,
        "reset_timestamp"   => null,
        "astro_pic"         => null
    );

    $db->db_update($db_tb_scans, $data, "WHERE `coords` = '{$coords}';")
        or error(GENERAL_ERROR, 'DB ResetGeodataByCoords Fehler!', '', __FILE__, __LINE__, '');
}

/**
 *
 * @desc   Allianzänderung einer bestimmten Aktualisierungszeit in Historytabele übertragen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db                  Datenbankhandle
 * @global string $db_tb_spieler       Bezeichner der Tabelle mit Spielerdaten
 * @global string $db_tb_scans         Bezeichner der Tabelle mit Planetendaten
 *
 * @param int     $updatetime          Spielername
 */
function AddAllychangetoHistory($updatetime)
{
    global $db, $db_tb_spielerallychange, $db_tb_spieler;

    $updatetime = (int)$updatetime;

    $sql = "INSERT INTO `{$db_tb_spielerallychange}` (`name`, `fromally`, `toally`, `time`)
            SELECT `name`, `exallianz`, `allianz`, `allychange_time`
            FROM `{$db_tb_spieler}`
            WHERE `allychange_time` = {$updatetime}
            ON DUPLICATE KEY UPDATE `{$db_tb_spielerallychange}`.`name`=`{$db_tb_spielerallychange}`.`name`"; //means ON DUPLICATE KEY 'DO NOTHING'

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB AddAllychangetoHistory Fehler!', '', __FILE__, __LINE__, $sql);
}

/**
 *
 * @desc   Allianzänderung eines bestimmten Spielers in Historytabele übertragen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_spieler Bezeichner der Tabelle mit Spielerdaten
 * @global string $db_tb_scans   Bezeichner der Tabelle mit Planetendaten
 *
 * @param string  $name          Spielername
 */
function AddAllychangetoHistoryByUser($name)
{
    global $db, $db_tb_spielerallychange, $db_tb_spieler;

    $name = $db->escape($name);

    $sql = "INSERT INTO `{$db_tb_spielerallychange}` (`name`, `fromally`, `toally`, `time`)
            SELECT `name`, `exallianz`, `allianz`, `allychange_time`
            FROM `{$db_tb_spieler}`
            WHERE `name` = '{$name}'
            ON DUPLICATE KEY UPDATE `{$db_tb_spielerallychange}`.`name`=`{$db_tb_spielerallychange}`.`name`"; //means ON DUPLICATE KEY 'DO NOTHING'

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB AddAllychangetoHistoryByUser Fehler!', '', __FILE__, __LINE__, $sql);
}

/**
 *
 * @desc   aktuelle Allianzen in Planetendaten übertragen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_spieler Bezeichner der Tabelle mit Spielerdaten
 * @global string $db_tb_scans   Bezeichner der Tabelle mit Planetendaten
 *
 * @param int     $updatetime    optional Zeitpunkt der Allianzaktualisierung
 */
function SyncAllies($updatetime)
{
    global $db, $db_tb_spieler, $db_tb_scans;

    $updatetime = (int)$updatetime;

    $sql = "UPDATE `{$db_tb_spieler}`, `{$db_tb_scans}`
            SET `{$db_tb_scans}`.`allianz` = `{$db_tb_spieler}`.`allianz`
            WHERE `{$db_tb_spieler}`.`name` = `{$db_tb_scans}`.`user`";

    if (!empty($updatetime)) {
        $sql .= "AND `{$db_tb_spieler}`.playerupdate_time = {$updatetime};";
    }

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB TransferAllytoScans Fehler!', '', __FILE__, __LINE__, $sql);

    deleteInvalidAlliances();
}

/**
 *
 * @desc   Ungültige Allianzbezeichner von Planetendaten löschen denen kein user mehr zugeordnet ist
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_scans   Bezeichner der Tabelle mit Planetendaten
 */
function deleteInvalidAlliances()
{
    global $db, $db_tb_scans;

    $sql = "UPDATE `{$db_tb_scans}` SET `allianz` = '' WHERE `user` = '';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB AllyDelete Fehler!', '', __FILE__, __LINE__, $sql);
}

/**
 *
 * @desc   Bestimmung von Spielernamen aufgrund von Koordinaten
 *
 * @author Mac (MacXY@herr-der-mails.de)
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_scans   Bezeichner der Tabelle mit Planetendaten
 *
 * @param string  $coords        Koordinaten des Planeten
 *
 * @return string                Spielername
 *
 * @todo   Funktion sollte gecached werden, damit nicht unnötig viele Aufrufe erfolgen?
 */
function getNameByCoords($coords)
{
    global $db, $db_tb_scans;

    if (empty($coords)) {
        return '';
    }

    $coords = $db->escape($coords);

    $sql = "SELECT `user` FROM `{$db_tb_scans}` WHERE `coords` = '$coords';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    return $row['user'];
}

/**
 *
 * @desc   Bestimmung von Allianznamen aufgrund von Spielername
 *
 * @author Mac (MacXY@herr-der-mails.de)
 *
 * @global object $db                   Datenbankhandle
 * @global string $db_tb_spieler        Bezeichner der Tabelle mit Spielerdaten
 *
 * @param string  $username             Spielername
 *
 * @return string Allianz
 *
 * @todo   Funktion sollte gecached werden, damit nicht unnötig viele Aufrufe erfolgen?
 */
function getAllianceByUser($username)
{
    global $db, $db_tb_spieler;

    if (empty($username)) {
        return '';
    }

    $username = $db->escape($username);

    $sql = "SELECT `allianz` FROM `{$db_tb_spieler}` WHERE `name` = '$username';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    return $row['allianz'];
}

/**
 *
 * @desc   Allianz ggf. aktualisieren und aktelle zurückgeben
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_spieler Bezeichner der Tabelle mit Spielerdaten
 *
 * @param string  $name
 * @param string  $allianz
 * @param int  $time
 *
 * @return string Allianz
 */
function updateUserAlliance($name, $allianz, $time)
{
    global $db, $db_tb_spieler;

    if ($name === '') {
        return '';
    }

    $name    = $db->escape($name);
    $allianz = $db->escape($allianz);
    $time    = (int)$time;

    $sql = "SELECT `allianz`, `playerupdate_time` FROM `$db_tb_spieler` WHERE `name`='" . $name . "';";
    $result_player = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query player information.', '', __FILE__, __LINE__, $sql);
    $row_player = $db->db_fetch_array($result_player);

    if (!empty($row_player)) {

        if ($time <= $row_player['playerupdate_time']) { //Übergebene informationen sind nicht neuer

            return $row_player['allianz'];

        } else { //Übergebene informationen sind aktueller -> bei Allianzänderung Allianz aktualisieren

            if ($row_player['allianz'] === $allianz) {

                $SQLdata = array(
                    'playerupdate_time' => $time
                );

                $db->db_update($db_tb_spieler, $SQLdata, "WHERE `name` = '{$name}';")
                    or error(GENERAL_ERROR, 'Could not update player information.', '', __FILE__, __LINE__, '');

            } else {

                $SQLdata = array(
                    'allianz'           => $allianz,
                    'allianzrang'       => null,
                    'exallianz'         => $row_player['allianz'],
                    'allychange_time'   => $time,
                    'playerupdate_time' => $time
                );

                $db->db_update($db_tb_spieler, $SQLdata, "WHERE `name` = '{$name}';")
                    or error(GENERAL_ERROR, 'Could not update player information.', '', __FILE__, __LINE__, '');

                //Allianzänderung in Historytabele übertragen
                AddAllychangetoHistory($time);

                //aktuelle Allianz in alle Kartendaten übertragen
                SyncAllies($time);

            }

            return $allianz;

        }

    } else { //neuer Spieler -> eintragen

        $SQLdata = array(

            'name'              => $name,
            'allianz'           => $allianz,
            'playerupdate_time' => $time

        );

        $db->db_insert($db_tb_spieler, $SQLdata)
            or error(GENERAL_ERROR, 'Could not insert player information.', '', __FILE__, __LINE__, '');

        return $allianz;

    }
}

/**
 *
 * @desc   Objekt auf einem Planeten anhand der Koordinaten bestimmen
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_spieler Bezeichner der Tabelle mit Spielerdaten
 *
 * @param string  $coords Koordinaten des Planeten
 *
 * @return string Objekt auf dem Planeten
 */
function getObjectByCoords($coords)
{
    global $db, $db_tb_scans;

    if (empty($coords)) {
        return '';
    }

    $coords = $db->escape($coords);

    $sql = "SELECT `objekt` FROM `{$db_tb_scans}` WHERE `coords` = '$coords';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    return $row['objekt'];
}

/**
 *
 * @desc   Objektbild des Objekts auf einem Planeten anhand der Koordinaten bestimmen
 *
 * @param string  $coords Koordinaten des Planeten
 *
 * @return string html img Element
 */
function getObjectPictureByCoords($coords)
{
    $objekt        = getObjectByCoords($coords);
    $objectPicture = '';

    if ($objekt === 'Kolonie') {
        $objectPicture = "<img src='" . BILDER_PATH . "kolo.png'>";
    } else if ($objekt === 'Sammelbasis') {
        $objectPicture = "<img src='" . BILDER_PATH . "ress_basis.png'>";
    } else if ($objekt === 'Artefaktbasis') {
        $objectPicture = "<img src='" . BILDER_PATH . "artefakt_basis.png'>";
    } else if ($objekt === 'Kampfbasis') {
        $objectPicture = "<img src='" . BILDER_PATH . "kampf_basis.png'>";
    }

    return $objectPicture;
}