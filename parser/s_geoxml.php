<?php
/*****************************************************************************
 * s_geoxml.php                                                              *
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
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum wenden:                                                   *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

/*****************************************************************************/
/* Geo-Scan-XML-Parser                                                       */
/* von [RoC]Thella                                                           */
/*****************************************************************************/

if (!defined('IRA')) {
    die('Hacking attempt...');
}

include_once "./parser/i_planet.php";

function parse_geoxml($scanlines)
{
    global $scan_data, $coords, $db_prefix;

    $scan_type = 'geoscan';
    $scan_data = reset_data();
    $cat       = "";
    foreach ($scanlines as $line) {
        if (strpos($line, "http://www.icewars.de/portal/kb/de/sb.php") !== false) {
            $link                       = html_entity_decode(trim($line)) . "&typ=xml";
            $xml                        = simplexml_load_file($link);
            $scan_data['coords_gal']    = (int)$xml->plani_data->koordinaten->gal;
            $scan_data['coords_sys']    = (int)$xml->plani_data->koordinaten->sol;
            $scan_data['coords_planet'] = (int)$xml->plani_data->koordinaten->pla;
            $scan_data['coords']        = $scan_data['coords_gal'] . ":" . $scan_data['coords_sys'] . ":" . $scan_data['coords_planet'];
            $scan_data['user']          = (string)$xml->plani_data->user->name;
            $scan_data['allianz']       = (string)$xml->plani_data->user->allianz_tag;
            $scan_data['typ']           = (string)$xml->plani_data->planeten_typ->name;
            $scan_data['objekt']        = (string)$xml->plani_data->objekt_typ->name;
            $ressourcen                 = $xml->plani_data->ressourcen_vorkommen->ressource;
            foreach ($ressourcen as $ressource) {
                $wert = ((string)$ressource->wert[0] * 100);
                switch ((int)$ressource->id) {
                    case 1:
                        $scan_data["eisengehalt"] = $wert;
                    case 4:
                        $scan_data["eisdichte"] = $wert;
                    case 5:
                        $scan_data["chemievorkommen"] = $wert;
                }
            }
            $ressourcen_tech_team = $xml->plani_data->ressourcen_vorkommen->ressource_tech_team;
            foreach ($ressourcen_tech_team as $ressource_tech_team) {
                $wert = ((string)$ressource_tech_team->wert[0] * 100);
                switch ((int)$ressource_tech_team->id) {
                    case 1:
                        $scan_data["tteisen"] = $wert;
                    case 4:
                        $scan_data["tteis"] = $wert;
                    case 5:
                        $scan_data["ttchemie"] = $wert;
                }
            }
            $scan_data['gravitation']       = (string)$xml->plani_data->gravitation;
            $scan_data['lebensbedingungen'] = ((string)$xml->plani_data->lebensbedingungen * 100);
            $scan_data['fmod']              = ((string)$xml->plani_data->modifikatoren->forschung * 100);
            $scan_data['kgmod']             = (string)$xml->plani_data->modifikatoren->gebaeude_bau->kosten;
            $scan_data['dgmod']             = (string)$xml->plani_data->modifikatoren->gebaeude_bau->dauer;
            $scan_data['ksmod']             = (string)$xml->plani_data->modifikatoren->schiff_bau->kosten;
            $scan_data['dsmod']             = (string)$xml->plani_data->modifikatoren->schiff_bau->dauer;
            foreach ($xml->plani_data->besonderheiten->besonderheit as $besonderheit) {
                if (stripos($besonderheit->name, "Nebel")) {
                    $nebula = (string)$besonderheit->name;
                }
            }
            $scan_data['reset_timestamp'] = (int)$xml->plani_data->reset_timestamp;
            $scan_data['geoscantime']     = (int)$xml->timestamp;
            switch (updateplanet()) {
                case 0:
                    echo "<div class='system_error'>Der Scan ist nicht komplett!</div>";
                    break;
                case 1:
                    echo "<div class='system_notification'>Planet " . $scan_data['coords'] . " aktualisiert.</div>";
                    break;
                case 2:
                    echo "<div class='system_notification'>Neuen Planeten " . $scan_data['coords'] . " hinzugefügt.</div>";
                    break;
                case 3:
                    echo "<div class='system_notification'>Neuer Planet " . $scan_data['coords'] . " . Planetendaten aktualisiert.</div>";
                    break;
            }
            if (isset($nebula)) {
                $sql = "UPDATE " . $db_prefix . "sysscans SET ";
                $sql .= " nebula='" . $nebula . "'";
                $sql .= " WHERE gal=" . $scan_data['coords_gal'];
                $sql .= " AND sys=" . $scan_data['coords_sys'];
            }
            $coords = $scan_data['coords'];
        }
    }
}

function display_geoxml()
{
    global $coords, $db, $db_tb_scans, $db_tb_allianzstatus, $user_planibilder;
    include("./modules/showplanet.php");
}