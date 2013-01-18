<?php
/*****************************************************************************
 * login.php                                                                 *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * ========================================================================= *
 * Copyright (c) 2004 Robert Riess - All Rights Reserved                     *
 *****************************************************************************
 * This program is free software; you can redistribute it and/or modify it   *
 * under the terms of the GNU General Public License as published by the     *
 * Free Software Foundation; either version 2 of the License, or (at your    *
 * option) any later version.                                                *
 *                                                                           *
 * This program is distributed in the hope that it will be useful, but       *
 * WITHOUT ANY WARRANTY; without even the implied warranty of                *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General *
 * Public License for more details.                                          *
 *                                                                           *
 * The GNU GPL can be found in LICENSE in this directory                     *
 *****************************************************************************
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

//****************************************************************************

echo '<h1>Login</h1>';

if ((isset($login_id)) && ($login_ok === false) && ($action != "memberlogout2")) {
    echo "<div class='doc_warning'>Falscher Benutzername oder Passwort!</div>\n";

    if ($wronglogins < $config_wronglogins) {
        echo "<div class='doc_warning'>Du hast noch " . ($config_wronglogins - $wronglogins) . " Versuch(e).</div>\n";
    }

    if ($wronglogins >= $config_wronglogins) {
        echo "<div class='doc_warning'>Du hast dich " . $wronglogins . " mal falsch eingeloggt! " .
            "Einloggen für die nächsten " .
            round($config_wronglogin_timeout / HOUR) .
            " Stunden gesperrt.<br>" .
            "Daten wurden an den Admin übermittelt.</div>\n";

    }
}
?>
<br>
<form method='POST' action='index.php?action=memberlogin2' enctype='multipart/form-data'>
    <table border='0' cellpadding='4' cellspacing='1' class='bordercolor' style="margin: 0 auto;">
        <tr>
            <td class='windowbg2'>Username:&nbsp;</td>
            <td class='windowbg1'><input style='width: 200px' type='text' name='login_id' required='required'></td>
        </tr>
        <tr>
            <td class='windowbg2'>Passwort:&nbsp;</td>
            <td class='windowbg1'><input style='width: 200px' type='password' name='login_password' required='required'></td>
        </tr>
        <tr>
            <td class='windowbg2'>Eingeloggt bleiben?</td>
            <td class='windowbg1' align='center'><input type='checkbox' name='login_cookie' value='1'></td>
        </tr>
        <tr>
            <td class='titlebg' align='center' colspan='2'><input type='submit' value='lass mich rein' name='B1' class='submit'></td>
        </tr>
    </table>
</form>
<a href='index.php?action=password'>Passwort vergessen?</a>
<br>
