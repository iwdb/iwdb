<?php
/*****************************************************************************
 * sid.php                                                                   *
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
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum/Repo wenden:                                              *
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

global $db, $db_tb_sid, $db_tb_user, $db_tb_wronglogin;

$strUserIpHash = sha1(REMOTE_IP);

//get hash of user agent string (limited to 100 chars)
$userAgentHash = sha1(mb_substr(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH), 0, 100));

// delete old sids from sid table
$sql = "DELETE FROM `{$db_tb_sid}` WHERE `date`<" . (CURRENT_UNIX_TIME - $config_sid_timeout);
$result = $db->db_query($sql);

$user_id  = false;
$login_ok = false;
$sid      = false;

if (isset($_COOKIE[$config_cookie_name])) {
    $sid = $db->escape($_COOKIE[$config_cookie_name]);

    $user_id = useSID($sid, $strUserIpHash, $userAgentHash);
}

if ($user_id === false) { //keine gültige Session vorhanden
    $login_id       = $db->escape(getVar('login_id'));
    $login_password = $db->escape(getVar('login_password'));
    $login_cookie   = (bool)getVar('login_cookie');

    if ((!empty($action)) AND (($action === "memberlogin2")) AND (!empty($login_id) AND (!empty($login_password)))) {

        $returndata = loginUser($login_id, $login_password);

        if (!empty($returndata['id'])) { //Logindaten korrekt
            $user_id = $returndata['id'];

            //SessionID erzeugen und in der DB speichern
            $sid = getRandomString($length = 48, 'hex');

            $SQLdata = array(
                'sid'           => $sid,
                'userAgentHash' => $userAgentHash,
                'date'          => CURRENT_UNIX_TIME,
                'id'            => $user_id
            );

            //ip change not allowed -> save ip hash in sessiondata
            if (empty($returndata['allow_ip_change'])) {
                $SQLdata['ipHash'] = $strUserIpHash;
            }

            $db->db_insertupdate($db_tb_sid, $SQLdata);

            //SessionID im Cookie speichern
            if ($login_cookie) {
                $result = setcookie($config_cookie_name, $sid, (CURRENT_UNIX_TIME + $config_cookie_timeout), null, null, false, true);
                if (!$result) {
                    exit('Setzen des permanenten Cookies fehlgeschlagen!');
                }
            } else {
                $result = setcookie($config_cookie_name, $sid, 0, null, null, false, true);
                if (!$result) {
                    exit('Setzen des temporären Cookies fehlgeschlagen!');
                }
            }

        } else { //Logindaten falsch
            $user_id = false;

            $wronglogins = $returndata['wronglogins'];

            // Eintrag in die Tabelle der falschen Logins
            $SQLdata = array(
                'user' => $login_id,
                'date' => CURRENT_UNIX_TIME,
                'ip'   => REMOTE_IP
            );
            $db->db_insert($db_tb_wronglogin, $SQLdata);

            if ($wronglogins === $config_wronglogins) {
                $ips = '';
                $sql = "SELECT `ip` FROM `{$db_tb_wronglogin}` WHERE `user` LIKE '" . $login_id . "';";
                $result_u = $db->db_query($sql);
                $wronglogins = $db->db_num_rows($result_u);
                while ($row_u = $db->db_fetch_array($result_u)) {
                    $ips .= $row_u['ip'] . "\n";
                }

                $subject = "Login Error at " . $config_allytitle;
                $message = "Anzahl maximaler falscher Logins bei '" . htmlspecialchars($login_id, ENT_QUOTES, 'UTF-8') . "' erreicht!\n";
                $message .= "Login-IPs:\n";
                $message .= $ips;

                if (!mail($config_mailto, $subject, $message)) {
                    echo "<div class='system_warning'>Fehler beim Mailverschicken</div>";
                }
            }
        }
    }
}

if ($user_id === false) {
    // not yet logged in and inputdata given lets sleep a bit ^^
    if (!empty($_REQUEST)) {
        sleep(1);
    }
} else {
    $login_ok = true;
}

//Cookie löschen wenn Logout oder falsche Anmeldedaten
if ((!empty($action) AND ($action === "memberlogout2")) OR ($login_ok === false)) {
    if (isset($_COOKIE[$config_cookie_name])) {
        setcookie($config_cookie_name, '', 1, null, null, false, true);
    }
}

// fill in some variables, so that these variables ar not
// unknown to the rest of the script
$user_adminsitten     = SITTEN_DISABLED;
$user_sitten          = "0";
$user_fremdesitten    = "0";
$user_vonfremdesitten = "0";
$user_allianz         = "";
$user_rules           = "0";
$user_gesperrt        = false;

// get user status
if ($login_ok) {
    $sql = "SELECT `status`, `gesperrt`, `allianz`, `allow_ip_change`, `sitterlogin`, `sitterskin`, `rules`, `sitterpwd`," .
        " `sitten`, `planibilder`, `gebbilder`, `adminsitten`, `gebaeude`, `peitschen`," .
        " `gengebmod`, `genbauschleife`, `genmaurer`, `menu_default`," .
        " `gal_start`, `gal_end`, `sys_start`, `sys_end`, `buddlerfrom`, `fremdesitten`, `vonfremdesitten`, `uniprop`" .
        " FROM " . $db_tb_user . " WHERE `id`='" . $user_id . "';";
    $result = $db->db_query($sql);
    $row = $db->db_fetch_array($result);

    $user_gesperrt = !empty($row['gesperrt']);

    if ($user_gesperrt === false) {
        $user_status          = $row['status'];
        $user_allianz         = $row['allianz'];
        $user_allow_ip_change = !empty($row['allow_ip_change']);
        $user_sitterlogin     = $row['sitterlogin'];
        $user_sitterskin      = $row['sitterskin'];
        $user_rules           = $row['rules'];
        $user_sitterpwd       = $row['sitterpwd'];
        $user_sitten          = $row['sitten'];
        $user_planibilder     = $row['planibilder'];
        $user_gebbilder       = $row['gebbilder'];
        $user_adminsitten     = $row['adminsitten'];
        $user_gebaeude        = $row['gebaeude'];
        $user_peitschen       = $row['peitschen'];
        $user_gengebmod       = $row['gengebmod'];
        $user_genbauschleife  = $row['genbauschleife'];
        $user_genmaurer       = $row['genmaurer'];
        $user_menu_default    = $row['menu_default'];
        $user_gal_start       = $row['gal_start'];
        $user_gal_end         = $row['gal_end'];
        $user_sys_start       = $row['sys_start'];
        $user_sys_end         = $row['sys_end'];
        $user_buddlerfrom     = $row['buddlerfrom'];
        $user_fremdesitten    = $row['fremdesitten'];
        $user_vonfremdesitten = $row['vonfremdesitten'];
        $user_uniprop         = $row['uniprop'];
    }

}

//sid mit dieser ip gültig?
function useSID($sid, $ipHash, $userAgentHash)
{
    global $db, $db_tb_sid;

    $sql = "SELECT `id` FROM `{$db_tb_sid}` WHERE `sid`='" . $sid . "' AND (`ipHash` IS NULL OR `ipHash`='" . $ipHash . "') AND `userAgentHash` = '" . $userAgentHash . "';";
    $result = $db->db_query($sql);
    $row_sid = $db->db_fetch_array($result);

    if (!empty($row_sid['id'])) {

        //Cookiedaten sind gültig -> Zeit der letzten DB Nutzung aktualisieren
        $db->db_update($db_tb_sid, array('date' => CURRENT_UNIX_TIME), "WHERE `id`='" . $row_sid['id'] . "'");

        return $row_sid['id'];

    } else {

        //Cookiedaten ungültig
        return false;

    }
}

// User mit den Daten versuchen einzuloggen
function loginUser($login_id, $password)
{
    global $db, $db_tb_wronglogin, $config_wronglogin_timeout, $db_tb_user, $config_wronglogins;

    $password_hash = md5($password);

    $returnData = array();

    // zu alte falsche Logins löschen
    $sql = "DELETE FROM `{$db_tb_wronglogin}` WHERE `date`<" . (CURRENT_UNIX_TIME - $config_wronglogin_timeout);
    $db->db_query($sql);

    // Anzahl der falschen Logins des Nutzers holen
    $sql = "SELECT COUNT(*) AS 'wronglogins' FROM `{$db_tb_wronglogin}` WHERE `user` LIKE '" . $login_id . "';";
    $result = $db->db_query($sql);
    $row = $db->db_fetch_array($result);
    $wronglogins = $row['wronglogins'];

    $sql = "SELECT `id`, `allow_ip_change` FROM " . $db_tb_user;
    $sql .= " WHERE (`id`='" . $login_id . "'";
    $sql .= " AND `password`='" . $password_hash . "' AND `password`<>''";
    $sql .= ");";
    $result = $db->db_query($sql);
    $row = $db->db_fetch_array($result);
    if ((!empty($row['id'])) AND ($wronglogins < $config_wronglogins)) {
        $returnData['id']              = $row['id'];
        $returnData['allow_ip_change'] = $row['allow_ip_change'];

        //Einlogzeit aktualisieren
        $db->db_update($db_tb_user, array('logindate' => CURRENT_UNIX_TIME), "WHERE `id`='" . $row['id'] . "'");

        //falsche Logins löschen
        $sql = "DELETE FROM `{$db_tb_wronglogin}` WHERE `user` = '" . $login_id . "';";
        $result_u = $db->db_query($sql);

    } else {
        $returnData['wronglogins'] = $wronglogins + 1;
    }

    return $returnData;
}