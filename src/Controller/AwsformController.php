<?php

namespace Drupal\awsform\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AwsformController extends ControllerBase {
  function apitest() {
    $module_path = drupal_get_path('module', 'awsform');

    $content  = "Ready.\n";
    $content .= "Module path is $module_path";

    return new HtmlResponse("<pre>$content</pre>");
  }
}
