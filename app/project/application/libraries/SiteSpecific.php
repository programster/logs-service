<?php

class SiteSpecific
{
    /**
     * Get an instance of the Codeigniter object in order to run methods
     * in static functions. E.g. the loading of models etc.
     * @return CI_Controller
     */
    public static function get_ci_instance()
    {
        $CI = &CI_Controller::get_instance();
        return $CI;
    }


    /**
     * Helper function that Fetches the database instance from codeigniter.
     * @param void
     * @return CI_DB_driver - the relevant driver for the database we are interfacing with
     */
    public static function get_ci_db()
    {
        $CI = &CI_Controller::get_instance();

        if (!isset($CI->db))
        {
            $CI->load->database();
        }

        return $CI->db;
    }


    /**
     * Helper function that Fetches the database instance from codeigniter.
     * @param void
     * @return CI_DB_driver - the relevant driver for the database we are interfacing with
     */
    public static function get_mysqli_db()
    {
        global $globals;
        static $db = null;

        if ($db == null)
        {
            $dbDetails = $globals['databases']['default'];

            $db = new mysqli(
                $dbDetails['hostname'],
                $dbDetails['username'],
                $dbDetails['password'],
                $dbDetails['database']
            );
        }

        return $db;
    }


    /**
     * Returns the dbforge object
     * @param void
     * @return CI_DB_forge $dbForge - the CI_DB_forge object from CI framework.
     */
    public static function get_dbforge()
    {
        # Technically, you dont need the CI_Controller part as get_instance has been 'reserved' for
        # this framework, but it helps understanding.
        $CI = &CI_Controller::get_instance();

        # Load it if it isn't already
        $CI->load->dbforge();
        $dbForge = $CI->dbforge;
        return $dbForge;
    }


    /**
     * Fetch Codeigniters session.
     * Useful for static functions or objects that are not part of the CI framework.
     * @return Object
     */
    public static function get_session()
    {
        $CI = &CI_Controller::get_instance();

        if (!isset($CI->session))
        {
            $CI->load->library('session');
        }

        return $CI->session;
    }


    /**
     * Way to allow rendering a view multiple times by storing it into a variable
     * refer to: http://www.paulund.co.uk/assign-html-to-a-variable
     * @param String $view_file_loc - path to the view file
     * @param Array $vars - the variables that the view needs.
     * @return mixed - string if view was found, false if not.
     */
    public static function get_view($view_file_loc, $vars = NULL)
    {
        $view = false;

        // Check the view file exists
        if (file_exists($view_file_loc))
        {
             // Extract the variables to be used by the view
            if (!is_null($vars))
            {
                extract($vars);
            }

            ob_start();

            include_once $view_file_loc;

            $view = ob_get_contents();

            ob_end_clean();
        }

        return $view;
    }


    /**
     * Fetch the single emailer instance that we use to send emails.
     * @staticvar iRAP\Emailers\AwsEmailer $emailer
     * @return iRAP\Emailers\EmailerInterface
     */
    public static function get_emailer()
    {
        static $emailer = null;

        if ($emailer === null)
        {
            $emailer = new iRAP\Emailers\PhpMailerEmailer(
                SMTP_HOST,
                SMTP_USERNAME,
                SMTP_PASSWORD,
                'tls',
                SMTP_FROM, ENVIRONMENT . ' Logs Service',
                587, 
                '',
                'noreply'
            );
        }

        return $emailer;
    }


    /**
     * Generate a session ID to use for a given user_id. We need to do this so
     * that we can figure out which file to destroy (to destroy the session) for
     * the appropriate user when we get a logout request for a specific user ID.
     * @param int $user_id - the ID of the user we are generating a session ID for.
     */
    public static function generateSessionId($user_id)
    {
        return hash_hmac('sha256', $user_id, BROKER_SECRET);
    }


    /**
     * Check whether the user details sent to us came from
     * the SSO service without being modified.
     * @param $dataArray - array of name/value pairs in the received data
     */
    public static function isValidSignature($dataArray)
    {
        if (!isset($dataArray['signature']))
        {
            throw new Exception("Missing signature");
        }

        $recievedSignature = $dataArray['signature'];
        unset($dataArray['signature']);
        ksort($dataArray);
        $jsonString = json_encode($dataArray);
        $generatedSignature = hash_hmac('sha256', $jsonString, BROKER_SECRET);

        return ($generatedSignature === $recievedSignature);
    }
}
