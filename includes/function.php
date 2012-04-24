<?php
/*****************************************************************************/
/* function.php                                                              */
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

//******************************************************************************
//
// Funktion um die Fehlermeldungen zusammenzusetzen.
function error($err_code, $err_msg = '', $err_title = '', $err_file = '', $err_line = '', $sql = '') {
	global $db, $error;
	$error_return = "";

	// timestamp for the error entry
	$err_dt = date("Y-m-d H:i:s (T)");

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
			if (defined( 'DEBUG' ) && DEBUG === TRUE)
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

		$modmaurer = ( ($genmaurer == 1) && (( strpos($row_geb['category'], "Bunker") !== FALSE ) || ( strpos($row_geb['category'], "Lager") !== FALSE )) ) ? 0.5: 1;

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

			$bild_url = ( empty($row_gebaeude['bild']) ) ? "bilder/gebs/blank.gif": "bilder/gebs/" . $row_gebaeude['bild'] . ".jpg";
			$modmaurer = ( ($user_genmaurer == 1) && (( strpos($row_gebaeude['category'], "Bunker") !== FALSE ) || ( strpos($row_gebaeude['category'], "Lager") !== FALSE )) ) ? 0.5: 1;

			$dauer = round($row_gebaeude['dauer'] * $user_gengebmod * $modmaurer * $planetenmod * $bauschleifenmod);

			$return = "<img src=\"" . $bild_url . "\" border=\"0\" width=\"50\" height=\"50\" style=\"vertical-align:middle; padding-top: 3px;\"> " . $row_gebaeude['name'] . " [" . dauer($dauer) . "]" . (( empty($bauschleife) ) ? "" : " [Bauschleife]" ) . "\n" . (( empty($text) ) ? "" : "<br><br>" . nl2br($text) );
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
function timeimport($textinput, $planet = '')
{
	$bau_type = "";
	$textinput = str_replace(" \t", " ", $textinput);
	$textinput = str_replace("\t", " ", $textinput);

	$text = explode("\r\n", $textinput);
	foreach ($text as $bau)
	{
		if ( empty($bau_type) )
		{
			if ( strpos($bau, "aktuell im Bau auf diesem Planeten") !== FALSE ) {
				$bau_type = 'planet';
			}
			if ( strpos($bau, "Ausbaustatus") !== FALSE ) {
				$bau_type = 'liste';
			}
		}
		elseif ( $bau_type == 'planet' )
		{
			if ( strpos($bau, " bis ") !== FALSE ) {
				$date = substr($bau, strpos($bau, " bis ") + 5);
				$date_split = explode(" ", trim($date));
				$date_d = explode(".", $date_split[0]);
				$date_t = explode(":", $date_split[1]);
				$date_stamp = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
				$bau_dates[] = $date_stamp;
			}
		}
		elseif ( $bau_type == 'liste' )
		{
			if (( strpos($bau, " bis ") !== FALSE ) && ( strpos($bau, "(" . $planet . ")") !== FALSE )) {
				$date = substr($bau, strpos($bau, " bis ") + 5);
				$date_split = explode(" ", trim($date));
				$date_d = explode(".", $date_split[0]);
				$date_t = explode(":", $date_split[1]);
				$date_stamp = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
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
function getVar($varname, $noentities = false) {
	global $_GET, $_POST;
	if( isset($_POST[$varname])) {
		if(!$noentities) {
			if (is_array($_POST[$varname])) {
				$returnary = array();
				foreach($_POST[$varname] as $key => $value) $returnary[$key] = htmlentities($value, ENT_QUOTES, 'UTF-8');
				return $returnary;
			} else {
				return htmlentities($_POST[$varname], ENT_QUOTES, 'UTF-8');
			}
		} else {
			return $_POST[$varname];
		}
	}
	if( isset($_GET[$varname])) {
		if(!$noentities) {
			if (is_array($_GET[$varname])) {
				$returnary = array();
				foreach($_GET[$varname] as $key => $value) $returnary[$key] = htmlentities($value, ENT_QUOTES, 'UTF-8');
				return $returnary;
			} else {			
				return htmlentities($_GET[$varname], ENT_QUOTES, 'UTF-8');
			}
		} else {
			return $_GET[$varname];
		}
	}

	return FALSE;
}

//******************************************************************************
//
// Function for getting an html code between green and red, depending on the
// given scandate. The date is green when 0 and red when reaching   
//
function scanAge($scandate) {
  global $config_date, $config_map_timeout;
  
	if ( $scandate < $config_date - $config_map_timeout )
	{
		return "#FF0000";
	}
  
  $i     = round(( $scandate - $config_date + $config_map_timeout ) / ($config_map_timeout / 510) );
  $gruen = ($i < 256) ? $i: 255;
  $rot   = ($i < 256) ? 255: 254 - ($i - 256);
  return ("#" . str_pad(dechex($rot), 2, "0", STR_PAD_LEFT) . str_pad(dechex($gruen), 2, "0", STR_PAD_LEFT) . "00");
}

//******************************************************************************
//
// Replace thousand-separator with nothing, and the comma-sign with a period
// Ideally the given string is a pure number with formatting.
//
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
	
	return "<span class=\"ranking_" . $color . "\">" . $rating . "</span>";
}

?>