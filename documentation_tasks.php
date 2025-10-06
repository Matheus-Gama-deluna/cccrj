<?php
// documentation_tasks.php

// Script para executar todas as tarefas de documentação

echo "Executando tarefas de documentação...\n\n";

// 1. Gerar documentação técnica completa
echo "1. Gerando documentação técnica completa...\n";

$documentationTasks = [
    'generate_documentation.php' => 'Documentação técnica',
    'generate_system_report.php' => 'Relatório do sistema',
    'generate_statistics.php' => 'Estatísticas do conteúdo',
    'generate_sitemap.php' => 'Mapa do site',
    'generate_rss_feed.php' => 'Feeds RSS'
];

foreach ($documentationTasks as $script => $description) {
    if (file_exists($script)) {
        echo "✓ Executando $description...\n";
        // Executar o script em segundo plano
        shell_exec("php $script > docs/" . str_replace('.php', '.log', $script) . " 2>&1 &");
    } else {
        echo "✗ $description: script não encontrado ($script)\n";
    }
}

echo "✓ Tarefas de documentação iniciadas\n\n";

// 2. Verificar documentação existente
echo "2. Verificando documentação existente...\n";

$docsDir = 'docs';
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0755, true);
}

$existingDocs = [
    'technical_documentation.md' => 'Documentação Técnica',
    'api_documentation.md' => 'Documentação da API',
    'user_manual.md' => 'Manual do Usuário',
    'installation_guide.md' => 'Guia de Instalação',
    'development_guide.md' => 'Guia de Desenvolvimento'
];

foreach ($existingDocs as $file => $description) {
    if (file_exists("$docsDir/$file")) {
        $modified = date('d/m/Y H:i:s', filemtime("$docsDir/$file"));
        echo "✓ $description: atualizado em $modified\n";
    } else {
        echo "? $description: documento não encontrado\n";
    }
}

echo "\n";

// 3. Gerar documentação de componentes
echo "3. Gerando documentação de componentes...\n";

$componentsDir = 'assets/js/components';
if (is_dir($componentsDir)) {
    $components = glob("$componentsDir/*.js");
    
    $componentsDoc = "# Documentação dos Componentes\n";
    $componentsDoc .= "=============================\n\n";
    
    $componentsDoc .= "## Visão Geral\n";
    $componentsDoc .= "Esta documentação descreve todos os componentes JavaScript utilizados no sistema.\n\n";
    
    $componentsDoc .= "## Componentes Disponíveis\n\n";
    
    foreach ($components as $component) {
        $componentName = basename($component, '.js');
        $componentContent = file_get_contents($component);
        
        // Extrair comentários JSDoc
        preg_match_all('/\/\*\*(.*?)\*\//s', $componentContent, $matches);
        
        $componentsDoc .= "### $componentName\n";
        $componentsDoc .= "**Arquivo**: `$component`\n\n";
        
        if (isset($matches[1][0])) {
            $docComment = trim($matches[1][0]);
            $docComment = preg_replace('/^\s*\*\s*/m', '', $docComment);
            $componentsDoc .= "$docComment\n\n";
        } else {
            $componentsDoc .= "Descrição não disponível.\n\n";
        }
        
        // Extrair funções públicas
        preg_match_all('/^(\s*)([a-zA-Z0-9_]+)\s*\([^)]*\)\s*{/m', $componentContent, $functionMatches);
        
        if (isset($functionMatches[2]) && count($functionMatches[2]) > 0) {
            $componentsDoc .= "**Funções públicas**:\n\n";
            foreach ($functionMatches[2] as $function) {
                if (!in_array($function, ['constructor'])) {
                    $componentsDoc .= "- `$function()`\n";
                }
            }
            $componentsDoc .= "\n";
        }
    }
    
    file_put_contents("$docsDir/components_documentation.md", $componentsDoc);
    echo "✓ Documentação de componentes gerada\n";
} else {
    echo "✗ Diretório de componentes não encontrado\n";
}

echo "\n";

// 4. Gerar documentação da API
echo "4. Gerando documentação da API...\n";

