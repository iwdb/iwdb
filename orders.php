<?php>
define('IRA', TRUE);
include_once('config/configsql.php');
include_once('includes/db_mysql.php');
$db = new db();
$link_id = $db->db_connect($db_host, $db_user, $db_pass, $db_name)
	or die('Could not connect to database.');
include_once('config/config.php');
include_once('includes/function.php');
include_once('includes/sid.php');
global $sid;
?>
RoC DB 2.0