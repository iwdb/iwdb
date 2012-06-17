<?php
/*****************************************************************************/
/* s_gebaeudescan.php                                                        */
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
/* Diese Erweiterung der urspünglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                                                                           */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

include_once "./parser/i_planet.php";  
  
//*****************************************************************************
//
function parse_gebaeudescan($scanlines) {
  global $scan_data;

  $scan_data = reset_data();
  $cat = "";
  foreach($scanlines as $scan) {
    if(strpos($scan, "Sondierungsbericht (Gebäude) von") !== FALSE ) {
    	$scan_data['coords'] = trim(str_replace("Sondierungsbericht (Gebäude) von", "", $scan));
    	$scan_data['coords'] = preg_replace('/\s*\[.+\]\s*/', '', $scan_data['coords']);
    } else {
      checkline( $scan, $scan_data, $cat);
    }
  }

	switch ( updateplanet() ) {
	case 0: echo "<div class='system_error'>Der Scan ist nicht komplett!</div>"; break;
	case 1: echo "<div class='system_notification'>Planet " . $scan_data['coords'] . " aktualisiert.</div>"; break;
	case 2: echo "<div class='system_notification'>Neuen Planeten " . $scan_data['coords'] . " hinzugefügt.</div>"; break;
	case 3: echo "<div class='system_notification'>Neuer Planet " . $scan_data['coords'] . " . Planetendaten aktualisiert.</div>"; break;
	}    
}
?>