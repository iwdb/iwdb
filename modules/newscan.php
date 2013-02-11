<?php
/*****************************************************************************
 * newscan.php                                                               *
 *****************************************************************************
 * This program is free software; you can redistribute it and/or modify it   *
 * under the terms of the GNU General Public License as published by the     *
 * Free Software Foundation; either version 2 of the License, or (at your    *
 * option) any later version.                                                *
 *                                                                           *
 * This program is distributed in the hope that it will be useful, but       *
 * WITHOUT ANY WARRANTY; without even the implied warranty of                *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General *
 * Public License for more details.                                          *
 *                                                                           *
 * The GNU GPL can be found in LICENSE in this directory                     *
 *****************************************************************************
 * Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  *
 * IW-Spielern.                                                              *
 *                                                                           *
 * Autor: Mac (MacXY@herr-der-mails.de)                                      *
 * Datum: April 2012                                                         *
 *                                                                           *
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum wenden:                                                   *
 *                   https://www.handels-gilde.org                           *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

if (!defined('IRA')) {
    die('Hacking attempt...');
}

$start = microtime(true);

//$debug = TRUE;

/*
	Autoload system for plib parser
*/
global $plibfiles;

$plibfiles = array();
function __autoload($class)
{
    global $plibfiles;
    if (empty($plibfiles)) {
        function ReadTheDir($base)
        {
            global $plibfiles;
            $base = realpath($base) . DIRECTORY_SEPARATOR;
            $dir  = opendir($base);
            while ($file = readdir($dir)) {
                if (is_file($base . $file)) {
                    if (substr($file, -4) == ".php") {
                        $plibfiles[md5($file)] = $base . $file; //add php-file to hashtable
                    }
                } else if (is_dir($base . $file) && $file != "." && $file != ".." && substr($file, 0, 1) != ".") { //! keine versteckten Verzeichnisse
                    ReadTheDir($base . $file . DIRECTORY_SEPARATOR);
                }
            }
            closedir($dir);
        }

        ReadTheDir('plib' . DIRECTORY_SEPARATOR);
    }
    if (isset($plibfiles[md5($class . ".php")]) && file_exists($plibfiles[md5($class . ".php")])) {
        require_once ($plibfiles[md5($class . ".php")]);
    }
}

function plural($singular)
{
    if (preg_match('/.*sicht$/i', $singular)) {
        return ($singular . "en");
    }

    if (preg_match('/.*bericht$/i', $singular)) {
        return ($singular . "e");
    }

    if (preg_match('/.*liste$/i', $singular)) {
        return ($singular . "n");
    }

    if (preg_match('/.*scan$/i', $singular)) {
        return ($singular . "s");
    }

    return $singular;
}

