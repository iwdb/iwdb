<?php

define('APPLICATION_PATH_ABSOLUTE', dirname(__FILE__));
define('APPLICATION_PATH_RELATIVE', dirname($_SERVER['SCRIPT_NAME']));
define('APPLICATION_PATH_URL', dirname($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']));

require_once("includes/bootstrap.php");

if (empty($sid) || empty($user_sitterlogin) || !($user_adminsitten == SITTEN_BOTH || $user_adminsitten == SITTEN_ONLY_LOGINS) || $user_id == "guest") {
    header("Location: ".APPLICATION_PATH_RELATIVE);
    exit;
}

// Get sitterprofile
$serverskin = 1;
$serverskin_typ = 3;

$status = array(
    'use'    => 1,
    'attack' => 2,
    'probe'  => 3,
    'past'   => 4,
);

$allianz = getVar("allianz");
if (!$user_fremdesitten) {
    $allianz = $user_allianz;
}

// Get avaible sitter logins
$sql = "SELECT * FROM " . $db_tb_user . " where sitten=1";
if (!empty($allianz)) {
    $sql .= " and allianz='" . $allianz . "'";
}
$result = $db->db_query($sql)
    or die('Could not query user.');
while ($row = $db->db_fetch_array($result)) {
    unset($user);

    $user['id']              = $row['id'];
    $user['typ']             = $row['budflesol'];
    $user['lastsitterlogin'] = $row['lastsitterlogin'];
    $user['lastsitteruser']  = $row['lastsitteruser'];
    if ($row['lastsitterloggedin'] && $row['lastsitterlogin'] > (CURRENT_UNIX_TIME - 5 * MINUTE)) {
        $user['lastsitterloggedin'] = 1;
        $user['next_status']        = $status['use'];
    } else {
        $user['lastsitterloggedin'] = 0;
        $user['next_status']        = $status['past'];
    }
    $user['ikea']            = $row['ikea'];
    $user['peitschen']       = $row['peitschen'];
    $user['alliance']        = empty($row['allianz']) ? "No Alliance" : $row['allianz'];
    $user['group']           = $row['buddlerfrom'];
    $user['dauersitten']     = $row['dauersitten'];
    $user['dauersittentext'] = $row['dauersittentext'];
    $user['dauersittenlast'] = $row['dauersittenlast'];
    if (!empty($user['dauersitten']) && (empty($user['dauersittenlast']) || ($user['dauersittenlast'] + $user['dauersitten'] < CURRENT_UNIX_TIME))) {
        $user['dauersittendue'] = true;
    } else {
        $user['dauersittendue'] = false;
    }

    $url = 'http://icewars.de/index.php?action=login&name=' . $row['id'];
    $url .= '&pswd=' . $row['sitterpwd'];
    if (!empty($serverskin)) {
        $url .= '&serverskin=1';
        $url .= '&serverskin_typ=' . $serverskin_typ;
    }
    $url .= '&sitter=1&ismd5=1&submit=1';
    $user['url'] = $url;
    /*
        $sql = "SELECT * FROM " . $db_tb_sitterauftrag . " WHERE user='" . $row['id'] . "' ORDER BY date DESC";
        $result_sitterorder = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        if ($row_sitterorder = $db->db_fetch_array($result_sitterorder)) {
            //$user['next_date'] = $row_sitterorder['date'];
            //$user['next_status'] = $user['next_date'] < CURRENT_UNIX_TIME ? 'due' : 'pending';
            $user['sitterorder']['planet'] = $row_sitterorder['planet'];
            if ($row_sitterorder['typ'] == 'Gebaeude') {
                $sql = "SELECT * FROM " . $db_tb_gebaeude . " WHERE id=" . $row_sitterorder['bauid'];
                $result_gebaeude = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
                if ($row_gebaeude = $db->db_fetch_array($result_gebaeude)) {
                    $user['sitterorder']['image'] = $row_gebaeude['bild'];
                    $user['sitterorder']['text'] = $row_gebaeude['name'];
                } else
                    $user['sitterorder']['text'] = '(unknown building)';
            } elseif ($row_sitterorder['typ'] == 'Sonstiges') {
                $user['sitterorder']['text'] = $row_sitterorder['auftrag'];
            } else
                $user['sitterorder']['text'] = 'Sitten';
        }
    */
    $sql = "SELECT * FROM " . $db_tb_lieferung . " WHERE user_to='" . $row['id'] . "' AND art IN ('Angriff','Sondierung','Sondierung (Schiffe/Def/Ress)','Sondierung (Gebäude/Ress)') AND time>" . (CURRENT_UNIX_TIME - (15 * MINUTE)) . " ORDER BY time DESC";
    $result_angriff = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row_angriff = $db->db_fetch_array($result_angriff)) {
        if ($row_angriff['time'] > (CURRENT_UNIX_TIME - ($row_angriff['art'] == 'Angriff' ? (15 * MINUTE) : (5 * MINUTE)))) {
            $key          = $row_angriff['art'] == 'Angriff' ? 'attack' : 'probe';
            $user[$key][] = array(
                'coords' => $row_angriff['coords_to_gal'] . ':' . $row_angriff['coords_to_sys'] . ':' . $row_angriff['coords_to_planet'],
                'time'   => $row_angriff['time'],
                'from'   => $row_angriff['user_from'],
            );
            if (!isset($user['next_date']) || $user['next_date'] > ($row_angriff['time'] + (15 * MINUTE))) {
                $user['next_date'] = $row_angriff['time'];
            }
            if ($user['next_status'] > $status[$key]) {
                $user['next_status'] = $status[$key];
            }
        }
    }

    if (!isset($user['next_date'])) {
        $user['next_date'] = $user['lastsitterlogin'];
    }

    if ($user['next_date'] > 0) {
        $user['next_date_text'] = strftime($config_sitter_timeformat, $user['next_date']);
    }

    $users[$row['id']]                                             = $user;
    $uview[$user['next_status']][$user['next_date'] . $user['id']] = $user;
}

// Assemble view
$view = array();
foreach ($status as $key) {
    if (isset($uview[$key])) {
        ksort($uview[$key]);
        $view = array_merge($view, $uview[$key]);
    }
}

// Get request parameter
if (isset($_REQUEST['login'])) {
    $login = $_REQUEST['login'];
}

if (isset($_REQUEST['mode'])) {
    $mode = $_REQUEST['mode'];
}

if (empty($mode)) {
    $mode = 'index';
}

$action = null;
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
}

