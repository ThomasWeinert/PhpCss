<?php
/**
 * Exception thrown if a visitor finds something that it can not use.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright 2010-2014 PhpCss Team
 */

namespace PhpCss\Exception {

  use Exception;
  use PhpCss;

  /**
   * Exception thrown if a visitor finds something that it can not use.
   */
  class NotConvertibleException
    extends Exception
    implements PhpCssException {

    public function __construct(string $source, string $target) {
      parent::__construct(
        sprintf(
          'Can not convert %s to %s.', $source, $target
        )
      );
    }
  }
}
