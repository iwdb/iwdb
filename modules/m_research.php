<?php
/*****************************************************************************/
/* m_research.php                                                            */
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
/* f�r die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspr�nglichen DB ist ein Gemeinschaftsprojekt von  */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens daf�r eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iw-smf.pericolini.de                         */
/*                                                                           */
/*****************************************************************************/

// -> Abfrage ob dieses Modul �ber die index.php aufgerufen wurde.
//    Kann unberechtigte Systemzugriffe verhindern.
if (basename($_SERVER['PHP_SELF']) != "index.php") {
	echo "Hacking attempt...!!";
	exit;
}

//****************************************************************************
//
// -> Name des Moduls, ist notwendig f�r die Benennung der zugeh�rigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung f�r
//    eine Installation �ber das Men�
//
global $modulname;
$modulname = "m_research";

//****************************************************************************
//
// -> Men�titel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "dyn. Techtree";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul �ber die Navigation
//    ausf�hren darf. M�gliche Werte:
//    - ""      <- nix = jeder,
//    - "admin" <- na wer wohl
//
$modulstatus = "";

//****************************************************************************
//
// -> Beschreibung des Moduls, wie es in der Men�-�bersicht angezeigt wird.
//
$moduldesc =
  "Das Forschungsmodul erlaubt die Darstellung und das Navigieren innerhalb " .
  "des bereits bekannten Forschungsbaumes. Die notwendigen Forschungen und " .
  "Gebäude werden ebenfalls dargestellt.";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module.
//

function workInstallDatabase() {
	global $db, $db_prefix, $db_tb_iwdbtabellen, $db_tb_parser,
         $db_tb_sitterauftrag, $config_gameversion;
/*
  $sqlscript = array(
    "CREATE TABLE " . $db_prefix . "building2building (" .
    "  bOld int(10) unsigned NOT NULL default '0'," .
    "  bNew int(10) unsigned NOT NULL default '0'," .
    "  PRIMARY KEY (bOld,bNew)" .
    ") COMMENT='Gebaeude bOld ermoeglicht Gebaeude bNew'",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('building2building')",

    "CREATE TABLE " . $db_prefix . "building2research (" .
    "  bId int(10) unsigned NOT NULL default '0'," .
    "  rId int(10) unsigned NOT NULL default '0'," .
    "  PRIMARY KEY  (bId,rId)" .
    ") COMMENT='Gebaeude bId ermoeglicht Forschung rId'",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('building2research')",

    "CREATE TABLE " . $db_prefix . "research (" .
    "  ID int(10) unsigned NOT NULL auto_increment," .
    "  `name` varchar(250) NOT NULL default ''," .
    "  description text," .
    "  FP int(10) unsigned NOT NULL default '0'," .
    "  gebiet int(10) unsigned NOT NULL default '0'," .
    "  highscore int(10) unsigned NOT NULL default '0'," .
    "  addcost text," .
    "  geblevels text," .
    "  declarations text," .
    "  defense text," .
    "  objects text," .
    "  genetics text," .
    "  rlevel int(10) unsigned NOT NULL default '0'," .
    "  gameversion varchar(10) NOT NULL default '" . $config_gameversion . "'," .
    "  PRIMARY KEY  (ID)," .
    "  UNIQUE KEY `name` (`name`)" .
    ") COMMENT='Forschungsinformation fuer Forschung Id'",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('research')",

    "CREATE TABLE " . $db_prefix . "research2building (" .
    "  rId int(10) unsigned NOT NULL default '0'," .
    "  bId int(10) unsigned NOT NULL default '0'," .
    "  lvl int(10) unsigned NOT NULL default '0'," .
    "  PRIMARY KEY  (rId,bId,lvl)" .
    ") COMMENT='Forschung rId ermoeglicht Gebaeude(stufe) bId'",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('research2building')",

    "CREATE TABLE " . $db_prefix . "research2prototype (" .
    "  rid int(10) unsigned NOT NULL default '0'," .
    "  pid int(10) unsigned NOT NULL default '0'," .
    "  PRIMARY KEY  (rid,pid)" .
    ") COMMENT='Forschung rId ermoeglicht Prototyp pId'",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('research2prototype')",

    "CREATE TABLE " . $db_prefix . "research2research (" .
    "  rOld int(10) unsigned NOT NULL default '0'," .
    "  rNew int(10) unsigned NOT NULL default '0'," .
    "  PRIMARY KEY  (rOld,rNew)" .
    ") COMMENT='Forschung rOld ermoeglicht Forschung rNew'",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('research2research')",

    "CREATE TABLE " . $db_prefix . "research2user (" .
    "  rid int(10) unsigned NOT NULL default '0'," .
    "  userid varchar(30) NOT NULL default '0'," .
    "  PRIMARY KEY  (rid,userid)" .
    ") COMMENT='bereits erforschte Forschungen des Benutzers'",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('research2user')",

    "CREATE TABLE " . $db_prefix . "researchfield (" .
    "  id int(10) unsigned NOT NULL auto_increment," .
    "  `name` varchar(50) NOT NULL default ''," .
    "  PRIMARY KEY  (id)" .
    ") COMMENT='Forschungsfelder'",

    "INSERT INTO " . $db_tb_iwdbtabellen . "(`name`)" .
    " VALUES('researchfield')",

    "INSERT INTO " . $db_prefix . "researchfield (id, name) VALUES " .
    "(0,'noch unbekannt')," .
    "(1,'---')," .
    "(2,'Chemie')," .
    "(3,'Ethik')," .
    "(4,'Evolution')," .
    "(5,'Industrie')," .
    "(6,'Informatik')," .
    "(7,'Kolonisation')," .
    "(8,'Militär')," .
    "(9,'Physik')," .
    "(10,'Prototypen')," .
    "(11,'Raumfahrt')," .
    "(12,'Unifragen')," .
    "(13,'Wirtschaft')",

    "INSERT INTO " . $db_prefix . "research(id, name, description, gebiet) VALUES " .
    "(1, 'Basiswissen', 'Ist halt Basiswissen, ohne das lassen sich einige " .
    "Geb�ude einfach nicht bauen.', 1)",

    "INSERT INTO " . $db_prefix . "research2building(rId,bId,lvl) VALUES " .
    "(1, " . find_the_building_id("Hilfskiste Eis auspacken") . ", 0)," .
    "(1, " . find_the_building_id("Hilfskiste Eisen auspacken") . ", 0)," .
    "(1, " . find_the_building_id("Hilfskiste Energie auspacken") . ", 0)," .
    "(1, " . find_the_building_id("Hilfskiste Stahl auspacken") . ", 0)," .
    "(1, " . find_the_building_id("Hilfskiste Wasser auspacken") . ", 0)," .
    "(1, " . find_the_building_id("kleine Eisenmine") . ", 0)," .
    "(1, " . find_the_building_id("kleines Forschungslabor") . ", 0)," .
    "(1, " . find_the_building_id("kleines Haus") . ", 0)," .
    "(1, " . find_the_building_id("kleines Stahlwerk") . ", 0)," .
    "(1, " . find_the_building_id("Zelt") . ", 0)",

    "INSERT INTO " . $db_tb_parser . "(modulename,recognizer,message) VALUES " .
    "('research', 'Forschungsinfo: ', 'Forschungsbericht')," .
    "('researchoverview', 'Erforschte Forschungen', 'Forschungsliste')",
    
    "ALTER TABLE " . $db_tb_sitterauftrag . " ADD resid INT DEFAULT '0' NOT NULL"
  );*/

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
    global $modultitle, $modulstatus;

    $menu    = getVar('menu');
    $submenu = getVar('submenu');

		$actionparamters = "";
  	insertMenuItem( $menu, $submenu, $modultitle, $modulstatus, $actionparameters );
	  //
	  // Weitere Wiederholungen f�r weitere Men�-Eintr�ge, z.B.
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
  global $config_gameversion;

  return
    "\$v04 = \" <font color=\\\"#ff4466\\\">(V " . $config_gameversion . ")</font>\";";
}

