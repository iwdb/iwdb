<?php
/*****************************************************************************/
/* searchdb.php                                                              */
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

$orderprio = array (
   "" => "Koordinaten",
   "rating_normal" => "Rating ohne Techteam",
   "rating_best_tt" => "Rating bestes Techteam",
   "rating_eisen_tt" => "Rating Techteam Eisen",
   "rating_chemie_tt" => "Rating Techteam Chemie",
   "rating_eis_tt" => "Rating Techteam Eis",
   "eisengehalt" => "Eisen",
   "chemievorkommen" => "Chemie",
   "eisdichte" => "Eis",
   "lebensbedingungen" => "Lebensbedingungen",
   "gravitation" => "Gravitation",
   "typ" => "Planetentyp",
   "objekt" => "Objekttyp",
   "user" => "Spielername",
   "allianz" => "Allianz",
   "punkte" => "Punkte",
   "kgmod" => "Gebäudekosten",
   "dgmod" => "Gebäudedauer",
   "ksmod" => "Schiffskosten",
   "dsmod" => "Schiffsdauer",
   "fmod" => "Forschungsmultiplikator"
);

$ratingtypes = array (
   "" => "Rating ohne Techteam",
   "rating_best_tt" => "Rating bestes Techteam",
   "rating_eisen_tt" => "Rating Techteam Eisen",
   "rating_chemie_tt" => "Rating Techteam Chemie",
   "rating_eis_tt" => "Rating Techteam Eis",
);

$orderpriod = array (
   "ASC" => "aufsteigend",
   "DESC" => "absteigend"
);

$typ_type = array (
   "%" => "Alle",
   "Steinklumpen" => "Steinklumpen",
   "Asteroid" => "Asteroid",
   "Eisplanet" => "Eisplanet",
   "Gasgigant" => "Gasgigant",
   "Nichts" => "Nichts"
);

$objekt_type = array (
   "%" => "Alle",
   "---" => "unbewohnt",
   "bewohnt" => "bewohnt",
   "Kolonie" => "Kolonie",
   "%basis" => "alle Basen",
   "Kampfbasis" => "Kampfbasis",
   "Sammelbasis" => "Sammelbasis"
);

$ansichten = array (
   "auto" => "automatisch",
   "geologisch" => "geologisch",
   "taktisch" => "taktisch",
   "beide" => "geologisch und taktisch"
);

$techteams = array (
   "" => "kein Techteam berücksichtigen",
   "EisenTT" => "Techteam Eisen berücksichtigen",
   "ChemieTT" => "Techteam Chemie berücksichtigen",
   "EisTT" => "Techteam Eis berücksichtigen"
);

$merkmale = array (
   "%" => "---",
   "Asteroidengürtel" => "Astrogürtel",
   "Gold" => "Gold",
   "instabiler Kern" => "instabil",
   "Mond" => "Mond",
   "planetarer Ring" => "planetarer Ring",
   "Natürliche Quelle" => "Quelle",
   "radioaktiv" => "radioaktiv",
   "wenig Rohstoffe" => "Rohstoffmangel",
   "alte Ruinen" => "Ruinen",
   "Ureinwohner" => "Ureinwohner",
   "toxisch" => "toxisch"
);

$sql = "SELECT * FROM " . $db_tb_user . " WHERE sitterlogin = '" . $user_sitterlogin . "'";
$result = $db->db_query($sql)
or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);

$gal_start = $row['gal_start']; $gal_end = $row['gal_end']; $sys_start = $row['sys_start']; $sys_end = $row['sys_end'];
$grav_von = $row['grav_von']; $grav_bis = $row['grav_bis'];

$preset = getVar('preset');
$preset = ( empty($preset) ) ? $row['preset']: $preset;


if ( ! empty($preset) )
{
	$sql = "SELECT * FROM " . $db_tb_preset . " WHERE id = '" . $preset . "'";
	$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	foreach($row as $key => $data)
	{
		if ($data <> "x") ${$key} = $data;
	}
}

?>
<div class='doc_title'>Planet suchen</div>
<br>

