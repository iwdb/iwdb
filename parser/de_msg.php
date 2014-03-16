<?php
/*****************************************************************************
 * de_msg.php                                                                *
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
 * Datum: Juni 2012                                                          *
 *                                                                           *
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum wenden:                                                   *
 *                   https://www.handels-gilde.org                           *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

function parse_de_msg($aParserData)
{
    global $selectedusername;
    global $db, $db_tb_transferliste, $db_tb_user, $db_tb_fremdsondierung;

    $transp_skipped     = 0;
    $transp_failed      = 0;
    $transp_succ        = 0;
    $gave_orbit_ignored = 0;

    //! Rückkehr enthält keine relevanten Informationen
//	foreach ($aParserData->objResultData->aReverseMsgs as $msg)
//	{
//		if (empty($msg->strCoords)) {
//			echo "error: " . $msg->eParserType . " " . $msg->strMsgTitle . "<br />";
//			continue;
//		}
//	}

    //! @TODO:
    /*
    eigenes Massdriverpaket angekommen / abgestürzt (16:26:6) 	Systemnachricht 	18.01.2011 16:13:32
Massdriverpaket
Ein Massdriverpaket ist auf dem Planeten Kampfbasis 16:26:6 angekommen. Der Absender ist Mac. Der Empfänger ist Biberlein.
Es konnte nicht gefangen werden und ist auf den Planeten gestürzt.
    eigenes Massdriverpaket wurde nicht gefangen und ist weitergeflogen (16:28:5) 	Systemnachricht 	18.01.2011 15:19:00
Massdriverpaket
Ein Massdriverpaket ist auf dem Planeten Tellan 16:28:5 angekommen. Der Absender ist Mac. Der Empfänger ist Mac.
Es wurde nicht gefangen und ist weitergeflogen
     fremdes Massdriverpaket angekommen / abgestürzt (15:141:14) 	Systemnachricht 	16.02.2011 10:45:54
Transport
Ein Massdriverpaket ist auf dem Planeten Hadante 15:141:14 angekommen. Der Absender ist CashCow. Der Empfänger ist Mac.
Es konnte nicht gefangen werden und ist auf den Planeten gestürzt.
Folgende Ressourcen konnten geborgen werden
Ressourcen
VV4A 	87592
Es wurden folgende Gebäude zerstört
1 Orbitales Habitat
Achja bei dem ganzen Chaos kamen 142 Leute ums Leben.
    */
    foreach (array_merge($aParserData->objResultData->aTransportMsgs, $aParserData->objResultData->aGaveMsgs, $aParserData->objResultData->aMassdriverMsgs) as $msg) {
        if (!$msg->bSuccessfullyParsed) {
            echo "..... failed Transport/Übergabe/Massdriver Msg!<br />";
            if (!empty($msg->aErrors)) {
                echo implode("<br />", $msg->aErrors) . "<br />";
            }
            ++$transp_failed;
            continue;
        }
        $fromOrbit = ($msg->eParserType == 'Übergabe' && $msg->bOutOfOrbit) ? true : false;
        $buddler   = $msg->strFromUserName;
        $fleeter   = $msg->strToUserName;
//		$transfair_to_coords = $msg->strCoords;		
        $transfair_date = $msg->iMsgDateTime;

        if ($fromOrbit) {
            ++$gave_orbit_ignored;
            continue;
        }

        if (empty($transfair_date) || empty($buddler) || empty($fleeter)) {
            doc_message("Fehler im Bericht - (" . $transfair_date . ", " . $buddler . ", " . $fleeter . ")");
            ++$transp_failed;
            continue;
        }

        //! Mac: Workaround, da libIwParser nicht die gleichen Ressnamen verwendet wie IWDB1. Eigentlich besser mit IDs handhaben
        foreach ($msg->aResources as $k => $resource) {
            $name = strtolower($resource['resource_name']); //! reicht fuer Eisen, Stahl, VV4A, Eis, Wasser, Energie

            if (strpos($name, "chem") !== false) {
                $name = "chem";
            } else if (strpos($name, "bev") !== false) {
                $name = "volk";
            }
            $resource['resource_name'] = $name;

            $msg->aResources[$k] = $resource;
        }

        // Lieferungen an sich selbst ignorieren
        // Manuell: DELETE FROM `prefix_transferliste` WHERE `buddler`=`fleeter`
        if (!empty($transfair_date) && $buddler == $fleeter) {
            //doc_message("Bericht ".$transfair_date." vom ".strftime(CONFIG_DATETIMEFORMAT, $transfair_date)." ignoriert! - Absender und Empfänger sind identisch...");
            ++$transp_skipped;
            continue;
        }

        if (count($msg->aResources) === 0) {
            //keine Ress transportiert, zum Beispiel Artefakte
            ++$transp_skipped;
            continue;
        }

        $sql = "SELECT COUNT(*) AS anzahl FROM " . $db_tb_transferliste .
            " WHERE zeitmarke=" . $transfair_date . " AND buddler='" . $buddler .
            "' AND fleeter='" . $fleeter . "'";
        $result = $db->db_query($sql);

        $row = $db->db_fetch_array($result);
        // Not found, so insert new
        if (empty($row) || $row['anzahl'] == 0) {
            $sql = "INSERT INTO " . $db_tb_transferliste . "(zeitmarke, buddler, fleeter, ";
            $val = "$transfair_date, '$buddler','$fleeter',";
            foreach ($msg->aResources as $resource) {
                $sql .= $resource['resource_name'] . ", ";
                $val .= "'" . $resource['resource_count'] . "', ";
            }

            $sql = substr($sql, 0, (strlen($sql) - 2));
            $val = substr($val, 0, (strlen($val) - 2));
            $sql .= ") VALUES(" . $val . ")";
        } else {
            $sql = "UPDATE " . $db_tb_transferliste . " SET zeitmarke=$transfair_date, buddler='$buddler', fleeter='$fleeter', ";
            foreach ($msg->aResources as $resource) {
                $sql .= $resource['resource_name'] . "='" . $resource['resource_count'] . "', ";
            }
            $sql = substr($sql, 0, (strlen($sql) - 2)) .
                " WHERE zeitmarke=" . $transfair_date .
                " AND buddler='" . $buddler . "' AND fleeter='" . $fleeter . "'";
        }
        $db->db_query($sql);

        // Aktualisierungszeit für Transportberichte setzen
        $sql = "UPDATE " . $db_tb_user . " SET lasttransport='" . CURRENT_UNIX_TIME . "' WHERE sitterlogin='" . $buddler . "'";
        $db->db_query($sql);

        ++$transp_succ;

        //! @todo: Mac: bisher in der IWDB1 noch gar nicht berücksichtigt ?
//		foreach ($msg->aSchiffe as $schiffe)
//		{
//			$schiffe_count = $schiffe['schiffe_count'];
//			$schiffe_name = $schiffe['schiffe_name'];
//		}
    }

    if (!empty($aParserData->objResultData->aScanFailMsgs)) {
        echo "<div class='system_notification'>Fehlgeschlagene Sondierung erkannt.</div><br>";
        finish_fehlscan($aParserData->objResultData->aScanFailMsgs);
    }

    foreach ($aParserData->objResultData->aScanGeoMsgs as $msg) {
        //! @todo:
        if (!$msg->bSuccessfullyParsed) {
            echo ".....  fehlgeschlagener Geoscan<br />";
            echo implode("<br>", $msg->aErrors);
            continue;
        }
        /** $scanLogs[] = "..... GeoScan allready parsed with paParser"; **/
        continue;

        //dummy
    }

    $transfair_failed  = 0;
    $transfair_skipped = 0;
    foreach ($aParserData->objResultData->aTransfairMsgs as $msg) {
        if (!$msg->bSuccessfullyParsed) {
            echo "..... fehlgeschlagene Tranportnachricht!<br />";
            if (!empty($msg->aErrors)) {
                echo implode("<br />", $msg->aErrors) . "<br />";
            }
            ++$transfair_failed;
            continue;
        }
//		echo "..... Transfair wegen unzureichender Informationen ignoriert<br />";
        ++$transfair_skipped;
        continue;

        $buddler             = $msg->strFromUserName;
        $fleeter             = $msg->strToUserName; //! immer leer ?
        $transfair_to_coords = $msg->strCoords;
        $transfair_date      = $msg->iMsgDateTime;

        if (empty($transfair_date) || empty($buddler) || empty($fleeter)) {
            doc_message("Fehler im Bericht - (" . $transfair_date . ", " . $buddler . ", " . $fleeter . ")");
            ++$transfair_failed;
            continue;
        }

        //! Mac: Workaround, da libIwParser nicht die gleichen Ressnamen verwendet wie IWDB1. Eigentlich besser mit IDs handhaben
        foreach ($msg->aCarriedResources as $k => $resource) {
            $name = strtolower($resource['resource_name']); //! reicht fuer Eisen, Stahl, VV4A, Eis, Wasser, Energie

            if (strpos($name, "chem") !== false) {
                $name = "chem";
            } else if (strpos($name, "bev") !== false) {
                $name = "volk";
            } else if ($name == "Eisen") {
                $resource['resource_name'] = "stahl";
            }
            $resource['resource_name'] = $name;

            $msg->aCarriedResources[$k] = $resource;
        }
        foreach ($msg->aFetchedResources as $k => $resource) {
            $name = strtolower($resource['resource_name']); //! reicht fuer Eisen, Stahl, VV4A, Eis, Wasser, Energie

            if (strpos($name, "chem") !== false) {
                $name = "chem";
            } else if (strpos($name, "bev") !== false) {
                $name = "volk";
            } else if ($name == "Eisen") {
                $resource['resource_name'] = "stahl";
            }
            $resource['resource_name'] = $name;

            $msg->aFetchedResources[$k] = $resource;
        }

        // Lieferungen an sich selbst ignorieren
        // Manuell: DELETE FROM `prefix_transferliste` WHERE `buddler`=`fleeter`
        if (!empty($transfair_date) && $buddler == $fleeter) {
            //doc_message("Bericht ".$transfair_date." vom ".strftime(CONFIG_DATETIMEFORMAT, $transfair_date)." ignoriert! - Absender und Empfänger sind identisch...");
            ++$transfair_skipped;
            continue;
        }


        //dummy
    }

    if ($transp_succ > 0) {
        echo $transp_succ . " Transport/Übergabe/Massdriver Msg <font color='green'> erfolgreich geparsed</font><br />";
    }
    if ($transp_skipped > 0) {
        echo $transp_skipped . " Transport/Übergabe/Massdriver Msg <font color='black'> ignoriert</font><br />";
    }
    if ($transp_failed > 0) {
        echo $transp_failed . " Transport/Übergabe/Massdriver Msg <font color='red'> fehlgeschlagen</font><br />";
    }
    if ($gave_orbit_ignored > 0) {
        echo $gave_orbit_ignored . " Übergaben aus dem Orbit <font color='black'> ignoriert</font><br />";
    }
    if ($transfair_skipped > 0) {
        echo $transfair_skipped . " Transfair Msg <font color='black'> ignoriert</font><br />";
    }

