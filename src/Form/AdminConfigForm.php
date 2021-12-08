<?php

namespace Drupal\commerce_review_notify\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AdminConfigForm.
 */
class AdminConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_review_notify_admin_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'commerce_review_notify.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = self::getValuesFromConfig();

    $form['email_subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('E-mail subject'),
      '#required' => TRUE,
      '#default_value' => $config['email_subject'],
    ];

    $form['email_subject_token_ui'] = [
      '#theme' => 'token_tree_link',
    ];

    $form['purge_interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Purge interval'),
      '#description' => $this->t('Sent notifications will be kept in the table for X days.'),
      '#field_suffix' => $this->t('days'),
      '#required' => TRUE,
      '#default_value' => $config['purge_interval'],
      '#attributes' => [
        'min' => 1,
      ],
    ];
    $form['sending_interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Sending interval'),
      '#description' => $this->t('Прозьба о отзыве будет отправлена через X days.<br><b>If 0, mail sent after order submitted.</b>'),
      '#field_suffix' => $this->t('days'),
      '#required' => TRUE,
      '#default_value' => $config['sending_interval'],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $days = $form_state->getValue('purge_interval');
    if ((int) $days <= 0) {
      $form_state->setErrorByName('purge_interval', $this->t('Purge interval must be a positive number.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $form_state->cleanValues();
    $config = $form_state->getValues();

    $this->config('commerce_review_notify.config')
      ->setData($config)
      ->save();
  }

  /**
   * Returns the configuration values.
   *
   * @return array
   *   Associative array containing the configuration.
   */
  public static function getValuesFromConfig() {
    $config = \Drupal::configFactory()->get('commerce_review_notify.config')->getRawData();
    $defaults = self::getDefaults();
    $values = [];
    $values['purge_interval'] = $config['purge_interval'] ?? $defaults['purge_interval'];
    $values['sending_interval'] = $config['sending_interval'] ?? $defaults['sending_interval'];
    $values['email_subject'] = $config['email_subject'] ?? $defaults['email_subject'];
    return $values;
  }

  /**
   * Get list of default values.
   *
   * @return array
   *   Default values.
   */
  public static function getDefaults() {
    return [
      'email_subject' => t('Cпасибо за покупку, оставьте пожалуйста отзыв! | [site:name]'),
      'purge_interval' => 30,
      'sending_interval' => 3,
    ];
  }

}
