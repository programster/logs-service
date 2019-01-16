<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller
{
    public function index()
    {
        try
        {
            if (isset($_GET['data']))
            {
                $decodedUserJsonData = urldecode($_GET['data']);
                $dataArray = json_decode($decodedUserJsonData, true);

                if (!isset($dataArray['signature']))
                {
                    throw Exception("Missing required signature");
                }

                if (!isset($dataArray['user_id']))
                {
                    throw Exception("Missing required user ID");
                }

                if (!isset($dataArray['time']))
                {
                    throw Exception("Missing required timestamp");
                }

                # check the timestamp is recent so we dont suffer from replay attacks.
                date_default_timezone_set('UTC');

                if (microtime($get_as_float=true) - $dataArray['time'] > REQUEST_MAX_AGE)
                {
                    throw new Exception("Request is out of date.");
                }

                if (microtime(true) - $dataArray['time'] < -1)
                {
                    throw new Exception("The request came from the future");
                }

                # Check the signature is valid (so we know request actually came from sso)
                if (SiteSpecific::isValidSignature($dataArray))
                {
                    $session_id = generateSessionId($dataArray['user_id']);
                    $session_filepath = session_save_path() . '/' . 'sess_' . $session_id;

                    if (file_exists($session_filepath))
                    {
                        $deletedFile = unlink($session_filepath);

                        if ($deletedFile)
                        {
                            $responseArray = array(
                                "result"  => "success",
                                "message" => "User wasn't logged in."
                            );
                        }
                        else
                        {
                            $responseArray = array(
                                "result"  => "error",
                                "message" => "Failed to destroy user's session."
                            );
                        }
                    }
                    else
                    {
                        $responseArray = array(
                            "result"  => "success",
                            "message" => "User wasn't logged in."
                        );
                    }
                }
                else
                {
                    # Invalid request, redirect the user back to sign in.
                    throw new Exception("Invalid signature.");
                }
            }
            else
            {
                # User may be trying to initialize the logout, rather than recieving a
                # logout request from the SSO, in which case unset the session and
                # direct the user to sso logout to log out.
                session_unset();

                if (USE_SSO)
                {
                    header("Location: " . SSO_SITE_HOSTNAME . "/logout");
                }
                else
                {
                    die("You have logged out the logs frontend.");
                }
            }
        }
        catch (Exception $e)
        {
            $responseArray = array(
                "result"  => "error",
                "message" => $e->getMessage()
            );
        }

        print json_encode($responseArray);
    }
}
