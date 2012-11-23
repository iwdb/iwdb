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

function parse_de_alli_kasse_member($return)
{
    global $db, $db_tb_user, $user_id;

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

    /**
     * Mac:
     * neue Version ab Runde 11 (gezahlte Steuern bleiben erhalten, und auch sichtbar wenn keine Steuern angenommen)
     * Allerdings erfolgt ein Reset bei Allianzwechsel
     * -> Eintrag also pro Spieler und Allianz (und Beitrittsdatum) nötig
     */
    $members = $return->objResultData->aMember;
    echo "<p><u>Bisherige Einzahlungen:</u></p>";
    foreach ($members as $member) {
        //Array ( [0] => EINZAHLER 14.04.2007 15:07 117.256,53 1.712 pro Tag [1] => EINZAHLER [2] => 117256.53 )
        updateIncoming($member->strUser, $member->fCreditsPaid, $allianz);
        //echo $member->strUser . "&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;" . $member->fCreditsPaid . "<br>\n";
    }
}


function updateIncoming($user, $amount, $ally)
{
    global $db, $db_tb_kasse_incoming;
    $sum_old = 0.0;
    $sql = "SELECT sum(amount) FROM $db_tb_kasse_incoming WHERE user like '" . $user . "' AND allianz like '" . $ally . "' AND time_of_insert != CURRENT_DATE()";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not get member cash incomming!', '', __FILE__, __LINE__, $sql);
    while ($row = $db->db_fetch_array($result)) {
        $sum_old = $row['sum(amount)'];
    }

    $amount = $amount - $sum_old;

    $sql = "REPLACE INTO $db_tb_kasse_incoming (user, amount, time_of_insert, allianz) 
             VALUES ('$user', $amount, NOW(), '$ally')";
    $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not update member cash incomming!', '', __FILE__, __LINE__, $sql);
}