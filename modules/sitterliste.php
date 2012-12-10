<?php
/*****************************************************************************/
/* sitterliste.php                                                           */
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

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!"; 
	exit; 
}

if ( ( $user_adminsitten != SITTEN_BOTH ) && ( $user_adminsitten != SITTEN_ONLY_LOGINS ) )
	die('Hacking attempt...');

function dauer($zeit)
{
	$tage = floor($zeit / DAY);
	$return = ($tage > 0) ? $tage . " Tage, ": "";
	$stunden = floor(($zeit - $tage * DAY) / 3600);
	$minuten = round(($zeit - $tage * DAY - $stunden * 3600) / 60);
	$return .= str_pad($stunden, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minuten, 2, "0", STR_PAD_LEFT);
	return $return;
}

// Auftrag editieren //
$alert = "";
$edit = getVar('edit');
if ( ! empty($edit) )
{
	$bis_array = array("bis:","bis");
	$date = str_replace($bis_array, "", getVar('date'));
	$date_b1 = str_replace($bis_array, "", getVar('date_b1'));
	$date_b2 = str_replace($bis_array, "", getVar('date_b2'));
	$plus_stunden = getVar('plus_stunden');
	$plus_minuten = getVar('plus_minuten');
	$date_parse = getVar('date_parse');
	$comment = getVar('comment');
	$del = getVar('del');
	$auftragid = getVar('auftragid');
	$auftragids = explode("|", $auftragid);

	$sql = "SELECT * FROM " . $db_tb_sitterauftrag . " WHERE id = " . $auftragids[0];
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_first = $db->db_fetch_array($result);
	$sql = "SELECT * FROM " . $db_tb_sitterauftrag . " WHERE id = " . $auftragids[(count($auftragids) - 1)];
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_last = $db->db_fetch_array($result);


	if ( ( ! empty($del) ) && ( $user_status == "admin" ) )
	{
		foreach ($auftragids as $delid)
		{
			$sql = "SELECT * FROM " . $db_tb_sitterauftrag . " WHERE id = " . $delid;
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$row = $db->db_fetch_array($result);

			$sql = "SELECT planetenname, dgmod FROM " . $db_tb_scans .  " WHERE coords = '" . $row['planet'] . "'";
			$result_planet = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$row_planet = $db->db_fetch_array($result_planet);

			$bauschleifenmod = 1;
			if ( empty($peitschen) )
			{
				if ( $row['date_b1'] <> $row['date'] ) $bauschleifenmod = 1.1;
				if ( $row['date_b2'] <> $row['date_b1'] ) $bauschleifenmod = 1.2;
			}
			$logtext = "<font color='#FF0000'><b>" . $row_planet['planetenname'] . " [" . $row['planet'] . "]<br>" . auftrag($row['typ'], $row['bauschleife'], $row['bauid'], $row['auftrag'], $row['schiffanz'], $row_planet['dgmod'], $row['user'], $bauschleifenmod) . "<br>gelöscht von " . $user_sitterlogin . ( (empty($comment) ) ? "" : ": " . nl2br($comment) ) . "</b></font>";
			$sql = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin, fromuser, date, action) VALUES ('" . $row['user'] . "', '" . $user_sitterlogin . "', '" . CURRENT_UNIX_TIME . "', '" . $logtext . "')";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		
			$sql = "DELETE FROM " . $db_tb_sitterauftrag . " WHERE id = '" . $delid . "'";
			$result_del = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}
		$alert = "<br><font color='#FF0000'><b>Auftrag gelöscht.</b></font><br>";
	}
	else
	{
		if ( ! empty($comment) )
		{
			$sql = "UPDATE " . $db_tb_sitterauftrag . " SET auftrag = '" . $row_last['auftrag'] . "\nvon " . $user_sitterlogin . ": " . $comment . "' WHERE id = '" . $auftragids[(count($auftragids) - 1)] . "'";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$alert .= "<br><font color='#FF0000'><b>Kommentar hinzugefügt.</b></font><br>";
		}
		if ( ! empty($date_parse) )
		{
			$date_parse = timeimport($date_parse, $row_first['planet']);
			$date = $date_parse['date'];
			$date_b1 = empty($date_parse['date_b1']) ? $date_parse['date']: $date_parse['date_b1'];
			$date_b2 = empty($date_parse['date_b2']) ? $date_b1: $date_parse['date_b2'];
		}
		else
		{
			$datetime = explode(" ", trim($date)); $date_d = explode(".", $datetime[0]); $date_t = explode(":", $datetime[1]);
			if ( ( $date_t[0] >= 0 ) && ( $date_t[0] <= 24 ) && ( $date_t[1] >= 0 ) && ( $date_t[1] < 60 ) && ( $date_d[1] >= 1 ) && ( $date_d[1] <= 12 ) && ( $date_d[0] >= 1 ) && ( $date_d[0] <= 31 ) )
				$date = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
		
			if ( ! empty($date_b1) )
			{
				$datetime = explode(" ", trim($date_b1)); $date_d = explode(".", $datetime[0]); $date_t = explode(":", $datetime[1]);
				if ( ( $date_t[0] >= 0 ) && ( $date_t[0] <= 24 ) && ( $date_t[1] >= 0 ) && ( $date_t[1] < 60 ) && ( $date_d[1] >= 1 ) && ( $date_d[1] <= 12 ) && ( $date_d[0] >= 1 ) && ( $date_d[0] <= 31 ) )
					$date_b1 = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
			}
			else $date_b1 = $date;
		
			if ( ! empty($date_b2) )
			{
				$datetime = explode(" ", trim($date_b2)); $date_d = explode(".", $datetime[0]); $date_t = explode(":", $datetime[1]);
				if ( ( $date_t[0] >= 0 ) && ( $date_t[0] <= 24 ) && ( $date_t[1] >= 0 ) && ( $date_t[1] < 60 ) && ( $date_d[1] >= 1 ) && ( $date_d[1] <= 12 ) && ( $date_d[0] >= 1 ) && ( $date_d[0] <= 31 ) )
					$date_b2 = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
			}
			else $date_b2 = $date_b1;
		}

		//Schieben auf alle Zeiten anwenden
		if ($plus_stunden>0 || $plus_minuten>0) {
			$schiebe_zeit=$plus_stunden*60*60+$plus_minuten*60;
			$date=CURRENT_UNIX_TIME+$schiebe_zeit;
			$date_b1=CURRENT_UNIX_TIME+$schiebe_zeit;
			$date_b2=CURRENT_UNIX_TIME+$schiebe_zeit;
		}

		if ( ( $date < CURRENT_UNIX_TIME - $config_sitterauftrag_timeout ) || ( $date_b1 < CURRENT_UNIX_TIME - $config_sitterauftrag_timeout ) || ( $date_b2 < CURRENT_UNIX_TIME - $config_sitterauftrag_timeout ) )
		{
			$alert .= "<br><font color='#FF0000'><b>Ungültiger Zeitpunkt.</b></font><br>";
		}
		else
		{
			if ( $row_first['bauschleife'] != "1" )
			{
			 $date_b1 = $date;
			 $date_b2 = $date;
			}

			$sql = "UPDATE " . $db_tb_sitterauftrag . " SET date = '" . $date . "', date_b1 = '" . $date_b1 . "', date_b2 = '" . $date_b2 . "' WHERE id = '" . $auftragids[0] . "'";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

			dates($auftragids[0], $row_first['user']);

			if ( ( $date <> $row_first['date']) || ( $date_b1 <> $row_first['date_b1']) || ( $date_b2 <> $row_first['date_b2']) ) $alert .= "<br><font color='#FF0000'><b>Zeit editiert.</b></font><br>";
		}
	}

	// Punkte //
	if ( $row['user'] != $user_sitterlogin )
	{	
		$sql = "UPDATE " . $db_tb_user . " SET sitterpunkte = sitterpunkte + " . $config_sitterpunkte_auftrag . " WHERE sitterlogin = '" . $user_sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}
	
	//Start Log
	
	$sql = "SELECT * FROM " . $db_tb_sitterauftrag . " WHERE id = " . $auftragids[0];
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	
	// Planetendaten //
	$sql = "SELECT planetenname, dgmod FROM " . $db_tb_scans .  " WHERE coords = '" . $row['planet'] . "'";
	$result_planet = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_planet = $db->db_fetch_array($result_planet);
	
	//Log
	$bauschleifenmod = 1;
	if ( empty($peitschen) )
	{
		if ( $row['date_b1'] <> $row['date'] ) $bauschleifenmod = 1.1;
		if ( $row['date_b2'] <> $row['date_b1'] ) $bauschleifenmod = 1.2;
	}
	if ($plus_stunden>0 || $plus_minuten>0) {
		$verschoben_text= " (verschoben um " . $plus_stunden . ":" . $plus_minuten . ") ";
	}
	else {
		$verschoben_text = "";
	}
	if ($del!="1"){
		$logtext = "Zeit geändert auf " . strftime($config_sitter_timeformat, $date) . $verschoben_text . "<br>" . $row_planet['planetenname'] . " [" . $row['planet'] . "]<br>" . auftrag($row['typ'], $row['bauschleife'], $row['bauid'], $row['auftrag'], $row['schiffanz'], $row_planet['dgmod'], $row['user'], $bauschleifenmod);
		}
		
	$sql = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin, fromuser, date, action) VALUES ('" . $row['user'] . "', '" . $user_sitterlogin . "', '" . CURRENT_UNIX_TIME . "', '" . $logtext . "')";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

