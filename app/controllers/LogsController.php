<?php


class LogsController extends AbstractSlimController
{
    /**
     * Register routes with slim routing.
     * @param \Slim\App $app
     */
    public static function registerWithApp(\Slim\App $app)
    {
        # create a new log
        $app->post('/logs', function (\Slim\Http\Request $request, Slim\Http\Response $response, $args) {
            $logsController = new LogsController($request, $response, $args);
            return $logsController->handlePostedLog();
        });
        
        $app->get('/', function (\Slim\Http\Request $request, Slim\Http\Response $response, $args) {
            return $response->withRedirect("/logs/page/1");
        });
        
        # display the list of logs
        $app->get('/logs', function(\Slim\Http\Request $request, Slim\Http\Response $response, $args) {
            return $response->withRedirect("/logs/page/1");
        });
        
        $app->get('/logs/page/{id:[0-9]+}', function (\Slim\Http\Request $request, Slim\Http\Response $response, $args) {
            $logsController = new LogsController($request, $response, $args);
            return $logsController->showLogs($args['id']);
        });
        
        # get a specific log
        $app->get('/logs/{id:[0-9]+}', function (\Slim\Http\Request $request, Slim\Http\Response $response, $args) {
            $logsController = new LogsController($request, $response, $args);
            return $logsController->showLog($args['id']);
        });
        
        return $app;
    }
    
    
    /**
     * Handle a POSTed log.
     * @return \Slim\Http\Response
     * @throws Exception
     */
    private function handlePostedLog() : \Slim\Http\Response
    {
        try
        {
            $requiredPostFields = array('message', 'context', 'priority', 'when');
            $allPostPutVars = $this->getRequest()->getParsedBody();

            foreach ($requiredPostFields as $requiredPostField)
            {
                if (!isset($allPostPutVars[$requiredPostField]))
                {
                    throw new Exception("Missing required POST field: " . $requiredPostField, 400);
                }
            }
            
            $message = $allPostPutVars['message'];
            $context = json_encode(json_decode($allPostPutVars['context']));
            $priority = intval($allPostPutVars['priority']);
            $when = intval($allPostPutVars['when']);
            $log = Log::createNew($message, $context, $priority, $when);
            $response = SiteSpecific::createJsonResponse($this->m_response, ["message" => "Log created"], 200);
        } 
        catch (Exception $ex) 
        {
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
            if ($ex->getCode() == 0)
            {
                $errorCode = 500;
                $message = "There was an unexpected exception.";
            }
            
            $response = SiteSpecific::createJsonResponse($this->m_response, ["error_message" => $message], $code);
        }
        
        return $response;
    }
    
    
    /**
     * User has specified seeing a certain page.
     */
    private function showLogs(int $pageNumber)
    {
        if (isset($_POST['filter_form'])) 
        {
            //$this->handleFilterSubmission();
        }
        
        $logFilter = LogFilter::load();
        $resultsPerPage = $_ENV['RESULTS_PER_PAGE'];
        $offset = ($pageNumber - 1) * $resultsPerPage;
        $limit = $resultsPerPage;
        
        /* @var $logTable LogTable */
        $logTable = LogTable::getInstance();
        $logCollection = $logTable->loadByFilter($offset, $limit, $logFilter);
        //$logsArray = $logTable->loadRange($offset, $resultsPerPage);
        //$logCollection = new LogCollection(...$logsArray);
        //$maximum = Log_model::getNumLogs($logFilter);
        //$paginationView = $maximum ? SiteSpecific::getView(__DIR__ . '/../views/pagination.php', $pagination_data) : '';
        
        $body = new ViewLogsTable($logCollection);
        $templateView = new ViewTemplate("Logs ({$pageNumber})", (string)$body);
        $response = SiteSpecific::createHtmlResponse($this->m_response, $templateView);
    }


    /**
     * Handle a user request to view a specific log by ID
     */
    private function showLog(int $id) : \Slim\Http\Response
    {
        try
        {
            $log = LogTable::getInstance()->load($id);
            $response = SiteSpecific::createHtmlResponse($this->m_response, new ViewSingleLog($log));
        } 
        catch (\iRAP\MysqlObjects\NoSuchIdException $ex) 
        {
            $response = SiteSpecific::createHtmlResponse($this->m_response, "No such log exists!");
        }
        
        return $response;
    }


    /**
     * Handle the submission of the filter form on the logs page.
     * This form allows the user to filter the logs that will appear.
     */
    private function handleFilterSubmission()
    {
        $filter = array();
        $filterObject = new LogFilter();

        if (!empty($_POST['search_text'])) 
        {
            $filterObject->setSearchText($_POST['search_text']);
        }

        if (!empty($_POST['max_age_amount'])) 
        {
            $maxAgeAmount = intval($_POST['max_age_amount']);
            $maxAgeUnits  = $_POST['max_age_units'];

            // convert the amount to minutes which we use in the rest of the system.
            switch ($maxAgeUnits)
            {
                case 'minutes':
                {
                    // do nothing, already in the right quantity.
                }
                break;

                case 'hours':
                {
                    $maxAgeAmount = $maxAgeAmount * 60;
                }
                break;

                case 'days':
                {
                    $maxAgeAmount = $maxAgeAmount * 60 * 24;
                }
                break;

                default:
                {
                    throw new Exception("Unrecognized min age unit: " . $maxAgeUnits);
                }
            }

            $filterObject->set_max_age($maxAgeAmount);
        }

        if (!empty($_POST['min_age_amount'])) 
        {
            $minAgeAmount = intval($_POST['min_age_amount']);
            $minAgeUnits  = $_POST['min_age_units'];

            // convert the amount to minutes which we use in the rest of the system.
            switch ($minAgeUnits)
            {
                case 'minutes':
                {
                    // do nothing, already in the right quantity.
                }
                break;

                case 'hours':
                {
                    $minAgeAmount = $minAgeAmount * 60;
                }
                break;

                case 'days':
                {
                    $minAgeAmount = $minAgeAmount * 60 * 24;
                }
                break;

                default:
                {
                        throw new Exception("Unrecognized min age unit: " . $maxAgeUnits);
                }
            }
            
            $filterObject->set_min_age($minAgeAmount);
        }

        // order is very important as it correlates to the alert level 0-6
        $alert_levels = array(
            "debug",
            "info",
            "notice",
            "warning",
            "error",
            "critical",
            "alert"
        );

        $filter['alert_levels'] = array();

        foreach ($alert_levels as $alert_level => $alert_level_name)
        {
            if ($_POST['level_' . $alert_level_name] === "true") 
            {
                $filterObject->enable_alert_level($alert_level);
            }
        }

        // save the filter to the session.
        $filterObject->save();
    }
}
