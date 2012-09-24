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

//direktes Aufrufen verhindern
if (basename($_SERVER['PHP_SELF']) != "index.php") {header('HTTP/1.1 404 not found');exit;};

//****************************************************************************
?>

<div class='doc_title'>Login</div>

<?php
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
?>

<br>

<form method='POST' action='index.php?action=memberlogin2' enctype='multipart/form-data'>
    <table border='0' cellpadding='4' cellspacing='1' class='bordercolor' style="margin: 0 auto;">
        <tr>
            <td class='windowbg2'>Username:&nbsp;</td>
            <td class='windowbg1'><input style='width: 200px' type='text' name='login_id' required='required'>
            </td>
        </tr>
        <tr>
            <td class='windowbg2'>Passwort:&nbsp;</td>
            <td class='windowbg1'><input style='width: 200px' type='password' name='login_password' required='required'>
            </td>
        </tr>
        <tr>
            <td class='windowbg2'>Eingeloggt bleiben?</td>
            <td class='windowbg1' align='center'><input type='checkbox' name='login_cookie' value='1'>
            </td>
        </tr>
        <tr>
            <td class='titlebg' align='center' colspan='2'><input type='submit' value='lass mich rein' name='B1' class='submit'>
            </td>
        </tr>
    </table>
</form>
<a href='index.php?action=password'>Passwort vergessen?</a>