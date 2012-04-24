<?php
/*****************************************************************************/
/* m_building.php                                                            */
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
/* für die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iwdb.de.vu                                   */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (!defined('IRA'))
die('Hacking attempt...');

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Dateinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
$modulname  = "m_building";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Gebäudeanzeige";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation
//    ausführen darf. Mögliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc =
  "Ermöglicht das Anzeigen der Gebäude. <br> Dieses Modul braucht eine Installation des dynamischen Techtrees!";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//

function workInstallDatabase() {
	global $db, $db_prefix, $db_tb_iwdbtabellen;
/*
	$sqlscript = array(
		"ALTER TABLE `" . $db_prefix . "gebaeude` ADD `info` TEXT NOT NULL ," .
		"ADD `n_building` TEXT NOT NULL ," .
		"ADD `n_research` TEXT NOT NULL ," .
		"ADD `n_kolotyp` TEXT NOT NULL ," .
		"ADD `n_planityp` TEXT NOT NULL ," .
		"ADD `e_research` TEXT NOT NULL ," .
		"ADD `e_building` TEXT NOT NULL ," .
		"ADD `zerstoert` TEXT NOT NULL ," .
		"ADD `bringt` VARCHAR( 200 ) NOT NULL ," .
		"ADD `kostet` VARCHAR( 200 ) NOT NULL ," .
		"ADD `Kosten` VARCHAR( 200 ) NOT NULL ",

		"ALTER TABLE `" . $db_prefix . "gebaeude` ADD `Punkte` INT( 5 ) NOT NULL ," .
		"CHANGE `category` `category` VARCHAR( 50 ) NOT NULL DEFAULT ' 0. unbekannt' ," .
		"ADD `MaximaleAnzahl` INT( 3 ) NOT NULL ," .
		"ADD `typ` VARCHAR( 5 ) NOT NULL ",
		
    "INSERT INTO " . $db_tb_parser . "(modulename,recognizer,message) VALUES " .
    "('building', 'Gebäudeinfo: ', 'Gebäude')"

		/*"UPDATE `gebaeude` SET `name` = 'Kraftwerk (Solar) (orbital)' WHERE `id` =23 LIMIT 1 ",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('neuername')" 
  );*/
    
    foreach($sqlscript as $sql) {
    	$result = $db->db_query($sql)
  	  or error(GENERAL_ERROR,
               'Could not query config information.', '',
  	  __FILE__, __LINE__, $sql);
    }
    echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
}

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
	// Weitere Wiederholungen für weitere Menü-Einträge, z.B.
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
	global $db, $db_prefix, $db_tb_iwdbtabellen;
