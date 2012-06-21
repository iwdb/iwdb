<?php
/*****************************************************************************/
/* sid.php                                                                   */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* ========================================================================= */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* ========================================================================= */
/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */
/*****************************************************************************/
/* This program is free software; you can redistribute it and/or modify it   */
/* under the terms of the GNU General Public License as published by the     */
/* Free Software Foundation; either version 2 of the License, or (at your    */
/* option) any later version.                                                */
/*                                                                           */
/* This program is distributed in the hope that it will be useful, but       */
/* WITHOUT ANY WARRANTY; without even the implied warranty of                */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General */
/* Public License for more details.                                          */
/*                                                                           */
/* The GNU GPL can be found in LICENSE in this directory                     */
/*****************************************************************************/

if (!defined('IRA'))
    die('Hacking attempt...');

global $db, $db_tb_sid, $db_prefix, $db_tb_user;

// get user ip
$client_ip = $_SERVER['REMOTE_ADDR'];

if( isset($_SERVER['HTTP_X_FORWARDED_FOR']) )
{
    if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $_SERVER['HTTP_X_FORWARDED_FOR'], $ip_list) ) {
        $private_ip = array('/^0\./', '/^127\.0\.0\.1/', '/^192\.168\..*/', '/^172\.16\..*/', '/^10.\.*/', '/^224.\.*/', '/^240.\.*/');
        $client_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
    }
}
$user_ip = encode_ip($client_ip);
// get sid //
$sid = getVar('sid');

// delete old ips from sid table //
$sql = "DELETE FROM " . $db_tb_sid . " WHERE date<" . ( $config_date - $config_sid_timeout );
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

// get user id //
$user_id = '';

$sql = "SELECT id FROM " . $db_tb_sid . " WHERE ip='" . $user_ip . "'";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row_ip = $db->db_num_rows($result);

$sql = "SELECT id, sid FROM " . $db_tb_sid . " WHERE (ip='" . $user_ip . "' AND sid='" . $sid . "')";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row_sid = $db->db_fetch_array($result);

if ( isset($row_sid['id']) )
{
    $user_id = $row_sid['id'];
}
else {
    $sid = randomstring($config_sid_string, $config_sid_length);
    // cookie is ok -> login
    if ( isset($_COOKIE[$config_cookie_name]) )
        $cookie = $_COOKIE[$config_cookie_name];
    if ( !empty($cookie) )
    {
        list($cookie_id, $cookie_password) = explode(';', $cookie);
        //check_username, damit man keine fremden zeichen in cookie id und password übers cookie einfügen kann
        $alert = check_username($cookie_id);
        if(!empty($alert)) {
            die ($alert);
        }
        $alert = check_username($cookie_password);
        if(!empty($alert)) {
            die ($alert);
        }
        $sql = "SELECT id, password FROM " . $db_tb_user . " WHERE (id='" . $cookie_id . "' AND password='" . $cookie_password . "' AND password<>'')";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result);

        if ( isset($row['id']) )
        {
            $user_id = $row['id'];
            $sql = "UPDATE " . $db_tb_user . " SET logindate='" . $config_date . "' WHERE id='" . $row['id'] . "'";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }
    }
}

