<?php
/*****************************************************************************/
/* admin_gebaeude.php                                                        */
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
	die("Hacking attempt...!!"); 
	exit; 
}

if ( $user_status != "admin" ) {
	die('Hacking attempt...');
}

function dauer($zeit)
{
  global $DAYS, $HOURS, $MINUTES;
  
	$return['d'] = floor($zeit / $DAYS);
	$return['h'] = floor(($zeit - $return['d'] * $DAYS) / $HOURS);
	$return['m'] = ($zeit - $return['d'] * $DAYS - $return['h'] * $HOURS) / $MINUTES;
	return $return;
}

//******************************************************************************

echo "<div class='doc_title'>Admin Geb&auml;ude</div>\n";

$dateis = array();
$dateis[''] = "keins";

$handle = opendir('bilder/gebs/');
while (false !== ($datei = readdir($handle)))
{
	if (strpos($datei, ".jpg") !== FALSE) 
	{
		$id = str_replace(".jpg", "", $datei);
		$dateis[$id] = $id;
	}
}
closedir($handle);
// EF 25-Jul-2006
ksort($dateis);

$editgebaeude = getVar('editgebaeude');           

$sql = "SELECT * FROM " . $db_tb_gebaeude . " ORDER BY id ASC";
$result_gebaeude = $db->db_query($sql)
	or error(GENERAL_ERROR, 
           'Could not query config information.', '', 
           __FILE__, __LINE__, $sql);

while($row_gebaeude = $db->db_fetch_array($result_gebaeude)) {
	if ( ! empty($editgebaeude) )	{
    $geb_name  = getVar(($row_gebaeude['id'] . '_name'));
	$geb_name = htmlspecialchars_decode($geb_name);
    $geb_name = str_replace("&szlig;","ß",$geb_name);
	$geb_cat   = html_entity_decode(getVar(($row_gebaeude['id'] . '_category')));
	$geb_cat = htmlspecialchars_decode($geb_cat);
	$geb_idcat = getVar(($row_gebaeude['id'] . '_idcat'));
    $geb_inact = getVar(($row_gebaeude['id'] . '_inactive'));
    $geb_bild  = getVar(($row_gebaeude['id'] . '_bild'));
    $id_iw     = getVar(($row_gebaeude['id'] . '_id_iw'));
    
    $dauer_d   = getVar(($row_gebaeude['id'] . '_dauer_d'));
    $dauer_h   = getVar(($row_gebaeude['id'] . '_dauer_h'));
    $dauer_m   = getVar(($row_gebaeude['id'] . '_dauer_m'));

    $delete = getVar(($row_gebaeude['id'] . '_delete'));
        
		$dauer     = ($dauer_d * $DAYS) + ($dauer_h * $HOURS) + ($dauer_m * $MINUTES);

        if (empty($delete)) {
             
		$sql = "UPDATE " . $db_tb_gebaeude . 
           " SET name='" . $geb_name. 
           "', category='" . $geb_cat . 
           "', idcat='" . $geb_idcat . 
           "', inactive='" . $geb_inact . 
           "', dauer='" . $dauer .
           "', bild='" . $geb_bild .
		   "', id_iw='" . $id_iw .
           "' WHERE id = '" . $row_gebaeude['id'] . "'";

         } else {

		$sql = "DELETE FROM " . $db_tb_gebaeude . 
           " WHERE name='" . $geb_name. 
           "'AND category='" . $geb_cat . 
           "'AND idcat='" . $geb_idcat . 
           "'AND id = '" . $row_gebaeude['id'] . "'";
           echo "<div class='system_notification'>Geb&auml;ude $geb_name gel&ouml;scht</div>";

         }
           
		$result_gebaeudeedit = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	}

       	$lastid = $row_gebaeude['id'];
  
}

$lastid_name  = getVar((($lastid + 1) . '_name'));

if((!empty($lastid_name)) && (empty($editgebaeude))) {
  $geb_name  = getVar((($lastid + 1) . '_name'));
  $geb_cat   = getVar((($lastid + 1) . '_category'));
  $geb_idcat = getVar((($lastid + 1) . '_idcat'));
  $geb_inact = getVar((($lastid + 1) . '_inactive'));
  $geb_bild  = getVar((($lastid + 1) . '_bild'));
  $id_iw     = getVar((($lastid + 1) . '_id_iw'));

  $dauer_d   = getVar((($lastid + 1) . '_dauer_d'));
  $dauer_h   = getVar((($lastid + 1) . '_dauer_h'));
  $dauer_m   = getVar((($lastid + 1) . '_dauer_m'));
        
	$dauer     = ($dauer_d * $DAYS) + ($dauer_h * $HOURS) + ($dauer_m * $MINUTES);
	$sql = "INSERT INTO " . $db_tb_gebaeude . 
         " (name, category, idcat, inactive, dauer, bild, id_iw) " . 
         " VALUES ('" . $geb_name . 
         "', '" . $geb_cat . 
         "', '" . $geb_idcat . 
         "', '" . $geb_inact . 
         "', '" . $dauer . 
         "', '" . $geb_bild .
	  "', '" . $id_iw . "')";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
             
	$lastid++;
	
echo "<div class='system_notification'>Gebaeude $lastid_name hinzugef&uuml;gt.</div>";
}

