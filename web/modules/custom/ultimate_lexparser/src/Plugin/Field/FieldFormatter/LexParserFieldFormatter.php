<?php

namespace Drupal\ultimate_lexparser\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use FormulaParser\FormulaParser;

/**
 * Plugin implementation of the 'lex_parser_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "lex_parser_field_formatter",
 *   label = @Translation("Lex parser field formatter"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class LexParserFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'precision' => '2',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      'precision' => [
        '#type' => 'textfield',
        '#title' => 'Precision',
        '#description' => 'Provide the level of precision to be calculated.',
      ],
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#markup' => $this->viewValue($item)];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    $formula = $item->value;
    $precision = 2; // Number of digits after the decimal point
    // TODO: Wire up precision to formatter settings.
    try {
      $parser = new FormulaParser($formula, $precision);
      $parser->setVariables(['x' => -4, 'y' => 8]);
      $result = $parser->getResult(); // [0 => 'done', 1 => 16.38]
    }
    catch (\Exception $e) {
      $result = $e->getMessage();
    }
    return nl2br(Html::escape($result[1]));
  }

}
