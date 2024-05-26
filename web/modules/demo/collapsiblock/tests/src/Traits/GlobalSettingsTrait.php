<?php

namespace Drupal\Tests\collapsiblock\Traits;

/**
 * Simplify working with global Collapsiblock settings.
 */
trait GlobalSettingsTrait {

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
   * Get the global Collapsiblock configuration.
   *
   * @return \Drupal\Core\Config\Config
   *   The global Collapsiblock configuration object.
   */
  protected function getCollapsiblockGlobalConfig() {
    return $this->config('collapsiblock.settings');
  }

  /**
   * Get a specific Collapsiblock global setting.
   *
   * @param string $key
   *   The setting to get the value for.
   *
   * @return mixed
   *   The given setting's current configuration.
   */
  protected function getCollapsiblockGlobalSetting($key = '') {
    return $this->getCollapsiblockGlobalConfig()->get($key);
  }

  /**
   * Set a specific Collapsiblock global setting.
   *
   * @param mixed $newValue
   *   The configuration to set.
   * @param string $key
   *   Which setting to set.
   */
  protected function setCollapsiblockGlobalSetting($newValue, $key = '') {
    $this->getCollapsiblockGlobalConfig()
      ->set($key, $newValue)
      ->save();
  }

}
