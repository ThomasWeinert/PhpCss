<?php

abstract class PhpCssAst {
  
  abstract public function visit(PhpCssAstVisitor $visitor);
  
}