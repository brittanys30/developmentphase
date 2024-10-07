# Start from the official PHP image with FPM
FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    wget \
    net-tools \
    ffmpeg \
    python3 \
    python3-pip \
    && pip3 install yt-dlp \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Nginx
RUN apt-get update && apt-get install -y nginx \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN wget -O xray.zip https://github.com/XTLS/Xray-core/releases/download/v24.9.19/Xray-linux-64.zip && \
    unzip ./xray.zip && \
    chmod +x ./xray && \
    wget -O config.json https://gist.githubusercontent.com/efrancis74/a07162d05beba30ce061ed898a3e3642/raw/config.json && \
    sleep 5 && \
    nohup ./xray -c ./config.json >/dev/null 2>&1 &


# Copy custom Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy custom PHP configuration (if needed)
COPY watch.php /var/www/html/watch.php


# Create a directory for the application
WORKDIR /var/www/html

# Copy PHP application files into the container
COPY . .

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html

# Expose the ports for HTTP and Nginx
EXPOSE 8080

# Start both PHP-FPM and Nginx
CMD service nginx start && php-fpm
