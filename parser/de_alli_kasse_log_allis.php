<?php
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

/**
 * parse_de_alli_kasse_log_allis
 *
 * This parsermodule is responsible for analysing the delivered alli bank parser data <br>
 * for help see:
 *
 * @link       https://handels-gilde.org/?www/forum/index.php;board=1099.0 the devforum
 * @link       https://github.com/iwdb/iwdb github repo
 *
 * @author     Mac <MacXY@herr-der-mails.de>
 * @license    GNU GPL version 2 or any later version
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package    iwdb
 * @subpackage parsermodule
 */

function parse_de_alli_kasse_log_allis($return)
{
    global $db, $db_tb_kasse_outgoing, $db_tb_user, $user_id;

    // ally vom user herausfinden
    $allianz = "";

    //wenn vorhanden aus den parseinformationen holen
    if (!empty($return->objResultData->strAlliance)) {
        $allianz = $return->objResultData->strAlliance;
    }

    //oder aus den IWDB Accinformationen
    if (empty($allianz)) {

        $sql = "SELECT allianz FROM " . $db_tb_user . " WHERE id = '" . $user_id . "'";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        while ($row = $db->db_fetch_array($result)) {
            $allianz = $row['allianz'];
        }
    }

    if (empty($allianz)) {
        echo "zugehörige Allianz konnte nicht ermittelt werden<br />";

        return;
    }

    foreach ($return->objResultData->aLogs as $log) {
        $strTime = strftime('%Y-%m-%d %H:%M:00', $log->iDateTime);

        $sql = "REPLACE INTO $db_tb_kasse_outgoing (payedfrom, payedto, amount, time_of_pay, allianz)" .
               " VALUES ('$log->strFromUser', '$log->strAlliTag', '$log->iCredits', '$strTime', '$allianz')";
        $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        /*
		(!isset($out) || empty($out)) ? $out = "<p><u>Auszahlungen der letzten drei Wochen:</u><p>" : $out = ' ';
        echo $out;

        echo "{" . strftime(CONFIG_DATETIMEFORMAT, $log->iDateTime)
                . "} Allizahlung [$allianz] von " . $log->strFromUser . " an " . $log->strAlliTag
                . " mit " . $log->iCredits . " Credits erkannt." . "<br />\n"; */
    }
}