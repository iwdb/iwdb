<?php
/*****************************************************************************/
/* profile_editdata.php                                                      */
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
/*
// $Id
*/
 
if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');
	
$skins = array (
   "2" => "Standard",
   "1" => "Skin Version 1",
   "3" => "Text only"
);

$spieltyp = array (
   "Solo" => "Solo",
   "Buddler" => "Buddler",
   "Fleeter" => "Fleeter",
   "Cash Cow" => "Cash Cow"
);

$adminsittens = array (
   SITTEN_DISABLED => "Sitterbereich deaktiviert",
   SITTEN_ONLY_NEWTASKS => "kann Sitterauftraege erstellen, darf keine anderen sitten",
   SITTEN_ONLY_LOGINS => "darf andere sitten, darf keine Sitterauftraege erstellen",
   SITTEN_BOTH => "darf andere sitten, darf Sitterauftraege erstellen"
);

// M&ouml;gliche Menue-Stilarten werden aus der Dateisystem-Struktur gelesen. 
$menustyles = array();
$fp6=opendir("./menustyles");

$staatsformen = array('keine', 'Diktator', 'Monarch', 'Demokrat', 'Kommunist');

while ($datei1 = readdir($fp6)) {
  if (strstr($datei1,"menu_")) {
    $menuname = str_replace("menu_", "", $datei1);
		$menuname =	str_replace(".php", "", $menuname);
		
		$menustyles[$menuname] = $menuname;
	}
}

closedir($fp6);
?>
<div class='doc_title'>Profil</div>
<?php

$id   = getVar('id');
$edit = getVar('edit');

