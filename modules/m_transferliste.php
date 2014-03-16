<?php
/*****************************************************************************
 * m_transferliste.php                                                       *
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
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
global $modulname;
$modulname = "m_transferliste";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Transfer-Statistik";

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
$moduldesc =
    "In der Buddler-Statistik werden die an die Fleeter transferierten Rohstoffe " .
        "erfasst und statistisch aufbereitet.";

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
    global $modultitle, $modulstatus;

    $menu    = getVar('menu');
    $submenu = getVar('submenu');

    $actionparameters = "";
    insertMenuItem($menu, $submenu, $modultitle, $modulstatus, $actionparameters);
    //
    // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
    //
    //    insertMenuItem( $_POST['menu'], ($_POST['submenu']+1), "Titel2", "hc", "&weissichnichtwas=1" );
    //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed
// for the configuration file.
//
function workInstallConfigString()
{
    return
        "\n" .
        "// Faktoren zur Punkteberechnung\n" .
        "\$fakt_eisen                 =   2;   // Default:  2\n" .
        "\$fakt_stahl                 =   5;   // Default:  5\n" .
        "\$fakt_chem                  =   2;   // Default:  2\n" .
        "\$fakt_vv4a                  =  11;   // Default: 11\n" .
        "\$fakt_eis                   =   2;   // Default:  2\n" .
        "\$fakt_wasser                =   6;   // Default:  6\n" .
        "\$fakt_energie               =   1;   // Default:  1\n" .
        "\$fakt_volk                  =   0;   // Default:  0\n" .
        "// In der Buddlerübersicht Grafik anzeigen (True / False ) ?\n" .
        "\$show_buddler_graph         =  True;\n" .
        "// Groesse des Diagramms\n" .
        "\$graph_xsize                = 750;\n" .
        "\$graph_ysize                = 200;\n" .
        "// Raender bis zu den Achsen\n" .
        "// Wenn mehr als 1 Buddler angezeigt muss eine Farblegende gezeichnet werden\n" .
        "// die mehr Platz benoetigt\n" .
        "\$config_borderleft          =  50;\n" .
        "\$config_borderright         =  50;\n" .
        "\$config_bordertop           =  20;\n" .
        "\$config_borderbottom        =  35;\n" .
        "\$config_borderright_legende = 150;\n" .
        "// Typ des Diagramms ('l' => Linien / 'b' => Balken )\n" .
        "\$buddler_graph_typ          = 'l';\n" .
        "// Standard Sortierrichtung Gesamttransferliste\n" .
        "\$default_order              = 'punkte';\n" .
        "\$default_ordered            = 'desc';\n" .
        "// Standard Sortierrichtung Buddlertransferliste\n" .
        "\$default_buddler_order      = 'zeitmarke';\n" .
        "\$default_buddler_ordered    = 'desc';\n";

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
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
    if ($user_status != "admin") {
        die('Hacking attempt...');
    }

    echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")</div>\n";

    require_once './includes/menu_fn.php';

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

// ****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

// Globale Definitionen, da diese Datei vom Scanner-Modul eingebunden
// werden kann. Fehlen dann diese Definitionen, koennen die Daten nicht
// gelesen werden.

global $db, $db_tb_transferliste;

$order      = getVar('order');
$ordered    = getVar('ordered');
$graph      = getVar('graph');
$graph_user = getVar('graph_user', true);

doc_title("Transfer-Statistik");

$selbuddler     = getVar('selbuddler');
$seltransferday = getVar('seltransferday');
if (empty($selbuddler)) {
    showallfleeters();
} else {
    if (empty($seltransferday)) {
        showbuddler($selbuddler);
    } else {
        showbuddlertransfers($selbuddler, $seltransferday);
    }
}
// ****************************************************************************
//
function make_link_transferday($styleclass, $order, $ordered, $buddler, $transferday)
{
    # Links 1 Tag vor & zurück
    echo "<table border='0' width='100%'>\n";
    echo "<tr>\n";
    $zeitmarke = $transferday - DAY;
    echo "  <td class='" . $styleclass . " left'><b>\n";
    make_link(
        "&lt;&lt; " . strftime(CONFIG_DATEFORMAT, $zeitmarke),
        array(
             "selbuddler=" . $buddler,
             "seltransferday=" . $zeitmarke,
             "order=" . $order,
             "ordered=" . $ordered
        )
    );
    echo "  </b></td>\n";
    echo "  <td class='" . $styleclass . " right'><b>\n";
    $zeitmarke = $transferday + DAY;
    make_link(
        strftime(CONFIG_DATEFORMAT, $zeitmarke) . " &gt;&gt;",
        array(
             "selbuddler=" . $buddler,
             "seltransferday=" . $zeitmarke,
             "order=" . $order,
             "ordered=" . $ordered
        )
    );
    echo "  </b></td>\n";
    echo "</tr>\n";
    echo "</table>\n";
}

// ****************************************************************************
//
function make_order_link($order, $ordered, $parameters = array())
{
    $link = "<a href='index.php?action=m_transferliste";
    $link .= "&order=" . $order;
    $link .= "&ordered=" . $ordered;
    foreach ($parameters as $parameter) {
        $link .= "&" . $parameter;
    }
    $link .= "'> ";
    $link .= "  <img src='".BILDER_PATH."" . $ordered . ".gif' alt='" . $ordered . "'> ";
    $link .= "</a>";
    echo $link;
}

// ****************************************************************************
//
function make_link($name, $parameters)
{
    $link = "<a href='index.php?action=m_transferliste";
    foreach ($parameters as $parameter) {
        $link .= "&" . $parameter;
    }
    $link .= "'> ";
    $link .= $name . "</a>";
    echo $link;
}

// ****************************************************************************
//
function build_graph_transfer($users, $fitthis, $date_min, $date_max, $typ)
{
    global $db,
           $order,
           $ordered,
           $fakt_eisen,
           $fakt_stahl,
           $fakt_chem,
           $fakt_vv4a,
           $fakt_eis,
           $fakt_wasser,
           $fakt_energie,
           $fakt_volk,
           $default_buddler_order,
           $default_buddler_ordered,
           $graph_xsize,
           $graph_ysize,
           $config_borderleft,
           $config_borderright,
           $config_bordertop,
           $config_borderbottom,
           $config_borderright_legende,
           $db_tb_transferliste;


    if (!isset($config_borderleft)) {
        $config_borderleft = 50;
    }
    if (!isset($config_borderright)) {
        $config_borderright = 50;
    }
    if (!isset($config_bordertop)) {
        $config_borderleft = 20;
    }
    if (!isset($config_borderbottom)) {
        $config_borderright = 35;
    }

    if ((count($users) > 1) && isset($config_borderright_legende)) {
        $config_borderright = $config_borderright_legende;
    }

    $value_max = 1;
    if (count($users) > 0) {
        $value_min = 0;

        // Maximum für Y - Achse ermitteln
        // um ohne Unterabfragen auszukommen von max zu min sortieren und dann das 1. nehmen
        foreach ($users as $buddler) {
            $sql = "SELECT " .
                " SUM(eisen) * " . ((isset($fakt_eisen) && floatval($fakt_eisen) >= 0) ? $fakt_eisen : 1) . "+" .
                " SUM(stahl) * " . ((isset($fakt_stahl) && floatval($fakt_stahl) >= 0) ? $fakt_stahl : 1) . "+" .
                " SUM(vv4a) * " . ((isset($fakt_vv4a) && floatval($fakt_vv4a) >= 0) ? $fakt_vv4a : 1) . "+" .
                " SUM(chem) * " . ((isset($fakt_chem) && floatval($fakt_chem) >= 0) ? $fakt_chem : 1) . "+" .
                " SUM(eis) * " . ((isset($fakt_eis) && floatval($fakt_eis) >= 0) ? $fakt_eis : 1) . "+" .
                " SUM(wasser) * " . ((isset($fakt_wasser) && floatval($fakt_wasser) >= 0) ? $fakt_wasser : 1) . "+" .
                " SUM(energie) * " . ((isset($fakt_energie) && floatval($fakt_energie) >= 0) ? $fakt_energie : 1) . "+" .
                " SUM(volk) * " . ((isset($fakt_volk) && floatval($fakt_volk) >= 0) ? $fakt_volk : 1) . " AS punkte" .
                " FROM " . $db_tb_transferliste .
                " WHERE buddler='" . $buddler . "' " .
                " AND UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) " .
                "     >=UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( " . $date_min . " ) ) ) " .
                " AND UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) " .
                "     <=UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( " . $date_max . " ) ) ) " .
                " GROUP BY UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) " .
                " ORDER BY Punkte DESC";

            $result = $db->db_query($sql);
            $value = 0;
            if ($row = $db->db_fetch_array($result)) {
                $value = $row['punkte'];
            }
            if ($value > $value_max) {
                $value_max = $value;
            }
        }
    }

    $value_grid = ($graph_ysize - $config_bordertop - $config_borderbottom) / ($value_max);
    $value_text = "Punkte";

    $date_diff = ($date_max - $date_min) == 0 ? 1 : ($date_max - $date_min) + DAY;
    $date_grid = ($graph_xsize - $config_borderleft - $config_borderright) / $date_diff;

    $user_max = count($users);

    $graph = @imagecreatetruecolor($graph_xsize, $graph_ysize);
    if ($graph === false) {
        error(GENERAL_ERROR, 'Could not create new GD image.', '', __FILE__, __LINE__);
    }

    $font_width  = ImageFontWidth(2);
    $font_height = ImageFontHeight(2);

    //$background_color = ImageColorAllocate($graph, 234, 235, 255);
    $background_color = ImageColorAllocate($graph, 255, 255, 255);
    $text_color       = ImageColorAllocate($graph, 0, 0, 0);

    // Koordinatensystem //
    // x-line
    ImageLine(
        $graph,
        $config_borderleft - 5,
        $graph_ysize - $config_borderbottom + 1,
        $graph_xsize - $config_borderright,
        $graph_ysize - $config_borderbottom + 1,
        $text_color
    );
    // Pfeilspitze
    ImageFilledPolygon(
        $graph,
        array(
             $graph_xsize - $config_borderright,
             $graph_ysize - $config_borderbottom + 1,
             $graph_xsize - $config_borderright - 5,
             $graph_ysize - $config_borderbottom - 2,
             $graph_xsize - $config_borderright - 5,
             $graph_ysize - $config_borderbottom + 4
        ),
        3,
        $text_color
    );

    ImageLine(
        $graph,
        ($graph_xsize + $config_borderleft - $config_borderright) / 2,
        $graph_ysize - $config_borderbottom + 1,
        ($graph_xsize + $config_borderleft - $config_borderright) / 2,
        $graph_ysize - $config_borderbottom - 4,
        $text_color
    );

    $date = $date_min;
    $i    = 0;
    $div  = intval(($date_max - $date_min) / (4 * 24 * 3600));
    while ($date < $date_max) {
        $xvalue   = ($typ == "l")
            ?
            $config_borderleft + ($date - $date_min) * $date_grid
            :
            $config_borderleft + ($date - $date_min + 12 * 3600) * $date_grid;
        $line_len = ($i % $div == 0) ? 6 : 3;

        // Beschriftung - Text
        if (($date <> $date_min) || ($typ <> "l")) {
            ImageLine(
                $graph,
                $xvalue,
                $graph_ysize - $config_borderbottom + 1,
                $xvalue,
                $graph_ysize - $config_borderbottom + $line_len,
                $text_color
            );
        }

        // Beschriftung - Linie
        $zeit = strftime(CONFIG_DATETIMEFORMAT, $date);
        if ($i % $div == 0) {
            ImageString(
                $graph,
                2,
                $xvalue - (strlen($zeit) * $font_width / 2),
                $graph_ysize - $config_borderbottom + 6,
                $zeit,
                $text_color
            );
        }

        $i++;
        $date += DAY;
    }

    // y-line
    ImageLine(
        $graph,
        $config_borderleft - 1,
        $config_bordertop,
        $config_borderleft - 1,
        $graph_ysize - $config_borderbottom + 5,
        $text_color
    );
    // Pfeilspitze
    ImageFilledPolygon(
        $graph,
        array(
             $config_borderleft - 1,
             $config_bordertop,
             $config_borderleft - 4,
             $config_bordertop + 5,
             $config_borderleft + 2,
             $config_bordertop + 5
        ),
        3,
        $text_color
    );
    // Beschriftung
    ImageString(
        $graph,
        2,
        $config_borderleft + 6,
        $config_bordertop + 4,
        $value_text,
        $text_color
    );

    for ($c = $graph_ysize - $config_borderbottom; $c > $config_bordertop; $c = $c - 64) {
        $wert = round(($graph_ysize - $config_borderbottom - $c) / ($value_grid == 0 ? 1 : $value_grid));
        // Linie
        ImageLine(
            $graph,
            $config_borderleft - 1,
            $c + 1,
            $config_borderleft + 4,
            $c + 1,
            $text_color
        );
        // Beschriftung
        ImageString(
            $graph,
            2,
            $config_borderleft - 6 - strlen($wert) * $font_width,
            $c - $font_height / 2 + 1,
            $wert,
            $text_color
        );
    }


    // User Graphen //
    $ic = 0;
    if (count($users) > 0) {
        foreach ($users as $buddler) {

            $xbefore = "";
            $ybefore = "";
            $ic++;

            $i     = 1530 / $user_max * $ic;
            $gruen = ($i < 256) ? $i : (($i < 766) ? 255 : (($i < 1021) ? 254 - ($i - 766) : 0));
            $blau  = ($i < 511) ? 0 : (($i < 766) ? $i - 510 : (($i < 1276) ? 255 : 254 - ($i - 1276)));
            $rot   = ($i < 256) ? 255 : (($i < 511) ? 254 - ($i - 256) : (($i < 1021) ? 0 : (($i < 1276) ? $i - 1020 : 255)));
            $color = ImageColorAllocate($graph, $rot, $gruen, $blau);


            $sql = "SELECT UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) as zeitmarke, " .
                " SUM(eisen) * " . ((isset($fakt_eisen) && floatval($fakt_eisen) >= 0) ? $fakt_eisen : 1) . "+" .
                " SUM(stahl) * " . ((isset($fakt_stahl) && floatval($fakt_stahl) >= 0) ? $fakt_stahl : 1) . "+" .
                " SUM(vv4a) * " . ((isset($fakt_vv4a) && floatval($fakt_vv4a) >= 0) ? $fakt_vv4a : 1) . "+" .
                " SUM(chem) * " . ((isset($fakt_chem) && floatval($fakt_chem) >= 0) ? $fakt_chem : 1) . "+" .
                " SUM(eis) * " . ((isset($fakt_eis) && floatval($fakt_eis) >= 0) ? $fakt_eis : 1) . "+" .
                " SUM(wasser) * " . ((isset($fakt_wasser) && floatval($fakt_wasser) >= 0) ? $fakt_wasser : 1) . "+" .
                " SUM(energie) * " . ((isset($fakt_energie) && floatval($fakt_energie) >= 0) ? $fakt_energie : 1) . "+" .
                " SUM(volk) * " . ((isset($fakt_volk) && floatval($fakt_volk) >= 0) ? $fakt_volk : 1) . " AS punkte" .
                " FROM " . $db_tb_transferliste .
                " WHERE buddler='" . $buddler . "' " .
                " AND UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) " .
                "     >=UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( " . $date_min . " ) ) ) " .
                " AND UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) " .
                "     <=UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( " . $date_max . " ) ) ) " .
                " GROUP BY UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) " .
                " ORDER BY UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) ASC";

            $result = $db->db_query($sql);

            while ($row = $db->db_fetch_array($result)) {
                $xactual = $config_borderleft + ($row['zeitmarke'] - $date_min) * $date_grid;
                // Rundungsfehler korrigieren
                if ($xactual < $config_borderleft) {
                    $xactual = $config_borderleft;
                }
                $yactual = $graph_ysize - $config_borderbottom - $row['punkte'] * $value_grid;

                if ($typ == "l") {
                    //nullwerte nachzeichnen
                    if ((!empty($datebefore)) && ($row['zeitmarke'] - $datebefore > 24 * 3600)) {
                        ImageLine(
                            $graph,
                            $xbefore,
                            $ybefore,
                            $config_borderleft + ($datebefore + 24 * 3600 - $date_min) * $date_grid,
                            $graph_ysize - $config_borderbottom,
                            $color
                        );
                        $xbefore = $config_borderleft + ($row['zeitmarke'] - $date_min - 24 * 3600) * $date_grid;
                        $ybefore = $graph_ysize - $config_borderbottom;
                    }
                    if (!empty($xbefore)) {
                        //Linie malen - vom letzten Punkt zum aktuellen
                        ImageLine(
                            $graph,
                            $xbefore,
                            $ybefore,
                            $xactual,
                            $yactual,
                            $color
                        );
                    }
                    /* else
                    {
                      //Linie malen von x Achse
                      $xleft = $config_borderleft + ($row['zeitmarke'] - $date_min) * $date_grid;
                      ImageLine( $graph,
                                 $xleft,
                                 $graph_ysize - $config_borderbottom,
                                 $xactual,
                                 $yactual,
                                 $color
                               );
            } */
                } else {
                    $xright = $config_borderleft + ($row['zeitmarke'] + 24 * 3600 - $date_min) * $date_grid;
                    // Balken malen
                    ImageFilledRectangle(
                        $graph,
                        $xright,
                        $graph_ysize - $config_borderbottom,
                        $xactual,
                        $yactual,
                        $color
                    );
                }
                $xbefore    = $xactual;
                $ybefore    = $yactual;
                $datebefore = $row['zeitmarke'];
            }
            if ($typ == "l") {
                //nullwerte nachzeichnen
                if ((!empty($datebefore)) && ($date_max - $datebefore > 24 * 3600)) {
                    ImageLine(
                        $graph,
                        $xbefore,
                        $ybefore,
                        $config_borderleft + ($datebefore + 24 * 3600 - $date_min) * $date_grid,
                        $graph_ysize - $config_borderbottom,
                        $color
                    );
                }
            }

            if (count($users) > 1) {
                // Legende malen
                ImageFilledRectangle(
                    $graph,
                    $graph_xsize - $config_borderright + 20,
                    $graph_ysize - $config_borderbottom - 18 - ($font_height + 5) * $ic,
                    $graph_xsize - $config_borderright + 20 + $font_height,
                    $graph_ysize - $config_borderbottom - 22 - ($font_height + 5) * $ic + $font_height,
                    $color
                );
                ImageString(
                    $graph,
                    2,
                    $graph_xsize - $config_borderright + 25 + $font_height,
                    $graph_ysize - $config_borderbottom - 20 - ($font_height + 5) * $ic,
                    $buddler,
                    $text_color
                );
            }
        }

        if (ImageTypes() & IMG_GIF) {
            ImageGif($graph, "graph_transport.gif");
            echo "<img src='graph_transport.gif' alt='Graph'>";
        } elseif (ImageTypes() & IMG_JPG) {
            ImageJpeg($graph, "graph_transport.jpg");
            echo "<img src='graph_transport.jpg' alt='Graph'>";
        } elseif (ImageTypes() & IMG_PNG) {
            ImagePng($graph, "graph_transport.png");
            echo "<img src='graph_transport.png' alt='Graph'>";
        } else {
            echo "Keine Grafik-Unterstützung vorhanden";
        }
    } else {
        echo "<div style='color:#FF0000'><b>Du musst mindestens einen User auswählen.</b></div>";
    }

    if (count($users) > 22) {
        echo "<div style='color:#FF0000'><b>Du solltest weniger User auswählen.</b></div>";
    }
    ImageDestroy($graph);
}

