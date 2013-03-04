<?php

define('APPLICATION_PATH_ABSOLUTE', dirname(__FILE__));
define('APPLICATION_PATH_RELATIVE', dirname($_SERVER['SCRIPT_NAME']));
define('APPLICATION_PATH_URL', dirname($_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']));

require_once("includes/bootstrap.php");
error_reporting(E_ALL & ~E_NOTICE);

if (($login_ok === false) || empty($user_sitterlogin) || !($user_adminsitten == SITTEN_BOTH || $user_adminsitten == SITTEN_ONLY_LOGINS)) {
    header("Location: " . APPLICATION_PATH_RELATIVE);
    exit;
}

if (!defined('DEBUG_LEVEL')) {
    define('DEBUG_LEVEL', 0);
}

// Get request parameter
$params = array();
$params['mode'] = getVar('mode');
$params['action'] = getVar('action');
$params['name'] = getVar('name');
$params['galaxy'] = getVar('galaxy');
$params['system'] = getVar('system');
$params['planet'] = getVar('planet');
$params['view'] = getVar('view');
$params['next'] = getVar('next');
$params['next_galaxy'] = getVar('next_galaxy');
$params['next_system'] = getVar('next_system');
$params['next_planet'] = getVar('next_planet');
$params['prev'] = getVar('prev');
$params['prev_galaxy'] = getVar('prev_galaxy');
$params['prev_system'] = getVar('prev_system');
$params['prev_planet'] = getVar('prev_planet');
$params['autocalc'] = getVar('autocalc');
debug_var('params', $params);

// Validate request parameter
if (empty($params['mode'])) {
    $params['mode'] = 'index';
}

if (empty($params['autocalc'])) {
    if (empty($params['next']) && empty($params['prev'])) {
        $params['autocalc'] = 'eisen,stahl,chemie,vv4a,eis,wasser,energie';
    } else {
        foreach (array('eisen', 'stahl', 'chemie', 'vv4a', 'eis', 'wasser', 'energie') as $ress) {
            if (!empty($_REQUEST[$ress])) {
                $params['autocalc'] .= ',' . $ress;
            }
        }
    }
}

// Execute actions
if (!empty($params['next'])) {
    $params['galaxy'] = getVar('next_galaxy');
    $params['system'] = getVar('next_system');
    $params['planet'] = getVar('next_planet');
}
if (!empty($params['prev'])) {
    $params['galaxy'] = getVar('prev_galaxy');
    $params['system'] = getVar('prev_system');
    $params['planet'] = getVar('prev_planet');
}

// Initialize data cache
$data = array(
    'targets_position' => 0,
    'targets_count'    => 0,
);

// Retrieve defence
debug_var("sql", $sql = "SELECT `name`, `id`, `abk`, `id_iw` FROM `{$db_tb_def}`;");
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $data['def'][$row['name']] = array(
        "id"    => $row['id'],
        "abk"   => $row['abk'],
        "id_iw" => $row['id_iw'],
    );
}

// Retrieve ships
debug_var("sql", $sql = "SELECT `schiff`, `id_iw`, `abk` FROM `{$db_tb_schiffstyp}`;");
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $data['ship'][$row['schiff']] = array(
        "id"    => $row['id_iw'],
        "abk"   => $row['abk'],
        "id_iw" => $row['id_iw'],
    );
}

// Retrieve target list
$data['targets'] = array();

$sql = 'SELECT `coords_gal`, `coords_sys`, `coords_planet`' .
    ' FROM ' . $db_tb_target .
    ' WHERE `user`="' . $user_sitterlogin . '"' .
    ' AND `name`="' . $params['name'] . '"' .
    ' ORDER BY `coords_gal`, `coords_sys`, `coords_planet`;';
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    if (empty($params['galaxy'])) {
        $params['galaxy'] = $row['coords_gal'];
        $params['system'] = $row['coords_sys'];
    }
    if (empty($params['planet'])) {
        $params['planet'] = $row['coords_planet'];
    } elseif ($params['galaxy'] > $row['coords_gal'] || ($params['galaxy'] == $row['coords_gal'] && $params['system'] > $row['coords_sys'])) {
        $data['targets_prev_gal'] = $row['coords_gal'];
        $data['targets_prev_sys'] = $row['coords_sys'];
        $data['targets_position']++;
    } elseif ($params['galaxy'] == $row['coords_gal'] && $params['system'] == $row['coords_sys']) {
        $data['targets_count']++;
    } elseif ($params['galaxy'] < $row['coords_gal'] || ($params['galaxy'] == $row['coords_gal'] && $params['system'] < $row['coords_sys'])) {
        if (empty($data['targets_next_gal'])) {
            $data['targets_next_gal'] = $row['coords_gal'];
            $data['targets_next_sys'] = $row['coords_sys'];
        }
    }
    if ($params['galaxy'] == $row['coords_gal'] && $params['system'] == $row['coords_sys'] && $params['planet'] == $row['coords_planet']) {
        $data['target'] = $row;
    } elseif (!isset($data['target'])) {
        $data['target_prev_gal']    = $row['coords_gal'];
        $data['target_prev_sys']    = $row['coords_sys'];
        $data['target_prev_planet'] = $row['coords_planet'];
    } elseif (isset($data['target']) && !isset($data['target_next_gal'])) {
        $data['target_next_gal']    = $row['coords_gal'];
        $data['target_next_sys']    = $row['coords_sys'];
        $data['target_next_planet'] = $row['coords_planet'];
    }
    $data['targets'][$row['coords_gal'] . ':' . $row['coords_sys'] . ':' . $row['coords_planet']] = $row;
}

if (empty($data['targets_prev_gal'])) {
    $data['targets_prev_gal'] = $params['galaxy'];
    $data['targets_prev_sys'] = $params['system'] - 1;
}

if (empty($data['targets_next_gal'])) {
    $data['targets_next_gal'] = $params['galaxy'];
    $data['targets_next_sys'] = $params['system'] + 1;
}

// Create url for links
$data['url'] = array(
    'main'       => ($params['view'] == 'fleet_send'
        ? 'http://sandkasten.icewars.de/game/index.php?action=flotten_send&gal=' . $params['galaxy'] . "&sol=" . $params['system'] . '&pla=' . $params['planet']
        : 'http://sandkasten.icewars.de/game/index.php?action=universum&gal=' . $params['galaxy'] . "&sol=" . $params['system']),
    'top'        => $_SERVER["SCRIPT_NAME"] . '?name=' . $params['name'] . '&mode=top&view=' . $params['view'] . '&galaxy=' . $params['galaxy'] . '&system=' . $params['system'] . '&planet=' . $params['planet'] . '&autocalc=' . $params['autocalc'],
    'right'      => $_SERVER["SCRIPT_NAME"] . '?name=' . $params['name'] . '&mode=right&view=' . $params['view'] . '&galaxy=' . $params['galaxy'] . '&system=' . $params['system'],
    'prev'       => $_SERVER["SCRIPT_NAME"] . '?name=' . $params['name'] . '&action=prev&view=' . $params['view'] . '&galaxy=' . $data['targets_prev_gal'] . '&system=' . $data['targets_prev_sys'],
    'next'       => $_SERVER["SCRIPT_NAME"] . '?name=' . $params['name'] . '&action=next&view=' . $params['view'] . '&galaxy=' . $data['targets_next_gal'] . '&system=' . $data['targets_next_sys'],
    'prevtarget' => $_SERVER["SCRIPT_NAME"] . '?name=' . $params['name'] . '&view=fleet_send&galaxy=' . $data['target_prev_gal'] . '&system=' . $data['target_prev_sys'] . '&planet=' . $data['target_prev_planet'],
    'nexttarget' => $_SERVER["SCRIPT_NAME"] . '?name=' . $params['name'] . '&view=fleet_send&galaxy=' . $data['target_next_gal'] . '&system=' . $data['target_next_sys'] . '&planet=' . $data['target_next_planet'],
    'uniview'    => $_SERVER["SCRIPT_NAME"] . '?name=' . $params['name'] . '&view=universum&galaxy=' . $params['galaxy'] . '&system=' . $params['system'] . '&planet=' . $params['planet'],
    'universum'  => 'http://sandkasten.icewars.de/game/index.php?action=universum&gal=' . $params['galaxy'] . "&sol=" . $params['system'],
);

