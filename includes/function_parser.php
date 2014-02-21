<?php
/**
 * (Parser-)Hilfsfunktionen
 *
 * @author masel (masel678@googlemail.com)
 * @author Mac (MacXY@herr-der-mails.de)
 *
 * @package IWDB
 */

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

/**
 * @desc   ungültige Gebäude/Deffdaten eines bestimmten Zeitpunkts der Planettyp-, Objekttyp- oder username-Änderung löschen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db          Datenbankhandle
 * @global string $db_tb_scans Bezeichner der Tabelle mit Planetendaten
 *
 * @param int     $iUpdateTime Koordinaten
 */
function ResetPlaniedata($iUpdateTime)
{
    global $db, $db_tb_scans;

    $iUpdateTime = (int)$iUpdateTime;

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

    $db->db_update($db_tb_scans, $data, "WHERE `userchange_time` = {$iUpdateTime} OR `typchange_time` = {$iUpdateTime}  OR `objektchange_time` = {$iUpdateTime};")
        or error(GENERAL_ERROR, 'DB ResetPlaniedata Fehler!', '', __FILE__, __LINE__, '');
}

/**
 * @desc     Gebäude/Deffdaten eines bestimmten Planeten löschen
 *
 * @author   masel (masel678@googlemail.com)
 *
 * @global object $db          Datenbankhandle
 * @global string $db_tb_scans Bezeichner der Tabelle mit Planetendaten
 *
 * @param string $strCoords Koordinaten
 */
function ResetPlaniedataByCoords($strCoords)
{
    global $db, $db_tb_scans, $db_tb_scans_geb;

    $strCoords = $db->escape($strCoords);

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

    $db->db_update($db_tb_scans, $data, "WHERE `coords` = '{$strCoords}';")
        or error(GENERAL_ERROR, 'DB ResetPlaniedataByCoords Fehler!', '', __FILE__, __LINE__, '');

    //vorhandene Gebäudeinfos löschen
    $sql_del="DELETE FROM `{$db_tb_scans_geb}` WHERE `coords` = '{$strCoords}';";
    $result = $db->db_query($sql_del)
        or error(GENERAL_ERROR, 'Could not delete buildingscan information.', '', __FILE__, __LINE__, $sql_del);
}

/**
 * @desc   ungültige Geodaten löschen von Planeten einer bestimmten Typänderungszeit
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db                   Datenbankhandle
 * @global string $db_tb_scans          Bezeichner der Tabelle mit Planetendaten
 *
 * @param int     $iTypchangeTime       Typänderungszeit
 */
function ResetGeodata($iTypchangeTime)
{
    global $db, $db_tb_scans;

    $iTypchangeTime = (int)$iTypchangeTime;

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

    $db->db_update($db_tb_scans, $data, "WHERE `typchange_time`={$iTypchangeTime};")
        or error(GENERAL_ERROR, 'DB ResetGeodata Fehler!', '', __FILE__, __LINE__, '');
}

/**
 * @desc   ungültige Geodaten löschen eines bestimmten Planeten
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db                  Datenbankhandle
 * @global string $db_tb_scans         Bezeichner der Tabelle mit Planetendaten
 *
 * @param string  $strCoords           Koordinaten
 */
function ResetGeodataByCoords($strCoords)
{
    global $db, $db_tb_scans;

    $strCoords = $db->escape($strCoords);

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

    $db->db_update($db_tb_scans, $data, "WHERE `coords` = '{$strCoords}';")
        or error(GENERAL_ERROR, 'DB ResetGeodataByCoords Fehler!', '', __FILE__, __LINE__, '');
}

/**
 * @desc   Allianzänderung einer bestimmten Aktualisierungszeit in Historytabele übertragen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db                  Datenbankhandle
 * @global string $db_tb_spieler       Bezeichner der Tabelle mit Spielerdaten
 * @global string $db_tb_scans         Bezeichner der Tabelle mit Planetendaten
 *
 * @param int     $iUpdateTime         Spielername
 */
