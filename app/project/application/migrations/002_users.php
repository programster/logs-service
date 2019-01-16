<?php

/* 
 * Add the users table for authentication when logging into the logging system
 */

class Migration_Users extends CI_Migration
{
    
    /**
     * Create the logs table that everything will be logged into.
     * @param \mysqli $mysqliConn
     */
    public function up() 
    {
        $query = 
            "CREATE TABLE `users` ( " .
                "`email` varchar(200) NOT NULL COMMENT 'the actual error message', " .
                "`password_hash` text NOT NULL COMMENT 'hash of the password from php password_hash', " .
                "PRIMARY KEY (`email`)" .
            ") ENGINE=InnoDB";
        
        $this->db->query($query);
        
        # Add the initial users        
        $initial_users = array(
            array(
                'email'         => '',
                'password_hash' => ''
            ),
        );
        
        foreach ($initial_users as $user_array)
        {
            $this->db->insert('users', $user_array);
        }
    }
    
    
    /**
     * Remove the logs table we created
     * @param \mysqli $mysqliConn - the mysqli connection.
     */
    public function down() 
    {
        $query  = "Drop table `users`";
        $this->db->query($query);
    }
}

