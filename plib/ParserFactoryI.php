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
 * @subpackage interfaces
 */

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



/**
 * A factory for instantiating parsers for a given text.
 *
 * Usage is intended to be as follows:
 *
 * @code
 *  $text = ...                    //get the text from somewhere...
 *  $parserFactoryClassName = ...  //get the classname of a class implementing
 *                                //ParserFactoryI
 *  $rcParserFactory = new ReflectionClass($parserFactoryClassName);
 *  $parser = NULL;
 *  $parserResult = NULL;
 *
 *
 *  //--- get the parser -------------------------------------------------------
 *
 *  if( $rcParserFactory->implementsInterface('ParserFactoryI') )
 *  {
 *    $parserFactory = $parserFactoryClassName::getInstance();
 *    $aParserIds = $parserFactory->getParserIdsFor( $text );
 *
 *    if( count($aParserIds) === 0 )
 *    {
 *      //error handling
 *    }
 *    elseif( count($aParserIds) === 1 )
 *    {
 *      $parser = $parserFactory->getParser( $text );
 *    }
 *    elseif( count($aParserIds) > 1 )
 *    {
 *      //implement mechanism to let the user decide which parser shall be
 *      //used to parse the text.
 *      //Then, use syntax:
 *      //$parser = $parserFactory->getParser( $text, $selectedParserId );
 *    }
 *  }
 *  else
 *  {
 *    //error handling
 *  }
 *
 *
 *  //--- parse the text -------------------------------------------------------
 *
 *  if( $parser instanceof ParserI )
 *  {
 *    $parserResult = new DTOParserResultC( $parser );
 *    $parser->parse( $parserResult );
 *  }
 *  else
 *  {
 *    //error handling
 *  }
 *
 *
 *  //--- proceed --------------------------------------------------------------
 *
 *  if( $parserResult instanceof DTOParserResultC )
 *  {
 *    //do whatever you need to do...
 *  }
 *
 * @endcode
 */
interface ParserFactoryI
{

  ///////////////////////////////////////////////////////////////////////

  /**
   * Returns an instance of the factory.
   *
   * @return instance of the factory.
   */
  static public function getInstance();

  ///////////////////////////////////////////////////////////////////////

  /**
   * Checks for all known parsers if they can parse the provided text.
   *
   * @param $text string, the text to be parsed
   * @return  array of strings, ids of the parsers, that can parse the
   *          provided text.
   */
  public function getParserIdsFor( $text );

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
  public function getParser( $text, $parserId = '' );

  ///////////////////////////////////////////////////////////////////////

}



/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



?>
