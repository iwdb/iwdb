<?php
/*****************************************************************************/
/* menu_default.php                                                          */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* ========================================================================= */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* ========================================================================= */
/* Copyright (c) 2007 Einfallslos & Atroxx - All Rights Reserved             */
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
//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

$anzahl = 0;
$anzauftrag = "";

if (($user_adminsitten == SITTEN_BOTH) || ($user_adminsitten == SITTEN_ONLY_LOGINS)) {
    // Anstehende Aufträge zählen
    $anzauftrag = "";

    $sql    = "SELECT count(*) AS anzahl FROM " . $db_tb_sitterauftrag .
        " WHERE date_b2 < " . CURRENT_UNIX_TIME;
    $result = $db->db_query($sql);
    $row    = $db->db_fetch_array($result);
    $anzahl = $row['anzahl'];
    $db->db_free_result($result);

    // Wurde gerade ein Auftrag erledigt? Falls ja, muss dieser von der
    // gerade ermittelten Zahl abgezogen werden.
    if ($action == "sitterliste") {
        $erledigt = getVar('erledigt');

        if (!empty($erledigt)) {
            $anzahl = $anzahl - 1;
        }
    }

    if ($anzahl > 0) {
        $anzauftrag = " (" . $anzahl . " offen)";
    }

}

include ('configmenu.php');

