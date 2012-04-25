<?php
/*****************************************************************************/
/* de_wirtschaft_geb.php                                                     */
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

if (!defined('DEBUG_LEVEL'))
	define('DEBUG_LEVEL', 0);

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_wirtschaft_geb ( $return )
{
    global $db, $db_tb_gebaeude_spieler, $selectedusername;
	$count = 0;
        
	foreach ($return->objResultData->aAreas as $area)
	{		
		foreach ($area->aBuildings as $building)
		{
            $sql = "REPLACE INTO $db_tb_gebaeude_spieler (";
            $sql .= "coords_gal,coords_sys,coords_planet,kolo_typ,user,category,building,count,time";
			$sql .= ") VALUES ";
                
			foreach ($building->aCounts as $coords => $count)
			{
                $aCoords = explode(":",$coords);
				$sql .= "(";
                $sql .= $aCoords[0];
				$sql .= "," . $aCoords[1];
				$sql .= "," . $aCoords[2];
				$sql .= ",'" . $return->objResultData->aKolos[$coords]->strObjectType . "'";
				$sql .= ",'" . $selectedusername . "'";
				$sql .= ",'" . $area->strAreaName . "'";
				$sql .= ",'" . $building->strBuildingName . "'";
				$sql .= "," . $count;
				$sql .= "," . time() . "),
                    ";
            }
                $sql = preg_replace('@\,\s+\z@',';',$sql);
                debug_var('sql', $sql);
				$db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				$count++;
		}
	}

    if ($count)
		echo "<div class='system_notification'>Geb&auml;ude&uuml;bersicht aktualisiert.</div>";

    return;
}

// ****************************************************************************
// Gibt den Wert einer Variablen aus.
if (!defined('DEBUG_VAR')) {
	define('DEBUG_VAR', true);
	function debug_var($name, $wert, $level = 2) {
		if (DEBUG_LEVEL >= $level) {
			echo "<div class='system_debug_blue'>" . $name . ":'";
			if (is_array($wert))
				print_r($wert);
			else
				echo $wert;
			echo "'</div>";
		}
	}
}
?>