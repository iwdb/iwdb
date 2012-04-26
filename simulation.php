<?php
define("IRA", TRUE);
define("DEBUG_LEVEL", 0);

include_once("./includes/iwdb.php");

// Seitenparameter ermitteln
debug_var("gal", $gal = getVar("gal"));
debug_var("sol", $sol = getVar("sol"));
debug_var("pla", $pla = getVar("pla"));

// Defence lesen
debug_var("sql", $sql = "SELECT * FROM $db_tb_def");
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	$def[$row['name']] = array(
		"id" => $row['id'],
		"abk" => $row['abk']);
}
debug_var("def", $def);

// Schiffe lesen
debug_var("sql", $sql = "SELECT * FROM $db_tb_schiffstyp");
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
	$schiff[$row['schiff']] = array(
		"id" => $row['id_iw'],
		"abk" => $row['abk']);
}
debug_var("schiff", $schiff);

// Ziel lesen
debug_var("sql", $sql = "
	SELECT $db_tb_scans.`def`,
		$db_tb_scans.`plan`,
		$db_tb_scans.`stat`
	FROM $db_tb_scans
	WHERE $db_tb_scans.`coords_gal`=" . $gal . "
       AND $db_tb_scans.`coords_sys`=" . $sol . "
	AND $db_tb_scans.`coords_planet`=" . $pla);
$result = $db->db_query($sql)
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
if ($row = $db->db_fetch_array($result)) {
	debug_var("row", $row);
	// Verteidigungs-Objekte parsen
	$sd = array();
	$lines = explode("\n", $row['def']);
	$lines = array_merge($lines, explode("\n", $row['plan']));
	$lines = array_merge($lines, explode("\n", $row['stat']));
	$current = 'block';
	$objects = array();
	foreach ($lines as $line) {
		if ($current == 'object')
			$objekt = $line;
		elseif ($current == 'value')
			$objects[$objekt] = $line;
		if (strpos($line, 'scan_object'))
			$current = 'object';
		elseif (strpos($line, 'scan_value'))
			$current = 'value';
		else
			$current = 'block';	
	}
	debug_var("objects", $objects);

	$url = "http://sandkasten.icewars.de/game/index.php?action=simulator";
	foreach ($objects as $key => $value) {
		if (isset($def[$key]))
			$url .= "&simu_def[" . $def[$key]['id'] . "]=" . $value;
		elseif (isset($schiff[$key]) && !empty($schiff[$key]['id']))
			$url .= "&simu_fl2[" . $schiff[$key]['id'] . "]=" . $value;
	}
	debug_var("url", $url);

	// Ziel speichern
	debug_var("sql", $sql = "
		INSERT INTO $db_tb_sim_user (`user`,`coords_gal`,`coords_sys`,`coords_planet`) VALUES (" .
		"'" . $user_sitterlogin . "'," .
		$gal . "," .
		$sol . "," .
		$pla .
		") ON DUPLICATE KEY UPDATE " .
		"`coords_gal`=" . $gal . "," .
		"`coords_sys`=" . $sol . "," .
		"`coords_planet`=" . $pla);
	$db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	header("Location: " . $url);
}
?>