if ( ( $sitterlogin <> "" ) && ( $edit == "true" ) && ( ($sitterlogin == $user_sitterlogin) || ($user_status == "admin") ) )
{
	$userd['password']     = getVar('password');
	$userd['passwordwdhl'] = getVar('passwordwdhl');
	$userd['email']        = getVar('email');

	$userd['gal_start'] = getVar('gal_start');
	$userd['gal_end']   = getVar('gal_end');
	$userd['sys_start'] = getVar('sys_start');
	$userd['sys_end']   = getVar('sys_end');
	$userd['allianz']   = getVar('allianz');
	$userd['preset']    = getVar('preset');
	$userd['squad']     = getVar('squad');
	$userd['grav_von']  = getVar('grav_von');
	$userd['grav_bis']  = getVar('grav_bis');
	
  $userd['staatsform']  = getVar('staatsform');

	$userd['grav_von']  = str_replace(",", ".", $userd['grav_von']);
	$userd['grav_bis']  = str_replace(",", ".", $userd['grav_bis']);

	$userd['geopunkte'] = getVar('geopunkte');
	$userd['syspunkte'] = getVar('syspunkte');

	$userd['sitterpwd']      = getVar('sitterpwd');
	$userd['sitterpwdwdhl']  = getVar('sitterpwdwdhl');

	$userd['sitterskin']     = getVar('sitterskin');
	$userd['sitterpunkte']   = getVar('sitterpunkte');
	$userd['sittercomment']  = getVar('sittercomment');
	$userd['sitten']         = getVar('sitten');
	$userd['adminsitten']    = getVar('adminsitten');
	$userd['genbauschleife'] = getVar('genbauschleife');
	$userd['genmaurer']      = getVar('genmaurer');
	$userd['iwsa']      	    = getVar('iwsa');
	$userd['gengebmod']      = getVar('gengebmod');
	$userd['gengebmod']      = str_replace(",", ".", $userd['gengebmod']);

	$userd['peitschen']   = getVar('peitschen');
	$userd['budflesol']   = getVar('budflesol');
	$userd['buddlerfrom'] = getVar('buddlerfrom');

	$userd['ikea'] = getVar('ikea');

	$userd['gesperrt']         = getVar('gesperrt');
	
	$userd['planibilder']  = getVar('planibilder');
	$userd['gebbilder']    = getVar('gebbilder');
	$userd['status']       = getVar('status');
	$userd['menu_default'] = getVar('menu_default');
	
	$userd['color'] = getVar('color');

	$userd['sound'] = getVar('sound');
	$userd['uniprop'] = getVar('uniprop');

	if ( $user_status != "admin" ) unset($userd['status']);
	if ( $user_status != "admin" ) unset($userd['adminsitten']);
	if ( $user_status != "admin" ) unset($userd['sitterpunkte']);
	if ( $user_status != "admin" ) unset($userd['geopunkte']);
	if ( $user_status != "admin" ) unset($userd['syspunkte']);
	if ( $user_status != "admin" ) unset($userd['sittercomment']);
	if ( $user_status != "admin" ) unset($userd['allianz']);
	if ( $user_status == "admin" ) $userd['id'] = $id;

	// Testet ob das Passwort sicher ist
	if ( ! empty($userd['password']) )
	{
		$alert = secure_password($userd['password']);

		if ( ! empty($alert) )
		{
			echo "<br><div class='system_error'>" . $alert . "</div>";
			unset($userd['password']); unset($userd['passwordwdhl']);
		}
		else
		{
			if ( $userd['password'] != $userd['passwordwdhl'] )
			{
				echo "<br><div class='system_error'>Passw&ouml;rter stimmen nicht &uuml;berein! Passwort zur&uuml;ckgesetzt.</div>";
				unset($userd['password']); unset($userd['passwordwdhl']);
			}
			else
			{
				unset($userd['passwordwdhl']);
				$userd['password'] = md5($userd['password']);
			}
		}
	}
	else
	{
		unset($userd['password']); unset($userd['passwordwdhl']);
	}

	if ( $userd['sitterpwd'] != $userd['sitterpwdwdhl'] )
	{
		echo "<br><div class='system_error'>Sitterpassw&ouml;rter stimmen nicht &uuml;berein! Passwort zur&uuml;ckgesetzt.</div>";
		unset($userd['sitterpwd']); 
		unset($userd['sitterpwdwdhl']);
	}
	else
	{
		unset($userd['sitterpwdwdhl']);
	}

  if ( $userd['sitterpwd'] == '' OR $userd['sitterpwd'] == '***' )
  {
  	unset($userd['sitterpwd']);
  }
  else 
  {
    $userd['sitterpwd'] = MD5($userd['sitterpwd']);   
  }

  if ( getVar('deleteSitterpass') == '1' AND getVar('deleteSitterpasswdh') == '1' )
  {
    $userd['sitterpwd'] = '';
    echo "<br><div class='system_notification'>Sitterpassw&ouml;rter gel&ouml;scht.</div>";	
  }

	foreach ($userd as $key => $data)
	{
		$update = ( empty($update) ) ? $key . "='" . $data . "'": $update . ", " . $key . "='" . $data . "'";
	}

	$sql = "UPDATE " . $db_tb_user. " SET " . $update . " WHERE sitterlogin='" . $sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	echo "<br><div class='system_notification'>Userdaten aktualisiert.</div>";

	$sql = "SELECT t1.* FROM " . $db_tb_sitterauftrag . " as t1 LEFT JOIN " . $db_tb_sitterauftrag . " as t2 ON t1.id = t2.refid WHERE t2.refid is null AND t1.user='" . $sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while ($row = $db->db_fetch_array($result))
	{
		if ( $row['typ'] == "Gebaeude" ) dates($row['id'], $sitterlogin);
	}
}

$groups = array();
$sql = "SELECT * FROM $db_tb_group";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
	$groups[$row["id"]] = array(
		"id" => $row["id"],
		"name" => $row["name"],
		"selected" => false,
	);
$selectedgroups = getVar("groups");
if (isset($selectedgroups) && is_array($selectedgroups))
	foreach ($selectedgroups as $selectedgroup)
		$groups[$selectedgroup]["selected"] = true;
if ($edit == true && $user_status == "admin") {
	$sql = "DELETE FROM $db_tb_group_user WHERE $db_tb_group_user.`user_id`='" . $id . "'";
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	if (isset($selectedgroups) && is_array($selectedgroups))
		foreach ($selectedgroups as $selectedgroup) {
			$sql = "INSERT INTO $db_tb_group_user (group_id,user_id) VALUES (" . $selectedgroup . ",'" . $id . "')";
			$db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}
} else {
	$sql = "SELECT * FROM $db_tb_group_user WHERE $db_tb_group_user.`user_id`='" . $sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while ($row = $db->db_fetch_array($result))
		$groups[$row["group_id"]]["selected"] = true;
}

