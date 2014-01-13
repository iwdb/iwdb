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
$modultitle = "KB-Parser";

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
    //nothing here
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
    if ($user_status !== "admin") {
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

// Einen String mit Fett, Kursiv, Unterstrichen und Farbe versehen
function makeFormatedString($string, $fett, $kursiv, $unterstrichen, $farbe)
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
    if (!empty($farbe) AND ($farbe !== "keine")) {
        $string = "[color=" . $farbe . "]" . $string . "[/color]";
    }

    return $string;
}

//Nachrichten für erfolgreiches Basisbomben
function getMessageBasebombSuccessful() {
    $aMessages = array(
        "Irgendwer drückte aus Versehen auf den Selbstzerstörungsknopf. Damit endet die ruhmvolle Existenz der Basis. Plop.",
        "Der Azubi hat mal wieder ein paar Knöpfe verwechselt. Das überhitzte den Reaktor und beendete die Existenz der Basis. Plop.",
        "Die anfliegenden Bomben zerlegten die Basis in " . mt_rand(100,10000) . " Stücke.",
        "Die Erschütterungen zerstörten die wertvolle Ming-Vase des Wachhabenden. Mit dieser Schande wollte er nicht weiterleben und sprengte die Basis.",
        "Der Wachhabende beschloss spontan Urlaub zu machen und sprengte die Basis.",
        "Die Explosionen beschädigten die komplexe Bierzapfanlage. Es war unzumutbar die Basis weiterzubetreiben. Plop.",
        "Die Basis ploppte wie eine Flasche Plexbier."
    );

    return $aMessages[array_rand($aMessages, 1)];
}

//Nachrichten für gescheitertes Basisbomben
function getMessageBasebombUnsuccessful() {
    $aMessages = array(
        "Der Wachhabende Offizier konnte den Selbstzerstörungsknopf nicht finden, daher steht die Basis noch.",
        "Die feindlichen Bomber produzierten ein wunderschönes Feuerwerk, die Besatzung war begeistert und die Basis steht noch.",
        "Aus den Bomben kam nur ein bisschen Rauch, die Basis steht noch.",
        "Die Bomben richteten etwas Schaden an, etwas Klebstoff und Hoffnung hällt sie aber weiterhin zusammen.",
    );

    return $aMessages[array_rand($aMessages, 1)];
}

//Nachrichten für gebombte Bevölkerung
function getMessageBevbomb() {
    $aMessages = array(
        "Leute starben durch die Bombardierung.",
        "Leute sind den ständigen Krach leid und zogen weg.",
        "Leute starben an einer Pfefferminzallergie.",
        "Leute sind wegen Biermangel verdurstet!"
    );

    return $aMessages[array_rand($aMessages, 1)];
}

