<?php
namespace PhpCss\Parser {

  use PhpCss;

  class Mock extends PhpCss\Parser {

    public $_tokens;

    public function parse(): PhpCss\Ast\Node {
      // Nothing to do here
    }

    /**
     * This function can be made public for testing because it is supposed to
     * be used by every subparser and we just expose it to be able to call it
     * during test without different mocks.
     *
     * @throws PhpCss\Exception\ParserException
     */
    public function read($expectedTokens): PhpCss\Scanner\Token {
      return parent::read($expectedTokens);
    }

    /**
     * This function can be made public for testing because it is supposed to
     * be used by every subparser and we just expose it to be able to call it
     * during test without different mocks.
     *
     * @throws PhpCss\Exception\ParserException
     */
    public function lookahead(
      $expectedTokens, int $position = 0, bool $allowEndOfTokens = false
    ): ?PhpCss\Scanner\Token {
      return parent::lookahead($expectedTokens, $position, $allowEndOfTokens);
    }

    /**
    * This function can be made public for testing because it is supposed to
    * be used by every subparser and we just expose it to be able to call it
    * during test without different mocks.
    */
    public function endOfTokens(int $position = 0): bool {
      return parent::endOfTokens($position);
    }

    /**
    * This function can be made public for testing because it is supposed to
    * be used by every subparser and we just expose it to be able to call it
    * during test without different mocks.
    */
    public function ignore($expectedTokens): bool {
      return parent::ignore($expectedTokens);
    }

    /**
     * This function can be made public for testing because it is supposed to
     * be used by every subparser and we just expose it to be able to call it
     * during test without different mocks.
     */
    public function delegate($parserClass): PhpCss\Ast\Node {
      return parent::delegate($parserClass);
    }
  }

  class MockDelegate extends PhpCss\Parser {

    public function parse(): PhpCss\Ast\Node {
      return new PhpCss\Ast\Value\Literal('Delegated!');
    }
  }
}
