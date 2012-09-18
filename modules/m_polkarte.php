<?php
/*****************************************************************************/
/* m_polkarte.php                                                            */
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

/*****************************************************************************/
/* Dieses Modul dient für eine politische Karte, in der Allianzstatuse       */
/*angezeigt werden                                                           */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                                                                           */
/*****************************************************************************/

//direktes Aufrufen verhindern
if (basename($_SERVER['PHP_SELF']) != "index.php") {header('HTTP/1.1 404 not found');exit;};
if (!defined('IRA')) {header('HTTP/1.1 404 not found');exit;};

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
$modulname  = "m_polkarte";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "politische Karte";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation
//    ausführen darf. Mögliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menü-übersicht angezeigt wird.
//
$moduldesc =
  "Anzeige einer Universumskarte mit allen Allianzstati";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//
function workInstallDatabase() {
//	global $db, $db_tb_user;
//
//  $sql ="ALTER TABLE `" . $db_tb_user . "`" .
//	  " ADD `notice` text NOT NULL AFTER `titel`;";
//
//  $result = $db->db_query($sql)
//	  or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

  echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu() {
    global $modultitle, $modulstatus, $_POST;

	$actionparamters = "";
  	insertMenuItem( $_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparamters );
	  //
	  // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
	  //
	  // 	insertMenuItem( $_POST['menu'], ($_POST['submenu']+1), "Titel2", "hc", "&weissichnichtwas=1" );
	  //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed
// for the configuration file.
//
function workInstallConfigString() {
  return
  "// verwendete Allianzstatuse  \n" .
  "\$config_polstat = array(); \n" .
  "\$config_polstat['1v1']='own'; \n" .
  "\$config_polstat['1v2']='wing'; \n" .
  "\$config_polstat['1v3']='Wing'; \n" .
  "\$config_polstat['2v1']='VB';   \n" .
  "\$config_polstat['2v2']='iVB';  \n" .
  "\$config_polstat['2v3']='keineAhnung'; \n" .
  "\$config_polstat['3v1']='Krieg';   \n" .
  "\$config_polstat['3v2']='hmmm';   \n" .
  "\$config_polstat['3v3']='waswasi'; \n" .
  "// verwendete Farben \n" .
  "\$config_polcolor = array(); " .
  "\$config_polcolor['nichts'] = '#FFFFFF'; \n" .
  "\$config_polcolor['1allein'] = '#C4F493';  \n" .
  "\$config_polcolor['2allein'] = '#4A71D5';  \n" .
  "\$config_polcolor['3allein'] = '#E84528';  \n" .
  "\$config_polcolor['1und2'] = '#191970';    \n" .
  "\$config_polcolor['1und3'] = '#FF7F24';    \n" .
  "\$config_polcolor['2und3'] = '#FA8072';     \n" .
  "\$config_polcolor['1und2und3'] = '#FF7F24'; \n";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//
function workUninstallDatabase() {
    echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgeführt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs:
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" natürlich deinen Server angeben und default
// durch den Dateinamen des Moduls ersetzen.
//
if( !empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)

    if ( $user_status != "admin" ) {
        die('Hacking attempt...');
    }

    echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
	     " ("  . $_REQUEST['was'] . ")</div>\n";

    if (!@include("./includes/menu_fn.php")) {
        die( "Cannot load menu functions" );
    }

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgeführt werden.
    return;
}

if (!@include("./config/".$modulname.".cfg.php")) {
	die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

$sqlALI = "SELECT allianz FROM ".$db_tb_allianzstatus . " WHERE status='own' OR status='wing'";
$resultALI = $db->db_query($sqlALI)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlALI);

$i = 0;
while( $rowALI = $db->db_fetch_array($resultALI)) {
    $i++;
    $arrrayofAlis[$i] = $rowALI['allianz'];
}

$galaxy = (int)getVar('galaxy');
if ( empty($galaxy) ) {
    $galaxy = $config_map_default_galaxy;
}

//ToDo: Profileinstellung ermöglichen - masel
$showmembers = FALSE;
if( defined('ALLY_MEMBERS_ON_MAP' ) && ALLY_MEMBERS_ON_MAP === TRUE ) {
    $showmembers = TRUE;
}

if ($showmembers) {
    $allymember = array();
    $sql = "SELECT DISTINCT coords_sys,allianz FROM " . $db_tb_scans .
           " WHERE coords_gal='" . $galaxy . "';";
    $result = $db->db_query($sql)
  	    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while( $row = $db->db_fetch_array($result)) {
        if (in_array($row['allianz'],$arrrayofAlis)) {
            $txta = 'a' . $row['coords_sys'];
            $txte = 'e' . $row['coords_sys'];
            $txtm = 'm' . $row['coords_sys'];
            $allymember[$txta] = "<b><i>";
            $allymember[$txte] = "</i></b>";
            $allymember[$txtm] = "";

            if( $showmembers === TRUE ) {
                $sql =  "SELECT DISTINCT user,allianz FROM " . $db_tb_scans .
                        " WHERE coords_gal='" . $galaxy .
                        "' AND coords_sys='" . $row['coords_sys'] . "'";

                $result2 = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

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
echo "<br />";

// Tooltip Auswahlbox zur Memberanzeige gelöscht -> bei Gelegenheit in die Profileinstellungen verschieben
// masel

// Anzeige Galaxiezahllinks (schnellere Auswahl als per Inputfeld) - masel
echo "<div class='doc_big_black'>";
if ($galaxy > 1 ) {
    echo "<a href='index.php?action=m_polkarte&amp;galaxy=" . ($galaxy - 1) .
        "&amp;sid=" . $sid . "'><b>&lt;&lt;</b></a>\n";                 // <<
}

if (isset($config_map_galaxy_min) AND !empty($config_map_galaxy_min)) {
    $gal = $config_map_galaxy_min;
} else {
    $gal=1;
}

while ($gal <= $config_map_galaxy_count) {                               // Galaxiezahl
    echo "<a href='index.php?action=m_polkarte&amp;galaxy=" . ($gal) . "&amp;sid=" . $sid . "'>";
    if ($gal == $galaxy) {
        echo "<b>[".$gal."]</b></a>\n";
    } else {
        echo $gal."</a>\n";
    }
    $gal++;
}

if ($galaxy < $config_map_galaxy_count ) {
    echo "<a href='index.php?action=m_polkarte&amp;galaxy=" . ($galaxy + 1) ."&amp;sid=" . $sid . "'><b>&gt;&gt;</b></a>\n";      // >>
}
echo "</div></p>";

echo "<table border='0' cellpadding='4' cellspacing='1' class='bordercolor' style='width: 80%;'>\n";
echo " <tr>\n";
echo "  <td class='titlebg' align='center' colspan=" . $config_map_cols . "'>\n";
echo "   <b>Galaxie " . $galaxy . "</b>\n";
echo "  </td>\n";
echo " </tr>\n";

if( defined( 'NEBULA' ) && NEBULA === TRUE ) {
    $sql =  "SELECT sys, objekt, nebula FROM " . $db_tb_sysscans .
            " WHERE gal = '" . $galaxy . "' ORDER BY sys";
} else {
    $sql =  "SELECT sys, objekt, FROM " . $db_tb_sysscans .
            " WHERE gal = '" . $galaxy . "' ORDER BY sys";
}
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

$maxsys = 0;
while ( $row = $db->db_fetch_array($result) ) {
	if ( $row['objekt'] == "Stargate" )	{
		$sys[$row['sys']] = $config_color['Stargate'];
	} elseif ( $row['objekt'] == "Schwarzes Loch" )	{
		$sys[$row['sys']] = $config_color['SchwarzesLoch'];
	} else {
	    $own=0;
        $vb=0;
        $krieg=0;
		$sql = "SELECT allianz FROM " . $db_tb_scans . " WHERE coords_sys='" . $row['sys'] . "' AND coords_gal='" . $galaxy . "'";
		$result_sys = $db->db_query($sql);

		while ( $row_sys = $db->db_fetch_array($result_sys)) {
             	$sql = "SELECT status FROM " . $db_tb_allianzstatus . " WHERE allianz LIKE '" . $row_sys['allianz'] . "'";
		        $result_status = $db->db_query($sql)
			        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			    $row_status = $db->db_fetch_array($result_status);
			    if( $row_status['status'] == $config_polstat['1v1'] ) $own=1;
			    if( $row_status['status'] == $config_polstat['1v2'] ) $own=1;
			    if( $row_status['status'] == $config_polstat['1v3'] ) $own=1;
			    if( $row_status['status'] == $config_polstat['2v1'] ) $vb=1;
			    if( $row_status['status'] == $config_polstat['2v2'] ) $vb=1;
			    if( $row_status['status'] == $config_polstat['2v3'] ) $vb=1;
			    if( $row_status['status'] == $config_polstat['3v1'] ) $krieg=1;
			    if( $row_status['status'] == $config_polstat['3v2'] ) $krieg=1;
			    if( $row_status['status'] == $config_polstat['3v3'] ) $krieg=1;
        }

        if ($own == 1) {
            if($vb == 1) {
              if($krieg == 1) {
                  $sys[$row['sys']] = $config_polcolor['1und2und3'];
              } else {
                  $sys[$row['sys']] = $config_polcolor['1und2'];
              }
            } else {
                if ($krieg == 1) {
                    $sys[$row['sys']] = $config_polcolor['1und3'];
                } else {
                    $sys[$row['sys']] = $config_polcolor['1allein'];
		        }
            }
        } else {
            if($vb == 1) {
                if($krieg == 1) {
                    $sys[$row['sys']] = $config_polcolor['2und3'];
                } else {
                    $sys[$row['sys']] = $config_polcolor['2allein'];
                }
            } else {
                if ($krieg == 1) {
                    $sys[$row['sys']] = $config_polcolor['3allein'];
                } else {
                    $sys[$row['sys']] = $config_polcolor['nichts'];
                }
		    }
        }
	}

    if (defined( 'NEBULA' ) && NEBULA === TRUE && !empty($row['nebula'])) {
        if (in_array($row['nebula'], array('blau','gelb','gruen','rot','violett'))) {
            $sys[$row['sys']] .= "; background-image:url(bilder/iwdb/nebel/{$row['nebula']}.png); background-repeat:no-repeat";
        }
    }

	$maxsys = $row['sys'];
}

$col = 0;
for ( $i = 1; $i <= $maxsys; $i++ ) {
	if ( empty($sys[$i]) ) {
		$sql =  "SELECT objekt FROM " . $db_tb_scans .
                " WHERE coords_sys='" . $i .
                "' AND coords_gal='" . $galaxy . "'";
		$result_leer = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_leer = $db->db_num_rows($result_leer);
		if ( $row_leer > 0 ) {
            $sys[$i] = "#FF0000";
        }
	}

    if ($showmembers) {
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

	if ( $col == 0 ) {
        echo "<tr>\n";
    }

    echo " <td class='windowbg1' style='width: " . floor(100 / $config_map_cols) . " %; background-color: "
        . (( empty($sys[$i]) ) ? "#FFFFFF"
            : $sys[$i] )
        . ";' align='center'>";

	if ( empty($sys[$i]) ) {
        echo $i;
    } else {
        $showgalaxylink = "<a href='index.php?action=showgalaxy&sys_end=" . $i .
         "&sys_start=" . $i . "&gal_end=" . $galaxy .
         "&gal_start=" . $galaxy . "&sid=" . $sid . "' ";

        if ($showmembers) {
            echo $formatStart;
            echo $showgalaxylink . " title='". $memberlist . "'>" . $i . "</a>\n";
            echo $formatEnd;
        } else {
            echo $showgalaxylink . ">". $i . "</a>\n";
        }
	}

    echo "  </td>\n";

	$col++;
	if ( $col == $config_map_cols )	{
		echo "</tr>\n";
		$col = 0;
	}
}

if ( $col <> $config_map_cols ) {
    echo "  <td class='windowbg1' colspan='" . ( $config_map_cols - $col ) . "'></td>\n";
    echo " </tr>\n";
}
echo "</table>\n";

echo "<br>\n";
echo "<br>\n";

echo "<table border='0'cellpadding='4' cellspacing='0'>\n";
echo " <tr>\n";
echo "  <td style='width: 30px; background-color: " . $config_color['Stargate'] . "'></td>\n";
echo "  <td style='width: 100px;'>Stargate</td>\n";
echo "  <td style='width: 30px; background-color: " . $config_color['SchwarzesLoch'] ."'></td>\n";
echo "  <td style='width: 100px;'>Schwarzes Loch</td>\n";
echo "  <td style='width: 30px; background-color: " . $config_polcolor['1allein'] ."'></td>\n";
echo "  <td style='width: 100px;'>Eigene + Wing</td>\n";
echo "  <td style='width: 30px; background-color: " . $config_polcolor['2allein'] ."'></td>\n";
echo "  <td style='width: 100px;'>Verbündete</td>\n";
echo "  <td style='width: 30px; background-color: " . $config_polcolor['3allein'] ."'></td>\n";
echo "  <td style='width: 100px;'>Verfeindete</td>\n";
echo " </tr>\n";
echo " <tr>\n";
echo " </tr>\n";
echo " <tr>\n";
echo "  <td style='width: 30px; background-color: " . $config_polcolor['1und2'] ."'></td>\n";
echo "  <td style='width: 100px;'>Eigene + Verbündete</td>\n";
echo "  <td style='width: 30px; background-color: " . $config_polcolor['1und3'] ."'></td>\n";
echo "  <td style='width: 100px;'>Eigenen + Verfeindete</td>\n";
echo "  <td style='width: 30px; background-color: " . $config_polcolor['2und3'] ."'></td>\n";
echo "  <td style='width: 100px;'>Verbündete + Verfeindete</td>\n";
echo "  <td style='width: 30px; background-color: " . $config_polcolor['1und2und3'] ."'></td>\n";
echo "  <td style='width: 100px;'>Von allem ein bissi was</td>\n";
echo " </tr>\n";

echo "</table>\n";