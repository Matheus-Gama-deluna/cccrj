<?php
// generate_documentation.php

// Script para gerar documentação técnica do sistema

echo "Gerando documentação técnica do sistema...\n\n";

// 1. Gerar estrutura de diretórios
echo "1. Gerando estrutura de diretórios...\n";

function generateDirectoryTree($dir, $indent = 0) {
    $tree = "";
    $files = array_diff(scandir($dir), array('.', '..'));
    
    foreach ($files as $file) {
        $path = "$dir/$file";
        $tree .= str_repeat("  ", $indent) . "|-- $file\n";
        
        if (is_dir($path)) {
            $tree .= generateDirectoryTree($path, $indent + 1);
        }
    }
    
    return $tree;
}

$directoryTree = "ESTRUTURA DE DIRETÓRIOS\n";
$directoryTree .= "=======================\n\n";
$directoryTree .= generateDirectoryTree(".", 0);

$docsDir = "docs";
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0755, true);
}

file_put_contents("$docsDir/directory_structure.txt", $directoryTree);

echo "✓ Estrutura de diretórios gerada em docs/directory_structure.txt\n\n";

// 2. Gerar documentação da API
echo "2. Gerando documentação da API...\n";

$apiDocs = "# Documentação da API\n";
$apiDocs .= "=====================\n\n";

$apiDocs .= "## Visão Geral\n";
$apiDocs .= "A API do sistema CCCRJ fornece acesso programático aos dados do sistema.\n\n";

$apiDocs .= "## Endpoints\n\n";

$apiEndpoints = [
    [
        'name' => 'Clipping',
        'path' => 'api/clipping',
        'methods' => [
            ['method' => 'GET', 'endpoint' => '/list.php', 'description' => 'Obter lista de clippings', 'params' => ['limit' => '(opcional)', 'offset' => '(opcional)']],
            ['method' => 'GET', 'endpoint' => '/details.php', 'description' => 'Obter detalhes de um clipping específico', 'params' => ['id' => '(requerido)']]
        ]
    ],
    [
        'name' => 'Publicações',
        'path' => 'api/publications',
        'methods' => [
            ['method' => 'GET', 'endpoint' => '/list.php', 'description' => 'Obter lista de publicações', 'params' => ['limit' => '(opcional)', 'offset' => '(opcional)']],
            ['method' => 'GET', 'endpoint' => '/details.php', 'description' => 'Obter detalhes de uma publicação específica', 'params' => ['id' => '(requerido)']]
        ]
    ],
    [
        'name' => 'Acervo',
        'path' => 'api/archive',
        'methods' => [
            ['method' => 'GET', 'endpoint' => '/list.php', 'description' => 'Obter lista de itens do acervo', 'params' => ['limit' => '(opcional)', 'offset' => '(opcional)']],
            ['method' => 'GET', 'endpoint' => '/details.php', 'description' => 'Obter detalhes de um item do acervo', 'params' => ['id' => '(requerido)']]
        ]
    ],
    [
        'name' => 'História',
        'path' => 'api/history',
        'methods' => [
            ['method' => 'GET', 'endpoint' => '/list.php', 'description' => 'Obter lista de eventos históricos', 'params' => ['limit' => '(opcional)', 'offset' => '(opcional)']],
            ['method' => 'GET', 'endpoint' => '/details.php', 'description' => 'Obter detalhes de um evento histórico', 'params' => ['id' => '(requerido)']]
        ]
    ],
    [
        'name' => 'Sobre',
        'path' => 'api/about',
        'methods' => [
            ['method' => 'GET', 'endpoint' => '/list.php', 'description' => 'Obter lista de seções sobre', 'params' => ['limit' => '(opcional)', 'offset' => '(opcional)']],
            ['method' => 'GET', 'endpoint' => '/details.php', 'description' => 'Obter detalhes de uma seção sobre', 'params' => ['id' => '(requerido)']]
        ]
    ],
    [
        'name' => 'CRMC',
        'path' => 'api/crmc',
        'methods' => [
            ['method' => 'GET', 'endpoint' => '/list.php', 'description' => 'Obter lista de itens do CRMC', 'params' => ['limit' => '(opcional)', 'offset' => '(opcional)']],
            ['method' => 'GET', 'endpoint' => '/details.php', 'description' => 'Obter detalhes de um item do CRMC', 'params' => ['id' => '(requerido)']]
        ]
    ]
];

