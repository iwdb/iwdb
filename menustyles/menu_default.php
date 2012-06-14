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
/* Copyright (c) 2007 Erik Frohne - All Rights Reserved                      */
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

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (!defined('IRA'))
	die('Hacking attempt...');

// Anstehende Aufträge zählen
$anzauftrag_sitter = "";
$anzauftrag_schiffe = "";
$anzauftrag_ress = "";

if(( $user_adminsitten == SITTEN_BOTH ) || ( $user_adminsitten == SITTEN_ONLY_LOGINS ))
{
  $sql = "SELECT count(*) AS anzahl FROM " . $db_tb_sitterauftrag .
         " WHERE date_b2 < " . ( $config_date );
  if (!$user_fremdesitten)
  {
	$sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_sitterauftrag . ".user) = '" . $user_allianz . "'";
  }
  $result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
  $row = $db->db_fetch_array($result);
  $anzahl = $row['anzahl'];
  $db->db_free_result($result);

	// Wurde gerade ein Auftrag erledigt? Falls ja, muss dieser von der
	// gerade ermittelten Zahl abgezogen werden.
	if($action == "sitterliste") {
		$erledigt = getVar('erledigt');

		if( !empty($erledigt)) {
		  $anzahl = $anzahl - 1;
		}
	}

  if($anzahl > 0) {
    $anzauftrag_sitter = " (" . $anzahl . " offen)";
  }
}

if (isset($db_tb_bestellung_schiffe)) {
	$sql = "SELECT COUNT(*) AS anzahl FROM " . $db_tb_bestellung_schiffe . " WHERE erledigt=0";
	$sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_bestellung_schiffe . ".user) = '" . $user_allianz . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	$anzahl = $row['anzahl'];
	$db->db_free_result($result);
	if($anzahl > 0) {
		$anzauftrag_schiffe = " (" . $anzahl . " offen)";
	}
} else
	$anzauftrag_schiffe = "";
if (isset($db_tb_bestellung)) {
	$sql = "SELECT COUNT(*) AS anzahl FROM " . $db_tb_bestellung . " WHERE erledigt=0";
	$sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE " . $db_tb_user . ".id=" . $db_tb_bestellung . ".user) = '" . $user_allianz . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	$anzahl = $row['anzahl'];
	$db->db_free_result($result);
	if($anzahl > 0) {
		$anzauftrag_ress = " (" . $anzahl . " offen)";
	}
} else
	$anzauftrag_ress = "";

$sql = "SELECT COUNT(*) AS 'anzahl' FROM $db_tb_lieferung, $db_tb_user WHERE art='Angriff' AND $db_tb_lieferung.user_to=$db_tb_user.id AND $db_tb_lieferung.time>" . (time() - 15 * 60);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
$anzahl = $row['anzahl'];
$db->db_free_result($result);
if($anzahl > 0) {
	$anz_angriffe = " (" . $anzahl . ")";
} else
	$anz_angriffe = "";


$sql = "SELECT COUNT(*) AS 'anzahl' FROM $db_tb_lieferung, $db_tb_user WHERE art='Sondierung (Schiffe/Deff/Ress)' OR art='Sondierung (Gebäude/Ress)' AND $db_tb_lieferung.user_to=$db_tb_user.id AND $db_tb_lieferung.time>" . (time() - 5 * 60);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
$anzahl = $row['anzahl'];
$db->db_free_result($result);
if($anzahl > 0) {
	$anz_sondierungen = " (" . $anzahl . ")";
} else
	$anz_sondierungen = "";

include ('configmenu.php');


//Warnung für nicht eingelesene Ressourcenkoloübersicht seit 24 Stunden
$sql = "SELECT time FROM " . $db_tb_lager . " WHERE user='" . $user_id . "' LIMIT 0,1";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
if ($row['time']<(time()-24*60*60)) {
	?>
	<br>
	<table width="95%" border="2" cellspacing="0" cellpadding="1" bordercolor="red">
	<tr>
	<td align='center' style='color:red; font-weight:bold; font-size:1.5em;'>
	Die Ressourcenkoloübersicht wurde seit 24h nicht mehr aktualisiert!
	</td>
	</tr>
	</table>
	<?php
}

