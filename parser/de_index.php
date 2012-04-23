<?php
/*****************************************************************************/
/* de_index.php                                                     */
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
/* Diese Erweiterung der urspuenglichen DB ist ein Gemeinschafftsprojekt von */
/* IW-Spielern.                                                              */
/*                                                                           */
/* Autor: Mac (MacXY@herr-der-mails.de)                                      */
/* Datum: Jun 2009 - April 2012                                              */
/*                                                                           */
/* Bei Problemen kannst du dich an das eigens dafür eingerichtete            */
/* Entwicklerforum wenden:                                                   */
/*                   http://www.iw-smf.pericolini.de                         */
/*                   https://github.com/iwdb/iwdb                            */
/*                                                                           */
/*****************************************************************************/


if (!defined('DEBUG_LEVEL'))
	define('DEBUG_LEVEL', 0);

if (basename($_SERVER['PHP_SELF']) != "index.php")
  die('Hacking attempt...!!');

if (!defined('IRA'))
	die('Hacking attempt...');

error_reporting(E_ALL);

function parse_de_index ( $return )
{
    echo "Startseiten Parser funktioniert, speichert im Moment aber noch keine Daten in der DB!<br>";
	foreach ($return->objResultData->aContainer as $aContainer)
	{
		if ($aContainer->bSuccessfullyParsed)
   		{
			if ($aContainer->strIdentifier == "de_index_fleet")
			{
				$fleetType = $aContainer->objResultData->strType;	//! own OR opposite
                if (!$aContainer->objResultData->bObjectsVisible)
                     echo "<font color='orange'>Info: </font> keine Transportinformation (" . $fleetType . ") sichtbar. Bitte Fluginformationen vor dem Parsen ausklappen";

				foreach ($aContainer->objResultData->aFleets as $msg)
				{	
					$tf_type = $msg->eTransfairType;
					if ( $tf_type == "Rückkehr") {	//! keine weiteren Infos vorhanden
						continue;		
					}
					else if ($tf_type == "Übergabe" || $tf_type == "Transport" || $tf_type == "Übergabe (tr Schiffe)" || $tf_type == "Massdriverpaket") {
						$transfair_of_user_name = $msg->strUserNameFrom;
						$transfair_to_coords = $msg->strCoordsTo;		

						if ($tf_type == "Transport" && !empty($msg->iAnkunft)) 	//! Ausladezeit: +5min
							$transfair_date = $msg->iAnkunft + 5*60;
						else
							$transfair_date = $msg->iAnkunft;

						$transfair_type = "NULL";
						if ($tf_type == "Transport" || $tf_type == "Massdriverpaket")
							$transfair_type = "'transport'";
						else if ($tf_type == "Übergabe" || $tf_type == "Übergabe (tr Schiffe)")
							$transfair_type = "'ownershiptransfair'";
						else {
                            echo "<font color='red'>unknown transfer_type detected: " .$msg->eTransfairType."</font>";
                            continue;
                        }	
						
                        //! Mac: @todo: Transport in die DB eintragen

						foreach ($msg->aObjects as $object)
						{												
							//! Mac: @todo: gelieferte Ress/Schiffe eintragen   
						}
					}
					// || $tf_type == "Sondierung (Geologie) (Scout)" //! Mac: nicht wichtig, oder ?
					else if ($tf_type == "Sondierung (Schiffe/Def/Ress)" || $tf_type == "Angriff"
                            || $tf_type == "Sondierung (Gebäude/Ress)" || $tf_type == "Sondierung (Schiff) (Scout)"
                            || $tf_type == "Sondierung (Gebäude) (Scout)" || $tf_type == "Sondierung (Geologie) (Scout)"
                            || $tf_type == "Sondierung (Geologie)") {
                        
                        //! Mac: @todo: Sondierungen auswerten/melden/warnen
					}
				}
			}	//! index_fleet
			else if (($aContainer->bSuccessfullyParsed) &&  ($aContainer->strIdentifier == "de_index_ressourcen"))
			{
				//! Mac: @todo: Ressourcen auf dem aktuellen Planeten auswerten
			}
			else if ($aContainer->strIdentifier == "de_index_research") 
			{
				foreach ($aContainer->objResultData->aResearch as $msg)
				{	
                    //! Mac: @todo: laufende Forschungen auswerten, ggf. aus Sitting entfernen
				}
			}	
			else if ($aContainer->strIdentifier == "de_index_geb") 
			{
				if (!isset($aContainer->objResultData->aGeb)) continue;
				foreach ($aContainer->objResultData->aGeb as $msg)
				{
					//! Mac: @todo: laufende Gebaeude auswerten, ggf. aus Sitting entfernen
				}
			}
			else if ($aContainer->strIdentifier == "de_index_schiff")
			{
				foreach ($aContainer->objResultData->aSchiff as $plan)
				{
				   foreach ($plan as $ship_types)
				   {
                       //! Mac: @todo: laufende Schiffe auswerten, ggf. aus Sitting entfernen oder Auftraege schieben
                   }
				}
			}
		}	
		else		//! successfully parsed
   		{
            foreach ($aContainer->aErrors as $msg)
                echo $msg."<br>";
   		} 
	}		//! for each container
}
