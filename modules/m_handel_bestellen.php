<?php
/*****************************************************************************/
/* m_handel_bestellen.php                                                             */
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

/*****************************************************************************/
/* Dieses Modul dient zum Bestellen von Schiffen bei einem Fleeter           */
/* fuer die Iw DB: Icewars geoscan and sitter database                       */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschaftsprojekt von */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete           */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iwdb.de.vu                                   */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul ueber die index.php aufgerufen wurde. 
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!";
	exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig fuer die Benennung der zugehoerigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung fuer 
//    eine Installation ueber das Menue
//
$modulname  = "m_handel_bestellen";

//****************************************************************************
//
// -> Menuetitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Anzeige aufgeben";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul �ber die Navigation 
//    ausfuehren darf. Moegliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "Das Modul dient dem Bestellen von Schiffen und dem Res-Handel.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//
function workInstallDatabase() {
	global $db, $db_prefix, $db_tb_iwdbtabellen;

	$sqlscript = array(
	"CREATE TABLE `". $db_prefix  ."bestellen` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` varchar(30) NOT NULL default '',
  `coords` varchar(30) NOT NULL default '',
  `coords2` varchar(30) NOT NULL default '',
  `squad` varchar(10) NOT NULL default '',
  `typ` int(1) NOT NULL default '0',
  `schiff` varchar(20) NOT NULL default '',
  `menge` int(3) NOT NULL default '0',
  `bs` int(1) NOT NULL default '0',
  `transport` int(1) NOT NULL default '0',
  `angenommen` varchar(50) NOT NULL default '',
  `eisen` int(11) NOT NULL default '0',
  `stahl` int(11) NOT NULL default '0',
  `vv4a` int(11) NOT NULL default '0',
  `chemie` int(11) NOT NULL default '0',
  `eis` int(11) NOT NULL default '0',
  `wasser` int(11) NOT NULL default '0',
  `energie` int(11) NOT NULL default '0',
  `bevoelkerung` int(11) NOT NULL default '0',
  `order_time` int(12) NOT NULL default '0',
  PRIMARY KEY  (`id`) );",
  "ALTER TABLE `". $db_prefix ."user` 
         ADD `squad` varchar(30) NOT NULL default '';",
  "ALTER TABLE `". $db_prefix ."schiffstyp` 
         ADD `Alpha` int(1) NOT NULL default '0';",
  "ALTER TABLE `". $db_prefix ."schiffstyp` 
         ADD `Beta` int(1) NOT NULL default '0';",
  "ALTER TABLE `". $db_prefix ."schiffstyp` 
         ADD `Gamma` int(1) NOT NULL default '0';",
  "ALTER TABLE `". $db_prefix ."schiffstyp` 
         ADD `Delta` int(1) NOT NULL default '0';",
  "ALTER TABLE `". $db_prefix ."schiffstyp` 
         ADD `CHunt` int(1) NOT NULL default '0';",
  "ALTER TABLE `". $db_prefix ."schiffstyp` 
         ADD `Aristocats` int(1) NOT NULL default '0';",
  "ALTER TABLE `". $db_prefix ."schiffstyp` 
         ADD `mufasa` int(1) NOT NULL default '0';",
  "ALTER TABLE `". $db_prefix ."schiffstyp` 
         ADD `Omega` int(1) NOT NULL default '0';",
  "INSERT INTO `". $db_prefix ."iwdbtabellen` (`name`) VALUES ('bestellen');"
	);

	foreach($sqlscript as $sql) {
	$result = $db->db_query($sql)
	or error(GENERAL_ERROR,
	'Could not query config information.', '',
	__FILE__, __LINE__, $sql);
	}
	echo "<div class='system_notification'>";
  echo "Installation: Datenbank&auml;nderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Function workInstallMenu is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu() {
	global $modultitle, $modulstatus;
	$menu    = getVar('menu');
	$submenu = getVar('submenu');
	$actionparamters = '';
	insertMenuItem( $menu, $submenu, $modultitle, $modulstatus, 
                  $actionparameters );
	//
	// Weitere Wiederholungen fuer weitere Menue-Eintraege, z.B.
	//
	// insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
	//
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed
// for the configuration file
//
function workInstallConfigString() {
	 return "";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//
function workUninstallDatabase() {
	global $db, $db_tb_iwdbtabellen, $db_tb_bestellen;

	$sqlscript = array(
	"DROP TABLE `". $db_prefix ."bestellen`;",
  "ALTER TABLE `" . $db_prefix ."user` DROP COLUMN `squad`;",
  "ALTER TABLE `" . $db_prefix ."schiffstyp` DROP COLUMN `Alpha`;",
  "ALTER TABLE `" . $db_prefix ."schiffstyp` DROP COLUMN `Beta`;",
  "ALTER TABLE `" . $db_prefix ."schiffstyp` DROP COLUMN `Gamma`;",
  "ALTER TABLE `" . $db_prefix ."schiffstyp` DROP COLUMN `Delta`;",
  "ALTER TABLE `" . $db_prefix ."schiffstyp` DROP COLUMN `CHunt`;",
  "ALTER TABLE `" . $db_prefix ."schiffstyp` DROP COLUMN `Aristocats`;",
  "ALTER TABLE `" . $db_prefix ."schiffstyp` DROP COLUMN `mufasa`;",
  "ALTER TABLE `" . $db_prefix ."schiffstyp` DROP COLUMN `Omega`;",
	"DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='bestellen';"
	);

	foreach($sqlscript as $sql) {
		$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
		'Could not query config information.', '',
		__FILE__, __LINE__, $sql);
	}
	echo "<div class='system_notification'>";
  echo "Deinstallation: Datenbank&auml;nderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgefuehrt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs:
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" nat�rlich deinen Server angeben und default 
// durch den Dateinamen des Moduls ersetzen.
//
if( !empty($_REQUEST['was'])) {
	//  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
	if ( $user_status != "admin" )
		die('Hacking attempt...');

	echo "<div class='system_notification'>";
  echo "Installationsarbeiten am Modul " . $modulname . " ("  . 
        $_REQUEST['was'] . ")</div>\n";

	if (!@include("./includes/menu_fn.php"))
		die( "Cannot load menu functions" );

	// Wenn ein Modul administriert wird, soll der Rest nicht mehr
	// ausgefuehrt werden.
	return;
}

if (!@include("./config/".$modulname.".cfg.php")) {
	die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

global $db,$db_tb_user,$db_tb_bestellen,$db_tb_schiffstyp,$db_tb_scans;

$sql = "SELECT sitterlogin,squad FROM " . $db_tb_user . " 
        WHERE id = '" . $user_id . "'";

$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '',
  __FILE__, __LINE__, $sql);		

while($row_data = $db->db_fetch_array($result))
{
	$user_sitterlogin = $row_data['sitterlogin'];
  $user_squad = $row_data['squad'];
}

?>
<font style="font-size: 22px; color: #004466">Bestellen</font><br><br>
<?php

$typ = getVar('typ');

if( ! empty($_REQUEST['angelegt']) )
{
  $b_schiff = ' ';
  $b_anzahl = 0;
  $b_bs = 0;
  $b_transport = 0;
  $b_eisen = 0;
  $b_stahl = 0;
  $b_vv4a = 0;
  $b_chemie = 0;
  $b_eis = 0;
  $b_wasser = 0;
  $b_energie = 0;
  $b_bevoelkerung = 0;

  if ( @$_REQUEST['schiff'] ) { $b_schiff = $_REQUEST['schiff'];}
  if ( @$_REQUEST['anzahl'] ) { $b_anzahl = $_REQUEST['anzahl'];}
  if ( @$_REQUEST['bs'] ) { $b_bs = $_REQUEST['bs'];}
  if ( @$_REQUEST['transport'] ) { $b_transport = $_REQUEST['transport'];}
  if ( @$_REQUEST['eisen'] ) { $b_eisen = $_REQUEST['eisen'];}
  if ( @$_REQUEST['stahl'] ) { $b_stahl = $_REQUEST['stahl'];}
  if ( @$_REQUEST['vv4a'] ) { $b_vv4a = $_REQUEST['vv4a'];}
  if ( @$_REQUEST['chemie'] ) { $b_chemie = $_REQUEST['chemie'];}
  if ( @$_REQUEST['eis'] ) { $b_eis = $_REQUEST['eis'];}
  if ( @$_REQUEST['wasser'] ) { $b_wasser = $_REQUEST['wasser'];}
  if ( @$_REQUEST['energie'] ) { $b_energie = $_REQUEST['energie'];}
  if ( @$_REQUEST['bevoelkerung'] ) { $b_bevoelkerung = $_REQUEST['bevoelkerung'];}

	echo "<font color=\"#FF0000\"><b>Auftrag eingestellt.</b></font><br><br>";
	$sql = "INSERT INTO " . $db_tb_bestellen . " SET user = '" . $user_sitterlogin . "', 
         coords = '" . $_REQUEST['coords'] . "',
         squad = '" . $user_squad . "', 
         typ = '" . $_REQUEST['typ'] . "', 
         schiff = '" . $b_schiff . "', 
         menge = '" . $b_anzahl . "', 
         bs = '" . $b_bs . "', 
         transport = '" . $b_transport . "', 
         eisen = '" . $b_eisen . "', 
         stahl = '" . $b_stahl . "', 
         vv4a = '" . $b_vv4a . "', 
         chemie = '" . $b_chemie . "', 
         eis = '" . $b_eis . "', 
         wasser = '" . $b_wasser . "', 
         energie = '" . $b_energie . "', 
         bevoelkerung = '" . $b_bevoelkerung . "',
         order_time = '" . time() . "'";

	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

$sql = "SELECT coords FROM " . $db_tb_scans . " WHERE user = '" . $user_sitterlogin . "' ORDER BY coords ASC";

$result_coords = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);		
while($row_coords = $db->db_fetch_array($result_coords))
{
	$coords[] = $row_coords['coords'];
}

if ( $user_squad == ''){ $user_squad='Omega'; echo "<br><b>User-Squad wurde fuer dich noch nicht gesetzt!</b><br>";}

$sql = "SELECT abk FROM " . $db_tb_schiffstyp . " WHERE " . $user_squad ." = 1";

$result_schiff = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);		

