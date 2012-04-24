<?php
/*****************************************************************************/
/* de_wirtschaft_planiress2.php                                              */
/*****************************************************************************/
/* This program is free software; you can redistribute it and/or modify it   */
/* under the terms of the GNU General Public License as published by the     */
/* Free Software Foundation; either version 2 of the License, or (at your    */
/* option) any later version.                                                */
/*                                                                           */
/* This program is distributed in the hope that it will be useful, but       */
/* WITHOUT ANY WARRANTY; without even the implied warranty of                */
/* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General */
/* Public License for more details.                                          */
/*                                                                           */
/* The GNU GPL can be found in LICENSE in this directory                     */
/*****************************************************************************/

/*****************************************************************************/
/* Diese Erweiterung der urspuenglichen DB ist ein Gemeinschafftsprojekt von */
/* IW-Spielern.                                                              */
/*                                                                           */
/* Autor: Mac (MacXY@herr-der-mails.de)                                      */
/* Datum: April 2012                                                         */
/*                                                                           */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                   http://www.iw-smf.pericolini.de                         */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/


if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_wirtschaft_planiress2 ( $return )
{
    global $db_tb_ressuebersicht, $db, $selectedusername;

    $fp      = $return->objResultData->iFPProduction;
    $credits = $return->objResultData->fCreditProduction + $return->objResultData->fCreditAlliance;   //! Mac: ist das wirklich richtig ?
    $bev_a   = $return->objResultData->iPeopleWithoutWork;
    $bev_g   = $return->objResultData->iPeopleWithWork;
    $bev_q   = $bev_a * 100 / $bev_g;

    $sql = "UPDATE ". $db_tb_ressuebersicht
    ." SET `fp_ph` = '" . $fp
    ."', `credits` = '" . $credits
    ."', `bev_a` = '" . $bev_a
    ."', `bev_g` = '" . $bev_g
    ."', `bev_q` = '" . $bev_q
    ."' WHERE user = '" . $selectedusername . "' LIMIT 1 ;";
    $db->db_query($sql)
            or error(GENERAL_ERROR,
        'Could not query config information.', '',
        __FILE__, __LINE__, $sql);

    echo "<div class='system_notification'>Produktion Teil 2 aktualisiert/hinzugef&uuml;gt mit den Werten:</div>";
    echo "FP/h (".$fp."), Credits (".$credits."), 1&euro;-Leute (".$bev_a."), Volk (".$bev_g."), Quote (".$bev_q.")";

}

//! Mac: rausgenommen, da zuviele Fehler, und nicht wirklich noetig, oder ?
//function display_de_wirtschaft_planiress2() {
//  include "./modules/m_ress.php";
//}

?>