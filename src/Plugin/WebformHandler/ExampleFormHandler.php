<?php
namespace Drupal\my_custom_form_handler\Plugin\WebformHandler;

use Drupal\Core\Session\Account\Interface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\RequestException;

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
    if ($fp = fopen('/tmp/webf', 'a')) {
      fwrite($fp, "name=" . $webform_submission->getData('name'));
      fclose($fp);
    }
  }
}