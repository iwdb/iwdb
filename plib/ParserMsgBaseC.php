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
 * @subpackage parsers
 */

///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

/**
 * Base class for msgparsers
 *
 * This class provides attributes and methods, needed by many concrete
 * parsers. By collecting them in this base class, we reduce redundant
 * code.
 */
class ParserMsgBaseC extends ParserFunctionC
{
  /**
   * @var string identifier of the concrete parser
   */
  private $_strIdentifier = '';

  /**
   * @var string representing the type
   */
  private $_reCanParseMsg = '';

  /**
   * @var object msg to be parsed
   */
  private $_strMsg = '';

  /////////////////////////////////////////////////////////////////////////////

  /**
   * protected constructor
   *
   * As this class isn't meant to do anything on its own,
   * we protect it from beeing instantiated.
   *
   * Only deriving classes shall be able to instantiate it.
   */
  protected function __construct()
  {
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::canParseMsg()
   */
  public function canParseMsg( $msg )
  {
    if( $msg->eParserType == $this->_reCanParseMsg )
    {
      $this->setMsg( $msg );
      return true;
    }
    else
    {
      $e = get_class($this) . '::canParseMsg - ERROR in equation of...';
      throw new Exception( $e );
      break;
    }
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * @see ParserI::getIdentifier()
   */
  public function getIdentifier()
  {
    return $this->_strIdentifier;
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function getMsg()
  {
    return $this->_strMsg;
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function setIdentifier( $value )
  {
    $this->_strIdentifier = PropertyValueC::ensureString($value);
  }

  /////////////////////////////////////////////////////////////////////////////

  protected function setCanParseMsg( $value )
  {
    $this->_reCanParseMsg = PropertyValueC::ensureString($value);
  }

  /////////////////////////////////////////////////////////////////////////////

  public function setMsg($value)
  {
    //set msg
    $this->_strMsg = $value;
  }
 
  /////////////////////////////////////////////////////////////////////////////

  /**
   * matches a Resource Line
   *
   * Resource Count
   */
  protected function getRegularExpressionResources()
  {
    $reResourceName     = $this->getRegExpResource();
  $reResourceCount     = $this->getRegExpDecimalNumber();

  #Just even don't think to ask anything about this regexp, fu!
    $regExp  = '/
          (?P<resource_name>'.$reResourceName.')
          [\s\t]+
          (?P<resource_count>'.$reResourceCount.')
          [\s\n\r\t]*
        /mx';
    return $regExp;
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * matches a Schiffe Line
   *
   * Schiffe Count
   */
  protected function getRegularExpressionSchiffe()
  {
    $reSchiffeName     = $this->getRegExpSchiffe();
  $reSchiffeCount     = $this->getRegExpDecimalNumber();

  #Just even don't think to ask anything about this regexp, fu!
    $regExp  = '/
          (?P<schiff_name>'.$reSchiffeName.')
          [\s\t]+
          (?P<schiffe_count>'.$reSchiffeCount.')
          [\s\n\r\t]*
        /mx';
    return $regExp;
  }
  
 
}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



?>
