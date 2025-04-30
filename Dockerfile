FROM php:8.2-fpm-alpine

# Gerekli PHP eklentilerini ve bağımlılıkları yükle
RUN apk add --no-cache \
    postgresql-dev \
    nginx \
    supervisor \
    freetype \
    libjpeg-turbo \
    libpng \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql gd \
    && apk del freetype-dev libjpeg-turbo-dev libpng-dev

# Nginx ve PHP-FPM için çalışma dizinini ayarla
WORKDIR /var/www/html

# Önce statik dosya dizinlerini oluştur
RUN mkdir -p /var/www/html/css \
    && mkdir -p /var/www/html/images

# Uygulama dosyalarını kopyala
COPY . /var/www/html/

# Nginx yapılandırma dosyasını kopyala
COPY nginx.conf /etc/nginx/http.d/default.conf

# Supervisor yapılandırmasını oluştur
RUN mkdir -p /etc/supervisor.d/
COPY supervisord.conf /etc/supervisor.d/supervisord.ini

# CSS ve resim dosyaları için izinleri ayarla
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && find /var/www/html/css -type f -exec chmod 644 {} \; \
    && find /var/www/html/images -type f -exec chmod 644 {} \;

# CSS dosyalarını izinleri ile birlikte özel olarak kopyala
COPY style.css /var/www/html/style.css
COPY css/ /var/www/html/css/

# Dosya izinlerini ve sahipliğini son bir kez kontrol et
RUN chown -R www-data:www-data /var/www/html/style.css \
    && chown -R www-data:www-data /var/www/html/css \
    && chmod 644 /var/www/html/style.css \
    && find /var/www/html/css -type f -exec chmod 644 {} \;

# Debug için - tüm dosyaları ve izinlerini listele
RUN echo "Listing files and permissions:" \
    && ls -la /var/www/html \
    && ls -la /var/www/html/css

# 80 portunu dışarı aç
EXPOSE 80

# Supervisor ile servisleri başlat
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor.d/supervisord.ini"] 