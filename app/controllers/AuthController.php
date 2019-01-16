<?php


class AuthController
{
    /**
     * Handle a user's request to see the login page.
     */
    public function showLoginPage()
    {
        if (!isset($_SESSION['user_id'])) {
            // Display the login form.
            $header_view = SiteSpecific::get_view(
                __DIR__ . '/../views/header.php', array(
                'title' => 'Logs')
            );
            
            $loginView   = SiteSpecific::get_view(__DIR__ . '/../views/login.php');
            $footer_view = SiteSpecific::get_view(__DIR__ . '/../views/footer.php');
            print $header_view . $loginView . $footer_view;
        }
        else
        {
            $this->loginRedirect();
        }
    }
    
    
    /**
     * Handle a user submitting the login form.
     */
    public function handleLoginRequest()
    {
        $requiredParams = array(
            'email',
            'password'
        );
        
        if (!isset($_POST['email']))
        {
            die("Missing required email");
        }
        
        if (!isset($_POST['password']))
        {
            die("Missing required password");
        }
        
        if
        (
               $_POST['email'] == HARDCODED_LOGIN_EMAIL 
            && $_POST['password'] === HARDCODED_LOGIN_PASSWORD
        ) 
        {
            // Log the user in
            $_SESSION['user_id'] = 570;
            header("Location: /logs");
        }
        else
        {
            die("Incorrect details provided.");
        }
    }

    
    /**
     * Handle a users request to logout.
     */
    public function logout()
    {
        session_unset();
        die("You have logged out the logs frontend.");
    }
}
