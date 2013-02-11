<?php
/*****************************************************************************
 * function.php                                                              *
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

// Funktion um die Fehlermeldungen zusammenzusetzen.
function error($err_code, $err_msg = '', $err_title = '', $err_file = '', $err_line = '', $sql = '') {
	global $db, $error;
	$error_return = "";

	// timestamp for the error entry
	$err_dt = strftime(CONFIG_DATETIMEFORMAT, CURRENT_UNIX_TIME);

	switch ( $err_code )
	{
		case GENERAL_ERROR:
			if ( ! $err_msg )
			{
				$err_msg = "A general error has occured.";
			}
			if ( ! $err_title )
			{
				$err_title = "General Error";
			}
			break;
	}

	switch ( $err_code  )
	{
		case GENERAL_ERROR:
			if (defined( 'DEBUG' ) && DEBUG === true)
			{
				$debug_msg = "<b>DEBUG INFORMATION:</b><br>\n";
				$debug_msg .= "<b>Time:</b> " . $err_dt . "<br>\n";
				$debug_msg .= "<b>Code:</b> " . $err_code . " \n";
				$debug_msg .= "<b>Title:</b> " . $err_title . "<br>\n";
				if ( ( ! empty($err_file) ) || ( ! empty($err_line) ) )
				{
					$debug_msg .= "<b>File:</b> " . $err_file . " \n";
					$debug_msg .= "<b>Line:</b> " . $err_line . "<br>\n";
				}
				$debug_msg .= "<b>Message:</b> " . $err_msg . "<br>\n";

				if ( $sql != "" )
				{
					$debug_msg .= "<b>SQL Version:</b> " . $db->db_version . "<br>\n";
					$debug_msg .= "<b>SQL Query:</b> " . $sql . "<br>\n";
				}

				$err_sql = $db->db_error();
				if ( ( ! empty($err_sql['code']) ) && ( ! empty($err_sql['msg']) ) )
				{
					$debug_msg .= "<b>SQL Code:</b> " . $err_sql['code'] . " \n";
					$debug_msg .= "<b>SQL Message:</b> " . $err_sql['msg'] . "<br>\n";
				}

				$error_return = $debug_msg . "\n";
			}
			else
			{
				$error_return = "<b>" . $err_title . "</b><br>\n" . $err_msg . "<br>\n";
			}
			break;
	}

	if ($err_code == GENERAL_ERROR)
	{
		if ( ! empty($error) )
		{
			$error .= "<br><br>\n";
		}
		$error .= $error_return;
	}
}

//******************************************************************************
//
// IP entschluesseln
function encode_ip($dotquad_ip)
{
    return sha1($dotquad_ip);
}

//******************************************************************************

//
// Funktion fuer Zufallsstring mit Zeichen $values und Länge $length
function randomstring($values = '', $length = 0)
{
	$return = '';
	mt_srand ((double) microtime() * 1000000);
	for ($i = 0; $i < $length; $i++)
	{
		$return .= substr($values, mt_rand (0, strlen($values)), 1);
	}
	return $return;
}

//******************************************************************************
//
// berechnet Zeiten fuer die Sitteraufträge
function dates($parentid, $user)
{
	global $db, $db_tb_sitterauftrag, $db_tb_gebaeude, $db_tb_scans, $db_tb_user;

	$sql = "SELECT coords, dgmod FROM " . $db_tb_scans .  " WHERE user LIKE '" . $user . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	while ($row = $db->db_fetch_array($result))
	{
		if ( empty($row['dgmod']) ) $row['dgmod'] = 1;
		$planetsmod[$row['coords']] = $row['dgmod'];
	}

	$sql = "SELECT gengebmod, genmaurer, peitschen, genbauschleife FROM " . $db_tb_user .  " WHERE sitterlogin LIKE '" . $user . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	$gengebmod = ( empty($row['gengebmod']) ) ? 1 : $row['gengebmod'];
	$genmaurer = $row['genmaurer'];
	$peitschen = $row['peitschen'];
	$genbauschleife = $row['genbauschleife'];

	$sql = "SELECT refid, date, date_b1, date_b2, bauid, planet FROM " . $db_tb_sitterauftrag . " WHERE id = '" . $parentid . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	while ( ! empty($row['refid']) )
	{
    $planet = $row['planet'];
    if(!empty($planet) && isset($planetsmod[$planet])) {
      $planetmod = $planetsmod[$planet];
    } else {
      $planetmod = 1;
    }
    
		$bauschleifenmod = 1;
		if ( empty($peitschen) )
		{
			if ( $row['date_b1'] <> $row['date'] ) $bauschleifenmod = 1.1;
			if ( $row['date_b2'] <> $row['date_b1'] ) $bauschleifenmod = 1.2;
		}

		$sql = "SELECT dauer, category FROM " . $db_tb_gebaeude . " WHERE id='" . $row['bauid'] . "'";
		$result_geb = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_geb = $db->db_fetch_array($result_geb);

		$modmaurer = ( ($genmaurer == 1) && (( strpos($row_geb['category'], "Bunker") !== false ) || ( strpos($row_geb['category'], "Lager") !== false )) ) ? 0.5: 1;

		if ( empty($genbauschleife) ) $date_b2 = $row['date'];
		else $date_b2 = $row['date_b1'];
		$date_b1 = $row['date'];
    
    
		$date = $row['date'] + $row_geb['dauer'] * $planetmod * $gengebmod * $modmaurer * $bauschleifenmod;

		$sql = "SELECT bauschleife FROM " . $db_tb_sitterauftrag . " WHERE id = '" . $row['refid'] . "'";
		$result_s = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_s = $db->db_fetch_array($result_s);
		if ( $row_s['bauschleife'] != "1" )
		{
		 $date_b1 = $date;
		 $date_b2 = $date;
		}

		$sql = "UPDATE " . $db_tb_sitterauftrag . " SET date = '" . $date . "', date_b1 = '" . $date_b1 . "', date_b2 = '" . $date_b2 . "', planet = '" . $planet . "' WHERE id = '" . $row['refid'] . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$sql = "SELECT refid, date, date_b1, date_b2, bauschleife, bauid, planet FROM " . $db_tb_sitterauftrag . " WHERE id = '" . $row['refid'] . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row = $db->db_fetch_array($result);
	}
}

//******************************************************************************
//
// Ausgabeformatierung von Aufträgen
function auftrag($typ, $bauschleife, $bauid, $text, $schiffanz, $planetenmod, $sitterlogin, $bauschleifenmod)
{
	global $db, $db_tb_gebaeude, $db_tb_user, $db_tb_schiffstyp;

	if ( empty($planetenmod) ) $planetenmod = 1;

	$sql = "SELECT gengebmod, genmaurer FROM " . $db_tb_user . " WHERE sitterlogin = '" . $sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	$user_genmaurer = $row['genmaurer'];
	$user_gengebmod = $row['gengebmod'];

	switch ( $typ )
	{
		case "Gebaeude":
			$sql = "SELECT * FROM " . $db_tb_gebaeude . " WHERE id = '" . $bauid . "'";
			$result_gebaeude = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$row_gebaeude = $db->db_fetch_array($result_gebaeude);

			$modmaurer = ( ($user_genmaurer == 1) && (( strpos($row_gebaeude['category'], "Bunker") !== false ) || ( strpos($row_gebaeude['category'], "Lager") !== false )) ) ? 0.5: 1;

			$dauer = round($row_gebaeude['dauer'] * $user_gengebmod * $modmaurer * $planetenmod * $bauschleifenmod);

            if (!empty($row_gebaeude['bild'])) {
                $bild_url = GEBAEUDE_BILDER_PATH . $row_gebaeude['bild'] . ".jpg";
            } else {
                $bild_url = GEBAEUDE_BILDER_PATH . "blank.gif";
            }
			$return = "<img src='" . $bild_url . "' width='50' height='50' style='vertical-align:middle; padding-top: 3px;'> " . $row_gebaeude['name'] . " [" . dauer($dauer) . "]" . (( empty($bauschleife) ) ? "" : " [Bauschleife]" ) . "\n" . (( empty($text) ) ? "" : "<br><br>" . nl2br($text) );
			break;
		case "Schiffe":
			$sql = "SELECT abk FROM " . $db_tb_schiffstyp . " WHERE id = '" . $bauid . "'";
			$result_schiff = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$row_schiff = $db->db_fetch_array($result_schiff);

			$return = "<b>" . $schiffanz . " " . $row_schiff['abk'] . "</b>\n" . (( empty($text) ) ? "" : "<br><br>" . nl2br($text) );
			break;
		case "Forschung":
			$return = "<b>Forschung:</b> " . nl2br($text);
			break;
		default:
			$return = (( empty($bauschleife) ) ? "" : "[Bauschleife] " ) . nl2br($text);
			break;
	}
	return $return;
}

//******************************************************************************
//
// Zeit parsen
//
//ToDo: rewrite to de_bauen_aktuell parser
function timeimport($textinput, $planet = '')
{
    $bau_type = "";
    $textinput = str_replace(" \t", " ", $textinput);
	$textinput = str_replace("\t", " ", $textinput);

	$text = explode("\r\n", $textinput);
    foreach ($text as $bau) {
        if (empty($bau_type)) {
            if (strpos($bau, "aktuell im Bau auf diesem Planeten") !== false) {
                $bau_type = 'planet';
            }
            if (strpos($bau, "Ausbaustatus") !== false) {
                $bau_type = 'liste';
            }
        } elseif ($bau_type == 'planet') {
            if (strpos($bau, "Ausbau") !== false) {
                break;
            }
            if (strpos($bau, " bis ") !== false) {
                $date        = substr($bau, strpos($bau, " bis ") + 5);
                $date_split  = explode(" ", trim($date));
                $date_d      = explode(".", $date_split[0]);
                $date_t      = explode(":", $date_split[1]);
                $date_stamp  = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
                $bau_dates[] = $date_stamp;
            }
        } elseif ($bau_type == 'liste') {
            if ((strpos($bau, " bis ") !== false) && (strpos($bau, "(" . $planet . ")") !== false)) {
                $date        = substr($bau, strpos($bau, " bis ") + 5);
                $date_split  = explode(" ", trim($date));
                $date_d      = explode(".", $date_split[0]);
                $date_t      = explode(":", $date_split[1]);
                $date_stamp  = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
                $bau_dates[] = $date_stamp;
            }
        }
    }
    $return['date'] = $bau_dates[(count($bau_dates) - 1)];
	$return['date_b1'] = isset($bau_dates[(count($bau_dates) - 2)]) ? $bau_dates[(count($bau_dates) - 2)]: '';
	$return['date_b2'] = isset($bau_dates[(count($bau_dates) - 3)]) ? $bau_dates[(count($bau_dates) - 3)]: '';
	return $return;
}

//******************************************************************************
//
// auf sicheres Passwort ueberpruefen
function secure_password($password)
{
	$alert = "";
	$password = trim($password);
	if ( strlen($password) < 7 ) $alert = "Passwort ist zu kurz (mindestens 7 Zeichen).";
	if ( ! preg_match('%^.*([^a-zA-Z0-9]|[0-9])+.*([^a-zA-Z0-9]|[0-9])+.*$%', $password) )
	{
		$alert = "Passwort enthält nicht mindestens 2 Sonderzeichen oder Zahlen.";
	}
	else
	{
		if ( ! preg_match('%^.*[a-zA-Z]+.*[a-zA-Z]+.*$%', $password) )
		{
			$alert = "Passwort enthält nicht mindestens 2 Buchstaben.";
		}
	}
	return $alert;
}

//******************************************************************************
//
// auf sicheren Benutzernamen ueberpruefen
function check_username($username)
{
	$alert = "";
  $username = trim($username);
  
  if(empty($username))
    return $alert;
  
  if( !preg_match( '%^([a-zA-Z0-9_=\.\-\+\*\(\)\{\} ])*$%', $username)) {
    $alert = "Benutzername enthält ungütige Zeichen.";
  } 
  
  return $alert;
}

//******************************************************************************
//
// Function for fetching a server variable. 
//
function getServerVar($varname, $default) {	
	if( isset($_SERVER[$varname]) && !empty($_SERVER[$varname])) {
        return $_SERVER[$varname];
    }

    return $default;
}

//******************************************************************************
//
// Function for fetching a get/post variable.
//
function getVar($varname, $keephtmlspecialchars = false) {
	global $_GET, $_POST;
	if( isset($_POST[$varname])) {
		if($keephtmlspecialchars === false) {
			if (is_array($_POST[$varname])) {
				$returnary = array();
				foreach($_POST[$varname] as $key => $value) $returnary[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
				return $returnary;
			} else {
				return htmlspecialchars($_POST[$varname], ENT_QUOTES, 'UTF-8');
			}
		} else {
			return $_POST[$varname];
		}
	}
	if( isset($_GET[$varname])) {
		if($keephtmlspecialchars === false) {
			if (is_array($_GET[$varname])) {
				$returnary = array();
				foreach($_GET[$varname] as $key => $value) $returnary[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
				return $returnary;
			} else {			
				return htmlspecialchars($_GET[$varname], ENT_QUOTES, 'UTF-8');
			}
		} else {
			return $_GET[$varname];
		}
	}

	return false;
}

/**
 * function getScanAgeColor
 *
 * Function for getting an html code between green and red, depending on the
 * given scandate. The date is green when 0 and red when reaching an age of $config_map_timeout
 *
 * @param int $scandate unixtime of scan
 *
 * @return string color in format #rrggbb
 */