function generateKBparserform($xml, $modulname, $KBLink)
{
    $html =  "<form method='post'>";
    $html .=  "<input type='hidden' name='action' value='" . $modulname . "'>";
    $html .=  "<input type='hidden' name='parsstatus' value='write'>";
    $html .=  "<table class='table_format' style='width: 95%;'>";

    // Optionen
    $html .=  "  <tr>";
    $html .=  "     <td class='center' colspan=8 >";
    $html .=  "        Optionen: <input type='checkbox' name='optionQuote' value='on' checked>Quoten";
    $html .=  "        / <input type='checkbox' name='optionAlign' value='on' checked>Ausrichtung";
    $html .=  "        / <input type='checkbox' name='optionColor' value='on' checked>Farbe";
    $html .=  "        / <input type='checkbox' name='optionForm' value='on' checked>Format";
    $html .=  "        / <input type='checkbox' name='optionKuerzen' value='on' checked>Schiffnamen kürzen";
    $html .=  "        / <input type='checkbox' name='optionHr' value='on' checked>horizonale Linien";
    $html .=  "        / <input type='checkbox' name='optionLink' value='on'>KB-Link in Quote-Tags";
    $html .=  "        / <input type='checkbox' name='optionColspan' value='on'>verkürzte Colspans ([td=x])";
    $html .=  "     </td>";
    $html .=  "  </tr>";

    $html .=  "  <tr>";
    $html .=  "     <td class='center' colspan='8'>";
    $html .=  "       <input type='submit' value='BB-Code gleich generieren' class='btn'>";
    $html .=  "     </td>";
    $html .=  "  </tr>";

    // Kampf auf dem Planeten
    $html .=  "  <tr>";
    $html .=  "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
    $html .=  "        <input type='hidden' name='mainline' type='text' value='" . urlencode("Kampf auf dem Planeten [b]" . (string)$xml->plani_data->koordinaten->string['value'] . "[/b]. Besitzer ist [b]" . (string)$xml->plani_data->user->name['value'] . (empty($xml->plani_data->user->allianz_tag['value']) ? "" : " [" . (string)$xml->plani_data->user->allianz_tag['value'] . "]") . "[/b]") . "'>";
    $html .=  "        Kampf auf dem Planeten <b>" . (string)$xml->plani_data->koordinaten->string['value'] . "</b>. Besitzer ist <b>" . (string)$xml->plani_data->user->name['value'] . (empty($xml->plani_data->user->allianz_tag['value']) ? "" : " [" . (string)$xml->plani_data->user->allianz_tag['value'] . "]") . "</b>";
    $html .=  "     </td>";
    $html .=  "  </tr>";

    // Die Schlacht endete am xx.xx.xxxx um xx:xx:xx mit einem Sieg für den ...
    $html .=  "  <tr>";
    $html .=  "     <td class='windowbg1 left' colspan='8'>";
    $html .=  "Die Schlacht endete " . strftime(" am <b>%d.%m.%Y</b> um <b>%H:%M:%S</b>", (int)$xml->timestamp['value']) . " mit einem Sieg für den ";
    if ((int)$xml->resultat->id['value'] === 1) {
        $html .=  "<span style='color:green; font-weight:bold;'>Angreifer</span>.";
    } else {
        $html .=  "<span style='color:red; font-weight:bold;'>Verteidiger</span>.";
    }
    $html .=  "        <input type='hidden' name='dateline' type='text' value='" . urlencode("Die Schlacht endete " . strftime("am [b]%d.%m.%Y[/b] um [b]%H:%M:%S[/b] ", (int)$xml->timestamp['value']) . " mit einem Sieg für den ");
    if ((int)$xml->resultat->id['value'] === 1) {
        $html .=  urlencode("[color=green][b]Angreifer[/b][/color].");
    } else {
        $html .=  urlencode("[color=red][b]Verteidiger[/b][/color].");
    }
    $html .=  "'>";
    $html .=  "     </td>";
    $html .=  "  </tr>";

    // Angreifende Flotten
    if (isset($xml->flotten_att->user)) {
        $i    = 1;
        foreach ($xml->flotten_att->user as $user_data) {

            $html .=  "  <tr>";
            $html .=  "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            $html .=  "       Angreifende Flotte von <b>" . (string)$user_data->name['value'] . (empty($user_data->allianz_tag['value']) ? "" : " [" . (string)$user_data->allianz_tag['value'] . "]") . "</b>, Startplanet war <b>" . (string)$user_data->startplanet->koordinaten->string['value'] . "</b>.";
            $html .=  "       <input type='hidden' name='atter" . $i . "' type='text' value='" . urlencode("[b]" . (string)$user_data->name['value'] . (empty($user_data->allianz_tag['value']) ? "" : " [" . (string)$user_data->allianz_tag['value'] . "]") . "[/b] von " . (string)$user_data->startplanet->koordinaten->string['value']) . "'>";
            $html .=  "     </td>";
            $html .=  "  </tr>";
            //Schiffe
            $html .=  "  <tr>";
            $html .=  "    <td class='center'>Schiffname</td>";
            $html .=  "    <td class='center'>Anzahl</td>";
            $html .=  "    <td class='center'>Zerstört</td>";
            $html .=  "    <td class='center'>Überlebend</td>";
            $html .=  "    <td class='center bold'>F</td>";
            $html .=  "    <td class='center italic'>K</td>";
            $html .=  "    <td class='center underline'>U</td>";
            $html .=  "    <td class='center'>Farbe</td>";
            $html .=  "  </tr>";

            if (isset($user_data->schiffe)) {
                $j       = 1;
                foreach ($user_data->schiffe->schifftyp as $schiffe_data) {
                    $html .=  "  <tr>";
                    $html .=  "    <td class='left'>";
                    $html .=  (string)$schiffe_data->name['value'];
                    $html .=  "      <input type='hidden' name='atterschiffname" . $i . "_" . $j . "' type='text' value='" . urlencode((string)$schiffe_data->name['value']) . "'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='right'>";
                    $html .=  number_format((float)$schiffe_data->anzahl_start['value'], 0, ',', '.');
                    $html .=  "      <input type='hidden' name='atterschiffstart" . $i . "_" . $j . "' type='text' value='" . number_format((float)$schiffe_data->anzahl_start['value'], 0, ",", ".") . "'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='right'>";
                    $html .=  number_format((float)$schiffe_data->anzahl_verlust['value'], 0, ',', '.');
                    $html .=  "      <input type='hidden' name='atterschiffweg" . $i . "_" . $j . "' type='text' value='" . number_format((float)$schiffe_data->anzahl_verlust['value'], 0, ",", ".") . "'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='right'>";
                    $html .=  number_format((float)$schiffe_data->anzahl_ende['value'], 0, ',', '.');
                    $html .=  "      <input type='hidden' name='atterschiffende" . $i . "_" . $j . "' type='text' value='" . number_format((float)$schiffe_data->anzahl_ende['value'], 0, ",", ".") . "'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='center'>";
                    $html .=  "      <input type='checkbox' name='Attformf" . $i . "_" . $j . "' value='f'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='center'>";
                    $html .=  "      <input type='checkbox' name='Attformk" . $i . "_" . $j . "' value='k'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='center'>";
                    $html .=  "      <input type='checkbox' name='Attformu" . $i . "_" . $j . "' value='u'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='center'>";
                    $html .=  "      <select name='Attformc" . $i . "_" . $j . "'>";
                    $html .=  "         <option>keine</option>";
                    $html .=  "         <option style='color:red' value='red'>rot</option>";
                    $html .=  "         <option style='color:yellow' value='yellow'>gelb</option>";
                    $html .=  "         <option style='color:pink' value='pink'>pink</option>";
                    $html .=  "         <option style='color:green' value='green'>grün</option>";
                    $html .=  "         <option style='color:orange' value='orange'>orange</option>";
                    $html .=  "         <option style='color:purple' value='purple'>violett</option>";
                    $html .=  "         <option style='color:blue' value='blue'>blau</option>";
                    $html .=  "         <option style='color:beige' value='beige'>beige</option>";
                    $html .=  "         <option style='color:brown' value='brown'>braun</option>";
                    $html .=  "         <option style='color:teal' value='teal'>türkis</option>";
                    $html .=  "         <option style='color:navy' value='navy'>dunkelblau</option>";
                    $html .=  "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                    $html .=  "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                    $html .=  "       </select>";
                    $html .=  "    </td>";
                    $html .=  "  </tr>";

                    $j++;
                }
            }

            if (!empty($user_data->bloedsinn->kaffee['value'])) {
                $html .=  "  <tr>";
                $html .=  "     <td class='left' colspan='8'>";
                $html .=  "        Die Flotte brachte Kaffee und Kuchen mit.";
                $html .=  "        <input type='hidden' name='atterkuchen" . $i . "' type='text' value='1'>";
                $html .=  "     </td>";
                $html .=  "  </tr>";
            }
            if (!empty($user_data->bloedsinn->lollies['value'])) {
                $html .=  "  <tr>";
                $html .=  "     <td class='left' colspan='8'>";
                $html .=  "        Diese wilden Barbarben haben unseren kleinen Kindern die Lollis geklaut!! Das schreit gradezu nach Rache.";
                $html .=  "        <input type='hidden' name='atterlollies" . $i . "' type='text' value='1'>";
                $html .=  "     </td>";
                $html .=  "  </tr>";
            }
            if (!empty($user_data->bloedsinn->msg['value'])) {
                $html .=  "  <tr>";
                $html .=  "     <td class='left' colspan='8'>";
                $html .=  "        Der Kommandant der angreifenden Flotte überbrachte folgende Botschaft:<br>" . (string)$user_data->bloedsinn->msg;
                $html .=  "        <input type='hidden' name='attermsg" . $i . "' type='text' value='" . urlencode("Der Kommandant der angreifenden Flotte überbrachte folgende Botschaft:[/td][/tr][tr][td colspan=4][color=brown][i]" . (string)$user_data->bloedsinn->msg . "[/i][/color]'>");
                $html .=  "     </td>";
                $html .=  "  </tr>";
            }

            $i++;
        }
    }

    // Def
    if (!empty($xml->pla_def)) {

        $html .=  "  <tr>";
        $html .=  "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;'>";
        $html .=  "       Verteidiger war <b>" . (string)$xml->pla_def->user->name['value'] . (empty($xml->pla_def->user->allianz_tag['value']) ? "" : " [" . (string)$xml->pla_def->user->allianz_tag['value'] . "]") . "</b>";
        $html .=  "       <input type='hidden' name='pladeffer1' type='text' value='" . urlencode("[b]" . (string)$xml->pla_def->user->name['value'] . (empty($xml->pla_def->user->allianz_tag['value']) ? "" : " [" . (string)$xml->pla_def->user->allianz_tag['value'] . "]") . "[/b]") . "'>";
        $html .=  "     </td>";
        $html .=  "  </tr>";

        //stationäre Türme
        if (!empty($xml->pla_def->user->defence)) {
            $html .=  "  <tr>";
            $html .=  "    <td class='center'>Verteidigungsanlage</td>";
            $html .=  "    <td class='center'>Anzahl</td>";
            $html .=  "    <td class='center'>Zerstört</td>";
            $html .=  "    <td class='center'>Überlebend</td>";
            $html .=  "    <td class='center bold'>F</td>";
            $html .=  "    <td class='center italic'>K</td>";
            $html .=  "    <td class='center underline'>U</td>";
            $html .=  "    <td class='center'>Farbe</td>";
            $html .=  "  </tr>";

            $j = 1;
            foreach ($xml->pla_def->user->defence->defencetyp as $def_data) {

                $html .=  "  <tr>";
                $html .=  "    <td class='left' >";
                $html .=  (string)$def_data->name['value'];
                $html .=  "      <input type='hidden' name='pladefturm_" . $j . "' type='text' value='" . urlencode((string)$def_data->name['value']) . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='right' >";
                $html .=  number_format((float)$def_data->anzahl_start['value'], 0, ',', '.');
                $html .=  "      <input type='hidden' name='pladefturm_start_" . $j . "' type='text' value='" . number_format((float)$def_data->anzahl_start['value'], 0, ",", ".") . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='right' >";
                $html .=  number_format((float)$def_data->anzahl_verlust['value'], 0, ',', '.');
                $html .=  "      <input type='hidden' name='pladefturm_verlust_" . $j . "' type='text' value='" . number_format((float)$def_data->anzahl_verlust['value'], 0, ",", ".") . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='right' >";
                $html .=  number_format((float)$def_data->anzahl_ende['value'], 0, ',', '.');
                $html .=  "      <input type='hidden' name='pladefturm_ende_" . $j . "' type='text' value='" . number_format((float)$def_data->anzahl_ende['value'], 0, ",", ".") . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <input type='checkbox' name='Pladefturm_form_f_" . $j . "' value='f'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <input type='checkbox' name='Pladefturm_form_k_" . $j . "' value='k'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <input type='checkbox' name='Pladefturm_form_u_" . $j . "' value='u'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <select name='Pladefturm_form_c_" . $j . "'>";
                $html .=  "         <option>keine</option>";
                $html .=  "         <option style='color:red' value='red'>rot</option>";
                $html .=  "         <option style='color:yellow' value='yellow'>gelb</option>";
                $html .=  "         <option style='color:pink' value='pink'>pink</option>";
                $html .=  "         <option style='color:green' value='green'>grün</option>";
                $html .=  "         <option style='color:orange' value='orange'>orange</option>";
                $html .=  "         <option style='color:purple' value='purple'>violett</option>";
                $html .=  "         <option style='color:blue' value='blue'>blau</option>";
                $html .=  "         <option style='color:beige' value='beige'>beige</option>";
                $html .=  "         <option style='color:brown' value='brown'>braun</option>";
                $html .=  "         <option style='color:teal' value='teal'>türkis</option>";
                $html .=  "         <option style='color:navy' value='navy'>dunkelblau</option>";
                $html .=  "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                $html .=  "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                $html .=  "       </select>";
                $html .=  "    </td>";
                $html .=  "  </tr>";

                $j++;
            }
        }

        //planetare flotte
        if (!empty($xml->pla_def->user->schiffe)) {
            $html .=  "  <tr>";
            $html .=  "    <td class='center'>Schiffnamen</td>";
            $html .=  "    <td class='center'>Anzahl</td>";
            $html .=  "    <td class='center'>Zerstört</td>";
            $html .=  "    <td class='center'>Überlebend</td>";
            $html .=  "    <td class='center bold'>F</td>";
            $html .=  "    <td class='center italic'>K</td>";
            $html .=  "    <td class='center underline'>U</td>";
            $html .=  "    <td class='center'>Farbe</td>";
            $html .=  "  </tr>";

            $j = 1;
            foreach ($xml->pla_def->user->schiffe->schifftyp as $schiff_data) {
                $html .=  "  <tr>";
                $html .=  "    <td class='left' >";
                $html .=  (string)$schiff_data->name['value'];
                $html .=  "      <input type='hidden' name='pladefschiff_" . $j . "' type='text' value='" . urlencode((string)$schiff_data->name['value']) . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='right' >";
                $html .=  number_format((float)$schiff_data->anzahl_start['value'], 0, ',', '.');
                $html .=  "      <input type='hidden' name='pladefschiff_start_" . $j . "' type='text' value='" . number_format((float)$schiff_data->anzahl_start['value'], 0, ",", ".") . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='right' >";
                $html .=  number_format((float)$schiff_data->anzahl_verlust['value'], 0, ',', '.');
                $html .=  "      <input type='hidden' name='pladefschiff_verlust_" . $j . "' type='text' value='" . number_format((float)$schiff_data->anzahl_verlust['value'], 0, ",", ".") . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='right' >";
                $html .=  number_format((float)$schiff_data->anzahl_ende['value'], 0, ',', '.');
                $html .=  "      <input type='hidden' name='pladefschiff_ende_" . $j . "' type='text' value='" . number_format((float)$schiff_data->anzahl_ende['value'], 0, ",", ".") . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <input type='checkbox' name='Pladefschiff_form_f_" . $j . "' value='f'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <input type='checkbox' name='Pladefschiff_form_k_" . $j . "' value='k'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <input type='checkbox' name='Pladefschiff_form_u_" . $j . "' value='u'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <select name='Pladefschiff_form_c_" . $j . "'>";
                $html .=  "         <option>keine</option>";
                $html .=  "         <option style='color:red' value='red'>rot</option>";
                $html .=  "         <option style='color:yellow' value='yellow'>gelb</option>";
                $html .=  "         <option style='color:pink' value='pink'>pink</option>";
                $html .=  "         <option style='color:green' value='green'>grün</option>";
                $html .=  "         <option style='color:orange' value='orange'>orange</option>";
                $html .=  "         <option style='color:purple' value='purple'>violett</option>";
                $html .=  "         <option style='color:blue' value='blue'>blau</option>";
                $html .=  "         <option style='color:beige' value='beige'>beige</option>";
                $html .=  "         <option style='color:brown' value='brown'>braun</option>";
                $html .=  "         <option style='color:teal' value='teal'>türkis</option>";
                $html .=  "         <option style='color:navy' value='navy'>dunkelblau</option>";
                $html .=  "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                $html .=  "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                $html .=  "       </select>";
                $html .=  "    </td>";
                $html .=  "  </tr>";

                $j++;
            }
        }

    }

    // Deffende Flotten im Orbit
    if (!empty($xml->flotten_def)) {
        $i = 1;
        foreach ($xml->flotten_def->user as $deff_user) {
            $html .=  "  <tr>";
            $html .=  "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
            $html .=  "       Verteidigende Flotte von <b>" . $deff_user->name['value'] . (empty($deff_user->allianz_tag['value']) ? "" : " [" . (string)$deff_user->allianz_tag['value'] . "]") . "</b>";
            $html .=  "       <input type='hidden' name='deffer" . $i . "' type='text' value='" . urlencode("[b]" . $deff_user->name['value'] . (empty($deff_user->allianz_tag['value']) ? "" : " [" . (string)$deff_user->allianz_tag['value'] . "]") . "[/b]") . "'>";
            $html .=  "     </td>";
            $html .=  "  </tr>";
            //schiffe
            $html .=  "  <tr>";
            $html .=  "    <td class='center'>Schiffnamen</td>";
            $html .=  "    <td class='center'>Anzahl</td>";
            $html .=  "    <td class='center'>Zerstört</td>";
            $html .=  "    <td class='center'>Überlebend</td>";
            $html .=  "    <td class='center bold'>F</td>";
            $html .=  "    <td class='center italic'>K</td>";
            $html .=  "    <td class='center underline'>U</td>";
            $html .=  "    <td class='center'>Farbe</td>";
            $html .=  "  </tr>";
            $j = 1;
            foreach ($deff_user->schiffe->schifftyp as $schiff_data) {
                $html .=  "  <tr>";
                $html .=  "    <td class='left' >";
                $html .=  (string)$schiff_data->name['value'];
                $html .=  "      <input type='hidden' name='defferschiffname" . $i . "_" . $j . "' type='text' value='" . urlencode((string)$schiff_data->name['value']) . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='right' >";
                $html .=  number_format((float)$schiff_data->anzahl_start['value'], 0, ',', '.');
                $html .=  "      <input type='hidden' name='defferschiffstart" . $i . "_" . $j . "' type='text' value='" . number_format((float)$schiff_data->anzahl_start['value'], 0, ",", ".") . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='right' >";
                $html .=  number_format((float)$schiff_data->anzahl_verlust['value'], 0, ',', '.');
                $html .=  "      <input type='hidden' name='defferschiffweg" . $i . "_" . $j . "' type='text' value='" . number_format((float)$schiff_data->anzahl_verlust['value'], 0, ",", ".") . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='right' >";
                $html .=  number_format((float)$schiff_data->anzahl_ende['value'], 0, ',', '.');
                $html .=  "      <input type='hidden' name='defferschiffende" . $i . "_" . $j . "' type='text' value='" . number_format((float)$schiff_data->anzahl_ende['value'], 0, ",", ".") . "'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <input type='checkbox' name='Deffformf" . $i . "_" . $j . "' value='f'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <input type='checkbox' name='Deffformk" . $i . "_" . $j . "' value='k'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <input type='checkbox' name='Deffformu" . $i . "_" . $j . "' value='u'>";
                $html .=  "    </td>";
                $html .=  "    <td class='center' >";
                $html .=  "      <select name='Deffformc" . $i . "_" . $j . "'>";
                $html .=  "         <option>keine</option>";
                $html .=  "         <option style='color:red' value='red'>rot</option>";
                $html .=  "         <option style='color:yellow' value='yellow'>gelb</option>";
                $html .=  "         <option style='color:pink' value='pink'>pink</option>";
                $html .=  "         <option style='color:green' value='green'>grün</option>";
                $html .=  "         <option style='color:orange' value='orange'>orange</option>";
                $html .=  "         <option style='color:purple' value='purple'>violett</option>";
                $html .=  "         <option style='color:blue' value='blue'>blau</option>";
                $html .=  "         <option style='color:beige' value='beige'>beige</option>";
                $html .=  "         <option style='color:brown' value='brown'>braun</option>";
                $html .=  "         <option style='color:teal' value='teal'>türkis</option>";
                $html .=  "         <option style='color:navy' value='navy'>dunkelblau</option>";
                $html .=  "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                $html .=  "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                $html .=  "       </select>";
                $html .=  "    </td>";
                $html .=  "  </tr>";

                $j++;
            }
            $i++;
        }
    }

    // Verluste Angreifer
    if (!empty($xml->resverluste->att)) {
        $html .=  "  <tr>";
        $html .=  "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;' >";
        $html .=  "       <b>Verluste der Angreifer</b>";
        $html .=  "     </td>";
        $html .=  "  </tr>";
        $html .=  "  <tr>";
        $html .=  "    <td class='center'>Ressource</td>";
        $html .=  "    <td class='center' colspan='3'>Anzahl</td>";
        $html .=  "    <td class='center bold'>F</td>";
        $html .=  "    <td class='center italic'>K</td>";
        $html .=  "    <td class='center underline'>U</td>";
        $html .=  "    <td class='center'>Farbe</td>";
        $html .=  "  </tr>";

        foreach ($xml->resverluste->att->resource as $loss_data) {
            $html .=  "  <tr>";
            $html .=  "     <td class='left'  >";
            $html .=  (string)$loss_data->name['value'];
            $html .=  "     </td>";
            $html .=  "     <td colspan='3' class='right'>";
            $html .=  number_format((float)$loss_data->anzahl['value'], 0, ",", ".");
            $html .=  "       <input type='hidden' name='attverlust" . (string)$loss_data->id['value'] . "' type='text' value='" . number_format((float)$loss_data->anzahl['value'], 0, ",", ".") . "'>";
            $html .=  "     </td>";
            $html .=  "    <td class='center' >";
            $html .=  "      <input type='checkbox' name='attverlustformf" . (string)$loss_data->id['value'] . "' value='f'>";
            $html .=  "    </td>";
            $html .=  "    <td class='center' >";
            $html .=  "      <input type='checkbox' name='attverlustformk" . (string)$loss_data->id['value'] . "' value='k'>";
            $html .=  "    </td>";
            $html .=  "    <td class='center' >";
            $html .=  "      <input type='checkbox' name='attverlustformu" . (string)$loss_data->id['value'] . "' value='u'>";
            $html .=  "    </td>";
            $html .=  "    <td class='center' >";
            $html .=  "      <select name='attverlustformc" . (string)$loss_data->id['value'] . "'>";
            $html .=  "         <option>keine</option>";
            $html .=  "         <option style='color:red' value='red'>rot</option>";
            $html .=  "         <option style='color:yellow' value='yellow'>gelb</option>";
            $html .=  "         <option style='color:pink' value='pink'>pink</option>";
            $html .=  "         <option style='color:green' value='green'>grün</option>";
            $html .=  "         <option style='color:orange' value='orange'>orange</option>";
            $html .=  "         <option style='color:purple' value='purple'>violett</option>";
            $html .=  "         <option style='color:blue' value='blue'>blau</option>";
            $html .=  "         <option style='color:beige' value='beige'>beige</option>";
            $html .=  "         <option style='color:brown' value='brown'>braun</option>";
            $html .=  "         <option style='color:teal' value='teal'>türkis</option>";
            $html .=  "         <option style='color:navy' value='navy'>dunkelblau</option>";
            $html .=  "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
            $html .=  "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
            $html .=  "       </select>";
            $html .=  "    </td>";
            $html .=  "  </tr>";
        }
    }

    // Verluste Deffer
    if (!empty($xml->resverluste->def)) {
        $html .=  "  <tr>";
        $html .=  "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF; font-weight:bold;' >";
        $html .=  "       Verluste der Verteidiger";
        $html .=  "     </td>";
        $html .=  "  </tr>";
        $html .=  "  <tr>";
        $html .=  "    <td class='center'>Ressource</td>";
        $html .=  "    <td class='center' colspan='3'>Anzahl</td>";
        $html .=  "    <td class='center bold'>F</td>";
        $html .=  "    <td class='center italic'>K</td>";
        $html .=  "    <td class='center underline'>U</td>";
        $html .=  "    <td class='center'>Farbe</td>";
        $html .=  "  </tr>";
        foreach ($xml->resverluste->def->resource as $loos_data) {
            $html .=  "  <tr>";
            $html .=  "     <td class='left' >";
            $html .=  (string)$loos_data->name['value'];
            $html .=  "     </td>";
            $html .=  "     <td colspan='3' class='right' >";
            $html .=  number_format((float)$loos_data->anzahl['value'], 0, ",", ".");
            $html .=  "       <input type='hidden' name='defverlust" . (string)$loos_data->id['value'] . "' type='text' value='" . number_format((float)$loos_data->anzahl['value'], 0, ",", ".") . "'>";
            $html .=  "     </td>";
            $html .=  "    <td class='center' >";
            $html .=  "      <input type='checkbox' name='defverlustformf" . (string)$loos_data->id['value'] . "' value='f'>";
            $html .=  "    </td>";
            $html .=  "    <td class='center' >";
            $html .=  "      <input type='checkbox' name='defverlustformk" . (string)$loos_data->id['value'] . "' value='k'>";
            $html .=  "    </td>";
            $html .=  "    <td class='center' >";
            $html .=  "      <input type='checkbox' name='defverlustformu" . (string)$loos_data->id['value'] . "' value='u'>";
            $html .=  "    </td>";
            $html .=  "    <td class='center' >";
            $html .=  "      <select name='defverlustformc" . (string)$loos_data->id['value'] . "'>";
            $html .=  "         <option>keine</option>";
            $html .=  "         <option style='color:red' value='red'>rot</option>";
            $html .=  "         <option style='color:yellow' value='yellow'>gelb</option>";
            $html .=  "         <option style='color:pink' value='pink'>pink</option>";
            $html .=  "         <option style='color:green' value='green'>grün</option>";
            $html .=  "         <option style='color:orange' value='orange'>orange</option>";
            $html .=  "         <option style='color:purple' value='purple'>violett</option>";
            $html .=  "         <option style='color:blue' value='blue'>blau</option>";
            $html .=  "         <option style='color:beige' value='beige'>beige</option>";
            $html .=  "         <option style='color:brown' value='brown'>braun</option>";
            $html .=  "         <option style='color:teal' value='teal'>türkis</option>";
            $html .=  "         <option style='color:navy' value='navy'>dunkelblau</option>";
            $html .=  "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
            $html .=  "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
            $html .=  "       </select>";
            $html .=  "    </td>";
            $html .=  "  </tr>";
        }
    }

    // Plünderungen
    if (!empty($xml->pluenderung->resource)) {

        $html .=  "  <tr>";
        $html .=  "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF; font-weight:bold;' >";
        $html .=  "       Es wurden folgende Ressourcen geplündert";
        $html .=  "     </td>";
        $html .=  "  </tr>";
        $html .=  "  <tr>";
        $html .=  "    <td class='center'>Ressource</td>";
        $html .=  "    <td class='center' colspan='3'>Anzahl</td>";
        $html .=  "    <td class='center bold'>F</td>";
        $html .=  "    <td class='center italic'>K</td>";
        $html .=  "    <td class='center underline'>U</td>";
        $html .=  "    <td class='center'>Farbe</td>";
        $html .=  "  </tr>";
        foreach ($xml->pluenderung->resource as $loos_data) {

            $html .=  "  <tr>";
            $html .=  "     <td class='left'>";
            $html .=  (string)$loos_data->name['value'];
            $html .=  "     </td>";
            $html .=  "     <td colspan='3' class='right'>";
            $html .=  number_format((float)$loos_data->anzahl['value'], 0, ",", ".");
            $html .=  "       <input type='hidden' name='weg" . (string)$loos_data->id['value'] . "' type='text' value='" . number_format((float)$loos_data->anzahl['value'], 0, ",", ".") . "'>";
            $html .=  "     </td>";
            $html .=  "    <td class='center'>";
            $html .=  "      <input type='checkbox' name='wegformf" . (string)$loos_data->id['value'] . "' value='f'>";
            $html .=  "    </td>";
            $html .=  "    <td class='center'>";
            $html .=  "      <input type='checkbox' name='wegformk" . (string)$loos_data->id['value'] . "' value='k'>";
            $html .=  "    </td>";
            $html .=  "    <td class='center'>";
            $html .=  "      <input type='checkbox' name='wegformu" . (string)$loos_data->id['value'] . "' value='u'>";
            $html .=  "    </td>";
            $html .=  "    <td class='center'>";
            $html .=  "      <select name='wegformc" . (string)$loos_data->id['value'] . "'>";
            $html .=  "         <option>keine</option>";
            $html .=  "         <option style='color:red' value='red'>rot</option>";
            $html .=  "         <option style='color:yellow' value='yellow'>gelb</option>";
            $html .=  "         <option style='color:pink' value='pink'>pink</option>";
            $html .=  "         <option style='color:green' value='green'>grün</option>";
            $html .=  "         <option style='color:orange' value='orange'>orange</option>";
            $html .=  "         <option style='color:purple' value='purple'>violett</option>";
            $html .=  "         <option style='color:blue' value='blue'>blau</option>";
            $html .=  "         <option style='color:beige' value='beige'>beige</option>";
            $html .=  "         <option style='color:brown' value='brown'>braun</option>";
            $html .=  "         <option style='color:teal' value='teal'>türkis</option>";
            $html .=  "         <option style='color:navy' value='navy'>dunkelblau</option>";
            $html .=  "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
            $html .=  "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
            $html .=  "       </select>";
            $html .=  "    </td>";
            $html .=  "  </tr>";
        }
    }

    // Bombing
    if (!empty($xml->bomben)) {
        if (!empty($xml->bomben->basis_zerstoert['value'])) { //Basisangriff

            $html .=  "  <tr>";
            $html .=  "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;'>";
            $html .=  "       Die Basis wurde von <b>" . (string)$xml->bomben->user->name['value'] . "</b>";
            $html .=  "       <input type='hidden' name='bomb1' type='text' value='" . urlencode("Der Planet wurde von [b]" . (string)$xml->bomben->user->name['value'] . "[/b] ") . "'>";
            $html .=  "       <input name='bomb2' type='text' value='bombadiert.' size='30' >";
            $html .=  "     </td>";
            $html .=  "  </tr>";

            if ($xml->bomben->basis_zerstoert['value'] == 1) {     //erfolgreich
                $html .=  "  <tr>";
                $html .=  "     <td class='left' colspan='8'>";
                $html .=  "      <input name='bomb3' type='text' value='".urlencode(getMessageBasebombSuccessful())."' size='150' >";
                $html .=  "     </td>";
                $html .=  "  </tr>";
            } else {                                               //gescheitert
                $html .=  "  <tr>";
                $html .=  "     <td class='left' colspan='8'>";
                $html .=  "      <input name='bomb3' type='text' value='".urlencode(getMessageBasebombUnsuccessful())."' size='150' >";
                $html .=  "     </td>";
                $html .=  "  </tr>";
            }

        } else {

            $html .=  "  <tr>";
            $html .=  "     <td class='left' colspan='8' style='color:#000000; background-color:#CAE1FF;'>";
            $html .=  "       Der Planet wurde von <b>" . (string)$xml->bomben->user->name['value'] . "</b>";
            $html .=  "       <input type='hidden' name='bomb1' type='text' value='" . urlencode("Der Planet wurde von [b]" . (string)$xml->bomben->user->name['value'] . "[/b] ") . "'>";
            $html .=  "       <input name='bomb2' type='text' value='bombadiert.' size='30' >";
            $html .=  "     </td>";
            $html .=  "  </tr>";

            if (isset($xml->bomben->bombentrefferchance['value'])) {
                $html .=  "  <tr>";
                $html .=  "     <td class='left' colspan='8'>";
                if ($xml->bomben->bombentrefferchance['value'] == 100) {
                    $html .=  "      <input name='bombtreff1' type='text' value='Es gab klare Sicht für die Bomberpiloten, die Trefferchance lag bei' size='80'>";
                } elseif ($xml->bomben->bombentrefferchance['value'] > 75) {
                    $html .=  "      <input name='bombtreff1' type='text' value='Ein paar kleine Pfefferminzwölkchen trübten den Himmel, die Trefferchance lag bei' size='80'>";
                } elseif ($xml->bomben->bombentrefferchance['value'] > 25) {
                    $html .=  "      <input name='bombtreff1' type='text' value='Pfefferminzwolken bedeckten den Himmel, die Trefferchance lag bei' size='80'>";
                } else {
                    $html .=  "      <input name='bombtreff1' type='text' value='Alles war mit Pfefferminzwolken vernebelt, die Trefferchance lag bei' size='80'>";
                }
                $html .=  " <b>" . $xml->bomben->bombentrefferchance['value'] . "%</b>";
                $html .=  "      <input type='hidden' name='bombtreff2' type='text' value='" . urlencode(" [b]" . $xml->bomben->bombentrefferchance['value'] . "%[/b]") . "'>";
                $html .=  "      <input name='bombtreff3' type='text' value='.' size='50'>";
                $html .=  "     </td>";
                $html .=  "  </tr>";
            }

            if (empty($xml->bomben->geb_zerstoert)) { //keine Gebäude
                $html .=  "  <tr>";
                $html .=  "     <td class='left' colspan='8'>";
                $html .=  "      <input name='bomb3' type='text' value='".urlencode('Es wurden keine Gebäude zerstört. Haha.')."' size='75'>";
                $html .=  "     </td>";
                $html .=  "  </tr>";
            } else { //mit Gebäuden
                $html .=  "  <tr>";
                $html .=  "    <td class='center'>Gebäude</td>";
                $html .=  "    <td class='center' colspan='3'>Anzahl</td>";
                $html .=  "    <td class='center bold'>F</td>";
                $html .=  "    <td class='center italic'>K</td>";
                $html .=  "    <td class='center underline'>U</td>";
                $html .=  "    <td class='center'>Farbe</td>";
                $html .=  "  </tr>";

                $i = 1;
                foreach ($xml->bomben->geb_zerstoert->geb as $loss_data) {
                    $html .=  "  <tr>";
                    $html .=  "     <td class='left' >";
                    $html .=  (string)$loss_data->name['value'];
                    $html .=  "       <input type='hidden' name='bombgeb" . $i . "' type='text' value='" . urlencode((string)$loss_data->name['value']) . "'>";
                    $html .=  "     </td>";
                    $html .=  "     <td colspan='3' class='right'>";
                    $html .=  number_format((float)$loss_data->anzahl['value'], 0, ',', '.');
                    $html .=  "       <input type='hidden' name='bombgebanz" . $i . "' type='text' value='" . number_format((float)$loss_data->anzahl['value'], 0, ",", ".") . "'>";
                    $html .=  "     </td>";
                    $html .=  "    <td class='center' >";
                    $html .=  "      <input type='checkbox' name='bombgebformf" . $i . "' value='f'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='center' >";
                    $html .=  "      <input type='checkbox' name='bombgebformk" . $i . "' value='k'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='center' >";
                    $html .=  "      <input type='checkbox' name='bombgebformu" . $i . "' value='u'>";
                    $html .=  "    </td>";
                    $html .=  "    <td class='center' >";
                    $html .=  "      <select name='bombgebformc" . $i . "'>";
                    $html .=  "         <option>keine</option>";
                    $html .=  "         <option style='color:red' value='red'>rot</option>";
                    $html .=  "         <option style='color:yellow' value='yellow'>gelb</option>";
                    $html .=  "         <option style='color:pink' value='pink'>pink</option>";
                    $html .=  "         <option style='color:green' value='green'>grün</option>";
                    $html .=  "         <option style='color:orange' value='orange'>orange</option>";
                    $html .=  "         <option style='color:purple' value='purple'>violett</option>";
                    $html .=  "         <option style='color:blue' value='blue'>blau</option>";
                    $html .=  "         <option style='color:beige' value='beige'>beige</option>";
                    $html .=  "         <option style='color:brown' value='brown'>braun</option>";
                    $html .=  "         <option style='color:teal' value='teal'>türkis</option>";
                    $html .=  "         <option style='color:navy' value='navy'>dunkelblau</option>";
                    $html .=  "         <option style='color:maroon' value='maroon'>dunkelrot</option>";
                    $html .=  "         <option style='color:limegreen' value='limegreen'>hellgrün</option>";
                    $html .=  "       </select>";
                    $html .=  "    </td>";
                    $html .=  "  </tr>";
                    $i++;
                }
            }
        }

        if (!empty($xml->bomben->bev_zerstoert['value'])) { // Bevölkerung
            $html .=  "  <tr>";
            $html .=  "     <td class='left' colspan='8'>";
            $html .=  "      <input name='bombbev1' type='text' value='' size='80'>";
            $html .=  " <b>" . number_format((float)$xml->bomben->bev_zerstoert['value'], 0, ",", ".") . "</b>";
            $html .=  "      <input type='hidden' name='bombbev2' type='text' value='" . urlencode(" [b]" . number_format((float)$xml->bomben->bev_zerstoert['value'], 0, ",", ".") . "[/b] ") . "'>";
            $html .=  "      <input name='bombbev3' type='text' value='" . getMessageBevbomb() . "' size='50'>";
            $html .=  "     </td>";
            $html .=  "  </tr>";
        }
    }

    $KBLink = str_replace("&typ=xml", "", $KBLink);

    $html .=  "  <tr>";
    $html .=  "     <td class='center' colspan='8'>";
    $html .=  "       <input type='hidden' name='KBLink' type='text' value='" . urlencode($KBLink) . "'>";
    $html .=  "       <input type='submit' value='BB-Code generieren' class='btn'>";
    $html .=  "     </td>";
    $html .=  "  </tr>";

    $html .=  "</table>";
    $html .=  "</form>";

    return $html;

}

