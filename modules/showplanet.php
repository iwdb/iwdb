<?php
/*****************************************************************************/
/* showplanet.php                                                            */
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

global $sid,$db;

function makeduration($time) {
	if (empty($time))
		return '---';
	$duration = $time - time();
	$text = "";
	$days = (int)($duration / (24 * 60 * 60));
	$duration -= $days * 24 * 60 * 60;
	$hours = (int)($duration / (60 * 60));
	$duration -= $hours * 60 * 60;
	$mins = (int)($duration / 60);
	$duration -= $mins * 60;
	$secs = $duration;
	if ($days)
		$text .= $days . " Tagen ";
	$text .= (($hours < 10) ? "0" . $hours : $hours) . ":";
	$text .= (($mins < 10) ? "0" . $mins : $mins) . ":";
	$text .= (($secs < 10) ? "0" . $secs : $secs);
	return $text;
}

?>

<div class='doc_title'>Planet</div>
<?php
$ansicht = getVar('ansicht');
$ansicht = ( empty($ansicht) ) ? "auto": $ansicht;
if (! isset($coords) ) $coords  = getVar('coords');
$order   = getVar('order');

if ( ! empty($coords) )
{
	$sql = "SELECT * FROM " . $db_tb_scans . " WHERE coords='" . $coords . "' " . $order;
	$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	$editplanet  = getVar('editplanet');
	$reservieren = getVar('reservieren');

	if ( ( ! empty($editplanet) ) && ( empty($row['reserviert']) || $row['reserviert'] == $user_sitterlogin || $user_status == "admin") )
	{
		$row['reserviert'] = ( empty($reservieren) ) ? "": $user_sitterlogin;
		$sql = "UPDATE " . $db_tb_scans . " SET reserviert='" . $row['reserviert'] . "' WHERE coords = '" . $coords . "'";
		$result_planetedit = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		echo "<div class='system_notification'>Planetenreservierung geändert.</div>";
	}

	$sql = "SELECT status FROM " . $db_tb_allianzstatus . " WHERE allianz LIKE '" . $row['allianz'] . "'";
	$result_status = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_status = $db->db_fetch_array($result_status);
	if(isset($config_allianzstatus[$row_status['status']])) {
		$color = $config_allianzstatus[$row_status['status']];
	} else {
		$color = "white";
	}

	$rating = rating($row);
	//	print_r($row);
	
	if ($row['dgmod']==0) {
		$eisen_effektiv=0;
		$chemie_effektiv=0;
		$eis_effektiv=0;
		$ttchemie_effektiv=0;
		$tteisen_effektiv=0;
		$tteis_effektiv=0;
	}
	else {
		$eisen_effektiv=$row['eisengehalt']/$row['dgmod'];
		$tteisen_effektiv=$row['tteisen']/$row['dgmod'];
		$chemie_effektiv=$row['chemievorkommen']/$row['dgmod'];
		$ttchemie_effektiv=$row['ttchemie']/$row['dgmod'];
		$eis_effektiv=$row['eisdichte']/$row['dgmod'];
		$tteis_effektiv=$row['tteis']/$row['dgmod'];
	}
		
}
?>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor"
	style="width: 80%;">
	<?php
	if ( $user_planibilder == "1" )
	{
		$path = "bilder/planeten/200x200/";
		switch ($row['typ'])
		{
			case "Steinklumpen":
				$path .= "stein/st_" . str_pad(mt_rand(1, 53), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Eisplanet":
				$path .= "eis/eis_" . str_pad(mt_rand(1, 34), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Gasgigant":
				$path .= "gas/gas_" . str_pad(mt_rand(1, 30), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Asteroid":
				$path .= "asteroiden/ast_" . str_pad(mt_rand(1, 45), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Nichts":
				$path .= "nix/nix_" . str_pad(mt_rand(1, 4), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
			case "Sonne":
				$path .= "sonne.jpg"; break;
			default:
				$path .= "bes/bes_" . str_pad(mt_rand(1, 20), 2, "0", STR_PAD_LEFT) . ".jpg"; break;
		}
		if ( $row['objekt'] == "Schwarzes Loch" ) $path = 'bilder/planeten/200x200/schwarzesloch.jpg';
		?>
	<tr>
		<td colspan="2" align="center"><img src="<?php echo $path;?>" border="0"
			alt="<?php echo $row['typ']?>"></td>
	</tr>
	<?php
}
?>
	<tr>
		<td colspan="2" class="titlebg"><b>Daten:</b></td>
	</tr>
	<tr>
		<td class="windowbg2" style="width: 20%;">Koordinaten:</td>
		<td class="windowbg1"><?php echo $row['coords'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">letztes Update:</td>
		<td class="windowbg1"><?php
		echo (empty($row['time']) ) ? "/": round((time() - $row['time']) / (24 * 60 * 60)) . " Tage";
		?></td>
	</tr>
	<?php
	if ( ( ( $ansicht == "auto") && ( $row['objekt'] != "---" ) ) || ( $ansicht == "taktisch") || ( $ansicht == "beide") )
	{
		?>
	<tr>
		<td class="windowbg2">Spielername:</td>
		<td class="windowbg1"><?php echo $row['user'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Allianz:</td>
		<td class="windowbg1" style=" background-color: <?php echo $color;?>;"><a
			href="index.php?action=showgalaxy&allianz=<?php echo $row['allianz'];?>&sid=<?php echo $sid;?>"><?php echo $row['allianz'];?><?php echo ( ( empty($row_status['status']) ) || ( $row_status['status'] == 'own' ) ) ? "": " (" . $row_status['status'] . ")";?></a>
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Planetennamen:</td>
		<td class="windowbg1"><?php echo $row['planetenname'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Punkte:</td>
		<td class="windowbg1"><?php echo $row['punkte'];?></td>
	</tr>
	<?php
}
?>
	<tr>
		<td colspan="2" class="titlebg"><b>Eigenschaften:</b></td>
	</tr>
	<tr>
		<td class="windowbg2">Planetentyp:</td>
		<td class="windowbg1"><?php echo $row['typ'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Objekttyp:</td>
		<td class="windowbg1"><?php echo $row['objekt'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Planetengröße:</td>
		<td class="windowbg1"><?php echo $row['bevoelkerungsanzahl'];?></td>
	</tr>
	<tr>
		<td colspan="2" class="titlebg"><b>Notizen:</b></td>
	</tr>
	<tr>
		<td class="windowbg2"><i>Hier bitte jegliche Informationen ueber diesen Planeten, sei es Raids, Uhrzeiten, Absprachen, Tipps fuer Raider eingeben.</i></td>
		<td class="windowbg1"><?php
		$notice = getVar('notice');
		$submitnotice = getVar('submitnotice');

		//Notizen eintragen
		if ($submitnotice == "Speichern"){
		  $sql = "UPDATE ".$db_tb_scans . " SET rnb='" . $notice . 
		         "' WHERE coords='" . $coords . "'";
		  $result = $db->db_query($sql)
		  or error(GENERAL_ERROR, 
		           'Could not query config information.', '', 
		           __FILE__, __LINE__, $sql);
		}

		//Notizen aufrufen
		  $sql = "SELECT rnb FROM " . $db_tb_scans . 
		         " WHERE coords='" . $coords . "'";
		  $result = $db->db_query($sql)
		    or error(GENERAL_ERROR, 
		             'Could not query config information.', '', 
		             __FILE__, __LINE__, $sql);
		  $result = $db->db_fetch_array($result);
		  $notice = $result["rnb"];

		echo "<form method='POST' action='index.php?action=showplanet&coords=".$coords."&sid=".$sid."&ansicht=auto' enctype='multipart/form-data'>";
		echo "  <table border='0' cellpadding='5' cellspacing='0' class='bordercolor' style='width: 80%;' align='center'>";
		echo "    <tr>";
		echo "      <td class='windowbg2' align='center'>";
		echo "<textarea name='notice' rows='10' cols='80'>".$notice."</textarea>";
		echo "      </td>";
		echo "    </tr>";
		echo "    <tr>";
		echo "      <td class='titlebg' align='center'>";
		echo "        <input type='submit' name='submitnotice' value='Speichern' class='submit'>";
		echo "        &nbsp;&nbsp;";
		echo "        <input type='reset' class='submit'>";
		echo "      </td>";
		echo "    </tr>";
		echo "  </table>";
		echo "</form>";
		?></td>
	</tr>
	<?php
	if ( ( ( $ansicht == "auto") && ( $row['objekt'] == "---" ) ) || ( $ansicht == "geologisch") || ( $ansicht == "beide") )
	{
		?>
	<tr>
		<td class="windowbg2">Gravitation:</td>
		<td class="windowbg1"><?php echo $row['gravitation'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Forschungsmod.:</td>
		<td class="windowbg1"><?php echo ($row['fmod'] < 100) ? "<div class='doc_red'>" . $row['fmod'] . " %</div>": $row['fmod'] . ' %';?>
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Gebäudekostenmod.:</td>
		<td class="windowbg1"><?php echo ($row['kgmod'] > 1) ? "<div class='doc_red'>" . $row['kgmod'] . "</div>": $row['kgmod'];?>
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Gebäudedauermod.:</td>
		<td class="windowbg1"><?php echo ($row['dgmod'] > 1) ? "<div class='doc_red'>" . $row['dgmod'] . "</div>": $row['dgmod'];?>
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Schiffskostenmod.:</td>
		<td class="windowbg1"><?php echo ($row['ksmod'] > 1) ? "<div class='doc_red'>" . $row['ksmod'] . "</div>": $row['ksmod'];?>
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Schiffsdauermod.:</td>
		<td class="windowbg1"><?php echo ($row['dsmod'] > 1) ? "<div class='doc_red'>" . $row['dsmod'] . "</div>": $row['dsmod'];?>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="titlebg"><b>Ressourcen:</b></td>
	</tr>
	<tr>
		<td class="windowbg2">Eisengehalt:</td>
		<td class="windowbg1"><?php echo ($row['eisengehalt'] > 100) ? "<b>" . $row['eisengehalt'] . " %</b>": $row['eisengehalt'] . ' %';?>
		<?php echo  "mit TechTeam: " ?> <?php echo ($row['tteisen'] > 130) ? "<b>" . $row['tteisen'] . "%</b>": $row['tteisen'] . "%";?>
		<?php echo  "=> effektiv Eisen: " ?> <?php echo ($eisen_effektiv) ? "<b>" . $eisen_effektiv . "%</b>": $eisen_effektiv . "%";?>
		<?php echo  "mit TechTeam: " ?> <?php echo ($tteisen_effektiv) ? "<b>" . $tteisen_effektiv . "%</b>": $tteisen_effektiv . "%";?>
		</td>
	
	</tr>
	<tr>
		<td class="windowbg2">Chemievorkommen:</td>
		<td class="windowbg1"><?php echo ($row['chemievorkommen'] > 100) ? "<b>" . $row['chemievorkommen'] . " %</b>": $row['chemievorkommen'] . ' %';?>
		<?php echo  "mit TechTeam: " ?> <?php echo ($row['ttchemie'] > 130) ? "<b>" . $row['ttchemie'] . "%</b>": $row['ttchemie'] . "%";?>
		<?php echo  "=> effektiv Chemie: " ?> <?php echo ($chemie_effektiv) ? "<b>" . $chemie_effektiv . "%</b>": $chemie_effektiv . "%";?>
		<?php echo  "mit TechTeam: " ?> <?php echo ($ttchemie_effektiv) ? "<b>" . $ttchemie_effektiv . "%</b>": $ttchemie_effektiv . "%";?>
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Eisdichte:</td>
		<td class="windowbg1"><?php echo ($row['eisdichte'] > 30) ? "<b>" . $row['eisdichte'] . " %</b>": $row['eisdichte'] . ' %';?>
		<?php echo  "mit TechTeam: " ?> <?php echo ($row['tteis'] > 30) ? "<b>" . $row['tteis'] . "%</b>": $row['tteis'] . "%";?>
		<?php echo  "=> effektiv Eis: " ?> <?php echo ($eis_effektiv) ? "<b>" . $eis_effektiv . "%</b>": $eis_effektiv . "%";?>
		<?php echo  "mit TechTeam: " ?> <?php echo ($tteis_effektiv) ? "<b>" . $tteis_effektiv . "%</b>": $tteis_effektiv . "%";?>
		</td>
	</tr>
	<tr>
		<td class="windowbg2">Lebensbedingungen:</td>
		<td class="windowbg1"><?php echo ($row['lebensbedingungen'] > 100) ? "<b>" . $row['lebensbedingungen'] . " %</b>": $row['lebensbedingungen'] . ' %';?>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="titlebg"><b>Besonderheiten:</b></td>
	</tr>
	<tr>
		<td colspan="2" class="windowbg2"><?php echo  !empty($row['besonderheiten']) ? str_replace(", ", "<br>", $row['besonderheiten']): "keine *moep*";?>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="titlebg"><b>Sprengung:</b></td>
	</tr>
	<tr>
		<td colspan="2" class="windowbg2">
Dieser Planet wird vorraussichtlich in <?php echo  makeduration($row['geoscantime'] + $row['reset_timestamp']) ?> (plusminus 24h) für den Bau einer Hyperraumumgehungsstraße gesprengt.
		</td>
	</tr>
	<?php
}
if ( $row['objekt'] == "---" )
{
	?>
	<tr>
		<td colspan="2" class="titlebg"><b>Rating:</b></td>
	</tr>
	<tr>
		<td colspan="2" class="windowbg2" align="center"><b><?php echo (!empty($rating) ? "<div class='doc_big_black'>" . $rating : "<div class='doc_red'>Kein Rating berechenbar, neuer Geoscan erforderlich");?>
		</div>
		</b></td>
	</tr>
	<tr>
		<td colspan="2" class="titlebg"><b>Reservieren:</b></td>
	</tr>
	<tr>
		<td colspan="2" class="windowbg2" align="center">
		<form method="POST"
			action="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&sid=<?php echo $sid;?>"
			enctype="multipart/form-data"><?php
			if ( empty($row['reserviert']) )
			echo "Diesen Planeten für dich reservieren? <input type=\"checkbox\" name=\"reservieren\"><input type=\"hidden\" name=\"editplanet\" value=\"true\"> <input type=\"submit\" value=\"speichern\" name=\"B1\" class=\"submit\">";
			elseif ( ( isset($user_sitterlogin) ) && ( $row['reserviert'] == $user_sitterlogin ) )
			echo "Diesen Planeten für dich reservieren? <input type=\"checkbox\" name=\"reservieren\" class=\"checkbox\" checked><input type=\"hidden\" name=\"editplanet\" value=\"true\"> <input type=\"submit\" value=\"speichern\" name=\"B1\" class=\"submit\">";
			else
			{
				echo "Dieser Planet ist für " . $row['reserviert'] . " reserviert. Bitte besiedel ihn nicht.";
				if ( ( isset($user_status) ) && ( $user_status == "admin" ) )
				{
					echo "<br>Ändern? <input type=\"checkbox\" name=\"reservieren\" class=\"checkbox\" checked><input type=\"hidden\" name=\"editplanet\" value=\"true\"> <input type=\"submit\" value=\"speichern\" name=\"B1\" class=\"submit\">";
				}
			}
			?></form>
		</td>
	</tr>
	<?php
}
if ( ( ( $ansicht == "auto") && ( $row['objekt'] != "---" ) ) || ( $ansicht == "taktisch") || ( $ansicht == "beide") )
{
	$class1 = $row['eisen'] + 2 * $row['stahl'] + 4 * $row['vv4a'] + 3 * $row['chemie'];
	$class2 = 2 * $row['eis'] + 2 * $row['wasser'] + $row['energie'];
	?>
	<tr>
		<td colspan="2" class="titlebg"><b>auf Lager:</b></td>
	</tr>
	<tr>
		<td class="windowbg2">Eisen:</td>
		<td class="windowbg1"><?php echo $row['eisen'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Stahl:</td>
		<td class="windowbg1"><?php echo $row['stahl'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">VV4A:</td>
		<td class="windowbg1"><?php echo $row['vv4a'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Chemie:</td>
		<td class="windowbg1"><?php echo $row['chemie'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Eis:</td>
		<td class="windowbg1"><?php echo $row['eis'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Wasser:</td>
		<td class="windowbg1"><?php echo $row['wasser'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Energie:</td>
		<td class="windowbg1"><?php echo $row['energie'];?></td>
	
	
	<tr>
		<td colspan="2" class="titlebg"><b>benötigte Frachtkapazität:</b></td>
	</tr>
	<tr>
		<td class="windowbg2">Klasse 1:</td>
		<td class="windowbg1"><?php echo $class1;?></td>
	</tr>
	<tr>
		<td class="windowbg2">Klasse 2:</td>
		<td class="windowbg1"><?php echo $class2;?></td>
	</tr>

	<?php
	if ( ! empty($row['lager_chemie']) AND ! empty($row['lager_eis']) AND ! empty($row['lager_energie']) )
	{
		?>


	<tr>
		<td colspan="2" class="titlebg"><b>Lagerkapazität:</b></td>
	</tr>
	<tr>
		<td class="windowbg2">Lager Chemie:</td>
		<td class="windowbg1"><?php echo $row['lager_chemie'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Lager Eis:</td>
		<td class="windowbg1"><?php echo $row['lager_eis'];?></td>
	</tr>
	<tr>
		<td class="windowbg2">Lager Energie:</td>
		<td class="windowbg1"><?php echo $row['lager_energie'];?></td>
	</tr>

	<?php
}
?>


<?php
if ( ! empty($row['geb']) )
{
	?>
	<tr>
		<td colspan="2" class="titlebg"><b>Gebäude:</b></td>
	</tr>
	<tr>
		<td class="windowbg1" colspan="2"><?php echo $row['geb'];?></td>
	</tr>
	<?php
}
?>

<?php
if ( ! empty($row['plan']) )
{
	?>
	<tr>
		<td colspan="2" class="titlebg"><b>planetare Flotte:</b></td>
	</tr>
	<tr>
		<td class="windowbg1" colspan="2"><?php echo $row['plan'];?></td>
	</tr>
	<?php
}
?>

<?php
if ( ! empty($row['stat']) )
{
	?>
	<tr>
		<td colspan="2" class="titlebg"><b>stationierte Flotte:</b></td>
	</tr>
	<tr>
		<td class="windowbg1" colspan="2"><?php echo $row['stat'];?></td>
	</tr>
	<?php
}
?>

<?php
if ( ! empty($row['def']) )
{
	?>
	<tr>
		<td colspan="2" class="titlebg"><b>Verteidigung:</b></td>
	</tr>
	<tr>
		<td class="windowbg1" colspan="2"><?php echo $row['def'];?></td>
	</tr>

	<?php
}
?>



<?php
}
?>
</table>
