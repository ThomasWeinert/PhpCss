<?php

/**
 * Exception thrown if a scanner status finds does not find a
 * valid character.
 *
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright 2010-2014 PhpCss Team
 */

namespace PhpCss\Exception {

  use PhpCss;
  use PhpCss\Scanner\Status;
  use UnexpectedValueException;

  /**
   * Exception thrown if a scanner status finds does not
   * find a valid character.
   */
  class InvalidCharacterException
    extends UnexpectedValueException
    implements PhpCssException {

    /** @var int $offset byte offset */
    private $_offset;
    /** @var string $buffer string buffer */
    private $_buffer;
    /** @var Status $status scanner status */
    private $_status;

    /**
     * @param string $buffer
     * @param int $offset
     * @param Status $status
     */
    public function __construct(string $buffer, int $offset, Status $status) {
      $this->_buffer = $buffer;
      $this->_offset = $offset;
      $this->_status = $status;
      parent::__construct(
        sprintf(
          'Invalid char "%s" for status "%s" at offset #%d in "%s"',
          $this->getChar(),
          get_class($this->_status),
          $this->_offset,
          $this->_buffer
        )
      );
    }

    /**
     * Match the utf-8 character at the byte offset position.
     *
     * @return string
     */
    public function getChar(): string {
      if (preg_match('(.)suS', $this->_buffer, $match, 0, $this->_offset)) {
        return $match[0];
      }
      return '';
    }

    public function getOffset(): int {
      return $this->_offset;
    }

    public function getBuffer(): string {
      return $this->_buffer;
    }

    public function getStatus(): Status {
      return $this->_status;
    }
  }
}
