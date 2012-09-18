<?php
/*****************************************************************************/
/* de_alli_kasse_member.php                                                  */
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

function parse_de_alli_kasse_member ( $return )
{
	global $db, $db_tb_scans, $config_date, $user_sitterlogin, $db_tb_user , $user_id;

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
        echo "zugeh&ouml;rige Allianz konnte nicht ermittelt werden<br />";
        return;
    }
    
    /**
      * Mac:
      * neue Version ab Runde 11 (gezahlte Steuern bleiben erhalten, und auch sichtbar wenn keine Steuern angenommen)
      * Allerdings erfolgt ein Reset bei Allianzwechsel
      * -> Eintrag also pro Spieler und Allianz (und Beitrittsdatum) noetig
      */
	$members = $return->objResultData->aMember;
    echo "<p><u>Bisherige Einzahlungen:</u></p>";
	foreach ($members as $member)
	{
        	//Array ( [0] => EINZAHLER 14.04.2007 15:07 117.256,53 1.712 pro Tag [1] => EINZAHLER [2] => 117256.53 )
		updateIncoming($member->strUser, $member->fCreditsPaid, $allianz);
                
        	echo $member->strUser . "&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;" . $member->fCreditsPaid . "<br>\n";
	}
}


function updateIncoming($user, $amount, $ally) {
    global $db, $db_tb_scans, $db_tb_kasse_incoming;
    $sum_old=0.0;
    $sql = "SELECT sum(amount) FROM $db_tb_kasse_incoming WHERE user like '" . $user . "' AND allianz like '" . $ally . "' AND time_of_insert != CURRENT_DATE()";
    $result = $db->db_query($sql)
      or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while($row = $db->db_fetch_array($result)) 
    {
      $sum_old = $row['sum(amount)'];
    }

    $amount = $amount - $sum_old;

    $sql = "REPLACE INTO $db_tb_kasse_incoming (user, amount, time_of_insert, allianz) 
             VALUES ('$user', $amount, CURRENT_DATE(), '$ally')";
    $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

?>