// ****************************************************************************
//
function showbuddlertransfers($buddler, $transferday)
{
    global $db,
           $order,
           $ordered,
           $fakt_eisen,
           $fakt_stahl,
           $fakt_chem,
           $fakt_vv4a,
           $fakt_eis,
           $fakt_wasser,
           $fakt_energie,
           $fakt_volk,
           $default_buddler_order,
           $default_buddler_ordered,
           $db_tb_transferliste;

    if (empty($order)) {
        $order = $default_buddler_order;
    }
    if (empty($ordered)) {
        $ordered = $default_buddler_ordered;
    }

    start_table();
    /* start_row("windowbg1 center", "style='width:95%' colspan='10'");
      make_link_transferday("windowbg1",$order, $ordered, $buddler,$transferday); */
    start_row("titlebg center", "style='width:95%' colspan='10'");
    echo "<b>" . $buddler . "</b>";
    next_row("windowbg2 center", "style='width:14%'");
    make_order_link("zeitmarke", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo " Datum ";
    make_order_link("zeitmarke", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>Empfänger';
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("eisen", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>';
    echo "Eisen";
    echo '<br>';
    make_order_link("eisen", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("stahl", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>';
    echo "Stahl";
    echo '<br>';
    make_order_link("stahl", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("vv4a", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>';
    echo "VV4A";
    echo '<br>';
    make_order_link("vv4a", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("chem", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>';
    echo "Chemie";
    echo '<br>';
    make_order_link("chem", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("eis", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>';
    echo "Eis";
    echo '<br>';
    make_order_link("eis", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("wasser", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>';
    echo "Wasser";
    echo '<br>';
    make_order_link("wasser", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("energie", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>';
    echo "Energie";
    echo '<br>';
    make_order_link("energie", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("volk", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>';
    echo "Volk";
    echo '<br>';
    make_order_link("volk", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("punkte", "asc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));
    echo '<br>';
    echo "Punkte";
    echo '<br>';
    make_order_link("punkte", "desc", array("selbuddler=" . $buddler, "seltransferday=" . $transferday));

    $count   = 0;
    $eisen   = 0;
    $stahl   = 0;
    $vv4a    = 0;
    $chem    = 0;
    $eis     = 0;
    $wasser  = 0;
    $energie = 0;
    $volk    = 0;
    $punkte  = 0;

    $sql = "SELECT zeitmarke, fleeter, " .
        " eisen, stahl, vv4a, chem, eis,wasser, energie, volk, " .
        " eisen * " . ((isset($fakt_eisen) && floatval($fakt_eisen) >= 0) ? $fakt_eisen : 1) . "+" .
        " stahl * " . ((isset($fakt_stahl) && floatval($fakt_stahl) >= 0) ? $fakt_stahl : 1) . "+" .
        " vv4a * " . ((isset($fakt_vv4a) && floatval($fakt_vv4a) >= 0) ? $fakt_vv4a : 1) . "+" .
        " chem * " . ((isset($fakt_chem) && floatval($fakt_chem) >= 0) ? $fakt_chem : 1) . "+" .
        " eis * " . ((isset($fakt_eis) && floatval($fakt_eis) >= 0) ? $fakt_eis : 1) . "+" .
        " wasser * " . ((isset($fakt_wasser) && floatval($fakt_wasser) >= 0) ? $fakt_wasser : 1) . "+" .
        " energie * " . ((isset($fakt_energie) && floatval($fakt_energie) >= 0) ? $fakt_energie : 1) . "+" .
        " volk * " . ((isset($fakt_volk) && floatval($fakt_volk) >= 0) ? $fakt_volk : 1) . " AS punkte" .
        " FROM " . $db_tb_transferliste .
        " WHERE buddler='" . $buddler . "' " .
        " AND UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) = " . $transferday .
        " ORDER BY " . $order . " " . $ordered;

    $result = $db->db_query($sql);

    while ($row = $db->db_fetch_array($result)) {
        $count++;
        $eisen += $row['eisen'];
        $stahl += $row['stahl'];
        $vv4a += $row['vv4a'];
        $chem += $row['chem'];
        $eis += $row['eis'];
        $wasser += $row['wasser'];
        $energie += $row['energie'];
        $volk += $row['volk'];
        $punkte += $row['punkte'];

        next_row("windowbg1 left", "style='width:12%'");
        echo strftime(CONFIG_DATETIMEFORMAT, $row['zeitmarke']);
        echo '<br>' . $row['fleeter'];
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['eisen'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['stahl'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['vv4a'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['chem'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['eis'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['wasser'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['energie'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['volk'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['punkte'], 0, ',', '.');
    }

    next_row("windowbg2 left", "style='width:12%'");
    echo "Summe";
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($eisen, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($stahl, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($vv4a, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($chem, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($eis, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($wasser, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($energie, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($volk, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($punkte, 0, ',', '.');

    next_row("windowbg1 center", "style='width:95%' colspan='10'");
    make_link_transferday("windowbg1", $order, $ordered, $buddler, $transferday);
    end_row();
    end_table();
}

// ****************************************************************************
//
function showbuddler($buddler)
{
    global $db,
           $order,
           $ordered,
           $fakt_eisen,
           $fakt_stahl,
           $fakt_chem,
           $fakt_vv4a,
           $fakt_eis,
           $fakt_wasser,
           $fakt_energie,
           $fakt_volk,
           $default_buddler_order,
           $default_buddler_ordered,
           $show_buddler_graph,
           $buddler_graph_typ,
           $db_tb_transferliste;

    if (empty($order)) {
        $order = $default_buddler_order;
    }
    if (empty($ordered)) {
        $ordered = $default_buddler_ordered;
    }

    if ($show_buddler_graph) {
        $date = intval(CURRENT_UNIX_TIME / DAY) * DAY;

        build_graph_transfer(array($buddler), "", $date - 30 * DAY, $date, $buddler_graph_typ);
    }

    start_table();
    start_row("titlebg center", "style='width:95%' colspan='10'");
    echo "<b>" . $buddler . "</b>";
    next_row("windowbg2 center", "style='width:14%'");
    make_order_link("zeitmarke", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "Datum";
    echo '<br>';
    make_order_link("zeitmarke", "desc", array("selbuddler=" . $buddler));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("eisen", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "Eisen";
    echo '<br>';
    make_order_link("eisen", "desc", array("selbuddler=" . $buddler));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("stahl", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "Stahl";
    echo '<br>';
    make_order_link("stahl", "desc", array("selbuddler=" . $buddler));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("vv4a", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "VV4A";
    echo '<br>';
    make_order_link("vv4a", "desc", array("selbuddler=" . $buddler));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("chem", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "Chemie";
    echo '<br>';
    make_order_link("chem", "desc", array("selbuddler=" . $buddler));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("eis", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "Eis";
    echo '<br>';
    make_order_link("eis", "desc", array("selbuddler=" . $buddler));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("wasser", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "Wasser";
    echo '<br>';
    make_order_link("wasser", "desc", array("selbuddler=" . $buddler));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("energie", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "Energie";
    echo '<br>';
    make_order_link("energie", "desc", array("selbuddler=" . $buddler));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("volk", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "Volk";
    echo '<br>';
    make_order_link("volk", "desc", array("selbuddler=" . $buddler));
    next_cell("windowbg2 center", "style='width:9%'");
    make_order_link("punkte", "asc", array("selbuddler=" . $buddler));
    echo '<br>';
    echo "Punkte";
    echo '<br>';
    make_order_link("punkte", "desc", array("selbuddler=" . $buddler));

    $count   = 0;
    $eisen   = 0;
    $stahl   = 0;
    $vv4a    = 0;
    $chem    = 0;
    $eis     = 0;
    $wasser  = 0;
    $energie = 0;
    $volk    = 0;
    $punkte  = 0;

    $sql = "SELECT UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) as zeitmarke, " .
        " SUM(eisen) AS eisen, SUM(stahl) AS stahl, " .
        " SUM(vv4a) AS vv4a, SUM(chem) AS chem, SUM(eis) AS eis, " .
        " SUM(wasser) AS wasser, SUM(energie) AS energie, SUM(volk) AS volk, " .
        " SUM(eisen) * " . ((isset($fakt_eisen) && floatval($fakt_eisen) >= 0) ? $fakt_eisen : 1) . "+" .
        " SUM(stahl) * " . ((isset($fakt_stahl) && floatval($fakt_stahl) >= 0) ? $fakt_stahl : 1) . "+" .
        " SUM(vv4a) * " . ((isset($fakt_vv4a) && floatval($fakt_vv4a) >= 0) ? $fakt_vv4a : 1) . "+" .
        " SUM(chem) * " . ((isset($fakt_chem) && floatval($fakt_chem) >= 0) ? $fakt_chem : 1) . "+" .
        " SUM(eis) * " . ((isset($fakt_eis) && floatval($fakt_eis) >= 0) ? $fakt_eis : 1) . "+" .
        " SUM(wasser) * " . ((isset($fakt_wasser) && floatval($fakt_wasser) >= 0) ? $fakt_wasser : 1) . "+" .
        " SUM(energie) * " . ((isset($fakt_energie) && floatval($fakt_energie) >= 0) ? $fakt_energie : 1) . "+" .
        " SUM(volk) * " . ((isset($fakt_volk) && floatval($fakt_volk) >= 0) ? $fakt_volk : 1) . " AS punkte" .
        " FROM " . $db_tb_transferliste .
        " WHERE buddler='" . $buddler . "' " .
        " GROUP BY UNIX_TIMESTAMP( DATE( FROM_UNIXTIME( zeitmarke ) ) ) " .
        " ORDER BY " . $order . " " . $ordered;

    $result = $db->db_query($sql);

    while ($row = $db->db_fetch_array($result)) {
        $count++;
        $eisen += $row['eisen'];
        $stahl += $row['stahl'];
        $vv4a += $row['vv4a'];
        $chem += $row['chem'];
        $eis += $row['eis'];
        $wasser += $row['wasser'];
        $energie += $row['energie'];
        $volk += $row['volk'];
        $punkte += $row['punkte'];

        next_row("windowbg1 left", "style='width:12%'");
        make_link(
            strftime(CONFIG_DATEFORMAT, $row['zeitmarke']),
            array(
                 "selbuddler=" . $buddler,
                 "seltransferday=" . $row['zeitmarke']
            )
        );
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['eisen'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['stahl'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['vv4a'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['chem'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['eis'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['wasser'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['energie'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['volk'], 0, ',', '.');
        next_cell("windowbg1 right", "style='width:10%'");
        echo number_format($row['punkte'], 0, ',', '.');
    }

    next_row("windowbg2 left", "style='width:12%'");
    echo "Summe";
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($eisen, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($stahl, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($vv4a, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($chem, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($eis, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($wasser, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($energie, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($volk, 0, ',', '.');
    next_cell("windowbg2 right", "style='width:10%'");
    echo number_format($punkte, 0, ',', '.');

    end_row();
    end_table();
}

// ****************************************************************************
//
function showallfleeters()
{
    global $db,
           $db_tb_transferliste,
           $fakt_eisen,
           $fakt_stahl,
           $fakt_chem,
           $fakt_vv4a,
           $fakt_eis,
           $fakt_wasser,
           $fakt_energie,
           $fakt_volk,
           $order,
           $db_tb_user,
           $ordered,
           $graph,
           $graph_user,
           $default_order,
           $default_ordered,
           $user_fremdesitten,
           $user_allianz;

    if (empty($order)) {
        $order = $default_order;
    }
    if (empty($ordered)) {
        $ordered = $default_ordered;
    }

    // Falls immer noch leer dann falsch configuriert oder über parser reingekommen
    if (empty($order)) {
        $order = 'punkte';
    }
    if (empty($ordered)) {
        $ordered = 'desc';
    }


    $sql = "SELECT DISTINCT fleeter FROM " . $db_tb_transferliste;
    if (!$user_fremdesitten) {
        $sql .= "," . $db_tb_user;
        $sql .= " WHERE " . $db_tb_transferliste . ".fleeter=" . $db_tb_user . ".id";
        $sql .= " AND " . $db_tb_user . ".allianz='" . $user_allianz . "'";
    }
    $sql .= " ORDER BY fleeter ASC";
    $db->db_query($sql);
    /*
    $sql = "SELECT DISTINCT fleeter FROM " . $db_tb_transferliste .
           " ORDER BY fleeter ASC";
    */
    $result = $db->db_query($sql);

    while ($row = $db->db_fetch_array($result)) {
        start_table();
        start_row("titlebg center", "style='width:95%' colspan='10'");
        echo "<b>" . $row['fleeter'] . "</b>";
        next_row("windowbg2 center", "style='width:14%'");
        make_order_link("buddler", "asc");
        echo '<br>';
        echo "Name";
        echo '<br>';
        make_order_link("buddler", "desc");
        next_cell("windowbg2 center", "style='width:9%'");
        make_order_link("eisen", "asc");
        echo '<br>';
        echo "Eisen";
        echo '<br>';
        make_order_link("eisen", "desc");
        next_cell("windowbg2 center", "style='width:9%'");
        make_order_link("stahl", "asc");
        echo '<br>';
        echo "Stahl";
        echo '<br>';
        make_order_link("stahl", "desc");
        next_cell("windowbg2 center", "style='width:9%'");
        make_order_link("vv4a", "asc");
        echo '<br>';
        echo "VV4A";
        echo '<br>';
        make_order_link("vv4a", "desc");
        next_cell("windowbg2 center", "style='width:9%'");
        make_order_link("chem", "asc");
        echo '<br>';
        echo "Chemie";
        echo '<br>';
        make_order_link("chem", "desc");
        next_cell("windowbg2 center", "style='width:9%'");
        make_order_link("eis", "asc");
        echo '<br>';
        echo "Eis";
        echo '<br>';
        make_order_link("eis", "desc");
        next_cell("windowbg2 center", "style='width:9%'");
        make_order_link("wasser", "asc");
        echo '<br>';
        echo "Wasser";
        echo '<br>';
        make_order_link("wasser", "desc");
        next_cell("windowbg2 center", "style='width:10%'");
        make_order_link("energie", "asc");
        echo '<br>';
        echo "Energie";
        echo '<br>';
        make_order_link("energie", "desc");
        next_cell("windowbg2 center", "style='width:10%'");
        make_order_link("volk", "asc");
        echo '<br>';
        echo "Volk";
        echo '<br>';
        make_order_link("volk", "desc");
        echo '<br>';
        next_cell("windowbg2 center", "style='width:10%'");
        make_order_link("punkte", "asc");
        echo '<br>';
        echo "Punkte";
        echo '<br>';
        make_order_link("punkte", "desc");

        $count   = 0;
        $eisen   = 0;
        $stahl   = 0;
        $vv4a    = 0;
        $chem    = 0;
        $eis     = 0;
        $wasser  = 0;
        $energie = 0;
        $volk    = 0;
        $punkte  = 0;

        $sql = "SELECT buddler, SUM(eisen) AS eisen, SUM(stahl) AS stahl, " .
            " SUM(vv4a) AS vv4a, SUM(chem) AS chem, SUM(eis) AS eis, " .
            " SUM(wasser) AS wasser, SUM(energie) AS energie, SUM(volk) AS volk, " .
            " SUM(eisen) * " . ((isset($fakt_eisen) && floatval($fakt_eisen) >= 0) ? $fakt_eisen : 1) . "+" .
            " SUM(stahl) * " . ((isset($fakt_stahl) && floatval($fakt_stahl) >= 0) ? $fakt_stahl : 1) . "+" .
            " SUM(vv4a) * " . ((isset($fakt_vv4a) && floatval($fakt_vv4a) >= 0) ? $fakt_vv4a : 1) . "+" .
            " SUM(chem) * " . ((isset($fakt_chem) && floatval($fakt_chem) >= 0) ? $fakt_chem : 1) . "+" .
            " SUM(eis) * " . ((isset($fakt_eis) && floatval($fakt_eis) >= 0) ? $fakt_eis : 1) . "+" .
            " SUM(wasser) * " . ((isset($fakt_wasser) && floatval($fakt_wasser) >= 0) ? $fakt_wasser : 1) . "+" .
            " SUM(energie) * " . ((isset($fakt_energie) && floatval($fakt_energie) >= 0) ? $fakt_energie : 1) . "+" .
            " SUM(volk) * " . ((isset($fakt_volk) && floatval($fakt_volk) >= 0) ? $fakt_volk : 1) . " AS punkte" .
            " FROM " . $db_tb_transferliste .
            " WHERE fleeter='" . $row['fleeter'] . "' " .
            " GROUP BY buddler " .
            " ORDER BY " . $order . " " . $ordered;

        $result2 = $db->db_query($sql);

        while ($row2 = $db->db_fetch_array($result2)) {
            $count++;
            $eisen += $row2['eisen'];
            $stahl += $row2['stahl'];
            $vv4a += $row2['vv4a'];
            $chem += $row2['chem'];
            $eis += $row2['eis'];
            $wasser += $row2['wasser'];
            $energie += $row2['energie'];
            $volk += $row2['volk'];
            $punkte += $row2['punkte'];

            // Farbmarkierung für LastTransport-Update
            $scancolor = array();
            $query     = "SELECT lasttransport" .
                " FROM " . $db_tb_user .
                " WHERE sitterlogin = '" . $row2['buddler'] . "'";
            $result_query = $db->db_query($query);

            $row_query = $db->db_fetch_array($result_query);
            if (empty($row_query['lasttransport'])) {
                $tmp   = "(---)";
                $color = "lightgrey";
            } else {
                $tmp   = strftime("(%d.%m.%Y)", $row_query['lasttransport']);
                $color = scanAge($row_query['lasttransport']);
            }

            next_row("windowbg1 left", "style='width:14%; background-color:" . $color . ";'");
            make_link(
                $row2['buddler'],
                array("selbuddler=" . $row2['buddler'])
            );
            // Ausgabe für LastTransport-Datum
            echo '<br>' . $tmp;

            next_cell("windowbg1 right", "style='width:9%'");
            echo number_format($row2['eisen'], 0, ',', '.');
            next_cell("windowbg1 right", "style='width:9%'");
            echo number_format($row2['stahl'], 0, ',', '.');
            next_cell("windowbg1 right", "style='width:9%'");
            echo number_format($row2['vv4a'], 0, ',', '.');
            next_cell("windowbg1 right", "style='width:9%'");
            echo number_format($row2['chem'], 0, ',', '.');
            next_cell("windowbg1 right", "style='width:9%'");
            echo number_format($row2['eis'], 0, ',', '.');
            next_cell("windowbg1 right", "style='width:9%'");
            echo number_format($row2['wasser'], 0, ',', '.');
            next_cell("windowbg1 right", "style='width:9%'");
            echo number_format($row2['energie'], 0, ',', '.');
            next_cell("windowbg1 right", "style='width:9%'");
            echo number_format($row2['volk'], 0, ',', '.');
            next_cell("windowbg1 right", "style='width:9%'");
            echo number_format($row2['punkte'], 0, ',', '.');
        }

        next_row("windowbg2 left", "style='width:14%'");
        echo "Summe";
        next_cell("windowbg2 right", "style='width:9%'");
        echo number_format($eisen, 0, ',', '.');
        next_cell("windowbg2 right", "style='width:9%'");
        echo number_format($stahl, 0, ',', '.');
        next_cell("windowbg2 right", "style='width:9%'");
        echo number_format($vv4a, 0, ',', '.');
        next_cell("windowbg2 right", "style='width:9%'");
        echo number_format($chem, 0, ',', '.');
        next_cell("windowbg2 right", "style='width:9%'");
        echo number_format($eis, 0, ',', '.');
        next_cell("windowbg2 right", "style='width:9%'");
        echo number_format($wasser, 0, ',', '.');
        next_cell("windowbg2 right", "style='width:9%'");
        echo number_format($energie, 0, ',', '.');
        next_cell("windowbg2 right", "style='width:9%'");
        echo number_format($volk, 0, ',', '.');
        next_cell("windowbg2 right", "style='width:9%'");
        echo number_format($punkte, 0, ',', '.');

        /*    next_row("windowbg1 right", "style='width:95%' colspan='10'");
              make_link("Alle auswählen",
                        array("order=".$order,
                      "ordered=".$ordered,
                      "select_all=true",
                      "graph=".$graph,
                      "graph_user=".$graph_user
                             )
                       );
              make_link("Auswahl entfernen",
                        array("order=".$order,
                      "ordered=".$ordered,
                      "select_none=true",
                      "graph=".$graph,
                      "graph_user=".$graph_user
                     )
                 ); */
        end_row();
        end_table();

        echo "<br><br>\n";
    }
}