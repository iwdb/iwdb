<?php
/*****************************************************************************/
/* members.php                                                               */
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
	
$sql = "SELECT MAX(date) AS MDATE FROM " . $db_tb_punktelog;
$result = $db->db_query($sql)	
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

$lastreport = "";

if($row = $db->db_fetch_array($result)) {
  $lastreport = strftime("(letzte Aktualisierung: %d.%m.%y %H:%M:%S)", $row['MDATE']);
}
?>
<div class='doc_title'>Memberliste</div>
<div class='doc_centered_blue'><?php echo $lastreport; ?></div>

<form method="POST" action="index.php?action=members&graph=1&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<?php
$graph_typs = array (
   "gesamtp" => "GesamtP",
   "gebp" => "GebP",
   "fp" => "FP",
   "ptag" => "P / Tag"
);


// Graph Ausgabe //
$graph       = getVar('graph');
$graph_typ   = getVar('graph_typ');
$select_all  = getVar('select_all');
$select_none = getVar('select_none');
$fitthis     = getVar('fitthis');

$sql = "SELECT sitterlogin FROM " . $db_tb_user;
$sql .= " ORDER BY sitterlogin DESC";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

if (isset($debug)) {
echo "<div class='system_debug_blue'>";
echo "<br><pre>";
print_r($_POST);
echo "</pre><br>";
echo "</div>";
}	
	