/*
	$sqlscript = array(
		"ALTER TABLE `" . $db_prefix . "gebaeude` " .
		"CHANGE `category` `category` VARCHAR( 50 ) NOT NULL DEFAULT '' ",
		
		"DELETE FROM " . $db_tb_parser . " WHERE modulename='building' "
		
		/*  global $db, $db_tb_iwdbtabellen, $db_tb_neuername;
		$sqlscript = array(
		"DROP TABLE " . $db_tb_neuername . ";",
		"DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='neuername';"
		); 
		
  );*/
    
  foreach($sqlscript as $sql) {
   	$result = $db->db_query($sql)
 	  or error(GENERAL_ERROR,
             'Could not query config information.', '',
 	  __FILE__, __LINE__, $sql);
  }
	echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
}

		//****************************************************************************
		//
		// Installationsroutine
		//
		// Dieser Abschnitt wird nur ausgeführt wenn das Modul mit dem Parameter
		// "install" aufgerufen wurde. Beispiel des Aufrufs:
		//
		//      http://Mein.server/iwdb/index.php?action=default&was=install
		//
		// Anstatt "Mein.Server" natürlich deinen Server angeben und default
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
	  // ausgeführt werden.
	  return;
		}

		if (!@include("./config/".$modulname.".cfg.php")) {
			die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
		}


		//****************************************************************************
		//
		// -> Und hier beginnt das eigentliche Modul

		echo "<p>&nbsp;</p>";

		global $db,$db_tb_gebaeude, $user_sitterlogin, $db_prefix, $show_building, $sid, $db_tb_research2user, $db_tb_gebaeude2user ;

		if (isset($show_building) AND !empty($show_building)) {

			$id = $show_building;

		} else {

			$id = GetVar('show_building');

		}
		if ( empty($id) ) {
			?>
<table>
	<tr>
	<?php
	$oldcat = 0;

	$sqlB = "SELECT id, name, bild, typ, idcat, category FROM " . $db_tb_gebaeude . " WHERE inactive != 1 ORDER BY category ASC ";
	$resultB = $db->db_query($sqlB)
	or error(GENERAL_ERROR,
             'Could not query config information.', '', 
	__FILE__, __LINE__, $sqlB);
	while ($rowB = $db->db_fetch_array($resultB)) {

		if ($rowB['category'] != $oldcat) {
			$oldcat = $rowB['category'];
			?>
	
	
	<tr>
		<td colspan="2"><?php echo $rowB['category'];?></td>
	</tr>
	<?php
}
?>
	<tr>
		<td><?php if (!empty($rowB['bild'])) { ?> <a
			href="index.php?action=m_building&show_building=<?php echo $rowB['id']?>&sid=<?php echo $sid;?>"><img
			src="bilder/gebs/<?php echo $rowB['bild'];?>.jpg"></a> <?php } else { ?> <a
			href="index.php?action=m_building&show_building=<?php echo $rowB['id']?>&sid=<?php echo $sid;?>"><img
			src="bilder/gebs/blank.jpg"></a> <?php } ?></td>
		<td><a
			href="index.php?action=m_building&show_building=<?php echo $rowB['id']?>&sid=<?php echo $sid;?>"><?php echo $rowB['name'];?></a>
		</td>
	</tr>
	<?php
}

} else {

	$sqlB = "SELECT * FROM " . $db_tb_gebaeude . " WHERE id = " . $id . " ";
	$resultB = $db->db_query($sqlB)
	or error(GENERAL_ERROR,
             'Could not query config information.', '', 
	__FILE__, __LINE__, $sqlB);
	$rowB = $db->db_fetch_array($resultB);

	$build['name'] = $rowB['name'];
	$build['cat'] = $rowB['category'];
	$build['besch'] = $rowB['info'];
	if (empty($build['besch'])) $build['besch'] = '---';
	$build['kolotyp'] = $rowB['n_kolotyp'];
	$build['planityp'] = $rowB['n_planityp'];

	$d = floor( $rowB['dauer'] / (24*60*60) );
	$h = floor($rowB['dauer'] / (60*60) - $d * 24 );
	$m = floor($rowB['dauer'] / 60 - $d * 24 * 60 - $h * 60 );
	$s =floor( $rowB['dauer'] - $d * 24 * 60 * 60 - $h * 60 * 60 - $m * 60 );

	$build['dauer'] = "";
	if ($d > 0) {
		if ($d == 1) {
			$build['dauer'] = "$d Tag ";
		}
		else {
			$build['dauer'] = "$d Tage ";
		}
	}
	if ($h < 10) {
		$build['dauer'] = $build['dauer'] . "0$h";
	}
	else {
		$build['dauer'] = $build['dauer'] . "$h";
	}
	if ($m < 10) {
		$build['dauer'] = $build['dauer'] . ":0$m";
	}
	else {
		$build['dauer'] = $build['dauer'] . ":$m";
	}

	$typ = $rowB['typ'];
	$max = $rowB['MaximaleAnzahl'];

	if ($typ == 'norma' AND empty($max)) $build['anzahl'] = '';
	if ($typ == 'norma' AND $max > 0) $build['anzahl'] = $max.' (planetar)';
	if ($typ == 'stufe') $build['anzahl'] = '';
	if ($typ == 'pteur') $teuer = 'planetar';
	if ($typ == 'gteur') $teuer = 'global';

	$img = '';
	if (!empty($rowB['bild'])) $img = '<img src="bilder/gebs/'.$rowB['bild'].'.jpg" alt="">';



	?>

	<table class="bordercolor" style="width: 60%;" border="0"
		cellpadding="4" cellspacing="1">
		<tbody>
			<tr>

				<td class="windowbg2" valign="middle" align="center"><?php echo $img;?></td>
				<td class="windowbg2" valign="middle" align="center">
				<div class="doc_title"><span class="bigtext"><?php echo $build['name'];?></span></div>
				</td>
				</td>
			</tr>

			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">Beschreibung:</div>
				</td>
				<td class="windowbg1" valign="top"><?php echo $build['besch'];?></td>
			</tr>
			<?php
			if ($typ != 'pteur' AND $typ != 'gteur') {
				?>
			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">Kosten:</div>
				</td>
				<td class="windowbg1" valign="top"><?php echo $rowB['Kosten'];?></td>
				</td>
			</tr>

			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">Dauer:</div>
				</td>
				<td class="windowbg1" valign="top"><?php echo $build['dauer'];?></td>
				</td>
			</tr>

			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">bringt:</div>
				</td>
				<td class="windowbg1" valign="top"><?php echo $rowB['bringt'];?></td>
				</td>
			</tr>
			<?php
} else {
	?>
			<tr>
				<td class="windowbg1" colspan="2" valign="top"><?php echo $teuer;?> in
				Stufen teurer werdendes Gebäude.</td>
			</tr>
			<?php
}
?>
			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">kostet:</div>
				</td>
				<td class="windowbg1" valign="top"><?php echo $rowB['kostet'];?></td>
				</td>
			</tr>

			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">Highscorepunkte:</div>
				</td>
				<td class="windowbg1" valign="top"><?php echo $rowB['Punkte'];?></td>
				</td>
			</tr>
			<?php if (!empty($build['kolotyp'])) { ?>
			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">benötigter Kolotyp:</div>
				</td>
				<td class="windowbg1" valign="top"><?php echo $build['kolotyp'];?></td>
				</td>
			</tr>
			<?php } ?>
			<?php if (!empty($build['planityp'])) { ?>
			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">benötigter Kolotyp:</div>
				</td>
				<td class="windowbg1" valign="top"><?php echo $build['planityp'];?></td>
				</td>
			</tr>
			<?php } ?>



			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">Benötigte<br>
				Forschungen:</div>
				</td>
				<td class="windowbg1" valign="top"><?php
				$nresearch = preg_split('<br>',$rowB['n_research']);

				foreach ($nresearch as $research) {
					$research = str_replace('(','',$research);
					$research = str_replace(')','',$research);
					$research = str_replace('\n','',$research);
					$research = trim($research);


					$sqlR = "SELECT id FROM " . $db_prefix . "research WHERE name = '" . $research . "' ";
					$resultR = $db->db_query($sqlR)
					or error(GENERAL_ERROR,
             'Could not query config information.', '', 
					__FILE__, __LINE__, $sqlR);
					$rowR = $db->db_fetch_array($resultR);
					$resid = $rowR['id'];

					if (isset($resid) AND !empty($resid)) {

						$sql = "SELECT * FROM " . $db_prefix . "research2user WHERE rid=" . $resid .
	       " AND userid='" . $user_sitterlogin . "'";
						$result = $db->db_query($sql)
						or error(GENERAL_ERROR,
             'Could not query config information.', '',
						__FILE__, __LINE__, $sql);

						if($db->db_num_rows($result) > 0) {
							$colorme_on = " <span class='doc_green'> ";
							$colorme_off = "</span>";
							$isresearched = TRUE;
						} else {
							$colorme_on = "<span class='doc_black'>";
							$colorme_off = "</span>";
							$isresearched = FALSE;
						}

						echo '<img src="bilder/point.gif" alt="a point o.O">';
						echo '&nbsp;';
						echo '<a href="index.php?action=m_research&amp;researchid='.$resid.'&amp;sid='.$sid.'">'.$colorme_on.$research.$colorme_off.'</a>';
						echo '<br>';

					} else {

						if (!empty($research)) {

							echo '<img src="bilder/point.gif" alt="a point o.O">';
							echo '&nbsp;';
							echo " <span class='doc_red'>".$research." </span>";
							echo '<br>';

						}

					}

				}

				?></td>
			</tr>



			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">Ermöglicht<br>
				Forschungen:</div>
				</td>
				<td class="windowbg1" valign="top"><?php
				$nresearch = preg_split('<br>',$rowB['e_research']);

				foreach ($nresearch as $research) {
					$research = str_replace('(','',$research);
					$research = str_replace(')','',$research);
					$research = str_replace('\n','',$research);
					$research = trim($research);

					$sqlR = "SELECT id FROM " . $db_prefix . "research WHERE name = '" . $research . "' ";
					$resultR = $db->db_query($sqlR)
					or error(GENERAL_ERROR,
             'Could not query config information.', '', 
					__FILE__, __LINE__, $sqlR);
					$rowR = $db->db_fetch_array($resultR);
					$resid = $rowR['id'];

					if (isset($resid) AND !empty($resid)) {

						$sql = "SELECT * FROM " . $db_prefix . "research2user WHERE rid=" . $resid .
	       " AND userid='" . $user_sitterlogin . "'";
						$result = $db->db_query($sql)
						or error(GENERAL_ERROR,
             'Could not query config information.', '',
						__FILE__, __LINE__, $sql);

						if($db->db_num_rows($result) > 0) {
							$colorme_on = " <span class='doc_green'> ";
							$colorme_off = "</span>";
							$isresearched = TRUE;
						} else {
							$colorme_on = "<span class='doc_black'>";
							$colorme_off = "</span>";
							$isresearched = FALSE;
						}

						if (!empty($research)) {
							echo '<img src="bilder/point.gif" alt="a point o.O">';
							echo '&nbsp;';
							echo '<a href="index.php?action=m_research&amp;researchid='.$resid.'&amp;sid='.$sid.'">'.$colorme_on.$research.$colorme_off.'</a>';
							echo '<br>';
						}

					} else {

						if (!empty($research)) {

							if (!empty($research)) {
								echo '<img src="bilder/point.gif" alt="a point o.O">';
								echo '&nbsp;';
								echo " <span class='doc_red'>".$research." </span>";
								echo '<br>';

							}

						}

					}


				}

				?></td>
			</tr>

			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">Benötigte<br>
				Gebäude:</div>
				</td>
				<td class="windowbg1" valign="top"><?php
				$ngebaeude = preg_split('<br>',$rowB['n_building']);

				foreach ($ngebaeude as $gebaeude) {
					$gebaeude = str_replace('[','(',$gebaeude);
					$gebaeude = str_replace(']',')',$gebaeude);
					$gebaeude = str_replace('\n','',$gebaeude);
					$gebaeude = trim($gebaeude);

					$sqlR = "SELECT id FROM " . $db_prefix . "gebaeude WHERE name = '" . $gebaeude . "' ";
					$resultR = $db->db_query($sqlR)
					or error(GENERAL_ERROR,
             'Could not query config information.', '', 
					__FILE__, __LINE__, $sqlR);
					$rowR = $db->db_fetch_array($resultR);
					$resid = $rowR['id'];

					if (!empty($gebaeude)) {

						echo '<img src="bilder/point.gif" alt="a point o.O">';
						echo '&nbsp;';

						echo '<a href="index.php?action=m_building&amp;show_building='.$resid.'&amp;sid='.$sid.'">'.$gebaeude.'</a>';

						echo '<br>';

					}


				}



				?></td>
			</tr>

			<tr>
				<td class="windowbg2" style="width: 20%;" valign="top">
				<div class="doc_blue">Ermöglicht<br>
				Gebäude:</div>
				</td>
				<td class="windowbg1" valign="top"><?php
				$ngebaeude = preg_split('<br>',$rowB['e_building']);

				foreach ($ngebaeude as $gebaeude) {
					$gebaeude = str_replace('[','(',$gebaeude);
					$gebaeude = str_replace(']',')',$gebaeude);
					$gebaeude = str_replace('\n','',$gebaeude);
					$gebaeude = trim($gebaeude);

					$sqlR = "SELECT id FROM " . $db_prefix . "gebaeude WHERE name = '" . $gebaeude . "' ";
					$resultR = $db->db_query($sqlR)
					or error(GENERAL_ERROR,
             'Could not query config information.', '', 
             __FILE__, __LINE__, $sqlR);
	$rowR = $db->db_fetch_array($resultR);
      $resid = $rowR['id'];

if (!empty($gebaeude)) {

echo '<img src="bilder/point.gif" alt="a point o.O">';
echo '&nbsp;';

echo '<a href="index.php?action=m_building&amp;show_building='.$resid.'&amp;sid='.$sid.'">'.$gebaeude.'</a>';

echo '<br>';  

}

}

?></td>
			</tr>
	
	</table>



	<?php

}

?>