if($editgebaeude) {
	echo "<div class='system_notification'>Geb&auml;ude aktualisiert.</div>";
}

echo "<br>\n";
echo "<form method=\"POST\" action=\"index.php?action=admin&uaction=gebaeude&amp;sid=" . 
     $sid . "\" enctype=\"multipart/form-data\">\n";
echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 90%;\">\n";
echo " <tr>\n";
echo "  <td class=\"windowbg2\" style=\"width:10%;\">Ausblenden?</td>\n";
echo "  <td class=\"windowbg2\" style=\"width:20%;\">Name</td>\n";
echo "  <td class=\"windowbg2\" style=\"width:15%;\">Kategorie</td>\n";
echo "  <td class=\"windowbg2\" style=\"width:10%;\">Reihenfolge</td>\n";
echo "  <td class=\"windowbg2\" style=\"width:15%;\">Baudauer</td>\n";
echo "  <td class=\"windowbg2\" style=\"width:10%;\">ID</td>\n";
echo "  <td class=\"windowbg2\" style=\"width:20%;\">Bild</td>\n";
echo " </tr>\n";
echo "\n";
echo " <tr>\n";
echo "  <td class=\"windowbg1\" align=\"center\">\n";
echo "   <input type=\"checkbox\" name=\"" . ($lastid + 1) . "_inactive\" value=\"1\">\n";
echo "  </td>\n";
echo "  <td class=\"windowbg1\">\n";
echo "   <input type=\"text\" name=\"" . ($lastid + 1) . "_name\" value=\"\" style=\"width: 200\">\n";
echo "  </td>\n";
echo "  <td class=\"windowbg1\">\n";
echo "   <input type=\"text\" name=\"" . ($lastid + 1) . "_category\" value=\"\" style=\"width: 100\">\n";
echo "  </td>\n";
echo "  <td class=\"windowbg1\">\n";
echo "   <input type=\"text\" name=\"" . ($lastid + 1) . "_idcat\" value=\"\" style=\"width: 40\">\n";
echo "  </td>\n";
echo "  <td class=\"windowbg1\">\n";
echo "    <input type=\"text\" name=\"" . ($lastid + 1) . "_dauer_d\" value=\"0\" style=\"width: 20\"> Tage<br>\n";
echo "    <input type=\"text\" name=\"" . ($lastid + 1) . "_dauer_h\" value=\"0\" style=\"width: 20\"> h<br>\n";
echo "    <input type=\"text\" name=\"" . ($lastid + 1) . "_dauer_m\" value=\"0\" style=\"width: 20\"> min\n";
echo "  </td>\n";
echo "  <td class=\"windowbg1\">\n";
echo "    <input type=\"text\" name=\"" . ($lastid + 1) . "_id_iw\" value=\"0\" style=\"width: 40\">\n";
echo "  </td>\n";
echo "  <td class=\"windowbg1\">\n";
echo "   <select name=\"" . ($lastid + 1) . "_bild\" style=\"width: 150\">\n";

foreach ($dateis as $key => $data) {
  echo "     <option value=\"" . $key . "\">" . $data . "</option>\n";
}

echo "</select>\n";
echo "  </td>\n";
echo " </tr>\n";
echo "\n";
echo " <tr>\n";
echo "  <td class=\"titlebg\" align=\"center\" colspan=\"7\">\n";
echo "   <input type=\"submit\" value=\"speichern\" name=\"B2\" class=\"submit\">\n";
echo "  </td>\n";
echo " </tr>\n";
echo "</table>\n";
echo "\n";
echo "<br>\n";

$sql = "SELECT DISTINCT category FROM " . $db_tb_gebaeude . 
       " ORDER BY category asc";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 
           'Could not query config information.', '', 
           __FILE__, __LINE__, $sql);
           
