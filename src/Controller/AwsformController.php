<?php

namespace Drupal\awsform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Aws\Rekognition\RekognitionClient;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\webform\WebformSubmissionForm;

class AwsformController extends ControllerBase {
  function apitest() {
    $module_path = drupal_get_path('module', 'awsform');

    $bucket = 'drupal-rekognition';
    $image = 'olive.jpg';

    // Export aws credentials

    putenv("AWS_ACCESS_KEY_ID=xxx");
    putenv("AWS_SECRET_ACCESS_KEY=xxx");
    putenv("AWS_SESSION_TOKEN=xxx");

    $rekognition = new RekognitionClient([
        'region' => 'us-east-1',
        'version' => 'latest'
    ]);

     // Call DetectLabels
     $result = $rekognition->DetectLabels(array(
      'Image' => array(
         'S3Object' => array(
             'Bucket' => $bucket,
             'Name' => $image
         )
      ),
      'Attributes' => array('ALL')
      )
   );

   $content = 'Results: ' . PHP_EOL;
   for ($n=0;$n<sizeof($result['Labels']); $n++){
      $content .= 'Label: ' . $result['Labels'][$n]['Name']
      . PHP_EOL
      . 'Confidence: ' . $result['Labels'][$n]['Confidence']
      . PHP_EOL . PHP_EOL;
    }

    return new HtmlResponse("<pre>$content</pre>");
  }

  function webform() {
    $sid = $_GET['sid'];

    $content = "ok";

    // Example IDs
    $webform_id = 'image_recognition';
    $webform_submission_id = $sid;

    // Check webform is open.
    $webform = Webform::load($webform_id);
    $is_open = WebformSubmissionForm::isOpen($webform);

    if ($is_open === TRUE) {
      // Load submission
      $webform_submission = WebformSubmission::load($webform_submission_id);
      $content .= "type=" . get_class($webform_submission);

      // Modify submission values
      $webform_submission->setElementData('name', 'Whatever');

      // Validate submission.
      $errors = WebformSubmissionForm::validateWebformSubmission($webform_submission);

      // Check there are no validation errors.
      if (!empty($errors)) {
        print_r($errors);
      }
      else {
        // Submit values and get submission ID.
        $webform_submission = WebformSubmissionForm::submitWebformSubmission($webform_submission);
//        print $webform_submission->id();
      }
    }

    return new HtmlResponse("<pre>$content</pre>");
  }
}
