<?php
/*****************************************************************************/
/* m_galastats.php                                                             */
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
/* Dieses Modul dient der Anzeige von Galastatistiken und Gedöns             */
/* für die Iw DB: Icewars geoscan and sitter database                        */
/*---------------------------------------------------------------------------*/
/* Diese Erweiterung der urspruenglichen DB ist ein Gemeinschaftsprojekt von */
/* IW-Spielern.                                                              */
/* Bei Problemen kannst du dich an das eigens dafuer eingerichtete           */
/* Entwicklerforum wenden:                                                   */
/*                                                                           */
/*                   http://www.iwdb.de.vu                                   */
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
// -> Name des Moduls, ist notwendig für die Benennung der zugehoerigen
//    Config.cfg.php
// -> Das m_ als Beginn des Datreinamens des Moduls ist Bedingung für 
//    eine Installation über das Menü
//
$modulname  = "m_galastats";

//****************************************************************************
//
// -> Menütitel des Moduls der in der Navigation dargestellt werden soll.
//
$modultitle = "Galastatistiken";

//****************************************************************************
//
// -> Status des Moduls, bestimmt wer dieses Modul über die Navigation 
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
  "Das Galastatistiken-Modul berechnet eine Highscore für Kolonien, Planipunkte und Kampfbasen für jede Galaxie und für die gesamte Sichtweite";

//****************************************************************************
//
// Function workInstallDatabase is creating all database entries needed for
// installing this module. 
//
function workInstallDatabase() {

  echo "<div class='system_notification'>Installation: Datenbankänderungen = <b>n/V (also OK)</b></div>";
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
	  // Weitere Wiederholungen für weitere Menue-Eintraege, z.B.
	  //
	  // 	insertMenuItem( $menu+1, ($submenu+1), "Titel2", "hc", "&weissichnichtwas=1" );
	  //
}

//****************************************************************************
//
// Function workInstallConfigString will return all the other contents needed 
// for the configuration file
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
 echo "<div class='system_notification'>Deinstallation: Datenbankänderungen = <b>n/V (also OK)</b></div>";
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

    global $sid;
    global $config_map_default_galaxy;
    global $config_map_galaxy_count;

    
