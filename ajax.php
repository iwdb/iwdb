<?php

require_once("includes/bootstrap.php");

if ($login_ok === false) {
    exit;
}

global $db, $db_tb_incomings;
$action = getVar('action');

if ($action === 'gesaved') {

    if (getVar('state') === 'true') {
        $gesavedTime = CURRENT_UNIX_TIME;
    } else {
        $gesavedTime = null;
    }
    $coords = $db->escape(getVar('coords'));

    $result = $db->db_update($db_tb_incomings, array('gesaved' => $gesavedTime), "WHERE koords_to = '$coords'");

    if ($result === true) {
        echo 'success';
    }

} elseif ($action === 'recalled') {

    if (getVar('state') === 'true') {
        $recalledTime = CURRENT_UNIX_TIME;
    } else {
        $recalledTime = null;
    }
    $coords = $db->escape(getVar('coords'));

    $result = $db->db_update($db_tb_incomings, array('recalled' => $recalledTime), "WHERE koords_to = '$coords'");

    if ($result === true) {
        echo 'success';
    }
}