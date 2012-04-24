<?php
/*****************************************************************************/
/* index.php                                                                 */
/* $Id $                                                                     */
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
error_reporting(E_ALL);

//ist noch eine install.php vorhanden?
if (file_exists('install.php')) {
    die ('<div style="text-align:center;color:red">Eine install.php ist noch vorhanden!</div><div style="text-align:center">Du darfst den Admin slammen, da er vergessen hat, diese aus dem Rootordner zu löschen!</div>');
}

// define's vor allen anderen Includes durchführen 
define('IRA', TRUE);

define('NEBULA', TRUE);
define('SPECIALSEARCH', TRUE);
define('ALLY_MEMBERS_ON_MAP', TRUE); 
define('SHOWWITHOUTSCAN', TRUE);
define('CONFIG_SERVER_URI', TRUE);
define('GENERAL_ERROR', 'GENERAL_ERROR');

global $db_host, $db_user, $db_pass, $db_name, $db_prefix;
include_once("config/configsql.php");
include_once("includes/function.php");
include_once("includes/db_mysql.php");

// Verschiebung der Erzeugung der globalen DB-Verbindung, da diese jetzt auch
// beim Laden der Konfiguration benötigt wird. 
$error = '';

$db = new db();
$link_id = $db->db_connect($db_host, $db_user, $db_pass, $db_name);
if(!$link_id) {
    exit('Could not connect to Database!');
}

include("config/config.php");

//$sql = "SET charset latin1";
//$result = $db->db_query($sql)
//	or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

$action = getVar('action');
if ( empty($action) )
{
	$action = $config_default_action;
}

include("includes/sid.php");
global $sid;

  //Abkratzen sollte der User gesperrt sein
 	$sql = "SELECT gesperrt FROM " . $db_tb_user . " WHERE id = '" . $user_id . "'"; 
 	$result_g = $db->db_query($sql)       
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      $row_g = $db->db_fetch_array($result_g);
      if ($row_g['gesperrt'] == 1 ) die ('<div style="text-align:center;color:red">Der Account ist gesperrt worden!</div>');

if (isset($user_status)) {

global $db_prefix;

$sqlIA = "SELECT text,value FROM " . $db_prefix . "params WHERE name = 'gesperrt' ";
    $resultIA = $db->db_query($sqlIA)
        or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlIA);
    $rowIA = $db->db_fetch_array($resultIA);
$grund = $rowIA['text'];
$isornot = $rowIA['value'];

if ($isornot == 1) {

if ($user_status <> 'admin') {

die ('
<div style="text-align:center;color:red">Die Datenbank ist zur Zeit gesperrt!</div>
<div style="text-align:center;color:red">Grund:</div>
<div style="text-align:center;color:red"> ' . $grund . '</div>
');

} else {

echo '
<div style="text-align:center">Die Datenbank ist zur Zeit gesperrt!</div>
<div style="text-align:center">Grund:</div>
<div style="text-align:center"> ' . $grund . '</div>
';

}


}

}

// Regeln akzeptieren //
$rules = getVar('rules');
if ( ( ! empty($rules) ) && ( $rules == "1" ) && ( $user_id <> "guest" ) )
{
	$user_rules = "1";
	$sql = "UPDATE " . $db_tb_user . " SET rules = '1' WHERE sitterlogin = '" . $user_sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
}

