<?php

/* 
 * Add a table to hold the archived logs.
 * A background script will try and move logs into this table.
 */

class Migration_Archive_table extends CI_Migration
{
    /**
     * Create the users table. 
     * @param \mysqli $mysqliConn
     */
    public function up() 
    {
        $db = SiteSpecific::get_mysqli_db();
        
        $createArchiveTableQuery = "CREATE TABLE `logs_archive` (
            `uuid` binary(16) NOT NULL,
            `message` text NOT NULL,
            `context` longtext NOT NULL,
            `priority` int(1) NOT NULL,
            `when` timestamp NOT NULL,
            PRIMARY KEY (`uuid`),
            KEY `priority` (`priority`),
            KEY `when` (`when`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        
        $result = $db->query($createArchiveTableQuery);
        
        if ($result === FALSE)
        {
            throw new Exception("Failed to convert logs table to utf8.");
        }
    }
    
    
    /**
     * Remove the logs table we created
     * @param \mysqli $mysqliConn - the mysqli connection.
     */
    public function down() 
    {
        
    }
}

