<?php
/*****************************************************************************
 * admin_wronglogin.php                                                      *
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

doc_title("falsche Logins");

$user = getVar('user');
if (!empty($user)) {
    $sql = "DELETE FROM " . $db_tb_wronglogin . " WHERE user='" . $user . "'";
    $result = $db->db_query($sql);
    doc_message("Loginsperre gelöscht");
}

echo "<br>\n";
?>
<table class='tablesorter-blue'>
	<thead>
		<tr>
			<th>
				<b>Username</b>
			</th>
			<th>
				<b>IPs / Zeit</b>
			</th>
			<th>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql = "SELECT `user` FROM `{$db_tb_wronglogin}` GROUP BY `user`";
		$result = $db->db_query($sql);

		while ($row = $db->db_fetch_array($result)) {
			?>
			<tr>
				<td>
					<?php
					echo $row['user'];
					?>
				</td>
				<td>
					<?php
					$sql = "SELECT `ip`, `date` FROM `{$db_tb_wronglogin}` WHERE `user` = '" . $row['user'] . "'";
					$result_ip = $db->db_query($sql);

					while ($row_ip = $db->db_fetch_array($result_ip)) {
						echo "<b>" . $row_ip['ip'] . "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
							strftime("%H:%M:%S am %d.%m.", $row_ip['date']) . "<br>\n";
					}
					?>
				</td>
				<td>
					<?php
					echo "<a href='index.php?action=admin&uaction=wronglogin&user=" . urlencode($row['user']) . "' onclick=\"return confirm('Loginsperre wirklich löschen?')\"><img src='".BILDER_PATH."file_delete_s.gif' alt='löschen'></a>\n";
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
</table>