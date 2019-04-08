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

# Ensure the apache service is not running as supervisord will manage it.
service apache2 stop

# run migrations
# it is important this runs before supervisord launches background processes.
/usr/bin/php /var/www/site/scripts/migrate.sh

# sync the clock
ntpdate ntp.ubuntu.com

# start the alerter script
/usr/bin/php /var/www/logger_frontend/project/scripts/Alerter.php

# Star the cron service
cron

# Start supervisord to manage all processes and tie up the frontend
/usr/bin/supervisord
