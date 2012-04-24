<?php
/*****************************************************************************/
/* help.php                                                                  */
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

if (!defined('IRA'))
	die('Hacking attempt...');
?>
<div class='doc_title'>Hilfe</div>
<br>
<table border="0" cellpadding="8" cellspacing="1" class="bordercolor" style="width: 70%;">
 <tr>
  <td class="help">
<p align="right"><a href="index.php?action=help&topic=index&sid=<?php echo $sid;?>">Index</a></p><hr><br>
<?php
$topic = getVar('topic');
$topic = ( empty($topic) ) ? "index" : $topic;

if (! preg_match('/^[a-zA-Z0-9_-]*$/', $topic)) {
  error(GENERAL_ERROR, 'Malformed help topic string (' . $topic . ') .', '',
        __FILE__, __LINE__);
  exit(1);
}

if ( file_exists("help/" . $topic . ".htm") === TRUE ) include("help/" . $topic . ".htm");
else include("help/default.htm");
?>
<br><hr>
<p align="right"><a href="javascript:history.back();">zurück</a></p>
  </td>
 </tr>
</table>