$selectedusername = validAccname(getVar('seluser'));
if ($selectedusername === false) {
    $selectedusername = $user_sitterlogin;
}
if (!isset($sitterschleife)) {


    doc_title('Neuer Bericht');
    echo "<form method='POST' action='index.php?action=newscan&sid=" . $sid . "' enctype='multipart/form-data'>\n";
    echo "<table class='table_format' style='width: 90%;'>\n";
    echo " <tr>\n";
    echo "  <td class='windowbg2 center'>\n";

    global $user_status, $user_sitten;

    $sqlP = "SELECT value FROM `{$db_tb_params}` WHERE name = 'bericht_fuer_rang';";
    $resultP = $db->db_query($sqlP)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
    $rowP = $db->db_fetch_array($resultP);

    $allow1 = false;

    if ($rowP['value'] == 'hc' AND (strtolower($user_status) == 'hc')) {
        $allow1 = true;
    }
    if ($rowP['value'] == 'mv' AND (strtolower($user_status) == 'hc' OR strtolower($user_status) == 'mv')) {
        $allow1 = true;
    }
    if ($rowP['value'] == 'all' AND (strtolower($user_status) != 'guest')) {
        $allow1 = true;
    }

    $sqlP = "SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'bericht_fuer_sitter';";
    $resultP = $db->db_query($sqlP)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
    $rowP = $db->db_fetch_array($resultP);

    $allow2 = false;

    if ($rowP['value'] == 0 AND ($user_sitten == 0 OR $user_sitten == 1)) {
        $allow2 = true;
    }
    if ($rowP['value'] == 1 AND ($user_sitten == 1)) {
        $allow2 = true;
    }
    if ($rowP['value'] == 3 AND ($user_sitten == 0 OR $user_sitten == 1)) {
        $allow2 = true;
    }
    if ($rowP['value'] == 2) {
        $allow2 = true;
    }

    if ($user_status == "admin") {
        $allow1 = true;
        $allow2 = true;
    }

    if ($allow1 AND $allow2) {
        echo "   Bericht einfügen für\n";
        echo "	 <select name='seluser' style='width: 200px;'>\n";

        $sql = "SELECT sitterlogin FROM " . $db_tb_user . " ORDER BY id ASC";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        while ($row = $row = $db->db_fetch_array($result)) {
            echo "      <option value='" . $row['sitterlogin'] . "'" . ($selectedusername == $row['sitterlogin'] ? " selected" : "") . ">" . $row['sitterlogin'] . "</option>";
        }
        echo " 	 </select><br />\n";
    }

    echo "   <textarea name='text' rows='14' cols='70'></textarea><br />\n";
    echo " 	 <br />\n";
    echo "<div style='color:yellow;'>Wichtig : Beim Einlesen der Ressübersicht darauf achten, dass das Lager ausgeklappt ist!</div>";
    echo "<div style='color:yellow;'>Wichtig : Beim Einlesen der Startseite darauf achten, dass die Fluginformationen ausgeklappt sind!</div>";
    echo " 	 <br />\n";
    echo "   Für Hilfe bitte oben auf den \"Hilfe\" Button drücken.\n";
    echo "  </td>\n";
    echo " </tr>\n";
    echo " <tr>\n";
    echo "  <td class='titlebg center'>\n";
    echo "   <input type='submit' value='abspeichern' name='B1' class='submit'>\n";
    echo "  </td>\n";
    echo " </tr>\n";
    echo "</table>\n";
    echo "</form>\n";
    echo "<br>";
}

