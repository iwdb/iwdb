<?php
/*****************************************************************************/
/* dauerauftraege.php                                                        */
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
if  ( ( $user_adminsitten != SITTEN_BOTH ) && ( $user_adminsitten != SITTEN_ONLY_LOGINS ) )
	die('Hacking attempt...');

$newlog = getVar('newlog');

if ( ! empty($newlog) )
{
	$sitterlogin = getVar('sitterlogin');
	$auftrag = getVar('auftrag');

	$sql = "SELECT id FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $sitterlogin . "' AND fromuser = '" . $user_sitterlogin . "' AND action <> 'login' AND date > " . ( $config_date - $config_sitterpunkte_timeout );
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$anz = $db->db_num_rows($result);

	// Log
	$logtext = nl2br($auftrag);
	$sql = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin, fromuser, date, action) VALUES ('" . $sitterlogin . "', '" . $user_sitterlogin . "', '" . $config_date . "', '" . $logtext . "')";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	// Punkte //
	if ( ( $sitterlogin != $user_sitterlogin ) && ( $anz == 0 ) )
	{
		$sql = "UPDATE " . $db_tb_user . " SET sitterpunkte = sitterpunkte + " . $config_sitterpunkte_auftrag_frei . " WHERE sitterlogin = '" . $user_sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}
}

$sql = "SELECT AVG(sitterpunkte) FROM " . $db_tb_user . " WHERE sitterpunkte <> 0";
$result_avg = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row_avg = $db->db_fetch_array($result_avg);

$sitterlogins = array();
$sql = "SELECT sitterlogin FROM " . $db_tb_user . " WHERE sitterpwd <> '' " . (($user_status == "admin") ? "": "AND sitten = '1' " ). "ORDER BY sitterlogin ASC";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while($row = $db->db_fetch_array($result))
{
	$sitterlogins[] = $row['sitterlogin'];
}

