<?php

/*****************************************************************************/
/* m_kbparser1.php                                                             */
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
/* Dieses Modul dient als Vorlage zum Erstellen von eigenen Zusatzmodulen    */
/* für die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschaftsprojekt von */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete           */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iwdb.de.vu                                   */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde. 
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
    echo "Hacking attempt...!!";
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
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc =
    "KBParser mit einer vielfältigen Ausgabe im BBCode";


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

//Komische Funktion die rekrusiv ein Array mit den Werten füllt.

function makeArray($Bericht)
{
    global $line;
    $rt = array();

    while ($line < count($Bericht)) { //Erkennen der Elemente
        if (strpos($Bericht[$line], "<?") !== false) //Überspringen der XML information
        {
            $line++;
        } elseif (strpos($Bericht[$line], "</") !== false) // Closer Tag, kein Überprüfung auf Korrektheit
        {
            $line++;
            break; //Abschließen des momentanen Wertes
        } elseif (strpos($Bericht[$line], "value=") !== false) // Endwert
        {
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
        } elseif (strpos($Bericht[$line], "<") !== false) // das sollte jetzt ein neues Element ohne Wert sein
        {
            $name = str_replace("<", "", $Bericht[$line]);
            $name = trim(str_replace(">", "", $name));
            while (empty($rt[$name]) == false) {
                $name = $name . "1";
            }
            $line++;
            $rt[$name] = makeArray($Bericht);
        } else // keine Verwertbare Zeile
        {
            $line++;
        }
    }

    return $rt; // Array zurückgeben

}


// Eienen String mit Fett, Kursiv, Unterstrichen und Farbe versehen

function makeString($string, $f, $k, $u, $color)
{
    $rt = $string;
    if ($f == true) {
        $rt = "[b]" . $rt . "[/b]";
    }
    if ($k == true) {
        $rt = "[i]" . $rt . "[/i]";
    }
    if ($u == true) {
        $rt = "[u]" . $rt . "[/u]";
    }
    if ($color != "keine") {
        $rt = "[color=" . $color . "]" . $rt . "[/color]";
    }

    return $rt;
}

//Und da fängts an.

$parsstatus = getVar('parsstatus');

echo "<font style='font-size: 18px; color: #004466'>KB Parser für BB-Code</font><br>\n";
echo "<br>\n";


if (empty($parsstatus) == true) //Angabe für die Datei
{
    echo "<form method='POST' action='index.php?action=m_kbparser1&parsstatus=read' enctype='multipart/form-data'>\n";
    echo <<< EOT
	 <table border="1" cellpadding="2" cellspacing="1" rules="none" width="90%">
  		<tr>
    		<td align="left" width="50">
      		KB-Link:
    		</td>
    		<td  align="center">
      			<input type="text" name="KBLink" value="" placeholder="KB-Link" style="width:95%;">
    		</td>
    		<td  align="center" width="50">
      			<input type="submit" value="Go" name="B1" width="45" style="color:#FFFFFF; background-color: #00688B;">
    		</td>
  		</tr>
	 </table>
	 </form>
EOT;
    if (empty($KBLink) == true) {
        echo "KB-Link nicht vergessen :) ";
    }
}


