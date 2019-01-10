<?php

/* 
 * 
 */

class Migration_Initial_schema extends CI_Migration
{
    
    /**
     * Create the logs table that everything will be logged into.
     * @param \mysqli $mysqliConn
     */
    public function up() 
    {
        $query = 
            "CREATE TABLE `logs` ( " .
                "`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier', " .
                "`message` text NOT NULL COMMENT 'the actual error message', " .
                "`context` longtext NOT NULL COMMENT 'json string of context for the error (see logging standards)', " .
                "`priority` int(1) NOT NULL COMMENT 'priority level, higher = more important', " .
                "`when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, " .
                "PRIMARY KEY (`id`), " .
                "KEY `priority` (`priority`), " .
                "KEY `when` (`when`) " .
            ") ENGINE=InnoDB COMMENT='table for logging errors.'";
        
        $this->db->query($query);
    }
    
    
    /**
     * Remove the logs table we created
     * @param \mysqli $mysqliConn - the mysqli connection.
     */
    public function down() 
    {
        $query  = "Drop table `logs`";
        $this->db->query($query);
    }

}