if ($action == 'own') {
    $login = $user_sitterlogin;
}

if (isset($_REQUEST['redirect'])) {
    $redirect = $_REQUEST['redirect'];
}

$logout = getVar('logout');
if ($logout == 'Ausloggen') {
    foreach ($users as $user) {
        if ($user['lastsitteruser'] == $user_sitterlogin) {
            $user['lastsitterloggedin'] = 0;
        }
    }
    $sql = "UPDATE " . $db_tb_user . " SET lastsitterloggedin=0 WHERE lastsitteruser='" . $user_sitterlogin . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}
$done = getVar('done');
if ($done == 'Erledigt') {
    foreach ($users as $user) {
        if ($user['lastsitteruser'] == $user_sitterlogin) {
            $user['lastsitterloggedin'] = 0;
            $sql                        = "UPDATE " . $db_tb_user . " SET lastsitterloggedin=0,dauersittenlast=" . CURRENT_UNIX_TIME . " WHERE id='" . $user['id'] . "'";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }
    }
}

if (empty($login)) {
    $mainurl = 'http://icewars.de';
} elseif (isset($users[$login])) {
    $login_user = $users[$login];
    $mainurl    = $login_user['url'];
    foreach ($users as $user) {
        if ($user['lastsitteruser'] == $user_sitterlogin) {
            $user['lastsitterloggedin'] = 0;
        }
    }
    $login_user['lastsitterlogin']    = CURRENT_UNIX_TIME;
    $login_user['lastsitteruser']     = $user_sitterlogin;
    $login_user['lastsitterloggedin'] = 1;
    $sql                              = "UPDATE " . $db_tb_user . " SET lastsitterloggedin=0 WHERE lastsitteruser='" . $user_sitterlogin . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $sql = "UPDATE " . $db_tb_user . " SET lastsitterlogin=" . $login_user['lastsitterlogin'] . ",lastsitteruser='" . $login_user['lastsitteruser'] . "',lastsitterloggedin=1 WHERE id='" . $login_user['id'] . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $sql = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin,fromuser,date,action) VALUES ('" . $login_user['id'] . "', '" . $user_sitterlogin . "', '" . $config_date . "', 'login')";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