//****************************************************************************
//
// Function workUninstallDatabase is creating all database entries needed for
// removing this module.
//

function workUninstallDatabase() {
  global $db, $db_tb_iwdbtabellen, $db_tb_parser, $db_tb_sitterauftrag,
         $db_tb_building2building, $db_tb_building2research,
         $db_tb_research2research, $db_tb_research2building,
         $db_tb_research2prototype, $db_tb_research,
         $db_tb_research2user,     $db_tb_researchfield;
/*
  $sqlscript = array(
    "DROP TABLE " . $db_tb_building2building,
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='building2building'",

    "DROP TABLE " . $db_tb_building2research,
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='building2research'",

    "DROP TABLE " . $db_tb_research,
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='research'",

    "DROP TABLE " . $db_tb_research2building,
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='research2building'",

    "DROP TABLE " . $db_tb_research2prototype,
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='research2prototype'",

    "DROP TABLE " . $db_tb_research2research,
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='research2research'",

    "DROP TABLE " . $db_tb_research2user,
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='research2user'",

    "DROP TABLE " . $db_tb_researchfield,
    "DELETE FROM " . $db_tb_iwdbtabellen . " WHERE name='researchfield'",

    "DELETE FROM " . $db_tb_parser . " WHERE modulename='research'" .
    " OR modulename='researchoverview'",
    
    "ALTER TABLE " . $db_tb_sitterauftrag . " DROP COLUMN resid"
  );*/

  foreach($sqlscript as $sql) {
    $result = $db->db_query($sql)
  	  or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql);
  }

  echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>OK</b></div>";
}

