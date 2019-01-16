<?php

/*
 * Change the users table to just be a list of user_ids we let through from the SSO service.
 * If user's are not in this table, we will send a request to the admin email address which they
 * can click to allow the user access.
 */

class Migration_Sso extends CI_Migration
{
    /**
     * Create the users table.
     * @param \mysqli $mysqliConn
     */
    public function up()
    {
        $query  = "Drop table `users`";
        $this->db->query($query);

        $query =
            "CREATE TABLE `users` ( " .
                "`id` int(11) NOT NULL COMMENT 'ID of user that is allowed to access this system.', " .
                "PRIMARY KEY (`id`)" .
            ") ENGINE=InnoDB";

        $this->db->query($query);
    }


    /**
     * Remove the logs table we created
     * @param \mysqli $mysqliConn - the mysqli connection.
     */
    public function down()
    {
        $query  = "Drop table `users`";
        $this->db->query($query);

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
}
