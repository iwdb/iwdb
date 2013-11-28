<?php
/*****************************************************************************
 * profile_editress.php                                       	             *
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

//****************************************************************************
doc_title('Ressbedarf für Planeten von '.$id);

global $db, $db_tb_scans;

if (!empty($sitterlogin)) {

    $editplaneten = getVar('editplaneten');
    if (!empty($editplaneten)) {
        echo "<div class='system_notification'>Ressbedarf der Planeten aktualisiert.</div>";
    }
    ?>
    <br>
    <form method="POST" action="index.php?action=profile&uaction=editress" enctype="multipart/form-data">
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
				// Ausgabe der Presets und Löschlink //
				$sql = "SELECT `coords`, `planetenname`, `typ`, `bed_eisen`, `bed_stahl`, `bed_vv4a`, `bed_chemie`, `bed_eis`, `bed_wasser`, `bed_energie`, `bed_bev` FROM `{$db_tb_scans}` WHERE (`user` LIKE '" . $sitterlogin . "' AND `objekt` = 'Kolonie')";
	
				$result = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not update planidata information.', '', __FILE__, __LINE__, $sql);
				while ($row = $db->db_fetch_array($result)) {
					if (!empty($editplaneten)) {
						
						$temp         = $row['coords'] . '_bed_eisen';
						$coords_bed_eisen = getVar($temp);
						
						$temp         = $row['coords'] . '_bed_stahl';
						$coords_bed_stahl = getVar($temp);
						
						$temp         = $row['coords'] . '_bed_vv4a';
						$coords_bed_vv4a = getVar($temp);
						
						$temp         = $row['coords'] . '_bed_chemie';
						$coords_bed_chemie = getVar($temp);
						
						$temp         = $row['coords'] . '_bed_eis';
						$coords_bed_eis = getVar($temp);
						
						$temp         = $row['coords'] . '_bed_wasser';
						$coords_bed_wasser = getVar($temp);
						
						$temp         = $row['coords'] . '_bed_energie';
						$coords_bed_energie = getVar($temp);
						
						$temp         = $row['coords'] . '_bed_bev';
						$coords_bed_bev = getVar($temp);
						
						$sql = "UPDATE `{$db_tb_scans}` SET `bed_eisen`='" . $coords_bed_eisen . "', `bed_stahl`='" . $coords_bed_stahl . "', `bed_vv4a`='" . $coords_bed_vv4a . "', `bed_chemie`='" . $coords_bed_chemie . "', `bed_eis`='" . $coords_bed_eis . "', `bed_wasser`='" . $coords_bed_wasser . "', `bed_energie`='" . $coords_bed_energie . "', `bed_bev`='" . $coords_bed_bev . "'  WHERE (`coords`='" . $row['coords'] . "' AND `user` LIKE '" . $sitterlogin . "')";
						$result_planet = $db->db_query($sql)
							or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
					}
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
						<input type="hidden" name="sitterlogin" value="<?php echo $sitterlogin;?>">
						<input type="submit" value="speichern" name="editplaneten">
					</th>
				</tr>
			</tfoot>
		</table>
	</form>
<br>
<?php
} else {
    doc_message('Kein IW Account angegeben, kein Einstellen der Planeten möglich.');
}