foreach ($apiEndpoints as $api) {
    $apiDocs .= "### {$api['name']}\n";
    $apiDocs .= "Endpoint base: `{$api['path']}`\n\n";
    
    foreach ($api['methods'] as $method) {
        $apiDocs .= "#### `{$method['method']}` {$method['endpoint']}\n";
        $apiDocs .= "**Descrição**: {$method['description']}\n\n";
        
        if (!empty($method['params'])) {
            $apiDocs .= "**Parâmetros**:\n";
            foreach ($method['params'] as $param => $description) {
                $apiDocs .= "- `$param`: $description\n";
            }
            $apiDocs .= "\n";
        }
    }
    
    $apiDocs .= "\n";
}

file_put_contents("$docsDir/api_documentation.md", $apiDocs);

echo "✓ Documentação da API gerada em docs/api_documentation.md\n\n";

// 3. Gerar documentação dos componentes JavaScript
echo "3. Gerando documentação dos componentes JavaScript...\n";

$jsDocs = "# Documentação dos Componentes JavaScript\n";
$jsDocs .= "========================================\n\n";

$jsComponents = [
    [
        'name' => 'CoffeeQuotes',
        'file' => 'assets/js/components/quotes.js',
        'description' => 'Componente para exibição de cotações de café em tempo real',
        'methods' => [
            'constructor' => 'Inicializa o componente',
            'init' => 'Inicializa os elementos do componente',
            'loadQuotes' => 'Carrega as cotações do servidor',
            'renderQuotes' => 'Renderiza as cotações na página',
            'updatePrices' => 'Atualiza os preços em tempo real',
            'calculatePriceChange' => 'Calcula a variação de preços'
        ]
    ],
    [
        'name' => 'NewsManager',
        'file' => 'assets/js/components/news.js',
        'description' => 'Componente para gerenciamento e exibição de notícias',
        'methods' => [
            'constructor' => 'Inicializa o componente',
            'init' => 'Inicializa os elementos do componente',
            'loadNews' => 'Carrega notícias do servidor',
            'renderNews' => 'Renderiza notícias na página',
            'createNewsCard' => 'Cria card de notícia',
            'loadMoreNews' => 'Carrega mais notícias'
        ]
    ],
    [
        'name' => 'ReportsManager',
        'file' => 'assets/js/components/reports.js',
        'description' => 'Componente para gerenciamento de relatórios em PDF',
        'methods' => [
            'constructor' => 'Inicializa o componente',
            'init' => 'Inicializa os elementos do componente',
            'loadReports' => 'Carrega relatórios do servidor',
            'renderReports' => 'Renderiza relatórios na página',
            'createReportCard' => 'Cria card de relatório',
            'downloadReport' => 'Download de relatório'
        ]
    ],
    [
        'name' => 'CoffeeCalculator',
        'file' => 'assets/js/components/calculator.js',
        'description' => 'Componente para calculadora de conversão de café',
        'methods' => [
            'constructor' => 'Inicializa o componente',
            'init' => 'Inicializa os elementos do componente',
            'calculate' => 'Realiza o cálculo de conversão',
            'formatCurrency' => 'Formata valor como moeda'
        ]
    ],
    [
        'name' => 'AboutManager',
        'file' => 'assets/js/components/about.js',
        'description' => 'Componente para exibição de informações institucionais',
        'methods' => [
            'constructor' => 'Inicializa o componente',
            'init' => 'Inicializa os elementos do componente',
            'loadAboutContent' => 'Carrega conteúdo institucional',
            'renderAboutContent' => 'Renderiza conteúdo na página'
        ]
    ],
    [
        'name' => 'HistoryManager',
        'file' => 'assets/js/components/history.js',
        'description' => 'Componente para exibição da história do CCCRJ',
        'methods' => [
            'constructor' => 'Inicializa o componente',
            'init' => 'Inicializa os elementos do componente',
            'loadHistory' => 'Carrega eventos históricos',
            'renderTimeline' => 'Renderiza timeline histórica'
        ]
    ],
    [
        'name' => 'PublicationsManager',
        'file' => 'assets/js/components/publications.js',
        'description' => 'Componente para exibição de publicações',
        'methods' => [
            'constructor' => 'Inicializa o componente',
            'init' => 'Inicializa os elementos do componente',
            'loadPublications' => 'Carrega publicações',
            'renderPublications' => 'Renderiza publicações na página'
        ]
    ],
    [
        'name' => 'ClippingManager',
        'file' => 'assets/js/components/clipping.js',
        'description' => 'Componente para exibição de clipping histórico',
        'methods' => [
            'constructor' => 'Inicializa o componente',
            'init' => 'Inicializa os elementos do componente',
            'loadClippings' => 'Carrega clipped content',
            'renderClippings' => 'Renderiza clipping na página'
        ]
    ]
];

