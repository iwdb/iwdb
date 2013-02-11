<?php
/*****************************************************************************
 * showhighscore.php                                                         *
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

// Nachfolgendes define auf FALSE setzen, wenn in der Liste der Hall of Shame
// nur negative Werte vorkommen dürfen.
define('SHOW_ONLY_NEGATIVE', true);

$ressu = (isset($db_tb_ressuebersicht) && !empty($db_tb_ressuebersicht)) ? true : false;

$hs_places = filter_int(getVar('to'), 5, 1, null);

//Limitieren der Highscoreplätze auf die Anzahl der IW-Accounts
$sql = "SELECT COUNT( DISTINCT sitterlogin ) AS 'igaccs' FROM " . $db_tb_user . ";";
$result = $db->db_query($sql) or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);

if ($hs_places > $row['igaccs']) {
    $hs_places = $row['igaccs'];
}

doc_title('Wer hat den Längsten?');

echo '
<form action="index.php" method="post"><p>
<input type="hidden" name="action" value="showhighscore">
<input type="hidden" name="sid" value="' . $sid . '">
Top/Flop <input type="number" min="1" max="999" name="to" value="' . $hs_places . '" size="3">
<input type="submit" value="zeigen" name="B1">
</p></form>
';

start_table(100, 0, 10, 0, "");
start_row('windowbg2 center', '', 3);
echo "<div style='font-size: 15px; color: white'>HALL OF FAME - TOP {$hs_places} DER BESTEN</div>";
next_row();

if ($ressu) {
    createRessieTable("Eisen", "DESC");
    next_cell();
    createRessieTable("Stahl", "DESC");
    next_cell();
    createRessieTable("VV4A", "DESC");

    next_row();
    createRessieTable("Chemie", "DESC", "chem");
    next_cell();
    createRessieTable("Eis", "DESC");
    next_cell();
    createRessieTable("Wasser", "DESC");

    next_row();
    createRessieTable("Energie", "DESC");
    next_cell();
    createRessieTable("FP", "DESC", "fp_ph");
    next_cell();
    createRessieTable("Credits", "DESC");

    next_row();
    if ($user_fremdesitten != 1) {
        createRessieTable(
            "Sitter&shy;punkte", "DESC", "sys", 0,
            "SELECT sitterlogin AS user, sitterpunkte AS ressie FROM " . $db_tb_user
                . " WHERE allianz='" . $user_allianz
                . "' ORDER BY sitterpunkte DESC"
        );
    } else {
        createRessieTable(
            "Sitter&shy;punkte", "DESC", "sys", 0,
            "SELECT sitterlogin AS user, sitterpunkte AS ressie FROM " . $db_tb_user
                . " ORDER BY sitterpunkte DESC"
        );
    }
}

next_cell();
if ($user_fremdesitten != 1) {
    createRessieTable(
        "Geo&shy;scan", "DESC", "geo", 0,
        "SELECT sitterlogin AS user, geopunkte AS ressie FROM " . $db_tb_user
            . " WHERE allianz='" . $user_allianz
            . "' ORDER BY geopunkte DESC"
    );
} else {
    createRessieTable(
        "Geo&shy;scan", "DESC", "geo", 0,
        "SELECT sitterlogin AS user, geopunkte AS ressie FROM " . $db_tb_user
            . " ORDER BY geopunkte DESC"
    );
}

next_cell();
if ($user_fremdesitten != 1) {
    createRessieTable(
        "System&shy;scan", "DESC", "sys", 0,
        "SELECT sitterlogin AS user, syspunkte AS ressie FROM " . $db_tb_user
            . " WHERE allianz='" . $user_allianz
            . "' ORDER BY syspunkte DESC"
    );
} else {
    createRessieTable(
        "System&shy;scan", "DESC", "sys", 0,
        "SELECT sitterlogin AS user, syspunkte AS ressie FROM " . $db_tb_user
            . " ORDER BY syspunkte DESC"
    );
}

if ($ressu) {
    next_row('windowbg2 center', '', 3);
    echo "<div style='font-size: 15px; color: white'>HALL OF SHAME - TOP {$hs_places} DER ERSTEN VON HINTEN</div>";
    next_row();

    createRessieTable("Eisen", "ASC");
    next_cell();
    createRessieTable("Stahl", "ASC");
    next_cell();
    createRessieTable("VV4A", "ASC");

    next_row();
    createRessieTable("Chemie", "ASC", "chem");
    next_cell();
    createRessieTable("Eis", "ASC");
    next_cell();
    createRessieTable("Wasser", "ASC");

    next_row();
    createRessieTable("Energie", "ASC");
    next_cell();
    createRessieTable("FP", "ASC", "fp_ph");
    next_cell();
    if ($user_fremdesitten != 1) {
        createRessieTable(
            "Sitter&shy;punkte", "ASC", "sys", 0,
            "SELECT sitterlogin AS user, sitterpunkte AS ressie FROM " . $db_tb_user
                . " WHERE allianz='" . $user_allianz
                . "' ORDER BY sitterpunkte ASC"
        );
    } else {
        createRessieTable(
            "Sitter&shy;punkte", "ASC", "sys", 0,
            "SELECT sitterlogin AS user, sitterpunkte AS ressie FROM " . $db_tb_user
                . " ORDER BY sitterpunkte ASC"
        );
    }
}

end_row();
end_table();
return;

//******************************************************************************
//
// Füllt eine kleine Tabelle mit den Werten für eine Hall of fame/shame.
// 
function createRessieTable($ressie, $direction, $altress = "", $decimals = 2, $altsql = "")
{
    global $db, $db_tb_ressuebersicht, $db_tb_user, $hs_places, $user_fremdesitten, $user_allianz;

    if (empty($altress)) {
        $lowress = strtolower($ressie);
    } else {
        $lowress = $altress;
    }

    if ($direction == "ASC") {
        $pic = '<img src="./bilder/krone_flop_' . $lowress . '.gif" alt="' . $ressie . '-Letzter">';
    } else {
        $pic = '<img src="./bilder/krone_top_' . $lowress . '.gif" alt="' . $ressie . '-Erster">';
    }

    echo "<table class='table_format' style='width: 350;'>\n";

    start_row('windowbg2');
    next_cell('windowbg2', 'style="width:20ex;"');
    echo '<b>Username</b>';
    next_cell('windowbg2', 'style="width:15ex;"');
    echo '<b>' . $ressie . '</b>';

    // Setze SQL String aus ressource und Sortierrichtung zusammen, wenn
    // keine andere Quelle angegeben wurde.
    if (empty($altsql)) {
        $sql = "SELECT user, " . $lowress . " AS ressie FROM " . $db_tb_ressuebersicht;
        if ($user_fremdesitten != 1) {
            $sql .= "," . $db_tb_user .
                " WHERE " . $db_tb_ressuebersicht . ".user=" . $db_tb_user . ".id " .
                " AND " . $db_tb_user . ".allianz='" . $user_allianz . "'";
        }
        $sql .= " ORDER BY " . $lowress . " " . $direction;
    } else {
        $sql = $altsql;
    }

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $count = 0;
    while ($count < $hs_places && $row = $db->db_fetch_array($result)) {
        next_row("windowbg1 center");
        if ($count == 0) {
            echo $pic;
        } else {
            echo $count + 1;
        }
        next_cell("windowbg1");
        if ((SHOW_ONLY_NEGATIVE === true)
            || ($direction == "DESC" && $row['ressie'] > 0)
            || ($direction == "ASC" && $row['ressie'] <= 0)
        ) {
            echo $row['user'];
        } else {
            echo "&nbsp;";
        }
        next_cell("windowbg1 right");
        if ((SHOW_ONLY_NEGATIVE === true)
            || ($direction == "DESC" && $row['ressie'] > 0)
            || ($direction == "ASC" && $row['ressie'] <= 0)
        ) {
            echo number_format($row['ressie'], $decimals, ',', '.');
        } else {
            echo "&nbsp;";
        }
        $count++;
    }

    while ($count < $hs_places) {
        next_row("windowbg1");
        if ($count == 0) {
            echo $pic;
        }
        next_cell("windowbg1");
        echo "&nbsp;";
        next_cell("windowbg1");
        echo "&nbsp;";
        $count++;
    }
    end_row();
    end_table();
}