// Create data for info fields
$data['info'] = array(
    'terminus' => array('caption' => 'T', 'title' => 'min. Terminus Sonde'),
    'x13'      => array('caption' => 'X13', 'title' => 'min. X13 Sonde'),
    'sd01'     => array('caption' => 'SD01', 'title' => 'SD01 Gatling'),
    'sd02'     => array('caption' => 'SD02', 'title' => 'SD02 Pulslaser'),
    'grav'     => array('caption' => 'Grav', 'title' => 'SDI Gravitonbeam'),
    'plasma'   => array('caption' => 'Plasma', 'title' => 'SDI Plasmalaser'),
    'pulssat'  => array('caption' => 'Puls', 'title' => 'PulslaserSat'),
    'arak'     => array('caption' => 'Arak', 'title' => 'SDI Atomraketen'),
    'rak'      => array('caption' => 'Rak', 'title' => 'SDI Raketensystem'),
    'lasersat' => array('caption' => 'LaS', 'title' => 'LaserSat'),
    'gauss'    => array('caption' => 'Gauss', 'title' => 'Gausskanonensatellit'),
    'raksat'   => array('caption' => 'RakS', 'title' => 'Raketensatellit'),
    'stopfi'   => array('caption' => 'Stopfi', 'title' => 'Stopfentenwerfer'),
    'ship'     => array('caption' => 'Sh'),
);

if ($params['view'] == 'fleet_send') {
    $data['info'] = array_merge(
        $data['info'], array(
            'Systrans (Systemtransporter Klasse 1)'         => array('caption' => 'Systrans', 'title' => 'Systrans (Systemtransporter Klasse 1)'),
            'Lurch (Systemtransporter Klasse 1)'            => array('caption' => 'Lurch', 'title' => 'Lurch (Systemtransporter Klasse 1)'),
            'Gorgol 9 (Hyperraumtransporter Klasse 1)'      => array('caption' => 'Gorgol', 'title' => 'Gorgol 9 (Hyperraumtransporter Klasse 1)'),
            'Eisb&auml;r (Hyperraumtransporter Klasse 2)'   => array('caption' => 'Eisb&auml;r', 'title' => 'Eisbär (Hyperraumtransporter Klasse 2)'),
            'Kamel Z-98 (Hyperraumtransporter Klasse 1)'    => array('caption' => 'Kamel', 'title' => 'Kamel Z-98 (Hyperraumtransporter Klasse 1)'),
            'Waschb&auml;r (Hyperraumtransporter Klasse 2)' => array('caption' => 'Waschb&auml;r', 'title' => 'Waschb&auml;r (Hyperraumtransporter Klasse 2)'),
        )
    );
}

// Create categories for info field groupings
if ($params['view'] == 'fleet_send') {
    $data['cat'] = array(
        'def'   => array('grav', 'plasma', 'pulssat', 'stopfi', 'lasersat', 'gauss', 'arak', 'raksat', 'rak'),
        'trans' => array('Kamel Z-98 (Hyperraumtransporter Klasse 1)', 'Gorgol 9 (Hyperraumtransporter Klasse 1)', 'Systrans (Systemtransporter Klasse 1)', 'Waschb&auml;r (Hyperraumtransporter Klasse 2)', 'Eisb&auml;r (Hyperraumtransporter Klasse 2)', 'Lurch (Systemtransporter Klasse 1)'),
        'ship1' => array('Sirius X300', 'Manta', 'Shark', 'Sheep', 'Nepomuk', 'Nova', 'Stormbringer', 'Atombomber'),
        'ship2' => array('Widowmaker', 'Crawler', 'Vendeta', 'Slayer', 'Eraser', 'Gatling', 'Victim', 'Hunter', 'Lionheart'),
        'ship3' => array('Zeus', 'Kronk', 'Big Daddy', 'Silent Sorrow', 'Succubus', 'Hitman'),
        'sd'    => array('sd01', 'sd02', 'x11', 'terminus', 'x13'),
    );
}

// Create data for ship attack calculation
$data['shipattack'] = array(
    // Jäger
    'Sheep'                          => 10,
    'Shark'                          => 12,
    'Manta'                          => 15,
    'Downbringer'                    => 25,
    'Sirius X300'                    => 45,
    'Nightcrawler'                   => 35,
    // Bomber
    'Atombomber'                     => 10,
    'Stormbringer'                   => 15,
    'Nova'                           => 20,
    'Nepomuk'                        => 5,
    // Korvette
    'Lionheart'                      => 20,
    'Hunter'                         => 30,
    'Victim'                         => 45,
    'Gatling'                        => 65,
    'Eraser'                         => 25,
    // Zerstörer
    'Slayer'                         => 35,
    'Vendeta'                        => 55,
    'Crawler'                        => 85,
    'Widowmaker'                     => 45,
    // Kreuzer
    'Hitman'                         => 120,
    'Succubus'                       => 360,
    'Sirius XPi'                     => 450,
    'TAG Vario Kreuzer'              => 420,
    'Silent Sorrow'                  => 130,
    // Schlachtschiff
    'Big Daddy'                      => 600,
    'Kronk'                          => 1000,
    'Quasal'                         => 1450,
    // Dreadnoughts
    'Rentier Kampftransporter'       => 3000,
    'Zeus'                           => 2580,
    'Tempest'                        => 3800,
    'Rosa-Plüschhasen-Spezialschiff' => 250,
    'Nimbus BP-1729'                 => 360,
);

// Create data for transport capacity calculation
$data['shiptrans1'] = array(
    'Systrans (Systemtransporter Klasse 1)'      => 5000,
    'Gorgol 9 (Hyperraumtransporter Klasse 1)'   => 20000,
    'Kamel Z-98 (Hyperraumtransporter Klasse 1)' => 75000,
);
$data['shiptrans2'] = array(
    'Lurch (Systemtransporter Klasse 1)'            => 2000,
    'Eisb&auml;r (Hyperraumtransporter Klasse 2)'   => 10000,
    'Waschb&auml;r (Hyperraumtransporter Klasse 2)' => 50000,
);