// Sitterlogin //
$sitterlogin = getVar('sitterlogin');
if (( ( $user_adminsitten == SITTEN_BOTH ) || ( $user_adminsitten == SITTEN_ONLY_LOGINS )) &&
    ( $action == "sitterlogins" ) && ( ! empty($sitterlogin) ) && ( $user_id <> "guest" ) )
{
	$sql = "DELETE FROM " . $db_tb_sitterlog . 
         " WHERE date<" . ( $config_date - $config_sitterlog_timeout );
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	$sql = "SELECT id FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $sitterlogin . "' AND fromuser = '" . $user_sitterlogin . "' AND action = 'login' AND date > " . ( $config_date - $config_sitterpunkte_timeout );
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$anz = $db->db_num_rows($result);

	$sql = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin, fromuser, date, action) VALUES ('" . $sitterlogin . "', '" . $user_sitterlogin . "', '" . $config_date . "', 'login')";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	// User
	$sql = "UPDATE " . $db_tb_user . " SET lastsitterloggedin=0 WHERE lastsitteruser='" . $user_sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$sql = "UPDATE " . $db_tb_user . " SET lastsitterlogin=" . time() . ",lastsitteruser='" . $user_sitterlogin . "',lastsitterloggedin=1 WHERE id='" . $sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);	

	if ( ( $sitterlogin != $user_sitterlogin ) && ( $anz == 0 ) )
	{	
		$sql = "UPDATE " . $db_tb_user . " SET sitterpunkte = sitterpunkte + " . $config_sitterpunkte_login . " WHERE sitterlogin = '" . $user_sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	}

	$sql = "SELECT sitterpwd FROM " . $db_tb_user . " WHERE sitterlogin = '" . $sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);
	header("Location: http://icewars.de/index.php?action=login&name=" . urlencode($sitterlogin) . "&pswd=" . $row['sitterpwd'] . "&sitter=1&ismd5=1&ip_change=1&serverskin=1&serverskin_typ=" . $user_sitterskin . "&submit=true");
//	echo "Location: http://icewars.de/index.php?action=login&name=" . urlencode($sitterlogin) . "&pswd=" . $row['sitterpwd'] . "&sitter=1&ismd5=1&ip_change=1&serverskin=1&serverskin_typ=" . $user_sitterskin . "&submit=true";
	exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $config_allytitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
if(defined('CONFIG_SERVER_URI') && CONFIG_SERVER_URI === TRUE ) {
  $SERVERURI = "index.php?action=" . $action . "&amp;sid=" . $sid;
} else {
  $SERVERURI = getServerVar('REQUEST_URI', "index.php?action=".$action."&amp;sid=".$sid);
}
if ( ( $action == "sitterlogins" ) || ( $action == "sitterliste" ) )
  if ( ( $user_adminsitten == SITTEN_BOTH ) || ( $user_adminsitten == SITTEN_ONLY_LOGINS ) )
    echo "<meta http-equiv=\"refresh\" content=\"" . $config_refresh_timeout . "; URL=" . $SERVERURI . "\">";
  else
    echo "<div class='system_error'>Wenn ich sag: \"Du darfst nicht sitten\",<b>DARFST DU NICHT SITTEN !!!</b></div>";
?>
<link href="style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function confirmlink(link, text)
{
    var is_confirmed = confirm(text);
    return is_confirmed;
}
</script>
</head>
<?php	if (!getVar("nobody")) { ?>
<body class="body">
<?php
?>
<div align="center">
  <table class="seite">
    <tr>
      <td align="center" valign="top" class="background">
        <p>
<?php
//hier hin verschoben da der IE iwie imemr sonst Mist baut ^^
include ('includes/sitterfadein.php');
?>
</p>
        <table width="100%" border="0" cellpadding="0" cellspacing="1" class="bordercolor">
          <tr> 
			
			
			<td class="titlebg" style="background-color: #000000" align="center">
<?php
if (isset($config_banner))
{
?>
            	<img src="<?php echo $config_banner; ?>" width="<?php echo $config_banner_width; ?>">
<?php
}
?>
            </td>
          </tr>
        </table>
<?php	} ?>					
<?php
if ( ( $user_id <> "guest" ) && ( $user_rules == "1" ) )
{
   if(getVar("action") == "profile") {
     // Menue-Änderung voraus?
     $newmenustyle = getVar("menu_default");
     if((!empty($newmenustyle)) && ($newmenustyle != $user_menu_default)) {
       $user_menu_default = $newmenustyle;
     }
   }
   
 	 if(empty($user_menu_default))
	 	 $user_menu_default = "default";
     
   $user_doc_style = $user_menu_default;
   if(!file_exists("./menustyles/doc_" . $user_doc_style . ".php")) 
     $user_doc_style = "default";
		 
   include "./menustyles/doc_" . $user_doc_style . ".php";
	if (!getVar("nobody"))
		include "./menustyles/menu_" . $user_menu_default . ".php";
}
else 
{
?>
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="windowbg1" style="padding-left: 0px;" align="center">
<?php 
}

