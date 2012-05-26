<?php
define('IRA', TRUE);

date_default_timezone_set('Europe/Berlin');

include_once('config/configsql.php');
include_once('includes/db_mysql.php');

// Create database connection
$db = new db();
$link_id = $db->db_connect($db_host, $db_user, $db_pass, $db_name)
	or die('Could not connect to database.');

include_once('config/config.php');
include_once('includes/function.php');
include_once('includes/sid.php');

global $sid;

$sql = "SELECT gesperrt FROM " . $db_tb_user . " WHERE id = '" . $user_id . "'"; 
$result_g = $db->db_query($sql)       
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row_g = $db->db_fetch_array($result_g);
if ($row_g['gesperrt'] == 1)
	die ('<div style="text-align:center;color:red">ihr Account ist gesperrt worden!</div>');

if (empty($sid) || empty($user_sitterlogin) || !($user_adminsitten == SITTEN_BOTH || $user_adminsitten == SITTEN_ONLY_LOGINS) || $user_id == "guest") {
	header("Location: " . $config_url);
	exit;
}

// Get sitterprofile
$serverskin = 1;
$serverskin_typ = 3;
$config_sitterlogin_timeout = 4 * 60;

$status = array(
	'use' => 1,
	'attack' => 2,
	'probe' => 3,
	'past' => 4,
);

$allianz = getVar("allianz");
if (!$user_fremdesitten)
	$allianz = $user_allianz;

// Get avaible sitter logins
$sql = "SELECT * FROM " . $db_tb_user . " where sitten=1";
if (!empty($allianz))
	$sql .= " and allianz='" . $allianz . "'";
$result = $db->db_query($sql)
	or die('Could not query user.');
while ($row = $db->db_fetch_array($result)) {
	unset($user);

	$user['id'] = $row['id'];
	$user['typ'] = $row['budflesol'];
	$user['lastsitterlogin'] = $row['lastsitterlogin'];
	$user['lastsitteruser'] = $row['lastsitteruser'];
	if ($row['lastsitterloggedin'] && $row['lastsitterlogin'] > (time() - 5 * 60)) {
		$user['lastsitterloggedin'] = 1;
		$user['next_status'] = $status['use'];
	} else {
		$user['lastsitterloggedin'] = 0;
		$user['next_status'] = $status['past'];
	}
	$user['ikea'] = $row['ikea'];
	$user['peitschen'] = $row['peitschen'];
	$user['alliance'] = empty($row['allianz']) ? "No Alliance" : $row['allianz'];
	$user['group'] = $row['buddlerfrom'];
	$user['dauersitten'] = $row['dauersitten'];
	$user['dauersittentext'] = $row['dauersittentext'];
	$user['dauersittenlast'] = $row['dauersittenlast'];
	if (!empty($user['dauersitten']) && (empty($user['dauersittenlast']) || ($user['dauersittenlast'] + $user['dauersitten'] < time())))
		$user['dauersittendue'] = true;
	else
		$user['dauersittendue'] = false;

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
		//$user['next_status'] = $user['next_date'] < time() ? 'due' : 'pending';
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
	$sql = "SELECT * FROM " . $db_tb_lieferung . " WHERE user_to='" . $row['id'] . "' AND art IN ('Angriff','Sondierung') AND time>" . (time() - (15 * 60)) . " ORDER BY time DESC";
	$result_angriff = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while ($row_angriff = $db->db_fetch_array($result_angriff)) {
		if ($row_angriff['time'] > (time() - ($row_angriff['art'] == 'Angriff' ? (15 * 60) : (5 * 60)))) {
			$key = $row_angriff['art'] == 'Angriff' ? 'attack' : 'probe';
			$user[$key][] = array(
				'coords' => $row_angriff['coords_to_gal'] . ':' . $row_angriff['coords_to_sys'] . ':' . $row_angriff['coords_to_planet'],
				'time' => $row_angriff['time'],
				'from' => $row_angriff['user_from'],
			);
			if (!isset($user['next_date']) || $user['next_date'] > ($row_angriff['time'] + (15 * 60))) {
				$user['next_date'] = $row_angriff['time'];
			}
			if ($user['next_status'] > $status[$key]) {
				$user['next_status'] = $status[$key];
			}
		}
	}

	if (!isset($user['next_date']))
		$user['next_date'] = $user['lastsitterlogin'];

	if ($user['next_date'] > 0)
		$user['next_date_text'] = strftime($config_sitter_timeformat, $user['next_date']);

	$users[$row['id']] = $user;
	$uview[$user['next_status']][$user['next_date'] . $user['id']] = $user;
}

// Assemble view
$view = array();
foreach ($status as $key)
	if (isset($uview[$key])) {
		ksort($uview[$key]);
 		$view = array_merge($view, $uview[$key]);
	}

// Get request parameter
$login = $_REQUEST['login'];

$mode = $_REQUEST['mode'];
if (empty($mode))
	$mode = 'index';