// Retrieve alliance status
$data['alliancestatus'] = array();
$sql = 'SELECT `allianz`, `status`' .
    ' FROM ' . $db_tb_allianzstatus .
    ' WHERE name="' . $user_allianz . "'";
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $data['alliancestatus'][$row['allianz']] = $row['status'];
}

// Retrieve planets of the current system
$data['planets'] = array();
$sql = 'SELECT *' .
    ',(SELECT date FROM ' . $db_tb_raidview . ' WHERE ' . $db_tb_raidview . '.coords=' . $db_tb_scans . '.coords ORDER BY date DESC LIMIT 1) AS raid_time' .
    ',(SELECT link FROM ' . $db_tb_raidview . ' WHERE ' . $db_tb_raidview . '.coords=' . $db_tb_scans . '.coords ORDER BY date DESC LIMIT 1) AS raid_link' .
    ' FROM ' . $db_tb_scans .
    ' WHERE coords_gal=' . $params['galaxy'] . ' AND coords_sys=' . $params['system'] .
    ' ORDER BY coords_planet';
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $info = array();
    // Alliance status
    $info['allianzstatus'] = isset($data['alliancestatus'][$row['allianz']]) ? $data['alliancestatus'][$row['allianz']] : '';
    // Calculate ressource sum
    $info['ress'] = $row['eisen'] + $row['stahl'] * 2 + $row['chemie'] * 3 + $row['vv4a'] * 4 + $row['eis'] * 2 + $row['wasser'] * 2 + $row['energie'] * 0.1;
    // Parse defence objects
    $lines   = explode("\n", $row['def']);
    $lines   = array_merge($lines, explode("\n", $row['plan']));
    $lines   = array_merge($lines, explode("\n", $row['stat']));
    $lines   = array_merge($lines, explode("\n", $row['geb']));
    $mode    = 'block';
    $objects = array();
    foreach ($lines as $line) {
        if ($mode == 'object') {
            $objekt = $line;
        } elseif ($mode == 'value') {
            $objects[$objekt] = $line;
        }
        if (strpos($line, 'scan_object')) {
            $mode = 'object';
        } elseif (strpos($line, 'scan_value')) {
            $mode = 'value';
        } else {
            $mode = 'block';
        }
    }
    $info['sd01'] = isset($objects['SD01 Gatling']) ? $objects['SD01 Gatling'] : 0;
    $info['sd02'] = isset($objects['SD02 Pulslaser']) ? $objects['SD02 Pulslaser'] : 0;
    // Scan failures
    if (hasFailScan($row) && hasOwner($row)) {
        if (!empty($row['terminus'])) {
            $info['terminus'] = '<span style="font-weight:700; color:#ff0000" title="Fehlscan mit ' . $row['terminus'] . ' Terminus Sonde">&gt;' . $row['terminus'] . '</span>';
        } elseif (!empty($row['x13'])) {
            $info['x13'] = '<span style="font-weight:700; color:#ff0000" title="Fehlscan mit ' . $row['x13'] . ' X13 Sonde">&gt;' . $row['x13'] . '</span>';
        }
    } elseif (hasShipScan($row) && hasOwner($row)) {
        $terminus         = ceil(($info['sd01'] / 1.2 + $info['sd02'] * 2.5 / 1.2 + 10));
        $x13              = ceil(($info['sd01'] / 2 + $info['sd02'] * 2.5 / 2 + 8));
        $info['terminus'] = '<span style="font-weight:700; color:#00ff00" title="min. ' . $terminus . ' Terminus Sonde">' . $terminus . '</span>';
        $info['x13']      = '<span style="font-weight:700; color:#00ff00" title="min. ' . $x13 . ' X13 Sonde">' . $x13 . '</span>';
    }
    $info['grav']     = isset($objects['SDI Gravitonbeam']) ? $objects['SDI Gravitonbeam'] : 0;
    $info['plasma']   = isset($objects['SDI Plasmalaser']) ? $objects['SDI Plasmalaser'] : 0;
    $info['arak']     = isset($objects['SDI Atomraketen']) ? $objects['SDI Atomraketen'] : 0;
    $info['rak']      = isset($objects['SDI Raketensystem']) ? $objects['SDI Raketensystem'] : 0;
    $info['pulssat']  = isset($objects['PulslaserSat']) ? $objects['PulslaserSat'] : 0;
    $info['lasersat'] = isset($objects['LaserSat']) ? $objects['LaserSat'] : 0;
    $info['gauss']    = isset($objects['Gausskanonensatellit']) ? $objects['Gausskanonensatellit'] : 0;
    $info['raksat']   = isset($objects['Raketensatellit']) ? $objects['Raketensatellit'] : 0;
    $info['stopfi']   = isset($objects['Stopfentenwerfer']) ? $objects['Stopfentenwerfer'] : 0;
    $info['def']      = $info['grav'] * 480 + $info['plasma'] * 300 + $info['arak'] * 15 + $info['rak'] * 10 + $info['pulssat'] * 55 + $info['lasersat'] * 35 + $info['gauss'] * 25 + $info['raksat'] * 25 + $info['stopfi'] * 1;
    // Parse ship objects
    $shipattack = 0;
    $ships      = array();
    foreach ($data['shipattack'] as $key => $attack) {
        if (isset($objects[$key])) {
            $shipattack += $objects[$key] * $attack;
            $ships[$key] = $objects[$key] . ' ' . $key;
            $info[$key]  = $objects[$key];
        }
    }
    foreach ($data['shiptrans1'] as $key => $capacity) {
        if (isset($objects[$key])) {
            $info[$key] = $objects[$key];
        }
    }
    foreach ($data['shiptrans2'] as $key => $capacity) {
        if (isset($objects[$key])) {
            $info[$key] = $objects[$key];
        }
    }
    if ($shipattack) {
        $info['ship'] = array(
            'title' => implode($ships, ", "),
            'value' => $shipattack,
        );
    }
    // Link to simulator
    $info['simulator'] = "http://sandkasten.icewars.de/game/index.php?action=simulator";
    foreach ($objects as $key => $value) {
        if (isset($data['def'][$key])) {
            $info['simulator'] .= "&simu_def[" . $data['def'][$key]['id_iw'] . "]=" . $value;
        } elseif (isset($data['ship'][$key])) {
            $info['simulator'] .= "&simu_fl2[" . $data['ship'][$key]['id_iw'] . "]=" . $value;
        }
    }
    // Link to fleet send view
    $info['fleet_send'] = $_SERVER["SCRIPT_NAME"] . '?name=' . $params['name'] . '&view=fleet_send&galaxy=' . $row['coords_gal'] . '&system=' . $row['coords_sys'] . '&planet=' . $row['coords_planet'];
    // Calculate total defence
    $info['total'] = $shipattack + $info['def'];
    // Calculate rating
    if (!empty($info['total'])) {
        $info['rating'] = round($info['ress'] / $info['total']);
    } else {
        $info['rating'] = $info['ress'];
    }
    // Merge data
    $planet         = array_merge($row, $info);
    $planet['chem'] = $row['chemie'];
    // Add to cache
    $data['planets'][$row['coords_gal'] . ':' . $row['coords_sys'] . ':' . $row['coords_planet']] = $planet;
}

