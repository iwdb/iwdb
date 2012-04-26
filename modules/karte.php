<?php
/*****************************************************************************/
/* karte.php                                                                 */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* ========================================================================= */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* ========================================================================= */
/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */
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

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (!defined('IRA'))
die('Hacking attempt...');

$galaxy = getVar('galaxy');
if ( empty($galaxy) ) {
	$galaxy = $config_map_default_galaxy;
}

$sqlALI = "SELECT allianz FROM ".$db_tb_allianzstatus . " WHERE status='own' OR status='wing'";
$resultALI = $db->db_query($sqlALI)
or error(GENERAL_ERROR,
                 'Could not query config information.', '',
__FILE__, __LINE__, $sqlALI);
$i = 0;
$arrrayofAlis = array();
while( $rowALI = $db->db_fetch_array($resultALI)) {
	$i++;
	$arrrayofAlis[$i] = $rowALI['allianz'];
}

if( defined('ALLY_MEMBERS_ON_MAP' ) && ALLY_MEMBERS_ON_MAP === TRUE ) {
	$showmembers = getVar('shownames');
    // Member-Tooltip = TRUE als Default, solange nichts anderes übermittelt wurde
    if (isset($_POST) && empty($_POST)) {
        $showmembers = TRUE;
    } else {
        $showmembers = !empty($showmembers);
    }

	$allymember = array();
	$sql = "SELECT DISTINCT allianz,coords_sys FROM " . $db_tb_scans .
         " WHERE coords_gal='" . $galaxy . "';";

	$result = $db->db_query($sql)
	or error(GENERAL_ERROR,
             'Could not query config information.', '',
	__FILE__, __LINE__, $sql);

	while( $row = $db->db_fetch_array($result)) {
		if (in_array($row['allianz'],$arrrayofAlis)) {

			$txta = 'a' . $row['coords_sys'];
			$txte = 'e' . $row['coords_sys'];
			$txtm = 'm' . $row['coords_sys'];
			$allymember[$txta] = "<b><i>";
			$allymember[$txte] = "</i></b>";
			$allymember[$txtm] = "";

			if( $showmembers == true ) {

				$sql = "SELECT DISTINCT user, allianz FROM " . $db_tb_scans .
               " WHERE coords_gal='" . $galaxy .
               "' AND coords_sys='" . $row['coords_sys'] . "';";
				$result2 = $db->db_query($sql)
				or error(GENERAL_ERROR,
                   'Could not query config information.', '',
				__FILE__, __LINE__, $sql);

				while( $row2 = $db->db_fetch_array($result2)) {
					if (in_array($row2['allianz'],$arrrayofAlis)) {
						if( !empty($allymember[$txtm])) {
							$allymember[$txtm] .= ", ";
						}
						$allymember[$txtm] .= $row2['user'];
					}
				}
			}
		}
	}
}


echo "<div class='doc_title'>Karte</div>\n";
echo "<br>\n";


//Link zum Nebel eintragen. Nur für Admins! <-- edit: doof, daher auskommentiert!
//if ( $user_status == "admin" ) {
    echo "<div class='system_notification'>
        <b>&gt; <a href=\"index.php?action=nebel&sid=" . $sid . "\" style='color:red'>
            Nebel eintragen
        </a> &lt;</b>
    </div>\n";
//}


echo "<form method=\"POST\" action=\"index.php?action=karte&sid=" . $sid .
     "\" enctype=\"multipart/form-data\" name=\"form1\">\n";
echo " <p align=\"center\">\n";

if( defined('ALLY_MEMBERS_ON_MAP' ) && ALLY_MEMBERS_ON_MAP === TRUE ) {
	echo "<input type=\"checkbox\" name=\"shownames\"" .
	($showmembers != false ? " checked" : "" ) .
      "> Mitgliedsnamen als Tooltip anzeigen\n";
	echo "<br/><br/>\n";
}

if ($galaxy > 1 )
{
	echo "<a href=\"index.php?action=karte&galaxy=" . ($galaxy - 1) .
        "&sid=" . $sid . "\"><b>&lt;&lt;</b></a>\n";
}
echo "Galaxie: <input type=\"text\" name=\"galaxy\" value=\"" . $galaxy .
     "\" style=\"width: 30\">&nbsp;<input type=\"submit\" value=\"los\" name=\"B1\" class=\"submit\">\n";

if ($galaxy < $config_map_galaxy_count )
{
	echo "<a href=\"index.php?action=karte&galaxy=" . ($galaxy + 1) .
       "&sid=" . $sid . "\"><b>&gt;&gt;</b></a>\n";
}

echo "</p></form>\n";
echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 80%;\">\n";
echo " <tr>\n";
echo "  <td class=\"titlebg\" align=\"center\" colspan=" . $config_map_cols . "\">\n";
echo "   <b>Galaxie " . $galaxy . "</b>\n";
echo "  </td>\n";
echo " </tr>\n";

if( defined( 'NEBULA' ) && NEBULA === TRUE ) {
	$sql = "SELECT sys, objekt, date, nebula FROM " . $db_tb_sysscans .
         " WHERE gal = '" . $galaxy . "' ORDER BY sys";
} else {
	$sql = "SELECT sys, objekt, date FROM " . $db_tb_sysscans .
         " WHERE gal = '" . $galaxy . "' ORDER BY sys";
}
$result = $db->db_query($sql)
or error(GENERAL_ERROR,
           'Could not query config information.', '',
__FILE__, __LINE__, $sql);