$action = $_REQUEST['action'];
if ($action == 'own')
	$login = $user_sitterlogin;

$redirect = $_REQUEST['redirect'];

$logout = getVar('logout');
if ($logout == 'Ausloggen') {
	foreach ($users as $user)
		if ($user['lastsitteruser'] == $user_sitterlogin)
			$user['lastsitterloggedin'] = 0;
	$sql = "UPDATE " . $db_tb_user . " SET lastsitterloggedin=0 WHERE lastsitteruser='" . $user_sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}
$done = getVar('done');
if ($done == 'Erledigt') {
	foreach ($users as $user)
		if ($user['lastsitteruser'] == $user_sitterlogin)
		{
			$user['lastsitterloggedin'] = 0;
			$sql = "UPDATE " . $db_tb_user . " SET lastsitterloggedin=0,dauersittenlast=" . time() . " WHERE id='" . $user['id'] . "'";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}
}

if (empty($login))
	$mainurl = 'http://icewars.de';
elseif (isset($users[$login])) {
	$login_user = $users[$login];
	$mainurl = $login_user['url'];
	foreach ($users as $user)
		if ($user['lastsitteruser'] == $user_sitterlogin)
			$user['lastsitterloggedin'] = 0;
	$login_user['lastsitterlogin'] = time();
	$login_user['lastsitteruser'] =  $user_sitterlogin;
	$login_user['lastsitterloggedin'] = 1;
	$sql = "UPDATE " . $db_tb_user . " SET lastsitterloggedin=0 WHERE lastsitteruser='" . $user_sitterlogin . "'";
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
	<head>
		<title>Icewars</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script>
var redirectURL = "<?php

	if ($redirect == 'planiress')
		echo 'http://sandkasten.icewars.de/game/index.php?action=wirtschaft&typ=planiress';
	else if ($redirect == 'schiff_uebersicht')
		echo 'http://sandkasten.icewars.de/game/index.php?action=mil&typ=schiff_uebersicht';
	else if ($redirect == 'gebaeude_uebersicht')
		echo 'http://sandkasten.icewars.de/game/index.php?action=wirtschaft&typ=geb';
	
?>";
function setRedirection(url)
{
	redirectURL = url;
}
function redirect(id)
{
	if (redirectURL.length)
	{
		contentFrameElement = document.getElementById(id);
		url = redirectURL;
		redirectURL = "";
		contentFrameElement.src = url;
	}

}
		</script>
	</head>
	<frameset rows="0,*" cols="*" frameborder="YES" border="0" framespacing="0">
		<frame src="?mode=top" name="topFrame" scrolling="NO" noresize >
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
			    background-image:url(bilder/bg_space3.png);
			}
			a:link {
				color: #bbbbbb;
			}
			a:visited {
				color: #bbbbbb;
			}
			body,table,tr,td,form {
				margin : 0 0 0 0;
				padding : 0 0 0 0;
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
		<script>
var icewarsClipboardInterval;
var icewarsClipboardLast;
function icewarsClipboardInstall()
{
	switchElement = document.getElementById('icewarsClipboardSwitch');
	if (switchElement.innerHTML == 'Automatische Zwischenablage (aus)')
	{
		icewarsClipboardLast = window.clipboardData.getData('Text');
		icewarsClipboardInterval = setInterval('icewarsClipboardExecute()', 1000);
		switchElement.innerHTML = "Automatische Zwischenablage (ein)";
	}
	else
	{
		clearInterval(icewarsClipboardInterval);
		switchElement.innerHTML = "Automatische Zwischenablage (aus)";
	}
}
function icewarsClipboardExecute()
{
	text = window.clipboardData.getData('Text');
	if (text != icewarsClipboardLast)
	{
		reportSaveElement = document.getElementById('reportSave');
		reportTextElement = document.getElementById('reportText');
		if (reportTextElement)
		{
			reportTextElement.value = text;
			reportSaveElement.click();
		}
	}
	icewarsClipboardLast = text;
}
function start() {
	//document.scan.text.value='';
}			
function update() {
	window.location.reload();
}
//erst mal wieder aus, dass muessen wir anders machen, zuviele probs mit gelockten accounts und so
setTimeout("update()", 60000);
		</script>
	</head>
	<body onLoad="start();">
		<table>
			<tr>
				<td>
					<select id="redirectPage" onchange="parent.document.getElementById('left').src = '?mode=index&redirect=' + options[selectedIndex].value + '&login=<?php echo $user['id'] ?><?php echo !empty($allianz) ? "&allianz=$allianz" : "" ?>';">
				 		<option value="">(Startseite)</option>
				 		<option value="planiress"<?php echo $redirect == 'planiress' ? ' selected' : '' ?>>Kolo-/Ress&uuml;bersicht</option>
				 		<option value="schiff_uebersicht"<?php echo $redirect == 'schiff_uebersicht' ? ' selected' : '' ?>>Schiff&uuml;bersicht</option>
						<option value="gebaeude_uebersicht"<?php echo $redirect == 'gebaeude_uebersicht' ? ' selected' : '' ?>>Geb&auml;ude&uuml;bersicht</option>
					</select>
				</td>
			<tr>
				<td nowrap width="100%">
					<!--<a href="?action=own<?php echo !empty($allianz) ? "&allianz=$allianz" : "" ?>" target="_top">Eigener Spieler</a><br>
					<a href="http://176.9.109.187/" target="main">Icewars-Notlogin</a><br>
					<a id="icewarsClipboardSwitch" href="#" onclick="icewarsClipboardInstall()">Automatische Zwischenablage (aus)</a>-->
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
		if (isset($login_user)) { ?>
			<form target="_top">
			<?php echo isset($login_user['alliance']) ? '[' . $login_user['alliance'] . ']' : '' ?><?php echo $login_user['id'] ?><br>
			<?php echo $login_user['typ'] ?>
<?php			if (!empty($login_user['group']) && $login_user['group'] != $login_user['id']) { ?>
			von <?php echo $login_user['group'] ?><br>
<?php			if (!empty($login_user['ikea']))
				echo "<font color=\"yellow\">IKEA</font><br>";
			else if (!empty($login_user['peitschen']))
				echo "<font color=\"pink\">MdP</font><br>";
			else if (!empty($login_user['ikea']) && !empty($login_user['peitschen']))
				echo "<font color=\"#FF00FF\"><b>Rausschmeissen!</b></font><br>";
			if (!empty($login_user['dauersitten'])) {
				echo "<span class=\"dursitting\">" . $login_user['dauersittentext'] . "</span>";
				echo " <span class=\"dursitting_time\">(alle " . ($login_user['dauersitten'] / 60) . " Minuten)</span><br>";
			}
		} else { ?>
			<br>
<?php		} ?>
<?php		if (!empty($login_user['dauersitten'])) { ?>
		<input type="submit" value="Erledigt" name="done" class="submit">
<?php		} ?>
		<input type="submit" value="Ausloggen" name="logout" class="submit">
		</form>
		<br><form name="scan" method="POST" action="index.php" target="iwdb" enctype="multipart/form-data">
			<input type="hidden" name="sid" value="<?php echo $sid ?>">
			<input type="hidden" name="action" value="newscan">
			<input type="hidden" name="seluser" value="<?php echo $login_user['id'] ?>">
			Neuer Bericht:<br>
			<textarea id="reportText" name="text" rows="2" cols="40"></textarea><br>
			<input id="reportSave" type="submit" value="Speichern" name="B1" class="submit">
		</form>
<?php	} ?>
		<br>
		<table>
			<tr>
				<td width="100%">
					<span class="time">Uhrzeit</span>
				</td>
				<td nowrap>
					<span class="time"><?php echo strftime($config_sitter_timeformat, time()); ?></span>
				</td>
			</tr>
		</table>
<?php	foreach ($view as $user) { ?>
		<table>
			<tr>
				<td width="100%">
					<a href="?mode=index&redirect=<?php echo $redirect ?>&login=<?php echo $user['id'] ?><?php echo !empty($allianz) ? "&allianz=$allianz" : "" ?>" target="_top"><?php echo $user['id']; ?></a>
				</td>
<?php		if (isset($user['next_date_text'])) { ?>
				<td nowrap>
<?php			if ($user['next_status'] == $status['attack'] && $user['next_date'] <= time()) { ?>
					<span class="time_critical">
<?php			} elseif ($user['next_status'] == $status['attack'] && $user['next_date'] > time()) { ?>
					<span class="time_warning">
<?php			} else { ?>
					<span class="time_normal">
<?php			} ?>
					<?php echo $user['next_date_text'] ?></span>
				</td>
<?php		} ?>
			</tr>
		</table>
<?php		if ($user['lastsitterloggedin'] || count($user['attack'] > 0) || count($user['probe']) > 0) { ?>
		<table>
			<tr>
				<td width="100%">
<?php			if (count($user['attack']) > 0) { ?>
				<span class="attack">Angriff von <?php echo $user['attack'][0]['from'] ?> auf <?php echo $user['attack'][0]['coords'] ?></span><br>
<?php			} ?>
<?php			if (count($user['probe']) > 0) { ?>
					<span class="probe">Sondierung von <?php echo $user['probe'][0]['from'] ?> auf <?php echo $user['probe'][0]['coords'] ?></span><br>
<?php			} ?>
<?php			if ($user['lastsitterloggedin']) { ?>
					<span class="loggedin"><?php echo $user['lastsitteruser'] ?> ist eingeloggt</span><br>
<?php			} ?>
<?php			if ($user['dauersittendue']) { ?>
					<span class="dursitting_due"><?php echo $user['dauersittentext'] ?></span><br>
<?php			} ?>
				</td>
				<td>
				</td>
			</tr>
		</table>
<?php		} ?>
<?php	} 
?>
	</body>
</html>
<?php
	break;
case 'register':
?>
<html>
	<body>
		Diese Seite verwendet Frames.
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