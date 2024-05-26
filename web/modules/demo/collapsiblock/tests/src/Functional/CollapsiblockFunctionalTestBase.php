<?php

namespace Drupal\Tests\collapsiblock\Functional;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\collapsiblock\Traits\GlobalSettingsTrait;
use Drupal\Tests\collapsiblock\Traits\InstanceSettingsTrait;

/**
 * Useful shortcuts for running Collapsiblock functional tests.
 */
abstract class CollapsiblockFunctionalTestBase extends WebDriverTestBase {
  use GlobalSettingsTrait;
  use InstanceSettingsTrait;

  /**
   * An unprivileged user to check collapsiblock output.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $collapsiblockUnprivilegedUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['block', 'collapsiblock'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();

    $this->collapsiblockUnprivilegedUser = $this->drupalCreateUser(['access content']);
  }

  /**
   * Get an unprivileged user to check collapsiblock output.
   *
   * @return \Drupal\user\UserInterface
   *   An unprivileged user to check collapsiblock output.
   */
  protected function getCollapsiblockUnprivilegedUser() {
    return $this->collapsiblockUnprivilegedUser;
  }

}