<table border="0" cellpadding="4" cellspacing="1"
	class="bordercolor" style="width: 80%;">
	<tr>
		<td colspan="2" class="windowbg2" align="center"><br>
		<form method="POST" action="index.php?action=searchdb&sid=<?php echo $sid;?>"
			enctype="multipart/form-data"><select name="preset"
			style="width: 100px;" onchange="this.form.submit();">
			<?php

			$sql = "SELECT id, name FROM " . $db_tb_preset .  " WHERE (fromuser = '" . $user_sitterlogin . "' OR fromuser = '')";
			$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			while ($row = $db->db_fetch_array($result))
			{
				echo ($preset == $row['id']) ? "<option value=\"" . $row['id'] . "\" selected>" . $row['name'] . "</option>\n": "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>\n";
			}
			?>
		</select></form>
		<br>
		</td>
	</tr>
	<form method="POST" action="index.php?action=showgalaxy&sid=<?php echo $sid;?>"
		enctype="multipart/form-data">
	
	
	<tr>
		<td colspan="2" class="titlebg"><b>Bereich:</b></td>
	</tr>
	<tr>
		<td class="windowbg2" style="width: 40%;">Galaxie:<br>
		<i>In welchem Galaxiebereich sollen Planeten gesucht werden?</i></td>
		<td class="windowbg1">von <input type="text" name="gal_start"
			value="<?php echo ((isset($gal_start)) ? $gal_start :'')?>" style="width: 30">
		bis <input type="text" name="gal_end"
			value="<?php echo ((isset($gal_end)) ? $gal_end : '')?>" style="width: 30"></td>
	</tr>
	<tr>
		<td class="windowbg2">System:<br>
		<i>In welchem Systemberich sollen Planeten gesucht werden?</i></td>
		<td class="windowbg1">von <input type="text" name="sys_start"
			value="<?php echo ((isset($sys_start)) ? $sys_start : '')?>" style="width: 30">
		bis <input type="text" name="sys_end"
			value="<?php echo ((isset($sys_end)) ? $sys_end : '')?>" style="width: 30"></td>
	</tr>
	<tr>
		<td colspan="2" class="titlebg"><b>Eigenschaften:</b></td>
	</tr>
	<tr>
		<td class="windowbg2">Planetentyp:</td>
		<td class="windowbg1"><select name="typ" style="width: 100">
		<?php
		$typ = (isset($typ) ) ? $typ : '';
		foreach ($typ_type as $key => $data)
		echo ($typ == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select></td>
	</tr>
	<tr>
		<td class="windowbg2">Objekttyp:</td>
		<td class="windowbg1"><select name="objekt" style="width: 100">
		<?php
		$objekt = (isset($objekt) ) ? $objekt: '';
		foreach ($objekt_type as $key => $data)
		echo ($objekt == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select></td>
	</tr>
	<tr>
		<td class="windowbg2">Gravitation:<br>
		<i>Wie viel Gravitation soll der Planet mindestens und maximal haben?</i>
		</td>
		<td class="windowbg1">von <input type="text" name="grav_von"
			value="<?php echo ((isset($grav_von)) ? $grav_von : '')?>" style="width: 30"
			maxlength="3"> bis <input type="text" name="grav_bis"
			value="<?php echo ((isset($grav_bis)) ? $grav_bis : '')?>" style="width: 30"
			maxlength="3">
			<?php
			$grav_von=str_replace(",",".",$grav_von);
			$grav_bis=str_replace(",",".",$grav_bis);
			?>
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Spielername (mehrere mit ; getrennt):<br>
		<i>Planeten eines bestimmten Spielers suchen</i></td>
		<td class="windowbg1"><input type="text" name="user"
			value="<?php echo ((isset($user)) ? $user : '')?>" style="width: 100"><br>
		<input type="checkbox" name="exact" value="1"
		<?php echo (isset($exact) && $exact) ? "checked": "";?>> exakte Suche?</td>
	</tr>
	<tr>
		<td class="windowbg2">Allianzen (mehrere mit ; getrennt):<br>
		<i>Planeten einer bestimmten Allianz suchen</i></td>
		<td class="windowbg1"><input type="text" name="allianz"
			value="<?php echo ((isset($allianz)) ? $allianz : '');?>" style="width: 100">
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Planetenname:<br>
		<i>Nach Planetennamen suchen</i></td>
		<td class="windowbg1"><input type="text" name="planetenname"
			value="<?php echo ((isset($planetenname)) ? $planetenname : '');?>"
			style="width: 100"></td>
	</tr>
	<tr>
		<td colspan="2" class="titlebg"><b>Modifikationen:</b><br>
		<i>Welche Modifikationen soll der Planet aufweisen?</i></td>
	</tr>
	<tr>
	
	
	<tr>
		<td class="windowbg2">&nbsp;</td>
		<td class="windowbg1">maximal</td>
	</tr>
	<td class="windowbg2">Gebäudekosten:</td>
	<td class="windowbg1"><input type="text" name="kgmod"
		value="<?php echo ((isset($kgmod)) ? $kgmod : '')?>" style="width: 100"
		maxlength="5">
		
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Gebäudedauer:</td>
		<td class="windowbg1"><input type="text" name="dgmod"
			value="<?php echo ((isset($dgmod)) ? $dgmod : '')?>" style="width: 100"
			maxlength="5">
			
			</td>
	</tr>
	<tr>
		<td class="windowbg2">Schiffkosten:</td>
		<td class="windowbg1"><input type="text" name="ksmod"
			value="<?php echo ((isset($ksmod)) ? $ksmod : '')?>" style="width: 100"
			maxlength="5">
			
			</td>
	</tr>
	<tr>
		<td class="windowbg2">Schiffdauer:</td>
		<td class="windowbg1"><input type="text" name="dsmod"
			value="<?php echo ((isset($dsmod)) ? $dsmod : '')?>" style="width: 100"
			maxlength="5">
			
			</td>
	</tr>
	<!--  <tr>
   <td class="windowbg2">
    Forschung:(UNDER CONSTRUCTION)
   </td>
   <td class="windowbg1">
      <input type="text" name="fmod_bis" value="<?php echo ((isset($fmod)) ? $fmod : '')?>" style="width: 100" maxlength="5">
   </td>
 </tr>
 <tr> //-->
	<td colspan="2" class="titlebg"><b>Ressourcen (min):</b><br>
	<i>Welche Ressourcenwerte soll der Planet mindestens aufweisen?</i></td>
	</tr>
	<tr>
		<td class="windowbg2">Eisengehalt:</td>
		<td class="windowbg1"><input type="text" name="eisengehalt"
			value="<?php echo ((isset($eisengehalt)) ? $eisengehalt : '')?>"
			style="width: 100" maxlength="3"></td>
	</tr>
	<tr>
		<td class="windowbg2">Chemievorkommen:</td>
		<td class="windowbg1"><input type="text" name="chemievorkommen"
			value="<?php echo ((isset($chemievorkommen)) ? $chemievorkommen : '')?>"
			style="width: 100" maxlength="3"></td>
	</tr>
	<tr>
		<td class="windowbg2">Eisdichte:</td>
		<td class="windowbg1"><input type="text" name="eisdichte"
			value="<?php echo ((isset($eisdichte)) ? $eisdichte : '')?>"
			style="width: 100" maxlength="3"></td>
	</tr>
	<tr>
		<td class="windowbg2">Techteams:</td>
		<td class="windowbg1"><select name="techteam" style="width: 225">
		<?php
		$techteam = (isset($techteam) ) ? $techteam : '';
		foreach ($techteams as $key => $data)
		echo ($techteams == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select></td>
	</tr>
	<tr>
		<td class="windowbg2">Lebensbedingungen:</td>
		<td class="windowbg1"><input type="text" name="lebensbedingungen"
			value="<?php echo ((isset($lebensbedingungen)) ? $lebensbedingungen : '')?>"
			style="width: 100" maxlength="3"></td>
	</tr>
	<?php if(defined('SPECIALSEARCH') && SPECIALSEARCH === TRUE ) { ?>
	<tr>
		<td class="windowbg2">Besonderheiten:</td>
		<td class="windowbg1"><select name="merkmal" style="width: 120">
		<?php
		$merkmal = (isset($merkmal) ) ? $merkmal : '';
		foreach ($merkmale as $key => $data)
		echo ($merkmal == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select></td>
	</tr>
	<tr>
		<td class="windowbg2">Rating:</td>
		<td class="windowbg1"><input type="text" name="ratingmin"
			value="<?php echo ((isset($ratingmin)) ? $ratingmin : '')?>"
			style="width: 100" maxlength="6"> <select name="ratingtyp" style="width: 170">
			<?php
			$ratingtyp = (isset($ratingtyp) ) ? $ratingtyp : '';
			foreach ($ratingtypes as $key => $data)
			echo ($ratingtyp == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
			?>
		</select></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="2" class="titlebg"><b>Sortierung:</b><br>
		<i>Nach was sollen die Suchergebnisse sortiert werden?</i></td>
	</tr>
	<tr>
		<td colspan="2" class="windowbg1" align="center"><select name="order1">
		<?php
		$order1 = (isset($order1) ) ? $order1: '';
		foreach ($orderprio as $key => $data)
		echo ($order1 == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select> <select name="order1_d">
		<?php
		$order1_d = (isset($order1_d) ) ? $order1_d: '';
		foreach ($orderpriod as $key => $data)
		echo ($order1_d == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" class="windowbg1" align="center"><select name="order2">
		<?php
		$order2 = (isset($order2) ) ? $order2: '';
		foreach ($orderprio as $key => $data)
		echo ($order2 == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select> <select name="order2_d">
		<?php
		$order2_d = (isset($order2_d) ) ? $order2_d: '';
		foreach ($orderpriod as $key => $data)
		echo ($order2_d == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" class="windowbg1" align="center"><select name="order3">
		<?php
		$order3 = (isset($order3) ) ? $order3: '';
		foreach ($orderprio as $key => $data)
		echo ($order3 == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select> <select name="order3_d">
		<?php
		$order3_d = (isset($order3_d) ) ? $order3_d: '';
		foreach ($orderpriod as $key => $data)
		echo ($order3_d == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
		?>
		</select></td>
	</tr>
	<tr>
		<td class="windowbg1" colspan="2" align="center">maximale Ergebnisse:
		<input type="text" name="max" value="<?php echo ( (isset($max)) ? $max : '')?>"
			style="width: 100" maxlength="6"></td>
	</tr>
	<tr>
		<td class="windowbg1" colspan="2" align="center">Ansicht: <select
			name="ansicht">
			<?php
			$ansicht = (isset($ansicht) ) ? $ansicht : '';
			foreach ($ansichten as $key => $data)
			echo ($ansicht == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
			?>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" class="titlebg" align="center"><input type="submit"
			value="OK" name="B1" class="submit"></td>
	</tr>
	<tr>
		<td colspan="2" class="titlebg" align="center">als Preset speichern? <input
			type="checkbox" name="newpreset" value="1"> <?php
			if ( $user_status == "admin" )
			{
				if (( isset($fromuser) ) && ( $fromuser == "" )) echo "global? <input type=\"checkbox\" name=\"global\" value=\"1\" checked>";
				else echo "global? <input type=\"checkbox\" name=\"global\" value=\"1\">";
			}
			?> <br>
		aendern: <select name="presetname1" style="width: 100px;">
		<?php

		if ($user_status == "admin" ) $sql = "SELECT id, name FROM " . $db_tb_preset .  " WHERE fromuser = '" . $user_sitterlogin . "' OR fromuser = '' ORDER BY fromuser, name";
		else $sql = "SELECT id, name FROM " . $db_tb_preset .  " WHERE fromuser = '" . $user_sitterlogin . "'";
		$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		while ($row = $db->db_fetch_array($result))
		{
			echo ($preset == $row['id']) ? "<option value=\"" . $row['id'] . "\" selected>" . $row['name'] . "</option>\n": "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>\n";
		}
		?>
		</select> oder neu: <input type="text" name="presetname2" value=""
			style="width: 100"></td>
	</tr>
</table>
</form>
