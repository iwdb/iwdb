<?php
/*****************************************************************************
 * admin_ressbedarf.php                                                      *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * ========================================================================= *
 * Copyright (c) 2004 Robert Riess - All Rights Reserved                     *
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

doc_title("Ressbedarf Member");

global $db, $db_tb_scans, $db_tb_user;

$editress = getVar('editress');
$spieler = getVar('spieler');
if (!empty($editress)) {
    doc_message("Ressbedarf für " . $spieler . " aktualisiert.");
	echo '</div><br>';
}

?>
<form method="POST" action="index.php?action=admin&uaction=ressbedarf" enctype="multipart/form-data">
<?php
	if (!empty($editress)) {
				
		$sql = "SELECT `coords`, `planetenname`, `typ`, `bed_eisen`, `bed_stahl`, `bed_vv4a`, `bed_chemie`, `bed_eis`, `bed_wasser`, `bed_energie`, `bed_bev` FROM `{$db_tb_scans}` WHERE (`user` LIKE '" . $spieler . "' AND `objekt` = 'Kolonie')";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not update planidata information.', '', __FILE__, __LINE__, $sql);
	
		while ($row = $db->db_fetch_array($result)) {
			$coords_bed_eisen 	= getVar($row['coords'] . '_bed_eisen');
			$coords_bed_stahl 	= getVar($row['coords'] . '_bed_stahl');
			$coords_bed_vv4a 	= getVar($row['coords'] . '_bed_vv4a');
			$coords_bed_chemie 	= getVar($row['coords'] . '_bed_chemie');
			$coords_bed_eis 	= getVar($row['coords'] . '_bed_eis');
			$coords_bed_wasser 	= getVar($row['coords'] . '_bed_wasser');
			$coords_bed_energie	= getVar($row['coords'] . '_bed_energie');
			$coords_bed_bev		= getVar($row['coords'] . '_bed_bev');
	
			$data = array(
				'bed_eisen'   	=> $coords_bed_eisen,
				'bed_stahl'   	=> $coords_bed_stahl,
				'bed_vv4a'    	=> $coords_bed_vv4a,
				'bed_chemie'  	=> $coords_bed_chemie,
				'bed_eis'     	=> $coords_bed_eis,
				'bed_wasser'  	=> $coords_bed_wasser,
				'bed_energie'	=> $coords_bed_energie,
				'bed_bev'		=> $coords_bed_bev
			);
						
			$db->db_update($db_tb_scans, $data, "WHERE `coords`='" . $row['coords'] ."'")
				or error(GENERAL_ERROR, 'Could not update ressbedarf information.', '', __FILE__, __LINE__);
		}
	}
	?>
	<table data-sortlist="[[2,1],[0,0]]" class='tablesorter-blue' style="width: 95%;">
		<thead>
			<tr>
				<th>
					Koordinaten
				</th>
				<th>
					Planetenname
				</th>
				<th>
					Typ
				</th>
				<th data-sorter="false">
					Bedarf Eisen
				</th>
				<th data-sorter="false">
					Bedarf Stahl
				</th>
				<th data-sorter="false">
					Bedarf VV4A
				</th>
				<th data-sorter="false">
					Bedarf Chemie
				</th>
				<th data-sorter="false">
					Bedarf Eis
				</th>
				<th data-sorter="false">
					Bedarf Wasser
				</th>
				<th data-sorter="false">
					Bedarf Energie
				</th>
				<th data-sorter="false">
					Bedarf Bevölkerung
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			
			// aktuelle Spielerauswahl ermitteln
			$params['playerSelection'] = getVar('playerSelection');

			// Auswahlarray zusammenbauen
			$playerSelectionOptions = array();
			$playerSelectionOptions['Member auswählen'] = 'Member auswählen';
			$playerSelectionOptions += getAllyAccs();

			// Spielerauswahl Dropdown erstellen
			echo "<div class='playerSelectionbox'>";
			echo "Auswahl: ";
			echo makeField(
				array(
					"type"   	=> 'select',
					"values" 	=> $playerSelectionOptions,
					"value"  	=> $params['playerSelection'],
					"onchange" 	=> "location.href='index.php?action=admin&uaction=ressbedarf&amp;playerSelection='+this.options[this.selectedIndex].value",
				), 'playerSelection'
			);
			echo '</div><br>';
			
			$sql = "SELECT `coords`, `planetenname`, `typ`, `bed_eisen`, `bed_stahl`, `bed_vv4a`, `bed_chemie`, `bed_eis`, `bed_wasser`, `bed_energie`, `bed_bev` FROM `{$db_tb_scans}` WHERE (`user` LIKE '" . $params['playerSelection'] . "' AND `objekt` = 'Kolonie')";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not update planidata information.', '', __FILE__, __LINE__, $sql);
			
			while ($row = $db->db_fetch_array($result)) {
				?>
				<tr>
					<td class="windowbg1">
						<?php echo $row['coords'];?>
					</td>
				<td class="windowbg1">
						<?php echo $row['planetenname'];?>
					</td>
				<td class="windowbg1">
						<?php echo $row['typ'];?>
					</td>
				<td class="windowbg1">
						<input type="text" name="<?php echo $row['coords'];?>_bed_eisen" value="<?php echo $row['bed_eisen'];?>" style="width: 5em">
					</td>
					<td class="windowbg1">
						<input type="text" name="<?php echo $row['coords'];?>_bed_stahl" value="<?php echo $row['bed_stahl'];?>" style="width: 5em">
					</td>
					<td class="windowbg1">
						<input type="text" name="<?php echo $row['coords'];?>_bed_vv4a" value="<?php echo $row['bed_vv4a'];?>" style="width: 5em">
					</td>
					<td class="windowbg1">
						<input type="text" name="<?php echo $row['coords'];?>_bed_chemie" value="<?php echo $row['bed_chemie'];?>" style="width: 5em">
					</td>
					<td class="windowbg1">
						<input type="text" name="<?php echo $row['coords'];?>_bed_eis" value="<?php echo $row['bed_eis'];?>" style="width: 5em">
					</td>
					<td class="windowbg1">
						<input type="text" name="<?php echo $row['coords'];?>_bed_wasser" value="<?php echo $row['bed_wasser'];?>" style="width: 5em">
					</td>
					<td class="windowbg1">
						<input type="text" name="<?php echo $row['coords'];?>_bed_energie" value="<?php echo $row['bed_energie'];?>" style="width: 5em">
					</td>
					<td class="windowbg1">
						<input type="text" name="<?php echo $row['coords'];?>_bed_bev" value="<?php echo $row['bed_bev'];?>" style="width: 5em">
					</td>
				</tr>
				
			<?php
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="11" class="titlebg center">
					<input type='hidden' name='spieler' value='<?php echo $params['playerSelection']; ?>'>
					<input type='hidden' name='editress' value='true'>
					<input type='submit' value='speichern' name='B1' class='submit'>
				</th>
			</tr>
		</tfoot>
	</table>	
</form>