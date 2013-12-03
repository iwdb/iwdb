<?php
/*****************************************************************************
 * de_xml.php                                                                *
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

global $anzahl_kb, $anzahl_kb_neu, $anzahl_sb;

$anzahl_kb     = 0;
$anzahl_kb_neu = 0;
$anzahl_sb     = 0;
$anzahl_unixml = 0;

function parse_de_xml($return)
{
    global $anzahl_kb, $anzahl_kb_neu, $anzahl_sb, $anzahl_unixml;

    foreach ($return->objResultData->aKbLinks as $xmlinfo) {
        if (parse_kbxml($xmlinfo)) {
            ++$anzahl_kb;
        }
    }

    foreach ($return->objResultData->aSbLinks as $xmlinfo) {
        if (parse_sbxml($xmlinfo)) {
            ++$anzahl_sb;
        }
    }

    foreach ($return->objResultData->aUniversumLinks as $xmlinfo) {
        if (parse_unixml($xmlinfo)) {
            ++$anzahl_unixml;
        }
    }

    if (isset($anzahl_kb) && $anzahl_kb > 0) {
        doc_message($anzahl_kb . ' KB-' . (($anzahl_kb == 1) ? 'Link' : 'Links') . ' geparsed (' . $anzahl_kb_neu . ' ' . (($anzahl_kb_neu == 1) ? 'neuer' : 'neue').')');
    }
    if (isset($anzahl_sb) && $anzahl_sb > 0) {
        doc_message($anzahl_sb . ' SB-'. (($anzahl_sb == 1) ? 'Link' : 'Links') . ' geparsed');
    }
    if (isset($anzahl_unixml) && $anzahl_unixml > 0) {
        doc_message($anzahl_unixml . ' Unixml-' . (($anzahl_unixml == 1) ? 'Link' : 'Links') . ' geparsed');
    }
}

/*****************************************************************************/
/* XML-Scan-Parser                                                           */
/* von [RoC]Thella                                                           */
/*****************************************************************************/
function parse_sbxml($xmldata)
{
	
	global $db, $db_tb_scans, $db_tb_scans_geb;
	
    $xml = simplexml_load_file_ex($xmldata->strUrl);
    if (empty($xml)) {
        echo "<div class='system_error'>XML-Fehler: {$xmldata->strUrl} konnte nicht geladen werden</div>\n";

        return false;
    }

    $scan_data['coords_gal'] = (int)$xml->plani_data->koordinaten->gal;
    $scan_data['coords_sys'] = (int)$xml->plani_data->koordinaten->sol;
    $scan_data['coords_planet'] = (int)$xml->plani_data->koordinaten->pla;
    $scan_data['coords'] = $scan_data['coords_gal'] . ":" . $scan_data['coords_sys'] . ":" . $scan_data['coords_planet'];
    $scan_data['user'] = (string)$xml->plani_data->user->name;
    $scan_data['allianz'] = (string)$xml->plani_data->user->allianz_tag;
    $scan_data['typ'] = (string)$xml->plani_data->planeten_typ->name;
    $scan_data['objekt'] = (string)$xml->plani_data->objekt_typ->name;
    $scan_data['time'] = (int)$xml->timestamp;
    $scan_data['vollstaendig'] = (int)$xml->informationen->vollstaendig;
    //Allianz ggf. aktualisieren
    $scan_data['allianz'] = updateUserAlliance($scan_data['user'], $scan_data['allianz'], $scan_data['time']);

    $scan_typ = (string)$xml->scann_typ->id;

    // Geo
    if ($scan_typ == 1) {
        debug_var("scan_data['geoscantime']", $scan_data['time']);
        $scan_data['geoscantime'] = (int)$xml->timestamp;
        $scan_data['geolink'] = $xmldata->strUrl;
		$ressourcen               = $xml->plani_data->ressourcen_vorkommen->ressource;
        foreach ($ressourcen as $ressource) {
            $wert = $wert = (int)((float)$ressource->wert[0] * 100);
            switch ((int)$ressource->id) {
                case 1:
                    $scan_data['eisengehalt'] = $wert;
                    break;
                case 4:
                    $scan_data['eisdichte'] = $wert;
                    break;
                case 5:
                    $scan_data['chemievorkommen'] = $wert;
            }
        }
        $ressourcen_tech_team = $xml->plani_data->ressourcen_vorkommen->ressource_tech_team;
        foreach ($ressourcen_tech_team as $ressource_tech_team) {
            $wert = (int)((float)$ressource_tech_team->wert[0] * 100);
            switch ((int)$ressource_tech_team->id) {
                case 1:
                    $scan_data['tteisen'] = $wert;
                    break;
                case 4:
                    $scan_data['tteis'] = $wert;
                    break;
                case 5:
                    $scan_data['ttchemie'] = $wert;
                    break;
            }
        }
        $scan_data['gravitation'] = (float)$xml->plani_data->gravitation;
        $scan_data['lebensbedingungen'] = (int)((float)$xml->plani_data->lebensbedingungen * 100);
        $scan_data['bevoelkerungsanzahl'] = (float)$xml->plani_data->bev_max;
        $scan_data['fmod'] = (int)((float)$xml->plani_data->modifikatoren->forschung * 100);
        if (isset($xml->plani_data->modifikatoren->gebaeude_bau)) {
            $scan_data['kgmod'] = (float)$xml->plani_data->modifikatoren->gebaeude_bau->kosten;
            $scan_data['dgmod'] = (float)$xml->plani_data->modifikatoren->gebaeude_bau->dauer;
        } else {
            $scan_data['kgmod'] = null;
            $scan_data['dgmod'] = null;
        }

        if (isset($xml->plani_data->modifikatoren->schiff_bau)) {
            $scan_data['ksmod'] = (float)$xml->plani_data->modifikatoren->schiff_bau->kosten;
            $scan_data['dsmod'] = (string)$xml->plani_data->modifikatoren->schiff_bau->dauer;
        } else {
            $scan_data['ksmod'] = null;
            $scan_data['dsmod'] = null;
        }
        $scan_data['besonderheiten'] = "";
        foreach ($xml->plani_data->besonderheiten->besonderheit as $besonderheit) {
            if (!empty($scan_data['besonderheiten'])) {
                $scan_data['besonderheiten'] .= ", " . (string)$besonderheit->name;
            } else {
                $scan_data['besonderheiten'] = (string)$besonderheit->name;
            }
            if (stripos($besonderheit->name, "Nebel")) {
                $scan_data['nebula'] = (string)$besonderheit->name;
            }
        }
        debug_var("scan_data['besonderheiten']", $scan_data['besonderheiten']);

        $scan_data['reset_timestamp'] = (int)$xml->plani_data->reset_timestamp;
        // Gebäude/Ress
    } else if ($scan_typ == 2) {
        $scan_data['gebscantime'] = (int)$xml->timestamp;
		$scan_data['geblink'] = $xmldata->strUrl;
        if (isset($xml->gebaeude)) {
            
			$sql_time="SELECT MIN(time) AS time FROM `{db_tb_scans_geb}` WHERE `coords`='".$scan_data['coords']."'";
			$result_time = $db->db_query($sql_time);
			$row_time = $db->db_fetch_array($result_time);
			if ($row_time['time']<$scan_data['time']) {
				$sql_del="DELETE FROM `{$db_tb_scans_geb}` WHERE `coords`='".$scan_data['coords']."'";
				$result = $db->db_query($sql_del)
					or error(GENERAL_ERROR, 'Could not delete gebscan information.', '', __FILE__, __LINE__, $sql_del);
			}
			
			foreach ($xml->gebaeude->gebaeude as $gebaeude) {
                if (!isset($scan_data['geb'])) {
                    $scan_data['geb'] = "<table class='scan_table'>\n";
                }
                $scan_data['geb'] .= "<tr class='scan_row'>\n";
                $scan_data['geb'] .= "\t<td class='scan_object'>\n";
                $scan_data['geb'] .= (string)$gebaeude->name;
                $scan_data['geb'] .= "\n\t</td>\n";
                $scan_data['geb'] .= "\t<td class='scan_value'>\n";
                $scan_data['geb'] .= (int)$gebaeude->anzahl;
                $scan_data['geb'] .= "\n\t</td>\n</tr>\n";
				
				$SQLdata = array (
					'coords'	=> $scan_data['coords'],
					'geb_id'	=> getGebIDByName((string)$gebaeude->name),
					'geb_anz'	=> (int)$gebaeude->anzahl,
					'time'	=> $scan_data['time']
				);
				$result = $db->db_insert($db_tb_scans_geb, $SQLdata)
					or error(GENERAL_ERROR, 'Could not insert gebscan.', '', __FILE__, __LINE__);
            }
        }
        if (isset($scan_data['geb'])) {
            $scan_data['geb'] .= "</table>\n";
            debug_var("scan_data['geb']", $scan_data['geb']);
        }
				
        // Schiffe/Ress
    } else if ($scan_typ == 3) {
        $scan_data['schiffscantime'] = (int)$xml->timestamp;
		$scan_data['schifflink'] = $xmldata->strUrl;
        foreach ($xml->pla_def as $pla_def) {
            foreach ($pla_def->user as $user) {
                foreach ($user->schiffe as $schiff) {
                    foreach ($schiff->schifftyp as $schifftyp) {
                        if (!isset($scan_data['plan'])) {
                            $scan_data['plan'] = "<table class='scan_table'>\n";
                        }
                        $scan_data['plan'] .= "<tr class='scan_row'>\n";
                        $scan_data['plan'] .= "\t<td class='scan_object'>\n";
                        $scan_data['plan'] .= (string)$schifftyp->name;
                        $scan_data['plan'] .= "\n\t</td>\n";
                        $scan_data['plan'] .= "\t<td class='scan_value'>\n";
                        $scan_data['plan'] .= (int)$schifftyp->anzahl;
                        $scan_data['plan'] .= "\n\t</td>\n</tr>\n";
                    }
                }
                foreach ($user->defence as $defence) {
                    foreach ($defence->defencetyp as $defencetyp) {
                        if (!isset($scan_data['def'])) {
                            $scan_data['def'] = "<table class='scan_table'>\n";
                        }
                        $scan_data['def'] .= "<tr class='scan_row'>\n";
                        $scan_data['def'] .= "\t<td class='scan_object'>\n";
                        $scan_data['def'] .= (string)$defencetyp->name;
                        $scan_data['def'] .= "\n\t</td>\n";
                        $scan_data['def'] .= "\t<td class='scan_value'>\n";
                        $scan_data['def'] .= (int)$defencetyp->anzahl;
                        $scan_data['def'] .= "\n\t</td>\n</tr>\n";
                    }
                }
            }
        }
        if (isset($scan_data['plan'])) {
            $scan_data['plan'] .= "</table>\n";
        } else {
            $scan_data['plan'] = "";
        }
        debug_var("scan_data['plan']", $scan_data['plan']);

        if (isset($scan_data['def'])) {
            $scan_data['def'] .= "</table>\n";
        } else {
            $scan_data['def'] = "";
        }
        debug_var("scan_data['def']", $scan_data['def']);

        foreach ($xml->flotten_def as $flotten_def) {
            foreach ($flotten_def->user as $user) {
                if (!isset($scan_data['stat'])) {
                    $scan_data['stat'] = "<table class='scan_table'>\n";
                }
                $scan_data['stat'] .= "\t<tr class='scan_row'>\n";
                $scan_data['stat'] .= "\t\t<td colspan='2' class='scan_title'>";
                $scan_data['stat'] .= "Stationierte Flotte von ";
                $scan_data['stat'] .= (string)$user->name;
                $scan_data['stat'] .= ":</td>\n";
                $scan_data['stat'] .= "\t</tr>\n";
                foreach ($user->schiffe as $schiffe) {
                    foreach ($schiffe->schifftyp as $schifftyp) {
                        $scan_data['stat'] .= "<tr class='scan_row'>\n";
                        $scan_data['stat'] .= "\t<td class='scan_object'>\n";
                        $scan_data['stat'] .= (string)$schifftyp->name;
                        $scan_data['stat'] .= "\n\t</td>\n";
                        $scan_data['stat'] .= "\t<td class='scan_value'>\n";
                        $scan_data['stat'] .= (int)$schifftyp->anzahl;
                        $scan_data['stat'] .= "\n\t</td>\n</tr>\n";
                    }
                }
            }
        }
        if (isset($scan_data['stat'])) {
            $scan_data['stat'] .= "</table>\n";
        } else {
            $scan_data['stat'] = "";
        }
        debug_var("scan_data['stat']", $scan_data['stat']);

    }
    // Gebäude oder Schiffe/Ress
    if ($scan_typ == 2 || $scan_typ == 3) {
        foreach ($xml->ressourcen as $ressourcen) {
            foreach ($ressourcen->ressource as $ressource) {
                if ($ressource->id == 1) {
                    debug_var("scan_data['eisen']", $scan_data['eisen'] = (int)$ressource->anzahl);
                } else if ($ressource->id == 2) {
                    debug_var("scan_data['stahl']", $scan_data['stahl'] = (int)$ressource->anzahl);
                } else if ($ressource->id == 3) {
                    debug_var("scan_data['vv4a']", $scan_data['vv4a'] = (int)$ressource->anzahl);
                } else if ($ressource->id == 4) {
                    debug_var("scan_data['eis']", $scan_data['eis'] = (int)$ressource->anzahl);
                } else if ($ressource->id == 5) {
                    debug_var("scan_data['chemie']", $scan_data['chemie'] = (int)$ressource->anzahl);
                } else if ($ressource->id == 6) {
                    debug_var("scan_data['wasser']", $scan_data['wasser'] = (int)$ressource->anzahl);
                } else if ($ressource->id == 7) {
                    debug_var("scan_data['energie']", $scan_data['energie'] = (int)$ressource->anzahl);
                }
            }
        }
    }
    $results = save_sbxml($scan_data);
    debug_var("save_sbxml", $results);
    foreach ($results as $result) {
        echo "<div class='system_notification'>" . $result . "</div>";
    }

    return true;
}

