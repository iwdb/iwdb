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
 * A DTO describing the modifiers of a planet
 */
class DTOPlanetModifiersC
{
  /**
   * @soap
   * @var float $fResearch
   */
  public $fResearch = 0.0;

  /**
   * @soap
   * @var float $fBuildingCosts
   */
  public $fBuildingCosts = 0.0;

  /**
   * @soap
   * @var float $fBuildingTime
   */
  public $fBuildingTime = 0.0;

  /**
   * @soap
   * @var float $fShipCosts
   */
  public $fShipCosts = 0.0;

  /**
   * @soap
   * @var float $fShipTime
   */
  public $fShipTime = 0.0;
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

?>
