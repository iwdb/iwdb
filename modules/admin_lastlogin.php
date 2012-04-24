<?php
/*****************************************************************************/
/* admin_lastlogin.php                                                       */
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
if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');
  
if ( $user_status != "admin" && $user_status != "hc" )
	die('Hacking attempt...');

doc_title("Admin Loginzeit");
echo "<br>\n";

start_table();
start_row("windowbg2", "style=\"width:20%;\"");
echo "Username";
next_cell("windowbg2", "style=\"width:20%;\"");
echo "letzter Login";
next_cell("windowbg2", "style=\"width:60%;\"");
end_row();

$sql = "SELECT sitterlogin, logindate, password FROM " . $db_tb_user . 
       " ORDER BY logindate, sitterlogin";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 
           'Could not query config information.', '', 
           __FILE__, __LINE__, $sql);

while($row = $db->db_fetch_array($result)) {
  $nopassword    = ( empty($row['password'])) ? " kein Passwort gesetzt" : ""; 
  $logindate     = $row['logindate'];
  $lastlogindate = ( empty($logindate)) ? "noch nie"
                                        : strftime($config_timeformat, $logindate);
  $lastloggedon  = ( empty($logindate)) ? "" 
                                        : floor(($config_date - $logindate) / ($DAYS) ) . 
                                          " Tage her";                                   
                                               
  start_row("windowbg1");
  echo "<a href=\"index.php?action=profile&sitterlogin=" .
       urlencode($row['sitterlogin']) . "&sid=" . $sid . "\">" . $row['sitterlogin'] . "</a>";
  next_cell("windowbg1");
  echo $lastlogindate;
  next_cell("windowbg1");
  echo $lastloggedon . $nopassword;
  end_row();
}

end_table();
?>