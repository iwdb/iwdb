<?php

function ResetPlaniedata($updatetime)
{
    //ungültige planSchiff/Deff/Ressscanberichte löschen (Änderung Planettyp oder Objekttyp oder username zur angegebenen Zeit)
    global $db_prefix, $db;

    $data = array(
        "eisen" => NULL,
        "stahl" => NULL,
        "vv4a" => NULL,
        "chemie" => NULL,
        "eis" => NULL,
        "wasser" => NULL,
        "energie" => NULL,
        "plan" => NULL,
        "def" => NULL,
        "geb" => NULL,
        "lager_chemie" => NULL,
        "lager_eis" => NULL,
        "lager_energie" => NULL,
        "x11" => NULL,
        "terminus" => NULL,
        "x13" => NULL,
        "fehlscantime" => NULL,
        "reserveraid" => NULL,
        "reserveraiduser" => NULL,
        "gebscantime" => NULL
    );

    $db->db_update("{$db_prefix}scans", $data, "WHERE `userchange_time` = {$updatetime} OR `typchange_time` = {$updatetime}  OR `objektchange_time` = {$updatetime};")
        or error(GENERAL_ERROR, 'DB ResetPlaniedata Fehler!', '', __FILE__, __LINE__, '');
}

function ResetGeodata($updatetime)
{
    global $db_prefix, $db;

    $data = array(
        "eisengehalt" => NULL,
        "chemievorkommen" => NULL,
        "eisdichte" => NULL,
        "lebensbedingungen" => NULL,
        "gravitation" => NULL,
        "besonderheiten" => NULL,
        "fmod" => NULL,
        "kgmod" => NULL,
        "dgmod" => NULL,
        "ksmod" => NULL,
        "dsmod" => NULL,
        "tteisen" => NULL,
        "ttchemie" => NULL,
        "tteis" => NULL,
        "geoscantime" => NULL,
        "reset_timestamp" => ($updatetime + 30 * 24 * 3600),
        "astro_pic" => NULL
    );

    //Query um ungültige Geodaten zu löschen (nach Änderung Planettyp zur angegebenen Zeit)
    $db->db_update("{$db_prefix}scans", $data, "WHERE typchange_time = {$updatetime};")
        or error(GENERAL_ERROR, 'DB ResetGeodata Fehler!', '', __FILE__, __LINE__, '');

    //Zuweisen neuer Planiebilder
    //planet_pic = 0-29 bei Steinklumpen, 0-19 bei Asteroid oder Gasgigant oder Eisplanet, wird bei Typ "nichts" ignoriert
    //shadow_pic = 0-2 wird bei Planietyp "Asteroid" oder "Nichts" ignoriert
    //bg_pic = 0-3
    $sql = "UPDATE `{$db_prefix}scans`
           SET `planet_pic` = IF(STRCMP(`typ`,'Steinklumpen'),ROUND( RAND() * 19),ROUND( RAND() * 29)), `shadow_pic` = ROUND( RAND() * 2), `bg_pic` = ROUND( RAND() * 3) 
           WHERE typchange_time = {$updatetime};";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB Planie_pic Update Fehler!', '', __FILE__, __LINE__, $sql);

}

function AddAllychangetoHistory($updatetime)
{
    //Allianzänderungen in Historytabele übertragen
    global $db_prefix, $db;

    $sql = "INSERT INTO `{$db_prefix}spielerallychange` (`name`, `fromally`, `toally`, `time`)
            SELECT `name`, `exallianz`, `allianz`, `allychange_time`
            FROM `{$db_prefix}spieler`
            WHERE `allychange_time` = {$updatetime}
            ON DUPLICATE KEY UPDATE `{$db_prefix}spielerallychange`.`name`=`{$db_prefix}spielerallychange`.`name`";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB AddAllychangetoHistory Fehler!', '', __FILE__, __LINE__, $sql);

}

function SyncAllies($updatetime)
{
    //aktuelle Allianzen in Kartendaten übertragen
    global $db_prefix, $db;

    $sql = "UPDATE `{$db_prefix}spieler`, `{$db_prefix}scans`
            SET `{$db_prefix}scans`.`allianz` = `{$db_prefix}spieler`.`allianz`
            WHERE `{$db_prefix}spieler`.`name` = `{$db_prefix}scans`.`user`
            AND `{$db_prefix}spieler`.playerupdate_time = {$updatetime};";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB TransferAllytoScans Fehler!', '', __FILE__, __LINE__, $sql);

    //Allianz für nicht mehr vorhandene Spieler löschen
    $sql = "UPDATE `{$db_prefix}scans` SET `allianz` = '' WHERE `userchange_time` = {$updatetime} AND `user` = '';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB AllyDelete Fehler!', '', __FILE__, __LINE__, $sql);
}

/**
 *
 * @desc Bestimmung von Spielernamen aufgrund von Koordinaten
 * @author Mac (MacXY@herr-der-mails.de)
 * @global obj $db
 * @global string $db_tb_scans
 * @param string $coords
 * @todo Funktion sollte gecached werden, damit nicht unnoetig viele Aufrufe erfolgen
 * @return string 
 */
function GetNameByCoords($coords)
{
    global $db, $db_tb_scans;
    
    if (empty($coords))
        return;
    
    $sql = "SELECT user FROM " . $db_tb_scans . " WHERE coords = '$coords'";
                 
    $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                'Could not query config information.', '', 
                __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    
    return $row['user'];
}

/**
 *
 * @desc Bestimmung von Allianznamen aufgrund von Spielername
 * @author Mac (MacXY@herr-der-mails.de)
 * @global obj $db
 * @global string $db_tb_scans
 * @param string $username
 * @todo Funktion sollte gecached werden, damit nicht unnoetig viele Aufrufe erfolgen
 * @return string 
 */
function GetAllianceByUser($username)
{
    global $db, $db_tb_scans;
    
    if (empty($username))
        return;
    
    $sql = "SELECT DISTINCT allianz FROM " . $db_tb_scans . " WHERE user = '$username'";
                 
    $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                'Could not query config information.', '', 
                __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    
    return $row['allianz'];
}

function GetObjectByCoords($coords) {
	global $db, $db_tb_scans;
	if (empty($coords))
		return;
	$sql = "SELECT objekt FROM " . $db_tb_scans . " WHERE coords = '$coords'";
	$result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                'Could not query config information.', '', 
                __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    return $row['objekt'];
}