if ( ( $action == "memberlogin2" ) || ( $action == "memberlogout2" ) )
{
    // check given userdata //

    // Erst mal den Benutzernamen so holen, wie er ?bergeben wird. Dann
    // auf Gueltigkeit pruefen (und eventuell abkratzen).
    $login_id = mysql_real_escape_string(getVar('login_id', true));
    $alert = check_username($login_id);
    if(!empty($alert)) {
        die ($alert);
    }

    // Benutzerdaten noch mal holen, aber diesmal mit htmlentities encodiert.
    $login_id       = mysql_real_escape_string(getVar('login_id'));
    $login_password = getVar('login_password');
    $login_cookie   = getVar('login_cookie');

    // count wrong logins //
    $sql = "DELETE FROM " . $db_tb_wronglogin . " WHERE date<" . ( $config_date - $config_wronglogin_timeout );
    $result_u = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $sql = "SELECT ip FROM " . $db_tb_wronglogin . " WHERE user LIKE '" . $login_id . "'";
    $result_u = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $wronglogins = $db->db_num_rows($result_u);

    $ips = '';
    while($row_u = $db->db_fetch_array($result_u))
    {
        $ips .= $row_u['ip'] . "<br>\n";
    }

    $sql = "SELECT id, password FROM " . $db_tb_user;
    $sql .= " WHERE (id='" . $login_id . "'";
    $sql .= " AND password='" . md5($login_password) . "' AND password<>''";
    $sql .= ")";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    if ( ( isset($row['id']) ) && ( $wronglogins < $config_wronglogins ) )
    {
        $user_id = $row['id'];
        $sql = "UPDATE " . $db_tb_user . " SET logindate='" . $config_date . "' WHERE id='" . $row['id'] . "'";
        $result = $db->db_query($sql);
        // login with cookie if set //
        if ($login_cookie == 1)
        {
            #    		setcookie($config_cookie_name, $login_id . ';' . $login_password, ( $config_date + $config_cookie_timeout ), '', '', 0);
            setcookie($config_cookie_name,  $login_id . ';' . md5($login_password), ( $config_date + $config_cookie_timeout ), '', '', 0);
            ${$config_cookie_name} = $login_id . ';' . md5($login_password);
        }
        $login_ok = TRUE;
        $sql = "DELETE FROM " . $db_tb_wronglogin . " WHERE user = '" . $login_id . "'";
        $result_u = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    else
    {
        // update wrong login table //
        if  ( $login_id != "" )
        {
            $sql = "INSERT INTO " . $db_tb_wronglogin . " (user, date, ip) VALUES ('" . $login_id . "', '" . $config_date . "', '" . $client_ip . "')";
            $result_u = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            if ( empty($wronglogins) )
            {
                $wronglogins = 1;
            }
            else
            {
                ++$wronglogins;

                if ( $wronglogins == $config_wronglogins )
                {
                    $message = '
<html>
<head>
 <title>Login Error</title>
</head>
<body>
<font face=verdana,arial size=2><b>Login Error at ' . $config_url . '</b><br><br>
Username: ' . $login_id . '<br>
Date: ' . date("d.m.Y H:i") . '<br>
IPs:<br>' . $ips . $client_ip . '</font>
</table>
</body>
</html>';

                    $mail_head =
                        "MIME-Version: 1.0\r\n".
                            "Content-type: text/html; charset=iso-8859-1\r\n".
                            "To: " . $config_mailto_id . " <" . $config_mailto . ">\r\n";
                    "From: " . $config_mailname . " <" . $config_mailfrom . ">\r\n";

                    @mail($config_mailto, "Login Error at " . $config_url, $message, $mail_head);
                }
            }
        }

        $user_id = 'guest';
        $login_ok = FALSE;
        // delete cookie at logout or wrong userdata //
        if ( isset($HTTP_COOKIE_VARS[$config_cookie_name]) )
        {
            setcookie($config_cookie_name, '', $config_date, '', '', 0);
            ${$config_cookie_name} = '';
        }
    }
}

//Cookie leeren wenn Logout
if ($action == "memberlogout2") {
    setcookie($config_cookie_name, '', time()-3600, '', '', 0);
}

// not yet logged in //
if ( !$user_id )
{
    $user_id = 'guest';
}
if ( isset($row_sid['sid']) )
{
    // delete all entries with user ID but not SID
    $sql = "DELETE FROM " . $db_tb_sid . " WHERE (id<>'guest' AND id='" . $user_id . "' AND sid<>'" . $sid . "')";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $sql = "UPDATE " . $db_tb_sid . " SET sid='" . $sid . "', ip='" . $user_ip . "', date='" . $config_date . "', id='" . $user_id . "' WHERE sid='" . $row_sid['sid'] . "'";
}
else
{
    $sql = "DELETE FROM " . $db_tb_sid . " WHERE (id='guest' AND ip='" . $user_ip . "')";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    // SID unknwon -> create new //
    $sql = "SELECT sid FROM " . $db_tb_sid . " WHERE (id='" . $user_id . "' AND id<>'guest')";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row_id = $db->db_fetch_array($result);

    if ( isset($row_id['sid']) )
    {
        $sql = "UPDATE " . $db_tb_sid . " SET sid='" . $sid . "', ip='" . $user_ip . "', date='" . $config_date . "', id='" . $user_id . "' WHERE sid='" . $row_id['sid'] . "'";
    }
    else
    {
        $sql = "INSERT INTO " . $db_tb_sid . " (sid, ip, date, id) VALUES ('" . $sid . "','" . $user_ip . "', '" . $config_date . "', '" . $user_id . "')";
    }
}
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
// get user status //
$user_allianz="";
if ( $user_id <> 'guest' )
{
    $sql = "SELECT status, allianz, password, sitterlogin, sitterskin, rules, sitterpwd," .
        " sitten, planibilder, gebbilder, adminsitten, gebaeude, peitschen," .
        " gengebmod, genbauschleife, genmaurer, menu_default," .
        " gal_start, gal_end, sys_start, sys_end, buddlerfrom, fremdesitten, vonfremdesitten, uniprop" .
        " FROM " . $db_tb_user . " WHERE id='" . $user_id . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    $user_status = $row['status'];
    $user_allianz = $row['allianz'];
    $user_password = $row['password'];
    $user_sitterlogin = $row['sitterlogin'];
    $user_sitterskin = $row['sitterskin'];
    $user_planibilder = $row['planibilder'];
    $user_gebbilder = $row['gebbilder'];
    $user_adminsitten = $row['adminsitten'];
    $user_gebaeude = $row['gebaeude'];
    $user_peitschen = $row['peitschen'];
    $user_gengebmod = $row['gengebmod'];
    $user_genmaurer = $row['genmaurer'];
    $user_genbauschleife = $row['genbauschleife'];
    $user_sitterpwd = $row['sitterpwd'];
    $user_sitten = $row['sitten'];
    $user_rules = $row['rules'];
    $user_menu_default = $row['menu_default'];
    $user_gal_start = $row['gal_start'];
    $user_gal_end = $row['gal_end'];
    $user_sys_start = $row['sys_start'];
    $user_sys_end = $row['sys_end'];
    $user_buddlerfrom = $row['buddlerfrom'];
    $user_fremdesitten = $row['fremdesitten'];
    $user_vonfremdesitten = $row['vonfremdesitten'];
    $user_uniprop = $row['uniprop'];

} else {
    // fill in some variables, so that these variables ar not
    // unknown to the rest of the script
    $user_adminsitten = SITTEN_DISABLED;
    $user_password    = "";
    $user_sitten      = "0";
    $user_fremdesitten = "0";
    $user_vonfremdesitten = "0";
}
// set online counter //
$counter_guest = 0;
$counter_member = 0;
$online_member = '';
$sql = "SELECT id FROM " . $db_tb_sid . " WHERE date>" . ( $config_date - $config_counter_timeout );
if (!$user_fremdesitten)
{
    $sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_sid . ".id=" . $db_tb_user . ".id)='" . $user_allianz . "'";
}
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'General_error_query', '', __FILE__, __LINE__, $sql);
while ( $row = $db->db_fetch_array($result) )
{
    if ( $row['id'] == 'guest' )
    {
        $counter_guest++;
    }
    else
    {
        $counter_member++;
        $online_member .= ( empty($online_member) ) ? $row['id'] : ", " . $row['id'];
    }
}

global $sid;
?>