function getScanAgeColor($scandate)
{
    global $config_map_timeout, $config_color;

    if ($scandate < CURRENT_UNIX_TIME - $config_map_timeout) {
        return $config_color['scanoutdated'];
    } elseif ((CURRENT_UNIX_TIME - $scandate) < DAY) {
        return $config_color['first24h'];
    }

    $i = round(($scandate - CURRENT_UNIX_TIME + $config_map_timeout) / ($config_map_timeout / 510));
    $gruen = ($i < 256) ? $i : 255;
    $rot = ($i < 256) ? 255 : 254 - ($i - 256);
    return "#" . sprintf("%02X",$rot) . sprintf("%02X",$gruen) . "00";
}

//******************************************************************************
//
// Function for getting an html code between green and red, depending on the
// given scandate. The date is green when 0 and red when reaching
//
// old use getScanAgeColor
function scanAge($scandate) {
  global $config_map_timeout;
  
	if ( $scandate < CURRENT_UNIX_TIME - $config_map_timeout )
	{
		return "#FF0000";
	}
  
  $i     = round(( $scandate - CURRENT_UNIX_TIME + $config_map_timeout ) / ($config_map_timeout / 510) );
  $gruen = ($i < 256) ? $i: 255;
  $rot   = ($i < 256) ? 255: 254 - ($i - 256);
  return ("#" . str_pad(dechex($rot), 2, "0", STR_PAD_LEFT) . str_pad(dechex($gruen), 2, "0", STR_PAD_LEFT) . "00");
}

