<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function index()
    {
        if (USE_SSO)
        {
            if (!isset($_SESSION['user_id']))
            {
                $gotSsoData = true;

                if (isset($_GET['user_data']))
                {
                    $decodedUserJsonData = urldecode($_GET['user_data']);
                    $userDataArray       = json_decode($decodedUserJsonData, true);

                    if (!isset($userDataArray['user_id']))
                    {
                        $gotSsoData = false;
                    }
                }
                else
                {
                    $gotSsoData = false;
                }

                if (!$gotSsoData)
                {
                    $params = array(
                        'broker_id' => BROKER_ID);
                    header("Location: " . SSO_SITE_HOSTNAME . "?" . http_build_query($params));
                }
                else
                {
                    if (SiteSpecific::isValidSignature($userDataArray))
                    {
                        // Specifically set the session ID for the user. This way we can destroy
                        // a session for a particular user from another script.
                        // Cannot call session_id AFTER session_start if setting ID.
                        session_destroy();
                        session_id(SiteSpecific::generateSessionId());
                        session_start();

                        $_SESSION['user_id']    = $userDataArray['user_id'];
                        $_SESSION['user_name']  = $userDataArray['user_name'];
                        $_SESSION['user_email'] = $userDataArray['user_email'];

                        $this->loginRedirect();
                    }
                    else
                    {
                        # Invalid request, redirect the user back to sign in.
                        die('Invalid signature recieved.');
                        $params = array(
                            'broker_id' => BROKER_ID);
                        header("Location: " . SSO_SITE_HOSTNAME . "?" . http_build_query($params));
                    }
                }
            }
            else
            {
                # User is logged in, send them to the logs page
                header("Location: " . HOSTNAME . "/logs");
            }
        }
        else
        {
            if (!isset($_SESSION['user_id']))
            {
                if (isset($_POST['email']))
                {
                    if
                    (
                            $_POST['email'] == HARDCODED_LOGIN_EMAIL && $_POST['password'] === HARDCODED_LOGIN_PASSWORD
                    )
                    {
                        // Log the user in
                        $_SESSION['user_id'] = 570;
                        $return              = array(
                            'result' => 'success'
                        );
                    }
                    else
                    {
                        $return = array(
                            'result'  => 'error',
                            'message' => "Incorrect details provided."
                        );
                    }
                    header('Content-type: application/json');
                    print json_encode($return);
                    die;
                }
                // Display the login form.
                $header_view = SiteSpecific::get_view(__DIR__ . '/../views/header.php', array(
                            'title' => 'Logs'));
                $loginView   = SiteSpecific::get_view(__DIR__ . '/../views/login.php');
                $footer_view = SiteSpecific::get_view(__DIR__ . '/../views/footer.php');
                print $header_view . $loginView . $footer_view;
            }
            else
            {
                $this->loginRedirect();
            }
        }
    }


    /**
     * Redirecting after successful login or in cases when there already is session
     */
    private function loginRedirect()
    {
        extract($_GET);
        $to = $to ?? '';

        if ($to)
        {
            iRAP\CoreLibs\Core::redirectUser($to);
        }
        else
        {
            iRAP\CoreLibs\Core::redirectUser('/logs');
        }
    }


}
