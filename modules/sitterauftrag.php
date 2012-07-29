<?php
/*****************************************************************************/
/* sitterauftrag.php                                                         */
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

//Definition des Modulnamens
$modulname  = "sitterauftrag";

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	exit("Hacking attempt...!!");
}

if ( ( $user_adminsitten != SITTEN_BOTH ) && ( $user_adminsitten != SITTEN_ONLY_NEWTASKS ) )
	die('Hacking attempt...');

if (@include("./config/m_research.cfg.php")) {
    if(!defined('RESEARCH')) {
        define('RESEARCH', TRUE);
    }
}
  
//if(defined('RESEARCH') && (RESEARCH === TRUE))
// 	include('includes/research_functions.php');

function dauer($zeit)
{
	$tage = floor($zeit / 86400);
	$return = ($tage > 0) ? $tage . " Tage, ": "";
	$stunden = floor(($zeit - $tage * 86400) / 3600);
	$minuten = ($zeit - $tage * 86400 - $stunden * 3600) / 60;
	$return .= str_pad($stunden, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minuten, 2, "0", STR_PAD_LEFT);
	return $return;
}

// User fest //

$id = getVar('sitterid');
$id = ( empty($id) ? $user_sitterlogin : $id );
$serie = "";
$geb = 0;

$differentid = !($user_sitterlogin == $id);
$editauftrag = getVar('editauftrag');

// speichere Planetendaten in Array //
$sql = "SELECT coords, planetenname, dgmod FROM " . $db_tb_scans .  " WHERE user = '" . $id . "' ORDER BY sortierung,coords_gal ASC, coords_sys ASC, coords_planet";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
{
	if ( empty($row['dgmod']) ) $row['dgmod'] = 1;
	$planets[$row['coords']] = $row['planetenname'];
	$planetsmod[$row['coords']] = $row['dgmod'];
}

// hole Auftragsdaten von Parent -> refid bei Einfuegen, Daten fuer "anhängen"
$parentid = getVar('parentid');
$delid = getVar('delid');

if ( ! empty($parentid) )
{
	$sql = "SELECT refid, date, date_b1, date_b2, date_b2, planet, bauschleife, schieben, typ, bauid FROM " . $db_tb_sitterauftrag . " WHERE id='" . $parentid . "'";
	$result_parent = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_parent = $db->db_fetch_array($result_parent);

	$refid = $row_parent['refid'];
	// Daten fuer "anhängen" //
	if ( ( empty($delid) ) && ( empty($editauftrag) ) )
	{
		$sql = "SELECT dauer FROM " . $db_tb_gebaeude . " WHERE id='" . $row_parent['bauid'] . "'";
		$result_geb = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_geb = $db->db_fetch_array($result_geb);

		$serie = 1;
		$thisid = $parentid;
		$date = $row_parent['date'];
		$date_b1 = $row_parent['date_b1'];
		$date_b2 = $row_parent['date_b2'];
		$planet = $row_parent['planet'];
		$bauschleife = $row_parent['bauschleife'];
		$schieben = $row_parent['schieben'];
		$typ = $row_parent['typ'];
		$geb = $row_parent['bauid'];
	}
}
else
{
	$refid = "";
}

// Parameter ermitteln
$params = array(
	'order' => getVar('order'),
	'orderd' => getVar('orderd'),
);

// Parameter validieren
if (empty($params['order'])) 
	$params['order'] = 'date';
if ($params['orderd'] != 'asc' && $params['orderd'] != 'desc')
	$params['orderd'] = 'asc';

