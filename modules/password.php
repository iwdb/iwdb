<?php
/*****************************************************************************
 * password.php                                                              *
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

include "./menustyles/doc_default.php";

doc_title("Passwort vergessen");

$username = getVar('username');
if (!empty($username)) {
    $sql = "SELECT email FROM " . $db_tb_user .
        " WHERE id = '" . $username . "'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $row = $db->db_fetch_array($result);
    if (!empty($row['email'])) {
        $newpass = randomstring($config_password_string, 7);

        $sql = "UPDATE " . $db_tb_user .
            " SET password = '" . md5($newpass) .
            "' WHERE id = '" . $username . "'";

        $result_u = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        $empfaenger = $row['email'];
        $betreff    = "Neues Passwort";
        $from       = $config_mailname;
        $text       = "Ein neues Passwort für die Icewars-DB wurde angefordert \n" .
            "Benutzername : " . $username . " \n" .
            "Passwort : " . $newpass . "\n";
        @mail($empfaenger, $betreff, $text, "From: $from ");

    }

    doc_message("Passwort an gespeicherte EMail-Adresse versendet.");
} else {
    ?>
    <form method='POST' action='index.php?action=password' enctype='multipart/form-data'>
        <table class='table_format' style="margin: 0 auto;">
            <tr>
                <td class='windowbg2'>Username:&nbsp;</td>
                <td class='windowbg1'><input style='width: 200px' type='text' name='username' placeholder='IW-Login-Name' required='required'></td>
            </tr>
            <tr>
                <td class='titlebg center' colspan='2'><input type='submit' value='OK' name='B1' class='submit'></td>
            </tr>
        </table>
        <a href='index.php'>Zurück zur Startseite</a>
    </form>
<?php
}