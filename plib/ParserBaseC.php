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
 * @package libIwParsers
 * @subpackage parsers
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Base class for parsers
 *
 * This class provides attributes and methods, needed by many concrete
 * parsers. By collecting them in this base class, we reduce redundant
 * code.
 */
class ParserBaseC extends ParserFunctionC
{
  const ALL_WHITESPACE  = 'ALL_WHITESPACE';
  const LEADING_WHITESPACE  = 'LEADING_WHITESPACE';
  const TRAILING_WHITESPACE = 'TRAILING_WHITESPACE';
  
  /**
   * @var string identifier of the concrete parser
   */
  private $_strIdentifier = '';

  /**
   * @var string regExp, checking if the concrete parser is able to parse the
   *             text provided.
   */
  private $_reCanParseText = '';

  /**
   * @var string regExp, finding the begin of the data section
   */
  private $_reBeginData = '';
  
  /**
   * @var string regExp, finding the end of the data section
   */
  private $_reEndData = '';

  /**
   * @var string text to be parsed
   */
  private $_strText = '';

  /////////////////////////////////////////////////////////////////////////////

  /**
   * protected constructor
   *
   * As this class isn't meant to do anything on its own,
   * we protect it from beeing instantiated.
   *
   * Only deriving classes shall be able to instantiate it.
   */
  protected function __construct()
  {
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * Strips useless boiler plate text
   *
   * Many pages of IceWars contain data, that is simply of no use for us.
   * By using _reBeginData and _reEndData regular expressions, we can strip
   * down the text to the data sections.
   */
  protected function stripTextToData()
  {
    if( $this->getText() === '' )
    {
      return;
    }
    
    $reStartData = $this->getRegExpStartData();
    $reEndData = $this->getRegExpEndData();
    
    if( $reStartData !== '' )
    {
      $aStart = preg_split( $reStartData, $this->getText() );
      
      if( isset($aStart[1]) )
      {
        $this->setText( trim($aStart[1]) );
      }
    }
    
    if( $reEndData !== '' )
    {
      $aStart = preg_split( $reEndData, $this->getText() );
    
      if( isset($aStart[0]) )
      {
        $this->setText( trim($aStart[0]) );
      }
    }
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::canParseText()
   */
  public function canParseText( &$text )
  {
    $retVal = preg_match( $this->getRegExpCanParseText(), $text, $aMatches );

    switch( $retVal )
    {
    case 0:
        return false;
        break;
    case $retVal > 0:
        $this->setText( $text );
        return true;
        break;
    case FALSE:
        $e = get_class($this) . '::canParseText - ERROR in preg_match...';
        throw new Exception( $e );
        break;
    }
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::getIdentifier()
   */
  public function getIdentifier()
  {
    return $this->_strIdentifier;
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function getRegExpCanParseText()
  {
    return $this->_reCanParseText;
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function getRegExpStartData()
  {
    return $this->_reBeginData;
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function getRegExpEndData()
  {
    return $this->_reEndData;
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function getText()
  {
    return $this->_strText;
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function setIdentifier( $value )
  {
    $this->_strIdentifier = PropertyValueC::ensureString($value);
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function setRegExpCanParseText( $value )
  {
    $this->_reCanParseText = PropertyValueC::ensureString($value);
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function setRegExpBeginData($value)
  {
    $this->_reBeginData = PropertyValueC::ensureString($value);
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function setRegExpEndData($value)
  {
    $this->_reEndData = PropertyValueC::ensureString($value);
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @todo I'm not sure about all these utf8 replacements,
   *       can you modify them to use utf8_encode/utf8_decode?
   *       Because eclipse/svn/dontknowwhat mixes them up all
   *       the time, and I don't want to break your tool!
   */
  public function setText($value)
  {
    //replace different line endings by \n
    $replacements = array (
      chr(13) . chr(10) => chr(10),   //windows
      //chr(10)           => chr(10),   //linux
      chr(13)           => chr(10),   //mac
      
      'blubbernde Gallertmasse'  => 'Bevölkerung',
      'Erdbeermarmelade'         => 'Stahl',
      'Erdbeerkonfitüre'         => 'VV4A',
      'Erdbeeren'                => 'Eisen',
      'Brause'                   => 'chem. Elemente',
      'Vanilleeis'               => 'Eis',
      'Eismatsch'                => 'Wasser',
      'Traubenzucker'            => 'Energie',
      'Kekse'                    => 'Credits',

      'Systrans (Systransporter Klasse 1)'  => 'Systrans (Systemtransporter Klasse 1)',
      'Lurch (Systransporter Klasse 2)'     => 'Lurch (Systemtransporter Klasse 2)',
      'Crux (Systransporter Kolonisten)'    => 'Crux (Systemtransporter Kolonisten)',
      'Pflaumenmus (Carrier)'               => 'Pflaumenmus  (kleiner Carrier)',
      
      '\\\%' => '\%',

    );

    
    
    /**
     * Okay...
     * This is to convert the utf-8 input into plain ascii, right?
     *
     * Then this is also the problem why you can't simply check for
     * utf-8 encoded characters any more.
     *
     * Example:
     * 1) utf-8 encoded 'ä' comes in (hex C3A4)
     * 2) utf8_decode turns it into ascii 'ä' (hex E4)
     * 3) now every processing unit (methods we call, our development tools, ...)
     *    that expects UTF-8 data will have problems, because this 'E4' isn't
     *    a valid UTF-8 character...
     *
     * I would suggest to remove the following lines of code and the _wasUTF8 member.
     * I can't see a reason why we should mix UTF-8 and ASCII. Everything should be
     * UTF-8 encoded.
     */
//    if( mb_detect_encoding($value,"UTF-8",true) ) #gisis db hack
//    {
//      $value = utf8_decode( $value );
//      $this->_wasUTF8 = true;
//    }
  
    
    
    if ( get_magic_quotes_gpc() )
    {
      $replacements["\\\""] = "\"";
      $replacements["\\\'"] = "\'";
    }
    
    $value = str_replace( array_keys($replacements),
                          array_values($replacements),
                          $value );
    
    //set text
    $this->_strText = PropertyValueC::ensureString($value);
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function cleanupLineEndings( $text )
  {
    //replace different line endings by \n
    $replacements = array (
      chr(13) . chr(10) => chr(10),   //windows
      //chr(10)           => chr(10),   //linux
      chr(13)           => chr(10),   //mac
    );
    
    return str_replace( array_keys($replacements), array_values($replacements), $text );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * Strips whitespace from lines.
   *
   * @param $text the text whos lines shall be striped
   * @param $option one of ParserBaseC::XYZ_WHITESPACE constants
   * @return the striped text
   */
  protected function stripWhitespace( $text, $option = ParserBaseC::ALL_WHITESPACE )
  {
    $replacements = array();
        
    /**
     * It's important to add the u switch.
     * Otherwise, some multibyte characters may be treated
     * as whitespace.
     * e.g. 'à' is encoded as 'C3A0' in UTF8, but 'A0' will
     *      be treated as whitespace if not in unicode mode
     */
    switch( $option )
    {
    case ParserBaseC::LEADING_WHITESPACE:
        $replacements['/^\s+/um'] = '';
        break;
    case ParserBaseC::TRAILING_WHITESPACE:
        $replacements['/\s+$/um'] = '';
        break;
    case ParserBaseC::ALL_WHITESPACE:
    default:
        $replacements['/^\s+/um'] = '';
        $replacements['/\s+$/um'] = '';
        break;
    }
    
    $toReplace    = array_keys  ($replacements);
    $replaceWith  = array_values($replacements);
    
    $retVal = preg_replace( $toReplace, $replaceWith, $text );
    
    //TODO: error processing
    if( $retVal === NULL )
    {
      
    }
    
    return $retVal;
  }

  /////////////////////////////////////////////////////////////////////////////

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



?>
