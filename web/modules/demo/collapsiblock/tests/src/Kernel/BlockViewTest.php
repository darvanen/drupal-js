<?php

namespace Drupal\Tests\collapsiblock\Kernel;

use Drupal\block\Entity\Block;

/**
 * Tests viewing blocks with Collapsiblock enabled.
 *
 * @group collapsiblock
 */
class BlockViewTest extends CollapsiblockKernelTestBase {

  /**
   * The block storage.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $blockStorage;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->blockStorage = $this->container
      ->get('entity_type.manager')
      ->getStorage('block');

    $this->renderer = $this->container->get('renderer');
  }

  /**
   * Tests viewing a block.
   */
  public function testViewBlock() {
    $entity = $this->blockStorage->create([
      'id' => 'test_block1',
      'theme' => 'stark',
      'plugin' => 'test_html',
    ]);
    $entity->save();

    // Test the rendering of a block.
    $entity = Block::load('test_block1');
    $builder = \Drupal::entityTypeManager()->getViewBuilder('block');
    $build = $builder->view($entity, 'block');
    $expected = [];
    $expected[] = '<div id="block-test-block1">';
    $expected[] = '  ';
    $expected[] = '    ';
    $expected[] = '      ';
    $expected[] = '  </div>';
    $expected[] = '';
    $expected_output = implode("\n", $expected);
    $this->assertEquals($expected_output, (string) $this->renderer->renderRoot($build));
  }

  /**
   * Tests viewing a block with an unexpected value for '#block'.
   */
  public function testViewBlockWithUnexpectedBlockValue() {
    // Create a block plugin instance.
    $block_instance = $this->container->get('plugin.manager.block')->createInstance('test_html', []);

    // Create a render array representing a block, but put an unexpected value
    // in '#block'.
    $base_id = $block_instance->getBaseId();
    $build = [
      '#theme' => 'block',
      '#attributes' => [],
      '#configuration' => $block_instance->getConfiguration(),
      '#plugin_id' => $block_instance->getPluginId(),
      '#base_plugin_id' => $base_id,
      '#derivative_plugin_id' => $block_instance->getDerivativeId(),
      '#id' => $block_instance->getMachineNameSuggestion(),
      // Put an unexpected value in '#block'.
      '#block' => 'foo',
      'content' => $block_instance->build(),
    ];

    // Invoke the hook_block_view_alter() hook.
    $this->container->get('module_handler')->alter([
      'block_view',
      "block_view_{$base_id}",
    ], $build, $block_instance);

    $expected = [];
    $expected[] = '<div id="block-testhtmlblock">';
    $expected[] = '  ';
    $expected[] = '    ';
    $expected[] = '      ';
    $expected[] = '  </div>';
    $expected[] = '';
    $expected_output = implode("\n", $expected);
    $this->assertEquals($expected_output, (string) $this->renderer->renderRoot($build));
  }

}
