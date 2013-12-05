<?php
/*****************************************************************************
 * menu_fn.php                                                               *
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
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum/Repo wenden:                                              *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

// Function createConfig creates a new configuration file for the module
// being installed.
//
function createConfig($configtext)
{
    global $modulname;
    global $moduldesc;

    if (empty($modulname)) {
        die("Der übergebene Modulname ist leer. Prüfe bitte den Quelltext des Moduls.");
    }

    // Check existance of the config directory and copy all the needed files
    if (!is_dir("config")) {
        mkdir("config", 0777);

        copy("./modules/.htaccess", "./config/.htaccess");
        copy("./modules/LICENSE", "./config/LICENSE");

        echo "<div class='system_notification'>Installation: Konfigurationsverzeichnis angelegt = <b>OK</b></div>";
    }

    // Check existance of the configuration file
    if (file_exists("./config/" . $modulname . ".cfg.php")) {
        die("Die Datei config/" . $modulname . ".cfg.php existiert bereits, " .
            "bitte lösche diese vor einer Neuinstallation.");
    }

    // Open new configuration file for writing.
    $fd = fopen("./config/" . $modulname . ".cfg.php", "w");

    // Write file header
    fwrite($fd, "<?PHP\n"); // Auch Configdateien sind ausführbare PHP-Scripts
    fwrite($fd, "/*****************************************************************************/\n");
    fwrite($fd, "/* Iw DB: Icewars geoscan and sitter database                                */\n");
    fwrite($fd, "/* Open-Source Project started by Robert Riess (robert@riess.net)            */\n");
    fwrite($fd, "/* Software Version: Iw DB 1.00                                              */\n");
    fwrite($fd, "/* ========================================================================= */\n");
    fwrite($fd, "/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */\n");
    fwrite($fd, "/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */\n");
    fwrite($fd, "/* ========================================================================= */\n");
    fwrite($fd, "/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */\n");
    fwrite($fd, "/*****************************************************************************/\n");
    fwrite($fd, "/* This program is free software; you can redistribute it and/or modify it   */\n");
    fwrite($fd, "/* under the terms of the GNU General Public License as published by the     */\n");
    fwrite($fd, "/* Free Software Foundation; either version 2 of the License, or (at your    */\n");
    fwrite($fd, "/* option) any later version.                                                */\n");
    fwrite($fd, "/*                                                                           */\n");
    fwrite($fd, "/* This program is distributed in the hope that it will be useful, but       */\n");
    fwrite($fd, "/* WITHOUT ANY WARRANTY; without even the implied warranty of                */\n");
    fwrite($fd, "/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General */\n");
    fwrite($fd, "/* Public License for more details.                                          */\n");
    fwrite($fd, "/*                                                                           */\n");
    fwrite($fd, "/* The GNU GPL can be found in LICENSE in this directory                     */\n");
    fwrite($fd, "/*****************************************************************************/\n");
    fwrite($fd, "/* This file was automatically generated by an installation routine.         */\n");
    fwrite($fd, "/*****************************************************************************/\n");
    fwrite($fd, "\n");
    fwrite($fd, "\$desc_" . $modulname . "='" . $moduldesc . "';\n"); // Definition einer Konfigurationsvariablen

    // write the other contents to the file.
    fwrite($fd, $configtext . "\n");

    // write closing comments.
    fwrite($fd, "?>\n");

    // Close the file again.
    fclose($fd);

    // Write out success message
    echo "<div class='system_notification'>Installation: Konfigurationsdatei " . $modulname . ".cfg.php = <b>OK</b></div>";
}

//****************************************************************************
//
// Function removeConfig removes the configuation file. 
//
//
function removeConfig()
{
    global $modulname;

    if (empty($modulname)) {
        die("Der übergebene Modulname ist leer. Prüfe bitte den Quelltext des Moduls.");
    }

    // Delete the configuration file ...
    unlink("./config/" . $modulname . ".cfg.php");

    // Write out success message
    echo "<div class='system_notification'>Deinstallation: Konfigurationsdatei " . $modulname . ".cfg.php = <b>OK</b></div>";
}