$apiDir = 'api';
if (is_dir($apiDir)) {
    $apiDoc = "# Documentação da API\n";
    $apiDoc .= "===================\n\n";
    
    $apiDoc .= "## Visão Geral\n";
    $apiDoc .= "A API fornece acesso programático a todos os dados do sistema.\n\n";
    
    $apiDoc .= "## Endpoints Disponíveis\n\n";
    
    $endpoints = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($apiDir));
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $relativePath = str_replace([$apiDir, '\\'], ['', '/'], $file->getPathname());
            $endpoints[] = $relativePath;
        }
    }
    
    sort($endpoints);
    
    foreach ($endpoints as $endpoint) {
        $apiDoc .= "- `$endpoint`\n";
    }
    
    $apiDoc .= "\n";
    
    // Documentar endpoints principais
    $mainEndpoints = [
        '/clipping/list.php' => 'Listar clippings',
        '/clipping/details.php' => 'Detalhes de clipping',
        '/publications/list.php' => 'Listar publicações',
        '/publications/details.php' => 'Detalhes de publicação',
        '/archive/list.php' => 'Listar itens do acervo',
        '/archive/details.php' => 'Detalhes de item do acervo',
        '/history/list.php' => 'Listar eventos históricos',
        '/history/details.php' => 'Detalhes de evento histórico',
        '/about/list.php' => 'Listar seções sobre',
        '/about/details.php' => 'Detalhes de seção sobre',
        '/crmc/list.php' => 'Listar itens do CRMC',
        '/crmc/details.php' => 'Detalhes de item do CRMC'
    ];
    
    $apiDoc .= "## Descrição dos Endpoints Principais\n\n";
    
    foreach ($mainEndpoints as $endpoint => $description) {
        $fullPath = "api$endpoint";
        if (file_exists($fullPath)) {
            $apiDoc .= "### $description\n";
            $apiDoc .= "**Endpoint**: `$endpoint`\n\n";
            
            // Ler conteúdo do arquivo para extrair parâmetros
            $content = file_get_contents($fullPath);
            
            // Extrair parâmetros GET
            if (preg_match_all('/\$_GET\[\'([^\']+)\'\]/', $content, $getParamMatches)) {
                $apiDoc .= "**Parâmetros GET**:\n";
                foreach ($getParamMatches[1] as $param) {
                    $apiDoc .= "- `$param`\n";
                }
                $apiDoc .= "\n";
            }
            
            // Extrair parâmetros POST
            if (preg_match_all('/\$_POST\[\'([^\']+)\'\]/', $content, $postParamMatches)) {
                $apiDoc .= "**Parâmetros POST**:\n";
                foreach ($postParamMatches[1] as $param) {
                    $apiDoc .= "- `$param`\n";
                }
                $apiDoc .= "\n";
            }
        }
    }
    
    file_put_contents("$docsDir/api_endpoints_documentation.md", $apiDoc);
    echo "✓ Documentação da API gerada\n";
} else {
    echo "✗ Diretório da API não encontrado\n";
}

echo "\n";

// 5. Gerar documentação do banco de dados
echo "5. Gerando documentação do banco de dados...\n";

$dbDoc = "# Documentação do Banco de Dados\n";
$dbDoc .= "===============================\n\n";

$dbDoc .= "## Visão Geral\n";
$dbDoc .= "O sistema utiliza um banco de dados MySQL para armazenar todas as informações.\n\n";

$dbDoc .= "## Configuração\n";
$dbDoc .= "**Arquivo**: `api/config/database.php`\n\n";

$dbDoc .= "```php\n";
$dbDoc .= "<?php\n";
$dbDoc .= "// Configuração do banco de dados\n";
$dbDoc .= "define('DB_HOST', 'localhost');\n";
$dbDoc .= "define('DB_NAME', 'cccrj_db');\n";
$dbDoc .= "define('DB_USER', 'root');\n";
$dbDoc .= "define('DB_PASS', '');\n";
$dbDoc .= "?>\n";
$dbDoc .= "```\n\n";

$dbDoc .= "## Tabelas do Sistema\n\n";

