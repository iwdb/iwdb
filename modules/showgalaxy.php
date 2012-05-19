<?php
/*****************************************************************************/
/* showgalaxy.php                                                            */
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

// get post and get vars //
$order1   = getVar('order1');
$order2   = getVar('order2');
$order3   = getVar('order3');
$order1_d = getVar('order1_d');
$order2_d = getVar('order2_d');
$order3_d = getVar('order3_d');

$withoutscan = getVar('withoutscan');
$reserv      = getVar('reserv');
$clean_reserv = getVar('clean_reserv');

$gal = getVar('gal');
$sys = getVar('sys');

$gal_start = getVar('gal_start');
$gal_end   = getVar('gal_end');
$sys_start = getVar('sys_start');
$sys_end   = getVar('sys_end');

if ( !empty($gal)) {
	$gal_start = $gal;
	$gal_end = $gal;
}
if ( !empty($sys)) {
	$sys_start = $sys;
	$sys_end = $sys;
}


$objekt  = getVar('objekt');
$objekt  = ( empty($objekt) ) ? "%": $objekt;
$typ     = getVar('typ');
$typ     = ( empty($typ) ) ? "%": $typ;
$user    = getVar('user');
$allianz = getVar('allianz');

$button       = getVar('B1');

$eisengehalt       = getVar('eisengehalt');
$chemievorkommen   = getVar('chemievorkommen');
$eisdichte         = getVar('eisdichte');
$lebensbedingungen = getVar('lebensbedingungen');

$kgmod = getVar('kgmod');
$dgmod = getVar('dgmod');
$ksmod = getVar('ksmod');
$dsmod = getVar('dsmod');
$fmod = getVar('fmod');
$kgmod = str_replace(",",".",$kgmod);
$dgmod=str_replace(",",".",$dgmod);
$ksmod=str_replace(",",".",$ksmod);
$dsmod=str_replace(",",".",$dsmod);

$grav_von = getVar('grav_von');
$grav_bis = getVar('grav_bis');
$grav_von = str_replace(",", ".", $grav_von);
$grav_bis = str_replace(",", ".", $grav_bis);

$max    = getVar('max');
$exact  = getVar('exact');
$global = getVar('global');

$newpreset   = getVar('newpreset');
$presetname1 = getVar('presetname1');
$presetname2 = getVar('presetname2');

$ansicht = getVar('ansicht');
$ansicht = ( empty($ansicht) ) ? "auto": $ansicht;

$techteam = getVar('techteam');
$ratingmin = getVar('ratingmin');
$ratingmin = str_replace(",", ".", $ratingmin);
$ratingtyp = getVar('ratingtyp');

if( defined('SPECIALSEARCH') && SPECIALSEARCH === TRUE ) {
	$merkmal = getVar('merkmal');
	$merkmal = ( empty($merkmal) ) ? "%" : $merkmal;
}

if ( defined('SHOWWITHOUTSCAN') && SHOWWITHOUTSCAN === TRUE ) {
	$unscanned_only = getVar('unscanned');
	$unscanned_only = ( empty($unscanned_only) ) ? "" : $unscanned_only;
}

$sql = "UPDATE
		$db_tb_scans
	SET
		eisengehalt = '0',
		chemievorkommen = '0',
		eisdichte = '0',
		lebensbedingungen = '0',
		gravitation = '0',
		besonderheiten = '',
		fmod = '0',
		kgmod = '0',
		dgmod = '0',
		ksmod = '0',
		dsmod = '0',
		bevoelkerungsanzahl = '0',
		tteisen = '0',
		ttchemie = '0',
		tteis = '0',
		geoscantime = '0'
	WHERE
		objekt = 'Kolonie'";

mysql_query($sql) OR die(mysql_error());

//ungescannte Planeten anzeigen
if ( ! empty($withoutscan) AND  empty($button))
{
	$sql0 = "SELECT id FROM " . $db_tb_sid . " WHERE " . $db_tb_sid . ".sid='" . $sid . "'";
	$result0 = $db->db_query($sql0)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql0);
	$row0 = $db->db_fetch_array($result0);

	if( !empty($row0)) {
		$sql0 = "SELECT gal_start, sys_start, gal_end, sys_end FROM " . $db_tb_user . " WHERE id='" . $row0['id'] . "'";
		$result0 = $db->db_query($sql0)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql0);
		$row0 = $db->db_fetch_array($result0);

		if(!empty($row0)) {
			$gal_start_inp=$row0['gal_start'];
			$sys_start_inp=$row0['sys_start'];
			$gal_end_inp  =$row0['gal_end'];
			$sys_end_inp  =$row0['sys_end'];
		}
	}
} else {

	$gal_start = getVar('gal_start');
	$gal_end   = getVar('gal_end');
	$sys_start = getVar('sys_start');
	$sys_end   = getVar('sys_end');

	if ( !empty($gal)) {
		$gal_start = $gal;
		$gal_end = $gal;
	}
	if ( !empty($sys)) {
		$sys_start = $sys;
		$sys_end = $sys;
	}

}