// Warnung nicht eingelesene Highscore seit 24 Stunden
$sql = "SELECT MAX(time) AS time FROM " . $db_tb_highscore . " LIMIT 0,1";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
if ($row['time'] < (time() - 24 * 60 * 60)) {
	?>
	<br>
	<table width="95%" border="2" cellspacing="0" cellpadding="1" bordercolor="red">
	<tr>
	<td align='center' style='color:red; font-weight:bold; font-size:1.5em;'>
	Die Highscore wurde seit über 24h nicht mehr aktualisiert!
	</td>
	</tr>
	</table>
	<?php
}

//Warnung für nicht eingelesene Schiffsübersicht seit 48 Stunden
$sql = "SELECT lastshipscan FROM " . $db_tb_user . " WHERE id='" . $user_id . "' LIMIT 0,1";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
if ($row['lastshipscan']<(time()-48*60*60)) {
	?>
	<br>
	<table width="95%" border="2" cellspacing="0" cellpadding="1" bordercolor="red">
	<tr>
	<td align='center' style='color:red; font-weight:bold; font-size:1.5em;'>
	Die Schiffsübersicht wurde seit 48h nicht mehr aktualisiert!
	</td>
	</tr>
	</table>
	<?php
}

//Warnung für nicht eingelesene Gebäudeübersicht seit 48 Stunden
$sql = "SELECT time FROM " . $db_tb_gebaeude_spieler . " WHERE user='" . $user_id . "' LIMIT 0,1";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
if ($row['time']<(time()-48*60*60)) {
	?>
	<br>
	<table width="95%" border="2" cellspacing="0" cellpadding="1" bordercolor="red">
	<tr>
	<td align='center' style='color:red; font-weight:bold; font-size:1.5em;'>
	Die Gebäudeübersicht wurde seit 48h nicht mehr aktualisiert!
	</td>
	</tr>
	</table>
	<?php
}

// Warnung nicht eingelesene Allikasse seit 24 Stunden
$sql = "SELECT MAX(time_of_insert) AS time FROM " . $db_tb_kasse_content . " LIMIT 0,1";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
//echo $row['time'];
$time1 = new DateTime($row['time']);
$time1 = date_format($time1,'U');
//echo $time1;
$time2 = time();
if (($time2-24*60*60)> $time1) {
	?>
	<br>
	<table width="95%" border="2" cellspacing="0" cellpadding="1" bordercolor="red">
	<tr>
	<td align='center' style='color:red; font-weight:bold; font-size:1.5em;'>
	Die Allianzkasse wurde seit über 24h nicht mehr aktualisiert!
	</td>
	</tr>
	</table>
	<?php
}

// Warnung nicht eingelesene Mitgliederliste seit 96 Stunden
$sql = "SELECT MAX(date) AS time FROM " . $db_tb_punktelog . " LIMIT 0,1";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
if ($row['time'] < (time() - 96 * 60 * 60)) {
	?>
	<br>
	<table width="95%" border="2" cellspacing="0" cellpadding="1" bordercolor="red">
	<tr>
	<td align='center' style='color:red; font-weight:bold; font-size:1.5em;'>
	Die Mitgliederliste wurde seit über 96h nicht mehr aktualisiert!
	</td>
	</tr>
	</table>
	<?php
}

?>
          <table width="95%" border="0" cellspacing="0" cellpadding="1">
            <tr>
              <td class="doc_greeting" width="20%">Hallo, <?php echo $user_id;?>.</td>
              <td class="doc_greeting" width="20%">Online: <?php echo ($counter_guest+$counter_member) . " (" . $online_member . ")";?></td>
              <td class="doc_mainmenu" width="60%">
