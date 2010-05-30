<?php
/**
* Collection of tests for the Parser class
*
* @version $Id$
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 Bastian Feder, Thomas Weinert
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once(dirname(__FILE__).'/TestCase.php');
require_once(dirname(__FILE__).'/Parser/Mock.php');
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
* Test class for PhpCssParser.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssParserTest extends PhpCssTestCase {
  
  /**
  * @covers PhpCssParser::read
  * @dataProvider provideDirectMatchingTokens
  */
  public function testReadMatch($expectedResult, $tokens, $allowedTokens) {
    $originalTokens = $tokens;
    array_shift($originalTokens);

    $parser = $this->getParserFixture($tokens);

    $result = $parser->read($allowedTokens);

    $this->assertSame($tokens[0], $result);
    $this->assertEquals($expectedResult, $result->type);
    $this->assertEquals($parser->_tokens, $originalTokens);
  }
    
  /*****************************
  * Fixtures
  *****************************/
  
  public function getParserFixture(array $tokens = array()) {
    return new PhpCssParserMock($tokens);
  }
  
  public function getParserFixtureWithReference(array &$tokens) {
    return new PhpCssParserMock($tokens);
  }
    
  /*****************************
  * Data Provider
  *****************************/
  
  public static function provideDirectMatchingTokens() {
    return array( 
      array( 
        PhpCssScannerToken::TYPE_SELECTOR, // expected token type
        array(new PhpCssScannerToken(PhpCssScannerToken::TYPE_SELECTOR, 'foo', 0)), // token list
        array(PhpCssScannerToken::TYPE_SELECTOR), // allowed token types
      )
    );
  }
}