<?php
/*****************************************************************************/
/* nebel.php                                                                 */
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
/*                                                                           */
/* Dieses Modul dient f�r die manuelle eintragung der Nebel in die  
 /* Datenbank, da diese aus  der Universums�bersicht nicht  geparsed  
 /* werden k�nnen.  
 /*                                                                           */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspr�nglichen DB ist ein Gemeinschaftsprojekt von  
 /* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens daf�r eingerichtete  
 /* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/


if ( $user_status != "admin" ) {
	die('Hacking attempt...');
}


//Daten mal wieder auffangen ^^
$sitterlogin = getvar('sitterlogin');
$sid         = getvar('sid');


//Sortierung auffangen
$gala = getVar('gala') ? getVar('gala') : '';
$sys = getVar('sys') ? getVar('sys') : '';
$nebel = getVar('nebel') ? getVar('nebel') : '';

if ((trim($gala)!='') && (trim($sys)!='')){

	$id = $gala.":".$sys;


	//Daten Updaten...
	$sql  = "UPDATE " . $db_tb_sysscans . " SET nebula='".$nebel."' WHERE id='".$id."'";
	$update = $db->db_query($sql)
	or error(GENERAL_ERROR,
	  'Could not query config information.', '', 
	__FILE__, __LINE__, $sql);

	echo "<br><div class='system_notification'>Nebel bei ".$id." hinzugef&uuml;gt/gel&ouml;scht. </div><br>\n";

}

else {

	echo "<br><div class='system_notification'>Alle Felder m&uuml;ssen ausgef�llt sein! </div>";
}




// Nebelarten in Arry speichern f�r das Formular sp�ter
$nebelart = array (
   "" => "keiner",
   "BLN" => "blau",
   "GEN" => "gelb",
   "GRN" => "gr&uuml;n",
   "RON" => "rot",
   "VIN" => "violett"
);



// �berschrift

echo "<font style=\"font-size: 22px; color: #004466\">\n";
echo "Nebel manuel eintragen\n";
echo "</font><br><br>\n";



//Anfang der Tabelle
echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 100%;\">\n";


//Spalten�berschriften 
echo "<tr>\n";
echo "  <td class=\"titlebg\" style=\"width:10%;\">\n";
echo "    Galaxie\n";
echo "  </td>\n";
echo "  <td class=\"titlebg\" style=\"width:20%;\">\n";
echo "    System\n";
echo "  </td>\n";
echo "  <td class=\"titlebg\" style=\"width:10%;\">\n";
echo "    Nebelart\n";
echo "  </td>\n";
echo "</tr>\n";


//Formulardaten...
echo "<form action=\"index.php\" method=\"post\">\n";
echo "     <input type=\"hidden\" name=\"action\" value=\"nebel\">\n";
echo "     <input type=\"hidden\" name=\"sid\" value=".$sid.">\n";


echo "<tr>\n";
echo "  <td class=\"windowbg1\" style=\"width: 20%;\">\n";
echo "  <input type=\"text\" name=\"gala\" value=\"\" style=\"width: 50;\"><br>\n";
echo "  </td>\n";
echo "  <td class=\"windowbg1\" style=\"width: 20%;\">\n";
echo "  <input type=\"text\" name=\"sys\" value=\"\" style=\"width: 50;\"><br>\n";
echo "  </td>\n";
echo "  <td class=\"windowbg1\" style=\"width: 20%;\">\n";
echo "  <select name=\"nebel\">\n";
foreach ($nebelart as $key => $data)
echo ($nebel == $key) ? " <option value=\"" . $key . "\" selected>" . $data . "</option>\n": " <option value=\"" . $key . "\">" . $data . "</option>\n";
echo "</select>\n";
echo "  </td>\n";
echo "</tr>\n";

echo "<tr>\n";
echo "  <td class=\"windowbg2\" align=\"right\" colspan =\"6\" nowrap>\n";
echo " Du hast alles eingegeben? Dann klicke hier zum ";
echo "     <input type=\"submit\" class=\"rahmen5\" value=\"speichern\" name=\"B1\" class=\"submit\">\n";
echo "  </td>\n";
echo "</tr>\n";
echo "</form>\n";


echo "</table>";





?>
