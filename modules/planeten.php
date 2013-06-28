<?php
/*****************************************************************************
 * planeten.php                                                              *
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

doc_title('Planetenliste');
?>

<table data-sortlist="[[0,0],[1,0]]" class="tablesorter-blue" style="width: 90%;">
	<thead>
		<tr>
			<th>
				<b>Koordinaten</b>
			</th>
			<th>
				<b>Username</b> 
			</th>
			<th>
				<b>Planetenname</b>
           </th>
			<th>
				<b>Spielart</b>
           </th>
		</tr>
	</thead>
	</tbody>
	<?php
    $sql = "SELECT * FROM " . $db_tb_scans . " AS t1 INNER JOIN " . $db_tb_user . " AS t2 WHERE t1.user=t2.sitterlogin";
    if (!$user_fremdesitten) {
		$sql .= " AND t2.allianz='" . $user_allianz . "'";
    }
    $sql .= " AND t2.sitterlogin<>''";
    
    $result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $sitpre = "";
    $num = 0;

    while ($row = $db->db_fetch_array($result)) {
        if ($row['sitterlogin'] != $sitpre) {
            $num    = ($num == 1) ? 2 : 1;
            $sitpre = $row['sitterlogin'];
        }
    ?>
		<tr>
			<td class="left">
				<a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=auto"><?php echo $row['coords'];?></a>
			</td>
			<td class="left">
				<?php
                if ($user_status == "admin") {
					echo "<a href='index.php?action=profile&sitterlogin=" . urlencode($row['sitterlogin']) . "'>" . $row['sitterlogin'] . "</a>";
                } else {
                    echo $row['sitterlogin'];
                }
                ?>
			</td>
			<td class="left">
				<a href="index.php?action=showplanet&coords=<?php echo $row['coords'];?>&ansicht=auto">
				<div class='doc_<?php
					if ($row['objekt'] == "Kolonie") {
						echo "black";
					} else if ($row['objekt'] == "Kampfbasis") {
						echo "red";
					} else if ($row['objekt'] == "Sammelbasis") {
						echo "green";
					} else if ($row['objekt'] == "Artefaktbasis") {
						echo "blue";
					} else {
						echo "'black'";
					}
					?>'><?php echo $row['planetenname'];?> (<?php
					if ($row['objekt'] == "Kolonie") {
						echo "K";
					} else if ($row['objekt'] == "Kampfbasis") {
						echo "B";
					} else if ($row['objekt'] == "Sammelbasis") {
						echo "S";
					} else if ($row['objekt'] == "Artefaktbasis") {
						echo "A";
					} else {
						echo "-";
					}
					?>)
              </div>
              </a>
			</td>
			<td class="left">
				<?php echo $row['budflesol']; echo ($row['buddlerfrom']) ? " von: " . $row['buddlerfrom'] : "";?>
			</td>
		</tr>
	<?php
    }
    ?>
	</tbody>
</table>
<br/>
<b>K = Kolonie, B = Kampfbasis, S = Sammelbasis, A = Artefaktbasis</b>