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
 * A DTO describing the resource deposits of a planet
 */
class DTOResourceDepositsC
{
  /**
   * @soap
   * @var float $fIron
   */
  public $fIron = 0.0;

  /**
   * @soap
   * @var float $fIronTechTeam
   */
  public $fIronTechTeam = 0.0;

  /**
   * @soap
   * @var float $fChemicals
   */
  public $fChemicals = 0.0;

  /**
   * @soap
   * @var float $fChemicalsTechTeam
   */
  public $fChemicalsTechTeam = 0.0;

  /**
   * @soap
   * @var float $fIce
   */
  public $fIce = 0.0;

  /**
   * @soap
   * @var float $fIceTechTeam
   */
  public $fIceTechTeam = 0.0;

}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

?>
