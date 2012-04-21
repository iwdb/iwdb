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

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



class AutoloaderC
{

  static private $_instance = NULL;
  private $_aPathes = array();

  ///////////////////////////////////////////////////////////////////////

  /**
   * Returns an instance of the factory.
   *
   * @return instance of the factory.
   */
  static private function getInstance()
  {
    if( self::$_instance === NULL )
    {
      self::$_instance = new AutoloaderC();
    }

    return self::$_instance;
  }

  ///////////////////////////////////////////////////////////////////////

  static public function addPath( $pathName )
  {
    $instance = AutoloaderC::getInstance();
    
    if( is_dir($pathName) &&
        in_array($pathName, $instance->_aPathes) === false )
    {
      $instance->_aPathes[] = $pathName;
    }
  }

  ///////////////////////////////////////////////////////////////////////

  static public function load( $className )
  {
    $instance = AutoloaderC::getInstance();
    
    foreach($instance->_aPathes as $path)
    {
      if( file_exists($path . DIRECTORY_SEPARATOR . $className . '.php') )
      {
        //the load-method will only be called for classes that haven't
        //been defined. So there should be no need for require_once.
        //Hopefully this gives a bit more speed.
        require( $path . DIRECTORY_SEPARATOR . $className . '.php' );
        break;
      }
    }
  }

  ///////////////////////////////////////////////////////////////////////

}



/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////



?>
