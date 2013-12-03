<?php

function getIncomingsTables()
{
    global $db, $db_tb_incomings, $db_tb_kb, $db_tb_kb_bomb;

    ob_start();

    //Löschen der Einträge in der Tabelle incomings, es sollen nur aktuelle Sondierungen und Angriffe eingetragen sein
    //ToDo : evtl Trennung Sondierung und Angriffe, damit die Sondierungen früher entfernt sind
    $sql = "DELETE FROM " . $db_tb_incomings . " WHERE arrivaltime<" . (CURRENT_UNIX_TIME - 20 * MINUTE);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not delete incomings information.', '', __FILE__, __LINE__, $sql);

    $sql = "SELECT koords_to, name_to, allianz_to, koords_from, name_from, allianz_from, arrivaltime, art, saved, recalled FROM " . $db_tb_incomings . " WHERE art = 'Sondierung (Schiffe/Def/Ress)' OR art = 'Sondierung (Gebäude/Ress)' ORDER BY arrivaltime ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);

    //Tabelle für die Sondierungen
    ?>
	<table class='tablesorter-blue' style='width:95%'>
		<thead>
			<tr class='center'>
				<th data-sorter="false" colspan='8'>
					<b>Sondierungen</b>
				</th>
			</tr>
			<tr>
				<th>
					<b>Opfer</b>
				</th>
				<th>
					<b>Zielplanet</b>
				</th>
				<th>
					<b>Pösewicht</b>
				</th>
				<th>
					<b>Ausgangsplanet</b>
				</th>
				<th>
					<b>Zeitpunkt</b>
				</th>
				<th>
					<b>Art der Sondierung</b>
				</th>
				<th>
					<b>gesaved</b>
				</th>
				<th>
					<b>recalled /<br>nichts zu tun</b>
				</th>
			</tr>
		</thead>
		<tbody>

        <?php
        while ($row = $db->db_fetch_array($result)) {
            if (($row['saved'] == "0") AND ($row['recalled'] == '0')) {

                $color1 = "#FF6347";
                $color2 = "#FF6347";

            } else if (($row['saved'] == '1') AND ($row['recalled'] == '0')) {

                $color1 = "#7FFF00";
                $color2 = "#FF6347";

            } else if (($row['saved'] == '0') AND ($row['recalled'] == '1')) {

                $color1 = "#7FFF00";
                $color2 = "#7FFF00";

            } else if (($row['saved'] == '1') AND ($row['recalled'] == '1')) {

                $color1 = "#7FFF00";
                $color2 = "#7FFF00";

            }
			
			?>
            
			<tr>
				<td>
					<?php									
                    echo "<a href='index.php?action=sitterlogins&sitterlogin=" . urlencode($row['name_to']) . "' target='_blank'><img src='" . BILDER_PATH . "user-login.gif' alt='L' title='Einloggen'>";
					echo "&emsp;" . $row['name_to'];
                    echo "</a>";
                    ?>
				</td>
				<td>
					<?php
                    echo getObjectPictureByCoords($row['koords_to']);
                    echo $row['koords_to'];
                    ?>
				</td>
				<td>
					<?php
                    if (!empty($row['allianz_from'])) {
                        echo ($row['name_from'] . " [" . $row['allianz_from'] . "]");
                    } else {
                        echo $row['name_from'];
                    }
                    ?>
				</td>
				<td>
					<?php
                    echo getObjectPictureByCoords($row['koords_from']);
                    echo $row['koords_from'];
                    ?>
				</td>
				<td>
					<?php
                    echo strftime(CONFIG_DATETIMEFORMAT, $row['arrivaltime']);
                    ?>
				</td>
				<td class="center">
					<?php
                    if ($row['art']=="Sondierung (Schiffe/Def/Ress)") {
						echo '<abbr title="Sondierung (Schiffe/Def/Ress)">';
						echo '<img src="'.BILDER_PATH.'scann_schiff.png">';
						echo '</abbr>';
					}
					else if ($row['art']=="Sondierung (Gebäude/Ress)") {
						echo '<abbr title="Sondierung (Gebäude/Ress)">';
						echo '<img src="'.BILDER_PATH.'scann_geb.png">';
						echo '</abbr>';
					}
					?>
				</td>
				<td style="background-color: <?php echo $color1 ?>">
					<?php
                    echo "<label><input type='checkbox' class='savedCheckbox' value='$row[koords_to]'";
                    if (!empty($row['saved'])) {
                        echo 'checked="checked"';
                    }
                    echo "'>";
                    echo "</label>";
                    ?>
				</td>
				<td style="background-color: <?php echo $color2 ?>">
					<?php
                    echo "<input type='checkbox' class='recalledCheckbox' value='$row[koords_to]'";
                    if (!empty($row['recalled'])) {
                        echo 'checked="checked"';
                    }
                    echo "'>";
                    ?>
				</td>
			</tr> 
		<?php
        }
        ?>
		</tbody>
    </table>

    <?php
    echo " 	 <br />\n";
    echo " 	 <br />\n";

    $sql = "SELECT koords_to, name_to, allianz_to, koords_from, name_from, allianz_from, arrivaltime, saved, recalled FROM " . $db_tb_incomings . " WHERE art = 'Angriff' ORDER BY arrivaltime ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);

    //Tabelle für die Angriffe
    ?>
    <table class='tablesorter-blue' style='width:95%'>
		<thead>
			<tr class='center'>
				<th data-sorter="false" colspan='8'>
					<b>Angriffe</b>
				</th>
			</tr>
            <tr>
                <th>
                    <b>Opfer</b>
                </th>
				<th>
					<b>Zielplanet</b>
				</th>
				<th>
					<b>Pösewicht</b>
				</th>
				<th>
					<b>Ausgangsplanet</b>
				</th>
				<th>
					<b>Zeitpunkt</b>
				</th>
				<th data-sorter="false">
					<?php
					echo '<abbr title="letzter Bomb">';
					echo '<img src="' . BILDER_PATH . 'bomb.png">';
					?>
				</th>
				<th>
					<b>gesaved</b>
				</th>
				<th>
					<b>recalled /<br>nichts zu tun</b>
				</th>
			</tr>
		</thead>
		<tbody>

        <?php
        while ($row = $db->db_fetch_array($result)) {

            if (($row['saved'] == "0") AND ($row['recalled'] == '0')) {

                $color1 = "#FF6347";
                $color2 = "#FF6347";

            } else if (($row['saved'] == '1') AND ($row['recalled'] == '0')) {

                $color1 = "#7FFF00";
                $color2 = "#FF6347";

            } else if (($row['saved'] == '0') AND ($row['recalled'] == '1')) {

                $color1 = "#7FFF00";
                $color2 = "#7FFF00";

            } else if (($row['saved'] == '1') AND ($row['recalled'] == '1')) {

                $color1 = "#7FFF00";
                $color2 = "#7FFF00";

            }
			
			?>
            
			<tr>
				<td>
					<?php
                    echo "<a href='index.php?action=sitterlogins&sitterlogin=" . urlencode($row['name_to']) . "' target='_blank'><img src='" . BILDER_PATH . "user-login.gif' alt='L' title='Einloggen'>";
                    echo "&emsp;" . $row['name_to'];
                    echo "</a>";
                    ?>
				</td>
				<td>
					<?php
                    echo getObjectPictureByCoords($row['koords_to']);
                    echo $row['koords_to'];
                    ?>
				</td>
				<td>
					<?php
                    if (!empty($row['allianz_from'])) {
                        echo ($row['name_from'] . " [" . $row['allianz_from'] . "]");
                    } else {
                        echo $row['name_from'];
                    }
                    ?>
				</td>
				<td>
					<?php
                    echo getObjectPictureByCoords($row['koords_from']);
                    echo $row['koords_from'];
                    ?>
				</td>
				<td><?php echo strftime(CONFIG_DATETIMEFORMAT, $row['arrivaltime']); ?></td>
				<td>
					<?php
					$coords = explode(":", $row['koords_to']);
					//$sql_bomb = "SELECT `time`, `ID_KB`, `hash` FROM `{$db_tb_kb}` WHERE (`koords_gal`='".$coords[0]."' AND `koords_sol`='".$coords[1]."' AND `koords_pla`='".$coords[2]."')";
					//$sql_bomb = "SELECT `time`, `ID_KB`, `hash` FROM `{$db_tb_kb}` LEFT JOIN `{$db_tb_kb_bomb}` ON `{$db_tb_kb}`.`ID_KB`=`{$db_tb_kb_bomb}`.`ID_KB` WHERE (`{$db_tb_kb}`.`koords_gal`='".$coords[0]."' AND `{$db_tb_kb}`.`koords_sol`='".$coords[1]."' AND `{$db_tb_kb}`.`koords_pla`='".$coords[2]."')";
					$sql_bomb = "SELECT MAX(time) AS bombtime, `ID_KB`, `hash` FROM `{$db_tb_kb}` WHERE ((`ID_KB` IN (SELECT `ID_KB` FROM `{$db_tb_kb_bomb}`)) AND (`{$db_tb_kb}`.`koords_gal`='".$coords[0]."' AND `{$db_tb_kb}`.`koords_sol`='".$coords[1]."' AND `{$db_tb_kb}`.`koords_pla`='".$coords[2]."'))";
					$result_bomb = $db->db_query($sql_bomb)
						or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql_bomb);
					$row_bomb = $db->db_fetch_array($result_bomb);
					echo '<div>';
					$time = strftime(CONFIG_DATETIMEFORMAT, $row_bomb['bombtime']);
					$url = "http://www.icewars.de/portal/kb/de/kb.php?id=" . $row_bomb['ID_KB'] . "&md_hash=" . $row_bomb['hash'];
					echo '<abbr title="'.$time.'">';
					if (isset($row_bomb['ID_KB']))
						echo '<a href="'.$url.'">KB</a>';
					echo '</abbr>';
					echo '</div>';
					?>
				</td>
				<td style="background-color: <?php echo $color1 ?>">
					<?php
                    echo "<input type='checkbox' class='savedCheckbox' value='$row[koords_to]'";
                    if (!empty($row['saved'])) {
                        echo 'checked="checked"';
                    }
                    echo "'>";
                    ?>
				</td>
				<td style="background-color: <?php echo $color2 ?>">
					<?php
                    echo "<input type='checkbox' class='recalledCheckbox' value='$row[koords_to]'";
                    if (!empty($row['recalled'])) {
                        echo 'checked="checked"';
                    }
                    echo "'>";
                    ?>
				</td>
			</tr> 
		<?php
        }
        ?>
		</tbody>
    </table>
    <?php

    $tables = ob_get_contents();
    ob_end_clean();

    return $tables;
}