<?php
/*****************************************************************************/
/* de_info_geb.php                                                     */
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

/*****************************************************************************/
/* Diese Erweiterung der urspuenglichen DB ist ein Gemeinschafftsprojekt von */
/* IW-Spielern.                                                              */
/*                                                                           */
/* Autor: Mac (MacXY@herr-der-mails.de)                                      */
/* Datum: April 2012                                                         */
/*                                                                           */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*        httpd://handels-gilde.org/?www/forum/index.php;board=1099.0        */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/

if (!defined('DEBUG_LEVEL'))
	define('DEBUG_LEVEL', 0);

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_info_geb ( $return )
{
	/* do some database stuff here */
    echo "InfoGeb Parser noch nicht vollständig implementiert<br />";
	return true;
}

?>