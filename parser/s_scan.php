<?php
/*****************************************************************************/
/* s_schiffscan.php                                                          */
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
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

/*****************************************************************************/
/* bei speziellen Fragen:                     martinmartimeo / reuq tgarfeg  */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

include_once "./parser/i_planet.php";  
  
//*****************************************************************************
//


if (function_exists('parse_scan') === false) {
function parse_scan($scanlines) {

  global $scan_data,$sid;

  $scan_data = reset_data();
  $cat = "";
  $scanzeug_str = array('Planetentyp' , 'Objekttyp');
  $scanzeug_num = array('Eisen', 'Stahl', 'VV4A', 'Eis', 'Wasser', 'Energie' , 'chem. Elemente');
  $scanzeug = array_merge($scanzeug_str,$scanzeug_num);

  foreach($scanlines as $scan) {
     
    //Cat auf Building setzen, da bei Gebäude keine Überschrift existiert
    if ( strpos($scan, "Sondierungsbericht (Geb&auml;ude) von") !== FALSE ) $cat = "bs";

    if(  (strpos($scan, "Sondierungsbericht (Schiffe) von") !== FALSE) OR (strpos($scan, "Sondierungsbericht (Geb&auml;ude) von") !== FALSE) ) {
    	$scan = trim(str_replace("Sondierungsbericht (Schiffe) von", "", $scan));
    	$scan = trim(str_replace("Sondierungsbericht (Geb&auml;ude) von", "", $scan));
	if ($scan = preg_replace('/am \d+\.\d+\.\d+. \d+:\d+\. Besitzer ist /', '(', $scan)) {
		$scan = substr($scan, 0, count($scan) - 2);
		$scan .= ")";
	}
    	$temp = preg_replace('/\s*\[.+\]\s*/', '', $scan);
      $temparray = explode(" ",trim($temp),2);
      $scan_data['coords'] = $temparray[0];

       //ab hier nur, wenn es sich um einen besiedelten Planeten handelt (erkennbar daran das der Name in runden Klammern vorkommt)
      if ( strpos ($scan , "(") > 0 ) {

      $scan_data['user'] = substr( trim(preg_replace('()', '', $temparray[1])) , 1 , count(trim(preg_replace('()', '', $temparray[1]))) - 2 );
      
      $tempaliname = trim(substr($scan , strpos($scan,'(') + 1 , strpos($scan,')') - 1 - strpos($scan,'(') ) ) ;   

      if (strpos($tempaliname,'[') > 0) {
      $scan_data['allianz'] = trim(substr($tempaliname , strpos($tempaliname,'[') + 1 , strpos($tempaliname,']') - 1 - strpos($tempaliname,'[') ) ) ;
      } else {
      $scan_data['allianz'] = '';
      }

       } else {

         $scan_data['user'] = '';
         $scan_data['allianz'] = '';

       }

      $temp = split(":",$scan_data['coords']);
      $scan_data['coords_gal'] = $temp[0];
      $scan_data['coords_sys'] = $temp[1];
      $scan_data['coords_planet'] = $temp[2];

      //$scan löschen da ja Parser für diese Zeile gefunden wurde
      $scan = "";
    }

//parsen
for ( $i = 0 ; $i < count($scanzeug) ; $i++ )
{

  if ( strpos($scan, $scanzeug[$i] ) !== FALSE  && strpos(trim($scan), $scanzeug[$i]) == 0 ) {

       $scan = str_replace($scanzeug[$i], "", $scan);
    //soll der Typ eine Nummer sein oder ein String?
    if ( in_array($scanzeug[$i] , $scanzeug_num) ){
           $scan_data[$scanzeug[$i]] = stripNumber(trim($scan));
    } else {
           $scan_data[$scanzeug[$i]] = trim($scan);
    }
       $scan = "";
   }

}


  if ( strpos($scan, 'Planetare Flotte' ) !== FALSE ) {
     $cat = "pf";
     $scan  = "";
   }
  if ( strpos($scan, 'Defence' ) !== FALSE ) {
     $cat = "de";
     $scan  = "";
   }

  if ( ( strpos($scan, 'Schiffe' )  !== FALSE ) AND ( strpos($scan, 'Flotte' ) === FALSE ) ) {
     $cat = "se";
     $scan  = "";
   }
  if ( strpos($scan, 'Ressourcen' ) !== FALSE ) {
     $cat = "rc";
     $scan  = "";
   }

  if ( strpos($scan, 'Stationierte Flotte von' ) !== FALSE ) {
     $cat = "st";

     $scan = str_replace('Stationierte Flotte von', '', $scan);
     $anzahl = trim(substr($scan , strpos($scan,'(') + 1 , strpos($scan,')') - 1 - strpos($scan,'(') ) ) ; 
     $scan = str_replace($anzahl, '', $scan);
     $anzahl = str_replace('Schiffe', '', $scan);  
     $scan = str_replace('(', '', $scan);
     $scan = str_replace(')', '', $scan);
     $anzahl = trim($anzahl);
     $scan = trim($scan);

     $owner = $scan;

     $scan  = "";
   }


//zum herauslesen von Schiffen wird der String umgedreht, das letzte (nun erste) abgdrennt
//dann beides umgedreht
//und einggefügt wo wir gerade sind

if ( strpos($scan, " " ) > 0 ) {

if ( ($cat == "pf") OR ($cat == "de") OR ($cat == "bs") OR ($cat == "st") ) {

$scangedreht = strrev($scan);
$scantemp = explode(' ', trim($scangedreht), 2);

if (count($scantemp) > 1)
	$objtyp = trim(strrev($scantemp[1]));
else
	$objtyp = '';
$objvar = trim(strrev($scantemp[0]));

 if ($cat != "st") {
//keine stationeirte Flotte dann normal, wenn eine stationeirte FLotte,
//dann muss das speziell gespeichert werden
  $scan_alldata[$cat][$objtyp] = $objvar;
 } else {
  $scan_alldata[$cat][$owner][$objtyp] = $objvar;
 }


  }

}

}


//Die Daten aus dem scan_alldata auslesen und in stringsspeichern
if ( isset($scan_alldata['pf']) AND count($scan_alldata['pf']) > 0 ) {

//war für externen Gebrauch: $str_pf = "<b class=\"scan_titel\"> planetare Flotte </b> \n";
$str_pf = "";
$str_pf = $str_pf . "\n <table class=\"scan_table\"> \n";
foreach ($scan_alldata['pf'] as $key => $value) {
  $str_pf = $str_pf . "   <tr class=\"scan_row\"> \n";
  $str_pf = $str_pf . "     <td class=\"scan_object\"> \n";
  $str_pf = $str_pf . $key . "\n";;
  $str_pf = $str_pf . "     </td> \n";
  $str_pf = $str_pf . "     <td class=\"scan_value\"> \n";
  $str_pf = $str_pf . $value . "\n";
  $str_pf = $str_pf . "     </td> \n";
  $str_pf = $str_pf . "   </tr> \n";
}
$str_pf = $str_pf . "\n </table> \n";

} else {

$str_pf = -1;

}

if ( isset($scan_alldata['bs']) AND count($scan_alldata['bs']) > 0 ) {

//war für externen Gebrauch: $str_bs = "<b class=\"scan_titel\"> Geb&auml;ude </b> \n";
$str_bs = "";
$str_bs = $str_bs . "\n <table class=\"scan_table\"> \n";
foreach ($scan_alldata['bs'] as $key => $value) {
  $str_bs = $str_bs . "   <tr class=\"scan_row\"> \n";
  $str_bs = $str_bs . "     <td class=\"scan_object\"> \n";
  $str_bs = $str_bs . $key . "\n";;
  $str_bs = $str_bs . "     </td> \n";
  $str_bs = $str_bs . "     <td class=\"scan_value\"> \n";
  $str_bs = $str_bs . $value . "\n";
  $str_bs = $str_bs . "     </td> \n";
  $str_bs = $str_bs . "   </tr> \n";
}
$str_bs = $str_bs . "\n </table> \n";

} else {

$str_bs = -1;

}

if ( isset($scan_alldata['st']) AND count($scan_alldata['st']) > 0 ) {

//war für externen Gebrauch: $str_bs = "<b class=\"scan_titel\"> stationeirte Flotten</b> \n";
$str_st = "";
$str_st = $str_st . "\n <table class=\"scan_table\"> \n";
foreach ($scan_alldata['st'] as $o_key => $o_array) {

  $str_st = $str_st . "   <tr class=\"scan_row\"> \n";
  $str_st = $str_st . "     <td colspan=\"2\" class=\"scan_title\">&nbsp;&nbsp;Stationierte Flotte von <a href=\"index.php?action=showgalaxy&sid=" . $sid . "&user=" . urldecode(trim($o_key)) . "\">" . $o_key . "</a>: </td> \n";
  $str_st = $str_st . "   </tr> \n";
foreach ($o_array as $key => $value) {
  $str_st = $str_st . "   <tr class=\"scan_row\"> \n";
  $str_st = $str_st . "     <td class=\"scan_object\"> \n";
  $str_st = $str_st . $key . "\n";;
  $str_st = $str_st . "     </td> \n";
  $str_st = $str_st . "     <td class=\"scan_value\"> \n";
  $str_st = $str_st . $value . "\n";
  $str_st = $str_st . "     </td> \n";
  $str_st = $str_st . "   </tr> \n";
}

}
$str_st = $str_st . "\n </table> \n";

} else {

$str_st = -1;

}


if ( isset($scan_alldata['de']) AND  count($scan_alldata['de']) > 0 ) {

//war für externen Gebrauch: $str_de = "<b class=\"scan_titel\"> Defence </b> \n";
$str_de = "";
$str_de = $str_de . "\n <table class=\"scan_table\"> \n";
foreach ($scan_alldata['de'] as $key => $value) {
  $str_de = $str_de . "   <tr class=\"scan_row\"> \n";
  $str_de = $str_de . "     <td class=\"scan_object\"> \n";
  $str_de = $str_de . $key . "\n";;
  $str_de = $str_de . "     </td> \n";
  $str_de = $str_de . "     <td class=\"scan_value\"> \n";
  $str_de = $str_de . $value . "\n";
  $str_de = $str_de . "     </td> \n";
  $str_de = $str_de . "   </tr> \n";
}
$str_de = $str_de . "\n </table> \n";

} else {

$str_de = -1;

}


//Array noch umstrukturieren, damit dieser von updateplanet richitg eingetragen werden kann

$scan_data['typ'] = $scan_data['Planetentyp'];
unset($scan_data['Planetentyp']);

$scan_data['objekt'] = $scan_data['Objekttyp'];
unset($scan_data['Objekttyp']);

$scan_data['chemie'] = $scan_data['chem. Elemente'];
unset($scan_data['chem. Elemente']);

global $sfscan,$gebscan;

if ($str_de <> -1) {
$scan_data['def'] = $str_de;
} else {
	if ( $sfscan ) {
		$scan_data['def'] = '';
	} else {
    unset($scan_data['def']);
  }
}

if ($str_st <> -1) {
$scan_data['stat'] = $str_st;
} else {
	if ( $sfscan ) {
		$scan_data['stat'] = '';
	} else {
    unset($scan_data['stat']);
  }
}

if ($str_pf <> -1) {
$scan_data['plan'] = $str_pf;
} else {
	if ( $sfscan ) {
		$scan_data['plan'] = '';
	} else {
    unset($scan_data['plan']);
  }
}

if ($str_bs <> -1) {
$scan_data['geb'] = $str_bs;
} else {
	if ( $gebscan ) {
		$scan_data['geb'] = '';
	} else {
    unset($scan_data['geb']);
  }
}

	switch ( updateplanet() ) {
	case 0: echo "<div class='system_error'>Der Scan ist nicht komplett!</div>"; break;
	case 1: echo "<div class='system_notification'>Planet " . $scan_data['coords'] . " aktualisiert.</div>"; break;
	case 2: echo "<div class='system_notification'>Neuen Planeten " . $scan_data['coords'] . " hinzugef&uuml;gt.</div>"; break;
	case 3: echo "<div class='system_notification'>Neuer Planet " . $scan_data['coords'] . " . Planetendaten aktualisiert.</div>"; break;
	}    
    
      global $coords;
      $coords = $scan_data['coords'];

}

}

if (function_exists('display_scan') === false) {
function display_scan () {
	global $coords, $db, $db_tb_scans, $db_tb_allianzstatus, $user_planibilder;
	include("./modules/showplanet.php");
}
}

?>