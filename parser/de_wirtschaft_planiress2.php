<?php
/*****************************************************************************
 * de_wirtschaft_planiress2.php                                              *
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

function parse_de_wirtschaft_planiress2($return)
{
    if ($return->bSuccessfullyParsed) {
        global $selectedusername, $db, $db_tb_ressuebersicht, $db_tb_lager, $db_tb_bestellung, $db_tb_sitterauftrag, $db_tb_scans;

        $AccName = getAccNameFromKolos($return->objResultData->aKolos);
        if ($AccName === false) { //kein Eintrag gefunden -> ausgewählten Accname verwenden
            $AccName = $selectedusername;
        }
        debug_var('wirtschaft_planiress2', $AccName);

        if (!empty($db_tb_ressuebersicht)) {

            $scan_data_total            = array();
            $scan_data_total['user']    = $AccName;
            $scan_data_total['fp_ph']   = $return->objResultData->iFPProduction;
            $scan_data_total['credits'] = ($return->objResultData->fCreditAlliance + $return->objResultData->fCreditProduction);
            $scan_data_total['bev_a']   = $return->objResultData->iPeopleWithoutWork;
            $scan_data_total['bev_g']   = $return->objResultData->iPeopleWithWork;
            $scan_data_total['bev_q']   = 0;
            if (!empty($scan_data_total['bev_g'])) {
                $scan_data_total['bev_q'] = $scan_data_total['bev_a'] * 100 / $scan_data_total['bev_g'];
            }
            $scan_data_total['datum'] = CURRENT_UNIX_TIME;
            debug_var('wirtschaft_planiress2', $scan_data_total);

            $db->db_insertupdate($db_tb_ressuebersicht, $scan_data_total)
                or error(GENERAL_ERROR, 'Could not update total ress information.', '', __FILE__, __LINE__);
        }

        foreach ($return->objResultData->aKolos as $kolo) {
            $scan_data                  = array();
            $scan_data['user']          = $AccName;
            $scan_data['coords_gal']    = $kolo->aCoords['coords_gal'];
            $scan_data['coords_sys']    = $kolo->aCoords['coords_sol'];
            $scan_data['coords_planet'] = $kolo->aCoords['coords_pla'];

            $scan_data['fp']      = $kolo->fFPProduction;
            $scan_data['fp_b']    = $kolo->fFPProductionWithoutMods;
            $scan_data['fp_m1']   = $kolo->fResearchModGlobal;
            $scan_data['fp_m2']   = $kolo->fResearchModPlanet;
            $scan_data['credits'] = $kolo->fCreditProduction;

            $scan_data['bev_a'] = $kolo->iPeopleWithoutWork;
            $scan_data['bev_g'] = $kolo->iPeopleWithWork;
            $scan_data['bev_q'] = 0;
            if (!empty($scan_data['bev_g'])) {
                $scan_data['bev_q'] = $scan_data['bev_a'] * 100 / $scan_data['bev_g'];
            }
            $scan_data['bev_w'] = $kolo->iSexRate;

            $scan_data['zufr']   = $kolo->fZufr;
            $scan_data['zufr_w'] = $kolo->fZufrGrowing;
            $scan_data['time']   = CURRENT_UNIX_TIME;
            debug_var('wirtschaft_planiress2', $scan_data);

            $db->db_insertupdate($db_tb_lager, $scan_data)
                or error(GENERAL_ERROR, 'Could not update ress information.', '', __FILE__, __LINE__);
			
			if ($scan_data['zufr']<'20,0') {
				$SQLdata = array (
                    'user' => $AccName,
					'planet' => ($scan_data['coords_gal'] . ":" . $scan_data['coords_sys'] . ":" . $scan_data['coords_planet']),
                    'auftrag' => 'automatischer Auftrag : bitte Zufriedenheit kontrollieren',
                    'date' => CURRENT_UNIX_TIME,
					'date_b1' => CURRENT_UNIX_TIME,
					'date_b2' => CURRENT_UNIX_TIME,
					'typ' => 'Sonstiges'
                );

                $db->db_insert($db_tb_sitterauftrag, $SQLdata)
                    or error(GENERAL_ERROR, 'Could not insert sitterauftrag!', '', __FILE__, __LINE__);
			}
			
			$bev_null=0;
			
			$sql_del = "DELETE FROM `{$db_tb_bestellung}` WHERE (`volk`!='0' AND (`coords_gal`='".$scan_data['coords_gal']."' AND `coords_sys`='".$scan_data['coords_sys']."' AND `coords_planet`='".$scan_data['coords_planet']."'))";
			$result_del = $db->db_query($sql_del)
				or error(GENERAL_ERROR, 'Could not delete bev bestellung.', '', __FILE__, __LINE__, $sql);
			
			$plani =$scan_data['coords_gal'] . ":" . $scan_data['coords_sys'] . ":" . $scan_data['coords_planet'];
			$sql = $db->db_query("SELECT `bed_bev` FROM `{$db_tb_scans}` WHERE `coords` = '" .$plani. "';");
			$row = $db->db_fetch_array($sql);
			$bev = $row['bed_bev'];
			if ($scan_data['bev_a']<$row['bed_bev']) {
				$SQLdata = array (
                    'user' => $AccName,
                    'coords_gal' => $scan_data['coords_gal'],
					'coords_sys' => $scan_data['coords_sys'],
					'coords_planet' => $scan_data['coords_planet'],
					'team' => '(Alle)',
                    'text' => 'Automatische Bestellung Bevölkerung',
                    'time' => CURRENT_UNIX_TIME,
                    'volk' => ($bev-$scan_data['bev_a']),
                    'offen_volk' => ($bev-$scan_data['bev_a']),
                    'time_created' => CURRENT_UNIX_TIME
                );
				
                $db->db_insert($db_tb_bestellung, $SQLdata)
                    or error(GENERAL_ERROR, 'Could not insert bev order!', '', __FILE__, __LINE__);
					
				$bev_null=1;
			}
			
			if ($bev_null=0) {
				if ($scan_data['bev_a']<'0') {
					$SQLdata = array (
						'user' => $AccName,
						'coords_gal' => $scan_data['coords_gal'],
						'coords_sys' => $scan_data['coords_sys'],
						'coords_planet' => $scan_data['coords_planet'],
						'team' => '(Alle)',
						'text' => 'Automatische Bestellung Bevölkerung',
						'time' => CURRENT_UNIX_TIME,
						'volk' => (abs($scan_data['bev_a'])+500),
						'offen_volk' => (abs($scan_data['bev_a'])+500),
						'time_created' => CURRENT_UNIX_TIME
					);
					
					$db->db_insert($db_tb_bestellung, $SQLdata)
						or error(GENERAL_ERROR, 'Could not insert bev order!', '', __FILE__, __LINE__);
				}
			}
        }

        echo "<div class='system_notification'>Lagerübersicht bei {$AccName} aktualisiert.</div>";
    }
}