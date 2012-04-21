<?php
/*
 * ----------------------------------------------------------------------------
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <martin@martimeo.de> wrote this file. As long as you retain
 * this notice you can do whatever you want with this stuff. If we meet some
 * day, and you think this stuff is worth it, you can buy me a beer in return.
 * Martin Martimeo
 * ----------------------------------------------------------------------------
 */
/**
 * @author Martin Martimeo <martin@martimeo.de>
 * @package libIwParsers
 * @subpackage parsers_de
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Result DTO of parser de_wirtschaft_geb
 */
class DTOParserWirtschaftGebResultC
{
  /**
   * @soap
   * @var array $aAreas an array of objects of type
   *      DTOParserWirtschaftGebAreaResultC, which represent the complete parsed area
   *      at the building economy list
   */
  public $aAreas = array();

  /**
   * @soap
   * @var array $aKolos an array of objects of type
   *      DTOParserWirtschaftGebKoloResultC, which represent the complete parsed kolos
   *      at the building economy list
   */
  public $aKolos = array();  
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserWirtschaftGebKoloResultC
{
  /**
   * @soap
   * @var array $aCoords the coords
   */
  public $aCoords = '';

  /**
   * @soap
   * @var string $strCoords the coords
   */
  public $strCoords = '';  

  /**
   * @soap
   * @var string $strObjectType the type of this kolo
   */
  public $strObjectType = '';  
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserWirtschaftGebAreaResultC
{
  /**
   * @soap
   * @var array $aBuildings an array of objects of type
   *      DTOParserWirtschaftGebBuildingResultC, which represent a building line
   *      at the building economy list
   */
  public $aBuildings = '';

  /**
   * @soap
   * @var string $strAreaName the name of the area
   */
  public $strAreaName = '';    
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

class DTOParserWirtschaftGebBuildingResultC
{

  /**
   * @soap
   * @var array (coords => building_count) $aCounts 
   */
  public $aCounts = '';    

  /**
   * @soap
   * @var string $strBuildingName the name of the building
   */
  public $strBuildingName = '';     
}

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

?>
