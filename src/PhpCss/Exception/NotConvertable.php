<?php
/**
 * Exception thrown if a visitor finds something that it can not use.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright 2010-2014 PhpCss Team
 */

namespace PhpCss\Exception {

  use PhpCss;

  /**
   * Exception thrown if a visitor finds something that it can not use.
   */
  class NotConvertable
    extends \Exception
    implements PhpCss\Exception {

    public function __construct($token, $target) {
      parent::__construct(
        sprintf(
          'Can not convert %s to %s.', (string)$token, (string)$target
        )
      );
    }
  }
}
