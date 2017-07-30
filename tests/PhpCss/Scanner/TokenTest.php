<?php
namespace PhpCss\Scanner {

  require_once(__DIR__.'/../../bootstrap.php');

  class TokenTest extends \PHPUnit\Framework\TestCase {

    /**
    * @covers \PhpCss\Scanner\Token::__construct
    * @covers \PhpCss\Scanner\Token::__get
    */
    public function testAttributeTypeRead() {
      $token = new Token(Token::STRING_CHARACTERS, 'hello', 42);
      $this->assertEquals(
        Token::STRING_CHARACTERS,
        $token->type
      );
    }

    /**
    * @covers \PhpCss\Scanner\Token::__construct
    * @covers \PhpCss\Scanner\Token::__get
    */
    public function testAttributeContentRead() {
      $token = new Token(Token::STRING_CHARACTERS, 'hello', 42);
      $this->assertEquals(
        'hello',
        $token->content
      );
    }

    /**
    * @covers \PhpCss\Scanner\Token::__construct
    * @covers \PhpCss\Scanner\Token::__get
    */
    public function testAttributeLengthRead() {
      $token = new Token(Token::STRING_CHARACTERS, 'hello', 42);
      $this->assertEquals(
        5,
        $token->length
      );
    }


    /**
    * @covers \PhpCss\Scanner\Token::__construct
    * @covers \PhpCss\Scanner\Token::__get
    */
    public function testAttributePositionRead() {
      $token = new Token(Token::STRING_CHARACTERS, 'hello', 42);
      $this->assertEquals(
        42,
        $token->position
      );
    }

    /**
    * @covers \PhpCss\Scanner\Token::__construct
    * @covers \PhpCss\Scanner\Token::__get
    */
    public function testAttributeInvalidReadExpectingException() {
      $token = new Token(Token::STRING_CHARACTERS, 'hello', 42);
      $this->expectException(\InvalidArgumentException::CLASS);
      /** @noinspection PhpUndefinedFieldInspection */
      $token->invalidAttribute;
    }

    /**
    * @covers \PhpCss\Scanner\Token::__set
    */
    public function testAttributeWriteExpectingException() {
      $token = new Token(Token::STRING_CHARACTERS, 'hello', 42);
      $this->expectException(\BadMethodCallException::CLASS);
      /** @noinspection PhpUndefinedFieldInspection */
      $token->anyAttribute = 'fail';
    }

    /**
    * @covers \PhpCss\Scanner\Token::__toString
    * @covers \PhpCss\Scanner\Token::quoteContent
    */
    public function testToString() {
      $token = new Token(Token::STRING_CHARACTERS, 'hello', 42);
      $this->assertEquals(
        "TOKEN::STRING_CHARACTERS @42 'hello'",
        (string)$token
      );
    }

    /**
    * @covers \PhpCss\Scanner\Token::typeToString
    */
    public function testTypeToString() {
      $this->assertEquals(
        'STRING_CHARACTERS',
        Token::typeToString(Token::STRING_CHARACTERS)
      );
    }
  }
}