if ($parsstatus == "read") // KB einlesen und für die Formatierung ausgeben
{
    $KBLink = getVar('KBLink') . "&typ=xml";

    if ($KBLink == "&typ=xml") {
        echo "<div class='system_error'>Keinen KB-Link eingetragen!</div>";
        $parsstatus = "";

    } else {
        //echo $KBLink;
        //$KBLink = str_replace("&","&",$KBLink);
        $KBLink  = str_replace("&amp;", "&", $KBLink);
        $Bericht = file_get_contents_utf8($KBLink, 'r');
        $Bericht = explode("\n", $Bericht);

        $line = 0;

        $KBdata = makeArray($Bericht); // komische verschachtelte Arrays


        echo "<form method='POST' action='index.php?action=m_kbparser1&parsstatus=write' enctype='multipart/form-data'>\n";
        echo "<table border='1' cellpadding='2' cellspacing='1' style='width: 90%;'>\n";

        // Optionen
        echo "  <tr>\n";
        echo "     <td align='center' colspan=8 >";
        echo "        Optionen: <input type='checkbox' name='optionQuote' value='on' checked>Quoten";
        echo "        / <input type='checkbox' name='optionTab' value='on' checked>Tabellen";
        echo "        / <input type='checkbox' name='optionHr' value='on' checked>horizonale Linie";
        echo "        / <input type='checkbox' name='optionAlign' value='on' checked>Ausrichtung";
        echo "        / <input type='checkbox' name='optionColor' value='on' checked>Farbe";
        echo "        / <input type='checkbox' name='optionForm' value='on' checked>Format";
        echo "        / <input type='checkbox' name='optionLink' value='on'>DirektLink";
        echo "        / <input type='checkbox' name='optionHtml' value='on' >HTML";
        echo "     </td>";
        echo "  </tr>\n";


        echo "  <tr>\n";
        echo "     <td align='center' colspan='8'>";
        echo "       <input type='submit' value='BB-Code gleich generieren' style='color:#FFFFFF; background-color: #00688B;'>";
        echo "     </td> ";
        echo "  </tr>\n ";


// Kampf auf dem Planeten
        echo "  <tr>\n";
        echo "     <td align='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
        echo "        <input TYPE=HIDDEN name='mainline' type='text' value='Kampf auf dem Planeten [b]" . $KBdata['kampf']['plani_data']['plani_name'] . " " . $KBdata['kampf']['plani_data']['koordinaten']['string'] . "[/b], der Besitzer ist [b]" . $KBdata['kampf']['plani_data']['user']['name'] . " [" . $KBdata['kampf']['plani_data']['user']['allianz_tag'] . "][/b].' readonly>";
        echo "        Kampf auf dem Planeten <b>" . $KBdata['kampf']['plani_data']['plani_name'] . " " . $KBdata['kampf']['plani_data']['koordinaten']['string'] . "</b>, der Besitzer ist <b>" . $KBdata['kampf']['plani_data']['user']['name'] . " [" . $KBdata['kampf']['plani_data']['user']['allianz_tag'] . "]</b> ";
        echo "     </td> ";
        echo "  </tr>\n ";
        // Am Datum endete der Kampf mit einem Sieg für den ...
        //setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
        setlocale(LC_ALL, 'de_DE.utf8');
		echo "  <tr>\n";
        echo "     <td class='windowbg1' align='left' colspan='8'>";
        echo          strftime("Am <i>%d. %B %Y um %H:%M:%S</i>", $KBdata['kampf']['timestamp']) . " endete der Kampf mit einem Sieg für den ";
        if ($KBdata['kampf']['resultat']['id'] == 1) {
            echo "<font color='green'><b>Angreifer</b></font>";
        }
        else {
            echo "<font color='red'><b>Verteidiger</b></font>";
        }
        echo "        <input TYPE=HIDDEN name='dateline' type='text' value='" . strftime("Am <i>%d. %B %Y um %H:%M:%S</i>", $KBdata['kampf']['timestamp']) . " endete der Kampf mit einem Sieg für den ";
        if ($KBdata['kampf']['resultat']['id'] == 1) {
            echo "[color=green][b]Angreifer[/b][/color]";
        }
        else {
            echo "[color=red][b]Verteidiger[/b][/color]";
        }
        echo                  "' readonly>";
        echo "     </td> ";
        echo "  </tr>\n ";
        // Angreifende Flotten
        $i = 1;
        foreach ($KBdata['kampf']['flotten_att'] as $user) {
            echo "  <tr>\n";
            echo "     <td align='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            echo "       Angreifende Flotte von <b>" . $user['name'] . " [" . $user['allianz_tag'] . "]</b>, Startplanet war " . $user['startplanet']['plani_name'] . " " . $user['startplanet']['koordinaten']['string'];
            echo "       <input TYPE=HIDDEN name='atter" . $i . "' type='text' value='Angreifende Flotte von [b]" . $user['name'] . " [" . $user['allianz_tag'] . "][/b], Startplanet war " . $user['startplanet']['plani_name'] . " " . $user['startplanet']['koordinaten']['string'] . "' readonly>";
            echo "     </td> ";
            echo "  </tr>\n ";
            if (empty($user['bloedsinn']['kaffee']) == false) {
                echo "  <tr>\n";
                echo "     <td align='left' colspan='8'>";
                echo "        Die Flotte brachte Kaffee und Kuchen mit";
                echo "        <input TYPE=HIDDEN name='atterkuchen" . $i . "' type='text' value='1' readonly>";
                echo "     </td>";
                echo "  </tr>\n";
            }
            if (empty($user['bloedsinn']['lollis']) == false) {
                echo "  <tr>\n";
                echo "     <td align='left' colspan='8'>";
                echo "        Diese wilden Barbarben haben unseren kleinen Kindern die Lollis geklaut!! Das schreit gradezu nach Rache.";
                echo "        <input TYPE=HIDDEN name='atterlollies" . $i . "' type='text' value='1' readonly>";
                echo "     </td>";
                echo "  </tr>\n";
            }
            if (empty($user['bloedsinn']['msg']) == false) {
                echo "  <tr>\n";
                echo "     <td align='left' colspan='8'>";
                echo "        Der Kommunikation konnte folgendes dekodieren: " . $user['bloedsinn']['msg'];
                echo "        <input TYPE=HIDDEN name='attermsg" . $i . "' type='text' value='Der Kommunikation konnte folgendes dekodieren: " . $user['bloedsinn']['msg'] . "' readonly>";
                echo "     </td>";
                echo "  </tr>\n";
            }
            //Schiffe
            echo "  <tr>\n";
            echo "    <td align='center'>";
            echo "     Schiffname";
            echo "    </td>";
            echo "    <td align='center'>";
            echo "     Anzahl";
            echo "    </td>";
            echo "    <td align='center'>";
            echo "     Zerstört";
            echo "    </td>";
            echo "    <td align='center'>";
            echo "     Überlebend";
            echo "    </td>";
            echo "    <td align='center'>";
            echo "       <b>F</b>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <i>K</i>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <u>U</u>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       Farbe";
            echo "    </td>";
            echo "  </tr>\n";
            $j = 1;
            foreach ($user['schiffe'] as $schiffe) {
                echo "  <tr>\n";
                echo "    <td align='left'>";
                echo        $schiffe['name'];
                echo "      <input TYPE=HIDDEN name='atterschiffname" . $i . "_" . $j . "' type='text' value='" . $schiffe['name'] . "' readonly>";
                echo "    </td>";
                echo "    <td align='right'>";
                echo        $schiffe['anzahl_start'];
                echo "      <input TYPE=HIDDEN name='atterschiffstart" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_start'] . "' readonly>";
                echo "    </td>";
                echo "    <td align='right'>";
                echo        $schiffe['anzahl_verlust'];
                echo "      <input TYPE=HIDDEN name='atterschiffweg" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_verlust'] . "' readonly>";
                echo "    </td>";
                echo "    <td align='right'>";
                echo        $schiffe['anzahl_ende'];
                echo "      <input TYPE=HIDDEN name='atterschiffende" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_ende'] . "' readonly>";
                echo "    </td>";
                echo "    <td align='center'>";
                echo "      <input type='checkbox' name='Attformf" . $i . "_" . $j . "' value='f'>";
                echo "    </td>";
                echo "    <td align='center'>";
                echo "      <input type='checkbox' name='Attformk" . $i . "_" . $j . "' value='k'>";
                echo "    </td>";
                echo "    <td align='center'>";
                echo "      <input type='checkbox' name='Attformu" . $i . "_" . $j . "' value='u'>";
                echo "    </td>";
                echo "    <td align='center'>";
                echo "      <select name='Attformc" . $i . "_" . $j . "' size='1'> ";
                echo "         <option>keine</option> ";
                echo "         <option>red</option> ";
                echo "         <option>yellow</option> ";
                echo "         <option>pink</option> ";
                echo "         <option>green</option> ";
                echo "         <option>orange</option> ";
                echo "         <option>purple</option> ";
                echo "         <option>blue</option> ";
                echo "         <option>beige</option> ";
                echo "         <option>brown</option> ";
                echo "         <option>teal</option> ";
                echo "         <option>navy</option> ";
                echo "         <option>maroon</option> ";
                echo "         <option>limegreen</option> ";
                echo "       </select>";
                echo "    </td>";
                echo "  </tr>\n";

                $j++;
            }
            $i++;
        }

        // Planetare Deff und Schiffe
        $i = 1;
        foreach ($KBdata['kampf']['pla_def'] as $user) {
            echo "  <tr>\n";
            echo "     <td align='left' colspan='8' style='color:#000000; background-color:#CAE1FF;'>";
            echo "       Verteidiger ist <b>" . $user['name'] . " [" . $user['allianz_tag'] . "]</b>";
            echo "       <input TYPE=HIDDEN name='pladeffer" . $i . "' type='text' value='Verteidiger ist [b]" . $user['name'] . " [" . $user['allianz_tag'] . "][/b]' readonly>";
            echo "     </td> ";
            echo "  </tr>\n ";

            //stationäre
            if (empty($user['defence']) == false) {
                echo "  <tr>\n";
                echo "    <td align='center' >";
                echo "     Verteidigungsanlage";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "     Anzahl";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "     Zerstört";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "     Überlebend";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       <b>F</b>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       <i>K</i>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       <u>U</u>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       Farbe";
                echo "    </td>";
                echo "  </tr>\n";
                $j = 1;
                foreach ($user['defence'] as $schiffe) {
                    echo "  <tr>\n";
                    echo "    <td align='left' >";
                    echo        $schiffe['name'];
                    echo "      <input TYPE=HIDDEN name='pladefturm" . $i . "_" . $j . "' type='text' value='" . $schiffe['name'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='right' >";
                    echo        $schiffe['anzahl_start'];
                    echo "      <input TYPE=HIDDEN name='pladefturmstart" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_start'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='right' >";
                    echo        $schiffe['anzahl_verlust'];
                    echo "      <input TYPE=HIDDEN name='pladefturmweg" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_verlust'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='right' >";
                    echo        $schiffe['anzahl_ende'];
                    echo "      <input TYPE=HIDDEN name='pladefturmende" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_ende'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='Pladefturmformf" . $i . "_" . $j . "' value='f'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='Pladefturmformk" . $i . "_" . $j . "' value='k'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='Pladefturmformu" . $i . "_" . $j . "' value='u'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <select name='Pladefturmformc" . $i . "_" . $j . "' size='1'> ";
                    echo "         <option>keine</option> ";
                    echo "         <option>red</option> ";
                    echo "         <option>yellow</option> ";
                    echo "         <option>pink</option> ";
                    echo "         <option>green</option> ";
                    echo "         <option>orange</option> ";
                    echo "         <option>purple</option> ";
                    echo "         <option>blue</option> ";
                    echo "         <option>beige</option> ";
                    echo "         <option>brown</option> ";
                    echo "         <option>teal</option> ";
                    echo "         <option>navy</option> ";
                    echo "         <option>maroon</option> ";
                    echo "         <option>limegreen</option> ";
                    echo "       </select>";
                    echo "    </td>";
                    echo "  </tr>\n";

                    $j++;
                }
            }
            //flotte
            if (empty($user['schiffe']) == false) {
                echo "  <tr>\n";
                echo "    <td align='center' >";
                echo "     Schiffnamen";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "     Anzahl";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "     Zerstört";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "     Überlebend";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       <b>F</b>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       <i>K</i>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       <u>U</u>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       Farbe";
                echo "    </td>";
                echo "  </tr>\n";
                $j = 1;
                foreach ($user['schiffe'] as $schiffe) {
                    echo "  <tr>\n";
                    echo "    <td align='left' >";
                    echo        $schiffe['name'];
                    echo "      <input TYPE=HIDDEN name='pladefschiff" . $i . "_" . $j . "' type='text' value='" . $schiffe['name'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='right' >";
                    echo        $schiffe['anzahl_start'];
                    echo "      <input TYPE=HIDDEN name='pladefschiffstart" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_start'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='right' >";
                    echo        $schiffe['anzahl_verlust'];
                    echo "      <input TYPE=HIDDEN name='pladefschiffweg" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_verlust'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='right' >";
                    echo        $schiffe['anzahl_ende'];
                    echo "      <input TYPE=HIDDEN name='pladefschiffende" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_ende'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='Pladefschiffformf" . $i . "_" . $j . "' value='f'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='Pladefschiffformk" . $i . "_" . $j . "' value='k'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='Pladefschiffformu" . $i . "_" . $j . "' value='u'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <select name='Pladefschiffformc" . $i . "_" . $j . "' size='1'> ";
                    echo "         <option>keine</option> ";
                    echo "         <option>red</option> ";
                    echo "         <option>yellow</option> ";
                    echo "         <option>pink</option> ";
                    echo "         <option>green</option> ";
                    echo "         <option>orange</option> ";
                    echo "         <option>purple</option> ";
                    echo "         <option>blue</option> ";
                    echo "         <option>beige</option> ";
                    echo "         <option>brown</option> ";
                    echo "         <option>teal</option> ";
                    echo "         <option>navy</option> ";
                    echo "         <option>maroon</option> ";
                    echo "         <option>limegreen</option> ";
                    echo "       </select>";
                    echo "    </td>";
                    echo "  </tr>\n";

                    $j++;
                }
            }
            $i++;
        }

        // Deffende Flotten

        if (empty($KBdata['kampf']['flotten_def']) == false) {
            $i = 1;
            foreach ($KBdata['kampf']['flotten_def'] as $user) {
                echo "  <tr>\n";
                echo "     <td align='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
                echo "       Verteidigende Flotte von <b>" . $user['name'] . " [" . $user['allianz_tag'] . "]</b>";
                echo "       <input TYPE=HIDDEN name='deffer" . $i . "' type='text' value='Verteidigenden Flotte von [b]" . $user['name'] . " [" . $user['allianz_tag'] . "][/b]' readonly>";
                echo "     </td> ";
                echo "  </tr>\n ";
                //schiffe
                echo "  <tr>\n";
                echo "    <td align='center' >";
                echo "     Schiffname";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "     Anzahl";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "     Zerstört";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "     Überlebend";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       <b>F</b>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       <i>K</i>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       <u>U</u>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "       Farbe";
                echo "    </td>";
                echo "  </tr>\n";
                $j = 1;
                foreach ($user['schiffe'] as $schiffe) {
                    echo "  <tr>\n";
                    echo "    <td align='left' >";
                    echo        $schiffe['name'];
                    echo "      <input TYPE=HIDDEN name='defferschiffname" . $i . "_" . $j . "' type='text' value='" . $schiffe['name'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='right' >";
                    echo        $schiffe['anzahl_start'];
                    echo "      <input TYPE=HIDDEN name='defferschiffstart" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_start'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='right' >";
                    echo        $schiffe['anzahl_verlust'];
                    echo "      <input TYPE=HIDDEN name='defferschiffweg" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_verlust'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='right' >";
                    echo        $schiffe['anzahl_ende'];
                    echo "      <input TYPE=HIDDEN name='defferschiffende" . $i . "_" . $j . "' type='text' value='" . $schiffe['anzahl_ende'] . "' readonly>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='Deffformf" . $i . "_" . $j . "' value='f'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='Deffformk" . $i . "_" . $j . "' value='k'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='Deffformu" . $i . "_" . $j . "' value='u'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <select name='Deffformc" . $i . "_" . $j . "' size='1'> ";
                    echo "         <option>keine</option> ";
                    echo "         <option>red</option> ";
                    echo "         <option>yellow</option> ";
                    echo "         <option>pink</option> ";
                    echo "         <option>green</option> ";
                    echo "         <option>orange</option> ";
                    echo "         <option>purple</option> ";
                    echo "         <option>blue</option> ";
                    echo "         <option>beige</option> ";
                    echo "         <option>brown</option> ";
                    echo "         <option>teal</option> ";
                    echo "         <option>navy</option> ";
                    echo "         <option>maroon</option> ";
                    echo "         <option>limegreen</option> ";
                    echo "       </select>";
                    echo "    </td>";
                    echo "  </tr>\n";

                    $j++;
                }
                $i++;
            }
        }

        // Verluste Angreifer

        if (empty($KBdata['kampf']['resverluste']['att']) == false) {
            echo "  <tr>\n";
            echo "     <td align='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            echo "       <b>Verluste des Angreiffers</b>";
            echo "     </td> ";
            echo "  </tr>\n ";
            echo "  <tr>\n";
            echo "    <td align='center'>";
            echo "     Ressource";
            echo "    </td>";
            echo "    <td colspan='3' align='center'>";
            echo "     Anzahl";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <b>F</b>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <i>K</i>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <u>U</u>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       Farbe";
            echo "    </td>";
            echo "  </tr>\n";
            foreach ($KBdata['kampf']['resverluste']['att'] as $loos) {
                echo "  <tr>\n";
                echo "     <td align='left'  >";
                echo         $loos['name'];
                echo "     </td>";
                echo "     <td colspan='3' align='right'>";
                echo         number_format($loos['anzahl'], 0, ",", " ");
                echo "       <input TYPE=HIDDEN name='attverlust" . $loos['id'] . "' type='text' value='" . number_format($loos['anzahl'], 0, ",", " ") . "' readonly>";
                echo "     </td>";
                echo "    <td align='center' >";
                echo "      <input type='checkbox' name='attverlustformf" . $loos['id'] . "' value='f'>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "      <input type='checkbox' name='attverlustformk" . $loos['id'] . "' value='k'>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "      <input type='checkbox' name='attverlustformu" . $loos['id'] . "' value='u'>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "      <select name='attverlustformc" . $loos['id'] . "' size='1'> ";
                echo "         <option>keine</option> ";
                echo "         <option>red</option> ";
                echo "         <option>yellow</option> ";
                echo "         <option>pink</option> ";
                echo "         <option>green</option> ";
                echo "         <option>orange</option> ";
                echo "         <option>purple</option> ";
                echo "         <option>blue</option> ";
                echo "         <option>beige</option> ";
                echo "         <option>brown</option> ";
                echo "         <option>teal</option> ";
                echo "         <option>navy</option> ";
                echo "         <option>maroon</option> ";
                echo "         <option>limegreen</option> ";
                echo "       </select>";
                echo "    </td>";
                echo "  </tr>\n";
            }
        }
        // Verluste Deffer

        if (empty($KBdata['kampf']['resverluste']['def']) == false) {
            echo "  <tr>\n";
            echo "     <td align='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            echo "       <b>Verluste des Verteidigers</b>";
            echo "     </td> ";
            echo "  </tr>\n ";
            echo "  <tr>\n";
            echo "    <td align='center'>";
            echo "     Ressource";
            echo "    </td>";
            echo "    <td colspan='3' align='center'>";
            echo "     Anzahl";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <b>F</b>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <i>K</i>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <u>U</u>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       Farbe";
            echo "    </td>";
            echo "  </tr>\n";
            foreach ($KBdata['kampf']['resverluste']['def'] as $loos) {
                echo "  <tr>\n";
                echo "     <td align='left' >";
                echo         $loos['name'];
                echo "     </td>";
                echo "     <td colspan='3' align='right' >";
                echo         number_format($loos['anzahl'], 0, ",", " ");
                echo "       <input TYPE=HIDDEN name='defverlust" . $loos['id'] . "' type='text' value='" . number_format($loos['anzahl'], 0, ",", " ") . "' readonly>";
                echo "     </td>";
                echo "    <td align='center' >";
                echo "      <input type='checkbox' name='defverlustformf" . $loos['id'] . "' value='f'>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "      <input type='checkbox' name='defverlustformk" . $loos['id'] . "' value='k'>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "      <input type='checkbox' name='defverlustformu" . $loos['id'] . "' value='u'>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "      <select name='defverlustformc" . $loos['id'] . "' size='1'> ";
                echo "         <option>keine</option> ";
                echo "         <option>red</option> ";
                echo "         <option>yellow</option> ";
                echo "         <option>pink</option> ";
                echo "         <option>green</option> ";
                echo "         <option>orange</option> ";
                echo "         <option>purple</option> ";
                echo "         <option>blue</option> ";
                echo "         <option>beige</option> ";
                echo "         <option>brown</option> ";
                echo "         <option>teal</option> ";
                echo "         <option>navy</option> ";
                echo "         <option>maroon</option> ";
                echo "         <option>limegreen</option> ";
                echo "       </select>";
                echo "    </td>";
                echo "  </tr>\n";
            }
        }

        // Plünderungen

        if (empty($KBdata['kampf']['pluenderung']) == false) {

            echo "  <tr>\n";
            echo "     <td align='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            echo "       <b>Es wurden folgende Ressourcen geplündert</b>";
            echo "     </td> ";
            echo "  </tr>\n ";
            echo "  <tr>\n";
            echo "    <td align='center'>";
            echo "     Ressource";
            echo "    </td>";
            echo "    <td colspan='3' align='center'>";
            echo "     Anzahl";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <b>F</b>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <i>K</i>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       <u>U</u>";
            echo "    </td>";
            echo "    <td align='center' >";
            echo "       Farbe";
            echo "    </td>";
            echo "  </tr>\n";
            foreach ($KBdata['kampf']['pluenderung'] as $loos) {
                echo "  <tr>\n";
                echo "     <td align='left' >";
                echo         $loos['name'];
                echo "     </td>";
                echo "     <td colspan='3' align='right'>";
                echo         number_format($loos['anzahl'], 0, ",", " ");
                echo "       <input TYPE=HIDDEN name='weg" . $loos['id'] . "' type='text' value='" . number_format($loos['anzahl'], 0, ",", " ") . "' readonly>";
                echo "     </td>";
                echo "    <td align='center' >";
                echo "      <input type='checkbox' name='wegformf" . $loos['id'] . "' value='f'>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "      <input type='checkbox' name='wegformk" . $loos['id'] . "' value='k'>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "      <input type='checkbox' name='wegformu" . $loos['id'] . "' value='u'>";
                echo "    </td>";
                echo "    <td align='center' >";
                echo "      <select name='wegformc" . $loos['id'] . "' size='1'> ";
                echo "         <option>keine</option> ";
                echo "         <option>red</option> ";
                echo "         <option>yellow</option> ";
                echo "         <option>pink</option> ";
                echo "         <option>green</option> ";
                echo "         <option>orange</option> ";
                echo "         <option>purple</option> ";
                echo "         <option>blue</option> ";
                echo "         <option>beige</option> ";
                echo "         <option>brown</option> ";
                echo "         <option>teal</option> ";
                echo "         <option>navy</option> ";
                echo "         <option>maroon</option> ";
                echo "         <option>limegreen</option> ";
                echo "       </select>";
                echo "    </td>";
                echo "  </tr>\n";
            }
        }

        // Bombing

        if (empty($KBdata['kampf']['bomben']) == false) {
            echo "  <tr>\n";
            echo "     <td align='left' colspan='8' style='color:#000000; background-color:#CAE1FF;'>";
            echo "       Der Planet wurde von <b>" . $KBdata['kampf']['bomben']['user']['name'] . "</b> ";
            echo "       <input TYPE=HIDDEN name='bomb1' type='text' value='Der Planet wurde von [b]" . $KBdata['kampf']['bomben']['user']['name'] . "[/b] ' readonly>";
            echo "       <input name='bomb2' type='text' value='bombadiert' size='30' >";
            echo "     </td> ";
            echo "  </tr>\n ";

            if (empty($KBdata['kampf']['bomben']['bombentrefferchance']) == false) // Bevölkerung
            {
                echo "  <tr>\n";
                echo "     <td align='left' colspan='8'>";
                echo "      <input name='bombtreff1' type='text' value='Staub, Rauch und Pfefferminzplätzchen hüllten den Planeten ein, so dass die Bombentrefferchance bei ' size='70'>";
                echo " <b>" . $KBdata['kampf']['bomben']['bombentrefferchance'] . "%</b> ";
                echo "      <input TYPE=HIDDEN name='bombtreff2' type='text' value=' [b]" . $KBdata['kampf']['bomben']['bombentrefferchance'] . "%[/b] ' readonly>";
                echo "      <input name='bombtreff3' type='text' value='lag' size='30'>";
                echo "     </td> ";
                echo "  </tr>\n ";
            }


            if (empty($KBdata['kampf']['bomben']['basis_zerstoert']) == false) //Basis
            {
                if ($KBdata['kampf']['bomben']['basis_zerstoert'] == 1) {
                    echo "  <tr>\n";
                    echo "     <td align='left' colspan='8'>";
                    echo "      <input name='bomb3' type='text' value='Irgendwer drückte aus Versehen auf den Selbstzerstörungsknopf. Damit endet die ruhmvolle Existenz der Basis. Plop.' size='100' >";
                    echo "     </td> ";
                    echo "  </tr>\n ";
                } else {
                    echo "  <tr>\n";
                    echo "     <td align='left' colspan='8'>";
                    echo "      <input name='bomb3' type='text' value='Der Wachhabende Offizier konnte den Selbstzerstörungsknopf nicht finden, daher steht die Basis noch.' size='100' >";
                    echo "     </td> ";
                    echo "  </tr>\n ";
                }
            } elseif (empty($KBdata['kampf']['bomben']['geb_zerstoert']) == true) //keine Gebäude
            {
                echo "  <tr>\n";
                echo "     <td align='left' colspan='8'>";
                echo "      <input name='bomb3' type='text' value='Ist nur blöderweise kein Gebäude getroffen worden.'>";
                echo "     </td> ";
                echo "  </tr>\n ";
            } else //mit Gebäuden
            {
                $i = 1;
                foreach ($KBdata['kampf']['bomben']['geb_zerstoert'] as $loos) {
                    echo "  <tr>\n";
                    echo "     <td align='left' >";
                    echo         $loos['name'];
                    echo "       <input TYPE=HIDDEN name='bombgeb" . $i . "' type='text' value='" . $loos['name'] . "' readonly>";
                    echo "     </td>";
                    echo "     <td colspan='3' align='right'>";
                    echo         $loos['anzahl'];
                    echo "       <input TYPE=HIDDEN name='bombgebanz" . $i . "' type='text' value='" . $loos['anzahl'] . "' readonly>";
                    echo "     </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='bombgebformf" . $i . "' value='f'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='bombgebformk" . $i . "' value='k'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <input type='checkbox' name='bombgebformu" . $i . "' value='u'>";
                    echo "    </td>";
                    echo "    <td align='center' >";
                    echo "      <select name='bombgebformc" . $i . "' size='1'> ";
                    echo "         <option>keine</option> ";
                    echo "         <option>red</option> ";
                    echo "         <option>yellow</option> ";
                    echo "         <option>pink</option> ";
                    echo "         <option>green</option> ";
                    echo "         <option>orange</option> ";
                    echo "         <option>purple</option> ";
                    echo "         <option>blue</option> ";
                    echo "         <option>beige</option> ";
                    echo "         <option>brown</option> ";
                    echo "         <option>teal</option> ";
                    echo "         <option>navy</option> ";
                    echo "         <option>maroon</option> ";
                    echo "         <option>limegreen</option> ";
                    echo "       </select>";
                    echo "    </td>";
                    echo "  </tr>\n";
                    $i++;
                }
            }
            if (empty($KBdata['kampf']['bomben']['bev_zerstoert']) == false) // Bevölkerung
            {
                echo "  <tr>\n";
                echo "     <td align='left' colspan='8'>";
                echo "      <input name='bombbev1' type='text' value='Bei der Bombadierung wurden' size='50'>";
                echo " <b>" . number_format($KBdata['kampf']['bomben']['bev_zerstoert'], 0, ",", " ") . "</b> ";
                echo "      <input TYPE=HIDDEN name='bombbev2' type='text' value=' [b]" . number_format($KBdata['kampf']['bomben']['bev_zerstoert'], 0, ",", " ") . "[/b] ' readonly>";
                echo "      <input name='bombbev3' type='text' value='Menschen getötet' size='50'>";
                echo "     </td> ";
                echo "  </tr>\n ";
            }
        }
        //Ende Bombing


        $KBLink = str_replace("&typ=xml", "", $KBLink);

        echo "  <tr>\n";
        echo "     <td align='center' colspan='8'>";
        echo "       <input TYPE=HIDDEN name='KBLink' type='text' value='" . $KBLink . "' readonly>";
        echo "       <input type='submit' value='BB-Code generieren' style='color:#FFFFFF; background-color: #00688B;'>";
        echo "     </td> ";
        echo "  </tr>\n ";


        echo "</table>\n";
        echo "</form>";

    }
}

if ($parsstatus == "write") // BB-Code ausgeben
{

// Dicken fetten String basteln für die Ausgabe

    $outBB = "[quote]<br>";
    $outBB = $outBB . $_POST['mainline'] . "<br>"; //Kampf auf dem ...
    $outBB = $outBB . $_POST['dateline'] . "<br>[hr]<br>"; //Die Schlacht endete mit ...

// Angreifer
    $i = 1;
    while (empty($_POST["atter" . $i]) == false) {
        $outBB = $outBB . $_POST["atter" . $i] . "<br>"; //Angreifende Flotte ...
        if (empty($_POST["atterkuchen" . $i]) == false) {
            $outBB = $outBB . "Die Flotte brachte Kaffee und Kuchen mit. <br>";
        }
        if (empty($_POST["atterlollies" . $i]) == false) {
            $outBB = $outBB . "Diese wilden Barbarben haben unseren kleinen Kindern die Lollis geklaut!! Das schreit gradezu nach Rache.<br>";
        }
        if (empty($_POST["attermsg" . $i]) == false) {
            $outBB = $outBB . $_POST["attermsg" . $i] . "<br>";
        }
        $outBB = $outBB . "[table]<br>";
        $outBB = $outBB . "[tr] [td][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anzahl[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zerstört[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Überlebend[/center][/td][/tr]<br>";
        $j     = 1;
        while (empty($_POST["atterschiffname" . $i . "_" . $j]) == false) {
            if ($_POST["atterschiffstart" . $i . "_" . $j] !== "") {
                $outBB = $outBB . "[tr][td]" . makeString($_POST["atterschiffname" . $i . "_" . $j], isset($_POST["Attformf" . $i . "_" . $j]), isset($_POST["Attformk" . $i . "_" . $j]), isset($_POST["Attformu" . $i . "_" . $j]), $_POST["Attformc" . $i . "_" . $j]) . "[/td]";
                $outBB = $outBB . "[td][right]" . makeString($_POST["atterschiffstart" . $i . "_" . $j], isset($_POST["Attformf" . $i . "_" . $j]), isset($_POST["Attformk" . $i . "_" . $j]), isset($_POST["Attformu" . $i . "_" . $j]), $_POST["Attformc" . $i . "_" . $j]) . "[/right][/td]";
                $outBB = $outBB . "[td][right]" . makeString($_POST["atterschiffweg" . $i . "_" . $j], isset($_POST["Attformf" . $i . "_" . $j]), isset($_POST["Attformk" . $i . "_" . $j]), isset($_POST["Attformu" . $i . "_" . $j]), $_POST["Attformc" . $i . "_" . $j]) . "[/right][/td]";
                $outBB = $outBB . "[td][right]" . makeString($_POST["atterschiffende" . $i . "_" . $j], isset($_POST["Attformf" . $i . "_" . $j]), isset($_POST["Attformk" . $i . "_" . $j]), isset($_POST["Attformu" . $i . "_" . $j]), $_POST["Attformc" . $i . "_" . $j]) . "[/right][/td][/tr]<br>";
            }
            $j++;
        }
        $outBB = $outBB . "[/table]<br>";
        $i++;
    }
    $outBB = $outBB . "[hr]<br>";
// Pladeff
    $i = 1;
    while (empty($_POST["pladeffer" . $i]) == false) {
        $outBB = $outBB . $_POST["pladeffer" . $i] . "<br>"; // Verteidiger ist ...
        $outBB = $outBB . "[table]<br>[tr][td][center]Deffanlagen[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anzahl[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zerstört[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Überlebend[/center][/td][/tr]<br>";
        $j     = 1;
        while (empty($_POST["pladefturm" . $i . "_" . $j]) == false) {
            $outBB = $outBB . "[tr][td]" . makeString($_POST["pladefturm" . $i . "_" . $j], isset($_POST["Pladefturmformf" . $i . "_" . $j]), isset($_POST["Pladefturmformk" . $i . "_" . $j]), isset($_POST["Pladefturmformu" . $i . "_" . $j]), $_POST["Pladefturmformc" . $i . "_" . $j]) . "[/td]";
            $outBB = $outBB . "[td][right]" . makeString($_POST["pladefturmstart" . $i . "_" . $j], isset($_POST["Pladefturmformf" . $i . "_" . $j]), isset($_POST["Pladefturmformk" . $i . "_" . $j]), isset($_POST["Pladefturmformu" . $i . "_" . $j]), $_POST["Pladefturmformc" . $i . "_" . $j]) . "[/right][/td]";
            $outBB = $outBB . "[td][right]" . makeString($_POST["pladefturmweg" . $i . "_" . $j], isset($_POST["Pladefturmformf" . $i . "_" . $j]), isset($_POST["Pladefturmformk" . $i . "_" . $j]), isset($_POST["Pladefturmformu" . $i . "_" . $j]), $_POST["Pladefturmformc" . $i . "_" . $j]) . "[/right][/td]";
            $outBB = $outBB . "[td][right]" . makeString($_POST["pladefturmende" . $i . "_" . $j], isset($_POST["Pladefturmformf" . $i . "_" . $j]), isset($_POST["Pladefturmformk" . $i . "_" . $j]), isset($_POST["Pladefturmformu" . $i . "_" . $j]), $_POST["Pladefturmformc" . $i . "_" . $j]) . "[/right][/td][/tr]<br>";
            $j++;
        }
        $j     = 1;
        $outBB = $outBB . "[tr][td][center]planetare Schiffe[/center][/td][/tr]<br>";
        while (empty($_POST["pladefschiff" . $i . "_" . $j]) == false) {
            $outBB = $outBB . "[tr][td]" . makeString($_POST["pladefschiff" . $i . "_" . $j], isset($_POST["Pladefschiffformf" . $i . "_" . $j]), isset($_POST["Pladefschiffformk" . $i . "_" . $j]), isset($_POST["Pladefschiffformu" . $i . "_" . $j]), $_POST["Pladefschiffformc" . $i . "_" . $j]) . "[/td]";
            $outBB = $outBB . "[td][right]" . makeString($_POST["pladefschiffstart" . $i . "_" . $j], isset($_POST["Pladefschiffformf" . $i . "_" . $j]), isset($_POST["Pladefschiffformk" . $i . "_" . $j]), isset($_POST["Pladefschiffformu" . $i . "_" . $j]), $_POST["Pladefschiffformc" . $i . "_" . $j]) . "[/right][/td]";
            $outBB = $outBB . "[td][right]" . makeString($_POST["pladefschiffweg" . $i . "_" . $j], isset($_POST["Pladefschiffformf" . $i . "_" . $j]), isset($_POST["Pladefschiffformk" . $i . "_" . $j]), isset($_POST["Pladefschiffformu" . $i . "_" . $j]), $_POST["Pladefschiffformc" . $i . "_" . $j]) . "[/right][/td]";
            $outBB = $outBB . "[td][right]" . makeString($_POST["pladefschiffende" . $i . "_" . $j], isset($_POST["Pladefschiffformf" . $i . "_" . $j]), isset($_POST["Pladefschiffformk" . $i . "_" . $j]), isset($_POST["Pladefschiffformu" . $i . "_" . $j]), $_POST["Pladefschiffformc" . $i . "_" . $j]) . "[/right][/td][/tr]<br>";
            $j++;
        }
        $outBB = $outBB . "[/table]<br>";
        $i++;
    }
// Deffer
    $i = 1;
    while (empty($_POST["deffer" . $i]) == false) {
        $outBB = $outBB . $_POST["deffer" . $i] . "<br>[table]<br>"; //Verteidigende Flotte ...
        $outBB = $outBB . "[tr][td] [/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Anzahl[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Zerstört[/center][/td][td][center]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Überlebend[/center][/td][/tr]<br>";
        $j     = 1;
        while (empty($_POST["defferschiffname" . $i . "_" . $j]) == false) {
            if ($_POST["defferschiffstart" . $i . "_" . $j] !== "") {
                $outBB = $outBB . "[tr][td]" . makeString($_POST["defferschiffname" . $i . "_" . $j], isset($_POST["Deffformf" . $i . "_" . $j]), isset($_POST["Deffformk" . $i . "_" . $j]), isset($_POST["Deffformu" . $i . "_" . $j]), $_POST["Deffformc" . $i . "_" . $j]) . "[/td]";
                $outBB = $outBB . "[td][right]" . makeString($_POST["defferschiffstart" . $i . "_" . $j], isset($_POST["Deffformf" . $i . "_" . $j]), isset($_POST["Deffformk" . $i . "_" . $j]), isset($_POST["Deffformu" . $i . "_" . $j]), $_POST["Deffformc" . $i . "_" . $j]) . "[/right][/td]";
                $outBB = $outBB . "[td][right]" . makeString($_POST["defferschiffweg" . $i . "_" . $j], isset($_POST["Deffformf" . $i . "_" . $j]), isset($_POST["Deffformk" . $i . "_" . $j]), isset($_POST["Deffformu" . $i . "_" . $j]), $_POST["Deffformc" . $i . "_" . $j]) . "[/right][/td]";
                $outBB = $outBB . "[td][right]" . makeString($_POST["defferschiffende" . $i . "_" . $j], isset($_POST["Deffformf" . $i . "_" . $j]), isset($_POST["Deffformk" . $i . "_" . $j]), isset($_POST["Deffformu" . $i . "_" . $j]), $_POST["Deffformc" . $i . "_" . $j]) . "[/right][/td][/tr]<br>";
            }
            $j++;
        }
        $outBB = $outBB . "[/table]<br>";
        $i++;
    }
    $outBB = $outBB . "[hr]<br>";
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
        if ($do == true) {
            break;
        }
        $i++;
    }
    if ($do == true) {
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
        $outBB        = $outBB . "Vernichtete und geplünderte Ressourcen<br>";
        $outBB        = $outBB . "[table]<br>[tr][td] [/td][td]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Verteidiger[/td][td]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Angreifer[/td][td]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Plünderung[/td][/tr]<br>";
        while ($i <= 11) {
            if (empty($_POST["attverlust" . $i]) == false || empty($_POST["defverlust" . $i]) == false || empty($_POST["weg" . $i]) == false) {
                $outBB = $outBB . "[tr][td]" . $ressname[$i] . "[/td]";
                if (empty($_POST["defverlust" . $i]) == false) {
                    $outBB = $outBB . "[td][right]" . makeString($_POST["defverlust" . $i], isset($_POST["defverlustformf" . $i]), isset($_POST["defverlustformk" . $i]), isset($_POST["defverlustformu" . $i]), $_POST["defverlustformc" . $i]) . "[/right][/td]";
                } else {
                    $outBB = $outBB . "[td][center]-[/center][/td]";
                }
                if (empty($_POST["attverlust" . $i]) == false) {
                    $outBB = $outBB . "[td][right]" . makeString($_POST["attverlust" . $i], isset($_POST["attverlustformf" . $i]), isset($_POST["attverlustformk" . $i]), isset($_POST["attverlustformu" . $i]), $_POST["attverlustformc" . $i]) . "[/right][/td]";
                } else {
                    $outBB = $outBB . "[td][center]-[/center][/td]";
                }
                if (empty($_POST["weg" . $i]) == false) {
                    $outBB = $outBB . "[td][right]" . makeString($_POST["weg" . $i], isset($_POST["wegformf" . $i]), isset($_POST["wegformk" . $i]), isset($_POST["wegformu" . $i]), $_POST["wegformc" . $i]) . "[/right][/td][/tr]";
                } else {
                    $outBB = $outBB . "[td][center]-[/center][/td][/tr]<br>";
                }
            }
            $i++;
        }
        $outBB = $outBB . "[/table]<br>";
        $outBB = $outBB . "[hr]<br>";
    }

//Bomben

    if (empty($_POST['bomb1']) == false) {
        $outBB = $outBB . $_POST['bomb1'] . $_POST['bomb2'] . "<br>"; // Der Planet wurde ...
        if (empty($_POST['bombtreff1']) == false) {
            $outBB = $outBB . $_POST['bombtreff1'] . $_POST['bombtreff2'] . $_POST['bombtreff3'] . ".<br>";
        } // Bombendtrefferchance
        if (empty($_POST['bomb3']) == false) {
            $outBB = $outBB . $_POST['bomb3'] . "<br>";
        } // KB oder keine Gebs
        else {
            $i     = 1;
            $outBB = $outBB . "Folgende Gebäude wurden dem Erdboden gleich gemacht:<br>[table]<br>";
            while (empty($_POST["bombgeb" . $i]) == false) {
                $outBB = $outBB . "[tr][td]" . makeString($_POST["bombgeb" . $i], isset($_POST["bombgebformf" . $i]), isset($_POST["bombgebformk" . $i]), isset($_POST["bombgebformu" . $i]), $_POST["bombgebformc" . $i]) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[/td][td][right]" . makeString($_POST["bombgebanz" . $i], isset($_POST["bombgebformf" . $i]), isset($_POST["bombgebformk" . $i]), isset($_POST["bombgebformu" . $i]), $_POST["bombgebformc" . $i]) . "[/right][/td][/tr]<br>";
                $i++;
            }
            $outBB = $outBB . "[/table]<br>";
        }
        if (empty($_POST['bombbev1']) == false) {
            $outBB = $outBB . $_POST['bombbev1'] . $_POST['bombbev2'] . $_POST['bombbev3'] . "<br>";
        }
        $outBB = $outBB . "[hr]<br>";
    }

//KBLink

    $outBB = $outBB . "[url=" . $_POST['KBLink'] . "]externer Link[/url]<br>[/quote]";


// Lange Schiffnamen killen
    $outBB = str_replace("(Hyperraumtransporter Klasse 1)", "", $outBB); // Gorgol, Kamel, Flughund
    $outBB = str_replace("(Hyperraumtransporter Klasse 2)", "", $outBB); // Eisbär, Waschbär, Seepferd
    $outBB = str_replace("(Systemtransporter Kolonisten)", "", $outBB); // Crux
    $outBB = str_replace("(Hyperraumtransporter Kolonisten)", "", $outBB); // Kolpor
    $outBB = str_replace("(Systemtransporter Klasse 1)", "", $outBB); // Systrans
    $outBB = str_replace("(Systemtransporter Klasse 2)", "", $outBB); // Lurch
    $outBB = str_replace("(Transporter)", "", $outBB); // Osterschiff
    $outBB = str_replace("(interstellares Kolonieschiff)", "", $outBB); // INS
    $outBB = str_replace("(Systemkolonieschiff)", "", $outBB); // KIS


//Daraus einen Html Code basteln / auch nicht gerade schön
    $outHTML = $outBB;
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
        $outHTML = str_replace("[center]", "<p align='center'>", $outHTML);
        $outHTML = str_replace("[/center]", "</p>", $outHTML);
        $outHTML = str_replace("[right]", "<p align='right'>", $outHTML);
        $outHTML = str_replace("[/right]", "</p>", $outHTML);
        $outHTML = str_replace("[left]", "<p align='left'>", $outHTML);
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
        $outHTML = str_replace("]externer Link[/url]", "' target='_blank'>externer Link</a> ", $outHTML);
    }

//BBCode beschneiden, Nicht gerade schön aber zu faul für den Rest
    if (empty($_POST['optionQuote']) == true) //checkbox quoten nicht aktiv
    {
        $outBB = str_replace("[quote]<br>", "", $outBB);
        $outBB = str_replace("<br>[/quote]", "", $outBB);

    }

    if (empty($_POST['optionLink']) == false) {
        $outBB = str_replace("[url=", "", $outBB);
        $outBB = str_replace("]externer Link[/url]", "", $outBB);

    }

    if (empty($_POST['optionTab']) == true) {
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

    }

    if (empty($_POST['optionHr']) == true) {
        $outBB = str_replace("[hr]", "", $outBB);
    }

    if (empty($_POST['optionAlign']) == true || empty($_POST['optionTab']) == true) {
        $outBB = str_replace("[center]", "", $outBB);
        $outBB = str_replace("[/center]", "", $outBB);
        $outBB = str_replace("[left]", "", $outBB);
        $outBB = str_replace("[/left]", "", $outBB);
        $outBB = str_replace("[right]", "", $outBB);
        $outBB = str_replace("[/right]", "", $outBB);
    }

    if (empty($_POST['optionColor']) == true) { //Da ich für pregreplace zu faul bin
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


    if (empty($_POST['optionForm']) == true) {
        $outBB = str_replace("[b]", "", $outBB);
        $outBB = str_replace("[/b]", "", $outBB);
        $outBB = str_replace("[i]", "", $outBB);
        $outBB = str_replace("[/i]", "", $outBB);
        $outBB = str_replace("[u]", "", $outBB);
        $outBB = str_replace("[/u]", "", $outBB);
    }


    echo "<table border='0' cellpadding='2' cellspacing='1' style='width: 90%;'>\n";
    echo "  <tr>\n";
    echo "     <td align='left' colspan='2'>";
    echo $outBB;
    echo "     <br></td>";
    echo "  </tr>\n";
// Eventuell HTML-Code ausgeben
    if (empty($_POST['optionHtml']) == false) {
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
    }
    echo "</table>";
}

function file_get_contents_utf8($fn)
{
    $content = file_get_contents($fn);

    return mb_convert_encoding(
        $content, 'UTF-8',
        mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)
    );
}
?>