<?php
/*****************************************************************************/
/* profile_editpreset.php                                                    */
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

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde. 
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") { 
	echo "Hacking attempt...!!"; 
	exit; 
}

if (!defined('IRA'))
	die('Hacking attempt...');
?>
<div class='doc_title'>eigene Presets</div>
<?php
$delid = getVar('delid');
if ( ! empty($delid) )
{
	$sql = "SELECT fromuser, name FROM " . $db_tb_preset . " WHERE id LIKE '" . $delid . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	if ( ( $row['fromuser'] == $user_sitterlogin ) || ( $user_status == "admin" ) )
	{
		$sql = "DELETE FROM " . $db_tb_preset . " WHERE id = '" . $delid . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		echo "<div class='system_notification'>Preset '" . $row['name'] . " (" . $row['fromuser'] . ")' geloescht.</div>";
	}
	else echo "<div class='system_notification'>Hack Attempt.</div>";
}
?>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
 <tr>
  <td class="windowbg2" style="width:20%;">
   Preset
  </td>
  <td class="windowbg2" style="width:20%;">
   Username
  </td>
  <td class="windowbg2" style="width:60%;">
   &nbsp;
  </td>
 </tr>
<?php
// Ausgabe der Presets und Loeschlink //
if ( ( $user_status == "admin" ) && ( $sitterlogin == $user_sitterlogin) ) $sql = "SELECT id, name, fromuser FROM " . $db_tb_preset .  " WHERE (fromuser = '" . $sitterlogin . "' OR fromuser = '') ORDER BY fromuser, name";
else $sql = "SELECT id, name, fromuser FROM " . $db_tb_preset .  " WHERE fromuser = '" . $sitterlogin . "'";

$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
{
?>
 <tr>
  <td class="windowbg1">
   <?=$row['name'];?>
  </td>
  <td class="windowbg1">
   <?=( empty($row['fromuser']) ) ? "<b>global</b>": $row['fromuser'];?>
  </td>
  <td class="windowbg1">
   <a href="index.php?action=profile&amp;uaction=editpresets&amp;delid=<?=$row['id'];?>&amp;sitterlogin=<?=urlencode($sitterlogin);?>&amp;sid=<?=$sid;?>"">loeschen</a>
  </td>
 </tr>
<?php
}
?>
</table>
<br>