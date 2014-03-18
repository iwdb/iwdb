<?php
/*****************************************************************************
 * index.php                                                                 *
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
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

define('APPLICATION_PATH_ABSOLUTE', dirname(__FILE__));
define('APPLICATION_PATH_RELATIVE', dirname($_SERVER['SCRIPT_NAME']));
define('APPLICATION_PATH_URL', dirname($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']));

require_once("./includes/bootstrap.php");

if ($user_gesperrt) {
    die ('<div style="text-align:center;color:red">Dein Account ist gesperrt worden!</div>');
}

if ($login_ok) {
    $IwdbLock = isIwdbLocked();

    if ($IwdbLock) {
        if ($user_status !== 'admin') {

            echo "<div style='text-align:center;color:red'>Die Datenbank ist zur Zeit gesperrt!</div>";
            if (is_string($IwdbLock)) {
                echo "<div style='text-align:center;color:red'>Grund: $IwdbLock</div>";
            }
            exit;

        } else {

            echo "<div style='text-align:center;color:red'>Die Datenbank ist zur Zeit gesperrt!</div>";
            if (is_string($IwdbLock)) {
                echo "<div style='text-align:center;color:red'>Grund: $IwdbLock</div>";
            }

        }
    }
}

// User hat sich ausgeloggt
if ($action === 'memberlogout2') {
    header("Location: " . APPLICATION_PATH_RELATIVE);
    exit;
}

// User hat Regeln akzeptiert
if (($action === 'rules') AND (getVar('accept_rules')) AND ($login_ok)) {
    $user_rules = "1";

    $result = $db->db_update($db_tb_user, array('rules' => 1), "WHERE id='$user_id'");

    $action = $config_default_action;
} elseif ($action === 'memberlogin2') {
    $action = $config_default_action;
}

// Sitterlogin in einen Account
$sitterlogin = $db->escape(getVar('sitterlogin'));
if (($action === "sitterlogins") AND $login_ok) {
    include("modules/do_sitterlogin.php");
    do_sitterlogin($sitterlogin);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title><?php echo $config_allytitle ?></title>
    <?php
    $SERVERURI = "index.php?action=" . $action;

    if (($action == "sitterlogins") || ($action == "sitterliste")) {
        if (($user_adminsitten == SITTEN_BOTH) || ($user_adminsitten == SITTEN_ONLY_LOGINS)) {
            echo "<meta http-equiv='refresh' content='" . $config_refresh_timeout . "; URL=" . $SERVERURI . "'>";
        }
    }
    ?>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/theme.blue.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
    <script src="javascript/respond.min.js"></script>
    <script src="javascript/jquery-1.11.0.min.js"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
    <script src="javascript/jquery-2.1.0.min.js"></script>
    <!--<![endif]-->
</head>
<?php
if (!getVar("nobody")) {
?>
<body class="body background">
<div align="center">
    <table class="seite">
        <tr>
            <td style="text-align: center;" class="background">
                <?php
                if (!empty($config_banner)) {
                    echo "<div id='iwdb_logo'><img src={$config_banner} alt='banner' style='vertical-align: middle;'></div>";
                }

                }

                if ( ($login_ok) && ($user_rules === "1") ) {

                    //hier hin verschoben da der IE iwie imemr sonst Mist baut ^^
                    include ('./includes/sitterfadein.php');

                    if ($action === "profile") {
                        // Menue-Änderung voraus?
                        $newmenustyle = getVar("menu_default");
                        if ((!empty($newmenustyle)) && ($newmenustyle != $user_menu_default)) {
                            $user_menu_default = $newmenustyle;
                        }
                    }

                    if (empty($user_menu_default)) {
                        $user_menu_default = "default";
                    }

                    if (!getVar("nobody")) {
                        include "./menustyles/menu_" . $user_menu_default . ".php";
                    }
                } else {
                ?>
                <table width="95%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="windowbg1" style="padding-left: 0; text-align: center;">
                            <?php
                            }

                            if ((empty($user_sitterpwd)) && ($user_sitten == "1")) {
                                echo "<br><div class='system_notification'><b>*moep* Achtung! Du hast zwar anderen das Sitten erlaubt, aber kein Sitterpasswort eingetragen.</b></div><br><br>";
                            }

                            if (($login_ok) AND ($user_rules === "1")) {

                                if ($action === 'deluser') {
                                    include("modules/delete_user.php");
                                    delete_user($sitterlogin);
                                } elseif (file_exists("modules/" . $action . ".php") === true) {
                                    include("modules/" . $action . ".php");
                                }

                            } elseif (($login_ok) AND ($user_rules != "1")) {
                                include("modules/rules.php");
                                exit;
                            } else {
                                if ($action === 'password') {
                                    include("modules/password.php");
                                } else {
                                    include("modules/login.php");
                                }
                            }
                            echo $error;
                            if (!getVar("nobody")) { ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br>
    </td>
    </tr>
    </table>
</div>
<script src="javascript/jquery.tablesorter.min.js"></script>
<script src="javascript/jquery.tablesorter.widgets.min.js"></script>
<script src="javascript/validatr.min.js"></script>
<script src="javascript/misc.js"></script>
</body>
</html>
<?php } ?>