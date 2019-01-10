<?php
global $globals;

$globals = array();


# Specify your databases here for codeigniter to use.
$default_database = array(
    'hostname' => '',
    'username' => '',
    'password' => '',
    'database' => ''
);

$globals['databases'] = array(
    'default' => $default_database
);

# Specify who should be alerted to important logs
$globals['SUBSCRIBERS'] = array(
    'FirstName LastName'     => 'name@org.org'
);

# Define the email address of the accoun that will be responsible for granting access
# to this logging site to users. This address will get emails with links to click on
# to grant access.
define('ADMIN_EMAIL', "");

# Specify the URL of this service, which will be used for sending links to logs
define('HOSTNAME', "");

# Specify how many results should show up on a page for pagination
define('RESULTS_PER_PAGE', 20);

# Define often we should check for and send out email alerts in seconds
define('EMAIL_ALERT_INTERVAL', 60);

# specify the email address that should be used for sending emails from AWS.
define('SMTP_HOST', '');
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM', '');

# Specify settings for connecting to RabbitMQ.
define('RABBITMQ_HOST', '');
define('RABBITMQ_USER', '');
define('RABBITMQ_PASSWORD', '');
define('RABBITMQ_LOG_QUEUE', '');

# Specify the SSO details
define('USE_SSO', FALSE);
define('SSO_SITE_HOSTNAME', '');
define('BROKER_ID', '2');
define('BROKER_SECRET', '');

# Specify the email and password that would need to be used to log in if USE_SSO is set to false.
define('HARDCODED_LOGIN_EMAIL', '');
define('HARDCODED_LOGIN_PASSWORD', '');