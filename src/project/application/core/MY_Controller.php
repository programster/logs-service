<?php

/*
 * Base controller to be extended for any controllers that require the user to be logged in.
 */

class My_Controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        # redirect the user to the login page if they are not logged in.
        if (!isset($_SESSION['user_id']))
        {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
            {
                header('HTTP/1.1 401 Unauthorized');
                die;
            }
            else
            {
                $protocol   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http';
                // the current url the user is trying to access, this is required so as to redirect user to this after successful login
                $return_url = urlencode("{$protocol}://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}");

                iRAP\CoreLibs\Core::redirectUser("/login?to={$return_url}");
            }
        }
    }


}
