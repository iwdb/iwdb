<?php
/*****************************************************************************
 * m_lieferstats.php                                                           *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * ========================================================================= *
 * Copyright (c) 2004 Robert Riess - All Rights Reserved                     *
 *****************************************************************************
 * This program is free software; you can redistribute it and/or modify it   *
 * under the terms of the GNU General Public License as published by the     *
 * Free Software Foundation; either version 2 of the License, or (at your    *
 * option) any later version.                                                *
 *                                                                           *
 * This program is distributed in the hope that it will be useful, but       *
 * WITHOUT ANY WARRANTY; without even the implied warranty of                *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General *
 * Public License for more details.                                          *
 *                                                                           *
 * The GNU GPL can be found in LICENSE in this directory                     *
 *****************************************************************************
 * Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  *
 * IW-Spielern.                                                              *
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname = "m_lieferstats";

//****************************************************************************
//
// -> Titel des Moduls
//
$modultitle = "Lieferstatistik";

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
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc = "Schwanzvergleich, wer wieviel geliefert hat :)";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    //nothing here
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modulstatus;

    $menu             = getVar('menu');
    $submenu          = getVar('submenu');
    $menuetitel       = "Lieferstatistik";
    $actionparameters = "";

    insertMenuItem($menu, $submenu, $menuetitel, $modulstatus, $actionparameters);
    //
    // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
    //
    // 	insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
    //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file
//
function workInstallConfigString()
{
    /*  global $config_gameversion;
      return
        "\$v04 = \" <div class=\\\"doc_lightred\\\">(V " . $config_gameversion . ")</div>\";";
    */
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase()
{
    //nothing here
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
if (!empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
    if ($user_status != "admin") {
        die('Hacking attempt...');
    }

    echo "<h2>Installationsarbeiten am Modul " . $modulname . " (" . $_REQUEST['was'] . ")</h2>\n";

    require_once './includes/menu_fn.php';

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

global $db, $db_prefix, $db_tb_user, $db_tb_transferliste;

// Titelzeile
doc_title($modultitle);
echo " 	 <br />\n";
echo " 	 <br />\n";

$sql = "SELECT `sitterlogin`, `budflesol` FROM `" . $db_tb_user . "`";
$result_sitterlogin = $db->db_query($sql);

$data = array();

$fak_eisen	= 2;
$fak_stahl	= 5;
$fak_vv4a	= 11;
$fak_chem	= 2;
$fak_eis	= 2;
$fak_wasser	= 6;
$fak_ene	= 1;
$fak_bev	= 0;

?>
<table data-sortlist="[[10,1]]" class='tablesorter-blue'>
	<thead>
		<tr class='center'>
			<th class='sorter-false' colspan='11'>
				<?php
				echo "Einfache Auflistung anhand eingelesener Transportberichte, was aus dem Acc heraus an andere Member verschickt wurde";
				?>
			</th>
		<tr>
		<tr>
			<th>
				<b>Spieler</b>
			</th>
			<th>
				<b>Spielart</b>
			</th>
			<th>
				<b>Eisen</b>
			</th>
			<th>
				<b>Stahl</b>
			</th>
			<th>
				<b>VV4A</b>
			</th>
			<th>
				<b>Chemie</b>
			</th>
			<th>
				<b>Eis</b>
			</th>
			<th>
				<b>Wasser</b>
			</th>
			<th>
				<b>Energie</b>
			</th>
			<th>
				<b>Bevölkerung</b>
			</th>
			<th>
				<b>Gesamtpunkte</b>
			</th>
		</tr>
	</thead>
	<tbody>
		
		<?php
		while ($row_sitterlogin = $db->db_fetch_array($result_sitterlogin)) {
			$sql1 = "SELECT buddler, SUM(eisen), SUM(stahl), SUM(vv4a), SUM(chem), SUM(eis), SUM(wasser), SUM(energie), SUM(volk) FROM `" . $db_tb_transferliste . "` WHERE `buddler` = '" . $row_sitterlogin['sitterlogin'] . "';";
			$result_sum = $db->db_query($sql1);
			$row_sum = $db->db_fetch_array($result_sum);
			
			?>
			
			<tr>
				<td>
					<?php
						echo $row_sitterlogin['sitterlogin'];
					?>
				</td>
				<td>
					<?php
						echo $row_sitterlogin['budflesol'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(eisen)']=='') echo "0";
						else echo $row_sum['SUM(eisen)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(stahl)']=='') echo "0";
						else echo $row_sum['SUM(stahl)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(vv4a)']=='') echo "0";
						else echo $row_sum['SUM(vv4a)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(chem)']=='') echo "0";
						else echo $row_sum['SUM(chem)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(eis)']=='') echo "0";
						else echo $row_sum['SUM(eis)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(wasser)']=='') echo "0";
						else echo $row_sum['SUM(wasser)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(energie)']=='') echo "0";
						else echo $row_sum['SUM(energie)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(volk)']=='') echo "0";
						else echo $row_sum['SUM(volk)'];
					?>
				</td>
				<td>
					<?php
						$gesamt = (($row_sum['SUM(eisen)']*$fak_eisen)+
									($row_sum['SUM(stahl)']*$fak_stahl)+
									($row_sum['SUM(vv4a)']*$fak_vv4a)+
									($row_sum['SUM(chem)']*$fak_chem)+
									($row_sum['SUM(eis)']*$fak_eis)+
									($row_sum['SUM(wasser)']*$fak_wasser)+
									($row_sum['SUM(energie)']*$fak_ene)+
									($row_sum['SUM(volk)']*$fak_bev));
						echo "<b>" . $gesamt . "</b>";
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
</table>

