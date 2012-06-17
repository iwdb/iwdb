<?php
/*****************************************************************************/
/* s_raid.php                                                                */
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

/*****************************************************************************/
/* Diese Erweiterung der ursp?nglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens daf?r eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                                                                           */
/*****************************************************************************/
/*
if (!defined('IRA'))
die('Hacking attempt...');
*/
// ****************************************************************************
// Konvertiert alternative Ressnamen in normale Ressnamen.
function getRessname($text) {
	switch (trim(strtoupper($text))) {
		case 'ERDBEEREN' :
		case 'EISEN' :
			return 'Eisen';
		case 'ERDBEERMATSCH' :
		case 'STAHL' :
			return 'Stahl';
		case 'ERDBEERKONFIT&UUML;RE' :
		case 'VV4A' :
			return 'VV4A';
		case 'BRAUSE' :
		case 'CHEM. ELEMENTE' :
			return 'chem. Elemente';
		case 'EISMATSCH' :
		case 'SCHNEEMATSCH' :
		case 'WASSER' :
			return 'Wasser';
		case 'VANILLEEIS' :
		case 'EIS' :
			return 'Eis';
		case 'TRAUBENZUCKER' :
		case 'ENERGIE' :
			return 'Energie';
		case 'BEV&OUML;LKERUNG' :
			return 'Volk';
	}
}