$sql = "SELECT * FROM " . $db_tb_user . " WHERE sitterlogin = '" . $sitterlogin . "'";
$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);

foreach($row as $key => $data)
{
	${$key} = $data;
}

if (!empty($sitterpwd)) {
  $sitterpwdsp = '***';
} else {
  $sitterpwdsp = '';
}

  //auslesen aller Memebr
 $alluser = array();
 $sqlM = "SELECT id FROM ".$db_prefix."user";
  $resultM = $db->db_query($sqlM)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
 while ($rowM = $db->db_fetch_array($resultM)) {
 	 $alluser[] = $rowM['id'];
 }	 

//auslesen der maximal zur Vef&uuml;gung stehenden Sittersounds:
$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'sound_global' ";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
 $rowP = $db->db_fetch_array($resultP); 

$sel0 = '';
$sel1 = '';
$sel2 = '';
$sel3 = '';
$sel4 = '';
$sel5 = '';


switch ($rowP['value']) {
 case '4':
   $asound[4] = 'fenster (blinkend) mit sound';
   if ($sound == 4) $sel4 = 'selected="selected"';
 case '3':
   $asound[3] = 'fenster (blinkend)';
   if ($sound == 3) $sel3 = 'selected="selected"';
 case '2':
   $asound[2] = 'fenster mit sound';
   if ($sound == 2) $sel2 = 'selected="selected"'; 
 case '1':
   $asound[1] = 'fenster';
   if ($sound == 1) $sel1 = 'selected="selected"'; 
 case '0':
   $asound[0] = 'ausgeschaltet';
   if ($sound == 0) $sel0 = 'selected="selected"';
   break;
 default:
   $asound[0] = 'ausgeschaltet';
   if ($sound == 0) $sel0 = 'selected="selected"';
}

?>
<br>
<form method="POST" action="index.php?action=profile&amp;sid=<?=$sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
 <tr>
  <td colspan="2" class="titlebg">
   <b>Daten:</b>
  </td>
 </tr>
 <tr>
 <td class="windowbg2" style="width:40%;">
   Username:<br>
   <i>Dein Loginnick.</i>
  </td>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" )
{
?>
   <input type="text" name="id" value="<?=$id;?>" style="width: 100">
<?php
}
else
{
?>
   <input type="hidden" name="id" value="<?=$id;?>"><?=$id;?>
<?php
}
?>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Passwort:<br>
   <i>Dein Loginpasswort.</i>
  </td>
  <td class="windowbg1">
   <input type="password" name="password" value="" style="width: 100">
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Passwort wdhl.:<br>
   <i>Passwort zur Sicherheit wiederholen.</i>
  </td>
  <td class="windowbg1">
   <input type="password" name="passwordwdhl" value="" style="width: 100">
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   EMail:<br>
   <i>An diese Adresse wird dein Passwort gesendet, wenn du es vergessen hast.</i>
  </td>
  <td class="windowbg1">
   <input type="text" name="email" value="<?=$email;?>" style="width: 200">
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Allianz:<br>
   <i>Trage hier deine Allianz (<?=$config_allytag;?>) ein.</i>
  </td>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" )
{
?>
   <input type="text" name="allianz" value="<?=$allianz;?>" style="width: 100">
<?php
} else {
?>
   <?=$allianz;?>
<?php
}
?>
  </td>
 </tr>
