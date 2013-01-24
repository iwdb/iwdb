<?php
/*****************************************************************************/
/* research_overview.mdl                                                     */
/*****************************************************************************/
/* Dieses Modul dient zur Vermeidung von Forschungsleerlauf                  */
/* in der Allianz                                             			     */
/*                                                                           */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschaftsprojekt von */
/* IW-Spielern.                                                              */
/* Author: Mac (MacXY@herr-de-mails.de)                                      */
/* Datum: 05.2012                                                            */
/*                   http://www.handels-gilde.org                            */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
    echo "Hacking attempt...!!";
    exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname = "research_overview";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Forschungsübersicht";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation 
//    ausführen darf. Mögliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "admin";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "Zeigt die aktuellen Forschungen in der Allianz, inkl. evtl. Folgeforschungen";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    //nothing here
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

    $actionparameters = "";
    insertMenuItem($_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparameters);
    //
    // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
    //
    // 	insertMenuItem( $_POST['menu'], ($_POST['submenu']+1), "Titel2", "hc", "&weissichnichtwas=1" );
    //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file.
//
function workInstallConfigString()
{
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase()
{
    //nothing here
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


/** SQL Stuff
 *INSERT INTO `gisis`.`cmd_data` (
`data_title` ,
`data_desc` ,
`data_key` ,
`data_type` ,
`data_default` ,
`data_exp`
)
VALUES (
'Forschungsleerlauf', 'Soll die <a href="">Forschungsübersicht</a> zur Minimierung von Forschungsleerlauf für diesen Account abgeschaltet werden ?', 'research_overview_disable', 'user', '0', 'boolean'
); 
 */

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

$users = obResearchGetOverview();

doc_title('Forschungsübersicht - laufende Forschungen');
?>
    <table style="width: 90%;" align="center">
        <thead>
        <tr class="titlebg">
            <th style="width: 15%; text-align: center;">Spieler</th>
            <th style="width: 50%; text-align: center;">Forschung</th>
            <th style="width: 15%; text-align: center;">bis</th>
            <th style="width: 20%; text-align: center;">Folgeauftrag</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user) { ?>
            <tr class="windowbg" style="text-align: center;">
                <td>
                    <a target="_blank" href="?{domain}/iwlogin.mdl;mode=sitt;user_id=<?php echo $user["user_id"];?>"><span style='color:blue'><?php echo $user["user_name"];?></span></a>
                </td>
                <td><?php echo $user["research_name"];?></td>
                <td><?php echo $user["research_time"];?></td>
                <td><?php echo $user["next_research_name"];?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

<?php

/**
 * @desc Ermittelt alle laufenden Forschungen von Spielern in der angegeben Allianz/Team oder der Meta des aktiven Benutzers
 * @author Mac (MacXY@herr-der-mails.de)
 * @global $db, $db_tb_user, $db_tb_research, $db_tb_user_research, $db_tb_sitterauftrag
 * @return array
 */
function obResearchGetOverview()
{
    global $db, $db_tb_user, $db_tb_research, $db_tb_user_research, $db_tb_sitterauftrag;

    //! Mac: @todo: keine Subquery verwenden, sondern nur einmal pro User die Forschung ermitteln
    $sql = "SELECT
                $db_tb_user.id as user_name, $db_tb_user_research.rid as research_id, $db_tb_research.name as research_name, $db_tb_user_research.date as finished_time ";
    $sql .= " ,(
        SELECT bauid 
        FROM $db_tb_sitterauftrag
        WHERE $db_tb_sitterauftrag.`user` = $db_tb_user.`id` AND $db_tb_sitterauftrag.typ = 'Forschung'
        ORDER BY date_b2 ASC LIMIT 0,1
                ) as next_research_id";
    $sql .= " FROM
                $db_tb_user";
    $sql .= " LEFT JOIN
                $db_tb_user_research
            ON $db_tb_user_research.user = $db_tb_user.id";
    $sql .= " LEFT JOIN
                $db_tb_research
            ON $db_tb_research.ID = $db_tb_user_research.rid";

    $sql .= "	ORDER BY finished_time ASC";

    $result = $db->db_query($sql);

    $users = array();
    while ($row = $db->db_fetch_array($result)) {

        if ($row["finished_time"] > 0 && $row["finished_time"] <= CURRENT_UNIX_TIME) {
            $sql = "DELETE FROM 
                        $db_tb_user_research
                    WHERE 
                        user = " . $row["user_name"] . " AND rid = " . $row["research_id"];
            $db->db_query($sql);
            $row["finished_time"] = 0;
            $row["research_name"] = "";
        }

        if ($row["finished_time"] > 0) {
            $row["research_time"] = strftime(CONFIG_DATETIMEFORMAT, $row["finished_time"]);
        } else {
            $row["research_time"] = "---";
        }

        if (empty($row["research_name"])) {
            $row["research_name"] = "<span style='background-color: red;'>Forschungsleerlauf!</span>";
        }

        if (obResearchGetNameById($row["next_research_id"]) !== OB_FAILED) {
            $row["next_research_name"] = obResearchGetNameById($row["next_research_id"]);
        } else {
            $row["next_research_name"] = '---';
        }

        $users[$row["user_id"]] = $row;
    }

    return $users;
}

?>