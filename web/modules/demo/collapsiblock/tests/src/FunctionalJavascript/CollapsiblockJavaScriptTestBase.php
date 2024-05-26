<?php

namespace Drupal\Tests\collapsiblock\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\collapsiblock\Traits\GlobalSettingsTrait;
use Drupal\Tests\collapsiblock\Traits\InstanceSettingsTrait;

/**
 * Useful shortcuts for running Collapsiblock functional tests.
 */
abstract class CollapsiblockJavaScriptTestBase extends WebDriverTestBase {
  use GlobalSettingsTrait;
  use InstanceSettingsTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['block', 'collapsiblock'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user with minimal permissions to test with.
   *
   * @var \Drupal\user\UserInterface
   */
  private $collapsiblockUnprivilegedUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();

    $this->collapsiblockUnprivilegedUser = $this->createUser(['access content']);
  }

  /**
   * Get the user with minimal permissions to test with.
   *
   * @return \Drupal\user\UserInterface
   *   A user with minimal permissions to test with.
   */
  protected function getCollapsiblockUnprivilegedUser() {
    return $this->collapsiblockUnprivilegedUser;
  }

}
