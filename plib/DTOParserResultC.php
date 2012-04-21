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
 * Class returned by all parsers
 *
 * This class only carries data, it provides no functionality. This is
 * important for transfers over the network.
 */
class DTOParserResultC
{
  /**
   * @soap
   * @var string $strIdentifier identifier of the parser that generater these
   *      results
   */
  public $strIdentifier = '';

  /**
   * @soap
   * @var bool $bSuccessfullyParsed hopefully true, false if any errors occured
   */
  public $bSuccessfullyParsed = false;

  /**
   * @soap
   * @var array $aErrors if any errors occured, this array holds additional
   *      information
   */
  public $aErrors = array();

  /**
   * @soap
   * @var object $objResultData the DTO the parser creates.
   */
  public $objResultData = NULL;

  /////////////////////////////////////////////////////////////////////////////

  public function __construct( $parent )
  {
    if( $parent instanceof ParserI )
    {
      $this->strIdentifier = $parent->getIdentifier();
    }
  }

  /////////////////////////////////////////////////////////////////////////////

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



?>
