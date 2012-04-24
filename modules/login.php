<?php
/*****************************************************************************/
/* login.php                                                                 */
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
include "./menustyles/doc_default.php";
doc_title("Login");
if(( isset($login_id) ) && ($login_ok === FALSE) && ($action != "memberlogout2" )) {
  doc_message("Falscher Benutzername oder Passwort!");

	if( $wronglogins < $config_wronglogins ) {
    doc_message("Du hast noch " . ($config_wronglogins - $wronglogins) . 
                " Versuch(e).");
  }
    
	if( $wronglogins >= $config_wronglogins ) { 
    doc_message("Du hast dich " . $wronglogins . " mal falsch eingeloggt! " .
                "Einloggen für die nächsten " .
                round($config_wronglogin_timeout / $HOURS) . 
                " Stunden gesperrt.<br>". 
                "Daten wurden an den Admin übermittelt.");
  }
}

echo "<br>\n";

start_form("memberlogin2");
start_table(0);

start_row("windowbg2");
echo "Username:&nbsp;";
next_cell("windowbg1");
echo "<input style=\"width: 200\" type=\"text\" name=\"login_id\">\n";

next_row("windowbg2");
echo "Passwort:&nbsp;";
next_cell("windowbg1");
echo "<input type=\"password\" style=\"width: 200\" name=\"login_password\">\n";

next_row("windowbg2");
echo "Eingeloggt bleiben?";
next_cell("windowbg1", "align=\"center\"");
echo "<input type=\"checkbox\" name=\"login_cookie\" value=\"1\">\n";

next_row("titlebg", "align=\"center\"", 2);
echo "<input type=\"submit\" value=\"OK\" name=\"B1\" class=\"submit\">&nbsp;".
     "<input type=\"reset\" value=\"reset\" name=\"B2\" class=\"submit\">\n";
end_row();
end_table();
end_form();
action("password", "Passwort vergessen?");
?>