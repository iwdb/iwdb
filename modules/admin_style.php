<?php
/*****************************************************************************
 * admin_style.php                                                           *
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

$action = 'default';

$daction = GetVar('daction');
$submit = GetVar('submit');

//submit existiert also neue Datei erstellen
if (!empty($submit) AND !empty($daction) AND ($daction == 'send')) {

    // datei erstellen und zum schrieben öffnen
    $fp = @fopen("css/style2.css", "w");
    if ($fp == false) {
        die ("Kann style2.css nicht öffnen.");
    }

    //Standartzeug am Anfang in die Datei schreiben
    $newlines     = array();
    $newlines[0]  = '/* style.css */';
    $newlines[1]  = '';
    $newlines[2]  = '/* Diese Style-CSS ist in 2 Teile aufgeteilt */';
    $newlines[3]  = '/* der erste Teil kann durch ein Modul ausgelesen werden */';
    $newlines[4]  = '/* deswegen dort nichts ändern! */';
    $newlines[5]  = '';
    $newlines[6]  = '/* Diese Teil wurde automatisch erstellt */';
    $newlines[7]  = '/* Zeitcode: ' . strftime(CONFIG_DATETIMEFORMAT, CURRENT_UNIX_TIME) . ' */';
    $newlines[12] = '';
    foreach ($newlines as $newline) {
        fwrite($fp, $newline . "\r\n");
    }
    unset($newlines);

    //Anzahl der Eigenschaften
    $rowcount = GetVar('row_count');
    $i        = 1;
    //jede Eigenschaft durchgehen
    for ($i = 1; $i <= $rowcount; $i++) {

        //header für die Eigenschaft schreiben
        $newlines    = array();
        $newlines[0] = '';
        $newlines[1] = '/* !N ' . trim(htmlspecialchars(GetVar('row_' . $i . '_name', true), ENT_NOQUOTES, 'UTF-8')) . ' */';
        $newlines[2] = '/* !T ' . trim(htmlspecialchars(GetVar('row_' . $i . '_title', true), ENT_NOQUOTES, 'UTF-8')) . ' */';
        $newlines[3] = '/* ' . trim(htmlspecialchars(GetVar('row_' . $i . '_conf', true), ENT_NOQUOTES, 'UTF-8')) . ' */';
        $newlines[4] = '';
        foreach ($newlines as $newline) {
            fwrite($fp, $newline . "\r\n");
        }
        unset($newlines);

        //Unterscheidung ob !A oder !I
        $conf = trim(htmlspecialchars(GetVar('row_' . $i . '_conf', true), ENT_NOQUOTES, 'UTF-8'));
        if ($conf == '!A') {
            //a also einfach alles was in der Value steckt rein schreiben
            $newline = trim(htmlspecialchars(GetVar('row_' . $i . '_value', true), ENT_NOQUOTES, 'UTF-8'));
            fwrite($fp, $newline . "\r\n");
        } else if ($conf == '!I') {
            //i
            //start reinschreiben
            $newlines    = array();
            $newlines[0] = trim(htmlspecialchars(GetVar('row_' . $i . '_start', true), ENT_NOQUOTES, 'UTF-8'));

            //nun die Menge der Werte auslesen
            $numwerte = trim(htmlspecialchars(GetVar('row_' . $i . '_werte', true), ENT_NOQUOTES, 'UTF-8'));
            $j        = 1;
            for ($j = 1; $j <= $numwerte; $j++) {
                $newlines[$j] = '    ' . trim(htmlspecialchars(GetVar('cell_k_' . $i . '_' . $j, true), ENT_NOQUOTES, 'UTF-8')) . ':' . trim(htmlspecialchars(GetVar('cell_v_' . $i . '_' . $j, true), ENT_NOQUOTES, 'UTF-8')) . '; ';
            }

            //ende reinschreiben
            $newlines[$numwerte + 1] = trim(GetVar('row_' . $i . '_end'));

            //und nun der ganze Mist in die Datei:
            foreach ($newlines as $newline) {
                fwrite($fp, $newline . "\r\n");
            }
            unset($newlines);
        }

        //das ganze mit 2 Leerzeilen beenden
        $newline = '';
        fwrite($fp, $newline . "\r\n");

    }

    //nun noch das Ende reinschrieben
    $newlines    = array();
    $newlines[0] = '';
    $newlines[1] = '/* !END */';
    $newlines[2] = '';
    $newlines[5] = htmlspecialchars(GetVar('row_other_value', true), ENT_NOQUOTES, 'UTF-8');
    $newlines[6] = '';
    foreach ($newlines as $newline) {
        fwrite($fp, $newline . "\r\n");
    }
    unset($newlines);

    //und die Datei wieder schließen, damit sie zum lesen wieder geöffnet werden kann
    fclose($fp);

    //datei zum guten Schluss (also im Erfolgsfall) verschieben und die temporäre löschen
    if (copy('css/style2.css', 'css/style.css')) {
        if (!unlink("css/style2.css")) {
            echo "<div class='system_error'>Date konnte nicht verschoben werden</div>";
        }
    } else {
        echo "<div class='system_error'>Date konnte nicht verschoben werden</div>";
    }

}

