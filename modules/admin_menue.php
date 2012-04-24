<?php
/*****************************************************************************/
/* admin_menue.php                                                               */
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
/*Dieses Modul dient als Vorlage zum Erstellen von eigenen Zusatzmodulen     */
/*f�r die Iw DB: Icewars geoscan and sitter database                         */
/*---------------------------------------------------------------------------*/
/*Diese Erweiterung der ursp�nglichen DB ist ein Gemeinschafftsprojekt von   */
/*IW-Spielern.                                                               */
/*Bei Problemen kannst du dich an das eigens daf�r eingerichtete             */
/*Entwiklerforum wenden:                                                     */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde. Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	die("Hacking attempt...!!");
	exit;
}

// -> lädt die Datei config.php und führt diese aus. In der config.php sind wichtige Variablen definiert.
if (!@include("./config/config.php")) {
  echo "<div class='system_error'>Error:<br><b>Cannot load Master-Config!</b></div>";
	exit;
}

//  -> Nur der Admin darf dieses Module benutzen, denn meistens weiß er was er tut 
if ( $user_status != "admin" )
  die('Hacking attempt...');

// -> lädt die statischen Modulbeschreibungen
  @include ('moduldesc.php');
  
?>  
<script type="text/javascript" language="javascript">
var confirmMsg  = 'Menütitel wirklich löschen?';
function confirmLink(theLink, theSqlQuery)
{
    if (confirmMsg == '') {
        return true;
    }

    var is_confirmed = confirm(confirmMsg);
    if (is_confirmed) {
        theLink.href += '&is_js_confirmed=1';
    }

    return is_confirmed;
}
</script>
<?php


/* #############################################################################

   Menütitel löschen

   ###########################################################################*/

if (!empty($_GET['delid'])) {
   $sql = "DELETE FROM " . $db_tb_menu . " WHERE id='" . $_GET['delid'] . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}


/* #############################################################################

   Neuer Menüeintrag

   ###########################################################################*/
if (!empty($_POST['new'])) {
    unset($fehler);
    // nach höchstem Menüeintrag suchen
    $sql = "SELECT * FROM " . $db_tb_menu . " ORDER BY menu DESC, submenu DESC LIMIT 0, 1";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    $hmenu =$row['menu']+1;
    mysql_free_result($result);




    $title  = trim($_POST['new_title']);
    if (empty($title)) $fehler = "Menütext vergessen!";
    if (empty($fehler)) {
       $sql = "INSERT INTO " . $db_tb_menu . "
              Set menu      ='" . $hmenu . "',
                  submenu   ='0',
                  active    ='" . $_POST['new_active'] . "',
                  title     ='" . $title . "',
                  status    ='" . $_POST['new_status'] . "',
                  action    ='',
                  extlink   ='n',
                  sittertyp ='" . $_POST['new_sittertyp'] . "'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    } else {
        echo $fehler;
    }
}

/* #############################################################################

   Menüeinträge bearbeiten

   ###########################################################################*/
if (!empty($_POST['edit'])) {
    unset($fehler);
    if (empty($_POST['edit_action'])) $_POST['edit_action'] = "";
    if (empty($_POST['edit_extlink'])) $_POST['edit_extlink'] = "";
    $title  = trim($_POST['edit_title']);
    $action = trim($_POST['edit_action']);
    if (empty($title)) $fehler = "Menütext vergessen!";
    if (empty($fehler)) {
       $sql = "UPDATE " . $db_tb_menu . "
              Set active='" . $_POST['edit_active'] . "',
                  title='" . $title . "',
                  status='" . $_POST['edit_status'] . "',
                  action='" . $action . "',
                  extlink='" . $_POST['edit_extlink'] . "',
                  sittertyp='" . $_POST['edit_sittertyp'] . "'
              WHERE id = '".$_GET['eid']."'";
       $result = $db->db_query($sql)
         or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    } else {
        echo $fehler;
    }
}



/* #############################################################################

   Menüeinträge umsortieren

   ###########################################################################*/
