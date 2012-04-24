<?php
/*****************************************************************************/
/* admin_allianzstatus.php                                                   */
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

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	exit("Hacking attempt...!!");
}

if ( $user_status != "admin" && $user_status != "hc" )
	die('Hacking attempt...');

doc_title("Admin Einstellungen");
echo "<br>\n";

/*****************************************************************************/
/* Fade-in Teil                                                              */
/*****************************************************************************/

$bs = GetVar('BS');
if ( !empty($bs) ) {

$sound_standart = GetVar('sound_standart');
$sound_global = GetVar('sound_global');
$sound_login = GetVar('sound_login');
if (empty($sound_login)) $sound_login = 0;

$sqlP = "UPDATE ".$db_prefix."params SET value = '".$sound_standart."' WHERE name = 'sound_standart'";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
$sqlP = "UPDATE ".$db_prefix."params SET value = '".$sound_login."' WHERE name = 'sound_login'";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
$sqlP = "UPDATE ".$db_prefix."params SET value = '".$sound_global."' WHERE name = 'sound_global'";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
$sqlM = "ALTER TABLE ".$db_prefix."menu CHANGE `sound` `sound` INT( 1 ) DEFAULT '".$sound_standart."'";
  $resultM = $db->db_query($sqlM)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlM);
$sqlM = "UPDATE ".$db_prefix."menu SET sound = '0'";
  $resultM = $db->db_query($sqlM)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlM);

$sound_menu = GetVar('sound_menu');
foreach ($sound_menu as $menuid) {
  $sqlM = "UPDATE ".$db_prefix."menu SET sound = 1 WHERE id = ".$menuid.";";
  $resultM = $db->db_query($sqlM)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlM);
}

}

global $db_prefix, $sid;


$menu_sel = array();
$menu_not = array();
$count = 0;

//auslesen aller Menüpunkte, um eine Liste zu erstellen, wo der Sound abgespielt werden soll
$sqlM = "SELECT action,sound,id FROM ".$db_prefix."menu ";
  $resultM = $db->db_query($sqlM)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlM);
  while ($rowM = $db->db_fetch_array($resultM)) {
    $count++;
    
    if ( !empty($rowM['action']) ) {

      //action auslesen
        $action = $rowM['action'];

      $count++;
 
      //ist action nicht leer, entschieden wo es hin soll:
      if ( !empty($action) AND empty($rowM['sound']) ) $menu_not[$action]['name'] = $action;
      if ( !empty($action) AND empty($rowM['sound']) ) $menu_not[$action]['id'] = $rowM['id']; 
      if ( !empty($action) AND !empty($rowM['sound']) ) $menu_sel[$action]['name'] = $action;  
      if ( !empty($action) AND !empty($rowM['sound']) ) $menu_sel[$action]['id'] = $rowM['id']; 

    }
  }


//auslesen des standards
$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'sound_standart' ";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
 $rowP = $db->db_fetch_array($resultP);

if ( !empty($rowP['value']) ) {

 $sel_sel = 'checked="checked"';
 $sel_not = '';

} else {

 $sel_not = 'checked="checked"';
 $sel_sel = '';

}

$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'sound_login' ";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
 $rowP = $db->db_fetch_array($resultP);  

$sel_login = '';
if ( !empty($rowP['value']) ) $sel_login = 'checked="checked"';


//auslesen was das globale maximum ist
$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'sound_global' ";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
 $rowP = $db->db_fetch_array($resultP);  

$sel0 = '';
$sel1 = '';
$sel2 = '';
$sel3 = '';
$sel4 = '';
$sel5 = '';


switch ($rowP['value']) {
 case '0':
   $sel_val = 'Ausgeschaltet';
   $sel0 = 'selected="selected"';
   break;
 case '1':
   $sel_val = 'Fenster';
   $sel1 = 'selected="selected"';
   break;   
 case '2':
   $sel_val = 'Fenster mit Sound';
   $sel2 = 'selected="selected"';
   break;   
 case '3':
   $sel_val = 'Fenster (blinkend)';
   $sel3 = 'selected="selected"';
   break;   
 case '4':
   $sel_val = 'Fenster (blinkend) mit Sound';
   $sel4 = 'selected="selected"';
   break;   
 default:
   $sel_val = 'Ausgeschaltet';
   $sel0 = 'selected="selected"';
   break;   
}

?>

