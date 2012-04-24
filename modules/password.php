<?php
/*****************************************************************************/
/* password.php                                                              */
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

// -> Abfrage ob dieses Modul �ber die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!"; 
	exit; 
}

include "./menustyles/doc_default.php";

doc_title("Passwort vergessen");

$username = getVar('username');
if( ! empty($username)) {
	$sql = "SELECT email FROM " . $db_tb_user . 
         " WHERE id = '" . $username . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sql);
             
	$row = $db->db_fetch_array($result);
	if ( ! empty($row['email']) )	{
		$newpass = randomstring($config_password_string, 7);
		$sql = "UPDATE " . $db_tb_user . 
           " SET password = '" . md5($newpass) . 
           "' WHERE id = '" . $username . "'";
           
		$result_u = $db->db_query($sql)
			or error(GENERAL_ERROR, 
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);

    $message = "<html>\n" .
               "<head>\n" .
               " <title>Neues Passwort</title>\n" .
               "</head>\n" .
               "<body>\n" .
               "<div class='doc_centered'><b>Neues Passwort für " .
               $config_server . "</b><br><br>\n" .
               "Username: " . $username . "<br>\n" .
               "Passwort: " . $newpass . "<br></div>\n" .
               "</body>\n" .
               "</html>\n";
		$mail_head =
			"MIME-Version: 1.0\r\n".
			"Content-type: text/html; charset=iso-8859-1\r\n".
			"To: " . $username . " <" . $row['email'] . ">\r\n";
			"From: " . $config_mailname . " <" . $config_mailfrom . ">\r\n";

		@mail($row['email'], "Neues Passwort", $message, $mail_head);
	}

  doc_message("Passwort an gespeicherte EMail-Adresse versendet.");
} else {
  start_form("password");
  start_table(0);
  start_row("windowbg2");
  echo "Username:&nbsp;";
  next_cell("windowbg1");
  echo "<input style=\"width: 200\" type=\"text\" name=\"username\">\n";
  next_row("titlebg", "align=\"center\"", 2);
  echo "<input type=\"submit\" value=\"OK\" name=\"B1\" class=\"submit\">&nbsp;".
       "<input type=\"reset\" value=\"reset\" name=\"B2\" class=\"submit\">";
  end_row();
  end_table();
  end_form();
	echo "<br>\n";
	echo "<br>\n";
	echo "<a href=\"index.php\">Zurück zur Startseite</a>";
}
?>