?>
<link rel="stylesheet" type="text/css" href="css/chromemenu-style2.css"/>
<script type="text/javascript" src="javascript/chromemenu.js"></script>
<!-- main menu start -->
<div class="chromestyle" id="chromemenu">
    <ul>
        <li>
            <table align="left" class='table_format_noborder'>
                <tr>
                    <td><strong>Hallo, <?php echo $user_id;?>.</strong></td>
                </tr>
            </table>
        </li>
        <li>
            <table align="right">
                <tr>
                    <td id="doc_usersonline" style="font-weight: bold;"></td>
                </tr>
            </table>
        </li>
        <?php
        // Menu auslesen
        $sql = "SELECT menu, title, status, sittertyp FROM " . $db_tb_menu . " WHERE active=1 AND submenu=0 ORDER BY menu ASC";

        $result = $db->db_query($sql);

        $miscmenu = 0;
        // Alle Menu-Eintraege durchgehen
        while ($row = $db->db_fetch_array($result)) {
            // Ist sitten für diesen Menu-Eintrag erlaubt?
            $sitterentry = ($user_adminsitten == SITTEN_BOTH)
                || ($row['sittertyp'] == 0)
                || ($user_adminsitten == SITTEN_ONLY_LOGINS
                    && ($row['sittertyp'] == 1 || $row['sittertyp'] == 3))
                || ($user_adminsitten == SITTEN_ONLY_NEWTASKS
                    && ($row['sittertyp'] == 2 || $row['sittertyp'] == 3));

            // Falls nicht, mit dem naechsten Eintrag weitermachen.
            if (!$sitterentry) {
                continue;
            }

            $title = $row['title'];
            $title = preg_replace('/#\S+/', $anzauftrag, $title);

            $miscmenu = $row['menu'];

            // Hat der angemeldete Benutzer die entsprechende Berechtigung?
            if (($row['status'] == "") || ($user_status == "admin") || ($user_status == $row['status'])) {
                if (($title == "Sitting") && ($anzahl > 0)) {
                    echo '<li style="background-image:url(\'menue/aktiv.gif\');" > ' . "\n";
                    echo "        <a href='#' rel='dropmenu" . ($row['menu'] - 1) . "'>" . $title .
                        " " . $anzauftrag . "</a>\n";
                } else {
                    echo "      <li>\n";
                    echo "        <a href='#' rel='dropmenu" . ($row['menu'] - 1) . "'>" . $title . "</a>\n";
                }
                echo "      </li>\n";
            }
        }
        // Standard Menü
        echo "      <li>\n";
        echo "        <a href='#' rel='dropmenu" . $miscmenu . "'>Misc</a>\n";
        echo "      <li>\n";

        // Abschliessen
        echo "    </ul>\n";
        echo "  </div>\n";

        // Menu nochmal auslesen, diesmal die SubMenüs
        $sql = "SELECT menu, submenu, title, status, action, extlink, sittertyp FROM " .
            $db_tb_menu . " WHERE active=1 AND submenu > 0 ORDER BY menu ASC, submenu ASC";
        $result = $db->db_query($sql);

        $lastmenu = 0;
        $tableopen = 0;

        // Alle Menu-Eintraege durchgehen
        while ($row = $db->db_fetch_array($result)) {
            // Ist sitten für diesen Menu-Eintrag erlaubt?
            $sitterentry = ($user_adminsitten == SITTEN_BOTH)
                || ($row['sittertyp'] == 0)
                || ($user_adminsitten == SITTEN_ONLY_LOGINS
                    && ($row['sittertyp'] == 1 || $row['sittertyp'] == 3))
                || ($user_adminsitten == SITTEN_ONLY_NEWTASKS
                    && ($row['sittertyp'] == 2 || $row['sittertyp'] == 3));

            // Falls nicht, mit dem naechsten Eintrag weitermachen.
            if (!$sitterentry) {
                continue;
            }

            // Hat der angemeldete Benutzer die entsprechende Berechtigung?
            if (($row['status'] == "") || ($user_status == "admin") || ($user_status == $row['status'])) {
                // Neues Hauptmenu?
                if ($lastmenu != $row['menu']) {
                    // Bin ich noch in der vorhergehenden Tabelle? Dann entsprechend schliessen.
                    if ($tableopen != 0) {
                        echo "    </div>\n";
                    }

                    echo "    <div id='dropmenu" . ($row['menu'] - 1) . "' class='dropmenudiv' style='width:200px; text-align:left;'>\n";

                    // Neue Tabelle aufmachen.
                    $tableopen = 1;
                    $lastmenu  = $row['menu'];
                }

                $title = $row['title'];
                $title = preg_replace('/#\S+/', $anzauftrag, $title);

                echo "      <a href='";
                // Es handelt sich hier um einen "internen" Link.
                if ($row['extlink'] == "n") {
                    echo "index.php?action=" . $row['action'] . "'>" . $title . "</a>";
                } else {
                    // Linkziel und Titel ausgeben
                    echo $row['action'] . "' target=_new>" . $title . "</a>";
                }
            }
        }

        if ($tableopen != 0) {
            echo "    </div>\n";
        }

        echo "    <div id='dropmenu" . $miscmenu . "' class='dropmenudiv' style='width:150px; text-align:left;'>\n
      <a href='index.php'><img src='bilder/icon_mini_home.gif' width='12' height='13' alt='Startseite' align='absmiddle'> Startseite</a>
      <a href='index.php?action=profile'><img src='bilder/icon_mini_profile.gif' width='12' height='13' alt='profil' align='absmiddle'> profil</a>
      ";
        if ($user_status == "admin") {
            ?>
            <a href="index.php?action=admin"><img src="bilder/icon_mini_members.gif" width="12" height="13" alt="admin" align="absmiddle">
                <span style="color:#e50f9f">admin</span></a>
        <?php } ?>
        <a href="index.php?action=help&topic=<?php echo $action;?>"><img src="bilder/icon_mini_search.gif" width="12" height="13" alt="profile" align="absmiddle"><span style="color:#e50f9f">
                hilfe</span></a>
        <a href="index.php?action=memberlogout2"><img src="bilder/icon_mini_login.gif" width="12" height="13" alt="login" align="absmiddle">
            logout</a>
</div>
<!-- main menu ende -->
<script type="text/javascript"> cssdropdown.startchrome("chromemenu") </script>
<!-- menu Ende -->
<br><br>
<script src="javascript/menu_atroxx.js"></script>
<!-- hauptfenster Start -->
<table align="center" style="width:100%;">
    <tr>
        <td class="windowbg1" align="center">
