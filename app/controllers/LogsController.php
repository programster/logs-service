<?php


class LogsController
{

    public function __construct()
    {
        parent::__construct();

        // only allow granted users access
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('missing session user ID');
        }

        $userId        = $_SESSION['user_id'];
        $query         = "SELECT * FROM `users` WHERE `id`='" . $userId . "'";
        $db            = SiteSpecific::getDb();
        $active_record = $db->query($query);

        if ($active_record->num_rows == 0) {
            $data = array(
                'user_id' => $userId,
                'nonce'   => \iRAP\CoreLibs\StringLib::generateRandomString(24)
            );

            ksort($data);

            $jsonData = json_encode($data);

            $signature         = hash_hmac('sha256', $jsonData, BROKER_SECRET);
            $data['signature'] = $signature;

            $jsonData = json_encode($data);
            $url      = HOSTNAME . '/grant_access?data=' . base64_encode($jsonData);

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


    public function index()
    {
        $this->page(1);
    }


    /**
     * User has specified seeing a certain page.
     */
    public function page($page_id)
    {
        $this->load->model('log_model');

        if (isset($_POST['filter_form'])) {
            $this->handle_filter_submission();
        }

        $log_filter = LogFilter::load();

        $offset = ($page_id - 1) * RESULTS_PER_PAGE;
        $limit  = RESULTS_PER_PAGE;

        $logs = Log_model::load_filter($offset, $limit, $log_filter);


        $url_generator = function ($page_counter) {
            return '/logs/page/' . $page_counter;
        };

        $maximum         = Log_model::get_num_logs($log_filter);
        $pagination_data = array(
            'maximum'       => $maximum,
            'url_generator' => $url_generator,
            'current_page'  => $page_id
        );

        $logs_view_data = array(
            'logs' => $logs
        );

        $search_data = array(
            'log_filter' => $log_filter
        );


        $header_view     = SiteSpecific::get_view(__DIR__ . '/../views/header.php', array('title' => 'Logs'));
        $search_view     = SiteSpecific::get_view(__DIR__ . '/../views/search_logs.php', $search_data);
        $pagination_view = $maximum ? SiteSpecific::get_view(__DIR__ . '/../views/pagination.php', $pagination_data) : '';
        $logs_view       = SiteSpecific::get_view(__DIR__ . '/../views/logs.php', $logs_view_data);
        $footer_view     = SiteSpecific::get_view(__DIR__ . '/../views/footer.php');

        print
            $header_view .
            $search_view .
            $pagination_view .
            $logs_view .
            $pagination_view .
            $footer_view;
    }


    /**
     * User has requested to see a specific log by id.
     */
    public function id($id)
    {
        $this->load->model('log_model');
        $logs = Log_model::load_id($id);

        if (count($logs) == 1) {
            $log = $logs[0];
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                print SiteSpecific::get_view(__DIR__ . '/../views/single_log.php', array('log' => $log));
            }
            else
            {
                iRAP\CoreLibs\Core::redirectUser("/logs#{$id}");
            }
        }
        else
        {
            print "No such log exists!";
        }
    }


    /**
     * Handle the submission of the filter form on the logs page.
     * This form allows the user to filter the logs that will appear.
     */
    private function handle_filter_submission()
    {
        $filter = array();

        $filter_object = new LogFilter();

        if (!empty($_POST['search_text'])) {
            $filter_object->setSearchText($_POST['search_text']);
        }

        if (!empty($_POST['max_age_amount'])) {
            $max_age_amount = intval($_POST['max_age_amount']);
            $max_age_units  = $_POST['max_age_units'];

            // convert the amount to minutes which we use in the rest of the system.
            switch ($max_age_units)
            {
            case 'minutes':
                {
                    // do nothing, already in the right quantity.
}
                break;

            case 'hours':
            {
                $max_age_amount = $max_age_amount * 60;
}
            break;

            case 'days':
            {
                $max_age_amount = $max_age_amount * 60 * 24;
}
            break;

            default:
            {
                throw new Exception("Unrecognized min age unit: " . $max_age_units);
}
            }

            $filter_object->set_max_age($max_age_amount);
        }

        if (!empty($_POST['min_age_amount'])) {
            $min_age_amount = intval($_POST['min_age_amount']);
            $min_age_units  = $_POST['min_age_units'];

            // convert the amount to minutes which we use in the rest of the system.
            switch ($min_age_units)
            {
            case 'minutes':
                {
                    // do nothing, already in the right quantity.
}
                break;

            case 'hours':
                {
                    $min_age_amount = $min_age_amount * 60;
}
                break;

            case 'days':
                {
                    $min_age_amount = $min_age_amount * 60 * 24;
}
                break;

            default:
                {
                    throw new Exception("Unrecognized min age unit: " . $max_age_units);
}
            }

            $filter_object->set_min_age($min_age_amount);
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
            if ($_POST['level_' . $alert_level_name] === "true") {
                $filter_object->enable_alert_level($alert_level);
            }
        }

        // save the filter to the session.
        $filter_object->save();
    }


}
