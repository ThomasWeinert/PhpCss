<?php

namespace PhpCss\Scanner {

  use PHPUnit\Framework\TestCase;

  require_once(__DIR__.'/../../bootstrap.php');

  class PatternsTest extends TestCase {

    /**
     * @dataProvider provideValidIdentifiers
     */
    public function testValidIdentifier($identifier): void {
      $this->assertRegExpMatchesString(Patterns::IDENTIFIER, $identifier);
    }

    public static function provideValidIdentifiers(): array {
      return [
        ['div'],
        ['xhtml|div'],
        ['xhtml|*'],
        ['*|*'],
        ['*|div'],
        ['xsl|for-each'],
      ];
    }

    /**
     * @dataProvider provideInvalidIdentifiers
     */
    public function testInvalidIdentifier($identifier): void {
      $this->assertNotRegExpMatchesString(Patterns::IDENTIFIER, $identifier);
    }

    public static function provideInvalidIdentifiers(): array {
      return [
        [' '],
        ['1div'],
        ['div bar'],
        ['**'],
        ['**|bar'],
        ['foo|**'],
        ['e.warning'],
        ['e#myid'],
        [':class'],
        ['attr=value'],
      ];
    }

    private function assertRegExpMatchesString(
      string $pattern, string $string, string $message = ''
    ): void {
      $this->assertTrue(
        preg_match($pattern, $string, $matches) && $matches[0] === $string,
        empty($message) ? 'The pattern did not match the full string.' : $message
      );
    }

    private function assertNotRegExpMatchesString(
      string $pattern, string $string, string $message = ''): void {
      $this->assertFalse(
        preg_match($pattern, $string, $matches) && $matches[0] == $string,
        empty($message) ? 'The pattern did match the full string.' : $message
      );
    }
  }
}
