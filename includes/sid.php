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

// get user ip
$user_ip      = $_SERVER['REMOTE_ADDR'];
$user_ip_hash = sha1($user_ip);

// delete old sids from sid table //
$sql = "DELETE FROM `{$db_tb_sid}` WHERE `date`<" . (CURRENT_UNIX_TIME - $config_sid_timeout);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not delete old sids.', '', __FILE__, __LINE__, $sql);

$user_id  = false;
$login_ok = false;
$sid      = false;

$debug = false;
$debugmessage = 'User-IP: ' . $user_ip . ' ';
$debugmessage .= 'Cookiedaten: ' . print_r($_COOKIE, true) . '<br>';

if (isset($_COOKIE[$config_cookie_name])) {
    $sid = $db->escape($_COOKIE[$config_cookie_name]);

    $user_id = useSID($sid, $user_ip_hash);
}

if (!empty($action) AND (($action == "memberlogin2"))) {

    $login_id = $db->escape(getVar('login_id'));

    $returndata = loginUser($login_id, getVar('login_password'));

    if (!empty($returndata['id'])) {
        $user_id  = $returndata['id'];
        $login_ok = true;

        $sid = randomstring($config_sid_string, $config_sid_length);

        $SQLdata = array(
            'sid'  => $sid,
            'ip'   => $user_ip_hash,
            'date' => CURRENT_UNIX_TIME,
            'id'   => $user_id
        );
        $db->db_insertupdate($db_tb_sid, $SQLdata)
            or error(GENERAL_ERROR, 'Could not insert sid!', '', __FILE__, __LINE__);

        if (getVar('login_cookie')) {
            $result = setcookie($config_cookie_name, $sid, (CURRENT_UNIX_TIME + $config_cookie_timeout), null, null, false, true);
            if (!$result) {
                exit('Setzen des permanenten Cookies fehlgeschlagen!');
            } elseif ($debug) {
                $debugmessage .= 'Permanentes Cookie gesetzt.<br>';
            }
        } else {
            $result = setcookie($config_cookie_name, $sid, 0, null, null, false, true);
            if (!$result) {
                exit('Setzen des temporären Cookies fehlgeschlagen!');
            } elseif ($debug) {
                $debugmessage .= 'Temporäres Cookie gesetzt.<br>';
            }
        }

    } else {
        $user_id = false;

        $wronglogins = $returndata['wronglogins'];

        // update wrong login table //
        $SQLdata = array(
            'user' => $login_id,
            'date' => CURRENT_UNIX_TIME,
            'ip'   => $user_ip
        );
        $db->db_insert($db_tb_wronglogin, $SQLdata)
            or error(GENERAL_ERROR, 'Could not update wrong login information.', '', __FILE__, __LINE__);

        if ($wronglogins == $config_wronglogins) {
            $ips = '';
            $sql = "SELECT `ip` FROM `{$db_tb_wronglogin}` WHERE `user` LIKE '" . $login_id . "';";
            $result_u = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            $wronglogins = $db->db_num_rows($result_u);
            while ($row_u = $db->db_fetch_array($result_u)) {
                $ips .= $row_u['ip'] . "\n";
            }

            $message = "Anzahl maximaler falscher Logins bei $login_id erreicht!\n";
            $message .= "Login-IPs:\n";
            $message .= $ips;

            if (!mail($config_mailto, "Login Error at " . $config_allytitle, $message)) {
                echo "<div class='system_warning'>Fehler beim Mailverschicken</div>";
            }
        }
    }
}

if ($user_id === false) {
    // not yet logged in lets sleep a bit ^^
    sleep(1);
} else {
    $login_ok = true;
}

//Cookie löschen wenn Logout oder falsche Anmeldedaten
if ((!empty($action) AND ($action === "memberlogout2")) OR ($login_ok === false)) {
    if (isset($_COOKIE[$config_cookie_name])) {
        setcookie($config_cookie_name, '', 1, null, null, false, true);
    }
}

