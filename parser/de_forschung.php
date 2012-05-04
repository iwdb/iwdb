<?php
/*****************************************************************************/
/* de_forschung.php                                                          */
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
/* Datum: Jun 2009 - April 2012                                              */
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

error_reporting(E_ALL);

function parse_de_forschung ( $return )
{
    global $db, $db_tb_research, $db_tb_user_research, $selectedusername;
	
    //! Mac: wurde nur gebraucht, um beim Parsen zu pruefen, ob das gefunden wirklich eine Forschung ist
    //  durch libiwparser und dynamische Datengenerierung unnoetig
    //  
//	$forschungen = array();
//
//    $sql = "SELECT name FROM " . $db_tb_research . 
//			" ORDER BY ID ASC";
//	$result = $db->db_query($sql)
//		or error(GENERAL_ERROR, 
//             'Could not query config information.', '', 
//             __FILE__, __LINE__, $sql);
//	while($row = $db->db_fetch_array($result)) {
//		$forschungen[] = $row['name'];
//	}
	
	$akt_fp = array();
	$akt_forschung = 0;
	$akt_date = 0;

    //! Mac: @todo: hier koenten mit Genetik auch zwei Forschungen laufen ...
    foreach ($return->objResultData->aResearchsProgress as $research)
	{
        $sql = "SELECT id FROM " . $db_tb_research . 
                " WHERE name='" . $research->strResearchName . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                    'Could not query config information.', '', 
                    __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result);
        $akt_forschung = $row['id'];
        $akt_date = $research->iUserResearchTime;  
    }
    
	foreach ($return->objResultData->aResearchsResearched as $research)
        $akt_fp[$research->strResearchName] = $research->iFP * ($research->iResearchCosts/100.);

	foreach ($return->objResultData->aResearchsOpen as $research)
        $akt_fp[$research->strResearchName] = $research->iFP * ($research->iResearchCosts/100.);

    $time = time();

	foreach($akt_fp as $key => $value) {
		$sql = "UPDATE " . $db_tb_research . " SET " .
			"FPakt=" . $value . ", " .
			"time=" . $time .
			" WHERE name='" . $key . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
	}

	$sql = "DELETE FROM " . $db_tb_user_research . 
			" WHERE user='" . $selectedusername . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
			 'Could not query config information.', '', 
			 __FILE__, __LINE__, $sql);

	$sql = "INSERT INTO " . $db_tb_user_research . 
			" SET user='" . $selectedusername . "', rid='" . $akt_forschung . "', date=" . $akt_date;
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
			 'Could not query config information.', '', 
			 __FILE__, __LINE__, $sql);
}


?>