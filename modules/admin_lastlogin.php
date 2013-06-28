<?php
/*****************************************************************************
 * admin_lastlogin.php                                                       *
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

doc_title("Admin Loginzeit");

?>
<table class='tablesorter-blue'>
	<thead>
		<tr>
			<th>
				<b>Username</b>
			</th>
			<th>
				<b>letzter Login</b>
			</th>
			<th>
			</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql = "SELECT `id`, `logindate`, `password` FROM `{$db_tb_user}` ORDER BY `logindate`, `id`";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		while ($row = $db->db_fetch_array($result)) {
			$nopassword    = (empty($row['password'])) ? " kein Passwort gesetzt" : "";
			$logindate     = $row['logindate'];
			$lastlogindate = (empty($logindate)) ? "noch nie"
				: strftime(CONFIG_DATETIMEFORMAT, $logindate);
			$lastloggedon  = (empty($logindate))
				? ""
				: floor((CURRENT_UNIX_TIME - $logindate) / DAY) . " Tage her";
			?>
			<tr>
				<td>
					<?php
					echo "<a href='index.php?action=profile&id=" . urlencode($row['id']) . "'>" . $row['id'] . "</a>";
					?>
				</td>
				<td>
					<?php
					echo $lastlogindate;
					?>
				</td>
				<td>
					<?php
					echo $lastloggedon . $nopassword;
					?>
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
</table>
<?php
$db->db_free_result($result);
?>