function save_sbxml($scan_data)
{
    global $db, $db_tb_scans, $db_tb_user, $selectedusername, $db_tb_scans_geb;

    $scan_coords = $scan_data['coords_gal'].':'.$scan_data['coords_sys'].':'.$scan_data['coords_planet'];

    $results = array();
    $sql = "SELECT * FROM `{$db_tb_scans}` WHERE `coords_gal`=" . $scan_data['coords_gal'] . " AND `coords_sys`=" . $scan_data['coords_sys'] . " AND `coords_planet`=" . $scan_data['coords_planet'];
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query planet information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    // vollständiger Scan?
    if (isset($scan_data['vollstaendig']) && $scan_data['vollstaendig'] == 1) {
        unset($scan_data['vollstaendig']);
    } else {
        $results[] = "Der Scan " . $scan_data['coords'] . " ist nicht vollständig.";

        return $results;
    }

    // Nebel vorhanden
    if (isset($scan_data['nebula'])) {
        //Nebel in sysscanstabelle wird von Systemscans aktualisert

        unset($scan_data['nebula']);
    }

    if (!empty($row)) {

        // Neuerer Geoscan vorhanden
        if (!empty($scan_data['geoscantime']) && $row['geoscantime'] >= $scan_data['geoscantime']) {
            $results[] = "Neuerer oder aktueller Geoscan bereits vorhanden.";

            return $results;
        }
        // Neuerer Schiffscan vorhanden
        if (!empty($scan_data['schiffscantime']) && $row['schiffscantime'] >= $scan_data['schiffscantime']) {
            $results[] = "Neuerer oder aktueller Schiffscan bereits vorhanden.";

            return $results;
        }
        // Neuerer Gebscan vorhanden
        if (!empty($scan_data['gebscantime']) && $row['gebscantime'] >= $scan_data['gebscantime']) {
            $results[] = "Neuerer oder aktueller Gebäudescan bereits vorhanden.";

            return $results;
        }

        //nach einem Planettypwechel sind alle Scans des Planeten veraltet
        if ($row['typ'] !== $scan_data['typ']) {
            if ($scan_data['time'] > $row['time']) {
                $scan_data['typchange_time'] = $scan_data['time'];
                ResetGeodataByCoords($scan_coords);
                ResetPlaniedataByCoords($scan_coords);
            } else {
                $results[] = "Scan veraltet.";

                return $results;
            }
        }

        //nach einem Objektwechsel sind Schiff- oder Geb-Scans des Planeten veraltet
        if (($row['objekt'] !== $scan_data['objekt']) AND (isset($scan_data['gebscantime']) OR isset($scan_data['schiffscantime']))) {
            if ($scan_data['time'] > $row['time']) {
                $scan_data['objektchange_time'] = $scan_data['time'];
                ResetPlaniedataByCoords($scan_coords);
            } else {
                $results[] = "Scan veraltet.";

                return $results;
            }
        }

        //nach einem userwechsel sind Schiff- oder Geb-Scans des Planeten veraltet
        if (($row['user'] !== $scan_data['user']) AND (isset($scan_data['gebscantime']) OR isset($scan_data['schiffscantime']))) {
            if ($scan_data['time'] > $row['time']) {
                $scan_data['userchange_time'] = $scan_data['time'];
                ResetPlaniedataByCoords($scan_coords);
            } else {
                $results[] = "Scan veraltet.";

                return $results;
            }
        }

        //Planetendaten aktualisieren
        $where = " WHERE `coords_gal`=" . $scan_data['coords_gal'] . " AND `coords_sys`=" . $scan_data['coords_sys'] . " AND `coords_planet`=" . $scan_data['coords_planet'];
        $db->db_update($db_tb_scans, $scan_data, $where)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

        $results[] = "Scan " . $scan_data['coords'] . " aktualisiert.";

    } else {

        // Planeten-Eintrag noch nicht vorhanden -> Planeteninformationen einfügen
        $db->db_insert($db_tb_scans, $scan_data)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        $results[] = "Scan " . $scan_data['coords'] . " hinzugefügt.";

    }

    //Geoscanpunkt vergeben
    if (isset($scan_data['geoscantime'])) {
        $sql1 = "UPDATE `{$db_tb_user}` SET `geopunkte`=`geopunkte`+1 WHERE `sitterlogin`='" . $selectedusername . "';";
        $result_u = $db->db_query($sql1)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql1);
    }
	
	return $results;
}

