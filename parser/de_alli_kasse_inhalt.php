<?php
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

/**
 * parse_de_alli_kasse_inhalt
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

function parse_de_alli_kasse_inhalt($aParserData)
{
    global $db, $db_tb_kasse_content, $user_allianz;

    if (empty($user_allianz)) {
        echo "<div class='system_warning'>Allianz nicht festgelegt</div>";

        return;
    }

    $SQLdata = array(
        'amount'         => $aParserData->objResultData->fCredits,
        'time_of_insert' => strftime('%Y-%m-%d %H:%M:00', CURRENT_UNIX_TIME),
        'allianz'        => $user_allianz
    );

    $db->db_insertupdate($db_tb_kasse_content, $SQLdata);

    echo "<div class='doc_message'>Inhalt (" . number_format($SQLdata['amount'], 2, ",", ".") . " Credits) aktualisiert</div>";
}