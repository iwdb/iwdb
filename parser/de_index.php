<?php
/*****************************************************************************
 * de_index.php                                                              *
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

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

//****************************************************************************

function parse_de_index($return)
{
    global $db, $db_tb_scans, $db_tb_user_research, $selectedusername, $scan_datas, $db_tb_params, $db_tb_bestellung, $db_tb_sitterauftrag, $db_tb_research;

    debug_var('Input', $return);

    //get Accname from Koords
    $AccName = false;
    foreach ($return->objResultData->aContainer as $aContainer) {
        if (($aContainer->strIdentifier == "de_index_geb") AND ($aContainer->bSuccessfullyParsed)) {
            $AccName = getAccNameFromKolos($aContainer->objResultData->aGeb);
        }
    }
    if ($AccName === false) { //kein Eintrag gefunden -> ausgewählten Accname verwenden
        $AccName = $selectedusername;
    }

    if ($return->objResultData->bOngoingResearch == false) { // keine laufende Forschung

    	$SQLdata = array (
            'user' => $AccName,
            'rId'  => 272,
            'date' => '',
            'time' => CURRENT_UNIX_TIME
        );

        $result = $db->db_insertupdate($db_tb_user_research, $SQLdata)
            or error(GENERAL_ERROR, 'Could not update researchtime.', '', __FILE__, __LINE__);

        //# alle Forschungsaufträge des Spielers anpassen

        //aktuellsten Forschungsauftrag holen
        $result = $db->db_query("SELECT `date`, `date_b1`, `date_b2`, `resid` FROM `{$db_tb_sitterauftrag}` WHERE `user` = '{$AccName}' AND `typ` = 'Forschung' ORDER BY `date` ASC LIMIT 1;");
        if ($row = $db->db_fetch_array($result)) {
            $res_order_time_diff = $row['date'] - CURRENT_UNIX_TIME;

            if ($res_order_time_diff > 0) { //alle Forschungsaufträge liegen in der Zukunft -> alle vorziehen

                $sql = "UPDATE `{$db_tb_sitterauftrag}` SET `date` = `date`-{$res_order_time_diff}, `date_b1` = `date_b1`-{$res_order_time_diff}, `date_b2` = `date_b2`-{$res_order_time_diff} WHERE `user` = '{$AccName}' AND `typ` = 'Forschung';";
                $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not update researchtime.', '', __FILE__, __LINE__, $sql);
                debug_var("Forschungsanpassung", "Zeiten der Forschungsaufträge bei {$AccName} angepasst");

            }
        }

        echo "<div class='system_warning'>Es läuft keine Forschung bei {$AccName}!</div>";
    }

    foreach ($return->objResultData->aContainer as $aContainer) {
        if ($aContainer->bSuccessfullyParsed) {
            if ($aContainer->strIdentifier == "de_index_fleet") {                  //Flotten
                $fleetType = $aContainer->objResultData->strType; //own OR opposite

                $flottentyp = "";
                if ($fleetType == "own") {
                    $flottentyp = "eigene Flotten, * = Anzahl Schiffe, + = Anzahl Ress";
                } else {
                    $flottentyp = "fremde Flotten, + = Anzahl Ress";
                }

                if (!$aContainer->objResultData->bObjectsVisible) {
                    echo "<font color='orange'>Info: </font> keine Transportinformation (" . $flottentyp . ") sichtbar. Bitte Fluginformationen vor dem Parsen ausklappen <br />";
                }

                foreach ($aContainer->objResultData->aFleets as $msg) {
                    $tf_type = $msg->eTransfairType;

                    //! Mac: fehlt noch
//	                $scan_data['art'] == 'Ressourcen abholen' ||
//	                $scan_data['art'] == 'Ressourcenhandel' ||
//	                $scan_data['art'] == 'Ressourcenhandel (ok)' ||
//	                $scan_data['art'] == 'Stationieren' ||

                    if ($tf_type == "Rückkehr") { //! keine weiteren Infos vorhanden
                        continue;
                    } else if ($tf_type == "Kolonisation") { //! keine weiteren Infos vorhanden
                        continue;
                    } else if ($tf_type == "Übergabe"
                        || $tf_type == "Transport"
                        || $tf_type == "Übergabe (tr Schiffe)"
                        || $tf_type == "Massdriverpaket"
                        || $tf_type == "Sondierung (Schiffe/Def/Ress)"
                        || $tf_type == "Angriff"
                        || $tf_type == "Sondierung (Gebäude/Ress)"
                        || $tf_type == "Sondierung (Schiff) (Scout)"
                        || $tf_type == "Sondierung (Gebäude) (Scout)"
                        || $tf_type == "Sondierung (Geologie) (Scout)"
                        || $tf_type == "Sondierung (Geologie)"
                        || $tf_type == "Basisaufbau (Kampf)"
                        || $tf_type == "Basisaufbau (Artefakte)"
                        || $tf_type == "Basisaufbau (Ressourcen)"
                    ) {

                        $scan_data = array();
                        if ($fleetType == "own") {
                            $scan_data['user_from'] = $AccName;
                        } else {
                            $scan_data['user_from'] = $msg->strUserNameFrom;
                        }
                        $scan_data['planet_to']        = $msg->strPlanetNameTo;
                        $scan_data['coords_to_gal']    = $msg->aCoordsTo["coords_gal"];
                        $scan_data['coords_to_sys']    = $msg->aCoordsTo["coords_sol"];
                        $scan_data['coords_to_planet'] = $msg->aCoordsTo["coords_pla"];

                        $scan_data['planet_from']        = $msg->strPlanetNameFrom;
                        $scan_data['coords_from_gal']    = $msg->aCoordsFrom["coords_gal"];
                        $scan_data['coords_from_sys']    = $msg->aCoordsFrom["coords_sol"];
                        $scan_data['coords_from_planet'] = $msg->aCoordsFrom["coords_pla"];

                        $scan_data['art'] = $tf_type;

                        if (empty($msg->iAnkunft)) { //! keine Zeit erkannt, evtl. angekommener Anflug
                            continue;
                        }

                        // Zeitstempel
                        if ($tf_type == "Transport") { //! Ausladezeit: +5min
                            $scan_data['time'] = $msg->iAnkunft + 5 * MINUTE;
                        } else {
                            $scan_data['time'] = $msg->iAnkunft;
                        }

                        if (empty($scan_data['user_to'])) {
                            $scan_data['user_to'] = "";

                            $sql = "SELECT user FROM " . $db_tb_scans;
                            $sql .= " WHERE coords_gal=" . $scan_data['coords_to_gal'];
                            $sql .= " AND coords_sys=" . $scan_data['coords_to_sys'];
                            $sql .= " AND coords_planet=" . $scan_data['coords_to_planet'];

                            $result = $db->db_query($sql)
                                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
                            if ($row = $db->db_fetch_array($result)) {
                                $scan_data['user_to'] = $row['user'];
                            }

                        }
                        if (empty($scan_data['user_from'])) {
                            // Von
                            $sql = "SELECT user FROM " . $db_tb_scans;
                            $sql .= " WHERE coords_gal=" . $scan_data['coords_from_gal'];
                            $sql .= " AND coords_sys=" . $scan_data['coords_from_sys'];
                            $sql .= " AND coords_planet=" . $scan_data['coords_from_planet'];

                            $result = $db->db_query($sql)
                                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
                            if ($row = $db->db_fetch_array($result)) {
                                $scan_data['user_from'] = $row['user'];
                            }

                        }

                        if (!isset($scan_data['schiffe'])) {
                            $scan_data['schiffe'] = array();
                        }

                        //! Mac: gelieferte Ress/Schiffe eintragen
                        foreach ($msg->aObjects as $object) {
                            $typ   = $object["object"];
                            $menge = $object["count"];

                            if ($typ != 'Eisen' && $typ != 'Stahl' && $typ != 'VV4A' && $typ != 'chem. Elemente' && $typ != 'Eis' && $typ != 'Wasser' && $typ != 'Energie') {
                                $scan_data['schiffe'][$typ] = $menge;
                            } else {
                                $scan_data['pos'][$typ] = $menge;
                            }
                        }

                        // Daten speichern
                        save_data($scan_data);
                        $scan_datas[] = $scan_data;
                    } else {
                        //echo "<div style='color:red;'>unknown transfer_type detected: " .$tf_type."</div>";
                        continue;
                    }
                }
                //! ende index_fleet
            } else if ($aContainer->strIdentifier == "de_index_ressourcen") {
                //! Mac: @todo: Ressourcen auf dem aktuellen Planeten auswerten

                //automatische Creditsbestellung
                if (isset($db_tb_bestellung)) { //Bestellmodul vorhanden
                    debug_var('Bestelltabelle', 'vorhanden');

                    //Status der automatischen Credsbestellung aus der DB holen
                    $sth = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'automatic_creds_order';");
                    $row = $db->db_fetch_array($sth);

                    if (!empty($row) AND ($row['value'] === 'true')) { //Eintrag für automatische Bestellung vorhanden und aktiv
                        debug_var('automatisches Creditsbestellen', 'aktiv');

                        $sth = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'automatic_creds_order_minvalue';"); //Credits Minimalwert holen
                        $row = $db->db_fetch_array($sth);

                        $automatic_creds_order_minvalue = $row['value'];
                        debug_var('Mindestwert', $automatic_creds_order_minvalue);

                        $sth = $db->db_query("SELECT `value` FROM `{$db_tb_params}` WHERE `name` = 'automatic_creds_order_minpayout';"); //Wert der kleinsten Creditsauszahlungemenge holen
                        $row = $db->db_fetch_array($sth);

                        $automatic_creds_order_minpayout = $row['value'];
                        debug_var('Mindestauszahlungsmenge', $automatic_creds_order_minpayout);

                        if (($automatic_creds_order_minvalue > 0)) { //Bestelldaten ok

                            foreach ($aContainer->objResultData->aData as $ParsedRess) {
                                if ($ParsedRess->strResourceName == 'Credits') {
                                    if (((int)$ParsedRess->iResourceVorrat < $automatic_creds_order_minvalue)) { //weniger als MinimalCredits vorhanden

                                        //Menge der fehlenden Credits berechnen
                                        $insufficient_creds = $automatic_creds_order_minvalue - (int)$ParsedRess->iResourceVorrat;
                                        debug_var('Credits zu wenig', $insufficient_creds);

                                        //bestehende Creditsbestellungen holen
                                        $creds_order         = Array();
                                        $creds_ordered_value = 0;

                                        $sth = $db->db_query("SELECT `id`, `credits`, `offen_credits` FROM `{$db_tb_bestellung}` WHERE `user` = '" . $AccName . "' ORDER BY `time` ASC;");
                                        while ($row = $db->db_fetch_array($sth)) {
                                            $creds_order[$row['id']] = Array('credits' => $row['credits'], 'offen_credits' => $row['offen_credits']);
                                            $creds_ordered_value     = $creds_ordered_value + $row['offen_credits'];
                                        }
                                        debug_var('vorhandene Bestellungen', $creds_order);

                                        //Menge der noch zusätzlich zu bestellenden Credits berechnen
                                        $insufficient_creds = $insufficient_creds - $creds_ordered_value;
                                        debug_var('noch zu bestellenden Credits', $insufficient_creds);

                                        if ($insufficient_creds > 0) {
                                            $creds_order_value = (int)(($automatic_creds_order_minvalue * 1.3) - (int)$ParsedRess->iResourceVorrat); //gewisser Zielbereich vermeidet 'krumme' Bestellungen
                                            $creds_order_value = floor($creds_order_value / ($automatic_creds_order_minvalue / 5)) * ($automatic_creds_order_minvalue / 5);

                                            //minimale Auszahlungsmenge ist eingestellt und fehlende Menge ist kleiner als diese -> minimale Auszahlungsmenge
                                            if (!empty($automatic_creds_order_minpayout) AND ($creds_order_value < $automatic_creds_order_minpayout)) {
                                                $creds_order_value = $automatic_creds_order_minpayout;
                                            }

                                            debug_var('Anzahl gerundet', $creds_order_value);

                                            //keine vorliegenden Creditsbestellungen -> eine einfügen
                                            if (count($creds_order) === 0) {
                                                $SQLdata = array (
                                                    'user' => $AccName,
                                                    'team' => '(Alle)',
                                                    'text' => 'Automatische Creditsbestellung',
                                                    'time' => CURRENT_UNIX_TIME,
                                                    'credits' => $creds_order_value,
                                                    'offen_credits' => $creds_order_value,
                                                    'time_created' => CURRENT_UNIX_TIME
                                                );

                                                $db->db_insert($db_tb_bestellung, $SQLdata)
                                                    or error(GENERAL_ERROR, 'Could not insert credits order!', '', __FILE__, __LINE__);

                                                doc_message('weniger als ' . number_format((float)$automatic_creds_order_minvalue, 0, ',', '.') . ' Credits bei ' . $AccName . ' -> ' . number_format((float)$creds_order_value, 0, ',', '.') . ' Credits automatisch bestellt');

                                            } else {
                                                //vorliegende Creditsbestellungen zu gering -> aktuellste modifizieren
                                                if ($creds_ordered_value > 0) {
                                                    reset($creds_order);
                                                    $order_id = key($creds_order);

                                                    $data = array(
                                                        'credits'       => $creds_order_value,
                                                        'offen_credits' => $creds_order_value
                                                    );
                                                    $db->db_update($db_tb_bestellung, $data, "WHERE `id`=" . $order_id)
                                                        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

                                                    if (count($creds_order) > 1) {
                                                        //Creditsbestellungen aus anderen Bestellungen streichen

                                                        while (next($creds_order)) {
                                                            $order_id = key($creds_order);
                                                            $data     = array(
                                                                'credits'       => 0,
                                                                'offen_credits' => 0
                                                            );
                                                            $db->db_update($db_tb_bestellung, $data, "WHERE `id`=" . $order_id)
                                                                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);
                                                        }
                                                    }

                                                    doc_message('weniger als ' . number_format((float)$automatic_creds_order_minvalue, 0, ',', '.') . ' Credits bei ' . $AccName . ' -> Creditsbestellung auf ' . number_format((float)$creds_order_value, 0, ',', '.') . ' erhöht');

                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else if ($aContainer->strIdentifier == "de_index_research") {          //Forschung

                //aktuell laufende Forschung aktualisieren
                //ToDo: sobald IWacc Tabelle vorhanden Einträge dahin verschieben

                $research_id = find_research_id($aContainer->objResultData->aResearch[0]->strResearchName);

                $sql = "INSERT INTO `$db_tb_user_research` "
                    . "(`user`, `rId`, `date`, `time`) VALUES "
                    . "('{$AccName}', {$research_id}, {$aContainer->objResultData->aResearch[0]->iResearchEnd}, " . CURRENT_UNIX_TIME . ") "
                    . " ON DUPLICATE KEY UPDATE "
                    . "`rId` = {$research_id}, "
                    . "`date` = {$aContainer->objResultData->aResearch[0]->iResearchEnd}, "
                    . "`time` = " . CURRENT_UNIX_TIME . ";";

                $result = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not update research.', '', __FILE__, __LINE__, $sql);
                debug_var("Forschungsanpassung", "Forschung bei {$AccName} aktualisiert");

                //Zeitanpassung der nächsten Forschungssitteraufträge

                //nächsten Forschungsauftrag holen
                $sql = "SELECT `id`, `resid` FROM `{$db_tb_sitterauftrag}` WHERE `user` = '{$AccName}' AND `typ` = 'Forschung' ORDER BY `date` ASC LIMIT 1;";
                $result = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not get research information.', '', __FILE__, __LINE__, $sql);

                if ($row = $db->db_fetch_array($result)) {
                    if ($row['resid'] == $research_id) { //nächster Forschungssitterauftrag läuft bereits -> Sitterauftrag löschen
                        $sql = "DELETE FROM `{$db_tb_sitterauftrag}` WHERE `id`='{$row['id']}'";
                        $db->db_query($sql)
                            or error(GENERAL_ERROR, 'Could not delete sitterorder.', '', __FILE__, __LINE__, $sql);
                    }

                    //nochmal nächsten Forschungsauftrag holen
                    $sql = "SELECT `date` FROM `{$db_tb_sitterauftrag}` WHERE `user` = '{$AccName}' AND `typ` = 'Forschung' ORDER BY `date` ASC LIMIT 1;";
                    $result = $db->db_query($sql)
                        or error(GENERAL_ERROR, 'Could not get next sitterorder.', '', __FILE__, __LINE__, $sql);

                    if ($row = $db->db_fetch_array($result)) {

                        $last_runing_research = end($aContainer->objResultData->aResearch);
                        $res_order_time_diff = $row['date'] - $last_runing_research->iResearchEnd;

                        if ($res_order_time_diff > 0) { //folgenden Forschungsaufträge liegen zu weit in der Zukunft -> alle vorziehen

                            $sql = "UPDATE `{$db_tb_sitterauftrag}` SET `date` = `date`-".$res_order_time_diff.", `date_b1` = `date_b1`-".$res_order_time_diff.", `date_b2` = `date_b2`-".$res_order_time_diff." WHERE `user` = '{$AccName}' AND `typ` = 'Forschung';";
                            $db->db_query($sql)
                                or error(GENERAL_ERROR, 'Could not modify sitterorder.', '', __FILE__, __LINE__, $sql);

                            debug_var("Forschungsanpassung", "Zeiten der Forschungsaufträge bei {$AccName} angepasst");

                        }
                    }
                }

            } else if ($aContainer->strIdentifier == "de_index_geb") {

                if (!isset($aContainer->objResultData->aGeb)) {
                    continue;
                }

                foreach ($aContainer->objResultData->aGeb as $PlanieBuildingQueue) {
                    //! Mac: @todo: laufende Gebäude auswerten, ggf. aus Sitting entfernen
                }

            } else if ($aContainer->strIdentifier == "de_index_schiff") {         //Werften
                //new dBug($aContainer);
                foreach ($aContainer->objResultData->aSchiff as $plan) {
                    foreach ($plan as $ship_types) {
                        //! Mac: @todo: laufende Schiffe auswerten, ggf. aus Sitting entfernen oder Aufträge schieben
                    }
                }
            }
        } else { //! successfully parsed

            foreach ($aContainer->aErrors as $msg) {
                echo $msg . "<br>";
            }
        }
    } //! for each container

    echo "<div class='system_notification'>Startseite komplett geparsed für {$AccName}</div>\n";

}

function save_data($scan_data)
{
    global $db, $db_tb_lieferung, $db_tb_scans, $db_tb_incomings, $config_allytag;

    $fields = array(
        'time'               => $scan_data['time'],
        'coords_from_gal'    => $scan_data['coords_from_gal'],
        'coords_from_sys'    => $scan_data['coords_from_sys'],
        'coords_from_planet' => $scan_data['coords_from_planet'],
        'coords_to_gal'      => $scan_data['coords_to_gal'],
        'coords_to_sys'      => $scan_data['coords_to_sys'],
        'coords_to_planet'   => $scan_data['coords_to_planet'],
        'user_from'          => $scan_data['user_from'],
        'user_to'            => $scan_data['user_to'],
        'art'                => $scan_data['art'],
    );
    if (isset($scan_data['pos']['Eisen'])) {
        $fields += array('eisen' => $scan_data['pos']['Eisen']);
    }
    if (isset($scan_data['pos']['Stahl'])) {
        $fields += array('stahl' => $scan_data['pos']['Stahl']);
    }
    if (isset($scan_data['pos']['VV4A'])) {
        $fields += array('vv4a' => $scan_data['pos']['VV4A']);
    }
    if (isset($scan_data['pos']['chem. Elemente'])) {
        $fields += array('chem' => $scan_data['pos']['chem. Elemente']);
    }
    if (isset($scan_data['pos']['Eis'])) {
        $fields += array('eis' => $scan_data['pos']['Eis']);
    }
    if (isset($scan_data['pos']['Wasser'])) {
        $fields += array('wasser' => $scan_data['pos']['Wasser']);
    }
    if (isset($scan_data['pos']['Energie'])) {
        $fields += array('energie' => $scan_data['pos']['Energie']);
    }

    if (isset($scan_data['schiffe'])) {
        foreach ($scan_data['schiffe'] as $name => $anzahl) {
            if (isset($fields['schiffe'])) {
                $fields['schiffe'] .= "<br>" . $anzahl . " " . $name;
            } else {
                $fields['schiffe'] = $anzahl . " " . $name;
            }
        }
    }

    if (($scan_data['art'] === "Transport") OR ($scan_data['art'] === "Massdriverpaket")) {

        //bei Transporten oder Massdriverpaketen sollten Ressmengen mit dastehen, sonst werden die Transporte ignoriert
        if (!empty($scan_data['pos'])) {

            $db->db_insertignore($db_tb_lieferung, $fields)
                or error(GENERAL_ERROR, 'Could not insert transports.', '', __FILE__, __LINE__);

        }

    } else {

        $db->db_insertignore($db_tb_lieferung, $fields)
            or error(GENERAL_ERROR, 'Could not insert transports.', '', __FILE__, __LINE__);

    }

    if ($scan_data['art'] == "Angriff") {

        $sql = "UPDATE $db_tb_scans
			 SET angriff=" . $scan_data['time'] . "
			    ,angriffuser='" . $scan_data['user_from'] . "'
			 WHERE coords_gal=" . $scan_data['coords_to_gal'] . "
			   AND coords_sys=" . $scan_data['coords_to_sys'] . "
			   AND coords_planet=" . $scan_data['coords_to_planet'];
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    } elseif (($scan_data['art'] == "Sondierung (Schiffe/Def/Ress)") || ($scan_data['art'] == "Sondierung (Gebäude/Ress)")) {

        $sql = "UPDATE $db_tb_scans
			 SET sondierung=" . $scan_data['time'] . "
			    ,sondierunguser='" . $scan_data['user_from'] . "'
			 WHERE coords_gal=" . $scan_data['coords_to_gal'] . "
			   AND coords_sys=" . $scan_data['coords_to_sys'] . "
			   AND coords_planet=" . $scan_data['coords_to_planet'];
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    }

    if (!empty($db_tb_incomings)) { //incoming-Modul vorhanden

        debug_var('Incomingstabelle', 'vorhanden');
        debug_var('Input-Daten', $scan_data);

        if (($scan_data['art'] == "Angriff") || (($scan_data['art'] == "Sondierung (Schiffe/Def/Ress)") || ($scan_data['art'] == "Sondierung (Gebäude/Ress)"))) {
            $allianz_to = getAllianceByUser($scan_data['user_to']);
            debug_var('allianz_to', $allianz_to);
            debug_var('allianz_to_status', getAllyStatus($allianz_to));

            //nicht mehr fliegende Incs löschen
            $sql = "DELETE FROM `{$db_tb_incomings}` WHERE `listedtime`<>" . CURRENT_UNIX_TIME . " AND `name_to`='" . $scan_data['user_to'] . "' AND `arrivaltime`>".CURRENT_UNIX_TIME;
            $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            //Löschen der Einträge älter als 20 min in der Tabelle incomings, es sollen nur aktuelle Sondierungen und Angriffe eingetragen sein
            //ToDo : evtl Trennung Sondierung und Angriffe, damit die Sondierungen früher entfernt sind
            $sql = "DELETE FROM `{$db_tb_incomings}` WHERE `arrivaltime`<" . (CURRENT_UNIX_TIME - 20 * MINUTE);
            $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            //nur incomings auf die eigene oder verbündete Allianzen und maximal 20 min in der Vergangenheit eintragen
            if (
                (
                    (getAllyStatus($allianz_to) === 'own') OR
                    (getAllyStatus($allianz_to) === 'wing') OR
                    (getAllyStatus($allianz_to) === 'VB') OR
                    (getAllyStatus($allianz_to) === 'iVB')
                ) AND (
                    $scan_data['time'] > (CURRENT_UNIX_TIME - 20 * MINUTE)
                )
            ) {
                $SQLdata = array(
                    'koords_to'    => $scan_data['coords_to_gal'] . ":" . $scan_data['coords_to_sys'] . ":" . $scan_data['coords_to_planet'],
                    'name_to'      => $scan_data['user_to'],
                    'allianz_to'   => $allianz_to,
                    'koords_from'  => $scan_data['coords_from_gal'] . ":" . $scan_data['coords_from_sys'] . ":" . $scan_data['coords_from_planet'],
                    'name_from'    => $scan_data['user_from'],
                    'allianz_from' => getAllianceByUser($scan_data['user_from']),
                    'art'          => $scan_data['art'],
                    'arrivaltime'  => $scan_data['time'],
                    'listedtime'   => CURRENT_UNIX_TIME
                );
                debug_var('sql-Daten', $SQLdata);
                $db->db_insertignore($db_tb_incomings, $SQLdata)
                    or error(GENERAL_ERROR, 'Could not insert incoming.', '', __FILE__, __LINE__);
            }
        }
    }
}

function display_de_index()
{
    global $scan_datas;

    if (is_array($scan_datas)) {
        echo "<br>";
        start_table();
        start_row("titlebg", "colspan='4'");
        echo "<span style='font-weight:bold;'>Fliegende Lieferungen</span>";
        next_row("windowbg2", "");
        echo "Ziel";
        next_cell("windowbg2", "");
        echo "Start";
        next_cell("windowbg2", "");
        echo "Ankunft";
        next_cell("windowbg2", "");
        echo "Aktionen";

        foreach ($scan_datas as $scan_data) {
            next_row("windowbg1 top");
            echo $scan_data['coords_to_gal'] . ":" . $scan_data['coords_to_sys'] . ":" . $scan_data['coords_to_planet'];
            next_cell("windowbg1 top");
            echo $scan_data['coords_from_gal'] . ":" . $scan_data['coords_from_sys'] . ":" . $scan_data['coords_from_planet'];
            next_cell("windowbg1 top");
            echo strftime(CONFIG_DATETIMEFORMAT, $scan_data['time']);
            next_cell("windowbg1 top", "style='width:100%;'");
            echo $scan_data['art'] . "<br>";

            if (isset($scan_data['pos'])) {
                foreach ($scan_data['pos'] as $typ => $menge) {
                    echo $menge . " " . $typ . "<br>";
                }
            }

            if (isset($scan_data['schiffe'])) {
                foreach ($scan_data['schiffe'] as $typ => $menge) {
                    echo $menge . " " . $typ . "<br>";
                }
            }
        }

        end_table();
        echo "<br>";
    }
}