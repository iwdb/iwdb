<?php
/*****************************************************************************/
/* de_alli_kasse_log_member.php                                              */
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
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/


if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
    die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_alli_kasse_log_member($return) 
{
    global $db, $db_tb_scans, $db_tb_kasse_outgoing, $config_date, $user_sitterlogin, $db_tb_user , $user_id;

    $seluser = getVar('seluser') ?  getVar('seluser') : $user_sitterlogin;

    $allianz = "";
    // ally vom user herausfinden
    //$sql = "SELECT DISTINCT allianz FROM $db_tb_scans WHERE user like '" . $seluser . "'";
    $sql = "SELECT allianz FROM " . $db_tb_user . " WHERE id = '" . $user_id . "'";
	$result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while($row = $db->db_fetch_array($result)) 
    {
        $allianz = $row['allianz'];
    }
    if (!empty($return->objResultData->strAlliance)) {
        $allianz = $return->objResultData->strAlliance;
    }
    if (empty($allianz)) {
        echo "zugehörige Allianz konnte nicht ermittelt werden<br />";
        return;
    }

//      //27.05.2007 08:55 von ZAHLENDER an EMPFAENGER 10.000 Credits ausgezahlt
//		//07.07.2010 20:28 von Labasu an Labasu 20.000 Credits ausgezahlt Grund war kisbau.
		
    foreach ($return->objResultData->aLogs as $log) {
        $strTime = strftime('%Y-%m-%d %H:%M:00',$log->iDateTime);
        $sql = "REPLACE INTO $db_tb_kasse_outgoing (payedfrom, payedto, amount, time_of_pay, allianz) 
                VALUES ('$log->strFromUser', '$log->strToUser', '$log->iCredits', '$strTime', '$allianz')";
        $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        (!isset($out) || empty($out)) ? $out = "<p><u>Auszahlungen der letzten drei Wochen:</u><p>" : $out = ' ';
        echo $out;

        echo "{" . date("d.m.Y H:i", $log->iDateTime)
                . "} Allizahlung [$allianz] von " . $log->strFromUser . " an " . $log->strToUser
                . " mit " . $log->iCredits . " Credits erkannt." . ( (!empty($log->strReason)) ? (" (" . $log->strReason . ")") : "") ."<br />\n";            
    }
}

?>