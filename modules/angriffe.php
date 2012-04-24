<?php
/*****************************************************************************/
/* angriffe.php                                                              */
/*****************************************************************************/
if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

// Angriff �bernehmen
if (isset($_GET['id']) && isset($_GET['subaction']) && $_GET['subaction'] == 'ueb') {
	
	$time = time();
	$id = $_GET['id'];
	
	$sql = "
		UPDATE {$db_prefix}angriffe
		SET
			ueb = '1',
			ueb_time = '$time',
			ueb_user = '$user_sitterlogin'
		WHERE
			ID_ANGRIFF = $id";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

// Angriff freigeben
if (isset($_GET['id']) && isset($_GET['subaction']) && $_GET['subaction'] == 'frei') {
	
	$time = time();
	$id = $_GET['id'];
	
	$sql = "
		UPDATE {$db_prefix}angriffe
		SET
			ueb = NULL
		WHERE
			ID_ANGRIFF = $id";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}


$time = time() - 60*15;

// Angriffe aus Datenbank holen
$angriffe = array();
$sql = "
	SELECT ID_ANGRIFF, time, def_coords, def_pla, def_user, att_coords, att_pla, att_user, ueb, ueb_time, ueb_user
	FROM {$db_prefix}angriffe
	WHERE
		time > $time
	ORDER BY
		time";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while($row = $db->db_fetch_array($result))
{
	// sortieren nach �bernommen/noch frei
	if (isset($row['ueb']))
		$sort = 'ueb';
	else
		$sort = 'frei';
		
	// Restzeit ausrechnen
	$angriffe[$sort][] = array(
		'id' => $row['ID_ANGRIFF'],
		'time' => $row['time'],
		'def_coords' => $row['def_coords'],
		'def_pla' => $row['def_pla'],
		'def_user' => $row['def_user'],
		'att_coords' => $row['att_coords'],
		'att_pla' => $row['att_pla'],
		'att_user' => $row['att_user'],
		'ueb' => $row['ueb'],
		'ueb_time' => $row['ueb_time'],
		'ueb_user' => $row['ueb_user'],
	);
}


echo '
	<script src="javascript/angriffe.js" type="text/javascript"></script>
	<div class="doc_title">Angriffe</div><br>';
if (isset($angriffe['frei'])) {
	echo '
	<table border="0" cellpadding="5" cellspacing="1" class="bordercolor" style="width: 90%;">
		<tr>
			<td class="titlebg" colspan="6" align="center"><span class="doc_red"><b>laufende Angriffe</b></span></td>
		</tr>
		<tr>
			<td class="titlebg" align="center"><b>Zeit</b></td>
			<td class="titlebg" align="center"><b>Angreifer</b></td>
			<td class="titlebg" align="center"><b>Planet</b></td>
			<td class="titlebg" align="center"><b>Verteidiger</b></td>
			<td class="titlebg" align="center"><b>Planet</b></td>
			<td class="titlebg" align="center"><b>übernommen</b></td>
		</tr>';
	foreach ($angriffe['frei'] as $key => $value) {
		echo '
		<script>setTimeout(\'countdown('.$value['time'].', '.$value['id'].')\',0);</script>
		<tr>
			<td class="windowbg1" align="center">',date('d.m.Y H:i:s', $value['time']),'<br><span id="countdown',$value['id'],'" class="doc_red"> </span></td>
			<td class="windowbg1" align="center">',$value['att_user'],'</td>
			<td class="windowbg1" align="center">',$value['att_pla'],'<br>(',$value['att_coords'],')</td>
			<td class="windowbg1" align="center">',$value['def_user'],'</td>
			<td class="windowbg1" align="center">',$value['def_pla'],'<br>(',$value['def_coords'],')</td>
			<td class="windowbg1" align="center">
				<span class="doc_red">von keinem</span>
				<br><br><a href="index.php?action=angriffe&subaction=ueb&id=', $value['id'] ,'&sid=', $sid ,'">[übernehmen]</a>';
		// Admin, HC und momentan Verantwortlicher d�rfen sich einloggen
		if ($user_status == "admin" || $user_status == "hc" || ($user_sitterlogin == $value['ueb_user'] && !empty($value['ueb'])) )
			echo '
				<br><br><a href="index.php?action=sitterlogins&sitterlogin=', urlencode($value['def_user']) ,'&sid=', $sid ,'" target="_blank">[einloggen]</a>';
		echo '
			</td>
		</tr>';
	}
	echo '
	</table>
	<br>';
}
if (isset($angriffe['ueb'])) {
	echo '
	<table border="0" cellpadding="5" cellspacing="1" class="bordercolor" style="width: 90%;">
		<tr>
			<td class="titlebg" colspan="6" align="center"><span class="doc_green"><b>laufende Angriffe (übernommen)</b></span></td>
		</tr>
		<tr>
			<td class="titlebg" align="center"><b>Zeit</b></td>
			<td class="titlebg" align="center"><b>Angreifer</b></td>
			<td class="titlebg" align="center"><b>Planet</b></td>
			<td class="titlebg" align="center"><b>Verteidiger</b></td>
			<td class="titlebg" align="center"><b>Planet</b></td>
			<td class="titlebg" align="center"><b>übernommen</b></td>
		</tr>';
	foreach ($angriffe['ueb'] as $key => $value) {
		echo '
		<script>setTimeout(\'countdown('.$value['time'].', '.$value['id'].')\',0);</script>
		<tr>
			<td class="windowbg1" align="center">',date('d.m.Y H:i:s', $value['time']),'<br><span id="countdown',$value['id'],'" class="doc_red"> </span></td>
			<td class="windowbg1" align="center">',$value['att_user'],'</td>
			<td class="windowbg1" align="center">',$value['att_pla'],'<br>(',$value['att_coords'],')</td>
			<td class="windowbg1" align="center">',$value['def_user'],'</td>
			<td class="windowbg1" align="center">',$value['def_pla'],'<br>(',$value['def_coords'],')</td>
			<td class="windowbg1" align="center">
				<span class="doc_green">', $value['ueb_user'] ,'</span>';
		// �bernahme von Planetenbesitzer
		if ($user_sitterlogin == $value['def_user'])
			echo '
				<br><br><a href="index.php?action=angriffe&subaction=ueb&id=', $value['id'] ,'&sid=', $sid ,'">[übernehmen]</a>';
		// Admin, HC und momentan Verantwortlicher d�rfen sich einloggen
		if ($user_status == "admin" || $user_status == "hc" || ($user_sitterlogin == $value['ueb_user'] && !empty($value['ueb'])) )
			echo '
				<br><br><a href="index.php?action=sitterlogins&sitterlogin=', urlencode($value['def_user']) ,'&sid=', $sid ,'" target="_blank">[einloggen]</a>';
		// Admin, HC und momentan Verantwortlicher d�rfen Angriff wieder freigeben
		if ($user_status == "admin" || $user_status == "hc" || ($user_sitterlogin == $value['ueb_user'] && !empty($value['ueb'])) )
			echo '
				<br><br><a href="index.php?action=angriffe&subaction=frei&id=', $value['id'] ,'&sid=', $sid ,'">[freigeben]</a>';
		echo '
			</td>
		</tr>';
	}
	echo '
	</table>
	<br>';
}
if (isset($angriffe) && empty($angriffe)) {
	echo '
	<table border="0" cellpadding="5" cellspacing="1" class="bordercolor" style="width: 90%;">
		<tr>
			<td class="titlebg" colspan="6" align="center"><b>laufende Angriffe</b></td>
		</tr>
		<tr>
			<td class="titlebg" align="center"><b>Zeit</b></td>
			<td class="titlebg" align="center"><b>Angreifer</b></td>
			<td class="titlebg" align="center"><b>Planet</b></td>
			<td class="titlebg" align="center"><b>Verteidiger</b></td>
			<td class="titlebg" align="center"><b>Planet</b></td>
			<td class="titlebg" align="center"><b>übernommen</b></td>
		</tr>
		<tr>
			<td class="windowbg1" colspan="6" align="center"><b>keiner traut sich uns anzugreifen MUHAHAHAHA</b></td>
		</tr>
	</table>';
}
?>
