<?php
/*****************************************************************************/
/* newscan.php                                                               */
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
if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

$start = microtime(true);

// $debug = TRUE;

/*
	Autoload system for plib parser
*/

$plibfiles = array();
function __autoload($class)
{
	global $plibfiles;
	if (empty($plibfiles))
	{
		function ReadTheDir ($base)
		{
			global $plibfiles;
			$base = realpath($base) . "/";
			$dir = opendir($base);
			while($file = readdir($dir)){
				if	(is_file($base.$file))
				{
					$plibfiles[md5($file)] = $base.$file;
				}
				else if (is_dir ($base.$file."/") && $file != "." && $file != "..")
				{
					ReadTheDir($base.$file."/");
				}
			}
			closedir($dir);
		}
		ReadTheDir ('plib/');
	}
    if (isset($plibfiles[md5($class.".php")]) && file_exists($plibfiles[md5($class.".php")]))
	{
		//! abh. checken
// 		$file = file_get_contents($plibfiles[md5($class.".php")]);
// 		if (preg_match_all('%class\s([.]+)\sextends\s([.]+)%',$file,$treffer, PREG_SET_ORDER) != FALSE)
// 		{
// 			
// 		}
		require_once ($plibfiles[md5($class.".php")]);
	}
}

// Load the different parsers from the parser base. 
$parser = array();
$sql = "SELECT modulename, recognizer, message FROM " . 
        $db_tb_parser . " ORDER BY message ASC";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 
           'Could not query config information.', '', 
           __FILE__, __LINE__, $sql);
           
while( $row = $db->db_fetch_array($result)) {
  $parser[$row['modulename']] = array($row['recognizer'], 0, $row['message']);
}
  

function plural($singular) 
{
  if( preg_match( '/.*sicht$/i', $singular ))
    return ($singular . "en");  

  if( preg_match( '/.*bericht$/i', $singular ))
    return ($singular . "e");  

  if( preg_match( '/.*liste$/i', $singular ))
    return ($singular . "n");  

  if( preg_match( '/.*scan$/i', $singular ))
    return ($singular . "s");  
} 

$selectedusername = getVar('seluser');	
if( empty($selectedusername)) 
  $selectedusername = $user_sitterlogin;

echo "<div class='doc_title'>Neuer Bericht</div>\n";
echo "<form method=\"POST\" action=\"index.php?action=newscan&amp;sid=" . $sid. "\" enctype=\"multipart/form-data\">\n";
echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 90%;\">\n";
echo " <tr>\n";
echo "  <td class=\"windowbg2\" align=\"center\">\n";

global $user_status, $user_sitten;

$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'bericht_fuer_rang' ";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
 $rowP = $db->db_fetch_array($resultP);  

$allow1 = FALSE;

if ( $rowP['value'] == 'hc' AND ( strtolower($user_status) == 'hc' ) ) $allow1 = TRUE;
if ( $rowP['value'] == 'mv' AND ( strtolower($user_status) == 'hc' OR strtolower($user_status) == 'mv' ) ) $allow1 = TRUE;
if ( $rowP['value'] == 'all' AND ( strtolower($user_status) != 'guest' ) ) $allow1 = TRUE;

$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'bericht_fuer_sitter' ";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
 $rowP = $db->db_fetch_array($resultP);  

$allow2 = FALSE;

if ($rowP['value'] == 0 AND ( $user_sitten == 0 OR $user_sitten == 1 ) ) $allow2 = TRUE;
if ($rowP['value'] == 1 AND ( $user_sitten == 1 ) ) $allow2 = TRUE;
if ($rowP['value'] == 3 AND ( $user_sitten == 0 OR $user_sitten == 1 ) ) $allow2 = TRUE;
if ($rowP['value'] == 2 ) $allow2 = TRUE;

if( $user_status == "admin" ) {
 $allow1 = TRUE;
 $allow2 = TRUE;
}

