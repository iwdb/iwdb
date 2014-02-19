<?php

/**
 * function ensureSortDirection
 *
 * stellt gültige Sortierungsrichtung sicher
 *
 * @param mixed $sortDirection         zu filternde Sortierungsrichtung
 * @param mixed $sortDirectionStandard optional Standardsortierungsrichtung
 *
 * @return string Sortierungsrichtung oder Standardwert
 *
 * @author   masel
 */
function ensureSortDirection($sortDirection, $sortDirectionStandard = 'ASC')
{
    return ensureValue($sortDirection, array('asc', 'desc', 'ASC', 'DESC'), $sortDirectionStandard);
}

/**
 * function ensureValue
 *
 * stellt gültigen Wert sicher
 *
 * @param mixed $inputValue     zu filternder Wert
 * @param array $possibleValues gültige Werte
 * @param mixed $standardValue  optional Standardwert
 *
 * @return mixed gefilterter Wert, ggf Standardwert oder bool false
 *
 * @author   masel
 */
function ensureValue($inputValue, $possibleValues, $standardValue = null)
{
    if (empty($possibleValues)) {
        return false;
    }

    if (in_array($inputValue, $possibleValues)) {
        return $inputValue;
    } elseif (!is_null($standardValue)) {
        return $standardValue;
    } else {
        return $possibleValues[0];
    }

}

/**
 * function filter_int
 *
 * filtert einfache Ganzzahlen mit Tausendertrennzeichen
 *
 * @param string $numberstring  Zahl zum filtern
 * @param int    $min_value     Minimalwert
 * @param int    $max_value     Maximalwert
 * @param int    $default_value Standardwert
 *
 * @return int gefilterte Zahl
 *
 * @author masel
 */
function filter_int($numberstring, $default_value = null, $min_value = null, $max_value = null)
{
    $filtered_number = filter_var($numberstring, FILTER_SANITIZE_NUMBER_INT);
    if (($filtered_number !== false) AND ($filtered_number !== '')) { //Ergebnis nicht fehlgeschlagen oder nicht leer

        $filtered_number = (int)$filtered_number;
    } else { //sonst Standardwert

        $filtered_number = $default_value;

    }

    if (!is_null($min_value) AND ($filtered_number < $min_value)) { //Limit-Check

        return (int)$min_value;

    } elseif (!is_null($max_value) AND ($filtered_number > $max_value)) {

        return (int)$max_value;

    } else {

        return $filtered_number;

    }
}

/**
 * function filter_number
 *
 * filtert beliebige Zahlen mit Tausendertrennzeichen ggf mit Exponenten oder Si-prefix Mega oder Kilo am Ende
 *
 * @param string   $numberstring  Zahlstring zum Filtern
 * @param int|bool $default_value optional Standardwert
 * @param int|bool $min_value     optional Minimalwert
 * @param int|bool $max_value     optional Maximalwert
 *
 * @return mixed gefilterte Zahl
 *
 * @author masel
 */
function filter_number($numberstring, $default_value = false, $min_value = false, $max_value = false)
{
    $filtered_number = '';

    $numberstring = trim($numberstring);

    if (preg_match('~^(?P<sign>-|\+|)\s?(?P<digit>\d{1,3}(?:(\D?)\d{3})?(?:\3\d{3})*)(?:\D(?P<part>\d{1,2}))?\s?(?P<si_prefix>m|M|k|K)?$~', $numberstring, $numberpart)) { //evl vorhandenes Negativ-Vorzeichen sichern

        $filtered_number = preg_replace('~\D~', '', $numberpart['digit']);


        if (isset($numberpart['part'])) { //Nachkommastellen vorhanden?
            if (strlen($numberpart['part']) === 2) { //zwei Nachkommastellen
                $filtered_number += $numberpart['part'] / 100;
            } else { //eine Nachkommastelle
                $filtered_number += $numberpart['part'] / 10;
            }
        }

        if (isset($numberpart['si_prefix'])) { //SI-Prefix vorhanden?
            if (($numberpart['si_prefix'] === 'm') or ($numberpart['si_prefix'] === 'M')) {
                $filtered_number = $filtered_number * 1000000; //mega-prefix reinmultiplizieren
            } elseif (($numberpart['si_prefix'] === 'k') or ($numberpart['si_prefix'] === 'K')) {
                $filtered_number = $filtered_number * 1000; //kilo-prefix reinmultiplizieren
            }
        }

        if ($numberpart['sign'] === '-') {
            $filtered_number = -$filtered_number; //negatives Vorzeichen wieder dazu
        }

        if (($min_value !== false) AND ($filtered_number < $min_value)) { //Limit-Check
            return $min_value;
        }
        if (($max_value !== false) AND ($filtered_number > $max_value)) {
            return $max_value;
        }

    } else if ($default_value !== false) {
        $filtered_number = $default_value;
    }

    return $filtered_number;

}