function parse_kbxml($xmldata)
{
    global $db, $db_tb_kb, $db_tb_kb_def, $db_tb_kb_verluste, $db_tb_kb_pluenderung, $db_tb_kb_bomb,
           $db_tb_kb_bomb_geb, $db_tb_kb_flotten, $db_tb_kb_flotten_schiffe, $anzahl_kb_neu, $ausgabe;

    $id   = $xmldata->iId;
    $hash = $xmldata->strHash;

    $link = str_replace("&typ=xml", "", $xmldata->strUrl); //! damit BBCode nachher funktioniert

    // Überprüfen, ob KB schon in Datenbank
    $sql = "SELECT ID_KB FROM {$db_tb_kb} WHERE ID_KB = '$id'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    // Wenn keiner da weiter
    if ($db->db_num_rows($result) == 0) {

        $xml = simplexml_load_file_ex($xmldata->strUrl);
        if (empty($xml)) {
            echo "<div class='system_error'>XML-Fehler: {$xmldata->strUrl} konnte nicht geladen werden</div>\n";

            return false;
        }

        ++$anzahl_kb_neu;

        // Allgemein
        $kb = array(
            'id'               => $id,
            'hash'             => $hash,
            'time'             => (int)$xml->timestamp['value'],
            'verteidiger'      => (string)$xml->plani_data->user->name['value'],
            'verteidiger_ally' => (string)$xml->plani_data->user->allianz_tag['value'],
            'planet_name'      => (string)$xml->plani_data->plani_name['value'],
            'koords_gal'       => (int)$xml->plani_data->koordinaten->gal['value'],
            'koords_sol'       => (int)$xml->plani_data->koordinaten->sol['value'],
            'koords_pla'       => (int)$xml->plani_data->koordinaten->pla['value'],
            'koords_string'    => (string)$xml->plani_data->koordinaten->string['value'],
            'typ'              => (int)$xml->kampf_typ->id['value'],
            'resultat'         => (int)$xml->resultat->id['value'],
        );

        // Defstellungen
        if (isset($xml->pla_def->user->defence->defencetyp)) {
            $def = $xml->pla_def->user->defence->defencetyp;
            foreach ($def as $value) {
                $kb['def'][] = array(
                    'id'      => (int)$value->id['value'],
                    'name'    => (string)$value->name['value'],
                    'start'   => (int)$value->anzahl_start['value'],
                    'ende'    => (int)$value->anzahl_ende['value'],
                    'verlust' => (int)$value->anzahl_verlust['value'],
                );
            }
        }

        // Verluste
        // att
        if (isset($xml->resverluste->att->resource)) {
            $res = $xml->resverluste->att->resource;
            foreach ($res as $value) {
                $kb['verluste'][] = array(
                    'id'     => (int)$value->id['value'],
                    'seite'  => 1,
                    'name'   => (string)$value->name['value'],
                    'anzahl' => (int)$value->anzahl['value'],
                );
            }
        }

        // def
        if (isset($xml->resverluste->def->resource)) {
            $res = $xml->resverluste->def->resource;
            foreach ($res as $value) {
                $kb['verluste'][] = array(
                    'id'     => (int)$value->id['value'],
                    'seite'  => 2,
                    'name'   => (string)$value->name['value'],
                    'anzahl' => (int)$value->anzahl['value'],
                );
            }
        }

        // Plünderung
        if (isset($xml->pluenderung->resource)) {
            $res = $xml->pluenderung->resource;
            foreach ($res as $value) {
                $kb['pluenderung'][] = array(
                    'id'     => (int)$value->id['value'],
                    'name'   => (string)$value->name['value'],
                    'anzahl' => (int)$value->anzahl['value'],
                );
            }
        }

        // Bomb
        if (isset($xml->bomben->user)) {
            $xml_bomb           = $xml->bomben;
            $kb['bomb']['user'] = (string)$xml_bomb->user->name['value'];
            // Bombertrefferchance
            if (isset($xml_bomb->bombentrefferchance)) {
                $kb['bomb']['trefferchance'] = $xml_bomb->bombentrefferchance['value'];
            }
            // Basis zerstört
            if (isset($xml_bomb->basis_zerstoert)) {
                $kb['bomb']['basis'] = (int)$xml_bomb->basis_zerstoert['value'];
            }
            // Bevölkerung
            if (isset($xml_bomb->bev_zerstoert)) {
                $kb['bomb']['bev'] = (int)$xml_bomb->bev_zerstoert['value'];
            }
            // getroffene Gebaude
            if (isset($xml_bomb->geb_zerstoert->geb)) {
                $xml_geb = $xml_bomb->geb_zerstoert->geb;
                foreach ($xml_geb as $value) {
                    $kb['bomb']['geb'][] = array(
                        'id'     => (int)$value->id['value'],
                        'name'   => (string)$value->name['value'],
                        'anzahl' => (int)$value->anzahl['value'],
                    );
                }
            }
        }

        // Flotten
        // Def (auf Planet)
        if (isset($xml->pla_def->user->schiffe)) {
            $user   = $xml->pla_def->user;
            $flotte = array(
                'art'  => 1,
                'name' => (string)$user->name['value'],
                'ally' => (string)$user->allianz_tag['value'],
            );
            if (isset($user->schiffe)) {
                $schiffe = $user->schiffe->schifftyp;
                foreach ($schiffe as $value) {
                    $flotte['schiffe'][] = array(
                        'id'             => (int)$value->id['value'],
                        'name'           => (string)$value->name['value'],
                        'klasse'         => (int)$value->klasse['value'],
                        'anzahl_start'   => (int)$value->anzahl_start['value'],
                        'anzahl_ende'    => (int)$value->anzahl_ende['value'],
                        'anzahl_verlust' => (int)$value->anzahl_verlust['value'],
                    );
                }
            }
            $kb['flotte'][] = $flotte;
        }

        // Def (stationiert)
        if (isset($xml->flotten_def->user)) {
            $user = $xml->flotten_def->user;
            foreach ($user as $value) {
                $flotte = array(
                    'art'  => 2,
                    'name' => (string)$value->name['value'],
                    'ally' => (string)$value->allianz_tag['value'],
                );
                if (isset($value->schiffe)) {
                    $schiffe = $value->schiffe->schifftyp;
                    foreach ($schiffe as $value) {
                        $flotte['schiffe'][] = array(
                            'id'             => (int)$value->id['value'],
                            'name'           => (string)$value->name['value'],
                            'klasse'         => (int)$value->klasse['value'],
                            'anzahl_start'   => (int)$value->anzahl_start['value'],
                            'anzahl_ende'    => (int)$value->anzahl_ende['value'],
                            'anzahl_verlust' => (int)$value->anzahl_verlust['value'],
                        );
                    }
                }
            }
            $kb['flotte'][] = $flotte;
        }

        //	Att
        if (isset($xml->flotten_att->user)) {
            $user = $xml->flotten_att->user;
            foreach ($user as $value) {
                $flotte = array(
                    'art'           => 3,
                    'name'          => (string)$value->name['value'],
                    'ally'          => (string)$value->allianz_tag['value'],
                    'planet_name'   => (string)$value->startplanet->plani_name['value'],
                    'koords_string' => (string)$value->startplanet->koordinaten->string['value'],
                );
                if (isset($value->schiffe)) {
                    $schiffe = $value->schiffe->schifftyp;
                    foreach ($schiffe as $value) {
                        $flotte['schiffe'][] = array(
                            'id'             => (int)$value->id['value'],
                            'name'           => (string)$value->name['value'],
                            'klasse'         => (int)$value->klasse['value'],
                            'anzahl_start'   => (int)$value->anzahl_start['value'],
                            'anzahl_ende'    => (int)$value->anzahl_ende['value'],
                            'anzahl_verlust' => (int)$value->anzahl_verlust['value'],
                        );
                    }
                }
            }
            $kb['flotte'][] = $flotte;
        }


        // Eintrag
        $sqldata = array(
            'ID_KB'            => $kb['id'],
            'hash'             => $kb['hash'],
            'TIME'             => $kb['time'],
            'verteidiger'      => $kb['verteidiger'],
            'verteidiger_ally' => $kb['verteidiger_ally'],
            'planet_name'      => $kb['planet_name'],
            'koords_gal'       => $kb['koords_gal'],
            'koords_sol'       => $kb['koords_sol'],
            'koords_pla'       => $kb['koords_pla'],
            'typ'              => $kb['typ'],
            'resultat'         => $kb['resultat']
        );
        $result = $db->db_insert($db_tb_kb, $sqldata)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        // Def
        if (isset($kb['def'])) {
            $sql = "
				INSERT INTO {$db_tb_kb_def} (ID_KB, ID_IW_DEF, anz_start, anz_verlust)
				VALUES";
            foreach ($kb['def'] as $key => $value) {
                if ($key == 0) {
                    $sql .= "('$kb[id]', '$value[id]', '$value[start]', '$value[verlust]')";
                } else {
                    $sql .= ", ('$kb[id]', '$value[id]', '$value[start]', '$value[verlust]')";
                }
            }
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not update kb deff information.', '', __FILE__, __LINE__, $sql);
        }
        // Verluste
        if (isset($kb['verluste'])) {
            $sql = "
				INSERT INTO {$db_tb_kb_verluste}
					(ID_KB, ID_IW_RESS, seite, anzahl)
				VALUES";
            foreach ($kb['verluste'] as $key => $value) {
                if ($key == 0) {
                    $sql .= "
					('$kb[id]', '$value[id]', '$value[seite]', '$value[anzahl]')";
                } else {
                    $sql .= ",
					('$kb[id]', '$value[id]', '$value[seite]', '$value[anzahl]')";
                }
            }
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }

        // Plünderung
        if (isset($kb['pluenderung'])) {
            $sql = "
				INSERT INTO {$db_tb_kb_pluenderung}
					(ID_KB, ID_IW_RESS, anzahl)
				VALUES";
            foreach ($kb['pluenderung'] as $key => $value) {
                if ($key == 0) {
                    $sql .= "
					('$kb[id]', '$value[id]', '$value[anzahl]')";
                } else {
                    $sql .= ",
					('$kb[id]', '$value[id]', '$value[anzahl]')";
                }
            }
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }

        // Bomb
        if (isset($kb['bomb'])) {
            $sql    = "
				INSERT INTO {$db_tb_kb_bomb}
					(`ID_KB`, `time`";
            $values = "
				VALUES
					('$kb[id]', '$kb[time]'";
            foreach ($kb['bomb'] as $key => $value) {
                if ($key != 'geb') {
                    $sql .= ", `$key`";
                    $values .= ", '$value'";
                }
            }
            $sql .= ") $values )";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            // Gebäude
            if (!empty($kb['bomb']['geb'])) {
                $sql = "
                    INSERT INTO {$db_tb_kb_bomb_geb}
                        (ID_KB, ID_IW_GEB, anzahl)
                    VALUES";
                foreach ($kb['bomb']['geb'] as $key => $value) {
                    if ($key == 0) {
                        $sql .= "
                            ('$kb[id]', '$value[id]', '$value[anzahl]')";
                    } else {
                        $sql .= ",
                            ('$kb[id]', '$value[id]', '$value[anzahl]')";
                    }
                }
                $result = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            }
        }
        // Eintrag Flotte
        if (isset($kb['flotte'])) {
            $sql = "
				INSERT INTO {$db_tb_kb_flotten}
					(ID_KB, TIME, art, NAME, ally)
				VALUES";
            foreach ($kb['flotte'] as $value) {
                if ($value['art'] == 3) {
                    $sql = "
						INSERT INTO {$db_tb_kb_flotten}
							(ID_KB, TIME, art, NAME, ally, planet_name, koords_string)
						VALUES
							('$kb[id]', '$kb[time]', '$value[art]', '$value[name]', '$value[ally]', '$value[planet_name]', '$value[koords_string]')";
                } else {
                    $sql = "
						INSERT INTO {$db_tb_kb_flotten}
							(ID_KB, TIME, art, NAME, ally)
						VALUES
							('$kb[id]', '$kb[time]', '$value[art]', '$value[name]', '$value[ally]')";
                }
                $result = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
                $ID_FLOTTE = @mysql_insert_id();
                $sql       = "
					INSERT INTO {$db_tb_kb_flotten_schiffe}
						(ID_FLOTTE, ID_IW_SCHIFF, anz_start, anz_verlust)
					VALUES";
                foreach ($value['schiffe'] as $key2 => $value2) {
                    if ($key2 == 0) {
                        $sql .= "
						('$ID_FLOTTE', '$value2[id]', '$value2[anzahl_start]', '$value2[anzahl_verlust]')";
                    } else {
                        $sql .= ",
						('$ID_FLOTTE', '$value2[id]', '$value2[anzahl_start]', '$value2[anzahl_verlust]')";
                    }
                }
                $result = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
            }
        }

        //! ########### HACK fuer raidmodul/raidview  #############################
        //! ToDo: Mac: Eintrag Daten fuer raidview Tabelle (voellig überholt. Raidmodul sollte einfach auf die KB tabellen umgeschrieben werden)
        global $db_tb_raidview, $selectedusername;

        // links sammeln die bereits in der db drinnen sind
        $sqlL = "SELECT link FROM " . $db_tb_raidview;
        $resultL = $db->db_query($sqlL)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlL);
        $links = array();
        while ($rowL = $db->db_fetch_array($resultL)) {
            $links[] = $rowL['link'];
        }

        if (in_array($link, $links, true)) { //! Überpruefung zu Beginn sollte eigentlich schon ausreichen ?
            echo "KB <a href='" . $link . "' target='_new'><i>" . $link = substr($link, 42, 60) . "</i></a> bereits vorhanden.\n";
        } else {
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

            foreach ($vars as $var) {
                ${$var} = 0;
            }

            $plani    = $kb["koords_string"];
            $zeit     = $kb['time'];
            $geraidet = $kb["verteidiger"];
            if (isset($kb['pluenderung'])) {
                foreach ($kb['pluenderung'] as $value) {
                    $name = strtolower($value["name"]);
                    if (strpos($name, "chem") !== false) {
                        $chem = $value["anzahl"];
                    } else {
                        ${$name} = $value["anzahl"];
                    }
                }
            }
            if (isset($kb["verluste"])) {
                foreach ($kb['verluste'] as $value) {
                    if ($value["seite"] == 2) {
                        continue;
                    } //! Verteidigerverluste überspringen

                    $name = "v_" . strtolower($value["name"]);
                    if (strpos($name, "chem") !== false) {
                        $v_chem = $value["anzahl"];
                    } else {
                        ${$name} = $value["anzahl"];
                    }
                }
            }

            $g_eisen   = $eisen - $v_eisen;
            $g_stahl   = $stahl - $v_stahl;
            $g_vv4a    = $vv4a - $v_vv4a;
            $g_chem    = $chem - $v_chem;
            $g_eis     = $eis - $v_eis;
            $g_wasser  = $wasser - $v_wasser;
            $g_energie = $energie - $v_energie;

            $sql = "INSERT INTO 
                        $db_tb_raidview 
                        (id,coords,DATE,eisen,stahl,vv4a,chemie,eis,wasser,energie,USER,geraided,link,v_eisen,v_stahl,v_vv4a,v_chem,v_eis,v_wasser,v_energie,g_eisen,g_stahl,g_vv4a,g_chem,g_eis,g_wasser,g_energie)
                    VALUES 
                        ('NULL','$plani','$zeit',$eisen,$stahl,$vv4a,$chem,$eis,$wasser,$energie,'$selectedusername','$geraidet','$link','$v_eisen','$v_stahl','$v_vv4a','$v_chem','$v_eis','$v_wasser','$v_energie','$g_eisen','$g_stahl','$g_vv4a','$g_chem','$g_eis','$g_wasser','$g_energie')";

            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            //echo "neuer KB: <a href='".$link."' target='_new'>" . $link=substr($link, 42, 60) . "</a>\n";
        }
        //! ########### HACK fuer raidmodul/raidview  Ende #############################

        // noch BBCode holen
        $bbcode = '';
        if (!empty($link)) {
            if ($handle = @fopen($link . '&typ=bbcode', "r")) {
                while (!@feof($handle)) {
                    $bbcode .= @fread($handle, 512);
                }
                @fclose($handle);
            }
        }

        $suchen   = '#(\[tr\]\[td\])((?:kleine|mittlere|grose|DN)(?: planetare| orbitale)? Werft)(\[/td\]\[td colspan=3\])([\d]+)(\[/td\]\[/tr\])#';
        $ersetzen = '$1[color=red]$2[/color]$3[color=red]$4[/color]$5';
        $bbcode   = preg_replace($suchen, $ersetzen, $bbcode);

        $suchen   = array('[td colspan=4]');
        $ersetzen = array('[td]');
        $bbcode   = str_replace($suchen, $ersetzen, $bbcode);

        $suchen   = array('[td colspan=3]');
        $ersetzen = array('[td]');
        $bbcode   = str_replace($suchen, $ersetzen, $bbcode);

        $ausgabe['KBs'][] = array(
            'Zeit'    => $kb['time'],
            'Bericht' => $bbcode,
            'Link'    => $link,
        );

    } else { // nur BBCode holen
        if (!empty($link)) {
            if ($handle = @fopen($link . '&typ=bbcode', "r")) {
                $bbcode = '';
                while (!@feof($handle)) {
                    $bbcode .= @fread($handle, 512);
                }
                @fclose($handle);
            }
        }

        $suchen   = '#(\[tr\]\[td\])((?:kleine|mittlere|grose|DN)(?: planetare| orbitale)? Werft)(\[/td\]\[td colspan=3\])([\d]+)(\[/td\]\[/tr\])#';
        $ersetzen = '$1[color=red]$2[/color]$3[color=red]$4[/color]$5';
        $bbcode   = preg_replace($suchen, $ersetzen, $bbcode);

        $suchen   = array('[td colspan=4]');
        $ersetzen = array('[td]');
        $bbcode   = str_replace($suchen, $ersetzen, $bbcode);

        $suchen   = array('[td colspan=3]');
        $ersetzen = array('[td]');
        $bbcode   = str_replace($suchen, $ersetzen, $bbcode);

        $xml = simplexml_load_file_ex($link . '&typ=xml');
        if (empty($xml)) {
            echo "<div class='system_error'>XML-Fehler: {$link}&typ=xml konnte nicht geladen werden</div>\n";

            return false;
        }

        $ausgabe['KBs'][] = array(
            'Zeit'    => (int)$xml->timestamp['value'],
            'Bericht' => $bbcode,
            'Link'    => $link,
        );
    }

    return true;
}

/**
 * function parse_unixml
 *
 * Läd die unixml-Datei und gibt Inhalt als SimpleXMLElement Objekt an den unixml-parser weiter.
 *
 * @param $xmldata object Daten von der parserlib
 *
 * @return bool Verarbeitung erfolgreich
 */

function parse_unixml($xmldata)
{
    $xml = simplexml_load_file_ex($xmldata->strUrl); //Unisichtxml-Datei laden und parsen
    if (!empty($xml)) {
        input_unixml($xml);
    } else {
        echo "<div class='system_error'>XML-Fehler: {$xmldata->strUrl} konnte nicht geladen werden</div>\n";

        return false;
    }

    return true;
}

/**
 * unixml-parser
 *
 * Verarbeitet Uni/Systemxml-Objekte und aktualisiert die DB.
 *
 * @param object SimpleXMLElement Uni/Systemxmldaten
 *
 * @return bool Verarbeitung erfolgreich
 *
 * @author masel
 */
function input_unixml($xml)
{
    global $db, $db_tb_scans, $db_tb_spieler, $db_tb_sysscans, $db_tb_user, $selectedusername;

    if (empty($xml)) {
        echo "<div class='system_error'>XML-Fehler</div>\n";

        return false;
    }

    $aktualisierungszeit = (int)$xml->informationen->aktualisierungszeit;
    if (empty($aktualisierungszeit)) { //keine gültige Aktualisierungszeit -> Ende
        echo "<div class='system_error'>Aktualisierungszeit nicht gefunden -> XML wird ignoriert</div>\n";

        return false;
    }

    $sql = "SELECT count(*) AS Anzahl FROM `{$db_tb_scans}` WHERE `time` = {$aktualisierungszeit};";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query planet information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    $planets_with_same_time_before = $row['Anzahl'];

    $sql = "SELECT count(*) AS Anzahl FROM `{$db_tb_sysscans}` WHERE `date` = {$aktualisierungszeit};";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query planet information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    $systems_with_same_time_before = $row['Anzahl'];

    $sql = "SELECT count(*) AS Anzahl FROM `{$db_tb_spieler}` WHERE `playerupdate_time` = {$aktualisierungszeit};";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query planet information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    $players_with_same_time_before = $row['Anzahl'];


    $sql_planet_update_begin = "INSERT INTO `{$db_tb_scans}` (`coords`, `coords_gal`, `coords_sys`, `coords_planet`, `user`, `userchange_time`, `planetenname`, `typ`, `typchange_time`, `objekt`, `objektchange_time`, `nebel`, `plaid`, `time`) VALUES ";

    //bei schon vorhandenem Planten in der DB werden einige Einträge selektiv ersetzt (Hinweis: Die Werte werden in Reihenfolge innerhalb des Queries nacheinander zugewiesen NICHT erst beim ende des kompletten Queries)
    $sql_planet_update_end = " ON DUPLICATE KEY UPDATE";
    $sql_planet_update_end .= " `userchange_time` = IF((({$aktualisierungszeit} > `time`) AND STRCMP(VALUES(`user`), `user`)), {$aktualisierungszeit}, `userchange_time`),";
    $sql_planet_update_end .= " `user` = IF(({$aktualisierungszeit} = `userchange_time`), VALUES(`user`), `user`),"; //Besitzer des Planeten ersetzen wenn aktualisierungszeit älter als in der DB und Änderung
    $sql_planet_update_end .= " `planetenname` = IF((({$aktualisierungszeit} > `time`) AND STRCMP(VALUES(`planetenname`), `planetenname`)), VALUES(`planetenname`), `planetenname`),"; //Planetenname ersetzen wenn aktualisierungszeit älter als in der DB und Änderung
    $sql_planet_update_end .= " `typchange_time` = IF((({$aktualisierungszeit} > `time`) AND STRCMP(VALUES(`typ`), `typ`)), {$aktualisierungszeit}, `typchange_time`),";
    $sql_planet_update_end .= " `typ` = IF({$aktualisierungszeit} = `typchange_time`, VALUES(`typ`), `typ`),"; //Planetentyp ersetzen wenn aktualisierungszeit älter als in der DB und vorliegender Änderung
    $sql_planet_update_end .= " `objektchange_time` = IF((({$aktualisierungszeit} > `time`) AND STRCMP(VALUES(`objekt`), `objekt`)), {$aktualisierungszeit}, `objektchange_time`),";
    $sql_planet_update_end .= " `objekt` = IF({$aktualisierungszeit} = `objektchange_time`, VALUES(`objekt`), `objekt`),"; //Objekttyp ersetzen wenn aktualisierungszeit älter als in der DB und vorliegender Änderung
    //$sql_planet_update_end .= " `nebel` = IF((({$aktualisierungszeit} > `time`) AND STRCMP(VALUES(`nebel`), `nebel`)), VALUES(`nebel`), `nebel`),";                //Nebel aktualisieren; sollten sich nicht ändern deswegen mal auskommentiert
    $sql_planet_update_end .= " `time` = IF({$aktualisierungszeit} > `time`, VALUES(`time`), `time`);";
    $sql_planet_update_end .= ";";

    $sql_player_update_begin = "INSERT INTO `{$db_tb_spieler}` (`name`, `allianz`, `dabeiseit`, `playerupdate_time`) VALUES ";
    //bei schon vorhandenem Spieler in der DB prüfen auf Allianzänderung
    $sql_player_update_end = " ON DUPLICATE KEY UPDATE";
    $sql_player_update_end .= " `allychange_time` = IF((STRCMP(VALUES(`allianz`), `allianz`) AND ((`allychange_time` IS NULL) OR ({$aktualisierungszeit} > `allychange_time`))), {$aktualisierungszeit}, `allychange_time`),"; //Allianzänderungszeit auf die des Scans setzen (wenn sie neuer bzw nicht vorhanden ist und sich die Allianz geändert hat), nachfolgende Abfragen können sich dann darauf beziehen
    $sql_player_update_end .= " `exallianz` =   IF(((`allychange_time` = {$aktualisierungszeit}) AND (`playerupdate_time` < {$aktualisierungszeit})), `allianz`, `exallianz`),"; //exallianz aktualisieren
    $sql_player_update_end .= " `allianzrang` = IF(((`allychange_time` = {$aktualisierungszeit}) AND (`playerupdate_time` < {$aktualisierungszeit})), NULL, `allianzrang`),"; //alten Allianzrang löschen
    $sql_player_update_end .= " `allianz` =     IF(((`allychange_time` = {$aktualisierungszeit}) AND (`playerupdate_time` < {$aktualisierungszeit})), VALUES(`allianz`), `allianz`),"; //neue Allianz schreiben
    $sql_player_update_end .= " `playerupdate_time` = IF((`playerupdate_time` < {$aktualisierungszeit}), {$aktualisierungszeit}, `playerupdate_time`);"; //Angabe des Updates der Spielerinformationen aktualisieren

    $sql_system_update_begin = "INSERT INTO `{$db_tb_sysscans}` (`id`, `gal`, `sys`, `objekt`, `date`, `nebula`) VALUES ";
    $sql_system_update_end   = " ON DUPLICATE KEY UPDATE";
    //andere Daten sollten sich nicht ändern deshalb nur die Aktualisierung des Scandatums   
    $sql_system_update_end .= " `date` = IF(({$aktualisierungszeit} > `date`), {$aktualisierungszeit}, `date`);";

    $numPlanetData     = 0;
    $sql_planet_update = $sql_planet_update_begin;

    $PlayerData         = Array();
    $PlayerDataToUpdate = Array();
    $sql_player_update  = $sql_player_update_begin;

    $SystemsToUpdate   = Array();
    $SystemsUpdated    = 0;
    $sql_system_update = $sql_system_update_begin;

    foreach ($xml->planet as $Plannie) {
        $planienummer = (int)($Plannie->koordinaten->pla);

        if ($planienummer > 0) { //Planieinfos ab Planienummer 1

            if (($planienummer === 1) AND ((string)$Plannie->objekt_typ === 'Raumstation')) { //check auf Raumstation (=Stargate)
                $id                             = (int)($Plannie->koordinaten->gal) . ':' . (int)($Plannie->koordinaten->sol);
                $SystemsToUpdate[$id]['objekt'] = 'Stargate';

                if (count($SystemsToUpdate) >= DB_MAX_INSERTS) { //eingestellte Maximalanzahl der Datensätze für die DB erreicht
                    // -> sql String zusammenbauen und in die DB einfügen
                    foreach ($SystemsToUpdate as $id => $sys) {
                        $sql_system_update .= "('{$id}', {$sys['gal']}, {$sys['sys']}, '{$sys['objekt']}', {$sys['date']}, '{$sys['nebula']}'),";
                    }

                    $sql_system_update = mb_substr($sql_system_update, 0, -1) . $sql_system_update_end; //letztes "," des SQL-Queries entfernen und ON DUPLICATE KEY UPDATE - Teil anhängen
                    $result = $db->db_query($sql_system_update)
                        or error(GENERAL_ERROR, 'DB System Insertfehler!', '', __FILE__, __LINE__, $sql_system_update);

                    $SystemsUpdated   += count($SystemsToUpdate);
                    $SystemsToUpdate   = Array();
                    $sql_system_update = $sql_system_update_begin;
                }
            }

            $username = (string)$Plannie->user->name;

            $sql_planet_update .= "('" . (string)$Plannie->koordinaten->string . "', " . (int)($Plannie->koordinaten->gal) . ", " . (int)($Plannie->koordinaten->sol) . ", " . $planienummer . ", '{$username}', {$aktualisierungszeit}, '" . (string)$Plannie->name . "', '" . (string)$Plannie->planet_typ . "', {$aktualisierungszeit}, '" . (string)$Plannie->objekt_typ . "', {$aktualisierungszeit}, '" . (isset($Plannie->nebel) ? (string)$Plannie->nebel : '') . "', " . (int)($Plannie->id) . ", {$aktualisierungszeit}),";
            ++$numPlanetData;

            if ($numPlanetData >= DB_MAX_INSERTS) { //eingestellte Maximalanzahl der Datensätze für die DB erreicht
                // -> sql String zusammenbauen und in die DB einfügen
                $sql_planet_update = mb_substr($sql_planet_update, 0, -1) . $sql_planet_update_end; //letztes "," des SQL-Queries entfernen und ON DUPLICATE KEY UPDATE - Teil anhängen
                $result = $db->db_query($sql_planet_update)
                    or error(GENERAL_ERROR, 'DB Planeten Insertfehler!', '', __FILE__, __LINE__, $sql_planet_update);

                $numPlanetData     = 0; //Planetendatensatzzähler und sql-query zurücksetzen
                $sql_planet_update = $sql_planet_update_begin;
            }


            if ($username !== '') {
                if (!array_key_exists($username, $PlayerData)) { //Spieler noch nicht im Spieler array vorhanden -> hinzufügen

                    $PlayerDataToUpdate[$username] = (string)$Plannie->user->allianz_tag;
                    $PlayerData[$username]         = (string)$Plannie->user->allianz_tag;

                    if (count($PlayerDataToUpdate) >= DB_MAX_INSERTS) { //eingestellte Maximalanzahl der Datensätze für die DB erreicht
                        // -> sql String zusammenbauen und in die DB einfügen
                        foreach ($PlayerDataToUpdate as $name => $ally) {
                            $sql_player_update .= "('" . $name . "', '" . $ally . "', {$aktualisierungszeit}, {$aktualisierungszeit}),";
                        }

                        $sql_player_update = mb_substr($sql_player_update, 0, -1) . $sql_player_update_end; //letztes "," des SQL-Queries entfernen und ON DUPLICATE KEY UPDATE - Teil anhängen
                        $result = $db->db_query($sql_player_update)
                            or error(GENERAL_ERROR, 'DB Spieler Insertfehler!', '', __FILE__, __LINE__, $sql_player_update);

                        $PlayerDataToUpdate = Array(); //neue Spieler und sql-query zurücksetzen
                        $sql_player_update  = $sql_player_update_begin;
                    }
                }
            }
        } elseif ($planienummer === 0) { //Planienummer 0 = Sonne / schwarzes Loch -> für Systeminfo Tabelle auswerten

            $id                             = (int)($Plannie->koordinaten->gal) . ':' . (int)($Plannie->koordinaten->sol);
            $SystemsToUpdate[$id]['gal']    = (int)($Plannie->koordinaten->gal);
            $SystemsToUpdate[$id]['sys']    = (int)($Plannie->koordinaten->sol);
            $SystemsToUpdate[$id]['objekt'] = (((string)$Plannie->planet_typ === 'Sonne') ? 'sys' : $Plannie->planet_typ);
            $SystemsToUpdate[$id]['date']   = $aktualisierungszeit;
            $SystemsToUpdate[$id]['nebula'] = (isset($Plannie->nebel) ? (string)$Plannie->nebel : '');

        }
    }

    if (!empty($numPlanetData)) { //letzten Planetendaten in die DB laden
        $sql_planet_update = mb_substr($sql_planet_update, 0, -1) . $sql_planet_update_end; //letztes "," des SQL-Queries entfernen und ON DUPLICATE KEY UPDATE - Teil anhängen
        $result = $db->db_query($sql_planet_update)
            or error(GENERAL_ERROR, 'DB Updatefehler!', '', __FILE__, __LINE__, $sql_planet_update);

        unset($sql_planet_update);
    }

    if (!empty($SystemsToUpdate)) { //letzten Systemdaten in die DB laden
        foreach ($SystemsToUpdate as $id => $sys) {
            $sql_system_update .= "('{$id}', {$sys['gal']}, {$sys['sys']}, '{$sys['objekt']}', {$sys['date']}, '{$sys['nebula']}'),";
        }

        $sql_system_update = mb_substr($sql_system_update, 0, -1) . $sql_system_update_end; //letztes "," des SQL-Queries entfernen und ON DUPLICATE KEY UPDATE - Teil anhängen
        $result = $db->db_query($sql_system_update)
            or error(GENERAL_ERROR, 'DB System Insertfehler!', '', __FILE__, __LINE__, $sql_system_update);

        $SystemsUpdated += count($SystemsToUpdate);
        if ($SystemsUpdated === 1) {
            reset($SystemsToUpdate);
            $System = (key($SystemsToUpdate));
        }
        unset($SystemsToUpdate);
    }

    if (!empty($PlayerDataToUpdate)) { //letzte Spielerdaten in die DB laden
        foreach ($PlayerDataToUpdate as $name => $ally) {
            $sql_player_update .= "('" . $name . "', '" . $ally . "', {$aktualisierungszeit}, {$aktualisierungszeit}),";
        }

        $sql_player_update = mb_substr($sql_player_update, 0, -1) . $sql_player_update_end;
        $result = $db->db_query($sql_player_update)
            or error(GENERAL_ERROR, 'DB Updatefehler!', '', __FILE__, __LINE__, $sql_player_update);

        unset($PlayerData);
        unset($PlayerDataToUpdate);
    }

    //ungültige planSchiff/Deff/Ressscanberichte löschen (bei Änderung Planettyp oder Objekttyp oder username)
    ResetPlaniedata($aktualisierungszeit);

    //ungültige Geodaten zu löschen (bei Änderung Planettyp)
    ResetGeodata($aktualisierungszeit);

    //Allianzänderungen in Historytabele übertragen
    AddAllychangetoHistory($aktualisierungszeit);

    //aktuelle Allianzen in alle Kartendaten übertragen
    SyncAllies($aktualisierungszeit);

    //Zahl der aktualisierten Planeten, Systeme und Spieler berechnen
    $sql = "SELECT count(*) AS Anzahl FROM `{$db_tb_scans}` WHERE `time` = {$aktualisierungszeit};";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query planet information.', '', __FILE__, __LINE__, $sql);
    $row                        = $db->db_fetch_array($result);
    $planets_with_same_time_now = $row['Anzahl'];
    $planets_updated            = $planets_with_same_time_now - $planets_with_same_time_before;

    $sql = "SELECT count(*) AS Anzahl FROM `{$db_tb_sysscans}` WHERE `date` = {$aktualisierungszeit};";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query planet information.', '', __FILE__, __LINE__, $sql);
    $row                        = $db->db_fetch_array($result);
    $systems_with_same_time_now = $row['Anzahl'];
    $systems_updated            = $systems_with_same_time_now - $systems_with_same_time_before;

    $sql = "SELECT count(*) AS Anzahl FROM `{$db_tb_spieler}` WHERE `playerupdate_time` = {$aktualisierungszeit};";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query planet information.', '', __FILE__, __LINE__, $sql);
    $row                        = $db->db_fetch_array($result);
    $players_with_same_time_now = $row['Anzahl'];
    $players_updated            = $players_with_same_time_now - $players_with_same_time_before;

    //Systemscanpunkte vergeben
    if ($systems_updated > 0) {
        $sql = "UPDATE `{$db_tb_user}` SET `syspunkte`=`syspunkte`+{$systems_updated} WHERE `id`='" . $selectedusername . "';";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }

    if ($SystemsUpdated === 1) {
        doc_message("System {$System} geparsed: {$planets_updated} Planeten aktualisiert, {$players_updated} Spieler aktualisiert ");
    } else {
        doc_message("Unixml geparsed: {$planets_updated} Planeten aktualisiert, " . $systems_updated . ($systems_updated === 1 ? " System " : " Systeme ") . "aktualisiert, {$players_updated} Spieler aktualisiert ");
    }
    return true;
}