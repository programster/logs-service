<?php


class AuthController extends AbstractSlimController
{
    /**
     * Register routes with slim routing.
     * @param \Slim\App $app
     */
    public static function registerWithApp(\Slim\App $app)
    {
        $app->get('/login', function (\Slim\Http\Request $request, Slim\Http\Response $response, $args) {
            $logsController = new AuthController($request, $response, $args);
            return $logsController->showLoginPage();
        });
        
        return $app;
    }
    
    
    /**
     * Handle a user's request to see the login page.
     */
    public function showLoginPage()
    {
        if (!isset($_SESSION['user_id'])) 
        {
            // Display the login form.
            $headerView = SiteSpecific::getView(
                __DIR__ . '/../views/header.php', array(
                'title' => 'Logs')
            );
            
            $loginView = SiteSpecific::getView(__DIR__ . '/../views/login.php');
            $footer_view = SiteSpecific::getView(__DIR__ . '/../views/footer.php');
            print $headerView . $loginView . $footer_view;
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
