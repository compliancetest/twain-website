FROM cloudontap/laravel:1.0.2-1

RUN apt-get update && \
    apt-get -y upgrade && \
    apt-get -y install cron && \
    apt-get clean

ADD apache2.conf /etc/apache2/apache2.conf
ADD 000-default.conf /etc/apache2/sites-available/000-default.conf
ADD . /var/www/html

EXPOSE 80
ADD start.sh /start.sh
ADD cleanupcron /etc/cron.d/cleanupcron
RUN chmod 0755 /start.sh
CMD ["bash", "start.sh"]