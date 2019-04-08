# Due to layout of this project, the dockerfile will be moved up two directories and run during
# the build. Thus when performing any ADD commands, remember that this is "where you are"
# The layout is this way so that the settings file sits one level above the project (trunk) so that
# each dev can have their own settings and they do not get merged into the trunk.

FROM ubuntu:18.04
MAINTAINER sdpagent@gmail.com


RUN apt-get update
RUN apt-get dist-upgrade -y


# Install all necessary packages
RUN apt-get install -y apache2 supervisor php7.2  curl libapache2-mod-php vim cron \
php7.2-cli php7.2-mysql php7.2-mbstring php7.2-bcmath php7.2-curl


# Manually set the apache environment variables in order to get apache to work immediately.
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2


# It appears that the new apache requires these env vars as well
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2/apache2.pid


# Set up url rewriting so that routing works
RUN a2enmod rewrite


#-------------------------- PHP ini Setttings-------------------------------------------------------

# Increase php's max allowed memory
RUN sed -i 's;memory_limit = .*;memory_limit = -1;' /etc/php/7.0/cli/php.ini
RUN sed -i 's;memory_limit = .*;memory_limit = -1;' /etc/php/7.0/apache2/php.ini

# Change apache php to display errors
# The application layer will decide whether to show errrors or not base on ENVIRONMENT variable
RUN sed -i 's;display_errors = Off;display_errors = On;' /etc/php/7.0/apache2/php.ini

# Change php cli to display errors
RUN sed -i 's;display_errors = Off;display_errors = On;' /etc/php/7.0/cli/php.ini

# Change apache php to allow larger uploads
RUN sed -i 's;post_max_size = .*;post_max_size = 1000M;' /etc/php/7.0/apache2/php.ini

# Allow the cli scripts to have an infinite execution time
RUN sed -i 's;max_execution_time = .*;max_execution_time = -1;' /etc/php/7.0/apache2/php.ini

# Increase session lifetime
RUN sed -i 's;session.gc_maxlifetime = .*;session.gc_maxlifetime = 43200;' /etc/php/7.0/apache2/php.ini

#---------------------------------------------------------------------------------------------------


# expose port 80 for web requests.
# Assuming using a reverse proxy/load-balancer for SSL.
EXPOSE 80

#------------------------- Set up Apache and site file permissions----------------------------------

# Add our website's files to the default apache directory (/var/www)
COPY --chown=root:www-data app /var/www/site

# Remove the .env file. This should not be part of the image and be added through a volume by the user.
RUN rm /var/www/site/.env

# need to ensure apache can get to our site.
RUN chown root:www-data /var/www/

# Update our apache sites available with the config we created
COPY project/docker/apache-config.conf /etc/apache2/sites-enabled/000-default.conf

# Add our supervisor config to the container
COPY project/docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Use the crontab file.
# The crontab file was already added when we added "project"
ADD docker/crons.conf /root/crons.conf
RUN crontab /root/crons.conf


# Execute the container's startup script which will start many processes/services
# The startup file was already added when we added "project"
COPY docker/startup.sh /root/startup.sh
CMD ["/bin/bash", "/root/startup.sh"]