// ****************************************************************************
//
//
function find_the_building_id($name) {
	global $db, $db_tb_gebaeude;

  // Aenderungen f�r Gebaeude, die bereits unter anderem Namen in der DB sind.
	if(strpos($name, "Glace") > 0) {
	  $name = "Eiscrusher der Sirius Corp, Typ Glace la mine";
	}

	$name = str_replace("(Kampfbasis)", "", $name);
	$name = str_replace(" mittels Phasenfluxdehydrierung", "", $name);
	$name = str_replace("kleiner chemischer Fabrikkomplex", "Chemiekomplex", $name);
	$name = str_replace("Katzeundmausabwehrstockproduktionsfabrik",
                      "Katze und Maus Stock Abwehrfabrik", $name);

	// Find first building identifier
	$sql = "SELECT ID FROM " . $db_tb_gebaeude . " WHERE name='" . $name . "'";
	$result = $db->db_query($sql)
		or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
	$row = $db->db_fetch_array($result);

	// Not found, so insert new
	if(empty($row)) {
		$sql2 = "INSERT INTO " . $db_tb_gebaeude . "(name) VALUES('" . $name . "')";
  	$result = $db->db_query($sql2)
  		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql2);

  	$result = $db->db_query($sql)
  		or error(GENERAL_ERROR,
               'Could not query config information.', '',
               __FILE__, __LINE__, $sql2);
		$row = $db->db_fetch_array($result);
	}

  return $row['ID'];
}


//****************************************************************************
//
// Installationsroutine
//
// Dieser Abschnitt wird nur ausgef�hrt wenn das Modul mit dem Parameter
// "install" aufgerufen wurde. Beispiel des Aufrufs:
//
//      http://Mein.server/iwdb/index.php?action=default&was=install
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
  // ausgef�hrt werden.
  return;
}

if (!@include("./config/".$modulname.".cfg.php")) {
	die( "Error:<br><b>Cannot load ".$modulname." - configuration!</b>");
}

// ****************************************************************************
//
// -> Und hier beginnt das eigentliche Modul

// Globale Definitionen, da diese Datei vom Scanner-Modul eingebunden
// werden kann. Fehlen dann diese Definitionen, koennen die Daten nicht
// gelesen werden.

global $sid,
       $db,
       $researchid,
       $db_tb_research,
       $db_tb_user,
       $user_id,
       $config_gameversion,
       $v04;

$resid = getVar('researchid');
$resid = ( empty($resid) ) ? ( empty($researchid) ? "1" : $researchid ): $resid;

$alphaorder = getVar('alphaorder');
$alphaorder = ( empty($alphaorder) ? "0" : $alphaorder);

$unknownonly = getVar('unknownonly');
$unknownonly = ( empty($unknownonly) ? "0" : $unknownonly);

$neededfpforresearch = 0;

echo "<div class='doc_title'>Forschungsübersicht</div>\n";
echo "<form name=\"Formular\" method=\"POST\" action=\"index.php?action=" . $modulname .
     "&amp;sid=" . $sid . "\" enctype=\"multipart/form-data\" onsubmit=\"return false\">\n";
echo "<select onchange=\"document.Formular.submit();\" name=\"researchid\" style=\"width: 400px;\">\n";
echo fill_selection($resid);
echo "</optgroup>\n";
echo "</select><br><br>\n";
echo "<table><tr><td>";
echo " <input type=\"checkbox\" name=\"alphaorder\"";
if($alphaorder == "on")
  echo "checked ";
echo "/>\n";
echo "Forschungsliste alphabetisch anzeigen<br><br>\n";
echo " <input type=\"checkbox\" name=\"unknownonly\"";
if($unknownonly == "on")
  echo "checked ";
echo "/>\n";
echo "nur noch nicht erforschte Forschungen anzeigen";
echo "</td><td valign=\"top\">";
echo " <input type=\"submit\" onclick=\"document.Formular.submit();\" name=\"ok\" value=\"Anzeigen\" />\n";
echo "</td></tr></table>";
echo " <input type=\"button\" onclick=\"dblclick()\" name=\"ssuchen\" value=\"Suche:\" /><br>\n";
//Gebaeude auslesen
global $db_tb_research2building,$db_tb_gebaeude;
$sql = "SELECT t2.name AS bname, t1.rID AS resid FROM " .
          $db_tb_research2building .
 	       " AS t1 INNER JOIN " . $db_tb_gebaeude .
 	       " AS t2 ON t1.bId=t2.id WHERE t1.lvl = 0";
