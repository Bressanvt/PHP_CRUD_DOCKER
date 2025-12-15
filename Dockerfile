# Imagem base com Apache + PHP já empacotados (boa para projetos simples)
FROM php:8.2-apache

# Instala extensões necessárias (PDO MySQL) e habilita mod_rewrite
RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Define o diretório raiz do site como /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Atualiza a configuração do VirtualHost para apontar para /public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copia todo o código para dentro do contêiner
WORKDIR /var/www/html
COPY . /var/www/html

# Porta exposta pelo container
EXPOSE 80

# Comando padrão do Apache (já configurado na imagem base)
CMD ["apache2-foreground"]

