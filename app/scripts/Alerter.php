<?php

/* 
 * Checks for any new activity that hasn't already been checked, and sends out email alerts
 * This is managed and called by the alerter script
 * 
 * This script is meant to be managed by supervisor to ensure that it is always running and only
 * one instance of it is running.
 */

require_once __DIR__ . '/../../bootstrap.php';

class Alerter
{
    private static $s_last_log_id;
    
    
    public function __construct() 
    {
    }
    
    
    /**
     * Entry point that checks if the script is running, and if not, continues by executing
     * the relevant controller(s) for the relevant time period.
     */
    public function run()
    {
        // We dont allow people to manually run this through the web interface as it is supposed to be managed
        // through a single instance script.
        if (! \iRAP\CoreLibs\Core::isCli()) {
            throw new Exception("The alerter is only meant to be run from the CLI.");
        }
        
        print 'running check' . PHP_EOL;
        $logs = self::get_logs();
        self::send_email_alerts($logs);
        
        print "sleeping" . PHP_EOL;
        
        // sleep for the EMAIL_ALERT_INTERVAL, so that this script will be called again by
        // supervisor in that amount of time.
        sleep(EMAIL_ALERT_INTERVAL); 
    }
    
    
    /**
     * Fetch any logs that we will need to send email alerts for.
     *
     * @return Array<Log_model>
     */
    private static function get_logs()
    {
        $filter = new LogFilter();
        
        // Will be nice if we stored the ID of the last log we covered previously, but until then
        // use the time period to cover logs that came in the during the time this script was sleeping.
        $filter->set_max_age(EMAIL_ALERT_INTERVAL + 2);
        
        $filter->enable_alert_level(iRAP\Logging\LogLevel::ERROR);
        $filter->enable_alert_level(iRAP\Logging\LogLevel::CRITICAL);
        $filter->enable_alert_level(iRAP\Logging\LogLevel::ALERT);
        $filter->enable_alert_level(iRAP\Logging\LogLevel::EMERGENCY);
        
        return LogTable::getInstance()->load_filter(null, null, $filter);
    }
    
    
    /**
     * Send email alerts for the logs provided.
     *
     * @param Array<Log> $logs
     */
    private static function send_email_alerts($logs)
    {
        global $globals;
        
        if (count($logs) > 0) {
            foreach ($logs as $log)
            {
                /* @var $log Log */
                $message = $log->getMessage();
                
                if (strlen($message) > 100) {
                    $message = substr($message, 0, 100) . "...";
                }
                
                $log_links[] = "<li><a href='" . HOSTNAME . '/logs/id/' . $log->get_id() . "'>" . $message . "</a></li>";
            }
            
            $email_subject = "Alerts - Logger Service";
            
            $email_body = 
                "Please review the following logging issues:<br />" .
                "<UL>" .
                    implode(PHP_EOL, $log_links) .
                "</UL>";
            
            $emailer = SiteSpecific::getEmailer();
            
            foreach ($globals['SUBSCRIBERS'] as $name => $email)
            {
                $emailer->send($name, $email, $email_subject, $email_body, $html_format = true);
                sleep(1); // delay for a second to prevent rate limiting kicking in
            }
        }
    }
}

$alerter = new Alerter();
$alerter->run();