$textinput = getVar('text', true); // ungefilterten Bericht holen
if (!empty($textinput)) {
    $count = 0;

    require_once ('plib/ParserFactoryConfigC.php');
    $availParsers = new ParserFactoryConfigC();
    $aParserIds   = $availParsers->getParserIdsFor($textinput);

    if (count($aParserIds)) {
        foreach ($aParserIds as $selectedParserId) {
            $parserObj = $availParsers->getParser($textinput, $selectedParserId);
            if ($parserObj instanceof ParserI) {
                $key = $parserObj->getIdentifier();
                if (!isset($parser[$key])) {
                    $parser[$key] = array("/deprecated/", 1, $parserObj->getName());
                }
                if ($parser[$key][1] == 1) {
                    echo "<div class='system_notification'>" . $parser[$key][2] . " erkannt. Parse ...</div>\n";
                } else {
                    echo "<div class='system_notification'>Weiteren " . $parser[$key][2] . " erkannt. Parse ...</div>\n";
                }

                $parserResult = new DTOParserResultC ($parserObj);
                $parserObj->parseText($parserResult);

                if ($parserResult->bSuccessfullyParsed) {
                    if (!empty($parserResult->aErrors) && count($parserResult->aErrors) > 0) {
                        echo "info:<br />";
                        foreach ($parserResult->aErrors as $t) {
                            echo "...$t <br />";
                        }
                    } else {
                        $lparser = $parserResult->strIdentifier;
                        if (file_exists('parser/' . $lparser . '.php')) {
                            require_once ('parser/' . $lparser . '.php');

                            if (function_exists('parse_' . $lparser)) {

                                if (isset($debug)) {
                                    echo "<div class='system_debug_blue'>";
                                    echo "Rufe Parserfunktion parse_" . $lparser . " mit folgendem Parameter:<br />\n";
                                    echo "<br /><pre>";
                                    print_r($parserResult);
                                    echo "</pre><br />";
                                    echo "</div>";
                                }

                                call_user_func('parse_' . $lparser, $parserResult);

                                if (function_exists('done_' . $lparser)) {
                                    call_user_func('done_' . $lparser, $parserResult);
                                }
                                $parser[$key][1]++;
                                $count++;

                            } else {
                                doc_message("Input erfolgreich erkannt (" . $parserObj->getName() . "). Passende Verarbeitung ist aber bisher nicht vorhanden.");
                                if (isset($debug)) {
                                    echo "<div class='system_debug_blue'>parse_{$lparser}() in parser/{$lparser}.php nicht gefunden!</div>";
                                }
                            }

                        } else {
                            doc_message("Input erfolgreich erkannt (" . $parserObj->getName() . "). Passende Verarbeitung ist aber bisher nicht vorhanden.");
                            if (isset($debug)) {
                                echo "<div class='system_debug_blue'>parser/{$lparser}.php nicht gefunden!</div>";
                            }
                        }
                    }
                } else {
                    doc_message("Input (" . $parserObj->getName() . ") wurde erkannt, konnte aber nicht fehlerfrei geparsed werden!");
                    if (!empty($parserResult->aErrors) && count($parserResult->aErrors) > 0) {
                        echo "error:<br />";
                        foreach ($parserResult->aErrors as $t) {
                            echo "...$t <br />";
                        }
                    }
                }
            }
        }
    }

    //! Anzeige für den Spieler ...
    if ($count > 0) {
        if ($count > 1) {
            echo "<table class='table_format' style='width: 90%;'>\n";
            echo "  <tr><td colspan='2' class='windowbg2' style='font-size: 18px;'>Zusammenfassung</td></tr>\n";
        }
        foreach ($parser as $key => $value) {
            if ($parser[$key][1] > 0) {
                if ($count > 1) {
                    echo "  <tr>\n";
                    echo "    <td class='windowbg1' align='right' width='30px'>" . $parser[$key][1] . "</td>\n";
                    echo "    <td class='windowbg1' align='left'>" . (($parser[$key][1] > 1) ? (plural($parser[$key][2])) : $parser[$key][2]) . "</td>\n";
                    echo "  </tr>\n";
                }
                // Closure hook for module after all needed things were inserted.
                // E.g. recalculating research levels after new researches were added. 
                if (function_exists("finish_" . $key)) {
                    $func = "finish_" . $key;
                    $func();
                }

                // Display hook for displaying the result of the insertation. 
                if (function_exists("display_" . $key)) {
                    $func = "display_" . $key;
                    $func();
                }
            }
        }
        if ($count > 1) {
            echo "</table><br />\n";
        }
    }

    // Eigenkreation Start
    //! Mac: erstmal rausgenommen, da es $ausgabe im Moment eh nicht gibt
//	if (isset($ausgabe['KBs'])) {
//	
//		sort($ausgabe['KBs']); // sortieren nach Zeit
//	
//		echo '
//			<table class="table_format" style="width: 90%;">
//				<tr>
//					<td colspan="2" class="windowbg2" style="font-size: 18px;">BBCode der Kampfberichte</td>
//				</tr>
//				<tr>
//					<td class="windowbg1">';
//		foreach($ausgabe['KBs'] as $key => $value) {
//			if ($key != 0)
//				echo '
//					<br />_______________________________________________________<br /><br />';
//			echo htmlspecialchars($value['Bericht'], 'UTF-8');
//		}
//		echo '
//				</tr>
//			</table><br />';
// 	} 

    $stop  = microtime(true);
    $dauer = $stop - $start;
    echo '<br>Dauer: ' . round($dauer, 4) . ' sec<br>';

    // Eigenkreation Ende

    return;
}
