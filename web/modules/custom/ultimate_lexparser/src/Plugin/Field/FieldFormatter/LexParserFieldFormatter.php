<?php

namespace Drupal\ultimate_lexparser\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ultimate_lexparser\ParserService;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\field\Entity\FieldConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Logger\LoggerChannelFactory;

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
class LexParserFieldFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The parser service.
   *
   * @var \Drupal\ultimate_lexparser\ParserService
   */
  public $parserService;

  /**
   * The logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $logger;

  /**
   * Constructs a FormatterBase object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\field\Entity\FieldConfig $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\ultimate_lexparser\ParserService $parser_service
   *   The ultimate LexParser parser service.
   * @param \Drupal\Core\Logger\LoggerChannelFactory $logger
   *   The Drupal core logger service.
   */
  public function __construct($plugin_id, $plugin_definition, FieldConfig $field_definition, array $settings, $label, $view_mode, array $third_party_settings, ParserService $parser_service, LoggerChannelFactory $logger) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->parserService = $parser_service;
    $this->logger = $logger->get('ultimate_lexparser');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('ultimate_lexparser.parser'),
      $container->get('logger.factory')
    );
  }

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
        '#default_value' => $this->getSetting('precision'),
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
    $formula = $item->value;
    $precision = $this->getSetting('precision');
    try {
      $result = $this->parserService->calculate($formula, $precision);
    }
    catch (\Exception $e) {
      // Don't expose error to front end, log it and display generic error.
      $result = "Error in parsing field. Please see error logs.";
      $this->logger->warning($e->getMessage());
    }
    return nl2br(Html::escape($result));
  }

}