while($row = $db->db_fetch_array($result))
{
  echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 90%;\">\n";
  echo " <tr>\n";
  echo "  <td class=\"titlebg\" align=\"center\" colspan=\"7\">\n";
  echo "    <b>" . ( empty($row['category']) ? "Sonstige" : $row['category'] ). "</b>\n";
  echo "  </td>\n";
  echo " </tr>\n";
  echo " <tr>\n";
  echo "  <td class=\"windowbg2\" style=\"width:10%;\"></td>\n";
  echo "  <td class=\"windowbg2\" style=\"width:20%;\">Name</td>\n";
  echo "  <td class=\"windowbg2\" style=\"width:15%;\">Kategorie</td>\n";
  echo "  <td class=\"windowbg2\" style=\"width:10%;\">Reihenfolge</td>\n";
  echo "  <td class=\"windowbg2\" style=\"width:15%;\">Baudauer</td>\n";
  echo "  <td class=\"windowbg2\" style=\"width:10%;\">ID</td>\n";
  echo "  <td class=\"windowbg2\" style=\"width:20%;\">Bild</td>\n";
  echo " </tr>\n";
  echo "\n";

	$sql = "SELECT * FROM " . $db_tb_gebaeude . 
         " WHERE category='" . $row['category'] . 
         "' ORDER BY idcat ASC";
	$result_gebaeude = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
	while($row_gebaeude = $db->db_fetch_array($result_gebaeude)) {
		$dauer = dauer($row_gebaeude['dauer']);
		$bild_url = "bilder/gebs/" . 
                (( empty($row_gebaeude['bild'])) ? "blank.gif" 
                                                 : $row_gebaeude['bild'] . ".jpg");
    echo " <tr>\n";
    echo "  <td class=\"windowbg1\" align=\"center\">\n";
    echo "  Ausblenden:  <input type=\"checkbox\" name=\"" . $row_gebaeude['id'] . 
         "_inactive\" value=\"1\"" . (($row_gebaeude['inactive']) ?  " checked": "") . ">\n";
    echo "  Löschen:  <input type=\"checkbox\" name=\"" . $row_gebaeude['id'] . 
         "_delete\" onclick=\"return confirmlink(this, 'M&ouml;chten sie dieses Geb&auml;ude wirklich l&ouml;eschen?')\" value=\"1\">\n";
    echo "  </td>\n";
    echo "  <td class=\"windowbg1\">\n";
    echo "    <input type=\"text\" name=\"" . $row_gebaeude['id'] . 
         "_name\" value=\"" . $row_gebaeude['name']. "\" style=\"width: 200\">\n";
    echo "  </td>\n";
    echo "  <td class=\"windowbg1\">\n";
    echo "   <input type=\"text\" name=\"" . $row_gebaeude['id'] . 
         "_category\" value=\"" . $row_gebaeude['category']. "\" style=\"width: 100\">\n";
    echo "  </td>\n";
    echo "  <td class=\"windowbg1\">\n";
    echo "    <input type=\"text\" name=\"" . $row_gebaeude['id'] . 
         "_idcat\" value=\"" . $row_gebaeude['idcat']. "\" style=\"width: 40\">\n";
    echo "  </td>\n";
    echo "  <td class=\"windowbg1\">\n";
    echo "    <input type=\"text\" name=\"" . $row_gebaeude['id'] . 
         "_dauer_d\" value=\"" . $dauer['d'] . "\" style=\"width: 20\"> Tage<br>\n";
    echo "    <input type=\"text\" name=\"" . $row_gebaeude['id'] . 
         "_dauer_h\" value=\"" . $dauer['h'] . "\" style=\"width: 20\"> h\n";
    echo "    <input type=\"text\" name=\"" . $row_gebaeude['id'] . 
         "_dauer_m\" value=\""  . $dauer['m'] . "\" style=\"width: 20\"> min\n";
    echo "  </td>\n";
    echo "  <td class=\"windowbg1\">\n";
    echo "    <input type=\"text\" name=\"" . $row_gebaeude['id'] . 
         "_id_iw\" value=\"" . $row_gebaeude['id_iw'] . "\" style=\"width: 40\">\n";
    echo "  </td>\n";
    echo "  <td class=\"windowbg1\">\n";
    echo "    <img src=\"" . $bild_url . "\" border=\"0\" width=\"50\" height=\"50\" style=\"vertical-align:middle;\">\n"; 
    echo "    <select name=\"" . $row_gebaeude['id'] . "_bild\" style=\"width: 150\">\n";
    foreach ($dateis as $key => $data) {
      echo "      <option value=\"" . $key . "\"";
  	  if($row_gebaeude['bild'] == $key) {
        echo  " selected";
      }
      echo ">" . $data . "</option>\n";
    }
    echo "    </select>\n";
    echo "  </td>\n";
    echo " </tr>\n";
	}

  echo "<br>\n";
}

echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 90%;\">\n";
echo " <tr>\n";
echo "  <td class=\"titlebg\" align=\"center\">\n";
echo "   <input type=\"submit\" value=\"speichern\" name=\"editgebaeude\" class=\"submit\">\n";
echo "  </td>\n";
echo " </tr>\n";
echo "</table>\n";
echo "<br>\n";
?>
