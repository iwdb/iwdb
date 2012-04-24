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
if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
    die('Hacking attempt...');

if ( $user_rules == "1" )
{
  if ( $user_id <> "guest" )
    {
      $anzahl = 0;
        $anzauftrag = "";
      $msgzahl = 0;
        $anzmsg = "";

    if(( $user_adminsitten == SITTEN_BOTH ) || ( $user_adminsitten == SITTEN_ONLY_LOGINS ))
    {
      // Anstehende Aufträge zählen
      $anzauftrag = "";

      $sql = "SELECT count(*) AS anzahl FROM " . $db_tb_sitterauftrag .
             " WHERE date_b2 < " . ( $config_date );
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      $row = $db->db_fetch_array($result);
      $anzahl = $row['anzahl'];
      $db->db_free_result($result);

      // Nachrichten zählen
      $anzmsg = "";

      $sql = "SELECT count(*) AS msgzahl FROM " . $db_tb_mails .
	               " WHERE userto = '$user_id' AND readed = 0 ";
	        $result = $db->db_query($sql)
	          or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	        $row = $db->db_fetch_array($result);
	        $msgzahl = $row['msgzahl'];
      $db->db_free_result($result);

      if($anzahl > 0) {
        $anzauftrag = " (" . $anzahl . " offen)";
      }

      if($msgzahl > 0) {
	          $anzmsg = " (" . $msgzahl . ")";
      }
    }

include ('configmenu.php');

?>
  <link rel="stylesheet" type="text/css" href="menue/menuestyle2.css" />
  <script type="text/javascript" src="menue.js"></script>
  <!-- main menu start -->
  <div class="chromestyle" id="chromemenu" style=" position: fixed; width:89.7%; top:1.1px;" >
    <ul>
      <li>
        <table align="left">
                  <tr>
                      <td><strong>Hallo, <?php echo $user_id;?>.</strong><td>
                    </tr>
                </table>
      </li>
      <li>
              <table align="right">
                  <tr>
                    <td><strong>Online: <?php echo ($counter_guest+$counter_member);?></strong><td>
                    </tr>
                </table>
      </li>
<?php
    // Menu auslesen
    $sql = "SELECT menu, title, status, sittertyp FROM " .
           $db_tb_menu . " WHERE active=1 AND submenu=0 ORDER BY menu ASC";

    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        $miscmenu = 0;
    // Alle Menu-Eintraege durchgehen
    while( $row = $db->db_fetch_array($result)) {
      // Ist sitten für diesen Menu-Eintrag erlaubt?
      $sitterentry = ($user_adminsitten == SITTEN_BOTH) ||
                                   ($row['sittertyp'] == 0 ) ||
                       ($user_adminsitten == SITTEN_ONLY_LOGINS &&
                                       ($row['sittertyp'] == 1 || $row['sittertyp'] == 3 )) ||
                       ($user_adminsitten == SITTEN_ONLY_NEWTASKS &&
                                       ($row['sittertyp'] == 2 || $row['sittertyp'] == 3 ));

        // Falls nicht, mit dem naechsten Eintrag weitermachen.
        if(!$sitterentry)
            continue;

          $title = $row['title'];
          $title = str_replace("#", $anzauftrag, $title);

           $miscmenu = $row['menu'];

        // Hat der angemeldete Benutzer die entsprechende Berechtigung?
      if(($row['status'] == "") || ($user_status == "admin") || ($user_status == $row['status'])) {
              if(($title == "Sitting") && ($anzahl > 0)) {
                  echo '<li style="background-image:url(\'menue/aktiv.gif\');" > ' . "\n";
                  echo "        <a href=\"#\" rel=\"dropmenu" . ($row['menu'] - 1) . "\">" . $title .
                             " " . $anzauftrag . "</a>\n";
             }
             if(($title == "Nachrichten") && ($msgzahl > 0)) {
                  echo '<li style="background-image:url(\'menue/aktiv.gif\');" > ' . "\n";
                  echo "        <a href=\"#\" rel=\"dropmenu" . ($row['menu'] - 1) . "\">" . $title .
                             " *</a>\n";
             }	else {
                echo "      <li>\n";
                  echo "        <a href=\"#\" rel=\"dropmenu" . ($row['menu'] - 1) . "\">" . $title . "</a>\n";
                }
              echo "      </li>\n";
            }
        }
        // Standard Menü
    echo "      <li>\n";
        echo "        <a href=\"#\" rel=\"dropmenu" . $miscmenu . "\">Misc</a>\n";
    echo "      <li>\n";

        // Abschliessen
        echo "    </ul>\n";
        echo "  </div>\n";

    // Menu nochmal auslesen, diesmal die Submenüs
    $sql = "SELECT menu, submenu, title, status, action, extlink, sittertyp FROM " .
               $db_tb_menu . " WHERE active=1 AND submenu > 0 ORDER BY menu ASC, submenu ASC";
    $result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $lastmenu    = 0;
    $tableopen   = 0;

    // Alle Menu-Eintraege durchgehen
    while( $row = $db->db_fetch_array($result)) {
      // Ist sitten für diesen Menu-Eintrag erlaubt?
      $sitterentry = ($user_adminsitten == SITTEN_BOTH) ||
                                   ($row['sittertyp'] == 0 ) ||
                       ($user_adminsitten == SITTEN_ONLY_LOGINS &&
                                       ($row['sittertyp'] == 1 || $row['sittertyp'] == 3 )) ||
                       ($user_adminsitten == SITTEN_ONLY_NEWTASKS &&
                                       ($row['sittertyp'] == 2 || $row['sittertyp'] == 3 ));

        // Falls nicht, mit dem naechsten Eintrag weitermachen.
        if(!$sitterentry)
            continue;

        // Hat der angemeldete Benutzer die entsprechende Berechtigung?
      if(($row['status'] == "") || ($user_status == "admin") || ($user_status == $row['status'])) {
		if(($title == "Dein Postfach") && ($msgzahl > 0)) {
			  	                    echo "        <a href=\"#\" rel=\"dropmenu" . ($row['menu'] - 1) . "\">" . $title .
	                               " " . $anzmsg . "</a>\n";
             }

          // Neues Hauptmenu?
        if($lastmenu != $row['menu']) {
              // Bin ich noch in der vorhergehenden Tabelle? Dann entsprechend schliessen.
            if($tableopen != 0) {
                        echo "    </div>\n";
              }

                    echo "    <div id=\"dropmenu" . ($row['menu'] - 1) . "\" class=\"dropmenudiv\" style=\"width:200px; position:fixed; text-align:left;\">\n";

                // Neue Tabelle aufmachen.
              $tableopen = 1;
              $lastmenu = $row['menu'];
          }

          $title = $row['title'];
          $title = str_replace("#", $anzauftrag, $title);

               echo "      <a href=\"";
              // Es handelt sich hier um einen "internen" Link.
              if($row['extlink'] == "n") {
			  echo "index.php?sid=" . $sid . "amp;action=". $row['action'] . "\">" . $title . "</a>";
			}else{
			// Linkziel und Titel ausgeben
			echo $row['action'] . "\" target=_new>" . $title . "</a>";
			}
          }
    }

      if($tableopen != 0) {
          echo "    </div>\n";
        }
  }
}
  echo "    <div id=\"dropmenu" . $miscmenu .  "\" class=\"dropmenudiv\" style=\"width:150px; text-align:left; position:fixed;\">\n
      <a href=\"index.php?sid=$sid\"><img src=\"bilder/icon_mini_home.gif\" width=\"12\" height=\"13\" alt=\"Startseite\" border=\"0\" align=\"absmiddle\"> Startseite</a>
      <a href=\"index.php?action=profile&sid=$sid\"><img src=\"bilder/icon_mini_profile.gif\" width=\"12\" height=\"13\" alt=\"profil\" border=\"0\" align=\"absmiddle\"> profil</a>
      ";
      if ( $user_status == "admin" )  {      ?>
        <a href="index.php?action=admin&sid=<?php echo $sid;?>"><img src="bilder/icon_mini_members.gif" width="12" height="13" alt="admin" border="0" align="absmiddle"> <font color="#e50f9f">admin</font></a></strong>
      <?php  } ?>
      <a href="index.php?action=help&topic=<?php echo $action;?>&sid=<?php echo $sid;?>"><img src="bilder/icon_mini_search.gif" width="12" height="13" alt="profile" border="0" align="absmiddle"><font color="#e50f9f"> hilfe</font></a>
      <a href="index.php?action=memberlogout2&sid=<?php echo $sid;?>"><img src="bilder/icon_mini_login.gif" width="12" height="13" alt="login" border="0" align="absmiddle"> logout</a>
    </div>
  <!-- main menu ende -->
  <script type="text/javascript"> cssdropdown.startchrome("chromemenu") </script>
<!-- <?$config_menue?> //-->
<!-- menu Ende -->
<table width="100%" align="left" ><tr><td><small><strong>Online:</strong> <?php echo ($counter_guest+$counter_member) . " (" . $online_member . ")";?></small></td></tr></table>
<br><br>

<!-- hauptfenster Start -->
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
            <tr>
             <td class="windowbg1" style="padding-left: 0px;" align="center">
