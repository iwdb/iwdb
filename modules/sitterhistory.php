<?php
/*****************************************************************************/
/* sitterhistory.php                                                         */
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

if (basename($_SERVER['PHP_SELF']) != "index.php") {
	exit("Hacking attempt...!!");
}
if (!defined('IRA'))
	exit('Hacking attempt...');
	
if ( $user_adminsitten == SITTEN_DISABLED )
	die('Hacking attempt...');
	
$limit = getVar('limit');
if ( empty($limit) ) $limit = 20;

$selecteduser = getVar('selecteduser');
if( empty($selecteduser))
  $selecteduser = $user_sitterlogin;
	
doc_title("Sitterhistorie von " . $selecteduser);
echo "<br>\n";

start_form("sitterhistory");
echo "<input type='hidden' name='selecteduser' value='" . $selecteduser . "'>\n";
echo "maximal: <input type='text' name='limit' value='" . $limit . "' style='width: 50;'>\n";
echo "<input type='submit' value='anzeigen' name='B1' class='submit'>\n";
end_form();
?>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
  <td class="titlebg" colspan="4" align="center">
   <b>Was andere bei <?php echo $selecteduser;?> gemacht haben:</b>
  </td>
 </tr>
 <tr>
  <td class="titlebg" style="width:20%;">
   <b>Username</b>
  </td>
  <td class="titlebg" style="width:15%;">
   <b>Zeit</b>
  </td>
  <td class="titlebg" style="width:65%;">
   <b>Auftrag</b>
  </td>
 </tr>
<?php
// Auftraege durchgehen //
$sql = "SELECT * FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . 
       $selecteduser . "' ORDER BY date DESC LIMIT " . $limit;
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	
while($row = $db->db_fetch_array($result))
{
?>
 <tr>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" ) 
  echo "<a href=\"index.php?action=profile&sitterlogin=" . urlencode($row['fromuser']) .
	     "&sid=" . $sid . "\">" . $row['fromuser'] . "</a>";
else 
  echo $row['fromuser'];
?>
  </td>
  <td class="windowbg1">
   <?php echo strftime($config_sitter_timeformat, $row['date']);?>
  </td>
  <td class="windowbg1">
   <?php echo $row['action'];?>
  </td>
 </tr>
<?php
}
?>
</table>
<br>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
  <td class="titlebg" colspan="4" align="center">
   <b>Was <?php echo $selecteduser;?> bei anderen gemacht hat</b>
  </td>
 </tr>
 <tr>
  <td class="titlebg" style="width:20%;">
   <b>Username</b>
  </td>
  <td class="titlebg" style="width:15%;">
   <b>Zeit</b>
  </td>
  <td class="titlebg" style="width:65%;">
   <b>Auftrag</b>
  </td>
 </tr>
<?php
// Auftraege durchgehen //
$sql = "SELECT * FROM " . $db_tb_sitterlog . " WHERE fromuser = '" . $selecteduser . "' ORDER BY date DESC LIMIT " . $limit;
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while($row = $db->db_fetch_array($result))
{
?>
 <tr>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" ) 
  echo "<a href=\"index.php?action=profile&sitterlogin=" . urlencode($row['sitterlogin']) .
	     "&sid=" . $sid . "\">" . $row['sitterlogin'] . "</a>";
else 
  echo $row['sitterlogin'];
?>
  </td>
  <td class="windowbg1">
   <?php echo strftime($config_sitter_timeformat, $row['date']);?>
  </td>
  <td class="windowbg1">
   <?php echo $row['action'];?>
  </td>
 </tr>
<?php
}
?>
</table>
<br>