function AddAllychangetoHistory($iUpdateTime)
{
    global $db, $db_tb_spielerallychange, $db_tb_spieler;

    $iUpdateTime = (int)$iUpdateTime;

    $sql = "INSERT INTO `{$db_tb_spielerallychange}` (`name`, `fromally`, `toally`, `time`)
            SELECT `name`, `exallianz`, `allianz`, `allychange_time`
            FROM `{$db_tb_spieler}`
            WHERE `allychange_time` = {$iUpdateTime}
            ON DUPLICATE KEY UPDATE `{$db_tb_spielerallychange}`.`name`=`{$db_tb_spielerallychange}`.`name`"; //means ON DUPLICATE KEY 'DO NOTHING'

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB AddAllychangetoHistory Fehler!', '', __FILE__, __LINE__, $sql);
}

/**
 * @desc   Allianzänderung eines bestimmten Spielers in Historytabele übertragen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db             Datenbankhandle
 * @global string $db_tb_spieler  Bezeichner der Tabelle mit Spielerdaten
 * @global string $db_tb_scans    Bezeichner der Tabelle mit Planetendaten
 *
 * @param string  $strSpielerName Spielername
 */
function AddAllychangetoHistoryByUser($strSpielerName)
{
    global $db, $db_tb_spielerallychange, $db_tb_spieler;

    $strSpielerName = $db->escape($strSpielerName);

    $sql = "INSERT INTO `{$db_tb_spielerallychange}` (`name`, `fromally`, `toally`, `time`)
            SELECT `name`, `exallianz`, `allianz`, `allychange_time`
            FROM `{$db_tb_spieler}`
            WHERE `name` = '{$strSpielerName}'
            ON DUPLICATE KEY UPDATE `{$db_tb_spielerallychange}`.`name`=`{$db_tb_spielerallychange}`.`name`"; //means ON DUPLICATE KEY 'DO NOTHING'

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB AddAllychangetoHistoryByUser Fehler!', '', __FILE__, __LINE__, $sql);
}

/**
 * @desc   aktuelle Allianzen in Planetendaten übertragen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_spieler Bezeichner der Tabelle mit Spielerdaten
 * @global string $db_tb_scans   Bezeichner der Tabelle mit Planetendaten
 *
 * @param int     $iUpdateTime   optional Zeitpunkt der Allianzaktualisierung
 */
function SyncAllies($iUpdateTime)
{
    global $db, $db_tb_spieler, $db_tb_scans;

    $iUpdateTime = (int)$iUpdateTime;

    $sql = "UPDATE `{$db_tb_spieler}`, `{$db_tb_scans}`
            SET `{$db_tb_scans}`.`allianz` = `{$db_tb_spieler}`.`allianz`
            WHERE `{$db_tb_spieler}`.`name` = `{$db_tb_scans}`.`user`";

    if (!empty($iUpdateTime)) {
        $sql .= "AND `{$db_tb_spieler}`.playerupdate_time = {$iUpdateTime};";
    }

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'DB TransferAllytoScans Fehler!', '', __FILE__, __LINE__, $sql);

    deleteInvalidAlliances();
}

/**
 * @desc   Ungültige Allianzbezeichner von Planetendaten löschen denen kein user mehr zugeordnet ist
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db          Datenbankhandle
 * @global string $db_tb_scans Bezeichner der Tabelle mit Planetendaten
 */
function deleteInvalidAlliances()
{
    global $db, $db_tb_scans;

    $sql = "UPDATE `{$db_tb_scans}` SET `allianz` = '' WHERE `user` = '';";
    $result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'DB AllyDelete Fehler!', '', __FILE__, __LINE__, $sql);
}

/**
 * @desc   Bestimmung von Spielernamen aufgrund von Koordinaten
 *
 * @author Mac (MacXY@herr-der-mails.de)
 *
 * @global object $db          Datenbankhandle
 * @global string $db_tb_scans Bezeichner der Tabelle mit Planetendaten
 *
 * @param string  $strCoords   Koordinaten des Planeten
 *
 * @return string              Spielername
 */
function getNameByCoords($strCoords)
{
    global $db, $db_tb_scans;
    static $aCoordUsers;

    if (empty($strCoords)) {
        return '';
    }

    $strCoords = $db->escape($strCoords);

    if (!isset($aCoordUsers[$strCoords])) {

        $sql = "SELECT `user` FROM `{$db_tb_scans}` WHERE `coords` = '$strCoords';";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result);

        $aCoordUsers[$strCoords] = $row['user'];

    }

    return $aCoordUsers[$strCoords];
}

