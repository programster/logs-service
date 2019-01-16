<?php

/**
 * Startup file for all things that need initialising that are not directly handled
 * by CodeIngiter.
 * This is bootstrap in the definition of the word, not related to Twitter's bootstrap
 * project
 */

// Ensure we are running on UTC time.
date_default_timezone_set('UTC'); 

# Load the projects settings
require_once(__DIR__ . '/defines.php');
require_once(__DIR__ . '/../settings/settings.php');

require_once(__DIR__ . '/application/libraries/SiteSpecific.php');
require_once(__DIR__ . '/application/objects/LogFilter.php');

# call to session start must be after all objects that may need storing in sessions have been defined
session_start();

# Load the autoloader for composer packages.
require_once(__DIR__ . '/vendor/autoload.php');

$classDirs = array(__DIR__ . '/application/models');
$autoloader = new iRAP\Autoloader\Autoloader($classDirs);