$maxsys = 0;
global $config_date;
while ( $row = $db->db_fetch_array($result) ) {
	if ( $row['objekt'] == "Stargate" )	{
		$sys[$row['sys']] = $config_color['Stargate'];
	}	elseif ( $row['objekt'] == "Schwarzes Loch" )	{
		$sys[$row['sys']] = $config_color['SchwarzesLoch'];
	}	elseif ( ($config_date - $row['date']) < 24*60*60 ) {
		$sys[$row['sys']] = $config_color['last24'];
	} else {
		$sys[$row['sys']] = scanAge($row['date']);
	}

	if (defined( 'NEBULA' ) && NEBULA === TRUE && !empty($row['nebula'])) {
		$sys[$row['sys']] .= "; background-image:url(bilder/" . $row['nebula'] .
                         ".gif); background-repeat:no-repeat";
	}

	$maxsys = $row['sys'];
}

$col = 0;
for ( $i = 1; $i <= $maxsys; $i++ ) {
	if ( empty($sys[$i]) ) {
		$sql = "SELECT objekt FROM " . $db_tb_scans .
           " WHERE coords_sys='" . $i .
           "' AND coords_gal='" . $galaxy . "'";
		$result_leer = $db->db_query($sql)
		or error(GENERAL_ERROR,
               'Could not query config information.', '',
		__FILE__, __LINE__, $sql);
		$row_leer = $db->db_num_rows($result_leer);
		if ( $row_leer > 0 )
		$sys[$i] = "#FF0000";
	}

	if( defined('ALLY_MEMBERS_ON_MAP' ) && ALLY_MEMBERS_ON_MAP === TRUE ) {
		$txta = 'a' . $i;
		$txte = 'e' . $i;
		$txtm = 'm' . $i;

		if(isset($allymember[$txta])) {
			$formatStart = $allymember[$txta];
		} else {
			$formatStart = "";
		}

		if(isset($allymember[$txte])) {
			$formatEnd = $allymember[$txte];
		} else {
			$formatEnd = "";
		}

		if(isset($allymember[$txtm])) {
			$memberlist = $allymember[$txtm];
		} else {
			$memberlist = "";
		}
	}

	if ( $col == 0 )
	echo "<tr>\n";

	echo " <td class=\"windowbg1\" style=\"width: " .
	floor(100 / $config_map_cols) . " %; background-color: " .
	(( empty($sys[$i]) ) ? "#FFFFFF"
	: $sys[$i] ) .
       ";\" align=\"center\">";

	if ( empty($sys[$i]) ) {
		echo $i;
	} else {
		$showgalaxylink = "<a href=\"index.php?action=showgalaxy&sys_end=" . $i .
         "&sys_start=" . $i . "&gal_end=" . $galaxy .
         "&gal_start=" . $galaxy . "&sid=" . $sid . "\" ";

		if( ALLY_MEMBERS_ON_MAP === TRUE ) {
			echo $formatStart;
			echo $showgalaxylink . " title=\"". $memberlist . "\">" . $i . "</a>\n";
			echo $formatEnd;
		} else {
			echo $showgalaxylink . ">". $i . "</a>\n";
		}
	}
	echo "  </td>\n";

	$col++;
	if ( $col == $config_map_cols )
	{
		echo "</tr>\n";
		$col = 0;
	}
}

if ( $col <> $config_map_cols ) {
	echo "  <td class=\"windowbg1\" colspan=\"" . ( $config_map_cols - $col ) . "\"></td>\n";
	echo " </tr>\n";
}
echo "</table>\n";

echo "<br>\n";
echo "<br>\n";

echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"0\">\n";
echo " <tr>\n";
echo "  <td style=\"width: 4%; background-color: " . $config_color['Stargate'] . "\"></td>\n";
echo "  <td style=\"width: 14%;\">Stargate</td>\n";
echo "  <td style=\"width: 4%; background-color: " . $config_color['SchwarzesLoch'] ."\"></td>\n";
echo "  <td style=\"width: 14%;\">Schwarzes Loch</td>\n";
echo "  <td style=\"width: 4%; background-color: " . $config_color['last24'] ."\"></td>\n";
echo "  <td style=\"width: 14%;\">jünger 24 Stunden</td>\n";
echo "  <td style=\"width: 4%; background-color: #00FF00\"></td>\n";
echo "  <td style=\"width: 14%;\">älter 24 Stunden</td>\n";
echo "  <td style=\"width: 4%; background-color: #FFFF00\"></td>\n";
echo "  <td style=\"width: 14%;\">" . (round( $config_map_timeout / $DAYS / 2)) . " Tage alt</td>\n";
echo "  <td style=\"width: 4%; background-color: #FF0000\"></td>\n";
echo "  <td style=\"width: 11%;\">älter als " . (round( $config_map_timeout / $DAYS)) . " Tage</td>\n";
echo " </tr>\n";

if( defined('ALLY_MEMBERS_ON_MAP' ) && ALLY_MEMBERS_ON_MAP === TRUE ) {
	echo " <tr>\n";
	echo "  <td colspan=\"10\" align=\"center\">Planeten mit <b><i>Wing- und Allianzmitgliedern</i></b></td>\n";
	echo " </tr>\n";
}
echo "</table>\n";
?>