?>
<form method="post" action="index.php?action=m_handel_bestellen&sid=<?php echo$sid;?>" enctype="multipart/form-data" name="test">
<table border="0" cellpadding="2" cellspacing="1" class="bordercolor" style="width: 60%;">
<tr>
<td class="windowbg2" style="width:40%;">
Typ
</td>
<td class="windowbg1">
<select name="typ" onChange="window.location='index.php?action=m_handel_bestellen&sid=<?php echo$sid;?>&typ=' + document.test.typ.options[document.test.typ.selectedIndex].value">
<!--<option value="">---</option>-->
<option value="1" <?php if($typ == "1") { echo "selected"; }?>>Schiffe</option>
<?php $typ="1"; ?>
<!-- <option value="2" <?php if($typ == "2") echo "selected";?>>Ress</option> -->
</select>
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
Zielplanet: <br>
<i>Der Planet, wo die Schiffe hin sollen</i>
</td>
<td class="windowbg1">
<select name="coords">
<?php
  foreach ($coords as $key)
		echo " <option value=\"" . $key . "\">" . $key . "</option>\n";
?>
</select>
</td>
</tr>
<?php if( $typ == "2" ){
?>
<tr>
<td class="windowbg2" style="width:40%;">
Eisen:
</td>
<td class="windowbg1">
<input type="text" name="eisen" size="15">
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
Stahl:
</td>
<td class="windowbg1">
<input type="text" name="stahl" size="15">
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
VV4A:
</td>
<td class="windowbg1">
<input type="text" name="vv4a" size="15">
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
Chemie:
</td>
<td class="windowbg1">
<input type="text" name="chemie" size="15">
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
Eis:
</td>
<td class="windowbg1">
<input type="text" name="eis" size="15">
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
Wasser:
</td>
<td class="windowbg1">
<input type="text" name="wasser" size="15">
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
Energie:
</td>
<td class="windowbg1">
<input type="text" name="energie" size="15">
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
Bev&ouml;lkerung:
</td>
<td class="windowbg1">
<input type="text" name="bevoelkerung" size="15">
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
Angebot:
</td>
<td class="windowbg1">
<select name="bs">
<option value="1">Suche</option>
<option value="2">Biete</option>

</select>
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%;">
Transport:
</td>
<td class="windowbg1">
<select name="transport">
<option value="2">Ich machs</option>
<option value="1">Mach du mal</option>
</select>
</td>
</tr>
<?php
}
if( $typ == "1" ) {
?>
<tr>
<td class="windowbg2" style="width:40%;">
Schiff:
</td>	
<td class="windowbg1">
<select name="schiff">
<option value="">---</option>

<?php
while($row_schiff = $db->db_fetch_array($result_schiff))
{
	if ( $typprev != $row_schiff['typ'] )
	{
		echo "<optgroup label=\"" . $row_schiff['typ'] . "\" title=\"" . $row_schiff['typ'] . "\"></optgroup>\n";
		$typprev = $row_schiff['typ'];
	}
	echo "<option value=\"" . $row_schiff['abk'] . "\">" . $row_schiff['abk'] . "</option>\n";
}
?>
</select>
</td>
</tr>
<tr>
<td class="windowbg2" style="width:40%">
Menge: <br>
<i>Die Anzahl der Schiffe, die geliefert werden sollen</i>
</td>
<td class="windowbg1">
<input type="text" name="anzahl">
</td>
</tr>
<?php
}
?>
<td class="windowbg2" align="center" colspan="2">
<input type="submit" name="B1" value="speichern">
</table>
<input type="hidden" name="angelegt" value="TRUE">
</form>
<?php
			?>
