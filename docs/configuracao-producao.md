# Configuração para Produção

## 1. Configurar .env
```bash
# Copiar arquivo de exemplo
cp .env.example .env

# Editar .env com valores de produção
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
DB_HOST=localhost
DB_USER=cccrj_user
DB_PASS=secure_password
```

## 2. Configurar Permissões (Linux)
```bash
# Criar estrutura
mkdir -p pdf/reports pdf/boletins temp cache

# Configurar permissões
chmod 755 pdf reports boletins temp cache
chmod 600 .env

# Proprietário do Apache
chown -R www-data:www-data pdf/ temp/ cache/
```

## 3. Configurar Web Server (Apache)
```apache
<Directory "/var/www/cccrj">
    AllowOverride All
    Require all granted
</Directory>

# Regras de reescrita para API
RewriteEngine On
RewriteRule ^api/(.*)$ api/$1 [QSA,L]
```

## 4. Instalar Dependências PHP
```bash
# Para JWT (opcional - pode usar implementação nativa)
composer require firebase/php-jwt

# Extensões necessárias (geralmente já incluídas)
php -m | grep -E "(fileinfo|openssl|ftp)"
```

## 5. Configurar Logs
```bash
mkdir logs
chmod 755 logs
```

## 6. Testar Instalação
```bash
# Testar APIs
curl http://localhost/api/reports/list.php

# Testar upload
curl -X POST http://localhost/api/upload_report.php \
  -F "file=@test.pdf" \
  -F "title=Teste"
```
