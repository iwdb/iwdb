<?php
/*****************************************************************************
 * m_forsch.php                                                              *
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
 * Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  *
 * IW-Spielern.                                                              *
 *                                                                           *
 * Entwicklerforum/Repo:                                                     *
 *                                                                           *
 *        https://handels-gilde.org/?www/forum/index.php;board=1099.0        *
 *                   https://github.com/iwdb/iwdb                            *
 *                                                                           *
 *****************************************************************************/

//direktes Aufrufen verhindern
if (!defined('IRA')) {
    header('HTTP/1.1 403 forbidden');
    exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname = "m_forsch";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Forschungsübersicht";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation 
//    ausfuehren darf. Mögliche Werte:
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "admin";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menü-Übersicht angezeigt wird.
//
$moduldesc =
    "Die Forschungsübersicht zeigt die aktuell laufenden Forschungen";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase()
{
    //nothing here
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu()
{
    global $modultitle, $modulstatus, $_POST;

    $actionparameters = "";
    insertMenuItem($_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparameters);
    //
    // Weitere Wiederholungen für weitere Menü-Einträge, z.B.
    //
    // 	insertMenuItem( $_POST['menu'], ($_POST['submenu']+1), "Titel2", "hc", "&weissichnichtwas=1" );
    //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file.
//
function workInstallConfigString()
{
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//
function workUninstallDatabase()
{
    //nothing here
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgefuehrt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs: 
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" natürlich deinen Server angeben und default 
// durch den Dateinamen des Moduls ersetzen.
//
if (!empty($_REQUEST['was'])) {
    //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
    if ($user_status != "admin") {
        die('Hacking attempt...');
    }

    echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
        " (" . $_REQUEST['was'] . ")</div>\n";

    if (!@include("./includes/menu_fn.php")) {
        die("Cannot load menu functions");
    }

    // Wenn ein Modul administriert wird, soll der Rest nicht mehr
    // ausgeführt werden.
    return;
}

if (!@include("./config/" . $modulname . ".cfg.php")) {
    die("Error:<br><b>Cannot load " . $modulname . " - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

global $db, $db_tb_user_research, $db_tb_research, $db_tb_user;

doc_title('aktuell laufende Forschungen');

//$sql = "SELECT `user`, `rId`, `date`, `time` FROM `" . $db_tb_user_research . "` ORDER BY `date` ASC;";
$sql = "SELECT * FROM " . $db_tb_user_research . " LEFT JOIN " . $db_tb_user . " ON " . $db_tb_user_research . ".user = " . $db_tb_user . ".id WHERE " . $db_tb_user . ".sitten='1' ORDER BY date ASC;";
$result_user_research = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

$data = array();

?>
<table class='table_hovertable'>
	<tr>
		<th>
			User
		</th>
		<th>
			laufende Forschung
		</th>
		<th>
			Forschung endet
		</th>
		<th>
			Einlesezeitpunkt
		</th>
	</tr>
	<?php
	while ($row_user_research = $db->db_fetch_array($result_user_research)) {
		$sql = "SELECT `name` FROM `" . $db_tb_research . "` WHERE `id` ='" . $row_user_research['rId'] . "';";
		$result_research = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row_research = $db->db_fetch_array($result_research);

		if (!empty($row_user_research['date'])) {
			if (($row_user_research['date'] > $row_user_research['time']) && ($row_user_research['date'] > CURRENT_UNIX_TIME)) {
				$color = "#00FF00";
			} else {
				$color = "#FFA500";
			}
            $row_user_research['date'] = strftime(CONFIG_DATETIMEFORMAT, $row_user_research['date']);
		} else {
            $row_user_research['date'] = '';
			$color       = "#FF0000";
		}
	
	?>
	<tr>
		<td style="background-color: <?php echo $color ?>">
			<?php
			echo "<a href='index.php?action=sitterlogins&sitterlogin=" . urlencode($row_user_research['user']) . "' target='_blank'><img src='" . BILDER_PATH . "user-login.gif' alt='L' title='Einloggen'>";
			echo "&emsp;" . $row_user_research['user'];
			?>
		</td>
		<td>
			<?php
			echo $row_research['name'];
			?>
		</td>
		<td>
			<?php
			echo $row_user_research['date'];
			?>
		</td>
		<td>
			<?php
			echo strftime(CONFIG_DATETIMEFORMAT, $row_user_research['time']);
			?>
		</td>
	</tr>
	<?php
	}
	?>
</table>
<br>
<br>
<table class='table_format_noborder'>
	<tr>
		<td style="width: 3em; background-color: #00FF00;"></td>
		<td>
			= Status aktuell
		</td>
		<td style="width: 3em; background-color: #FF0000;"></td>
		<td>
			= es wird nicht geforscht
		</td>
		<td style="width: 3em; background-color: #FFA500;"></td>
		<td>
			= Startseite muss neu eingelesen werden
		</td>
	</tr>
</table>
<br>
<br>

<?php
doc_title('erforschte Forschungen eines Spielers anschauen');
?>
<form method="POST" action="" enctype="multipart/form-data"> 
<select name="spieler">
<option value ="">Spieler auswählen ...</option>
<?php
$sql = "SELECT id FROM " . $db_tb_user;
$result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
while($row = mysql_fetch_object($result)) { 
echo "<option>"; 
echo $row->id; 
echo "</option>";  
} 
?> 
</select><br><br> 
<input type="submit" name="formSubmit" value="und los" >
</form>
<br><br>

<script>
$(document).ready(function() 
    { 
        $("#myTable").tablesorter(); 
    } 
);
</script>

<?php
if(isset($_POST['formSubmit']) ) {
	
	echo "Bisher erforschte Forschungen von " . $_POST['spieler'] . " anschauen";
	
	$sql = "SELECT * FROM " . $db_tb_research2user . " WHERE userid = '" . $_POST['spieler'] . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		
	?>
	<table id='myTable' class='tablesorter' style='width:90%'>
	
	<thead>
		<tr>
			<th>
				<b>Forschungsname</b>
			</th>
		<tr>
	</thead>
	<tbody>
	<?php
	while ($row = $db->db_fetch_array($result)) {
		$sql = "SELECT name FROM " . $db_tb_research . " WHERE id ='" . $row['rid'] . "'";
		$result_forsch = $db->db_query($sql)
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
		$row1 = $db->db_fetch_array($result_forsch);

		?>
		<tr>
			<td>
				<?php
				echo $row1['name'];
				?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>
	</table>
<?php		
}
?>
<script src="javascript/jquery.tablesorter.min.js"></script>