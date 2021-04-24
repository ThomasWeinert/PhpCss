<?php

namespace PhpCss\Parser {

  use PhpCss;
  use PhpCss\Ast;
  use PhpCss\Scanner;
  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../bootstrap.php');

  class AttributeTest extends TestCase {

    /**
     * @covers       \PhpCss\Parser\Attribute
     * @dataProvider provideParseData
     */
    public function testParse($expected, $tokens): void {
      $parser = new Attribute($tokens);
      $this->assertEquals(
        $expected, $parser->parse()
      );
    }

    /**
     * @covers       \PhpCss\Parser\Attribute
     * @dataProvider provideInvalidParseData
     */
    public function testParseExpectingException($tokens): void {
      $parser = new Attribute($tokens);
      $this->expectException(PhpCss\Exception\TokenMismatchException::CLASS);
      $parser->parse();
    }

    public static function provideParseData(): array {
      return [
        'simple identifier' => [
          new Ast\Selector\Simple\Attribute('attr'),
          [
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'attr',
              0
            ),
            new Scanner\Token(
              Scanner\Token::ATTRIBUTE_SELECTOR_END,
              ']',
              4
            ),
          ],
        ],
        'class attribute' => [
          new Ast\Selector\Simple\Attribute(
            'class',
            Ast\Selector\Simple\Attribute::MATCH_INCLUDES,
            'warning'
          ),
          [
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'class',
              0
            ),
            new Scanner\Token(
              Scanner\Token::ATTRIBUTE_OPERATOR,
              '~=',
              5
            ),
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'warning',
              7
            ),
            new Scanner\Token(
              Scanner\Token::ATTRIBUTE_SELECTOR_END,
              ']',
              14
            ),
          ],
        ],
      ];
    }

    public static function provideInvalidParseData(): array {
      return [
        'identifier followed by string start' => [
          [
            new Scanner\Token(
              Scanner\Token::IDENTIFIER,
              'attr',
              0
            ),
            new Scanner\Token(
              Scanner\Token::SINGLEQUOTE_STRING_START,
              "'",
              4
            ),
          ],
        ],
      ];
    }
  }
}