/**
 * function validAccname
 *
 * Überprüft ob sich der angegebene Acc in der IWDB befindet
 *
 * @param string $name Zu überprüfender Accname
 *
 * @return string geprüfter Accname oder bool false falls nicht vorhanden
 *
 * @author masel
 */
function validAccname($name)
{
    global $db, $db_tb_user;
    static $IwAccnames;

    if (empty($name)) {
        return false;
    }

    //sind Informationen nicht im statischen cache -> neu holen
    if (empty($IwAccnames)) {
        $IwAccnames = Array();

        $sql = "SELECT `sitterlogin` FROM  `$db_tb_user`";
        $result = $db->db_query($sql)
            or error(GENERAL_ERROR, 'Could not query iw accnames.', '', __FILE__, __LINE__, $sql);

        while ($row = $db->db_fetch_array($result)) {
            $IwAccnames[] = $row['sitterlogin'];
        }
    }

    if (!in_array($name, $IwAccnames)) {
        return false;
    }

    return $name;
}

/**
 * function ensureSortDirection
 *
 * filtert Sortierungsrichtung
 *
 * @param mixed $inputValue    zu filternder Wert
 * @param mixed $standardValue optional Standardwert
 *
 * @return int|string gefilterter Wert oder Standardwert
 *
 * @author   masel
 */
