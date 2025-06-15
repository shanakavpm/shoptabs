# Use official PHP image with FPM
FROM php:8.1-fpm

# Install Nginx, OpenSSL, and PDO MySQL extension
RUN apt-get update && \
    apt-get install -y nginx openssl && \
    docker-php-ext-install pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

# Set up working directory
WORKDIR /var/www/html

# Copy app source
COPY . /var/www/html

# Generate self-signed SSL certificate for localhost
RUN mkdir -p /etc/nginx/ssl && \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
      -keyout /etc/nginx/ssl/localhost.key \
      -out /etc/nginx/ssl/localhost.crt \
      -subj "/CN=localhost"

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Expose HTTPS port
EXPOSE 443

# Install Supervisor
RUN apt-get update && apt-get install -y supervisor

# Copy supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Start Supervisor (which manages php-fpm and nginx)
CMD ["/usr/bin/supervisord", "-n"]