// neuer Auftrag oder Auftrag aktualisieren //
$umenu = getVar('umenu');
if ( ( ! empty($editauftrag) ) && ( empty($umenu) ) )
{
	$auftragid = getVar('auftragid');
	$typ = getVar('typ');

	$geb = getVar('geb');
	$schiff = getVar('schiff');
	$schiffanz = getVar('schiffanz');
	$auftrag = getVar('auftrag');

	$bis_array = array("bis:","bis");
	$date = str_replace($bis_array, "", getVar('date'));
	$date_b1 = str_replace($bis_array, "", getVar('date_b1'));
	$date_b2 = str_replace($bis_array, "", getVar('date_b2'));
	$date_parse = getVar('date_parse');

	$planet = getVar('planet');
	$bauschleife = getVar('bauschleife');
	$schieben = getVar('schieben');

	$serie = getVar('serie');

if( defined('RESEARCH') && (RESEARCH === TRUE)) {
	$resid = getVar('researchid');
	if(empty($auftrag) && !empty($resid)) {
	  $auftrag = find_research_name($resid);
	}
}
	
	if ( ! empty($date_parse) )
	{
		$date_parse = timeimport($date_parse, $planet);
		$date = $date_parse['date'];
		$date_b1 = empty($date_parse['date_b1']) ? $date_parse['date']: $date_parse['date_b1'];
		$date_b2 = empty($date_parse['date_b2']) ? $date_b1: $date_parse['date_b2'];
	}
	else
	{
		if ( ! empty($date) ) {
			$datetime = explode(" ", trim($date)); $date_d = explode(".", $datetime[0]); $date_t = explode(":", $datetime[1]);
			if ( (isset($date_t[0])) && (isset($date_t[1])) && ( $date_t[0] >= 0 ) && ( $date_t[0] <= 24 ) && ( $date_t[1] >= 0 ) && ( $date_t[1] < 60 ) && ( $date_d[1] >= 1 ) && ( $date_d[1] <= 12 ) && ( $date_d[0] >= 1 ) && ( $date_d[0] <= 31 ) )
				$date = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
		}

		if ( ! empty($date_b1) )
		{
			$datetime = explode(" ", trim($date_b1)); $date_d = explode(".", $datetime[0]); $date_t = explode(":", $datetime[1]);
			if ( (isset($date_t[0])) && (isset($date_t[1])) && ( $date_t[0] >= 0 ) && ( $date_t[0] <= 24 ) && ( $date_t[1] >= 0 ) && ( $date_t[1] < 60 ) && ( $date_d[1] >= 1 ) && ( $date_d[1] <= 12 ) && ( $date_d[0] >= 1 ) && ( $date_d[0] <= 31 ) )
				$date_b1 = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
		}
		else $date_b1 = $date;
	
		if ( ! empty($date_b2) )
		{
			$datetime = explode(" ", trim($date_b2)); $date_d = explode(".", $datetime[0]); $date_t = explode(":", $datetime[1]);
			if ( (isset($date_t[0])) && (isset($date_t[1])) && ( $date_t[0] >= 0 ) && ( $date_t[0] <= 24 ) && ( $date_t[1] >= 0 ) && ( $date_t[1] < 60 ) && ( $date_d[1] >= 1 ) && ( $date_d[1] <= 12 ) && ( $date_d[0] >= 1 ) && ( $date_d[0] <= 31 ) )
				$date_b2 = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
		}
		else $date_b2 = $date_b1;
	}

	if ( $bauschleife != "1" )
	{
	 $date_b1 = $date;
	 $date_b2 = $date;
	}

	$bauid = ( $typ == "Schiffe" ) ? $schiff: ( ( $typ == "Gebaeude" ) ? $geb: "" );

	if ( ( $date < $config_date - $config_sitterauftrag_timeout ) || ( $date_b1 < $config_date - $config_sitterauftrag_timeout )  || ( $date_b2 < $config_date - $config_sitterauftrag_timeout ) )
		$alert = "<div class='system_error'>Ungültiger Zeitpunkt!</div>";
	else
	{
		if ( empty($auftragid) )
		{
			// neuer Eintrag //
if( defined('RESEARCH') && (RESEARCH === TRUE) && !empty($resid)) {
			$sql = "INSERT INTO " . $db_tb_sitterauftrag . " (date, date_b1, date_b2, user, byuser, planet, auftrag, bauid, bauschleife, schieben, schiffanz, refid, typ, resid) VALUES ('" . $date . "', '" . $date_b1 . "', '" . $date_b2 . "', '" . $id . "', '" . $user_sitterlogin . "', '" . $planet . "', '" . $auftrag . "', '" . $bauid . "', '" . $bauschleife . "', '" . $schieben . "', '" . $schiffanz . "', '" . $refid . "', '" . $typ . "', " . $resid .")";
} else {
			$sql = "INSERT INTO " . $db_tb_sitterauftrag . " (date, date_b1, date_b2, user, byuser, planet, auftrag, bauid, bauschleife, schieben, schiffanz, refid, typ) VALUES ('" . $date . "', '" . $date_b1 . "', '" . $date_b2 . "', '" . $id . "', '" . $user_sitterlogin . "', '" . $planet . "', '" . $auftrag . "', '" . $bauid . "', '" . $bauschleife . "', '" . $schieben . "', '" . $schiffanz . "', '" . $refid . "', '" . $typ . "')";
}
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$thisid = mysql_insert_id();
			$alert = "<div class='system_notification'>Sitterauftrag erstellt.</div>";
			dates($thisid, $id);

			// refid bei Parent aktualisieren //
			if ( ! empty($parentid) )
			{
				$sql = "UPDATE " . $db_tb_sitterauftrag . " SET refid = '" . $thisid . "' WHERE id = '" . $parentid . "'";
				$result = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			}

			// falls Serie noch nicht zuende, Menu ausgeben //
			if ( ! empty($serie) ) {
				$umenu = 1;
			}
		} else {
			// Eintrag updaten //
            if( defined('RESEARCH') && (RESEARCH === TRUE) && !empty($resid)) {
			    $sql = "UPDATE " . $db_tb_sitterauftrag . " SET date = '" . $date . "', date_b1 = '" . $date_b1 . "', date_b2 = '" . $date_b2 . "', user = '" . $id . "', planet = '" . $planet . "', auftrag  = '" . $auftrag . "', bauid = '" . $bauid . "', bauschleife = '" . $bauschleife . "', schieben = '" . $schieben . "', schiffanz = '" . $schiffanz . "', resid='" . $resid . "' WHERE id = '" . $auftragid . "'";
            } else {
			    $sql = "UPDATE " . $db_tb_sitterauftrag . " SET date = '" . $date . "', date_b1 = '" . $date_b1 . "', date_b2 = '" . $date_b2 . "', user = '" . $id . "', planet = '" . $planet . "', auftrag  = '" . $auftrag . "', bauid = '" . $bauid . "', bauschleife = '" . $bauschleife . "', schieben = '" . $schieben . "', schiffanz = '" . $schiffanz . "' WHERE id = '" . $auftragid . "'";
            }
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$alert = "<div class='system_notification'>Sitterauftrag aktualisiert.</div>";
			if ( empty($parentid) ) {
                dates($auftragid, $id);
            } else {
                dates($parentid, $id);
            }
		}
	}
}