// Auftrag erledigt //
$erledigt = getVar('erledigt');
if ( ! empty($erledigt) )
{
	$erledigtids = explode("|", $erledigt);

	$sql = "SELECT * FROM " . $db_tb_sitterauftrag . " WHERE id = " . $erledigtids[0];
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	if ( ! empty($row['typ']) )
	{
		$sql = "SELECT genbauschleife, peitschen FROM " . $db_tb_user .  " WHERE sitterlogin = '" . $row['user'] . "'";
		$result_user = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_user = $db->db_fetch_array($result_user);
		$bauschleifenlaenge = ( empty($row_user['genbauschleife'] ) ) ? 2: 3;
		$peitschen = $row_user['peitschen'];
	
		// Planetendaten //
		$sql = "SELECT planetenname, dgmod FROM " . $db_tb_scans .  " WHERE coords = '" . $row['planet'] . "'";
		$result_planet = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_planet = $db->db_fetch_array($result_planet);
	
		if ( ( $row['date'] > CURRENT_UNIX_TIME ) || ( empty($row['refid']) ) )
		{
			$sql = "DELETE FROM " . $db_tb_sitterauftrag . " WHERE id = '" . $erledigtids[0] . "'";
			$result_del = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	
			// Log //
			$bauschleifenmod = 1;
			if ( empty($peitschen) )
			{
				if ( $row['date_b1'] <> $row['date'] ) $bauschleifenmod = 1.1;
				if ( $row['date_b2'] <> $row['date_b1'] ) $bauschleifenmod = 1.2;
			}
			$logtext = $row_planet['planetenname'] . " [" . $row['planet'] . "]<br>" . auftrag($row['typ'], $row['bauschleife'], $row['bauid'], $row['auftrag'], $row['schiffanz'], $row_planet['dgmod'], $row['user'], $bauschleifenmod);
	    if(!empty($row['ByUser']) && ($row['user'] != $row['ByUser'])) {
			  $logtext .= "<br>(Auftrag erstellt von " . $row['ByUser'] . ")";
			}
			$sql = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin, fromuser, date, action) VALUES ('" . $row['user'] . "', '" . $user_sitterlogin . "', '" . CURRENT_UNIX_TIME . "', '" . $logtext . "')";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		
			// Punkte //
			if ( $row['user'] != $user_sitterlogin )
			{	
				$sql = "UPDATE " . $db_tb_user . " SET sitterpunkte = sitterpunkte + " . $config_sitterpunkte_auftrag . " WHERE sitterlogin = '" . $user_sitterlogin . "'";
				$result = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			}

			$alert = "<br><font color='#FF0000'><b>Auftrag als erledigt markiert.</b></font><br>";
		}
		else
		{
			$bis_array = array("bis:","bis");
			$date = str_replace($bis_array, "", getVar('date'));
			$date_b1 = str_replace($bis_array, "", getVar('date_b1'));
			$date_b2 = str_replace($bis_array, "", getVar('date_b2'));
			$date_parse = getVar('date_parse');
      
			if ( ! empty($date_parse) )
			{
				$date_parse = timeimport($date_parse, $row['planet']);
				$date = $date_parse['date'];
				$date_b1 = empty($date_parse['date_b1']) ? $date_parse['date']: $date_parse['date_b1'];
				$date_b2 = empty($date_parse['date_b2']) ? $date_b1: $date_parse['date_b2'];
			}
			else
			{

				if ( ! empty($date) ) {
					$datetime = explode(" ", trim($date));
					$date_d = explode(".", $datetime[0]);
					$date_t = explode(":", $datetime[1]);
					if ( ( $date_t[0] >= 0 ) && ( $date_t[0] <= 24 ) && ( $date_t[1] >= 0 ) && ( $date_t[1] < 60 ) && ( $date_d[1] >= 1 ) && ( $date_d[1] <= 12 ) && ( $date_d[0] >= 1 ) && ( $date_d[0] <= 31 ) )
						$date = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
				
					if ( ! empty($date_b1) )
					{
						$datetime = explode(" ", trim($date_b1)); $date_d = explode(".", $datetime[0]); $date_t = explode(":", $datetime[1]);
						if ( ( $date_t[0] >= 0 ) && ( $date_t[0] <= 24 ) && ( $date_t[1] >= 0 ) && ( $date_t[1] < 60 ) && ( $date_d[1] >= 1 ) && ( $date_d[1] <= 12 ) && ( $date_d[0] >= 1 ) && ( $date_d[0] <= 31 ) )
							$date_b1 = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
					}
					else $date_b1 = $date;
				
					if ( ! empty($date_b2) )
					{
						$datetime = explode(" ", trim($date_b2)); $date_d = explode(".", $datetime[0]); $date_t = explode(":", $datetime[1]);
						if ( ( $date_t[0] >= 0 ) && ( $date_t[0] <= 24 ) && ( $date_t[1] >= 0 ) && ( $date_t[1] < 60 ) && ( $date_d[1] >= 1 ) && ( $date_d[1] <= 12 ) && ( $date_d[0] >= 1 ) && ( $date_d[0] <= 31 ) )
							$date_b2 = mktime($date_t[0], $date_t[1], 00, $date_d[1], $date_d[0], $date_d[2]);
					}
					else $date_b2 = $date_b1;
				}
			}

			if ( empty($date) && empty($date_parse) && empty($date_b1) && empty($date_b2 ) )
				$alert = "<br><font color='#FF0000'><b>Bitte ausfüllen.</b></font><br>";
			else
				$alert = ( ( $date < CURRENT_UNIX_TIME - $config_sitterauftrag_timeout ) || ( $date_b1 < CURRENT_UNIX_TIME - $config_sitterauftrag_timeout ) || ( $date_b2 < CURRENT_UNIX_TIME - $config_sitterauftrag_timeout ) ) ? "<br><font color='#FF0000'><b>Ungueltiger Zeitpunkt.</b></font><br>": "";
	
			$sql = "SELECT bauschleife, refid FROM " . $db_tb_sitterauftrag . " WHERE id = '" . $erledigtids[(count($erledigtids) - 1)] . "'";
			$result_act = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$row_act = $db->db_fetch_array($result_act);
			$nextid = $row_act['refid'];
			if ( empty($nextid) ) $count = 0;
			else $count = 1;

			while ( ( $count < $bauschleifenlaenge ) && ( isset($row_act) ) ) 
			{
				// Auftragsabhaengigkeiten //
				$sql = "SELECT bauschleife, refid FROM " . $db_tb_sitterauftrag . " WHERE id = '" . $row_act['refid'] . "'";
				$result_act = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				$row_act = $db->db_fetch_array($result_act);
				if ( $row_act['bauschleife'] != "1" )
				{
					unset($row_act);
				}
				else
				{
					if ( ! empty($row_act['refid']) ) $count++;
				}
			}
	
			if ( empty($alert) )
			{
				$sql = "UPDATE " . $db_tb_sitterauftrag . " SET date = '" . $date . "', date_b1 = '" . $date_b1 . "', date_b2 = '" . $date_b2 . "' WHERE id = '" . $nextid . "'";
				$result_update = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				dates($nextid, $row['user']);
			}
	
			if ( ( empty($alert) ) || ( $count == 0 ) )
			{
				foreach ( $erledigtids as $erledigtid )
				{
					// Log //
					$sql = "SELECT * FROM " . $db_tb_sitterauftrag . " WHERE id = " . $erledigtid;
					$result_x = $db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
					$row_x = $db->db_fetch_array($result_x);
	
					$sql = "SELECT peitschen FROM " . $db_tb_user .  " WHERE sitterlogin = '" . $row_x['user'] . "'";
					$result_user = $db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
					$row_user = $db->db_fetch_array($result_user);
			
					$bauschleifenmod = 1;
					if ( empty($row_user['peitschen']) )
					{
						if ( $row_x['date_b1'] <> $row_x['date'] ) $bauschleifenmod = 1.1;
						if ( $row_x['date_b2'] <> $row_x['date_b1'] ) $bauschleifenmod = 1.2;
					}
	
					$logtext = $row_planet['planetenname'] . " [" . $row['planet'] . "]<br>" . auftrag($row_x['typ'], $row_x['bauschleife'], $row_x['bauid'], $row_x['auftrag'], $row_x['schiffanz'], $row_planet['dgmod'], $row_x['user'], $bauschleifenmod);
					$sql = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin, fromuser, date, action) VALUES ('" . $row['user'] . "', '" . $user_sitterlogin . "', '" . CURRENT_UNIX_TIME . "', '" . $logtext . " ')";
					$result_log = $db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	
					$sql = "DELETE FROM " . $db_tb_sitterauftrag . " WHERE id = '" . $erledigtid . "'";
					$result_del = $db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	
				}
				// Punkte //
				if ( $row['user'] != $user_sitterlogin)
				{	
					$sql = "UPDATE " . $db_tb_user . " SET sitterpunkte = sitterpunkte + " . $config_sitterpunkte_auftrag . " WHERE sitterlogin = '" . $user_sitterlogin . "'";
					$result = $db->db_query($sql)
						or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				}
	
				$alert = "<br><font color='#FF0000'><b>Auftrag als erledigt markiert.</b></font><br>";
			}
	
			if ( ( empty($date) ) && ( empty($date_parse) ) && ( $count != 0 ) )
			{
?>
<font style="font-size: 22px; color: #004466">Sitterauftragszeit aktualisieren</font><br>
<br>
Den Auftrag, den du eben erledigt hast, hat Folgeaufträge eingetragen.<br>
Bitte aktualisiere für diese die Zeit, indem du folgendes Formular ausfüllst.<br>
Danach wird der Auftrag als erledigt markiert. Danke.<br><br>
<form method="POST" action="index.php?action=sitterliste&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 60%;">
<?php
				if ( $count > 1 )
				{
?>
 <tr>
  <td class="windowbg2">
   Zeit frühstens 2:
  </td>
  <td class="windowbg1">
   <input type="text" name="date_b2" id="date_b2_<?php echo $row['id'];?>" value="" style="width: 200;">
  </td>
 </tr>
<?php
				}
				if ( $count > 0 )
				{
?>
 <tr>
  <td class="windowbg2">
   Zeit frühstens 1:
  </td>
  <td class="windowbg1">
   <input type="text" name="date_b1" id="date_b1_<?php echo $row['id'];?>" value="" style="width: 120;">
   <input type="button" name="kopieren" value="kopieren" onclick="kopiere_zeit('<?php echo $row['id'];?>');">
  </td>
 </tr>
<?php
				}
?>
 <tr>
  <td class="windowbg2">
   Zeit spätestens:<br>
   <i>Zeit, zu der alle Bauschleifenaufträge auslaufen.</i>
  </td>
  <td class="windowbg1">
   <input type="text" name="date" id="date_<?php echo $row['id'];?>" value="" style="width: 200;">
  </td>
 </tr>
 <tr>
  <td class="windowbg2">
   oder automatische Erkennung:<br>
   <i>Aktuelle Bauliste aus Icewars kopieren.</i>
  </td>
  <td class="windowbg1">
   <textarea name="date_parse" rows="4" style="width: 200;"></textarea>
  </td>
 </tr>
 <tr>
  <td colspan="2" class="titlebg" align="center">
   <input type="hidden" name="erledigt" value="<?php echo $erledigt;?>"><input type="submit" value="speichern" name="B1" class="submit">
  </td>
 </tr>
</form>
</table><br><br>
<?php
			}
		}
	}
}
?>
<script language="JavaScript" type="text/javascript"><!--
function Collapse(what)
{
	var collapseImage = document.getElementById("collapse_" + what);
	var collapseRow = document.getElementById("row_" + what);

	if (!collapseImage)
		return;

	if (collapseRow.style.display == "")
	{
		collapseRow.style.display = "none";
		collapseImage.src = "bilder/plus.gif";
	}
	else
	{
		collapseRow.style.display = "";
		collapseImage.src = "bilder/minus.gif";
	}
}

