<?php
/*****************************************************************************/
/* admin.php                                                                */
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
	exit ("Hacking attempt...!!");
}

if ( $user_status != "admin" && $user_status != "hc" )
	die('Hacking attempt...');
	
if ( $user_status == "admin" ) { 
    echo "<br>\n";
    start_table(0, 0, 0, 1);
    start_row("menutop", "style='text-align: center;'");
    action("admin&uaction=schiffstypen", "Schiffstypen");
    next_cell("menutop", "style='text-align: center;'");
    action("admin&uaction=gebaeude", "Gebäude");
    next_cell("menutop", "style='text-align: center;'");
    action("admin&uaction=allianzstatus", "Allianzstatus");
    next_cell("menutop", "style='text-align: center;'");
    action("admin&uaction=lastlogin", "letzte Logins");
    next_cell("menutop", "style='text-align: center;'");
    action("admin&uaction=wronglogin", "falsche Logins");
    next_cell("menutop", "style='text-align: center;'");
    action("admin&uaction=style", "Style");
    next_cell("menutop", "style='text-align: center;'");
    action("admin&uaction=einstellungen", "Einstellungen");
    end_row();
    end_table();
    echo "<br>\n";
}

$uaction = getVar('uaction');
switch ( $uaction )
{
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
	case "style": 
        include("./modules/admin_style.php");
        break;
	case "einstellungen": 
        include("./modules/admin_einstellungen.php");
        break;
	default: 
        include("./modules/admin_schiffstypen.php");
        break;
}
?>