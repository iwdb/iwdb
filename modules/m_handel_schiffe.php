<?php
/*****************************************************************************/
/* m_handel_schiffe.php                                                             */
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
/* Dieses Modul dient als Vorlage zum Erstellen von eigenen Zusatzmodulen    */
/* fï¿½r die Iw DB: Icewars geoscan and sitter database  
 /*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschaftsprojekt von */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete           */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iwdb.de.vu                                   */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul ï¿½ber die index.php aufgerufen wurde. 
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!";
	exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig fï¿½r die Benennung der zugehoerigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung fï¿½r 
//    eine Installation ï¿½ber das Menï¿½
//
$modulname  = "m_handel_schiffe";

//****************************************************************************
//
// -> Menï¿½titel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "bestellte Schiffe";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul ï¿½ber die Navigation 
//    ausfuehren darf. Moegliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc =
  "Das Anzeige-Modul bestellte Schiffe ist Bestandteil der Handels-Suite ".
	"und funktioniert eigenständig nicht! Benoetigt m_handel_bestellen.php.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//
function workInstallDatabase() {
	/*	global $db, $db_prefix, $db_tb_iwdbtabellen;

	$sqlscript = array(
	"CREATE TABLE " . $db_prefix . "neuername
	(
	);",

	"INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
	" VALUES('neuername')"
	);
	foreach($sqlscript as $sql) {
	$result = $db->db_query($sql)
	or error(GENERAL_ERROR,
	'Could not query config information.', '',
	__FILE__, __LINE__, $sql);
	}
	echo "<div class='system_notification'>Installation: Datenbank&auml;nderungen = <b>OK</b></div>";
	*/}

	//****************************************************************************
	//
	// Function workUninstallDatabase is creating all menu entries needed for
	// installing this module. This function is called by the installation method
	// in the included file includes/menu_fn.php
	//
	function workInstallMenu() {
		global $modultitle, $modulstatus;

		$menu    = getVar('menu');
		$submenu = getVar('submenu');

		$actionparamters = "";
		insertMenuItem( $menu, $submenu, $modultitle, $modulstatus, $actionparameters );
		//
		// Weitere Wiederholungen fï¿½r weitere Menue-Eintraege, z.B.
		//
		// 	insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
		//
	}

	//****************************************************************************
	//
	// Function workInstallConfigString will return all the other contents needed
	// for the configuration file
	//
	function workInstallConfigString() {
		/*  global $config_gameversion;
		 return
		 "\$v04 = \" <div class=\\\"doc_lightred\\\">(V " . $config_gameversion . ")</div>\";";
		 */}

		//****************************************************************************
		//
		// Function workUninstallDatabase is creating all database entries needed for
		// removing this module.
		//
		function workUninstallDatabase() {
			/*  global $db, $db_tb_iwdbtabellen, $db_tb_neuername;

			$sqlscript = array(
			"DROP TABLE " . $db_tb_neuername . ";",
			"DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='neuername';"
			);

			foreach($sqlscript as $sql) {
			$result = $db->db_query($sql)
			or error(GENERAL_ERROR,
			'Could not query config information.', '',
			__FILE__, __LINE__, $sql);
			}
			echo "<div class='system_notification'>Deinstallation: Datenbank&auml;nderungen = <b>OK</b></div>";
			*/}

			//****************************************************************************
			//
			// Installationsroutine
			//
			// Dieser Abschnitt wird nur ausgefuehrt wenn das Modul mit dem Parameter
			// "install" aufgerufen wurde. Beispiel des Aufrufs:
			//
			//      http://Mein.server/iwdb/index.php?action=default&was=install
			//
			// Anstatt "Mein.Server" natï¿½rlich deinen Server angeben und default 
			// durch den Dateinamen des Moduls ersetzen.
			//
			if( !empty($_REQUEST['was'])) {
				//  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
				if ( $user_status != "admin" )
				die('Hacking attempt...');

				echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
	     " ("  . $_REQUEST['was'] . ")</div>\n";

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

$sql = "SELECT sitterlogin,squad,rang FROM " . $db_tb_user . " 
        WHERE id = '" . $user_id . "'";

$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '',
  __FILE__, __LINE__, $sql);		