function kopiere_zeit(id) {
	document.getElementById("date_" + id).value=document.getElementById("date_b1_" + id).value;
	document.getElementById("date_b2_" + id).value=document.getElementById("date_b1_" + id).value;
}
// --></script>

<font style="font-size: 22px; color: #004466">Sitteraufträge</font><br>
<?php
	echo ( empty($alert) ) ? "": $alert;

// Start Dauerauftraege //
include("dauerauftraege.php");
// Ende Dauerauftraege //

?>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
  <td class="titlebg" colspan="6" align="center">
   <b>aktuelle Sitteraufträge</b>
  </td>
 </tr>
 <tr>
  <td class="titlebg" style="width:20%;">
   <b>Username</b>
  </td>
  <td class="titlebg" style="width:15%;">
   <b>Zeit</b>
  </td>
  <td class="titlebg" style="width:15%;">
   <b>Planet</b>
  </td>
  <td class="titlebg" style="width:30%;">
   <b>Auftrag</b>
  </td>
  <td class="titlebg" style="width:10%;">
   <b>einloggen / übernehmen</b>
  </td> 
  <td class="titlebg" style="width:10%;">
   <b>letzter Login</b>
  </td>  
 </tr>
<?php
// Auftraege durchgehen //
$sql = "SELECT AVG(sitterpunkte) FROM " . $db_tb_user . " WHERE sitterpunkte <> 0";
$result_avg = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row_avg = $db->db_fetch_array($result_avg);