/**
 * @desc   Bestimmung von Allianznamen anhand des IW-Spielernamens
 *
 * @author Mac (MacXY@herr-der-mails.de)
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_spieler Bezeichner der Tabelle mit Spielerdaten
 *
 * @param string  $strUserName   Spielername
 *
 * @return string Allianz
 */
function getAllianceByUser($strUserName)
{
    global $db, $db_tb_spieler;
    static $aUserAlly;

    $strUserName = trim($strUserName);
    if (empty($strUserName)) {
        return false;
    }

    $strUserName = $db->escape($strUserName);

    if (!isset($aUserAlly[$strUserName])) {

        $sql = "SELECT `allianz` FROM `{$db_tb_spieler}` WHERE `name` = '$strUserName';";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query userally information.', '', __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result);

        $aUserAlly[$strUserName] = $row['allianz'];

    }

    return $aUserAlly[$strUserName];

}

/**
 * @desc   Allianz eines Spielers ggf. aktualisieren und aktelle zurückgeben
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_spieler Bezeichner der Tabelle mit Spielerdaten
 *
 * @param string  $strSpielerName
 * @param string  $strAlliance
 * @param int     $iTime
 *
 * @return string Allianz
 */
function updateUserAlliance($strSpielerName, $strAlliance, $iTime)
{
    global $db, $db_tb_spieler;

    if ($strSpielerName === '') {
        return '';
    }

    $strSpielerName = $db->escape($strSpielerName);
    $strAlliance = $db->escape($strAlliance);
    $iTime    = (int)$iTime;

    $sql = "SELECT `allianz`, `playerupdate_time` FROM `$db_tb_spieler` WHERE `name`='" . $strSpielerName . "';";
    $result_player = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query player information.', '', __FILE__, __LINE__, $sql);
    $row_player = $db->db_fetch_array($result_player);

    if (!empty($row_player)) {

        if ($iTime <= $row_player['playerupdate_time']) { //Übergebene informationen sind nicht neuer

            return $row_player['allianz'];

        } else { //Übergebene informationen sind aktueller -> bei Allianzänderung Allianz aktualisieren

            if ($row_player['allianz'] === $strAlliance) {

                $SQLdata = array(
                    'playerupdate_time' => $iTime
                );

                $db->db_update($db_tb_spieler, $SQLdata, "WHERE `name` = '{$strSpielerName}';")
                or error(GENERAL_ERROR, 'Could not update player information.', '', __FILE__, __LINE__, '');

            } else {

                $SQLdata = array(
                    'allianz'           => $strAlliance,
                    'allianzrang'       => null,
                    'exallianz'         => $row_player['allianz'],
                    'allychange_time'   => $iTime,
                    'playerupdate_time' => $iTime
                );

                $db->db_update($db_tb_spieler, $SQLdata, "WHERE `name` = '{$strSpielerName}';")
                or error(GENERAL_ERROR, 'Could not update player information.', '', __FILE__, __LINE__, '');

                //Allianzänderung in Historytabele übertragen
                AddAllychangetoHistory($iTime);

                //aktuelle Allianz in alle Kartendaten übertragen
                SyncAllies($iTime);

            }

            return $strAlliance;

        }

    } else { //neuer Spieler -> eintragen

        $SQLdata = array(

            'name'              => $strSpielerName,
            'allianz'           => $strAlliance,
            'playerupdate_time' => $iTime

        );

        $db->db_insert($db_tb_spieler, $SQLdata)
        or error(GENERAL_ERROR, 'Could not insert player information.', '', __FILE__, __LINE__, '');

        return $strAlliance;

    }
}

/**
 * @desc   Objekt auf einem Planeten anhand der Koordinaten bestimmen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @global object $db            Datenbankhandle
 * @global string $db_tb_spieler Bezeichner der Tabelle mit Spielerdaten
 *
 * @param string  $strCoords     Koordinaten des Planeten
 *
 * @return string Objekt auf dem Planeten
 */
