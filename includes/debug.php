<?php
// ****************************************************************************
// Gibt den Wert einer Variablen aus.
function debug_var($name, $wert, $level = 2)
{
    if (DEBUG_LEVEL >= $level) {
        echo "<div class='system_debug_blue'>" . $name . ":";
        if (is_array($wert)) {
            print_r($wert);
        } else {
            echo "'" . $wert . "'";
        }
        echo "</div>";
    }
}

// ****************************************************************************
// Gibt den Wert einen Text aus.
//veraltet -> debug_var nutzen
function debug_echo($text, $level = 2)
{
    if (DEBUG_LEVEL >= $level) {
        echo "<div class='system_debug_blue'>" . $text . "</div>";
    }
}

//eine bessere Möglichkeit Debugdaten anzuzeigen
//usage: new dBug ( $myVariable );
include_once("./includes/dBug.php");