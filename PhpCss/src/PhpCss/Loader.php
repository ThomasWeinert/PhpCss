<?php

class PhpCssLoader {
  
  private static $classes = array();
  
  public static function addAutoloadFile($fileName) {
    $classes = include($fileName);
    if (is_array($classes)) {
      self::$classes = array_merge(self::$classes, $classes);
    }
  }
  
  public static function autoload($className) {
    if (isset(self::$classes[$className])) {
      include(self::$classes[$className]);
    }
  }
}