function getObjectByCoords($strCoords)
{
    global $db, $db_tb_scans;
    static $aPlanetObjects;

    if (empty($strCoords)) {
        return '';
    }

    $strCoords = $db->escape($strCoords);

    if (!isset($aPlanetObjects[$strCoords])) {

        $sql = "SELECT `objekt` FROM `{$db_tb_scans}` WHERE `coords` = '$strCoords';";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result);

        $aPlanetObjects[$strCoords] = $row['objekt'];

    }

    return $aPlanetObjects[$strCoords];
}

/**
 * @desc   Objektbild des Objekts auf einem Planeten anhand der Koordinaten bestimmen
 *
 * @author masel (masel678@googlemail.com)
 *
 * @param string $strCoords Koordinaten des Planeten
 *
 * @return string html img Element
 */
function getObjectPictureByCoords($strCoords)
{
    $objekt        = getObjectByCoords($strCoords);
    $objectPicture = '';

    if ($objekt === 'Kolonie') {
        $objectPicture = "<img src='" . BILDER_PATH . "kolo.png' title='Kolonie'>";
    } else if ($objekt === 'Sammelbasis') {
        $objectPicture = "<img src='" . BILDER_PATH . "ress_basis.png' title='Sammelbasis'>";
    } else if ($objekt === 'Artefaktbasis') {
        $objectPicture = "<img src='" . BILDER_PATH . "artefakt_basis.png' title='Artefaktbasis'>";
    } else if ($objekt === 'Kampfbasis') {
        $objectPicture = "<img src='" . BILDER_PATH . "kampf_basis.png' title='Kampfbasis'>";
    }

    return $objectPicture;
}

function getLastBombByCoords($strCoords) {
    global $db, $db_tb_kb, $db_tb_kb_bomb;
    static $aPlanetBombs;

    if (empty($strCoords)) {
        return '';
    }

    $strCoords = $db->escape($strCoords);

    if (!isset($aPlanetBombs[$strCoords])) {

        $aPlanetBombs[$strCoords] = array();

        $coords = explode(":", $strCoords);
        $sql_bomb = "SELECT time AS bombtime, `ID_KB`, `hash` FROM `{$db_tb_kb}` WHERE ((`ID_KB` IN (SELECT `ID_KB` FROM `{$db_tb_kb_bomb}`)) AND (`{$db_tb_kb}`.`koords_gal`='".$coords[0]."' AND `{$db_tb_kb}`.`koords_sol`='".$coords[1]."' AND `{$db_tb_kb}`.`koords_pla`='".$coords[2]."')) ORDER BY bombtime DESC LIMIT 1";
        $result_bomb = $db->db_query($sql_bomb)
            or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql_bomb);
        $row_bomb = $db->db_fetch_array($result_bomb);
        if (!empty($row_bomb['ID_KB'])) {

            $aPlanetBombs[$strCoords]['time'] = $row_bomb['bombtime'];
            $aPlanetBombs[$strCoords]['link'] = "http://www.icewars.de/portal/kb/de/kb.php?id=" . $row_bomb['ID_KB'] . "&md_hash=" . $row_bomb['hash'];

        }

    }

    return $aPlanetBombs[$strCoords];
}


/**
 * @desc   bestimmt die IWDB-GebäudeId anhand des Gebäudenamens
 *
 * @author masel (masel678@googlemail.com)
 *
 * @param string $strBuildingName Gebäudename
 * @param bool   $insert_building optional true um noch nicht vorhandenes Gebäude hinzuzufügen
 *
 * @return bool|int
 * @throws Exception
 */
function getBuildingIdByName($strBuildingName, $insert_building = false) {
    global $db, $db_tb_gebaeude;

    if (empty($strBuildingName)) {
        throw new Exception('empty buildingname!');
    }

    $strBuildingName = $db->escape($strBuildingName);

    $sql_building = "SELECT `id` FROM `{$db_tb_gebaeude}` WHERE `name` = '$strBuildingName';";
    $result_building = $db->db_query($sql_building)
    or error(GENERAL_ERROR, 'Could not query building information!', '', __FILE__, __LINE__, $sql_building);
    $row_building = $db->db_fetch_array($result_building);

    if (!empty($row_building['id'])) {

        return (int)$row_building['id'];

    } else {
        if ($insert_building) {

            $SQLdata = array('name'	=> $strBuildingName);

            $result_insert = $db->db_insert($db_tb_gebaeude, $SQLdata)
            or error(GENERAL_ERROR, 'Could not insert building!', '', __FILE__, __LINE__);

            return $db->db_insert_id();

        } else {

            return false;

        }
    }
}

