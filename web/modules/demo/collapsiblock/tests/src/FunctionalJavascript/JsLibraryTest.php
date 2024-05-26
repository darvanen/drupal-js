<?php

namespace Drupal\Tests\collapsiblock\FunctionalJavascript;

/**
 * Test the JS library operates on blocks with collapsiblock instance settings.
 *
 * @group collapsiblock
 */
class JsLibraryTest extends CollapsiblockJavaScriptTestBase {

  /**
   * Test JS modifies block HTML w/ Block collapse behavior !== "None".
   */
  public function testJsDoesEnhanceConfiguredBlocks() {
    // Set up a block to test with, with a label, with a Block collapse behavior
    // which we know will result in a client-side DOM transformation.
    $testBlock = $this->drupalPlaceBlock('system_powered_by_block', [
      'label_display' => TRUE,
    ]);
    $this->setCollapsiblockBlockInstanceSetting($testBlock, 2, 'collapse_action');

    $testBlockHtmlId = 'block-' . $testBlock->id();

    // Load a page that the block will be displayed on.
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());
    $this->drupalGet('<front>');

    // Check that the block is on the page.
    $this->assertSession()->elementExists('xpath', $this->assertSession()->buildXPathQuery('//*[@id=:blockId]', [
      ':blockId' => $testBlockHtmlId,
    ]));

    // Check that the JS adds a 'collapsiblock-wrapper-' element inside the
    // block.
    $this->assertSession()->waitForElement('xpath', $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//div[starts-with(@id, :innerWrapperIdStart)]', [
      ':blockId' => $testBlockHtmlId,
      ':innerWrapperIdStart' => 'collapsiblock-wrapper-',
    ]));

    // Check that the JS adds a #collapse- link inside the block.
    $this->assertSession()->waitForElement('xpath', $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//a[starts-with(@href, :collapseLinkHrefStart)]', [
      ':blockId' => $testBlockHtmlId,
      ':collapseLinkHrefStart' => '#collapse-',
    ]));

    // @phpstan-ignore-next-line
    $this->getSession()->getSelectorsHandler();
    $this->assertSession();
  }

  /**
   * Test JS only partially modifies a block without a title.
   */
  public function testBlockWithoutBlockTitlePartiallyAffected() {
    // Set up a block to test with, WITHOUT a label, with a Block collapse
    // behavior which we know will result in a client-side DOM transformation.
    $testBlock = $this->drupalPlaceBlock('system_powered_by_block', [
      'label_display' => FALSE,
    ]);
    $this->setCollapsiblockBlockInstanceSetting($testBlock, 2, 'collapse_action');

    $testBlockHtmlId = 'block-' . $testBlock->id();

    // Load a page that the block will be displayed on.
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());
    $this->drupalGet('<front>');

    // Check that the block is on the page.
    $this->assertSession()->elementExists('xpath', $this->assertSession()->buildXPathQuery('//*[@id=:blockId]', [
      ':blockId' => $testBlockHtmlId,
    ]));

    // Check that the block contains a 'collapsiblock-wrapper-' element inside
    // it (indicating that it did undergo a client-side DOM transformation).
    $this->assertSession()->elementExists('xpath', $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//div[starts-with(@id, :innerWrapperIdStart)]', [
      ':blockId' => $testBlockHtmlId,
      ':innerWrapperIdStart' => 'collapsiblock-wrapper-',
    ]));

    // Check that the block does not contain a #collapse- link inside it
    // (indicating that it did not undergo a client-side DOM transformation).
    $this->assertSession()->elementNotExists('xpath', $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//a[starts-with(@href, :collapseLinkHrefStart)]', [
      ':blockId' => $testBlockHtmlId,
      ':collapseLinkHrefStart' => '#collapse-',
    ]));
  }

  /**
   * Test JS does not modify block HTML w/ Block collapse behavior == "None".
   */
  public function testJsDoesNotEnhanceNoActionBlock() {
    // Set up a block to test with, with a Block collapse behavior of "None",
    // which we know will NOT result in a client-side DOM transformation.
    $testBlock = $this->drupalPlaceBlock('system_powered_by_block', [
      'label_display' => TRUE,
    ]);
    $this->setCollapsiblockBlockInstanceSetting($testBlock, 1, 'collapse_action');

    $testBlockHtmlId = 'block-' . $testBlock->id();

    // Load a page that the block will be displayed on.
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());
    $this->drupalGet('<front>');

    // Check that the block is on the page.
    $this->assertSession()->elementExists('xpath', $this->assertSession()->buildXPathQuery('//*[@id=:blockId]', [
      ':blockId' => $testBlockHtmlId,
    ]));

    // Check that the block does not contain a 'collapsiblock-wrapper-' element
    // inside it (indicating that it did not undergo a client-side DOM
    // transformation).
    $this->assertSession()->elementNotExists('xpath', $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//div[starts-with(@id, :innerWrapperIdStart)]', [
      ':blockId' => $testBlockHtmlId,
      ':innerWrapperIdStart' => 'collapsiblock-wrapper-',
    ]));

    // Check that the block does not contain a #collapse- link inside it
    // (indicating that it did not undergo a client-side DOM transformation).
    $this->assertSession()->elementNotExists('xpath', $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//a[starts-with(@href, :collapseLinkHrefStart)]', [
      ':blockId' => $testBlockHtmlId,
      ':collapseLinkHrefStart' => '#collapse-',
    ]));
  }

}