// Select page mode
switch ($mode) {
    case 'index':
        ?>
        <!DOCTYPE html>
        <html lang="de">
        <head>
            <meta charset="utf-8">
            <title>Icewars</title>
            <script>
                var redirectURL = "<?php

	if ($redirect == 'planiress') {
	    echo 'http://sandkasten.icewars.de/game/index.php?action=wirtschaft&typ=planiress';
    } else if ($redirect == 'schiff_uebersicht') {
        echo 'http://sandkasten.icewars.de/game/index.php?action=mil&typ=schiff_uebersicht';
    } else if ($redirect == 'gebaeude_uebersicht') {
        echo 'http://sandkasten.icewars.de/game/index.php?action=wirtschaft&typ=geb';
    } else if ($redirect == 'forschung_uebersicht') {
        echo 'http://sandkasten.icewars.de/game/index.php?action=forschung&forschung_allshow=1';
    }

?>";
                function setRedirection(url) {
                    redirectURL = url;
                }
                function redirect(id) {
                    if (redirectURL.length) {
                        contentFrameElement = document.getElementById(id);
                        url = redirectURL;
                        redirectURL = "";
                        contentFrameElement.src = url;
                    }

                }
            </script>
        </head>
        <frameset rows="0,*" cols="*" frameborder="YES" border="0" framespacing="0">
            <frame src="?mode=top" name="topFrame" scrolling="NO" noresize>
            <frameset rows="*" cols="300,*" framespacing="0" frameborder="YES" border="0">
                <frame src="?mode=left&redirect=<?php echo $redirect ?><?php echo !empty($login) ? "&login=$login" : "" ?><?php echo !empty($action) ? "&action=$action" : "" ?><?php echo !empty($allianz) ? "&allianz=$allianz" : "" ?>" name="left" id="left" scrolling="YES">
                <frame src="<?php echo $mainurl ?>" name="main" id="main" onload="redirect(this.id)">
            </frameset>
        </frameset>
        <noframes>
            <body>
            </body>
        </noframes>
        </html>
        <?php
        break;
    case 'left':
        ?>
        <html>
        <head>
            <style type="text/css">
                * {
                    font-family: verdana;
                    font-size: 11px;
                }

                body {
                    color: #ffffff;
                    background-color: #111111;
                    background-image: url(bilder/bg_space3.png);
                }

                a:link {
                    color: #bbbbbb;
                }

                a:visited {
                    color: #bbbbbb;
                }

                body, table, tr, td, form {
                    margin: 0 0 0 0;
                    padding: 0 0 0 0;
                }

                .attack {
                    color: #ff0000;
                }

                .probe {
                    color: #cc9900;
                }

                .loggedin {
                    color: #33AA33;
                }

                .dursitting {
                    color: #ff00ff;
                }

                .dursitting_time {
                    color: #bbbbbb;
                }

                .dursitting_due {
                    color: #ff00ff;
                }

                .time_critical {
                    color: #ff0000;
                }

                .time_warning {
                    color: #cc9900;
                }

                .time_normal {
                    color: #bbbbbb;
                }

                .time {
                    color: #990066;
                }
            </style>
        </head>
        <body onLoad="start();">
        <table>
            <tr>
                <td>
                    <select id="redirectPage" onchange="parent.document.getElementById('left').src = '?mode=index&redirect=' + options[selectedIndex].value + '&login=<?php echo $user['id'] ?><?php echo !empty($allianz) ? "&allianz=$allianz" : "" ?>';">
                        <option value="">(Startseite)</option>
                        <option value="planiress"<?php echo $redirect == 'planiress' ? ' selected' : '' ?>>
                            Kolo-/Ressübersicht
                        </option>
                        <option value="schiff_uebersicht"<?php echo $redirect == 'schiff_uebersicht' ? ' selected' : '' ?>>
                            Schiffübersicht
                        </option>
                        <option value="gebaeude_uebersicht"<?php echo $redirect == 'gebaeude_uebersicht' ? ' selected' : '' ?>>
                            Geb&auml;udeübersicht
                        </option>
                    </select>
                </td>
            <tr>
                <td nowrap width="100%">
                    <!--<a href="?action=own<?php echo !empty($allianz) ? "&allianz=$allianz" : "" ?>" target="_top">Eigener Spieler</a><br>
					<a href="http://176.9.109.187/" target="main">Icewars-Notlogin</a>-->
                </td>
                <td nowrap>
                </td>
            </tr>
            <tr>
                <td>
                </td>
                <td>
                </td>
            </tr>
        </table>
        <br>
        <?php
        if (isset($login_user)) {
            ?>
            <form target="_top">
                <?php echo isset($login_user['alliance']) ? '[' . $login_user['alliance'] . ']' : '' ?><?php echo $login_user['id'] ?>
                <br>
                <?php echo $login_user['typ'] ?>
                <?php            if (!empty($login_user['group']) && $login_user['group'] != $login_user['id']) { ?>
                    von <?php echo $login_user['group'] ?><br>
                    <?php            if (!empty($login_user['ikea'])) {
                        echo "<font color='yellow'>IKEA</font><br>";
                    } else if (!empty($login_user['peitschen'])) {
                        echo "<font color='pink'>MdP</font><br>";
                    } else if (!empty($login_user['ikea']) && !empty($login_user['peitschen'])) {
                        echo "<font color='#FF00FF'><b>Rausschmeissen!</b></font><br>";
                    }
                    if (!empty($login_user['dauersitten'])) {
                        echo "<span class='dursitting'>" . $login_user['dauersittentext'] . "</span>";
                        echo " <span class='dursitting_time'>(alle " . ($login_user['dauersitten'] / MINUTE) . " Minuten)</span><br>";
                    }
                } else {
                    ?>
                    <br>
                <?php } ?>
                <?php        if (!empty($login_user['dauersitten'])) { ?>
                    <input type="submit" value="Erledigt" name="done" class="submit">
                <?php } ?>
                <input type="submit" value="Ausloggen" name="logout" class="submit">
            </form>
            <br>
            <form name="scan" method="POST" action="index.php" target="iwdb" enctype="multipart/form-data">
                <input type="hidden" name="sid" value="<?php echo $sid ?>">
                <input type="hidden" name="action" value="newscan">
                <input type="hidden" name="seluser" value="<?php echo $login_user['id'] ?>">
                Neuer Bericht:<br>
                <textarea id="reportText" name="text" rows="2" cols="40"></textarea><br>
                <input id="reportSave" type="submit" value="Speichern" name="B1" class="submit">
            </form>
        <?php } ?>
        <br>
        <table>
            <tr>
                <td width="100%">
                    <span class="time">Uhrzeit</span>
                </td>
                <td nowrap>
                    <span class="time"><?php echo strftime($config_sitter_timeformat, CURRENT_UNIX_TIME); ?></span>
                </td>
            </tr>
        </table>
        <?php    foreach ($view as $user) { ?>
            <table>
                <tr>
                    <td width="100%">
                        <a href="?mode=index&redirect=<?php echo $redirect ?>&login=<?php echo $user['id'] ?><?php echo !empty($allianz) ? "&allianz=$allianz" : "" ?>" target="_top"><?php echo $user['id']; ?></a>
                    </td>
                    <?php        if (isset($user['next_date_text'])) { ?>
                        <td nowrap>
                            <?php            if ($user['next_status'] == $status['attack'] && $user['next_date'] <= CURRENT_UNIX_TIME) { ?>
                            <span class="time_critical">
<?php            } elseif ($user['next_status'] == $status['attack'] && $user['next_date'] > CURRENT_UNIX_TIME) { ?>
                                <span class="time_warning">
<?php            } else { ?>
                                    <span class="time_normal">
<?php            } ?>
                                        <?php echo $user['next_date_text'] ?></span>
                        </td>
                    <?php } ?>
                </tr>
            </table>
            <?php if ($user['lastsitterloggedin'] || (!empty($user['attack']) AND count($user['attack'] > 0)) || (!empty($user['probe']) AND count($user['probe']) > 0)) { ?>
                <table>
                    <tr>
                        <td width="100%">
                            <?php            if (!empty($user['attack']) AND count($user['attack']) > 0) { ?>
                                <span class="attack">Angriff von <?php echo $user['attack'][0]['from'] ?>
                                    auf <?php echo $user['attack'][0]['coords'] ?></span><br>
                            <?php } ?>
                            <?php            if (!empty($user['probe']) AND count($user['probe']) > 0) { ?>
                                <span class="probe">Sondierung von <?php echo $user['probe'][0]['from'] ?>
                                    auf <?php echo $user['probe'][0]['coords'] ?></span><br>
                            <?php } ?>
                            <?php            if ($user['lastsitterloggedin']) { ?>
                                <span class="loggedin"><?php echo $user['lastsitteruser'] ?> ist eingeloggt</span><br>
                            <?php } ?>
                            <?php            if ($user['dauersittendue']) { ?>
                                <span class="dursitting_due"><?php echo $user['dauersittentext'] ?></span><br>
                            <?php } ?>
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>
            <?php } ?>
        <?php
        }
        ?>
        </body>
        </html>
        <?php
        break;
    default:
        ?>
            Fehler: Unbekannter Modus '"<?php echo $mode ?>"'.
        <?php
}
?>