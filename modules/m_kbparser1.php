<?php
/*****************************************************************************
 * m_kbparser1.php                                                           *
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
$modulname = "m_kbparser1";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "KBParser2";

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
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc = "KB-Parser von Blob im Original-Look mit einer vielfältigen Ausgabe im BBCode";

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

    $actionparamters = "";
    insertMenuItem($menu, $submenu, $modultitle, $modulstatus, $actionparamters);
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
// Dieser Abschnitt wird nur ausgeführt wenn das Modul mit dem Parameter "install" aufgerufen wurde.
// Beispiel des Aufrufs:
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" natürlich deinen Server angeben und default durch den Dateinamen des Moduls ersetzen.
//
if (!empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
    if ($user_status != "admin") {
        die('Hacking attempt...');
    }

    echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")</div>\n";

    require_once './includes/menu_fn.php';

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

//Komische Funktion die rekrusiv ein Array mit den Werten füllt.

function makeArray($Bericht)
{
    global $line;
    $rt = array();

    while ($line < count($Bericht)) { //Erkennen der Elemente
        if (strpos($Bericht[$line], "<?") !== false) {  //Überspringen der XML information
            $line++;
        } elseif (strpos($Bericht[$line], "</") !== false) {    // Closer Tag, kein Überprüfung auf Korrektheit
            $line++;
            break; //Abschließen des momentanen Wertes
        } elseif (strpos($Bericht[$line], "value=") !== false) {    // Endwert
            $temp = str_replace("<", "", $Bericht[$line]);
            $temp = str_replace("'", "", $temp);
            $temp = str_replace("\"", "", $temp);
            $temp = str_replace("/>", "", $temp);
            $temp = explode("value=", $temp);
            $name = trim($temp[0]); // Name des Elementes

            while (empty($rt[$name]) == false) {
                $name = $name . "1";
            }

            $rt[$name] = trim($temp[1]); // Wert des Elementes
            $line++;
        } elseif (strpos($Bericht[$line], "<") !== false) { // das sollte jetzt ein neues Element ohne Wert sein
            $name = str_replace("<", "", $Bericht[$line]);
            $name = trim(str_replace(">", "", $name));
            while (empty($rt[$name]) == false) {
                $name = $name . "1";
            }
            $line++;
            $rt[$name] = makeArray($Bericht);
        } else {    // keine Verwertbare Zeile
            $line++;
        }
    }

    return $rt; // Array zurückgeben

}


// Eienen String mit Fett, Kursiv, Unterstrichen und Farbe versehen

function makeString($string, $fett, $kursiv, $unterstrichen, $farbe)
{
    if ($fett) {
        $string = "[b]" . $string . "[/b]";
    }
    if ($kursiv) {
        $string = "[i]" . $string . "[/i]";
    }
    if ($unterstrichen) {
        $string = "[u]" . $string . "[/u]";
    }
    if ($farbe != "keine") {
        $string = "[color=" . $farbe . "]" . $string . "[/color]";
    }

    return $string;
}

//Und da fängts an.

$parsstatus = getVar('parsstatus');

doc_title("KB Parser für BB-Code");

if (empty($parsstatus)) { //Angabe für die Datei
    echo "<form method='post'>";
    echo "<input type='hidden' name='action' value='".$modulname."'>";
    echo "<input type='hidden' name='parsstatus' value='read'>";
    echo <<< EOT
	 <table border="1" cellpadding="2" cellspacing="1" rules="none" width="95%">
  		<tr>
    		<td class="left" width="50">
      		KB-Link:
    		</td>
    		<td  class="center">
      			<input type="text" name="KBLink" autofocus="autofocus" placeholder="KB-Link" style="width:95%;">
    		</td>
    		<td  class="center" width="50">
      			<input type="submit" value="Go" width="45" style="color:#FFFFFF; background-color: #00688B;">
    		</td>
  		</tr>
	 </table>
	 </form>
EOT;
    if (empty($KBLink)) {
        echo "KB-Link nicht vergessen :) ";
    }
}


if ($parsstatus === "read") {   // KB einlesen und für die Formatierung ausgeben

    $KBLink = trim(getVar('KBLink'));
    if (preg_match('#^https?://www.?\.icewars\.de/portal/kb/de/kb\.php\?id=[\d]+.{1,5}md_hash=[\w]{32}$#', $KBLink) !== 1) {
        echo "<div class='system_error'>Keinen KB-Link eingetragen!</div>";
        $parsstatus = "";
    } else {
        $KBLink  = $KBLink . "&typ=xml";
        $KBLink  = str_replace("&amp;", "&", $KBLink);
        $Bericht = file_get_contents_utf8($KBLink, 'r');
        if (empty($Bericht)) {
            exit('Fehler beim Laden des Berichts!');
        }
        $Bericht = explode("\n", $Bericht);

        $line = 0;

        $KBdata = makeArray($Bericht); // komische verschachtelte Arrays

        echo "<form method='post'>";
        echo "<input type='hidden' name='action' value='".$modulname."'>";
        echo "<input type='hidden' name='parsstatus' value='write'>";
        echo "<table class='table_format' style='width: 95%;'>";

        // Optionen
        echo "  <tr>";
        echo "     <td class='center' colspan=8 >";
        echo "        Optionen: <input type='checkbox' name='optionQuote' value='on' checked>Quoten";
        // echo "        / <input type='checkbox' name='optionTab' value='on' checked>Tabellen";
        echo "        / <input type='checkbox' name='optionAlign' value='on' checked>Ausrichtung";
        echo "        / <input type='checkbox' name='optionColor' value='on' checked>Farbe";
        echo "        / <input type='checkbox' name='optionForm' value='on' checked>Format";
        echo "        / <input type='checkbox' name='optionKuerzen' value='on' checked>Schiffnamen kürzen";
        echo "        / <input type='checkbox' name='optionHr' value='on' checked>horizonale Linien";
        echo "        / <input type='checkbox' name='optionLink' value='on'>KB-Link in Quote-Tags";
        echo "        / <input type='checkbox' name='optionColspan' value='on' checked>verkürzte Colspans ([td=x])";
        // echo "       / <input type='checkbox' name='optionHtml' value='on' >HTML";
        echo "     </td>";
        echo "  </tr>";


        echo "  <tr>";
        echo "     <td class='center' colspan='8'>";
        echo "       <input type='submit' value='BB-Code gleich generieren' class='btn'>";
        echo "     </td>";
        echo "  </tr>";


        // Kampf auf dem Planeten
        echo "  <tr>";
        echo "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
        echo "        <input type='hidden' name='mainline' type='text' value='" . urlencode("Kampf auf dem Planeten [b]" . $KBdata['kampf']['plani_data']['koordinaten']['string'] . "[/b]. Besitzer ist [b]" . $KBdata['kampf']['plani_data']['user']['name'] . " [" . $KBdata['kampf']['plani_data']['user']['allianz_tag'] . "][/b]") . "'>";
        echo "        Kampf auf dem Planeten <b>" . $KBdata['kampf']['plani_data']['koordinaten']['string'] . ". Besitzer ist <b>" . $KBdata['kampf']['plani_data']['user']['name'] . " [" . $KBdata['kampf']['plani_data']['user']['allianz_tag'] . "]</b>";
        echo "     </td>";
        echo "  </tr>";
        // Der Kampf mit einem Sieg für den ...
        //setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
        setlocale(LC_ALL, 'de_DE.utf8');
        echo "  <tr>";
        echo "     <td class='windowbg1 left' colspan='8'>";
        echo "Die Schlacht endete " . strftime(" am <b>%d.%m.%Y</b> um <b>%H:%M:%S</b>", $KBdata['kampf']['timestamp']) . " mit einem Sieg für den ";
        if ($KBdata['kampf']['resultat']['id'] == 1) {
            echo "<span style='color:green'><b>Angreifer</b></span>.";
        } else {
            echo "<span style='color:red'><b>Verteidiger</b></span>.";
        }
        echo "        <input type='hidden' name='dateline' type='text' value='" . urlencode("Die Schlacht endete " . strftime("am [b]%d.%m.%Y[/b] um [b]%H:%M:%S[/b] ", $KBdata['kampf']['timestamp']) . " mit einem Sieg für den ");
        if ($KBdata['kampf']['resultat']['id'] == 1) {
            echo urlencode("[color=green][b]Angreifer[/b][/color].");
        } else {
            echo urlencode("[color=red][b]Verteidiger[/b][/color].");
        }
        echo "'>";
        echo "     </td>";
        echo "  </tr>";

        // Angreifende Flotten
        $i = 1;
        foreach ($KBdata['kampf']['flotten_att'] as $user) {
            echo "  <tr>";
            echo "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            echo "       Angreifende Flotte von <b>" . $user['name'] . " [" . $user['allianz_tag'] . "]</b>, Startplanet ist <b>" . $user['startplanet']['koordinaten']['string'] . "</b>.";
            echo "       <input type='hidden' name='atter" . $i . "' type='text' value='" . urlencode("[b]" . $user['name'] . " [" . $user['allianz_tag'] . "][/b] von " . $user['startplanet']['koordinaten']['string']) . "'>";
            echo "     </td>";
            echo "  </tr>";
            if (empty($user['bloedsinn']['kaffee']) == false) {
                echo "  <tr>";
                echo "     <td class='left' colspan='8'>";
                echo "        Die Flotte brachte Kaffee und Kuchen mit.";
                echo "        <input type='hidden' name='atterkuchen" . $i . "' type='text' value='1'>";
                echo "     </td>";
                echo "  </tr>";
            }
            if (empty($user['bloedsinn']['lollis']) == false) {
                echo "  <tr>";
                echo "     <td class='left' colspan='8'>";
                echo "        Diese wilden Barbarben haben unseren kleinen Kindern die Lollis geklaut!! Das schreit gradezu nach Rache.";
                echo "        <input type='hidden' name='atterlollies" . $i . "' type='text' value='1'>";
                echo "     </td>";
                echo "  </tr>";
            }
            if (empty($user['bloedsinn']['msg']) == false) {
                echo "  <tr>";
                echo "     <td class='left' colspan='8'>";
                echo "        Der Kommandant der angreifenden Flotte überbrachte folgende Botschaft:<br>" . $user['bloedsinn']['msg'];
                echo "        <input type='hidden' name='attermsg" . $i . "' type='text' value='" . urlencode("Der Kommandant der angreifenden Flotte überbrachte folgende Botschaft:[/td][/tr][tr][td colspan=4][color=brown][i]" . $user['bloedsinn']['msg'] . "[/i][/color]'>");
                echo "     </td>";
                echo "  </tr>";
            }
            //Schiffe
            echo "  <tr>";
            echo "    <td class='center'>Schiffname</td>";
            echo "    <td class='center'>Anzahl</td>";
            echo "    <td class='center'>Zerstört</td>";
            echo "    <td class='center'>Überlebend</td>";
            echo "    <td class='center bold'>F</td>";
            echo "    <td class='center italic'>K</td>";
            echo "    <td class='center underline'>U</td>";
            echo "    <td class='center'>Farbe</td>";
            echo "  </tr>";
            $j = 1;
            foreach ($user['schiffe'] as $schiffe) {
                echo "  <tr>";
                echo "    <td class='left'>";
                echo $schiffe['name'];
                echo "      <input type='hidden' name='atterschiffname" . $i . "_" . $j . "' type='text' value='" . urlencode($schiffe['name']) . "'>";
                echo "    </td>";
                echo "    <td class='right'>";
                echo number_format($schiffe['anzahl_start'], 0, ',', '.');
                echo "      <input type='hidden' name='atterschiffstart" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_start'], 0, ",", ".") . "'>";
                echo "    </td>";
                echo "    <td class='right'>";
                echo number_format($schiffe['anzahl_verlust'], 0, ',', '.');
                echo "      <input type='hidden' name='atterschiffweg" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_verlust'], 0, ",", ".") . "'>";
                echo "    </td>";
                echo "    <td class='right'>";
                echo number_format($schiffe['anzahl_ende'], 0, ',', '.');
                echo "      <input type='hidden' name='atterschiffende" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_ende'], 0, ",", ".") . "'>";
                echo "    </td>";
                echo "    <td class='center'>";
                echo "      <input type='checkbox' name='Attformf" . $i . "_" . $j . "' value='f'>";
                echo "    </td>";
                echo "    <td class='center'>";
                echo "      <input type='checkbox' name='Attformk" . $i . "_" . $j . "' value='k'>";
                echo "    </td>";
                echo "    <td class='center'>";
                echo "      <input type='checkbox' name='Attformu" . $i . "_" . $j . "' value='u'>";
                echo "    </td>";
                echo "    <td class='center'>";
                echo "      <select name='Attformc" . $i . "_" . $j . "'>";
                echo "         <option>keine</option>";
                echo "         <option style='color:red' value='red'>rot</option>";
                echo "         <option style='color:yellow' value='yellow'>gelb</option>";
                echo "         <option style='color:pink' value='pink'>pink</option>";
                echo "         <option style='color:green' value='green'>grün</option>";
                echo "         <option style='color:orange' value='orange'>orange</option>";
                echo "         <option style='color:purple' value='purple'>violett</option>";
                echo "         <option style='color:blue' value='blue'>blau</option>";
                echo "         <option style='color:beige' value='beige'>beige</option>";
                echo "         <option style='color:brown' value='brown'>braun</option>";
                echo "         <option style='color:teal' value='teal'>türkis</option>";
                echo "         <option style='color:navy' value='navy'>dunkelblau</option>";
                echo "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                echo "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                echo "       </select>";
                echo "    </td>";
                echo "  </tr>";

                $j++;
            }
            $i++;
        }

        // Planetare Deff und Schiffe
        $i = 1;
        foreach ($KBdata['kampf']['pla_def'] as $user) {
            echo "  <tr>";
            echo "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;'>";
            echo "       Verteidiger ist <b>" . $user['name'] . " [" . $user['allianz_tag'] . "]</b>";
            echo "       <input type='hidden' name='pladeffer" . $i . "' type='text' value='" . urlencode("[b]" . $user['name'] . " [" . $user['allianz_tag'] . "][/b]") . "'>";
            echo "     </td>";
            echo "  </tr>";

            //stationäre
            if (empty($user['defence']) == false) {
                echo "  <tr>";
                echo "    <td class='center'>Verteidigungsanlage</td>";
                echo "    <td class='center'>Anzahl</td>";
                echo "    <td class='center'>Zerstört</td>";
                echo "    <td class='center'>Überlebend</td>";
                echo "    <td class='center bold'>F</td>";
                echo "    <td class='center italic'>K</td>";
                echo "    <td class='center underline'>U</td>";
                echo "    <td class='center'>Farbe</td>";
                echo "  </tr>";
                $j = 1;
                foreach ($user['defence'] as $schiffe) {
                    echo "  <tr>";
                    echo "    <td class='left' >";
                    echo $schiffe['name'];
                    echo "      <input type='hidden' name='pladefturm" . $i . "_" . $j . "' type='text' value='" . urlencode($schiffe['name']) . "'>";
                    echo "    </td>";
                    echo "    <td class='right' >";
                    echo number_format($schiffe['anzahl_start'], 0, ',', '.');
                    echo "      <input type='hidden' name='pladefturmstart" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_start'], 0, ",", ".") . "'>";
                    echo "    </td>";
                    echo "    <td class='right' >";
                    echo number_format($schiffe['anzahl_verlust'], 0, ',', '.');
                    echo "      <input type='hidden' name='pladefturmweg" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_verlust'], 0, ",", ".") . "'>";
                    echo "    </td>";
                    echo "    <td class='right' >";
                    echo number_format($schiffe['anzahl_ende'], 0, ',', '.');
                    echo "      <input type='hidden' name='pladefturmende" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_ende'], 0, ",", ".") . "'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='Pladefturmformf" . $i . "_" . $j . "' value='f'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='Pladefturmformk" . $i . "_" . $j . "' value='k'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='Pladefturmformu" . $i . "_" . $j . "' value='u'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <select name='Pladefturmformc" . $i . "_" . $j . "'>";
                    echo "         <option>keine</option>";
                    echo "         <option style='color:red' value='red'>rot</option>";
                    echo "         <option style='color:yellow' value='yellow'>gelb</option>";
                    echo "         <option style='color:pink' value='pink'>pink</option>";
                    echo "         <option style='color:green' value='green'>grün</option>";
                    echo "         <option style='color:orange' value='orange'>orange</option>";
                    echo "         <option style='color:purple' value='purple'>violett</option>";
                    echo "         <option style='color:blue' value='blue'>blau</option>";
                    echo "         <option style='color:beige' value='beige'>beige</option>";
                    echo "         <option style='color:brown' value='brown'>braun</option>";
                    echo "         <option style='color:teal' value='teal'>türkis</option>";
                    echo "         <option style='color:navy' value='navy'>dunkelblau</option>";
                    echo "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                    echo "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                    echo "       </select>";
                    echo "    </td>";
                    echo "  </tr>";

                    $j++;
                }
            }

            //flotte
            if (empty($user['schiffe']) == false) {
                echo "  <tr>";
                echo "    <td class='center'>Schiffnamen</td>";
                echo "    <td class='center'>Anzahl</td>";
                echo "    <td class='center'>Zerstört</td>";
                echo "    <td class='center'>Überlebend</td>";
                echo "    <td class='center bold'>F</td>";
                echo "    <td class='center italic'>K</td>";
                echo "    <td class='center underline'>U</td>";
                echo "    <td class='center'>Farbe</td>";
                echo "  </tr>";
                $j = 1;
                foreach ($user['schiffe'] as $schiffe) {
                    echo "  <tr>";
                    echo "    <td class='left' >";
                    echo $schiffe['name'];
                    echo "      <input type='hidden' name='pladefschiff" . $i . "_" . $j . "' type='text' value='" . urlencode($schiffe['name']) . "'>";
                    echo "    </td>";
                    echo "    <td class='right' >";
                    echo number_format($schiffe['anzahl_start'], 0, ',', '.');
                    echo "      <input type='hidden' name='pladefschiffstart" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_start'], 0, ",", ".") . "'>";
                    echo "    </td>";
                    echo "    <td class='right' >";
                    echo number_format($schiffe['anzahl_verlust'], 0, ',', '.');
                    echo "      <input type='hidden' name='pladefschiffweg" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_verlust'], 0, ",", ".") . "'>";
                    echo "    </td>";
                    echo "    <td class='right' >";
                    echo number_format($schiffe['anzahl_ende'], 0, ',', '.');
                    echo "      <input type='hidden' name='pladefschiffende" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_ende'], 0, ",", ".") . "'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='Pladefschiffformf" . $i . "_" . $j . "' value='f'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='Pladefschiffformk" . $i . "_" . $j . "' value='k'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='Pladefschiffformu" . $i . "_" . $j . "' value='u'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <select name='Pladefschiffformc" . $i . "_" . $j . "'>";
                    echo "         <option>keine</option>";
                    echo "         <option style='color:red' value='red'>rot</option>";
                    echo "         <option style='color:yellow' value='yellow'>gelb</option>";
                    echo "         <option style='color:pink' value='pink'>pink</option>";
                    echo "         <option style='color:green' value='green'>grün</option>";
                    echo "         <option style='color:orange' value='orange'>orange</option>";
                    echo "         <option style='color:purple' value='purple'>violett</option>";
                    echo "         <option style='color:blue' value='blue'>blau</option>";
                    echo "         <option style='color:beige' value='beige'>beige</option>";
                    echo "         <option style='color:brown' value='brown'>braun</option>";
                    echo "         <option style='color:teal' value='teal'>türkis</option>";
                    echo "         <option style='color:navy' value='navy'>dunkelblau</option>";
                    echo "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                    echo "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                    echo "       </select>";
                    echo "    </td>";
                    echo "  </tr>";

                    $j++;
                }
            }
            $i++;
        }

        // Deffende Flotten
        if (empty($KBdata['kampf']['flotten_def']) == false) {
            $i = 1;
            foreach ($KBdata['kampf']['flotten_def'] as $user) {
                echo "  <tr>";
                echo "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
                echo "       Verteidigende Flotte von <b>" . $user['name'] . " [" . $user['allianz_tag'] . "]</b>";
                echo "       <input type='hidden' name='deffer" . $i . "' type='text' value='" . urlencode("[b]" . $user['name'] . " [" . $user['allianz_tag'] . "][/b]") . "'>";
                echo "     </td>";
                echo "  </tr>";
                //schiffe
                echo "  <tr>";
                echo "    <td class='center'>Schiffnamen</td>";
                echo "    <td class='center'>Anzahl</td>";
                echo "    <td class='center'>Zerstört</td>";
                echo "    <td class='center'>Überlebend</td>";
                echo "    <td class='center bold'>F</td>";
                echo "    <td class='center italic'>K</td>";
                echo "    <td class='center underline'>U</td>";
                echo "    <td class='center'>Farbe</td>";
                echo "  </tr>";
                $j = 1;
                foreach ($user['schiffe'] as $schiffe) {
                    echo "  <tr>";
                    echo "    <td class='left' >";
                    echo $schiffe['name'];
                    echo "      <input type='hidden' name='defferschiffname" . $i . "_" . $j . "' type='text' value='" . urlencode($schiffe['name']) . "'>";
                    echo "    </td>";
                    echo "    <td class='right' >";
                    echo number_format($schiffe['anzahl_start'], 0, ',', '.');
                    echo "      <input type='hidden' name='defferschiffstart" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_start'], 0, ",", ".") . "'>";
                    echo "    </td>";
                    echo "    <td class='right' >";
                    echo number_format($schiffe['anzahl_verlust'], 0, ',', '.');
                    echo "      <input type='hidden' name='defferschiffweg" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_verlust'], 0, ",", ".") . "'>";
                    echo "    </td>";
                    echo "    <td class='right' >";
                    echo number_format($schiffe['anzahl_ende'], 0, ',', '.');
                    echo "      <input type='hidden' name='defferschiffende" . $i . "_" . $j . "' type='text' value='" . number_format($schiffe['anzahl_ende'], 0, ",", ".") . "'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='Deffformf" . $i . "_" . $j . "' value='f'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='Deffformk" . $i . "_" . $j . "' value='k'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='Deffformu" . $i . "_" . $j . "' value='u'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <select name='Deffformc" . $i . "_" . $j . "'>";
                    echo "         <option>keine</option>";
                    echo "         <option style='color:red' value='red'>rot</option>";
                    echo "         <option style='color:yellow' value='yellow'>gelb</option>";
                    echo "         <option style='color:pink' value='pink'>pink</option>";
                    echo "         <option style='color:green' value='green'>grün</option>";
                    echo "         <option style='color:orange' value='orange'>orange</option>";
                    echo "         <option style='color:purple' value='purple'>violett</option>";
                    echo "         <option style='color:blue' value='blue'>blau</option>";
                    echo "         <option style='color:beige' value='beige'>beige</option>";
                    echo "         <option style='color:brown' value='brown'>braun</option>";
                    echo "         <option style='color:teal' value='teal'>türkis</option>";
                    echo "         <option style='color:navy' value='navy'>dunkelblau</option>";
                    echo "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                    echo "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                    echo "       </select>";
                    echo "    </td>";
                    echo "  </tr>";

                    $j++;
                }
                $i++;
            }
        }

        // Verluste Angreifer
        if (empty($KBdata['kampf']['resverluste']['att']) == false) {
            echo "  <tr>";
            echo "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            echo "       <b>Verluste des Angreifers</b>";
            echo "     </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td class='center'>Ressource</td>";
            echo "    <td class='center' colspan='3'>Anzahl</td>";
            echo "    <td class='center bold'>F</td>";
            echo "    <td class='center italic'>K</td>";
            echo "    <td class='center underline'>U</td>";
            echo "    <td class='center'>Farbe</td>";
            echo "  </tr>";
            foreach ($KBdata['kampf']['resverluste']['att'] as $loos) {
                echo "  <tr>";
                echo "     <td class='left'  >";
                echo $loos['name'];
                echo "     </td>";
                echo "     <td colspan='3' class='right'>";
                echo number_format($loos['anzahl'], 0, ",", ".");
                echo "       <input type='hidden' name='attverlust" . $loos['id'] . "' type='text' value='" . number_format($loos['anzahl'], 0, ",", ".") . "'>";
                echo "     </td>";
                echo "    <td class='center' >";
                echo "      <input type='checkbox' name='attverlustformf" . $loos['id'] . "' value='f'>";
                echo "    </td>";
                echo "    <td class='center' >";
                echo "      <input type='checkbox' name='attverlustformk" . $loos['id'] . "' value='k'>";
                echo "    </td>";
                echo "    <td class='center' >";
                echo "      <input type='checkbox' name='attverlustformu" . $loos['id'] . "' value='u'>";
                echo "    </td>";
                echo "    <td class='center' >";
                echo "      <select name='attverlustformc" . $loos['id'] . "'>";
                echo "         <option>keine</option>";
                echo "         <option style='color:red' value='red'>rot</option>";
                echo "         <option style='color:yellow' value='yellow'>gelb</option>";
                echo "         <option style='color:pink' value='pink'>pink</option>";
                echo "         <option style='color:green' value='green'>grün</option>";
                echo "         <option style='color:orange' value='orange'>orange</option>";
                echo "         <option style='color:purple' value='purple'>violett</option>";
                echo "         <option style='color:blue' value='blue'>blau</option>";
                echo "         <option style='color:beige' value='beige'>beige</option>";
                echo "         <option style='color:brown' value='brown'>braun</option>";
                echo "         <option style='color:teal' value='teal'>türkis</option>";
                echo "         <option style='color:navy' value='navy'>dunkelblau</option>";
                echo "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                echo "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                echo "       </select>";
                echo "    </td>";
                echo "  </tr>";
            }
        }

        // Verluste Deffer
        if (empty($KBdata['kampf']['resverluste']['def']) == false) {
            echo "  <tr>";
            echo "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            echo "       <b>Verluste des Verteidigers</b>";
            echo "     </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td class='center'>Ressource</td>";
            echo "    <td class='center' colspan='3'>Anzahl</td>";
            echo "    <td class='center bold'>F</td>";
            echo "    <td class='center italic'>K</td>";
            echo "    <td class='center underline'>U</td>";
            echo "    <td class='center'>Farbe</td>";
            echo "  </tr>";
            foreach ($KBdata['kampf']['resverluste']['def'] as $loos) {
                echo "  <tr>";
                echo "     <td class='left' >";
                echo $loos['name'];
                echo "     </td>";
                echo "     <td colspan='3' class='right' >";
                echo number_format($loos['anzahl'], 0, ",", ".");
                echo "       <input type='hidden' name='defverlust" . $loos['id'] . "' type='text' value='" . number_format($loos['anzahl'], 0, ",", ".") . "'>";
                echo "     </td>";
                echo "    <td class='center' >";
                echo "      <input type='checkbox' name='defverlustformf" . $loos['id'] . "' value='f'>";
                echo "    </td>";
                echo "    <td class='center' >";
                echo "      <input type='checkbox' name='defverlustformk" . $loos['id'] . "' value='k'>";
                echo "    </td>";
                echo "    <td class='center' >";
                echo "      <input type='checkbox' name='defverlustformu" . $loos['id'] . "' value='u'>";
                echo "    </td>";
                echo "    <td class='center' >";
                echo "      <select name='defverlustformc" . $loos['id'] . "'>";
                echo "         <option>keine</option>";
                echo "         <option style='color:red' value='red'>rot</option>";
                echo "         <option style='color:yellow' value='yellow'>gelb</option>";
                echo "         <option style='color:pink' value='pink'>pink</option>";
                echo "         <option style='color:green' value='green'>grün</option>";
                echo "         <option style='color:orange' value='orange'>orange</option>";
                echo "         <option style='color:purple' value='purple'>violett</option>";
                echo "         <option style='color:blue' value='blue'>blau</option>";
                echo "         <option style='color:beige' value='beige'>beige</option>";
                echo "         <option style='color:brown' value='brown'>braun</option>";
                echo "         <option style='color:teal' value='teal'>türkis</option>";
                echo "         <option style='color:navy' value='navy'>dunkelblau</option>";
                echo "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                echo "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                echo "       </select>";
                echo "    </td>";
                echo "  </tr>";
            }
        }

        // Plünderungen
        if (empty($KBdata['kampf']['pluenderung']) == false) {

            echo "  <tr>";
            echo "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            echo "       <b>Es wurden folgende Ressourcen geplündert</b>";
            echo "     </td>";
            echo "  </tr>";
            echo "  <tr>";
            echo "    <td class='center'>Ressource</td>";
            echo "    <td class='center'>Anzahl</td>";
            echo "    <td class='center bold'>F</td>";
            echo "    <td class='center italic'>K</td>";
            echo "    <td class='center underline'>U</td>";
            echo "    <td class='center'>Farbe</td>";
            echo "  </tr>";
            foreach ($KBdata['kampf']['pluenderung'] as $loos) {
                echo "  <tr>";
                echo "     <td class='left' >";
                echo $loos['name'];
                echo "     </td>";
                echo "     <td colspan='3' class='right'>";
                echo number_format($loos['anzahl'], 0, ",", ".");
                echo "       <input type='hidden' name='weg" . $loos['id'] . "' type='text' value='" . number_format($loos['anzahl'], 0, ",", ".") . "'>";
                echo "     </td>";
                echo "    <td class='center' >";
                echo "      <input type='checkbox' name='wegformf" . $loos['id'] . "' value='f'>";
                echo "    </td>";
                echo "    <td class='center' >";
                echo "      <input type='checkbox' name='wegformk" . $loos['id'] . "' value='k'>";
                echo "    </td>";
                echo "    <td class='center' >";
                echo "      <input type='checkbox' name='wegformu" . $loos['id'] . "' value='u'>";
                echo "    </td>";
                echo "    <td class='center' >";
                echo "      <select name='wegformc" . $loos['id'] . "'>";
                echo "         <option>keine</option>";
                echo "         <option style='color:red' value='red'>rot</option>";
                echo "         <option style='color:yellow' value='yellow'>gelb</option>";
                echo "         <option style='color:pink' value='pink'>pink</option>";
                echo "         <option style='color:green' value='green'>grün</option>";
                echo "         <option style='color:orange' value='orange'>orange</option>";
                echo "         <option style='color:purple' value='purple'>violett</option>";
                echo "         <option style='color:blue' value='blue'>blau</option>";
                echo "         <option style='color:beige' value='beige'>beige</option>";
                echo "         <option style='color:brown' value='brown'>braun</option>";
                echo "         <option style='color:teal' value='teal'>türkis</option>";
                echo "         <option style='color:navy' value='navy'>dunkelblau</option>";
                echo "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                echo "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                echo "       </select>";
                echo "    </td>";
                echo "  </tr>";
            }
        }

        // Bombing
        if (empty($KBdata['kampf']['bomben']) == false) {
            echo "  <tr>";
            echo "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;'>";
            echo "       Der Planet wurde von <b>" . $KBdata['kampf']['bomben']['user']['name'] . "</b>";
            echo "       <input type='hidden' name='bomb1' type='text' value='" . urlencode("Der Planet wurde von [b]" . $KBdata['kampf']['bomben']['user']['name'] . "[/b]") . "'>";
            echo "       <input name='bomb2' type='text' value='bombadiert.' size='30' >";
            echo "     </td>";
            echo "  </tr>";
            if (empty($KBdata['kampf']['bomben']['bombentrefferchance']) == false) // Bevölkerung
            {
                echo "  <tr>";
                echo "     <td class='left' colspan='8'>";
                if ($KBdata['kampf']['bomben']['bombentrefferchance'] == 100) {
                    echo "      <input name='bombtreff1' type='text' value='Es gab klare Sicht für die Bomberpiloten, die Trefferchance lag bei' size='70'>";
                } elseif ($KBdata['kampf']['bomben']['bombentrefferchance'] > 75) {
                    echo "      <input name='bombtreff1' type='text' value='Ein paar kleine Pfefferminzwölkchen trübten den Himmel, die Trefferchance lag bei' size='70'>";
                } elseif ($KBdata['kampf']['bomben']['bombentrefferchance'] > 25) {
                    echo "      <input name='bombtreff1' type='text' value='Pfefferminzwolken bedeckten den Himmel, die Trefferchance lag bei' size='70'>";
                } else {
                    echo "      <input name='bombtreff1' type='text' value='Alles war mit Pfefferminzwolken vernebelt. Die Bomberpiloten haben kaum was gesehen, die Trefferchance lag bei' size='70'>";
                }
                echo " <b>" . $KBdata['kampf']['bomben']['bombentrefferchance'] . "%</b>";
                echo "      <input type='hidden' name='bombtreff2' type='text' value='" . urlencode(" [b]" . $KBdata['kampf']['bomben']['bombentrefferchance'] . "%[/b]") . "'>";
                echo "      <input name='bombtreff3' type='text' value='.' size='30'>";
                echo "     </td>";
                echo "  </tr>";
            }

            if (empty($KBdata['kampf']['bomben']['basis_zerstoert']) == false) {    //Basis
                if ($KBdata['kampf']['bomben']['basis_zerstoert'] == 1) {
                    echo "  <tr>";
                    echo "     <td class='left' colspan='8'>";
                    echo "      <input name='bomb3' type='text' value='Irgendwer drückte aus Versehen auf den Selbstzerstörungsknopf. Damit endet die ruhmvolle Existenz der Basis. Plop.' size='100' >";
                    echo "     </td>";
                    echo "  </tr>";
                } else {
                    echo "  <tr>";
                    echo "     <td class='left' colspan='8'>";
                    echo "      <input name='bomb3' type='text' value='Der Wachhabende Offizier konnte den Selbstzerstörungsknopf nicht finden, daher steht die Basis noch.' size='100' >";
                    echo "     </td>";
                    echo "  </tr>";
                }
            } elseif (empty($KBdata['kampf']['bomben']['geb_zerstoert'])) {     //keine Gebäude
                echo "  <tr>";
                echo "     <td class='left' colspan='8'>";
                echo "      <input name='bomb3' type='text' value='Es wurden keine Gebäude zerstört. Haha.' size='75'>";
                echo "     </td>";
                echo "  </tr>";
            } else {    //mit Gebäuden
                echo "  <tr>";
                echo "    <td class='center'>Gebäude</td>";
                echo "    <td class='center' colspan='3'>Anzahl</td>";
                echo "    <td class='center bold'>F</td>";
                echo "    <td class='center italic'>K</td>";
                echo "    <td class='center underline'>U</td>";
                echo "    <td class='center'>Farbe</td>";
                echo "  </tr>";

                $i = 1;
                foreach ($KBdata['kampf']['bomben']['geb_zerstoert'] as $loos) {
                    echo "  <tr>";
                    echo "     <td class='left' >";
                    echo $loos['name'];
                    echo "       <input type='hidden' name='bombgeb" . $i . "' type='text' value='" . urlencode($loos['name']) . "'>";
                    echo "     </td>";
                    echo "     <td colspan='3' class='right'>";
                    echo number_format($loos['anzahl'], 0, ',', '.');
                    echo "       <input type='hidden' name='bombgebanz" . $i . "' type='text' value='" . number_format($loos['anzahl'], 0, ",", ".") . "'>";
                    echo "     </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='bombgebformf" . $i . "' value='f'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='bombgebformk" . $i . "' value='k'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <input type='checkbox' name='bombgebformu" . $i . "' value='u'>";
                    echo "    </td>";
                    echo "    <td class='center' >";
                    echo "      <select name='bombgebformc" . $i . "'>";
                    echo "         <option>keine</option>";
                    echo "         <option style='color:red' value='red'>rot</option>";
                    echo "         <option style='color:yellow' value='yellow'>gelb</option>";
                    echo "         <option style='color:pink' value='pink'>pink</option>";
                    echo "         <option style='color:green' value='green'>grün</option>";
                    echo "         <option style='color:orange' value='orange'>orange</option>";
                    echo "         <option style='color:purple' value='purple'>violett</option>";
                    echo "         <option style='color:blue' value='blue'>blau</option>";
                    echo "         <option style='color:beige' value='beige'>beige</option>";
                    echo "         <option style='color:brown' value='brown'>braun</option>";
                    echo "         <option style='color:teal' value='teal'>türkis</option>";
                    echo "         <option style='color:navy' value='navy'>dunkelblau</option>";
                    echo "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                    echo "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                    echo "       </select>";
                    echo "    </td>";
                    echo "  </tr>";
                    $i++;
                }
            }

            if (empty($KBdata['kampf']['bomben']['bev_zerstoert']) == false) {  // Bevölkerung
                echo "  <tr>";
                echo "     <td class='left' colspan='8'>";
                echo "      <input name='bombbev1' type='text' value='' size='75'>";
                echo " <b>" . number_format($KBdata['kampf']['bomben']['bev_zerstoert'], 0, ",", ".") . "</b>";
                echo "      <input type='hidden' name='bombbev2' type='text' value='" . urlencode(" [b]" . number_format($KBdata['kampf']['bomben']['bev_zerstoert'], 0, ",", ".") . "[/b] ") . "'>";
                echo "      <input name='bombbev3' type='text' value='Leute starben durch die Bombardierung.' size='50'>";
                echo "     </td>";
                echo "  </tr>";
            }
        }
        //Ende Bombing


        $KBLink = str_replace("&typ=xml", "", $KBLink);

        echo "  <tr>";
        echo "     <td class='center' colspan='8'>";
        echo "       <input type='hidden' name='KBLink' type='text' value='" . urlencode($KBLink) . "'>";
        echo "       <input type='submit' value='BB-Code generieren' class='btn'>";
        echo "     </td>";
        echo "  </tr>";


        echo "</table>";
        echo "</form>";

    }
}

if ($parsstatus === "write") {  // BB-Code ausgeben

    // Dicken fetten String basteln für die Ausgabe

    if (empty($_POST['optionLink'])) {
        $outBB = "[quote][table]";
    }
    else {
        $outBB = "[quote=" . urldecode(getVar('KBLink')) . "][table]";
    }
    $outBB = $outBB . "[tr][td colspan=4]" . urldecode(getVar('mainline')) . "[/td][/tr]"; //Kampf auf dem ...
    $outBB = $outBB . "[tr][td colspan=4]" . urldecode(getVar('dateline')) . "[/td][/tr]"; //Die Schlacht endete mit ...

    // Angreifer
    $outBB = $outBB . "[tr][td colspan=4][hr][/td][/tr]"; //horrizontale Linie
    $outBB = $outBB . "[tr][td][u]Angreifer[/u][/td][td][right]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[u]Anzahl[/u][/right][/td][td][right]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[u]Zerstört[/u][/right][/td][td][right]&nbsp;&nbsp;[u]Überlebende[/u][/right][/td][/tr]";
    $i     = 1;
    while (empty($_POST["atter" . $i]) == false) {
        $outBB = $outBB . "[tr][td colspan=4]" . urldecode(getVar("atter" . $i)) . "[/td][/tr]"; //Angreifende Flotte ...
        $j     = 1;
        while (empty($_POST["atterschiffname" . $i . "_" . $j]) == false) {
            if (getVar("atterschiffstart" . $i . "_" . $j) !== "") {
                $outBB = $outBB . "[tr][td]" . makeString(urldecode(getVar("atterschiffname" . $i . "_" . $j)), getVar("Attformf" . $i . "_" . $j), getVar("Attformk" . $i . "_" . $j), getVar("Attformu" . $i . "_" . $j), getVar("Attformc" . $i . "_" . $j)) . "[/td]";
                $outBB = $outBB . "[td][right]" . makeString(getVar("atterschiffstart" . $i . "_" . $j), getVar("Attformf" . $i . "_" . $j), getVar("Attformk" . $i . "_" . $j), getVar("Attformu" . $i . "_" . $j), getVar("Attformc" . $i . "_" . $j)) . "[/right][/td]";
                $outBB = $outBB . "[td][right]" . makeString(getVar("atterschiffweg" . $i . "_" . $j), getVar("Attformf" . $i . "_" . $j), getVar("Attformk" . $i . "_" . $j), getVar("Attformu" . $i . "_" . $j), getVar("Attformc" . $i . "_" . $j)) . "[/right][/td]";
                $outBB = $outBB . "[td][right]" . makeString(getVar("atterschiffende" . $i . "_" . $j), getVar("Attformf" . $i . "_" . $j), getVar("Attformk" . $i . "_" . $j), getVar("Attformu" . $i . "_" . $j), getVar("Attformc" . $i . "_" . $j)) . "[/right][/td][/tr]";
            }
            $j++;
        }
        if (empty($_POST["atterkuchen" . $i]) == false) {
            $outBB = $outBB . "[tr][td colspan=4]Die Flotte brachte Kaffee und Kuchen mit.[/td][/tr]";
        }
        if (empty($_POST["atterlollies" . $i]) == false) {
            $outBB = $outBB . "[tr][td colspan=4]Diese wilden Barbarben haben unseren kleinen Kindern die Lollis geklaut!! Das schreit gradezu nach Rache.[/td][/tr]";
        }
        if (empty($_POST["attermsg" . $i]) == false) {
            $outBB = $outBB . "[tr][td colspan=4]" . urldecode(getVar("attermsg" . $i)) . "[/td][/tr]";
        }
        $i++;
    }

    // Pladeff
    $outBB = $outBB . "[tr][td colspan=4][hr][/td][/tr]"; //horrizontale Linie
    $outBB = $outBB . "[tr][td][u]Verteidiger[/u][/td][td][right][u]Anzahl[/u][/right][/td][td][right][u]Zerstört[/u][/right][/td][td][right][u]Überlebende[/u][/right][/td][/tr]";
    $i     = 1;
    while (empty($_POST["pladeffer" . $i]) == false) {
        $outBB = $outBB . "[tr][td colspan=4]" . urldecode(getVar("pladeffer" . $i)) . "[/td][/tr]"; // Verteidiger ist ...
        $j     = 1;
        while (empty($_POST["pladefturm" . $i . "_" . $j]) == false) {
            $outBB = $outBB . "[tr][td]" . makeString(urldecode(getVar("pladefturm" . $i . "_" . $j)), getVar("Pladefturmformf" . $i . "_" . $j), getVar("Pladefturmformk" . $i . "_" . $j), getVar("Pladefturmformu" . $i . "_" . $j), getVar("Pladefturmformc" . $i . "_" . $j)) . "[/td]";
            $outBB = $outBB . "[td][right]" . makeString(getVar("pladefturmstart" . $i . "_" . $j), getVar("Pladefturmformf" . $i . "_" . $j), getVar("Pladefturmformk" . $i . "_" . $j), getVar("Pladefturmformu" . $i . "_" . $j), getVar("Pladefturmformc" . $i . "_" . $j)) . "[/right][/td]";
            $outBB = $outBB . "[td][right]" . makeString(getVar("pladefturmweg" . $i . "_" . $j), getVar("Pladefturmformf" . $i . "_" . $j), getVar("Pladefturmformk" . $i . "_" . $j), getVar("Pladefturmformu" . $i . "_" . $j), getVar("Pladefturmformc" . $i . "_" . $j)) . "[/right][/td]";
            $outBB = $outBB . "[td][right]" . makeString(getVar("pladefturmende" . $i . "_" . $j), getVar("Pladefturmformf" . $i . "_" . $j), getVar("Pladefturmformk" . $i . "_" . $j), getVar("Pladefturmformu" . $i . "_" . $j), getVar("Pladefturmformc" . $i . "_" . $j)) . "[/right][/td][/tr]";
            $j++;
        }
        $j = 1;
        while (empty($_POST["pladefschiff" . $i . "_" . $j]) == false) {
            $outBB = $outBB . "[tr][td]" . makeString(urldecode(getVar("pladefschiff" . $i . "_" . $j)), getVar("Pladefschiffformf" . $i . "_" . $j), getVar("Pladefschiffformk" . $i . "_" . $j), getVar("Pladefschiffformu" . $i . "_" . $j), getVar("Pladefschiffformc" . $i . "_" . $j)) . "[/td]";
            $outBB = $outBB . "[td][right]" . makeString(getVar("pladefschiffstart" . $i . "_" . $j), getVar("Pladefschiffformf" . $i . "_" . $j), getVar("Pladefschiffformk" . $i . "_" . $j), getVar("Pladefschiffformu" . $i . "_" . $j), getVar("Pladefschiffformc" . $i . "_" . $j)) . "[/right][/td]";
            $outBB = $outBB . "[td][right]" . makeString(getVar("pladefschiffweg" . $i . "_" . $j), getVar("Pladefschiffformf" . $i . "_" . $j), getVar("Pladefschiffformk" . $i . "_" . $j), getVar("Pladefschiffformu" . $i . "_" . $j), getVar("Pladefschiffformc" . $i . "_" . $j)) . "[/right][/td]";
            $outBB = $outBB . "[td][right]" . makeString(getVar("pladefschiffende" . $i . "_" . $j), getVar("Pladefschiffformf" . $i . "_" . $j), getVar("Pladefschiffformk" . $i . "_" . $j), getVar("Pladefschiffformu" . $i . "_" . $j), getVar("Pladefschiffformc" . $i . "_" . $j)) . "[/right][/td][/tr]";
            $j++;
        }
        $i++;
    }

    // Deffer
    $i = 1;
    while (empty($_POST["deffer" . $i]) == false) {
        $outBB = $outBB . "[tr][td colspan=4]" . urldecode(getVar("deffer" . $i)) . "[/td][/tr]"; //Verteidigende Flotte ...
        $j     = 1;
        while (empty($_POST["defferschiffname" . $i . "_" . $j]) == false) {
            if (empty($_POST["defferschiffstart" . $i . "_" . $j]) == false) {
                $outBB = $outBB . "[tr][td]" . makeString(urldecode(getVar("defferschiffname" . $i . "_" . $j)), getVar("Deffformf" . $i . "_" . $j), getVar("Deffformk" . $i . "_" . $j), getVar("Deffformu" . $i . "_" . $j), getVar("Deffformc" . $i . "_" . $j)) . "[/td]";
                $outBB = $outBB . "[td][right]" . makeString(getVar("defferschiffstart" . $i . "_" . $j), getVar("Deffformf" . $i . "_" . $j), getVar("Deffformk" . $i . "_" . $j), getVar("Deffformu" . $i . "_" . $j), getVar("Deffformc" . $i . "_" . $j)) . "[/right][/td]";
                $outBB = $outBB . "[td][right]" . makeString(getVar("defferschiffweg" . $i . "_" . $j), getVar("Deffformf" . $i . "_" . $j), getVar("Deffformk" . $i . "_" . $j), getVar("Deffformu" . $i . "_" . $j), getVar("Deffformc" . $i . "_" . $j)) . "[/right][/td]";
                $outBB = $outBB . "[td][right]" . makeString(getVar("defferschiffende" . $i . "_" . $j), getVar("Deffformf" . $i . "_" . $j), getVar("Deffformk" . $i . "_" . $j), getVar("Deffformu" . $i . "_" . $j), getVar("Deffformc" . $i . "_" . $j)) . "[/right][/td][/tr]";
            }
            $j++;
        }
        $i++;
    }

    // Verluste & Plündern

    // Suchen eines Eintrages
    $i  = 1;
    $do = false;
    while ($i <= 7) {
        if (empty($_POST["attverlust" . $i]) == false) {
            $do = true;
        }
        if (empty($_POST["defverlust" . $i]) == false) {
            $do = true;
        }
        if (empty($_POST["weg" . $i]) == false) {
            $do = true;
        }
        if ($do) {
            break;
        }
        $i++;
    }

    if ($do) {
        $i            = 1;
        $ressname[1]  = "Eisen";
        $ressname[2]  = "Stahl";
        $ressname[3]  = "VV4A";
        $ressname[4]  = "Eis";
        $ressname[5]  = "chem. Elemente";
        $ressname[6]  = "Wasser";
        $ressname[7]  = "Energie";
        $ressname[10] = "Credits";
        $ressname[11] = "Bevölkerung";
        $outBB        = $outBB . "[tr][td colspan=4][hr][/td][/tr]"; //horrizontale Linie
        $outBB        = $outBB . "[tr][td][u]Zerstörte und geplünderte Ressourcen[/u][/td][td][right][u]Angreifer[/u][/right][/td][td][right][u]Verteidiger[/u][/right][/td][td][right][u]Plünderung[/u][/right][/td][/tr]";
        while ($i <= 11) {
            if (empty($_POST["attverlust" . $i]) == false || empty($_POST["defverlust" . $i]) == false || empty($_POST["weg" . $i]) == false) {
                $outBB = $outBB . "[tr][td]" . $ressname[$i] . "[/td]";
                if (empty($_POST["attverlust" . $i]) == false) {
                    $outBB = $outBB . "[td][right]" . makeString($_POST["attverlust" . $i], isset($_POST["attverlustformf" . $i]), isset($_POST["attverlustformk" . $i]), isset($_POST["attverlustformu" . $i]), $_POST["attverlustformc" . $i]) . "[/right][/td]";
                } else {
                    $outBB = $outBB . "[td][right]-[/right][/td]";
                }
                if (empty($_POST["defverlust" . $i]) == false) {
                    $outBB = $outBB . "[td][right]" . makeString($_POST["defverlust" . $i], isset($_POST["defverlustformf" . $i]), isset($_POST["defverlustformk" . $i]), isset($_POST["defverlustformu" . $i]), $_POST["defverlustformc" . $i]) . "[/right][/td]";
                } else {
                    $outBB = $outBB . "[td][right]-[/right][/td]";
                }
                if (empty($_POST["weg" . $i]) == false) {
                    $outBB = $outBB . "[td][right][color=green]" . makeString($_POST["weg" . $i], isset($_POST["wegformf" . $i]), isset($_POST["wegformk" . $i]), isset($_POST["wegformu" . $i]), $_POST["wegformc" . $i]) . "[/color][/right][/td][/tr]";
                } else {
                    $outBB = $outBB . "[td][right]-[/right][/td][/tr]";
                }
            }
            $i++;
        }
    }

    //Bomben
    if (empty($_POST['bomb1']) == false) {
        $outBB = $outBB . "[tr][td colspan=4][hr][/td][/tr]"; //horrizontale Linie
        $outBB = $outBB . "[tr][td colspan=4]" . $_POST['bomb1'] . $_POST['bomb2'] . "[/td][/tr]"; // Der Planet wurde ...
        if (empty($_POST['bombtreff1']) == false) {
            $outBB = $outBB . "[tr][td colspan=4]" . $_POST['bombtreff1'] . $_POST['bombtreff2'] . $_POST['bombtreff3'] . "[/td][/tr]";
        } // Bombendtrefferchance
        if (empty($_POST['bomb3']) == false) {
            $outBB = $outBB . "[tr][td colspan=4]" . $_POST['bomb3'] . "[/td][/tr]";
        } // KB oder keine Gebs
        else {
            $i     = 1;
            $outBB = $outBB . "[tr][td colspan=4]Folgende Gebäude wurden zerstört:[/td][/tr]";
            while (empty($_POST["bombgeb" . $i]) == false) {
                $outBB = $outBB . "[tr][td]" . makeString($_POST["bombgeb" . $i], isset($_POST["bombgebformf" . $i]), isset($_POST["bombgebformk" . $i]), isset($_POST["bombgebformu" . $i]), $_POST["bombgebformc" . $i]) . "[/td][td][right]" . makeString($_POST["bombgebanz" . $i], isset($_POST["bombgebformf" . $i]), isset($_POST["bombgebformk" . $i]), isset($_POST["bombgebformu" . $i]), $_POST["bombgebformc" . $i]) . "[/right][/td][/tr]";
                $i++;
            }
        }
        if (empty($_POST['bombbev2']) == false) {
            $outBB = $outBB . "[tr][td colspan=4]" . $_POST['bombbev1'] . $_POST['bombbev2'] . $_POST['bombbev3'] . "[/td][/tr]";
        }
    }

    // KBLink - Ende
    if (empty($_POST['optionLink'])) {
        $outBB = $outBB . "[tr][td colspan=4][hr][/td][/tr]"; //horrizontale Linie
        $outBB = $outBB . "[tr][td colspan=4][url=" . urldecode(getVar("KBLink" . $i)) . "]Link zum externen Kampfbericht[/url][/td][/tr][/table][/quote]";
    }
    else
    {
        $outBB = $outBB . "[/table][/quote]";
    }

    // Leere Allytags entfernen
    $outBB = str_replace("[]", "", $outBB);

    // Lange Schiffnamen killen
    if (empty($_POST['optionKuerzen']) == false) {
        $outBB = str_replace("(Hyperraumtransporter Klasse 1)", "", $outBB); // Gorgol, Kamel, Flughund
        $outBB = str_replace("(Hyperraumtransporter Klasse 2)", "", $outBB); // Eisbär, Waschbär, Seepferd
        $outBB = str_replace("(Systemtransporter Kolonisten)", "", $outBB); // Crux
        $outBB = str_replace("(Hyperraumtransporter Kolonisten)", "", $outBB); // Kolpor
        $outBB = str_replace("(Systemtransporter Klasse 1)", "", $outBB); // Systrans
        $outBB = str_replace("(Systemtransporter Klasse 2)", "", $outBB); // Lurch
        $outBB = str_replace("(Transporter)", "", $outBB); // Osterschiff
        $outBB = str_replace("(interstellares Kolonieschiff)", "", $outBB); // INS
        $outBB = str_replace("(Systemkolonieschiff)", "", $outBB); // KIS
    }

    //Daraus einen Html Code basteln / auch nicht gerade schön
    /*$outHTML = $outBB;
    if (empty($_POST['optionHtml']) == false) {
        $outHTML = str_replace("[quote]<br>", "", $outHTML);
        $outHTML = str_replace("<br>[/quote]", "", $outHTML);
        $outHTML = str_replace("[table]<br>", "<table  cellpadding='5%'>", $outHTML);
        $outHTML = str_replace("[/table]<br>", "</table>", $outHTML);
        $outHTML = str_replace("[tr]<br>", "<tr>", $outHTML);
        $outHTML = str_replace("[tr]", "<tr>", $outHTML);
        $outHTML = str_replace("[/tr]<br>", "</tr>", $outHTML);
        $outHTML = str_replace("[/tr]", "</tr>", $outHTML);
        $outHTML = str_replace("[td]", "<td>", $outHTML);
        $outHTML = str_replace("[/td]", "</td>", $outHTML);
        $outHTML = str_replace("[hr]<br>", "<hr>", $outHTML);
        $outHTML = str_replace("[center]", "<p class='center'>", $outHTML);
        $outHTML = str_replace("[/center]", "</p>", $outHTML);
        $outHTML = str_replace("[right]", "<p class='right'>", $outHTML);
        $outHTML = str_replace("[/right]", "</p>", $outHTML);
        $outHTML = str_replace("[left]", "<p class='left'>", $outHTML);
        $outHTML = str_replace("[/left]", "</p>", $outHTML);
        $outHTML = str_replace("&nbsp;", "", $outHTML);
        $outHTML = str_replace("[b]", "<b>", $outHTML);
        $outHTML = str_replace("[/b]", "</b>", $outHTML);
        $outHTML = str_replace("[i]", "<i>", $outHTML);
        $outHTML = str_replace("[/i]", "</i>", $outHTML);
        $outHTML = str_replace("[u]", "<u>", $outHTML);
        $outHTML = str_replace("[/u]", "</u>", $outHTML);
        $outHTML = str_replace("[color=red]", "<font color='red'>", $outHTML);
        $outHTML = str_replace("[color=pink]", "<font color='pink'>", $outHTML);
        $outHTML = str_replace("[color=yellow]", "<font color='yellow'>", $outHTML);
        $outHTML = str_replace("[color=green]", "<font color='green'>", $outHTML);
        $outHTML = str_replace("[color=orange]", "<font color='orange'>", $outHTML);
        $outHTML = str_replace("[color=purple]", "<font color='purple'>", $outHTML);
        $outHTML = str_replace("[color=blue]", "<font color='blue'>", $outHTML);
        $outHTML = str_replace("[color=beige]", "<font color='beige'>", $outHTML);
        $outHTML = str_replace("[color=brown]", "<font color='brown'>", $outHTML);
        $outHTML = str_replace("[color=teal]", "<font color='teal'>", $outHTML);
        $outHTML = str_replace("[color=navy]", "<font color='navy'>", $outHTML);
        $outHTML = str_replace("[color=maroon]", "<font color='maroon'>", $outHTML);
        $outHTML = str_replace("[color=limegreen]", "<font color='limegreen'>", $outHTML);
        $outHTML = str_replace("[/color]", "</font>", $outHTML);
        $outHTML = str_replace("[url=", "<a href='>", $outHTML);
        $outHTML = str_replace("]externer Link[/url]", "' target='_blank'>externer Link</a>", $outHTML);
    }*/

    //BBCode beschneiden, Nicht gerade schön aber zu faul für den Rest
    if (empty($_POST['optionQuote'])) //checkbox quoten nicht aktiv
    {
        $outBB = str_replace("[quote]", "", $outBB);
        $outBB = str_replace("[/quote]", "", $outBB);

    }

    /*if (empty($_POST['optionTab'])) {
        $outBB = str_replace("[table]<br>", "", $outBB);
        $outBB = str_replace("[/table]<br>", "", $outBB);
        $outBB = str_replace("[tr] [td][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anzahl[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zerstört[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Überlebend[/center][/td][/tr]", "Schiffname/ Anzahl/ Zerstört/ Überlebend", $outBB);
        $outBB = str_replace("[tr][td][center]Deffanlagen[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anzahl[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zerstört[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Überlebend[/center][/td][/tr]", "Verteidigunganlage/ Anzahl/ Zerstört/ Überlebend", $outBB);
        $outBB = str_replace("[tr][td] [/td][td]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Verteidiger[/td][td]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Angreifer[/td][td]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Plünderung[/td][/tr]", "Ressource/ Verteidiger/ Angreifer/ Plünderungen", $outBB);
        $outBB = str_replace("[/td][td]", " / ", $outBB);
        $outBB = str_replace("[tr][td]", "", $outBB);
        $outBB = str_replace("[/td]", "", $outBB);
        $outBB = str_replace("[td]", "", $outBB);
        $outBB = str_replace("[/tr]", "", $outBB);

    }*/

    if (empty($_POST['optionHr'])) {
        $outBB = str_replace("[hr]", "---", $outBB);
    }

    if (empty($_POST['optionColspan']) == false) {
        $outBB = str_replace(" colspan", "", $outBB);
    }

    if (empty($_POST['optionAlign'])) {
        $outBB = str_replace("[center]", "", $outBB);
        $outBB = str_replace("[/center]", "", $outBB);
        $outBB = str_replace("[left]", "", $outBB);
        $outBB = str_replace("[/left]", "", $outBB);
        $outBB = str_replace("[right]", "", $outBB);
        $outBB = str_replace("[/right]", "", $outBB);
    }

    if (empty($_POST['optionColor'])) { //Da ich für pregreplace zu faul bin
        $outBB = str_replace("[color=red]", "", $outBB);
        $outBB = str_replace("[color=yellow]", "", $outBB);
        $outBB = str_replace("[color=pink]", "", $outBB);
        $outBB = str_replace("[color=green]", "", $outBB);
        $outBB = str_replace("[color=orange]", "", $outBB);
        $outBB = str_replace("[color=purple]", "", $outBB);
        $outBB = str_replace("[color=blue]", "", $outBB);
        $outBB = str_replace("[color=beige]", "", $outBB);
        $outBB = str_replace("[color=brown]", "", $outBB);
        $outBB = str_replace("[color=teal]", "", $outBB);
        $outBB = str_replace("[color=navy]", "", $outBB);
        $outBB = str_replace("[color=maroon]", "", $outBB);
        $outBB = str_replace("[color=limegreen]", "", $outBB);
        $outBB = str_replace("[/color]", "", $outBB);
    }


    if (empty($_POST['optionForm'])) {
        $outBB = str_replace("[b]", "", $outBB);
        $outBB = str_replace("[/b]", "", $outBB);
        $outBB = str_replace("[i]", "", $outBB);
        $outBB = str_replace("[/i]", "", $outBB);
        $outBB = str_replace("[u]", "", $outBB);
        $outBB = str_replace("[/u]", "", $outBB);
    }

    echo "       <textarea name='bbcode' rows='10' style='width: 95%;' onclick='this.select()' readonly>" . $outBB . "</textarea>";

    // Eventuell HTML-Code ausgeben
    /*if (empty($_POST['optionHtml']) == false) {
        echo "  <tr>";
        echo "    <td colspan='2'><br><hr><br></td>";
        echo "  </tr>";
        echo "  <tr>";
        echo "     <td class='left top'>";
        //$outHTML = str_replace("<","&lt;",$outHTML);
        //$outHTML = str_replace(">","&gt;",$outHTML);
        //$outHTML = str_replace("&","&;",$outHTML);
        echo "     <b>HTML-Code:</b></td>";
        echo "     <td><textarea name='HTMLCode' cols='100' rows='10' readonly>" . $outHTML . "</textarea>";
        echo "     </td>";
        echo "  </tr>";
    }*/
    echo "</table>";
}

function file_get_contents_utf8($fn)
{
    $content = file_get_contents($fn);

    if (!empty($content)) {
        return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
    } else {
        return false;
    }

}