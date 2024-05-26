<?php

namespace Drupal\Tests\collapsiblock\Functional;

/**
 * Test Collapsiblock settings on block instances.
 *
 * @group collapsiblock
 */
class BlockInstanceTest extends CollapsiblockFunctionalTestBase {

  /**
   * A user with permission to administer block settings.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $collapsiblockBlockAdminUser;

  /**
   * A block to test Collapsiblock with.
   *
   * @var \Drupal\block\BlockInterface
   */
  protected $collapsiblockTestBlock;

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $this->collapsiblockBlockAdminUser = $this->drupalCreateUser([
      'administer blocks',
      'access administration pages',
    ]);
    $this->collapsiblockTestBlock = $this->drupalPlaceBlock('page_title_block');
  }

  /**
   * Test that the block instance form shows functional Collapsiblock controls.
   */
  public function testBlockInstanceConfigForm() {
    $this->drupalLogin($this->collapsiblockBlockAdminUser);
    $testBlockEditUrl = $this->collapsiblockTestBlock->toUrl('edit-form');
    $this->drupalGet($testBlockEditUrl);

    // Test that the form controls are present.
    $this->assertSession()->checkboxChecked('edit-collapsiblock-settings-collapse-action-0');
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-1');
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-2');
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-3');
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-4');
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-5');

    // Submit the form with updated values.
    $configFormValues = [];
    $configFormValues['collapsiblock_settings[collapse_action]'] = '2';
    $this->submitForm($configFormValues, 'Save block');

    // Test that the form controls now show the updated configuration.
    $this->drupalGet($testBlockEditUrl);
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-0');
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-1');
    $this->assertSession()->checkboxChecked('edit-collapsiblock-settings-collapse-action-2');
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-3');
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-4');
    $this->assertSession()->checkboxNotChecked('edit-collapsiblock-settings-collapse-action-5');
  }

  /**
   * Test that block instance configuration is output to pages.
   */
  public function testBlockInstanceConfigOutput() {
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());

    $testBlockHtmlId = 'collapsiblock-wrapper-' . $this->collapsiblockTestBlock->id();

    // Add Collapsiblock configuration to the test block, telling the Block
    // Collapse Behavior to mirror the Global Default. Then, change the Global
    // Default to something other than 1 (i.e.: "None") so that Collapsiblock
    // acts on the block. Then test that the test block's output does in fact
    // use the Global Default value.
    $this->setCollapsiblockBlockInstanceSetting($this->collapsiblockTestBlock, 0, 'collapse_action');
    $this->setCollapsiblockGlobalSetting(2, 'default_action');
    drupal_flush_all_caches();
    $this->drupalGet('<front>');
    $this->assertSession()->responseContains('<div id="' . $testBlockHtmlId . '" class="collapsiblockTitle" data-collapsiblock-action="2" data-once="collapsiblock">');

    // Change the test block's Collapsiblock configuration to something other
    // than the Global Default, and test that the output uses the new value.
    $this->setCollapsiblockBlockInstanceSetting($this->collapsiblockTestBlock, 3, 'collapse_action');
    drupal_flush_all_caches();
    $this->drupalGet('<front>');
    $this->assertSession()->responseContains('<div id="' . $testBlockHtmlId . '" class="collapsiblockTitle" data-collapsiblock-action="3" data-once="collapsiblock">');
  }

}
