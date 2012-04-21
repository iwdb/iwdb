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
 * A DTO describing a number of some objects (buildings/ ships/ whatever)
 */
class DTOOwnedObjectsCounterC
{
  /**
   * @soap
   * @var string $strOwnerName identifies the owner of the object
   */
  public $strOwnerName = "";

  /**
   * @soap
   * @var string $strObjectName some string that identifies your object
   */
  public $strObjectName = "";

  /**
   * @soap
   * @var integer $iCounter number of objects
   */
  public $iCounter = 0;
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

?>
