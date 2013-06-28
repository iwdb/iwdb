<?php

function getIncomingsTables()
{
    global $db, $db_tb_incomings;

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
				<td>
					<?php
                    echo $row['art'];
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
				<th data-sorter="false" colspan='7'>
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