# Due to layout of this project, the dockerfile will be moved up two directories and run during
# the build. Thus when performing any ADD commands, remember that this is "where you are"
# The layout is this way so that the settings file sits one level above the project (trunk) so that
# each dev can have their own settings and they do not get merged into the trunk.

FROM ubuntu:16.04
MAINTAINER mainainer@mainainer.org

RUN apt-get update
RUN apt-get dist-upgrade -y

# Install all necessary packages
RUN apt-get install -y apache2 supervisor php7.0  curl libapache2-mod-php vim cron \
php7.0-cli php7.0-mysql php7.0-mbstring php7.0-bcmath php7.0-curl

# Manually set the apache environment variables in order to get apache to work immediately.
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

# It appears that the new apache requires these env vars as well
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2/apache2.pid

# Set up url rewriting so that "routing" works
RUN a2enmod rewrite


#-------------------------- PHP ini Setttings-------------------------------------------------------

# Increase php's max allowed memory size for the script executing the computes
RUN sed -i 's;memory_limit = .*;memory_limit = -1;' /etc/php/7.0/cli/php.ini
RUN sed -i 's;memory_limit = .*;memory_limit = -1;' /etc/php/7.0/apache2/php.ini

# Change apache php to display errors
RUN sed -i 's;display_errors = Off;display_errors = On;' /etc/php/7.0/apache2/php.ini

# Change php cli to display errors
RUN sed -i 's;display_errors = Off;display_errors = On;' /etc/php/7.0/cli/php.ini

# Change apache php to allow larger uploads
RUN sed -i 's;post_max_size = .*;post_max_size = 1000M;' /etc/php/7.0/apache2/php.ini
RUN sed -i 's;upload_max_filesize = .*;upload_max_filesize = 1000M;' /etc/php/7.0/apache2/php.ini

# Allow the cli scripts to have an infinite execution time (some test datasets are HUGE!)
RUN sed -i 's;max_execution_time = .*;max_execution_time = -1;' /etc/php/7.0/apache2/php.ini

# Increase session lifetime
RUN sed -i 's;session.gc_maxlifetime = .*;session.gc_maxlifetime = 43200;' /etc/php/7.0/apache2/php.ini

#---------------------------------------------------------------------------------------------------


# expose port 80 and 443 for the web requests
# 80 should redirect to ssl on 443
EXPOSE 80
EXPOSE 443

#------------------------- Set up Apache and site file permissions----------------------------------

# Add our website's files to the default apache directory (/var/www)
COPY settings /var/www/logger_frontend/settings
COPY project /var/www/logger_frontend/project

# Make root and www-data owner/group of the www area for permissions (uploads writeable by apache)
RUN chown root:www-data -R /var/www/

# Update our apache sites available with the config we created
COPY project/docker/apache-config.conf /etc/apache2/sites-enabled/000-default.conf

# Add our supervisor config to the container
COPY project/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf


# Configure apache to use a newly generate ssl key
# or generate one if there is no key in the files.
# This MUST go after we have added the project files to the container.
RUN /bin/bash /var/www/logger_frontend/project/docker/create-ssl-key.sh

# Use the crontab file.
# The crontab file was already added when we added "project"
RUN crontab /var/www/logger_frontend/project/docker/crons.conf


# Execute the container's startup script which will start many processes/services
# The startup file was already added when we added "project"
CMD ["/bin/bash", "/var/www/logger_frontend/project/docker/startup.sh"]
