<?php

/* 
 * A script to run when you wish to run migrations
 */

if (\Programster\CoreLibs\Core::isCli() === FALSE)
{
    die("Cannot execute this script from the web.");
}

require_once(__DIR__ . '/../bootstrap.php');

$migrationManager = new iRAP\Migrations\MigrationManager(__DIR__ . '/../migrations', SiteSpecific::getDb());
$migrationManager->migrate();
