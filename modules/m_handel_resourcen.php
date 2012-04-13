<?php
/*****************************************************************************/
/* m_handel_resourcen.php                                                             */
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
$modulname  = "m_handel_resourcen";

//****************************************************************************
//
// -> Menï¿½titel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Ressourcen";

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
  "Das Resourcen-Handels-Modul ist Bestandteil der Handels-Suite ".
	"und funktioniert eigenständig nicht! Benoetigt m_handel_bestellen.php";

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

?>

<font style="font-size: 22px; color: #004466">Schiffs/Ressanforderung</font>
<br><br>
<table border="0" cellpadding="0" cellspacing="1" class="bordercolor">
 <tr> 
  <td class="menutop" align="center">
   <a href="index.php?action=m_handel_resourcen&menu=suche&sid=<?php echo $sid;?>">Suche</a>
  </td>
  <td class="menutop" align="center">
   <a href="index.php?action=m_handel_resourcen&menu=biete&sid=<?php echo $sid;?>">Biete</a>
  </td>
  <td class="menutop" align="center">
   <a href="index.php?action=m_handel_resourcen&menu=meine&sid=<?php echo $sid;?>">Meine Ress</a>
  </td>
 </tr>
</table><br><br>
<?php
if ( !@$_GET['menu'] ) $_GET['menu'] = "suche";

if ( isset($_POST['holen']) )
{
	$sql = "UPDATE " . $db_tb_bestellen . " SET angenommen = '" . $user_sitterlogin . "' WHERE id = '" . $_POST['id'] . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}
