<?php

require_once("de_xml.php"); //unixml-parser input_unixml ist dort

/**
 * function parse_de_universum
 *
 * verarbeitet Systemxml-Strings zu SimpleXMLElement Objekten und gibt sie an den unixml-parser weiter
 *
 * @param $xmldata object Daten von der parserlib
 *
 * @return bool Verarbeitung erfolgreich
 */

function parse_de_universum($xmldata)
{
    if ($xmldata->objResultData instanceof DTOParserUniversumXmlTextC) {
        //we have an array of xml-strings

        foreach ($xmldata->objResultData->aXmlText as $XmlText) {

            $XmlText = mb_convert_encoding($XmlText, "ISO-8859-1");         //zurückkonvertieren zu "ISO-8859-1" weil in der xml so angegeben aber IWDB Input ist utf-8
            $xmlobject = simplexml_load_string($XmlText);

            if (!empty($xmlobject)) {
                input_unixml($xmlobject);

                return true;
            } else {
                echo "<div class='system_warning'>XML-Fehler</div>\n";

                return false;
            }

        }

    } elseif ($xmldata->objResultData instanceof DTOParserUniversumResultC) {
        echo "<div class='system_warning'>Input erfolgreich erkannt. Passende Verarbeitung ist aber bisher nicht vorhanden.</div>";

        return false;

    } else {

        echo "<div class='system_warning'>Unbekannter Fehler.</div>";

    }

    return false;
}