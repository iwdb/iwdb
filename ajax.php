<?php

require_once("includes/bootstrap.php");

if ($login_ok === false) {
    exit;
}

global $db, $db_tb_incomings;

$action = getVar('action');
if ($action === 'getIncomings') {

    getIncomings((int)getVar('timestamp'));

} elseif ($action === 'setSaved') {

    if (getVar('state') === 'true') {
        $saved = 1;
    } else {
        $saved = 0;
    }
    $coords = $db->escape(getVar('coords'));

    $result = $db->db_update($db_tb_incomings, array('saved' => $saved, 'savedUpdateTime' => CURRENT_UNIX_TIME), "WHERE koords_to = '$coords'");

    if ($result === true) {
        echo json_encode(array('result' => 'success', 'time' => CURRENT_UNIX_TIME, 'tables' => getIncomingsTables()));
    }

} elseif ($action === 'setRecalled') {

    if (getVar('state') === 'true') {
        $recalled = 1;
    } else {
        $recalled = 0;
    }
    $coords = $db->escape(getVar('coords'));

    $result = $db->db_update($db_tb_incomings, array('recalled' => $recalled, 'recalledUpdateTime' => CURRENT_UNIX_TIME), "WHERE koords_to = '$coords'");

    if ($result === true) {
        echo json_encode(array('result' => 'success', 'time' => CURRENT_UNIX_TIME, 'tables' => getIncomingsTables()));
    }

}

function getIncomings($timestamp) {
    global $db, $db_tb_incomings;

    $sql = "SELECT COUNT(*) AS newEntries FROM `{$db_tb_incomings}` WHERE (`listedtime`>{$timestamp} OR `savedUpdateTime`>{$timestamp} OR `recalledUpdateTime`>{$timestamp}) ORDER BY `arrivaltime` ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);
    if (empty($row['newEntries'])) {
        //es sind keine aktuelleren Einträge da
        header('HTTP/1.1 304 Not Modified');
    } else {
        echo json_encode(array('result' => 'success', 'time' => CURRENT_UNIX_TIME, 'tables' => getIncomingsTables()));
    }

}

