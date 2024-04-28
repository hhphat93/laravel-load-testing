#Download base image ubuntu 20.04
FROM ubuntu:20.04
 
# Update Software repository, install package
RUN apt-get update && apt-get install -y \
    sudo \
    curl \
    rsync \
    iputils-ping \
    openssh-server \
    openssh-client \
    nginx \
    supervisor

# Install php-fpm8.1 from ubuntu repository, remove package in linux after apt-get update
RUN apt-get install -y software-properties-common
RUN add-apt-repository ppa:ondrej/php
RUN apt-get install -y php8.1-fpm \
    php8.1-common \
    php8.1-mysql \
    php8.1-xml \
    php8.1-xmlrpc \
    php8.1-curl \
    php8.1-gd \
    php8.1-imagick \
    php8.1-cli \
    php8.1-dev \
    php8.1-imap \
    php8.1-mbstring \
    php8.1-soap \
    php8.1-zip \
    php8.1-bcmath \
&& rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install NVM, now using node v19.4.0 (npm v9.2.0) - laravel ^9.19
ENV NODE_VERSION=19.4.0
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version

# Add webuser
RUN sudo adduser webuser

# Enviroment
ENV PHP_CONF /etc/php/8.1/fpm/php.ini
ENV PHP_CONF_USER /etc/php/8.1/fpm/pool.d/www.conf
ENV NGINX_CONF /etc/nginx/nginx.conf
ENV SUPERVISOR_CONF /etc/supervisor/supervisord.conf

# Enable php-fpm on nginx virtualhost configuration
RUN sed -i -e 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' ${PHP_CONF} && \
    echo "\ndaemon off;" >> ${NGINX_CONF} && \
    sed -i -e 's/user www-data;/user webuser;/g' ${NGINX_CONF} && \
    sed -i -e 's/user = www-data/user = webuser/g' ${PHP_CONF_USER} && \
    sed -i -e 's/group = www-data/group = webuser/g' ${PHP_CONF_USER} && \
    sed -i -e 's/listen.owner = www-data/listen.owner = webuser/g' ${PHP_CONF_USER} && \
    sed -i -e 's/listen.group = www-data/listengroup = webuser/g' ${PHP_CONF_USER}

COPY supervisord.conf ${SUPERVISOR_CONF}

# Make folder and set permission
RUN mkdir -p /run/php && \
    mkdir -p /var/www/html && \
    chown -R webuser:webuser /var/www/html && \
    chown -R webuser:webuser /run/php
    
# Configure Services and Port
CMD /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf

EXPOSE 9000 9001 9002 443
