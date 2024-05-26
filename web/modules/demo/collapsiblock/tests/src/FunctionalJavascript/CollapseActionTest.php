<?php

namespace Drupal\Tests\collapsiblock\FunctionalJavascript;

/**
 * Test that collapse actions work as-intended.
 *
 * @group collapsiblock
 */
class CollapseActionTest extends CollapsiblockJavaScriptTestBase {

  /**
   * A block to test with.
   *
   * @var \Drupal\block\BlockInterface
   */
  protected $collapsiblockTestBlock;

  /**
   * The HTML ID of the test block.
   *
   * @var string
   */
  protected $collapsiblockTestBlockHtmlId;

  /**
   * An XPath string for the test block's title.
   *
   * @var string
   */
  protected $collapsiblockTestBlockTitleXpath;

  /**
   * An XPath string for the test block's content.
   *
   * @var string
   */
  protected $collapsiblockTestBlockContentXpath;

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $this->collapsiblockTestBlock = $this->drupalPlaceBlock('system_powered_by_block', [
      'label_display' => TRUE,
    ]);
    $this->collapsiblockTestBlockHtmlId = 'block-' . $this->collapsiblockTestBlock->id();
    $this->collapsiblockTestBlockTitleXpath = $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//h2', [
      ':blockId' => $this->collapsiblockTestBlockHtmlId,
    ]);
    $this->collapsiblockTestBlockContentXpath = $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//span', [
      ':blockId' => $this->collapsiblockTestBlockHtmlId,
    ]);
  }

  /**
   * Test the "Collapsible, collapsed all the time" Block Collapse Behavior.
   */
  public function testCollapsibleAlwaysCollapsed() {
    // Set the collapse action.
    $this->setCollapsiblockBlockInstanceSetting($this->collapsiblockTestBlock, 4, 'collapse_action');

    // Load a page that the block will be displayed on.
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());
    $this->drupalGet('<front>');

    // Check that initially, the block title is visible but the contents are not
    // visible.
    $beforeTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($beforeTitle);
    $this->assertTrue($beforeTitle->isVisible());
    $beforeContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($beforeContent);
    $this->assertFalse($beforeContent->isVisible());

    // Click on the block title.
    $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath)->click();
    sleep(1);

    // Check that the block title and contents are visible after the click.
    $afterTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($afterTitle);
    $this->assertTrue($afterTitle->isVisible());
    $afterContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($afterContent);
    $this->assertTrue($afterContent->isVisible());
  }

  /**
   * Test the "Collapsible, expanded all the time" Block Collapse Behavior.
   */
  public function testCollapsibleAlwaysExpanded() {
    // Set the collapse action.
    $this->setCollapsiblockBlockInstanceSetting($this->collapsiblockTestBlock, 5, 'collapse_action');

    // Load a page that the block will be displayed on.
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());
    $this->drupalGet('<front>');

    // Check that the block title and contents are initially visible.
    $beforeTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($beforeTitle);
    $this->assertTrue($beforeTitle->isVisible());
    $beforeContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($beforeContent);
    $this->assertTrue($beforeContent->isVisible());

    // Click on the block title.
    $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath)->click();
    sleep(1);

    // Check that the block title is visible but the contents are not visible
    // after the click.
    $afterTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($afterTitle);
    $this->assertTrue($afterTitle->isVisible());
    $afterContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($afterContent);
    $this->assertFalse($afterContent->isVisible());
  }

  /**
   * Test the "Collapsible, collapsed by default" Block Collapse Behavior.
   */
  public function testCollapsibleDefaultCollapsed() {
    // Set the collapse action.
    $this->setCollapsiblockBlockInstanceSetting($this->collapsiblockTestBlock, 3, 'collapse_action');

    // Load a page that the block will be displayed on.
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());
    $this->drupalGet('<front>');

    // Check that initially, the block title is visible but the contents are not
    // visible.
    $beforeTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($beforeTitle);
    $this->assertTrue($beforeTitle->isVisible());
    $beforeContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($beforeContent);
    $this->assertFalse($beforeContent->isVisible());

    // Click on the block title.
    $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath)->click();
    sleep(1);

    // Check that the block title and contents are visible after the click.
    $afterTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($afterTitle);
    $this->assertTrue($afterTitle->isVisible());
    $afterContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($afterContent);
    $this->assertTrue($afterContent->isVisible());
  }

  /**
   * Test the "Collapsible, expanded by default" Block Collapse Behavior.
   */
  public function testCollapsibleDefaultExpanded() {
    // Set the collapse action.
    $this->setCollapsiblockBlockInstanceSetting($this->collapsiblockTestBlock, 2, 'collapse_action');

    // Load a page that the block will be displayed on.
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());
    $this->drupalGet('<front>');

    // Check that the block title and contents are initially visible.
    $beforeTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($beforeTitle);
    $this->assertTrue($beforeTitle->isVisible());
    $beforeContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($beforeContent);
    $this->assertTrue($beforeContent->isVisible());

    // Click on the block title.
    $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath)->click();
    sleep(1);

    // Check that the block title is visible but the contents are not visible
    // after the click.
    $afterTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($afterTitle);
    $this->assertTrue($afterTitle->isVisible());
    $afterContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($afterContent);
    $this->assertFalse($afterContent->isVisible());
  }

  /**
   * Test the "None" Block Collapse Behavior makes no change.
   */
  public function testNoAction() {
    // Set the collapse action.
    $this->setCollapsiblockBlockInstanceSetting($this->collapsiblockTestBlock, 1, 'collapse_action');

    // Load a page that the block will be displayed on.
    $this->drupalLogin($this->getCollapsiblockUnprivilegedUser());
    $this->drupalGet('<front>');

    // Check that the block title and contents are initially visible.
    $beforeTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($beforeTitle);
    $this->assertTrue($beforeTitle->isVisible());
    $beforeContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($beforeContent);
    $this->assertTrue($beforeContent->isVisible());

    // Click on the block title.
    $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath)->click();
    sleep(1);

    // Check that the block title and contents are visible after the click.
    $afterTitle = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($afterTitle);
    $this->assertTrue($afterTitle->isVisible());
    $afterContent = $this->getSession()->getPage()->find('xpath', $this->collapsiblockTestBlockContentXpath);
    $this->assertNotNull($afterContent);
    $this->assertTrue($afterContent->isVisible());
  }

}
