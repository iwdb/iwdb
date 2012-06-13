<?php
/*****************************************************************************/
/* de_msg.php                                                     */
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
/* Datum: Juni 2012                                                          */
/*                                                                           */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                   http://www.handels-gilde.org                            */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/

error_reporting(E_ALL);

function parse_de_msg ( $return )
{
    global $db, $db_tb_raid, $config_date, $db_tb_transferliste, $db_tb_user;
    
	$transp_skipped=0;
	$transp_failed=0;
	$transp_succ=0;
    $gave_orbit_ignored=0;
    
    //! Rueckkehr enthaelt keine relevanten Informationen
//	foreach ($return->objResultData->aReverseMsgs as $msg)
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
	foreach (array_merge($return->objResultData->aTransportMsgs,$return->objResultData->aGaveMsgs,$return->objResultData->aMassdriverMsgs) as $msg)
	{	
		if (!$msg->bSuccessfullyParsed) {
			echo "..... failed Transport/Übergabe/Massdriver Msg!<br />";
			if (!empty($msg->aErrors))
				echo implode("<br />",$msg->aErrors) . "<br />";
			++$transp_failed;
			continue;
		}
        $fromOrbit = ($msg->eParserType == 'Übergabe' && $msg->bOutOfOrbit) ? true : false;
        $buddler = $msg->strFromUserName;
		$fleeter = $msg->strToUserName;
//		$transfair_to_coords = $msg->strCoords;		
		$transfair_date = $msg->iMsgDateTime;

        if ($fromOrbit) {
            ++$gave_orbit_ignored;
            continue;
        }
        
        if(empty($transfair_date) || empty($buddler) || empty($fleeter)) {
            doc_message("Fehler im Bericht - (" . $transfair_date . ", " . $buddler .", " . $fleeter . ")");
            ++$transp_failed;
            continue;
        }
    
        //! Mac: Workaround, da libIwParser nicht die gleichen Ressnamen verwendet wie IWDB1. Eigentlich besser mit IDs handhaben
        foreach ($msg->aResources as $k => $resource)
        {
            $name = strtolower($resource['resource_name']);     //! reicht fuer Eisen, Stahl, VV4A, Eis, Wasser, Energie

            if (strpos($name,"chem") !== FALSE) $name = "chem";
            else if (strpos($name,"bev") !== FALSE) $name = "volk";
            else if ($name == "Eisen") $resource['resource_name'] = "stahl";
            $resource['resource_name'] = $name;
            
            $msg->aResources[$k] = $resource;
        }		
        
        // Lieferungen an sich selbst ignorieren
        // Manuell: DELETE FROM `prefix_transferliste` WHERE `buddler`=`fleeter`
        if(!empty($transfair_date) && $buddler == $fleeter) {
            doc_message("Bericht ".$transfair_date." vom ".strftime("%d.%m.%Y %H:%M:%S", $transfair_date)." ignoriert! - Absender und Empfänger sind identisch...");
            ++$transp_skipped;
            continue;
        }

        $sql = "SELECT COUNT(*) AS anzahl FROM " . $db_tb_transferliste . 
            " WHERE zeitmarke=" . $transfair_date . " AND buddler='" . $buddler . 
                    "' AND fleeter='" . $fleeter . "'"; 
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                'Could not query config information.', '', 
                __FILE__, __LINE__, $sql);

        $row = $db->db_fetch_array($result);	
        // Not found, so insert new
        if(empty($row) || $row['anzahl'] == 0) {
            $sql = "INSERT INTO " . $db_tb_transferliste . "(zeitmarke, buddler, fleeter, ";
            $val = "$transfair_date, '$buddler','$fleeter',";
			foreach ($msg->aResources as $resource)
            {
                $sql .=  $resource['resource_name'] . ", ";
                $val .= "'" . $resource['resource_count'] . "', ";	
            }		
                        
            $sql = substr($sql, 0, (strlen($sql) - 2));
            $val = substr($val, 0, (strlen($val) - 2));
            $sql .= ") VALUES(" . $val . ")";		
        } else {
            $sql = "UPDATE " . $db_tb_transferliste . " SET zeitmarke=$transfair_date, buddler='$buddler', fleeter='$fleeter', ";
            foreach ($msg->aResources as $resource)
            {
                $sql .= $resource['resource_name'] . "='" . $resource['resource_count'] . "', ";
            }		
            $sql = substr($sql, 0, (strlen($sql) - 2)) .
                " WHERE zeitmarke=" . $transfair_date . 
                " AND buddler='" . $buddler . "' AND fleeter='" . $fleeter . "'"; 
        }
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                'Could not query config information.', '', 
                __FILE__, __LINE__, $sql);
            
        // Aktualisierungszeit für Transportberichte setzen
        $sql = "UPDATE " . $db_tb_user . " SET lasttransport='" . $config_date . 
            "' WHERE sitterlogin='" . $buddler . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                'Could not query config information.', '',
                __FILE__, __LINE__, $sql);
    
        ++$transp_succ;
    
        //! @todo: Mac: bisher in der IWDB1 noch gar nicht beruecksichtigt ?
//		foreach ($msg->aSchiffe as $schiffe)
//		{
//			$schiffe_count = $schiffe['schiffe_count'];
//			$schiffe_name = $schiffe['schiffe_name'];
//		}
	}
	
	if ($transp_succ > 0) 
        echo $transp_succ ." Transport/Übergabe/Massdriver Msg <font color='green'> erfolgreich geparsed</font><br />";
	if ($transp_skipped > 0) 
        echo $transp_skipped ." Transport/Übergabe/Massdriver Msg <font color='black'> ignoriert</font><br />";
	if ($transp_failed > 0) 
        echo $transp_failed ." Transport/Übergabe/Massdriver Msg <font color='red'> fehlgeschlagen</font><br />";
    if ($gave_orbit_ignored > 0) 
        echo $gave_orbit_ignored ." Übergaben aus dem Orbit <font color='black'> ignoriert</font><br />";

	foreach ($return->objResultData->aScanGeoMsgs as $msg)
	{	
		//! @todo:
		if (!$msg->bSuccessfullyParsed) {
			echo ".....  failed Geo Scan<br />";
			echo implode("<br>",$msg->aErrors);
			continue;
		}
		/** $scanLogs[] = "..... GeoScan allready parsed with paParser"; **/
		continue;

		//dummy
	}

	foreach ($return->objResultData->aTransfairMsgs as $msg)
	{	
		if (!$msg->bSuccessfullyParsed) {
			echo "..... failed TransfairMsg!<br />";
			echo implode("<br />",$msg->aErrors);
			continue;
		}
		echo "..... Transfair not yet implemented<br />";
		//dummy
	}

//! ..... Stationieren not yet implemented

	foreach ($return->objResultData->aMsgs as $msg)
	{	
		if (!$msg->bSuccessfullyParsed) {
			echo "..... failed UserMsg!<br />";
			echo implode("<br />",$msg->aErrors);
			continue;
		}
// 		$scanLogs[] = "..... UserMsg not yet implemented";
		if ($msg->eParserType == "Angriff")
			/** $scanLogs[] = "..... Angriff <font color='red'> wurde per Link geparsed</font>"; **/
			continue;
		else if ($msg->eParserType == "Basisaufbau")
			continue;
		else {
			echo "..... " . $msg->eParserType . "<font color='red'> not yet implemented</font><br />";
        }
		//dummy
	}

}

function display_de_msg() {
  include "./modules/m_transferliste.php";
}

?>