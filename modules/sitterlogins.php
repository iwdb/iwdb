<?php
/*****************************************************************************/
/* sitterlogins.php                                                                */
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

if (!defined('IRA')) {
    exit('Hacking attempt...');
}
if (basename($_SERVER['PHP_SELF']) != "index.php") {
    exit("Hacking attempt...!!");
}

if  ( ( $user_adminsitten != SITTEN_BOTH ) && ( $user_adminsitten != SITTEN_ONLY_LOGINS ) )
    die('Hacking attempt...');

function NumToStaatsform($num) {
	if ($num == 1) {
        return 'Diktator';
    } elseif ($num == 2) {
        return 'Monarch';
    } elseif ($num == 3) {
        return 'Demokrat';
    } elseif ($num == 4) {
        return 'Kommunist';
    } else {
        return 'Barbar';
    }
}

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
$sql = "SELECT sitterlogin FROM " . $db_tb_user . " WHERE sitterpwd <> '' " . (($user_status == "admin") ? "": "AND sitten = '1' " );
$sql .= "ORDER BY sitterlogin ASC";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while($row = $db->db_fetch_array($result))
{
	$sitterlogins[] = $row['sitterlogin'];
}
?>
<font style="font-size: 22px; color: #004466">Sitterlogins</font><br>
<?php
if ( ! empty($newlog) )
{
	echo "<br><font color=\"#FF0000\"><b>Auftrag gespeichert.</b></font><br>";
}
?>
<br>
<form method="POST" action="index.php?action=sitterlogins&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor">
 <tr>
  <td class="titlebg" colspan="2" align="center">
   <b>Sitteraktivität</b>
  </td>
 </tr>
 <tr>
  <td class="windowbg1">
   User:
   <select name="sitterlogin" style="width: 200;">
<?php
  foreach ($sitterlogins as $key => $data)
		echo " <option value=\"" . $data . "\">" . $data . "</option>\n";
?>
   </select>
  <td class="windowbg1">
   <textarea name="auftrag" rows="3" style="width: 400;">Auftrag</textarea>
  </td>
 </tr>
 <tr>
  <td class="titlebg" colspan="2" align="center">
   <input type="hidden" name="newlog" value="true"><input type="submit" value="speichern" name="B1" class="submit">
  </td>
 </tr>
</table></form>
<br>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 95%;">
 <tr>
  <td class="titlebg" style="width:25%;">
   <a href="index.php?action=sitterlogins&order=sitterlogin&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Username</b> <a href="index.php?action=sitterlogins&order=sitterlogin&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
  <td class="titlebg" style="width:15%;">
   <a href="index.php?action=sitterlogins&order=sitterpunkte&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Aktivität</b> <a href="index.php?action=sitterlogins&order=sitterpunkte&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="asc"></a>
  </td>
  <td class="titlebg" style="width:30%;">
   <b>Sitterlogin</b>
  </td>
  <td class="titlebg" style="width:30%;">
   <b>Besonderheiten</b>
  </td>
  <td class="titlebg" style="width:30%;">
   <a href="index.php?action=sitterlogins&order=lastlogin&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>letzter Login</b> <a href="index.php?action=sitterlogins&order=lastlogin&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
 </tr>
<?php
$count = 0;
$users = array();

$order = getVar('order');
$order = ( empty($order) ) ? "sitterpunkte": $order;
$ordered = getVar('ordered');
$ordered = ( empty($ordered) ) ? SORT_DESC: ( ( $ordered == "desc" ) ? SORT_DESC: SORT_ASC);

