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



return array(
  'lib' =>  array(
    'aThousandSeperators' => array( '.', ' ', "'", '"', 'k', '`', '´',','),
    'aRegisteredParsers'  => array(
      array(
        'filename'  =>  dirname(__FILE__) . DIRECTORY_SEPARATOR .
                        'de'              . DIRECTORY_SEPARATOR .
                        'parsers'         . DIRECTORY_SEPARATOR .
                        'ParserMsgGeoscansC.php',
        'classname' => 'ParserMsgGeoscansC',
      ),
      array(
        'filename'  =>  dirname(__FILE__) . DIRECTORY_SEPARATOR .
                        'de'              . DIRECTORY_SEPARATOR .
                        'parsers'         . DIRECTORY_SEPARATOR .
                        'ParserUniversumC.php',
        'classname' => 'ParserUniversumC',
      ),
      array(
        'filename'  =>  dirname(__FILE__) . DIRECTORY_SEPARATOR .
                        'de'              . DIRECTORY_SEPARATOR .
                        'parsers'         . DIRECTORY_SEPARATOR .
                        'ParserMsgScansC.php',
        'classname' => 'ParserMsgScansC',
      ),
    ),
    'aPathesAutoload' => array(
      dirname(__FILE__),
      dirname(__FILE__) . DIRECTORY_SEPARATOR . 'de',
      dirname(__FILE__) . DIRECTORY_SEPARATOR . 'enums',
      dirname(__FILE__) . DIRECTORY_SEPARATOR . 'de'
                        . DIRECTORY_SEPARATOR . 'parsers',
      dirname(__FILE__) . DIRECTORY_SEPARATOR . 'de'
                        . DIRECTORY_SEPARATOR . 'parserResults',
      dirname(__FILE__) . DIRECTORY_SEPARATOR . '..'
                        . DIRECTORY_SEPARATOR . '3rdParty'
                        . DIRECTORY_SEPARATOR . 'dBug',
    ),
  ),
  'test' =>  array(
  ),
  'path' =>  array(
    '3rdParty'  => array(
      'simpletest'  =>  dirname(__FILE__) . DIRECTORY_SEPARATOR .
                        '..'              . DIRECTORY_SEPARATOR .
                        '3rdParty'        . DIRECTORY_SEPARATOR .
                        'simpletest_1.0.1',
      'dBug'        =>  dirname(__FILE__) . DIRECTORY_SEPARATOR .
                        '..'              . DIRECTORY_SEPARATOR .
                        '3rdParty'        . DIRECTORY_SEPARATOR .
                        'dBug',
    ),
    'rng' =>  dirname(__FILE__) . DIRECTORY_SEPARATOR .
              'xml'             . DIRECTORY_SEPARATOR .
              'relaxng',
    'xslt' => dirname(__FILE__) . DIRECTORY_SEPARATOR .
              'xml'             . DIRECTORY_SEPARATOR .
              'xslt',
  ),
);



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



?>
