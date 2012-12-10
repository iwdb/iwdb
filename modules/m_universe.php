<?php
/**
 * m_universe
 *
 * Dieses Modul stellt das bekannte Hasiversum grafisch dar.
 *
 * Bei Fragen oder Problemen kannst du dich im Entwicklerforum
 * ({@link https://handels-gilde.org/?www/forum/index.php;board=1099.0})
 * bzw
 * im Github Repo ({@link https://github.com/iwdb/iwdb}) melden.
 *
 * @author Robert Riess ?
 * @author masel <masel789@googlemail.com>
 * @license GNU GPL version 2 or any later version
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package iwdb
 * @subpackage module
 */
/*****************************************************************************/
//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
/*****************************************************************************/
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
$modulname = "m_universe";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "zeige Universum";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation
//    ausführen darf. Mögliche Werte:
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = "Dieses Modul stellt das bekannte Hasiversum grafisch dar.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    //nothing here
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modultitle, $modulstatus, $_POST;

    $actionparamters = "";
    insertMenuItem($_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparamters);
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file.
//
function workInstallConfigString()
{
    return
        "\$config_lineheight   = 22; // vertical size of a single system (min of fontheight + 2)\n" .
        "\$config_linewidth    =  3; // horizontal size of a single system (min 2)\n" .
        "\n" .
        "\$config_borderleft   = 50; // min 25\n" .
        "\$config_borderright  = 25;\n" .
        "\$config_bordertop    = 40; // min 6 + \$config_lineheight\n" .
        "\$config_borderbottom = 25;\n" .
        "\n" .
        "\$grid_color          = '#aaa';\n" .
        "\$background_color    = '#000';\n" .
        "\$text_color          = '#fff';\n" .
        "\$config_font_to_use  =  2;\n";
}


