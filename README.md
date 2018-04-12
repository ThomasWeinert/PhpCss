# PhpCss - PHP CSS Parser Library

[![Build Status](https://travis-ci.org/ThomasWeinert/PhpCss.svg?branch=master)](https://travis-ci.org/ThomasWeinert/PhpCss)
[![License](https://poser.pugx.org/carica/phpcss/license.svg)](https://packagist.org/packages/carica/phpcss)
[![Total Downloads](https://poser.pugx.org/carica/phpcss/downloads.svg)](https://packagist.org/packages/carica/phpcss)
[![Latest Stable Version](https://poser.pugx.org/carica/phpcss/v/stable.svg)](https://packagist.org/packages/carica/phpcss)
[![Latest Unstable Version](https://poser.pugx.org/carica/phpcss/v/unstable.svg)](https://packagist.org/packages/carica/phpcss)

* License: The MIT License
* Copyright: 2010-2018 PhpCss Team
* Author: [Thomas Weinert](http://thomas.weinert.info) <thomas@weinert.info>

Thanks to Benjamin Eberlei, Bastian Feder and Jakob Westhoff for ideas and concepts.

PhpCSS is a parser for CSS 3 selectors. It parses them into an AST and allows them to compile the AST to CSS selectors or Xpath expressions.

The main target of this project is the possibilty to convert CSS selectors into Xpath expressions.

## Demo

A small demo application can be found at: http://xpath.thomas.weinert.info/

## Installation

PhpCss is available on Packagist: [Carica/PhpCss](https://packagist.org/packages/carica/phpcss).
Add it to you composer.json and update.

## Basic Usage

Get CSS selector as Xpath expression

    $expression = PhpCss::toXpath($selector);

Reformat/Validate CSS Selector

    $selector = PhpCss::reformat($selector);

Get the AST

    $ast = PhpCss::getAst($selector);


## FluentDOM

[FluentDOM 5](https://github.com/FluentDOM/FluentDOM) allows to inject a callback to convert selectors. If you have FluentDOM and PhpCss installed in your project, you can use CSS selectors in FluentDOM:

    $fd = FluentDOM::QueryCss($xml);
    $fd
      ->find('td:nth-of-type(even)')
      ->addClass('even');

## Supported

<table width="100%">
  <thead>
    <tr>
      <th>Selector</th><th>to CSS</th><th>to Xpath</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>*</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>ns|*</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>ns|E</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <th colspan="3">Attributes</th>
    </tr>
    <tr>
      <td>E[foo]</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E[foo="bar"]</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E[foo~="bar"]</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E[foo^="bar"]</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E[foo$="bar"]</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E[foo*="bar"]</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E[foo|="bar"]</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <th colspan="3">Structural Pseudo Classes</th>
    </tr>
    <tr>
      <td>E:root</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:nth-child(42)</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:nth-last-child(42)</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:nth-of-type(42)</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:nth-last-of-type(42)</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:first-child</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:last-child</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:first-of-type</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:last-of-type</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:only-child</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:only-of-type</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E:empty</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <th colspan="3">Link Pseudo Classes</th>
    </tr>
    <tr>
      <td>E:link</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <td>E:visited</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <th colspan="3">User Action Pseudo Classes</th>
    </tr>
    <tr>
      <td>E:active</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <td>E:hover</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <td>E:focus</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <th colspan="3">Target Pseudo Class</th>
    </tr>
    <tr>
      <td>E:target</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <th colspan="3">Language Pseudo Class</th>
    </tr>
    <tr>
      <td>E:lang(fr)</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <th colspan="3">UI Element states Pseudo Class</th>
    </tr>
    <tr>
      <td>E:enabled</td><td>✓</td><td>✓ (not disabled)</td>
    </tr>
    <tr>
      <td>E:disabled</td><td>✓</td><td>✓ (attribute)</td>
    </tr>
    <tr>
      <td>E:checked</td><td>✓</td><td>✓ (attribute)</td>
    </tr>
    <tr>
      <th colspan="3">Pseudo Elements</th>
    </tr>
    <tr>
      <td>E:first-line</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <td>E:first-letter</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <td>E:before</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <td>E:after</td><td>✓</td><td>✗</td>
    </tr>
    <tr>
      <th colspan="3">Class Selector</th>
    </tr>
    <tr>
      <td>E.warning</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <th colspan="3">Id Selector</th>
    </tr>
    <tr>
      <td>E#myid</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <th colspan="3">Negation Pseudo Class</th>
    </tr>
    <tr>
      <td>E:not(s)</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <th colspan="3">Combinators</th>
    </tr>
    <tr>
      <td>E F</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E > F</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E + F</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>E ~ F</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <th colspan="3">jQuery</th>
    </tr>
    <tr>
      <td>:contains("text")</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>:has(s)</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>:gt(42)</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>:lt(42)</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>:odd</td><td>✓</td><td>✓</td>
    </tr>
    <tr>
      <td>:even</td><td>✓</td><td>✓</td>
    </tr>
  </tbody>
</table>
