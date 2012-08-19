<?php
/*****************************************************************************/
/* de_highscore.php                                                          */
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
/* Datum: Jun 2012 - August 2012                                             */
/*                                                                           */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_highscore ( $result )
{
	$count = 0;
	$scan_data = array("time" => $result->objResultData->iTimestamp);
    
    $bDateOfEntryVisible = $result->objResultData->bDateOfEntryVisible;

	foreach ($result->objResultData->aMembers as $object_user)
	{
		if ($bDateOfEntryVisible)
			$scan_data['dabei_seit'] = $object_user->iDabeiSeit;

        $scan_data['pos'] = $object_user->iPos;
		$scan_data['name'] = $object_user->strName;
		$scan_data['allianz'] = $object_user->strAllianz;

		$scan_data['gebp'] = $object_user->iGebP;
		$scan_data['fp'] = $object_user->iFP;
		$scan_data['gesamtp'] = $object_user->iGesamtP;
		$scan_data['ptag'] = $object_user->iPperDay;
        $scan_data['diff'] = $object_user->iPosChange;

		save_data($scan_data);
        $count++;
	}
	
    echo "<div class='system_notification'>" . $count . " Highscore(s) hinzugefügt.</div>";
    
	return true;
}

function save_data($scan_data) {
	global $db, $db_tb_highscore, $db_tb_scans;

	$scan_data["gebp_nodiff"] = $scan_data["time"];
	$scan_data["fp_nodiff"] = $scan_data["time"];
	$sql = "SELECT * FROM " . $db_tb_highscore . " WHERE name='" . $scan_data['name'] . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	if ($row = $db->db_fetch_array($result)) {
		if ($row["gebp"] == $scan_data["gebp"])
			$scan_data["gebp_nodiff"] = $row["gebp_nodiff"];
		if ($row["fp"] == $scan_data["fp"])
			$scan_data["fp_nodiff"] = $row["fp_nodiff"];
	}
	$sql = "INSERT INTO " . $db_tb_highscore;
	$sql .= " (pos,name,allianz,gebp,fp,gesamtp,ptag,diff,dabei_seit,gebp_nodiff,fp_nodiff,time) VALUES (";
	$sql .= $scan_data["pos"];
	$sql .= ",'".$scan_data["name"]."'";
	$sql .= ",'".$scan_data["allianz"]."'";
	$sql .= ",".$scan_data["gebp"];
	$sql .= ",".$scan_data["fp"];
	$sql .= ",".$scan_data["gesamtp"];
	$sql .= ",".$scan_data["ptag"];
	$sql .= ",".$scan_data["diff"];
	$sql .= ",".$scan_data["dabei_seit"];
	$sql .= ",".$scan_data["gebp_nodiff"];
	$sql .= ",".$scan_data["fp_nodiff"];
	$sql .= ",".$scan_data["time"];
	$sql .= ") ON DUPLICATE KEY UPDATE ";
	$sql .= "pos=".$scan_data["pos"];
	$sql .= ",allianz='".$scan_data["allianz"]."'";
	$sql .= ",gebp=".$scan_data["gebp"];
	$sql .= ",fp=".$scan_data["fp"];
	$sql .= ",gesamtp=".$scan_data["gesamtp"];
	$sql .= ",ptag=".$scan_data["ptag"];
	$sql .= ",diff=".$scan_data["diff"];
	$sql .= ",dabei_seit=".$scan_data["dabei_seit"];
	$sql .= ",gebp_nodiff=".$scan_data["gebp_nodiff"];
	$sql .= ",fp_nodiff=".$scan_data["fp_nodiff"];
	$sql .= ",time=".$scan_data["time"];
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	$sql = "UPDATE " . $db_tb_scans . " SET punkte=".$scan_data["gesamtp"]." WHERE user='".$scan_data["name"]."'";
	
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	}
?>