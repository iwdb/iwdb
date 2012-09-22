<?php
include_once('config/configsql.php');
include_once('includes/db_mysql.php');

$db = new db();
$link_id = $db->db_connect($db_host, $db_user, $db_pass, $db_name)
	or die('Could not connect to database.');

include_once('config/config.php');
include_once('includes/function.php');
include_once('includes/sid.php');
include_once("includes/debug.php");

global $sid;

$sql = "SELECT gesperrt FROM " . $db_tb_user . " WHERE id = '" . $user_id . "'"; 
$result = $db->db_query($sql)       
	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row = $db->db_fetch_array($result);
if ($row['gesperrt'] == 1)
	die('<div style="text-align:center;color:red">ihr Account ist gesperrt worden!</div>');

if (empty($sid) || empty($user_sitterlogin) || !($user_adminsitten == SITTEN_BOTH || $user_adminsitten == SITTEN_ONLY_LOGINS) || $user_id == "guest") {
	header("Location: " . APPLICATION_PATH);
	exit;
}
?>