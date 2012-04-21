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
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



/**
 * A DTO describing coordinates in the IW universe
 */
class DTOCoordinatesC
{
  /**
   * @soap
   * @var int $iGalaxy
   */
  public $iGalaxy = 0;

  /**
   * @soap
   * @var int $iSystem
   */
  public $iSystem = 0;

  /**
   * @soap
   * @var int $iPlanet
   */
  public $iPlanet = 0;
}



/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



?>
