<?php
// setup_development_environment.php

// Script para configurar o ambiente de desenvolvimento

echo "Configurando ambiente de desenvolvimento...\n\n";

// 1. Criar diretórios necessários
echo "1. Criando diretórios necessários...\n";

$directories = [
    'uploads',
    'api/logs',
    'api/cache'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✓ Diretório '$dir' criado\n";
        } else {
            echo "✗ Erro ao criar diretório '$dir'\n";
        }
    } else {
        echo "✓ Diretório '$dir' já existe\n";
    }
}

echo "\n";

// 2. Configurar permissões
echo "2. Configurando permissões...\n";

foreach ($directories as $dir) {
    if (is_dir($dir)) {
        if (chmod($dir, 0755)) {
            echo "✓ Permissões configuradas para '$dir'\n";
        } else {
            echo "✗ Erro ao configurar permissões para '$dir'\n";
        }
    }
}

echo "\n";

// 3. Criar arquivo de configuração do banco de dados se não existir
echo "3. Criando configuração do banco de dados...\n";

$dbConfigFile = 'api/config/database.php';

if (!file_exists($dbConfigFile)) {
    $dbConfigContent = "<?php
// Configuração do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'cccrj_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Função para conectar ao banco de dados
function connectDatabase() {
    try {
        \$pdo = new PDO(
            \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=utf8\",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        return \$pdo;
    } catch (PDOException \$e) {
        error_log(\"Erro na conexão com o banco de dados: \" . \$e->getMessage());
        throw new Exception(\"Não foi possível conectar ao banco de dados.\");
    }
}
?>
";
    
    if (file_put_contents($dbConfigFile, $dbConfigContent)) {
        echo "✓ Arquivo de configuração do banco de dados criado\n";
    } else {
        echo "✗ Erro ao criar arquivo de configuração do banco de dados\n";
    }
} else {
    echo "✓ Arquivo de configuração do banco de dados já existe\n";
}

echo "\n";

// 4. Criar arquivo .htaccess para URL amigáveis (se necessário)
echo "4. Criando arquivos de configuração do servidor...\n";

$htaccessContent = "RewriteEngine On

# Redirecionar URLs da API
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api/$1 [QSA,L]

# Redirecionar para index.html para SPA (se necessário)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.html [QSA,L]

# Configurações de segurança
<IfModule mod_headers.c>
    Header set X-Content-Type-Options nosniff
    Header set X-Frame-Options DENY
    Header set X-XSS-Protection \"1; mode=block\"
</IfModule>

# Arquivos de cache
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css \"access plus 1 month\"
    ExpiresByType text/javascript \"access plus 1 month\"
    ExpiresByType application/javascript \"access plus 1 month\"
    ExpiresByType image/png \"access plus 1 year\"
    ExpiresByType image/jpg \"access plus 1 year\"
    ExpiresByType image/jpeg \"access plus 1 year\"
    ExpiresByType image/gif \"access plus 1 year\"
    ExpiresByType image/svg+xml \"access plus 1 year\"
</IfModule>
";

if (file_put_contents('.htaccess', $htaccessContent)) {
    echo "✓ Arquivo .htaccess criado\n";
} else {
    echo "✗ Erro ao criar arquivo .htaccess\n";
}

echo "\n";

// 5. Criar arquivo robots.txt
echo "5. Criando arquivo robots.txt...\n";

$robotsContent = "User-agent: *
Disallow: /api/
Disallow: /admin/
Disallow: /uploads/

Sitemap: http://www.cccrj.com.br/sitemap.xml
";

if (file_put_contents('robots.txt', $robotsContent)) {
    echo "✓ Arquivo robots.txt criado\n";
} else {
    echo "✗ Erro ao criar arquivo robots.txt\n";
}

echo "\n";

// 6. Criar arquivo humans.txt
echo "6. Criando arquivo humans.txt...\n";

$humansContent = "/* TEAM */
Developer: CCCRJ Development Team
Contact: riocafe@cccrj.com.br
Twitter: @cccrj
Location: Rio de Janeiro, Brazil

/* SITE */
Last update: " . date('Y-m-d') . "
Language: Portuguese (Brazil)
Doctype: HTML5
IDE: Visual Studio Code
Components: Tailwind CSS, Alpine.js
Software: PHP, MySQL, Apache
";

if (file_put_contents('humans.txt', $humansContent)) {
    echo "✓ Arquivo humans.txt criado\n";
} else {
    echo "✗ Erro ao criar arquivo humans.txt\n";
}

echo "\n";

// 7. Criar arquivo de configuração do ambiente
echo "7. Criando arquivo de configuração do ambiente...\n";

$envContent = "APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/cccrj

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cccrj_db
DB_USERNAME=root
DB_PASSWORD=

LOG_CHANNEL=file
LOG_LEVEL=debug

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
";

if (file_put_contents('.env', $envContent)) {
    echo "✓ Arquivo .env criado\n";
} else {
    echo "✗ Erro ao criar arquivo .env\n";
}

echo "\n";

echo "✅ Configuração do ambiente de desenvolvimento concluída!\n";
echo "\nPróximos passos:\n";
echo "1. Execute o script create_database_tables.php para criar o banco de dados\n";
echo "2. Execute o script populate_db.php para popular com dados de exemplo\n";
echo "3. Execute o script populate_from_scraped_content.php para migrar conteúdo raspado\n";
echo "4. Inicie o Apache e MySQL no XAMPP\n";
echo "5. Acesse http://localhost/cccrj no navegador\n";
?>