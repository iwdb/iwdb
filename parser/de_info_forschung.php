<?php
/*****************************************************************************
 * de_info_forschung.php                                                     *
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
 * Autor: Mac (MacXY@herr-der-mails.de)                                      *
 * Datum: April 2012                                                         *
 *                                                                           *
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum wenden:                                                   *
 *                   https://www.handels-gilde.org                           *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

if (!defined('IRA')) {
    die('Hacking attempt...');
}

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

function parse_de_info_forschung($return)
{
    debug_var("input", $return);

    $scan_data = array();
    $research  = $return->objResultData;

    $scan_data['research']    = $research->strResearchName;
    $scan_data['description'] = $research->strResearchComment;

    $scan_data['addcost']     = '';
    if (!empty($research->aCosts)) {
        foreach ($research->aCosts as $costRess) {
            $scan_data['addcost'] .= $costRess['strResourceName'].': '.$costRess['iResourceCount'].', ';
        }
        $scan_data['addcost'] = substr($scan_data['addcost'], 0, -2);
    }

    $scan_data['FP']          = $research->iFP;
    $scan_data['gebiet']      = $research->strAreaName;

    if ($research->bIsPrototypResearch) {
        $scan_data['Prototyp'] = $research->strPrototypName;
    }

    $scan_data['NeededResearch']     = $research->aResearchsNeeded;
    $scan_data['NeededBuildings']    = $research->aBuildingsNeeded;
    $scan_data['NeededObject']       = $research->aObjectsNeeded;
    $scan_data['GivesResearch']      = $research->aResearchsDevelop;
    $scan_data['GivesBuildingLevel'] = $research->aBuildingLevelsDevelop;
    $scan_data['GivesBuilding']      = $research->aBuildingsDevelop;
    $scan_data['GivesDefense']       = $research->aDefencesDevelop;
    $scan_data['GivesGenetics']      = $research->aGeneticsDevelop;

    update_research($scan_data);

}

// ****************************************************************************
//
function display_de_info_forschung()
{
    include "./modules/m_research.php";
}

// ****************************************************************************
//
//
function update_research($scan_data)
{
    global $db, $db_tb_research, $db_tb_research2prototype, $researchid, $db_tb_researchfield,
           $db_tb_research2research, $db_tb_research2building, $config_gameversion, $user_id;

    // Anpassung des Forschungstitels
    $scan_data['research'] = str_replace(
        "(en) (mittels eines P&uuml;mpels)",
        "(en)(mittels eines P&uuml;mpels)",
        $scan_data['research']
    );

    $rid = find_research_id($scan_data['research'], true);
    if (empty($rid)) {
        echo "<div class='system_error'>Update der Forschung " . $scan_data['research'] . " fehlgeschlagen.</div>\n";

        return;
    }

    echo "<div class='system_notification'>Update der Forschung " . $scan_data['research'] . "</div>\n";

    // Forschungsgebiet heraussuchen
    $sql = "SELECT ID FROM `{$db_tb_researchfield}` WHERE `name`='" . $scan_data['gebiet'] . "';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $row    = $db->db_fetch_array($result);
    $gebiet = $row['ID'];

    if (empty($gebiet)) {
        $db->db_insert($db_tb_researchfield, array('name' => $scan_data['gebiet']))
            or error(GENERAL_ERROR, 'Could not insert information.', '', __FILE__, __LINE__);

        $gebiet = $db->db_insert_id();
        if (empty($gebiet)) {
            $gebiet = 1;
        }
    }

    // Abgeleitete Gebaeude, Forschungen und Prototypen entfernen
    $sql = "DELETE FROM `{$db_tb_research2building}` WHERE `rId`=" . $rid;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $sql = "DELETE FROM `{$db_tb_research2prototype}` WHERE `rId`=" . $rid;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    $sql = "DELETE FROM `{$db_tb_research2research}` WHERE `rOld`=" . $rid . " OR `rNew`=" . $rid;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    // Deklarationen aufsplitten
    $declares = "";
    if (!empty($scan_data['GivesDeclares'])) {
        foreach ($scan_data['GivesDeclares'] as $decl) {
            if (!empty($declares)) {
                $declares .= "<br />\n";
            }
            $declares .= $decl;
        }
    }

    // Objekte aufsplitten
    $objects = "";
    if (!empty($scan_data['NeededObject'])) {
        foreach ($scan_data['NeededObject'] as $obj) {
            if (!empty($objects)) {
                $objects .= "<br />\n";
            }
            $objects .= $obj;
        }
    }

    // Gebaeude-Stufen herausfiltern und in die DB eintragen
    $bldlvl = "";
    if (!empty($scan_data['GivesBuildingLevel'])) {
        foreach ($scan_data['GivesBuildingLevel'] as $lvl) {
            if (!empty($bldlvl)) {
                $bldlvl .= "<br />\n";
            }
            $bldlvl .= $lvl;

            $lvltext = explode(" Stufe ", $lvl);
            insert_building_on_research($scan_data['research'], $lvltext[0], $lvltext[1]);
        }
    }

    // Verteidigungsoptionen eintragen
    $defense = "";
    if (!empty($scan_data['GivesDefense'])) {
        foreach ($scan_data['GivesDefense'] as $def) {
            if (!empty($defense)) {
                $defense .= "<br />\n";
            }
            $defense .= $def;
        }
    }

    // Genetische Optionen eintragen
    $genetics = "";
    if (!empty($scan_data['GivesGenetics'])) {
        foreach ($scan_data['GivesGenetics'] as $gen) {
            if (!empty($genetics)) {
                $genetics .= "<br />\n";
            }
            $genetics .= $gen;
        }
    }

    $highscore = empty($scan_data['highscore']) ? 0 : $scan_data['highscore'];
    $fp        = empty($scan_data['FP']) ? 0 : $scan_data['FP'];

    // So, wir haben jetzt alles zusammen und können die Daten eintragen.
    $data = array(
        'description'  => $scan_data['description'],
        'FP'           => $fp,
        'Gebiet'       => $gebiet,
        'highscore'    => $highscore,
        'addcost'      => $scan_data['addcost'],
        'geblevels'    => $bldlvl,
        'declarations' => $declares,
        'defense'      => $defense,
        'objects'      => $objects,
        'genetics'     => $genetics,
        'gameversion'  => $config_gameversion,
        'reingestellt' => $user_id,
        'time'         => CURRENT_UNIX_TIME
    );

    $result = $db->db_update($db_tb_research, $data, "WHERE ID=" . $rid)
        or error(GENERAL_ERROR, 'Could not update research information.', '', __FILE__, __LINE__);

    // Check if the prototype is in the database and set the research/ship pair.
    if (!empty($scan_data['Prototyp'])) {
        $pid = find_ship_id($scan_data['Prototyp']);

        $db->db_insert($db_tb_research2prototype, array('rid' => $rid, 'pid' => $pid))
            or error(GENERAL_ERROR, 'Could not insert information.', '', __FILE__, __LINE__);
    }

    // Actual research depends on these researches
    if (!empty($scan_data['NeededResearch'])) {
        foreach ($scan_data['NeededResearch'] as $depres) {
            insert_research_on_research($depres, $scan_data['research']);
        }
    }

    // Actual research gives these researches
    if (!empty($scan_data['GivesResearch'])) {
        foreach ($scan_data['GivesResearch'] as $depres) {
            insert_research_on_research($scan_data['research'], $depres);
        }
    }

    // Actual research needs these buildings
    if (!empty($scan_data['NeededBuildings'])) {
        foreach ($scan_data['NeededBuildings'] as $depres) {
            insert_research_on_building($scan_data['research'], $depres);
        }
    }

    // Actual research gives these buildings
    if (!empty($scan_data['GivesBuilding'])) {
        foreach ($scan_data['GivesBuilding'] as $depres) {
            insert_building_on_research($scan_data['research'], $depres, 0);
        }
    }

    $researchid = $rid;
}

// ****************************************************************************
//
//
function find_building_id($name)
{
    global $db, $db_tb_gebaeude;

    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $name = $db->escape($name);

    // Änderungen für Gebäude, die bereits unter anderem Namen in der DB sind.
    if (strpos($name, "Glace") > 0) {
        $name = "Eiscrusher der Sirius Corp, Typ Glace la mine";
    }
    $name = str_replace("[Solar]", "(Solar)", $name);
    $name = str_replace("[orbital]", "(orbital)", $name);

    $name = str_replace("(Kampfbasis)", "", $name);
    $name = str_replace(" mittels Phasenfluxdehydrierung", "", $name);
    $name = str_replace("kleiner chemischer Fabrikkomplex", "Chemiekomplex", $name);
    $name = str_replace("Katzeundmausabwehrstockproduktionsfabrik", "Katze und Maus Stock Abwehrfabrik", $name);

    $sql = "SELECT `ID` FROM `{$db_tb_gebaeude}` WHERE name='" . $name . "';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    if (!empty($row)) {

        return $row['ID'];

    } else {
        // Not found, so insert new
        $result = $db->db_insert($db_tb_gebaeude, array('name' => $name))
            or error(GENERAL_ERROR, 'Could not insert geb information.', '', __FILE__, __LINE__);

        echo "<div class='doc_blue'>Neues Gebäude: " . $name . "</div>\n";

        return $db->db_insert_id();
    }
}

// ****************************************************************************
//
//
function insert_building_on_research($research, $building, $level)
{
    global $db, $db_tb_research2building;

    $resid = find_research_id($research, false);
    $bldid = find_building_id($building);

    $sql = "SELECT COUNT(*) AS Zahl FROM `{$db_tb_research2building}`
            WHERE `rId`=" . $resid . " AND `bId`=" . $bldid . " AND `lvl`=" . $level;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row    = $db->db_fetch_array($result);
    $anzahl = $row['Zahl'];

    if ($anzahl == 0) {
        $db->db_insert($db_tb_research2building, array('rId' => $resid, 'bId' => $bldid, 'lvl' => $level))
            or error(GENERAL_ERROR, 'Could not insert information.', '', __FILE__, __LINE__);
    }
}

// ****************************************************************************
//
//
function find_ship_id($shipname)
{
    global $db, $db_tb_schiffstyp;

    // Find first ship identifier
    $sql = "SELECT `ID` FROM `{$db_tb_schiffstyp}` WHERE `schiff`='" . $shipname . "';";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    // Not found, so insert new
    if (!empty($row)) {

        return $row['ID'];

    } else {
        $result = $db->db_insert($db_tb_schiffstyp, array('schiff' => $shipname))
            or error(GENERAL_ERROR, 'Could not insert geb information.', '', __FILE__, __LINE__);

        echo "<div class='doc_blue'>Neuer Schiffstyp: " . $shipname . "</div>\n";

        return $db->db_insert_id();
    }
}

// ****************************************************************************
//
//
function insert_research_on_research($oldres, $newres)
{
    global $db, $db_tb_research2research;

    $oldid = find_research_id($oldres, false);
    $newid = find_research_id($newres, false);

    $sql = "SELECT COUNT(*) AS Zahl FROM `{$db_tb_research2research}` WHERE `rNew`=" . $newid . " AND `rOld`=" . $oldid;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row    = $db->db_fetch_array($result);
    $anzahl = $row['Zahl'];

    if ($anzahl == 0) {
        $db->db_insert($db_tb_research2research, array('rOld' => $oldid, 'rNew' => $newid))
            or error(GENERAL_ERROR, 'Could not insert information.', '', __FILE__, __LINE__);
    }
}

// ****************************************************************************
//
//
function insert_research_on_building($research, $building)
{
    global $db, $db_tb_building2research;

    $resid = find_research_id($research, false);
    $bldid = find_building_id($building);

    $sql = "SELECT COUNT(*) AS Zahl FROM `{$db_tb_building2research}` WHERE `rId`=" . $resid . " AND `bId`=" . $bldid;
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    $row    = $db->db_fetch_array($result);
    $anzahl = $row['Zahl'];

    if ($anzahl == 0) {
        $result = $db->db_insert($db_tb_building2research, array('rId' => $resid,'bId' => $bldid))
            or error(GENERAL_ERROR, 'Could not insert information.', '', __FILE__, __LINE__);

    }
}

// ****************************************************************************
//
//
function finish_de_info_forschung()
{
    global $db, $db_tb_research2research, $db_tb_building2research,
           $db_tb_research2building, $db_tb_research;

    $db->db_update($db_tb_research, array('rLevel' => 0))
        or error(GENERAL_ERROR, 'Could not update information.', '', __FILE__, __LINE__);

    $sql = "SELECT DISTINCT `ID` AS rid FROM `{$db_tb_research}`;";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while (($research_data = $db->db_fetch_array($result)) !== false) {
        $sql = "SELECT DISTINCT `rNew` FROM `{$db_tb_research2research}` WHERE `rNew`=" . $research_data['rid'];

        $result2 = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result2);

        $sql = "SELECT COUNT(*) AS Zahl FROM `{$db_tb_building2research}` WHERE `rId`=" . $research_data['rid'];
        $result3 = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row2   = $db->db_fetch_array($result3);
        $anzahl = $row2['Zahl'];

        if ($anzahl == 0 && empty($row)) {
            $db->db_update($db_tb_research, array('rLevel' => 1), "WHERE ID=" . $research_data['rid'])
                or error(GENERAL_ERROR, 'Could not update information.', '', __FILE__, __LINE__);
        }
    }

    $db->db_free_result($result);
    $db->db_free_result($result2);
    $db->db_free_result($result3);

    $level  = 0;
    $anzahl = 0;

    while ($level == 0 || $anzahl > 0) {
        $level++;

        $sql = "SELECT count(*) as Zahl FROM `{$db_tb_research}` WHERE `rLevel`=" . $level;
        $result3 = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row2   = $db->db_fetch_array($result3);
        $anzahl = $row2['Zahl'];

        if ($anzahl > 0) {
            $sql = "SELECT DISTINCT `ID` AS rid FROM `{$db_tb_research}` WHERE `rLevel`=" . $level;
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            while (($research_data = $db->db_fetch_array($result)) !== false) {
                $sql = "SELECT `rNew` FROM `{$db_tb_research2research}` WHERE `rOld`=" . $research_data['rid'];

                $result3 = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

                while (($row2 = $db->db_fetch_array($result3)) !== false) {
                    $db->db_update($db_tb_research, array('rLevel' => $level + 1), "WHERE ID=" . $row2['rNew'])
                        or error(GENERAL_ERROR, 'Could not update information.', '', __FILE__, __LINE__);
                }
            }
        }
    }

    $sql = "SELECT DISTINCT `ID` AS rid FROM `{$db_tb_research}` WHERE `rLevel`=0";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

    while (($research_data = $db->db_fetch_array($result)) !== false) {
        $sql = "SELECT `bID` FROM `{$db_tb_building2research}` WHERE `rId`=" . $research_data['rid'];
        $result2 = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

        while (($row = $db->db_fetch_array($result2)) !== false) {
            $sql = "SELECT `rid` FROM `{$db_tb_research2building}` WHERE `bId`=" . $row['bID'];
            $result3 = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

            if (($row2 = $db->db_fetch_array($result3)) !== false) {
                $sql = "SELECT `rLevel` FROM `{$db_tb_research}` WHERE `ID`=" . $row2['rid'];
                $result4 = $db->db_query($sql)
                    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

                if (($row3 = $db->db_fetch_array($result4)) !== false) {
                    $db->db_update($db_tb_research, array('rLevel' => $row3['rLevel'] + 1), "WHERE ID=" . $research_data['rid'])
                        or error(GENERAL_ERROR, 'Could not update information.', '', __FILE__, __LINE__);
                }
            }
        }
    }
}