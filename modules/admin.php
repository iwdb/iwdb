<?php
/*****************************************************************************
 * admin.php                                                                 *
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

if ($user_status != "admin" && $user_status != "hc") {
    die('Hacking attempt...');
}

//****************************************************************************

if ($user_status == "admin") {
    echo '<br>';
    start_table(0, 0, 0, 1);
    start_row("menutop center");
    action("admin&uaction=schiffstypen", "Schiffstypen");
    next_cell("menutop center");
    action("admin&uaction=gebaeude", "Gebäude");
    next_cell("menutop center");
    action("admin&uaction=allianzstatus", "Allianzstatus");
    next_cell("menutop center");
    action("admin&uaction=lastlogin", "letzte Logins");
    next_cell("menutop center");
    action("admin&uaction=wronglogin", "falsche Logins");
	next_cell("menutop center");
    action("admin&uaction=ressbedarf", "Ressbedarf Member");
    next_cell("menutop center");
    action("admin&uaction=einstellungen", "Einstellungen");
    end_row();
    end_table();
    echo '<br>';
}

$uaction = getVar('uaction');
switch ($uaction) {
    case "schiffstypen":
        include("./modules/admin_schiffstypen.php");
        break;
    case "gebaeude":
        include("./modules/admin_gebaeude.php");
        break;
    case "allianzstatus":
        include("./modules/admin_allianzstatus.php");
        break;
    case "lastlogin":
        include("./modules/admin_lastlogin.php");
        break;
    case "wronglogin":
        include("./modules/admin_wronglogin.php");
        break;
	case "ressbedarf":
        include("./modules/admin_ressbedarf.php");
        break;
    case "einstellungen":
        include("./modules/admin_einstellungen.php");
        break;
    default:
        include("./modules/admin_schiffstypen.php");
        break;
}
?>