<?php

class SiteSpecific
{
    /**
     * Helper function that Fetches the database instance from codeigniter.
     *
     * @param  void
     * @return CI_DB_driver - the relevant driver for the database we are interfacing with
     */
    public static function getDb() : mysqli
    {
        static $db = null;
        
        if ($db === null)
        {
            $db = new mysqli(
                getenv("DB_HOST"), 
                getenv("MYSQL_USER"), 
                getenv("MYSQL_PASSWORD"), 
                getenv("MYSQL_DATABASE")
            );
        }
        
        return $db;
    }
    
    
    /**
     * Fetch the single emailer instance that we use to send emails.
     *
     * @staticvar iRAP\Emailers\AwsEmailer $emailer
     * @return    iRAP\Emailers\EmailerInterface
     */
    public static function getEmailer()
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
    
    
    public static function createHtmlResponse(\Slim\Http\Response $response, string $html, int $code=200) : \Slim\Http\Response
    {
        $newResponse = $response->write($html);
        return $newResponse;
    }
    
    
    public static function createJsonResponse(\Slim\Http\Response $response, array $content, int $code) : \Slim\Http\Response
    {
        $bodyJson = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        return $response->withStatus($code)
            ->withHeader("Content-Type", "application/json")
            ->write($bodyJson);
    }
}
