<?php

namespace Drupal\Tests\Core\Menu;

use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use Drupal\Tests\UnitTestCase;
use Drupal\ultimate_lexparser\ParserService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Defines a base unit test for testing existence of local tasks.
 *
 * @todo Add tests for access checking and url building,
 *   https://www.drupal.org/node/2112245.
 */
class ParseTest extends UnitTestCase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['ultimate_lexparser'];

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * The container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerBuilder
   */
  protected $container;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $container = new ContainerBuilder();
    $config_factory = $this->getConfigFactoryStub([]);
    $container->set('config.factory', $config_factory);
    $container->set('app.root', $this->root);
    $parser = new ParserService();
    $container->set('ultimate_lexparser.parser', $parser);
    \Drupal::setContainer($container);
    $this->container = $container;
  }

  /**
   * Tests local task existence.
   *
   * @dataProvider getFormulasToTest
   */
  public function testFormulas($formula, $precision, $expected) {
    $parser = new ParserService();
    $result = $parser->calculate($formula, $precision);
    $this->assertEquals($expected, $result);
  }

  /**
   * Provides a list of routes to test.
   */
  public function getFormulasToTest() {
    return [
      ["1+4", "2", "5"],
      ["3+3*9", "2", "30"],
      ["6-1+4*25/4", "2", "30"],
    ];
  }

}
