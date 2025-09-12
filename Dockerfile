FROM php:8.1-apache

# Limpar diretório web padrão  
RUN rm -rf /var/www/html/*

# Copiar TODOS os arquivos do projeto
COPY . /var/www/html/

# Debug: Listar arquivos copiados
RUN echo "=== Arquivos copiados ===" && ls -la /var/www/html/

# Definir permissões
RUN chmod -R 755 /var/www/html

# Expor porta
EXPOSE 80