if (!empty($_GET['sort']) AND !empty($_GET['id'])) {
  //  -> Sortierwunsch UP
  if ($_GET['sort']=="up"){
    $sql = "SELECT * FROM " . $db_tb_menu . " ORDER BY menu DESC, submenu DESC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $merk=0;
    while($row = $db->db_fetch_array($result)) {
      if ($merk==2){
        $row3 = $row;
        $id3  = $row['id'];
        $merk=3;
      }
      if ($merk==1){
        $row2 = $row;
        $id2  = $row['id'];
        $merk=2;
      }
      if ($row['id']== $_GET['id']){
        $row1 = $row;
        $id1  = $row['id'];
        $merk=1;
      }
    }
    //  -> Wenn beide Menüzeilen Module sind einfach die Variablen menu und submenu tauschen
    if (($row1['submenu'] != "0") AND ($row2['submenu'] != "0")){
      $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row1['menu'] . "',submenu='" . $row1['submenu'] . "'
              WHERE id = '".$id2."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row2['menu'] . "',submenu='" . $row2['submenu'] . "'
              WHERE id = '".$id1."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    //  -> Wenn beide Menüzeilen Titel sind einfach die Variablen menu und submenu tauschen
    if (($row1['submenu']== 0) AND ($row2['submenu']== 0)){
      $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row1['menu'] . "',submenu='" . $row1['submenu'] . "'
              WHERE id = '".$id2."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row2['menu'] . "',submenu='" . $row2['submenu'] . "'
              WHERE id = '".$id1."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    //  -> Wenn ein Modul nach oben geschoben wird und sich darüber ein Titel befindet.
    // die Nummeration des Titels bleibt unverändert, das Modul bekommt den Menüwert der weiter darüberliegenden Zeile und den Submenüwert plus eins
    if (($row1['submenu']!= 0) AND ($row2['submenu']== 0)){
    // gibt es eine weiter darüberliegende Zeile?
      if (!empty($row3)){
        $subm3 = $row3['submenu']+1;
        $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row3['menu'] . "',submenu='" . $subm3 . "'
              WHERE id = '".$id1."'";
        $result = $db->db_query($sql)
          or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      }else{
        $menu3 = $row2['menu']-1;
        $subm3 = 1;
        $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $menu3 . "',submenu='" . $subm3 . "'
              WHERE id = '".$id1."'";
        $result = $db->db_query($sql)
           or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      }
    }
    //  -> Wenn ein Titel nach oben geschoben wird und sich darüber ein Modul befindet.
    // die Nummeration des Titels bleibt unverändert, das Modul und alle Module unter dem Titel müssen neu sortiert werden.
    if (($row1['submenu']== 0) AND ($row2['submenu']!= 0)){
    // Auslesen aller darunter liegenden Mudule und Zwischenspeichern in einem Array
      $sql = "SELECT * FROM " . $db_tb_menu . " where menu = '" . $row1['menu'] . "' ORDER BY submenu ASC";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      $i=0;
      while($row = $db->db_fetch_array($result)) {
        //nur merken wenn submenu ungleich 0
        if ($row['submenu']!= 0){
          $i++;
          $zeile[$i]=$row['id'];
        }
      }
      //das darüberliegen Modul als erste Zeile eintragen
      $sql = "UPDATE " . $db_tb_menu . "
             Set menu='" . $row1['menu'] . "',submenu='1'
             WHERE id = '".$id2."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      // danach die gemerkten Module wenn welche vorhanden sind
      if (!empty($zeile)){
        $i=1;
        foreach ($zeile as $value){
          $i++;
          $sql = "UPDATE " . $db_tb_menu . "
                 Set submenu='".$i."'
                 WHERE id = '".$value."'";
          $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }
      }
    }
  }
  //  -> Sortierwunsch down
  if ($_GET['sort']=="down"){
    $sql = "SELECT * FROM " . $db_tb_menu . " ORDER BY menu ASC, submenu ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $merk=0;
    while($row = $db->db_fetch_array($result)) {
      if ($merk==2){
        $row3 = $row;
        $id3  = $row['id'];
        $merk=3;
      }
      if ($merk==1){
        $row2 = $row;
        $id2  = $row['id'];
        $merk=2;
      }
      if ($row['id']== $_GET['id']){
        $row1 = $row;
        $id1  = $row['id'];
        $merk=1;
      }
    }
    //  -> Wenn beide Menüzeilen Module sind einfach die Variablen menu und submenu tauschen
    if (($row1['submenu'] != "0") AND ($row2['submenu'] != "0")){
      $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row1['menu'] . "',submenu='" . $row1['submenu'] . "'
              WHERE id = '".$id2."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row2['menu'] . "',submenu='" . $row2['submenu'] . "'
              WHERE id = '".$id1."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    //  -> Wenn beide Menüzeilen Titel sind einfach die Variablen menu und submenu tauschen
    if (($row1['submenu']== 0) AND ($row2['submenu']== 0)){
      $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row1['menu'] . "',submenu='" . $row1['submenu'] . "'
              WHERE id = '".$id2."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row2['menu'] . "',submenu='" . $row2['submenu'] . "'
              WHERE id = '".$id1."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    }
    //  -> Wenn ein Modul nach unten geschoben wird und sich darunter ein Titel befindet.
    if (($row1['submenu']!= 0) AND ($row2['submenu']== 0)){
    // Auslesen aller darunter liegenden Module und Zwischenspeichern in einem Array
      $sql = "SELECT * FROM " . $db_tb_menu . " where menu = '" . $row2['menu'] . "' ORDER BY submenu ASC";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      $i=0;
      while($row = $db->db_fetch_array($result)) {
        //nur merken wenn submenu ungleich 0
        if ($row['submenu']!= 0){
          $i++;
          $zeile[$i]=$row['id'];
        }
      }
      //das darüberliegen Modul als erste Zeile eintragen
      $sql = "UPDATE " . $db_tb_menu . "
             Set menu='" . $row2['menu'] . "',submenu='1'
             WHERE id = '".$id1."'";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      // danach die gemerkten Module wenn welche vorhanden sind
      if (!empty($zeile)){
        $i=1;
        foreach ($zeile as $value){
          $i++;
          $sql = "UPDATE " . $db_tb_menu . "
                 Set submenu='".$i."'
                 WHERE id = '".$value."'";
          $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        }
      }
    }
    //  -> Wenn ein Titel nach unten geschoben wird und sich darunter ein Modul befindet.
    if (($row1['submenu']== 0) AND ($row2['submenu']!= 0)){
    // feststellen ob es über dem Titel noch ein weites Menü gibt
      $sql = "SELECT * FROM " . $db_tb_menu . " ORDER BY menu DESC, submenu DESC";
      $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      $merk=0;
    while($row = $db->db_fetch_array($result)) {
      if ($merk==1){
        $row4 = $row;
        $id4  = $row['id'];
        $merk=2;
      }
      if ($row['id']== $_GET['id']){
        $merk=1;
      }
    }
    // gibt es eine weiter darüberliegende Zeile?
      if (!empty($row4)){
        $subm4 = $row4['submenu']+1;
        $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $row4['menu'] . "',submenu='" . $subm4 . "'
              WHERE id = '".$id2."'";
        $result = $db->db_query($sql)
          or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      }else{
        $menu3 = $row1['menu']-1;
        $subm3 = 1;
        $sql = "UPDATE " . $db_tb_menu . "
              Set menu='" . $menu3 . "',submenu='" . $subm3 . "'
              WHERE id = '".$id2."'";
        $result = $db->db_query($sql)
           or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      }
    }
  }
}

echo "<br>\n";

  $sql = "SELECT * FROM " . $db_tb_menu . " ORDER BY menu ASC, submenu ASC LIMIT 0, 1";
  $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
  $row = $db->db_fetch_array($result);
  $hid = $row['id'];
  mysql_free_result($result);

  $sql = "SELECT * FROM " . $db_tb_menu . " ORDER BY menu DESC, submenu DESC LIMIT 0, 1";
  $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
  $row = $db->db_fetch_array($result);
  $lid =$row['id'];
  mysql_free_result($result);

  // -> Hier auslesen der Menübereiche.
  $sql = "SELECT * FROM " . $db_tb_menu . " ORDER BY menu ASC, submenu ASC";
  $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

  echo "<table width=\"90%\" class=\"bordercolor\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" >";

  while( $row = $db->db_fetch_array($result)) {
    if ($row['submenu'] == 0){
      $cl="windowbg2";
    }else{
      $cl="windowbg1";
    }
    $grart  = "plus";
    $grtext = "Bearbeiten";
    if ((!empty($_GET['eid'])) && ($row['id'] == $_GET['eid'])){
      $grart = "minus";
      $grtext = "";
    }
    echo "<tr>";
    echo "<td width=\"50%\" class=\"".$cl."\" >&nbsp;".$row['title']."&nbsp;</td>";
    echo "<td width=\"50%\" class=\"".$cl."\" align='right'>";
    if ($row['submenu'] == 0){
      echo "<a href=\"index.php?action=admin_menue&sid=".$sid."&delid=".$row['id']."\" target=\"_self\"><img src=\"./bilder/delete.gif\" align=\"absmiddle\" border=\"0\" title=\"Men�titel L�schen\" alt=\"Men�titel L�schen\" onclick=\"return confirmLink(this, '" . $row['id'] . "')\"></a>";
    }
    if ($row['id'] == $hid){
      echo "<img src=\"./bilder/sort_up2.gif\" align=\"absmiddle\" border=\"0\" title=\"Sortieren: Up\" alt=\"Sortieren: Up\">";
    }else{
      echo "<a href=\"index.php?action=admin_menue&sid=".$sid."&sort=up&id=".$row['id']."\" target=\"_self\"><img src=\"./bilder/sort_up.gif\" align=\"absmiddle\" border=\"0\" title=\"Sortieren: Up\" alt=\"Sortieren: Up\"></a>";
    }
    if ($row['id'] == $lid){
      echo "<img src=\"./bilder/sort_down2.gif\" align=\"absmiddle\" border=\"0\" title=\"Sortieren: Down\" alt=\"Sortieren: Down\">";
    }else{
      echo "<a href=\"index.php?action=admin_menue&sid=".$sid."&sort=down&id=".$row['id']."\" target=\"_self\"><img src=\"./bilder/sort_down.gif\" align=\"absmiddle\" border=\"0\" title=\"Sortieren: Down\" alt=\"Sortieren: Down\"></a>";
    }
    if (!empty($grtext)){
      echo "<a href=\"index.php?action=admin_menue&sid=".$sid."&eid=".$row['id']."\" target=\"_self\"><img src=\"./bilder/edit_" . $grart . ".gif\" align=\"absmiddle\" border=\"0\" title=\"" . $grtext . "\" alt=\"" . $grtext . "\"></a>";
    }else{
      echo "<a href=\"index.php?action=admin_menue&sid=".$sid."\" target=\"_self\"><img src=\"./bilder/edit_" . $grart . ".gif\" align=\"absmiddle\" border=\"0\" title=\"" . $grtext . "\" alt=\"" . $grtext . "\"></a>";
    }
    echo "</td>";
    echo "</tr>";


    if ($grart == "minus") {

    echo "<tr><td colspan=2 width=\"100%\" class=\"".$cl."\" align='center'><table width=\"90%\" class=\"bordercolor\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\" >";

    echo "<tr><form name=\"form\" action=\"index.php?action=admin_menue&sid=".$sid."&eid=".$row['id']."\" method=\"post\">";
    echo "<td width=\"50%\" class=\"".$cl."\" align='left'>Menütext:</td>";
    echo "<td width=\"50%\" class=\"".$cl."\" align='left'><input name=\"edit_title\" type=\"text\" size=50 maxlength=\"100\" value=\"".$row['title']."\"></td>";
    echo "</tr><tr>";
    if ($row['active'] == "0") {
      $checkno  =" checked";
      $checkyes ="";
    }else{
      $checkyes =" checked";
      $checkno  ="";
    }
    echo "<td width=\"50%\" class=\"".$cl."\" align='left'>Menütext anzeigen:</td>";
    echo "<td width=\"50%\" class=\"".$cl."\" align='left'><input type=\"radio\" name=\"edit_active\" value=\"0\"". $checkno."> - Nein&nbsp;<input type=\"radio\" name=\"edit_active\" value=\"1\"". $checkyes."> - Ja</td>";
    echo "</tr><tr>";
    if ($row['status'] == "") {
      $checkalle  =" checked";
      $checkhc    ="";
      $checkadmin ="";
    }
    if ($row['status'] == "HC") {
      $checkalle  ="";
      $checkhc    =" checked";
      $checkadmin ="";
    }
    if ($row['status'] == "admin") {
      $checkalle  ="";
      $checkhc    ="";
      $checkadmin =" checked";
    }
    echo "<td width=\"50%\" class=\"".$cl."\" align='left'>Wer dieses Menü sehen darf:</td>";
    echo "<td width=\"50%\" class=\"".$cl."\" align='left'><input type=\"radio\" name=\"edit_status\" value=\"\"". $checkalle."> - Alle&nbsp;<input type=\"radio\" name=\"edit_status\" value=\"HC\"". $checkhc."> - HC&nbsp;<input type=\"radio\" name=\"edit_status\" value=\"admin\"". $checkadmin."> - Admin</td>";
    echo "</tr><tr>";
    $st[0] ="";
    $st[1] ="";
    $st[2] ="";
    $st[3] ="";
    $st[$row['sittertyp']] =" selected";
    echo "<td width=\"50%\" class=\"".$cl."\" align='left'>Anzeigen bei Sittertyp:</td>";
    echo "<td width=\"50%\" class=\"".$cl."\" align='left'><select name=\"edit_sittertyp\">";
    echo "<option value=\"2\"".$st[2].">Sitterbereich deaktiviert</option>";
    echo "<option value=\"0\"".$st[0].">kann Sitteraufträge erstellen, darf keine anderen sitten</option>";
    echo "<option value=\"3\"".$st[3].">darf andere sitten, darf keine Sitteraufträge erstellen</option>";
    echo "<option value=\"1\"".$st[1].">darf andere sitten, darf Sitteraufträge erstellen</option>";
    echo "</select></td>";
    if ($row['submenu'] != 0){
      echo "</tr><tr>";
      if ($row['extlink'] == "n") {
        $checkno  =" checked";
        $checkyes ="";
      }else{
        $checkyes =" checked";
        $checkno  ="";
      }
      echo "<td width=\"50%\" class=\"".$cl."\" align='left'>Externer Link:</td>";
      echo "<td width=\"50%\" class=\"".$cl."\" align='left'><input type=\"radio\" name=\"edit_extlink\" value=\"n\"". $checkno."> - Nein&nbsp;<input type=\"radio\" name=\"edit_extlink\" value=\"y\"". $checkyes."> - Ja</td>";
      echo "</tr><tr>";
      echo "<td width=\"50%\" class=\"".$cl."\" align='left'>Link:</td>";
      echo "<td width=\"50%\" class=\"".$cl."\" align='left'><input name=\"edit_action\" type=\"text\" size=50 maxlength=\"200\" value=\"".$row['action']."\"></td>";
    }
    echo "</tr><tr>";
    echo "<td colspan=2 width=\"100%\" class=\"".$cl."\" align='center'><input type=\"submit\" name=\"edit\" value=\"Speichern\"></td>";

    echo "</tr></table></td>";

    echo "</form></tr>";
    }

  }
echo "</table><br><br>";



echo "<table width=\"90%\" class=\"bordercolor\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\" >";
    echo "<tr><form name=\"form2\" action=\"index.php?action=admin_menue&sid=".$sid."\" method=\"post\">";
    echo "<td colspan=2 width=\"100%\" class=\"windowbg2\" align='center'>Neuer Menütitel</td>";
    echo "</tr><tr>";
    echo "<td width=\"50%\" class=\"windowbg1\" align='left'>Menütext:</td>";
    echo "<td width=\"50%\" class=\"windowbg1\" align='left'><input name=\"new_title\" type=\"text\" size=50 maxlength=\"100\" value=\"Menütext\"></td>";
    echo "</tr><tr>";
    echo "<td width=\"50%\" class=\"windowbg1\" align='left'>Menütext anzeigen:</td>";
    echo "<td width=\"50%\" class=\"windowbg1\" align='left'><input type=\"radio\" name=\"new_active\" value=\"0\"> - Nein&nbsp;<input type=\"radio\" name=\"new_active\" value=\"1\" checked> - Ja</td>";
    echo "</tr><tr>";
    echo "<td width=\"50%\" class=\"windowbg1\" align='left'>Wer dieses Menü sehen darf:</td>";
    echo "<td width=\"50%\" class=\"windowbg1\" align='left'><input type=\"radio\" name=\"new_status\" value=\"\" checked> - Alle&nbsp;<input type=\"radio\" name=\"new_status\" value=\"hc\"> - HC&nbsp;<input type=\"radio\" name=\"new_status\" value=\"admin\"> - Admin</td>";
    echo "</tr><tr>";
    echo "<td width=\"50%\" class=\"windowbg1\" align='left'>Anzeigen bei Sittertyp:</td>";
    echo "<td width=\"50%\" class=\"windowbg1\" align='left'><select name=\"new_sittertyp\">";
    echo "<option value=\"0\" checked>kann Sitteraufträge erstellen, darf keine anderen sitten</option>";
    echo "<option value=\"1\">darf andere sitten, darf Sitteraufträge erstellen</option>";
    echo "<option value=\"2\">Sitterbereich deaktiviert</option>";
    echo "<option value=\"3\">darf andere sitten, darf keine Sitteraufträge erstellen</option>";
    echo "</select></td>";
    echo "</tr><tr>";
    echo "<td colspan=2 width=\"100%\" class=\"windowbg2\" align='center'><input type=\"submit\" name=\"new\" value=\"Speichern\"></td>";
    echo "</form></tr>";
echo "</table>";

// Sucht nach Modulen und sieht nach, ob eine zugehörige config.cfg.php existiert
// Wenn ein Modul gefunden wird, versucht es dieses zu installieren.
$uninstallecho = "";
$fp6=opendir("./modules");
echo "<br><br> \n";
echo "<br><div width=\"90%\" class=\"windowbg2\" style=\"padding:2px; width:90%; border-width:1px; border-style: solid; boder-color:black\">Installierte Module:</div><br>";
while ($datei1 = readdir($fp6)) {
  if (strstr($datei1, "m_")) {
    $modulname = str_replace(".php", "", $datei1);
    if(file_exists("./config/".$modulname.".cfg.php")){
		  // Read configuration header
		  @include("./config/".$modulname.".cfg.php");

			echo "<form method='POST' action='index.php?action=".$modulname."&was=uninstall&sid=".$sid."'>\n";
			echo " <table class=\"bordercolor\" width=\"90%\" cellpadding=\"4\" cellspacing=\"1\">\n";
			echo "  <tr>\n";
      if (isset($modulary[$modulname]["name"])) {
 		      echo "   <td align=\"left\" valign=\"center\" class=\"titlebg\"><strong>\n" . $modulary[$modulname]["titel"] . "</strong>&nbsp;<i>(".$modulname.")</i></td>\n"; 
      } else {
			echo "   <td align=\"left\" valign=\"center\" class=\"titlebg\"><strong>\n" . $modulname . "</strong></td>\n";
      }
			echo "   <td width=\"140\" rowspan=\"2\" class=\"windowbg1\" align=\"center\">";
			echo "<input type='submit' value='deinstallieren' name='uninstall' class='submit'>";
			echo "</td>\n";
			echo "  </tr>\n";
			echo "  <tr>\n";
			echo "   <td valign=\"top\" class=\"windowbg1\">" . ${"desc_".$modulname} . "</td>\n";
			echo "  </tr>\n";
			echo " </table>";
                  echo "</form><br>\n";
    }else{

      if (isset($modulary[$modulname]["name"])) {

        $uninstallecho = $uninstallecho . "<form method='POST' action='index.php?action=".$modulname."&was=install&sid=".$sid."'>";
		$uninstallecho = $uninstallecho . " <table class=\"bordercolor\" width=\"90%\" cellpadding=\"4\" cellspacing=\"1\">\n";
		$uninstallecho = $uninstallecho . "  <tr>\n";
		$uninstallecho = $uninstallecho . "   <td align=\"left\" valign=\"center\" class=\"titlebg\"><strong>\n" . $modulary[$modulname]["titel"] . "</strong>&nbsp;<i>(".$modulname.")</i></td>\n";
		$uninstallecho = $uninstallecho . "   <td width=\"140\" rowspan=\"2\" class=\"windowbg1\" align=\"center\">";
            $uninstallecho = $uninstallecho . "<input type='submit' value='Installieren' name='install' class='submit'>";
		$uninstallecho = $uninstallecho . "</td>\n";
		$uninstallecho = $uninstallecho . "  </tr>\n";
		$uninstallecho = $uninstallecho . "  <tr>\n";
		$uninstallecho = $uninstallecho . "   <td valign=\"top\" class=\"windowbg1\">" . $modulary[$modulname]["desc"] . "</td>\n";
		$uninstallecho = $uninstallecho . "  </tr>\n";
		$uninstallecho = $uninstallecho . " </table>";
            $uninstallecho = $uninstallecho . "</form><br>\n";

      } else {

     $uninstallecho = $uninstallecho . "<hr width='90%'>";
     $uninstallecho = $uninstallecho . "<big><b>".$modulname."</b></big><br><br>";
     $uninstallecho = $uninstallecho . "Willst du es jetzt installieren?<br><br>";
     $uninstallecho = $uninstallecho . "<form method='POST' action='index.php?action=".$modulname."&was=install&sid=".$sid."'>";
     $uninstallecho = $uninstallecho . "<input type='submit' value='Na klar!' name='install' class='submit'>";
     $uninstallecho = $uninstallecho . "</form><br>";
     $uninstallecho = $uninstallecho . "<hr width='90%'><br>";

      }
    }
  }
}

if ($uninstallecho <> "") {

      echo "<center>";

echo "<br><div width=\"90%\" class=\"windowbg2\" style=\"padding:2px; width:90%; border-width:1px; border-style: solid; boder-color:black\">Nicht installierte Module:</div><br>";

      echo "Es wurde mindestens ein Modul gefunden, das noch nicht installiert wurde:<br><br>";

      echo  $uninstallecho;

      echo "Solltest du eines dieser Module nicht installieren wollen,<br>";
      echo "oder gar nicht wissen wie es auf deinen Server kommt,<br>";
      echo "rate ich dir es aus den Ordner \"modules\" zu löschen.<br><br>";

      echo "</center> \n";

}

global $db_prefix;

//das Sperren der DB
$sperre = GetVar('sperre');
if ($sperre == 'auf') {
$sqlIA = "UPDATE " . $db_prefix . "params SET value = '0' ";
    $resultIA = $db->db_query($sqlIA)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlIA);
}
if ($sperre == 'ab') {
$grund = GetVar('grund');
$sqlIA = "UPDATE " . $db_prefix . "params SET value = '1', text = '".$grund."' ";
    $resultIA = $db->db_query($sqlIA)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlIA);
}




$sqlIA = "SELECT text,value FROM " . $db_prefix . "params WHERE name = 'gesperrt' ";
    $resultIA = $db->db_query($sqlIA)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlIA);
    $rowIA = $db->db_fetch_array($resultIA);
$grund = $rowIA['text'];
$isornot = $rowIA['value'];

if ($isornot != 1) {
?>
<form name="form" action="index.php?action=admin_menue&sid=<?php echo $sid;?>&sperre=ab" method="post">
<div class='doc_green'>Datenbank ist aktiv</div><br>
<input name="grund" type="text" size="50" value=""><br>
<input name="submit" type="submit" value="Sperre Datenbank">
</form>
<?php
} else {
?>
<form name="form" action="index.php?action=admin_menue&sid=<?php echo $sid;?>&sperre=auf" method="post">
<div class='doc_red'>Datenbank ist inaktiv</div><br>
Grund: <?php echo $grund;?><br>
<input name="submit" type="submit" value="Entriegele Datenbank">
</form>
<?php
}


?>
