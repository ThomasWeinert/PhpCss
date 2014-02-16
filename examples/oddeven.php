<?php

$xml = <<<'XML'
<html>
  <body>
    <h1>Title</h1>
    <p>paragraph one</p>
    <p>paragraph two</p>
    <p>paragraph three</p>
    <p>paragraph four</p>
    <p>paragraph five</p>
  </body>
</html>
XML;

$loader = require(__DIR__.'/../vendor/autoload.php');
$loader->add('PhpCss', __DIR__.'/../src');

$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);

foreach ($xpath->evaluate(PhpCss::toXpath('p:nth-of-type(odd)')) as $p) {
  $p->setAttribute('class', 'odd');
}
foreach ($xpath->evaluate(PhpCss::toXpath('p:nth-of-type(even)')) as $p) {
  $p->setAttribute('class', 'even');
}
echo $dom->saveXML();