$sql = "SELECT sitterlogin, sitterpwd, sitten, sitterpunkte, peitschen, staatsform, ikea, sittercomment, iwsa FROM " . $db_tb_user;
if (!$user_fremdesitten) {
	$sql .= " WHERE allianz='" . $user_allianz . "' ";
}
$sql .= " ORDER BY sitterlogin ASC";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
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
		unset($row_lastlogin);
		while ($row_lastlogins = $db->db_fetch_array($result_lastlogin))
		{
			if ( $row_lastlogins['date'] == $row_lastlogins['MAX(date)'] ) $row_lastlogin = $row_lastlogins;
		}

		$users_sitterpunkte[$count] = $row['sitterpunkte'] + $config_sitterpunkte_friend * $db->db_num_rows($result_punkte);
		$users_sitterpunkte_anz[$count] = $row['sitterpunkte'] .  " [+ " . ($config_sitterpunkte_friend * $db->db_num_rows($result_punkte)) . "]";
		$users_sitterlogin[$count] = $row['sitterlogin'];
		$users_sitterpeitschen[$count] = $row['peitschen'];
		$users_sitterstaatsform[$count] = $row['staatsform']; 
		$users_sitterikea[$count] = $row['ikea'];
		$users_sitteriwsa[$count] = $row['iwsa'];
		$comments = explode("\n", $row['sittercomment']);
		$users_sittercomment[$count] = trim($comments[0]);
		$users_sitten[$count] = $row['sitten'];
		if (isset($row_lastlogin)) {
			$users_lastlogin[$count] = $row_lastlogin['MAX(date)'];
			$users_lastlogin_user[$count] = $row_lastlogin['fromuser'];
			$users_logged_in[$count] = ( ( $row_lastlogin['MAX(date)'] > ($config_date - $config_sitterlogin_timeout) ) && ( $row_lastlogin['fromuser'] != $user_sitterlogin ) ) ? $row_lastlogin['fromuser'] : "";
		} else {
			$users_lastlogin[$count] = 0;
			$users_lastlogin_user[$count] = '';
			$users_logged_in[$count] = '';
		}
		$count++;
	}
}