$result = $db->db_query($sql);
echo "<select name=\"searchbuildings\" style=\"width: 400px;display:none;\">\n";
while ($row = $db->db_fetch_array($result)){
	echo "<option value=\"".$row['resid']."\">".$row['bname']."</option>";
}
echo "</select>";
echo '<input name="suche" style="width:400px" onkeyup="livesearch(document.Formular.suche.value,event.keyCode)">';
?>
  <script type="text/javascript">
  <!--
  function dblclick()
  {
  	   	if (document.forms.Formular.elements.searchselect.size == 3 || document.forms.Formular.elements.searchselect.size == 2 ) document.forms.Formular.elements.searchselect.selectedIndex = 0;
     var sel = document.forms.Formular.elements.searchselect.selectedIndex;
     if (document.forms.Formular.elements.searchselect.options[sel].value != "") {
     	 var val = document.forms.Formular.elements.searchselect.options[sel].value;
     	 var txt = document.forms.Formular.elements.searchselect.options[sel].text;
     	 document.Formular.suche.value = "+call:"+val+":("+txt+")+";    	 
     	 document.Formular.submit();
     }	
     
  }
  function livesearch(text,key)
  {
   var zwischen;   //speichert Inhalt der Zelle
   var result;    //speichert, ob ein Treffer gefunden wurde
   result=0;
   ergebnisse=0;
   
   text = text.toLowerCase();
   
   if (key == 38) {
     if(document.forms.Formular.elements.searchselect.size > 2) {
     	 var sel = document.forms.Formular.elements.searchselect.selectedIndex;
     	 if (sel != 0) {
     	 	 document.forms.Formular.elements.searchselect.options[sel-1].selected = true;
     	 }	 
     }   	
   } else {
   if (key == 40) {
     if(document.forms.Formular.elements.searchselect.size > 2) {
     	 var sel = document.forms.Formular.elements.searchselect.selectedIndex;
     	 if (sel != document.forms.Formular.elements.searchselect.size) {
     	 	 document.forms.Formular.elements.searchselect.options[sel+1].selected = true;
     	 }	 
     }   	
   } else {
   if (key == 11 || key == 13) {  
     dblclick(); 
   } else {   
   for (var i=document.forms.Formular.elements.searchselect.length; i > 0; i--) {
   	 document.forms.Formular.elements.searchselect.options[i-1] = null;
   }	
   
   isresearch = true;
   isbuilding = true;
   
   rescount = 0;
   bcount = 0;
   
   if ( isresearch ) {
   for (var i=0; i < document.forms.Formular.elements.researchid.length; i++) {
     zwischen = document.forms.Formular.elements.researchid.options[i].text;
     zwischen = zwischen.toLowerCase();
     var Aussage = ""+zwischen+"";
     var Ergebnis = Aussage.search(text);
     if (Ergebnis != -1) {
      result=1;
     }
    if(result == 1) {
    	name = "ress"+document.forms.Formular.elements.researchid.options[i].value; 
    	NeuerEintrag = new Option(document.forms.Formular.elements.researchid.options[i].text, document.forms.Formular.elements.researchid.options[i].value, false, false);
      document.forms.Formular.elements.searchselect.options[document.forms.Formular.elements.searchselect.length] = NeuerEintrag;
    	ergebnisse++;
      result=0;
    }
   }
  }
  rescount = ergebnisse;
   if (  isbuilding && isresearch && rescount > 0 ) {
      NeuerEintrag = new Option("- - - - - - - - - -  - - - - - - - - - -  - - - - - - - - - -  - - - - - - - - - -  - - - - - - - - - -", "", true, true);
      document.forms.Formular.elements.searchselect.options[document.forms.Formular.elements.searchselect.length] = NeuerEintrag;
   }
   if ( isbuilding ) {
   for (var i=0; i < document.forms.Formular.elements.searchbuildings.length; i++) {
     zwischen = document.forms.Formular.elements.searchbuildings.options[i].text;
     zwischen = zwischen.toLowerCase();
     var Aussage = ""+zwischen+"";
     var Ergebnis = Aussage.search(text);
     if (Ergebnis != -1) {
      result=1;
     }
    if(result == 1) {
    	name = "ress"+document.forms.Formular.elements.searchbuildings.options[i].value; 
    	NeuerEintrag = new Option(document.forms.Formular.elements.searchbuildings.options[i].text, document.forms.Formular.elements.searchbuildings.options[i].value, false, false);
      document.forms.Formular.elements.searchselect.options[document.forms.Formular.elements.searchselect.length] = NeuerEintrag;
    	ergebnisse++;
      result=0;
    }
   }
  }
  bcount = ergebnisse - rescount;
  
  if ( ergebnisse == 1) {
  	document.forms.Formular.elements.searchselect.selectedIndex = 0;
  	document.forms.Formular.elements.searchselect.value = document.forms.Formular.elements.searchselect.options[0].value;
  	document.forms.Formular.elements.searchselect.options[0].selected = true;
  	document.forms.Formular.elements.searchselect.size = ergebnisse+1;
  }
  if ( ergebnisse >= 2 && bcount != 0 && rescount != 0 ) {
    NeuerEintrag = new Option("- - - - - - - - - -  - - - - - - - - - -  - - - - - - - - - -  - - - - - - - - - -  - - - - - - - - - -", "", true, true);
    document.forms.Formular.elements.searchselect.options[document.forms.Formular.elements.searchselect.length] = NeuerEintrag;
    ergebnisse++;
   }
   if (ergebnisse >= 10) {
    document.forms.Formular.elements.searchselect.size = 1;
    NeuerEintrag = new Option("Es wurden " + ergebnisse + " Ergebnisse gefunden", "", true, true);
    document.forms.Formular.elements.searchselect.options[document.forms.Formular.elements.searchselect.length] = NeuerEintrag;
  }
   if (ergebnisse == 0) {
    document.forms.Formular.elements.searchselect.size = 1;
    NeuerEintrag = new Option("Es wurden keine Ergebnisse gefunden", "", true, true);
    document.forms.Formular.elements.searchselect.options[document.forms.Formular.elements.searchselect.length] = NeuerEintrag;
  }
  if (ergebnisse < 10 && ergebnisse > 1) {
   	document.forms.Formular.elements.searchselect.size = ergebnisse+2;
    document.forms.Formular.elements.searchselect.options[0].selected = true;
    NeuerEintrag = new Option("Es wurden " + ergebnisse + " Ergebnisse gefunden", "", true, true);
    document.forms.Formular.elements.searchselect.options[document.forms.Formular.elements.searchselect.length] = NeuerEintrag;
  }
  document.forms.Formular.elements.searchselect.style.display = "block";
 
 
}
}
}
} 

  //-->
  </script>
  <select ondblclick="dblclick()" name="searchselect" id="search" size="8" style="display:none;width:400px;"> 	
  </select>	
  <br>	
