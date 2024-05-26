<?php

namespace Drupal\Tests\collapsiblock\FunctionalJavascript;

use Drupal\block\Entity\Block;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\system\Entity\Menu;

/**
 * Test the global remember collapsed state on the active pages setting.
 *
 * @group collapsiblock
 */
class ActiveTrailTest extends CollapsiblockJavaScriptTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['block', 'collapsiblock', 'user', 'node', 'menu_ui'];

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer site configuration',
      'bypass node access',
      'administer site configuration',
      'access administration pages',
      'administer menu',
      'administer blocks',
    ]);
    $this->drupalLogin($this->adminUser);

    // Set global settings to "collapsible, collapsed by default".
    $this->drupalGet('admin/config/user-interface/collapsiblock');
    $configFormValues = [];
    $configFormValues['default_action'] = '3';
    $configFormValues['active_pages'] = '0';
    $configFormValues['slide_speed'] = 500;
    $configFormValues['cookie_lifetime'] = '1';
    $this->submitForm($configFormValues, 'Save configuration');

    // Create content type.
    $this->createContentType(['type' => 'test_menu']);

    // Create nodes.
    $this->drupalCreateNode([
      'type' => 'test_menu',
      'title' => 'Item 1',
    ]);

    $this->drupalCreateNode([
      'type' => 'test_menu',
      'title' => 'Item 2',
    ]);

    // Create a test menu.
    $menu = Menu::create([
      'id' => 'test_menu',
      'label' => 'Test',
      'description' => 'Test menu',
    ]);
    $menu->save();

    $menu_element_1 = MenuLinkContent::create([
      'id' => 'item_1',
      'parent' => '',
      'title' => 'Item 1',
      'menu_name' => 'test_menu',
      'link' => ['uri' => 'internal:/node/1'],
      'bundle' => 'test_menu',
    ]);
    $menu_element_1->save();

    $menu_element_2 = MenuLinkContent::create([
      'id' => 'item_2',
      'parent' => '',
      'title' => 'Item 2',
      'menu_name' => 'test_menu',
      'link' => ['uri' => 'internal:/node/2'],
      'bundle' => 'test_menu',
    ]);
    $menu_element_2->save();

    // Create block to display the menu as block.
    $block = Block::create([
      'plugin' => 'system_menu_block:' . $menu->id(),
      'region' => 'content',
      'id' => 'menu_block',
      'theme' => 'stark',
      'settings' => [
        'label' => 'Menu block',
      ],
    ]);
    $block->save();

    $this->collapsiblockTestBlock = $this->drupalPlaceBlock($block->id(), [
      'label_display' => TRUE,
    ]);
  }

  /**
   * Test the global remember the block state.
   */
  public function testMenuActiveTrail() {
    // Visit the node 1 page.
    $this->drupalGet('node/1');

    $collapsiblockTestBlockTitleXpath = $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//h2', [
      ':blockId' => '#collapse-menu_block',
    ]);
    $collapsiblockTestBlockContentXpath = $this->assertSession()->buildXPathQuery('//*[@id=:blockId]//li[1]', [
      ':blockId' => 'collapse-menu_block-content',
    ]);

    // We expecting that menu items are visible.
    $beforeTitle = $this->getSession()->getPage()->find('xpath', $collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($beforeTitle);
    $this->assertTrue($beforeTitle->isVisible());
    $beforeContent = $this->getSession()->getPage()->find('xpath', $collapsiblockTestBlockContentXpath);
    $this->assertNotNull($beforeContent);
    $this->assertTrue($beforeContent->isVisible());

    // Visit the user profile page.
    $this->drupalGet('/user/1');

    // We expecting that menu items are hidden.
    $beforeTitle = $this->getSession()->getPage()->find('xpath', $collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($beforeTitle);
    $this->assertTrue($beforeTitle->isVisible());
    $beforeContent = $this->getSession()->getPage()->find('xpath', $collapsiblockTestBlockContentXpath);
    $this->assertNotNull($beforeContent);
    $this->assertFalse($beforeContent->isVisible());

    // Visit the node 2 page.
    $this->drupalGet('node/2');

    // We expecting that menu items are visible.
    $beforeTitle = $this->getSession()->getPage()->find('xpath', $collapsiblockTestBlockTitleXpath);
    $this->assertNotNull($beforeTitle);
    $this->assertTrue($beforeTitle->isVisible());
    $beforeContent = $this->getSession()->getPage()->find('xpath', $collapsiblockTestBlockContentXpath);
    $this->assertNotNull($beforeContent);
    $this->assertTrue($beforeContent->isVisible());
  }

}
