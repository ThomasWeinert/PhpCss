<?php

class PhpCssAstSelectorSimplePseudoClass extends PhpCssAstSelectorSimplePseudo {

  public $name = '';
  public $parameter = NULL;

  public function __construct($name, $parameter = NULL) {
    $this->name = $name;
    $this->parameter = $parameter;
  }

  public function accept(PhpCssAstVisitor $visitor) {
    if ($this->parameter instanceOf PhpCssAst) {
      $visitor->visitEnter($this);
      $this->parameter->accept($visitor);
      $visitor->visitLeave($this);
    } else {
      $visitor->visit($this);
    }
  }
}