<?php
$search = GetVar('suche');
$treffer = '';
if ($search AND preg_match('/\+.*:(.*):.*\+/',$search,$treffer) ) {
	$resid = $treffer[1];
}
//Versteckten Input mit den Geb -> Resid bilden.


echo "</form>\n\n";

$sql = "SELECT * FROM " . $db_tb_research . " WHERE ID=" . $resid;
$result = $db->db_query($sql)
	or error(GENERAL_ERROR,
           'Could not query config information.', '',
           __FILE__, __LINE__, $sql);

$research_data = $db->db_fetch_array($result);
$db->db_free_result($result);

$td1 = "\n  <tr>\n    <td class=\"windowbg2\" style=\"width: 20%;\"" .
       " valign=\"top\"><div class='doc_blue'>";
$td2 = "</div></td>\n" .
       "    <td class=\"windowbg1\" valign=\"top\">\n";

echo "<br>\n";
echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 60%;\">\n";
echo "  <tr>\n";
echo "    <td class=\"windowbg2\" colspan=\"2\"><div class='doc_blue'>\n";
echo $research_data['name'];
if($config_gameversion != $research_data['gameversion']) {
  echo $v04;
}
echo "</div>\n";
echo "    </td>\n";
echo "  </tr>\n";

echo $td1 . "Gebiet:" . $td2 . find_resfield($research_data['gebiet']);
echo "</td>\n";
echo "  </tr>\n";

echo $td1 . "Beschreibung:" . $td2;
if( !empty($research_data['description'])) {
  echo $research_data['description'];
} else {
  echo "---";
}
echo "</td>\n";
echo "  </tr>\n";
echo $td1 . "Kosten:" . $td2;

if( $research_data['FP'] > 0 ) {
  echo $research_data['FP'] . " Forschungspunkte\n";
   if( !empty($research_data['addcost'])) {
     echo "<br>" . $research_data['addcost'];
  }
} else {
  echo "---";
}
echo "</td>\n";
echo "  </tr>\n";

echo $td1 . "Highscorepunkte:" . $td2;

if( $research_data['highscore'] > 0 ) {
  echo $research_data['highscore'] . "\n";
} else {
  echo "---";
}
echo "</td>\n";
echo "  </tr>\n";
echo $td1 . "Benötigte<br>Forschungen:" . $td2;
echo create_depends_on($resid);
echo "</td>\n";
echo "  </tr>\n";

$depBuildings = create_depends_on_building($resid);
if(!empty($depBuildings)) {
  echo $td1 . "Benötigte<br>Gebäude:" . $td2 . $depBuildings;
  echo "</td>\n";
  echo "  </tr>\n";
}

$allows = create_allows($resid);
if(!empty($allows)) {
  echo $td1 . "Ermöglicht<br>Forschungen:" . $td2 . $allows;
  echo "</td>\n";
  echo "  </tr>\n";
}

$newBuildings = create_allows_building($resid, FALSE);
if(!empty($newBuildings)) {
  echo $td1 . "Ermöglicht<br>Gebäude:" . $td2 . $newBuildings;
  echo "</td>\n";
  echo "  </tr>\n";
}

$newLevels = create_allows_building($resid, TRUE);
if(!empty($newLevels)) {
  echo $td1 . "Ermöglicht<br>Gebäudestufen:" . $td2 . $newLevels;
  echo "</td>\n";
  echo "  </tr>\n";
}

