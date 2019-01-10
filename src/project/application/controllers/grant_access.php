<?php

/* 
 * Controller for handling a request to grant a user access to the system.
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Grant_Access extends CI_Controller 
{
    public function index()
    {
        if (!isset($_GET['data']))
        {
            throw new Exception("missing required data parameter");
        }
        
        $urlEcodedData = $_GET['data'];
        $dataString = base64_decode($urlEcodedData);
        $dataArray = json_decode($dataString, true);
        
        if (!isset($dataArray['user_id']))
        {
            throw new Exception("Missing required user ID");
        }
        
        if (SiteSpecific::isValidSignature($dataArray))
        {
            $userId = $dataArray['user_id'];
            $query = "REPLACE INTO `users` SET `id`='" . $userId . "'";
            $db = SiteSpecific::get_ci_db();
            $db->query($query);
            
            die("User " . $userId . " has been granted access.");
        }
        else
        {
            throw new Exception("Invalid signature.");
        }
    }
}