while($row_data = $db->db_fetch_array($result))
{
	$user_sitterlogin = $row_data['sitterlogin'];
  $user_squad = $row_data['squad'];
  $user_rang = $row_data['rang'];
}

$typprev = '';

?>



<script language="JavaScript" type="text/javascript">

function fill_delivery(id_edit,id_del,delivery) {
	if (document.getElementById(id_del).checked == true ) {
		document.getElementById(id_edit).value=delivery;
	}
	else {
		document.getElementById(id_edit).value='0';
	}
}
function uncheck_delivery(id_del) {
	document.getElementById(id_del).checked=false;
}
</script>

<font style="font-size: 22px; color: #004466">Schiffshandel</font>
<br><br>
<table border="0" cellpadding="0" cellspacing="1" class="bordercolor">
<tr>
  <td class="menutop" align="center">
   <a href="index.php?action=m_handel_schiffe&menu=squadanzeige&sid=<?=$sid;?>">Bestellte Schiffe<br>- eigener Squad -</a>
  </td>
  <td class="menutop" align="center">
   <a href="index.php?action=m_handel_schiffe&menu=anzeige&sid=<?=$sid;?>">Alle bestellten Schiffe<br> - Reihenfolge -</a>
  </td>
  <?php 
  if( $user_status == "admin" || strpos($user_rang, "HC") !== FALSE || strpos($user_rang, "Gründer") !== FALSE)
  {
  ?>
  <td class="menutop" align="center">
   <a href="index.php?action=m_handel_schiffe&menu=ausblenden&sid=<?=$sid;?>">Schiffe freischalten</a>
  </td>
  <?php
  }
  ?>
 </tr>
</table>
<br>
<br>
<?php

if (isset( $_POST['frei'] ))
{
  if( ! empty ( $_POST['frei'] ) )
  {
	  echo "<font color=\"#FF0000\"><b>Freigegebene Schiffe wurden editiert.<br> BITTE NEU LADEN!</b></font><br><br>";
  }
}

