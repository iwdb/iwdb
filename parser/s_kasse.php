<?php
/*****************************************************************************/
/* s_kasse.php                                                               */
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
/* Diese Erweiterung der ursp?nglichen DB ist ein Gemeinschafftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens daf?r eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
    die('Hacking attempt...');

//*****************************************************************************
//
// Scanner-Funktion, die von newscan.php aus aufgerufen wird.
//



function parse_kasse($scanlines) {
  global $db, $db_tb_scans, $config_date, $user_sitterlogin;

  $seluser = getVar('seluser') ?  getVar('seluser') : $user_sitterlogin;
  
  // ally vom user herausfinden
  $sql = "SELECT DISTINCT allianz FROM $db_tb_scans WHERE user like '" . $seluser . "'";
  $result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
  while($row = $db->db_fetch_array($result)) 
  {
    $allianz = $row['allianz'];
  }
    
  // Gehe alle Zeilen des Berichtes durch.
  $count=0;
  foreach($scanlines as $scan) {
      $count++;
      
      if ($scan == "Kasseninhalt") {
              $content=preg_replace("/\D/", "", $scanlines[$count]);
              $content=$content/100;
              updateKasse($content, $allianz);
              echo "<p><b>Allykasse updated: $content</b></p>\n";
                echo "<p><u>Bisherige Einzahlungen:</u></p>";
        }

            if (preg_match('/^(\S+.*\S)\s*\d\d\.\d\d\.\d\d\d\d\s\d\d:\d\d\s(\S*).*$/', $scan, $temp)) {
          $money=preg_replace("/\D/", "", $temp[2]);
          $money=$money/100;
          updateIncoming($temp[1], $money, $allianz);
                //Array ( [0] => EINZAHLER 14.04.2007 15:07 117.256,53 1.712 pro Tag [1] => EINZAHLER [2] => 117.256,53 )
              echo $temp[1] . "&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;" . $temp[2] . "<br>\n";
      }

            if (preg_match('/^(\d\d)\.(\d\d)\.(\d\d\d\d)\s(\d\d):(\d\d)\svon\s(.+)\san\s(.+)\s(\d+(?:\D\d\d\d)*)\sCredits\sausgezahlt$/', $scan, $temp))
            {
          $temp[8]=preg_replace("/\D/", "", $temp[8]);
          updateOutgoing($temp[3] . "-" . $temp[2] . "-" . $temp[1] . " " . $temp[4] . ":" . $temp[5] . ":00", $temp[6], $temp[7], $temp[8], $allianz);
            //Array ( [0] => 27.05.2007 08:55 von ZAHLENDER an EMPFAENGER 10.000 Credits ausgezahlt [1] => 27 [2] => 05 [3] => 2007 [4] => 08 [5] => 55 [6] => ZAHLENDER [7] => EMPFAENGER [8] => 10000 )
            (empty($out)) ? $out = "<p><u>Auszahlungen der letzten drei Wochen:</u><p>" : $out = ' ';
          echo $out . $temp[0] . "<br>\n";
      }

		//07.07.2010 20:28 von Labasu an Labasu 20.000 Credits ausgezahlt Grund war kisbau.
		if (preg_match('/^(\d\d)\.(\d\d)\.(\d\d\d\d)\s(\d\d):(\d\d)\svon\s(.+)\san\s(.+)\s(\d+(?:\D\d\d\d)*)\sCredits\sausgezahlt\sGrund\swar\s(.*)$/', $scan, $temp))
		{
			$temp[8]=preg_replace("/\D/", "", $temp[8]);
			updateOutgoing($temp[3] . "-" . $temp[2] . "-" . $temp[1] . " " . $temp[4] . ":" . $temp[5] . ":00", $temp[6], $temp[7], $temp[8], $allianz);
			//Array ( [0] => 27.05.2007 08:55 von ZAHLENDER an EMPFAENGER 10.000 Credits ausgezahlt [1] => 27 [2] => 05 [3] => 2007 [4] => 08 [5] => 55 [6] => ZAHLENDER [7] => EMPFAENGER [8] => 10000 )
			(empty($out)) ? $out = "<p><u>Auszahlungen der letzten drei Wochen:</u><p>" : $out = ' ';
			echo $out . $temp[0] . "<br>\n";
		}
  }
}

function updateKasse($amount, $ally) {
    global $db, $db_tb_kasse_content;
    $sql = "REPLACE INTO $db_tb_kasse_content (amount, time_of_insert, allianz) 
             VALUES ($amount, CURRENT_DATE(), '$ally')";
    $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);   
}

function updateIncoming($user, $amount, $ally) {
    global $db, $db_tb_scans, $db_tb_kasse_incoming;
    $sum_old=0.0;
    $sql = "SELECT sum(amount) FROM $db_tb_kasse_incoming WHERE user like '" . $user . "' AND allianz like '" . $ally . "' AND time_of_insert != CURRENT_DATE()";
    $result = $db->db_query($sql)
      or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
    while($row = $db->db_fetch_array($result)) 
    {
      $sum_old = $row['sum(amount)'];
    }

    $amount = $amount - $sum_old;

    $sql = "REPLACE INTO $db_tb_kasse_incoming (user, amount, time_of_insert, allianz) 
             VALUES ('$user', $amount, CURRENT_DATE(), '$ally')";
    $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

function updateOutgoing($time, $payedfrom, $payedto, $amount, $ally) {
    global $db, $db_tb_kasse_outgoing;
  
    $sql = "REPLACE INTO $db_tb_kasse_outgoing (payedfrom, payedto, amount, time_of_pay, allianz) 
             VALUES ('$payedfrom', '$payedto', '$amount', '$time', '$ally')";
    $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

?> 
