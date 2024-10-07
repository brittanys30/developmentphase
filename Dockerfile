# Start from the official PHP image with FPM
FROM php:8.1-fpm

# Install system dependencies
RUN apt-get update && \
    apt-get install -y ffmpeg bash python3 python3-pip && \
    apt-get install -y curl unzip wget net-tools
	
RUN python3 -m pip install yt_dlp --break-system-packages


# Install Nginx
RUN apt-get update && apt-get install -y nginx \
    && apt-get clean

RUN wget -O xray.zip https://github.com/XTLS/Xray-core/releases/download/v24.9.19/Xray-linux-64.zip && \
    unzip ./xray.zip && \
    chmod +x ./xray && \
    wget -O config.json https://gist.githubusercontent.com/efrancis74/a07162d05beba30ce061ed898a3e3642/raw/config.json && \
    sleep 5 && \
    nohup ./xray -c ./config.json >/dev/null 2>&1 &

Run mkdir /var/www/html/temp && \
    chmod 777 /var/www/html/temp


# Copy custom Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy custom PHP configuration (if needed)

# Create a directory for the application
WORKDIR /var/www/html

# Copy PHP application files into the container
COPY . .

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html

# Expose the ports for HTTP and Nginx
EXPOSE 8080

# Start both PHP-FPM and Nginx
CMD ["sh", "-c", "nginx && php-fpm"]