//! ..... Stationieren not yet implemented

    foreach ($aParserData->objResultData->aSondierungMsgs as $msg) {
        $parsertyp = ($msg->eParserType == "Sondierung (Schiffe/Def/Ress)") ? "schiffe" : "gebaeude";

        //! Hier die Namen für die Koordinaten aus der Datenbank holen
        $name_to = getNameByCoords($msg->strCoordsTo);
        if (empty($name_to)) {
            $name_to = $selectedusername;
        }
        $alliance_to = getAllianceByUser($name_to);

        if (empty($msg->strNameFrom)) {
            $msg->strNameFrom = '';
        } else {
            if (empty($msg->strAllianceFrom)) {
                // Allianz konnte nicht aus dem Text bestimmt werden
                $msg->strAllianceFrom = getAllianceByUser($msg->strNameFrom);
            }
        }

        doc_message("Fremdsondierung erkannt");

        $sql = "INSERT INTO " . $db_tb_fremdsondierung
            . "(koords_to, name_to, allianz_to, koords_from, name_from, allianz_from, sondierung_art, timestamp, erfolgreich ";
        $sql .= ") VALUES( '$msg->strCoordsTo', '$name_to', '$alliance_to', '$msg->strCoordsFrom', '$msg->strNameFrom', '$msg->strAllianceFrom', '$parsertyp', $msg->iMsgDateTime, '$msg->bSuccess' ) "
            . "ON DUPLICATE KEY UPDATE
                    name_to='$name_to', allianz_to='$alliance_to', koords_from='$msg->strCoordsFrom', name_from='$msg->strNameFrom', allianz_from='$msg->strAllianceFrom', sondierung_art='$parsertyp', erfolgreich='$msg->bSuccess'";
        $db->db_query($sql);
    }

    foreach ($aParserData->objResultData->aMsgs as $msg) {
        if (!$msg->bSuccessfullyParsed) {
            echo "..... fehlgeschlagene UserMsg!<br />";
            echo implode("<br />", $msg->aErrors);
            continue;
        }
// 		$scanLogs[] = "..... UserMsg not yet implemented";
        if ($msg->eParserType == "Angriff") /** $scanLogs[] = "..... Angriff <font color='red'> wurde per Link geparsed</font>"; **/ {
            continue;
        } else if ($msg->eParserType == "Basisaufbau") {
            continue;
        } else {
            echo "..... " . $msg->eParserType . "<font color='red'> noch nicht implementiert</font><br />";
        }
        //dummy
    }

}

