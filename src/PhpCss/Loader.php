<?php
/**
* Autoloader for the PhpCss files
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Loader
*/

/**
* Abstract class implementing functionallity to ease parsing in extending
* subparsers.
*
* @package PhpCss
* @subpackage Loader
*/
class PhpCssLoader {

  private static $classes = array();

  /**
  * Include the given file into fetch a returned array
  * and add all entries to the static alss file mapping
  *
  * @param string $fileName
  */
  public static function addAutoloadFile($fileName) {
    $classes = include($fileName);
    if (is_array($classes)) {
      self::$classes = array_merge(self::$classes, $classes);
    }
  }

  /**
  * Autoloader function should be registered using spl_autoload_register
  *
  * @param string $className
  */
  public static function autoload($className) {
    if (isset(self::$classes[$className])) {
      include(self::$classes[$className]);
    }
  }
}