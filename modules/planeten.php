<?php
/*****************************************************************************/
/* planeten.php                                                              */
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
?>
<div class='doc_title'>Planetenliste</div>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
  <td class="titlebg" style="width:15%;">
   <a href="index.php?action=planeten&amp;order=coords&amp;ordered=asc&amp;sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Koordinaten</b> <a href="index.php?action=planeten&amp;order=coords&amp;ordered=desc&amp;sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
  <td class="titlebg" style="width:20%;">
   <a href="index.php?action=planeten&amp;order=t2.sitterlogin&amp;ordered=asc&amp;sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Username</b> <a href="index.php?action=planeten&amp;order=t2.sitterlogin&amp;ordered=desc&amp;sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
  <td class="titlebg" style="width:40%;">
   <a href="index.php?action=planeten&amp;order=t1.planetenname&amp;ordered=asc&amp;sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Planetenname</b> <a href="index.php?action=planeten&amp;order=t1.planetenname&amp;ordered=desc&amp;sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
  <td class="titlebg" style="width:25%;">
   <a href="index.php?action=planeten&amp;order=t2.budflesol&amp;ordered=asc&amp;sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Spielart</b> <a href="index.php?action=planeten&amp;order=t2.budflesol&amp;ordered=desc&amp;sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
 </tr>
<?php
$order  = getVar('order');
$order  = ( empty($order) ) ? "coords": $order;
$ordered = getVar('ordered');
$ordered = ( empty($ordered) ) ? "ASC": $ordered;

if ( $order == "coords" ) $order = "t1.coords_gal " . $ordered . ", t1.coords_sys " . $ordered . ", t1.coords_planet";
if ( $order == "t2.budflesol" ) $order = "t2.budflesol " . $ordered . ", t2.buddlerfrom";

$sql = "SELECT * FROM " . $db_tb_scans . " AS t1 INNER JOIN " . $db_tb_user . " AS t2 WHERE t1.user=t2.sitterlogin";
if (!$user_fremdesitten)
{
	$sql .= " AND t2.allianz='" . $user_allianz . "'";
}
$sql .= " AND t2.sitterlogin<>''";
$sql .= " ORDER BY " . $order . " " . $ordered;
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
  
$sitpre = "";
$num    = 0;

while($row = $db->db_fetch_array($result))
{
	if ($row['sitterlogin'] != $sitpre)
	{
		$num = ($num == 1) ? 2: 1;
		$sitpre = $row['sitterlogin'];
	}
?>
 <tr>
  <td class="windowbg<?php echo $num;?>">
   <a href="index.php?action=showplanet&amp;coords=<?php echo $row['coords'];?>&amp;ansicht=auto&amp;sid=<?php echo $sid;?>"><?php echo $row['coords'];?></a>
  </td>
  <td class="windowbg<?php echo $num;?>">
<?php
if ( $user_status == "admin" ) echo "<a href=\"index.php?action=profile&amp;sitterlogin=" . urlencode($row['sitterlogin']) . "&amp;sid=" . $sid . "\">" . $row['sitterlogin'] . "</a>";
else echo $row['sitterlogin'];
?>
  </td>
  <td class="windowbg<?php echo $num;?>"><a href="index.php?action=showplanet&amp;coords=<?php echo $row['coords'];?>&amp;ansicht=auto&amp;sid=<?php echo $sid;?>"><div class='doc_<?php 
	 if($row['objekt'] == "Kolonie") echo "black"; 
	 else if($row['objekt'] == "Kampfbasis") echo "red";
	 else if($row['objekt'] == "Sammelbasis") echo "green";
	 else if($row['objekt'] == "Artefaktbasis") echo "blue";
	 else echo "\"black\""; 	  
	 ?>'><?php echo $row['planetenname'];?> (<?php 
	 if($row['objekt'] == "Kolonie") echo "K"; 
	 else if($row['objekt'] == "Kampfbasis") echo "B";
	 else if($row['objekt'] == "Sammelbasis") echo "S";
	 else if($row['objekt'] == "Artefaktbasis") echo "A";
	 else echo "-"; 	  	  
	 ?>)</div></a>
  </td>
  <td class="windowbg<?php echo $num;?>">
    <?php echo $row['budflesol'];?><?php echo ($row['buddlerfrom']) ? " von: " . $row['buddlerfrom']: "";?>
  </td>
 </tr>
<?php
}
?>
</table>
<br/>
<b>K = Kolonie, B = Kampfbasis, S = Sammelbasis, A = Artefaktbasis</b> 
