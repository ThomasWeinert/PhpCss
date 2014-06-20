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

  /**
   * Exception thrown if a scanner status finds does not
   * find a valid character.
   */
  class InvalidCharacter
    extends \UnexpectedValueException
    implements PhpCss\Exception {

    /** @var int $offset byte offset */
    private $_offset = 0;
    /** @var string $buffer string buffer */
    private $_buffer = '';
    /** @var PhpCss\Scanner\Status $status scanner status */
    private $_status = '';

    /**
     * @param string $buffer
     * @param int $offset
     * @param \PhpCss\Scanner\Status $status
     */
    public function __construct($buffer, $offset, $status) {
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
    public function getChar() {
      if (preg_match('(.)suS', $this->_buffer, $match, 0, $this->_offset)) {
        return $match[0];
      }
      return '';
    }

    public function getOffset() {
      return $this->_offset;
    }

    public function getBuffer() {
      return $this->_buffer;
    }

    public function getStatus() {
      return $this->_status;
    }
  }
}
