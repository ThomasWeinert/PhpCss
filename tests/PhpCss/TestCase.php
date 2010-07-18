<?php

/**
* Load necessary files
*/
require_once('PHPUnit/Framework.php');
require_once(dirname(__FILE__).'/../../src/PhpCss/Loader.php');
PhpCssLoader::addAutoloadFile(dirname(__FILE__).'/../../src/PhpCss/Loader/All.php');
spl_autoload_register('PhpCssLoader::autoload');

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

abstract class PhpCssTestCase extends PHPUnit_Framework_TestCase {

  public static function includePhpCssFile($file) {
    include_once(dirname(__FILE__).'/../../src/PhpCss'.$file);
  }
}