<?php

# Controller for running migrations that will be run once upon deployment.
# Please refer to:
# https://ellislab.com/codeigniter/user-guide/libraries/migration.html

class Migrate extends CI_Controller 
{
    public function index()
    {
        if (iRAP\CoreLibs\Core::isCli())
        {
            $this->load->library('migration');
            
            # very important to use "latest" here instead of "current"
            # latest will figure out from the schemas what the latest one is
            # current will rely on it being hardcoded somewhere.
            if (!$this->migration->latest())
            {
                print $this->migration->error_string();
            }
        }
        else
        {
            $err_msg = "Migrations cannot be called from the web interface " . 
                       "but are supposed to be run once on deployment";
            show_error($err_msg);
        }
    }
}