if ( $newpreset == 1 )
{
	?>
<div class='doc_title'>Preset hinzufügen</div>
<br>
	<?php
	if ( ( $user_status == "admin" ) && ( ! empty($global) ) ) $fromuser = "";
	else $fromuser = $user_sitterlogin;

	if ( ! empty($presetname2) )
	{
		$sql = "INSERT INTO " . $db_tb_preset . " (name, typ, objekt, user, exact, allianz, ";
		if( defined('SPECIALSEARCH') && SPECIALSEARCH === TRUE) {
			$sql .= "merkmal, ";
		}
		$sql .= "eisengehalt, chemievorkommen, eisdichte, lebensbedingungen, order1, order1_d, order2, order2_d, order3, order3_d, grav_von, grav_bis, fmod, kgmod, dgmod, ksmod, dsmod, gal_start, gal_end, sys_start, sys_end, max, fromuser, ansicht) VALUES ('" . $presetname2 . "', '" . $typ . "', '" . $objekt . "', '" . $user . "', '" . $exact . "', '" . $allianz;
		if( defined('SPECIALSEARCH') && SPECIALSEARCH === TRUE) {
			$sql .= "', '" . $merkmal;
		}
		$sql .= "', '" . $eisengehalt . "', '" . $chemievorkommen . "', '" . $eisdichte . "', '" . $lebensbedingungen . "', '" . $order1 . "', '" . $order1_d . "', '" . $order2 . "', '" . $order2_d . "', '" . $order3 . "', '" . $order3_d . "', '" . $grav_von . "', '" . $grav_bis . "', '" . $fmod . "', '" . $kgmod . "', '" . $dgmod . "', '" . $ksmod . "', '" . $dsmod . "', '" . $gal_start . "', '" . $gal_end . "', '" . $sys_start . "', '" . $sys_end . "', '" . $max . "', '" . $fromuser . "', '" . $ansicht . "')";
		$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		echo "<div class='system_notification'>Neues Preset '" . $presetname2 . "' hinzugefuegt.</div>";
	}
	else
	{
		$sql = "SELECT fromuser, name FROM " . $db_tb_preset . " WHERE id = '" . $presetname1 . "'";
		$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row = $db->db_fetch_array($result);

		if ( ( $row['fromuser'] == $user_sitterlogin ) || ( $user_status == "admin" ) )
		{
			$sql = "UPDATE " . $db_tb_preset . " SET typ = '" . $typ . "', objekt = '" . $objekt . "', user = '" . $user . "', exact = '" . $exact . "', allianz = '" . $allianz;
			if( defined('SPECIALSEARCH') && SPECIALSEARCH === TRUE) {
				$sql .= "', merkmal = '" . $merkmal;
			}
			$sql .= "', eisengehalt = '" . $eisengehalt . "', chemievorkommen = '" . $chemievorkommen . "', eisdichte = '" . $eisdichte . "', lebensbedingungen = '" . $lebensbedingungen . "', order1 = '" . $order1 . "', order1_d = '" . $order1_d . "', order2 = '" . $order2 . "', order2_d = '" . $order2_d . "', order3 = '" . $order3 . "', order3_d = '" . $order3_d . "', gal_start = '" . $gal_start . "', gal_end = '" . $gal_end . "', sys_start = '" . $sys_start . "', sys_end = '" . $sys_end . "', max = '" . $max . "', fromuser = '" . $fromuser . "', ansicht = '" . $ansicht . "', kgmod = '" . $kgmod . "', dgmod = '" . $dgmod . "', ksmod = '" . $ksmod . "', dsmod = '" . $dsmod . "', dsmod = '" . $dsmod . "' WHERE id = '" . $presetname1 . "'";
			$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			echo "<div class='system_notification'>Preset '" . $row['name'] . " (" . $row['fromuser'] . ")' aktualisiert.</div>";

		}
		else echo "<div class='system_error'>Hack Attempt.</div>";
	}
}
else
{
	?>
<div class='doc_title'>Galaxie</div>
<br>
	<?php
	if ( $objekt <> "---" )
	{
		$kgmod_von = "";
		$kgmod_bis = "";
		$dgmod_von = "";
		$dgmod_bis = "";
		$ksmod_von = "";
		$ksmod_bis = "";
		$dsmod_von = "";
		$dsmod_bis = "";
		$fpmod_von = "";
		$fpmod_bis = "";
	}

	// Sortierung //
	$order = ( ! empty($order1) ) ? "ORDER BY " . $order1 . " " . $order1_d : "ORDER BY coords_gal " . $order1_d . ", coords_sys " . $order1_d . ", coords_planet " . $order1_d;
	$order = ( ! empty($order2) ) ? ( ($order) ? $order . ", " . $order2 . " " . $order2_d: "ORDER BY " . $order2 . " " . $order2_d) : $order . ", coords_gal " . $order2_d . ", coords_sys " . $order2_d . ", coords_planet " . $order2_d;
	$order = ( ! empty($order3) ) ? ( ($order) ? $order . ", " . $order2 . " " . $order3_d: "ORDER BY " . $order3 . " " . $order3_d) : $order . ", coords_gal " . $order3_d . ", coords_sys " . $order3_d . ", coords_planet " . $order3_d;

	// Planetentyp
	$where = "WHERE typ LIKE '" . $typ . "'";

	// Objekttyp //
	if ($objekt == "bewohnt")
	{
		$where .= " AND objekt <> '---'";
	}
	else
	{
		$where .= " AND objekt LIKE '" . $objekt . "'";
	}

	$nothing_types = "(`typ` = 'Steinklumpen' OR `typ` = 'Asteroid' OR `typ` = 'Gasgigant' OR `typ` = 'Eisplanet')";

	// Galaxy, System //
	if ( ! empty($gal_start) ) $where .= " AND coords_gal >= " . $gal_start;
	if ( ! empty($gal_end) ) $where .= " AND coords_gal <= " . $gal_end;

	if ( ! empty($sys_start) ) $where .= " AND coords_sys >= " . $sys_start;
	if ( ! empty($sys_end) ) $where .= " AND coords_sys <= " . $sys_end;

	// Scans unbewohnt und ohne GeoScan
	if ( defined('SHOWWITHOUTSCAN') && SHOWWITHOUTSCAN === TRUE) {
		if ( ! empty($withoutscan)) {
			if( empty( $unscanned_only )) {
				$querytime = time() - ($config_geoscan_red * $DAYS);
				$where .= " AND (time = 0 OR time < " . $querytime . " ) AND objekt = '---' AND " . $nothing_types;
			} else {
				$where .= " AND time = 0 AND objekt = '---' AND " . $nothing_types;
			}
		}
	} else {
		if ( ! empty($withoutscan)) {
			$where .= " AND time = 0 AND objekt = '---' AND " . $nothing_types;
		}
	}
	if ( ( ! empty($withoutscan) ) && ( empty($gal_start) ) && ( empty($gal_end) ) && ( empty($sys_start) ) && ( empty($sys_end) ) ) $where .= " AND objekt <> '---'";

	// reservierte Planeten
	if ( ! empty($reserv) ) $where .= " AND reserviert <> ''";

	// Spielername, Planetenname, Allianz //
	if ( ! empty($planetenname) ) $where .= " AND planetenname LIKE '" . $planetenname . "'";

	$exact = ( ! empty($exact) ) ? "" : "%";
	if ( ! empty($user) )
	{
		$users = explode(";", $user);
		foreach ($users as $data)
		{
			$where_all = ( isset($where_all) ) ? $where_all . " OR user LIKE '" . $exact . $data . $exact . "'" : "user LIKE '" . $exact . $data . $exact . "'";
		}
		$where .= " AND (" . $where_all . ")";
	}

	if ( ! empty($allianz) )
	{
		$allianzen = explode(";", $allianz);
		$where_all = "";
		foreach ($allianzen as $data)
		{
			$where_all = (!empty($where_all)) ? $where_all . " OR allianz LIKE '" . $data . "'" : "allianz LIKE '" . $data . "'";
		}
		$where .= " AND (" . $where_all . ")";
	}
	// Merkmale
	if( defined('SPECIALSEARCH') && SPECIALSEARCH === TRUE) {
		if( !empty($merkmal) && $merkmal !== "%" ) $where .= " AND besonderheiten LIKE '%" . $merkmal . "%'";
	}

	// Eisengehalt, Chemiegehalt, Eisgehalt, Lebensbedingungen //
	if ( ! empty($eisengehalt) ) {
		if ($techteam == "EisenTT") {
			$where .= " AND tteisen >= " . $eisengehalt;
		}
		else {
			$where .= " AND eisengehalt >= " . $eisengehalt;
		}
	}
	if ( ! empty($chemievorkommen) ) {
		if ($techteam == "ChemieTT") {
			$where .= " AND ttchemie >= " . $chemievorkommen;
		}
		else {
			$where .= " AND chemievorkommen >= " . $chemievorkommen;
		}
	}
	if ( ! empty($eisdichte) ) {
		if ($techteam == "EisTT") {
			$where .= " AND tteis >= " . $eisdichte;
		}
		else {
			$where .= " AND eisdichte >= " . $eisdichte;
		}
	}
	if ( ! empty($lebensbedingungen) ) {
		$where .= " AND lebensbedingungen >= " . $lebensbedingungen;
	}

	// Gravitation //
	if (strlen($grav_von) > 0) {
		$where .= " AND gravitation >= " . $grav_von . "";
	}
	if (strlen($grav_bis) > 0) {
		$where .= " AND gravitation <= " . $grav_bis . "";
	}

	if ( ! empty($kgmod) ) $where .= " AND kgmod <= " . $kgmod . "";
	if ( ! empty($dgmod) ) $where .= " AND dgmod <= " . $dgmod . "";
	if ( ! empty($ksmod) ) $where .= " AND ksmod <= " . $ksmod . "";
	if ( ! empty($dsmod) ) $where .= " AND dsmod <= " . $dsmod . "";
	if ( ! empty($fpmod) ) $where .= " AND fpmod <= " . $fpmod . "";

	// maximale Anzahl //
	if ( ! empty($max) ) {
		$limit = " LIMIT " . $max;
	} else {
		$limit = "";
	}

	// Rating
	$rating_normal = "";
	if (((strlen($ratingmin) > 0) AND (strlen($ratingtyp) == 0)) OR ($order1 == "rating_normal") OR ($order2 == "rating_normal") OR ($order3 == "rating_normal")) {
		$rating_normal = ", " . sqlRating( "" ) . " AS rating_normal";
		if ((strlen($ratingmin) > 0) AND (strlen($ratingtyp) == 0)) {
			$where .= " AND " . sqlRating( "" ) . " > " . $ratingmin . "";
		}
	}
	$rating_best_tt = "";
	if (((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_best_tt")) OR ($order1 == "rating_best_tt") OR ($order2 == "rating_best_tt") OR ($order3 == "rating_best_tt")) {
		$rating_best_tt = ", " . sqlRating( "rating_best_tt" ) . " AS rating_best_tt";
		if ((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_best_tt")) {
			$where .= " AND " . sqlRating( "rating_best_tt" ) . " > " . $ratingmin . "";
		}
	}
	$rating_eisen_tt = "";
	if (((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_eisen_tt")) OR ($order1 == "rating_eisen_tt") OR ($order2 == "rating_eisen_tt") OR ($order3 == "rating_eisen_tt")) {
		$rating_eisen_tt = ", " . sqlRating( "rating_eisen_tt" ) . " AS rating_eisen_tt";
		if ((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_eisen_tt")) {
			$where .= " AND " . sqlRating( "rating_eisen_tt" ) . " > " . $ratingmin . "";
		}
	}
	$rating_chemie_tt = "";
	if (((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_chemie_tt")) OR ($order1 == "rating_chemie_tt") OR ($order2 == "rating_chemie_tt") OR ($order3 == "rating_chemie_tt")) {
		$rating_chemie_tt = ", " . sqlRating( "rating_chemie_tt" ) . " AS rating_chemie_tt";
		if ((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_chemie_tt")) {
			$where .= " AND " . sqlRating( "rating_chemie_tt" ) . " > " . $ratingmin . "";
		}
	}
	$rating_eis_tt = "";
	if (((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_eis_tt")) OR ($order1 == "rating_eis_tt") OR ($order2 == "rating_eis_tt") OR ($order3 == "rating_eis_tt")) {
		$rating_eis_tt = ", " . sqlRating( "rating_eis_tt" ) . " AS rating_eis_tt";
		if ((strlen($ratingmin) > 0) AND ($ratingtyp == "rating_eis_tt")) {
			$where .= " AND " . sqlRating( "rating_eis_tt" ) . " > " . $ratingmin . "";
		}
	}
	$rating = $rating_normal . $rating_best_tt . $rating_eisen_tt . $rating_chemie_tt . $rating_eis_tt;

	$sql = "SELECT *" . $rating . " FROM " . $db_tb_scans . " " . $where . " " . $order . $limit;
	$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	if ( ( empty($withoutscan) ) && ($gal_end == $gal_start) && ($sys_end == $sys_start) && ( ! empty($gal_end) ) && ( ! empty($gal_start) ) )
	{
		?>
<form method="POST"
	action="index.php?action=showgalaxy&sid=<?php echo $sid;?>"
	enctype="multipart/form-data">
<p align="center"><?php
if ($sys_start > 1 )
{
	?> <a
	href="index.php?action=showgalaxy&sys_end=<?php echo ($sys_end - 1);?>&sys_start=<?php echo ($sys_end - 1);?>&gal_end=<?php echo $gal_end;?>&gal_start=<?php echo $gal_start;?>&sid=<?php echo $sid;?>"><b><<</b></a>
	<?php
}
?> Galaxie: <input type="text" name="gal" value="<?php echo $gal_start;?>"
	style="width: 30"> System: <input type="text" name="sys"
	value="<?php echo $sys_start;?>" style="width: 30"> <?php
	if ( defined('SHOWWITHOUTSCAN') && SHOWWITHOUTSCAN === TRUE) {
		if(!empty($withoutscan)) {
			echo "<br><input type=\"checkbox\" name=\"unscanned\"" . (!empty($unscanned_only) ? " \"checked\"" : "") . ">Nur ungescannte Planeten anzeigen<br>\n";
		}
	}
	?> <input type="submit" value="los" name="B1" class="submit"> <a
	href="index.php?action=showgalaxy&sys_end=<?php echo ($sys_end + 1);?>&sys_start=<?php echo ($sys_end + 1);?>&gal_end=<?php echo $gal_end;?>&gal_start=<?php echo $gal_start;?>&sid=<?php echo $sid;?>"><b>>></b></a>
</p>
</form>
	<?php
}
if ( ! empty($withoutscan) )
{

	?>
	<?php
	if ( isset($sys_start_inp) AND !empty($sys_start_inp) AND isset($sys_end_inp) AND !empty($sys_end_inp) ) {
		$sys_start = $sys_start_inp;
		$sys_end = $sys_end_inp;
	}
	if ( isset($gal_start_inp) AND !empty($gal_start_inp) AND isset($gal_end_inp) AND !empty($gal_end_inp) ) {
		$gal_start = $gal_start_inp;
		$gal_end = $gal_end_inp;
	}
	?>
<form method="POST"
	action="index.php?action=showgalaxy&withoutscan=1&sid=<?php echo $sid;?>"
	enctype="multipart/form-data">
<p align="center">


<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td>Galaxie von: <input type="text" name="gal_start"
			value="<?php echo $gal_start;?>" style="width: 30"> bis: <input type="text"
			name="gal_end" value="<?php echo $gal_end;?>" style="width: 30"></td>
	</tr>
	<tr>
		<td>System von: <input type="text" name="sys_start"
			value="<?php echo $sys_start;?>" style="width: 30"> bis: <input type="text"
			name="sys_end" value="<?php echo $sys_end;?>" style="width: 30"></td>
	</tr>
	<?php
	if ( defined('SHOWWITHOUTSCAN') && SHOWWITHOUTSCAN === TRUE) {
		if(!empty($withoutscan)) {
			echo "<tr><td align=\"center\"><input type=\"checkbox\" name=\"unscanned\"" . (!empty($unscanned_only) ? " \"checked\"" : "") . ">Nur ungescannte Planeten anzeigen</td></tr>\n";
			echo "<tr><td>Typ: ";
			echo '<select name="typ">';
			echo '<option value="%" selected>Alle</option>';
			echo '<option value="Steinklumpen">Steinklumpen</option>';
			echo '<option value="Asteroid">Asteroid</option>';
			echo '<option value="Eisplanet">Eisplanet</option>';
			echo '<option value="Gasgigant">Gasgigant</option>';
			echo '<option value="Nichts">Nichts</option>';
			echo '</select></td></tr>';
		}
	}
	?>
	<tr>
		<td align="center"><input type="submit" value="los" name="B1"
			class="submit"></td>
	</tr>
</table>
</p>
</form>
	<?php
}

if ( $db->db_num_rows($result) == 0 )
{
	if ( ( ! empty($withoutscan) ) && ( empty($gal_start) ) && ( empty($gal_end) ) && ( empty($sys_start) ) && ( empty($sys_end) ) )
	echo "<div class='system_error'>Bitte Bereich eingeben.</div>";
	else
	echo "<div class='system_error'>Keine passenden Planeten gefunden.</div>";
}
else
{
	switch ( $ansicht )
	{
		case "beide": $teiler = 1.54; break;
		case "taktisch": $teiler = 1.14; break;
		case "geologisch": $teiler = 0.6; break;
		case "auto":
			if ( $objekt == "---" ) $teiler = 0.6;
			else $teiler = 1.14;
			break;
		default: $teiler = 1; break;
	}
	if ( $user_planibilder == "1" ) $teiler = $teiler + 0.05;
	if ( $reserv == "1" ) $teiler = $teiler + 0.1;
	// Merkmale
	if( defined('SPECIALSEARCH') && SPECIALSEARCH === TRUE) {
		if( !empty($merkmal) && $merkmal !== "%" ) echo "<div class='doc_centered'>Ausgabe von Planeten mit " . $merkmal . "</div>\n";
	}

	if ( defined('SHOWWITHOUTSCAN') && SHOWWITHOUTSCAN === TRUE) {
		if( !empty($gal_start) && !empty($sys_start) && $gal_start == $gal_end && $sys_start == $sys_end) {
			$syssql = "SELECT date, nebula FROM " . $db_tb_sysscans . " WHERE gal=" . $gal_start . " AND sys=" . $sys_start;

			$result1 = $db->db_query($syssql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $syssql);
			$row1 = $db->db_fetch_array($result1);

			if(!empty($row1)) {
				$rtime = round((time() - $row1['date']) / (24 * 60 * 60));

				if ( $rtime == 0 ) {
					echo "<br/><b>System zuletzt gescannt: heute</b><br/><br/>";
				} else if ( $rtime == 1 ) {
					echo "<br/><b>System zuletzt gescannt: gestern</b><br/><br/>";
				} else {
					echo "<br/><b>System zuletzt gescannt: vor " . $rtime . " Tagen</b><br/><br/>";
				}
				switch ($row1['nebula']) {
					case "BLN": echo "<b>Blauer Nebel</b><br/><br/>"; break;
					case "GEN": echo "<b>Gelber Nebel</b><br/><br/>"; break;
					case "GRN": echo "<b>Grüner Nebel</b><br/><br/>"; break;
					case "RON": echo "<b>Roter Nebel</b><br/><br/>"; break;
					case "VIN": echo "<b>Violetter Nebel</b><br/><br/>"; break;
				}
			}
		}
	}

	?>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor"
	style="width: 90%;">
	<tr>
	<?php
	if ( $user_planibilder == "1" )
	{
		?>
		<td class="titlebg" style="width: <?php echo (5 / $teiler);?>%;" valign="middle" align="center">
		&nbsp;</td>
		<?php
}
if ( ( ( $ansicht == "auto") && ( $objekt != "---" ) ) || ( $ansicht == "taktisch") || ( $ansicht == "beide") )
{
	?>
		<td class="titlebg" style="width: <?php echo (12 / $teiler);?>%;" valign="middle" align="center">
		<b>Koords</b></td>
		<td class="titlebg" style="width: <?php echo (14 / $teiler);?>%;" valign="middle" align="center">
		<b>Planetentyp</b></td>
		<td class="titlebg" style="width: <?php echo (14 / $teiler);?>%;" valign="middle" align="center">
		<b>Objekttyp</b></td>
		<td class="titlebg" style="width: <?php echo (16 / $teiler);?>%;" valign="middle" align="center">
		<a
			href="index.php?action=showgalaxy&user=<?php echo urlencode($row['user']);?>&order=user&orderd=desc&sid=<?php echo $sid;?>"><img
			src="bilder/desc.gif" border="0"></a> <b>Spieler-<br>
		name</b> <a
			href="index.php?action=showgalaxy&user=<?php echo urlencode($row['user']);?>&order=user&orderd=asc&sid=<?php echo $sid;?>"><img
			src="bilder/asc.gif" border="0"></a></td>
		<td class="titlebg" style="width: <?php echo (12 / $teiler);?>%;" valign="middle" align="center">
		<b>Allitag</b></td>
		<td class="titlebg" style="width: <?php echo (16 / $teiler);?>%;" valign="middle" align="center">
		<b>Planeten-<br>
		name</b></td>
		<!--
		<td class="titlebg" style="width: <?php echo (9 / $teiler);?>%;" valign="middle" align="center">
		<b>Punkte</b></td>
		-->
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>letztes Update</b></td>
		<td class="titlebg" style="width: <?php echo (11 / $teiler);?>%;" valign="middle" align="center">
		<b>Scan / Raid</b></td>

		<?php
}
$order  = getVar('order');
$orderd = getVar('orderd');
if ( ( ( $ansicht == "auto") && ( $objekt == "---" ) ) || ( $ansicht == "geologisch") || ( $ansicht == "beide") )
{
	if ( $ansicht != "beide" )
	{
		?>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Koordinaten</b></td>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Planetentyp</b></td>
		<?php
}
?>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Eisen-<br>
		gehalt</b></td>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Chemie-<br>
		vorkommen</b></td>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Eisdichte</b></td>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Lebens-<br>
		bedingungen</b></td>
		<?php
		//if (  (!empty($kgmod)) OR (!empty($dgmod)) OR (!empty($ksmod)) OR (!empty($dsmod)) ) {
		if ( (!empty($kgmod_von)) OR (!empty($dgmod_von)) OR (!empty($ksmod_von)) OR (!empty($dsmod_von)) OR (!empty($kgmod_bis)) OR (!empty($dgmod_bis)) OR (!empty($ksmod_bis)) OR (!empty($dsmod_bis)) OR (!empty($fmod_von)) OR (!empty($fmod_bis)) OR (!empty($kgmod)) OR (!empty($dgmod)) OR (!empty($ksmod)) OR (!empty($dsmod)) OR (!empty($fmod)) ) {
			?>
		<td class="titlebg" style="width: <?php echo (9 / $teiler);?>%;" valign="middle" align="center">
		<b title="Gebäudekostenmodifikation">kgmod</b></td>
		<td class="titlebg" style="width: <?php echo (9 / $teiler);?>%;" valign="middle" align="center">
		<b title="Gebäudekostendauer">dgmod</b></td>
		<td class="titlebg" style="width: <?php echo (9 / $teiler);?>%;" valign="middle" align="center">
		<b title="Schiffkostenmodifikation">ksmod</b></td>
		<td class="titlebg" style="width: <?php echo (9 / $teiler);?>%;" valign="middle" align="center">
		<b title="Schiffmodifikation">dsmod</b></td>
		<?php
}
?>
<?php
if ( (!empty($grav_von)) OR (!empty($grav_bis)) ) {
	?>
		<td class="titlebg" style="width: <?php echo (9 / $teiler);?>%;" valign="middle" align="center">
		<b title="Gravitation">grav</b></td>
		<?php
}
?>


<?php
}
if ( $reserv == "1" )
{
	if ( !isset($clean_reserv) || $clean_reserv != 1 ) {
		echo '<b><font size="2">[<a href="index.php?action=showgalaxy&reserv=1&clean_reserv=1&ansicht=geologisch"><font color="red">Leichen löschen</font></a>]</font></b>';
	} else {
		$sql99 = "UPDATE `" . $db_tb_scans . "` SET `reserviert`=NULL WHERE `user`!=''";
		$result99 = $db->db_query($sql99)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql99);
		echo '<b><font color="red" size="2">Leichen wurden gelöscht...</font></b><br>';
		echo 'Seite [<a href="index.php?action=showgalaxy&reserv=1&ansicht=geologisch">neu laden</a>], um Änderungen zu sehen!';
	}
	echo '<br><br>';

	?>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>reserviert</b></td>
		<?php
}
if ( strlen($rating_normal) > 0 )
{
	?>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Rating</b></td>
		<?php
}
if ( strlen($rating_best_tt) > 0 )
{
	?>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Rating<br>bestes<br>Techteam</b></td>
		<?php
}
if ( strlen($rating_eisen_tt) > 0 )
{
	?>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Rating<br>Techteam<br>Eisen</b></td>
		<?php
}
if ( strlen($rating_chemie_tt) > 0 )
{
	?>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Rating<br>Techteam<br>Chemie</b></td>
		<?php
}
if ( strlen($rating_eis_tt) > 0 )
{
	?>
		<td class="titlebg" style="width: <?php echo (10 / $teiler);?>%;" valign="middle" align="center">
		<b>Rating<br>Techteam<br>Eis</b></td>
		<?php
}
?>
	</tr>
	<?php
	while ( $row = $db->db_fetch_array($result) ) 	{
		if(isset($row['allianz']) && !empty($row['allianz'])) {
			$sql = "SELECT status FROM " . $db_tb_allianzstatus . " WHERE allianz LIKE '" . $row['allianz'] . "'";
			$result_status = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$row_status = $db->db_fetch_array($result_status);
			if(isset($config_allianzstatus[$row_status['status']])) {
				$color = $config_allianzstatus[$row_status['status']];
			} else {
				$color = "white";
				$row_status = "";
			}
		} else {
			$color = "white";
			$row_status = "";
		}
		if ( $row['objekt'] == "Stargate" )
		$color = $config_color['Stargate'];
		if ( $row['objekt'] == "Schwarzes Loch" )
		$color = $config_color['SchwarzesLoch'];
		if ( ! empty($row['reserviert']) )
		$color = $config_color['reserviert'];
		if(!isset($sys_bev))
		$sys_bev = "";
		if (($row['coords_sys'] <> $sys_bev) && ($order1 == ""))
		{
			?>
</table>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor"
	style="width: 90%;">
	<?php
	$sys_bev = $row['coords_sys'];
}
?>
	<tr>
	<?php
	if ( $user_planibilder == "1" )
	{
		$path = "bilder/planeten/40x40/";
		switch ($row['typ'])	{
			case "Steinklumpen":
				$path .= "stein/st_" . str_pad(mt_rand(1, 53), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Eisplanet":
				$path .= "eis/eis_" . str_pad(mt_rand(1, 34), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Gasgigant":
				$path .= "gas/gas_" . str_pad(mt_rand(1, 30), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Asteroid":
				$path .= "asteroiden/ast_" . str_pad(mt_rand(1, 45), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Nichts":
				$path .= "nix/nix_" . str_pad(mt_rand(1, 4), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Sonne":
				$path .= "sonne.jpg"; break;
			default:
				$path .= "bes/bes_" . str_pad(mt_rand(1, 20), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
		}
		if ( $row['objekt'] == "Schwarzes Loch" )
		$path = 'bilder/planeten/40x40/schwarzesloch.jpg'; ?>
		<td class="titlebg" style="width: <?php echo (5 / $teiler);?>%;" valign="middle" align="center">
		<img src="<?php echo $path;?>" border="0"></td>
		<?php
}
if ( ( ( $ansicht == "auto") && ( $objekt != "---" ) ) || ( $ansicht == "taktisch") || ( $ansicht == "beide") )
{
	?>
		<td class="windowbg2" style="width: <?php echo (12 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['coords'];?></a>
		</td>
		<td class="windowbg2" style="width: <?php echo (14 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['typ'];?></a>
		</td>
		<td class="windowbg2" style="width: <?php echo (14 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['objekt'];?></a>
		</td>
		<td class="windowbg2" style="width: <?php echo (16 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showgalaxy&user=<?php echo urlencode($row['user']);?>&exact=1&sid=<?php echo $sid;?>"><?php echo $row['user'];?></a>
		</td>
		<td class="windowbg2" style="width: <?php echo (12 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showgalaxy&allianz=<?php echo $row['allianz'];?>&sid=<?php echo $sid;?>"><?php echo $row['allianz'];?><?php echo ( ( empty($row_status['status']) ) || ( $row_status['status'] == 'own' ) ) ? "": " (" . $row_status['status'] . ")";?></a>
		</td>
		<td class="windowbg2" style="width: <?php echo (16 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['planetenname'];?></a>
		</td>
		<!--
		<td class="windowbg2" style="width: <?php echo (9 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['punkte'];?></a>
		</td>
		-->
		<td class="windowbg2" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>">
			<?php
			if ( defined('SHOWWITHOUTSCAN') && SHOWWITHOUTSCAN === TRUE) {
				if(empty($row['time']) ) {
					echo "/";
				} else {
					$rtime = round((time() - $row['time']) / (24 * 60 * 60));
					if ($rtime > $config_geoscan_yellow && $rtime <= $config_geoscan_red) {
						echo "<div class='doc_yellow'>" . $rtime . " Tage</div>";
					} else if ($rtime > $config_geoscan_red) {
						echo "<div class='doc_red'>" . $rtime . " Tage</div>";
					} else 			echo $rtime . " Tage";
				}
			} else  echo (empty($row['time'])) ? "/" : round((time() - $row['time']) / (24 * 60 * 60)) . " Tage"; ?>
		</a></td>
		<td class="windowbg2" style="width: <?php echo (11 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>">
			<?php
			if ( ! empty($row['geb'])) echo "Gebäude<br>";
			if (( ! empty($row['plan'])) OR ( ! empty($row['stat'])) OR ( ! empty($row['def']))) echo "Schiffe<br>";
			if ( ($row['lager_chemie'] > 0) || ($row['lager_eis'] > 0) || ($row['lager_energie'] > 0) )  {
				echo "Koloinfo<br>";
			}
			if ( ($row['eisengehalt'] > 0) || ($row['chemievorkommen'] > 0) || ($row['eisdichte'] > 0) )  {
				echo "Geo<br>";
				echo "(" . rating( 0 , $row['coords']) . ")";
			}
			?> </a>&nbsp; <?php
			/*
			// Raid-Tabelle wird zu gross... daher raus damit.
			$sql = "SELECT * FROM " . $db_tb_raid . " WHERE coords='" . $row['coords'] . "'";
			$resultraid = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$rowraid = $db->db_fetch_array($resultraid);
			if ($rowraid['id']) echo "<a href=\"index.php?action=showraid&coords=" . $row['coords'] . "&sid=" . $sid . "\">Raid</a>";
			*/
			?></td>
			<?php
}
if ( ( ( $ansicht == "auto") && ( $objekt == "---" ) ) || ( $ansicht == "geologisch") || ( $ansicht == "beide") ) {
	if ( $ansicht != "beide" ) 	{ ?>
		<td class="windowbg2" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['coords'];?></a>
		</td>
		<td class="windowbg2" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['typ'];?></a>
		</td>
		<?php
}
?>
		<td class="windowbg2" align="right" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php
			if ($row['eisengehalt'] > 100) {
				echo "<b>" . $row['eisengehalt'] . "</b>";
			} else {
				echo $row['eisengehalt'];
			}
			if ($row['tteisen'] > 0) {
				echo ", max.";
				if ($row['tteisen'] > 130) {
					echo "<b><font color='red'>" . $row['tteisen'] . "</font></b>";
				} else if ($row['tteisen'] > 100) {
					echo "<b>" . $row['tteisen'] . "</b>";
				} else {
					echo $row['tteisen'];
				}
			}
			?> </a></td>
		<td class="windowbg2" align="right" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php
			if ($row['chemievorkommen'] > 100) {
				echo "<b>" . $row['chemievorkommen'] . "</b>";
			} else {
				echo $row['chemievorkommen'];
			}
			if ($row['ttchemie'] > 0) {
				echo ", max.";
				if ($row['ttchemie'] > 130) {
					echo "<b><font color='red'>" . $row['ttchemie'] . "</font></b>";
				} else if ($row['ttchemie'] > 100) {
					echo "<b>" . $row['ttchemie'] . "</b>";
				} else {
					echo $row['ttchemie'];
				}
			}
			?> </a></td>
		<td class="windowbg2" align="right" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php
			if ($row['eisdichte'] > 30 ) {
				echo "<b>" . $row['eisdichte'] . "</b>";
			} else {
				echo $row['eisdichte'];
			}
			if ($row['tteis'] > 0) {
				echo ", max.";
				if ($row['tteis'] > 100) {
					echo "<b><font color='red'>" . $row['tteis'] . "</font></b>";
				} else if ($row['tteis'] > 30 ) {
					echo "<b>" . $row['tteis'] . "</b>";
				} else {
					echo $row['tteis'];
				}
			}
			?> </a></td>
		<td class="windowbg2" align="right" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo ($row['lebensbedingungen'] > 100) ? "<b>" . $row['lebensbedingungen'] . "</b>": $row['lebensbedingungen'];?></a>

		</td>
		<?php if ( !empty($kgmod) OR !empty($dgmod) OR !empty($ksmod) OR !empty($dsmod) ) { ?>
		<td class="windowbg2" style="width: <?php echo (9 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['kgmod'];?></a>
		</td>
		<td class="windowbg2" style="width: <?php echo (9 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['dgmod'];?></a>
		</td>
		<td class="windowbg2" style="width: <?php echo (9 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['ksmod'];?></a>
		</td>
		<td class="windowbg2" style="width: <?php echo (9 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['dsmod'];?></a>
		</td>
		<?php
}
?>
<?php if ( !empty($grav_von) OR !empty($grav_bis) ) { ?>
		<td class="windowbg2" style="width: <?php echo (9 / $teiler);?>%; background-color: <?php echo $color;?>;" valign="middle" align="center">
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['gravitation'];?></a>
		</td>
		<?php
}
?>


<?php 		}  	if ( $reserv == "1" ) 	{ ?>
		<td class="windowbg2" align="center" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo $row['reserviert'];?></a>
		</td>
		<?php 	} ?>
<?php 		  	if ( strlen($rating_normal) > 0 ) 	{ ?>
		<td class="windowbg2" align="center" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo sprintf("%.2f", $row['rating_normal']);?></a>
		</td>
		<?php 	} ?>
<?php 		  	if ( strlen($rating_best_tt) > 0 ) 	{ ?>
		<td class="windowbg2" align="center" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo sprintf("%.2f", $row['rating_best_tt']);?></a>
		</td>
		<?php 	} ?>
<?php 		  	if ( strlen($rating_eisen_tt) > 0 ) 	{ ?>
		<td class="windowbg2" align="center" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo sprintf("%.2f", $row['rating_eisen_tt']);?></a>
		</td>
		<?php 	} ?>
<?php 		  	if ( strlen($rating_chemie_tt) > 0 ) 	{ ?>
		<td class="windowbg2" align="center" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo sprintf("%.2f", $row['rating_chemie_tt']);?></a>
		</td>
		<?php 	} ?>
<?php 		  	if ( strlen($rating_eis_tt) > 0 ) 	{ ?>
		<td class="windowbg2" align="center" style="width: <?php echo (10 / $teiler);?>%; background-color: <?php echo $color;?>;" >
		<a
			href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=<?php echo $ansicht;?>&sid=<?php echo $sid;?>"><?php echo sprintf("%.2f", $row['rating_eis_tt']);?></a>
		</td>
		<?php 	} ?>

	</tr>
	<?php 	} } ?>
</table>
	<?php } ?>