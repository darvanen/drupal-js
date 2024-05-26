<?php

namespace Drupal\Tests\collapsiblock\Traits;

use Drupal\block\BlockInterface;

/**
 * Simplify working with Collapsiblock settings attached to a specific block.
 */
trait InstanceSettingsTrait {

  /**
   * Configuration accessor for tests. Returns non-overridden configuration.
   *
   * @param string $name
   *   Configuration name.
   *
   * @return \Drupal\Core\Config\Config
   *   The configuration object with original configuration data.
   */
  abstract protected function config($name);

  /**
   * Get a specific Collapsiblock instance setting.
   *
   * @param \Drupal\block\BlockInterface $block
   *   The block instance to get the setting from.
   * @param string $key
   *   The key of the setting to get. Will be automatically prefixed with
   *   '"third_party.collapsiblock.'.
   *
   * @return mixed
   *   The value of the given Collapsiblock block instance setting.
   */
  protected function getCollapsiblockBlockInstanceSetting(BlockInterface $block, $key = '') {
    return $this->config($block->getConfigDependencyName())
      ->get("third_party_settings.collapsiblock.$key");
  }

  /**
   * Set a specific Collapsiblock instance setting.
   *
   * @param \Drupal\block\BlockInterface $block
   *   The block instance to change the setting in.
   * @param mixed $newValue
   *   The new value for the setting.
   * @param string $key
   *   The key of the setting to change. Will be automatically prefixed with
   *   '"third_party.collapsiblock.'.
   */
  protected function setCollapsiblockBlockInstanceSetting(BlockInterface $block, $newValue, $key = '') {
    $this->config($block->getConfigDependencyName())
      ->set("third_party_settings.collapsiblock.$key", $newValue)
      ->save();
  }

}
