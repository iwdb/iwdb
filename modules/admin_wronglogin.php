<?php
/*****************************************************************************/
/* admin_wronglogin.php                                                      */
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
	echo "Hacking attempt...!!"; 
	exit; 
}

if ( $user_status != "admin" ) {
	die('Hacking attempt...');
}

doc_title("falsche Logins");

$user = getVar('user');
if( ! empty($user)) {
	$sql = "DELETE FROM " . $db_tb_wronglogin . 
         " WHERE user='" . $user . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
	doc_message("Loginsperre geloescht");
}

echo "<br>\n";
start_table();
start_row("windowbg2", "style=\"width:30%;\"");
echo "Username";
next_cell("windowbg2", "style=\"width:30%;\"");
echo "IPs / Zeit";
next_cell("windowbg2", "style=\"width:30%;\"");
end_row();

$sql = "SELECT user FROM " . $db_tb_wronglogin . " GROUP BY user";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 
           'Could not query config information.', '', 
           __FILE__, __LINE__, $sql);
           
while($row = $db->db_fetch_array($result)) {
  start_row("windowbg1", "valign=\"top\"");
  echo $row['user'];
  next_cell("windowbg1", "valign=\"top\"");

	$sql = "SELECT ip, date FROM " . $db_tb_wronglogin . 
         " WHERE user = '" . $row['user'] . "'";
	$result_ip = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
             
	while($row_ip = $db->db_fetch_array($result_ip)) {
		echo "<b>" . $row_ip['ip'] . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . 
         strftime("%H:%M:%S am %d.%m.", $row_ip['date']) . "<br>\n";
	}
  next_cell("windowbg1", "valign=\"top\"");
  echo "<a href=\"index.php?action=admin&uaction=wronglogin&user=" . urlencode($row['user']) .
       "&sid=" . $sid . "\" onclick=\"return confirmlink(this, 'Loginsperre wirklich " .
       "löschen?')\"><img src=\"bilder/file_delete_s.gif\" border=\"0\" " .
       "alt=\"löschen\"></a>\n";
  end_row();
}

end_table();
?>