function generateBBcode() {

    if (empty($_POST['optionLink'])) {       //KB-Link in Quote-Tags nicht aktiv
        $outBB = "[quote][table]";
    } else {
        $outBB = "[quote=" . urldecode(getVar('KBLink')) . "][table]";
    }
    $outBB .= "[tr][td colspan=4]" . urldecode(getVar('mainline')) . "[/td][/tr]"; //Kampf auf dem ...
    $outBB .= "[tr][td colspan=4]" . urldecode(getVar('dateline')) . "[/td][/tr]"; //Die Schlacht endete mit ...

    // Angreifer
    $outBB .= "[tr][td colspan=4][hr][/td][/tr]"; //horizontale Linie
    $outBB .= "[tr][td][u]Angreifer[/u][/td][td][right]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[u]Anzahl[/u][/right][/td][td][right]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[u]Zerstört[/u][/right][/td][td][right]&nbsp;&nbsp;[u]Überlebende[/u][/right][/td][/tr]";

    $i     = 1;
    while (!empty($_POST["atter" . $i])) {
        $outBB .= "[tr][td colspan=4]" . urldecode(getVar("atter" . $i)) . "[/td][/tr]"; //Angreifende Flotte ...
        $j     = 1;
        while (!empty($_POST["atterschiffname" . $i . "_" . $j])) {
            if (getVar("atterschiffstart" . $i . "_" . $j) !== "") {
                $outBB .= "[tr][td]" . makeFormatedString(urldecode(getVar("atterschiffname" . $i . "_" . $j)), getVar("Attformf" . $i . "_" . $j), getVar("Attformk" . $i . "_" . $j), getVar("Attformu" . $i . "_" . $j), getVar("Attformc" . $i . "_" . $j)) . "[/td]";
                $outBB .= "[td][right]" . makeFormatedString(getVar("atterschiffstart" . $i . "_" . $j), getVar("Attformf" . $i . "_" . $j), getVar("Attformk" . $i . "_" . $j), getVar("Attformu" . $i . "_" . $j), getVar("Attformc" . $i . "_" . $j)) . "[/right][/td]";
                $outBB .= "[td][right]" . makeFormatedString(getVar("atterschiffweg" . $i . "_" . $j), getVar("Attformf" . $i . "_" . $j), getVar("Attformk" . $i . "_" . $j), getVar("Attformu" . $i . "_" . $j), getVar("Attformc" . $i . "_" . $j)) . "[/right][/td]";
                $outBB .= "[td][right]" . makeFormatedString(getVar("atterschiffende" . $i . "_" . $j), getVar("Attformf" . $i . "_" . $j), getVar("Attformk" . $i . "_" . $j), getVar("Attformu" . $i . "_" . $j), getVar("Attformc" . $i . "_" . $j)) . "[/right][/td][/tr]";
            }
            $j++;
        }
        if (!empty($_POST["atterkuchen" . $i])) {
            $outBB .= "[tr][td colspan=4]Die Flotte brachte Kaffee und Kuchen mit.[/td][/tr]";
        }
        if (!empty($_POST["atterlollies" . $i])) {
            $outBB .= "[tr][td colspan=4]Diese wilden Barbarben haben unseren kleinen Kindern die Lollis geklaut!! Das schreit gradezu nach Rache.[/td][/tr]";
        }
        if (!empty($_POST["attermsg" . $i])) {
            $outBB .= "[tr][td colspan=4]" . urldecode(getVar("attermsg" . $i)) . "[/td][/tr]";
        }
        $i++;
    }

    // Pladeff
    $outBB .= "[tr][td colspan=4][hr][/td][/tr]"; //horizontale Linie
    $outBB .= "[tr][td][u]Verteidiger[/u][/td][td][right][u]Anzahl[/u][/right][/td][td][right][u]Zerstört[/u][/right][/td][td][right][u]Überlebende[/u][/right][/td][/tr]";

    if (!empty($_POST["pladeffer"])) {
        $outBB .= "[tr][td colspan=4]" . urldecode(getVar("pladeffer")) . "[/td][/tr]"; // Verteidiger war ...

        $j     = 1;
        while (!empty($_POST["pladefturm_" . $j])) {
            $outBB .= "[tr][td]" . makeFormatedString(urldecode(getVar("pladefturm_" . $j)), getVar("Pladefturm_form_f_" . $j), getVar("Pladefturm_form_k_" . $j), getVar("Pladefturm_form_u_" . $j), getVar("Pladefturm_form_c_" . $j)) . "[/td]";
            $outBB .= "[td][right]" . makeFormatedString(getVar("pladefturm_start_" . $j), getVar("Pladefturm_form_f_" . $j), getVar("Pladefturm_form_k_" . $j), getVar("Pladefturm_form_u_" . $j), getVar("Pladefturm_form_c_" . $j)) . "[/right][/td]";
            $outBB .= "[td][right]" . makeFormatedString(getVar("pladefturm_varlust_" . $j), getVar("Pladefturm_form_f_" . $j), getVar("Pladefturm_form_k_" . $j), getVar("Pladefturm_form_u_" . $j), getVar("Pladefturm_form_c_" . $j)) . "[/right][/td]";
            $outBB .= "[td][right]" . makeFormatedString(getVar("pladefturm_ende_" . $j), getVar("Pladefturm_form_f_" . $j), getVar("Pladefturm_form_k_" . $j), getVar("Pladefturm_form_u_" . $j), getVar("Pladefturm_form_c_" . $j)) . "[/right][/td][/tr]";
            $j++;
        }

        $j = 1;
        while (!empty($_POST["pladefschiff_" . $j])) {
            $outBB .= "[tr][td]" . makeFormatedString(urldecode(getVar("pladefschiff_" . $j)), getVar("Pladefschiff_form_f_" . $j), getVar("Pladefschiff_form_k_" . $j), getVar("Pladefschiff_form_u_" . $j), getVar("Pladefschiff_form_c_" . $j)) . "[/td]";
            $outBB .= "[td][right]" . makeFormatedString(getVar("pladefschiff_start_" . $j), getVar("Pladefschiff_form_f_" . $j), getVar("Pladefschiff_form_k_" . $j), getVar("Pladefschiff_form_u_" . $j), getVar("Pladefschiff_form_c_" . $j)) . "[/right][/td]";
            $outBB .= "[td][right]" . makeFormatedString(getVar("pladefschiff_verlust_" . $j), getVar("Pladefschiff_form_f_" . $j), getVar("Pladefschiff_form_k_" . $j), getVar("Pladefschiff_form_u_" . $j), getVar("Pladefschiff_form_c_" . $j)) . "[/right][/td]";
            $outBB .= "[td][right]" . makeFormatedString(getVar("pladefschiff_ende_" . $j), getVar("Pladefschiff_form_f_" . $j), getVar("Pladefschiff_form_k_" . $j), getVar("Pladefschiff_form_u_" . $j), getVar("Pladefschiff_form_c_" . $j)) . "[/right][/td][/tr]";
            $j++;
        }
    }

    // Deffer
    $i = 1;
    while (!empty($_POST["deffer" . $i])) {
        $outBB .= "[tr][td colspan=4]" . urldecode(getVar("deffer" . $i)) . "[/td][/tr]"; //Verteidigende Flotte ...
        $j     = 1;
        while (!empty($_POST["defferschiffname" . $i . "_" . $j])) {
            if (!empty($_POST["defferschiffstart" . $i . "_" . $j])) {
                $outBB .= "[tr][td]" . makeFormatedString(urldecode(getVar("defferschiffname" . $i . "_" . $j)), getVar("Deffformf" . $i . "_" . $j), getVar("Deffformk" . $i . "_" . $j), getVar("Deffformu" . $i . "_" . $j), getVar("Deffformc" . $i . "_" . $j)) . "[/td]";
                $outBB .= "[td][right]" . makeFormatedString(getVar("defferschiffstart" . $i . "_" . $j), getVar("Deffformf" . $i . "_" . $j), getVar("Deffformk" . $i . "_" . $j), getVar("Deffformu" . $i . "_" . $j), getVar("Deffformc" . $i . "_" . $j)) . "[/right][/td]";
                $outBB .= "[td][right]" . makeFormatedString(getVar("defferschiffweg" . $i . "_" . $j), getVar("Deffformf" . $i . "_" . $j), getVar("Deffformk" . $i . "_" . $j), getVar("Deffformu" . $i . "_" . $j), getVar("Deffformc" . $i . "_" . $j)) . "[/right][/td]";
                $outBB .= "[td][right]" . makeFormatedString(getVar("defferschiffende" . $i . "_" . $j), getVar("Deffformf" . $i . "_" . $j), getVar("Deffformk" . $i . "_" . $j), getVar("Deffformu" . $i . "_" . $j), getVar("Deffformc" . $i . "_" . $j)) . "[/right][/td][/tr]";
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
        if (!empty($_POST["attverlust" . $i])) {
            $do = true;
        }
        if (!empty($_POST["defverlust" . $i])) {
            $do = true;
        }
        if (!empty($_POST["weg" . $i])) {
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

        $outBB .= "[tr][td colspan=4][hr][/td][/tr]"; //horizontale Linie
        $outBB .= "[tr][td][u]Zerstörte und geplünderte Ressourcen[/u][/td][td][right][u]Angreifer[/u][/right][/td][td][right][u]Verteidiger[/u][/right][/td][td][right][u]Plünderung[/u][/right][/td][/tr]";
        while ($i <= 11) {
            if (!empty($_POST["attverlust" . $i]) OR !empty($_POST["defverlust" . $i]) OR !empty($_POST["weg" . $i])) {
                $outBB .= "[tr][td]" . $ressname[$i] . "[/td]";
                if (!empty($_POST["attverlust" . $i])) {
                    $outBB .= "[td][right]" . makeFormatedString($_POST["attverlust" . $i], isset($_POST["attverlustformf" . $i]), isset($_POST["attverlustformk" . $i]), isset($_POST["attverlustformu" . $i]), $_POST["attverlustformc" . $i]) . "[/right][/td]";
                } else {
                    $outBB .= "[td][right]-[/right][/td]";
                }
                if (!empty($_POST["defverlust" . $i])) {
                    $outBB .= "[td][right]" . makeFormatedString($_POST["defverlust" . $i], isset($_POST["defverlustformf" . $i]), isset($_POST["defverlustformk" . $i]), isset($_POST["defverlustformu" . $i]), $_POST["defverlustformc" . $i]) . "[/right][/td]";
                } else {
                    $outBB .= "[td][right]-[/right][/td]";
                }
                if (!empty($_POST["weg" . $i])) {
                    $outBB .= "[td][right][color=green]" . makeFormatedString($_POST["weg" . $i], isset($_POST["wegformf" . $i]), isset($_POST["wegformk" . $i]), isset($_POST["wegformu" . $i]), $_POST["wegformc" . $i]) . "[/color][/right][/td][/tr]";
                } else {
                    $outBB .= "[td][right]-[/right][/td][/tr]";
                }
            }
            $i++;
        }
    }

    //Bomben
    if (!empty($_POST['bomb1'])) {
        $outBB .= "[tr][td colspan=4][hr][/td][/tr]"; //horizontale Linie
        $outBB .= "[tr][td colspan=4]" . urldecode($_POST['bomb1']) . urldecode($_POST['bomb2']) . "[/td][/tr]"; // Der Planet wurde ...
        if (!empty($_POST['bombtreff1'])) {  // Bombendtrefferchance
            $outBB .= "[tr][td colspan=4]" . $_POST['bombtreff1'] . urldecode($_POST['bombtreff2']) . $_POST['bombtreff3'] . "[/td][/tr]";
        }
        if (!empty($_POST['bomb3'])) { // KB oder keine Gebs
            $outBB .= "[tr][td colspan=4]" . urldecode($_POST['bomb3']) . "[/td][/tr]";
        } else {
            $i     = 1;
            $outBB .= "[tr][td colspan=4]Folgende Gebäude wurden zerstört:[/td][/tr]";
            while (!empty($_POST["bombgeb" . $i])) {
                $outBB .= "[tr][td]" . makeFormatedString(urldecode($_POST["bombgeb" . $i]), isset($_POST["bombgebformf" . $i]), isset($_POST["bombgebformk" . $i]), isset($_POST["bombgebformu" . $i]), $_POST["bombgebformc" . $i]) . "[/td][td][right]" . makeFormatedString($_POST["bombgebanz" . $i], isset($_POST["bombgebformf" . $i]), isset($_POST["bombgebformk" . $i]), isset($_POST["bombgebformu" . $i]), $_POST["bombgebformc" . $i]) . "[/right][/td][/tr]";
                $i++;
            }
        }
        if (!empty($_POST['bombbev2'])) {
            $outBB .= "[tr][td colspan=4]" . $_POST['bombbev1'] . urldecode($_POST['bombbev2']) . $_POST['bombbev3'] . "[/td][/tr]";
        }
    }

    // KBLink am Ende
    $outBB .= "[tr][td colspan=4][hr][/td][/tr]"; //horizontale Linie
    $outBB .= "[tr][td colspan=4][url=" . urldecode(getVar("KBLink")) . "]Link zum externen Kampfbericht[/url][/td][/tr]";
    $outBB .= "[/table][/quote]";

    // Lange Schiffnamen killen
    if (!empty($_POST['optionKuerzen'])) {
        $outBB = str_replace(" (Systemtransporter Klasse 1)", "", $outBB); // Systrans
        $outBB = str_replace(" (Hyperraumtransporter Klasse 1)", "", $outBB); // Gorgol, Kamel, Flughund
        $outBB = str_replace(" (Systemtransporter Klasse 2)", "", $outBB); // Lurch
        $outBB = str_replace(" (Hyperraumtransporter Klasse 2)", "", $outBB); // Eisbär, Waschbär, Seepferd
        $outBB = str_replace(" (Systemtransporter Kolonisten)", "", $outBB); // Crux
        $outBB = str_replace(" (Hyperraumtransporter Kolonisten)", "", $outBB); // Kolpor
        $outBB = str_replace(" (Transporter)", "", $outBB); // andere Transporter
        $outBB = str_replace(" (Systemkolonieschiff)", "", $outBB); // KISS
        $outBB = str_replace(" (interstellares Kolonieschiff)", "", $outBB); // INS
    }

    //BBCode beschneiden
    if (empty($_POST['optionQuote'])) { //checkbox quoten nicht aktiv
        $outBB = str_replace("[quote]", "", $outBB);
        $outBB = str_replace("[/quote]", "", $outBB);
    }

    if (empty($_POST['optionHr'])) {        //horizonale Linien nicht aktiv
        $outBB = str_replace("[hr]", "---", $outBB);
    }

    if (!empty($_POST['optionColspan'])) {        //verkürzte Colspans aktiv
        $outBB = str_replace(" colspan", "", $outBB);
    }

    if (empty($_POST['optionAlign'])) { //Ausrichtung nicht aktiv
        $outBB = str_replace("[center]", "", $outBB);
        $outBB = str_replace("[/center]", "", $outBB);
        $outBB = str_replace("[left]", "", $outBB);
        $outBB = str_replace("[/left]", "", $outBB);
        $outBB = str_replace("[right]", "", $outBB);
        $outBB = str_replace("[/right]", "", $outBB);
    }

    if (empty($_POST['optionColor'])) { //Farbe nicht aktiv
        $outBB = preg_replace ( '/\[color=.+?]/', '', $outBB);
        $outBB = str_replace("[/color]", "", $outBB);
    }

    if (empty($_POST['optionForm'])) { //Format (fett, kursiv, unterstrichen) nicht aktiv
        $outBB = str_replace("[b]", "", $outBB);
        $outBB = str_replace("[/b]", "", $outBB);
        $outBB = str_replace("[i]", "", $outBB);
        $outBB = str_replace("[/i]", "", $outBB);
        $outBB = str_replace("[u]", "", $outBB);
        $outBB = str_replace("[/u]", "", $outBB);
    }

    return $outBB;

}

//Hier fängts wirklich an.

doc_title("KB Parser für BB-Code");

$parsstatus = getVar('parsstatus');
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

} elseif ($parsstatus === "read") {   // KB einlesen und für die Formatierung ausgeben

    $KBLink = trim(getVar('KBLink'));
    if (preg_match('#^https?://www.?\.icewars\.de/portal/kb/de/kb\.php\?id=[\d]+.{1,5}md_hash=[\w]{32}$#', $KBLink) !== 1) {
        echo "<div class='system_error'>Keinen KB-Link eingetragen!</div>";
        $parsstatus = "";
    } else {
        $KBLink = $KBLink . "&typ=xml";
        $KBLink = str_replace("&amp;", "&", $KBLink);
        $xml = simplexml_load_file_ex($KBLink);
        if (!empty($xml)) {
            echo generateKBparserform($xml, $modulname, $KBLink);
        } else {
            echo "<div class='system_error'>XML-Fehler: {$KBLink} konnte nicht geladen werden.</div>";
        }

    }

} elseif ($parsstatus === "write") {  // BB-Code ausgeben

    echo "<textarea name='bbcode' rows='10' style='width: 95%;' onclick='this.select()' readonly>" . generateBBcode() . "</textarea>";

}

