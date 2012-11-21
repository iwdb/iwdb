<?php
/*****************************************************************************/
/* m_incomings.php                                                           */
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
/* Anzeige der Incomings auf die eigene Allianz                              */
/* für die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*         https://handels-gilde.org/?www/forum/index.php;board=1099.0       */
/*                                                                           */
/*****************************************************************************/

//****************************************************************************
//
// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
    echo "Hacking attempt...!!";
    exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname = "m_incomings";

//****************************************************************************
//
// -> Titel des Moduls
//
$modultitle = "Incomings";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation 
//    ausführen darf. Mögliche Werte:
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "admin";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc = "Anzeige der Incomings (Sondierung/Angriff) auf die eigene Allianz";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    /*
    global $db, $db_prefix, $db_tb_iwdbtabellen;

    $sqlscript = array(
        "CREATE TABLE " . $db_prefix . "neuername
        (
        );",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('neuername')"
    );
    foreach($sqlscript as $sql) {
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR,
            'Could not query config information.', '',
            __FILE__, __LINE__, $sql);
    }
    */
    echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
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

    $menu = getVar('menu');
    $submenu = getVar('submenu');
    $menuetitel = "Incomings #incomings";    // -> Menütitel in der Navigation, #incomings wird gegen die Anzahl ersetzt
    $actionparamters = "";

    insertMenuItem($menu, $submenu, $menuetitel, $modulstatus, $actionparamters);
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
    /*
    global $db, $db_tb_iwdbtabellen, $db_tb_neuername;

    $sqlscript = array(
        "DROP TABLE " . $db_tb_neuername . ";",
        "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='neuername';"
    );

    foreach($sqlscript as $sql) {
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR,
            'Could not query config information.', '',
            __FILE__, __LINE__, $sql);
    }
    */
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
if (!empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
    if ($user_status != "admin")
        die('Hacking attempt...');

    echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")</div>\n";

    if (!@include("./includes/menu_fn.php"))
        die("Cannot load menu functions");

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

global $db, $db_prefix, $db_tb_incomings, $db_tb_user;

// Titelzeile
echo "<div class='doc_title'>$modultitle</div>\n";
echo " 	 <br />\n";
echo "Anzeige der laufenden Sondierungen und Angriffe auf uns";
echo " 	 <br />\n";
echo " 	 <br />\n";

//Löschen der Einträge in der Tabelle incomings, es sollen nur aktuelle Sondierungen und Angriffe eingetragen sein
//ToDo : evtl Trennung Sondierung und Angriffe, damit die Sondierungen früher entfernt sind
$sql = "DELETE FROM " . $db_tb_incomings . " WHERE timestamp<" . (CURRENT_UNIX_TIME - 20 * 60);
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not delete incomings information.', '', __FILE__, __LINE__, $sql);

$sql = "SELECT * FROM " . $db_tb_incomings . " WHERE art = 'Sondierung (Schiffe/Def/Ress)' OR art = 'Sondierung (Gebäude/Ress)' ORDER BY timestamp ASC";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);

$data = array();

