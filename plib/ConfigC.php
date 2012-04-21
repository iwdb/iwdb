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

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



class ConfigC
{

  /////////////////////////////////////////////////////////////////////////////

  private $_aConfig = NULL;
  static private $_instance = NULL;

  /////////////////////////////////////////////////////////////////////////////

  static private function &getInstance()
  {
    if( self::$_instance === NULL )
    {
      self::$_instance = new ConfigC;
      self::$_instance->_aConfig = include(
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php' );
    }
    
    return self::$_instance;
  }

  /////////////////////////////////////////////////////////////////////////////

  static public function get( $key )
  {
    $me =& ConfigC::getInstance();
    $retVal =& $me->_aConfig;
    $breadcrumbs = explode('.', $key);
    
    foreach( $breadcrumbs as $breadcrumb )
    {
      if( array_key_exists($breadcrumb, $retVal) )
      {
        $retVal =& $retVal[$breadcrumb];
      }
    }
    
    return $retVal;
  }

  /////////////////////////////////////////////////////////////////////////////

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



?>
