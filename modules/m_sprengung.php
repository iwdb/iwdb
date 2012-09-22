<?php
/*****************************************************************************/
/* m_sprengung.php                                                           */
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
/* Anzeige der Sprengungen                                                               */
/* für die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschaftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*         http://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                                                                           */
/*****************************************************************************/

//direktes Aufrufen verhindern
if (basename($_SERVER['PHP_SELF']) != "index.php") {header('HTTP/1.1 404 not found');exit;};
if (!defined('IRA')) {header('HTTP/1.1 404 not found');exit;};

//****************************************************************************
//
// -> Name des Moduls, ist notwendig fuer die Benennung der zugehuerigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung fuer 
//    eine Installation ueber das Menue
//
$modulname  = "m_sprengung";

//****************************************************************************
//
// -> Menuetitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "zeige Sprengungen";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul ueber die Navigation 
//    ausfuehren darf. Muegliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "zeigt an wann Planeten vorraussichtlich gesprengt werden";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase() {
	global $db, $db_prefix, $db_tb_iwdbtabellen;

/*	foreach ($sqlscript as $sql) {
		echo "<br>" . $sql;
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

  echo "<div class='system_notification'>Installation: Datenbank&auml;nderungen = <b>OK</b></div>";*/
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
  	insertMenuItem( $_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparameters );
	  //
	  // Weitere Wiederholungen fuer weitere Menue-Eintraege, z.B.
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
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase() {
	global $db, $db_tb_scans_historie, $db_tb_iwdbtabellen;

/*	foreach ($sqlscript as $sql) {
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

    echo "<div class='system_notification'>Deinstallation: Datenbank&auml;nderungen = <b>OK</b></div>";*/
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
  if ( $user_status != "admin" ) 
		die('Hacking attempt...');

  echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname . 
	     " ("  . $_REQUEST['was'] . ")</div>\n";

  if (!@include("./includes/menu_fn.php")) 
	  die( "Cannot load menu functions" );

  // Wenn ein Modul administriert wird, soll der Rest nicht mehr 
  // ausgeführt werden.
  return;
}

//****************************************************************************

Global $config_date;


if ((getVar('ordered') === 'desc')) {
    $sort='DESC';
} else {
    $sort='ASC';
}

// Seitenparameter ermitteln und filtern
$gal_start = filter_int(getVar('gal_start'), $user_gal_start, $config_map_galaxy_min, $config_map_galaxy_max);
$gal_end   = filter_int(getVar('gal_end')  , $user_gal_end, $config_map_galaxy_min, $config_map_galaxy_max);
if ($gal_start>$gal_end) {  // ggf Werte vertauschen
    list($gal_start,$gal_end) = array($gal_end,$gal_start);
}
$sys_start = filter_int(getVar('sys_start'), $user_sys_start, $config_map_system_min, $config_map_system_max);
$sys_end   = filter_int(getVar('sys_end')  , $user_sys_end, $config_map_system_min, $config_map_system_max);
if ($sys_start>$sys_end) {  // ggf Werte vertauschen
    list($sys_start,$sys_end) = array($sys_end,$sys_start);
}


// Titelzeile
echo "<div class='doc_title'>Sprengungen</div><br>\n";
echo "<div></div>Hier kann man sehen, wann Hasi die nächsten Planeten sprengt um neue Hyperraumumgehungsstraßen zu bauen:</div>";
echo "<form method='POST' action='index.php?action=" . $modulname . "&amp;sid=" . $sid . "' enctype='multipart/form-data'><p align='center'>\n";
echo "  Galaxie von: <input name='gal_start' value='" . $gal_start . "' style='width: 5em' type='number' min='".$config_map_galaxy_min."' max='".$config_map_galaxy_max."'> bis: <input name='gal_end' value='" . $gal_end . "' style='width: 5em' type='number' min='".$config_map_galaxy_min."' max='".$config_map_galaxy_max."'><br><br>";
echo "  System von: <input name='sys_start' value='" . $sys_start . "' style='width: 5em' type='number' min='".$config_map_system_min."' max='".$config_map_system_max."'> bis: <input name='sys_end' value='" . $sys_end . "' style='width: 5em' type='number' min='".$config_map_system_min."' max='".$config_map_system_max."'><br><br>";
echo "  <input type='submit' value='los' name='B1' class='submit'><br>";
echo "</form>\n";
?>
<br>