$delserie = getVar('delserie');
if ( ! empty($delserie) )
{
	// id im parent aktualisieren //
	$sql = "UPDATE " . $db_tb_sitterauftrag . " SET refid = '0' WHERE refid = '" . $delserie . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	$alert = "<div class='system_notification'>Serienelement ausgegliedert.</div>";
}

if ( ! empty($delid) )
{
	// nachfolgende zeiten aktualisieren //
	$sql = "SELECT id FROM " . $db_tb_sitterauftrag . " WHERE refid = '" . $delid . "'";
	$result_bev = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_bev = $db->db_fetch_array($result_bev);

	// id im parent aktualisieren //
	$sql = "UPDATE " . $db_tb_sitterauftrag . " SET refid = '" . $row_parent['refid'] . "' WHERE refid = '" . $delid . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	// löschen //
	$sql = "DELETE FROM " . $db_tb_sitterauftrag . " WHERE user = '" . $id . "' AND id = '" . $delid . "'";
	$result_del = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	dates($row_bev['id'], $id);

	$alert = "<div class='system_notification'>Sitterauftrag gelöscht.</div>";
}

// Übersicht über eigene Aufträge //
if ( empty($umenu) )
{
?>
<br>
<table border="0" cellpadding="0" cellspacing="1" class="bordercolor">
 <tr> 
  <td class="menutop" align="center">
   <a href="index.php?action=sitterauftrag&typ=Gebaeude&umenu=1&sitterid=<?php echo urlencode($id);?>&sid=<?php echo $sid;?>">[Gebäude]</a>
  </td>
  <td class="menutop" align="center">
   <a href="index.php?action=sitterauftrag&typ=Schiffe&umenu=1&sitterid=<?php echo urlencode($id);?>&sid=<?php echo $sid;?>">[Schiffe]</a>
  </td>
  <td class="menutop" align="center">
   <a href="index.php?action=sitterauftrag&typ=Forschung&umenu=1&sitterid=<?php echo urlencode($id);?>&sid=<?php echo $sid;?>">[Forschung]</a>
  </td>
  <td class="menutop" align="center">
   <a href="index.php?action=sitterauftrag&typ=Sonstiges&umenu=1&sitterid=<?php echo urlencode($id);?>&sid=<?php echo $sid;?>">[Sonstiges]</a>
  </td>
 </tr>
</table>
<br>
<br>
<?php
if($id == $user_sitterlogin ) {
?>
<font style="font-size: 22px; color: #004466">meine Sitteraufträge</font><br>
<?php
} else {
?>
<font style="font-size: 22px; color: #004466">Sitteraufträge von <?php echo $id;?></font><br>
<?php
}
	echo ( empty($alert) ) ? "": $alert;
?>
<br>
<form method="POST" action="index.php?action=sitterauftrag&sitterid=<?php echo urlencode($id);?>&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
  <td class="titlebg" colspan="4" align="center">
   <b>schnell hinzufügen</b>
  </td>
 </tr>
 <tr>
  <td class="windowbg1" style="width:15%;">
   Planet: 
   <select name="planet" style="width: 200px;">
<?php
  foreach ($planets as $key => $data)
		echo " <option value='" . $key . "'>[" . $key . "] " . $data . "</option>\n";
?>
   </select>
  </td>
  <td class="windowbg1" style="width:15%;">
   Zeit:
   <input type="text" name="date" value="" style="width: 200px;">
  </td>
  <td class="windowbg1" style="width:15%;">
   Bauschleife:
   <input type="checkbox" name="bauschleife" value="1"<?php echo ($user_peitschen) ? " checked": "";?>>
  </td>
  <td class="windowbg1" style="width:15%;">
   <textarea name="auftrag" rows="2" cols="25" style="width: 200px;">Auftrag</textarea>
  </td>
 </tr>
 <tr>
  <td class="titlebg" colspan="4" align="center">
   <input type="hidden" name="typ" value="Sonstiges"><input type="hidden" name="editauftrag" value="true"><input type="submit" value="speichern" name="B1" class="submit">
  </td>
 </tr>
</table>
<br>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
  <td class="titlebg" style="width:3%;">
   &nbsp;
  </td>
  <td class="titlebg" style="width:15%;">
  <?php
  echo makelink(
		array(
			'order' => 'planet',
			'orderd' => 'asc'
		),
		"<img src='./bilder/asc.gif' border='0' alt=''>");
	?>
   <b>Planet</b>
   <?php
   echo makelink(
   		array(
			'order' => 'planet',
			'orderd' => 'desc'
		),
		"<img src='./bilder/desc.gif' border='0' alt=''>");

	?>
  </td>
  <td class="titlebg" style="width:15%;">
    <?php
  echo makelink(
		array(
			'order' => 'date',
			'orderd' => 'asc'
		),
		"<img src='./bilder/asc.gif' border='0' alt=''>");
	?>
   <b>Zeit</b>
   <?php
   echo makelink(
   		array(
			'order' => 'date',
			'orderd' => 'desc'
		),
		"<img src='./bilder/desc.gif' border='0' alt=''>");
	?>
  </td>
  <td class="titlebg" style="width:10%;">
   <b>Typ</b>
  </td>
  <td class="titlebg" style="width:47%;">
   <b>Auftrag</b>
  </td>
  <td class="titlebg" style="width:10%;">
   &nbsp;
  </td>  
 </tr>
<?php
	// Ausgabe des Auftrags //
	
	if ($params['order']=="planet") {
		$params['order']="t3.sortierung ".$params['orderd'];
	}
	else {
		$params['order']="t1.date_b1 ".$params['orderd'].", t1.date ".$params['orderd'];	
	}

	$sql =  "SELECT t1.*,t3.planet_farbe,t3.sortierung FROM " . $db_tb_sitterauftrag . " as t1";
	$sql .= " LEFT JOIN " . $db_tb_sitterauftrag . " as t2";
	$sql .= " ON t1.id = t2.refid";
	$sql .= " LEFT JOIN " . $db_tb_scans . " as t3";
	$sql .= " ON t1.planet = t3.coords";
	$sql .= " WHERE t2.refid is null AND t1.user='" . $id . "' ORDER BY " . $params['order'];
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while((isset($result_act) && ($row = $db->db_fetch_array($result_act))) || ($row = $db->db_fetch_array($result)))
	{
		$sql = "SELECT t1.*,t2.planet_farbe FROM " . $db_tb_sitterauftrag . " as t1";
		$sql .= " LEFT JOIN " . $db_tb_scans . " as t2";
		$sql .= " ON t1.planet = t2.coords";
		$sql .= " WHERE id = '" . $row['refid'] . "'";
		$result_act = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$sql = "SELECT id FROM " . $db_tb_sitterauftrag . " WHERE refid = '" . $row['id'] . "'";
		$result_bev = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_bev = $db->db_fetch_array($result_bev);

		$bauschleifenmod = 1;
		if ( empty($user_peitschen) )
		{
			if ( $row['date_b1'] <> $row['date'] ) $bauschleifenmod = 1.1;
			if ( $row['date_b2'] <> $row['date_b1'] ) $bauschleifenmod = 1.2;
		}

    $planet = $row['planet'];
    if(!empty($planet) && isset($planetsmod[$planet])) {
      $planetmod = $planetsmod[$planet];
    } else {
      $planetmod = 1;
    }
    
		$num = ( $row['date'] < $config_date) ? 2: 1;
		$row['auftrag'] = auftrag($row['typ'], $row['bauschleife'], $row['bauid'], $row['auftrag'], $row['schiffanz'], $planetmod, $row['user'], $bauschleifenmod);
?>
 <tr>
  <td class="windowbg<?php echo $num;?>" align="center">
<?php
    if( !$differentid  || ( ($user_status == "admin") OR ($user_status == "SV") ) || ($user_sitterlogin == $row['ByUser'])) {
        echo ( empty($row_bev['id']) ) ?
            "<img src='bilder/point.gif' border='0' alt=''>" :
            "<a href='index.php?action=sitterauftrag&delserie=" . $row['id'] . "&sid=" . $sid. "'><img src='bilder/plus.gif' border='0'></a>";
    } else {
        echo "<img src='bilder/point.gif' border='0' alt=''>";
	}
?>
  </td>
  <?php
  	if (!isset($row['planet_farbe'])) { $row['planet_farbe']="white"; }
  ?>
  <td class="windowbg<?php echo $num;?>" style="background-color:<?php echo $row['planet_farbe']?>;">
<?php 
if(!empty($row['planet']) && isset($planets[$row['planet']]))
  echo $planets[$row['planet']] . " [" . $row['planet']. "]\n";
else 
  echo "[---]\n";
  
if(!empty($row['ByUser']) && ($row['user'] != $row['ByUser'])) {
  echo "<br>(von " . $row['ByUser'] . ")"; 
}
?>
  </td>
  <td class="windowbg<?php echo $num;?>">
   <?php echo ( empty($row['date_b2']) || empty($row['bauschleife']) || $row['date_b2'] == $row['date_b1'] ) ? "": strftime($config_sitter_timeformat, $row['date_b2']) . "<br>";?>
   <?php echo ( empty($row['date_b1']) || empty($row['bauschleife']) || $row['date_b1'] == $row['date'] ) ? "": strftime($config_sitter_timeformat, $row['date_b1']) . "<br>";?>
   <?php echo strftime($config_sitter_timeformat, $row['date']);?>
  </td>
  <td class="windowbg<?php echo $num;?>">
   <?php echo $row['typ'];?>
  </td>
  <td class="windowbg<?php echo $num;?>">
   <?php echo $row['auftrag'];?>
  </td>
  <td class="windowbg<?php echo $num;?>" align="center">
<?php
    if( !$differentid || ( ($user_status == "admin") OR ($user_status == "SV") ) || ($user_sitterlogin == $row['ByUser'])) {
?>
   <a href="index.php?action=sitterauftrag&typ=<?php echo $row['typ'];?>&auftragid=<?php echo $row['id'];?>&umenu=1&sitterid=<?php echo urlencode($id);?>&sid=<?php echo $sid;?>"><img src="bilder/file_edit_s.gif" border="0" alt="editieren"></a>
<?php
    }
		if ( $row['typ'] == "Gebaeude" )
		{
?>
    <a href="index.php?action=sitterauftrag&umenu=1&parentid=<?php echo $row['id'];?>&sitterid=<?php echo urlencode($id);?>&sid=<?php echo $sid;?>"><img src="bilder/file_new_s.gif" border="0" alt="anhängen"></a>
<?php
		}
    if( !$differentid || ( ($user_status == "admin") OR ($user_status == "SV") )  || ($user_sitterlogin == $row['ByUser'])) {
?>
    <a href="index.php?action=sitterauftrag&parentid=<?php echo $row['id'];?>&delid=<?php echo $row['id'];?>&sitterid=<?php echo urlencode($id);?>&sid=<?php echo $sid;?>" onclick="return confirmlink(this, 'Auftrag wirklich löschen?')"><img src="bilder/file_delete_s.gif" border="0" alt="löschen"></a>
<?php
    }
?>
  </td>
 </tr>

<?php
	}
?>
</table>
<br>

<?php
}
// neuer Auftrag //
if ( ! empty($umenu) )
{
	$auftragid = getVar('auftragid');

	if ( ! empty($auftragid) )
	{
		$sql = "SELECT * FROM " . $db_tb_sitterauftrag . " WHERE id='" . $auftragid . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row = $db->db_fetch_array($result);

		$planet = $row['planet'];    
		$date = strftime($config_sitter_timeformat, $row['date']);
		$date_b1 = strftime($config_sitter_timeformat, $row['date_b1']);
		$date_b2 = strftime($config_sitter_timeformat, $row['date_b2']);
		$auftrag = $row['auftrag'];
		$schiff = $row['bauid'];
		$schiffanz = $row['schiffanz'];		
		$geb = $row['bauid'];
		$bauschleife = $row['bauschleife'];
		$schieben = $row['schieben'];
		$typ = $row['typ'];
if( defined('RESEARCH') && (RESEARCH === TRUE)) {
    $resid = $row['resid'];
}

		$sql = "SELECT id FROM " . $db_tb_sitterauftrag . " WHERE refid = '" . $auftragid . "'";
		$result_bev = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_bev = $db->db_fetch_array($result_bev);
		$thisid = $row_bev['id'];
	}
	else
	{
		$auftragid = "";
		$auftrag = "";
		
		$schieben = "";
	
		if ( empty($serie) )
		{
			$bauschleife = ( $user_peitschen == 1 ) ? 1: "";
			$typ = getVar('typ');
		}
		else
		{
      if(empty($planet))
        $planet = getVar('planet');
        
      if(!empty($planet) && isset($planetsmod[$planet])) {
        $planetmod = $planetsmod[$planet];
      } else {
        $planetmod = 1;
      }
      
      if($planetmod == 0)
        $planetmod = 1;
    
			$sql = "SELECT dauer, category FROM " . $db_tb_gebaeude . " WHERE id='" . $geb . "'";
			$result_geb = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$row_geb = $db->db_fetch_array($result_geb);

			$modmaurer = ( ($user_genmaurer == 1) && (( strpos($row_geb['category'], "Bunker") !== FALSE ) || ( strpos($row_geb['category'], "Lager") !== FALSE )) ) ? 0.5: 1;

			$date_b2 = $date_b1;
			$date_b1 = $date;
			$date = $date + $row_geb['dauer'] * $planetmod * $user_gengebmod * $modmaurer;

			$date = strftime($config_sitter_timeformat, $date);
			$date_b1 = strftime($config_sitter_timeformat, $date_b1);
			$date_b2 = strftime($config_sitter_timeformat, $date_b2);
		}
	}
?>
<font style="font-size: 22px; color: #004466">Sitterauftrag</font><br>
<?php
	echo ( empty($alert) ) ? "": $alert;
?>
<br>
<form method="POST" action="index.php?action=sitterauftrag&sitterid=<?php echo urlencode($id);?>&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 60%;">
 <tr>
  <td class="windowbg2" style="width: 30%;">
   Planet:<?php echo ($typ == "Forschung") ? "<br><i>(optional)</i>": "";?>
  </td>
  <td class="windowbg1" style="width: 70%;">
<?php
	if ( ( ! empty($serie) ) || ( ! empty($auftragid) && ! empty($thisid)) ) {
    if(! empty($planet) && isset($planets[$planet]))
  		echo "<input type=\"hidden\" name=\"planet\" value=\"" . $planet . "\">[" . $planet . "] " . $planets[$planet];
  }
	else
	{
?>
   <select name="planet" style="width: 200px;">
<?php
	  foreach ($planets as $key => $data)
			echo ($planet == $key) ? " <option value=\"" . $key . "\" selected>[" . $key . "] " . $data . "</option>\n": " <option value=\"" . $key . "\">[" . $key . "] " . $data . "</option>\n";
?>
   </select>
<?php
	} 
?>
  </td>
 </tr>
<?php
	if ( $typ == "Gebaeude" )
	{
		if ( ! empty($user_genbauschleife) )
		{
?>
 <tr>
  <td class="windowbg2">
   Zeit frühstens 2:<br>
   <i>Nur bei Bauschleifennutzung relevant.</i>
  </td>
  <td class="windowbg1">
<?php
			if ( ( ! empty($serie) ) || ( ! empty($auftragid) && ! empty($thisid)) )
				echo "<input type=\"hidden\" name=\"date_b2\" value=\"" . (isset($date_b2) ? $date_b2 : "") . "\">" . (isset($date_b2) ? $date_b2 : "");
			else
			{
?>
   <input type="text" name="date_b2" value="<?php echo (isset($date_b2) ? $date_b2 : "");?>" style="width: 200px;">
<?php
			}
?>  </td>
 </tr>
<?php
		}
?>
 <tr>
  <td class="windowbg2">
   Zeit frühstens 1:<br>
   <i>Nur bei Bauschleifennutzung relevant.</i>
  </td>
  <td class="windowbg1">
<?php
		if ( ( ! empty($serie) ) || ( ! empty($auftragid) && ! empty($thisid)) )
			echo "<input type=\"hidden\" name=\"date_b1\" value=\"" . (isset($date_b1) ? $date_b1 : "") . "\">" . (isset($date_b1) ? $date_b1 : "");
		else
		{
?>
   <input type="text" name="date_b1" value="<?php echo (isset($date_b1) ? $date_b1 : "");?>" style="width: 200px;">
<?php
		}
?>  </td>
 </tr>
<?php
	}
?>
 <tr>
<?php
	if ( $typ == "Forschung" )
	{
?>
  <td class="windowbg2">
   Zeit:<br>
   <i>Zeit, zu der die aktuelle Forschung ausläuft.</i>
  </td>
<?php
	}
	else
	{
?>
  <td class="windowbg2">
   Zeit spätestens:<br>
   <i>Zeit, zu der alle Bauschleifenaufträge auslaufen.</i>
  </td>
<?php
	}
?>
  <td class="windowbg1">
<?php
	if ( ( ! empty($serie) ) || ( ! empty($auftragid) && ! empty($thisid)) )
		echo "<input type=\"hidden\" name=\"date\" value=\"" . (isset($date) ? $date : "") . "\">" . (isset($date) ? $date : "");
	else
	{
?>
   <input type="text" name="date" value="<?php echo (isset($date) ? $date : "");?>" style="width: 200px;">
<?php
	}
?>
  </td>
 </tr>
<?php
	if ( ( $typ == "Gebaeude" ) && ( empty($serie) ) )
	{
?>
 <tr>
  <td class="windowbg2">
   oder automatische Erkennung:<br>
   <i>Aktuelle Bauliste aus Icewars kopieren.</i>
  </td>
  <td class="windowbg1">
   <textarea name="date_parse" rows="4" cols="25" style="width: 200px;"></textarea>
  </td>
 </tr>
<?php
	}
?>
 <tr>
<?php
	if ( $typ == "Forschung" )
	{
if( defined('RESEARCH') && (RESEARCH === TRUE)) {
	  if(empty($resid) && !empty($auftrag)) {
			$resid = find_research_id($auftrag);
		} 
}
?>
  <td class="windowbg2">
   Forschung:
  </td>
  <td class="windowbg1">
<?php if( defined('RESEARCH') && (RESEARCH === TRUE)) { ?>
   <select name="researchid" style="width: 400px;">
<!--  	 <optgroup label="Unbekannt" title="Unbekannt"></optgroup> -->
		 <?php echo  fill_selection($resid); ?>
	 </select>
    Sollte eine Forschung noch nicht aufgeführt sein, bitte die Forschungsinfo ingame in den Parser einfügen.
<?php } else {?>
   <input type="text" name="auftrag" value="<?php echo $auftrag;?>" style="width: 200px;">
<?php } ?>
  </td>
<?php
	}
	else
	{
?>
  <td class="windowbg2">
   Notizen:<?php echo ($typ == "Sonstiges") ? "": "<br><i>(optional)</i>";?>
  </td>
  <td class="windowbg1">
   <textarea name="auftrag" rows="4" cols="25" style="width: 200px;"><?php echo $auftrag;?></textarea>
  </td>
<?php
	}
?>
 </tr>
<?php
	if ( $typ == "Schiffe" )
	{
?>
 <tr>
  <td class="windowbg2">
   Schiffe:
  </td>
  <td class="windowbg1">
   <select name="schiff" style="width: 200px;">
   <option value="">---</option>
<?php
	$typprev = '';
	$sql = "SELECT typ, id, abk FROM " . $db_tb_schiffstyp . " typ ORDER BY typ asc";
	$result_schiff = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while($row_schiff = $db->db_fetch_array($result_schiff))
	{
		if ( $typprev != $row_schiff['typ'] )
		{
			echo "<optgroup label=\"" . $row_schiff['typ'] . "\" title=\"" . $row_schiff['typ'] . "\"></optgroup>\n";
			$typprev = $row_schiff['typ'];
		}
		echo ($schiff == $row_schiff['id']) ? " <option value=\"" . $row_schiff['id'] . "\" selected>" . $row_schiff['abk'] . "</option>\n": " <option value=\"" . $row_schiff['id'] . "\">" . $row_schiff['abk'] . "</option>\n";
	}
?>
   </select>
   Anzahl: <input type="text" name="schiffanz" value="<?php echo (isset($schiffanz) ? $schiffanz : "");?>" style="width: 100px;">
  </td>
 </tr>
<?php
	}
	if ( ( $typ == "Gebaeude" ) || ( $typ == "Sonstiges" ) )
	{
?>
 <tr>
  <td class="windowbg2">
   Bauschleife nutzen?:
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="bauschleife" value="1"<?php echo ($bauschleife) ? " checked": "";?>>
  </td>
 </tr>

<?php
	}
	if ( $typ != "Forschung" ) {
	if (($db_user=="iwdb") && ( $typ == "Sonstiges" )) {
	 ?>
	 <tr>
	  <td class="windowbg2">
	   Schiebeauftrag?:
	  </td>
	  <td class="windowbg1">
	   <input type="checkbox" name="schieben" value="1"<?php echo ($schieben) ? " checked": "";?>>
	  </td>		
	 </tr>
 	<?php
 	}
	}
	if ( $typ == "Gebaeude" )
	{
		$bauschleifenmod = 1;
		if ( empty( $peitschen ) && isset($date) && isset($date_b1) && isset($date_b2) )
		{
			if ( $date_b1 <> $date ) $bauschleifenmod = 1.1;
			if ( $date_b2 <> $date_b1 ) $bauschleifenmod = 1.2;
		}
?>
 <tr>
  <td class="windowbg1" colspan="2">
   <table border="0" cellpadding="4" cellspacing="4" style="width: 100%;">
    <tr><td colspan="2" class="windowbg2">Zeiten ohne Bauschleifenmodifikator (x <?php echo $bauschleifenmod;?>) und Planetenmodifikator.</td></tr>
<?php
	$catprev = "";
	$rownum = 0;
	$colnum = 0;
  
	$sql = "SELECT * FROM " . $db_tb_gebaeude . " WHERE inactive = '' ORDER BY category ASC, idcat ASC";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    
  if(empty($user_gengebmod))
    $user_gengebmod = 1;
      
	while($row = $db->db_fetch_array($result))
	{
		if (strpos($user_gebaeude, "|" . $row['id'] . "|") === FALSE)
		{
			$modmaurer = ( ($user_genmaurer == 1) && (( strpos($row['category'], "Bunker") !== FALSE ) || ( strpos($row['category'], "Lager") !== FALSE )) ) ? 0.5: 1;

			if ( $catprev != $row['category'] )
			{
				if ( ($rownum != 0) && ( $colnum == 2 ) ) echo "<td>&nbsp;</td>\n</tr>\n";
		
				echo "<tr><td colspan='2' class='windowbg2'>" . $row['category'] . "</td></tr>\n";
				$catprev = $row['category'];
				$colnum = 1;
				$rownum++;
			}
      if(defined('RESEARCH') && (RESEARCH === TRUE)) {
  			$resid = find_research_for_building($row['id']);
  			if($resid == 0) {
  			  $altname = "";
  			  $resRowName = $row['name'];
  			} else {
                $altname = "Benötigte Forschung:" . find_research_name($resid);
                $resRowName = "<a href='index.php?action=m_research&researchid=" . $resid . "&sid=" . $sid . "' title='" . $altname . "'>" . $row['name'] . "</a>";
  			}
      } else {
        $resRowName = $row['name'];
        $altname    = "";
      }
			if ( $colnum == 1 ) echo "<tr>\n";
			echo "<td class='windowbg1' valign='middle'>";
			if ( $user_gebbilder == "1" ) {
                echo "<table><tr><td>";
            }
			echo "<input type='radio' name='geb' value='" . $row['id'] . "'" . (( $geb == $row['id'] ) ? " checked" : "" ) . "> ";

			if ( $user_gebbilder == "1" ) {
                $bild_url = ( empty($row['bild']) ) ? "bilder/gebs/blank.jpg" : "bilder/gebs/" . $row['bild'] . ".jpg";
                echo "</td><td><img src='" . $bild_url . "' title='" . $altname . "' border='0' width='50' height='50' style='vertical-align:middle;'></td><td>";
            }
			echo $resRowName . " [" . dauer($row['dauer'] * $user_gengebmod * $modmaurer) . "]";
			if ( $user_gebbilder == "1" ) {
                echo "</td></tr></table>";
            }
			echo "</td>\n";
			if ( $colnum == 2 ) {
                echo "</tr>\n";
            }
		
			$colnum = ( $colnum == 2 ) ? 1: 2;
		}
	}
	if ( $colnum == 2 ) echo "<td>&nbsp;</td>\n</tr>\n";
?>

   </table>
  </td>
 </tr>
<?php
		if ( empty($auftragid) )
		{
?>
 <tr>
  <td class="windowbg2">
   Auftrag anhängen?:
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="serie" value="1">
  </td>
 </tr>
<?php
		}
	}
?>
 <tr>
  <td colspan="2" class="titlebg" align="center">
   <input type="hidden" name="parentid" value="<?php echo $thisid;?>"><input type="hidden" name="typ" value="<?php echo $typ;?>"><input type="hidden" name="auftragid" value="<?php echo $auftragid;?>"><input type="hidden" name="editauftrag" value="true"><input type="submit" value="speichern" name="B1" class="submit">
  </td>
 </tr>
</table>
</form>
<?php
}

