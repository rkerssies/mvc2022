FROM php:8.2-apache
ENV TIMEZONE=Europe/Amsterdam

# some PHP Extensions
RUN apt-get update && docker-php-ext-install mysqli
RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/


# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

ADD vhost-default.conf /usr/local/apache2/conf/httpd.conf
#COPY ./app /var/www/html/app

# Open Ports
EXPOSE 80
EXPOSE 443

#COPY ../app/* ./var/www/html/app/
WORKDIR ../

