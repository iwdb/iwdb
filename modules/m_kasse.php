<?php
/*****************************************************************************
 * m_kasse.php                                                               *
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
// -> Name des Moduls, ist notwendig für die Benennung der zugehoerigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
$modulname = "m_kasse";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Allianzkasse";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation
//    ausfuehren darf. Moegliche Werte:
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc =
    "Das Allianzkassenmodul dient zur Speicherung und übersichtlichen Anzeige von Daten aus der Allianzkasse";

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
    global $modultitle, $modulstatus;

    $menu    = getVar('menu');
    $submenu = getVar('submenu');

    $actionparameters = "";
    insertMenuItem($menu, $submenu, $modultitle, $modulstatus, $actionparameters);
    //
    // Weitere Wiederholungen für weitere Menue-Einträge, z.B.
    //
    //     insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
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
        "\$v04 = ' <div class=\\'doc_lightred\\'>(V " . $config_gameversion . ")</div>';";
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
// Dieser Abschnitt wird nur ausgefuehrt wenn das Modul mit dem Parameter
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

    echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")</div>\n";

    if (!@include("./includes/menu_fn.php")) {
        die("Cannot load menu functions");
    }

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgefuehrt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

//getvariablen organisieren

$type  = getVar('type') ? getVar('type') : 'payedto';

if (getVar('fromday') && getVar('fromyear') && getVar('frommonth')) {
    $fromday   = sprintf('%02d', getVar('fromday'));
    $frommonth = sprintf('%02d', getVar('frommonth'));
    $fromyear  = sprintf('%04d', getVar('fromyear'));
    $fromdate  = $fromyear . "-" . $frommonth . "-" . $fromday;
} else {
    $fromday   = '10';
    $frommonth = '03';
    $fromyear  = '2007';
}
if (getVar('today') && getVar('toyear') && getVar('tomonth')) {
    $today   = sprintf('%02d', getVar('today'));
    $tomonth = sprintf('%02d', getVar('tomonth'));
    $toyear  = sprintf('%02d', getVar('toyear'));
    $todate  = $toyear . "-" . $tomonth . "-" . $today;
} else {
    $heute   = getdate(CURRENT_UNIX_TIME);
    $today   = sprintf('%02d', $heute['mday']);
    $tomonth = sprintf('%02d', $heute['mon']);
    $toyear  = $heute['year'];
}

global $db_tb_user, $user_id;
//Allianz des Users herausfinden
$sql = "SELECT allianz FROM " . $db_tb_user . " WHERE id = '" . $user_id . "'";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $allianz = $row['allianz'];
}
if (strtolower($user_status) == 'admin' && getVar('allianz')) {
    $allianz = getVar('allianz');
    $allianz = urldecode($allianz);
}

//url fürs sortieren wieder zusammensetzen
$url = "index.php?action=m_kasse&type=$type&today=$today&tomonth=$tomonth&toyear=$toyear&fromday=$fromday&frommonth=$frommonth&fromyear=$fromyear&allianz=$allianz";

doc_title("Allianzkasse");

$sql = "SELECT MAX(time_of_insert) AS TOI FROM " . $db_tb_kasse_content;
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

$lastreport = "";

if ($row = $db->db_fetch_array($result)) {
    $time1      = strtotime($row['TOI']);
    $lastreport = strftime("%d.%m.%y %H:%M", $time1);
    echo "zuletzt aktualisiert am : " . $lastreport;
}

//inputform basteln
echo "<div class='doc_centered'>\n";
echo "<form name='frm'>\n";

if (strtolower($user_status) == 'admin') {
    $ally = Array();
    $sql  = "SELECT DISTINCT allianz FROM $db_tb_kasse_content;";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        $ally[] = $row['allianz'];
    }
    if (count($ally) > 1) {
        echo "<p><SELECT NAME='allianz' size=1>\n";
        foreach ($ally as $alli) {
            echo "<OPTION VALUE='" . urlencode($alli) . "'";
            if ($allianz == $alli) {
                echo " selected='selected'";
            }
            echo ">\n";
            echo $alli;

        }
        echo "</select></p>\n";
    }
}

echo "<input type='hidden' name='action' value='" . $modulname . "'>\n";
echo "<p>";
echo "<select name='type' size=1>\n";
echo "<OPTION VALUE='content'\n";
if ($type == 'content') {
    echo " selected='selected'";
}
echo ">\n";
echo "Kasseninhalt";

echo "<OPTION VALUE='payedto'";
if ($type == 'payedto') {
    echo " selected='selected'";
}
echo ">\n";
echo "Wer hat Credits bekommen?";

echo "<OPTION VALUE='payedfrom'";
if ($type == 'payedfrom') {
    echo " selected='selected'";
}
echo ">\n";
echo "Wer hat Credits ausgezahlt?";

echo "<OPTION VALUE='payedfromto'";
if ($type == 'payedfromto') {
    echo " selected='selected'";
}
echo ">\n";
echo "Wer hat von wem Credits ausgezahlt bekommen?";

echo "<OPTION VALUE='incoming'";
if ($type == 'incoming') {
    echo " selected='selected'";
}
echo ">\n";
echo "Wer hat Credits eingezahlt?";

echo "</select>\n&nbsp;&nbsp;<input type='submit' value='anzeigen'>";
echo "</p>\n<p>";
echo "Zeitraum vom <input type='text' name='fromday' size='2' maxlength='2' value='" . $fromday . "'>";
echo ". <input type='text' name='frommonth' maxlength='2' size='2' value='" . $frommonth . "'>";
echo ". <input type='text' name='fromyear' maxlength='4' size='4' value='" . $fromyear . "'>";
echo "bis zum <input type='text' name='today' maxlength='2' size='2' value='" . $today . "'>";
echo ". <input type='text' name='tomonth' maxlength='2' size='2' value='" . $tomonth . "'>";
echo ". <input type='text' name='toyear' maxlength='4' size='4' value='" . $toyear . "'></p>";

echo "</form>\n";
echo "</div>\n";

//allykassendaten abfragen und ausgeben

if ($type == 'payedto') { //ausrechnen, was jeder member so bekommen hat

    $whereclause = "AND ";
    if (isset($fromdate)) {
        $whereclause .= "time_of_pay >= '" . $fromdate . " 00:00:00' AND ";
    }
    if (isset($todate)) {
        $whereclause .= "time_of_pay <= '" . $todate . " 23:59:59' AND ";
    }
    $whereclause .= "1";

    ?>
	<table class='tablesorter' style='width:40%'>
		<thead>
			<tr class='center'>
				<th data-sorter="false" colspan='2'>
					<b>Wer hat Credits bekommen?</b>
				</th>
			</tr>
			<tr>
				<th>
					Empfänger
				</th>
				<th>
					Summe der ausgezahlten Credits
				</th>
			</tr>
		</thead>
		<tbody>
	
	<?php
		
    $sql = "SELECT payedto, sum(amount) as sumof FROM " . $db_tb_kasse_outgoing . " WHERE allianz='" . $allianz . "' " . $whereclause . " GROUP BY payedto ";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while ($row = $db->db_fetch_array($result)) {
    ?>
			<tr>
				<td>
					<?php
					echo $row['payedto'];
					?>
				</td>
				<td class='right'>
					<?php
					echo number_format($row['sumof'], 0, ',', '.');
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
			
	<?php
		

} else if ($type == 'payedfrom') { //ausrechnen, was die auszahler so ausbezahlt haben

    $whereclause = "AND ";
    if (isset($fromdate)) {
        $whereclause .= "time_of_pay >= '" . $fromdate . " 00:00:00' AND ";
    }
    if (isset($todate)) {
        $whereclause .= "time_of_pay <= '" . $todate . " 23:59:59' AND ";
    }
    $whereclause .= "1";

    ?>
	<table class='tablesorter' style='width:40%'>
		<thead>
			<tr class='center'>
				<th data-sorter="false" colspan='2'>
					<b>Wer hat Credits ausgezahlt?</b>
				</th>
			</tr>
			<tr>
				<th>
					Auszahlender
				</th>
				<th>
					Summe der ausgezahlten Credits
				</th>
			</tr>
		</thead>
		<tbody>
	
	<?php
	
	$sql = "SELECT payedfrom, sum(amount) as sumof FROM " . $db_tb_kasse_outgoing . " WHERE allianz='" . $allianz . "' " . $whereclause . " GROUP BY payedfrom ";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while ($row = $db->db_fetch_array($result)) {
		?>
			<tr>
				<td>
					<?php
					echo $row['payedfrom'];
					?>
				</td>
				<td class='right'>
					<?php
					echo number_format($row['sumof'], 0, ',', '.');
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
			
	<?php
	
} else if ($type == 'payedfromto') { //ausrechnen, was die auszahler an jeden member ausbezahlt haben

    $whereclause = "AND ";
    if (isset($fromdate)) {
        $whereclause .= "time_of_pay >= '" . $fromdate . " 00:00:00' AND ";
    }
    if (isset($todate)) {
        $whereclause .= "time_of_pay <= '" . $todate . " 23:59:59' AND ";
    }
    $whereclause .= "1";

	?>
	<table class='tablesorter' style='width:60%'>
		<thead>
			<tr class='center'>
				<th data-sorter="false" colspan='3'>
					<b>Wer hat von wem Credits ausgezahlt bekommen?</b>
				</th>
			</tr>
			<tr>
				<th>
					Auszahlender
				</th>
				<th>
					Empfänger
				</th>
				<th>
					Summe der ausgezahlten Credits
				</th>
			</tr>
		</thead>
		<tbody>
	
	<?php
	
    $sql = "SELECT payedfrom, payedto, sum(amount) as sumof FROM " . $db_tb_kasse_outgoing . " WHERE allianz='$allianz' $whereclause GROUP BY payedfrom, payedto";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while ($row = $db->db_fetch_array($result)) {     
		?>
			<tr>
				<td>
					<?php
					echo $row['payedfrom'];
					?>
				</td>
				<td>
					<?php
					echo $row['payedto'];
					?>
				</td>
				<td class='right'>
					<?php
					echo number_format($row['sumof'], 0, ',', '.');
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
			
	<?php
		
} else if ($type == 'content') { //anzeigen, wie viel wann in der kasse war


    $whereclause = "AND ";
    if (isset($fromdate)) {
        $whereclause .= "time_of_insert >= '" . $fromdate . " 00:00:00' AND ";
    }
    if (isset($todate)) {
        $whereclause .= "time_of_insert <= '" . $todate . " 23:59:59' AND ";
    }
    $whereclause .= "1";

	?>
	<table class='tablesorter' style='width:40%'>
		<thead>
			<tr class='center'>
				<th data-sorter="false" colspan='2'>
					<b>Kasseninhalt</b>
				</th>
			</tr>
			<tr>
				<th>
					Datum
				</th>
				<th>
					Inhalt der Allianzkasse
				</th>
			</tr>
		</thead>
		<tbody>
	
	<?php
	
    $sql = "SELECT amount, time_of_insert FROM " . $db_tb_kasse_content . " WHERE allianz='$allianz' $whereclause ORDER BY time_of_insert ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while ($row = $db->db_fetch_array($result)) {
        $time  = strtotime($row['time_of_insert']);
        $time1 = strftime("%d.%m.%y %H:%M", $time);
        
		?>
			<tr>
				<td>
					<?php
					echo $time1;
					?>
				</td>
				<td class='right'>
					<?php
					echo number_format($row['amount'], 2, ',', '.');
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
			
	<?php
		
} else if ($type == 'incoming') { //anzeigen, wer wie viel eingezahlt hat


    $whereclause = "AND ";
    if (isset($fromdate)) {
        $whereclause .= "time_of_insert >= '" . $fromdate . " 00:00:00' AND ";
    }
    if (isset($todate)) {
        $whereclause .= "time_of_insert <= '" . $todate . " 23:59:59' AND ";
    }
    $whereclause .= "1";

	?>
	<table class='tablesorter' style='width:40%'>
		<thead>
			<tr class='center'>
				<th data-sorter="false" colspan='2'>
					<b>Wer hat Credits eingezahlt?</b>
				</th>
			</tr>
			<tr>
				<th>
					Einzahler
				</th>
				<th>
					Summe der eingezahlten Credits
				</th>
			</tr>
		</thead>
		<tbody>
	
	<?php
	
    $sql = "SELECT user, sum(amount) as sumof FROM " . $db_tb_kasse_incoming . " WHERE allianz='$allianz' $whereclause GROUP BY user";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while ($row = $db->db_fetch_array($result)) {
        ?>
			<tr>
				<td>
					<?php
					echo $row['user'];
					?>
				</td>
				<td class='right'>
					<?php
					echo number_format($row['sumof'], 2, ',', '.');
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
			
	<?php
}