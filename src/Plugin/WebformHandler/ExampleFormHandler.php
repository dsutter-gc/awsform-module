<?php
namespace Drupal\awsform\Plugin\WebformHandler;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
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

    $data = $webform_submission->getData();
    $name = $data['name'];
    $fid = $data['image_rekognition'];

    $image = \Drupal::entityManager()->getStorage('file')->load($fid);
    $fn = $image->getFilename();
    $uri = $image->getFileUri();
    $path = \Drupal::service('file_system')->realpath($uri);

    /* Read image bytes
    $file = fopen($path, "rb");
    $contents = fread($file, filesize($path));
    fclose($file);
    */
    $contents = file_get_contents($path);

    $conf = \Drupal::config('awsform.settings');
    $aws_access_key_id = $conf->get('aws_access_key_id');
    $aws_secret_access_key = $conf->get('aws_secret_access_key');
    $aws_session_token = $conf->get('aws_session_token');

    // Export aws credentials
    putenv("AWS_ACCESS_KEY_ID=$aws_access_key_id");
    putenv("AWS_SECRET_ACCESS_KEY=$aws_secret_access_key");
    putenv("AWS_SESSION_TOKEN=$aws_session_token");


    $s3 = new S3Client([
      'version' => 'latest',
      'region'  => 'us-east-1'
    ]);

    $bucket = 'drupal-rekognition';

    try {
      // Upload the submitted image to S3 bucket
      $result = $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => $fn,
        'Body'   => $contents,
        'ACL'    => 'private'
      ]);

      // Print the URL to the object in /tmp/webform
      if ($fp = fopen('/tmp/webform', 'a')) {
        fwrite($fp, $result['ObjectURL']);
        fclose($fp);
      }
    } catch (S3Exception $e) {
       // Print error message in /tmp/webform_error
       if ($fp = fopen('/tmp/webform_error', 'a')) {
        fwrite($fp, $e->getMessage());
        fclose($fp);
      }
    }
  }
}