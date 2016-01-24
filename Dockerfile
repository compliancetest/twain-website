FROM debian:jessie
RUN apt-get update && \
    apt-get -y upgrade && \
    apt-get -y install apache2 libapache2-mod-php5 php5 php5-common php5-gd php5-mcrypt php5-xsl php5-xmlrpc php5-xdebug php5-tidy php5-sqlite php5-apcu php5-mysqlnd php5-imagick curl && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    apt-get purge -y --auto-remove curl && \
    apt-get clean

RUN rm -rf /var/www/html
ADD . /var/www/html
EXPOSE 80
ADD start.sh /start.sh
RUN chmod 0755 /start.sh
CMD ["bash", "start.sh"]
