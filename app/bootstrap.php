<?php

// Load the autoloader for composer packages.
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/.env');

// Switch on error reporting if ENVIRONMENT is not production/live
if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT)
    {
        case 'dev':
        case 'staging':
        case 'release':
        {
            error_reporting(E_ALL);
        }
        break;

        case 'live':
        case 'production':
        {
            error_reporting(0);
        }
        break;

        default:
        {
            exit('The application environment is not set correctly.');
        }
    }
}


// Ensure we are running on UTC time.
date_default_timezone_set('UTC'); 

// Load the projects settings
require_once __DIR__ . '/defines.php';

require_once __DIR__ . '/application/libraries/SiteSpecific.php';
require_once __DIR__ . '/application/objects/LogFilter.php';


// call to session start must be after all objects that may need storing in sessions have been defined
session_start();


$classDirs = array(__DIR__ . '/application/models');
$autoloader = new iRAP\Autoloader\Autoloader($classDirs);
