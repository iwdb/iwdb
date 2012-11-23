<?php
/**
 * Parsermodule for the alli bank parser
 *
 * This parsemodule is responsible for analysing the alli bank parser data
 * for help see
 * @link https://handels-gilde.org/?www/forum/index.php;board=1099.0 the devforum
 * or
 * @link https://github.com/iwdb/iwdb the repo at github
 *
 * @author    Mac <MacXY@herr-der-mails.de>
 * @copyright Mac <MacXY@herr-der-mails.de>
 * @license   GNU GPL version 2 or any later version
 * @license   http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package   iwdb
 * @subpackage parsermodule
 */

if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

function parse_de_alli_kasse_inhalt($return)
{

    global $db, $db_tb_kasse_content, $db_tb_user, $user_id;

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

    $content = $return->objResultData->fCredits;

    $sql = "REPLACE INTO $db_tb_kasse_content (amount, time_of_insert, allianz)
          VALUES ($content, " . CURRENT_UNIX_TIME . ", '$allianz')";
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    //echo "<p><b>Allykasse updated: $content</b></p>\n";
}