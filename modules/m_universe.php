<?php
/*****************************************************************************/
/* m_universe.php                                                            */
/*****************************************************************************/
/* Iw DB: Icewars geoscan and sitter database                                */
/* Open-Source Project started by Robert Riess (robert@riess.net)            */
/* Software Version: Iw DB 1.00                                              */
/* ========================================================================= */
/* Software Distributed by:    http://lauscher.riess.net/iwdb/               */
/* Support, News, Updates at:  http://lauscher.riess.net/iwdb/               */
/* ========================================================================= */
/* Copyright (c) 2004 Robert Riess - All Rights Reserved                     */
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
/* Diese Erweiterung der ursp�nglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens daf�r eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul �ber die index.php aufgerufen wurde. Kann unberechtigte Systemzugriffe verindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!"; 
	exit; 
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig f�r die Benennung der zugeh�rigen 
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung f�r 
//    eine Installation �ber das Men�
//
$modulname  = "m_universe";

//****************************************************************************
//
// -> Men�titel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "zeige Universum";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul �ber die Navigation 
//    ausf�hren darf. M�gliche Werte: 
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = 
  "Dieses Modul fügt eine Übersicht für das bekannte Universum " .
	"hinzu.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase() {
  echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu() {
    global $modultitle, $modulstatus, $_POST;
		
		$actionparamters = "";
  	insertMenuItem( $_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparameters );
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file.
//
function workInstallConfigString() {
  return 
    "\$config_lineheight   = 22; // vertical size of a single system (min of fontheight + 2)\n" .
    "\$config_linewidth    =  3; // horizontal size of a single system (min 2)\n" .
    "\n" .
    "\$config_borderleft   = 50; // min 25\n" .
    "\$config_borderright  = 25;\n" .
    "\$config_bordertop    = 40; // min 6 + \$config_lineheight\n" .
    "\$config_borderbottom = 25;\n" .
    "\n" .
    "\$config_font_to_use  =  2;\n";
}


//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase() {
    echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgef�hrt wenn das Modul mit dem Parameter 
// "install" aufgerufen wurde. Beispiel des Aufrufs: 
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" nat�rlich deinen Server angeben und default 
// durch den Dateinamen des Moduls ersetzen.
//
if( !empty($_REQUEST['was'])) {
  //  -> Nur der Admin darf Module installieren. (Meistens weis er was er tut)
  if ( $user_status != "admin" ) 
		die('Hacking attempt...');

  echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname . " ("  . $_REQUEST['was'] . ")</div>\n";

  if (!@include("./includes/menu_fn.php")) 
	  die( "Cannot load menu functions" );
    
  // Wenn ein Modul administriert wird, soll der Rest nicht mehr 
  // ausgef�hrt werden. 
  return;
}

if (!@include("./config/".$modulname.".cfg.php")) { 
	die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

echo "<div class='doc_title'>Universumsübersicht</doc>\n";
echo "<br>\n";
echo "<br>\n";

$font_width  = ImageFontWidth( $config_font_to_use );
$font_height = ImageFontHeight( $config_font_to_use );

// ----------------------------------------------------------------
// Retrieve the maximum system count for the horizontal dimension.
//
$sql = "SELECT MAX(sys) AS maxsys FROM " . $db_tb_sysscans;
$result = $db->db_query($sql)
  or error(GENERAL_ERROR, 
       'Could not query config information.', 
       '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);

$maxsys = $row['maxsys'];   
$db->db_free_result($result);

// ----------------------------------------------------------------
// Image dimensions (borders + inner part depending on linewidth 
// and height)
//
$imageheight = $config_bordertop  + $config_borderbottom + 
               ( $config_map_galaxy_count * $config_lineheight );
$imagewidth  = $config_borderleft + $config_borderright +  
               ( $maxsys * $config_linewidth );

// ----------------------------------------------------------------
// Create the image 
//
$graph = @ImageCreate($imagewidth, $imageheight)
  or error(GENERAL_ERROR, 
       'Could not create new GD image.', 
       '', __FILE__, __LINE__, $sql);

// ----------------------------------------------------------------
// Allocate base colors. 
//
$text_color       = ImageColorAllocate($graph, 255, 255, 255);
$grid_color       = ImageColorAllocate($graph, 160, 160, 160);
$background_color = ImageColorAllocate($graph,   0,   0,   0);

$stargate_color   = ImageColorAllocate($graph, 160, 191, 205);
$blackhole_color  = ImageColorAllocate($graph,  63, 103, 120);
$unscanned_color  = ImageColorAllocate($graph,  75,  75,   0);

$redcolor         = ImageColorAllocate($graph, 255,   0,   0);

// ----------------------------------------------------------------
// Clear image for drawing
//
ImageFilledRectangle(
    $graph, 
    0, 
    0, 
    $imagewidth, 
    $imageheight, 
    $background_color);

// ----------------------------------------------------------------
// Draw horizontal grid lines first
//
echo "<map name=\"universemap\">\n";
for( $i=0; $i<=$config_map_galaxy_count; $i++ ) { 
    if( $i != 0 ) {
        // Draw galaxy number
        ImageString( 
           $graph,
           $config_font_to_use, 
           $config_borderleft - (8 + (2 * $font_width)), 
           $config_bordertop + 6 + (($i-1) * $config_lineheight), 
           str_pad($i, 2, " ", STR_PAD_LEFT), 
           $text_color );

        // Determine the max extension for this galaxy
        $sql = "SELECT MAX(sys) AS maxextends FROM " . 
               $db_tb_sysscans . " WHERE gal=" . $i;
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 
                'Could not query config information.', 
                '', __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result);

        $maxextends = $row['maxextends']; 
        $db->db_free_result($result);

        // Fill galaxy first as unscanned
        ImageFilledRectangle( 
            $graph, 
            $config_borderleft + 1 + ($config_linewidth), 
            $config_bordertop + 1 + (($i-1) * $config_lineheight), 
            $config_borderleft + ($maxextends * $config_linewidth) , 
            $config_bordertop - 1 + ($i * $config_lineheight), 
            $unscanned_color );

  			echo "<area shape=\"rect\" coords=\"" . 
  				 ($config_borderleft + 1 + ($config_linewidth)) . "," .
  				 ($config_bordertop + 1 + (($i-1) * $config_lineheight)) . "," .
  				 ($config_borderleft + ($maxsys * $config_linewidth) ) . "," .
  				 ($config_bordertop - 1 + ($i * $config_lineheight)) . "\"". 
  				 " href=\"index.php?action=karte&amp;galaxy=" . $i . "&amp;sid=" . $sid . "\">\n";
    }

    // Draw the horizontal line 
    ImageLine( 
        $graph, 
        $config_borderleft - 5, 
        $config_bordertop  + ($i * $config_lineheight), 
        $config_borderleft + 5 + ($maxsys * $config_linewidth), 
        $config_bordertop  + ($i * $config_lineheight), 
        $grid_color );
}
echo "</map>\n";


// ----------------------------------------------------------------
// Draw each sysscan found in the database
//
$sql = "SELECT gal, sys, objekt, date FROM " . 
       $db_tb_sysscans . " ORDER BY sys DESC";
$result = $db->db_query($sql)
      or error(GENERAL_ERROR, 
            'Could not query config information.', 
            '', __FILE__, __LINE__, $sql);

// Helper array for the colors in the map.
$days_color = array();

while ( $row = $db->db_fetch_array($result) ) {
    // determine color for the actual system
    $color = $text_color;

    if ( $row['objekt'] == "Stargate" ) {
        $color = $stargate_color;
    } elseif ( $row['objekt'] == "Schwarzes Loch" ) {
        $color = $blackhole_color;
    } else {
        // Specials are checked, now check the date
        if ( $row['date'] < $config_date - $config_map_timeout ) {
            // System totally out of time frame.
            $color = $redcolor;
        } else {
            // Color is calculates by the same method as in "karte.php"
            $i = round(
                    ($row['date'] - $config_date + $config_map_timeout) /
                    ($config_map_timeout / 510)
                 );
            
            $gruen = ($i < 256) ? $i: 255;
            $rot   = ($i < 256) ? 255: 254 - ($i - 256);

            // create a hexadecimal color code to enter into the hash
            $x = str_pad(dechex($rot), 2, "0", STR_PAD_LEFT) 
               . str_pad(dechex($gruen), 2, "0", STR_PAD_LEFT) 
               . "00";

            // Allocate color if it does not exist.
            if( empty($days_color[$x])) {
                $days_color[$x] = 
                    ImageColorAllocate($graph, $rot, $gruen, 0);
            }
    
            $color = $days_color[$x];
          }
    }

    // Calculate starting edge for system and draw it into the graphic
    $x0 = $config_borderleft + 1 + (($row['sys'] ) * $config_linewidth);
    $y0 = $config_bordertop  + 1 + (($row['gal'] - 1 ) * $config_lineheight);

    ImageFilledRectangle( 
        $graph, 
        $x0, 
        $y0, 
        $x0 + $config_linewidth - 1, 
        $y0 + $config_lineheight - 2, 
        $color);
}

$db->db_free_result($result);

// ----------------------------------------------------------------
// All systems have been painted, draw the verical grid lines 
// over the map
//
ImageLine( 
    $graph, 
    $config_borderleft + 1 + ($config_linewidth), 
    $config_bordertop - 5, 
    $config_borderleft + 1 + ($config_linewidth), 
    $imageheight - $config_borderbottom + 5, 
    $grid_color );
                         
ImageString( 
    $graph,
    $config_font_to_use, 
    $config_borderleft + 1 + (2 * $config_linewidth), 
    $config_bordertop - (6 + $font_height), 
    "1", 
    $text_color );

for( $i=51; $i<$maxsys; $i+=50 ) {  
    ImageLine( 
        $graph, 
        $config_borderleft + 1 + (($i-1) * $config_linewidth), 
        $config_bordertop - 5, 
        $config_borderleft + 1 + (($i-1) * $config_linewidth), 
        $imageheight - $config_borderbottom + 5, 
        $grid_color );
                         
    ImageString( 
        $graph,
        $config_font_to_use, 
        $config_borderleft + 1 + ($i * $config_linewidth), 
        $config_bordertop - (6 + $font_height), 
        ($i-1), 
        $text_color );
}
    

// ----------------------------------------------------------------
// Finally: save image to disk and write link back to browser.
//
if (ImageTypes() & IMG_GIF) {
    ImageGif($graph, "universe.gif");
    echo "<img src=\"universe.gif\" border=\"0\" alt=\"Das Hasiversum\" usemap=\"#universemap\">";
}
elseif (ImageTypes() & IMG_JPG) {
    ImageJpeg($graph, "universe.jpg");
    echo "<img src=\"universe.jpg\" border=\"0\" alt=\"Das Hasiversum\" usemap=\"#universemap\">";
}
elseif (ImageTypes() & IMG_PNG) {
    ImagePng($graph, "universe.png");
    echo "<img src=\"universe.png\" border=\"0\" alt=\"Das Hasiversum\" usemap=\"#universemap\">";
}
else {
    echo "Keine Grafik-Unterst�tzung vorhanden";
}

ImageDestroy($graph);

echo "<br>\n";
echo "<br>\n";
echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"0\">\n";
echo " <tr>\n";
echo "  <td style=\"width: 30; background-color: " . $config_color['Stargate'] . "\"></td>\n";
echo "  <td style=\"width: 100;\">Stargate</td>\n";
echo "  <td style=\"width: 30; background-color: " . $config_color['SchwarzesLoch'] . "\"></td>\n";
echo "  <td style=\"width: 100;\">Schwarzes Loch</td>\n";
echo "  <td style=\"width: 30; background-color: #00FF00\"></td>\n";
echo "  <td style=\"width: 100;\">neu</td>\n";
echo "  <td style=\"width: 30; background-color: #FFFF00\"></td>\n";
echo "  <td style=\"width: 100;\">" . round( $config_map_timeout / 24 / 60 / 60 / 2). " Tage alt</td>\n";
echo "  <td style=\"width: 30; background-color: #FF0000\"></td>\n";
echo "  <td style=\"width: 100;\">älter als " . round( $config_map_timeout / 24 / 60 / 60). " Tage</td>\n";
echo "  <td style=\"width: 30; background-color: #4B4B00\"></td>\n";
echo "  <td style=\"width: 100;\">ungescannt</td>\n";
echo " </tr>\n";
echo "</table>\n";

?>