<table class="centeredtable">
    <tr>
        <th>Koords</th>
        <th>Planetentyp</th>
        <th>Eisen<br><span style="font-size:x-small">(eff)</span></th>
        <th>Chemie<br><span style="font-size:x-small">(eff)</span></th>
        <th>Eis<br><span style="font-size:x-small">(eff)</span></th>
        <th>LB</th>
        <th>Gebäude-<br>dauer</th>
        <th>
            <a href="index.php?action=m_sprengung&amp;ordered=asc&amp;sid=<?php echo $sid; ?>"><img src="bilder/desc.gif" border="0" alt="a"></a>
            Sprengung
            <a href="index.php?action=m_sprengung&amp;ordered=desc&amp;sid=<?php echo $sid; ?>"><img src="bilder/asc.gif" border="0" alt="d"></a>
            <br><span style="font-size:x-small">frühestens</span>
        </th>
    

    </tr>

<?php

// SQL-Statement aufbauen
$sql_where='';
    
if ($gal_start > 0) {
	$sql_where .= ' coords_gal>=' . $gal_start;
}

if ($gal_end > 0) {
	if ($sql_where != '') {
        $sql_where .= ' AND ';
    }
	$sql_where .= ' coords_gal<=' . $gal_end;
}

if ($sys_start > 0) {
	if ($sql_where != '') {
        $sql_where .= ' AND ';
    }
	$sql_where .= ' coords_sys>=' . $sys_start;
}

if ($sys_end > 0) {
	if ($sql_where != '') {
        $sql_where .= ' AND ';
    }
	$sql_where .= ' coords_sys<=' . $sys_end;
}

if ($sql_where != '') {
	$sql_where .= ' AND ';
}

$sql_where = " WHERE " . $sql_where . " reset_timestamp>0 AND geoscantime>0 AND objekt='---' ";

$sql_order = " ORDER BY reset_timestamp ".$sort." , coords_gal ASC , coords_sys ASC , coords_planet ASC";

$Limit = " Limit 100";

// Abfrage ausführen
$sql = "SELECT coords,typ,(eisengehalt*100/DGmod) AS Eisen_eff,(chemievorkommen*100/DGmod) AS Chem_eff,(eisdichte*100/DGmod) AS Eis_eff,lebensbedingungen,DGmod,reset_timestamp FROM " . $db_tb_scans . $sql_where . $sql_order . $Limit;

$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query scans_historie information.', '', __FILE__, __LINE__, $sql);

// Abfrage auswerten
while ($row = $db->db_fetch_array($result)) {
    if (empty($row['DGmod'])) {
        $row['DGmod']=1;
    }

	echo "  <tr>\n";
	echo "    <td>\n";
	echo "      <a href='index.php?action=showplanet&amp;coords=" . $row['coords'] . "&amp;ansicht=auto&amp;sid=" . $sid . "'>\n";
	echo "      " . $row['coords'] . "\n";
	echo "      </a>\n";
	echo "    </td>\n";
	echo "    <td>\n";
	echo "      " . $row['typ'] . "\n";
	echo "    </td>\n";
	echo "    <td>\n";
	echo "      " . (int)($row['Eisen_eff']) . " %\n";
	echo "    </td>\n";
	echo "    <td>\n";
	echo "      " . (int)($row['Chem_eff']) . " %\n";
	echo "    </td>\n";
	echo "    <td>\n";
	echo "      " . (int)($row['Eis_eff']) . " %\n";
	echo "    </td>\n";
	echo "    <td>\n";
	echo "      " . $row['lebensbedingungen'] . "%\n";
	echo "    </td>\n";
	echo "    <td>\n";
	echo "      " . $row['DGmod'] . " %\n";
	echo "    </td>\n";
	echo "    <td>\n";

    echo '<a href="index.php?action=m_sprengung&amp;ordered=asc&amp;sid='.$sid.'><img src="bilder/asc.gif" border="0" alt="asc"></a>';
	$timediff = ($row['reset_timestamp'] - 86400) - $config_date;   //vorverlegen des Sprengdatums wegen +-24h
    if ($timediff>0)
        echo makeduration2(0, $timediff) . " \n";
    elseif ($timediff>-172800)                                        // 2 Tage Toleranz
		echo "evl. seit ".makeduration2($timediff, 0)." gesprengt\n";
    else
        echo "wahrscheinlich gesprengt!";                             //alles was drüber ist ist wohl weg
    
	echo "    </td>\n";
	echo "  </tr>\n";
}
echo "</table>";

?>
<br>