/**
 * @desc   bestimmt die IW-GebäudeId anhand des Gebäudenamens
 *
 * @author masel (masel678@googlemail.com)
 *
 * @param string $strBuildingName Gebäudename
 * @param bool   $insert_building optional true um noch nicht vorhandenes Gebäude hinzuzufügen
 *
 * @return int|null IW-GebäudeId
 * @throws Exception
 */
function getBuildingIWIdByName($strBuildingName, $insert_building = false) {
    global $db, $db_tb_gebaeude;

    if (empty($strBuildingName)) {
        throw new Exception('empty buildingname!');
    }

    $strBuildingName = $db->escape($strBuildingName);

    $sql_building = "SELECT `id`, `id_iw` FROM `{$db_tb_gebaeude}` WHERE `name` = '$strBuildingName';";
    $result_building = $db->db_query($sql_building)
    or error(GENERAL_ERROR, 'Could not query building information.', '', __FILE__, __LINE__, $sql_building);
    $row_building = $db->db_fetch_array($result_building);

    if (!empty($row_building['id'])) {

        return (int)$row_building['id_iw'];

    } else {
        if ($insert_building) {

            $SQLdata = array('name'	=> $strBuildingName);

            $result_insert = $db->db_insert($db_tb_gebaeude, $SQLdata)
            or error(GENERAL_ERROR, 'Could not insert building.', '', __FILE__, __LINE__);

        }

        return null;

    }
}

/**
 * @desc   bestimmt die IW-GebäudeId anhand der IWDB-GebäudeId
 *
 * @author masel (masel678@googlemail.com)
 *
 * @param int $iBuildingID IWDB-GebäudeId
 *
 * @return int|null IW-GebäudeId
 * @throws Exception
 */
function getBuildingIWIdByID($iBuildingID) {
    global $db, $db_tb_gebaeude;

    $iBuildingID = (int)$iBuildingID;
    if (empty($iBuildingID)) {
        throw new Exception('empty BuildingID!');
    }

    $sql_building = "SELECT `id_iw` FROM `{$db_tb_gebaeude}` WHERE `id` = $iBuildingID;";
    $result_building = $db->db_query($sql_building)
    or error(GENERAL_ERROR, 'Could not query building information.', '', __FILE__, __LINE__, $sql_building);
    $row_building = $db->db_fetch_array($result_building);

    if (!empty($row_building['id_iw'])) {

        return $row_building['id_iw'];

    } else {

        return null;

    }
}

/**
 * @desc   bestimmt die IWDB-GebäudeId anhand der IW-GebäudeId
 *
 * @author masel (masel678@googlemail.com)
 *
 * @param int $iBuildingIWID IW-GebäudeId
 *
 * @return int|null IWDB-GebäudeId
 * @throws Exception
 */
function getBuildingIDByIWId($iBuildingIWID) {
    global $db, $db_tb_gebaeude;

    $iBuildingIWID = (int)$iBuildingIWID;
    if (empty($iBuildingIWID)) {
        throw new Exception('empty BuildingID!');
    }

    $sql_building = "SELECT `id` FROM `{$db_tb_gebaeude}` WHERE `id_iw` = $iBuildingIWID;";
    $result_building = $db->db_query($sql_building)
    or error(GENERAL_ERROR, 'Could not query building information.', '', __FILE__, __LINE__, $sql_building);
    $row_building = $db->db_fetch_array($result_building);

    if (!empty($row_building['id'])) {

        return $row_building['id'];

    } else {

        return null;

    }
}

/**
 * @desc   bestimmt den Gebäudenamen anhand der IWDB-ID
 *
 * @author masel (masel678@googlemail.com)
 *
 * @param string $iBuildingID IWDB-GebäudeId
 *
 * @return string|bool  Gebäudenamen
 * @throws Exception
 */