function ensureSortDirection($inputValue, $standardValue='asc') {

    return ensureValue($inputValue, array('asc', 'desc'), $standardValue);

}

/**
 * function ensureValue
 *
 * filtert einfache Ganzzahlen mit Tausendertrennzeichen
 *
 * @param mixed $inputValue zu filternder Wert
 * @param array $possibleValues gültige Werte
 * @param mixed $standardValue optional Standardwert
 *
 * @return int|string gefilterter Wert, ggf Standardwert oder bool false
 *
 * @author   masel
 */
function ensureValue($inputValue, $possibleValues, $standardValue=null) {

    if (in_array($inputValue, $possibleValues) AND $inputValue !== '') {
        return $inputValue;
    } elseif (!is_null($standardValue)) {
        return $standardValue;
    } else {
        return false;
    }

}

/**
 * function filter_int
 *
 * filtert einfache Ganzzahlen mit Tausendertrennzeichen
 *
 * @param string $numberstring Zahl zum filtern
 * @param int $min_value Minimalwert
 * @param int $max_value Maximalwert
 * @param int $default_value Standardwert
 *
 * @return int gefilterte Zahl
 *
 * @author masel
 */
function filter_int($numberstring, $default_value = null, $min_value = null, $max_value = null) {

    $filtered_number = filter_var($numberstring, FILTER_SANITIZE_NUMBER_INT);
    if (($filtered_number !== false) AND ($filtered_number !== '')) {                    //Ergebnis nicht fehlgeschlagen oder leer

        $filtered_number = (int)$filtered_number;
    } else {                                                                             //sonst Standardwert

        $filtered_number = $default_value;

    }

    if (!is_null($min_value) AND ($filtered_number < $min_value)) {                   //Limit-Check

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
function stripNumber($numberstring, $thousand='.', $comma=',') {

    $numbers = array('0','1','2','3','4','5','6','7','8','9');

    //alles entfernen was keine Zahl ist
     $return = preg_replace("/[^0-9]/","",$numberstring);

    if (isset($debug)) {
    echo "<div class='system_debug_blue'>" . $numberstring . " > " . $return . "</div>";
    }

    //dvisior für den Teiler finden
     $where = 0;
     for ($i = strlen($numberstring)-1; $i>=0; $i--) {

      //ist es eine Nummer und wir beginnen egrade von rechts nach Nummern zu suchen?
      if ( in_array($numberstring[$i],$numbers)) {
       $where++;
      }
      //keine Numemr und iwr haben shcon eine gefunden?
      if ( !in_array($numberstring[$i],$numbers) AND $where > 0 ) {
       break;
      }

    if (isset($debug)) {
    echo "<div class='system_debug_orange'>" . $i . " = " . $numberstring[$i] . " () " . $where . "</div>";
    }
    }
    //wenn where gröser dann handelt es sich um ein Tausendertrennzeichen
    //als where gibt es nur null und eins
    if ($where >= 3) $where = 0;

    //Spezialfall zwei und einstellige Zahlen und Zahlen ohne irgendetwas
    if (strlen($return) == $where) $where = 0;

     $return = $return / pow( 10 , ($where));

    //so jetzt könnte das ganze ja auch negativ sein. Hierbei wird davon ausgegangen,
    // dass der User als Trennzeichen keinen Strich nimmt!
    $position = StrPos($numberstring,"-");
    if ( !($position === false) )  $return = $return * (-1);
    return $return;
}

function sqlRating ( $type )
{
	$normal = "(eisengehalt + chemievorkommen + eisdichte / 2)";
	$eisen_tt = "(tteisen + chemievorkommen + eisdichte / 2)";
	$chemie_tt = "(eisengehalt + ttchemie + eisdichte / 2)";
	$eis_tt = "(eisengehalt + chemievorkommen + tteis / 2)";
	$rating = "(";
	if ($type == 'rating_eisen_tt')
	{
		$rating .= $eisen_tt;
	}
	else if ($type == 'rating_chemie_tt')
	{
		$rating .= $chemie_tt;
	}
	else if ($type == 'rating_eis_tt')
	{
		$rating .= $eis_tt;
	}
	else if ($type == 'rating_best_tt')
	{
		$eisen_eis  = "IF(" . $eisen_tt  . ">" . $eis_tt . "," . $eisen_tt  . "," . $eis_tt . ")";
		$chemie_eis = "IF(" . $chemie_tt . ">" . $eis_tt . "," . $chemie_tt . "," . $eis_tt . ")";
		$rating .= "IF(" . $eisen_tt . ">" . $chemie_tt . ", " . $eisen_eis . "," . $chemie_eis . ")";
	}
	else
	{
		$rating .= $normal;
	}

	$rating .= "+ lebensbedingungen / 4)";
	$rating .= " / (IFNULL(kgmod, 1) * IFNULL(dgmod, 1) * IFNULL(ksmod, 1))";
	$rating .= "+ IF( besonderheiten LIKE '%Asteroidengürtel%',40,0)";
	$rating .= "+ IF( besonderheiten LIKE '%Ureinwohner%',5,0)";
	$rating .= "+ IF( besonderheiten LIKE '%mystische Quelle%',5,0)";
	$rating .= "+ IF( besonderheiten LIKE '%Mond%',25,0)";
	$rating .= "- IF( besonderheiten LIKE '%instabiler Kern%',50,0)";
	$rating .= "- IF( besonderheiten LIKE '%planetarer Ring%',50,0)";
	$rating .= "+ IF( besonderheiten LIKE '%Gold%',30,0)";
	$rating .= "+ IF( besonderheiten LIKE '%roter Nebel%',30,0)";
	$rating .= "+ IF( besonderheiten LIKE '%gelber Nebel%',15,0)";
	$rating .= "+ IF( besonderheiten LIKE '%grüner Nebel%',15,0)";
	$rating .= "+ IF( besonderheiten LIKE '%violetter%',15,0)";
	$rating .= "+ IF( besonderheiten LIKE '%blauer%',10,0)";

	return $rating;
}		
 
function rating ( $scan_data , $coords = '0:0:0' )
{
  global $db, $db_tb_scans;

      if ( isset($coords) AND $coords != '0:0:0' ) {
	
	$sql = "SELECT * FROM " . $db_tb_scans . " WHERE coords='" . $coords . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$scan_data = $db->db_fetch_array($result);

      }

	$minerals = ( $scan_data['eisengehalt'] / 1 ) + 
	            ( $scan_data['chemievorkommen'] / 1 ) + 
				( $scan_data['eisdichte'] / 2 ) + 
				( $scan_data['lebensbedingungen'] / 4 );

	$divisor  = ($scan_data['kgmod'] * $scan_data['dgmod'] * $scan_data['ksmod'] * $scan_data['dsmod']);
	if( $divisor == 0 )
		$divisor = 1;

	$rating = ( $minerals ) / ( $divisor );

	if(!(strpos($scan_data['besonderheiten'],"Asteroidengürtel") === false))
	 $rating += 40;
	if(!(strpos($scan_data['besonderheiten'],"Ureinwohner") === false))
	 $rating += 5;
	if(!(strpos($scan_data['besonderheiten'],"mystische Quelle") === false))
	 $rating += 5;
	if(!(strpos($scan_data['besonderheiten'],"Mond") === false))
	 $rating += 25;
	if(!(strpos($scan_data['besonderheiten'],"instabiler Kern") === false))
	 $rating -= 50;
	if(!(strpos($scan_data['besonderheiten'],"planetarer Ring") === false))
	 $rating -= 50;
	if(!(strpos($scan_data['besonderheiten'],"Gold") === false))
	 $rating += 30;
	if(!(strpos($scan_data['besonderheiten'],"roter Nebel") === false))
	 $rating += 30;
	if(!(strpos($scan_data['besonderheiten'],"gelber Nebel") === false))
	 $rating += 15;
	if(!(strpos($scan_data['besonderheiten'],"grüner Nebel") === false))
	 $rating += 15;
	if(!(strpos($scan_data['besonderheiten'],"violetter Nebel") === false))
	 $rating += 15;
	if(!(strpos($scan_data['besonderheiten'],"blauer Nebel") === false))
	 $rating += 10;
	 
	$rating = sprintf("%.2f", $rating);
	$lifemod = $scan_data['lebensbedingungen'];
	 
	$color = "green";
	if($lifemod < 75) { 
	  $color = "red";	
	}	else if($lifemod >=75 && $lifemod < 95) {
	  $color = "yellow";	
	}
	
	return "<span class='ranking_" . $color . "'>" . $rating . "</span>";
}

/**
 * function makeShortDuration
 *
 * erzeugt eine kurzen String der Dauer zwischen zwei Zeitpunkten
 *
 * @param int $time1 1. Zeitpunkt
 * @param int $time2 optional 2. Zeitpunkt sonst jetzt
 *
 * @return string Dauerstring
 *
 * @author   masel
 */
function makeShortDuration($time1, $time2=null) {
    if (!isset($time1)) {
        return '---';
    }
    if (!isset($time2)) {
        $time2 = CURRENT_UNIX_TIME;
    }

    if ($time1>$time2) {
        $duration = $time1 - $time2;
        $text = '-';
    } else {
        $duration = $time2 - $time1;
        $text = '';
    }

    if (round($duration / MINUTE)<99) {
        $text .= round($duration / MINUTE) . 'm';
    } else if (round($duration / HOUR)<99) {
        $text .= round($duration / HOUR) . 'h';
    } else {
        $text .= round($duration / DAY) . 'd';
    }

    return $text;
}

/**
 * function makeduration2
 *
 * erzeugt eine String der Dauer zwischen zwei Zeitpunkten
 *
 * @param int $time1 1. Zeitpunkt
 * @param int $time2 optional 2. Zeitpunkt sonst jetzt
 *
 * @return string Dauerstring
 *
 * @author   masel
 */
function makeduration2($time1, $time2=null) {
    if (!isset($time1)) {
        return '---';
    }
    if (!isset($time2)) {
        $time2 = CURRENT_UNIX_TIME;
    }

    if ($time1>$time2) {
        $duration = $time1 - $time2;
        $text = '-';
    } else {
        $duration = $time2 - $time1;
        $text = '';
    }
    $Tage = (int)($duration / DAY);
    $duration -= $Tage * DAY;
    $Stunden = (int)($duration / HOUR);
    $duration -= $Stunden * HOUR;
    $Minuten = (int)($duration / MINUTE);
    //$duration -= $Minuten * MINUTE;
    //$Sekunden = $duration;
    if ($Tage === 1) {
        $text .= $Tage . '&nbsp;Tag ';
    } elseif ($Tage > 1) {
        $text .= $Tage . '&nbsp;Tage ';
    }

    $text .= str_pad($Stunden, 2 , '0', STR_PAD_LEFT) . ':';
    $text .= str_pad($Minuten, 2 , '0', STR_PAD_LEFT) . '&nbsp;h';

    return $text;
}

/**
 * function simplexml_load_file_ex
 *
 * läd und parsed die angegebene xml Datei
 *
 * @param string $url URL der xml-Datei
 *
 * @throws Exception
 * @return bool|object|\SimpleXMLElement Simplexml Object bei erfolg, false on error
 *
 * @author   masel
 *
 * @todo error handling
 */
function simplexml_load_file_ex($url)
{
    if (ini_get('allow_url_fopen') == true) {       //zuerst versuchen die xml per simple_load_file zu laden und parsen falls allow_url_fopen on (spart braucht weniger Speicher?)

        return simplexml_load_file($url);

    } elseif (function_exists('curl_init')) {                 //alternativ per curl falls vorhanden

        if ($curl = curl_init($url)) {
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_USERAGENT, 'IWDB');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($curl, CURLOPT_TIMEOUT, 60);
            $result = curl_exec($curl);

            if((curl_errno($curl) === 0)) {
                $info = curl_getinfo($curl);
                if ($info['http_code'] < 400) {
                    curl_close($curl);
                    return simplexml_load_string($result);
                }

            }

        }

    }

    return false;

}

function convert_bbcode($string) {
    global $db, $db_tb_bbcodes;

    if ($string === '') {
       return '';
    }

    //ToDo: implement some caching
    $sql = "SELECT `isregex`, `bbcode`, `htmlcode` FROM `{$db_tb_bbcodes}`;";
    $result_bbcodes = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query bbcodes.', '', __FILE__, __LINE__, $sql);
    while($row_bbcodes = $db->db_fetch_array($result_bbcodes))
    {
        if (!empty($row_bbcodes['bbcode']) AND !empty($row_bbcodes['htmlcode'])) {
            if ($row_bbcodes['isregex']) {
                $return = preg_replace('~'.$row_bbcodes['bbcode'].'~Us', $row_bbcodes['htmlcode'], $string);
                if (!is_null($return)) {
                    $string = $return;
                }
            } else {
                $string = str_replace($row_bbcodes['bbcode'], $row_bbcodes['htmlcode'], $string);
            }
        }
    }

    return $string;
}

function bbcode_buttons($id) {
    global $db, $db_tb_bbcodes;
    //ToDo: implement some caching

    $smilies = array();
    $bbscriptcode = "<script>\n";
    $bbscriptcode .= "var smilies = new Array();\n";

    $sql = "SELECT `bbcode`, `htmlcode` FROM `{$db_tb_bbcodes}` WHERE `htmlcode` LIKE '%<img src=%' GROUP BY `htmlcode`;";        //Smiliebilder holen
    $result_bbcodes = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query bbcodes.', '', __FILE__, __LINE__, $sql);
    while($row_bbcodes = $db->db_fetch_array($result_bbcodes))
    {
        if (!empty($row_bbcodes['bbcode'])) {
            $smilies[$row_bbcodes['bbcode']] = $row_bbcodes['htmlcode'];
        }
    }
    $bbscriptcode .= "smilies = ".json_encode($smilies).";\n";
    $bbscriptcode .= "</script><br>";

    $bbscriptcode .= "<button type='button' class='bbcodebutton' id='bbcode_b_button' onclick='insertText(\"{$id}\",\"[b]\",\"[/b]\")' title='fett'></button>";
    $bbscriptcode .= "<button type='button' class='bbcodebutton' id='bbcode_i_button' onclick='insertText(\"{$id}\",\"[i]\",\"[/i]\")' title='kursiv'></button>";
    $bbscriptcode .= "<button type='button' class='bbcodebutton' id='bbcode_u_button' onclick='insertText(\"{$id}\",\"[u]\",\"[/u]\")' title='unterstrichen'></button>";
    $bbscriptcode .= "<button type='button' class='bbcodebutton' id='bbcode_s_button' onclick='insertText(\"{$id}\",\"[s]\",\"[/s]\")' title='durchgestrichen'></button>";
    $bbscriptcode .= "<button type='button' class='bbcodebutton' id='bbcode_farbe_button' onclick='generateColorPicker(\"{$id}\")' title='Schriftfarbe'></button>";
    $bbscriptcode .= "<button type='button' class='bbcodebutton' id='bbcode_smilie_button' onclick='generateSmiliePicker(\"{$id}\")' title='Smilies'></button>";

    return $bbscriptcode;
}

function getAccNameFromKolos($aKolos) {
    global $db, $db_tb_spieler;

    foreach ($aKolos as $Kolo) {
        //schauen ob Koordinaten als Hauptplanetenkoordinaten bekannt sind (nur bei Kolonien überprüfen)
        if (!empty($Kolo->strObjectType) AND ($Kolo->strObjectType === 'Kolonie')) {
            $Coords = $db->escape($Kolo->strCoords);

            $sql = "SELECT `name` FROM `{$db_tb_spieler}` WHERE `Hauptplanet`='" . $Coords . "';";
            $result = $db->db_query($sql)
                or error(GENERAL_ERROR, 'Could not query homeplanet information.', '', __FILE__, __LINE__, $sql);

            $row = $db->db_fetch_array($result);
            if (!empty($row)) {              //Besitzer gefunden
                return $row['name'];
            }
        }
    }

    //nichts gefunden (nicht eingetragen)
    return false;
}

function find_research_id($researchname, $hidenew = false)
{
    global $db, $db_tb_research, $user_id;

    // Find first research identifier
    $sql = "SELECT `ID` FROM `{$db_tb_research}` WHERE `name`='{$researchname}'";
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR, 'Could not query research information.', '', __FILE__, __LINE__, $sql);
    $row = $db->db_fetch_array($result);

    // Not found, so insert new
    if (empty($row)) {
        $sql2 = "INSERT INTO `{$db_tb_research}` (`name`,`reingestellt`) VALUES('{$researchname}','{$user_id}')";
        $result = $db->db_query($sql2)
            or error(GENERAL_ERROR, 'Could not add research information.', '', __FILE__, __LINE__, $sql);

        if ($hidenew === false) {
            doc_message("Neue Forschung: " . $researchname . " hinzugefügt.");
        }

        return $db->db_insert_id();

    } else {
        return $row['ID'];
    }
}

