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
 * A DTO describing a planet
 */
class DTOPlanetC
{
  /**
   * @soap
   * @var string $strOwnerName the name of the planets owner
   */
  public $strOwnerName = '';

  /**
   * @soap
   * @var string $strOwnerAllianceTag the name of the planet owners alliance
   */
  public $strOwnerAllianceTag = '';

  /**
   * @soap
   * @var DTOCoordinatesC $objCoordinates an object describing
   *      the coordinates of the planet.
   */
  public $objCoordinates = DTOCoordinatesC;

  /**
   * @soap
   * @var string $strPlanetType the type of the planet
   * @deprecated should be an enum value, use
   * @see $ePlanetType
   */
  public $strPlanetType = '';

  /**
   * @soap
   * @var string $ePlanetType the type of the planet
   * @todo check how enums can be transformed and transported
   */
  public $ePlanetType = '';

  /**
   * @soap
   * @var string $strObjectType the type of the object
   * @deprecated should be an enum value
   * @see $eObjectType
   */
  public $strObjectType = '';

  /**
   * @soap
   * @var string $eObjectType the type of the object at these coordinates
   * @todo check how enums can be transformed and transported
   */
  public $eObjectType = '';

  /**
   * @soap
   * @var DTOResourceDepositsC $objCoordinates an object describing
   *      the coordinates of the planet.
   */
  public $objResourceDeposits = DTOResourceDepositsC;

  /**
   * @soap
   * @var float $fGravity the gravity of the planet
   */
  public $fGravity = 0.0;

  /**
   * @soap
   * @var float $fLivingConditions the living conditions of the planet
   */
  public $fLivingConditions = 0.0;

  /**
   * @soap
   * @var integer $iMaximumPopulation the maximum number of people
   *      that can live on the planet
   */
  public $iMaximumPopulation = 0;

  /**
   * @soap
   * @var array $aSpecials of strings that define the specials of the planet.
   *      All possible values are defined in ePlanetSpecials.
   */
  public $aSpecials = array();

  /**
   * @soap
   * @var DTOPlanetModifiersC $objPlanetModifiers an object describing
   *      the modifiers of the planet.
   */
  public $objPlanetModifiers = DTOPlanetModifiersC;

  /**
   * @soap
   * @var int $iTimestamp when will the planet be reset. This class
   *      really delivers the unix timestamp, instead of the offset
   *      the geoscan itself delivers.
   */
  public $iResetTimestamp = 0;
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

?>
