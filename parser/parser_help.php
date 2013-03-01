<?php

if (!defined('IRA')) {
    die('Hacking attempt...');
}

function ResetPlaniedata($updatetime)
{
    //ungültige planSchiff/Deff/Ressscanberichte löschen (Änderung Planettyp oder Objekttyp oder username zur angegebenen Zeit)
    global $db, $db_tb_scans;

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

function ResetGeodata($updatetime)
{
    global $db, $db_tb_scans;

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
        "reset_timestamp"   => ($updatetime + 30 * DAY),
        "astro_pic"         => null
    );

    //Query um ungültige Geodaten zu löschen (nach Änderung Planettyp zur angegebenen Zeit)
    $db->db_update($db_tb_scans, $data, "WHERE `typchange_time`={$updatetime};")
        or error(GENERAL_ERROR, 'DB ResetGeodata Fehler!', '', __FILE__, __LINE__, '');

    //Zuweisen neuer Planiebilder
    //planet_pic = 0-29 bei Steinklumpen, 0-19 bei Asteroid oder Gasgigant oder Eisplanet, wird bei Typ "nichts" ignoriert
    //shadow_pic = 0-2 wird bei Planietyp "Asteroid" oder "Nichts" ignoriert
    //bg_pic = 0-3
    $sql = "UPDATE `{$db_tb_scans}`
           SET `planet_pic` = IF(STRCMP(`typ`,'Steinklumpen'),ROUND( RAND() * 19),ROUND( RAND() * 29)), `shadow_pic` = ROUND( RAND() * 2), `bg_pic` = ROUND( RAND() * 3) 
           WHERE typchange_time = {$updatetime};";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB Planie_pic Update Fehler!', '', __FILE__, __LINE__, $sql);

}

function AddAllychangetoHistory($updatetime)
{
    //Allianzänderungen in Historytabele übertragen
    global $db, $db_tb_spielerallychange, $db_tb_spieler;

    $sql = "INSERT INTO `{$db_tb_spielerallychange}` (`NAME`, `fromally`, `toally`, `TIME`)
            SELECT `NAME`, `exallianz`, `allianz`, `allychange_time`
            FROM `{$db_tb_spieler}`
            WHERE `allychange_time` = {$updatetime}
            ON DUPLICATE KEY UPDATE `{$db_tb_spielerallychange}`.`name`=`{$db_tb_spielerallychange}`.`name`";     //means ON DUPLICATE KEY 'DO NOTHING'

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB AddAllychangetoHistory Fehler!', '', __FILE__, __LINE__, $sql);

}

function SyncAllies($updatetime)
{
    //aktuelle Allianzen in Kartendaten übertragen
    global $db, $db_tb_spieler, $db_tb_scans;

    $sql = "UPDATE `{$db_tb_spieler}`, `{$db_tb_scans}`
            SET `{$db_tb_scans}`.`allianz` = `{$db_tb_spieler}`.`allianz`
            WHERE `{$db_tb_spieler}`.`name` = `{$db_tb_scans}`.`user`
            AND `{$db_tb_spieler}`.playerupdate_time = {$updatetime};";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB TransferAllytoScans Fehler!', '', __FILE__, __LINE__, $sql);

    //Allianz für nicht mehr vorhandene Spieler löschen
    $sql = "UPDATE `{$db_tb_scans}` SET `allianz` = '' WHERE `userchange_time` = {$updatetime} AND `USER` = '';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB AllyDelete Fehler!', '', __FILE__, __LINE__, $sql);
}

/**
 *
 * @desc Bestimmung von Spielernamen aufgrund von Koordinaten
 * @author Mac (MacXY@herr-der-mails.de)
 * @global object    $db
 * @global string    $db_tb_scans
 *
 * @param string     $coords
 *
 * @todo   Funktion sollte gecached werden, damit nicht unnötig viele Aufrufe erfolgen
 * @return string
 */
function GetNameByCoords($coords)
{
    global $db, $db_tb_scans;

    if (empty($coords)) {
        return '';
    }

    $sql = "SELECT `user` FROM `{$db_tb_scans}` WHERE `coords` = '$coords';";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    return $row['user'];
}

/**
 *
 * @desc Bestimmung von Allianznamen aufgrund von Spielername
 * @author Mac (MacXY@herr-der-mails.de)
 * @global object    $db
 * @global string    $db_tb_scans
 *
 * @param string     $username
 *
 * @todo   Funktion sollte gecached werden, damit nicht unnoetig viele Aufrufe erfolgen
 * @return string
 */
function GetAllianceByUser($username)
{
    global $db, $db_tb_scans;

    if (empty($username)) {
        return '';
    }

    $sql = "SELECT DISTINCT `allianz` FROM `{$db_tb_scans}` WHERE `user` = '$username';";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    return $row['allianz'];
}

function GetObjectByCoords($coords)
{
    global $db, $db_tb_scans;
    if (empty($coords)) {
        return '';
    }
    $sql = "SELECT `objekt` FROM `{$db_tb_scans}` WHERE `coords` = '$coords';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    return $row['objekt'];
}

function GetObjectPictureByCoords($coords)
{

    $objekt = GetObjectByCoords($coords);
    $objectPicture = '';

    if ($objekt == 'Kolonie') {
        $objectPicture = "<img src='" . BILDER_PATH . "kolo.png'>";
    } else if ($objekt == 'Sammelbasis') {
        $objectPicture = "<img src='" . BILDER_PATH . "ress_basis.png'>";
    } else if ($objekt == 'Artefaktbasis') {
        $objectPicture = "<img src='" . BILDER_PATH . "artefakt_basis.png'>";
    } else if ($objekt == 'Kampfbasis') {
        $objectPicture = "<img src='" . BILDER_PATH . "kampf_basis.png'>";
    }

    return $objectPicture;
}