<?php
//Squadname INPUT eingefügt!
?>
 <tr>
  <td class="windowbg2">
   Squad:<br>
   <i>Gebe hier deinen Squadnamen an.</i>
  </td>
  <td class="windowbg1">
      <input type="text" name="squad" value="<?=$squad;?>" style="width: 100">
  </td>
 </tr><tr>
  <td class="windowbg2">
   Gravitation:<br>
   <i>Trage hier den Bereich der Gravitation ein, die du besiedeln kannst.</i>
  </td>
  <td class="windowbg1">
    von <input type="text" name="grav_von" value="<?=$grav_von;?>" style="width: 30" maxlength="3"> bis <input type="text" name="grav_bis" value="<?=$grav_bis;?>" style="width: 30" maxlength="3">
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Galaxie:<br>
   <i>Trage hier den Bereich der Galaxien ein, die du sehen kannst.</i>
  </td>
  <td class="windowbg1">
   von <input type="text" name="gal_start" value="<?=$gal_start;?>" style="width: 30"> bis <input type="text" name="gal_end" value="<?=$gal_end;?>" style="width: 30">
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   System:<br>
   <i>Trage hier den Bereich der Systeme ein, die du sehen kannst.</i>
  </td>
  <td class="windowbg1">
   von <input type="text" name="sys_start" value="<?=$sys_start;?>" style="width: 30"> bis <input type="text" name="sys_end" value="<?=$sys_end;?>" style="width: 30">
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Standardpreset:<br>
   <i>Diese Voreinstellung wird bei "Planet suchen" standardmaessig geladen.</i>
  </td>
  <td class="windowbg1">
<select name="preset" style="width: 100px;">
<?php

$sql = "SELECT id, name FROM " . $db_tb_preset .  " WHERE (fromuser = '" . $user_sitterlogin . "' OR fromuser = '') ORDER BY fromuser, name";
$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result))
{
	echo ($preset == $row['id']) ? "<option value=\"" . $row['id'] . "\" selected>" . $row['name'] . "</option>\n": "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>\n";
}
?>
</select>
  </td>
 </tr>
 <tr>
  <td colspan="2" class="titlebg">
   <b>Sitten:</b>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Sittererstatus?:<br>
   <i>Zeigt an, ob du andere sitten darfst und Sitterauftraege erstellen darfst. Kann nur von Admins geaendert werden.</i>
  </td>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" )
{
?>
<select name="adminsitten">
<?php
  foreach ($adminsittens as $key => $data)
		echo ($adminsitten == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
?>
</select>
<?php
}
else
{
	echo ( isset($adminsittens[$adminsitten]) ) ? $adminsittens[$adminsitten]: '';
}
?>
  </td>
 </tr>

 <tr>
  <td class="windowbg2">
   Sitten erlauben?:<br>
   <i>Sollen andere deinen Account sitten koennen? (Auftraege kannst du auch wenn deaktiviert erstellen.)</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="sitten" value="1"<?=($sitten) ? " checked": "";?>>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Ingame Nick:<br>
   <i>Dein Loginnick in Icewars.</i>
  </td>
  <td class="windowbg1">
   <input type="hidden" name="sitterlogin" value="<?=$sitterlogin;?>"><?=$sitterlogin;?>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Sitterpasswort:<br>
   <i>Dein Sitterpasswort in Icewars.</i>
  </td>
  <td class="windowbg1">
   <input type="password" name="sitterpwd" value="<?=$sitterpwdsp;?>" style="width: 100">
   Null:
   <input type="checkbox" name="deleteSitterpass" value="1"> 
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Sitterpasswort wdhl.:
   <i>Bitte wiederholen :)</i>
  </td>
  <td class="windowbg1">
   <input type="password" name="sitterpwdwdhl" value="<?=$sitterpwdsp;?>" style="width: 100">
   Null:
   <input type="checkbox" name="deleteSitterpasswdh" value="1"> 
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Serverskin:<br>
   <i>Welchen Skin moechtest du beim Sitten verwenden?</i>
  </td>
  <td class="windowbg1">
