#!/bin/bash

# Please do not manually call this file!
# This script is run by the docker container when it is "run"

# Bash guard to ensure running bash.
if ! [ -n "$BASH_VERSION" ];then
    echo "this is not bash, calling self with bash....";
    SCRIPT=$(readlink -f "$0")
    /bin/bash $SCRIPT
    exit;
fi

SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT") 

echo "starting apache server...."
# Run the apache process in the background
/usr/sbin/apache2 -D APACHE_PROCESS &

# run migrations
# it is important this runs before supervisord launches background processes.
/usr/bin/php $SCRIPTPATH/../index.php migrate

# sync the clock
ntpdate ntp.ubuntu.com

# start the alerter script
/usr/bin/php /var/www/logger_frontend/project/scripts/alerter/main.php

# Star the cron service
cron

# Stop apache so that supervisor can start and manage it.
# leaving apache running will result in supervisor not managing the process.
service apache2 stop

# Start supervisord to manage all processes and tie up the frontend
/usr/bin/supervisord