if(!empty($research_data['defense'])) {
  echo $td1 . "Ermöglicht<br>Verteidigung:" . $td2 .
       $research_data['defense'];
  echo "</td>\n";
  echo "  </tr>\n";
}

 if(!empty($research_data['genetics'])) {
  echo $td1 . "Ermöglicht<br>Genetikoptionen:" . $td2 .
 	     $research_data['genetics'];
  echo "</td>\n";
  echo "  </tr>\n";
}

if( strpos( find_resfield($research_data['gebiet']), "Prototypen") !== FALSE) {
  echo $td1 . "liefert Prototyp:" . $td2 .
 	     create_allows_prototype($resid);
  echo "</td>\n";
  echo "  </tr>\n";
}

echo "</table>\n";
echo "<br>\n";
echo "<table border=\"0\" cellpadding=\"4\" cellspacing=\"1\" class=\"bordercolor\" style=\"width: 60%;\">\n";
echo " <tr>\n";
echo "   <td class=\"windowbg2\" colspan=\"2\"><div class='doc_blue'>";
if(!$unknownonly)
  echo "Und wie komme ich jetzt dahin?";
else 
  echo "Und was benötige ich noch?";
echo "</div></td>\n </tr>\n";
echo $td1 . "benötigte<br/>Forschungen:" . $td2;
$deplist = dependencies($resid);

if(!empty($deplist))
  echo $deplist;
else
  echo "- keine -";
	
echo "</td>\n";
echo " </tr>\n";

if(!empty($buildings)) {
  echo $td1 . "benötigte<br/>Gebäude:" . $td2 .
       dependend_buildings($resid);
  echo "</td>\n";
  echo " </tr>\n";
}

echo "</table>\n";
return;

//****************************************************************************
//
function fill_selection($selected_id) {
  global $db, $db_tb_research, $db_tb_researchfield;

	$fields = array();
  $sql = "SELECT id, name FROM " . $db_tb_researchfield . " ORDER BY id";
  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

  while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
		$resid = $research_data['id'];
	  $fields[$resid] = $research_data['name'];
	}
	$db->db_free_result($result);

  $sql = "SELECT ID, name, gebiet FROM " . $db_tb_research .
         " ORDER BY gebiet ASC, name ASC";
  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	//$count     = 0;
	$gebietalt = 0;
	$retVal    = "";
	while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
		$resid    = $research_data['ID'];
	  $resname  = $research_data['name'];
		$resfield = $research_data['gebiet'];
		
		if ( strlen($resname) > 62 ) {
		 $resname = substr($resname,0,60)."~";
	  }	

		if (($gebietalt != $resfield) && $resfield > 1) {
			$retVal .= "</optgroup>\n";
		}			

		if($gebietalt != $resfield) {
		  //$count++;
		  $retVal .= "<optgroup label=\"" . $fields[$resfield] . "\"" .
                 " title=\"" . $fields[$resfield] . "\">\n";
			$gebietalt = $resfield;
		}

		$retVal .= "<option value=\"" . $resid . "\"";
		if($resid == $selected_id) {
		  $retVal .= " selected";
		}

		$retVal .= ">" . $resname . "</option>\n";
	}
	$db->db_free_result($result);

	return $retVal;
}

//****************************************************************************
//
function find_resfield($id) {
  global $db, $db_tb_researchfield;

  $sql = "SELECT name FROM " . $db_tb_researchfield . " WHERE id=" . $id;
  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	$row = $db->db_fetch_array($result);
	$db->db_free_result($result);

	if(!empty($row['name'])) {
		return $row['name'];
	}

	return "---";
}

//****************************************************************************
//
function create_depends_on($resid) {
  global $sid, $db, $db_tb_research2research, $db_tb_research, $v04,
         $alphaorder, $config_gameversion, $modulname;

	$sql = "SELECT t1.rOld AS rid, t2.name AS rname, t2.gameversion AS gameversion FROM " .
         $db_tb_research2research .
	       " AS t1 INNER JOIN " . $db_tb_research .
	       " AS t2 ON t1.rOld=t2.id WHERE t1.rNew=" . $resid;

  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	$retVal = "";
  $lind = FALSE;
  while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
	  if($lind !== FALSE) {
		  $retVal .= "<br>\n";
		}
		$retVal .= "<img src=\"bilder/point.gif\" alt=\"a point o.O\"/>&nbsp;";
	  $retVal .= "<a href=\"index.php?action=" . $modulname  .
               "&amp;researchid=" . $research_data['rid'] .
               "&amp;sid=". $sid .
               "&amp;alphaorder=" . $alphaorder . "\">" .
               $research_data['rname'] . "</a>";
		if($research_data['gameversion'] != $config_gameversion) {
		  $retVal .= $v04;
		}
		$lind = TRUE;
	}
	if(empty($retVal))
    return "---";

	return $retVal;
}