//function display_de_msg() {
//  include "./modules/m_transferliste.php";
//}

function finish_fehlscan($fehlscans)
{
    global $db, $db_tb_scans, $db_tb_lieferung;

    echo '<form id="fehlscan_form" method="POST" action="index.php?action=m_raid" enctype="multipart/form-data"><p>' . "\n";
    echo '<input type="hidden" name="fehlscan" id="fehlscan_count" value="' . count($fehlscans) . '">';
    echo '<tr class="windowbg1"\>';
    echo '<td colspan=2>';

    start_table();
    start_row("titlebg", "colspan='8'");
    echo "<b>Fehlgeschlagene Sondierungen</b>";
    next_row("windowbg2", "");
    echo "Planet";
    next_cell("windowbg2", "");
    echo "Spieler";
    next_cell("windowbg2", "");
    echo "Allianz";
    next_cell("windowbg2", "");
    echo "Uhrzeit";
    next_cell("windowbg2", "");
    echo "Scantyp";
    next_cell("windowbg2", "");
    echo "X11";
    next_cell("windowbg2", "");
    echo "Terminus";
    next_cell("windowbg2", "");
    echo "X13";
    $index = 1;
    foreach ($fehlscans as $fehlscan) {
        // Abfragen der letzten Sondierung
        $x11      = "";
        $terminus = "";
        $x13      = "";
        $sql      = "SELECT * FROM " . $db_tb_lieferung . " WHERE coords_to_gal=" . $fehlscan->aCoords["gal"] . " AND coords_to_sys=" . $fehlscan->aCoords["sys"] . " AND coords_to_planet=" . $fehlscan->aCoords["planet"] . " AND art LIKE '%Sondierung%' ORDER BY time DESC";
        $result = $db->db_query($sql);
        if ($row = $db->db_fetch_array($result)) {
            if (preg_match('/(\d+)\s+Sonde\s+X11/', $row['schiffe'], $match) > 0) {
                $x11 = $match[1];
            } elseif (preg_match('/(\d+)\s+Terminus\s+Sonde/', $row['schiffe'], $match) > 0) {
                $terminus = $match[1];
            } elseif (preg_match('/(\d+)\s+Sonde\s+X13/', $row['schiffe'], $match) > 0) {
                $x13 = $match[1];
            }
        }
        echo '<input type="hidden" name="time_' . $index . '"  id="fehlscan_time_' . $index . '" value="' . $fehlscan->iMsgDateTime . '">';
        echo '<input type="hidden" name="coords_gal_' . $index . '" id="fehlscan_coords_gal_' . $index . '" value="' . $fehlscan->aCoords["gal"] . '">';
        echo '<input type="hidden" name="coords_sys_' . $index . '" id="fehlscan_coords_sys_' . $index . '" value="' . $fehlscan->aCoords["sys"] . '">';
        echo '<input type="hidden" name="coords_planet_' . $index . '" id="fehlscan_coords_planet_' . $index . '" value="' . $fehlscan->aCoords["planet"] . '">';
        next_row("windowbg1", "");
        echo $fehlscan->strCoords;
        $sql = "SELECT * FROM " . $db_tb_scans . " WHERE coords_gal=" . $fehlscan->aCoords["gal"] . " AND coords_sys=" . $fehlscan->aCoords["sys"] . " AND coords_planet=" . $fehlscan->aCoords["planet"];
        $result = $db->db_query($sql);
        if ($row = $db->db_fetch_array($result)) {
            $spieler = $row['user'];
            $allianz = $row['allianz'];
        } else {
            $spieler = "&nbsp;";
            $allianz = "&nbsp;";
        }
        next_cell("windowbg1", "");
        echo $spieler;
        next_cell("windowbg1", "");
        echo $allianz;
        next_cell("windowbg1", "");
        echo strftime("%d.%m.%Y %H:%M", $fehlscan->iMsgDateTime);
        next_cell("windowbg1", "");
        echo $fehlscan->eParserType;
        next_cell("windowbg1", "");
        echo '<input type="text" name="x11_' . $index . '" id="fehlscan_x11_' . $index . '" value="' . $x11 . '" style="width: 50">';
        next_cell("windowbg1", "");
        echo '<input type="text" name="terminus_' . $index . '" id="fehlscan_terminus_' . $index . '" value="' . $terminus . '" style="width: 50">';
        next_cell("windowbg1", "");
        echo '<input type="text" name="x13_' . $index++ . '" id="fehlscan_x13_' . $index . '" value="' . $x13 . '" style="width: 50">';
    }
    next_row("windowbg3", "colspan='5'");
    echo "Für alle dieselbe Sondenzahl übernehmen:";
    next_cell("windowbg3", "");
    echo "<input type='text' name='x11_all' style='width: 5em'>";
    next_cell("windowbg3", "");
    echo "<input type='text' name='terminus_all' style='width: 5em'>";
    next_cell("windowbg3", "");
    echo "<input type='text' name='x13_all' style='width: 5em'>";
    next_row("titlebg center", "colspan='8'");
    echo "<input type='submit' value='abspeichern' name='B1' class='submit'>";
    end_table();
    echo '</form>';
}