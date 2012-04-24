<?php
/*****************************************************************************/
/* profile_editplaneten.php                                                  */
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
<div class='doc_title'>eigene Planeten</div>
<?php
  $editplaneten = getVar('editplaneten');
	if ( ! empty($editplaneten) )
	{
		echo "<div class='system_notification'>Planetendaten aktualisiert.</div>";
		$sql = "SELECT t1.* FROM " . $db_tb_sitterauftrag . " as t1 LEFT JOIN " . $db_tb_sitterauftrag . " as t2 ON t1.id = t2.refid WHERE t2.refid is null AND t1.user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		while ($row = $db->db_fetch_array($result))
		{
			if ( $row['typ'] == "Gebaeude" ) dates($row['id'], $sitterlogin);
		}
	}
?>
<br>
<form method="POST" action="index.php?action=profile&uaction=editplaneten&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
 <tr>
  <td class="windowbg2" style="width:10%;">
   Koordinaten
  </td>
  <td class="windowbg2" style="width:20%;">
   Planetenname
  </td>
  <td class="windowbg2" style="width:10%;">
   Objekt
  </td>
  <td class="windowbg2" style="width:20%;">
   Schiffsbaudauermod.
  </td>
  <td class="windowbg2" style="width:20%;">
   Gebaeudebaudauermod.
  </td>
  <td class="windowbg2" style="width:10%;">
   BG-Farbe<br>[#Hexwert]
  </td>
  <td class="windowbg2" style="width:10%;">
   Sort.<br>[0-99]
  </td>
 </tr>
<?php
// Ausgabe der Presets und Loeschlink //
$sql = "SELECT coords, planetenname, objekt, dsmod, dgmod, planet_farbe, sortierung FROM " . $db_tb_scans .  " WHERE user LIKE '" . $sitterlogin . "'";

$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
{
	if ( ! empty($editplaneten) )
	{
    $temp = $row['coords'] . '_dsmod';
    $coords_dsmod = getVar($temp);

    $temp = $row['coords'] . '_dgmod';
    $coords_dgmod = getVar($temp);
    
    $temp = $row['coords'] . '_planet_farbe';
    $coords_planet_farbe = getVar($temp);
    
    $temp = $row['coords'] . '_sortierung';
    $coords_sortierung = getVar($temp);
    
		$sql = "UPDATE " . $db_tb_scans . " SET dsmod='" . str_replace(",", ".", $coords_dsmod) . "', dgmod='" . str_replace(",", ".", $coords_dgmod) . "', planet_farbe='" . str_replace(",", ".", $coords_planet_farbe) . "', sortierung='" . str_replace(",", ".", $coords_sortierung) . "' WHERE (coords='" . $row['coords'] . "' AND user LIKE '" . $sitterlogin . "')";
		$result_planet = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row['dsmod'] = str_replace(",", ".", $coords_dsmod);
		$row['dgmod'] = str_replace(",", ".", $coords_dgmod);
		$row['planet_farbe'] = str_replace(",", ".", $coords_planet_farbe);
		$row['sortierung'] = str_replace(",", ".", $coords_sortierung);
	}
?>
 <tr>
  <td class="windowbg1">
   <?php echo $row['coords'];?>
  </td>
  <td class="windowbg1">
   <?php echo $row['planetenname'];?>
  </td>
  <td class="windowbg1">
   <?php echo $row['objekt'];?>
  </td>
  <td class="windowbg1">
   <input type="text" name="<?php echo $row['coords'];?>_dsmod" value="<?php echo $row['dsmod'];?>" style="width: 100">
  </td>
  <td class="windowbg1">
   <input type="text" name="<?php echo $row['coords'];?>_dgmod" value="<?php echo $row['dgmod'];?>" style="width: 100">
  </td>
  <td class="windowbg1">
   <input type="text" name="<?php echo $row['coords'];?>_planet_farbe" value="<?php echo $row['planet_farbe'];?>" style="width: 60">
  </td>
  <td class="windowbg1">
   <input type="text" name="<?php echo $row['coords'];?>_sortierung" value="<?php echo $row['sortierung'];?>" style="width: 50">
  </td>
 </tr>
<?php
}
?>
  <tr>
    <td colspan="7" class="titlebg" align="center">
      <input type="hidden" name="sitterlogin" value="<?php echo $sitterlogin;?>"><input type="hidden" name="editplaneten" value="true"><input type="submit" value="speichern" name="B1" class="submit">
    </td>
  </tr>
</form>
</table>
<br> 
