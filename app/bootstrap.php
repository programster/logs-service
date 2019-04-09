<?php

// Load the autoloader for composer packages.
require_once(__DIR__ . '/vendor/autoload.php');

$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/.env');


// Load the projects settings
require_once(__DIR__ . '/defines.php');

$classDirs = array(
    __DIR__,
    __DIR__ . '/collections',
    __DIR__ . '/controllers',
    __DIR__ . '/libs',
    __DIR__ . '/middleware',
    __DIR__ . '/models',
    __DIR__ . '/objects',
    __DIR__ . '/views',
    __DIR__ . '/views/partials',
);

$autoloader = new iRAP\Autoloader\Autoloader($classDirs);



switch (getenv('ENVIRONMENT'))
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

// Ensure we are running on UTC time.
date_default_timezone_set('UTC'); 

// call to session start must be after all objects that may need storing in sessions have been defined
require_once __DIR__ . '/libs/SiteSpecific.php';
require_once __DIR__ . '/objects/LogFilter.php';
session_start();
