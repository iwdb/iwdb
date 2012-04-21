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

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Interface for parsers
 *
 * Every parser needs to implement this interface in order to register
 * with the parser factory.
 */
interface ParserI
{

  /////////////////////////////////////////////////////////////////////////////

  /**
   * Returns the identifier of this parser
   *
   * The identifier of each parser is defined by the following rules:
   * 1. "de_" if the parser recognises a german IceWars page.
   * 2. the value of action parameter
   * 3. if sub pages exist, "_" + value of the typ parameter
   *
   * E.g.
   * - german server, universe page. ActionId := "de_universum"
   * - german server, start page. ActionId := "de_main"
   * - german server, economics page. ActionId := "de_wirtschaft"
   * - german server, economics page, subpage colony information.
   *   ActionId := "de_wirtschaft_planiinfo"
   * - german server, economics page, subpage resource overview.
   *   ActionId := "de_wirtschaft_planiress"
   *
   * @return string identifier of the parser
   */
  public function getIdentifier();

  /////////////////////////////////////////////////////////////////////////////

  /**
   * Checks if the parser can handle the text provided
   *
   * This method is intendet to find the correct parser for a given
   * text.
   *
   * It checks, if this concrete parser can handle the text provided. It
   * normally should check this, by searching for simple, small identifiers,
   * unique within a page.
   *
   * If this concrete parser thinks it can handle the text, it should copy the
   * text and save it for further operations.
   *
   * @param string &$text a reference to the text to be parsed
   * @return bool if the parser claims to be the right one for the text provided
   */
  public function canParseText( &$text );

  /////////////////////////////////////////////////////////////////////////////

  /**
   * Parses a text
   *
   * This method actually parses the text, the concrete parser has saved
   * previously, when it was asked if it could do the job.
   *
   * @param DTOParserResult &$parserResult - Ergebnisse und Fehler des Parsevorgangs
   */
  public function parseText( DTOParserResultC& $parserResult );

  /////////////////////////////////////////////////////////////////////////////

}

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

?>
