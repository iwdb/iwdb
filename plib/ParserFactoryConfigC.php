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
 * @subpackage helpers
 */

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



class ParserFactoryConfigC implements ParserFactoryI
{

  static private $_instance    = NULL;
  private $_aRegisteredParsers = array();
  

  ///////////////////////////////////////////////////////////////////////

  /**
   * constructor of the factory.
   *
   * Use getInstance() to get an instance of the factory.
   */
  private function __construct()
  {
    $aConfiguredParsers = ConfigC::get('lib.aRegisteredParsers');
    $rcParser           = NULL;
    
    foreach( $aConfiguredParsers as $parserInfo )
    {
      require_once( $parserInfo['filename'] );
      $rcParser = new ReflectionClass($parserInfo['classname']);
      
      if( $rcParser->implementsInterface('ParserI')
          && $rcParser->isInstantiable() )
      {
        $parser = $rcParser->newInstance();
        $this->_aRegisteredParsers[ $parser->getIdentifier() ] = $parser;
      }
    }
  }

  ///////////////////////////////////////////////////////////////////////

  /**
   * Returns an instance of the factory.
   *
   * @return instance of the factory.
   */
  static public function getInstance()
  {
    if( self::$_instance === NULL )
    {
      self::$_instance = new ParserFactoryConfigC();
    }
    
    return self::$_instance;
  }

  ///////////////////////////////////////////////////////////////////////

  /**
   * Checks for all known parsers if they can parse the provided text.
   *
   * @param $text string, the text to be parsed
   * @return  array of strings, ids of the parsers, that can parse the
   *          provided text.
   */
  public function getParserIdsFor( $text )
  {
    $retVal = array();
    
    $this->cleanupText( $text );
    
    foreach( $this->_aRegisteredParsers as $parserId => $parser )
    {
      if( $parser->canParseText($text) )
      {
        $retVal[] = $parserId;
      }
    }
    
    return $retVal;
  }

  ///////////////////////////////////////////////////////////////////////

  /**
   * Gets an instance of the first parser the factory knows, that can
   * parse the provided text. You can define the parser that is to be
   * used to parse the text, by providing its id as second parameter.
   *
   * @param $text string, the text to be parsed
   * @param $parserId string, the id of a known parser that shall be used
   *        to parse the provided text
   * @return  mixed
   *          ParserI - instance of the parser that can be used to parse
   *                    the provided text.
   *          boolean - false, if
   *                    1) no parser was found that could parse the provided
   *                       text
   *                    2) you provided a parser id that does not exist
   *                    3) you provided a parser id of an existent parser, but
   *                       that parser isn't able to parse the provided text.
   */
  public function getParser( $text, $parserId = '' )
  {
    $retVal = false;
    
    $this->cleanupText( $text );
    
    if( $parserId === '' )
    {
      foreach( $this->_aRegisteredParsers as $parserId => $parser )
      {
        if( $parser->canParseText($text) )
        {
          $retVal = $this->_aRegisteredParsers[$parserId];
          break;
        }
      }
    }
    elseif( $parserId !== ''
            && array_key_exists($parserId, $this->_aRegisteredParsers)
            && $this->_aRegisteredParsers[$parserId]->canParseText($text) )
    {
      $retVal = $this->_aRegisteredParsers[$parserId];
    }

    return $retVal;
  }

  ///////////////////////////////////////////////////////////////////////

  protected function cleanupText( &$text )
  {
    //replace different line endings by \n
    $replacements = array (
      chr(13) . chr(10) => chr(10),   //windows
      //chr(10)           => chr(10),   //linux
      chr(13)           => chr(10),   //mac
    );
  
    if ( get_magic_quotes_gpc() )
    {
      $replacements["\\\""] = "\"";
      $replacements["\\\'"] = "\'";
    }
    
    $text = str_replace( array_keys($replacements),
                         array_values($replacements),
                         $text );
  }
  
  ///////////////////////////////////////////////////////////////////////

}



/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



?>
