<?php

namespace Drupal\ultimate_lexparser;

use Exception;
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
    $resultData = $parser->getResult();
    // Result state can indicate error.
    $resultState = $resultData[0];
    // The computed result, or an error message.
    $resultComputed = $resultData[1];
    if ($resultState === 'error') {
      throw new Exception("Error in parse: " . $resultComputed);
    }
    return $resultComputed;
  }

}
