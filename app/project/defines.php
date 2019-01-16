<?php

# The maximum age of a signed request that we will handle (in seconds)
define('REQUEST_MAX_AGE', 520);

# Specify how old a log can be (in seconds) before we archive it.
$thirtyDaysInSeconds = 30 * 24 * 60 * 60;
define('ARCHIVE_AGE', $thirtyDaysInSeconds);