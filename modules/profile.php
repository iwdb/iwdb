<?php
/*****************************************************************************
 * profile.php                                                               *
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
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

//****************************************************************************

$sitterlogin = getVar('sitterlogin');
$id = getVar('id');
if (empty($id)) {
    $id = $user_id;
    if (!empty($sitterlogin)) {     //masel: vorläufig
        $id = $sitterlogin;
    } else {
        $sitterlogin = $user_sitterlogin;
    }
}

if (($id !== $user_id) and ($user_status !== "admin")) {
    $id = $user_id;
    $sitterlogin = $user_sitterlogin;
}
?>
    <br>
    <table class="table_format">
        <tr>
            <td class="menutop center">
                <a href="index.php?action=profile&id=<?php echo urlencode($id);?>&sitterlogin=<?php echo urlencode($sitterlogin);?>">Einstellungen</a>
            </td>
            <td class="menutop center">
                <a href="index.php?action=profile&id=<?php echo urlencode($id);?>&sitterlogin=<?php echo urlencode($sitterlogin);?>&uaction=editplaneten">Planeten</a>
            </td>
			<td class="menutop center">
                <a href="index.php?action=profile&id=<?php echo urlencode($id);?>&sitterlogin=<?php echo urlencode($sitterlogin);?>&uaction=editress">Ressbedarf Planis</a>
            </td>
            <td class="menutop center">
                <a href="index.php?action=profile&id=<?php echo urlencode($id);?>&sitterlogin=<?php echo urlencode($sitterlogin);?>&uaction=editpresets">Presets</a>
            </td>
            <td class="menutop center">
                <a href="index.php?action=profile&id=<?php echo urlencode($id);?>&sitterlogin=<?php echo urlencode($sitterlogin);?>&uaction=gebaeude">Gebäude ausblenden</a>
            </td>
        </tr>
    </table>
    <br>
<?php

$uaction = getVar('uaction');
switch ($uaction) {
    case "editplaneten":
        include("./modules/profile_editplaneten.php");
        break;
    case "editpresets":
        include("./modules/profile_editpresets.php");
        break;
	case "editress":
        include("./modules/profile_editress.php");
        break;
    case "gebaeude":
        include("./modules/profile_gebaeude.php");
        break;
    default:
        include("./modules/profile_editdata.php");
        break;
}
?>