//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
function workUninstallDatabase()
{
    //nothing here
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgeführt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs: 
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" natürlich deinen Server angeben und default
// durch den Dateinamen des Moduls ersetzen.
//
if (!empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weis er was er tut)
    if ($user_status != "admin") {
        exit('Hacking attempt...');
    }

    echo "<div class='system_notification_blue'>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")</div>\n";

    include("./includes/menu_fn.php");

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    exit("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//some fallback for old installs
if (!isset($grid_color)) {
    $grid_color          = '#aaa';
}
if (!isset($background_color)) {
    $background_color    = '#000';
}
if (!isset($text_color)) {
    $text_color          = '#fff';
}

// -> Und hier beginnt das eigentliche Modul
echo "<div class='doc_title'>Universumsübersicht</div><br>\n";

$font_width = ImageFontWidth($config_font_to_use);
$font_height = ImageFontHeight($config_font_to_use);

// Image dimensions (borders + inner part depending on linewidth and height)
$imageheight = $config_bordertop + $config_borderbottom + ($config_map_galaxy_max * $config_lineheight);
$imagewidth = $config_borderleft + $config_borderright + ($config_map_system_max * $config_linewidth);

// svg start
$svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
$svg .= '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="' . $imagewidth . 'px" height="' . $imageheight . 'px">' . "\n";
$svg .= '<rect width="' . $imagewidth . '" height="' . $imageheight . '" fill="'.$background_color.'"/>' . "\n";
$svg .= '<g transform="translate(0.5,0.5)">' . "\n";

// Draw system numbers
$svg .= '<g fill="'.$text_color.'" font-size="'.$font_height.'" id="Systemnumbers">' . "\n";
$svg .= '<text x="' . ($config_borderleft - 4 + (2 * $config_linewidth)) . '" y="' . ($config_bordertop - ($font_height)) . '">  1</text>' . "\n";
for ($i = 51; $i <= $config_map_system_max + 1; $i += 50) {
    $svg .= '<text x="' . ($config_borderleft - 12 + ($i * $config_linewidth)) . '" y="' . ($config_bordertop - ($font_height)) . '">' . sprintf("%3u", ($i - 1)) . '</text>' . "\n";
}
$svg .= '</g>' . "\n";

// Draw galaxy numbers
$svg .= '<g fill="'.$text_color.'" font-size="'.$font_height.'" id="Galaxynumbers">' . "\n";
for ($i = 1; $i <= $config_map_galaxy_max; $i++) {
        $svg .= '<text x="' . ($config_borderleft - (10 + (2 * $font_width))) . '" y="' . ($config_bordertop + 17 + (($i - 1) * $config_lineheight)) . '">' . sprintf("%2u", $i) . '</text>' . "\n";
}
$svg .= '</g>' . "\n";

//fill galaxyarea as unscanned
$svg .= '<g fill="'.$config_color['unscanned'].'" id="unscanned area">' . "\n";
$existing_galas = array();
for ($i = 1; $i <= $config_map_galaxy_max; $i++) {
        $sql = "SELECT MAX(sys) AS maxextends FROM " . $db_tb_sysscans . " WHERE gal=" . $i;
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
        $row = $db->db_fetch_array($result);

        $maxextends = $row['maxextends'];
        $db->db_free_result($result);

        if (!empty($maxextends)) {

            $svg .= '<rect x="' . ($config_borderleft + 1 + ($config_linewidth)) . '" y="' . ($config_bordertop + (($i - 1) * $config_lineheight)) . '" width="' . ((($maxextends - 1) * $config_linewidth)) . '" height="' . ($config_lineheight) . '"/>' . "\n";
            $existing_galas[] = $i;

        }
}
$svg .= '</g>' . "\n";

// Draw each sys found in the database
$svg .= '<g id="systems">' . "\n";

$gal = 0;
$x_start = 0;
$y_start = 0;
$width = 0;
$color = '';
$old_color = '';
$star_gates = Array();
$black_holes = Array();

$sql = "SELECT gal, sys, objekt, date FROM " . $db_tb_sysscans . " ORDER BY gal,sys ASC";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $color = getScanAgeColor($row['date']);

    if ($row['gal'] === $gal) {
        if ($row['objekt'] == "Stargate") {
            $star_gates[] = array('x' => ($config_borderleft + (($row['sys']) * $config_linewidth)),
                'y' => ($config_bordertop + (($row['gal'] - 1) * $config_lineheight)));
            $width += $config_linewidth;
        } elseif ($row['objekt'] == "schwarzes Loch") {
            $black_holes[] = array('x' => ($config_borderleft + (($row['sys']) * $config_linewidth)),
                'y' => ($config_bordertop + (($row['gal'] - 1) * $config_lineheight)));
            $width += $config_linewidth;
        } else {
            if ($color === $old_color) {
                $width += $config_linewidth;
            } else {
                $svg .= '<rect x="' . ($x_start) . '" y="' . ($y_start) . '" width="' . ($width) . '" height="' . ($config_lineheight) . '" fill="'.$old_color.'"/>' . "\n";
                $gal = $row['gal'];
                $x_start = $config_borderleft + (($row['sys']) * $config_linewidth);
                $y_start = $config_bordertop + (($row['gal'] - 1) * $config_lineheight);
                $width = $config_linewidth;
                $old_color = $color;
            }
        }
    } else {
        if ($gal > 0) {
             $svg .= '<rect x="' . ($x_start) . '" y="' . ($y_start) . '" width="' . ($width) . '" height="' . ($config_lineheight) . '" fill="'.$old_color.'"/>' . "\n";
        }

        $gal = $row['gal'];
        $x_start = $config_borderleft + (($row['sys']) * $config_linewidth);
        $y_start = $config_bordertop + (($row['gal'] - 1) * $config_lineheight);
        $width = $config_linewidth;
        $old_color = $color;
    }

}
$db->db_free_result($result);
//draw the last sysarea
$svg .= '<rect x="' . ($x_start) . '" y="' . ($y_start) . '" width="' . ($width) . '" height="' . ($config_lineheight) . '" fill="'.$color.'"/>' . "\n";
$svg .= '</g>' . "\n";

//draw the stargates / spacestations
$svg .= '<g fill="' . $config_color['Stargate'] . '" id="stargates">' . "\n";
foreach ($star_gates as $gate) {
    $svg .= '<rect x="' . ($gate['x']) . '" y="' . ($gate['y']) . '" width="' . ($config_linewidth) . '" height="' . ($config_lineheight) . '"/>' . "\n";
}
$svg .= '</g>' . "\n";