//****************************************************************************
//
// Function createMenu creates a menu tree with several selection points,
// where the new menu item can be placed. 
//
//
function createMenu()
{
    global $db, $db_tb_menu, $modulname;

    if (empty($modulname)) {
        die("Der übergebene Modulname ist leer. Prüfe bitte den Quelltext des Moduls.");
    }

    // Auslesen der vorhanden Menütabelle
    $sql = "SELECT menu, submenu, title, status, action, extlink, sittertyp FROM " .
        $db_tb_menu . " ORDER BY menu ASC, submenu ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    // -> Nun ein Formular das das vorhandene Menü anzeigt und zusätzlich freie Slots bietet
    // -> wo man wählen kann wo dieses Modul seinen Platz im Menü bekommen soll.
    echo "<div class='normal'>Wähle nun in welchen Abschnitt des Menüs du dieses Modul anwählen willst!</div>";

    $lastmenu    = "";
    $tableopen   = 0;
    $insidetable = 0;
    $lastsubmenu = 0;

    // Alle Menu-Einträge durchgehen
    while ($row = $db->db_fetch_array($result)) {
        // Neues Hauptmenu?
        if ($lastmenu != $row['menu']) {
            // Bin ich noch in der vorhergehenden Tabelle? Dann entsprechend schliessen.
            if ($tableopen != 0) {
                if ($insidetable == 0) {
                    echo "  <td class='menu'>";
                }
                echo "<form name='form' method='POST'>";
                echo " <input type='hidden' name='action' value='" . $modulname . "'>";
                echo " <input type='hidden' name='was' value='install2'>";
                echo " <input type='hidden' name='menu' value='" . $lastmenu . "'>";
                $submenu = $lastsubmenu + 1;
                echo " <input type='hidden' name='submenu' value=" . $submenu . ">";
                echo " <input type='submit' value='Bitte Hier' name='install' class='submit'>";
                echo "</form>";
                echo "</td>";
                echo "</tr></table><br>";
            }

            // Neue Tabelle aufmachen.
            echo "<table width='100%' border='0' cellpadding='0' cellspacing='1' class='bordercolor'><tr>";
            $tableopen   = 1;
            $insidetable = 0;
            $lastmenu    = $row['menu'];
        }
        $title = $row['title'];

        // Habe ich hier den neuen Hauptmenu-Titel?
        if ($row['submenu'] == 0) {
            // Ja, dann in entsprechender Formatierung ausgeben.
            echo "  <td class='titlebg' style='padding: 3px;'><b>" . $title . "</b></td>";
            echo "</tr>";
            echo "<tr>";
        } else {
            // Kein Hauptmenu. Eintraege in einzelne Tabellenzelle zusammenfassen.
            if ($insidetable == 0) {
                echo "  <td class='menu'>";
                $insidetable = 1;
            }
            // Titel ausgeben
            echo $title . "<br>";
            $lastsubmenu = $row['submenu'];
        }
    }

    // Restliche Tabelle wieder schliessen.
    if ($tableopen != 0) {
        if ($insidetable == 0) {
            echo "<td class='menu'>";
        }
        // Für das letzte Menü benötigen wir auch noch einen Button.
        echo "<form name='form' method='POST'>";
        echo " <input type='hidden' name='action' value='" . $modulname . "'>";
        echo " <input type='hidden' name='was' value='install2'>";
        echo " <input type='hidden' name='menu' value='" . $lastmenu . "'>";
        $submenu = $lastsubmenu + 1;
        echo " <input type='hidden' name='submenu' value=" . $submenu . ">";
        echo " <input type='submit' value='Bitte Hier' name='install' class='submit'>";
        echo "</form>";
        echo "</td>";
        echo " </tr></table><br>";
    }
}

//****************************************************************************
//
// Function insertMenuItem inserts a new menu item into the database.
//
//
function insertMenuItem($m_menu, $m_submenu, $modultitle, $modulstatus, $actionparameters = '')
{
    global $modulname;
    global $db, $db_tb_menu;

    if (empty($m_menu) OR empty($m_submenu)) {
        die("Menu-Item oder Submenu-Item nicht gültig (sollte nicht so sein).");
    }

    $SQLdata = array(
        'menu'      => $m_menu,
        'submenu'   => $m_submenu,
        'title'     => $modultitle,
        'status'    => $modulstatus,
        'action'    => $modulname . (empty($actionparameters) ? "" : "&" . $actionparameters),
        'extlink'   => 'n',
        'sittertyp' => '0'
    );
    $db->db_insert($db_tb_menu, $SQLdata)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__);

    echo "<div class='system_notification'>Menü-Eintrag " . $modultitle . " in die Datenbank eingefügt</div>";
}


