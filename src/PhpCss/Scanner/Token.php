<?php
/**
* PhpCssScannerToken represents a token from a scan.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Scanner
*/

/**
* PhpCssScannerToken represents a token from a scan.
*
* @package PhpCss
* @subpackage Scanner
*
* @property-read integer $type
* @property-read string $content
* @property-read integer $length
* @property-read integer $position
*/
class PhpCssScannerToken {

  // special any token (a joker)
  const ANY = 255;

  //whitespace
  const WHITESPACE = 1;
  const NUMBER = 2;

  //simple selectors
  const TYPE_SELECTOR = 10;
  const CLASS_SELECTOR = 11;
  const ID_SELECTOR = 12;
  const PSEUDO_CLASS = 13;

  // attribute selectors - [...]
  const ATTRIBUTE_SELECTOR_START = 20;
  const ATTRIBUTE_SELECTOR_END = 21;
  const ATTRIBUTE_OPERATOR = 22;

  // parentheses - (...)
  const PARENTHESES_START = 31;
  const PARENTHESES_END = 32;

  //selector separator
  const COMBINATOR = 41;
  const SEPARATOR = 42;

  //single quoted strings
  const SINGLEQUOTE_STRING_START = 100;
  const SINGLEQUOTE_STRING_END = 101;
  // double quoted strings
  const DOUBLEQUOTE_STRING_START = 102;
  const DOUBLEQUOTE_STRING_END = 103;
  // string general
  const STRING_CHARACTERS = 110;
  const STRING_ESCAPED_CHARACTER = 111;

  private static $_names = array(
    self::ANY => 'ANY',
    self::WHITESPACE => 'WHITESPACE',
    self::NUMBER => 'NUMBER',
    self::TYPE_SELECTOR => 'SIMPLESELECTOR_TYPE',
    self::CLASS_SELECTOR => 'SIMPLESELECTOR_CLASS',
    self::ID_SELECTOR => 'SIMPE_SELECTOR_ID',
    self::PSEUDO_CLASS => 'PSEUDOCLASS',
    self::ATTRIBUTE_SELECTOR_START => 'SIMPLESELECTOR_ATTRIBUTE_START',
    self::ATTRIBUTE_SELECTOR_END => 'SIMPLESELECTOR_ATTRIBUTE_END',
    self::ATTRIBUTE_OPERATOR => 'SIMPLESELECTOR_ATTRIBUTE_OPERATOR',
    self::PARENTHESES_START => 'PARENTHESES_START',
    self::PARENTHESES_END => 'PARENTHESES_END',
    self::COMBINATOR => 'SELECTOR_COMBINATOR',
    self::SEPARATOR => 'SELECTOR_SEPARATOR',
    self::SINGLEQUOTE_STRING_START => 'STRING_SINGLE_QUOTE_START',
    self::SINGLEQUOTE_STRING_END => 'STRING_SINGLE_QUOTE_END',
    self::DOUBLEQUOTE_STRING_START => 'STRING_DOUBLE_QUOTE_START',
    self::DOUBLEQUOTE_STRING_END => 'STRING_DOUBLE_QUOTE_END',
    self::STRING_CHARACTERS => 'STRING_CHARACTERS',
    self::STRING_ESCAPED_CHARACTER => 'STRING_ESCAPED_CHARACTER'
  );

  /**
  * Token type
  * @var integer
  */
  private $_type = NULL;
  /**
  * Token string content
  * @var string
  */
  private $_content = NULL;
  /**
  * Token string content length
  * @var integer
  */
  private $_length = 0;
  /**
  * Byte position the token was found at
  * @var integer
  */
  private $_position = 0;

  /**
  * Construct and initialize token
  *
  * @param integer $type
  * @param string $content
  * @param integer $position
  * @return PhpCssScannerToken
  */
  public function __construct($type = 0, $content = '', $position = -1) {
    $this->_type = $type;
    $this->_content = $content;
    $this->_length = strlen($content);
    $this->_position = $position;
  }

  /**
  * Get token attribute
  *
  * @param string $name
  */
  public function __get($name) {
    switch ($name) {
    case 'type' :
      return $this->_type;
    case 'content' :
      return $this->_content;
    case 'length' :
      return $this->_length;
    case 'position' :
      return $this->_position;
    }
    throw new InvalidArgumentException();
  }

  /**
  * Do not allow to set attributes
  *
  * @param string $name
  * @param mixed $value
  * @return void
  */
  public function __set($name, $value) {
    throw new BadMethodCallException();
  }

  /**
  * Convert token object to string
  * @return string
  */
  public function __toString() {
    return 'TOKEN::'.self::typeToString($this->type).
      ' @'.$this->position.' '.$this->quoteContent($this->content);
  }

  /**
  * Return string representation of token type
  *
  * @param integer $type
  * @return string
  */
  public static function typeToString($type) {
    return self::$_names[$type];
  }

  /**
  * Escape content for double quoted, single line string representation
  *
  * @param string $content
  * @return string
  */
  protected function quoteContent($content) {
    return "'".str_replace(
      array('\\', "\r", "\n", "'"),
      array('\\\\', '\\r', '\\n', "\\'"),
      (string)$content
    )."'";
  }
}