//****************************************************************************
//
function create_allows($resid) {
  global $sid, $db, $db_tb_research2research, $modulname,
         $db_tb_research, $v04, $alphaorder, $config_gameversion;

	$sql = "SELECT t1.rNew AS rid, t2.name AS rname, t2.gebiet AS rgebiet," .
	       " t2.gameversion AS gameversion  FROM " . $db_tb_research2research .
	       " AS t1 INNER JOIN " . $db_tb_research .
	       " AS t2 ON t1.rNew=t2.id WHERE t1.rOld=" . $resid;

  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	$retVal = "";
  $lind   = FALSE;
  while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
	  if($lind !== FALSE) {
		  $retVal .= "<br>\n";
		}
		$retVal .= "<img src=\"bilder/point.gif\" alt=\"a point o.O\"/>&nbsp;";
		$retVal .= "<a href=\"index.php?action=" . $modulname .
		"&amp;researchid=" . $research_data['rid'] .
		"&amp;sid=". $sid . "&amp;alphaorder=" . $alphaorder . "\">";
		if($research_data['rgebiet'] == 0) {
		  $retVal .= "<span class='doc_red'>";
		}
		$retVal .= $research_data['rname'];
		if($research_data['rgebiet'] == 0) {
		  $retVal .= "</span>";
		}
		$retVal .= "</a>";
		if($research_data['gameversion'] != $config_gameversion) {
		  $retVal .= $v04;
		}
		$lind = TRUE;
	}

	return $retVal;
}

//****************************************************************************
//
function create_depends_on_building($resid) {
  global $sid, $db, $db_tb_building2research, $db_tb_gebaeude;

	$retVal = "---";
	$sql = "SELECT t2.name AS bname, t2.bild AS bbild FROM " . $db_tb_building2research .
	       " AS t1 INNER JOIN " . $db_tb_gebaeude .
	       " AS t2 ON t1.bId=t2.id WHERE t1.rId=" . $resid;

  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	if($db->db_num_rows($result) > 0) {
    $retVal = "<table>\n";
    while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
  	  $retVal .= "<tr><td>";
  		if( !empty($research_data['bbild'])) {
  		  $retVal .= "<img src=\"bilder/gebs/" . $research_data['bbild'].
                   ".jpg\" width=\"50\" height=\"50\" alt=\"".$research_data['bname'] ."\"/>";
  		} else {
  		  $retVal .= "<img src=\"bilder/gebs/blank.jpg\" width=\"50\" height=\"50\" alt=\"blank\"/>";
  		}
  		$retVal .= "</td><td>";
  	  $retVal .= $research_data['bname'];
  	  $retVal .= "</td></tr>";
  	}
    $retVal .= "</table>\n";
	}

	return $retVal;
}

//****************************************************************************
//
function create_allows_building($resid, $isLevel) {
  global $sid, $db, $db_tb_research2building, $db_tb_gebaeude;

	$sql = "SELECT t2.name AS bname, t2.bild AS bbild, t1.lvl AS blevel FROM " .
         $db_tb_research2building .
	       " AS t1 INNER JOIN " . $db_tb_gebaeude .
	       " AS t2 ON t1.bId=t2.id WHERE t1.rId=" . $resid ;
	if($isLevel == TRUE) {
	  $sql .= " AND t1.lvl>0";
	} else {
	  $sql .= " AND t1.lvl=0";
	}
	$sql .= " ORDER BY bname ASC, t1.lvl ASC";

	$retVal = "";
  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	if($db->db_num_rows($result) > 0) {
    $retVal .= "<table>\n";
    while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
  	  $retVal .= "<tr><td>";
  		if( !empty($research_data['bbild'])) {
  		  $retVal .= "<img src=\"bilder/gebs/" . $research_data['bbild']. ".jpg\" width=\"50\" height=\"50\" alt=\"".$research_data['bname'] ."\"/>";
  		} else {
  		  $retVal .= "<img src=\"bilder/gebs/blank.jpg\" width=\"50\" height=\"50\" alt=\"".$research_data['bname'] ."\"/>";
  		}
  		$retVal .= "</td><td>";
  	  $retVal .= $research_data['bname'];
  		if($research_data['blevel'] > 0) {
  		  $retVal .= " Stufe " . $research_data['blevel'];
  		}
  	  $retVal .= "</td></tr>";
  	}
    $retVal .= "</table>\n";
	}

	return $retVal;
}

