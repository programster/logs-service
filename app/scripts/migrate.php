<?php

/* 
 * A script to run when you wish to run migrations
 */

require_once(__DIR__ . '/../bootstrap.php');

if (\iRAP\CoreLibs\Core::isCli() === FALSE)
{
    die("Cannot execute this script from the web.");
}

$migrationManager = new \iRAP\Migrations\MigrationManager(__DIR__ . '/../migrations', SiteSpecific::getDb());
$migrationManager->migrate();
