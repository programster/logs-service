<?php

// Specify how old a log can be (in seconds) before we archive it.
$thirtyDaysInSeconds = 30 * 24 * 60 * 60;
define('ARCHIVE_AGE', $thirtyDaysInSeconds);


// Switch on error reporting if ENVIRONMENT is not production/live
define('ENVIRONMENT', getenv('ENVIRONMENT'));
