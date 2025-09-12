# Use uma imagem PHP básica 
FROM php:8.1-apache

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar arquivos PHP específicos
COPY *.php /var/www/html/
COPY *.md /var/www/html/
COPY *.json /var/www/html/

# Criar diretórios necessários
RUN mkdir -p /var/www/html/logs /var/www/html/temp

# Definir permissões
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Configurar Apache para index automático
RUN echo "DirectoryIndex index.php install.php" >> /etc/apache2/apache2.conf

# Expor porta 80
EXPOSE 80
