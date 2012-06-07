<?php
/**
* Test class for PhpCss Auto Loader.
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright (c) 2010 PhpCss Team
*
* @package PhpCss
* @subpackage Tests
*/

/**
* Load necessary files
*/
require_once('PHPUnit/Autoload.php');
require_once(dirname(dirname(dirname(__FILE__))).'/src/PhpCss/Loader.php');

/**
* Test class for PhpCss Auto Loader.
*
* @package PhpCss
* @subpackage Tests
*/
class PhpCssLoaderTest extends PHPUnit_Framework_TestCase {

  /**
  * @covers PhpCssLoader::addAutoloadFile
  */
  public function testAddAutoloadFile() {
    PhpCssLoader::addAutoloadFile(dirname(__FILE__).'/TestData/LoaderData.php');
    $classes = $this->readAttribute('PhpCssLoader', 'classes');
    $this->assertContains(
      'PhpCssLoaderTestClass', array_keys($classes)
    );
  }

  public function testAutoloadExpectingTrue() {
    PhpCssLoader::addAutoloadFile(dirname(__FILE__).'/TestData/LoaderData.php');
    PhpCssLoader::autoload('PhpCssLoaderTestClass');
    $this->assertTrue(class_exists('PhpCssLoaderTestClass', FALSE));
  }
}