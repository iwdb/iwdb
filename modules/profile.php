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
if ($sitterlogin === false) {
    $sitterlogin = $user_sitterlogin;
}
?>
    <br>
    <table border="0" cellpadding="0" cellspacing="1" class="bordercolor">
        <tr>
            <td class="menutop" align="center">
                <a href="index.php?action=profile&sitterlogin=<?php echo urlencode($sitterlogin);?>&sid=<?php echo $sid;?>">Einstellungen</a>
            </td>
            <td class="menutop" align="center">
                <a href="index.php?action=profile&sitterlogin=<?php echo urlencode($sitterlogin);?>&uaction=editplaneten&sid=<?php echo $sid;?>">eigene
                    Planeten</a>
            </td>
            <td class="menutop" align="center">
                <a href="index.php?action=profile&sitterlogin=<?php echo urlencode($sitterlogin);?>&uaction=editpresets&sid=<?php echo $sid;?>">eigene
                    Presets</a>
            </td>
            <td class="menutop" align="center">
                <a href="index.php?action=profile&sitterlogin=<?php echo urlencode($sitterlogin);?>&uaction=gebaeude&sid=<?php echo $sid;?>">Gebäude
                    ausblenden</a>
            </td>
        </tr>
    </table>
    <br>
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
    case "gebaeude":
        include("./modules/profile_gebaeude.php");
        break;
    default:
        include("./modules/profile_editdata.php");
        break;
}
?>