function getIncomingsTables() {
    global $db, $db_tb_incomings;

    //Löschen der Einträge in der Tabelle incomings, es sollen nur aktuelle Sondierungen und Angriffe eingetragen sein
    //ToDo : evtl Trennung Sondierung und Angriffe, damit die Sondierungen früher entfernt sind
    $sql = "DELETE FROM " . $db_tb_incomings . " WHERE arrivaltime<" . (CURRENT_UNIX_TIME - 20 * MINUTE);
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not delete incomings information.', '', __FILE__, __LINE__, $sql);

    $sql = "SELECT koords_to, name_to, allianz_to, koords_from, name_from, allianz_from, arrivaltime, art, saved, recalled FROM " . $db_tb_incomings . " WHERE art = 'Sondierung (Schiffe/Def/Ress)' OR art = 'Sondierung (Gebäude/Ress)' ORDER BY arrivaltime ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);

    $tabellen = "
    <table class='table_hovertable' style='width:95%'>
        <caption>Sondierungen</caption>
        <thead>
        <tr>
            <th>Opfer</th>
            <th>Zielplanet</th>
            <th>Pösewicht</th>
            <th>Ausgangsplanet</th>
            <th>Zeitpunkt</th>
            <th>Art der Sondierung</th>
            <th>gesaved</th>
            <th>recalled</th>
        </tr>
        </thead>
        <tbody>";

    while ($row = $db->db_fetch_array($result)) {
        $name_to = "<a href='index.php?action=sitterlogins&sitterlogin=" . urlencode($row['name_to']) . "' target='_blank'><img src='" . BILDER_PATH . "user-login.gif' alt='L' title='Einloggen'>&emsp;$row[name_to]</a>";

        $koords_to = getObjectPictureByCoords($row['koords_to']) . $row['koords_to'];

        if (!empty($row['allianz_from'])) {
            $name_from = ($row['name_from'] . " [" . $row['allianz_from'] . "]");
        } else {
            $name_from = $row['name_from'];
        }

        $koords_from = getObjectPictureByCoords($row['koords_from']) . $row['koords_from'];

        $arrivaltime = strftime(CONFIG_DATETIMEFORMAT, $row['arrivaltime']);

        $art = $row['art'];

        $savedCheckbox = "<input type='checkbox' class='savedCheckbox' value='$row[koords_to]'";
        if (!empty($row['saved'])) {
            $savedCheckbox .= 'checked="checked"';
        }
        $savedCheckbox .= "'>";

        $recalledCheckbox = "<input type='checkbox' class='recalledCheckbox' value='$row[koords_to]'";
        if (!empty($row['recalled'])) {
            $recalledCheckbox .= 'checked="checked"';
        }
        $recalledCheckbox .= "'>";

        $tabellen .= "
        <tr>
            <td>$name_to</td>
            <td>$koords_to</td>
            <td>$name_from</td>
            <td>$koords_from</td>
            <td>$arrivaltime</td>
            <td>$art</td>
            <td>$savedCheckbox</td>
            <td>$recalledCheckbox</td>
        </tr>";
    }
    $tabellen .= "
        </tbody>
    </table>
    <br />
    <br />";

    $sql = "SELECT koords_to, name_to, allianz_to, koords_from, name_from, allianz_from, arrivaltime, saved, recalled FROM " . $db_tb_incomings . " WHERE art = 'Angriff' ORDER BY arrivaltime ASC";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query incomings information.', '', __FILE__, __LINE__, $sql);

//Tabelle für die Angriffe

    $tabellen .= "
    <table class='table_hovertable' style='width:95%'>
        <caption>Angriffe</caption>
        <thead>
        <tr>
            <th>Opfer</th>
            <th>Zielplanet</th>
            <th>Pösewicht</th>
            <th>Ausgangsplanet</th>
            <th>Zeitpunkt</th>
            <th>gesaved</th>
            <th>recalled</th>
        </tr>
        </thead>
        <tbody>";

    while ($row = $db->db_fetch_array($result)) {
        $name_to = "<a href='index.php?action=sitterlogins&sitterlogin=" . urlencode($row['name_to']) . "' target='_blank'><img src='" . BILDER_PATH . "user-login.gif' alt='L' title='Einloggen'>&emsp;$row[name_to]</a>";

        $koords_to = getObjectPictureByCoords($row['koords_to']) . $row['koords_to'];

        if (!empty($row['allianz_from'])) {
            $name_from = ($row['name_from'] . " [" . $row['allianz_from'] . "]");
        } else {
            $name_from = $row['name_from'];
        }

        $koords_from = getObjectPictureByCoords($row['koords_from']) . $row['koords_from'];

        $arrivaltime = strftime(CONFIG_DATETIMEFORMAT, $row['arrivaltime']);

        $savedCheckbox = "<input type='checkbox' class='savedCheckbox' value='$row[koords_to]'";
        if (!empty($row['saved'])) {
            $savedCheckbox .= 'checked="checked"';
        }
        $savedCheckbox .= "'>";

        $recalledCheckbox = "<input type='checkbox' class='recalledCheckbox' value='$row[koords_to]'";
        if (!empty($row['recalled'])) {
            $recalledCheckbox .= 'checked="checked"';
        }
        $recalledCheckbox .= "'>";

        $tabellen .= "
        <tr>
            <td>$name_to</td>
            <td>$koords_to</td>
            <td>$name_from</td>
            <td>$koords_from</td>
            <td>$arrivaltime</td>
            <td>$savedCheckbox</td>
            <td>$recalledCheckbox</td>
        </tr>
        ";
    }
    $tabellen .= "
        </tbody>
    </table>";

    return $tabellen;

}