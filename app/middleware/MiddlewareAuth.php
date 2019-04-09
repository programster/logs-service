<?php

/*
 * Middleware that ensures that a user is logged in. 
 */

class MiddlewareAuth
{
    /**
     * Create our middleware. You don't have to have a constructor.
     * @param $someValue - some value to configure our middleware
     */
    public function __construct()
    {
        // do nothing
    }


    /**
     * This is the important bit when creating middleware and what gets invoked (hence the name)
     */
    public function __invoke(Slim\Http\Request $request, \Slim\Http\Response $response, $next) 
    {
        $route = $request->getAttribute('route');

        if (!empty($route))
        {
            $routeName = $route->getName();
        }

        if ($this->doSomeCheck($routeName))
        {
            $returnResponse = $next($request, $response);
        }
        else
        {
            // pretend something went wrong, causing the check to fail
            // and return a JSON error message
            $data = array(
                "message" => "something went wrong",
            );

            $responseCode = 500; //internal server error
            $bodyJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            $returnResponse = $response->withStatus($responseCode)
                                ->withHeader("Content-Type", "application/json")
                                ->write($bodyJson);
        }

        return $returnResponse;
    }
    
    
    /**
     * Return whether the user is logged in or not.
     * @return bool - true if the user is logged in, false if not.
     * @throws Exception
     */
    private function isLoggedIn() : bool
    {
        // only allow granted users access
        if (!isset($_SESSION['user_id']))
        {
            throw new Exception('missing session user ID');
        }

        $userId = $_SESSION['user_id'];
        $query = "SELECT * FROM `users` WHERE `id`='" . $userId . "'";
        $db = SiteSpecific::getDb();
        $result = $db->query($query);
        /* @var $result mysqli_result */
        
        if ($result->num_rows == 0) 
        {
            
        }
    }
    
    
    /**
     * Send a server-signed request to an admin with a link that if clicked will give the
     * user who tried to sign in, the ability to log in, in future.
     */
    private function sendAdminEmail()
    {
        $data = array(
            'user_id' => $userId,
            'nonce' => \Programster\CoreLibs\StringLib::generateRandomString(24)
        );

        ksort($data);
        $jsonData = json_encode($data);
        $signature = hash_hmac('sha256', $jsonData, BROKER_SECRET);
        $data['signature'] = $signature;
        $jsonData = json_encode($data);
        $url = HOSTNAME . '/grant_access?data=' . base64_encode($jsonData);

        $emailBody = 
            "User: " . $userId . " has tried to access the logging service but has " .
                "not been given access yet. " .
                "<br />" .
                "<a href=" . $url . ">Click here to grant permission.</a>";

        $emailer = SiteSpecific::getEmailer();

        $emailer->send(
            "IT Admin", 
            ADMIN_EMAIL, 
            $subject = "Logs Access Request", 
            $emailBody
        );

        $outputMessage = 
            "You have not yet been granted access. " . 
            "An email has been sent to the admin account to grant/deny access.";

        die($outputMessage);
    }
}