<?php
/*****************************************************************************
 * de_info_schiff.php                                                        *
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
 * Autor: masel <masel789@googlemail.com>                                    *
 * Datum: Januar 2013                                                        *
 *                                                                           *
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum wenden:                                                   *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

function parse_de_info_schiff($aParserData)
{
    global $db, $db_tb_schiffstyp;

    $ship_data['schiff'] = $aParserData->objResultData->strSchiffName;

    if (!empty($aParserData->objResultData->aCosts)) {

        $ship_data['kosten_eisen']   = 0;
        $ship_data['kosten_stahl']   = 0;
        $ship_data['kosten_vv4a']    = 0;
        $ship_data['kosten_chemie']  = 0;
        $ship_data['kosten_eis']     = 0;
        $ship_data['kosten_wasser']  = 0;
        $ship_data['kosten_energie'] = 0;
        $ship_data['kosten_bev']     = 0;

        foreach ($aParserData->objResultData->aCosts as $cost) {
            switch ($cost['strResourceName']) {
                case 'Eisen':
                    $ship_data['kosten_eisen'] = $cost['iResourceCount'];
                    break;
                case 'Stahl':
                    $ship_data['kosten_stahl'] = $cost['iResourceCount'];
                    break;
                case 'VV4A':
                    $ship_data['kosten_vv4a'] = $cost['iResourceCount'];
                    break;
                case 'chem. Elemente':
                    $ship_data['kosten_chemie'] = $cost['iResourceCount'];
                    break;
                case 'Eis':
                    $ship_data['kosten_eis'] = $cost['iResourceCount'];
                    break;
                case 'Wasser':
                    $ship_data['kosten_wasser'] = $cost['iResourceCount'];
                    break;
                case 'Energie':
                    $ship_data['kosten_energie'] = $cost['iResourceCount'];
                    break;
                case 'Bevölkerung':
                    $ship_data['kosten_bev'] = $cost['iResourceCount'];
                    break;
            }


        }

    }

    if (isset($aParserData->objResultData->iGschwdSol)) {
        $ship_data['GeschwindigkeitSol'] = $aParserData->objResultData->iGschwdSol;
    }
    if (isset($aParserData->objResultData->iGschwdGal)) {
        $ship_data['GeschwindigkeitGal'] = $aParserData->objResultData->iGschwdGal;
    }
    if (isset($aParserData->objResultData->bCanLeaveGalaxy)) {
        $ship_data['canLeaveGalaxy'] = $aParserData->objResultData->bCanLeaveGalaxy;
    }
    if (isset($aParserData->objResultData->bCanBeTransported)) {
        $ship_data['canBeTransported'] = $aParserData->objResultData->bCanBeTransported;
    }
    if (isset($aParserData->objResultData->iVerbrauchBrause)) {
        $ship_data['VerbrauchChemie'] = $aParserData->objResultData->iVerbrauchBrause;
    }
    if (isset($aParserData->objResultData->iVerbrauchEnergie)) {
        $ship_data['VerbrauchEnergie'] = $aParserData->objResultData->iVerbrauchEnergie;
    }

    if (isset($aParserData->objResultData->iAttack)) {
        $ship_data['angriff'] = $aParserData->objResultData->iAttack;
    }
    if (isset($aParserData->objResultData->strWeaponClass)) {
        $ship_data['waffenklasse'] = $aParserData->objResultData->strWeaponClass;
    }
    if (isset($aParserData->objResultData->iDefence)) {
        $ship_data['verteidigung'] = $aParserData->objResultData->iDefence;
    }
    if (isset($aParserData->objResultData->iArmour_kin)) {
        $ship_data['panzerung_kinetisch'] = $aParserData->objResultData->iArmour_kin;
    }
    if (isset($aParserData->objResultData->iArmour_electr)) {
        $ship_data['panzerung_elektrisch'] = $aParserData->objResultData->iArmour_electr;
    }
    if (isset($aParserData->objResultData->iArmour_grav)) {
        $ship_data['panzerung_gravimetrisch'] = $aParserData->objResultData->iArmour_grav;
    }
    if (isset($aParserData->objResultData->iShields)) {
        $ship_data['schilde'] = $aParserData->objResultData->iShields;
    }
    if (isset($aParserData->objResultData->iAccuracy)) {
        $ship_data['accuracy'] = $aParserData->objResultData->iAccuracy;
    }
    if (isset($aParserData->objResultData->iMobility)) {
        $ship_data['mobility'] = $aParserData->objResultData->iMobility;
    }
    if (isset($aParserData->objResultData->iNoEscort)) {
        $ship_data['numEscort'] = $aParserData->objResultData->iNoEscort;
    }
    if (isset($aParserData->objResultData->fBonusAtt)) {
        $ship_data['escortBonusAtt'] = $aParserData->objResultData->fBonusAtt;
    }
    if (isset($aParserData->objResultData->fBonusDef)) {
        $ship_data['escortBonusDef'] = $aParserData->objResultData->fBonusDef;
    }

    if (isset($aParserData->objResultData->strWerftTyp)) {
        $ship_data['werftTyp'] = $aParserData->objResultData->strWerftTyp;
    }
    if (isset($aParserData->objResultData->iProductionTime)) {
        $ship_data['dauer'] = $aParserData->objResultData->iProductionTime;
    }

    if (isset($aParserData->objResultData->bIsTransporter)) {
        $ship_data['isTransporter'] = $aParserData->objResultData->bIsTransporter;
    }
    if (isset($aParserData->objResultData->iKapa1)) {
        $ship_data['klasse1'] = $aParserData->objResultData->iKapa1;
    } else {
        $ship_data['klasse1'] = 0;
    }
    if (isset($aParserData->objResultData->iKapa2)) {
        $ship_data['klasse2'] = $aParserData->objResultData->iKapa2;
    } else {
        $ship_data['klasse2'] = 0;
    }
    if (isset($aParserData->objResultData->iKapaBev)) {
        $ship_data['bev'] = $aParserData->objResultData->iKapaBev;
    } else {
        $ship_data['bev'] = 0;
    }

    if (isset($aParserData->objResultData->bIsCarrier)) {
        $ship_data['isCarrier'] = $aParserData->objResultData->bIsCarrier;
    }
    if (isset($aParserData->objResultData->iShipKapa1)) {
        $ship_data['shipKapa1'] = $aParserData->objResultData->iShipKapa1;
    } else {
        $ship_data['shipKapa1'] = 0;
    }
    if (isset($aParserData->objResultData->iShipKapa2)) {
        $ship_data['shipKapa2'] = $aParserData->objResultData->iShipKapa2;
    } else {
        $ship_data['shipKapa2'] = 0;
    }
    if (isset($aParserData->objResultData->iShipKapa3)) {
        $ship_data['shipKapa3'] = $aParserData->objResultData->iShipKapa3;
    } else {
        $ship_data['shipKapa3'] = 0;
    }

    $ship_data['aktualisiert'] = CURRENT_UNIX_TIME;

    $db->db_insertupdate($db_tb_schiffstyp, $ship_data);

    doc_message($ship_data['schiff'] . ' aktualisiert');
}