if ( @$_GET['menu'] == "squadanzeige" || @$_GET['menu'] == "anzeige" || @$_GET['menu'] == "" )
{ 
	if( @$_GET['menu'] == "squadanzeige" || @$_GET['menu'] == "" )
	{
		$breite = "70";
	}elseif( @$_GET['menu'] == "anzeige" )
	{
		$breite = "90";
	}

$phpself = $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'];

?>

<form method="POST" action="<?=$phpself;?>" enctype="multipart/form-data">
<table border="0" cellpadding="2" cellspacing="1" class="bordercolor" style="width: <?=$breite;?>%;">
<tr>
<td class="titlebg" colspan="7" align="center">
<b>Schiffe</b>
</td>
</tr>
<tr>
<td class="titlebg" width="15%">
<b>Bestellzeitpunkt</b>
</td>
<td class="titlebg" width="15%">
<b>Koordinaten</b>
</td>
<td class="titlebg" width="15%">
<b>Username</b>
</td>
<?php
if ( @$_GET['menu'] == "anzeige" ) 
{
?>
<td class="titlebg" width="15%">
<b>Squad</b>
</td>
<?php
}
?>
<td class="titlebg">
<b>Schiff</b>
</td>
<td class="titlebg" width="6%">
<b>Anzahl</b>
</td>
<td class="titlebg" width="17%">

</td>
</tr>
<?php
if( @$_GET['menu'] == "squadanzeige" || @$_GET['menu'] == "") 
{
$sql = "SELECT id, user, coords, schiff, menge , order_time FROM " . $db_tb_bestellen . " WHERE squad = '" . $user_squad . "' AND typ = 1 ORDER BY id ASC";
}elseif(@$_GET['menu'] == "anzeige")
{
$sql = "SELECT id, user, coords, squad, schiff, menge , order_time FROM " . $db_tb_bestellen . " WHERE typ = 1 ORDER BY id ASC";
}
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while($row = $db->db_fetch_array($result))
	{
		$open_delivery="1";
		if( ! empty( $_POST[$row['id'] . '_edit'] ) ) 
		{
			$open_delivery=$row['menge']-$_POST[$row['id'] . '_edit'];
			$sql = "UPDATE " . $db_tb_bestellen . " SET menge = " . $open_delivery . " WHERE id = " . $row['id'];
			$result_del = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			echo '<meta http-equiv="refresh" content="0"';
		}
		if( ! empty( $_POST[$row['id'] . '_del'] ) || ($open_delivery <= "0" ) ) 
		{
			$sql = "DELETE FROM " . $db_tb_bestellen . " WHERE id = " . $row['id'];
			$result_del = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			echo '<meta http-equiv="refresh" content="0"';
		}	
?>
<tr>
<td class="windowbg1">
<?=strftime($config_sitter_timeformat, $row['order_time']);?>
</td>
<td class="windowbg1">
<?=$row['coords'];?>
</td>
<td class="windowbg1">
<?=$row['user'];?>
</td>
<?php
if ( @$_GET['menu'] == "anzeige" ) 
{
?>
<td class="windowbg1">
<?=$row['squad'];?>
</td>
<?php
}
?>
<td class="windowbg1">
<?=$row['schiff'];?>
</td>
<td class="windowbg1">
<?=$row['menge'];?>

</td>
<?php
if ( $user_status == "admin" || strpos($user_rang, "HC") !== FALSE || strpos($user_rang, "Gründer") !== FALSE || strpos($user_rang, "iHC") !== FALSE || $user_sitterlogin == $row['user'] )
{
?>
<td class="windowbg1">

<?php
if ( $user_status == "admin" || strpos($user_rang, "HC") !== FALSE || strpos($user_rang, "Gründer") !== FALSE || strpos($user_rang, "iHC") !== FALSE )
{
?>
geliefert: <input type="text" size="3" name="<?=$row['id'];?>_edit" id="<?=$row['id'];?>_edit" value="0" onChange="javascript:uncheck_delivery('<?=$row['id'];?>_del');">

<?php
}
?>

<input type="checkbox" name="<?=$row['id'];?>_del" id="<?=$row['id'];?>_del" onclick="javascript:fill_delivery('<?=$row['id'];?>_edit','<?=$row['id'];?>_del','<?=$row['menge'];?>');">
</td>
<?php
}else{
?>
<td class="windowbg1">
</td>
<?php
}
?>
</tr>
<?php
	}
?>
<tr>
<td class="titlebg" colspan="7" align="center">
<input type="submit" name="B1" value="l&ouml;schen/&auml;ndern">
</td>
</tr>
</table>
</form>
<?php
}elseif( @$_GET['menu'] == "ausblenden" )
{ 
if( $user_status <> "admin" && strpos($user_rang, "HC") !== FALSE && strpos($user_rang, "Gründer") !== FALSE)
	die('Hacking attempt...');
?>
<form method="POST" action="index.php?action=m_handel_schiffe&menu=ausblenden&sid=<?=$sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="2" cellspacing="1" class="bordercolor" style="width: 50%;">
<tr>
<td class="titlebg">
<b>Schiff</b>
</td>
<?php

if( $user_status == "admin" || $user_rang == "HC" || $user_rang == "Gründer")
{
?>
<td class="titlebg" width="5%">
<b>WildesStiefmuetterchen</b>
</td>
<td class="titlebg" width="5%">
<b>Vergissmeinnicht</b>
</td>
<td class="titlebg" width="5%">
<b>Ferocactus</b>
</td>
<td class="titlebg" width="5%">
<b>Omega</b>
</td>
<?php
}
?>
</tr>
<?php

//
//
//

	if ($user_status == "admin" || $user_rang == "HC" || $user_rang == "Gründer")
	{
		$sql = "SELECT * FROM " . $db_tb_schiffstyp . " typ ORDER BY typ ASC";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		while($row = $db->db_fetch_array($result))
		{		
			if( $row['WildesStiefmuetterchen'] == "1" )
			{
			$WildesStiefmuetterchen = 'checked="checked"';
			}else{
			$WildesStiefmuetterchen = "";
			}
			if( $row['Vergissmeinnicht'] == "1" )
			{
			$Vergissmeinnicht = 'checked="checked"';
			}else{
			$Vergissmeinnicht = "";
			}
			if( $row['Ferocactus'] == "1" )
			{
			$Ferocactus = 'checked="checked"';
			}else{
			$Ferocactus = "";
			}
			if( $row['Omega'] == "1" )
			{
			$omega = 'checked="checked"';
			}else{
			$omega = "";
			}		
		
			if( ! empty ( $_POST['frei'] ) )
			{
				$squad = array("WildesStiefmuetterchen", "Vergissmeinnicht", "Ferocactus", "Omega");
				for( $x=0 ; $x < 4 ; $x++)
				{	
					if( @$_POST[$row['id'] . '_' . $squad[$x] . '_accept' ] == TRUE )
					{	
						$sql = "UPDATE " . $db_tb_schiffstyp . " SET " . $squad[$x] . " = 1 WHERE id = " . $row['id'];
						$result_del = $db->db_query($sql)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
							
						if( $x == 0 )
							$WildesStiefmuetterchen = 'checked="checked"';
						if( $x == 1 )
							$Vergissmeinnicht == 'checked="checked"';
						if( $x == 2 )
							$Ferocactus == 'checked="checked"';
						if( $x == 3 )
							$omega == 'checked="checked"';
	
					}elseif( @$_POST[$row['id'] . '_' . $squad[$x] . '_accept' ] == FALSE )
					{
						$sql = "UPDATE " . $db_tb_schiffstyp . " SET " . $squad[$x] . " = 0 WHERE id = " . $row['id'];
						$result_del = $db->db_query($sql)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
							
						if( $x == 0 )
							$WildesStiefmuetterchen = "";
						if( $x == 1 )
							$Vergissmeinnicht == "";
						if( $x == 2 )
							$Ferocactus == "";
						if( $x == 3 )
							$omega == "";
					}
				}
			}
		if ( $typprev != $row['typ'] )
		{
			echo "<tr><td class=\"titlebg\" colspan=\"9\" align=\"center\">" . $row['typ'] . "</td></tr>";
			$typprev = $row['typ'];
		}
?>
<tr>
<td class="windowbg1">
<?=$row['abk'];?>
</td>
<td class="windowbg1" align="center">
<input type="checkbox" name="<?=$row['id'];?>_WildesStiefmuetterchen_accept" <?=$WildesStiefmuetterchen;?>>
</td>
<td class="windowbg1" align="center">
<input type="checkbox" name="<?=$row['id'];?>_Vergissmeinnicht_accept" <?=$Vergissmeinnicht;?>>
</td>
<td class="windowbg1" align="center">
<input type="checkbox" name="<?=$row['id'];?>_Ferocactus_accept" <?=$Ferocactus;?>>
</td>
<td class="windowbg1" align="center">
<input type="checkbox" name="<?=$row['id'];?>_Omega_accept" <?=$omega;?>>
</td>
</tr>
<?php
		}
	}
?>
<tr>
<td class="titlebg" colspan="9" align="center">
<input type="submit" name="B1" value="speichern">
<input type="hidden" name="frei" value="TRUE">
</table>
</form>
<?php
}

?>