function getBuildingNameByID($iBuildingID) {
    global $db, $db_tb_gebaeude;

    $iBuildingID = (int)$iBuildingID;
    if (empty($iBuildingID)) {
        throw new Exception('invalid building iwid!');
    }

    $sql_building = "SELECT `name` FROM `{$db_tb_gebaeude}` WHERE `id` = $iBuildingID;";
    $result_building = $db->db_query($sql_building)
    or error(GENERAL_ERROR, 'Could not query building information.', '', __FILE__, __LINE__, $sql_building);
    $row_building = $db->db_fetch_array($result_building);

    if (!empty($row_building['name'])) {

        return $row_building['name'];

    } else {

        return false;

    }
}

/**
 * @desc   bestimmt den Gebäudenamen anhand der IW-ID
 *
 * @author masel (masel678@googlemail.com)
 *
 * @param $iBuildingIWID
 *
 * @return bool
 * @throws Exception
 */
function getBuildingNameByIWID($iBuildingIWID) {
    global $db, $db_tb_gebaeude;

    $iBuildingIWID = (int)$iBuildingIWID;
    if (empty($iBuildingIWID)) {
        throw new Exception('invalid building iwid!');
    }

    $sql_building = "SELECT `name` FROM `{$db_tb_gebaeude}` WHERE `id_iw` = $iBuildingIWID;";
    $result_building = $db->db_query($sql_building)
    or error(GENERAL_ERROR, 'Could not query building information.', '', __FILE__, __LINE__, $sql_building);
    $row_building = $db->db_fetch_array($result_building);

    if (!empty($row_building['name'])) {

        return $row_building['name'];

    } else {

        return false;

    }
}

/**
 * @desc   erstellt html-Tabelle die die Gebäudedaten beinhaltet
 *
 * @author masel (masel678@googlemail.com)
 *
 * @param array $aBuildings
 *
 * @return string
 *
 * @todo   incomplete
 */
function makeBuildingTable($aBuildings) {

    $strBuildingTable = '';

    foreach ($aBuildings as $gebaeude) {

        if (!isset($scan_data['geb'])) {
            $strBuildingTable = "<table class='scan_table'>\n";
        }
        $strBuildingTable .= "<tr class='scan_row'>\n";
        $strBuildingTable .= "\t<td class='scan_object'>\n";
        $strBuildingTable .= $gebaeude->name;
        $strBuildingTable .= "\n\t</td>\n";
        $strBuildingTable .= "\t<td class='scan_value'>\n";
        $strBuildingTable .= $gebaeude->anzahl;
        $strBuildingTable .= "\n\t</td>\n</tr>\n";

    }

    if (isset($scan_data['geb'])) {
        $strBuildingTable .= "</table>\n";
    }

    return $strBuildingTable;

}

/**
 * @desc   bereinigt Produktionswert von Resstransfer
 *
 * @author masel (masel678@googlemail.com)
 *
 * @param float $fProductionValue unbereinigter Produktionswert
 *
 * @return float bereinigter Produktionswert
 */
function getRealProduction($fProductionValue)
{
    if (empty($fProductionValue)) {
        return 0;
    } elseif ($fProductionValue < 197000) { //kein Lagertransfer
        return $fProductionValue;
    } else {

        if ($fProductionValue < 1997000) { //Lagertransfer auf Kampfbasis Alpha o.ä.

            $iMinTransferSpeed = 200000;

        } elseif ($fProductionValue < 19997000) { //Lagertransfer auf Kampfbasis Beta o.ä.

            $iMinTransferSpeed = 2000000;

        } else { //Lagertransfer auf Kampfbasis Gamma

            $iMinTransferSpeed = 20000000;

        }

        $fTransferIncreaseStep = round(log($fProductionValue / $iMinTransferSpeed) / log(1.1));

        $fTransferSpeed        = pow(1.1, $fTransferIncreaseStep) * $iMinTransferSpeed;
        $fRealProductionValue  = $fProductionValue - $fTransferSpeed;

        if ($fRealProductionValue < -3000) { //Verbrauch kann nicht größer sein -> Transfergeschwindigkeit ist zu hoch ermittelt
            $fTransferSpeed       = pow(1.1, $fTransferIncreaseStep - 1) * $iMinTransferSpeed;
            $fRealProductionValue = $fProductionValue - $fTransferSpeed;
        }

        return $fRealProductionValue;

    }

}