foreach ($jsComponents as $component) {
    if (file_exists($component['file'])) {
        $jsDocs .= "## {$component['name']}\n";
        $jsDocs .= "**Arquivo**: `{$component['file']}`\n\n";
        $jsDocs .= "**Descrição**: {$component['description']}\n\n";
        
        $jsDocs .= "**Métodos principais**:\n\n";
        foreach ($component['methods'] as $method => $description) {
            $jsDocs .= "- `$method`: $description\n";
        }
        
        $jsDocs .= "\n";
    }
}

file_put_contents("$docsDir/js_components_documentation.md", $jsDocs);

echo "✓ Documentação dos componentes JavaScript gerada em docs/js_components_documentation.md\n\n";

// 4. Gerar documentação do banco de dados
echo "4. Gerando documentação do banco de dados...\n";

$dbDocs = "# Documentação do Banco de Dados\n";
$dbDocs .= "===============================\n\n";

$dbDocs .= "## Visão Geral\n";
$dbDocs .= "O sistema utiliza um banco de dados MySQL para armazenar as informações do sistema.\n\n";

$dbDocs .= "## Tabelas\n\n";

$tables = [
    [
        'name' => 'historical_events',
        'description' => 'Eventos históricos do CCCRJ',
        'columns' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'title' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT',
            'content' => 'TEXT',
            'date' => 'DATE',
            'event_type' => 'VARCHAR(100)',
            'image_url' => 'VARCHAR(500)',
            'is_featured' => 'TINYINT(1) DEFAULT 0',
            'is_active' => 'TINYINT(1) DEFAULT 1',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]
    ],
    [
        'name' => 'publications',
        'description' => 'Publicações técnicas e revistas',
        'columns' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'title' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT',
            'file_path' => 'VARCHAR(500)',
            'date' => 'DATE',
            'number' => 'VARCHAR(50)',
            'type' => 'VARCHAR(50)',
            'is_active' => 'TINYINT(1) DEFAULT 1',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]
    ],
    [
        'name' => 'archive_items',
        'description' => 'Itens do acervo histórico',
        'columns' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'title' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT',
            'file_path' => 'VARCHAR(500)',
            'date' => 'DATE',
            'item_type' => 'VARCHAR(100)',
            'category' => 'VARCHAR(100)',
            'metadata' => 'TEXT',
            'is_active' => 'TINYINT(1) DEFAULT 1',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]
    ],
    [
        'name' => 'clippings',
        'description' => 'Clipping de notícias do setor',
        'columns' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'title' => 'VARCHAR(255) NOT NULL',
            'summary' => 'TEXT',
            'content' => 'TEXT',
            'source_url' => 'VARCHAR(500)',
            'date' => 'DATE',
            'category' => 'VARCHAR(100)',
            'is_active' => 'TINYINT(1) DEFAULT 1',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]
    ],
    [
        'name' => 'about_sections',
        'description' => 'Seções da página Sobre',
        'columns' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'title' => 'VARCHAR(255) NOT NULL',
            'content' => 'TEXT',
            'section_type' => 'VARCHAR(100)',
            'order' => 'INT DEFAULT 0',
            'is_active' => 'TINYINT(1) DEFAULT 1',
            'image_url' => 'VARCHAR(500)',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]
    ],
    [
        'name' => 'crmc_items',
        'description' => 'Itens do Centro de Referência e Memória do Café',
        'columns' => [
            'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
            'title' => 'VARCHAR(255) NOT NULL',
            'description' => 'TEXT',
            'content' => 'TEXT',
            'category' => 'VARCHAR(100)',
            'image_url' => 'VARCHAR(500)',
            'file_path' => 'VARCHAR(500)',
            'is_active' => 'TINYINT(1) DEFAULT 1',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]
    ]
];

