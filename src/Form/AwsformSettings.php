<?php

namespace Drupal\awsform\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class AwsformSettings extends ConfigFormBase {
  const SETTINGS = 'awsform.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'awsform_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $credentials = preg_split("/\r?\n/", $form_state->getValue('aws_credentials'));

    $settings = [];
    foreach ($credentials as $cred) {
      $cred = str_replace('export ', '', $cred);
      $setting = explode('=', $cred, 2);
      $len = strlen($setting[1]);
      $settings[strtolower($setting[0])] = substr($setting[1], 1, $len-2);
    }

    if ($fp = fopen("/tmp/settings", "w")) {
      fwrite($fp, print_r($settings, true));
    }

    $config = $this->configFactory->getEditable(static::SETTINGS);
    foreach ($settings as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['pm_video.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $conf = $this->config(static::SETTINGS);
    // This initialises the config variable if not already initialised.

    $form['awsform']['aws_credentials'] = [
      '#type' => 'textarea',
      '#title' => t('AWS Credentials.'),
      '#default_value' => '',
      '#description' => t('Paste the whole set in here.'),
    ];

    return parent::buildForm($form, $form_state);
  }
}
