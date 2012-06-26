<?php
/*****************************************************************************/
/* m_kasse.php                                                               */
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
/* f?r die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschaftsprojekt von */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete           */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iwdb.de.vu                                   */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul ?ber die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
    echo "Hacking attempt...!!"; 
    exit; 
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig f?r die Benennung der zugehoerigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung f?r 
//    eine Installation ?ber das Men?
//
$modulname  = "m_kasse";

//****************************************************************************
//
// -> Men?titel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Allianzkasse";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul ?ber die Navigation 
//    ausfuehren darf. Moegliche Werte:
//    - ""      <- nix = jeder, 
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Menue-Uebersicht angezeigt wird.
//
$moduldesc = 
  "Das Allianzkassenmodul dient zur Speicherung und übersichtlichen Anzeige von Daten aus der Allianzkasse";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//

function workInstallDatabase() {
/*
  global $db, $db_prefix, $db_tb_iwdbtabellen, $db_tb_parser;

  $sqlscript = array(
    "CREATE TABLE " . $db_prefix . "kasse_content
    (`amount` DECIMAL( 22,2 ) NOT NULL ,
     `allianz` VARCHAR( 50 ) NOT NULL ,
     `time_of_insert` DATE NOT NULL ,
      UNIQUE KEY ( `allianz`, `time_of_insert`)
        );",
    "CREATE TABLE " . $db_prefix . "kasse_incoming
    (`user` varchar( 30 ) NOT NULL,
     `amount` decimal( 22,2 ) NOT NULL,
     `time_of_insert` DATE NOT NULL,
     `allianz` varchar( 50 ) NOT NULL,
      UNIQUE KEY ( `user`,  `time_of_insert` )        
        );",
    "CREATE TABLE " . $db_prefix . "kasse_outgoing
    (`payedfrom` varchar( 30 ) NOT NULL,
     `payedto` varchar( 30 ) NOT NULL,
     `amount` bigint unsigned NOT NULL,
     `time_of_pay` datetime NOT NULL,
     `allianz` varchar( 50 ) NOT NULL,
      UNIQUE KEY ( `payedfrom`,  `payedto`, `amount`, `time_of_pay`)        
        );",
    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('kasse_incoming')",
    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('kasse_content')",
    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('kasse_outgoing')",
    "INSERT INTO " . $db_tb_parser . "(`modulename`, `recognizer`,`message`)" .
    " VALUES('kasse', 'Standardbeitrag', 'Allianzkasse')"
  );

  foreach($sqlscript as $sql) {
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
  }
  echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>OK</b></div>";
*/
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all menu entries needed for
// installing this module. This function is called by the installation method
// in the included file includes/menu_fn.php
//
function workInstallMenu() {
    global $modultitle, $modulstatus;

    $menu    = getVar('menu');
    $submenu = getVar('submenu');

        $actionparamters = "";
      insertMenuItem( $menu, $submenu, $modultitle, $modulstatus, $actionparameters );
      //
      // Weitere Wiederholungen f?r weitere Menue-Eintraege, z.B.
      //
      //     insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
      //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file
//
function workInstallConfigString() {
/*  global $config_gameversion;
  return
    "\$v04 = \" <div class=\\\"doc_lightred\\\">(V " . $config_gameversion . ")</div>\";";
*/}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module. 
//
/*
function workUninstallDatabase() {
  global $db, $db_tb_iwdbtabellen, $db_tb_kasse_content, $db_tb_kasse_incoming, $db_tb_kasse_outgoing, $db_tb_parser;

  $sqlscript = array(
    "DROP TABLE " . $db_tb_kasse_content . ";",
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='kasse_content';",
    "DROP TABLE " . $db_tb_kasse_incoming . ";",
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='kasse_incoming';",
    "DROP TABLE " . $db_tb_kasse_outgoing . ";",
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='kasse_outgoing';",
    "DELETE FROM " . $db_tb_parser . " WHERE modulename='kasse';"
  );

  foreach($sqlscript as $sql) {
    $result = $db->db_query($sql)
        or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
  }
  echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
}
*/
//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgefuehrt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs: 
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
//
// Anstatt "Mein.Server" nat?rlich deinen Server angeben und default 
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
  // ausgefuehrt werden.
  return;
}

if (!@include("./config/".$modulname.".cfg.php")) { 
    die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

//****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul
 
  //getvariablen organisieren

  $type = getVar('type') ? getVar('type') : 'payedto';
  $order = "";
  if (getVar ('order') && getVar('ordered')) {
    $order = "ORDER BY " . getVar('order') . " " . getVar('ordered');
  }
  
  if (getVar('fromday') && getVar('fromyear') && getVar('frommonth')) {
    $fromday = sprintf('%02d', getVar('fromday'));
    $frommonth = sprintf('%02d', getVar('frommonth'));
    $fromyear = sprintf('%04d', getVar('fromyear'));
    $fromdate=$fromyear . "-" . $frommonth . "-" . $fromday;
  } else {
    $fromday = '10';
    $frommonth = '03';
    $fromyear = '2007';
  }
  if (getVar('today') && getVar('toyear') && getVar('tomonth')) {
    $today = sprintf('%02d', getVar('today'));
    $tomonth = sprintf('%02d', getVar('tomonth'));
    $toyear = sprintf('%02d', getVar('toyear'));
    $todate= $toyear . "-" . $tomonth . "-" . $today;
  } else {
    $heute = getdate();
    $today = sprintf('%02d', $heute['mday']);
    $tomonth = sprintf('%02d',  $heute['mon']);
    $toyear = $heute['year'];
  }

  global $db_tb_user , $user_id;
    //Allianz des Users herausfinden
  $sql = "SELECT allianz FROM " . $db_tb_user . " WHERE id = '" . $user_id . "'";
  $result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
  while($row = $db->db_fetch_array($result)) 
  {
    $allianz = $row['allianz'];
  }
  if ( strtolower($user_status) == 'admin' && getVar('allianz') ) {
    $allianz=getVar('allianz');
    $allianz=urldecode($allianz);
  }
      
      
  //url fuers sortieren wieder zusammensetzen
  $url = "index.php?action=m_kasse&sid=$sid&type=$type&today=$today&tomonth=$tomonth&toyear=$toyear&fromday=$fromday&frommonth=$frommonth&fromyear=$fromyear&allianz=$allianz";

     doc_title("Allianzkasse");
     
	 $sql = "SELECT MAX(time_of_insert) AS TOI FROM " . $db_tb_kasse_content;
		$result = $db->db_query($sql)	
			or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);

		$lastreport = "";

	if($row = $db->db_fetch_array($result)) {
		
		
		$time1=strtotime($row['TOI']);
		$lastreport = strftime("%d.%m.%y %H:%M", $time1);
		echo "zuletzt aktualisiert am : " . $lastreport;
	}
	 
  //inputform basteln
     echo "<div class='doc_centered'>\n";
     echo "<form name=\"frm\">\n";

     
    if ( strtolower($user_status) == 'admin' ) {
      $ally=Array();
      $sql= "SELECT DISTINCT allianz FROM $db_tb_kasse_content";
      $result = $db->db_query($sql)
    or error(GENERAL_ERROR, 'Could not query config information.', '', __FILE__, __LINE__, $sql);
      while($row = $db->db_fetch_array($result)) 
      {
        $ally[] = $row['allianz'];
      }
      if (count($ally)>1) {
        echo "<p><SELECT NAME=\"allianz\" size=1>\n";
    foreach($ally as $alli) {
      echo "<OPTION VALUE=\"".urlencode($alli)."\"";
      if ($allianz == $alli) { echo " selected=\"selected\""; }
          echo ">\n";
          echo $alli;
      
    }
    echo "</select></p>\n";
      }
    }
     
     echo "<input type=\"hidden\" name=\"sid\" value=\"" . $sid . "\">\n";
     echo "<input type=\"hidden\" name=\"action\" value=\"" . $modulname . "\">\n";
     echo "<p>";
     echo "<SELECT NAME=\"type\" size=1>\n";
     echo "<OPTION VALUE=\"content\"\n";
     if ($type=='content') { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Kasseninhalt";

     echo "<OPTION VALUE=\"payedto\"";
     if ($type=='payedto') { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Wer hat Credits bekommen?";

     echo "<OPTION VALUE=\"payedfrom\"";
     if ($type=='payedfrom') { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Wer hat Credits ausgezahlt?";

     echo "<OPTION VALUE=\"payedfromto\"";
     if ($type=='payedfromto') { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Wer hat von wem Credits ausgezahlt bekommen?";

     echo "<OPTION VALUE=\"incoming\"";
     if ($type=='incoming') { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Wer hat Credits eingezahlt?";

     echo "</select>\n&nbsp;&nbsp;<input type=\"submit\" value=\"anzeigen\">";
     echo "</p>\n<p>";
     echo "Zeitraum vom <input type=\"text\" name=\"fromday\" size=\"2\" maxlength=\"2\" value=\"" . $fromday . "\">";
     echo ". <input type=\"text\" name=\"frommonth\" maxlength=\"2\" size=\"2\" value=\"" .$frommonth . "\">";
     echo ". <input type=\"text\" name=\"fromyear\" maxlength=\"4\" size=\"4\" value=\"" . $fromyear . "\">";
     echo "bis zum <input type=\"text\" name=\"today\" maxlength=\"2\" size=\"2\" value=\"" . $today . "\">";
     echo ". <input type=\"text\" name=\"tomonth\" maxlength=\"2\" size=\"2\" value=\"" . $tomonth . "\">";
     echo ". <input type=\"text\" name=\"toyear\" maxlength=\"4\" size=\"4\" value=\"" . $toyear . "\"></p>";
     
     echo "</form>\n";
     echo "</div>\n";

  

  
  //allykassendaten abfragen und ausgeben
  
  if ($type == 'payedto') { //ausrechnen, was jeder member so bekommen hat

    $whereclause = "AND ";
    if (isset($fromdate)) {
      $whereclause.="time_of_pay >= '" . $fromdate . " 00:00:00' AND ";
    }
    if (isset($todate)) {
      $whereclause.="time_of_pay <= '" . $todate . " 23:59:59' AND ";
    }
    $whereclause.="1";

     echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 30em;\">";
     start_row("titlebg", "style=\"width:40%\" align=\"center\" colspan=\"2\"");
     echo "  <b>Wer hat Credits bekommen?</b>\n";
     next_row("windowbg2", "style=\"width:60%\" align=\"center\"");
     echo "<a href=\"$url&order=payedto&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
     echo "Empfänger";
     echo "<a href=\"$url&order=payedto&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";
     next_cell("windowbg2", "align=\"center\"");
     echo "<a href=\"$url&order=sumof&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
     echo "Summe der ausgezahlten Credits";
     echo "<a href=\"$url&order=sumof&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";
    
    $sql = "SELECT payedto, sum(amount) as sumof FROM " . $db_tb_kasse_outgoing . " WHERE allianz='" . $allianz . "' " . $whereclause . " GROUP BY payedto " . $order;
    $result = $db->db_query($sql)
             or error(GENERAL_ERROR,
                 'Could not query config information.', '',
                 __FILE__, __LINE__, $sql);

     while( $row = $db->db_fetch_array($result)) {
       next_row("windowbg1", "style=\"width:60%\" align=\"left\"");
       echo $row['payedto'];
       next_cell("windowbg1", "align=\"right\"");
       echo number_format($row['sumof'], 0, ',', '.');
     }
     end_row();
     end_table();

     
  } else if ($type == 'payedfrom') { //ausrechnen, was die auszahler so ausbezahlt haben

    $whereclause = "AND ";
    if (isset($fromdate)) {
      $whereclause.="time_of_pay >= '" . $fromdate . " 00:00:00' AND ";
    }
    if (isset($todate)) {
      $whereclause.="time_of_pay <= '" . $todate . " 23:59:59' AND ";
    }
    $whereclause.="1";

     echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 30em;\">";
     start_row("titlebg", "style=\"width:40%\" align=\"center\" colspan=\"2\"");
     echo "  <b>Wer hat Credits ausgezahlt?</b>\n";
     next_row("windowbg2", "style=\"width:60%\" align=\"center\"");
     echo "<a href=\"$url&order=payedfrom&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
     echo "Auszahlender";
     echo "<a href=\"$url&order=payedfrom&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";
     next_cell("windowbg2", "align=\"center\"");
     echo "<a href=\"$url&order=sumof&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
     echo "Summe der ausgezahlten Credits";
     echo "<a href=\"$url&order=sumof&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";
    
      $sql = "SELECT payedfrom, sum(amount) as sumof FROM " . $db_tb_kasse_outgoing . " WHERE allianz='" . $allianz . "' " . $whereclause . " GROUP BY payedfrom " . $order;
    $result = $db->db_query($sql)
             or error(GENERAL_ERROR,
                 'Could not query config information.', '',
                 __FILE__, __LINE__, $sql);

     while( $row = $db->db_fetch_array($result)) {

         next_row("windowbg1", "style=\"width:60%\" align=\"left\"");
         echo $row['payedfrom'];
         next_cell("windowbg1", "align=\"right\"");
         echo number_format($row['sumof'], 0, ',', '.');
     }
     end_row();
     end_table();



    
  } else if ($type == 'payedfromto') { //ausrechnen, was die auszahler an jeden member ausbezahlt haben

    $whereclause = "AND ";
    if (isset($fromdate)) {
      $whereclause.="time_of_pay >= '" . $fromdate . " 00:00:00' AND ";
    }
    if (isset($todate)) {
      $whereclause.="time_of_pay <= '" . $todate . " 23:59:59' AND ";
    }
    $whereclause.="1";
  
     start_table();
     start_row("titlebg", "style=\"width:40%\" align=\"center\" colspan=\"3\"");
     echo "  <b>Wer hat Credits bekommen?</b>\n";
     next_row("windowbg2", "style=\"width:40%\" align=\"center\"");
     echo "<a href=\"$url&rder=payedfrom&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
     echo "Auszahlender";
     echo "<a href=\"$url&order=payedfrom&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";
     next_cell("windowbg2", "style=\"width:40%\" align=\"center\"");
     echo "<a href=\"$url&order=payedto&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
     echo "Empfänger";
     echo "<a href=\"$url&order=payedto&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";;
     next_cell("windowbg2", "align=\"center\"");
     echo "<a href=\"$url&order=sumof&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
     echo "Summe der ausgezahlten Credits";
     echo "<a href=\"$url&order=sumof&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";
    
    
    $sql = "SELECT payedfrom, payedto, sum(amount) as sumof FROM " . $db_tb_kasse_outgoing . " WHERE allianz='$allianz' $whereclause GROUP BY payedfrom, payedto $order";
    $result = $db->db_query($sql)
             or error(GENERAL_ERROR,
                 'Could not query config information.', '',
                 __FILE__, __LINE__, $sql);

     while( $row = $db->db_fetch_array($result)) {
     next_row("windowbg1", "style=\"width:40%\" align=\"left\"");
     echo $row['payedfrom'];
     next_cell("windowbg1", "style=\"width:40%\" align=\"left\"");
     echo $row['payedto'];
     next_cell("windowbg1", "align=\"right\"");
     echo number_format($row['sumof'], 0, ',', '.');
     }
     end_row();
     end_table();

     
  } else if ($type == 'content') { //anzeigen, wie viel wann in der kasse war

  
    $whereclause = "AND ";
    if (isset($fromdate)) {
      $whereclause.="time_of_insert >= '" . $fromdate . " 00:00:00' AND ";
    }
    if (isset($todate)) {
      $whereclause.="time_of_insert <= '" . $todate . " 23:59:59' AND ";
    }
    $whereclause.="1";
    
    echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 30em;\">";
    start_row("titlebg", "style=\"width:40%\" align=\"center\" colspan=\"3\"");
    echo "  <b>Kasseninhalt</b>\n";
    next_row("windowbg2", "style=\"width:40%\" align=\"center\"");
    echo "<a href=\"$url&order=time_of_insert&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
    echo "Datum";
    echo "<a href=\"$url&order=time_of_insert&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";
    next_cell("windowbg2", "align=\"center\"");
    echo "<a href=\"$url&order=amount&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
    echo "Inhalt der Allianzkasse";
    echo "<a href=\"$url&order=amount&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";

    $sql = "SELECT amount, time_of_insert FROM " . $db_tb_kasse_content . " WHERE allianz='$allianz' $whereclause ORDER BY time_of_insert ASC";
    $result = $db->db_query($sql)
             or error(GENERAL_ERROR,
                 'Could not query config information.', '',
                 __FILE__, __LINE__, $sql);

     while( $row = $db->db_fetch_array($result)) {
		$time=strtotime($row['time_of_insert']);
		$time1 = strftime("%d.%m.%y %H:%M", $time);
        next_row("windowbg1", "style=\"width:50%\" align=\"left\"");
        
		 echo $time1;
		 next_cell("windowbg1", "align=\"right\"");
         echo number_format($row['amount'], 2, ',', '.');
     }
     end_row();
     end_table();
     
  } else if ($type == 'incoming') { //anzeigen, wer wie viel eingezahlt hat

  
    $whereclause = "AND ";
    if (isset($fromdate)) {
      $whereclause.="time_of_insert >= '" . $fromdate . "' AND ";
    }
    if (isset($todate)) {
      $whereclause.="time_of_insert <= '" . $todate . "' AND ";
    }
    $whereclause.="1";
  
    echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 30em;\">";
    start_row("titlebg", "style=\"width:40%\" align=\"center\" colspan=\"3\"");
    echo "  <b>Wer hat Credits bekommen?</b>\n";
    next_row("windowbg2", "style=\"width:40%\" align=\"center\"");
    echo "<a href=\"$url&order=user&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
    echo "Einzahler";
    echo "<a href=\"$url&order=user&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";
    next_cell("windowbg2", "align=\"center\"");
    echo "<a href=\"$url&order=sumof&ordered=asc\"> <img src=\"bilder/asc.gif\" border=\"0\" alt=\"asc\"> </a>";
    echo "Summe der eingezahlten Credits";
    echo "<a href=\"$url&order=sumof&ordered=desc\"> <img src=\"bilder/desc.gif\" border=\"0\" alt=\"desc\"> </a>";
  
    $sql = "SELECT user, sum(amount) as sumof FROM " . $db_tb_kasse_incoming . " WHERE allianz='$allianz' $whereclause GROUP BY user $order";
    $result = $db->db_query($sql)
             or error(GENERAL_ERROR,
                 'Could not query config information.', '',
                 __FILE__, __LINE__, $sql);

     while( $row = $db->db_fetch_array($result)) {
         next_row("windowbg1", "style=\"width:60%\" align=\"left\"");
         echo $row['user'];
         next_cell("windowbg1", "align=\"right\"");
         echo number_format($row['sumof'], 2, ',', '.');
     }
     end_row();
     end_table();
  }
    
?>