//draw the black holes
$svg .= '<g fill="' . $config_color['SchwarzesLoch'] . '" id="black holes">' . "\n";
foreach ($black_holes as $hole) {
    $svg .= '<rect x="' . ($hole['x']) . '" y="' . ($hole['y']) . '" width="' . ($config_linewidth) . '" height="' . ($config_lineheight) . '"/>' . "\n";
}
$svg .= '</g>' . "\n";

// all systems have been painted, draw the grid lines over the map
$svg .= '<g stroke="'.$grid_color.'" id="grid lines">' . "\n";

// horizontal lines
for ($i = 0; $i <= $config_map_galaxy_max; $i++) {
    $svg .= '<line x1="' . ($config_borderleft - 5) . '" y1="' . ($config_bordertop + ($i * $config_lineheight)) . '" x2="' . ($config_borderleft + 5 + ($config_map_system_max * $config_linewidth)) . '" y2="' . ($config_bordertop + ($i * $config_lineheight)) . '"/>' . "\n";
}

//vertical
$svg .= '<line x1="' . ($config_borderleft + 1 + ($config_linewidth)) . '" y1="' . ($config_bordertop - 5) . '" x2="' . ($config_borderleft + 1 + ($config_linewidth)) . '" y2="' . ($imageheight - $config_borderbottom + 5) . '"/>' . "\n";
for ($i = 51; $i <= $config_map_system_max + 1; $i += 50) {
    $svg .= '<line x1="' . ($config_borderleft + 1 + (($i - 1) * $config_linewidth)) . '" y1="' . ($config_bordertop - 5) . '" x2="' . ($config_borderleft + 1 + (($i - 1) * $config_linewidth)) . '" y2="' . ($imageheight - $config_borderbottom + 5) . '"/>' . "\n";
}
$svg .= '</g>' . "\n";

// now draw the invisible clickable areas on the map locating to the maps page
$svg .= '<g fill-opacity="0" style="cursor:pointer;" id="clickable areas">' . "\n";
foreach ($existing_galas as $gala) {
    $svg .= '<rect x="' . ($config_borderleft + 1 + ($config_linewidth)) . '" y="' . ($config_bordertop + (($gala - 1) * $config_lineheight)) . '" width="' . ($config_map_system_max * $config_linewidth) . '" height="' . $config_lineheight . '" onclick="self.location.href=\'index.php?action=karte&galaxy='.$gala.'&sid='.$sid.'\';return false;"/>' . "\n";
}
$svg .= '</g>' . "\n";

$svg .= '</g>' . "\n";
$svg .= '</svg>';

//output svg inline
echo $svg;

//add legend
echo "<br>\n";
echo "<br>\n";
echo "<table style='border-spacing:0;'>\n";
echo " <tr>\n";
echo "  <td style='width: 4%; background-color: " . $config_color['Stargate'] . "'></td>\n";
echo "  <td style='width: 10%; padding:4px; text-align: left;'>Stargate</td>\n";
echo "  <td style='width: 4%; background-color: " . $config_color['SchwarzesLoch'] . "'></td>\n";
echo "  <td style='width: 14%; padding:4px; text-align: left'>Schwarzes Loch</td>\n";
echo "  <td style='width: 4%; background-color: " . $config_color['first24h'] . "'></td>\n";
echo "  <td style='width: 14%; padding:4px; text-align: left'>jünger 24 Stunden</td>\n";
echo "  <td style='width: 4%; background-color: #00FF00'></td>\n";
echo "  <td style='width: 14%; padding:4px; text-align: left'>älter 24 Stunden</td>\n";
echo "  <td style='width: 4%; background-color: #FFFF00'></td>\n";
echo "  <td style='width: 14%; padding:4px; text-align: left'>" . (round($config_map_timeout / 2 / DAY)) . " Tage alt</td>\n";
echo "  <td style='width: 4%; background-color: " . $config_color['scanoutdated'] . "'></td>\n";
echo "  <td style='width: 14%; padding:4px; text-align: left'>älter als " . (round($config_map_timeout / DAY)) . " Tage</td>\n";
echo " </tr>\n";
echo "</table>\n";