if($count > 0) {
if ( ( $order == "sitterlogin" ) && ($ordered == SORT_DESC) )
	krsort($users_sitterlogin);
if ( $order == "sitterpunkte" )
	array_multisort($users_sitterpunkte, $ordered, SORT_NUMERIC, $users_sitterlogin, $users_sitterpunkte_anz, $users_lastlogin, $users_lastlogin_user, $users_logged_in, $users_sitterpeitschen, $users_sitterstaatsform, $users_sitterikea, $users_sitteriwsa, $users_sittercomment, $users_sitten);
if ( $order == "lastlogin" )
	array_multisort($users_lastlogin, $ordered, SORT_NUMERIC, $users_sitterlogin, $users_sitterpunkte, $users_sitterpunkte_anz, $users_lastlogin_user, $users_logged_in, $users_sitterpeitschen, $users_sitterstaatsform, $users_sitterikea, $users_sitteriwsa, $users_sittercomment, $users_sitten);

$count = 0;
$num = 1;
foreach ($users_sitterlogin as $key => $data)
{
	if ($count == 3) {
		$num = ($num == 1) ? 2: 1;
		$count = 1;
	}
	else $count++;
?>
 <tr>
  <td class="windowbg<?php echo $num;?>" valign="top">
<?php
	if ( $user_status == "admin" ) echo "<a href=\"index.php?action=profile&sitterlogin=" . urlencode($data) . "&sid=" . $sid . "\">" . $data . "</a>";
	else echo $data;
	if ( ! empty ($users_sittercomment[$key]) ) echo "<br><font size=\"1\"><i>[" . $users_sittercomment[$key] . "]</i></font>";
?>
  </td>
  <td class="windowbg<?php echo $num;?>" valign="top">
   <?php echo ( $users_sitterpunkte[$key] > (3 * round($row_avg['AVG(sitterpunkte)']) ) ) ? "<img src=\"bilder/star1.gif\" border=\0\" style=\"vertical-align:middle;\">": ( ( $users_sitterpunkte[$key] > (2 * round($row_avg['AVG(sitterpunkte)']) ) ) ? "<img src=\"bilder/star2.gif\" border=\0\" style=\"vertical-align:middle;\">" : ( ( $users_sitterpunkte[$key] > round($row_avg['AVG(sitterpunkte)'] ) ) ? "<img src=\"bilder/star3.gif\" border=\0\" style=\"vertical-align:middle;\">" : ""));?>
   <?php echo $users_sitterpunkte_anz[$key];?>
  </td>
  <td class="windowbg<?php echo $num;?>" valign="top">
<?php
	if ( ! empty($users_logged_in[$key]) ) 
	  echo "<b><font color='#ff0000'>" . $users_logged_in[$key] . " ist eingeloggt </font></b>".
		     "<br/><a href=\"index.php?action=sitterlogins&sitterlogin=" . urlencode($data) . "&sid=" . $sid .
				 "\" target=\"_blank\" onclick=\"return confirmlink(this, " .
				 "'Jemand ist gerade im Account eingeloggt. Trotzdem einloggen?'".
				 ")\">[trotzdem einloggen]</a>" . 
				 "<td class='windowbg".$num."' valign='top'>".
				 (( $users_sitterpeitschen[$key] == "1" ) ? " <i>Meister d. Peitschen<br/></i>": "").
				 (( !empty($users_sitterstaatsform[$key]) ) ? " <i>Staatsform: ".NumToStaatsform($users_sitterstaatsform[$key])."</i><br/>": "").
				 (( $users_sitterikea[$key] == "M" ) ? " <i>Meister d. Ikea</i><br/>": "").
				 (( $users_sitterikea[$key] == "L" ) ? " <i>Lehrling d. Ikea</i><br/>": "").
				 (( $users_sitteriwsa[$key] == "1" ) ? " <i>IWSA/IWBP-Account<br/></i>": "").
				 "</td>";
	elseif ( ( ($user_status == "admin") OR ($user_status == "SV") ) && ( empty($users_sitten[$key]) ) ) 
	  echo "<a href=\"index.php?action=sitterlogins&sitterlogin=" . urlencode($data) .
		     "&sid=" . $sid . "\" target=\"_blank\" onclick=\"return confirmlink(this, ".
				 "'Dieser User hat das Sitten deaktiviert. Trotzdem einloggen?'".
				 ")\">[sitten deaktiviert - einloggen]</a> " . 
				 "<a href=\"index.php?action=sitterauftrag&sitterid=" .
				 urlencode($data) . "&sid=" . $sid . "\"><img src=\"bilder/file_new_s.gif\" border=\"0\" " .
				 "alt=\"Sitterauftrag erstellen\" title=\"Sitterauftrag erstellen\"></a>" . 
				 " <a href=\"index.php?action=sitterhistory&selecteduser=" .
				 urlencode($data) . "&sid=" . $sid . "\"><img src=\"bilder/file_history.gif\" border=\"0\" " .
				 "alt=\"Sitterhistorie anschauen\" title=\"Sitterhistorie anschauen\"></a>" . 
         "<td class='windowbg".$num."' valign='top'>".
				 (( $users_sitterpeitschen[$key] == "1" ) ? " <i>Meister d. Peitschen<br/></i>": "").
				 (( !empty($users_sitterstaatsform[$key]) ) ? " <i>Staatsform: ".NumToStaatsform($users_sitterstaatsform[$key])."</i><br/>": "").
				 (( $users_sitterikea[$key] == "M" ) ? " <i>Meister d. Ikea</i><br/>": "").
				 (( $users_sitterikea[$key] == "L" ) ? " <i>Lehrling d. Ikea</i><br/>": "").
				 (( $users_sitteriwsa[$key] == "1" ) ? " <i>IWSA/IWBP-Account<br/></i>": "").
				 "</td>";
	else 
	  echo "<a href=\"index.php?action=sitterlogins&sitterlogin=" . urlencode($data) .
		     "&sid=" . $sid . "\" target=\"_blank\">[jetzt einloggen]</a>&nbsp;" .
	       "<a href=\"index.php?action=sitterauftrag&sitterid=" . urlencode($data) .
				 "&sid=" . $sid . "\"><img src=\"bilder/file_new_s.gif\" border=\"0\" " .
				 "alt=\"Sitterauftrag erstellen\" title=\"Sitterauftrag erstellen\"></a>" .
				 " <a href=\"index.php?action=sitterhistory&selecteduser=" .
				 urlencode($data) . "&sid=" . $sid . "\"><img src=\"bilder/file_history.gif\" border=\"0\" " .
				 "alt=\"Sitterhistorie anschauen\" title=\"Sitterhistorie anschauen\"></a>" . 
				 "<td class='windowbg".$num."' valign='top'>".
				 (( $users_sitterpeitschen[$key] == "1" ) ? " <i>Meister d. Peitschen<br/></i>": "").
				 (( !empty($users_sitterstaatsform[$key]) ) ? " <i>Staatsform: ".NumToStaatsform($users_sitterstaatsform[$key])."</i><br/>": "").
				 (( $users_sitterikea[$key] == "M" ) ? " <i>Meister d. Ikea</i><br/>": "").
				 (( $users_sitterikea[$key] == "L" ) ? " <i>Lehrling d. Ikea</i><br/>": "").
				 (( $users_sitteriwsa[$key] == "1" ) ? " <i>IWSA/IWBP-Account<br/></i>": "").
				 "</td>";
?>

  </td>
  <td class="windowbg<?php echo $num;?>" valign="top" style="white-space:nowrap;">
   <?php echo ( empty($users_lastlogin_user[$key]) ) ? "": strftime($config_sitter_timeformat, $users_lastlogin[$key]) . "<br> von: " . $users_lastlogin_user[$key];?>
  </td>
 </tr>
<?php
}
} else { // $count == 0
?>
 <tr>
  <td class="windowbg1" valign="center" colspan="4" align="center" height="80">Keine Sitterdaten gefunden!</td>
 </tr>
<?php
}
?>
</table>
<br>