//****************************************************************************
//
function fill_selection($selected_id) {
  global $db, $db_tb_research, $db_tb_researchfield, $id, $db_tb_research2user;

    $fields = array();
    $sql = "SELECT id, name FROM " . $db_tb_researchfield . " ORDER BY id";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

    while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
        $resid = $research_data['id'];
        $fields[$resid] = $research_data['name'];
	}
	$db->db_free_result($result);

	$where = "";		
	$idlist = "";

	if(!empty($id)) {
        // MySQL < Version 4.3 is incompatible with a subselect query. Statement was:
		// 
		// $where = " WHERE NOT ID IN" .
		//          " (SELECT rID FROM research2user where userid='" . $id ."')";		
		
		$sql = "SELECT rID FROM " . $db_tb_research2user . " where userid='" . $id ."'";
        $result = $db->db_query($sql)
    	    or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
		while(($row = $db->db_fetch_array($result)) !== FALSE) {
		  if(!empty($idlist))
				$idlist .= ", ";
				
			$idlist .= $row['rID'];
		} 
        $db->db_free_result($result);

		if(!empty($idlist)) {
            $where = " WHERE NOT ( ID IN (" . $idlist . ") )";
        }
    }
	
    $sql =  "SELECT ID, name, gebiet FROM " . $db_tb_research . $where .
            " ORDER BY gebiet ASC, name ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	$gebietalt = 0;
	$retVal    = "";
    while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
        $resid    = $research_data['ID'];
        $resname  = $research_data['name'];
		$resfield = $research_data['gebiet'];

		if($gebietalt != $resfield) {
		  $retVal .= "<optgroup label='" . $fields[$resfield] . "'" .
                 " title='" . $fields[$resfield] . "'></optgroup>\n";
			$gebietalt = $resfield;
		}

		$retVal .= "<option value='" . $resid . "'";
		if($resid == $selected_id) {
		  $retVal .= " selected";
		}

		$retVal .= ">" . $resname . "</option>\n";
	}
	$db->db_free_result($result);

	return $retVal;
}