// Retrieve stocks
$sql = 'SELECT *' .
    ' FROM ' . $db_tb_lager .
    ' WHERE coords_gal=' . $params['galaxy'] . ' AND coords_sys=' . $params['system'];
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $data['planets'][$row['coords_gal'] . ':' . $row['coords_sys'] . ':' . $row['coords_planet']]['stock'] = $row;
}

// Retrieve transports
$sql = 'SELECT *' .
    ' FROM ' . $db_tb_lieferung .
    ' WHERE coords_to_gal=' . $params['galaxy'] . ' AND coords_to_sys=' . $params['system'] .
    '   AND time>' . time() .
    ' ORDER BY coords_to_gal,coords_to_sys,coords_to_planet,time';
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while ($row = $db->db_fetch_array($result)) {
    $caption = strftime("%d.%m.%Y %H:%M", $row['time']);
    $caption .= ' ';
    $caption .= $row['art'];
    $caption .= ' ';
    $caption .= $row['coords_from_gal'] . ':' . $row['coords_from_sys'] . ':' . $row['coords_from_planet'];
    $caption .= ' ';
    $caption .= $row['user_from'];
    $amounts = array();
    foreach (array('eisen', 'stahl', 'chem', 'vv4a', 'eis', 'wasser', 'energie') as $ress) {
        $amount = $row[$ress];
        if ($amount) {
            $amounts[] = formatAmount($amount) . ' ' . $ress;
        }
    }
    $caption .= ' ';
    $caption .= implode(' ', $amounts);
    $info                                                                                                                  = array(
        'caption' => $caption,
    );
    $data['planets'][$row['coords_to_gal'] . ':' . $row['coords_to_sys'] . ':' . $row['coords_to_planet']]['transports'][] = array_merge($info, $row);
}

// Calculate supplies
foreach ($data['planets'] as $key => $planet) {
    if (isset($planet['stock'])) {
        foreach (array('eisen', 'stahl', 'chem', 'vv4a', 'eis', 'wasser', 'energie') as $ress) {
            $data['planets'][$key][$ress] = 0;
            $supply                       = $planet['stock'][$ress];
            $supply -= $planet['stock'][$ress . '_soll'];
            if (isset($planet['transports'])) {
                foreach ($planet['transports'] as $transport) {
                    $supply += $transport[$ress == 'chemie' ? 'chem' : $ress];
                }
            }
            if ($supply < 0) {
                $data['planets'][$key][$ress] = $supply;
            }
        }
    }
}

// Set selected planet
if (!empty($params['planet'])) {
    $data['planet'] = $data['planets'][$params['galaxy'] . ':' . $params['system'] . ':' . $params['planet']];
}