if( $_GET['menu'] == "suche" || $_GET['menu'] == "" ) 
{
?>
<table border="0" cellpadding="2" cellspacing="1" class="bordercolor" style="width: 80%;">
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
<td class="titlebg" width="15%">
<b>Squad</b>
</td>
<td class="titlebg">
<b>Menge</b>
</td>
<td class="titlebg">
<b>Transport</b>
</td>
<td class="titlebg">

</td>
</tr>
<?php
	$sql = "SELECT id, user, coords, squad, typ, transport, bs, eisen, stahl, vv4a, chemie, eis, wasser, energie, bevoelkerung, order_time FROM " . $db_tb_bestellen . " WHERE typ = 2 AND bs = 1 AND user <> '" . $user_sitterlogin . "' AND angenommen = '' ORDER BY squad ASC, id ASC";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		while($row = $db->db_fetch_array($result))
		{
			if ( isset($_POST[$row['id'] . '_edit_suche']) )
			{
        $eisen_weg = $row['eisen'];
        $stahl_weg = $row['stahl'];
        $vv4a_weg = $row['vv4a'];
        $chemie_weg = $row['chemie'];
        $eis_weg = $row['eis'];
        $wasser_weg = $row['wasser'];
        $energie_weg = $row['energie'];
        $bevoelkerung_weg = $row['bevoelkerung'];

				if( @$_POST[$row['id'] . '_eisen'] < $row['eisen'] || @$_POST[$row['id'] . 'stahl'] < $row['stahl'] || @$_POST[$row['id'] . 'vv4a'] < $row['vv4a'] || @$_POST[$row['id'] . 'chemie'] < $row['chemie'] || @$_POST[$row['id'] . 'eis'] < $row['eis'] || @$_POST[$row['id'] . 'wasser'] < $row['wasser'] || @ $_POST[$row['id'] . 'energie'] < $row['energie'] || @$_POST[$row['id'] . 'bevoelkerung'] < $row['bevoelkerung'] )
				{

          if (isset($_POST[$row['id'] . '_eisen']))
					{
            $eisen_weg = $row['eisen'] - $_POST[$row['id'] . '_eisen'];
            $eisen_post = $_POST[$row['id'] . '_eisen'];
          }
          else { $eisen_post = 0; }

          if (isset($_POST[$row['id'] . '_stahl']))
					{
            $stahl_weg = $row['stahl'] - $_POST[$row['id'] . '_stahl'];
            $stahl_post = $_POST[$row['id'] . '_stahl'];
          }
          else { $stahl_post = 0; }

          if (isset($_POST[$row['id'] . '_vv4a']))
					{
            $vv4a_weg = $row['vv4a'] - $_POST[$row['id'] . '_vv4a'];
            $vv4a_post = $_POST[$row['id'] . '_vv4a'];
          }
          else { $vv4a_post = 0; }

          if (isset($_POST[$row['id'] . '_chemie']))
					{
            $chemie_weg = $row['chemie'] - $_POST[$row['id'] . '_chemie'];
            $chemie_post = $_POST[$row['id'] . '_chemie'];
          }
          else { $chemie_post = 0; }

          if (isset($_POST[$row['id'] . '_eis']))
					{
            $eis_weg = $row['eis'] - $_POST[$row['id'] . '_eis'];
            $eis_post = $_POST[$row['id'] . '_eis'];
          }
          else { $eis_post = 0; }

          if (isset($_POST[$row['id'] . '_wasser']))
					{
            $wasser_weg = $row['wasser'] - $_POST[$row['id'] . '_wasser'];
            $wasser_post = $_POST[$row['id'] . '_wasser'];
          }
          else { $wasser_post = 0; }

          if (isset($_POST[$row['id'] . '_energie']))
					{
            $energie_weg = $row['energie'] - $_POST[$row['id'] . '_energie'];
            $energie_post = $_POST[$row['id'] . '_energie'];
          }
          else { $energie_post = 0; }

          if (isset($_POST[$row['id'] . '_bevoelkerung']))
					{
            $bevoelkerung_weg = $row['bevoelkerung'] - $_POST[$row['id'] . '_bevoelkerung'];
            $bevoelkerung_post = $_POST[$row['id'] . '_bevoelkerung'];
          }
          else { $bevoelkerung_post = 0; }

					
					$sql = "INSERT INTO " . $db_tb_bestellen . " SET user = '" .$row['user'] . "', coords = '" . $row['coords'] . "', squad = '" . $row['squad'] . "', transport = '" . $row['transport'] . "', typ = '" . $row['typ'] . "', bs = '" . $row['bs'] . "', eisen = '" . $eisen_weg . "', stahl = '" . $stahl_weg . "', vv4a = '" . $vv4a_weg . "', chemie = '" . $chemie_weg . "', eis = '" . $eis_weg . "', wasser = '" . $wasser_weg . "', energie = '" . $energie_weg . "', bevoelkerung = '" . $bevoelkerung_weg . "', angenommen = '" . $user_sitterlogin . "'"; 
					$result = $db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
					
					$sql = "UPDATE " . $db_tb_bestellen . " SET eisen = '" . $eisen_post . "', stahl = '" . $stahl_post . "', vv4a = '" . $vv4a_post . "', chemie = '" . $chemie_post . "', eis = '" . $eis_post . "', wasser = '" . $wasser_post . "', energie = '" . $energie_post . "', bevoelkerung = '" . $bevoelkerung_post . "' WHERE id = '" . $row['id'] . "'";
					$result = $db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
					
					$sql = "DELETE FROM " . $db_tb_bestellen . " WHERE typ = 2 AND eisen = 0 AND stahl = 0 AND vv4a = 0 AND chemie = 0 AND eis = 0 AND wasser = 0 AND energie = 0 AND bevoelkerung = 0";
					$result = $db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
					echo '<meta http-equiv="refresh" content="0"';
				}
			}
			
			if( ! empty( $_POST[$row['id'] . '_del'] ) ) 
			{
				$sql = "DELETE FROM " . $db_tb_bestellen . " WHERE id = " . $row['id'];
				$result_del = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				echo '<meta http-equiv="refresh" content="0"';
			}
?>
<form action="index.php?action=m_handel_resourcen&menu=<?php echo $_GET['menu'];?>&sid=<?php echo $sid;?>" method="post">
<tr>
<td class="windowbg1">
<?php echo strftime($config_sitter_timeformat, $row['order_time']);?>
</td>
<td class="windowbg1">
<?php echo $row['coords'];?>
</td>
<td class="windowbg1">
<?php echo $row['user'];?>
</td>
<td class="windowbg1">
<?php echo $row['squad'];?>
</td>
<td class="windowbg1">
<?php
if( $row['eisen'] != "0" && $row['eisen'] != "" ) echo "Eisen: <input type=text name=" . $row['id'] . "_eisen size=8 value=" . $row['eisen'] . "><br>";
if( $row['stahl'] != "0" && $row['stahl'] != "" ) echo "Stahl: <input type=text name=" . $row['id'] . "_stahl size=8 value=" . $row['stahl'] . "><br>";
if( $row['vv4a'] != "0" && $row['vv4a'] != "" ) echo "VV4A: <input type=text name=" . $row['id'] . "_vv4a size=8 value=" . $row['vv4a'] . "><br>";
if( $row['chemie'] != "0" && $row['chemie'] != "" ) echo "Chemie: <input type=text name=" . $row['id'] . "_chemie size=8 value=" . $row['chemie'] . "><br>";
if( $row['eis'] != "0" && $row['eis'] != "" ) echo "Eis: <input type=text name=" . $row['id'] . "_eis size=8 value=" . $row['eis'] . "><br>";
if( $row['wasser'] != "0" && $row['wasser'] != "" ) echo "Wasser: <input type=text name=" . $row['id'] . "_wasser size=8 value=" . $row['wasser'] . "><br>";
if( $row['energie'] != "0" && $row['energie'] != "" ) echo "Energie: <input type=text name=" . $row['id'] . "_energie size=8 value=" . $row['energie'] . "><br>";
if( $row['bevoelkerung'] != "0" && $row['bevoelkerung'] != "" ) echo "Bev&ouml;lkerung: <input type=text name=" . $row['id'] . "_bevoelkerung size=5 value=" . $row['bevoelkerung'] . ">";
?>
</td>
<td class="windowbg1">
<?php
if( $row['transport'] == "1" ) echo "Ich machs";
if( $row['transport'] == "2" ) echo "Mach du mal";
?>
</td>
<td class="windowbg1" align="center">
<input type="submit" name="<?php echo $row['id'];?>_edit_suche" value="&auml;ndern">
</td>
</tr>
</form>
<?php
	}
?>
</table>
<?php
}
if( $_GET['menu'] == "biete") 
{
?>
<table border="0" cellpadding="2" cellspacing="1" class="bordercolor" style="width: 80%;">
<tr>
<td class="titlebg" width="15%">
<b>Abgabedatum</b>
</td>
<td class="titlebg" width="15%">
<b>Koordinaten</b>
</td>
<td class="titlebg" width="15%">
<b>Username</b>
</td>
<td class="titlebg" width="15%">
<b>Squad</b>
</td>
<td class="titlebg">
<b>Menge</b>
</td>
<td class="titlebg">
<b>Transport</b>
</td>
<td class="titlebg" width="5%">

</td>
</tr>
<?php
	$sql = "SELECT id, user, coords, squad, transport, eisen, stahl, vv4a, chemie, eis, wasser, energie, bevoelkerung, order_time FROM " . $db_tb_bestellen . " WHERE typ = 2 AND bs = 2 AND angenommen = '' AND user <> '" . $user_sitterlogin . "' ORDER BY squad ASC, id ASC";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while($row = $db->db_fetch_array($result))
	{
		if( ! empty( $_POST[$row['id'] . '_del'] ) ) 
		{
			$sql = "DELETE FROM " . $db_tb_bestellen . " WHERE id = " . $row['id'];
			$result_del = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			echo '<meta http-equiv="refresh" content="0"';
		}
		if ( isset($_POST[$row['id'] . '_holen']) )
		{
			$sql = "UPDATE " . $db_tb_bestellen . " SET angenommen = '" . $user_sitterlogin . "' WHERE id = '" . $row['id'] . "'";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			echo '<meta http-equiv="refresh" content="0"';
		}

?>
<form action="index.php?action=m_handel_resourcen&menu=<?php echo $_GET['menu'];?>&sid=<?php echo $sid;?>" method="post">
<tr>
<td class="windowbg1">
<?php echo strftime($config_sitter_timeformat, $row['order_time']);?>
</td>
<td class="windowbg1">
<?php echo $row['coords'];?>
</td>
<td class="windowbg1">
<?php echo $row['user'];?>
</td>
<td class="windowbg1">
<?php echo $row['squad'];?>
</td>
<td class="windowbg1">
<?php
if( $row['eisen'] != "0" ) echo "Eisen: " . $row['eisen'] . "<br>";
if( $row['stahl'] != "0" ) echo "Stahl: " . $row['stahl'] . "<br>";
if( $row['vv4a'] != "0" ) echo "VV4A: " . $row['vv4a'] . "<br>";
if( $row['chemie'] != "0" ) echo "Chemie: " . $row['chemie'] . "<br>";
if( $row['eis'] != "0" ) echo "Eis: " . $row['eis'] . "<br>";
if( $row['wasser'] != "0" ) echo "Wasser: " . $row['wasser'] . "<br>";
if( $row['energie'] != "0" ) echo "Energie: " . $row['energie'] . "<br>";
if( $row['bevoelkerung'] != "0" ) echo "Bev&ouml;lkerung: " . $row['bevoelkerung'];
?>
</td>
<td class="windowbg1">
<?php
if( $row['transport'] == "1" ) echo "Ich machs";
if( $row['transport'] == "2" ) echo "Mach du mal";
?>
</td>
<td class="windowbg1" align="center">
<input type="submit" name="<?php echo $row['id'];?>_holen" value="Meins!!">
</td>
</tr>
<?php
	}
?>
</table>
</form>
<?php
}
if ( $_GET['menu'] == "meine" )
{
?>
<form action="index.php?action=m_handel_resourcen&menu=<?php echo $_GET['menu'];?>&sid=<?php echo $sid;?>" method="post">
<table border="0" cellpadding="2" cellspacing="1" class="bordercolor" style="width: 95%;">
<tr>
<td class="titlebg" align="center" colspan="7">
<b>Eigene Handel</b>
</td>
</tr>
<tr>
<td class="titlebg">
<b>Von</b>
</td>
<td class="titlebg">
<b>An</b>
</td>
<td class="titlebg">
<b>coords</b>
</td>
<td class="titlebg">
<b>Menge</b>
</td>
<td class="titlebg">
<b>Transport</b>
</td>
<td class="titlebg">
<b>Typ</b>
</td>
<td class="titlebg" width="5%">

</td>
</tr>
<?php
	$sql = "SELECT id, coords, coords2, bs, angenommen, transport, eisen, stahl, vv4a, chemie, eis, wasser, energie, bevoelkerung FROM " . $db_tb_bestellen . " WHERE user LIKE '" . $user_sitterlogin . "' AND typ = 2 ORDER BY bs ASC, id ASC";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while( $row = $db->db_fetch_array($result))
	{
		if( ! empty( $_POST[$row['id'] . '_del'] ) ) 
		{
			$sql = "DELETE FROM " . $db_tb_bestellen . " WHERE id = " . $row['id'];
			$result_del = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			echo '<meta http-equiv="refresh" content="0"';
		}
?>
<tr>
<td class="windowbg1">
<?php echo $row['coords'];?>
</td>
<td class="windowbg1">
<?php echo $row['angenommen'];?>
</td>
<td class="windowbg1">
<?php echo $row['coords2'];?>
</td>
<td class="windowbg1">
<?php
if( $row['eisen'] != "0" ) echo "Eisen: " . $row['eisen'] . "<br>";
if( $row['stahl'] != "0" ) echo "Stahl: " . $row['stahl'] . "<br>";
if( $row['vv4a'] != "0" ) echo "VV4A: " . $row['vv4a'] . "<br>";
if( $row['chemie'] != "0" ) echo "Chemie: " . $row['chemie'] . "<br>";
if( $row['eis'] != "0" ) echo "Eis: " . $row['eis'] . "<br>";
if( $row['wasser'] != "0" ) echo "Wasser: " . $row['wasser'] . "<br>";
if( $row['energie'] != "0" ) echo "Energie: " . $row['energie'] . "<br>";
if( $row['bevoelkerung'] != "0" ) echo "Bev&ouml;lkerung: " . $row['bevoelkerung'];
?>
</td>
<td class="windowbg1">
<?php
if( $row['transport'] == "1" && $row['bs'] == "1" ) echo "Bleiben se ruhig!!!";
if( $row['transport'] == "2" && $row['bs'] == "1" ) echo "Ich muss verschicken";
if( $row['transport'] == "1" && $row['bs'] == "2" ) echo "Ich muss verschicken";
if( $row['transport'] == "2" && $row['bs'] == "2" ) echo "Einfach nur best&auml;tigen";
?>
</td>
<td class="windowbg1">
<?php
if( $row['bs'] == "1" ) echo "Suche";
if( $row['bs'] == "2" ) echo "Biete";
?>
</td>
<td class="windowbg1">
<input type="checkbox" name="<?php echo $row['id'];?>_del">
</td>
</tr>
<?php
	}
?>
<tr>
<td class="titlebg" colspan="7" align="center">
<input type="submit" name="B1" value="l&ouml;schen">
</td>
</tr>
</table>
</form><br><br>
<form action="index.php?action=m_handel_resourcen&menu=<?php echo $_GET['menu'];?>&sid=<?php echo $sid;?>" method="post">
<table border="0" cellpadding="2" cellspacing="1" class="bordercolor" style="width: 95%;">
<tr>
<td class="titlebg" align="center" colspan="9">
<b>Angenommene Handel</b>
</td>
</tr>
<tr>
<td class="titlebg">
<b>Von / Zu</b>
</td>
<td class="titlebg">
<b>coords</b>
</td>
<td class="titlebg">
<b>Zu / Von</b>
</td>
<td class="titlebg">
<b>Menge</b>
</td>
<td class="titlebg">
<b>Transport</b>
</td>
<td class="titlebg">
<b>Typ</b>
</td>
<td class="titlebg" width="5%">

</td>
<td class="titlebg" width="5%">

</td>
<td class="titlebg" width="5%">

</td>
</tr>
<?php
	$sql = "SELECT coords FROM " . $db_tb_scans . " WHERE user = '" . $user_sitterlogin . "' ORDER BY coords ASC";
	$result_coords = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);		
	while($row_coords = $db->db_fetch_array($result_coords))
	{
		$coords[] = $row_coords['coords'];
	}
	
	$sql = "SELECT id, user, coords, coords2, bs, transport, eisen, stahl, vv4a, chemie, eis, wasser, energie, bevoelkerung FROM " . $db_tb_bestellen . " WHERE angenommen LIKE '" . $user_sitterlogin . "' AND typ = 2 ORDER BY bs ASC, id ASC";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while( $row = $db->db_fetch_array($result))
	{
		if( ! empty( $_POST[$row['id'] . '_del'] ) ) 
		{
			$sql = "DELETE FROM " . $db_tb_bestellen . " WHERE id = " . $row['id'];
			$result_del = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			echo '<meta http-equiv="refresh" content="0"';
		}
		
		if( isset( $_POST[$row['id'] . '_edit'] ) ) 
		{
			$sql = "UPDATE " . $db_tb_bestellen . " SET coords2 = '" . $_POST['coords'] . "', transport = '" . $_POST['transport'] . "' WHERE id = " . $row['id'];
			$result_del = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$row['coords2'] = $_POST['coords'];
			$row['transport'] = $_POST['transport'];
		}
		
		if( isset( $_POST[$row['id'] . '_deins'] ) ) 
		{
			$sql = "UPDATE " . $db_tb_bestellen . " SET angenommen = '', coords2 = '' WHERE id = " . $row['id'];
			$result_del = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			echo '<meta http-equiv="refresh" content="0"';
		}
//		$sql = "SELECT komm FROM " . $db_tb_scans . " WHERE coords = '" . $row['coords'] . "'";
//		$result_komm = $db->db_query($sql)
//			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
//		$row_komm = $db->db_fetch_array($result_komm);



		
?>
<tr>
<td class="windowbg1">
<?php echo $row['user'];?>
</td>
<td class="windowbg1">
<?php echo $row['coords'];?>
</td>
<td class="windowbg1">
<select name="coords">
<?php
	if( $row['coords2'] == "" ) echo " <option value=\"\">---</option>\n";
	foreach ($coords as $key)
	if( $key == $row['coords2'] ) 
	{
		echo " <option value=\"" . $key . "\" selected>" . $key . "</option>\n";
	}else{
		echo " <option value=\"" . $key . "\">" . $key . "</option>\n";
	}
?>
</select>
</td>
<td class="windowbg1">
<?php
if( $row['eisen'] != "0" ) echo "Eisen: " . $row['eisen'] . "<br>";
if( $row['stahl'] != "0" ) echo "Stahl: " . $row['stahl'] . "<br>";
if( $row['vv4a'] != "0" ) echo "VV4A: " . $row['vv4a'] . "<br>";
if( $row['chemie'] != "0" ) echo "Chemie: " . $row['chemie'] . "<br>";
if( $row['eis'] != "0" ) echo "Eis: " . $row['eis'] . "<br>";
if( $row['wasser'] != "0" ) echo "Wasser: " . $row['wasser'] . "<br>";
if( $row['energie'] != "0" ) echo "Energie: " . $row['energie'] . "<br>";
if( $row['bevoelkerung'] != "0" ) echo "Bev&ouml;lkerung: " . $row['bevoelkerung'];
?>
</td>
<td class="windowbg1">
<select name="transport">
<?php
if( $row['transport'] == "1" && $row['bs'] == "1" ) echo "<option value=\"1\" selected>Ich muss verschicken</option>";
if( $row['transport'] != "1" && $row['bs'] == "1" ) echo "<option value=\"1\">Ich muss verschicken</option>";
if( $row['transport'] == "2" && $row['bs'] == "1" ) echo "<option value=\"2\" selected>Einfach nur best&auml;tigen</option>"; 
if( $row['transport'] != "2" && $row['bs'] == "1" ) echo "<option value=\"2\">Einfach nur best&auml;tigen</option>";
if( $row['transport'] == "1" && $row['bs'] == "2" ) echo "<option value=\"1\" selected>Lieferung frei Haus</option>"; 
if( $row['transport'] != "1" && $row['bs'] == "2" ) echo "<option value=\"1\">Lieferung frei Haus</option>";
if( $row['transport'] == "2" && $row['bs'] == "2" ) echo "<option value=\"2\" selected>Dat muss ich holen</option>";
if( $row['transport'] != "2" && $row['bs'] != "2" && $row_komm['komm'] == "1" ) echo "<option value=\"2\">Dat muss ich holen</option>";
?>
</select>
</td>
<td class="windowbg1">
<?php
if( $row['bs'] == "1" ) echo "Suche";
if( $row['bs'] == "2" ) echo "Biete";
?>
</td>
<td class="windowbg1">
<input type="submit" name="<?php echo $row['id'];?>_edit" value="&auml;ndern">
</td>
<td class="windowbg1">
<input type="submit" name="<?php echo $row['id'];?>_del" value="l&ouml;schen">
</td>
<?php
if( $row['bs'] == "2" )
{
?>
<td class="windowbg1">
<input type="submit" name="<?php echo $row['id'];?>_deins" value="Ach behalts">
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
</table>
</form>
<?php
}
?>