//****************************************************************************
//
// Function removeMenuItems removes all menu entries where the action starts
// with the given modulname.
//
function removeMenuItems()
{
    global $modulname;
    global $db, $db_tb_menu;

    $sql = "DELETE FROM " . $db_tb_menu . " WHERE action LIKE '" . $modulname . "%'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    echo "<div class='system_notification'>Menü-Einträge entfernt</div>";
}


//****************************************************************************
//
// Installation method. Will need the module defined functions
//
//   workInstallConfigString  
//   workInstallDatabase  
//   workInstallMenu
// 
// for installation and for deinstallation the functions
// 
//   workUninstallDatabase
//
switch (fetchPOST('was')) {
    case "install":
        // Erstellung einer eigenen Configdatei für dieses Modul
        // fest eingestellte Werte sollten in einer eigenen Configdatei gespeichet werden,
        // da nachträgliche Änderungen einfacher sind als im Quellcode des Scrips danach zu suchen.
        // Alle Zeilen werden nach und nach in die Variable $merk gespeichert.
        // Auch wenn das Modul keine Variablen benötigt ist diese Configdatei zu erstellen, sonst funktioniert die Installation nicht!
        $merk = workInstallConfigString();

        createConfig($merk);
        unset($merk);

        // Nun wird gleich die soeben erstellte Configdatei gelesen
        // sodaß gleich mit den definierten Variablen weiter gearbeitet werden kann
        if (!@include("./config/" . $modulname . ".cfg.php")) {
            echo "<div class='system_error'>Error:<br><b>Cannot load " . $modulname . " - configuration!</b></div>";

            return;
        }

        // Nun folgt die Erweiterung der IW-DB, falls notwendig.
        if (function_exists ('workInstallDatabase')) {
            workInstallDatabase();
        }

        // Anzeige des Menübaumes mit Auswahlmöglichkeit, in welches HauptMenü
        // das Modul eingetragen werden soll.
        createMenu();

        return;

    case "install2":
        // Erzeugung der Menue-Eintraege, falls notwendig.
        if (function_exists ('workInstallMenu')) {
            workInstallMenu();
        }

        echo "<form method='POST'>";
        echo " <input type='hidden' name='action' value='admin_menue'>";
        echo " <input type='submit' value='Installation fertig stellen' name='fertig' class='submit'>";
        echo "</form>";

        return;

    case "uninstall":
        // Anzeige des Deinstallationshinweises, mit der Möglichkeit, noch mal abzubrechen.
        // Wer hier falsch klickt ist selbst schuld ... :)
        echo "<br>" .
            "Das Modul '<b>" . $modultitle . "'</b> soll jetzt deinstalliert werden.<br>" .
            "Die Deinstallation wird Daten aus der Datenbank löschen und ist " .
            "daher nicht mehr rückgängig zu machen.<br>" .
            "<br>" .
            "Soll die Deinstallation wirklich durchgeführt werden?";
        echo "<table width='100%'><tr><td align='right'>";
        echo "<form method='POST'>";
        echo " <input type='hidden' name='action' value='" . $modulname . "'>";
        echo " <input type='hidden' name='was' value='uninstall2'>";
        echo " <input type='submit' value='Ja, klar doch' name='fertig' class='submit' style='width: 200px'>";
        echo "</form>";
        echo "</td><td align='left'>";
        echo "<form method='POST'>";
        echo " <input type='hidden' name='action' value='admin_menue'>";
        echo " <input type='submit' value='Besser doch nicht' name='fertig' class='submit' style='width: 200px'>";
        echo "</form>";
        echo "</td></tr></table>";

        return;

    case "uninstall2":
        // Hier findet jetzt wirklich die Deinstallation statt.
        //Deinstallation der Menüeinträge, falls notwendig.
        if (function_exists('workInstallMenu')) {
            removeMenuItems();
        }

        //Deinstallation der Datenbankeinträge, falls notwendig.
        if (function_exists('workUninstallDatabase')) {
            workUninstallDatabase();
        }

        removeConfig();

        echo "<div class='system_notification'>Deinstallation: Abgeschlossen</div><br>";
        echo "<form method='POST'>";
        echo " <input type='hidden' name='action' value='admin_menue'>";
        echo " <input type='submit' value='Deinstallation fertig stellen' name='fertig' class='submit'>";
        echo "</form>";

        return;

    default:
        return;
}