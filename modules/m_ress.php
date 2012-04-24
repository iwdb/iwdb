<?php
/*****************************************************************************/
/* m_ress.php                                                                */
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

/*****************************************************************************/
/* Dieses Modul dient als Vorlage zum Erstellen von eigenen Zusatzmodulen    */
/* für die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der ursprünglichen DB ist ein Gemeinschaftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul über die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!";
	exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig für die Benennung der zugehörigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für
//    eine Installation über das Menü
//
$modulname  = "m_ress";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Produktion";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation
//    ausführen darf. Mögliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc =
  "Dieses Modul dient zur Anzeige der Ressproduktion der Spieler in der Allianz.".
  " Dabei wird anhand der Kolo-/Ressübersicht der Tagesbedarf bzw. ".
  " Tagesoutput errechnet.";


//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//
function workInstallDatabase() {
  global $db, $db_prefix, $db_tb_iwdbtabellen, $db_tb_parser;

  $sqlscript = array(
    "CREATE TABLE " . $db_prefix . "ressuebersicht( " . 
    "`user` varchar(50) NOT NULL default '', ".
    "`datum` int(11) default NULL,  " .
    "`eisen` float default NULL,  " .
    "`stahl` float default NULL,  " .
    "`vv4a` float default NULL,  ". 
    "`chem` float default NULL, " . 
    "`eis` float default NULL,  " . 
    "`wasser` float default NULL,  " .
    "`energie` float default NULL, " .
    "`fp_ph` float default NULL, " .
    "`credits` float default NULL, " .
    "`bev_a` float default NULL, " .
    "`bev_g` float default NULL, " .
    "`bev_q` float default NULL, " .
    "PRIMARY KEY  (`user`))",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('ressuebersicht')",

    "INSERT INTO " . $db_tb_parser . "(modulename,recognizer,message) VALUES " .
    "('production', 'Ressourcenkoloübersicht', 'Produktionsübersicht')"
  );

  foreach($sqlscript as $sql) {
    $result = $db->db_query($sql)
  	  or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
  }
 
  echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu() {
    global $modultitle, $modulstatus, $_POST;

		$actionparamters = "";
  	insertMenuItem( $_POST['menu'], $_POST['submenu'], $modultitle, $modulstatus, $actionparameters );
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
function workInstallConfigString() {
  return "";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//
function workUninstallDatabase() {
	global $db, $db_tb_ressuebersicht, $db_tb_parser;

  $sqlscript = array(
    "DROP TABLE " . $db_tb_ressuebersicht,

    "DELETE FROM " . $db_tb_iwdbtabellen . 
    " WHERE `name`='ressuebersicht'",

    "DELETE FROM " . $db_tb_parser . " WHERE modulename='production'"
  );

  foreach($sqlscript as $sql) {
    $result = $db->db_query($sql)
  	  or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
  }
 
  echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
}

//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgeführt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs:
//
//      http://Mein.server/iwdb/index.php?action=ress&was=install
//
// Anstatt "Mein.Server" nat�rlich deinen Server angeben und default
// durch den Dateinamen des Moduls ersetzen.
//
if( !empty($_REQUEST['was'])) {
  //  -> Nur der Admin darf Module installieren. (Meistens weiss er was er tut)
  if ( $user_status != "admin" )
		die('Hacking attempt...');

  echo "<div class='system_notification'>Installationsarbeiten am Modul " . $modulname .
	     " ("  . $_REQUEST['was'] . ")</div>\n";

  if (!@include("./includes/menu_fn.php"))
	  die( "Cannot load menu functions" );

  // Wenn ein Modul administriert wird, soll der Rest nicht mehr
  // ausgeführt werden.
  return;
}

if (!@include("./config/".$modulname.".cfg.php")) {
	die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

function make_link($order, $ordered) {
 global $sid;
 echo "<a href=\"index.php?action=m_ress&amp;order=" . $order . "&amp;ordered=" . $ordered .
      "&amp;sid=$sid\"> <img src=\"bilder/" . $ordered . ".gif\" border=\"0\" alt=\"" . $ordered . "\"> </a>";
}

//bestehende zeit holen

$sql = "SELECT
		switch
	FROM
		$db_tb_user
	WHERE
           id = '$user_id'";

$result = mysql_query($sql) OR die(mysql_error());

$row = mysql_fetch_assoc($result);

$switch = $row['switch'];

//zeit ändern?

echo '
<form action="index.php?action=m_ress&sid='.$sid.'" method="post">
	<p>Anzeigen der Produktion für <input type="text" name="switch" size="3"> Stunden <input type="submit" value="speichern" name="form" class="submit">
	</p>
</form>
';

if(isset($_POST['switch'])) {
$sql = "UPDATE
		$db_tb_user
	SET
		switch = '$_POST[switch]'
	WHERE
		id = '$user_id'";
mysql_query($sql) OR die(mysql_error());
}

//aktualisiere die Zeit

$sql = "SELECT
		switch
	FROM
		$db_tb_user
	WHERE
           id = '$user_id'";

$result = mysql_query($sql) OR die(mysql_error());

$row = mysql_fetch_assoc($result);

$switch = $row['switch'];

if (empty($switch) || $switch == 24) {
	$switch = 24;
	doc_title("Tagesproduktion/-Verbrauch");
} else doc_title("Verbrauch in ".$switch." Stunde(n)");
doc_title("sowie Bevölkerungsdaten");

echo "<br>";

start_table();

start_row("titlebg", "style=\"width:9%\" align=\"center\" nowrap=\"nowrap\"");
make_link("user", "asc");
echo "<b>User</b>";
make_link("user", "desc");
echo "<br>";
make_link("datum", "asc");
echo "<b>Zeit</b>";
make_link("datum", "desc");
           
next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("eisen", "asc");
echo "<b>Eisen</b>";
make_link("eisen", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("stahl", "asc");
echo "<b>Stahl</b>";
make_link("stahl", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("vv4a", "asc");
echo "<b>VV4A</b>";
make_link("vv4a", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("chem", "asc");
echo "<b>Chemie</b>";
make_link("chem", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("eis", "asc");
echo "<b>Eis</b>";
make_link("eis", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("wasser", "asc");
echo "<b>Wasser</b>";
make_link("wasser", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("energie", "asc");
echo "<b>Energie</b>";
make_link("energie", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("fp_ph", "asc");
echo "<b>FP</b>";
make_link("fp_ph", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("credits", "asc");
echo "<b>Credits</b>";
make_link("credits", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("bev_a", "asc");
echo "<b>Hartz IV</b>";
make_link("bev_a", "desc");

next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
make_link("bev_g", "asc");
echo "<b>Volk</b>";
make_link("bev_g", "desc");

next_cell("titlebg", "style=\"width:3%\" align=\"center\"");
make_link("bev_q", "asc");
echo "<b>Quote</b>";
make_link("bev_q", "desc");

$order  = getVar('order');
$ordered = getVar('ordered');

if(empty($order)) 
  $order='datum';
  
if(empty($ordered)) 
  $ordered='asc';

global $db, $db_tb_ressuebersicht, $config_sitter_timeformat;
  
// Anzeigen der Daten im Browser
$sql = "SELECT `datum` , `user` , `eisen` , `stahl` , `vv4a` , `chem` , `eis` ," .
       " `wasser` , `energie`, `fp_ph`, `credits`, `bev_a`, `bev_g`, `bev_q` FROM `" . $db_tb_ressuebersicht . "`";
if (!$user_fremdesitten)
{
	$sql .= " WHERE (SELECT allianz FROM " . $db_tb_user . " WHERE id=" . $db_tb_ressuebersicht . ".user) = '" . $user_allianz . "'";
}
$sql .= " ORDER BY `" . $order . "` " . $ordered;
$result = $db->db_query($sql)
  or error(GENERAL_ERROR, 
           'Could not query config information.', '', 
           __FILE__, __LINE__, $sql);


while($row = $db->db_fetch_array($result)) {
	$color = scanAge($row['datum']);

  next_row("windowbg1", "style=\"background-color:" . $color . "\" nowrap=\"nowrap\"");
  echo $row['user'] . "<br>";
  echo strftime("(%d.%m.%y %H:%M:%S)", $row['datum']);

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['eisen']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['stahl']*$switch, 0, ',', '.');
  
  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['vv4a']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['chem']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['eis']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['wasser']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['energie']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['fp_ph']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['credits']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['bev_a'], 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['bev_g'], 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['bev_q'], 2, ',', '.');
}

end_row();

// Gesamtanzeige
$sql = "SELECT sum(`eisen`) as eisen , sum(`stahl`) as stahl, sum(`vv4a`) as vv4a,".
       " sum(`chem`) as chem, sum(`eis`) as eis, sum(`wasser`) as wasser,".
       " sum(`energie`) as energie, sum(`fp_ph`) as fp_ph, sum(`credits`) as credits,".
       " sum(`bev_a`) as bev_a, sum(`bev_g`) as bev_g, sum(`bev_q`)/count(`bev_a`) as bev_q".
       " FROM " . $db_tb_ressuebersicht;
$result = $db->db_query($sql)
  or error(GENERAL_ERROR,
           'Could not query config information.', '', 
           __FILE__, __LINE__, $sql);
while($row = $db->db_fetch_array($result)) {
  next_row("titlebg", "align=\"center\" style=\"background-color:\$FFFFFF\"");
  echo "Gesamt:";
  
  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['eisen']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['stahl']*$switch, 0, ',', '.');
  
  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['vv4a']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['chem']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['eis']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['wasser']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['energie']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['fp_ph']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['credits']*$switch, 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['bev_a'], 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo number_format($row['bev_g'], 0, ',', '.');

  next_cell("windowbg1", "align=\"right\"");
  echo "&Oslash;". number_format($row['bev_q'], 2, ',', '.');
}
end_row();

end_table();

// 
// Erweiterung für Zusatz-Tabellen; sortiert nach Usern, die einem Fleeter zugeordnet sind
// 

global $db, $db_tb_ressuebersicht, $db_tb_user, $config_sitter_timeformat;

$sql2 = "SELECT
            us.id, us.sitterlogin, us.budflesol
        FROM
            ".$db_tb_user." as us
        WHERE
            us.budflesol = 'Fleeter'";

$result2 = $db->db_query($sql2)
  or error(GENERAL_ERROR, 
           'Could not query config information.', '', 
           __FILE__, __LINE__, $sql);

$fleeterlist = array();
    while ($row = mysql_fetch_assoc($result2)) {
        $fleeterlist[] = $row;
    };

/* echo "<PRE>";
print_r($fleeterlist);
echo "</PRE>"; */