//settings überprüfen und entsprechend setzen

    $galamin = getVar('galamin');
    $galamax = getVar('galamax');
    $gesamtmin = getVar('gesamtmin');
    $gesamtmax = getVar('gesamtmax');
    $order = getVar('order');
    $showfrom = getVar('showfrom');
    $showto = getVar('showto');

    $galamin = (is_numeric($galamin)) ? $galamin : 1;          //Start-Position der Allys in der Gala, ab wann in den Galalisten angezeigt werden.
    $galamax = (is_numeric($galamax)) ? $galamax : 10;         //End-Position der Allys in der Gala, die in den Galalisten angezeigt werden.
    $gesamtmin = (is_numeric($gesamtmin)) ? $gesamtmin : 1;    //Start-Position der Allys im Hasiversum, ab wann in der Gesamtliste angezeigt werden.
    $gesamtmax = (is_numeric($gesamtmax)) ? $gesamtmax : 35;   //End-Position der allys im Hasiversum, die in der Gesamtliste angezeigt werden.
    $order = (is_numeric($order) && $order>=0 && $order<=6) ? $order : 0;   //Sortierung: 0 Steinklumpen, 1 Astro, 2 Gasgiga, 3 Eisi, 4 Pkte, 5 Pkte/Planni, 6 Kbs
    $showfrom = (is_numeric($showfrom) && ($showfrom>=1) && ($showfrom<=$config_map_galaxy_count)) ? $showfrom : ($config_map_default_galaxy-2); //erste Gala, die angezeigt wird
    $showto = (is_numeric($showto) && ($showto>=1) && ($showto<=$config_map_galaxy_count)) ? $showto : ($config_map_default_galaxy+2);           //letzte Gala, die angezeigt wird
      
    //erstmal alle besiedelten Planis aus der DB holen:
    $sql = "SELECT coords_gal, coords_sys, coords_planet, allianz, punkte, user, typ, objekt FROM ".$db_tb_scans . " WHERE objekt not like '---'";
    $result = $db->db_query($sql)
             or error(GENERAL_ERROR,
                 'Could not query config information.', '',
                 __FILE__, __LINE__, $sql);
     $i = 0;
     while( $rowGal = $db->db_fetch_array($result)) {
       $i++;
       $universe[$rowGal['coords_gal']][$rowGal['coords_sys']][$rowGal['coords_planet']] = array( $rowGal['allianz'], (int) $rowGal['punkte'], $rowGal['typ'], $rowGal['objekt'], $rowGal['user']);
     }

     doc_title("Galaxie-Statistiken");
          
     if ($i==0) {
       echo "<div class='system_error'>Solange noch keine Systeme in den Parser eingelesen sind, gibt es leider auch noch keine Statistiken.</div>";
     } else {

     //mal kurz lustig verschachtelte arraystrukturen erklären:

     //$universe: erste dimension key=gala, 2. Dimension key=sys, 3. dimension key=planni, 4. dim siehe nächste zeilen
     //planni[0]: allianz,
     //planni[1]: plannipunkte
     //planni[2]: typ (Steinklumpen, ...)
     //planni[3]: objekt (Kolonie, ...)
    
     //$allys: erste dimension key=Gala, 2. Dimension key=allianztag, 3. Dimension: 0 -> anz Steinklkolo, 1 -> anz Astrokolo, 2 -> anz Gasgigakolo, 3 -> anz eiskolo, 4 -> gesamtpkte, 5 -> pkte/planni, 6 -> anz kbs

     //$allylist: erste dimension key=allytag, 2. dimension wie 3. dim allys
    
     //und jetzt: galaweise Plannianzahlen zusammenzählen und $allys fuellen
    
     foreach( $universe as $galano => $gala ) {
            foreach ( $gala as $system ) {
              foreach ( $system as $planni ) {
                 if ($planni[3] == "Kolonie") {
                    if ($planni[2] == "Steinklumpen") {
              if ( !isset( $allys[$galano][$planni[0]]) ) {
                $allys[$galano][$planni[0]] = array( 1, 0, 0, 0, $planni[1], $planni[1], 0);
                $allylist[$planni[0]] = array(0,0,0,0,0,0,0);  // in allylist werden alle allianzen als key reingeschrieben, hinten wird mit alles mit 0 initialisiert, zusammenrechnen kommt später. siehe unten ^^
          }
          else {
                        $allys[$galano][$planni[0]][0] += 1;
            $allys[$galano][$planni[0]][4] += $planni[1];
	    $allys[$galano][$planni[0]][5] = $allys[$galano][$planni[0]][4]/($allys[$galano][$planni[0]][0] + $allys[$galano][$planni[0]][1] + $allys[$galano][$planni[0]][2] + $allys[$galano][$planni[0]][3]); // durchschnitt = gesamtpkte / summe aller plannis
	  }
            } else if ($planni[2] == "Asteroid") {
              if ( !isset( $allys[$galano][$planni[0]])  ) {
                $allys[$galano][$planni[0]] = array( 0, 1, 0, 0, $planni[1], $planni[1], 0);
                $allylist[$planni[0]] = array(0,0,0,0,0,0,0);
              } else {
                        $allys[$galano][$planni[0]][1] += 1;
            $allys[$galano][$planni[0]][4] += $planni[1];
	    $allys[$galano][$planni[0]][5] = $allys[$galano][$planni[0]][4]/($allys[$galano][$planni[0]][0] + $allys[$galano][$planni[0]][1] + $allys[$galano][$planni[0]][2] + $allys[$galano][$planni[0]][3]); // durchschnitt = gesamtpkte / summe aller plannis
              }
            } else if ($planni[2] == "Gasgigant") {
              if ( !isset( $allys[$galano][$planni[0]])  ) {
                $allys[$galano][$planni[0]] = array( 0, 0, 1, 0, $planni[1], $planni[1], 0);
                $allylist[$planni[0]] = array(0,0,0,0,0,0,0);
              } else {
                        $allys[$galano][$planni[0]][2] += 1;
            $allys[$galano][$planni[0]][4] += $planni[1];
	    $allys[$galano][$planni[0]][5] = $allys[$galano][$planni[0]][4]/($allys[$galano][$planni[0]][0] + $allys[$galano][$planni[0]][1] + $allys[$galano][$planni[0]][2] + $allys[$galano][$planni[0]][3]); // durchschnitt = gesamtpkte / summe aller plannis
              }
            } else if ($planni[2] == "Eisplanet") {
              if ( !isset( $allys[$galano][$planni[0]])  ) {
                $allys[$galano][$planni[0]] = array( 0, 0, 0, 1, $planni[1], $planni[1], 0);
                $allylist[$planni[0]] = array(0,0,0,0,0,0,0);
              } else {
                        $allys[$galano][$planni[0]][3] += 1;
            $allys[$galano][$planni[0]][4] += $planni[1];
	    $allys[$galano][$planni[0]][5] = $allys[$galano][$planni[0]][4]/($allys[$galano][$planni[0]][0] + $allys[$galano][$planni[0]][1] + $allys[$galano][$planni[0]][2] + $allys[$galano][$planni[0]][3]); // durchschnitt = gesamtpkte / summe aller plannis
              }
            }
         } else if ($planni[3] == "Kampfbasis") {
                    if ( !isset( $allys[$galano][$planni[0]])  )  {
              $allys[$galano][$planni[0]] = array( 0, 0, 0, 0, 0, 0, 1 );
              $allylist[$planni[0]] = array(0,0,0,0,0,0,0);
            } else {
                      $allys[$galano][$planni[0]][6] += 1;
            }
         }
          }
        }
     }

     //nun mal gesamtplanizahlen zusammenrechnen und sortierarrays aufschreiben:
    
       foreach ($allylist as $ally => $stats) {
          foreach ($allys as $gala => $galaallys) {
            foreach ($galaallys as $galaally => $galastats) {     
              $sortargal[$gala][$galaally]=$galastats[$order];     //sortierarray fuer galalisten zusammenbasteln
              $sortargalpkt[$gala][$galaally]=$galastats['0'];     //2. sortierreihenfolge ist steinklumpen
            if ($ally == $galaally) {
              foreach ($stats as $index => $status) {
                     $allylist[$ally][$index]+=$galastats[$index];
              }
            }
          }
       }
       
       ($allylist[$ally][4]==0) ? ($allylist[$ally][5] = 0) : ($allylist[$ally][5] = $allylist[$ally][4]/($allylist[$ally][0]+$allylist[$ally][1]+$allylist[$ally][2]+$allylist[$ally][3]));        //durchschnittspunkte nochmal richtig berechnen
       $sortar[$ally] = $allylist[$ally][$order];    //sortierarray fuer die gesamtliste zusammenbasteln
       $sortarpkt[$ally] = $allylist[$ally]['0'];    //2. sortierreihenfolge ist immer steinklumpen
     }


     //jetzt den ganzen mist huebsch (oder auch nicht so huebsch) ausgeben:
     echo "<br>\n";
     echo "<div class='doc_centered'>\n";
     echo "<form name=\"frm\">\n";

     echo "<input type=\"hidden\" name=\"sid\" value=\"$sid\">\n";
     echo "<input type=\"hidden\" name=\"action\" value=\"$modulname\">\n";
     echo "<p>";
     echo "Statistiken anzeigen für Gala <input type=\"text\" name=\"showfrom\" value=\"$showfrom\" size=\"4\">&nbsp;\n";
     echo "bis <input type=\"text\" name=\"showto\" value=\"$showto\" size=\"4\">&nbsp;\n";
     echo "</p>\n<p>";
     echo "Galaxieliste anzeigen von Ally <input type=\"text\" name=\"galamin\" value=\"$galamin\" size=\"4\">&nbsp;\n";
     echo "bis <input type=\"text\" name=\"galamax\" value=\"$galamax\" size=\"4\">&nbsp;\n";
     echo "</p>\n<p>";
     echo "Hasiversumsliste von Ally <input type=\"text\" name=\"gesamtmin\" value=\"$gesamtmin\" size=\"4\">&nbsp;\n";
     echo "bis <input type=\"text\" name=\"gesamtmax\" value=\"$gesamtmax\" size=\"4\">&nbsp;\n";
     echo "</p>\n<p>";
     echo "Listen sortieren nach <SELECT NAME=\"order\" size=1>\n";
     echo "<OPTION VALUE=\"0\"\n";
     if ($order==0) { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Kolonien auf Steinklumpen";

     echo "<OPTION VALUE=\"1\"";
     if ($order==1) { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Kolonien auf Asteroiden";

     echo "<OPTION VALUE=\"2\"";
     if ($order==2) { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Kolonien auf Gasgiganten";

     echo "<OPTION VALUE=\"3\"";
     if ($order==3) { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Kolonien auf Eisplaneten";

     echo "<OPTION VALUE=\"4\"";
     if ($order==4) { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Punkte";

     echo "<OPTION VALUE=\"5\"";
     if ($order==5) { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Punkte pro Planet";


     echo "<OPTION VALUE=\"6\"";
     if ($order==6) { echo " selected=\"selected\""; }
     echo ">\n";
     echo "Kampfbasen";

     echo "</select></p>\n<p>";
     echo "<input type=\"submit\" value=\"anzeigen\">";
     echo "</p>\n<br>";
     echo "</form>";
     echo "</div>";

     //sortieren der gesamtausgabe nach planipunkten
     array_multisort($sortar, SORT_NUMERIC, SORT_DESC, $sortarpkt, SORT_NUMERIC, SORT_DESC, $allylist);
     
     start_table();
     start_row("titlebg", "style=\"width:95%\" align=\"center\" colspan=\"6\"");
     echo "  <b>Bekanntes Hasiversum</b>\n";
         next_row("windowbg2", "style=\"width:16%\" align=\"center\"");
     echo "Allianz";
     next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
     echo "Kolonien auf Steinklumpen";
     next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
     echo "Kolonien auf Asteroiden";
     next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
     echo "Kolonien auf Gasgiganten";
     next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
     echo "Kolonien auf Eisplaneten";
     next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
     echo "Kampfbasen";

     $i=0;
     foreach ( $allylist as $ally => $allystats ) {
       $i++;
       if ($i>=$gesamtmin){
         if ($ally == "") {$ally = "<i>allylos</i>";}
         next_row("windowbg2", "style=\"width:16%\" align=\"left\"");
         echo "$i. <a href='index.php?action=m_allystats&allianz=$ally'>$ally";
         next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
         echo  $allystats[0];
         next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
         echo  $allystats[1];
         next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
         echo  $allystats[2];
         next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
         echo  $allystats[3];
#         next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
#         echo  number_format($allystats[4], 0, ",", ".");
#         next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
#         echo  number_format($allystats[5], 2, ",", ".");
         next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
         echo  $allystats[6];
       }//echo "  <tr><td>". $ally . "</td><td>" . $allystats[0] . "</td><td>" . $allystats[1] . "</td><td>" . $allystats[2] . "</td><td>" . $allystats[3] . "</td><td>" . $allystats[4] . "</td><td>" . $allystats[5] . "</td></tr>\n";
       if ($i==$gesamtmax) {break;}
     }
     end_row();
     end_table();
     echo "<br>\n";

     //sortieren nach galaxien
     ksort($allys, SORT_NUMERIC);  
     foreach ($allys as $gala => $galaallys) {

       if ( $gala>=$showfrom && $gala<=$showto ) {
	     
         array_multisort($sortargal[$gala], SORT_NUMERIC, SORT_DESC, $sortargalpkt[$gala], SORT_NUMERIC, SORT_DESC, $galaallys); //sortieren nach plannipunkten
         start_table();
         start_row("titlebg", "style=\"width:95%\" align=\"center\" colspan=\"10\"");
         echo "  <b>Gala $gala </b>\n";
         next_row("windowbg2", "style=\"width:16%\" align=\"center\"");
         echo "Allianz";
         next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
         echo "Kolonien auf Steinklumpen";
         next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
         echo "Kolonien auf Asteroiden";
         next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
         echo "Kolonien auf Gasgiganten";
         next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
         echo "Kolonien auf Eisplaneten";
         next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
         echo "Planetenpunkte";
         next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
         echo "Punkte pro Planet";
         next_cell("windowbg2", "style=\"width:12%\" align=\"center\"");
         echo "Kampfbasen";
         
         $i=0;
         foreach ( $galaallys as $galaally => $galaallystats ) {
           $i++;
           if ($i>=$galamin){
             if ($galaally == "") {$galaally = "<i>allylos</i>";}
             next_row("windowbg2", "style=\"width:16%\" align=\"left\"");
             echo "$i. <a href='index.php?action=m_allystats&allianz=$galaally'>$galaally</a>";
             next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
             echo  $galaallystats[0];
             next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
             echo  $galaallystats[1];
             next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
             echo  $galaallystats[2];
             next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
             echo  $galaallystats[3];
             next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
             echo  number_format($galaallystats[4], 0, ",", ".");
             next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
             echo  number_format($galaallystats[5], 2, ",", ".");
             next_cell("windowbg1", "style=\"width:12%\" align=\"right\"");
             echo  $galaallystats[6];
           }
           if ($i==$galamax) {break;}
         }
         end_row();
         end_table();
         echo "<br>\n";
       }
     }
   }
?>
