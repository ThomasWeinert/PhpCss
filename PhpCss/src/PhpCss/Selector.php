<?php

abstract class PhpCssSelector {

  abstract public function visit(PhpCssSelectorVisitor $visitor);

}