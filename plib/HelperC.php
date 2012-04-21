<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <benjamin.woester@googlemail.com> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Benjamin Wöster
 * ----------------------------------------------------------------------------
 */
/**
 * @author Benjamin Wöster <benjamin.woester@googlemail.com>
 * @author Martin Martimeo <martin@martimeo.de>
 * @package libIwParsers
 * @subpackage helpers
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



class HelperC
{

  /////////////////////////////////////////////////////////////////////////////

  /**
  * Tries to convert the given parameter into a unix timestamp.
  *
  *
  * @return mixed - integer if conversion was successfull
  *                 boolean false if the provided parameter couldn't be
  *                 recognized as a date
  */
  static public function convertDateToTimestamp( $value )
  {
    static $month2int = array(
      'January'=>1,
      'Jan'=>1,
      'Januar'=>1,
      'February'=>2,
      'Feb'=>2,
      'Februar'=>2,
      'March'=>3,
      'Mar'=>3,
      'März'=>3,
      'April'=>4,
      'Apr'=>4,
      'May'=>5,
      'Mai'=>5,
      'June'=>6,
      'Juni'=>6,
      'July'=>7,
      'Juli'=>7,
      'August'=>8,
      'Aug'=>8,
      'September'=>9,
      'Sept'=>9,
      'October'=>10,
      'Oct'=>10,
      'Oktober'=>10,
      'November'=>11,
      'Nov'=>11,
      'December'=>12,
      'Dez'=>12,
      'Dec'=>12,
      'Dezember'=>12
    );
    
    $aResult  = array();
    $aMktime  = array();
    if (preg_match('@(\d{1,2})\w{0,2}(\s|\.)(\d{1,2}|\w+)(\s|\.)(\d{4})@i', $value, $aResult ) != false)
    {
      $mktime['d'] = (int) $aResult[1];
      $aResult3 = (int) $aResult[3];
      if (!empty($aResult3))
      {
        $mktime['m'] = (int) $aResult[3];
      }
      else
      {
        $mktime['m'] = (int) $month2int[$aResult3];
      }
      $mktime['y'] = (int) $aResult[5];
      $mktime['h'] = 0;
      $mktime['i'] = 0;
    }
    elseif (preg_match('@(\d{4})(\-|\.)(\d{1,2})(\-|\.)(\d{1,2})@i', $value, $aResult ) != false)
    {
      $mktime['d'] = (int) $aResult[5];
      $mktime['m'] = (int) $aResult[3];
      $mktime['y'] = (int) $aResult[1];
      $mktime['h'] = 0;
      $mktime['i'] = 0;
    }
    elseif (preg_match('@(\w+)(\s)(\d{1,2})\w{0,2}(\s|\,\s)(\d{4})@i', $value, $aResult ) != false)
    {
      $mktime['d'] = (int) $aResult[3];
      $mktime['m'] = (int) $month2int[$aResult[1]];
      $mktime['y'] = (int) $aResult[5];
      $mktime['h'] = 0;
      $mktime['i'] = 0;
    }
    else
    {
      return false;
    }
    
    $mktime['unix'] = mktime($mktime['h'], $mktime['i'], 0, $mktime['m'], $mktime['d'], $mktime['y'] );
    return $mktime['unix'];
  }
  
  /////////////////////////////////////////////////////////////////////////////

  /**
  * Tries to convert the given parameter into a unix timestamp.
  *
  *
  * @return mixed - integer if conversion was successfull
  *                 boolean false if the provided parameter couldn't be
  *                 recognized as a date
  */
  static public function convertMixedTimeToTimestamp( $value )
  {
      $aResult  = array();
      $aMktime  = array();
      if (preg_match('@((\d+)\s(Tag|Tage)\s+|)(\d{1,2})\:(\d{1,2})(\:(\d{1,2})|)@i', $value, $aResult ) != false)
      {
          $mktime['d'] = (int) $aResult[2];
          $mktime['h'] = (int) $aResult[4];
          $mktime['i'] = (int) $aResult[5];
          if (isset($aResult[7])) $mktime['s'] = (int) $aResult[7];
          else $mktime['s'] = 0;
      }
      elseif (preg_match('@((\d+)\s(Tag|Tage)\s+|)(\d{1,2})\:(\d{1,2})(\:(\d{1,2})|)(\s(am|pm)|)@i', $value, $aResult ) != false)
      {
          $mktime['d'] = (int) $aResult[2];
          $mktime['h'] = (int) $aResult[4];
          $mktime['i'] = (int) $aResult[5];
          if (isset($aResult[7])) $mktime['s'] = (int) $aResult[7];
          else $mktime['s'] = 0;
          if (isset($aResult[9]) && $aResult[9] == 'pm') $mktime['h'] += 12;
      }
      else
      {
          return false;
      }
  
      $mktime['unix'] = $mktime['d']*24*60*60 + $mktime['h']*60*60 + $mktime['i']*60 + $mktime['s'];
      return $mktime['unix'];
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
  * Tries to convert the given parameter into a unix timestamp.
  *
  *
  * @return mixed - integer if conversion was successfull
  *                 boolean false if the provided parameter couldn't be
  *                 recognized as a date
  */
  static public function convertTimeToTimestamp( $value )
  {
    $aResult  = array();
    $aMktime  = array();
    if (preg_match('@(\d{1,2})\:(\d{1,2})(\:(\d{1,2})|)@i', $value, $aResult ) != false)
    {
      $mktime['h'] = (int) $aResult[1];
      $mktime['i'] = (int) $aResult[2];
      $mktime['s'] = (int) $aResult[4];
    }
    elseif (preg_match('@(\d{1,2})\:(\d{1,2})(\:(\d{1,2})|)(\s(am|pm)|)@i', $value, $aResult ) != false)
    {
      $mktime['h'] = (int) $aResult[1];
      $mktime['i'] = (int) $aResult[2];
      $mktime['s'] = (int) $aResult[4];
      if (isset($aResult[6]) && $aResult[6] == 'pm') $mktime['h'] += 12;
    }
    else
    {
      return false;
    }
    
    $mktime['unix'] = mktime( $mktime['h'], $mktime['i'], $mktime['s'] );
    return $mktime['unix'];
  }

  /////////////////////////////////////////////////////////////////////////////
  
  static public function convertBracketStringToArray ( $string )
  {
    $return = array();
    $treffer = array();
    if (preg_match_all('%(?:\(((?:[^\n\(\)]+)(?:\((?:[^\n\(\)]*)\)(?:[^\n\(\)]*))*)\))%', $string, $treffer))
    {
      $return = $treffer[1];
    }
    return $return;
  }
  
  /////////////////////////////////////////////////////////////////////////////

  /**
   * Converts the given string into an coordinates result DTO.
   * @param $strCoordinates a string of the format gal:sys:pla
   * @return DTOCoordinatesC
   */
  static public function convertCoordinates( $strCoordinates )
  {
    $retVal = new DTOCoordinatesC();
    $aPieces = explode( ':', $strCoordinates );
    
    if( count($aPieces) === 3 )
    {
      $retVal->iGalaxy = PropertyValueC::ensureInteger( $aPieces[0] );
      $retVal->iSystem = PropertyValueC::ensureInteger( $aPieces[1] );
      $retVal->iPlanet = PropertyValueC::ensureInteger( $aPieces[2] );
    }
    
    return $retVal;
  }
  
  /////////////////////////////////////////////////////////////////////////////

  /**
   * Tries to convert the given parameter into a unix timestamp.
   *
   *
   * @return mixed - integer if conversion was successfull
   *                 boolean false if the provided parameter couldn't be
   *                 recognized as a date
   */
  static public function convertDateTimeToTimestamp( $value )
  {
    static $month2int = array(
      'January'  =>  1,
      'Jan'      =>  1,
      'Januar'   =>  1,
      'February' =>  2,
      'Feb'      =>  2,
      'Februar'  =>  2,
      'March'    =>  3,
      'Mar'      =>  3,
      'März'     =>  3,
      'April'    =>  4,
      'Apr'      =>  4,
      'May'      =>  5,
      'Mai'      =>  5,
      'June'     =>  6,
      'Juni'     =>  6,
      'July'     =>  7,
      'Juli'     =>  7,
      'August'   =>  8,
      'Aug'      =>  8,
      'September'=>  9,
      'Sept'     =>  9,
      'October'  => 10,
      'Oct'      => 10,
      'Oktober'  => 10,
      'November' => 11,
      'Nov'      => 11,
      'December' => 12,
      'Dez'      => 12,
      'Dec'      => 12,
      'Dezember' => 12
    );
    
    $aResult  = array();
    $mktime  = array(
      'unix'    => 0,
      'hours'   => 0,
      'minutes' => 0,
      'seconds' => 0,
      'month'   => 0,
      'day'     => 0,
      'year'    => 0,
    );
    
    /*
     * match standard date.
     * See IW Account => Settings => Administration => Time
     * Format TT.MM.JJJJ HH:MM:SS (german)
     * 
     * I define that the parts only are in a certain range, to make it
     * less likely the expression matches other formats (english ones?).
     * 
     * TT   :=   00 -   39 [0-3]\d
     * MM   :=   00 -   19 [0-1]\d
     * JJJJ := 2000 - 2099 20\d\d
     * HH   :=   00 -   29 [0-2]\d
     * MM   :=   00 -   69 [0-6]\d
     * SS   :=   00 -   69 [0-6]\d
     */
    if( preg_match( '/^(?P<day>[0-3]\d)\.(?P<month>[0-1]\d)\.(?P<year>20\d\d) (?P<hours>[0-2]\d):(?P<minutes>[0-6]\d):(?P<seconds>[0-6]\d)$/', $value, $aResult ) != false )
    {
      $mktime['day']      = (int)$aResult['day'];
      $mktime['month']    = (int)$aResult['month'];
      $mktime['year']     = (int)$aResult['year'];
      $mktime['hours']    = (int)$aResult['hours'];
      $mktime['minutes']  = (int)$aResult['minutes'];
      $mktime['seconds']  = (int)$aResult['seconds'];
    }
    else if (preg_match('@(\d{1,2})\w{0,2}(\s|\.)(\d{1,2}|\w+)(\s|\.)(\d{4})(\s)(\d{1,2})\:(\d{1,2})(\:(\d{1,2})|)@i', $value, $aResult ) != false)
    {
      $mktime['day'] = (int) $aResult[1];
      $aResult3 = (int) $aResult[3];
      if (!empty($aResult3))
      {
        $mktime['month'] = (int) $aResult[3];
      }
      else
      {
        $mktime['month'] = (int) $month2int[$aResult3];
        if (!isset($month2int[$aResult3]))
            error(E_NOTICE,__FILE__,__LINE__,1,$value,$aResult);
      }
      $mktime['year'] = (int) $aResult[5];
      $mktime['hours'] = (int) $aResult[7];
      $mktime['minutes'] = (int) $aResult[8];
      if (isset($aResult[10]))
          $mktime["seconds"] = (int) $aResult[10];
    }
    elseif (preg_match('@(\d{4})(\-|\.)(\d{1,2})(\-|\.)(\d{1,2})(\s)(\d{1,2})\:(\d{1,2})(\:(\d{1,2})|)@i', $value, $aResult ) != false)
    {
      $mktime['day'] = (int) $aResult[5];
      $mktime['month'] = (int) $aResult[3];
      $mktime['year'] = (int) $aResult[1];
      $mktime['hours'] = (int) $aResult[7];
      $mktime['minutes'] = (int) $aResult[8];
      if (isset($aResult[10]))
          $mktime["seconds"] = (int) $aResult[10];
    }
    elseif (preg_match('@(\w+)(\s)(\d{1,2})\w{0,2}(\s|\,\s)(\d{4})(\s|\,\s)(\d{1,2})\:(\d{1,2})(\:(\d{1,2})|)(\s(am|pm)|)@i', $value, $aResult ) != false)
    {
      $mktime['day'] = (int) $aResult[3];
      $mktime['month'] = (int) $month2int[$aResult[1]];
      $mktime['year'] = (int) $aResult[5];
      $mktime['hours'] = (int) $aResult[7];
      $mktime['minutes'] = (int) $aResult[8];
      if (isset($aResult[10]))
          $mktime["seconds"] = (int) $aResult[10];
      if (isset($aResult[13]) && $aResult[13] == 'pm') $mktime['hours'] += 12;
    }
    elseif (preg_match('@(\d{1,2})\w{0,2}(\s|\.)(\d{1,2}|\w+)(\s|\.)(\d{4})@i', $value, $aResult ) != false)
    {
      $mktime['day'] = (int) $aResult[1];
      $aResult3 = (int) $aResult[3];
      if (!empty($aResult3))
      {
        $mktime['month'] = (int) $aResult[3];
      }
      else
      {
        $mktime['month'] = (int) $month2int[$aResult3];
      }
      $mktime['year'] = (int) $aResult[5];
      $mktime['hours'] = 0;
      $mktime['minutes'] = 0;
      $mktime['seconds'] = 0;
    }
    elseif (preg_match('@(\d{4})(\-|\.)(\d{1,2})(\-|\.)(\d{1,2})@i', $value, $aResult ) != false)
    {
      $mktime['day'] = (int) $aResult[5];
      $mktime['month'] = (int) $aResult[3];
      $mktime['year'] = (int) $aResult[1];
      $mktime['hours'] = 0;
      $mktime['minutes'] = 0;
      $mktime['seconds'] = 0;
    }
    elseif (preg_match('@(\w+)(\s)(\d{1,2})\w{0,2}(\s|\,\s)(\d{4})@i', $value, $aResult ) != false)
    {
      $mktime['day'] = (int) $aResult[3];
      $mktime['month'] = (int) $month2int[$aResult[1]];
      $mktime['year'] = (int) $aResult[5];
      $mktime['hours'] = 0;
      $mktime['minutes'] = 0;
      $mktime['seconds'] = 0;
    }
    else
    {
      return false;
    }
    
    $mktime['unix'] = mktime( $mktime['hours'], $mktime['minutes'], $mktime['seconds'], $mktime['month'], $mktime['day'], $mktime['year'] );
    return $mktime['unix'];
  }
  
  /////////////////////////////////////////////////////////////////////////////

  /**
   * Removes all thousand seperators from the argument
   */
  static public function stripThousandSeperators ( $value )
  {
    $retVal = false;
    $supportedThousandSeperators = ConfigC::get( 'lib.aThousandSeperators' );

    $retVal = str_replace( $supportedThousandSeperators, '', $value );

    return $retVal;
  }
  
  /////////////////////////////////////////////////////////////////////////////

  /**
   * cast a string to float
   * @deprecated
   *
   * Use PropertyValueC::ensureFloat instead.
   *
   * Most parsers will return strings, and castFloat will handle them
   * well. However, with growing support for XML-parsing, more and more
   * parsers might return SimpleXmlElement-Objects or even other Structures.
   *
   * My suggestion is to move this method to PropertyValueC.php and to make
   * it private or protected, to hold the lib's interface consistent.
   * There should only be one method that cares for converting and setting
   * float values.
   */
  static public function castFloat( $value )
  {
    $retVal = false;

    $treffer = array();
    if (preg_match('/(?P<digit>.*)[^\d](?P<part>\d{0,2})$/', $value, $treffer))
    {
      $digit = $treffer['digit'];
      $digit = self::stripThousandSeperators($digit);
      $digit = (int) $digit;

      $part = $treffer['part'];
      $pot = strlen($part);
      $part = (int) $part;

      if ($digit >= 0) {
        $retVal = $digit + ($part / Pow(10, $pot));
      } else {
        $retVal = $digit - ($part / Pow(10, $pot));
      }
    }
    else
    {
      $retVal = self::stripThousandSeperators($retVal);
    }

    return $retVal;
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * Includes resource names into a xml document
   *
   * The method takes a source xml, which is processed and searched
   * for occurences of resource definitions. For every definition that
   * is found, the corresponding resource name retrieved from
   * http://www.icewars.de/portal/xml/de/ressourcen.xml will be injected.
   *
   * @param mixed $xml the method tries to figure out what you provided.
   *        It supports xml-strings, xml files, DOMDocuments and
   *        SimpleXMLElements.
   * $param string $language optional. Providing this parameter, you can
   *        define if you want the german or the englisch resource names
   *        being injected. Valid inputs are 'de' and 'en'. The default
   *        id 'de'.
   * @return mixed. Reference to the DOMDocument representing the new XML
   *        document or NULL if an error occured.
   */
  static public function &xmlInjectResourceNames( $xml, $language = 'de' )
  {
    $retVal = NULL;
    $xsltProcessor = new XSLTProcessor();
    $docInjectionXsl = new DOMDocument();
    $docSourceXml = NULL;
    $filenameResources = "http://www.icewars.de/portal/xml/$language/ressourcen.xml";
    $filenameInjectionXsl = ConfigC::get( 'path.xslt' ) . DIRECTORY_SEPARATOR . 'injectResourceNames.xsl';

    $docInjectionXsl->load( $filenameInjectionXsl );


    //set the name of the resource.xml that shall be used.
    $xpath = new DOMXPath( $docInjectionXsl );
    $xpathResult = $xpath->query( '//xsl:variable[@name="filenameResources"]' );
    
    if( $xpathResult instanceof DOMNodeList && $xpathResult->length === 1 )
    {
      $domVariable = $xpathResult->item(0);
      $domVariable->nodeValue = $filenameResources;
    }

    
    $xsltProcessor->importStyleSheet( $docInjectionXsl );

    if( $xml instanceof DOMDocument )
    {
      $docSourceXml =& $xml;
    }
    elseif( $xml instanceof SimpleXMLElement )
    {
      $docSourceXml = dom_import_simplexml( $xml );
    }
    elseif( is_string($xml) )
    {
      $docSourceXml = new DOMDocument();
      
      //treat the param as filename...
      $fRetVal = $docSourceXml->load( $xml );
      
      //okay, it was no file... Treat it as xml-string
      if( $fRetVal === false )
      {
        $fRetVal = $docSourceXml->loadXML( $xml );
        
        //hm, it was neigther a xml-string?
        if( $fRetVal === false )
        {
          //I give up.
          $docSourceXml = NULL;
        }
      }
    }

    //If we found a way to open the source xml, do the real work
    if( $docSourceXml instanceof DOMDocument )
    {
      $retVal = $xsltProcessor->transformToDoc( $docSourceXml );

      //TODO: error processing
      if( $retVal === false )
      {
        $retVal = NULL;
      }
    }

    return $retVal;
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * For debugging with "The Regex Coach" which doesn't support named groups
   */
  static public function removeNamedGroups( $regularExpression )
  {
    $retVal = preg_replace( '/\?P<\w+>/', '', $regularExpression );
    return $retVal;
  }
  
  /////////////////////////////////////////////////////////////////////////////

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



?>
