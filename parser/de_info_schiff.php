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

function parse_de_info_schiff($return)
{
    if ($return->bSuccessfullyParsed) {
        global $db, $db_tb_schiffstyp;

        $ship_data['schiff'] = $return->objResultData->strSchiffName;

        if (!empty($return->objResultData->aCosts)) {

            $ship_data['kosten_eisen']   = 0;
            $ship_data['kosten_stahl']   = 0;
            $ship_data['kosten_vv4a']    = 0;
            $ship_data['kosten_chemie']  = 0;
            $ship_data['kosten_eis']     = 0;
            $ship_data['kosten_wasser']  = 0;
            $ship_data['kosten_energie'] = 0;
            $ship_data['kosten_bev']     = 0;

            foreach ($return->objResultData->aCosts as $cost) {
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

        if (isset($return->objResultData->iGschwdSol)) {
            $ship_data['GeschwindigkeitSol'] = $return->objResultData->iGschwdSol;
        }
        if (isset($return->objResultData->iGschwdGal)) {
            $ship_data['GeschwindigkeitGal'] = $return->objResultData->iGschwdGal;
        }
        if (isset($return->objResultData->bCanLeaveGalaxy)) {
            $ship_data['canLeaveGalaxy'] = $return->objResultData->bCanLeaveGalaxy;
        }
        if (isset($return->objResultData->bCanBeTransported)) {
            $ship_data['canBeTransported'] = $return->objResultData->bCanBeTransported;
        }
        if (isset($return->objResultData->iVerbrauchBrause)) {
            $ship_data['VerbrauchChemie'] = $return->objResultData->iVerbrauchBrause;
        }
        if (isset($return->objResultData->iVerbrauchEnergie)) {
            $ship_data['VerbrauchEnergie'] = $return->objResultData->iVerbrauchEnergie;
        }

        if (isset($return->objResultData->iAttack)) {
            $ship_data['angriff'] = $return->objResultData->iAttack;
        }
        if (isset($return->objResultData->strWeaponClass)) {
            $ship_data['waffenklasse'] = $return->objResultData->strWeaponClass;
        }
        if (isset($return->objResultData->iDefence)) {
            $ship_data['verteidigung'] = $return->objResultData->iDefence;
        }
        if (isset($return->objResultData->iArmour_kin)) {
            $ship_data['panzerung_kinetisch'] = $return->objResultData->iArmour_kin;
        }
        if (isset($return->objResultData->iArmour_electr)) {
            $ship_data['panzerung_elektrisch'] = $return->objResultData->iArmour_electr;
        }
        if (isset($return->objResultData->iArmour_grav)) {
            $ship_data['panzerung_gravimetrisch'] = $return->objResultData->iArmour_grav;
        }
        if (isset($return->objResultData->iShields)) {
            $ship_data['schilde'] = $return->objResultData->iShields;
        }
        if (isset($return->objResultData->iAccuracy)) {
            $ship_data['accuracy'] = $return->objResultData->iAccuracy;
        }
        if (isset($return->objResultData->iMobility)) {
            $ship_data['mobility'] = $return->objResultData->iMobility;
        }
        if (isset($return->objResultData->iNoEscort)) {
            $ship_data['numEscort'] = $return->objResultData->iNoEscort;
        }
        if (isset($return->objResultData->fBonusAtt)) {
            $ship_data['escortBonusAtt'] = $return->objResultData->fBonusAtt;
        }
        if (isset($return->objResultData->fBonusDef)) {
            $ship_data['escortBonusDef'] = $return->objResultData->fBonusDef;
        }

        if (isset($return->objResultData->strWerftTyp)) {
            $ship_data['werftTyp'] = $return->objResultData->strWerftTyp;
        }
        if (isset($return->objResultData->iProductionTime)) {
            $ship_data['dauer'] = $return->objResultData->iProductionTime;
        }

        if (isset($return->objResultData->bIsTransporter)) {
            $ship_data['isTransporter'] = $return->objResultData->bIsTransporter;
        }
        if (isset($return->objResultData->iKapa1)) {
            $ship_data['klasse1'] = $return->objResultData->iKapa1;
        } else {
            $ship_data['klasse1'] = 0;
        }
        if (isset($return->objResultData->iKapa2)) {
            $ship_data['klasse2'] = $return->objResultData->iKapa2;
        } else {
            $ship_data['klasse2'] = 0;
        }
        if (isset($return->objResultData->iKapaBev)) {
            $ship_data['bev'] = $return->objResultData->iKapaBev;
        } else {
            $ship_data['bev'] = 0;
        }

        if (isset($return->objResultData->bIsCarrier)) {
            $ship_data['isCarrier'] = $return->objResultData->bIsCarrier;
        }
        if (isset($return->objResultData->iShipKapa1)) {
            $ship_data['shipKapa1'] = $return->objResultData->iShipKapa1;
        } else {
            $ship_data['shipKapa1'] = 0;
        }
        if (isset($return->objResultData->iShipKapa2)) {
            $ship_data['shipKapa2'] = $return->objResultData->iShipKapa2;
        } else {
            $ship_data['shipKapa2'] = 0;
        }
        if (isset($return->objResultData->iShipKapa3)) {
            $ship_data['shipKapa3'] = $return->objResultData->iShipKapa3;
        } else {
            $ship_data['shipKapa3'] = 0;
        }

        $ship_data['aktualisiert'] = CURRENT_UNIX_TIME;

        $db->db_insertupdate($db_tb_schiffstyp, $ship_data)
            or error(GENERAL_ERROR, 'Could not insert ship information.', '', __FILE__, __LINE__);

        doc_message($ship_data['schiff'] . ' aktualisiert');
    }
}