foreach ($tables as $table) {
    $dbDocs .= "### `{$table['name']}`\n";
    $dbDocs .= "**Descrição**: {$table['description']}\n\n";
    
    $dbDocs .= "**Colunas**:\n\n";
    foreach ($table['columns'] as $column => $definition) {
        $dbDocs .= "- `$column`: $definition\n";
    }
    
    $dbDocs .= "\n";
}

file_put_contents("$docsDir/database_documentation.md", $dbDocs);

echo "✓ Documentação do banco de dados gerada em docs/database_documentation.md\n\n";

// 5. Gerar documentação de instalação
echo "5. Gerando documentação de instalação...\n";

$installDocs = "# Guia de Instalação\n";
$installDocs .= "==================\n\n";

$installDocs .= "## Requisitos do Sistema\n";
$installDocs .= "- XAMPP (Apache, MySQL, PHP)\n";
$installDocs .= "- PHP 7.4 ou superior\n";
$installDocs .= "- MySQL 5.7 ou superior\n";
$installDocs .= "- Navegador moderno (Chrome, Firefox, Edge, Safari)\n\n";

$installDocs .= "## Instalação\n\n";

$installDocs .= "### 1. Instalar XAMPP\n";
$installDocs .= "1. Baixe o XAMPP de [https://www.apachefriends.org/](https://www.apachefriends.org/)\n";
$installDocs .= "2. Instale o XAMPP seguindo as instruções do instalador\n";
$installDocs .= "3. Inicie os serviços Apache e MySQL no Painel de Controle do XAMPP\n\n";

$installDocs .= "### 2. Configurar o Projeto\n";
$installDocs .= "1. Copie todo o conteúdo do projeto para o diretório `htdocs` do XAMPP:\n";
$installDocs .= "   ```bash\n";
$installDocs .= "   cp -r cccrj/ C:/xampp/htdocs/\n";
$installDocs .= "   ```\n\n";

$installDocs .= "### 3. Criar o Banco de Dados\n";
$installDocs .= "1. Acesse `http://localhost/phpmyadmin`\n";
$installDocs .= "2. Crie um novo banco de dados chamado `cccrj_db`\n";
$installDocs .= "3. Execute o script `create_database_tables.php`:\n";
$installDocs .= "   ```bash\n";
$installDocs .= "   php create_database_tables.php\n";
$installDocs .= "   ```\n\n";

$installDocs .= "### 4. Popular o Banco de Dados\n";
$installDocs .= "1. Execute o script `populate_db.php` para adicionar dados de exemplo:\n";
$installDocs .= "   ```bash\n";
$installDocs .= "   php populate_db.php\n";
$installDocs .= "   ```\n\n";

$installDocs .= "### 5. Acessar o Sistema\n";
$installDocs .= "1. Abra seu navegador e acesse `http://localhost/cccrj`\n";
$installDocs .= "2. O sistema estará pronto para uso\n\n";

$installDocs .= "## Configuração Adicional\n\n";

$installDocs .= "### Configuração do Banco de Dados\n";
$installDocs .= "Edite o arquivo `api/config/database.php` para definir as credenciais do banco de dados:\n";
$installDocs .= "```php\n";
$installDocs .= "<?php\n";
$installDocs .= "// Configuração do banco de dados\n";
$installDocs .= "define('DB_HOST', 'localhost');\n";
$installDocs .= "define('DB_NAME', 'cccrj_db');\n";
$installDocs .= "define('DB_USER', 'root');\n";
$installDocs .= "define('DB_PASS', '');\n";
$installDocs .= "?>\n";
$installDocs .= "```\n\n";

file_put_contents("$docsDir/install_guide.md", $installDocs);

echo "✓ Documentação de instalação gerada em docs/install_guide.md\n\n";

