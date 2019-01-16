#!/bin/bash

a2enmod ssl
service apache2 restart
mkdir /etc/apache2/ssl 

if [ ! -f /var/www/logger_frontend/settings/ssl/apache.crt ]; then
    echo "generating ssl keys since they weren't provided."
    openssl \
    req -x509 \
    -nodes \
    -days 365 \
    -newkey rsa:4096 \
    -keyout /etc/apache2/ssl/apache.key \
    -out /etc/apache2/ssl/apache.crt \
    -subj "/C=GB/ST=London/L=London/O=Global Security/OU=IT Department/CN=common.name"
else
    mv /var/www/logger_frontend/settings/ssl/apache.key /etc/apache2/ssl/apache.key
    mv /var/www/logger_frontend/settings/ssl/apache.crt /etc/apache2/ssl/apache.crt
    mv /var/www/logger_frontend/settings/ssl/sub.class1.server.ca.pem /etc/apache2/ssl/sub.class1.server.ca.pem
fi

SCRIPT=$(readlink -f "$0")
SCRIPTPATH=$(dirname "$SCRIPT") 
cd $SCRIPTPATH

mv apache-ssl-config.conf /etc/apache2/sites-available/default-ssl.conf

a2ensite default-ssl
service apache2 reload