//****************************************************************************
//
function create_allows_prototype($resid) {
  global $sid, $db, $db_tb_research2prototype, $db_tb_schiffstyp;

	$sql = "SELECT t2.schiff AS pname, t2.bild AS pbild  FROM " . $db_tb_research2prototype .
	       " AS t1 INNER JOIN " . $db_tb_schiffstyp .
	       " AS t2 ON t1.pId=t2.id WHERE t1.rId=" . $resid;

	$retVal = "";

  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	if($db->db_num_rows($result) > 0) {
    $retVal .= "<table>\n";
    while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
  	  $retVal .= "<tr><td>";
  		if( !empty($research_data['pbild'])) {
  		  $retVal .= "<img src=\"bilder/ships/" . $research_data['pbild']. ".jpg\" width=\"70\" height=\"70\" alt=\"".$research_data['pname'] ."\"/>";
  		} else {
  		  $retVal .= "<img src=\"bilder/ships/blank.jpg\" width=\"70\" height=\"70\" alt=\"Leider kein Bild vorhanden.\"/>";
  		}
  		$retVal .= "</td><td>";
  	  $retVal .= $research_data['pname'];
  	  $retVal .= "</td></tr>";
  	}
    $retVal .= "</table>\n";
	}

	return $retVal;
}

//****************************************************************************
//
function dependencies($resid) {
  global $sid, $db, $db_tb_building2research, $db_tb_research2research, $alphaorder,
	     $db_tb_research, $buildings, $researches, $v04, $unknownonly, $neededfpforresearch, 
		   $db_tb_research2user, $user_sitterlogin, $modulname, $config_gameversion;

	$sql = "SELECT * FROM " . $db_tb_research . " WHERE ID=" . $resid;
  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
	$research_data = $db->db_fetch_array($result);

	$gebiet             = $research_data['gebiet'];
	$researches[$resid] = $research_data['name'];
  if($research_data['gameversion'] != $config_gameversion) {
  	$researches[$resid] .= $v04;
	}

	$sql = "SELECT rOld AS rid FROM " . $db_tb_research2research . " WHERE rNew=" . $resid;
  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);
             
                       

	$retVal = "";
  while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
	  $newresid = $research_data['rid'];
	  if(empty($researches[$newresid])) {
	    $retVal .= dependencies($newresid);
		}
	}

	$sql = "SELECT bId FROM " . $db_tb_building2research . " WHERE rId=" . $resid;
  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

  while(($data = $db->db_fetch_array($result)) !== FALSE) {
	  $bldg = $data['bId'];
		$buildings[$bldg] = getBuilding($bldg);
	}

	$sql = "SELECT * FROM " . $db_tb_research2user . " WHERE rid=" . $resid .
	       " AND userid='" . $user_sitterlogin . "'";
    $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	if($db->db_num_rows($result) > 0) {
	  $colorme_on = " <span class='doc_green'> ";
	  $colorme_off = "</span>";
		$isresearched = TRUE;				
		
	} else {
	  $colorme_on = "<span class='doc_black'>";
	  $colorme_off = "</span>";
		$isresearched = FALSE;
		
	}

	if($gebiet > 0) {
	  if(($unknownonly && $isresearched == FALSE) || !$unknownonly) {
    	$retVal .= "<img src=\"bilder/point.gif\" alt=\"a point o.O\"/>&nbsp;";
      $retVal .= "<a href=\"index.php?action=" . $modulname .
                 "&amp;researchid=" . $resid .
    	     "&amp;sid=". $sid . "&amp;alphaorder=" . $alphaorder . "\">" . 
  				 $colorme_on . $researches[$resid] . $colorme_off . "</a><br>";
	  }
	} else {
  	$retVal .= "<img src=\"bilder/point.gif\" alt=\"a point o.O\"/>&nbsp;";
    $retVal .= "<a href=\"index.php?action=" . $modulname .
               "&amp;researchid=" . $resid .
  	     "&amp;sid=". $sid . "&amp;alphaorder=" . $alphaorder . "\"><span class='doc_red'>" .
				 $researches[$resid] . "</span></a><br>";
				 
	
	}

	return $retVal;
}


//****************************************************************************
//
function getBuilding($bid) {
  global $db, $db_tb_gebaeude;

	$sql = "SELECT name, bild FROM " . $db_tb_gebaeude . " WHERE id=" . $bid;

  $result = $db->db_query($sql)
  	or error(GENERAL_ERROR,
             'Could not query config information.', '',
             __FILE__, __LINE__, $sql);

	$retval = "";
  while(($research_data = $db->db_fetch_array($result)) !== FALSE) {
	  $retval .= "<tr>\n<td>";
		if( !empty($research_data['bild'])) {
		  $retval .= "<img src=\"bilder/gebs/" . $research_data['bild']. ".jpg\" width=\"50\" height=\"50\" alt=\"".$research_data['name'] ."\"/>";
		} else {
		  $retval .= "<img src=\"bilder/gebs/blank.gif\" width=\"50\" height=\"50\" alt=\"".$research_data['name'] ."\"/>";
		}
		$retval .= "</td>\n<td>";
	  $retval .= $research_data['name'];
	  $retval .= "</td>\n</tr>\n";
	}

	return $retval;
}

//****************************************************************************
//
function dependend_buildings($resid) {
  global $buildings;

  $retVal = "<table>\n";
	foreach($buildings as $bld) {
	  $retVal .= $bld;
	}
  $retVal .= "</table>\n";

	return $retVal;
}

?>