//anzeigen der CSS Datei
if ($action == 'default') {

    doc_title("Style");

    //Tabelle beginnen
    ?>
    <form action='index.php?action=admin&uaction=style&daction=send' method='post'>
    <table style='width: 100%;'>
    <tr>
        <td colspan="4">&nbsp;</td>
    </tr>

    <?php
    // Datei öffnen
    $fp = @fopen("css/style.css", "r");
    if ($fp == false) {
        die ("Kann Datei nicht lesen.");
    }

    //Daten resetten
    $num_eigenschaft = 0;
    $where = 0;

    // Datei zeilenweise auslesen
    while ($line = fgets($fp, "1024")) {

        //Datei auslesen, alle <> parse, da die eh nicht vorkommen dürfen
        $line = htmlspecialchars($line, ENT_NOQUOTES, 'UTF-8');

        //Neue Eigenschaft
        $pos = strpos($line, '!N');
        if (!($pos === false)) {

            //CSS endet bei A
            if ($where == 200) {

                echo "</textarea> \n";
                echo "</td>  \n";
                echo "</tr> \n";

                $where = 199;
            }

            $num_eigenschaft++;
            $line = str_replace('!N', '', $line);
            $line = str_replace('/*', '', $line);
            $line = str_replace('*/', '', $line);

            echo "</tr>  \n";
            echo "<tr>   \n";
            echo "<td colspan='2' class='windowbg2'>   \n";
            echo $num_eigenschaft;
            echo ")&nbsp;  ";
            echo trim($line);
            echo "<input type='hidden' name='row_" . $num_eigenschaft . "_name' value='" . $line . "'>";
            echo "</td>  \n";

            $where = 146;

            continue;
        }

//ganze Beenden und den Rest nur noch in das Textfeld schreiben
        $pos = strpos($line, '!END');
        if (!($pos === false)) {

            //CSS endet bei A
            if ($where == 200) {

                echo "</textarea> \n";
                echo "</td>  \n";
                echo "</tr> \n";

                $where = 199;
            }

            echo "</tr>  \n";
            echo "<tr>   \n";
            echo "<td colspan='5' class='windowbg2'>   \n";
            echo  'other';
            echo "<input type='hidden' name='row_count' value='" . $num_eigenschaft . "'>";
            echo "</td>  \n";
            echo "</tr>  \n";
            echo "<tr>  \n";
            echo "<td colspan='4' class='windowbg1 center'>  \n";
            echo "<textarea cols='70' rows='100' name='row_other_value'>";

            $where = 350;

            continue;
        }

//der Titel
        $pos = strpos($line, '!T');
        if (!($pos === false)) {

            $line = str_replace('!T', '', $line);
            $line = str_replace('/*', '', $line);
            $line = str_replace('*/', '', $line);

            echo "<td colspan='2' class='windowbg2'>   \n";
            echo trim($line);
            echo "<input type='hidden' name='row_" . $num_eigenschaft . "_title' value='" . $line . "'>";
            echo "</td>  \n";
            echo "</tr>  \n";

            $where = 147;

            continue;
        }

//Rest einfach noch eintragen
//CSS läuft durch bei A 
        if ($where == 350) {
            echo $line;

            continue;
        }

//Konfiguration A
        $pos = strpos($line, '!A');
        if (!($pos === false)) {

            $line = str_replace('/*', '', $line);
            $line = str_replace('*/', '', $line);

            echo "<tr>  \n";
            echo "<td colspan='4' class='windowbg1 center'>  \n";
            echo "<input type='hidden' name='row_" . $num_eigenschaft . "_conf' value='" . $line . "'>";
            echo "<textarea cols='70' rows='20' name='row_" . $num_eigenschaft . "_value'>";

            $where = 200;

            continue;
        }


//CSS läuft durch bei A 
        if ($where == 200) {
            echo $line;

            continue;
        }

//Konfiguration I
        $pos = strpos($line, '!I');
        if (!($pos === false)) {

            $line = str_replace('/*', '', $line);
            $line = str_replace('*/', '', $line);

            echo "<input type='hidden' name='row_" . $num_eigenschaft . "_conf' value='" . $line . "'>";

            $where = 50;

            continue;
        }

//CSS startet bei I
        $pos = strpos($line, '{');
        if (!($pos === false) AND ($where == 50)) {

            echo "<input type='hidden' name='row_" . $num_eigenschaft . "_start' value='" . $line . "'>";

            $where    = 100;
            $num_wert = 0;

            continue;
        }

//CSS endet bei I
        $pos = strpos($line, '}');
        if (!($pos === false) AND ($where == 100)) {

            echo "<input type='hidden' name='row_" . $num_eigenschaft . "_end' value='" . $line . "'>";
            echo "<input type='hidden' name='row_" . $num_eigenschaft . "_werte' value='" . $num_wert . "'>";

            $where = 99;

            continue;
        }

//CSS  I
        $pos = strpos($line, ':');
        if (($where == 100) AND (trim($line) <> '') AND  !($pos === false)) {

            $num_wert++;

            $temp    = explode(":", $line);
            $temp[0] = trim($temp[0]);
            $temp[1] = trim($temp[1]);
            $temp[1] = str_replace(';', '', $temp[1]);

            $key   = $temp[0];
            $value = $temp[1];

            echo "<tr> ";
            echo "<td colspan='1' class='windowbg1'> \n";
            echo "- - -";
            echo "</td> ";
            echo "<td colspan='2' class='windowbg1'> \n";
            echo "<input type='hidden' name='cell_k_" . $num_eigenschaft . "_" . $num_wert . "' value='" . $key . "'>";
            echo $key;
            echo "</td> ";
            echo "<td colspan='2' class='windowbg1'> \n";
            echo "<input type='text' name='cell_v_" . $num_eigenschaft . "_" . $num_wert . "' value='" . $value . "'>";
            echo "</td> ";
            echo "</tr> ";

            continue;
        }

    }
    // Datei schliessen
    fclose($fp);

    echo '
</textarea>
</td>
</tr>';
    ?>

    </table>
    <div class='doc_centered'><input type='submit' name='submit' value='Änderungen speichern und CSS Datei erstellen'>
    </div>
    </form>

<?php
}
?>