//Tabelle für die Sondierungen	
start_table();
start_row("titlebg", "align='center' colspan='6'");
echo "<b>Sondierungen</b>";
next_row("titlebg", "nowrap style='width:0%' align='center' ");
echo "<b>Wer wird sondiert?</b>";
next_cell("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Zielplanet</b>";
next_cell("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Wer sondiert?</b>";
next_cell("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Von wo wird sondiert?</b>";
next_cell("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Zeitpunkt</b>";
next_cell("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Art der Sondierung</b>";

while ($row = $db->db_fetch_array($result)) {

    next_row("windowbg1", "nowrap style='width:0%' align='left'");
    echo $row['name_to'];

    next_cell("windowbg1", "nowrap style='width:0%' align='left'");
    $objekt = GetObjectByCoords($row['koords_to']);
    if ($objekt == 'Kolonie') {
        echo "<img src='bilder/kolo.png'>";
    } else if ($objekt == 'Sammelbasis') {
        echo "<img src='bilder/ress_basis.png'>";
    } else if ($objekt == 'Artefaktbasis') {
        echo "<img src='bilder/artefakt_basis.png'>";
    } else if ($objekt == 'Kampfbasis') {
        echo "<img src='bilder/kampf_basis.png'>";
    }
    echo $row['koords_to'];

    next_cell("windowbg1", "nowrap style='width:0%' align='center'");
    if (!empty($row['allianz_from'])) {
        echo ($row['name_from'] . " [" . $row['allianz_from'] . "]");
    } else {
        echo $row['name_from'];
    }

    next_cell("windowbg1", "nowrap style='width:0%' align='center'");
    $objekt = GetObjectByCoords($row['koords_from']);
    if ($objekt == 'Kolonie') {
        echo "<img src='bilder/kolo.png'>";
    } else if ($objekt == 'Sammelbasis') {
        echo "<img src='bilder/ress_basis.png'>";
    } else if ($objekt == 'Artefaktbasis') {
        echo "<img src='bilder/artefakt_basis.png'>";
    } else if ($objekt == 'Kampfbasis') {
        echo "<img src='bilder/kampf_basis.png'>";
    }
    echo $row['koords_from'];

    next_cell("windowbg1", "nowrap style='width:0%' align='center'");
    echo strftime("%d.%m.%y %H:%M:%S", $row['timestamp']);

    next_cell("windowbg1", "nowrap style='width:0%' align='center'");
    echo $row['art'];

}
end_row();
end_table();

echo " 	 <br />\n";
echo " 	 <br />\n";

$sql = "SELECT * FROM " . $db_tb_incomings . " WHERE art = 'Angriff' ORDER BY timestamp DESC";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);

$data = array();

//Tabelle für die Angriffe	
start_table();
start_row("titlebg", "align='center' colspan='5'");
echo "<b>Angriffe</b>";
next_row("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Wer wird angegriffen?</b>";
next_cell("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Zielplanet</b>";
next_cell("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Wer greift an?</b>";
next_cell("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Von wo wird angegriffen?</b>";
next_cell("titlebg", "nowrap style='width:0%' align='center'");
echo "<b>Zeitpunkt</b>";

while ($row = $db->db_fetch_array($result)) {

    next_row("windowbg1", "nowrap style='width:0%' align='center'");
    echo $row['name_to'];

    next_cell("windowbg1", "nowrap style='width:0%' align='center'");
    $objekt = GetObjectByCoords($row['koords_to']);
    if ($objekt == 'Kolonie') {
        echo "<img src='bilder/kolo.png'>";
    } else if ($objekt == 'Sammelbasis') {
        echo "<img src='bilder/ress_basis.png'>";
    } else if ($objekt == 'Artefaktbasis') {
        echo "<img src='bilder/artefakt_basis.png'>";
    } else if ($objekt == 'Kampfbasis') {
        echo "<img src='bilder/kampf_basis.png'>";
    }
    echo $row['koords_to'];

    next_cell("windowbg1", "nowrap style='width:0%' align='center'");
    if (!empty($row['allianz_from'])) {
        echo ($row['name_from'] . " [" . $row['allianz_from'] . "]");
    } else {
        echo $row['name_from'];
    }

    next_cell("windowbg1", "nowrap style='width:0%' align='center'");
    $objekt = GetObjectByCoords($row['koords_from']);
    if ($objekt == 'Kolonie') {
        echo "<img src='bilder/kolo.png'>";
    } else if ($objekt == 'Sammelbasis') {
        echo "<img src='bilder/ress_basis.png'>";
    } else if ($objekt == 'Artefaktbasis') {
        echo "<img src='bilder/artefakt_basis.png'>";
    } else if ($objekt == 'Kampfbasis') {
        echo "<img src='bilder/kampf_basis.png'>";
    }
    echo $row['koords_from'];

    next_cell("windowbg1", "nowrap style='width:0%' align='center'");
    echo strftime("%d.%m.%y %H:%M:%S", $row['timestamp']);
}
end_row();
end_table();

echo " 	 <br />\n";
echo " 	 <br />\n";

//Legende, weil es immer noch IW-Spieler gibt, die nichts mit den Symbolen anfangen können 
start_table();
start_row("windowbg1", "nowrap style='width:0%' align='center'");
echo "<img src='bilder/kolo.png'> = Kolonie";
next_cell("windowbg1", "nowrap style='width:0%' align='center'");
echo "<img src='bilder/ress_basis.png'> = Ressourcensammelbasis";
next_cell("windowbg1", "nowrap style='width:0%' align='center'");
echo "<img src='bilder/artefakt_basis.png'> = Artefaktsammelbasis";
next_cell("windowbg1", "nowrap style='width:0%' align='center'");
echo "<img src='bilder/kampf_basis.png'> = Kampfbasis";
end_row();
end_table();