// 6. Gerar documentação de desenvolvimento
echo "6. Gerando documentação de desenvolvimento...\n";

$devDocs = "# Guia de Desenvolvimento\n";
$devDocs .= "======================\n\n";

$devDocs .= "## Estrutura do Projeto\n";
$devDocs .= "```\n";
$devDocs .= "cccrj/\n";
$devDocs .= "├── api/                    # API em PHP puro\n";
$devDocs .= "│   ├── config/            # Configurações\n";
$devDocs .= "│   ├── utils/             # Funções utilitárias\n";
$devDocs .= "│   ├── models/            # Modelos de dados\n";
$devDocs .= "│   ├── clipping/          # Endpoints para clipping\n";
$devDocs .= "│   ├── publications/      # Endpoints para publicações\n";
$devDocs .= "│   ├── archive/           # Endpoints para acervo\n";
$devDocs .= "│   ├── history/           # Endpoints para história\n";
$devDocs .= "│   ├── about/             # Endpoints para seção sobre\n";
$devDocs .= "│   └── crmc/              # Endpoints para CRMC\n";
$devDocs .= "├── assets/                # Recursos frontend\n";
$devDocs .= "│   ├── css/               # Folhas de estilo\n";
$devDocs .= "│   └── js/                # Scripts JavaScript\n";
$devDocs .= "│       └── components/    # Componentes modulares\n";
$devDocs .= "├── scraping_cccrj/        # Conteúdo raspado do site original\n";
$devDocs .= "│   └── cccrj_content/      # Conteúdo HTML raspado\n";
$devDocs .= "├── uploads/              # Arquivos enviados\n";
$devDocs .= "├── index.html             # Página principal\n";
$devDocs .= "└── ...\n";
$devDocs .= "```\n\n";

$devDocs .= "## Componentes\n\n";

$devDocs .= "### Componentes JavaScript\n";
$devDocs .= "Cada componente JavaScript segue o padrão:\n";
$devDocs .= "```javascript\n";
$devDocs .= "class ComponentName {\n";
$devDocs .= "    constructor() {\n";
$devDocs .= "        this.apiUrl = 'api/component/endpoint.php';\n";
$devDocs .= "        this.init();\n";
$devDocs .= "    }\n";
$devDocs .= "    \n";
$devDocs .= "    async init() {\n";
$devDocs .= "        // Inicialização do componente\n";
$devDocs .= "    }\n";
$devDocs .= "    \n";
$devDocs .= "    async loadData() {\n";
$devDocs .= "        // Carregar dados da API\n";
$devDocs .= "    }\n";
$devDocs .= "    \n";
$devDocs .= "    render() {\n";
$devDocs .= "        // Renderizar interface\n";
$devDocs .= "    }\n";
$devDocs .= "}\n";
$devDocs .= "```\n\n";

$devDocs .= "### Endpoints da API\n";
$devDocs .= "Cada endpoint da API segue o padrão:\n";
$devDocs .= "```php\n";
$devDocs .= "<?php\n";
$devDocs .= "// api/component/action.php\n";
$devDocs .= "\n";
$devDocs .= "// Incluir modelos e utilitários\n";
$devDocs .= "require_once __DIR__ . '/../models/ComponentModel.php';\n";
$devDocs .= "require_once __DIR__ . '/../utils/functions.php';\n";
$devDocs .= "\n";
$devDocs .= "// Definir cabeçalhos de resposta\n";
$devDocs .= "header('Content-Type: application/json');\n";
$devDocs .= "\n";
$devDocs .= "// Processar requisição\n";
$devDocs .= "try {\n";
$devDocs .= "    // Obter parâmetros\n";
$devDocs .= "    \$limit = isset(\$_GET['limit']) ? (int)\$_GET['limit'] : 10;\n";
$devDocs .= "    \$offset = isset(\$_GET['offset']) ? (int)\$_GET['offset'] : 0;\n";
$devDocs .= "    \n";
$devDocs .= "    // Instanciar modelo\n";
$devDocs .= "    \$model = new ComponentModel();\n";
$devDocs .= "    \n";
$devDocs .= "    // Buscar dados\n";
$devDocs .= "    \$data = \$model->getAll(\$limit, \$offset);\n";
$devDocs .= "    \n";
$devDocs .= "    // Retornar resposta\n";
$devDocs .= "    echo json_encode([\n";
$devDocs .= "        'success' => true,\n";
$devDocs .= "        'data' => \$data,\n";
$devDocs .= "        'limit' => \$limit,\n";
$devDocs .= "        'offset' => \$offset\n";
$devDocs .= "    ]);\n";
$devDocs .= "    \n";
$devDocs .= "} catch (Exception \$e) {\n";
$devDocs .= "    http_response_code(500);\n";
$devDocs .= "    echo json_encode([\n";
$devDocs .= "        'success' => false,\n";
$devDocs .= "        'message' => \$e->getMessage()\n";
$devDocs .= "    ]);\n";
$devDocs .= "}\n";
$devDocs .= "?>\n";
$devDocs .= "```\n\n";

