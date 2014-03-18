<?php
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

/**
 * parse_de_alli_kasse_log_member
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

function parse_de_alli_kasse_log_member($aParserData)
{
    global $user_allianz;
    global $db, $db_tb_kasse_outgoing;

    if (empty($user_allianz)) {
        echo "<div class='system_warning'>User-Allianz nicht festgelegt</div>";

        return;
    }

    //27.05.2007 08:55 von ZAHLENDER an EMPFAENGER 10.000 Credits ausgezahlt
    //07.07.2010 20:28 von Labasu an Labasu 20.000 Credits ausgezahlt Grund war kisbau.

    $logentries = array();
    foreach ($aParserData->objResultData->aLogs as $log) {
        $logentry = array();

        $logentry['payedfrom']   = $log->strFromUser;
        $logentry['payedto']     = $log->strToUser;
        $logentry['amount']      = $log->iCredits;
        $logentry['time_of_pay'] = strftime('%Y-%m-%d %H:%M:%S', $log->iDateTime);
        $logentry['allianz']     = $user_allianz;

        $logentries[] = $logentry;
    }

    $db->db_insertignore_multiple($db_tb_kasse_outgoing, array_keys(reset($logentries)), $logentries);

    echo "<div class='doc_message'>" . count($logentries) . " Einträge erfolgreich geparsed</div>";

}