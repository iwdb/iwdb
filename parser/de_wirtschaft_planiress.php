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
/*                   http://www.iw-smf.pericolini.de                         */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/


if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_wirtschaft_planiress ( $return )
{
    global $config_date, $db_tb_ressuebersicht, $db, $selectedusername;
      
    $scan_data = array("eisen"=>0, "stahl"=>0, "vv4a"=>0, "chem"=>0, "eis"=>0, "wasser"=>0, "energie"=>0);

    if (!$return->objResultData->bLagerBunkerVisible)
        echo "<font color='orange'>Info:</font> keine LagerBunker Infos sichtbar! Bitte 'Lager und Bunker anzeigen' aktivieren<br />";

    foreach ($return->objResultData->aKolos as $kolo)
	{
        foreach ($kolo->aData as $resource)
        {
            $resource_name = $resource->strResourceName;
            $resource_name = trim (strtolower($resource_name));
            if (strpos($resource_name,"chem") !== FALSE)
                    $resource_name = "chem";
            
            $scan_data[$resource_name] += $resource->fResourceProduction;
        }
	}
  
    $sql = "SELECT user FROM " . $db_tb_ressuebersicht . 
            " WHERE user = '" . $selectedusername . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 
                'Could not query config information.', '', 
                __FILE__, __LINE__, $sql);

    $row = $db->db_fetch_array($result);

    if(!empty($row)) {
    $sql = "UPDATE " . $db_tb_ressuebersicht . 
            " SET datum=" . $config_date . 
            ", eisen='" . ($scan_data['eisen']) .
            "', stahl='" . ($scan_data['stahl']) .
            "', vv4a='" . ($scan_data['vv4a']) .
            "', chem='" . ($scan_data['chem']).
            "', eis='" . ($scan_data['eis']).
            "', wasser='". ($scan_data['wasser']) .
            "', energie='". ($scan_data['energie']).
            "' WHERE user='". $selectedusername . "'";
    } else {
    $sql = "INSERT INTO " . $db_tb_ressuebersicht .  
            " (user, datum, eisen, stahl, vv4a, chem, eis, wasser, energie)" .
            " VALUES('" . $selectedusername . "', " . $config_date . 
            ",'" . ($scan_data['eisen']) .
            "', '" . ($scan_data['stahl']) .
            "', '".($scan_data['vv4a']) .
            "', '".($scan_data['chem']) .
            "', '".($scan_data['eis']) .
            "', '".($scan_data['wasser']) .
            "', '".($scan_data['energie']) . "')";
    }

    $db->db_query($sql)
        or error(GENERAL_ERROR, 
                'Could not query config information.', '', 
                __FILE__, __LINE__, $sql);

    echo "<div class='system_notification'>Produktion Teil 1 aktualisiert/hinzugef&uuml;gt.</div>";
    echo "<b>Produktion Teil 2 fehlt noch, wird -- falls vorhanden! -- nun geparsed.</b>";

}

?>