$devDocs .= "## Modelos de Dados\n";
$devDocs .= "Cada modelo de dados segue o padrão:\n";
$devDocs .= "```php\n";
$devDocs .= "<?php\n";
$devDocs .= "// api/models/ComponentModel.php\n";
$devDocs .= "\n";
$devDocs .= "require_once __DIR__ . '/../config/database.php';\n";
$devDocs .= "\n";
$devDocs .= "class ComponentModel {\n";
$devDocs .= "    private \$pdo;\n";
$devDocs .= "    \n";
$devDocs .= "    public function __construct() {\n";
$devDocs .= "        \$this->pdo = connectDatabase();\n";
$devDocs .= "    }\n";
$devDocs .= "    \n";
$devDocs .= "    public function getAll(\$limit = null, \$offset = null) {\n";
$devDocs .= "        try {\n";
$devDocs .= "            // Implementar lógica de consulta\n";
$devDocs .= "        } catch (PDOException \$e) {\n";
$devDocs .= "            error_log(\"Erro ao buscar dados: \" . \$e->getMessage());\n";
$devDocs .= "            throw new Exception(\"Não foi possível carregar os dados.\");\n";
$devDocs .= "        }\n";
$devDocs .= "    }\n";
$devDocs .= "    \n";
$devDocs .= "    public function getById(\$id) {\n";
$devDocs .= "        try {\n";
$devDocs .= "            // Implementar lógica de consulta por ID\n";
$devDocs .= "        } catch (PDOException \$e) {\n";
$devDocs .= "            error_log(\"Erro ao buscar dado: \" . \$e->getMessage());\n";
$devDocs .= "            throw new Exception(\"Não foi possível carregar o dado.\");\n";
$devDocs .= "        }\n";
$devDocs .= "    }\n";
$devDocs .= "}\n";
$devDocs .= "?>\n";
$devDocs .= "```\n\n";

file_put_contents("$docsDir/development_guide.md", $devDocs);

echo "✓ Documentação de desenvolvimento gerada em docs/development_guide.md\n\n";

// 7. Criar índice da documentação
echo "7. Criando índice da documentação...\n";

$indexDocs = "# Documentação Técnica do Sistema CCCRJ\n";
$indexDocs .= "====================================\n\n";

$indexDocs .= "## Índice\n\n";

$documentationFiles = [
    'directory_structure.txt' => 'Estrutura de Diretórios',
    'api_documentation.md' => 'Documentação da API',
    'js_components_documentation.md' => 'Documentação dos Componentes JavaScript',
    'database_documentation.md' => 'Documentação do Banco de Dados',
    'install_guide.md' => 'Guia de Instalação',
    'development_guide.md' => 'Guia de Desenvolvimento'
];

foreach ($documentationFiles as $file => $title) {
    if (file_exists("$docsDir/$file")) {
        $indexDocs .= "- [$title]($file)\n";
    }
}

file_put_contents("$docsDir/index.md", $indexDocs);

echo "✓ Índice da documentação gerado em docs/index.md\n\n";

echo "✅ Documentação técnica gerada com sucesso!\n";
echo "\nDocumentos gerados:\n";
foreach ($documentationFiles as $file => $title) {
    if (file_exists("$docsDir/$file")) {
        echo "- $docsDir/$file ($title)\n";
    }
}
?>