$sql = "SELECT t1.* FROM " . $db_tb_sitterauftrag . " as t1 LEFT JOIN " . $db_tb_sitterauftrag . " as t2 ON t1.id = t2.refid WHERE t2.refid is null AND t1.date_b2 <= " . CURRENT_UNIX_TIME;
if ($user_fremdesitten == "0")
{
	$sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE id=t1.user) = '" . $user_allianz . "'";
}
$sql .= " ORDER BY t1.date ASC, t1.date_b2 ASC";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while($row = $db->db_fetch_array($result))
{
	if ( $row['date'] < CURRENT_UNIX_TIME )
	{
		$row['date_b1'] = $row['date'];
	}
	if ( $row['date_b1'] < CURRENT_UNIX_TIME )
	{
		$row['date_b2'] = $row['date_b1'];
	}
	if ( ( $row['date_b1'] < CURRENT_UNIX_TIME ) || ( $row['date_b2'] < CURRENT_UNIX_TIME ) )
	{
		$sql = "UPDATE " . $db_tb_sitterauftrag . " SET date_b1 = '" . $row['date_b1'] . "', date_b2 = '" . $row['date_b2'] . "' WHERE id = '" . $row['id'] . "'";
		$result_u = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		dates($row['id'], $row['user']);
	}
	
	$count = 0;
	// Planetendaten //
	$sql = "SELECT planetenname, dgmod FROM " . $db_tb_scans .  " WHERE coords = '" . $row['planet'] . "'";
	$result_planet = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_planet = $db->db_fetch_array($result_planet);



	$row_act = $row;

	$sql = "SELECT genbauschleife, peitschen, ikea FROM " . $db_tb_user .  " WHERE sitterlogin = '" . $row['user'] . "'";
	$result_user = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_user = $db->db_fetch_array($result_user);
	$bauschleifenlaenge = ( empty($row_user['genbauschleife'] ) ) ? 2: 3;
	$peitschen = $row_user['peitschen'];
	$ikea = $row_user['ikea'];

	$row['auftrag'] = "";
	while ( ( $count < $bauschleifenlaenge ) && ( isset($row_act) ) ) 
	{
		$count++;

		// Auftragsformatierung //
		$num = ( $row['date'] <= CURRENT_UNIX_TIME) ? 2: 1;
		if (!empty($ikea) && $row_act['typ']=="Gebaeude") {
			$sql = "SELECT * FROM " . $db_tb_gebaeude . " WHERE id = '" . $row_act['bauid'] . "'";
			$result_gebaeude = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			$row_gebaeude = $db->db_fetch_array($result_gebaeude);
			if ($row_gebaeude['category']==" 5. Förderungsanlagen") {
				$num = 4;
			}
		}

		$bauschleifenmod = 1;
		if ( empty($peitschen) )
		{
			if ( $row_act['date_b1'] <> $row_act['date'] ) $bauschleifenmod = 1.1;
			if ( $row_act['date_b2'] <> $row_act['date_b1'] ) $bauschleifenmod = 1.2;
		}
		$row['auftrag'] .= auftrag($row_act['typ'], $row_act['bauschleife'], $row_act['bauid'], $row_act['auftrag'], $row_act['schiffanz'], $row_planet['dgmod'], $row_act['user'], $bauschleifenmod) . "<br>";

		// Auftragsabhaengigkeiten //
$sql = "SELECT id, auftrag, bauid, bauschleife, typ, refid, user, date_b1, date_b2, date, schiffanz FROM " . $db_tb_sitterauftrag . " WHERE id = '" . $row_act['refid'] . "' AND date <= '".(CURRENT_UNIX_TIME + $sitter_wie_lange_vorher_zeigen) . "';";
		$result_act = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_act = $db->db_fetch_array($result_act);
		if (( $row_act['bauschleife'] != "1" ) || (! isset($row_act['id']) ) ) unset($row_act);
		if ( ( ! empty($row_act['id']) ) && ($count < $bauschleifenlaenge) ) $row['id'] .= "|" . $row_act['id'];
	}
	// Sitteraktivitaet //
	$sql = "SELECT sitterpunkte FROM " . $db_tb_user . "  WHERE sitterlogin = '" . $row['user'] . "'";
	$result_user = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_user = $db->db_fetch_array($result_user);

	$sql = "SELECT id FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $user_sitterlogin . "' AND fromuser = '" . $row['user'] . "' AND fromuser <> '" . $user_sitterlogin . "'";
	$result_punkte = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	// letzter Login //
	$sql = "SELECT fromuser, date, MAX(date) FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $row['user'] . "' AND action = 'login' GROUP BY date";
	$result_lastlogin = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	unset($row_lastlogin);
	while ($row_lastlogins = $db->db_fetch_array($result_lastlogin))
	{
		if ( $row_lastlogins['date'] == $row_lastlogins['MAX(date)'] ) $row_lastlogin = $row_lastlogins;
	}

	$users_sitterpunkte = $row_user['sitterpunkte'];
	$users_sitterpunkte_user = $config_sitterpunkte_friend * $db->db_num_rows($result_punkte);
	if (isset($row_lastlogin) ) {
		$users_lastlogin = $row_lastlogin['MAX(date)'];
		$users_lastlogin_user = $row_lastlogin['fromuser'];
		$users_logged_in = ( ( $row_lastlogin['MAX(date)'] > (CURRENT_UNIX_TIME - $config_sitterlogin_timeout) ) && ( $row_lastlogin['fromuser'] != $user_sitterlogin ) ) ? $row_lastlogin['fromuser'] : "";
	}
    else
    {
       $users_logged_in="";
    }
?>
 <tr>
  <td class="windowbg<?php echo $num;?>">
<?php
if ( $user_status == "admin" ) echo "<a href='index.php?action=profile&sitterlogin=" . urlencode($row['user']) . "&sid=" . $sid . "'>" . $row['user'] . "</a>";
else echo $row['user'];
?>
   [<?php echo $users_sitterpunkte;?> + <?php echo $users_sitterpunkte_user;?>]
   <?php echo ( ($users_sitterpunkte+$users_sitterpunkte_user) > (3 * round($row_avg['AVG(sitterpunkte)']) ) ) ? "<img src='bilder/star1.gif' alt='star1' style='border:0;vertical-align:middle;'>": ( ( ($users_sitterpunkte+$users_sitterpunkte_user) > (2 * round($row_avg['AVG(sitterpunkte)']) ) ) ? "<img src='bilder/star2.gif' alt='star2' style='border:0;vertical-align:middle;'>" : ( ( ($users_sitterpunkte+$users_sitterpunkte_user) > (round($row_avg['AVG(sitterpunkte)']) ) ) ? "<img src='bilder/star3.gif' alt='star3' style='border:0;vertical-align:middle;'>" : ""));?>
<?php
  if(!empty($row['ByUser']) && ($row['user'] != $row['ByUser'])) {
	  echo "<br>(eingestellt von " . $row['ByUser'] . ")";
	}
?>
  </td>
  <td class="windowbg<?php echo $num;?>">
   <?php echo ( empty($row['date_b2']) || empty($row['bauschleife']) || $row['date_b2'] == $row['date_b1'] ) ? "": strftime($config_sitter_timeformat, $row['date_b2']) . "<br>";?>
   <?php echo ( empty($row['date_b1']) || empty($row['bauschleife']) || $row['date_b1'] == $row['date'] ) ? "": strftime($config_sitter_timeformat, $row['date_b1']) . "<br>";?>
   <?php echo strftime($config_sitter_timeformat, $row['date']);?>
  </td>
  <td class="windowbg<?php echo $num;?>">
   <?php echo $row_planet['planetenname'];?> [<?php echo $row['planet'];?>]
  </td>
  <td class="windowbg<?php echo $num;?>">
    <?php echo convert_bbcode($row['auftrag']);?>
  </td>
  <td class="windowbg<?php echo $num;?>" align="center">
<?php

if (is_array($users_logged_in)) { 
  foreach ($users_logged_in as $user) { 
    $tmp .= $user.', ';
  } 
  $users_logged_in = substr($tmp,0,-2);
}

	if ( empty($users_logged_in) ) {
		echo "<a href='index.php?action=sitterlogins&sitterlogin=" . urlencode($row['user']) . "&sid=" . $sid . "' target='sitterbereich'>[einloggen]</a>";

		if ($row['schieben']=="1") {
			echo "<a href='javascript:Collapse(".$row['id'].");'>[schieben]</a>";
		} else {
			echo "<a href='index.php?action=sitterliste&erledigt=" . $row['id'] . "&sid=" . $sid . "' onclick='return confirmlink(this, \"Auftrag wirklich erledigt?\")'>[erledigt]</a>";
		}
	} else {
        echo $users_logged_in . " ist eingeloggt";
    }

    echo "<br><a href='javascript:Collapse(".$row['id'].");'><img src='bilder/plus.gif' alt='' border='0' id='collapse_".$row['id']."'></a>";

?>

  </td>
  <td class="windowbg<?php echo $num;?>">
   <?php echo ( empty($users_lastlogin_user) ) ? "": strftime($config_sitter_timeformat, $users_lastlogin) . " - " . $users_lastlogin_user;?>
  </td>
 </tr>
 <tr id="row_<?php echo $row['id'];?>" style="display: none;">
  <td colspan="6" class="windowbg1" valign="top" align="center" style="width: 100%;">
<form method="POST" action="index.php?action=sitterliste&sid=<?php echo $sid;?>" enctype="multipart/form-data">
<table border="0" cellpadding="4" cellspacing="0" class="bordercolor">
 <tr>
  <td colspan="2" class="windowbg1" align="center">
   <b>Kommentar hinzufügen</b>
  </td>
 </tr>
 <tr>
  <td class="windowbg1">
   Kommentar:<br>
   <i>Hier kannst du einen Kommentar<br>zu dem Auftrag hinzufügen.</i>
  </td>
  <td class="windowbg1">
   <textarea name='comment' id='comment' rows='4' cols='25' style='width: 200px;'></textarea>
   <?php echo bbcode_buttons('comment'); ?>
  </td>
 </tr>
 <tr>
  <td class="windowbg1" colspan="2"><hr class="hr" size="1"></td>
 </tr>
 <tr>
  <td colspan="2" class="windowbg1" align="center">
   <b>Zeit aktualisieren</b>
  </td>
 </tr>
<?php
	if ( $bauschleifenlaenge >= 3 )
	{
?>
 <tr>
  <td class="windowbg1">
   Zeit frühstens 2:
  </td>
  <td class="windowbg1">
   <input type="text" name="date_b2" id="date_b2_<?php echo $row['id'];?>" value="<?php echo strftime($config_sitter_timeformat, $row['date_b2']);?>" style="width: 200;">
  </td>
 </tr>
<?php
	}
?>
<?php
	if ( $bauschleifenlaenge >= 2 )
	{
?>
 <tr>
  <td class="windowbg1">
   Zeit frühstens 1:
  </td>
  <td class="windowbg1">
   <input type="text" name="date_b1" id="date_b1_<?php echo $row['id'];?>" value="<?php echo strftime($config_sitter_timeformat, $row['date_b1']);?>" style="width: 120;">
   <input type="button" name="kopieren" value="kopieren" onclick="kopiere_zeit('<?php echo $row['id'];?>');">
  </td>
 </tr>
<?php
	}
?>
 <tr>
  <td class="windowbg1">
   Zeit spätestens:<br>
   <i>Zeit, zu der alle Bauschleifenaufträge auslaufen.</i>
  </td>
  <td class="windowbg1">
   <input type="text" name="date" id="date_<?php echo $row['id'];?>" value="<?php echo strftime($config_sitter_timeformat, $row['date']);?>" style="width: 200;">
  </td>
 </tr>

 <tr>
  <td class="windowbg1">
   Zeiten + Variable:<br>
   <i>Eingestellte Zeiten um ... verschieben</i>
  </td>
  <td class="windowbg1">
   <select name="plus_stunden">
<?php
  $time_stunden=0;
  while ($time_stunden<15) {
		echo " <option value='" . $time_stunden . "'>" . $time_stunden . "</option>\n";
		$time_stunden++;
  }
?>
   </select> h 
   <select name="plus_minuten">
	<option value="0">00</option>
    <option value="5">05</option>
    <option value="15">15</option>
	<option value="30">30</option>
	<option value="45">45</option>
   </select> min
  </td>
 </tr>

 <tr>
  <td class="windowbg1">
   oder automatische Erkennung:<br>
   <i>Aktuelle Bauliste aus Icewars kopieren.</i>
  </td>
  <td class="windowbg1">
   <textarea name="date_parse" rows="4" cols="25" style="width: 200;"></textarea>
  </td>
 </tr>
<?php
	if ( $user_status == "admin" )
	{
?>
 <tr>
  <td class="windowbg1" colspan="2"><hr class="hr" size="1"></td>
 </tr>
 <tr>
  <td colspan="2" class="windowbg1" align="center">
   <b>Auftrag löschen</b>
  </td>
 </tr>
 <tr>
  <td class="windowbg1">
   Löschen bestätigen:
  </td>
  <td class="windowbg1">
   <input type="checkbox" name="del" value="1">
  </td>
 </tr>
<?php
	}
?>
 <tr>
  <td colspan="2" class="windowbg1" align="center">
   <input type="hidden" name="auftragid" value="<?php echo $row['id'];?>"><input type="hidden" name="edit" value="1"><input type="submit" value="speichern" name="B1" class="submit">
  </td>
 </tr>
</form>
</table>
  </td>
 </tr>
<?php
}
?>
</table>
<br>
<br>
<table border="0" cellpadding="4" cellspacing="1" class="bordercolor" style="width: 90%;">
 <tr>
  <td class="titlebg" colspan="4" align="center">
   <b>Sitteraufträge der nächsten <?php echo (round($config_sitterliste_timeout / 60 / 60));?> Stunden</b>
  </td>
 </tr>
 <tr>
  <td class="titlebg" style="width:20%;">
   <b>Username</b>
  </td>
  <td class="titlebg" style="width:15%;">
   <b>Zeit</b>
  </td>
  <td class="titlebg" style="width:15%;">
   <b>Planet</b>
  </td>
  <td class="titlebg" style="width:50%;">
   <b>Auftrag</b>
  </td>
 </tr>
