<?php

namespace Drupal\ultimate_lexparser;

use FormulaParser\FormulaParser;

/**
 * Class ParserService.
 */
class ParserService implements ParserServiceInterface {

  /**
   * The formula parser.
   */
  public $parser;

  /**
   * Constructs a new ParserService object.
   */
  public function __construct() {
    
  }

  /**
   * Performs a calculation from a string.
   *
   * @param string $formula
   *   The formula to parse.
   * @param string $precision
   *   Number of digits after the decimal point.
   *
   * @return string
   *   The result from the equation.
   */
  public function calculate($formula, $precision) {
    $parser = new FormulaParser($formula, $precision);
    $parser->setVariables(['x' => -4, 'y' => 8]);
    $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]
    return $result[1];
  }

}