//******************************************************************************
//
// Replace thousand-separator with nothing, and the comma-sign with a period
// Ideally the given string is a pure number with formatting.
//
// masel: veraltet -> filter_number nutzen
function stripNumber($numberstring, $thousand = '.', $comma = ',')
{
    $numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    //alles entfernen was keine Zahl ist
    $return = preg_replace("/[^0-9]/", "", $numberstring);

    if (isset($debug)) {
        echo "<div class='system_debug_blue'>" . $numberstring . " > " . $return . "</div>";
    }

    //dvisior für den Teiler finden
    $where = 0;
    for ($i = strlen($numberstring) - 1; $i >= 0; $i--) {

        //ist es eine Nummer und wir beginnen egrade von rechts nach Nummern zu suchen?
        if (in_array($numberstring[$i], $numbers)) {
            $where++;
        }
        //keine Numemr und iwr haben shcon eine gefunden?
        if (!in_array($numberstring[$i], $numbers) AND $where > 0) {
            break;
        }

        if (isset($debug)) {
            echo "<div class='system_debug_orange'>" . $i . " = " . $numberstring[$i] . " () " . $where . "</div>";
        }
    }
    //wenn where gröser dann handelt es sich um ein Tausendertrennzeichen
    //als where gibt es nur null und eins
    if ($where >= 3) {
        $where = 0;
    }

    //Spezialfall zwei und einstellige Zahlen und Zahlen ohne irgendetwas
    if (strlen($return) == $where) {
        $where = 0;
    }

    $return = $return / pow(10, ($where));

    //so jetzt könnte das ganze ja auch negativ sein. Hierbei wird davon ausgegangen,
    // dass der User als Trennzeichen keinen Strich nimmt!
    $position = StrPos($numberstring, "-");
    if (!($position === false)) {
        $return = $return * (-1);
    }

    return $return;
}

/**
 * function filter_coords
 *
 * filtert Koordinatenstrings
 *
 * @param string $coords zu filternder Koordinatenstring
 *
 * @return mixed gefilterte Koordinaten oder bool false
 *
 * @ToDo   weitergehende Prüfungen?
 *
 * @author masel
 */
function filter_coords($coords)
{
    $coords = trim($coords);

    if (empty($coords)) {
        return false;
    }

    if (preg_match('/(\d{1,2}):(\d{1,3}):(\d{1,2})/', $coords, $aResult)) {
        return $aResult[1] . ':' . $aResult[2] . ':' . $aResult[3];
    } else {
        return false;
    }
}

/**
 * function validateIwAccname
 *
 * Überprüft ob sich der angegebene IW-Acc in der IWDB befindet
 *
 * @param string $name Zu überprüfender Accname
 *
 * @return string geprüfter Accname oder bool false falls nicht vorhanden
 *
 * @author masel
 */
function validateIwAccname($name)
{
    global $db, $db_tb_user;
    static $IwAccnames;

    if (empty($name)) {
        return false;
    }

    //sind Informationen nicht im statischen cache -> neu holen
    if (empty($IwAccnames)) {
        $IwAccnames = Array();

        $sql = "SELECT `sitterlogin` FROM `$db_tb_user` WHERE `sitterlogin` = '".$name ."' LIMIT 1;";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query iw accname.', '', __FILE__, __LINE__, $sql);

        $row = $db->db_fetch_array($result);
        if (!empty($row)) {
            $IwAccnames[$name] = $row['sitterlogin'];
        }
    }

    if (!in_array($name, $IwAccnames)) {
        return false;
    }

    return $name;
}

/**
 * function validateIwdbAccname
 *
 * Überprüft ob sich der angegebene IW-Acc in der IWDB befindet
 *
 * @param string $name Zu überprüfender Accname
 *
 * @return string geprüfter Accname oder bool false falls nicht vorhanden
 *
 * @author masel
 */
function validateIwdbAccname($name)
{
    global $db, $db_tb_user;

    if (empty($name)) {
        return false;
    }
    $name = $db->escape($name);

    $sql = "SELECT `id` FROM  `$db_tb_user` WHERE `id` = '".$name ."' LIMIT 1;";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query iwdb accname.', '', __FILE__, __LINE__, $sql);

    $row = $db->db_fetch_array($result);
    if (!empty($row['id'])) {
        return $name;
    } else {
        return false;
    }
}