if ( ! empty($newlog) )
{
	echo "<div class='system_notification'>Auftrag gespeichert.</div>";
}
$count = 0;
$sql = "SELECT sitterlogin, sitterpwd, sitten, sitterpunkte, peitschen, sittercomment FROM " . $db_tb_user . " WHERE sittercomment <> '' ORDER BY sitterlogin ASC";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$anz = $db->db_num_rows($result);
if ( ! empty($anz) )
{
?>
<br>

<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
  <td class="titlebg" colspan="4" align="center">
   <b>Dauerauftraege</b>
  </td>
 </tr>
 <tr>
  <td class="titlebg" style="width:20%;">
   <b>Username</b>
  </td>
  <td class="titlebg" style="width:60%;">
   <b>Grund / Anweisungen</b>
  </td>  
  <td class="titlebg" style="width:10%;">
   <b>einloggen</b>
  </td>
  <td class="titlebg" style="width:10%;">
   <b>letzter Login</b>
  </td>
 </tr>
<?php
	while($row = $db->db_fetch_array($result))
	{
		if ( ( ( $user_status == "admin" ) || ( $row['sitten'] == 1 ) ) && ( ! empty($row['sitterpwd']) ) )
		{
			$sql = "SELECT id FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $user_sitterlogin . "' AND fromuser = '" . $row['sitterlogin'] . "' AND fromuser <> '" . $user_sitterlogin . "'";
			$result_punkte = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	
			$sql = "SELECT fromuser, date, MAX(date) FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $row['sitterlogin'] . "' AND action = 'login' GROUP BY date";
			$result_lastlogin = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				
			$row_lastlogin['MAX(date)'] = 0;
			$row_lastlogin['date'] = 0;
			$row_lastlogin['fromuser'] = "";
			
			while ($row_lastlogins = $db->db_fetch_array($result_lastlogin))
			{
				if ( $row_lastlogins['date'] == $row_lastlogins['MAX(date)'] ) $row_lastlogin = $row_lastlogins;
			}
	
			$users_sitterpunkte[$count] = $row['sitterpunkte'] + $config_sitterpunkte_friend * $db->db_num_rows($result_punkte);
			$users_sitterpunkte_anz[$count] = " [" . $row['sitterpunkte'] .  " + " . ($config_sitterpunkte_friend * $db->db_num_rows($result_punkte)) . "]";
			$users_sitterlogin[$count] = $row['sitterlogin'];
			$users_sitterpeitschen[$count] = $row['peitschen'];
			$users_sittercomment[$count] = $row['sittercomment'];
			$users_sitten[$count] = $row['sitten'];
			$users_lastlogin[$count] = $row_lastlogin['MAX(date)'];
			$users_lastlogin_user[$count] = $row_lastlogin['fromuser'];
			$users_logged_in[$count] = ( ( $row_lastlogin['MAX(date)'] > ($config_date - $config_sitterlogin_timeout) ) && ( $row_lastlogin['fromuser'] != $user_sitterlogin ) ) ? $row_lastlogin['fromuser'] : "";
	
			$count++;
		}
	}
	array_multisort($users_lastlogin, SORT_ASC, SORT_NUMERIC, $users_sitterlogin, $users_sitterpunkte, $users_sitterpunkte_anz, $users_lastlogin_user, $users_logged_in, $users_sitterpeitschen, $users_sittercomment, $users_sitten);
	
	foreach ($users_sitterlogin as $key => $data)
	{
		if ($config_date - $config_dauer_timeout < $users_lastlogin[$key] )	$num = 1;
		else $num = 2;
?>
 <tr>
  <td class="windowbg<?php echo $num;?>" valign="top">
<?php
		if ( $user_status == "admin" ) echo "<a href=\"index.php?action=profile&sitterlogin=" . urlencode($data) . "&sid=" . $sid . "\">" . $data . "</a>";
		else echo $data;
?>
   <?php echo $users_sitterpunkte_anz[$key];?>
   <?php echo ( $users_sitterpunkte[$key] > (3 * round($row_avg['AVG(sitterpunkte)']) ) ) ? "<img src=\"bilder/star1.gif\" border=\0\" style=\"vertical-align:middle;\">": ( ( $users_sitterpunkte[$key] > (2 * round($row_avg['AVG(sitterpunkte)']) ) ) ? "<img src=\"bilder/star2.gif\" border=\0\" style=\"vertical-align:middle;\">" : ( ( $users_sitterpunkte[$key] > round($row_avg['AVG(sitterpunkte)'] ) ) ? "<img src=\"bilder/star3.gif\" border=\0\" style=\"vertical-align:middle;\">" : ""));?> 
  </td>
  <td class="windowbg<?php echo $num;?>" valign="top">
   <?php echo nl2br($users_sittercomment[$key]);?>
   <?php echo ( $users_sitterpeitschen[$key] == "1" ) ? "<br><br><i>Meister d. Peitschen</i>": "";?>
  </td>
  <td class="windowbg<?php echo $num;?>" valign="middle" align="center">
<?php
		if ( ! empty($users_logged_in[$key]) ) echo $users_logged_in[$key] . " ist eingeloggt";
		else echo "<a href=\"index.php?action=sitterlogins&sitterlogin=" . urlencode($data) . "&sid=" . $sid . "\" target=\"_blank\">[einloggen]</a>";
?>
   <br><a href=\"javascript:Collapse('d<?php echo $key;?>');\"><img src="bilder/plus.gif" alt="" border="0" id="collapse_d<?php echo $key;?>"></a>
  </td>
  <td class="windowbg<?php echo $num;?>" valign="top">
   <?php echo ( empty($users_lastlogin_user[$key]) ) ? "": strftime($config_sitter_timeformat, $users_lastlogin[$key]) . " - " . $users_lastlogin_user[$key];?>
  </td>
 </tr>
 <tr id="row_d<?php echo $key;?>" style="display: none;">
  <td colspan="4" class="windowbg1" valign="top" align="center" style="width: 100%;">
<form method="POST" action="index.php?action=sitterliste&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="0" class="bordercolor">
 <tr>
  <td colspan="2" class="windowbg1" align="center">
   <b>Sitteraktivität</b>
  </td>
 </tr>
 <tr>
 <tr>
  <td class="windowbg1">
   Auftrag:<br>
   <i>Wenn du etwas gebaut hast,<br>bitte hier eintragen.</i>
  </td>
  <td class="windowbg1">
   <textarea name="auftrag" rows="3" style="width: 200;"></textarea>
  </td>
 </tr>
 <tr>
  <td colspan="2" class="windowbg1" align="center">
   <input type="hidden" name="newlog" value="true"><input type="hidden" name="sitterlogin" value="<?php echo $data;?>"><input type="submit" value="speichern" name="B1" class="submit">
  </td>
 </tr>
</table>
</form>
  </td>
 </tr>
<?php
	}
?>
</table>
<br>
<?php
}
?>