if ( ! empty($graph) )
{
	while($row = $db->db_fetch_array($result))
	{
	    $testvar = getVar(('graph_' . str_replace(".", "%99", rawurlencode($row['sitterlogin']))));
			
		if ( ( $select_all == "true") || ( $testvar == "true" ) )
			$users[] = $row['sitterlogin'];
	}
	include("./includes/function_graph.php");
	if(!empty($users)) {
  	build_graph($users, $db_tb_punktelog, "user", "date", $graph_typ, $fitthis);
	}
?>
<p align="center">
Achsen optimieren? <input type="checkbox" name="fitthis" value="1"<?php echo ($fitthis) ? " checked": "";?>> 
<select name="graph_typ">
<?php
  foreach ($graph_typs as $key => $data)
		echo ($graph_typ == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
?>
</select> 
<input type="submit" value="Graph zeichnen" name="B1" class="submit"></p>
<?php
}
?>
<br>
<table border="0" cellpadding="2" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
<?php 	if ( $user_status == "admin" ) { ?>
 	<td class="titlebg" style="width:56px;" align="center">
    &nbsp;
  </td>
<?php } ?>
  <td class="titlebg" style="width:23%;" align="center">
   <a href="index.php?action=members&order=sitterlogin&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Username</b> <a href="index.php?action=members&order=sitterlogin&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a><br>
   <a href="index.php?action=members&order=budflesol&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a> <b>Spielart</b> <a href="index.php?action=members&order=budflesol&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a>
  </td>
  <td class="titlebg" style="width:4%;" align="center">
   <a href="index.php?action=members&order=allianz&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a><br><b>Alli</b><br><a href="index.php?action=members&order=allianz&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a>
  </td>
  <td class="titlebg" style="width:8%;" align="center">
   <a href="index.php?action=members&order=rang&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a><br><b>Rang</b><br><a href="index.php?action=members&order=rang&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a>
  </td>
  <td class="titlebg" style="width:8%;" align="center">
   <a href="index.php?action=members&order=gebp&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a><br><b>GebP</b><br><a href="index.php?action=members&order=gebp&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a>
  </td>
  <td class="titlebg" style="width:8%;" align="center">
   <a href="index.php?action=members&order=fp&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a><br><b>FP</b><br><a href="index.php?action=members&order=fp&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a>
  </td>
  <td class="titlebg" style="width:9%;" align="center">
   <a href="index.php?action=members&order=gesamtp&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a><br><b>GesamtP</b><br><a href="index.php?action=members&order=gesamtp&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a>
  </td>
  <td class="titlebg" style="width:8%;" align="center">
   <a href="index.php?action=members&order=ptag&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a><br><b>P/Tag</b><br><a href="index.php?action=members&order=ptag&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a>
  </td>
  <td class="titlebg" style="width:10%;" align="center">
   <a href="index.php?action=members&order=dabei&ordered=desc&sid=<?php echo $sid;?>"><img src="bilder/desc.gif" border="0" alt="desc"></a><br><b>dabei s.</b><br><a href="index.php?action=members&order=dabei&ordered=asc&sid=<?php echo $sid;?>"><img src="bilder/asc.gif" border="0" alt="asc"></a>
  </td>
  <td class="titlebg" style="width:20%;" align="center">
<?php
if ( $user_status == "admin" ) {
echo '<a href="index.php?action=members&order=adminsitten&ordered=desc&sid=' . $sid . '"><img src="bilder/desc.gif" border="0" alt="desc"></a> <b>Sitterrechte</b> <a href="index.php?action=members&order=adminsitten&ordered=asc&sid=' . $sid . '"><img src="bilder/asc.gif" border="0" alt="asc"></a>';
echo '<br><a href="index.php?action=members&order=status&ordered=desc&sid=' . $sid . '"><img src="bilder/desc.gif" border="0" alt="desc"></a> <b>Status</b> <a href="index.php?action=members&order=status&ordered=asc&sid=' . $sid . '"><img src="bilder/asc.gif" border="0" alt="asc"></a>';
}else echo '<a href="index.php?action=members&order=titel&ordered=desc&sid=' . $sid . '"><img src="bilder/desc.gif" border="0" alt="desc"></a><br><b>Titel</b><br><a href="index.php?action=members&order=titel&ordered=asc&sid=' . $sid . '"><img src="bilder/asc.gif" border="0" alt="asc"></a>';
?>
  </td>
  <td class="titlebg" style="width:2%;" align="center">
   &nbsp;
  </td>
 </tr>
<?php

//die Fleeter mit ihren Farben auslesen
$fletocolo = array();
$sql = "SELECT id,color FROM " . $db_tb_user . " WHERE budflesol LIKE 'Fleeter'";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while($row = $db->db_fetch_array($result))
{
		$fletocolo[urlencode($row['id'])] = $row['color'];
}

$count = 0;
$num = 1;
$users = array();

$order = getVar('order');
$order = ( empty($order) ) ? "sitterlogin": $order;
$ordered = getVar('ordered');
$ordered = ( empty($ordered) ) ? "ASC": $ordered;

if ( $order == "budflesol" ) $order = "budflesol " . $ordered . ", buddlerfrom";

$sql = "SELECT * FROM " . $db_tb_user;
if ($user_fremdesitten != "1")
{
	$sql .= " WHERE allianz='" . $user_allianz . "'";
}
$sql .= " ORDER BY " . $order . " " . $ordered;
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

while($row = $db->db_fetch_array($result))
{
	$row['rang'] = str_replace("Memberverwalter", "MV", $row['rang']);
	$row['rang'] = str_replace("interner HC", "iHC", $row['rang']);
	if ($count == 3) {
		$num = ($num == 1) ? 2: 1;
		$count = 1;
	}
	else $count++;
	
	$sitterlogin = urlencode($row['sitterlogin']);	
	
	
	if ( $row['budflesol'] != 'Fleeter' ) {
	  if (!empty($row['buddlerfrom'])) {
	    if (isset($fletocolo[urlencode($row['buddlerfrom'])])) {
	  	  $color = $fletocolo[urlencode($row['buddlerfrom'])];
    	} else {
	    	$sqlC = "SELECT color FROM " . $db_tb_user . " WHERE id = '".$row['buddlerfrom']."'";
	    	$resultC = $db->db_query($sqlC)
	      or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlC);
	    	$rowC = $db->db_fetch_array($resultC);
	    	$color = $rowC['color'];
    	}		
    } else {
      $color = "#000000";	
	  }
	} else {
		$color = $fletocolo[$sitterlogin];
	}	
?>
 <tr>
<?php 	if ( $user_status == "admin" ) { ?>
 	<td class="windowbg<?php echo $num;?>" valign="top">
 		<a href="index.php?action=profile&sitterlogin=<?php echo  $sitterlogin; ?>&sid=<?php echo $sid;?>">
 		  <img border="0" src="bilder/user-profil.gif" alt="P" title="Profil">	
 		</a>	
 		<a href="index.php?action=sitterlogins&sitterlogin=<?php echo  $sitterlogin; ?>&sid=<?php echo $sid;?>">
 		  <img border="0" src="bilder/user-login.gif" alt="L" title="Einloggen">	
 		</a>	
  </td>
<?php } ?>
  <td class="windowbg<?php echo $num;?>">
<?php
if ( $user_status == "admin" ) echo "<a href=\"index.php?action=profile&sitterlogin=" . urlencode($row['sitterlogin']) . "&sid=" . $sid . "\">" . $row['sitterlogin'] . "</a>";
else echo $row['sitterlogin'];
?>
   <br><font size="1"><i style="color:<?php echo $color;?>">[<?php echo $row['budflesol'];?><?php echo ($row['buddlerfrom']) ? " v. " . $row['buddlerfrom']: "";?>]</i></font>
  </td>
  <td class="windowbg<?php echo $num;?>" valign="top">
   <?php echo $row['allianz'];?>
  </td>
  <td class="windowbg<?php echo $num;?>" valign="top">
   <?php echo $row['rang'];?>
  </td>
  <td class="windowbg<?php echo $num;?>" align="right" valign="top">
   <?php echo $row['gebp'];?>
  </td>
  <td class="windowbg<?php echo $num;?>" align="right" valign="top">
   <?php echo $row['fp'];?>
  </td>
  <td class="windowbg<?php echo $num;?>" align="right" valign="top">
   <?php echo $row['gesamtp'];?>
  </td>
  <td class="windowbg<?php echo $num;?>" align="right" valign="top">
   <?php echo $row['ptag'];?>
  </td>
  <td class="windowbg<?php echo $num;?>" align="right" valign="top">
   <?php
      if (!empty($row['dabei'])) {
          echo strftime($config_members_timeformat, $row['dabei']);
      }
   ?>
  </td>
<!--  <td class="windowbg<?php echo $num;?>" valign="top"> -->
<?php
$output = "<td class=\"windowbg" . $num. "\" ";
if ( $user_status == "admin" ) {
	if  ( isset($row['adminsitten']) ) {
		$output .= "style=\"color:#0000FF;\" ";
		switch ($row['adminsitten']) {
		// Festlegung der Sitterrechte
			case SITTEN_ONLY_LOGINS: $output .= ">--"; break;
			case SITTEN_DISABLED: $output .= "abbr=\"login\">Sitten"; break;
			case SITTEN_BOTH: $output .= "abbr=\"both\">Aufträge & Sitten"; break;
			case SITTEN_ONLY_NEWTASKS: $output .= "abbr=\"newtask\">Aufträge"; break;
		}
		
	} else
		$output .= "style=\"color:#00FF00;\" abbr=\"both\">neuer Member";
	$output .= "<br><span style=\"color:#FF0000; font-style:italic;\">" . $row['status'] . "</span>";
}
else $output .= $row['titel'];
echo $output;
?>
  </td>
  <td class="windowbg<?php echo $num;?>" valign="top">
  <?php
    $tempname  = 'graph_' . str_replace(".", "%99", rawurlencode($row['sitterlogin']));
    $graphname = getVar($tempname); 
  ?><input type="checkbox" name="<?php echo $tempname?>" value="true"<?php echo ($select_none) ? "": (($select_all) ? " checked" : (($graphname) ?  " checked": ""));?>>
  </td> 
 </tr>
<?php
}
?>
</table>
<table border="0" cellpadding="2" cellspacing="1" style="width: 90%;">
 <tr>
  <td align="right">
   <a href="index.php?action=members&order=<?php echo $order;?>&ordered=<?php echo $ordered;?>&select_all=true&graph=<?php echo $graph;?>&graph_typ=<?php echo $graph_typ;?>&fitthis=<?php echo $fitthis;?>&sid=<?php echo $sid;?>">Alle auswaehlen</a> /
   <a href="index.php?action=members&order=<?php echo $order;?>&ordered=<?php echo $ordered;?>&select_none=true&graph=<?php echo $graph;?>&graph_typ=<?php echo $graph_typ;?>&fitthis=<?php echo $fitthis;?>&sid=<?php echo $sid;?>">Auswahl entfernen</a>
  </td>
 </tr>
<?php
if ( empty($graph) )
{
?>
 <tr>
  <td align="right">
   Achsen optimieren? <input type="checkbox" name="fitthis" value="1"<?php echo ($fitthis) ? " checked": "";?>> 
   <select name="graph_typ">
<?php
  foreach ($graph_typs as $key => $data)
		echo ($graph_typ == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
?>
   </select>
   <input type="submit" value="Graph zeichnen" name="B1" class="submit"><br>
  </td>
 </tr>
<?php
}
?>
</table>
</form>
