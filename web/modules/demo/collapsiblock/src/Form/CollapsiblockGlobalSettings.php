<?php

namespace Drupal\collapsiblock\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for collapsiblock module.
 *
 * @package Drupal\collapsiblock\Form.
 */
class CollapsiblockGlobalSettings extends ConfigFormBase {

  const ACTION_OPTIONS = [
    1 => 'None.',
    2 => 'Collapsible, expanded by default.',
    3 => 'Collapsible, collapsed by default.',
    5 => 'Collapsible, expanded all the time.',
    4 => 'Collapsible, collapsed all the time.',
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'collapsiblock_global_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      'collapsiblock.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('collapsiblock.settings');

    $form['default_action'] = [
      '#type' => 'radios',
      '#title' => $this->t('Default block collapse behavior'),
      '#options' => $this::ACTION_OPTIONS,
      '#default_value' => $config->get('default_action'),
    ];
    $form['active_pages'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow menu blocks to be collapsed on page load if they have active links'),
      '#default_value' => $config->get('active_pages'),
    ];
    $options = [
      '50',
      '100',
      '200',
      '300',
      '400',
      '500',
      '700',
      '1000',
      '1300',
    ];
    $form['slide_speed'] = [
      '#type' => 'select',
      '#title' => $this->t('Animation speed'),
      '#options' => array_combine($options, $options),
      '#description' => $this->t('The animation speed in milliseconds.'),
      '#default_value' => $config->get('slide_speed'),
    ];

    $form['cookie_lifetime'] = [
      '#type' => 'number',
      '#step' => '0.01',
      '#title' => $this->t('Cookie lifetime (days)'),
      '#description' => $this->t('This field accepts days or part of the day, if you set to 0.5 for example, it will be a half day(12 hours)
                                    <br/>If you leave it blank, the cookie will expire at the time the user closes the browser.
                                    <br/>Insert a negative number (-1) if you don\'t want to store a cookie at all, for GDPR compliance for example.'),
      '#default_value' => $config->get('cookie_lifetime'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $values = $form_state->getValues();
    $this->config('collapsiblock.settings')
      ->set('default_action', $values['default_action'])
      ->set('active_pages', $values['active_pages'])
      ->set('slide_speed', $values['slide_speed'])
      ->set('cookie_lifetime', $values['cookie_lifetime'] ?? NULL)
      ->save();
  }

}