function redirect($link, $linktext=''){
    /* little helperfunction, it just redirects the page to another
    *
    * by masel
    */

    if (empty($link)) {                  //ohne Weiterleitungslink sinnfrei -> kommentarlos zurückgeben
        return;
    }

    echo "<a href='$link'>$linktext</a>";
    echo "<script language ='JavaScript'>";
    echo "window.location.replace('$link')";
    echo "</script>";
}

/**
 * function sortValuesInc
 *
 * sortiert übergebene Parameter in Aufsteigender richtung und gibt sie als Array zurück
 *
 * @param mixed ...
 *
 * @return array sortierte Parameter
 *
 * @author   masel
 */
function sortValuesInc() {
    $vars = func_get_args();
    sort ($vars, SORT_NUMERIC);
    return $vars;
}

/**
 * function parsetime
 *
 * wandelt Zeitstring in Unixzeit um
 *
 * @param string   $timestring  Zeitstring
 *
 * @return int Unixzeit
 *
 * @author masel
 */
function parseTime($timestring = '')
{
    $timestring = trim($timestring);

    $parsed_datetime = strtotime($timestring);

    if ($parsed_datetime !== false) {
        if ($parsed_datetime > CURRENT_UNIX_TIME) { //ein gültiges Datum in der Zukunft?
            return $parsed_datetime;
        } elseif (strtotime("+1 day", $parsed_datetime) > CURRENT_UNIX_TIME) { //vielleicht morgen um die Zeit?
            return strtotime("+1 day", $parsed_datetime);
        }
    }

    return CURRENT_UNIX_TIME; //ansonsten momentane Zeit zurückgeben
}