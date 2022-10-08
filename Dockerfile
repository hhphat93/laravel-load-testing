#Download base image ubuntu 20.04
FROM ubuntu:20.04
 
# Update Software repository
RUN apt-get update

# Install sudo, add user
RUN apt-get install -y sudo
RUN sudo adduser webuser

# Install nano, curl, ping
RUN apt-get install nano
RUN apt-get install -y curl
RUN apt-get install -y iputils-ping

# Install SSH client, server
RUN apt-get install -y openssh-server openssh-client

# Install rsync
RUN apt-get install -y rsync

# Install php-fpm from ubuntu repository
RUN apt-get install -y software-properties-common
RUN add-apt-repository ppa:ondrej/php
RUN apt-get install php8.1-fpm php8.1-common php8.1-mysql php8.1-xml php8.1-xmlrpc php8.1-curl php8.1-gd php8.1-imagick php8.1-cli php8.1-dev php8.1-imap php8.1-mbstring php8.1-soap php8.1-zip php8.1-bcmath -y

# Enviroment
ENV php_conf /etc/php/8.1/fpm/php.ini
ENV nginx_conf /etc/nginx/nginx.conf
ENV supervisor_conf /etc/supervisor/supervisord.conf

# Install nginx, supervisord and remove package in linux after apt-get update
RUN apt-get install -y nginx supervisor && \
    rm -rf /var/lib/apt/lists/*

# Enable php-fpm on nginx virtualhost configuration
RUN sed -i -e 's/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/g' ${php_conf} && \
    echo "\ndaemon off;" >> ${nginx_conf}

COPY supervisord.conf ${supervisor_conf}
 
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install NVM
ENV NODE_VERSION=16.17.1
RUN apt install -y curl
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
RUN node --version
RUN npm --version
 
RUN mkdir -p /run/php && \
    mkdir -p /var/www/html && \
    chown -R www-data:www-data /var/www/html && \
    chown -R www-data:www-data /run/php
    
# Configure Services and Port
COPY start.sh /start.sh

RUN chmod +x start.sh

CMD ./start.sh

EXPOSE 80 443