// get user status //
if ($login_ok) {
    $sql = "SELECT status, allianz, password, sitterlogin, sitterskin, rules, sitterpwd," .
        " sitten, planibilder, gebbilder, adminsitten, gebaeude, peitschen," .
        " gengebmod, genbauschleife, genmaurer, menu_default," .
        " gal_start, gal_end, sys_start, sys_end, buddlerfrom, fremdesitten, vonfremdesitten, uniprop" .
        " FROM " . $db_tb_user . " WHERE id='" . $user_id . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    $user_status          = $row['status'];
    $user_allianz         = $row['allianz'];
    $user_password        = $row['password'];
    $user_sitterlogin     = $row['sitterlogin'];
    $user_sitterskin      = $row['sitterskin'];
    $user_planibilder     = $row['planibilder'];
    $user_gebbilder       = $row['gebbilder'];
    $user_adminsitten     = $row['adminsitten'];
    $user_gebaeude        = $row['gebaeude'];
    $user_peitschen       = $row['peitschen'];
    $user_gengebmod       = $row['gengebmod'];
    $user_genmaurer       = $row['genmaurer'];
    $user_genbauschleife  = $row['genbauschleife'];
    $user_sitterpwd       = $row['sitterpwd'];
    $user_sitten          = $row['sitten'];
    $user_rules           = $row['rules'];
    $user_menu_default    = $row['menu_default'];
    $user_gal_start       = $row['gal_start'];
    $user_gal_end         = $row['gal_end'];
    $user_sys_start       = $row['sys_start'];
    $user_sys_end         = $row['sys_end'];
    $user_buddlerfrom     = $row['buddlerfrom'];
    $user_fremdesitten    = $row['fremdesitten'];
    $user_vonfremdesitten = $row['vonfremdesitten'];
    $user_uniprop         = $row['uniprop'];

} else {
    // fill in some variables, so that these variables ar not
    // unknown to the rest of the script
    $user_adminsitten     = SITTEN_DISABLED;
    $user_password        = "";
    $user_sitten          = "0";
    $user_fremdesitten    = "0";
    $user_vonfremdesitten = "0";
    $user_allianz         = "";
}

if (($debug) AND $action !== 'memberlogout2') {
    if (strpos($_SERVER['SCRIPT_NAME'], 'index.php')) {
        echo "<span style='color:#DDDDDD;'>".$debugmessage."</span";
    }
}
unset($debug);
unset($debugmessage);

//sid mit dieser ip gültig?
function useSID($sid, $ip_hash)
{
    global $db, $db_tb_sid, $debugmessage;

    $sql = "SELECT `id` FROM `{$db_tb_sid}` WHERE `ip`='" . $ip_hash . "' AND `sid`='" . $sid . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row_sid = $db->db_fetch_array($result);

    if (!empty($row_sid['id'])) {
        //Cookiedaten sind gültig -> Zeit der letzten DB Nutzung aktualisieren
        $db->db_update($db_tb_sid, array('date' => CURRENT_UNIX_TIME), "WHERE `id`='".$row_sid['id']."'")
            or error(GENERAL_ERROR, 'Could not update sid!', '', __FILE__, __LINE__);

        return $row_sid['id'];
    } else {
        //Cookiedaten ungültig

        $sql = "SELECT `id`, `ip` FROM `{$db_tb_sid}` WHERE `sid`='" . $sid . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row_sid = $db->db_fetch_array($result);
        if (!empty($row_sid['id'])) {
            $debugmessage .= 'sid vorhanden aber mit anderer ip: '.$row_sid['ip'].' <-> '.$ip_hash.'<br>';
        } else {
            $sql = "SELECT `sid` FROM `{$db_tb_sid}` WHERE `ip`='" . $ip_hash . "'";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            $row_sid = $db->db_fetch_array($result);
            if (!empty($row_sid['sid'])) {
                $debugmessage .= 'Eintrag mit der ip vorhanden aber mit anderer sessionid? '.$row_sid['sid'].' <-> '.$sid.'<br>';
            }
        }

        return false;
    }
}

//User mit den Daten versuchen einzuloggen
function loginUser($login_id, $password)
{
    global $db, $db_tb_wronglogin, $config_wronglogin_timeout, $db_tb_user, $config_wronglogins;

    $password_hash = md5($password);

    $returnData = array();

    // zu alte falsche Logins löschen
    $sql = "DELETE FROM `{$db_tb_wronglogin}` WHERE `date`<" . (CURRENT_UNIX_TIME - $config_wronglogin_timeout);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not delete wrong login information.', '', __FILE__, __LINE__, $sql);

    // Anzahl der falschen Logins des Nutzers holen
    $sql = "SELECT COUNT(*) AS 'wronglogins' FROM `{$db_tb_wronglogin}` WHERE `user` LIKE '" . $login_id . "';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row         = $db->db_fetch_array($result);
    $wronglogins = $row['wronglogins'];

    $sql = "SELECT `id`, `password` FROM " . $db_tb_user;
    $sql .= " WHERE (`id`='" . $login_id . "'";
    $sql .= " AND `password`='" . $password_hash . "' AND `password`<>''";
    $sql .= ")";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query user information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    if ((!empty($row['id'])) AND ($wronglogins < $config_wronglogins)) {
        $returnData['id'] = $row['id'];

        //Einlogzeit aktualisieren
        $db->db_update($db_tb_user, array('logindate' => CURRENT_UNIX_TIME), "WHERE `id`='" . $row['id'] . "'")
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

        //falsche Logins löschen
        $sql = "DELETE FROM `{$db_tb_wronglogin}` WHERE `user` = '" . $login_id . "';";
        $result_u = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    } else {
        $returnData['wronglogins'] = $wronglogins + 1;
    }

    return $returnData;
}