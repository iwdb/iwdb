<?php
/*****************************************************************************/
/* s_transferliste.php                                                       */
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
// $Id: s_transferliste.php 205 2007-04-24 18:54:05Z reuq tgarfeg $

/*****************************************************************************/
/* Diese Erweiterung der ursp�nglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens daf�r eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

if (!defined('DEBUG_LEVEL'))
	define('DEBUG_LEVEL', 0);

include_once("./includes/debug.php");

function parse_transferliste($scanlines) {
	global $db, $db_tb_raid, $config_date, $db_tb_transferliste, $db_tb_user;

	$vars = array(
	  'zeitmarke',
	  'buddler',
	  'fleeter',
	  'eisen',
	  'stahl',
	  'vv4a',
	  'chem',
	  'eis',
	  'wasser',
	  'energie',
	  'volk');
		
	foreach($vars as $var) {
	  ${$var} = "";
	}

	$state = 0;
	foreach($scanlines as $scan) {
            $scan = str_replace("von:System","Systemnachricht",$scan);
            $scan = str_replace("von: System","Systemnachricht",$scan);
            $scan = str_replace("von System","Systemnachricht",$scan);
		debug_var('scan', $scan);
		switch ($state) {
		case 0:
			if (preg_match('/Transport angekommen \((\d+:\d+:\d+)\)\s+Systemnachricht\s+(\d+)\.(\d+).(\d+) (\d+):(\d+):(\d+)/', $scan, $match) > 0)
			{
				$planet = $match[1];
				debug_var('planet', $planet);
				$zeitmarke = mktime($match[5], $match[6], $match[7], $match[3], $match[2], $match[4]);
				debug_var('zeitmarke', $zeitmarke);
				$state++;
				debug_var('state', $state);
			}
			break;
		case 1:
			if (preg_match('/Transport/', $scan, $match) > 0)
			{
				$state++;
				debug_var('state', $state);
			}
			else
				$state = 0;
			break;
		case 2: 
			//if (preg_match('/Eine Flotte ist auf dem Planeten (.*) (\d+):(\d+):(\d+) angekommen\. Der Absender ist (.*)\. Der Empfänger ist (.*)\./', $scan, $match) > 0)
			if (preg_match('&Eine Flotte ist auf dem Planeten (.*) (\d+):(\d+):(\d+) angekommen\. Der Absender ist (.*)\. Der Empf\&auml;nger ist (.*)\.&is', $scan, $match) > 0)
			
			{
				$planet = $match[1];
				debug_var('planet', $planet);
				$coord_gal = $match[2];
				debug_var('coord_gal', $coord_gal);
				$coord_sys = $match[3];
				debug_var('coord_sys', $coord_sys);
				$coord_plan = $match[4];
				debug_var('coord_plan', $coord_plan);
				$buddler = $match[5];
				debug_var('buddler', $buddler);
				$fleeter = $match[6];
				debug_var('fleeter', $fleeter);
				$state++;
				debug_var('state', $state);
			}
			else if (preg_match('/Eine Flotte ist auf dem Planeten (\d+):(\d+):(\d+) angekommen\. Der Absender ist (.*)\. Der Empfänger ist (.*)\./', $scan, $match) > 0)
			{
				debug_var('coord_gal', $coord_gal = $match[1]);
				debug_var('coord_sys', $coord_sys = $match[2]);
				debug_var('coord_plan', $coord_plan = $match[3]);
				debug_var('buddler', $buddler = $match[4]);
				debug_var('fleeter', $fleeter = $match[5]);
				debug_var('state', ++$state);
			}	
			else
				$state = 0;
			break;
		case 3:
			if (preg_match('/Es wurden folgende Sachen angeliefert/', $scan, $match) > 0)
			{
				$state++;
				debug_var('state', $state);
			}
			else
				$state = 0;
			break;
		case 4:
			if (preg_match('/Ressourcen/', $scan, $match) > 0)
			{
				$state++;
				debug_var('state', $state);
			}
			else
				$state = 0;
			break;
		case 5:
			if (preg_match('/(Eisen|Erdbeeren|Stahl|Erdbeermarmelade|VV4A|Erdbeerkonfit&uumlre|chem\. Elemente|Brause|Eis|Vanilleeis|Wasser|Schneematch|Eismatsch|Bev&ouml;lkerung|Energie|Traubenzucker)[\t| ]+(\d+)/', $scan, $match) > 0)
			{
				switch (getRessname($match[1])) {
				case 'Eisen' : 
					$eisen = $match[2]; 
					debug_var('eisen', $eisen);
					break;
				case 'Stahl' : 
					$stahl = $match[2]; 
					debug_var('stahl', $stahl);
					break;
				case 'VV4A' : 
					$vv4a = $match[2];
					debug_var('vv4a', $vv4a);
					break;
				case 'chem. Elemente' :
					$chem = $match[2];
					debug_var('chem', $chem);
					break;
				case 'Eis' :
					$eis = $match[2];
					debug_var('eis', $eis);
					break;
				case 'Wasser' :
					$wasser = $match[2];
					debug_var('wasser', $wasser);
					break;
				case 'Energie' :
					$energie = $match[2];
					debug_var('energie', $energie);
					break;
				case 'Volk' :
					$volk = $match[2];
					debug_var('Volk', $volk);
					break;
				}
			}
			else
				$state = 0;
			break;
		}
	}

	if(empty($zeitmarke) || empty($buddler) || empty($fleeter)) {
	  doc_message("Fehler im Bericht - (" . $zeitmarke . ", " . $buddler .", " . $fleeter . ")");
	  return;
	}
    
    // Lieferungen an sich selbst ignorieren
    // Manuell: DELETE FROM `prefix_transferliste` WHERE `buddler`=`fleeter`
    if(!empty($zeitmarke) && $buddler == $fleeter) {
      doc_message("Bericht ".$zeitmarke." vom ".strftime("%d.%m.%Y %H:%M:%S", $zeitmarke)." ignoriert! - Absender und Empfänger sind identisch...");
      return;
    }

	$sql = "SELECT COUNT(*) AS anzahl FROM " . $db_tb_transferliste . 
	       " WHERE zeitmarke=" . $zeitmarke . " AND buddler='" . $buddler . 
				 "' AND fleeter='" . $fleeter . "'"; 
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
						 
	$row = $db->db_fetch_array($result);	
	// Not found, so insert new
	if(empty($row) || $row['anzahl'] == 0) {
	  $sql = "INSERT INTO " . $db_tb_transferliste . "(";
		$val = "";
		
  	foreach($vars as $var) {
  	  $sql .=  $var . ", ";
			$val .= "'" . ${$var} . "', ";
  	}
		$sql = substr($sql, 0, (strlen($sql) - 2));
		$val = substr($val, 0, (strlen($val) - 2));
		$sql .= ") VALUES( " . $val . ")";		
	} else {
	  $sql = "UPDATE " . $db_tb_transferliste . " SET ";
  	foreach($vars as $var) {
  	  $sql .= $var . "='" . ${$var} . "', ";
		}
		$sql = substr($sql, 0, (strlen($sql) - 2)) .
		       " WHERE zeitmarke=" . $zeitmarke . 
		       " AND buddler='" . $buddler . "' AND fleeter='" . $fleeter . "'"; 
	}
	
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
    
    // Aktualisierungszeit für Transportberichte setzen
    $sql = "UPDATE " . $db_tb_user . " SET lasttransport='" . $config_date . 
         "' WHERE sitterlogin='" . $buddler . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 
             'Could not query config information.', '',
              __FILE__, __LINE__, $sql);
}
/*
// ****************************************************************************
// Gibt den Wert einer Variablen aus.
function debug_var($name, $wert, $level = 2) {
	if (DEBUG_LEVEL >= $level)
		echo "<div class='system_debug_blue'>$" . $name . ":'" . $wert . "'</div>";
}
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

// ****************************************************************************
//
//
function display_transferliste() {
  include "./modules/m_transferliste.php";
}

?>