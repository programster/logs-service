<?php

require_once __DIR__ . '/bootstrap.php';



$slimSettings = array('determineRouteBeforeAppMiddleware' => true);

if (ENVIRONMENT === 'dev') {
    $slimSettings['displayErrorDetails'] = true;
}

$slimConfig = array('settings' => $slimSettings);
$app = new Slim\App($slimConfig);
