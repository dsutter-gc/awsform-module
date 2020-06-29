<?php

namespace Drupal\awsform\Controller;

//use Drupal\Core\Controller\ControllerBase;
//use Drupal\Core\Render\HtmlResponse;
//use Symfony\Component\HttpFoundation\JsonResponse;
//use Symfony\Component\HttpFoundation\RedirectResponse;
use Aws\Rekognition\RekognitionClient;


class AwsformController {

  private $rekognition;

  function __construct() {
    $this->rekognition = new RekognitionClient([
      'region' => 'us-east-1',
      'version' => 'latest'
    ]);
  }

  function detectLabels($filename) {

     // Call DetectLabels
     $result = $this->rekognition->DetectLabels(array(
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

  function detectFaces($filename){

  }
  
}
