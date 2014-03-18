<?php

function do_sitterlogin($strSitterlogin)
{
    global $user_adminsitten, $user_sitterlogin;
    global $db, $db_tb_sitterlog, $db_tb_user;
    global $config_sitterlog_timeout, $config_sitterpunkte_timeout, $config_sitterpunkte_login;

    if ((!empty($strSitterlogin)) AND (($user_adminsitten == SITTEN_BOTH) OR ($user_adminsitten == SITTEN_ONLY_LOGINS))) {
        $strSitterlogin = $db->escape($strSitterlogin);

        $sql    = "DELETE FROM " . $db_tb_sitterlog . " WHERE date<" . (CURRENT_UNIX_TIME - $config_sitterlog_timeout);
        $db->db_query($sql);

        $sql    = "SELECT id FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $strSitterlogin . "' AND fromuser = '" . $user_sitterlogin . "' AND action = 'login' AND date > " . (CURRENT_UNIX_TIME - $config_sitterpunkte_timeout);
        $result = $db->db_query($sql);
        $anz    = $db->db_num_rows($result);

        $sql    = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin, fromuser, date, action) VALUES ('" . $strSitterlogin . "', '" . $user_sitterlogin . "', '" . CURRENT_UNIX_TIME . "', 'login')";
        $db->db_query($sql);

        // User
        $sql    = "UPDATE " . $db_tb_user . " SET lastsitterloggedin=0 WHERE lastsitteruser='" . $user_sitterlogin . "'";
        $db->db_query($sql);
        $sql    = "UPDATE " . $db_tb_user . " SET lastsitterlogin=" . CURRENT_UNIX_TIME . ",lastsitteruser='" . $user_sitterlogin . "',lastsitterloggedin=1 WHERE id='" . $strSitterlogin . "'";
        $db->db_query($sql);

        if (($strSitterlogin != $user_sitterlogin) && ($anz == 0)) {
            $sql    = "UPDATE " . $db_tb_user . " SET sitterpunkte = sitterpunkte + " . $config_sitterpunkte_login . " WHERE sitterlogin = '" . $user_sitterlogin . "'";
            $db->db_query($sql);
        }

        $sql    = "SELECT sitterpwd FROM " . $db_tb_user . " WHERE sitterlogin = '" . $strSitterlogin . "'";
        $result = $db->db_query($sql);
        $row    = $db->db_fetch_array($result);
        if (!empty($row['sitterpwd'])) {
            $redirectLocation = "http://icewars.de/index.php?action=login&name=" . urlencode($strSitterlogin) . "&pswd=" . $row['sitterpwd'] . "&sitter=1&ismd5=1&submit=true";
            if (!empty($user_sitterskin)) {
                $redirectLocation .= "&serverskin=1&serverskin_typ=" . $user_sitterskin;
            }
            if (!empty($user_allow_ip_change)) {
                $redirectLocation .= "&ip_change=1";
            }

            header("Location: " . $redirectLocation);
        }
        exit;
    }
}