<br>
<form method="POST" action="index.php?action=admin&uaction=einstellungen&send=sound&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
 <tr>
  <td colspan="2" class="titlebg">
   <b>Sitterbenachrichtigung:</b>
  </td>
 </tr>
 <tr>
  <td class="windowbg2" style="width:40%;">
   Benachrichtigung möglich:<br>
   <i>Hier wird das Fenster eingeblendet</i>
  </td>
  <td class="windowbg1">
    <select name="sound_menu[]" size="10" multiple="multiple">
      <?php foreach ($menu_sel as $menu): ?>
        <option selected="selected" value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option> 
      <?php endforeach; ?>
      <?php foreach ($menu_not as $menu): ?>
        <option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option> 
      <?php endforeach; ?>
    </select>
  </td>
 </tr>
 <tr>
  <td class="windowbg2" style="width:40%;">
   Sound beim Login:<br>
   <i>Soll das Skript auch beim Login geladen werden?</i>
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="sound_login" <?php echo $sel_login;?> value="1">Yes
  </td>
 </tr>
 <tr>
  <td class="windowbg2" style="width:40%;">
   Benachrichtigung maximal:<br>
   <i>Welche Erinnerungsart können die User maximal anwählen:</i>
  </td>
  <td class="windowbg1">
    <select value="<?php echo $sel_val;?>" name="sound_global" size="1">
        <option <?php echo $sel0;?> value="0">Ausgeschaltet</option> 
        <option <?php echo $sel1;?> value="1">Fenster</option> 
        <option <?php echo $sel2;?> value="2">Fenster mit Sound</option> 
        <option <?php echo $sel3;?> value="3">Fenster (blinkend)</option> 
        <option <?php echo $sel4;?> value="4">Fenster (blinkend) mit Sound</option> 
    </select>
  </td>
 </tr>
 <tr>
  <td class="windowbg2" style="width:40%;">
   Standardeinstellung:<br>
   <i>Welche Einstellung sollen neu installierte Module haben?</i>
  </td>
  <td class="windowbg1">
   <input type="radio" name="sound_standart" <?php echo $sel_sel;?> value="1">Sound eingeschaltet
   <input type="radio" name="sound_standart" <?php echo $sel_not;?> value="0">Sound ausgeschaltet
  </td>
 </tr>
 <tr>
  <td colspan="2" class="titlebg" align="center">
   <input type="submit" value="Fadeineinstellungen ändern" name="BS">
  </td>
 </tr>
</table>
</form>
<br>

<?php

$be = GetVar('BE');
if ( !empty($be) ) {


$bericht_fuer_rang = GetVar('bericht_fuer_rang');
$bericht_fuer_sitter = GetVar('bericht_fuer_sitter');

$sqlP = "UPDATE ".$db_prefix."params SET value = '".$bericht_fuer_rang."' WHERE name = 'bericht_fuer_rang'";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
$sqlP = "UPDATE ".$db_prefix."params SET value = '".$bericht_fuer_sitter."' WHERE name = 'bericht_fuer_sitter'";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);

}

//auslesen rang
$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'bericht_fuer_rang' ";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
 $rowP = $db->db_fetch_array($resultP);

if ( !empty($rowP['value']) ) {

switch ($rowP['value']) {
 case 'all':
   $sel_val = 'alle';
   $sel0 = 'selected="selected"';
   $sel1a = '';
   $sel1b = '';
   $sel2 = '';
   break;
 case 'mv':
    $sel_val = 'hc';  
   $sel1a = 'selected="selected"';
   $sel1b = '';
   $sel0 = '';
   $sel2 = '';
   break;   
 case 'hc':
    $sel_val = 'hc';  
   $sel1a = '';
   $sel1b = 'selected="selected"';
   $sel0 = '';
   $sel2 = '';
   break;   
 case 'admin':
   $sel_val = 'admin';
   $sel2 = 'selected="selected"';
   $sel1a = '';
   $sel1b = '';
   $sel0 = '';
   break;    
 default:
   $sel_val = 'admin';
   $sel2 = '"';
   $sel1a = '';
   $sel1b = '';
   $sel0 = 'selected="selected"';
   break; 
}


} else {

   $sel_val = 'admin';
   $sel2 = '"';
   $sel1a = '';
   $sel1b = '';
   $sel0 = 'selected="selected"';

}

//auslesen sitter
$sqlP = "SELECT value FROM ".$db_prefix."params WHERE name = 'bericht_fuer_sitter' ";
  $resultP = $db->db_query($sqlP)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlP);
 $rowP = $db->db_fetch_array($resultP);

$sitval0 = '';
$sitval1 = '';
$sitval2 = '';
$sitval3 = '';

if ( !empty($rowP['value']) ) {

   ${'sitval'.$rowP['value']} = 'selected="selected"';

} else {

   $sitval0 = 'selected="selected"';

}

?>

<form method="POST" action="index.php?action=admin&uaction=einstellungen&send=bericht&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 80%;">
 <tr>
  <td colspan="2" class="titlebg">
   <b>'Bericht einfügen für':</b>
  </td>
 </tr>
 <tr>
  <td class="windowbg2" style="width:40%;">
   Bericht einfügen für:<br>
   <i>Wer darf das Fenster 'Bericht einfügen für' nutzen?</i>
  </td>
  <td class="windowbg1">
    Rang:<br>
    <select value="<?php echo $sel_val;?>" name="bericht_fuer_rang" size="1">
        <option <?php echo $sel0;?> value="all">Alle</option> 
        <option <?php echo $sel1a;?> value="mv">MV / HC und Admin</option> 
        <option <?php echo $sel1b;?> value="hc">HC und Admin</option> 
        <option <?php echo $sel2;?> value="admin">Admin</option> 
    </select>
     <br><br>
    Sittertyp:<br>
    <select value="<?php echo $sitter_val;?>" name="bericht_fuer_sitter" size="1">
    <?php
    echo "<option $sitval2 value=\"2\"".$st[2].">Sitterbereich deaktiviert</option>";
    echo "<option $sitval0 value=\"0\"".$st[0].">kann Sitteraufträge erstellen, darf keine anderen sitten</option>";
    echo "<option $sitval3 value=\"3\"".$st[3].">darf andere sitten, darf keine Sitteraufträge erstellen</option>";
    echo "<option $sitval1 value=\"1\"".$st[1].">darf andere sitten, darf Sitteraufträge erstellen</option>";
    ?>
    </select>
  </td>
 </tr>
 <tr>
  <td colspan="2" class="titlebg" align="center">
   <input type="submit" value="'Bericht einfügen für' ändern" name="BE">
  </td>
 </tr>
</table>
</form>


