<?php

namespace Drupal\Tests\collapsiblock\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Provides a base class for Collapsiblock kernel tests.
 */
abstract class CollapsiblockKernelTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'block_test',
    'collapsiblock',
    'system',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp():void {
    parent::setUp();

    $this->installConfig(['collapsiblock']);
  }

}
