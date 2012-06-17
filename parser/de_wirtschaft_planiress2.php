<?php
/*****************************************************************************/
/* de_wirtschaft_planiress2.php                                              */
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
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/


if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_wirtschaft_planiress2 ( $return )
{    
    $scan_data_total = array();
    $scan_data_total['total_fp'] = $return->objResultData->iFPProduction;

    $scan_data_total['total_credits_w'] = $return->objResultData->fCreditProduction;
    $scan_data_total['total_alli'] = $return->objResultData->fCreditAlliance;
    
    $scan_data_total['total_bev_a'] = $return->objResultData->iPeopleWithoutWork;
    $scan_data_total['total_bev_g'] = $return->objResultData->iPeopleWithWork;
    $scan_data_total['total_bev_q'] = $scan_data_total['total_bev_a'] * 100 / $scan_data_total['total_bev_g'];

    insert_data_total_2($scan_data_total);
                
    foreach ($return->objResultData->aKolos as $kolo)
    {
        $scan_data = array();
        $scan_data['coords_gal'] = $kolo->aCoords["coords_gal"];
        $scan_data['coords_sys'] = $kolo->aCoords["coords_sol"];
        $scan_data['coords_planet'] = $kolo->aCoords["coords_pla"];
        
        $scan_data['fp'] = $kolo->fFPProduction;
        $scan_data['fp_b'] = $kolo->fFPProductionWithoutMods;
        $scan_data['fp_m1'] = $kolo->fResearchModGlobal;
        $scan_data['fp_m2'] = $kolo->fResearchModPlanet;
        $scan_data['credits'] = $kolo->fCreditProduction;
        
        $scan_data['bev_a'] = $kolo->iPeopleWithoutWork;
        $scan_data['bev_g'] = $kolo->iPeopleWithWork;
        $scan_data['bev_q'] = $scan_data['bev_a'] * 100 / ($scan_data['bev_g'] > 0 ? $scan_data['bev_g'] : 1);
        $scan_data['bev_w'] = $kolo->iSexRate;
        
        $scan_data['zufr'] = $kolo->fZufr;
        $scan_data['zufr_w'] =$kolo->fZufrGrowing;
                        
        insert_data_2($scan_data);                
    }    
    
    echo "<div class='system_notification'>Lager&uuml;bersicht aktualisiert.</div>";
}

function insert_data_2($scan_data) {
	global $db, $db_tb_lager, $config_date;
	$sql = "UPDATE " . $db_tb_lager;
	$sql .= " SET fp=" . $scan_data['fp'];
	$sql .= ",fp_b=" . $scan_data['fp_b'];
	$sql .= ",fp_m1=" . $scan_data['fp_m1'];
	$sql .= ",fp_m2=" . $scan_data['fp_m2'];
	$sql .= ",credits=" . $scan_data['credits'];
	$sql .= ",bev_a=" . $scan_data['bev_a'];
	$sql .= ",bev_g=" . $scan_data['bev_g'];
	if (!empty($scan_data['bev_q']))
		$sql .= ",bev_q=" . $scan_data['bev_q'];
	$sql .= ",bev_w=" . $scan_data['bev_w'];
	if (!empty($scan_data['zufr']))
		$sql .= ",zufr=" . $scan_data['zufr'];
	if (!empty($scan_data['zufr_w']))
		$sql .= ",zufr_w=" . $scan_data['zufr_w'];
	$sql .= ",time=" . $config_date;
	$sql .= " WHERE coords_gal=" . $scan_data['coords_gal'];
	$sql .= " AND coords_sys=" . $scan_data['coords_sys'];
	$sql .= " AND coords_planet=" . $scan_data['coords_planet'];
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

function insert_data_total_2($scan_data) {
	global $db, $db_tb_ressuebersicht, $selectedusername, $config_date;

	if (empty($db_tb_ressuebersicht))
		return;
	
	$sql = "UPDATE " . $db_tb_ressuebersicht;
	$sql .= " SET fp_ph=" . $scan_data['total_fp'];
#	$sql .= ",credits=" . $scan_data['total_credits_w'];
		$total_credits=$scan_data['total_alli']+$scan_data['total_credits_w'];
	$sql .= ",credits=" . $total_credits;
	$sql .= ",bev_a=" . $scan_data['total_bev_a'];
	$sql .= ",bev_g=" . $scan_data['total_bev_g'];
	$sql .= ",bev_q=" . $scan_data['total_bev_q'];
	$sql .= ",datum=" . $config_date;
	$sql .= " WHERE user='" . $selectedusername . "'";
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);		
}

?>