<?php
// Auftraege durchgehen //
$sql = "SELECT * FROM " . $db_tb_sitterauftrag . " WHERE date_b2 > " . CURRENT_UNIX_TIME . " AND date_b2 < " . (CURRENT_UNIX_TIME + $config_sitterliste_timeout);
if ($user_fremdesitten == "0")
{
	$sql .= " AND (SELECT allianz FROM " . $db_tb_user . " WHERE id=" . $db_tb_sitterauftrag . ".user) = '" . $user_allianz . "'";
}
$sql .= " ORDER BY date_b2 ASC, date_b1 ASC, date ASC";
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while($row = $db->db_fetch_array($result))
{
	// Planetendaten //
	$sql = "SELECT planetenname, dgmod FROM " . $db_tb_scans .  " WHERE coords = '" . $row['planet'] . "'";
	$result_planet = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_planet = $db->db_fetch_array($result_planet);

	$sql = "SELECT peitschen FROM " . $db_tb_user .  " WHERE sitterlogin = '" . $row['user'] . "'";
	$result_user = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row_user = $db->db_fetch_array($result_user);

	$bauschleifenmod = 1;
	if ( empty($row_user['peitschen']) )
	{
		if ( $row['date_b1'] <> $row['date'] ) $bauschleifenmod = 1.1;
		if ( $row['date_b2'] <> $row['date_b1'] ) $bauschleifenmod = 1.2;
	}

	$row['auftrag'] = auftrag($row['typ'], $row['bauschleife'], $row['bauid'], $row['auftrag'], $row['schiffanz'], $row_planet['dgmod'], $row['user'], $bauschleifenmod) . "<br>";
?>
 <tr>
  <td class="windowbg1">
<?php
if ( $user_status == "admin" ) echo "<a href='index.php?action=profile&sitterlogin=" . urlencode($row['user']) . "&sid=" . $sid . "'>" . $row['user'] . "</a>";
else echo $row['user'];
if(!empty($row['ByUser']) && ($row['user'] != $row['ByUser'])) {
  echo "<br>(eingestellt von " . $row['ByUser'] . ")";
}
?>
  </td>
  <td class="windowbg1">
   <?php echo ( empty($row['date_b2']) || empty($row['bauschleife']) || $row['date_b2'] == $row['date_b1'] ) ? "": strftime($config_sitter_timeformat, $row['date_b2']) . "<br>";?><?php echo ( empty($row['date_b1']) || empty($row['bauschleife']) || $row['date_b1'] == $row['date'] ) ? "": strftime($config_sitter_timeformat, $row['date_b1']) . "<br>";?><?php echo strftime($config_sitter_timeformat, $row['date']);?>
  </td>
  <td class="windowbg1">
   <?php echo $row_planet['planetenname'];?> [<?php echo $row['planet'];?>]
  </td>
  <td class="windowbg1">
    <?php echo convert_bbcode($row['auftrag']);?>
  </td>
 </tr>
<?php
}
?>
</table>
<br>