$tables = [
    'clippings' => 'Clippings de notícias',
    'publications' => 'Publicações técnicas',
    'archive_items' => 'Itens do acervo',
    'historical_events' => 'Eventos históricos',
    'about_sections' => 'Seções sobre',
    'crmc_items' => 'Itens do CRMC'
];

foreach ($tables as $table => $description) {
    $dbDoc .= "### `$table`\n";
    $dbDoc .= "**Descrição**: $description\n\n";
}

$dbDoc .= "## Diagrama de Relacionamento\n";
$dbDoc .= "```\n";
$dbDoc .= "[clippings] -----\n";
$dbDoc .= "                 |\n";
$dbDoc .= "[publications] --+--> [cccrj_db]\n";
$dbDoc .= "                 |\n";
$dbDoc .= "[archive_items] -\n";
$dbDoc .= "                 |\n";
$dbDoc .= "[historical_events]\n";
$dbDoc .= "                 |\n";
$dbDoc .= "[about_sections] |\n";
$dbDoc .= "                 |\n";
$dbDoc .= "[crmc_items] ----\n";
$dbDoc .= "```\n\n";

file_put_contents("$docsDir/database_documentation.md", $dbDoc);
echo "✓ Documentação do banco de dados gerada\n\n";

// 6. Gerar documentação de instalação
echo "6. Gerando documentação de instalação...\n";

$installDoc = "# Guia de Instalação\n";
$installDoc .= "==================\n\n";

$installDoc .= "## Requisitos do Sistema\n";
$installDoc .= "- XAMPP (Apache, MySQL, PHP)\n";
$installDoc .= "- PHP 7.4 ou superior\n";
$installDoc .= "- MySQL 5.7 ou superior\n";
$installDoc .= "- Navegador moderno\n\n";

$installDoc .= "## Passos de Instalação\n\n";

$installDoc .= "### 1. Instalar XAMPP\n";
$installDoc .= "1. Baixe o XAMPP de https://www.apachefriends.org/\n";
$installDoc .= "2. Instale seguindo as instruções do instalador\n";
$installDoc .= "3. Inicie Apache e MySQL no Painel de Controle\n\n";

$installDoc .= "### 2. Configurar o Projeto\n";
$installDoc .= "1. Copie o projeto para `C:/xampp/htdocs/`\n";
$installDoc .= "2. Acesse `http://localhost/phpmyadmin`\n";
$installDoc .= "3. Crie o banco de dados `cccrj_db`\n";
$installDoc .= "4. Execute os scripts de criação de tabelas\n\n";

$installDoc .= "### 3. Configurar o Banco de Dados\n";
$installDoc .= "Edite `api/config/database.php` com as credenciais corretas.\n\n";

file_put_contents("$docsDir/installation_guide.md", $installDoc);
echo "✓ Documentação de instalação gerada\n\n";

// 7. Criar índice da documentação
echo "7. Criando índice da documentação...\n";

$indexDoc = "# Índice da Documentação\n";
$indexDoc .= "=====================\n\n";

$documentationFiles = [
    'technical_documentation.md' => 'Documentação Técnica Completa',
    'components_documentation.md' => 'Documentação dos Componentes',
    'api_endpoints_documentation.md' => 'Documentação dos Endpoints da API',
    'database_documentation.md' => 'Documentação do Banco de Dados',
    'installation_guide.md' => 'Guia de Instalação',
    'development_guide.md' => 'Guia de Desenvolvimento',
    'user_manual.md' => 'Manual do Usuário'
];

$indexDoc .= "## Documentos Disponíveis\n\n";

foreach ($documentationFiles as $file => $title) {
    if (file_exists("$docsDir/$file")) {
        $modified = date('d/m/Y H:i:s', filemtime("$docsDir/$file"));
        $indexDoc .= "- [$title]($file) (atualizado em $modified)\n";
    } else {
        $indexDoc .= "- $title (não gerado)\n";
    }
}

file_put_contents("$docsDir/documentation_index.md", $indexDoc);
echo "✓ Índice da documentação criado\n\n";

echo "✅ Todas as tarefas de documentação concluídas!\n";
echo "\nDocumentos gerados em: $docsDir/\n";

?>