if ( ( $user_password == "a338268847bac752d23c30b410570c2c" ) || 
     ( $user_password == "2f5a63d542da883a490dd61ef46fe2a9" ) ) 
  echo "<br><div class='system_notification'><b>*moep* Achtung! Ändere bitte dein Passwort im Profil. Danke.</b></div><br><br>";
  
if ( ( empty($user_sitterpwd) ) && ( $user_sitten == "1" ) ) 
  echo "<br><div class='system_notification'><b>*moep* Achtung! Du hast zwar anderen das Sitten erlaubt, aber kein Sitterpasswort eingetragen.</b></div><br><br>";

if ( ( $user_id <> "guest" ) && ( $user_rules == "1" ) )
{
  # check action string for valid chars         
  if (! preg_match('/^[a-zA-Z0-9_-]*$/', $action)) {
    error(GENERAL_ERROR, 'Malformed action string (' . $action . ') .', '',
          __FILE__, __LINE__);
    exit(1);
  }
	if ( file_exists("modules/" . $action . ".php") === TRUE ) include("modules/" . $action . ".php");
	if ( $action == 'memberlogin2' ) include("modules/" . $config_default_action . ".php");
	if ( $action == 'deluser' )
	{
		$sql = "DELETE FROM " . $db_tb_user . " WHERE sitterlogin='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$sql = "DELETE FROM " . $db_tb_punktelog . " WHERE user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$sql = "DELETE FROM " . $db_tb_schiffe . " WHERE user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$sql = "DELETE FROM " . $db_tb_preset . " WHERE fromuser='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			
		$sql = "DELETE FROM " . $db_tb_lager . " WHERE user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$sql = "DELETE FROM " . $db_tb_ressuebersicht . " WHERE user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			
		$sql = "DELETE FROM " . $db_tb_research2user . " WHERE userid='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);	
			
		$sql = "DELETE FROM " . $db_tb_group_user . " WHERE user_id='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$sql = "DELETE FROM " . $db_tb_group_sort . " WHERE user_id='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
			
		$sql = "DELETE FROM " . $db_tb_gebaeude_spieler . " WHERE user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$sql = "DELETE FROM " . $db_tb_bestellung . " WHERE user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$sql = "DELETE FROM " . $db_tb_bestellen . " WHERE user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);


		// remove alliance info on member	in the maps		
  	$sql = "UPDATE " . $db_tb_scans . " SET allianz='' WHERE user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		

?>

<br><br>
<div class='doc_title'>Account löschen</div>
<br>
<div class='system_notification'>Account '<?php echo $sitterlogin;?>' gelöscht!</div>
<?php
	}
}
elseif ( ( $user_id <> "guest" ) && ( $user_rules != "1" ) )
{
?>
<table border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td align="left">
<?php
	include("help/rules.htm");
?>
  </td>
 </tr>
</table><br><br>
<form method="POST" action="index.php?sid=<?php echo $sid;?>" enctype="multipart/form-data">
Regeln akzeptieren? <input type="checkbox" name="rules" value="1"> <input type="submit" value="speichern" name="B1" class="submit"></form>
<?php
}
else
{
	if ( $action == 'password' ) include("modules/password.php");
	else include("modules/login.php");
}
echo $error;
?>
<?php	if (!getVar("nobody")) { ?>
&nbsp;
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <br>
      </td>
    </tr>
  </table>
</div>
</body>
</html>
<?php	} ?>