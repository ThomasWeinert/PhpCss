<?php
/**
* Autoloader array definition for the PhpCss files
*
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @copyright Copyright 2010-2012 PhpCss Team
*
* @package PhpCss
* @subpackage Loader
*/

$path = dirname(dirname(__FILE__));

return array(
  'PhpCssAst' => $path.'/Ast.php',
  'PhpCssAstSelector' => $path.'/Ast/Selector.php',
  'PhpCssAstSelectorCombinator' => $path.'/Ast/Selector/Combinator.php',
  'PhpCssAstSelectorCombinatorChild' => $path.'/Ast/Selector/Combinator/Child.php',
  'PhpCssAstSelectorCombinatorDescendant' => $path.'/Ast/Selector/Combinator/Descendant.php',
  'PhpCssAstSelectorCombinatorFollower' => $path.'/Ast/Selector/Combinator/Follower.php',
  'PhpCssAstSelectorCombinatorNext' => $path.'/Ast/Selector/Combinator/Next.php',
  'PhpCssAstSelectorGroup' => $path.'/Ast/Selector/Group.php',
  'PhpCssAstSelectorSequence' => $path.'/Ast/Selector/Sequence.php',
  'PhpCssAstSelectorSimple' => $path.'/Ast/Selector/Simple.php',
  'PhpCssAstSelectorSimpleAttribute' => $path.'/Ast/Selector/Simple/Attribute.php',
  'PhpCssAstSelectorSimpleClass' => $path.'/Ast/Selector/Simple/Class.php',
  'PhpCssAstSelectorSimpleId' => $path.'/Ast/Selector/Simple/Id.php',
  'PhpCssAstSelectorSimplePseudo' => $path.'/Ast/Selector/Simple/Pseudo.php',
  'PhpCssAstSelectorSimplePseudoClass' => $path.'/Ast/Selector/Simple/Pseudo/Class.php',
  'PhpCssAstSelectorSimplePseudoClassLanguage' =>
    $path.'/Ast/Selector/Simple/Pseudo/Class/Language.php',
  'PhpCssAstSelectorSimplePseudoClassPosition' =>
    $path.'/Ast/Selector/Simple/Pseudo/Class/Position.php',
  'PhpCssAstSelectorSimplePseudoElement' => $path.'/Ast/Selector/Simple/Pseudo/Element.php',
  'PhpCssAstSelectorSimpleType' => $path.'/Ast/Selector/Simple/Type.php',
  'PhpCssAstSelectorSimpleUniversal' => $path.'/Ast/Selector/Simple/Universal.php',
  'PhpCssAstVisitor' => $path.'/Ast/Visitor.php',
  'PhpCssAstVisitorCss' => $path.'/Ast/Visitor/Css.php',
  'PhpCssAstVisitorOverload' => $path.'/Ast/Visitor/Overload.php',
  'PhpCssAstVisitorXpath' => $path.'/Ast/Visitor/Xpath.php',
  'PhpCssException' => $path.'/Exception.php',
  'PhpCssExceptionParser' => $path.'/Exception/Parser.php',
  'PhpCssExceptionTokenMismatch' => $path.'/Exception/TokenMismatch.php',
  'PhpCssExceptionUnexpectedEndOfFile' => $path.'/Exception/UnexpectedEndOfFile.php',
  'PhpCssExceptionUnknownPseudoClass' => $path.'/Exception/UnknownPseudoClass.php',
  'PhpCssExceptionUnknownPseudoElement' => $path.'/Exception/UnknownPseudoElement.php',
  'PhpCssParser' => $path.'/Parser.php',
  'PhpCssParserAttribute' => $path.'/Parser/Attribute.php',
  'PhpCssParserDefault' => $path.'/Parser/Default.php',
  'PhpCssParserPseudoClass' => $path.'/Parser/Pseudo/Class.php',
  'PhpCssParserSequence' => $path.'/Parser/Sequence.php',
  'PhpCssParserString' => $path.'/Parser/String.php',
  'PhpCssScanner' => $path.'/Scanner.php',
  'PhpCssScannerStatus' => $path.'/Scanner/Status.php',
  'PhpCssScannerStatusSelector' => $path.'/Scanner/Status/Selector.php',
  'PhpCssScannerStatusSelectorAttribute' => $path.'/Scanner/Status/Selector/Attribute.php',
  'PhpCssScannerStatusStringDouble' => $path.'/Scanner/Status/String/Double.php',
  'PhpCssScannerStatusStringSingle' => $path.'/Scanner/Status/String/Single.php',
  'PhpCssScannerPatterns' => $path.'/Scanner/Patterns.php',
  'PhpCssScannerToken' => $path.'/Scanner/Token.php',
);