<?php
echo " 	 <br />\n";
echo " 	 <br />\n";

$sql = "SELECT `sitterlogin`, `budflesol` FROM `" . $db_tb_user . "`";
$result_sitterlogin = $db->db_query($sql);

$data = array();

$fak_eisen	= 2;
$fak_stahl	= 5;
$fak_vv4a	= 11;
$fak_chem	= 2;
$fak_eis	= 2;
$fak_wasser	= 6;
$fak_ene	= 1;
$fak_bev	= 0;

?>
<table data-sortlist="[[10,1]]" class='tablesorter-blue'>
	<thead>
		<tr class='center'>
			<th class='sorter-false' colspan='11'>
				<?php
				echo "Einfache Auflistung anhand eingelesener Transportberichte, was man alles geliefert bekommen hat";
				?>
			</th>
		<tr>
		<tr>
			<th>
				<b>Spieler</b>
			</th>
			<th>
				<b>Spielart</b>
			</th>
			<th>
				<b>Eisen</b>
			</th>
			<th>
				<b>Stahl</b>
			</th>
			<th>
				<b>VV4A</b>
			</th>
			<th>
				<b>Chemie</b>
			</th>
			<th>
				<b>Eis</b>
			</th>
			<th>
				<b>Wasser</b>
			</th>
			<th>
				<b>Energie</b>
			</th>
			<th>
				<b>Bevölkerung</b>
			</th>
			<th>
				<b>Gesamtpunkte</b>
			</th>
		</tr>
	</thead>
	<tbody>
		
		<?php
		while ($row_sitterlogin = $db->db_fetch_array($result_sitterlogin)) {
			$sql1 = "SELECT fleeter, SUM(eisen), SUM(stahl), SUM(vv4a), SUM(chem), SUM(eis), SUM(wasser), SUM(energie), SUM(volk) FROM `" . $db_tb_transferliste . "` WHERE `fleeter` = '" . $row_sitterlogin['sitterlogin'] . "';";
			$result_sum = $db->db_query($sql1);
			$row_sum = $db->db_fetch_array($result_sum);
			
			?>
			
			<tr>
				<td>
					<?php
						echo $row_sitterlogin['sitterlogin'];
					?>
				</td>
				<td>
					<?php
						echo $row_sitterlogin['budflesol'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(eisen)']=='') echo "0";
						else echo $row_sum['SUM(eisen)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(stahl)']=='') echo "0";
						else echo $row_sum['SUM(stahl)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(vv4a)']=='') echo "0";
						else echo $row_sum['SUM(vv4a)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(chem)']=='') echo "0";
						else echo $row_sum['SUM(chem)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(eis)']=='') echo "0";
						else echo $row_sum['SUM(eis)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(wasser)']=='') echo "0";
						else echo $row_sum['SUM(wasser)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(energie)']=='') echo "0";
						else echo $row_sum['SUM(energie)'];
					?>
				</td>
				<td>
					<?php
						if ($row_sum['SUM(volk)']=='') echo "0";
						else echo $row_sum['SUM(volk)'];
					?>
				</td>
				<td>
					<?php
						$gesamt = (($row_sum['SUM(eisen)']*$fak_eisen)+
									($row_sum['SUM(stahl)']*$fak_stahl)+
									($row_sum['SUM(vv4a)']*$fak_vv4a)+
									($row_sum['SUM(chem)']*$fak_chem)+
									($row_sum['SUM(eis)']*$fak_eis)+
									($row_sum['SUM(wasser)']*$fak_wasser)+
									($row_sum['SUM(energie)']*$fak_ene)+
									($row_sum['SUM(volk)']*$fak_bev));
						echo "<b>" . $gesamt . "</b>";
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
</table>