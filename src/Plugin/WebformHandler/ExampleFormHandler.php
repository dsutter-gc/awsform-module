<?php
namespace Drupal\awsform\Plugin\WebformHandler;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

//use Guzzle\Http\Client;
//use Guzzle\Http\Exception\RequestException;

/**
 * Form submission handler.
 *
 * @WebformHandler(
 *   id = "example_form_handler",
 *   label = @Translation("Example form handler"),
 *   category = @Translation("Examples"),
 *   description = @Translation("An example form handler"),
 *   cardinality = Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class ExampleFormHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    if ($fp = fopen('/tmp/webform', 'a')) {
      fwrite($fp, "name=" . print_r($webform_submission->getData('name'), true));
      fclose($fp);
    }
  }
}