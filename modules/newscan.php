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

/*****************************************************************************/
/* Diese Erweiterung der urspuenglichen DB ist ein Gemeinschafftsprojekt von */
/* IW-Spielern.                                                              */
/*                                                                           */
/* Autor: Mac (MacXY@herr-der-mails.de)                                      */
/* Datum: April 2012                                                         */
/*                                                                           */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                   http://www.iw-smf.pericolini.de                         */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
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
global $plibfiles;

$plibfiles = array();
function __autoload($class)
{
	global $plibfiles;
	if (empty($plibfiles))
	{
		function ReadTheDir ($base)
		{
			global $plibfiles;
			$base = realpath($base) . DIRECTORY_SEPARATOR;
			$dir = opendir($base);
			while($file = readdir($dir)){
				if	(is_file($base.$file) && substr($file,-4) == ".php")
				{
					$plibfiles[md5($file)] = $base.$file;
				}
				else if (is_dir ($base.$file.DIRECTORY_SEPARATOR) && $file != "." && $file != ".." && substr($file,0,1) != ".") //! keine versteckten Verzeichnisse
				{
					ReadTheDir($base.$file.DIRECTORY_SEPARATOR);
				}
			}
			closedir($dir);
		}
		ReadTheDir ('plib'.DIRECTORY_SEPARATOR);
	}
    if (isset($plibfiles[md5($class.".php")]) && file_exists($plibfiles[md5($class.".php")]))
	{
		require_once ($plibfiles[md5($class.".php")]);
	}
}

// parser werden nun dynamisch durch die ParserFactory ermittelt
$parser = array();

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

$textinput = getVar('text',true);        //! ungefilterten Bericht holen
if ( ! empty($textinput) )
{
	$count = 0;	
    //! Mac @todo: SB/KB Links verarbeiten
    //! Mac @todo: UniXML verarbeiten
    
    require_once ('plib/ParserFactoryConfigC.php');
    $availParsers = new ParserFactoryConfigC();
    $parserObj = $availParsers->getParser($textinput);

    if( $parserObj instanceof ParserI )
    {
        $key = $parserObj->getIdentifier();
        if (!isset($parser[$key])) {
            $parser[$key] = array("/deprecated/", 0, $parserObj->getName());
        }
        if($parser[$key][1] == 1) {
            echo "<div class='system_notification'>" . $parser[$key][2] . 
                " erkannt. Parse ...</div>\n";
        } else {
            echo "<div class='system_notification'>Weiteren " . $parser[$key][2] . 
                " erkannt. Parse ...</div>\n";
        }

        $parserResult = new DTOParserResultC ($parserObj);
        $parserObj->parseText ($parserResult);

        if ($parserResult->bSuccessfullyParsed) {
            if (!empty($parserResult->aErrors) && count($parserResult->aErrors) > 0)
            {
                echo "... error:";
                print_r($parserResult->aErrors);
                echo "<br />";
            } else {
                $lparser = $parserResult->strIdentifier;
                if (file_exists('parser/'.$lparser.'.php')) {
                    require_once ('parser/'.$lparser.'.php');

                    if(isset($debug)) {
                        echo "<div class='system_debug_blue'>";
                        echo "Rufe Parserfunktion parse_" . $lparser . " mit folgendem Parameter:<br>\n";
                        echo "<br><pre>";
                        print_r($parserResult);
                        echo "</pre><br>";
                        echo "</div>";  
                    }

                    call_user_func ('parse_'.$lparser, $parserResult);

                    if (function_exists('done_'.$lparser))
                    {
                        call_user_func ('done_'.$lparser, $parserResult);
                    }	
                    $parser[$key][1]++;
                    $count++;
                }
                else {
                    echo "Input konnte erfolgreich geparsed, die Daten allerdings nicht in die Datenbank eingetragen werden!<br />";
                }
            }
        }
        else {
            echo "... der Input konnte nicht fehlerfrei geparsed werden!<br />";
            if (!empty($parserResult->aErrors) && count($parserResult->aErrors) > 0)
            {
                echo "... error:";
                print_r($parserResult->aErrors);
                echo "<br />";
            }
        }
    }
    else {
            echo "unrecognised Input (no suitable Parser found)<br />";
    }
    
    //! Anzeige fuer den Spieler ...
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
      foreach( $parser as $key => $value ) {
        if(function_exists("finish_".$key)) {
            $func = "finish_" . $key;
            $func();
        }
      
        // Display hook for displaying the result of the insertation. 
        if(function_exists("display_".$key)) {
            $func = "display_" . $key;
            $func();
        }
      }
    }
    
 	// Eigenkreation Start
    //! Mac: erstmal rausgenommen, da es $ausgabe im Moment eh nicht gibt
//	if (isset($ausgabe['KBs'])) {
//	
//		sort($ausgabe['KBs']); // sortieren nach Zeit
//	
//		echo '
//			<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
//				<tr>
//					<td colspan="2" class="windowbg2" style="font-size: 18px;">BBCode der Kampfberichte</td>
//				</tr>
//				<tr>
//					<td class="windowbg1">';
//		foreach($ausgabe['KBs'] as $key => $value) {
//			if ($key != 0)
//				echo '
//					<br />_______________________________________________________<br /><br />';
//			echo htmlentities($value['Bericht']);
//		}
//		echo '
//				</tr>
//			</table><br />';
// 	} 
	
	$stop = microtime(true);
	$dauer = $stop - $start;
	echo '
			Dauer: '.round($dauer,4).' sec<br />';

	// Eigenkreation Ende
  
  return;
}
?>
