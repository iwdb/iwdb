<?php
/*****************************************************************************
 * doc_default.php                                                           *
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
 * Bei Problemen kannst du dich an das eigens dafür eingerichtete            *
 * Entwicklerforum/Repo wenden:                                              *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//******************************************************************************
//
// 
function doc_title($text)
{
    echo "<h1>" . $text . "</h1>\n";
}

//******************************************************************************
//
function doc_message($text)
{
    echo "<div class='doc_message'>" . $text . "</div>\n";
}

//******************************************************************************
//
function start_table($width = 90, $border = 0, $cellpadding = 4, $cellspacing = 1, $class = "table_format")
{
    echo "<table border='" . $border . "' " .
        "cellpadding='" . $cellpadding . "' " .
        "cellspacing='" . $cellspacing . "' " .
        "class='" . $class . "'";
    if ($width > 0) {
        echo  " style='width: " . $width . "%;'";
    }
    echo ">\n";
}

//******************************************************************************
//
function end_table()
{
    echo "</table>\n";
}


//******************************************************************************
//
function cell($class = "", $extra = "", $columns = 1)
{
    echo "  <td";

    if (!empty($class)) {
        echo " class='" . $class . "'";
    }

    if (!empty($extra)) {
        echo " " . $extra;
    }

    if ($columns > 1) {
        echo " colspan='" . $columns . "'";
    }

    echo ">";
}

//******************************************************************************
//
function next_cell($class = "", $extra = "", $columns = 1)
{
    echo "</td>\n";
    cell($class, $extra, $columns);
}

//******************************************************************************
//
function end_cell()
{
    echo "</td>\n";
}

//******************************************************************************
//
function start_row($class = "", $extra = "", $columns = 1)
{
    echo " <tr>\n";
    cell($class, $extra, $columns);
}

//******************************************************************************
//
function start_row_only($class = "", $extra = "")
{
    $html = " <tr";
    if (!empty($class)) {
        $html .= " class='$class'";
    }
    if (!empty($extra)) {
        $html .= " $extra";
    }
    $html .= ">\n";
    echo $html;
}

//******************************************************************************
//
function end_row($closecell = true)
{
    if ($closecell) {
        echo "</td>\n";
    }

    echo " </tr>\n";
}

//******************************************************************************
//
function next_row($class = "", $extra = "", $columns = 1)
{
    end_row();
    start_row($class, $extra, $columns);
}


//******************************************************************************
//
function start_form($action, $params = '')
{
    $html = "<form method='POST' action='";
    $html .= url($action, $params);
    $html .= "' enctype='multipart/form-data'>\n";
    echo $html;
}

//******************************************************************************
//
function end_form()
{
    echo "</form>\n";
}

//******************************************************************************
//
function action($action, $text)
{
    echo "<a href='index.php?action=" . $action . "'>" . $text . "</a>\n";
}

//******************************************************************************
//
function url($action, $params = '')
{
    $url = "index.php?action=" . $action;
    if (is_array($params)) {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subkey => $subvalue) {
                    $url .= "&" . $key . "[" . $subkey . "]" . "=" . $subvalue;
                }
            } else {
                $url .= "&" . $key . "=" . $value;
            }
        }
    }

    return $url;
}

//******************************************************************************
// Tabelle ausgeben
//
// Beispiel:
//
// $view = array("columns" => array("user" => "Spieler", "coords" => "Koordinaten"));
// $data[] = array("user" => "Thella", "coords" => "1:1:1");
// make_table($view, $data);
//
// Referenz:
//
// $view = array(
//	"columns" => array(
//		"spaltenschluessel" => "Spaltentitel",
//	),
//	"attributes" => array(
//		// col_extra wird im <td>-Tag ausgegeben
//		"col_extra" => array(
//			"spaltenschluessel" => "width='100%'",
//		),
//		// col_value_func wird fuer jeden Datenwert aufgerufen
//		"col_value_func" => array(
//			"spaltenschluessel" => create_function('$col,$row,$data,$row_key,$col_key','
//				return $row["spaltenschluessel"];
//			'),
//		),
//	),
// );
//
// $data[] = array(
//	"spaltenschluessel" => "wert",
//	"attributes" => array(
//		// row_class wird im <tr class="">-Tag ausgegeben
//		"row_class" => "",
//		// row_extra wird im <tr>-Tag ausgegeben
//		"row_extra" => "",
//	),
// );
function make_table($view, $data)
{
    // Tabelle ausgeben
    start_table(100);

    // Spalten iterieren
    $col_index = 0;
    foreach ($view["columns"] as $value) {
        // Zeile beginnen
        if (!$col_index++) {
            start_row_only();
            cell("titlebg");
        } else { // Spalte beginnen
            next_cell("titlebg");
        }
        // Wert ausgeben
        echo "<b>";
        echo $value;
        echo "</b>";
    }
    end_row();

    if (isset($data)) {
        // Daten iterieren
        foreach ($data as $row_key => $row) {
            // Zeilenattribute
            if (isset($row["attributes"]["row_class"])) {
                $row_class = $row["attributes"]["row_class"];
            } else {
                $row_class = "windowbg1";
            }
            $row_extra = "id='" . $row_key . "'";
            if (isset($row["attributes"]["row_extra"])) {
                $row_extra .= " " . $row["attributes"]["row_extra"];
            } elseif (isset($view["attributes"]["row_extra"])) {
                $row_extra .= " " . $view["attributes"]["row_extra"];
            }
            // Spalten iterieren
            $col_index = 0;
            foreach ($view['columns'] as $col_key => $value) {
                // Spalte beginnen
                if (isset($col_span) && --$col_span) {
                    continue;
                }

                // Spaltenattribute
                if (isset($row["attributes"]["col_class"][$col_key])) {
                    $col_class = $row["attributes"]["col_class"][$col_key];
                } elseif (isset($view["attributes"]["col_class"][$col_key])) {
                    $col_class = $view["attributes"]["col_class"][$col_key];
                } else {
                    $col_class = $row_class;
                }
                $col_extra = "id='" . $row_key . "_" . $col_key . "'";

                if (isset($row["attributes"]["col_extra"][$col_key])) {
                    $col_extra .= " " . $row["attributes"]["col_extra"][$col_key];
                } elseif (isset($view["attributes"]["col_extra"][$col_key])) {
                    $col_extra .= " " . $view["attributes"]["col_extra"][$col_key];
                }

                if (isset($row["attributes"]["col_span"][$col_key])) {
                    $col_span = $row["attributes"]["col_span"][$col_key];
                } elseif (isset($view["attributes"]["col_span"][$col_key])) {
                    $col_span = $view["attributes"]["col_span"][$col_key];
                } else {
                    $col_span = 1;
                }

                // Zeile beginnen
                if (!$col_index++) {
                    start_row_only($row_class, $row_extra);
                    cell($col_class, $col_extra);
                } else {
                    next_cell($col_class, $col_extra, $col_span);
                }

                // Wert ausgeben
                if (isset($row["attributes"]["col_value_func"][$col_key])) {
                    echo $row["attributes"]["col_value_func"][$col_key]($row[$col_key], $row, $data, $row_key, $col_key);
                } elseif (isset($view["attributes"]["col_value_func"][$col_key])) {
                    echo $view["attributes"]["col_value_func"][$col_key]($row[$col_key], $row, $data, $row_key, $col_key);
                } else {
                    echo $row[$col_key];
                }
            }
            end_row();
        }
    }
    end_table();
}

//******************************************************************************
//
// Erstellt ein Formularfeld.
function makeField($options, $name)
{
    switch ($options['type']) {
        case 'label':
            $html = '<span';
            if (isset($options['style'])) {
                $html .= ' style="' . $options['style'] . '"';
            }
            $html .= '>' . $options['value'] . '</span>';
            break;
        case 'text':
            $html = '<input type="text" name="' . $name . '"';
            if (isset($options['value'])) {
                $html .= ' value="' . $options['value'] . '"';
            }
            if (isset($options['style'])) {
                $html .= ' style="' . $options['style'] . '"';
            }
            $html .= '>';
            break;
        case 'hidden':
            $html = '<input type="hidden" name="' . $name . '" value="' . $options['value'] . '"';
            if (isset($options['style'])) {
                $html .= ' style="' . $options['style'] . '"';
            }
            $html .= '>';
            break;
        case 'number':
            $html = '<input type="number" name="' . $name . '"';
            if (isset($options['id'])) {
                $html .= ' id="' . $options['id'] . '"';
            }
            if (isset($options['value'])) {
                $html .= ' value="' . $options['value'] . '"';
            }
            if (isset($options['onchange'])) {
                $html .= ' onchange="' . $options['onchange'] . '"';
            }
            if (isset($options['min'])) {
                $html .= ' min="' . $options['min'] . '"';
            }
            if (isset($options['max'])) {
                $html .= ' max="' . $options['max'] . '"';
            }
            if (isset($options['style'])) {
                $html .= ' style="' . $options['style'] . '"';
            }
            $html .= '>';
            break;
        case 'select':
            $html = '<select name="' . $name . '"';
            if (isset($options['id'])) {
                $html .= ' id="' . $options['id'] . '"';
            }
            if (isset($options['onchange'])) {
                $html .= ' onchange="' . $options['onchange'] . '"';
            }
            $html .= '>';
            foreach ($options['values'] as $key => $value) {
                $html .= '<option value="' . $key . '"';
                if (isset($options['value']) && $options['value'] == $key) {
                    $html .= ' selected';
                }
                $html .= '>' . $value . '</option>';
            }
            $html .= '</select>';
            break;
        case 'area':
            $html = '<textarea name="' . $name . '" rows="' . $options['rows'] . '" cols="' . $options['cols'] . '">';
            $html .= $options['value'];
            $html .= '</textarea>';
            break;
        case 'checkbox':
            $html = '<input type="checkbox" name="' . $name . '" value="1"';
            if ($options['value']) {
                $html .= ' checked';
            }
            if (isset($options['style'])) {
                $html .= ' style="' . $options['style'] . '"';
            }
            $html .= '>';
            break;
        default:
            $html = 'unbekanter typ ' . $options['type'];
    }

    return $html;
}