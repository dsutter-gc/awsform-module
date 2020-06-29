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
  function apitest($filename) {
    $module_path = drupal_get_path('module', 'awsform');

    $rekognition = new RekognitionClient([
        'region' => 'us-east-1',
        'version' => 'latest'
    ]);

     // Call DetectLabels
     $result = $rekognition->DetectLabels(array(
      'Image' => array(
         'S3Object' => array(
             'Bucket' => 'drupal-rekognition',
             'Name' => $filename
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

    return $content;
  }
  
}
