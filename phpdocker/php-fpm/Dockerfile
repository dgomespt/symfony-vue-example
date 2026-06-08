FROM phpdockerio/php:8.5-fpm
WORKDIR "/application"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        git \
        php8.5-redis \
        php8.5-gd \ 
        php8.5-intl \ 
        php8.5-pcov \
        php8.5-redis \ 
        php8.5-xdebug \ 
        php8.3-yaml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*