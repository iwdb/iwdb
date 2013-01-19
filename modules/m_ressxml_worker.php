<?php
/*****************************************************************************
 * m_ressxml_worker.php                                                      *
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
 * Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  *
 * IW-Spielern.                                                              *
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
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
$modulname = "m_ressxml_worker";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Ressourcen-XML Updater";

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
    "Ding zum Holen von Ressübersichtsdaten über XML-Übersichts-Links";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//
function workInstallDatabase()
{
    global $db, $db_tb_ressuebersicht;

    $sql = "ALTER TABLE `$db_tb_ressuebersicht` " .
        " ADD `xml_link` VARCHAR(255) NULL," .
        " ADD `last_xml_try` INT(11) NOT NULL DEFAULT '0'," .
        " ADD `xml_valid` INT(11) NOT NULL DEFAULT '0'";

    $result = $db->db_query($sql);

    if ($result !== false) {
        echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
    } else {
        echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>FAIL</b></div>";
    }
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modultitle, $modulstatus, $_POST;

    $actionparamters = "";
    insertMenuItem($_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparamters);

}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed
// for the configuration file.
//
function workInstallConfigString()
{
    return "";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//
function workUninstallDatabase()
{
    global $db, $db_tb_ressuebersicht;

    $sql = "ALTER TABLE `$db_tb_ressuebersicht` DROP `xml_link`, DROP `last_xml_try`, DROP `xml_valid`;";

    $result = $db->db_query($sql);
    if ($result !== false) {
        echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
    } else {
        echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>FAIL</b></div>";
    }
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
if (!empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
    if ($user_status != "admin") {
        die('Hacking attempt...');
    }

    echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")</div>\n";

    if (!@include("./includes/menu_fn.php")) {
        die("Cannot load menu functions");
    }

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br /><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul


function updateXML($user)
{
    global $db, $db_tb_ressuebersicht, $db_tb_lager;


    if (!isset($db_tb_ressuebersicht)) {
        return;
    }
    if (!isset($db_tb_lager)) {
        return;
    }

    $sql    = "SELECT `xml_link`, `datum` " .
        " FROM `" . $db_tb_ressuebersicht . "`" .
        " WHERE (`xml_link` IS NOT NULL)" .
        " AND (`xml_valid` > " . CURRENT_UNIX_TIME . ") " .
        " and (`user` = '$user') " .
        " LIMIT 1";
    $result = $db->db_query($sql);

    if ($row = $db->db_fetch_array($result)) {
        $xml = simplexml_load_file_ex($row['xml_link']);
        if ($xml == null) {
            doc_message('Xml-Link scheint kaputt!');

            return;
        }
        if ($row['datum'] < $xml->timestamp) { //ist wirklich neuer

            foreach ($xml->{'plani_data'} as $data) { //für jeden planni

                $rarr = null;
                $sql  = "UPDATE $db_tb_lager " .
                    "SET " .
                    " kolo_typ = '" . $data->objekt_typ->name . "',";
                foreach ($data->ressourcen->ressource as $ress) {
                    switch ($ress->id) {
                        case 1:
                            $sql .= " eisen = " . $ress->anzahl . " ,\n";
                            break;
                        case 2:
                            $sql .= " stahl = " . $ress->anzahl . " ,\n";
                            break;
                        case 3:
                            $sql .= " vv4a = " . $ress->anzahl . " ,\n";
                            break;
                        case 4:
                            $sql .= " chem = " . $ress->anzahl . " ,\n";
                            break;
                        case 5:
                            $sql .= " eis = " . $ress->anzahl . " ,\n";
                            break;
                        case 6:
                            $sql .= " wasser = " . $ress->anzahl . " ,\n";
                            break;
                        case 7:
                            $sql .= " energie = " . $ress->anzahl . " ,\n";
                            break;
                        case 11:
                            $sql .= " bev_g  = " . $ress->anzahl . ",\n" .
                                " bev_a  = " . $ress->anzahl_work . " ,\n";
                            if ($ress->anzahl > 0) {
                                $sql .= " bev_q = " . number_format($ress->anzahl_work / $ress->anzahl, 4, '.', '') . ",\n";
                            } else {
                                $sql .= " bev_q = 0 ,\n";
                            }
                            break;
                    }

                }

                foreach ($data->bunker->ressource as $bunker) {
                    switch ($bunker->id) {
                        case 1:
                            $sql .= " eisen_bunker = " . $bunker->anzahl . " ,\n";
                            break;
                        case 2:
                            $sql .= " stahl_bunker = " . $bunker->anzahl . " ,\n";
                            break;
                        case 3:
                            $sql .= " vv4a_bunker = " . $bunker->anzahl . " ,\n";
                            break;
                        case 4:
                            $sql .= " chem_bunker = " . $bunker->anzahl . " ,\n";
                            break;
                        case 5:
                            $sql .= " eis_bunker = " . $bunker->anzahl . " ,\n";
                            break;
                        case 6:
                            $sql .= " wasser_bunker = " . $bunker->anzahl . " ,\n";
                            break;
                        case 7:
                            $sql .= " energie_bunker = " . $bunker->anzahl . " ,\n";
                            break;
                    }
                }

                foreach ($data->lager->ressource as $lager) {
                    switch ($lager->id) {
                        case 4:
                            $sql .= " chem_lager = " . $lager->anzahl . " ,\n";
                            break;
                        case 5:
                            $sql .= " eis_lager = " . $lager->anzahl . " ,\n";
                            break;
                        //case 6: $sql .= " wasser_lager = ".$lager->anzahl." ,\n"; break;
                        case 7:
                            $sql .= " energie_lager = " . $lager->anzahl . " ,\n";
                            break;
                    }
                }
                foreach ($data->ressourcen_einkommen->ressource as $prod) {
                    switch ($prod->id) {
                        case 1:
                            $sql .= " energie_prod = " . $prod->anzahl . " ,\n";
                            break;
                        case 2:
                            $sql .= " stahl_prod = " . $prod->anzahl . " ,\n";
                            break;
                        case 3:
                            $sql .= " vv4a_prod = " . $prod->anzahl . " ,\n";
                            break;
                        case 4:
                            $sql .= " chem_prod = " . $prod->anzahl . " ,\n";
                            break;
                        case 5:
                            $sql .= " eis_prod = " . $prod->anzahl . " ,\n";
                            break;
                        case 6:
                            $sql .= " wasser_prod = " . $prod->anzahl . " ,\n";
                            break;
                        case 7:
                            $sql .= " energie_prod = " . $prod->anzahl . " ,\n";
                            break;
                        case 10:
                            $sql .= " credits = " . $prod->anzahl . " ,\n";
                            break;
                        case 11:
                            $sql .= " bev_w = " . $prod->anzahl . " ,\n";
                            break;
                    }
                }
                $sql .= " fp = " . $data->forschung->anzahl . ",\n" .
                    " zufr = " . $data->zufriedenheit->anzahl . ",\n" .
                    " zufr_w = " . $data->zufriedenheit->aenderung . ",\n" .
                    " time = " . $xml->timestamp . "\n" .
                    " WHERE ( coords_gal = " . $data->koordinaten->gal . " ) AND\n" .
                    " ( coords_sys = " . $data->koordinaten->sol . " ) AND\n" .
                    " ( coords_planet = " . $data->koordinaten->pla . " ) AND\n" .
                    " ( user = '$user' );";

                $db->db_query($sql);

            }
            //für jeden planni

            $sql = "SELECT sum(eisen_prod) as eisen_g, \n" .
                "sum(stahl_prod) as stahl_g, \n" .
                "sum(vv4a_prod) as vv4a_g, \n" .
                "sum(chem_prod) as chem_g, \n" .
                "sum(eis_prod) as eis_g, \n" .
                "sum(wasser_prod) as wasser_g, \n" .
                "sum(energie_prod) as energie_g, \n" .
                "sum(fp) as fp_g, \n" .
                "sum(bev_g) as bev_g, \n" .
                "sum(bev_a) as h4, \n" .
                "sum(credits) as cred_g \n" .
                "FROM $db_tb_lager " .
                " WHERE (`user` = '$user');\n";

            $result = $db->db_query($sql);
            if ($row = $db->db_fetch_array($result)) {
                $sql = " UPDATE " . $db_tb_ressuebersicht .
                    " SET 	eisen = " . $row['eisen_g'] . " ,\n" .
                    "stahl = " . $row['stahl_g'] . " ,\n" .
                    "vv4a = " . $row['vv4a_g'] . " ,\n" .
                    "chem = " . $row['chem_g'] . " ,\n" .
                    "wasser = " . $row['wasser_g'] . " ,\n" .
                    "energie = " . $row['energie_g'] . " ,\n" .
                    "fp_ph = " . $row['fp_g'] . " ,\n" .
                    "credits = " . $row['cred_g'] . " ,\n" .
                    "bev_a = " . $row['cred_g'] . " ,\n" .
                    "bev_g = " . $row['bev_g'] . " ,\n" .
                    "bev_a = " . $row['h4'] . " ,\n";
                if ($row['bev_g'] > 0) {
                    $sql .= " bev_q = " . number_format($row['h4'] / $row['bev_g'], 4, '.', '') . ",\n";
                } else {
                    $sql .= " bev_q = 0 ,\n";
                }

                $sql .= "datum = " . $xml->timestamp . " ,\n" .
                    "last_xml_try = " . CURRENT_UNIX_TIME . " \n" .
                    " WHERE (`user` = '$user');";

                $db->db_query($sql);
            } else {
                error(GENERAL_ERROR, "Tabelle $db_tb_ressuebersicht konnte nicht mit Tabelle $db_tb_lager synchonisiert werden" . __FILE__, __LINE__, '');
            }

            echo "<div class='system_notification'>AutoUpdate von " . $user . "'s Datensatz ok</div>";

        } else { //ist nicht neuer

            echo "<div class='system_notification'>Autoupdates von $user's Datensatz über XML-Link fehlgeschlagen.<br />XML ist veraltet/Datenbasis ist neuer</div>";
            $sql = " UPDATE " . $db_tb_ressuebersicht .
                " SET last_xml_try = " . CURRENT_UNIX_TIME .
                " WHERE `user` = '$user'";

            $db->db_query($sql);
        }
    }
}

doc_title('Kolo-Ress-Info-Via-XML-Holer-Ding');
?>
<div align='left'>
    Einschränkungen :
    <ul>
        <li>Die XML wird nur aktualisiert, wenn die Seite Kolo-/Ressübersicht in IW aufgerufen wird</li>
        <li>Die XML wird nicht aktualisiert, wenn ein Sitter diese Seite aufruft</li>
        <li>Die XML wird nur mindestens alle 12 Stunden aktualisiert</li>
        <li>Die Einwilligung, dass die XML generiert wird, muss alle 14 Tage erneut gegeben werden</li>
    </ul>
    ... wenn es also nicht funzt, weil die XML veraltet ist, liegt es daran, dass der Spieler einige Zeit nicht auf der
    Kolo-/Ressübersicht in IW war, oder eben das letzte Update der XML vor weniger als 12 Stunden immer noch älter als
    die Datenbasis ist.
</div><br/>
<?php


function make_link($order, $ordered)
{
    global $sid, $modulname;
    echo "<a href='index.php?action=" . $modulname . "&order=" . $order . "&ordered=" . $ordered .
        "&sid=$sid'> <img src='bilder/" . $ordered . ".gif' border='0' alt='" . $ordered . "'> </a>";
}

if (isset($_GET['xmlrun'])) {
    updateXML($_GET['xmlrun']);
}

if ((isset($_GET['do'])) && ($_GET['do'] == "enter_link")) {

    $link = $_POST["xml_link"];
    $date = $_POST["xml_valid"];
    $ok   = true;
    if (preg_match("/(http\S+icewars\S+ress_uebersicht\S+)/", $link, $match) > 0) {
        $link = $match[1];
    } else {
        $ok = false;
        echo "<div class='system_notification'>Link fehlerhaft</div>";
    }
    if (preg_match("/(\d{1,2})\.(\d{1,2})\.(\d{2,4})\s+(\d{2})\:(\d{2})/", $date, $match) > 0) {
        $day  = $match[1];
        $mon  = $match[2];
        $y    = $match[3];
        $y    = ($y < 100) ? $y + 2000 : $y;
        $h    = $match[4];
        $min  = $match[5];
        $date = mktime($h, $min, 0, $mon, $day, $y);
    } else {
        $ok = false;
        echo "<div class='system_notification'>Datum fehlerhaft</div>";
    }

    if ($ok) {
        $sql = " UPDATE " . $db_tb_ressuebersicht .
            " SET xml_link = '$link', xml_valid = '$date'" .
            " WHERE `user` = '$user_id'";

        $result = $db->db_query($sql);
        if ($result !== false) {
            echo "<div class='system_notification'>Link eingetragen</div>";
        } else {
            echo "<div class='system_notification'>Da ging was schief...</div>";
        }
    }

}

start_table();

start_row("titlebg", "style='width:9%' align='center' nowrap='nowrap'");
make_link("user", "asc");
echo "<b>User</b>";
make_link("user", "desc");

next_cell("titlebg", "style='width:3%' align='center' nowrap='nowrap'");
make_link("datum", "asc");
echo "<b>Datenbasis Alter</b>";
make_link("datum", "desc");

next_cell("titlebg", "style='width:3%' align='center'");
make_link("xml_valid", "asc");
echo "<b>Xml-link</b>";
make_link("xml_valid", "desc");

next_cell("titlebg", "style='width:3%' align='center' nowrap='nowrap'");
make_link("last_xml_try", "asc");
echo "<b>Letztes update via xml</b>";
make_link("last_xml_try", "desc");
echo "<br />(auch Versuche)";



$order = getVar('order');
$ordered = getVar('ordered');

if (empty($order)) {
    $order = 'datum';
}

if (empty($ordered)) {
    $ordered = 'asc';
}

$sql = "SELECT `user`, `xml_link`, `datum`, last_xml_try, xml_valid " .
    " FROM `" . $db_tb_ressuebersicht . "`" .
    " ORDER BY `" . $order . "` " . $ordered;

$result = $db->db_query($sql);

while ($row = $db->db_fetch_array($result)) {


    next_row("windowbg1", "nowrap='nowrap'");
    echo $row['user'];

    $difftime = CURRENT_UNIX_TIME - $row['datum'];
    $color    = scanAge(0);
    if ($difftime < (3 * DAY)) {
        $color = scanAge(CURRENT_UNIX_TIME - (7 * DAY));
    } elseif ($difftime < DAY) {
        $color = scanAge(CURRENT_UNIX_TIME);
    }
    next_cell("windowbg1", "align='center' nowrap='nowrap' style='background-color:" . $color . "'");
    $timestr  = ((int)($difftime / DAY)) ? number_format($difftime / DAY, 1, ',', '') . " Tage " : null;
    $difftime = $difftime % DAY;
    if ($timestr == null) {
        $timestr = ((int)($difftime / HOUR)) ? ((int)($difftime / HOUR)) . " Stunden" : null;
    }
    $difftime = $difftime % HOUR;
    if ($timestr == null) {
        $timestr = ((int)($difftime / MINUTE)) . " Minuten ";
    }
    echo $timestr;

    next_cell("windowbg1", "align='center' nowrap='nowrap'");
    if ($row['xml_valid'] > CURRENT_UNIX_TIME) {
        echo "Gültig bis " . strftime("%d.%m.%y %H:%M", $row['xml_valid']) . "<br />";
        $link = "index.php?action=" . $modulname . "&sid=" . $sid . "&xmlrun=" . $row['user'] . "&order=" . $order . "&ordered=" . $ordered;
        echo "<input type='button' onclick='window.location.href = '$link';' value='Update'>";
    } else {
        echo "Link ungülig/veraltet";
    }

    next_cell("windowbg1", "align='center' nowrap='nowrap'");
    if ($row['last_xml_try']) {
        echo strftime("%d.%m.%y %H:%M", $row['last_xml_try']);
    } else {
        echo "<i>nie</i>";
    }

}

end_row();
end_table();

$link = "index.php?action=" . $modulname . "&sid=" . $sid . "&do=enter_link&order=" . $order . "&ordered=" . $ordered;
?>
<form action='<?php echo $link; ?>' method='post'>
    <h2>Eigenen Link eintragen</h2>
    <?php
    start_table();
    start_row("titlebg", "style='width:9%' align='center' nowrap='nowrap'");
    echo "Link";

    next_cell("titlebg", "style='width:3%' align='center' nowrap='nowrap'");
    echo "gültig bis";

    next_row("windowbg1", "nowrap='nowrap'");
    ?>
    <input name="xml_link" type="text" size="70" maxlength="255">
    <?php
    next_cell("windowbg1", "nowrap='nowrap'");
    echo "<input name='xml_valid' type='text' size='15' maxlength='20' value='" . date("d.m.Y H:i", CURRENT_UNIX_TIME + 14 * DAY) . "'>";

    end_row();

    end_table();
    ?><br/>
    <input type="submit" value="eintragen">
</form>