if( $allow1 AND $allow2 ) { 
  echo "   Bericht einf&uuml;gen f&uuml;r\n";
  echo "	 <select name=\"seluser\" style=\"width: 200px;\">\n";

  $sql = "SELECT sitterlogin FROM " . $db_tb_user . " ORDER BY id ASC";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
             
	while( $row = $row = $db->db_fetch_array($result)) {
	  echo "      <option value=\"" . $row['sitterlogin'] . "\"" . ($selectedusername == $row['sitterlogin'] ? " selected" : "") . ">" . $row['sitterlogin'] . "</option>";
	}
  echo " 	 </select><br>\n";
}

echo "   <textarea name=\"text\" rows=\"14\" cols=\"70\"></textarea><br>\n";
echo "   F&uuml;r Hilfe bitte oben auf den \"Hilfe\" Button dr&uuml;cken.\n";
echo "  </td>\n";
echo " </tr>\n";
echo " <tr>\n";
echo "  <td class=\"titlebg\" align=\"center\">\n";
echo "   <input type=\"submit\" value=\"abspeichern\" name=\"B1\" class=\"submit\">\n";
echo "  </td>\n";
echo " </tr>\n";
echo "</table>\n";
echo "</form>\n";

$textinput = getVar('text');
if ( ! empty($textinput) )
{
	$count = 0;
	$textinput = str_replace(" \t", " ", $textinput);
	$textinput = str_replace("\t", " ", $textinput);

	$text = str_replace("%", "\\%", $textinput);
	$text = str_replace(" (HC)", "", $text);
	$text = str_replace(" (iHC)", "", $text);
	$text = str_replace("\r", "\n ", $text);
	$text = str_replace("\n \n", "\n", $text);

	$text = str_replace("Erdbeeren", "Eisen", $text);
	$text = str_replace("Erdbeermarmelade", "Stahl", $text);
	$text = str_replace("Erdbeerkonfit&uuml;re", "VV4A", $text);
	$text = str_replace("Brause", "chem. Elemente", $text);
	$text = str_replace("Vanilleeis", "Eis", $text);
	$text = str_replace("Eismatsch", "Wasser", $text);
	$text = str_replace("Traubenzucker", "Energie", $text);
	
/* Bereich nur aktivieren, wenn im Spiel Ressourcennamen mit Keks vorkommen
	$text = str_replace("weicher Keks", "Eis", $text);
	$text = str_replace("Keksmatsch", "Wasser", $text);
	$text = str_replace("Doppelkeks mit Cremef&uuml;llung", "VV4A", $text);
	$text = str_replace("Doppelkeks", "Stahl", $text);
	$text = str_replace("Cremef&uuml;llung", "chem. Elemente", $text);
	$text = str_replace("Powerkeks", "Energie", $text);
	$text = str_replace("Sandtaler", "Credits", $text);
	$text = str_replace("Keksvernichter", "Bev&ouml;lkerung", $text);
	$text = str_replace("Keks", "Eisen", $text);
*/

	// Eigenkreation Start
	// SB/KB-Links finden
	$suche = array(
		'sb' => 'www\.icewars\.de/portal/kb/de/sb\.php\?id=(?P<id>[\d]+)&amp;md_hash=(?P<hash>[\w]{32})',
		'kb' => 'www\.icewars\.de/portal/kb/de/kb\.php\?id=(?P<id>[\d]+)&amp;md_hash=(?P<hash>[\w]{32})',
		);
	
	require_once ('parser/xml_kb_sb.php');
	
	foreach($suche as $typ => $link){
		preg_match_all('#'.$link.'#', $text, $treffer, PREG_SET_ORDER);
		foreach($treffer as $value){
			$typ($value['id'], $value['hash']); //Funktion aufrufen
		}
	}
	
	if (isset($anzahl_kb) && $anzahl_kb >= 1) {
		echo '
	<div class="system_notification">',$anzahl_kb,' KB-',($anzahl_kb == 1) ? 'Link': 'Links',' gefunden (',$anzahl_kb_neu,' ',($anzahl_kb_neu == 1) ? 'neuer': 'neue',')</div><br />';
	}
	if (isset($anzahl_sb) && $anzahl_sb >= 1) {
		echo '
	<div class="system_notification">',$anzahl_kb,' SB-',($anzahl_kb == 1) ? 'Link': 'Links',' gefunden</div><br />';
	}	
	
	// Eigenkreation Ende
	
	$text = explode("\n", $text);
  
	$scan_type = '';
	$cat = '';
	$update_users = array();
	$scanlines = array();

      $ignoremenu = FALSE;
      $ignorekoloinfo = -1;

  foreach ($text as $scan) {


      //Wirtshcaftsmenu auslassen, da das sonst zu Fehlern mi9t der Koloinfo f&uuml;hrt
      if( strpos( $scan, 'Wirtschaft - Men&uuml;' ) !== FALSE ) {
        $ignoremenu = TRUE;
      }
      if( strpos( $scan, 'Artefakt&uuml;bersicht' ) !== FALSE ) {
        $ignoremenu = FALSE; 
        $ignorekoloinfo = 1;
      }
      if( $ignoremenu ) {
        $scan = '';
      }


    foreach( $parser as $key => $value ) {
      // Nach der Umstellung in der IWDB.sql ist das Halten der html-Entities 
      // fuer $value[0] nicht mehr noetig ...
        if (substr($value[0],0,1) == "/")      //! Mac: regExp fuer libIWParser auslassen
            continue;
        
        //da die Koloinfo den gleichen Recognizer wie als Titel hat muss hier Bugcatching betrieben werden
        if ( ($value[0] == 'Kolonieinfo') AND ($ignorekoloinfo == 1) AND ( strpos( $scan, $value[0] ) !== FALSE ) ) {
            $scan = 'ignored';
            $ignorekoloinfo = 0;
        }

        if( strpos( $scan, $value[0] ) !== FALSE ) {
            if( !empty( $scan_type )) {
                if($parser[$scan_type][1] == 1) {
                    echo "<div class='system_notification'>" . $parser[$scan_type][2] . 
                        " erkannt. Parse ...</div>\n";
                    include("./parser/s_" . $scan_type . ".php");
                } else {
                    echo "<div class='system_notification'>Weiteren " . $parser[$scan_type][2] . 
                        " erkannt. Parse ...</div>\n";
                }

                $func = "parse_" . $scan_type;

                if(isset($debug)) {
                    echo "<div class='system_debug_blue'>";
                    echo "Rufe Parserfunktion " . $func . " mit folgendem Parameter:<br>\n";
                    echo "<br><pre>";
                    print_r($scanlines);
                    echo "</pre><br>";
                    echo "</div>";  
                }

                $func($scanlines);
                $count++;
            }

            // den ganzen Mist vor dem Schluessel ignorieren. Uns interessiert
            // wirklich nur, was nach dem Schluessel kommt.
            unset($scanlines);
            $scanlines = array();

            $parser[$key][1]++;
            $scan_type = $key;

        }
    }
    $scanlines[] = $scan;
  }
  
  if( !empty( $scan_type )) {
    if($parser[$scan_type][1] == 1) {
      echo "<div class='system_notification'>" . $parser[$scan_type][2] . 
           " erkannt. Parse ...</div>\n";
      include("./parser/s_" . $scan_type . ".php");
    } else {
      echo "<div class='system_notification'>Weiteren " . $parser[$scan_type][2] . 
           " erkannt. Parse ...</div>\n";
    }
    $func = "parse_" . $scan_type;
    if(isset($debug)) {
        echo "<div class='system_debug_blue'>";
        echo "Rufe Parserfunktion " . $func . " mit folgendem Parameter:<br>\n";
        echo "<br><pre>";
        print_r($scanlines);
        echo "</pre><br>";
        echo "</div>";  
    }
    $func($scanlines);
    $count++;
    
    echo "<br>\n";
  }
  
    if ($count == 0) {
        $raw_input = getVar("text", true);
        foreach( $parser as $key => $value ) {
            if (substr($value[0],0,1) != "/")     //! Mac: "alte" parser ignorieren
                continue;

            $parser_regexp = $value[0];
            if (preg_match($parser_regexp,$raw_input) !== FALSE)
            {
                if($parser[$key][1] == 1) {
                    echo "<div class='system_notification'>" . $parser[$key][2] . 
                        " erkannt. Parse ...</div>\n";
                } else {
                    echo "<div class='system_notification'>Weiteren " . $parser[$key][2] . 
                        " erkannt. Parse ...</div>\n";
                }
                
                $fparser = 'Parser'.$key.'C';
                require_once ('plib/de/parsers/'.$fparser.'.php');
                require_once ('plib/DTOParserResultC.php');
                
                $parserobj = new $fparser;
                $b = $parserobj->canParseText($raw_input);

                if ($b)
                {
                    $result = new DTOParserResultC ($parserobj);
                    $parserobj->parseText ($result);
                    if (!empty($result->aErrors) && count($result->aErrors) > 0)
                    {
                        echo "... error:";
                        print_r(array_merge($return, $result->aErrors));
                    } else {
                        $lparser = $result->strIdentifier;
                        require_once ('parser/'.$lparser.'.php');
                        echo "... load $lparser";
                        call_user_func ($lparser.'_parse', $result);

                        if (function_exists($lparser.'_done'))
                        {
                            $a = call_user_func ($lparser.'_done', $result);
                        }	
                        $parser[$key][1]++;
                    }
                } else {
                    echo "... failed (canParseText = 0)";
                }
            }
        }
    }
    
    if($count > 1) {
      echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 90%;\">\n";
      echo "  <tr><td colspan=\"2\" class=\"windowbg2\" style=\"font-size: 18px;\">Zusammenfassung</td></tr>\n";
      foreach( $parser as $key => $value ) {
        if( $parser[$key][1] > 0 ) {
          echo "  <tr>\n";
          echo "    <td class=\"windowbg1\" align=\"right\" width=\"30px\">" . $parser[$key][1] . "</td>\n";
          echo "    <td class=\"windowbg1\" align=\"left\">" . (($parser[$key][1] > 1) ? (plural($parser[$key][2])) : $parser[$key][2]) . "</td>\n";
          echo "  </tr>\n";
          
          // Closure hook for module after all needed things were inserted.
          // E.g. recalculating research levels after new researches were added. 
          if(function_exists("finish_".$key)) {
            $func = "finish_" . $key;
            $func();
          }
        }
      }
      echo "</table><br />\n";  
    } elseif($count == 1) {
      // Closure hook for module after all needed things were inserted.
      // E.g. recalculating research levels after new researches were added. 
      if(function_exists("finish_".$scan_type)) {
        $func = "finish_" . $scan_type;
        $func();
      }
      
      // Display hook for displaying the result of the insertation. 
      if(function_exists("display_".$scan_type)) {
        $func = "display_" . $scan_type;
        $func();
      }
    }
 	// Eigenkreation Start
	
	if (isset($ausgabe['KBs'])) {
	
		sort($ausgabe['KBs']); // sortieren nach Zeit
	
		echo '
			<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
				<tr>
					<td colspan="2" class="windowbg2" style="font-size: 18px;">BBCode der Kampfberichte</td>
				</tr>
				<tr>
					<td class="windowbg1">';
		foreach($ausgabe['KBs'] as $key => $value) {
			if ($key != 0)
				echo '
					<br />_______________________________________________________<br /><br />';
			echo htmlentities($value['Bericht']);
		}
		echo '
				</tr>
			</table><br />';
 	} 

	
	$stop = microtime(true);
	$dauer = $stop - $start;
	echo '
			Dauer: '.round($dauer,4).' sec<br />';

	// Eigenkreation Ende
  
  return;
}
?>
