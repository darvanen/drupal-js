<?php

namespace Drupal\Tests\collapsiblock\Functional;

/**
 * Test the Collapsiblock global configuration form.
 *
 * @group collapsiblock
 */
class GlobalConfigurationTest extends CollapsiblockFunctionalTestBase {

  /**
   * A user with permission to administer global collapsiblock settings.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $collapsiblockGlobalAdminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();

    $this->collapsiblockGlobalAdminUser = $this->drupalCreateUser([
      'administer site configuration',
      'access administration pages',
    ]);
  }

  /**
   * Test the global config form exists and functions correctly.
   */
  public function testGlobalConfigForm() {
    $this->drupalLogin($this->collapsiblockGlobalAdminUser);
    $this->drupalGet('admin/config/user-interface/collapsiblock');

    // Test that the form controls are present, have the expected options, and
    // are set to the default configuration values.
    $this->assertSession()->checkboxChecked('edit-default-action-1');
    $this->assertSession()->checkboxNotChecked('edit-default-action-2');
    $this->assertSession()->checkboxNotChecked('edit-default-action-3');
    $this->assertSession()->checkboxNotChecked('edit-default-action-4');
    $this->assertSession()->checkboxNotChecked('edit-default-action-5');

    $this->assertSession()->checkboxNotChecked('active_pages');

    $this->assertSession()->selectExists('slide_speed');
    $this->assertSession()->optionExists('slide_speed', 50);
    $this->assertSession()->optionExists('slide_speed', 100);
    $this->assertSession()->optionExists('slide_speed', 200)
      ->hasAttribute('selected');
    $this->assertSession()->optionExists('slide_speed', 300);
    $this->assertSession()->optionExists('slide_speed', 400);
    $this->assertSession()->optionExists('slide_speed', 500);
    $this->assertSession()->optionExists('slide_speed', 700);
    $this->assertSession()->optionExists('slide_speed', 1000);
    $this->assertSession()->optionExists('slide_speed', 1300);

    $this->assertSession()->fieldExists('cookie_lifetime');

    // Submit the form with new values.
    $configFormValues = [];
    $configFormValues['default_action'] = '2';
    $configFormValues['active_pages'] = 1;
    $configFormValues['slide_speed'] = 500;
    $configFormValues['cookie_lifetime'] = '1';
    $this->submitForm($configFormValues, 'Save configuration');

    // Test that the form controls now show the updated configuration.
    $this->assertSession()->checkboxNotChecked('edit-default-action-1');
    $this->assertSession()->checkboxChecked('edit-default-action-2');
    $this->assertSession()->checkboxNotChecked('edit-default-action-3');
    $this->assertSession()->checkboxNotChecked('edit-default-action-4');
    $this->assertSession()->checkboxNotChecked('edit-default-action-5');
    $this->assertSession()->checkboxChecked('active_pages');
    $this->assertSession()->optionExists('slide_speed', 500)
      ->hasAttribute('selected');
    $this->assertSession()->fieldValueEquals('cookie_lifetime', 1);
  }

  /**
   * Test global configuration is output to pages.
   *
   * Note this is a slow test because we have to repeatedly flush caches after
   * changing each global configuration setting in order for it to be output.
   */
  public function testGlobalConfigOutput() {
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());

    // Test that the default configuration values are output when the site has
    // been freshly installed. Note that the default_action is NOT output.
    $this->drupalGet('<front>');
    $this->assertSession()->responseContains('"collapsiblock":{"active_pages":false,"slide_speed":200,"cookie_lifetime":null}');

    // Test that the default action still is not output, even after changing
    // settings.
    $this->setCollapsiblockGlobalSetting('2', 'default_action');
    drupal_flush_all_caches();
    $this->drupalGet('<front>');
    $this->assertSession()->responseContains('"collapsiblock":{"active_pages":false,"slide_speed":200,"cookie_lifetime":null}');

    // Test that a changed active_pages is output. Note we (intentionally) did
    // not reset global settings to their default since the last global settings
    // change.
    $this->setCollapsiblockGlobalSetting(TRUE, 'active_pages');
    drupal_flush_all_caches();
    $this->drupalGet('<front>');
    $this->assertSession()->responseContains('"collapsiblock":{"active_pages":true,"slide_speed":200,"cookie_lifetime":null}');

    // Test that a changed slide_speed is output. Note we (intentionally) did
    // not reset global settings to their default since the last global settings
    // change.
    $this->setCollapsiblockGlobalSetting(500, 'slide_speed');
    drupal_flush_all_caches();
    $this->drupalGet('<front>');
    $this->assertSession()->responseContains('"collapsiblock":{"active_pages":true,"slide_speed":500,"cookie_lifetime":null}');

    // Test that a changed cookie_lifetime is output. Note we (intentionally)
    // did not reset global settings to their default since the last
    // global settings change.
    $this->setCollapsiblockGlobalSetting(1, 'cookie_lifetime');
    drupal_flush_all_caches();
    $this->drupalGet('<front>');
    $this->assertSession()->responseContains('"collapsiblock":{"active_pages":true,"slide_speed":500,"cookie_lifetime":1}');
  }

}