<select name="sitterskin">
<?php
  foreach ($skins as $key => $data)
		echo ($sitterskin == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
?>
</select>
  </td>
 </tr>
 <tr>
 <td class="windowbg2" style="width:30%;">
   Sitterpunkte:<br>
   <i>So viele Punkte hast du schon fuers Sitten erhalten.</i>
  </td>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" )
{
?>
   <input type="text" name="sitterpunkte" value="<?=$sitterpunkte;?>" style="width: 100">
<?php
}
else
{
?>
   <?=$sitterpunkte;?>
<?php
}
$sql = "SELECT AVG(sitterpunkte) FROM " . $db_tb_user . " WHERE sitterpunkte <> 0";
$result_avg = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row_avg = $db->db_fetch_array($result_avg);

?>
 Durchschnitt: <?=round($row_avg['AVG(sitterpunkte)']);?>
  </td>
 </tr>
 <tr>
 <td class="windowbg2" style="width:30%;">
   Sitterkommentar:<br>
   <i>Der Admin kann hier einen Kommentar hinzufuegen, der bei den Sitterlogins angezeigt wird (z.B. "im Urlaub").</i>
  </td>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" )
{
?>
   <textarea name="sittercomment" rows="3" style="width: 200;"><?=$sittercomment;?></textarea>
<?php
}
else
{
?>
   <?=$sittercomment;?>
<?php
}
?>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Fadein: <br>
   <i>Wie m&ouml;chtest du bei Sitterauftr&auml;gen zusa&auml;tzlich benahcrichtet werden?</i>
  </td>
  <td class="windowbg1">
    <select name="sound" size="1">
      <?php foreach ($asound as $key => $menu): ?>
        <option <?=${'sel'.$key};?> value="<?=$key;?>"><?=$asound[$key];?></option> 
      <?php endforeach?>
    </select>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Meister der Peitschen?:<br>
   <i>Wenn du die Genetikoption hast, bitte Haken setzen.</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="peitschen" value="1"<?=($peitschen) ?  " checked": "";?>>
  </td>
 </tr>

<?php
$lchecked  = '';
$mchecked  = '';
if ($ikea == 'L') $lchecked = 'checked="checked"';
if ($ikea == 'M') $mchecked = 'checked="checked"';
?>
 <tr>
  <td class="windowbg2">
   Ikea?:<br>
   <i>Wenn du die Genetikoption hast, bitte Haken setzen.</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="ikea" value="L" <?=$lchecked;?>>Lehrling
   <input type="checkbox" name="ikea" value="M" <?=$mchecked;?>>Meister
  </td>
 </tr>

 <tr>
  <td class="windowbg2">
   Ich will mehr Zeit?:<br>
   <i>Wenn du die Genetikoption hast, bitte Haken setzen.</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="genbauschleife" value="1"<?=($genbauschleife) ?  " checked": "";?>>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Der Einmaurer?:<br>
   <i>Wenn du die Genetikoption hast, bitte Haken setzen.</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="genmaurer" value="1"<?=($genmaurer) ?  " checked": "";?>>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Bau auf Bau auf Bau auf Bau auf?:<br>
   <i>Stelle hier deinen Gebaeudebaudauermodifikator ein (Standard 1).</i>
  </td>
  <td class="windowbg1">
   <input type="text" name="gengebmod" value="<?=$gengebmod;?>" style="width: 100">
  </td>
 </tr>
<tr>
 <td class="windowbg2">
  IWSA/IWBP-Account?:<br>
  <i>Wenn du einen solchen Account hast, bitte Haken setzen.
     Wichtig wg. FFP!</i>
 </td>
 <td class="windowbg1">
  <input type="checkbox" name="iwsa" value="1" <?=($iwsa) ?  " checked": "";?>>
 </td>
</tr>
<tr>
  <td colspan="2" class="titlebg">
   <b>Sonstiges:</b>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Spieltyp:<br>
   <i>Hier deinen Spieltyp eintragen. Wenn du Buddler bist, bitte noch das 2. Feld ausfuellen (ansonsten leer lassen).</i>
  </td>
  <td class="windowbg1">
<select name="budflesol">
<?php
  foreach ($spieltyp as $key => $data)
		echo ($budflesol == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
?>
</select>
von
<select name="buddlerfrom">
	<option value="">---</option>
<?php
  foreach ($alluser as $auser) {
  	echo ($buddlerfrom == $auser) ? " <option value=\"" . $auser . "\" selected>" . $auser . "</option>\n": " <option value=\"" . $auser . "\">" . $auser . "</option>\n";
  }	
?>
</select>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Deine Farbe:<br>
   <i>Sollest du Fleeter sein trage hier eine Farbe für deine Buddler ein (Format: #RRGGBB)</i>
  </td>
  <td class="windowbg1">
   <input type="text" name="color" size="8" maxlength="7" value="<?=$color;?>">
  </td>
 </tr>
  <tr>
  <td class="windowbg2">
   Staatsform:<br>
   <i>Deine Staatsform.</i>
  </td>
  <td class="windowbg1">
<select name="staatsform">
<?php
  foreach ($staatsformen as $key => $data)
		echo ($staatsform == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
?>
</select>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Planetenbilder anzeigen?:<br>
   <i>Sollen Bilder, den Planetentypen entsprechend in der Karte angezeigt werden?</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="planibilder" value="1"<?=($planibilder) ? " checked": "";?>>
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   Gebaeudebilder anzeigen?:<br>
   <i>Sollen Gebaeudebilder beim Erstellen eines Auftrages und bei "Gebaeude ausblenden" angezeigt werden?</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="gebbilder" value="1"<?=($gebbilder) ? " checked": "";?>>
  </td>
 </tr>
 <tr>
 <td class="windowbg2" style="width:30%;">
   Geoscanpunkte:<br>
   <i>So viele GeoScans hast du schon eingestellt.</i>
  </td>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" )
{
?>
   <input type="text" name="geopunkte" value="<?=$geopunkte;?>" style="width: 100">
<?php
}
else
{
?>
   <?=$geopunkte;?>
<?php
}
?>
  </td>
 </tr>
 <tr>
 <td class="windowbg2" style="width:30%;">
   Systemscanpunkte:<br>
   <i>So viele SystemScans hast du schon eingestellt.</i>
  </td>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" )
{
?>
   <input type="text" name="syspunkte" value="<?=$syspunkte;?>" style="width: 100">
<?php
}
else
{
?>
   <?=$syspunkte;?>
<?php
}
?>
  </td>
 </tr>
<?php
if ($user_status == "admin" )
{
?>
 <tr>
  <td class="windowbg2">
   Status:<br>
   <i>admin, HC, MV, SV, ...</i>
  </td>
  <td class="windowbg1">
   <input type="text" name="status" value="<?=$status;?>" style="width: 100">
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   User sperren?:<br>
   <i>Soll der User sich nicht mehr einloggen k&ouml;nnen?</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="gesperrt" value="1"<?=($gesperrt) ? " checked": "";?>>
  </td>
 </tr>
<?php
}
?>
 <tr>
  <td class="windowbg2">
   Men&uuml;-Darstellung:
  </td>
  <td class="windowbg1">
	 <select name="menu_default"  style="width: 100">
<?php 
  foreach ($menustyles as $key => $data)
		echo ($menu_default == $key) 
        ? "    <option value=\"" . $key . "\" selected>" . $data . "</option>\n" 
        : "    <option value=\"" . $key . "\">" . $data . "</option>\n";
?>
	 </select>
  </td>
 </tr>
<?php
if ($user_status == "admin" )
{
?>
 <tr>
  <td class="windowbg2">
   Gruppen:<br>
   <i>Welchen Gruppen wird der User zugeordnet?</i>
  </td>
 <td class="windowbg1">
	<select name="groups[]" size="5" multiple="multiple">
<?php
  foreach ($groups as $group) {
	echo "<option value=\"";
	echo $group["id"];
	echo "\"";
	if ($group["selected"])
		echo " selected>";
	else
		echo ">";
	echo $group["name"];
	echo "</option>";
  }
?>
	</select>
 </td>
 </tr>
<?php
}
?>
 <tr>
  <td class="windowbg2">
   Proportionale Universumsansicht?:<br>
   <i>Sollen alle Zeilenhöhen der Planeten in der Universumsansicht gleich hoch sein?</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="uniprop" value="1"<?=($uniprop) ? " checked": "";?>>
  </td>
 </tr>
 <tr>
  <td colspan="2" class="titlebg" align="center">
   <input type="hidden" name="edit" value="true"><input type="submit" value="speichern" name="B1" class="submit">
  </td>
 </tr>
</table>
</form>
<?php
if ( ( $user_status == "admin" ) && ( $sitterlogin != $user_sitterlogin ) )
{
?>
<br><br>
<div class='doc_centered_blue'>Account loeschen</div>
<br>
<a href="index.php?action=deluser&amp;sitterlogin=<?=urlencode($sitterlogin);?>&amp;sid=<?=$sid;?>" onclick="return confirmlink(this, 'Account wirklich loeschen?')">[jetzt loeschen]</a>
<?php
}
?>
<br>