function parse_raid($scanlines) {
	global $db, $db_tb_raid, $config_date;

	/*
	// eintragungen inm die raidtabelle
	$textinput = "";
	preg_match('/Kampf auf dem Planeten .* (\d+:\d+:\d+)\. .*Besitzer ist/', $scanlines[0], $temp);
	foreach($scanlines as $scan) {
		$textinput .= $scan . "\r\n";
	}

	$sql = "INSERT INTO " . $db_tb_raid . " (coords, date, bericht) VALUES ('" .
	$temp[1] . "', '" . $config_date . "', '" . $textinput . "')";
	$result = $db->db_query($sql)
	or error(GENERAL_ERROR,
             'Could not query config information.', '',
	__FILE__, __LINE__, $sql);
	*/


	// modul-raidview installiert ??
	if(file_exists("./config/m_raidview.cfg.php")){
		global $db_tb_raidview, $selectedusername;

		// links sammeln die bereits in der db drinnen sind
		$sqlL = "SELECT link FROM " . $db_tb_raidview;
		$resultL = $db->db_query($sqlL)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
		__FILE__, __LINE__, $sqlL);
		$links=array();
		while($rowL=$db->db_fetch_array($resultL)) {
			$links[] = $rowL['link'];
		}

		$vars = array(
	'eisen',
	'stahl',
	'vv4a',
	'chem',
	'eis',
	'wasser',
	'energie',
	'v_eisen',
	'v_stahl',
	'v_vv4a',
	'v_chem',
	'v_eis',
	'v_wasser',
	'v_energie',
	'g_eisen',
	'g_stahl',
	'g_vv4a',
	'g_chem',
	'g_eis',
	'g_wasser',
	'g_energie',
	);

		foreach($vars as $var) {
			${$var} = 0;
		}

		$status ="";
		foreach($scanlines as $scan){

			//der nette Link
			if ( !(StrPos( $scan , htmlentities('http://www.icewars.de/portal/kb/de/kb.php', ENT_QUOTES, 'UTF-8') ) === FALSE) ) {
				$link = trim($scan);
			}
			//echo $scan."<br>";
			// ZielPlani
			if (preg_match('/Kampf auf dem Planeten .* (\d+:\d+:\d+)\. .*Besitzer ist/', $scan, $temp) >0){
				$plani = $temp[1];
			}
			// Angrifszeit
			if (preg_match('/Kampf auf dem Planeten .* \((\d+).(\d+).(\d+) (\d+):(\d+):(\d+)\) .*Besitzer ist/', $scan, $temp) >0){
				$zeit = mktime($temp[4],$temp[5],$temp[6],$temp[2],$temp[1],$temp[3]);

				$geraidet=preg_split('/Besitzer ist/', $scan, 2);
				$geraidet=$geraidet[1];
			}


			//Kampf auf dem Planeten Kolonie von Hinker 15:115:6. (26.04.2007 04:23:56) Besitzer ist Hinker

			//entscheidung: wenn ein String vorkommt dan wird gewechselt
			if(preg_match('/Es wurden folgende Ressourcen/',$scan) > 0) $status = "plundern";
			if(preg_match('/Es wurden folgende Werte beim Angreifer/',$scan) > 0) $status = "angreifer";
			if(preg_match('/Es wurden folgende Werte beim Verteidiger /',$scan) > 0) $status = "verteidiger";

			if($status == "plundern"){
				if (preg_match('/(Eisen|Stahl|VV4A|chem\. Elemente|Eis|Wasser|Bevölkerung|Energie)[\t| ]+(.+)/', $scan, $match) > 0){
					$match[2] = StripNumber($match[2]);
					switch (getRessname($match[1])) {
						case 'Eisen' :
							$eisen = $match[2];
							break;
						case 'Stahl' :
							$stahl = $match[2];
							break;
						case 'VV4A' :
							$vv4a = $match[2];
							break;
						case 'chem. Elemente' :
							$chem = $match[2];
							break;
						case 'Eis' :
							$eis = $match[2];
							break;
						case 'Wasser' :
							$wasser = $match[2];
							break;
						case 'Energie' :
							$energie = $match[2];
							break;
						case 'Volk' :
							$volk = $match[2];
							break;
					}
				}
			}
			
			
			if($status == "angreifer"){
				if (preg_match('/(Eisen|Stahl|VV4A|chem\. Elemente|Eis|Wasser|Bevölkerung|Energie)[\t| ]+(.+)/', $scan, $match) > 0){
					$match[2] = StripNumber($match[2]);
					switch (getRessname($match[1])) {
						case 'Eisen' :
							$v_eisen = $match[2];
							break;
						case 'Stahl' :
							$v_stahl = $match[2];
							break;
						case 'VV4A' :
							$v_vv4a = $match[2];
							break;
						case 'chem. Elemente' :
							$v_chem = $match[2];
							break;
						case 'Eis' :
							$v_eis = $match[2];
							break;
						case 'Wasser' :
							$v_wasser = $match[2];
							break;
						case 'Energie' :
							$v_energie = $match[2];
							break;
					}
				}
			}
			
			
			
		}
		$g_eisen=$eisen-$v_eisen;
		$g_stahl=$stahl-$v_stahl;
		$g_vv4a=$vv4a-$v_vv4a;
		$g_chem=$chem-$v_chem;
		$g_eis=$eis-$v_eis;
		$g_wasser=$wasser-$v_wasser;
		$g_energie=$energie-$v_energie;
		
		
		//  echo $zeit."<br>".$plani."<br>".$eisen.$stahl.$vv4a.$chem.$eis.$wasser.$energie.">br>";

		if ( !isset($link) OR empty($link) ) {
			?>
<divclass'system_notification'>Bericht unvollständig (Link fehlt)</div>
			<?php
}
elseif (in_array($link, $links))
echo "KB <a href=\"".$link."\" target=\"_new\"><i>" . $link=substr($link, 42, 60) . "</i></a> bereits vorhanden.\n";
else {

	$sql = "INSERT INTO $db_tb_raidview (id,coords,date,eisen,stahl,vv4a,chemie,eis,wasser,energie,user,geraided,link,v_eisen,v_stahl,v_vv4a,v_chem,v_eis,v_wasser,v_energie,g_eisen,g_stahl,g_vv4a,g_chem,g_eis,g_wasser,g_energie)
	VALUES ('NULL','$plani','$zeit',$eisen,$stahl,$vv4a,$chem,$eis,$wasser,$energie,'$selectedusername','$geraidet','$link','$v_eisen','$v_stahl','$v_vv4a','$v_chem','$v_eis','$v_wasser','$v_energie','$g_eisen','$g_stahl','$g_vv4a','$g_chem','$g_eis','$g_wasser','$g_energie')";

	//echo $sql;
	//echo "<br>";
	//echo $link;
	$result = $db->db_query($sql)
	or error(GENERAL_ERROR,
             'Could not query config information.', '',
	__FILE__, __LINE__, $sql);
	echo "neuer KB: <a href=\"".$link."\" target=\"_new\">" . $link=substr($link, 42, 60) . "</a>\n";

}
}
}

?>