function NumToStaatsform($num) {
    if ($num == 0) return '---';
    if ($num == 1) return 'Diktator';
    if ($num == 2) return 'Monarch';
    if ($num == 3) return 'Demokrat';
    if ($num == 4) return 'Kommunist';
}

foreach ($fleeterlist as $key => $value) {

    $fleetername = $value['id'];

    echo "\n\n<br><br>\n\n";
    
    start_table();

    start_row("titlebg", "align=\"center\" colspan=\"12\"");
    if ($fleetername == $value['sitterlogin']) {
        echo "<b>Fleeter: ".$fleetername."</b>";
    } else {
        echo "<b>Fleeter: ".$fleetername."<br>(ingamenick &laquo;".$value['sitterlogin']."&raquo;)</b>";
    }
    echo "<br>";

    next_row("titlebg", "style=\"width:9%\" align=\"center\" nowrap=\"nowrap\"");
    make_link("user", "asc");
    echo "<b>User</b>";
    make_link("user", "desc");
    echo "<br>";

    make_link("datum", "asc");
    echo "<b>Zeit</b>";
    make_link("datum", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    make_link("eisen", "asc");
    echo "<b>Eisen</b>";
    make_link("eisen", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    make_link("stahl", "asc");
    echo "<b>Stahl</b>";
    make_link("stahl", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    make_link("vv4a", "asc");
    echo "<b>VV4A</b>";
    make_link("vv4a", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    make_link("chem", "asc");
    echo "<b>Chemie</b>";
    make_link("chem", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    make_link("eis", "asc");
    echo "<b>Eis</b>";
    make_link("eis", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    make_link("wasser", "asc");
    echo "<b>Wasser</b>";
    make_link("wasser", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    make_link("energie", "asc");
    echo "<b>Energie</b>";
    make_link("energie", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    make_link("fp_ph", "asc");
    echo "<b>FP</b>";
    make_link("fp_ph", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    make_link("credits", "asc");
    echo "<b>Credits</b>";
    make_link("credits", "desc");

    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    echo "<b>Spieltyp</b>";
    
    next_cell("titlebg", "style=\"width:9%\" align=\"center\"");
    echo "<b>Staatsform</b>";

    // Anzeigen der Daten im Browser
    $sql3 = "SELECT
                ro.datum, ro.user, ro.eisen, ro.stahl, ro.vv4a, ro.chem, ro.eis, ro.wasser, 
                ro.energie, ro.fp_ph, ro.credits,
                us.sitterlogin, us.buddlerfrom, us.budflesol, us.staatsform
            FROM
                ".$db_tb_ressuebersicht." as ro, ".$db_tb_user." as us
            WHERE
                ro.user = us.sitterlogin AND us.buddlerfrom = '".$fleetername."'
            ORDER BY
                `" . $order . "` " . $ordered;

    $result3 = $db->db_query($sql3)
      or error(GENERAL_ERROR, 
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);

    while($row = $db->db_fetch_array($result3)) {
        $color = scanAge($row['datum']);

      next_row("windowbg1", "style=\"background-color:" . $color . "\" nowrap=\"nowrap\"");
      echo $row['user'] . "<br>";
      echo strftime("(%d.%m.%y<br>%H:%M:%S)", $row['datum']);
  
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['eisen']*$switch, 0, '.', ',');

      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['stahl']*$switch, 0, '.', ',');
  
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['vv4a']*$switch, 0, '.', ',');

      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['chem']*$switch, 0, '.', ',');

      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['eis']*$switch, 0, '.', ',');

      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['wasser']*$switch, 0, '.', ',');

      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['energie']*$switch, 0, '.', ',');
  
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['fp_ph']*$switch, 0, '.', ',');
  
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['credits']*$switch, 0, '.', ',');
  
      next_cell("windowbg1", "align=\"right\"");
      echo $row['budflesol'];
      
      next_cell("windowbg1", "align=\"right\"");
      echo NumToStaatsform($row['staatsform']);
    }

    end_row();
    
    // Gesamtanzeige
    $sql = "SELECT
                sum(ro.eisen) as eisen, sum(ro.stahl) as stahl, sum(ro.vv4a) as vv4a,
                sum(ro.chem) as chem, sum(ro.eis) as eis, sum(ro.wasser) as wasser,
                sum(ro.energie) as energie, sum(ro.fp_ph) as fp_ph,
                sum(ro.credits) as credits, ro.user, us.sitterlogin, us.buddlerfrom
            FROM
                ".$db_tb_ressuebersicht." as ro, ".$db_tb_user." as us
            WHERE
                ro.user = us.sitterlogin AND us.buddlerfrom = '".$fleetername."'
            GROUP BY
                us.buddlerfrom";
      
      $result = $db->db_query($sql)
      or error(GENERAL_ERROR,
               'Could not query config information.', '', 
               __FILE__, __LINE__, $sql);
    
    while($row = $db->db_fetch_array($result)) {
      next_row("titlebg", "align=\"center\" style=\"background-color:\$FFFFFF\" nowrap=\"nowrap\"");
      echo "Gesamt";
      
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['eisen']*$switch, 0, '.', ',');
    
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['stahl']*$switch, 0, '.', ',');
      
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['vv4a']*$switch, 0, '.', ',');
    
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['chem']*$switch, 0, '.', ',');
    
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['eis']*$switch, 0, '.', ',');
    
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['wasser']*$switch, 0, '.', ',');
    
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['energie']*$switch, 0, '.', ',');
      
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['fp_ph']*$switch, 0, '.', ',');
      
      next_cell("windowbg1", "align=\"right\"");
      echo number_format($row['credits']*$switch, 0, '.', ',');
      
      next_cell("titlebg", "align=\"center\" style=\"background-color:\$FFFFFF\" colspan=\"2\"");
      echo "Gesamt";
    }
    end_row();

    end_table();

}; //Ende vom foreach

?>
