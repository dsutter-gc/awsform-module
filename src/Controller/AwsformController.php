<?php

namespace Drupal\awsform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Aws\Rekognition\RekognitionClient;

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
}

?>
