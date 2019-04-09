<?php

/* 
 * 
 */

class InitialSchema implements iRAP\Migrations\MigrationInterface
{
    public function up(mysqli $mysqliConn) 
    {
        $query = 
            "CREATE TABLE `logs` ( " .
                "`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier', " .
                "`message` text NOT NULL COMMENT 'the actual error message', " .
                "`context` longtext NOT NULL COMMENT 'json string of context for the error (see logging standards)', " .
                "`priority` int(1) NOT NULL COMMENT 'priority level, higher = more important', " .
                "`when` int unsigned NOT NULL, " .
                "`recieved` int unsigned NOT NULL, " .
                "PRIMARY KEY (`id`), " .
                "KEY `priority` (`priority`), " .
                "KEY `when` (`when`) " .
            ") ENGINE=InnoDB COMMENT='table for logging errors.'";
        
        SiteSpecific::getDb()->query($query);
    }
    
    
    /**
     * Remove the logs table we created
     *
     * @param \mysqli $mysqliConn - the mysqli connection.
     */
    public function down(mysqli $mysqli) 
    {
        $query  = "Drop table `logs`";
        SiteSpecific::getDb()->query($query);
    }
}