<?php
if ( $user_id <> "guest" )
{
?>
              <a href="index.php?sid=<?php echo $sid;?>"><img
							           src="bilder/icon_mini_home.gif" width="12" height="13"
											   alt="Startseite" border="0" align="middle"> Startseite</a> |
										  <a href="index.php?action=memberlogout2&sid=<?php echo $sid;?>"><img
											   src="bilder/icon_mini_login.gif" width="12" height="13"
												 alt="login" border="0" align="middle"> logout</a> |
										  <a href="index.php?action=profile&sid=<?php echo $sid;?>"><img
											   src="bilder/icon_mini_profile.gif" width="12" height="13"
												 alt="profil" border="0" align="middle"> profil</a> |
											<a href="index.php?action=help&topic=<?php echo $action;?>&sid=<?php echo $sid;?>"><img
											   src="bilder/icon_mini_search.gif" width="12" height="13"
												 alt="profile" border="0" align="middle"> hilfe</a>  	
<?php
}
if ( $user_status == "admin" )
{
?>
               | <a href="index.php?action=admin&sid=<?php echo $sid;?>"><img src="bilder/icon_mini_members.gif" width="12" height="13" alt="admin" border="0" align="middle"> admin</a>
<?php
}
?>
              </td>
            </tr>
          </table>
          <p>&nbsp;</p>
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="12%" valign="top" class='doc_menu'>
<?php

// Menu auslesen
$sql = "SELECT menu, submenu, title, status, action, extlink, sittertyp FROM " .
       $db_tb_menu . " WHERE active=1 ORDER BY menu ASC, submenu ASC";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

$lastmenu    = 0;
$tableopen   = 0;
$insidetable = 0;

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
	  // Neues Hauptmenu?
    if($lastmenu != $row['menu']) {
		  // Bin ich noch in der vorhergehenden Tabelle? Dann entsprechend schliessen.
  	  if($tableopen != 0) {
			  if($insidetable != 0) {
				  echo "  </td>\n";
				}
  		  echo " </tr>\n</table><br>\n";
  		}

			// Neue Tabelle aufmachen.
  		echo "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" class=\"bordercolor\">\n <tr>\n";
  		$tableopen = 1;
			$insidetable = 0;
  		$lastmenu = $row['menu'];
  	}

	  $title = $row['title'];
	  $title = str_replace("#sitter", $anzauftrag_sitter, $title);
	  $title = str_replace("#schiffe", $anzauftrag_schiffe, $title);
	  $title = str_replace("#ress", $anzauftrag_ress, $title);
	  $title = str_replace("#angriffe", $anz_angriffe, $title);
	  $title = str_replace("#sondierungen", $anz_sondierungen, $title);
//  	$title = str_replace("#", $anzauftrag, $title);

		// Habe ich hier den neuen Hauptmenu-Titel?
 	  if($row['submenu'] == 0) {
		  // Ja, dann in entsprechender Formatierung ausgeben.
  	  echo "  <td class=\"titlebg\" style=\"padding: 3px;\"><b>" . $title . "</b></td>\n" .
           " </tr>\n" .
					 " <tr>\n";
  	} else {
		  // Kein Hauptmenu. Eintraege in einzelne Tabellenzelle zusammenfassen.
		  if($insidetable == 0) {
			  echo "  <td class=\"menu\">\n";
				$insidetable = 1;
			}

			echo "<a href=\"";
			// Es handelt sich hier um einen "internen" Link.
			if($row['extlink'] == "n") {
			  echo "index.php?sid=" . $sid . "&action=". $row['action'] . "\">" . $title . "</a>";
			}else{
			// Linkziel und Titel ausgeben
			echo $row['action'] . "\" target=_new>" . $title . "</a>";
			}
			
		}
  }
}

// Restliche Tabelle wieder schliessen.
if($tableopen != 0) {
  if($insidetable != 0) {
    echo "  </td>\n";
  }
  echo " </tr>\n</table><br>\n";
}

?>
              </td>
              <td width="2%">&nbsp;</td>
              <td width="86%" valign="top" align="center">
              <table class="doc_main" align="center">

                  <tr>
                    <td class="doc_main" align="center">
