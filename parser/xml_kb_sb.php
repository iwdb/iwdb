<?php
/*****************************************************************************/
/* neu_xml.php																 */
/*****************************************************************************/

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

$anzahl_kb = 0;
$anzahl_kb_neu = 0;
$anzahl_sb = 0;


function kb($id, $hash){
	global $db_prefix, $db, $anzahl_kb, $anzahl_kb_neu, $ausgabe;
	
	$anzahl_kb++;

	$link = 'http://www.icewars.de/portal/kb/de/kb.php?id='.$id.'&md_hash='.$hash;

	// Uberprufen, ob KB schon in Datenbank
	$sql = "
		SELECT ID_KB
		FROM {$db_prefix}kb
		WHERE
			ID_KB = '$id'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	// Wenn keiner da weiter
	if (mysql_num_rows($result) == 0) {

		$anzahl_kb_neu++;

		$xml = simplexml_load_file($link.'&typ=xml');
		
		$kb_id = $id;
		$kb_hash = $hash;
		$kb_time = (int)$xml->timestamp['value'];
		
		$kb = array();
		
		// Allgemein
		$kb = array(
			'verteidiger' => utf8_decode((string)$xml->plani_data->user->name['value']),
			'verteidiger_ally' => utf8_decode((string)$xml->plani_data->user->allianz_tag['value']),
			'planet_name' => utf8_decode((string)$xml->plani_data->plani_name['value']),
			'koords_gal' => (int)$xml->plani_data->koordinaten->gal['value'],
			'koords_sol' => (int)$xml->plani_data->koordinaten->sol['value'],
			'koords_pla' => (int)$xml->plani_data->koordinaten->pla['value'],
			'koords_string' => utf8_decode((string)$xml->plani_data->koordinaten->string['value']),
			'typ' => (int)$xml->kampf_typ->id['value'],
			'resultat' => (int)$xml->resultat->id['value'],
		);
		// Defstellungen
		if (isset($xml->pla_def->user->defence->defencetyp)){
			$def = $xml->pla_def->user->defence->defencetyp;
			foreach($def as $value){
				$kb['def'][] = array(
					'id' => (int)$value->id['value'],
					'name' => utf8_decode((string)$value->name['value']),
					'start' => (int)$value->anzahl_start['value'],
					'ende' => (int)$value->anzahl_ende['value'],
					'verlust' => (int)$value->anzahl_verlust['value'],
				);
			}
		}

		// Verluste
			// att
		if (isset($xml->resverluste->att->resource)){
			$res = $xml->resverluste->att->resource;
			foreach($res as $value){
				$kb['verluste'][] = array(
					'id' => (int)$value->id['value'],
					'seite' => 1,
					'name' => utf8_decode((string)$value->name['value']),
					'anzahl' => (int)$value->anzahl['value'],
				);
			}
		}
			// def
		if (isset($xml->resverluste->def->resource)){
			$res = $xml->resverluste->def->resource;
			foreach($res as $value){
				$kb['verluste'][] = array(
					'id' => (int)$value->id['value'],
					'seite' => 2,
					'name' => utf8_decode((string)$value->name['value']),
					'anzahl' => (int)$value->anzahl['value'],
				);
			}
		}
		// Plunderung
		if (isset($xml->pluenderung->resource)) {
			$res = $xml->pluenderung->resource;
			foreach ($res as $value) {
				$kb['pluenderung'][] = array(
					'id' => (int)$value->id['value'],
					'name' => utf8_decode((string)$value->name['value']),
					'anzahl' => (int)$value->anzahl['value'],
				);
			}
		}
		// Bomb
		if (isset($xml->bomben->user)) {
			$xml_bomb = $xml->bomben;
			$kb['bomb']['user'] = utf8_decode((string)$xml_bomb->user->name['value']);
			// Bombertrefferchance
			if (isset($bomb->bombentrefferchance))
				$kb['bomb']['trefferchance'] = $xml_bomb->bombentrefferchance['value'];
			// Basis zerstort
			if (isset($bomb->basis_zerstoert))
				$kb['bomb']['basis'] = (int)$xml_bomb->basis_zerstoert['value'];
			// Bevolerung
			if (isset($bomb->bev_zerstoert))
				$kb['bomb']['bev'] = (int)$xml_bomb->bev_zerstoert['value'];
			// getroffene Gebaude
			if (isset($xml_bomb->geb_zerstoert->geb)) {
				$xml_geb = $xml_bomb->geb_zerstoert->geb;
				foreach ($xml_geb as $value) {
					$kb['bomb']['geb'][] = array(
						'id' => (int)$value->id['value'],
						'name' => utf8_decode((string)$value->name['value']),
						'anzahl' => (int)$value->anzahl['value'],
					);
				}
			}
		}
		// Flotten
			// Def (auf Planet)
		if (isset($xml->pla_def->user->schiffe)) {
			$user = $xml->pla_def->user;
			$flotte = array(
				'art' => 1,
				'name' => utf8_decode((string)$user->name['value']),
				'ally' => utf8_decode((string)$user->allianz_tag['value']),
			);
			if (isset($user->schiffe)) {
				$schiffe = $user->schiffe->schifftyp;
				foreach ($schiffe as $value) {
					$flotte['schiffe'][] = array(
						'id' => (int)$value->id['value'],
						'name' => utf8_decode((string)$value->name['value']),
						'klasse' => (int)$value->klasse['value'],
						'anzahl_start' => (int)$value->anzahl_start['value'],
						'anzahl_ende' => (int)$value->anzahl_ende['value'],
						'anzahl_verlust' => (int)$value->anzahl_verlust['value'],
					);
				}
			}
			$kb['flotte'][] = $flotte;
		}

			// Def (stationiert)
		if (isset($xml->flotten_def->user)) {
			$user = $xml->flotten_def->user;
			foreach ($user as $value) {
				$flotte = array(
					'art' => 2,
					'name' => utf8_decode((string)$value->name['value']),
					'ally' => utf8_decode((string)$value->allianz_tag['value']),
				);
				if (isset($value->schiffe)) {
					$schiffe = $value->schiffe->schifftyp;
					foreach ($schiffe as $value) {
						$flotte['schiffe'][] = array(
							'id' => (int)$value->id['value'],
							'name' => utf8_decode((string)$value->name['value']),
							'klasse' => (int)$value->klasse['value'],
							'anzahl_start' => (int)$value->anzahl_start['value'],
							'anzahl_ende' => (int)$value->anzahl_ende['value'],
							'anzahl_verlust' => (int)$value->anzahl_verlust['value'],
						);
					}
				}
			}
			$kb['flotte'][] = $flotte;
		}
		//	Att
		if (isset($xml->flotten_att->user)) {
			$user = $xml->flotten_att->user;
			foreach ($user as $value) {
				$flotte = array(
					'art' => 3,
					'name' => utf8_decode((string)$value->name['value']),
					'ally' => utf8_decode((string)$value->allianz_tag['value']),
					'planet_name' => (string)$value->startplanet->plani_name['value'],
					'koords_string' => utf8_decode((string)$value->startplanet->koordinaten->string['value']),
				);
				if (isset($value->schiffe)) {
					$schiffe = $value->schiffe->schifftyp;
					foreach ($schiffe as $value) {
						$flotte['schiffe'][] = array(
							'id' => (int)$value->id['value'],
							'name' => utf8_decode((string)$value->name['value']),
							'klasse' => (int)$value->klasse['value'],
							'anzahl_start' => (int)$value->anzahl_start['value'],
							'anzahl_ende' => (int)$value->anzahl_ende['value'],
							'anzahl_verlust' => (int)$value->anzahl_verlust['value'],
						);
					}
				}
			}
			$kb['flotte'][] = $flotte;
		}


		// Eintrag
		$sql = "
			INSERT INTO {$db_prefix}kb
				(ID_KB, hash, time, verteidiger, verteidiger_ally, planet_name, koords_gal, koords_sol, koords_pla, typ, resultat)
			VALUES
				('$kb_id', '$kb_hash', '$kb_time', '$kb[verteidiger]', '$kb[verteidiger_ally]', '$kb[planet_name]', '$kb[koords_gal]', 
				'$kb[koords_sol]', '$kb[koords_pla]', '$kb[typ]', '$kb[resultat]')";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			// Def
		if (isset($kb['def'])) {
			$sql = "
				INSERT INTO {$db_prefix}kb_def
					(ID_KB, ID_IW_DEF, anz_start, anz_verlust)
				VALUES";
			foreach ($kb['def'] as $key => $value) {
				if ($key == 0)
					$sql .= "
					('$kb_id', '$value[id]', '$value[start]', '$value[verlust]')";
				else
					$sql .= ",
					('$kb_id', '$value[id]', '$value[start]', '$value[verlust]')";
			}
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}
			// Verluste
		if (isset($kb['verluste'])) {
			$sql = "
				INSERT INTO {$db_prefix}kb_verluste
					(ID_KB, ID_IW_RESS, seite, anzahl)
				VALUES";
			foreach ($kb['verluste'] as $key => $value) {
				if ($key == 0)
					$sql .= "
					('$kb_id', '$value[id]', '$value[seite]', '$value[anzahl]')";
				else
					$sql .= ",
					('$kb_id', '$value[id]', '$value[seite]', '$value[anzahl]')";
			}
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			}
			// Plunderung
		if (isset($kb['pluenderung'])) {
			$sql = "
				INSERT INTO {$db_prefix}kb_pluenderung
					(ID_KB, ID_IW_RESS, anzahl)
				VALUES";
			foreach ($kb['pluenderung'] as $key => $value) {
				if ($key == 0)
					$sql .= "
					('$kb_id', '$value[id]', '$value[anzahl]')";
				else
					$sql .= ",
					('$kb_id', '$value[id]', '$value[anzahl]')";
			}
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}
			// Bomb
		if (isset($kb['bomb'])) {
			$sql = "
				INSERT INTO {$db_prefix}kb_bomb
					(ID_KB, time";
			$values = "
				VALUES
					('$kb_id', '$kb_time'";
			foreach ($kb['bomb'] as $key => $value) {
				if ($key != 'geb') {
					$sql .= ", $key";
					$values .= ", '$value'";
				}
			}
			$sql .= ") $values )";
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				// Gebaude
			$sql = "
				INSERT INTO {$db_prefix}kb_bomb_geb
					(ID_KB, ID_IW_GEB, anzahl)
				VALUES";
			foreach ($kb['bomb']['geb'] as $key => $value) {
				if ($key == 0)
					$sql .= "
						('$kb_id', '$value[id]', '$value[anzahl]')";
				else
					$sql .= ",
						('$kb_id', '$value[id]', '$value[anzahl]')";
			}
			$result = $db->db_query($sql)
				or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		}
		// Eintrag Flotte
		if (isset($kb['flotte'])) {
			$sql = "
				INSERT INTO {$db_prefix}kb_flotten
					(ID_KB, time, art, name, ally)
				VALUES";
			foreach ($kb['flotte'] as $key => $value) {
				if ($value['art'] == 3)
					$sql = "
						INSERT INTO {$db_prefix}kb_flotten
							(ID_KB, time, art, name, ally, planet_name, koords_string)
						VALUES
							('$kb_id', '$kb_time', '$value[art]', '$value[name]', '$value[ally]', '$value[planet_name]', '$value[koords_string]')";
				else
					$sql = "
						INSERT INTO {$db_prefix}kb_flotten
							(ID_KB, time, art, name, ally)
						VALUES
							('$kb_id', '$kb_time', '$value[art]', '$value[name]', '$value[ally]')";
				$result = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
				$ID_FLOTTE = @mysql_insert_id();
				$sql = "
					INSERT INTO {$db_prefix}kb_flotten_schiffe
						(ID_FLOTTE, ID_IW_SCHIFF, anz_start, anz_verlust)
					VALUES";
				foreach ($value['schiffe'] as $key2 => $value2) {
					if ($key2 == 0)
						$sql .= "
						('$ID_FLOTTE', '$value2[id]', '$value2[anzahl_start]', '$value2[anzahl_verlust]')";
					else
						$sql .= ",
						('$ID_FLOTTE', '$value2[id]', '$value2[anzahl_start]', '$value2[anzahl_verlust]')";
				}
				$result = $db->db_query($sql)
					or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			}
		}

		// noch BBCode holen
		if ( !empty($link) ) {
			if ($handle = @fopen($link.'&typ=bbcode', "r")) {
				$bbcode	= '';
				while (!@feof($handle))
					$bbcode .= @fread($handle, 512);
				@fclose($handle);
			}
		}
		
		$suchen = '#(\[tr\]\[td\])((?:kleine|mittlere|grose|DN)(?: planetare| orbitale)? Werft)(\[/td\]\[td colspan=3\])([\d]+)(\[/td\]\[/tr\])#'; 
		$ersetzen = '$1[color=red]$2[/color]$3[color=red]$4[/color]$5';
		$bbcode = preg_replace($suchen, $ersetzen, $bbcode);
	
		$suchen = array('[td colspan=4]');
		$ersetzen = array('[td]');
		$bbcode = str_replace($suchen, $ersetzen, $bbcode);
	
		$suchen = array('[td colspan=3]');
		$ersetzen = array('[td]');
		$bbcode = str_replace($suchen, $ersetzen, $bbcode);
		
		$ausgabe['KBs'][] = array(
			'Zeit' => $kb_time,
			'Bericht' => $bbcode,
			'Link' => $link,
			);
		
	}
	else {		// nur BBCode holen
		if ( !empty($link) ) {
			if ($handle = @fopen($link.'&typ=bbcode', "r")) {
				$bbcode	= '';
				while (!@feof($handle))
					$bbcode .= @fread($handle, 512);
				@fclose($handle);
			}
		}
		
		$suchen = '#(\[tr\]\[td\])((?:kleine|mittlere|grose|DN)(?: planetare| orbitale)? Werft)(\[/td\]\[td colspan=3\])([\d]+)(\[/td\]\[/tr\])#'; 
		$ersetzen = '$1[color=red]$2[/color]$3[color=red]$4[/color]$5';
		$bbcode = preg_replace($suchen, $ersetzen, $bbcode);
	
		$suchen = array('[td colspan=4]');
		$ersetzen = array('[td]');
		$bbcode = str_replace($suchen, $ersetzen, $bbcode);
	
		$suchen = array('[td colspan=3]');
		$ersetzen = array('[td]');
		$bbcode = str_replace($suchen, $ersetzen, $bbcode);

		$xml = simplexml_load_file($link.'&typ=xml');
		
		
		$ausgabe['KBs'][] = array(
			'Zeit' => (int)$xml->timestamp['value'],
			'Bericht' => $bbcode,
			'Link' => $link,
			);
	}	
//	echo '<div class="system_debug_blue"><pre>'.print_r($kb, true).'</pre></div>'; //Testausgabe
}

function sb($id, $hash){
	global $db_prefix, $db, $anzahl_sb;
	
	$anzahl_sb++;
}
?>