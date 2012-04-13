<?php
/*****************************************************************************/
/* sprengung.php                                                             */
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
<div class='doc_title'>Sprengung</div>
<br>
Hier könnt ihr nachsehen, wann die pösen Vogonen die nächsten Planeten sprengen um Platz für eine Hyperraum-Umgehungsstrasse zu schaffen.
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
  <td class="titlebg" style="width:15%;">
   <a href="index.php?action=planeten&amp;order=coords&amp;ordered=asc&amp;sid=<?=$sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Koordinaten</b> <a href="index.php?action=planeten&amp;order=coords&amp;ordered=desc&amp;sid=<?=$sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
  <td class="titlebg" style="width:25%;">
   <a href="index.php?action=planeten&amp;order=t2.budflesol&amp;ordered=asc&amp;sid=<?=$sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Objekttyp</b> <a href="index.php?action=planeten&amp;order=t2.budflesol&amp;ordered=desc&amp;sid=<?=$sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
  <td class="titlebg" style="width:25%;">
   <a href="index.php?action=planeten&amp;order=t2.budflesol&amp;ordered=asc&amp;sid=<?=$sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Sprengung</b> <a href="index.php?action=planeten&amp;order=t2.budflesol&amp;ordered=desc&amp;sid=<?=$sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
 </tr>
<?php
$order  = getVar('order');
$order  = ( empty($order) ) ? "coords": $order;
$ordered = getVar('ordered');
$ordered = ( empty($ordered) ) ? "ASC": $ordered;

if ( $order == "coords" ) $order = "t1.coords_gal " . $ordered . ", t1.coords_sys " . $ordered . ", t1.coords_planet";
if ( $order == "t2.budflesol" ) $order = "t2.budflesol " . $ordered . ", t2.buddlerfrom";

$sql = "SELECT * FROM " . $db_tb_scans . " ORDER BY " . $order . " " . $ordered;
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
  
$sitpre = "";
$num    = 0;

while($row = $db->db_fetch_array($result))
{
?>
 <tr>
  <td class="windowbg<?=$num;?>">
   <a href="index.php?action=showplanet&amp;coords=<?=$row['coords'];?>&amp;ansicht=auto&amp;sid=<?=$sid;?>"><?=$row['coords'];?></a>
  </td>
  <td class="windowbg<?=$num;?>">
    Steinklumpen
  </td>
  <td class="windowbg<?=$num;?>">
    1.1.1800
  </td>
 </tr>
<?php
}
?>
</table>