// ****************************************************************************
//
//
function find_research_id($researchname) {
	global $db, $db_tb_research;

	// Find first research identifier 
	$sql = "SELECT ID FROM " . $db_tb_research . " WHERE name='" . $researchname . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	
	// Not found, return base 
	if(empty($row)) {
        return 1;
	}

    return $row['ID'];
}

// ****************************************************************************
//
//
function find_research_name($researchid) {
	global $db, $db_tb_research;

	// Find first research identifier 
	$sql = "SELECT name FROM " . $db_tb_research . " WHERE ID=" . $researchid;
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	$row = $db->db_fetch_array($result);
	
	// Not found, so insert new
	if(empty($row)) {
	  return "";
	}

    return $row['name'];
}

/**
 * Find the researchId of the building with the given building identifier.
 * return 0 if the building was not found (which usually happens for the
 * buildings at the start of the game).
 */
 
// ****************************************************************************
//
// Erzeugt einen Modul-Link.
function makelink($newparams, $content) {
    return '<a href="' . makeurl($newparams) . '">' . $content . '</a>';
}
// ****************************************************************************
//
// Erzeugt eine Modul-URL.
function makeurl($newparams) {
	global $modulname, $sid, $params;

	$url = 'index.php?action=' . $modulname;
	$url .= '&sid=' . $sid;
	if (is_array($newparams))	
		$mergeparams = array_merge($params, $newparams);
	else
		$mergeparams = $params;
	foreach ($mergeparams as $paramkey => $paramvalue)
		$url .= '&' . $paramkey . '=' . $paramvalue;
	return $url;
}

function find_research_for_building($bid, $level=0) {
    global $db, $db_tb_research2building;
		
	$sql = "SELECT rId FROM " . $db_tb_research2building . " WHERE lvl=" . $level . " AND bId=" . $bid;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	$row = $db->db_fetch_array($result);
	$db->db_free_result($result);
	
	if(!empty($row['rId'])) {
		return $row['rId'];
	}
	
	return 0;
}
?>