// Output pages
switch ($params['mode']) {
    case 'index':
        if ($params['view'] == 'universum') {
            ?>

            <!DOCTYPE html>
            <html lang="de">
            <head>
                <meta charset="utf-8">
                <title>Universum <?php echo $params['galaxy'] . ':' . $params['system'] ?></title>
            </head>
            <frameset rows="*" cols="*,500" framespacing="0" frameborder="YES" border="0" scrolling="YES">
                <frame src="<?php echo $data['url']['main'] ?>" name="main" id="main">
                <frame src="<?php echo $data['url']['right'] ?>" name="right" id="right" scrolling="YES">
            </frameset>
            <noframes>
                <body>
                </body>
            </noframes>
            </html>

        <?php } elseif ($params['view'] == 'fleet_send') { ?>

            <!DOCTYPE html>
            <html lang="de">
            <head>
                <meta charset="utf-8">
                <title>Flotte versenden</title>
            </head>
            <frameset rows="130,*" cols="*" framespacing="0" frameborder="NO" border="0">
                <frame src="<?php echo $data['url']['top'] ?>" name="top" id="top" scrolling="NO">
                <frame src="<?php echo $data['url']['main'] ?>" name="main" id="main" scrolling="YES">
            </frameset>
            <noframes>
                <body>
                </body>
            </noframes>
            </html>

        <?php
        }
        break;
    case 'top':
        ?>
        <html>
        <head>
            <link href="css/game.css" rel="stylesheet" type="text/css">
            <script>
                function transCalcOnLoad() {
                    <?php	if (!empty($data['planet'])) {
                            $planet = $data['planet']; 		if (strpos($params['autocalc'], 'eisen') !== false) { ?>
                    transCalcSetRess('transCalcEisen', '<?php echo abs($planet['eisen']) ?>');
                    <?php		} 		if (strpos($params['autocalc'], 'stahl') !== false) { ?>
                    transCalcSetRess('transCalcStahl', '<?php echo abs($planet['stahl']) ?>');
                    <?php		} 		if (strpos($params['autocalc'], 'chemie') !== false) { ?>
                    transCalcSetRess('transCalcChemie', '<?php echo abs($planet['chem']) ?>');
                    <?php		} 		if (strpos($params['autocalc'], 'vv4a') !== false) { ?>
                    transCalcSetRess('transCalcVV4A', '<?php echo abs($planet['vv4a']) ?>');
                    <?php		} 		if (strpos($params['autocalc'], 'eis') !== false) { ?>
                    transCalcSetRess('transCalcEis', '<?php echo abs($planet['eis']) ?>');
                    <?php		} 		if (strpos($params['autocalc'], 'wasser') !== false) { ?>
                    transCalcSetRess('transCalcWasser', '<?php echo abs($planet['wasser']) ?>');
                    <?php		} 		if (strpos($params['autocalc'], 'energie') !== false) { ?>
                    transCalcSetRess('transCalcEnergie', '<?php echo abs($planet['energie']) ?>');
                    <?php		} 	} ?>
                }
                function transCalcSetRess(id, amount) {
                    var input = document.getElementById(id);
                    if (input)
                        if (amount == 0)
                            transCalcResetRess(id);
                        else
                            input.value = amount;
                    transCalcUpdate();
                }
                function transCalcResetRess(id) {
                    var input = document.getElementById(id);
                    if (input)
                        input.value = '';
                    transCalcUpdate();
                }
                function transCalcUpdate() {
                    var class1 = 0;
                    var class2 = 0;
                    var value;
                    var result;

                    value = parseInt(document.getElementById('transCalcEisen').value);
                    class1 += isNaN(value) ? 0 : value;

                    value = parseInt(document.getElementById('transCalcStahl').value);
                    class1 += isNaN(value) ? 0 : value * 2;

                    value = parseInt(document.getElementById('transCalcChemie').value);
                    class1 += isNaN(value) ? 0 : value * 3;

                    value = parseInt(document.getElementById('transCalcVV4A').value);
                    class1 += isNaN(value) ? 0 : value * 4;

                    value = parseInt(document.getElementById('transCalcEis').value);
                    class2 += isNaN(value) ? 0 : value * 2;

                    value = parseInt(document.getElementById('transCalcWasser').value);
                    class2 += isNaN(value) ? 0 : value * 2;

                    value = parseInt(document.getElementById('transCalcEnergie').value);
                    class2 += isNaN(value) ? 0 : value;

                    result = Math.ceil(class1 / 400000);
                    document.getElementById('transCalcFlughund').value = isNaN(result) ? '' : result;

                    result = Math.ceil(class1 / 75000);
                    document.getElementById('transCalcKamel').value = isNaN(result) ? '' : result;

                    result = Math.ceil(class1 / 20000);
                    document.getElementById('transCalcGorgol').value = isNaN(result) ? '' : result;

                    result = Math.ceil(class1 / 5000);
                    document.getElementById('transCalcSystrans').value = isNaN(result) ? '' : result;

                    result = Math.ceil(class2 / 250000);
                    document.getElementById('transCalcSeepferdchen').value = isNaN(result) ? '' : result;

                    result = Math.ceil(class2 / 50000);
                    document.getElementById('transCalcWaschbaer').value = isNaN(result) ? '' : result;

                    result = Math.ceil(class2 / 10000);
                    document.getElementById('transCalcEisbaer').value = isNaN(result) ? '' : result;

                    result = Math.ceil(class2 / 2000);
                    document.getElementById('transCalcLurch').value = isNaN(result) ? '' : result;
                }
            </script>
        </head>
        <body onLoad="transCalcOnLoad()">
        <form target="_top">
        <input type="hidden" name="name" value="<?php echo $params['name'] ?>"/>
        <input type="hidden" name="view" value="<?php echo $params['view'] ?>"/>
        <input type="hidden" name="galaxy" value="<?php echo $params['galaxy'] ?>"/>
        <input type="hidden" name="system" value="<?php echo $params['system'] ?>"/>
        <input type="hidden" name="planet" value="<?php echo $params['planet'] ?>"/>
        <input type="hidden" name="next_galaxy" value="<?php echo $data['target_next_gal'] ?>"/>
        <input type="hidden" name="next_system" value="<?php echo $data['target_next_sys'] ?>"/>
        <input type="hidden" name="next_planet" value="<?php echo $data['target_next_planet'] ?>"/>
        <input type="hidden" name="prev_galaxy" value="<?php echo $data['target_prev_gal'] ?>"/>
        <input type="hidden" name="prev_system" value="<?php echo $data['target_prev_sys'] ?>"/>
        <input type="hidden" name="prev_planet" value="<?php echo $data['target_prev_planet'] ?>"/>
        <table width="100%">
        <tr>
        <td nowrap width="100%">
        <table>
        <tr>
            <td nowrap>
                <?php    if (!empty($data['planet'])) {
                    $planet = $data['planet'];
                    if (isColony($planet)) {
                        echo "<img src='".BILDER_PATH."kolo.png' title='Kolonie'/>";
                    }
                    if (isBattleBase($planet)) {
                        echo "<img src='".BILDER_PATH."kampf_basis.png' title='Kampfbasis'/>";
                    }
                    if (isRessourceBase($planet)) {
                        echo "<img src='".BILDER_PATH."ress_basis.png' title='Sammelbasis'/>";
                    }
                    if (isArtefactBase($planet)) {
                        echo "<img src='".BILDER_PATH."artefakt_basis.png' title='Artefaktbasis'/>";
                    }
                } ?>
            <td nowrap>
                <?php echo $planet['coords_gal'] ?>:<?php echo $planet['coords_sys'] ?>
                :<?php echo $planet['coords_planet'] ?>
            </td>
            <?php    if (hasAlliance($planet)) { ?>
                <td nowrap>
                    [<span class="alliance<?php echo !empty($planet['allianzstatus']) ? '-' . $planet['allianzstatus'] : '' ?>"><?php echo $planet['allianz'] ?></span>]
                </td>
            <?php } ?>
            <td nowrap align="left">
                <?php echo $planet['user']; ?>
            </td>
            <td nowrap width="100%" align="left">
                <?php    if (!empty($planet['transports'])) {
                    foreach ($planet['transports'] as $transport) {
                        echo "<img src='".BILDER_PATH."raumschiff.png' title='".$transport['caption']."' />";
                    }
                } ?>
            </td>
            <td nowrap align="right" valign="top">
                <a href="index.php?action=newscan" target="_top">[ X ]</a>
            </td>
        </tr>
        <tr height="90px">
        <td colspan="5" valign="top" width="100%" nowrap>
        <div style="float:left">
            <div style="margin-right:10px">
                <img src="bilder/eisen.png" title="Eisen"/>
                <?php echo formatAmount($planet['eisen']) ?>
            </div>
            <div style="margin-right:10px">
                <img src="bilder/stahl.png" title="Stahl"/>
                <?php echo formatAmount($planet['stahl']) ?>
            </div>
            <div style="margin-right:10px">
                <img src="bilder/vv4a.png" title="VV4A"/>
                <?php echo formatAmount($planet['vv4a']) ?>
            </div>
            <div style="margin-right:10px">
                <img src="bilder/chemie.png" title="chem. Elemente"/>
                <?php echo formatAmount($planet['chem']) ?>
            </div>
            <div style="margin-right:10px">
                <img src="bilder/eis.png" title="Eis"/>
                <?php echo formatAmount($planet['eis']) ?>
            </div>
            <div style="margin-right:10px">
                <img src="bilder/wasser.png" title="Wasser"/>
                <?php echo formatAmount($planet['wasser']) ?>
            </div>
            <div style="margin-right:10px">
                <img src="bilder/energie.png" title="Energie"/>
                <?php echo formatAmount($planet['energie']) ?>
            </div>
        </div>
        <?php    if (!isset($planet['stock'])) {
            foreach ($data['cat'] as $category) { ?>
                <div style="float:left">
                    <?php        foreach ($category as $key) {
                        if (!empty($planet[$key])) { ?>
                            <div>
                                <?php                if (empty($data['info'][$key])) { ?>
                                    <a href="javascript:void(0)"><?php echo $key ?></a>
                                <?php } else { ?>
                                    <a href="javascript:void(0)"><?php echo $data['info'][$key]['caption'] ?></a>
                                <?php } ?>
                            </div>
                        <?php }
                    } ?>
                </div>
                <div style="float:left">
                    <?php        foreach ($category as $key) {
                        if (!empty($planet[$key])) { ?>
                            <div style="margin-left:2px; margin-right:10px">
                                <?php echo $planet[$key] ?>
                            </div>
                        <?php }
                    } ?>
                </div>
            <?php }
        } ?>
        <div style="float:left; width:170px">
            <div style="height:25px;">
                <div style="float:left; width:50px; line-height:25px; vertical-align:middle">
                    Eisen
                </div>
                <div style="height:100%; line-height:25px; vertical-align:middle">
                    <a href=\"javascript:transCalcSetRess('transCalcEisen','<?php echo abs($planet['eisen']) ?>')\">--&gt;</a>
                    <input id="transCalcEisen" type="text" name="eisen" size="6" onkeyup="transCalcUpdate()">
                    <a href="javascript:transCalcResetRess('transCalcEisen')">-x-</a>
                </div>
            </div>
            <div style="height:25px;">
                <div style="float:left; width:50px; line-height:25px; vertical-align:middle">
                    Stahl
                </div>
                <div>
                    <a href=\"javascript:transCalcSetRess('transCalcStahl','<?php echo abs($planet['stahl']) ?>')\">--&gt;</a>
                    <input id="transCalcStahl" type="text" name="stahl" size="6" onkeyup="transCalcUpdate()">
                    <a href="javascript:transCalcResetRess('transCalcStahl')">-x-</a>
                </div>
            </div>
            <div style="height:25px">
                <div style="float:left; width:50px; line-height:25px; vertical-align:middle">
                    Chemie
                </div>
                <div>
                    <a href=\"javascript:transCalcSetRess('transCalcChemie','<?php echo abs($planet['chem']) ?>')\">--&gt;</a>
                    <input id="transCalcChemie" type="text" name="chemie" size="6" onkeyup="transCalcUpdate()">
                    <a href="javascript:transCalcResetRess('transCalcChemie')">-x-</a>
                </div>
            </div>
            <div style="height:25px">
                <div style="float:left; width:50px; line-height:25px; vertical-align:middle">
                    VV4A
                </div>
                <div>
                    <a href=\"javascript:transCalcSetRess('transCalcVV4A','<?php echo abs($planet['vv4a']) ?>')\">--&gt;</a>
                    <input id="transCalcVV4A" type="text" name="vv4a" size="6" onkeyup="transCalcUpdate()">
                    <a href="javascript:transCalcResetRess('transCalcVV4A')">-x-</a>
                </div>
            </div>
        </div>
        <div style="float:left; width:170px">
            <div style="height:25px;">
                <div style="float:left; width:50px; line-height:25px; vertical-align:middle">
                    Eis
                </div>
                <div style="height:100%; line-height:25px; vertical-align:middle">
                    <a href=\"javascript:transCalcSetRess('transCalcEis','<?php echo abs($planet['eis']) ?>')\">--&gt;</a>
                    <input id="transCalcEis" type="text" name="eis" size="6" onkeyup="transCalcUpdate()">
                    <a href="javascript:transCalcResetRess('transCalcEis')">-x-</a>
                </div>
            </div>
            <div style="height:25px;">
                <div style="float:left; width:50px; line-height:25px; vertical-align:middle">
                    Wasser
                </div>
                <div>
                    <a href=\"javascript:transCalcSetRess('transCalcWasser','<?php echo abs($planet['wasser']) ?>')\">--&gt;</a>
                    <input id="transCalcWasser" type="text" name="wasser" size="6" onkeyup="transCalcUpdate()">
                    <a href="javascript:transCalcResetRess('transCalcWasser')">-x-</a>
                </div>
            </div>
            <div style="height:25px">
                <div style="float:left; width:50px; line-height:25px; vertical-align:middle">
                    Energie
                </div>
                <div>
                    <a href=\"javascript:transCalcSetRess('transCalcEnergie','<?php echo abs($planet['energie']) ?>')\">--&gt;</a>
                    <input id="transCalcEnergie" type="text" name="energie" size="6" onkeyup="transCalcUpdate()">
                    <a href="javascript:transCalcResetRess('transCalcEnergie')">-x-</a>
                </div>
            </div>
        </div>
        <div style="float:left; width:120px">
            <div style="height:25px;">
                <div style="float:left; width:60px; line-height:25px; vertical-align:middle">
                    <a href="javascript:void(0)">Flughund</a>
                </div>
                <div style="height:100%; line-height:25px; vertical-align:middle">
                    <input id="transCalcFlughund" type="text" name="flughund" size="4">
                </div>
            </div>
            <div style="height:25px">
                <div style="float:left; width:60px; line-height:25px; vertical-align:middle">
                    <a href="javascript:void(0)">Kamel</a>
                </div>
                <div>
                    <input id="transCalcKamel" type="text" name="kamel" size="4">
                </div>
            </div>
            <div style="height:25px;">
                <div style="float:left; width:60px; line-height:25px; vertical-align:middle">
                    <a href="javascript:void(0)">Gorgol</a>
                </div>
                <div>
                    <input id="transCalcGorgol" type="text" name="gorgol" size="4">
                </div>
            </div>
            <div style="height:25px;">
                <div style="float:left; width:60px; line-height:25px; vertical-align:middle">
                    <a href="javascript:void(0)">Systrans</a>
                </div>
                <div>
                    <input id="transCalcSystrans" type="text" name="systrans" size="4">
                </div>
            </div>
        </div>
        <div style="float:left; width:160px">
            <div style="height:25px;">
                <div style="float:left; width:90px; line-height:25px; vertical-align:middle">
                    <a href="javascript:void(0)">Seepferdchen</a>
                </div>
                <div style="height:100%; line-height:25px; vertical-align:middle">
                    <input id="transCalcSeepferdchen" type="text" name="flughund" size="4">
                </div>
            </div>
            <div style="height:25px">
                <div style="float:left; width:90px; line-height:25px; vertical-align:middle">
                    <a href="javascript:void(0)">Waschb&auml;r</a>
                </div>
                <div>
                    <input id="transCalcWaschbaer" type="text" name="kamel" size="4">
                </div>
            </div>
            <div style="height:25px;">
                <div style="float:left; width:90px; line-height:25px; vertical-align:middle">
                    <a href="javascript:void(0)">Eisb&auml;r</a>
                </div>
                <div>
                    <input id="transCalcEisbaer" type="text" name="eisbaer" size="4">
                </div>
            </div>
            <div style="height:25px;">
                <div style="float:left; width:90px; line-height:25px; vertical-align:middle">
                    <a href="javascript:void(0)">Lurch</a>
                </div>
                <div>
                    <input id="transCalcLurch" type="text" name="lurch" size="4">
                </div>
            </div>
        </div>
        <div style="float:left">
            <div>
                <a href="<?php echo $data['url']['main'] ?>" target="main">Flotte versenden</a>
            </div>
            <div>
                <a href="<?php echo $data['url']['uniview'] ?>" target="_top">Universum</a>
            </div>
            <div style="margin-bottom:12px">
                <a href="<?php echo $planet['simulator'] ?>" target="main">Simulator</a>
            </div>
            <?php    if (isset($data['target_prev_gal'])) { ?>
                <div style="margin-bottom:5px">
                    <input type="submit" name="prev" value="&lt;&lt; Zur&uuml;ck"/>
                </div>
            <?php }     if (isset($data['target_next_gal'])) { ?>
                <div>
                    <input type="submit" name="next" value="Weiter &gt;&gt;"/>
                </div>
            <?php } ?>
        </div>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </form>
        </body>
        </html>
        <?php break;
    case 'right':
        ?>
        <html>
        <head>
            <link href="css/game.css" rel="stylesheet" type="text/css">
        </head>
        <body>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" height="177px">
            <tr>
                <td align="right" nowrap>[<a href="index.php?action=newscan" target="_top">X</a>]
                </td>
            </tr>
            <tr>
                <td align="center" nowrap>Universum</td>
            </tr>
            <tr>
                <?php    if (hasSystem()) { ?>
                    <td align="center" nowrap>
                        <a href="<?php echo $data['url']['universum'] ?>" target="main">Galaxy <?php echo $params['galaxy'] ?>
                            , Sonnensystem <?php echo $params['system'] ?></a></td>
                <?php } else { ?>
                    <td align="center" nowrap>Unbekannte Galaxie</td>
                <?php } ?>
            </tr>
            <tr>
                <td align="center" nowrap><?php echo $params['name'] ?></td>
            </tr>
            <tr height="100%">
                <td>&nbsp;</td>
            </tr>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" valign="top">
        <tr>
            <td colspan="3" width="100%" height="71px" nowrap>
                <table width="100%">
                    <tr>
                        <td align="left" nowrap>
                            <a href="<?php echo $data['url']['prev'] ?>" target="_top">
                                &lt;--</a> <?php echo $data['targets_prev_gal'] . ':' . $data['targets_prev_sys'] ?>
                        </td>
                        <td width="100%" align="center" nowrap>
                            Ziele <?php echo $data['targets_position'] + 1;  echo $data['targets_count'] > 1 ? '-' . ($data['targets_position'] + $data['targets_count']) : '' ?>
                            von <?php echo count($data['targets']) ?>
                        </td>
                        <td align="right" nowrap>
                            <?php    if (!empty($data['targets_next_gal'])) {
                                echo $data['targets_next_gal'] . ':' . $data['targets_next_sys'] ?>
                                <a href="<?php echo $data['url']['next'] ?>" target="_top">--&gt;</a>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php    foreach ($data['planets'] as $planet) {         if (isset($data['targets'][$planet['coords']])) {             if ($user_uniprop) { ?>
        <tr class="universum-row-selected top" height="71px" >
            <?php            } else { ?>
        <tr class="universum-row-selected top" >
            <?php            }         } else {             if ($user_uniprop) { ?>
        <tr class="universum-row<?php echo !empty($planet['allianzstatus']) ? '-' . $planet['allianzstatus'] : '' ?>" height="71px" valign="top">
            <?php            } else { ?>
        <tr class="universum-row<?php echo !empty($planet['allianzstatus']) ? '-' . $planet['allianzstatus'] : '' ?>" valign="top">
            <?php            }         } ?>
            <td class="universum center middle" style="width:20px">
                <?php echo $planet['coords_planet'] ?>
            </td>
            <td class="universum center middle" style="width:20px">
                <?php echo formatTyp($planet['typ']) ?>
            </td>
            <td class="universum top">
                <table>
                    <tr class="middle">
                        <td nowrap>
                            <?php    if (isColony($planet)) { ?>
                                <img src="bilder/kolo.png" title="Kolonie"/>
                            <?php }     if (isBattleBase($planet)) { ?>
                                <img src="bilder/kampf_basis.png" title="Kampfbasis"/>
                            <?php }      if (isRessourceBase($planet)) { ?>
                                <img src="bilder/ress_basis.png" title="Sammelbasis"/>
                            <?php }     if (isArtefactBase($planet)) { ?>
                                <img src="bilder/artefakt_basis.png" title="Artefaktbasis"/>
                            <?php } ?>
                        </td>
                        <?php    if (hasAlliance($planet)) { ?>
                            <td nowrap>
                                [<span class="alliance<?php echo !empty($planet['allianzstatus']) ? '-' . $planet['allianzstatus'] : '' ?>"><?php echo $planet['allianz'] ?></span>]
                            </td>
                        <?php } ?>
                        <td nowrap>
                            <?php    if (hasGeoScan($planet) && !hasOwner($planet)) { ?>
                                <table>
                                    <tr>
                                        <td><a href="javascript:void(0)" title="Vorkommen Eisen">Eisen</a></td>
                                        <td><?php echo formatYield($planet["eisengehalt"]) ?></td>
                                        <td><a href="javascript:void(0)" title="Vorkommen chem. Elemente">Chem.</a></td>
                                        <td><?php echo formatYield($planet["chemiegehalt"]) ?></td>
                                        <td><a href="javascript:void(0)" title="Vorkommen Eis">Eis</a></td>
                                        <td><?php echo formatYield($planet["eisdichte"]) ?></td>
                                        <td><a href="javascript:void(0)" title="Lebensbedingungen">LB</a></td>
                                        <td><?php echo formatYield($planet["lebensbedingungen"]) ?></td>
                                    </tr>
                                </table>
                            <?php } else {
                                if (isset($planet['fleet_send'])) { ?>
                                    <a href="<?php echo $planet['fleet_send'] ?>" target="_top"><?php echo $planet['user']; ?></a>
                                <?php } else {
                                    echo $planet['user'];
                                }
                            } ?>
                        </td>
                        <td nowrap width="100%">
                            <?php    if (!empty($planet['transports'])) {
                                foreach ($planet['transports'] as $transport) {
                                    ?>
                                    <img src="bilder/raumschiff.png" title="<?php echo $transport['caption'] ?>"/>
                                <?php
                                }
                            } ?>
                        </td>
                        <?php    if (hasRaid($planet)) { ?>
                            <td nowrap class="middle">
                                <a href="<?php echo $planet['raid_link'] ?>" target="main">Raid</a>
                            </td>
                            <td nowrap class="middle">
                                <?php echo formatDuration($planet['raid_time'], 48 * 60) ?>
                            </td>
                        <?php }     if (hasFailScan($planet)) { ?>
                            <td nowrap>
                            </td>
                            <td nowrap class="middle">
                                <span style="color:#ff0000;" title="Fehlgeschlagene Sondierung"><?php echo formatDuration($planet['fehlscantime']) ?></span>
                            </td>
                        <?php }     if (hasShipScan($planet)) { ?>
                            <td nowrap>
                                <img src="bilder/scann_schiff.png" title="Schiffscan"/>
                            </td>
                            <td nowrap class="middle">
                                <?php echo formatDuration($planet['schiffscantime'], 48 * 60) ?>
                            </td>
                        <?php }     if (hasGebScan($planet)) { ?>
                            <td nowrap class="top">
                                <img src="bilder/scann_geb.png" title="Gebäudescan"/>
                            </td>
                            <td nowrap class="middle">
                                4d
                            </td>
                        <?php }     if (hasGeoScan($planet)) { ?>
                            <td nowrap class="top">
                                <img src="bilder/scann_geo.png" title="Geoscan"/>
                            </td>
                            <td nowrap class="middle">
                                <?php echo formatDuration($planet['geoscantime']) ?>
                            </td>
                        <?php } ?>
                    </tr>
                </table>
                <?php    if (hasShipScan($planet) && hasOwner($planet)) { ?>
                    <table>
                        <tr>
                            <td nowrap>
                                <img src="bilder/eisen.png" title="Eisen"/>
                            </td>
                            <td class="top" nowrap>
                                <?php echo formatAmount($planet["eisen"]) ?>
                            </td>
                            <td nowrap>
                                <img src="bilder/stahl.png" title="Stahl"/>
                            </td>
                            <td class="top" nowrap>
                                <?php echo formatAmount($planet["stahl"]) ?>
                            </td>
                            <td nowrap>
                                <img src="bilder/vv4a.png" title="VV4A"/>
                            </td>
                            <td class="top" nowrap>
                                <?php echo formatAmount($planet["vv4a"]) ?>
                            </td>
                            <td nowrap>
                                <img src="bilder/chemie.png" title="chem. Elemente"/>
                            </td>
                            <td class="top" nowrap>
                                <?php echo formatAmount($planet["chemie"]) ?>
                            </td>
                            <td nowrap>
                                <img src="bilder/eis.png" title="Eis"/>
                            </td>
                            <td class="top" nowrap>
                                <?php echo formatAmount($planet["eis"]) ?>
                            </td>
                            <td nowrap>
                                <img src="bilder/wasser.png" title="Wasser"/>
                            </td>
                            <td valign="top" nowrap>
                                <?php echo formatAmount($planet["wasser"]) ?>
                            </td>
                            <td nowrap>
                                <img src="bilder/energie.png" title="Energie"/>
                            </td>
                            <td class="top" nowrap>
                                <?php echo formatAmount($planet["energie"]) ?>
                            </td>
                            <td nowrap style="width:100%">
                            </td>
                            <td nowrap>
                                <?php echo formatRating($planet["rating"]) ?>
                            </td>
                        </tr>
                    </table>
                <?php } elseif (hasGeoScan($planet) && hasOwner($planet)) { ?>
                    <table>
                        <tr>
                            <td><a href="javascript:void(0)" title="Vorkommen Eisen">Eisen</a></td>
                            <td><?php echo formatYield($planet["eisengehalt"]) ?></td>
                            <td><a href="javascript:void(0)" title="Vorkommen chem. Elemente">Chem.</a></td>
                            <td><?php echo formatYield($planet["chemiegehalt"]) ?></td>
                            <td><a href="javascript:void(0)" title="Vorkommen Eis">Eis</a></td>
                            <td><?php echo formatYield($planet["eisdichte"]) ?></td>
                            <td><a href="javascript:void(0)" title="Lebensbedingungen">LB</a></td>
                            <td><?php echo formatYield($planet["lebensbedingungen"]) ?></td>
                        </tr>
                    </table>
                <?php } ?>
                <table>
                    <tr>
                        <?php        foreach ($data['info'] as $key => $info) {
                            if (!empty($planet[$key])) {
                                if (is_array($planet[$key])) {
                                    ?>
                                    <td nowrap>
                                        <a href="javascript:void(0)" title="<?php echo $planet[$key]['title'] ?>"><?php echo $info['caption'] ?></a>
                                    </td>
                                    <td nowrap><?php echo formatValue($planet[$key]['value']) ?></td>
                                <?php } else { ?>
                                    <td nowrap>
                                        <a href="javascript:void(0)" title="<?php echo $info['title'] ?>"><?php echo $info['caption'] ?></a>
                                    </td>
                                    <td nowrap><?php echo formatValue($planet[$key]) ?></td>
                                <?php
                                }
                            }
                        } ?>
                    </tr>
                </table>
                <?php    } ?>
            </td>
        </tr>
        <tr height="40px" valign="top">
            <td class="universum-spacer top" colspan="3" width="100%" height="40px"  nowrap>
                <table width="100%" height="40px" valign="top">
                    <tr height="40px" valign="top">
                        <td align="left" valign="top" nowrap>
                            <a href="<?php echo $data['url']['prev'] ?>" target="_top">
                                &lt;--</a> <?php echo $data['targets_prev_gal'] . ':' . $data['targets_prev_sys'] ?>
                        </td>
                        <td width="100%" valign="top" align="center" nowrap>
                            Ziele <?php echo $data['targets_position'] + 1;  echo $data['targets_count'] > 1 ? '-' . ($data['targets_position'] + $data['targets_count']) : '' ?>
                            von <?php echo count($data['targets']) ?>
                        </td>
                        <td align="right" valign="top" nowrap>
                            <?php    if (!empty($data['targets_next_gal'])) {
                                echo $data['targets_next_gal'] . ':' . $data['targets_next_sys'] ?>
                                <a href="<?php echo $data['url']['next'] ?>" target="_top">--&gt;</a>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        </table>
        </body>
        </html>
        <?php
        break;
    default:
        echo 'Fehler: Unbekannter Modus '.$params['mode'];
}

/// Local functions

function hasSystem()
{
    global $params;

    return !empty($params['galaxy']) && !empty($params['system']);
}

function hasOwner($planet)
{
    return !empty($planet['user']);
}

function isColony($planet)
{
    return !empty($planet["objekt"]) && $planet["objekt"] == "Kolonie";
}

function isBattleBase($planet)
{
    return !empty($planet["objekt"]) && $planet["objekt"] == "Kampfbasis";
}

function isRessourceBase($planet)
{
    return !empty($planet["objekt"]) && $planet["objekt"] == "Sammelbasis";
}

function isArtefactBase($planet)
{
    return !empty($planet["objekt"]) && $planet["objekt"] == "Artefaktbasis";
}

function hasAllianceStatus($planet)
{
    return false;
}

function hasRaid($planet)
{
    return !empty($planet['raid_link']);
}

function hasFailScan($planet)
{
    return !empty($planet['fehlscantime']) && ($planet['fehlscantime'] > $planet['schiffscantime']) && ($planet['fehlscantime'] > $planet['gebscantime']);
}

function hasShipScan($planet)
{
    return !empty($planet['schiffscantime']);
}

function hasGebScan($planet)
{
    return false;
}

function hasGeoScan($planet)
{
    return !empty($planet['geoscantime']) || !empty($planet['eisengehalt']);
}

function hasRess($planet)
{
    return !empty($planet["eisen"]) || !empty($planet["stahl"]) || !empty($planet["vv4a"]) || !empty($planet["chemie"]) || !empty($planet["eis"]) || !empty($planet["wasser"]) || !empty($planet["energie"]);
}

function hasAlliance($planet)
{
    return !empty($planet["allianz"]);
}

function formatRess($value)
{
    return $value;
}

function formatTyp($value)
{
    return substr($value, 0, 1);
}

//****************************************************************************
//
// Rating formatieren
function formatRating($value)
{
    if ($value < 100) {
        $result = '<span style="color:#ff0000">';
    } elseif ($value >= 100 && $value < 999) {
        $result = '<span style="color:#ffff00">';
    } else {
        $result = '<span style="color:#00ff00">';
    }
    $result .= number_format($value, 0, ',', '.') . '%';
    $result .= '</span>';

    return $result;
}

//****************************************************************************
//
// Zahl formatieren
function formatValue($value, $decimals = 0)
{
    if (is_numeric($value)) {
        return number_format($value, $decimals, ',', '.');
    } else {
        return $value;
    }
}

//****************************************************************************
//
// Vorkommen/Gehalt formatieren
function formatYield($yield)
{
    return number_format($yield, 0, ",", '.') . "%";
}

//****************************************************************************
//
// Menge formatieren
function formatAmount($amount)
{
    $pre  = $amount < 0 ? '<span style="color:red">' : '';
    $post = $amount < 0 ? '</span>' : '';
    if (abs($amount) > 1000) {
        return $pre . number_format(round($amount / 1000), 0, ",", '.') . "k" . $post;
    } else {
        return $pre . number_format($amount, 0, ",", '.') . $post;
    }
}

//****************************************************************************
//
// Dauer formatieren
function formatDuration($time, $minYellow = 0)
{
    if (empty($time)) {
        return '---';
    }
    $duration = time() - $time;
    $hours    = round($duration / HOUR);
    $minutes  = round($duration / MINUTE);
    if (!empty($minYellow)) {
        $pre  = '<span style="color:' . ($minutes >= $minRed ? '#ffff00' : '#00ff00') . '">';
        $post = '</span>';
    } else {
        $pre  = '';
        $post = '';
    }
    if ($duration > 2 * DAY) {
        return $pre . round($duration / DAY) . 'd' . $post;
    } elseif ($duration > HOUR) {
        return $pre . ($hours == 1 ? "1 Stunde" : $hours) . 'h' . $post;
    } else {
        return $pre . ($minutes == 1 ? "1 Minute" : $minutes) . 'm' . $post;
    }
}