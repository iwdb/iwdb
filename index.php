<?php
/*****************************************************************************
 * index.php                                                                 *
 *****************************************************************************
 * Iw DB: Icewars geoscan and sitter database                                *
 * Open-Source Project started by Robert Riess (robert@riess.net)            *
 * ========================================================================= *
 * Copyright (c) 2004 Robert Riess - All Rights Reserved                     *
 *****************************************************************************
 * This program is free software; you can redistribute it and/or modify it   *
 * under the terms of the GNU General Public License as published by the     *
 * Free Software Foundation; either version 2 of the License, or (at your    *
 * option) any later version.                                                *
 *                                                                           *
 * This program is distributed in the hope that it will be useful, but       *
 * WITHOUT ANY WARRANTY; without even the implied warranty of                *
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General *
 * Public License for more details.                                          *
 *                                                                           *
 * The GNU GPL can be found in LICENSE in this directory                     *
 *****************************************************************************
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

define('APPLICATION_PATH_ABSOLUTE', dirname(__FILE__));
define('APPLICATION_PATH_RELATIVE', dirname($_SERVER['SCRIPT_NAME']));
define('APPLICATION_PATH_URL', dirname($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']));

require_once("includes/bootstrap.php");
global $sid;

//Abkratzen sollte der User gesperrt sein
$sql = "SELECT gesperrt FROM " . $db_tb_user . " WHERE id = '" . $user_id . "'";
$result_g = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
$row_g = $db->db_fetch_array($result_g);
if ($row_g['gesperrt'] == 1 ) {
    die ('<div style="text-align:center;color:red">Der Account ist gesperrt worden!</div>');
}

if (isset($user_status)) {
    $sqlIA = "SELECT text,value FROM " . $db_prefix . "params WHERE name = 'gesperrt' ";
        $resultIA = $db->db_query($sqlIA)
            or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sqlIA);
        $rowIA = $db->db_fetch_array($resultIA);
    $grund = $rowIA['text'];
    $isornot = $rowIA['value'];

    if ($isornot == 'true') {
        if ($user_status <> 'admin') {

            echo "<div style='text-align:center;color:red'>Die Datenbank ist zur Zeit gesperrt!</div>";
            echo "<div style='text-align:center;color:red'>Grund: $grund</div>";
            exit;

        } else {

            echo "<div style='text-align:center'>Die Datenbank ist zur Zeit gesperrt!</div>";
            echo "<div style='text-align:center'>Grund: $grund</div>";

        }
    }
}

// Regeln akzeptieren //
$rules = getVar('rules');
if ( ( ! empty($rules) ) && ( $rules == "1" ) && ( $user_id <> "guest" ) ) {
	$user_rules = "1";
	$sql = "UPDATE " . $db_tb_user . " SET rules = '1' WHERE sitterlogin = '" . $user_sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query rules information.', '', __FILE__, __LINE__, $sql);
}

// Sitterlogin //
$sitterlogin = getVar('sitterlogin');
if (( ( $user_adminsitten == SITTEN_BOTH ) || ( $user_adminsitten == SITTEN_ONLY_LOGINS )) &&
    ( $action == "sitterlogins" ) && ( ! empty($sitterlogin) ) && ( $user_id <> "guest" ) )
{
	$sql = "DELETE FROM " . $db_tb_sitterlog . 
         " WHERE date<" . ( CURRENT_UNIX_TIME - $config_sitterlog_timeout );
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	$sql = "SELECT id FROM " . $db_tb_sitterlog . " WHERE sitterlogin = '" . $sitterlogin . "' AND fromuser = '" . $user_sitterlogin . "' AND action = 'login' AND date > " . ( CURRENT_UNIX_TIME - $config_sitterpunkte_timeout );
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$anz = $db->db_num_rows($result);

	$sql = "INSERT INTO " . $db_tb_sitterlog . " (sitterlogin, fromuser, date, action) VALUES ('" . $sitterlogin . "', '" . $user_sitterlogin . "', '" . CURRENT_UNIX_TIME . "', 'login')";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

	// User
	$sql = "UPDATE " . $db_tb_user . " SET lastsitterloggedin=0 WHERE lastsitteruser='" . $user_sitterlogin . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
	$sql = "UPDATE " . $db_tb_user . " SET lastsitterlogin=" . CURRENT_UNIX_TIME . ",lastsitteruser='" . $user_sitterlogin . "',lastsitterloggedin=1 WHERE id='" . $sitterlogin . "'";
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
	exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title><?php echo $config_allytitle ?></title>

    <?php
    $SERVERURI = "index.php?action=" . $action . "&sid=" . $sid;

    if ( ( $action == "sitterlogins" ) || ( $action == "sitterliste" ) ) {
        if ( ( $user_adminsitten == SITTEN_BOTH ) || ( $user_adminsitten == SITTEN_ONLY_LOGINS ) ) {
            echo "<meta http-equiv='refresh' content='" . $config_refresh_timeout . "; URL=" . $SERVERURI . "'>";
        }
    }
    ?>
    <link href="style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="javascript/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="javascript/bbcode.js"></script>
    <script type="text/javascript">
    function confirmlink(link, text)
    {
        return is_confirmed = confirm(text);
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
      <td style="text-align: center; vertical-align:top;" class="background">
        <p>
<?php
//hier hin verschoben da der IE iwie imemr sonst Mist baut ^^
include ('includes/sitterfadein.php');
?>
</p>
        <table width="100%" border="0" cellpadding="0" cellspacing="1" class="bordercolor">
          <tr> 
			
			
			<td class="titlebg" style="background-color: #000000; text-align: center;">
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
<?php	}
if ( ( $user_id <> "guest" ) && ( $user_rules == "1" ) ) {

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
} else {
?>
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="windowbg1" style="padding-left: 0; text-align: center;">
<?php 
}

if ( ( $user_password == "a338268847bac752d23c30b410570c2c" ) || 
     ( $user_password == "2f5a63d542da883a490dd61ef46fe2a9" ) ) 
  echo "<br><div class='system_notification'><b>*moep* Achtung! Ändere bitte dein Passwort im Profil. Danke.</b></div><br><br>";
  
if ( ( empty($user_sitterpwd) ) && ( $user_sitten == "1" ) ) 
  echo "<br><div class='system_notification'><b>*moep* Achtung! Du hast zwar anderen das Sitten erlaubt, aber kein Sitterpasswort eingetragen.</b></div><br><br>";

if ( ( $user_id <> "guest" ) && ( $user_rules == "1" ) ) {

	if ( file_exists("modules/" . $action . ".php") === TRUE ) include("modules/" . $action . ".php");
	if ( $action == 'memberlogin2' ) include("modules/" . $config_default_action . ".php");
	if ( $action == 'deluser' AND $user_status === "admin")	{

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
			
		$sql = "DELETE FROM " . $db_tb_user_research . " WHERE user='" . $sitterlogin . "'";
		$result = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

?>

<br><br>
<div class='doc_title'>Account löschen</div>
<br>
<div class='system_notification'>Account '<?php echo $sitterlogin;?>' gelöscht!</div>
<?php
	}
} elseif ( ( $user_id <> "guest" ) && ( $user_rules != "1" ) ) {
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
} else {
	if ( $action == 'password' ) {
        include("modules/password.php");
    } else {
        include("modules/login.php");
    }
}
    echo $error;
	if (!getVar("nobody")) { ?>
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
<?php	}?>
