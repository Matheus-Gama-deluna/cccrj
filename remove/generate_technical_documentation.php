<?php
// generate_technical_documentation.php

// Script para gerar documentação técnica completa do sistema

echo "Gerando documentação técnica do sistema...\n\n";

// 1. Visão Geral do Sistema
echo "1. Gerando visão geral do sistema...\n";

$systemOverview = [
    'project_name' => 'Centro de Comércio do Café do Rio de Janeiro (CCCRJ)',
    'version' => '1.0.0',
    'release_date' => date('Y-m-d'),
    'architecture' => 'Frontend/Backend Separated with REST API',
    'technologies' => [
        'Frontend' => 'HTML5, Tailwind CSS, JavaScript (ES6+)',
        'Backend' => 'PHP 8+ (Pure PHP, no frameworks)',
        'Database' => 'MySQL',
        'Server' => 'Apache (XAMPP)',
        'API' => 'RESTful API',
        'Authentication' => 'Session-based Authentication'
    ],
    'developers' => [
        'Lead Developer' => 'Equipe de Desenvolvimento CCCRJ',
        'Frontend Developer' => 'Especialista em UI/UX',
        'Backend Developer' => 'Especialista em PHP',
        'Database Administrator' => 'Especialista em MySQL',
        'Content Specialist' => 'Especialista em Conteúdo Histórico'
    ]
];

echo "✓ Visão geral do sistema gerada\n";

// 2. Estrutura do Projeto
echo "2. Gerando estrutura do projeto...\n";

$projectStructure = [
    'root' => [
        'description' => 'Diretório raiz do projeto',
        'contents' => [
            'api/' => 'API em PHP puro',
            'assets/' => 'Recursos frontend',
            'scraping_cccrj/' => 'Conteúdo raspado do site original',
            'uploads/' => 'Arquivos enviados',
            'reports/' => 'Relatórios gerados',
            'exports/' => 'Exportações de dados',
            'index.html' => 'Página principal',
            'main.js' => 'Script principal',
            'style.css' => 'Folha de estilo principal',
            'README.md' => 'Documentação principal'
        ]
    ],
    'api' => [
        'description' => 'Diretório da API em PHP puro',
        'contents' => [
            'config/' => 'Configurações do sistema',
            'models/' => 'Modelos de dados',
            'utils/' => 'Funções utilitárias',
            'clipping/' => 'Endpoints para clipping',
            'publications/' => 'Endpoints para publicações',
            'history/' => 'Endpoints para história',
            'about/' => 'Endpoints para seção sobre',
            'crmc/' => 'Endpoints para CRMC',
            'archive/' => 'Endpoints para acervo'
        ]
    ],
    'assets' => [
        'description' => 'Diretório de recursos frontend',
        'contents' => [
            'css/' => 'Folhas de estilo',
            'js/' => 'Scripts JavaScript',
            'js/components/' => 'Componentes JavaScript modulares',
            'images/' => 'Imagens e recursos visuais'
        ]
    ]
];

echo "✓ Estrutura do projeto gerada\n";

// 3. Componentes do Sistema
echo "3. Gerando documentação dos componentes...\n";

$components = [
    'Frontend Components' => [
        'quotes.js' => [
            'description' => 'Componente de Cotações de Café',
            'responsibilities' => [
                'Exibir cotações em tempo real',
                'Atualizar preços automaticamente',
                'Calcular variações de preços',
                'Mostrar gráficos de tendência'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons',
                'API de cotações (/api/quotes/)'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'priceUpdate' => 'Atualização de preços',
                'error' => 'Tratamento de erros'
            ]
        ],
        'news.js' => [
            'description' => 'Componente de Notícias',
            'responsibilities' => [
                'Exibir notícias do setor cafeeiro',
                'Paginação de notícias',
                'Filtragem por categoria',
                'Busca de notícias'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons',
                'API de notícias (/api/news/)'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'newsLoad' => 'Carregamento de notícias',
                'error' => 'Tratamento de erros'
            ]
        ],
        'reports.js' => [
            'description' => 'Componente de Relatórios',
            'responsibilities' => [
                'Exibir relatórios técnicos em PDF',
                'Download de relatórios',
                'Visualização de metadados',
                'Filtragem por tipo'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons',
                'API de relatórios (/api/reports/)'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'reportLoad' => 'Carregamento de relatórios',
                'downloadReport' => 'Download de relatórios',
                'error' => 'Tratamento de erros'
            ]
        ],
        'calculator.js' => [
            'description' => 'Componente de Calculadora',
            'responsibilities' => [
                'Converter unidades de medida',
                'Calcular valores de café',
                'Exibir resultados',
                'Validar entradas'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'calculate' => 'Realização de cálculos',
                'input' => 'Captura de entradas'
            ]
        ],
        'clipping.js' => [
            'description' => 'Componente de Clipping Histórico',
            'responsibilities' => [
                'Exibir clipping histórico',
                'Filtragem por categoria',
                'Busca de clipping',
                'Paginação de conteúdo'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons',
                'API de clipping (/api/clipping/)'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'clippingLoad' => 'Carregamento de clipping',
                'error' => 'Tratamento de erros'
            ]
        ],
        'publications.js' => [
            'description' => 'Componente de Publicações',
            'responsibilities' => [
                'Exibir revistas e boletins',
                'Download de publicações',
                'Visualização de metadados',
                'Filtragem por tipo'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons',
                'API de publicações (/api/publications/)'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'publicationLoad' => 'Carregamento de publicações',
                'downloadPublication' => 'Download de publicações',
                'error' => 'Tratamento de erros'
            ]
        ],
        'history.js' => [
            'description' => 'Componente de História',
            'responsibilities' => [
                'Exibir timeline histórica',
                'Mostrar eventos históricos',
                'Filtragem por período',
                'Detalhes de eventos'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons',
                'API de história (/api/history/)'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'historyLoad' => 'Carregamento de eventos históricos',
                'error' => 'Tratamento de erros'
            ]
        ],
        'about.js' => [
            'description' => 'Componente Sobre Nós',
            'responsibilities' => [
                'Exibir informações institucionais',
                'Mostrar história do CCCRJ',
                'Apresentar diretoria',
                'Exibir estatutos'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons',
                'API de seção sobre (/api/about/)'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'aboutLoad' => 'Carregamento de informações institucionais',
                'error' => 'Tratamento de erros'
            ]
        ],
        'crmc.js' => [
            'description' => 'Componente CRMC',
            'responsibilities' => [
                'Exibir conteúdo do CRMC',
                'Mostrar biblioteca',
                'Apresentar cafeteria temática',
                'Exibir programação cultural'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons',
                'API do CRMC (/api/crmc/)'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'crmcLoad' => 'Carregamento de conteúdo do CRMC',
                'error' => 'Tratamento de erros'
            ]
        ],
        'archive.js' => [
            'description' => 'Componente de Acervo',
            'responsibilities' => [
                'Exibir itens do acervo',
                'Mostrar documentos históricos',
                'Apresentar galeria de fotos',
                'Exibir metadados'
            ],
            'dependencies' => [
                'Tailwind CSS',
                'Material Icons',
                'API de acervo (/api/archive/)'
            ],
            'events' => [
                'DOMContentLoaded' => 'Inicialização do componente',
                'archiveLoad' => 'Carregamento de itens do acervo',
                'error' => 'Tratamento de erros'
            ]
        ]
    ],
    'Backend Components' => [
        'Clipping.php' => [
            'description' => 'Modelo de Clipping',
            'responsibilities' => [
                'Gerenciar dados de clipping',
                'Buscar clipping do banco de dados',
                'Criar e atualizar clipping',
                'Excluir clipping'
            ],
            'dependencies' => [
                'PDO (PHP Data Objects)',
                'Database Configuration'
            ],
            'methods' => [
                'getAll()' => 'Buscar todos os clippings',
                'getById()' => 'Buscar clipping por ID',
                'create()' => 'Criar novo clipping',
                'update()' => 'Atualizar clipping existente',
                'delete()' => 'Excluir clipping'
            ]
        ],
        'Publication.php' => [
            'description' => 'Modelo de Publicação',
            'responsibilities' => [
                'Gerenciar dados de publicações',
                'Buscar publicações do banco de dados',
                'Criar e atualizar publicações',
                'Excluir publicações'
            ],
            'dependencies' => [
                'PDO (PHP Data Objects)',
                'Database Configuration'
            ],
            'methods' => [
                'getAll()' => 'Buscar todas as publicações',
                'getById()' => 'Buscar publicação por ID',
                'create()' => 'Criar nova publicação',
                'update()' => 'Atualizar publicação existente',
                'delete()' => 'Excluir publicação'
            ]
        ],
        'HistoricalEvent.php' => [
            'description' => 'Modelo de Evento Histórico',
            'responsibilities' => [
                'Gerenciar dados de eventos históricos',
                'Buscar eventos do banco de dados',
                'Criar e atualizar eventos',
                'Excluir eventos'
            ],
            'dependencies' => [
                'PDO (PHP Data Objects)',
                'Database Configuration'
            ],
            'methods' => [
                'getAll()' => 'Buscar todos os eventos históricos',
                'getById()' => 'Buscar evento por ID',
                'create()' => 'Criar novo evento',
                'update()' => 'Atualizar evento existente',
                'delete()' => 'Excluir evento'
            ]
        ],
        'AboutSection.php' => [
            'description' => 'Modelo de Seção Sobre',
            'responsibilities' => [
                'Gerenciar dados de seções sobre',
                'Buscar seções do banco de dados',
                'Criar e atualizar seções',
                'Excluir seções'
            ],
            'dependencies' => [
                'PDO (PHP Data Objects)',
                'Database Configuration'
            ],
            'methods' => [
                'getAll()' => 'Buscar todas as seções sobre',
                'getById()' => 'Buscar seção por ID',
                'create()' => 'Criar nova seção',
                'update()' => 'Atualizar seção existente',
                'delete()' => 'Excluir seção'
            ]
        ],
        'CrmcItem.php' => [
            'description' => 'Modelo de Item do CRMC',
            'responsibilities' => [
                'Gerenciar dados de itens do CRMC',
                'Buscar itens do banco de dados',
                'Criar e atualizar itens',
                'Excluir itens'
            ],
            'dependencies' => [
                'PDO (PHP Data Objects)',
                'Database Configuration'
            ],
            'methods' => [
                'getAll()' => 'Buscar todos os itens do CRMC',
                'getById()' => 'Buscar item por ID',
                'create()' => 'Criar novo item',
                'update()' => 'Atualizar item existente',
                'delete()' => 'Excluir item'
            ]
        ],
        'ArchiveItem.php' => [
            'description' => 'Modelo de Item do Acervo',
            'responsibilities' => [
                'Gerenciar dados de itens do acervo',
                'Buscar itens do banco de dados',
                'Criar e atualizar itens',
                'Excluir itens'
            ],
            'dependencies' => [
                'PDO (PHP Data Objects)',
                'Database Configuration'
            ],
            'methods' => [
                'getAll()' => 'Buscar todos os itens do acervo',
                'getById()' => 'Buscar item por ID',
                'create()' => 'Criar novo item',
                'update()' => 'Atualizar item existente',
                'delete()' => 'Excluir item'
            ]
        ]
    ]
];

echo "✓ Documentação dos componentes gerada\n";

// 4. API Endpoints
echo "4. Gerando documentação da API...\n";

$apiEndpoints = [
    'Clipping' => [
        '/api/clipping/list.php' => [
            'method' => 'GET',
            'description' => 'Listar clippings',
            'parameters' => [
                'limit' => '(opcional) Número de registros por página',
                'offset' => '(opcional) Deslocamento dos registros'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'array',
                'limit' => 'integer',
                'offset' => 'integer'
            ]
        ],
        '/api/clipping/details.php' => [
            'method' => 'GET',
            'description' => 'Detalhes de clipping específico',
            'parameters' => [
                'id' => '(requerido) ID do clipping'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'object'
            ]
        ]
    ],
    'Publications' => [
        '/api/publications/list.php' => [
            'method' => 'GET',
            'description' => 'Listar publicações',
            'parameters' => [
                'limit' => '(opcional) Número de registros por página',
                'offset' => '(opcional) Deslocamento dos registros'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'array',
                'limit' => 'integer',
                'offset' => 'integer'
            ]
        ],
        '/api/publications/details.php' => [
            'method' => 'GET',
            'description' => 'Detalhes de publicação específica',
            'parameters' => [
                'id' => '(requerido) ID da publicação'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'object'
            ]
        ]
    ],
    'History' => [
        '/api/history/list.php' => [
            'method' => 'GET',
            'description' => 'Listar eventos históricos',
            'parameters' => [
                'limit' => '(opcional) Número de registros por página',
                'offset' => '(opcional) Deslocamento dos registros'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'array',
                'limit' => 'integer',
                'offset' => 'integer'
            ]
        ],
        '/api/history/details.php' => [
            'method' => 'GET',
            'description' => 'Detalhes de evento histórico específico',
            'parameters' => [
                'id' => '(requerido) ID do evento'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'object'
            ]
        ]
    ],
    'About' => [
        '/api/about/list.php' => [
            'method' => 'GET',
            'description' => 'Listar seções sobre',
            'parameters' => [
                'limit' => '(opcional) Número de registros por página',
                'offset' => '(opcional) Deslocamento dos registros'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'array',
                'limit' => 'integer',
                'offset' => 'integer'
            ]
        ],
        '/api/about/details.php' => [
            'method' => 'GET',
            'description' => 'Detalhes de seção sobre específica',
            'parameters' => [
                'id' => '(requerido) ID da seção'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'object'
            ]
        ]
    ],
    'CRMC' => [
        '/api/crmc/list.php' => [
            'method' => 'GET',
            'description' => 'Listar itens do CRMC',
            'parameters' => [
                'limit' => '(opcional) Número de registros por página',
                'offset' => '(opcional) Deslocamento dos registros'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'array',
                'limit' => 'integer',
                'offset' => 'integer'
            ]
        ],
        '/api/crmc/details.php' => [
            'method' => 'GET',
            'description' => 'Detalhes de item do CRMC específico',
            'parameters' => [
                'id' => '(requerido) ID do item'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'object'
            ]
        ]
    ],
    'Archive' => [
        '/api/archive/list.php' => [
            'method' => 'GET',
            'description' => 'Listar itens do acervo',
            'parameters' => [
                'limit' => '(opcional) Número de registros por página',
                'offset' => '(opcional) Deslocamento dos registros'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'array',
                'limit' => 'integer',
                'offset' => 'integer'
            ]
        ],
        '/api/archive/details.php' => [
            'method' => 'GET',
            'description' => 'Detalhes de item do acervo específico',
            'parameters' => [
                'id' => '(requerido) ID do item'
            ],
            'response' => [
                'success' => 'boolean',
                'data' => 'object'
            ]
        ]
    ]
];

echo "✓ Documentação da API gerada\n";

// 5. Banco de Dados
echo "5. Gerando documentação do banco de dados...\n";

$databaseSchema = [
    'clippings' => [
        'description' => 'Tabela para clipping histórico',
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
        ],
        'indexes' => [
            'idx_date' => 'INDEX (date)',
            'idx_category' => 'INDEX (category)',
            'idx_is_active' => 'INDEX (is_active)'
        ]
    ],
    'publications' => [
        'description' => 'Tabela para revistas e boletins',
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
        ],
        'indexes' => [
            'idx_date' => 'INDEX (date)',
            'idx_type' => 'INDEX (type)',
            'idx_is_active' => 'INDEX (is_active)'
        ]
    ],
    'historical_events' => [
        'description' => 'Tabela para eventos históricos',
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
        ],
        'indexes' => [
            'idx_date' => 'INDEX (date)',
            'idx_event_type' => 'INDEX (event_type)',
            'idx_is_featured' => 'INDEX (is_featured)',
            'idx_is_active' => 'INDEX (is_active)'
        ]
    ],
    'about_sections' => [
        'description' => 'Tabela para seções sobre',
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
        ],
        'indexes' => [
            'idx_section_type' => 'INDEX (section_type)',
            'idx_order' => 'INDEX (order)',
            'idx_is_active' => 'INDEX (is_active)'
        ]
    ],
    'crmc_items' => [
        'description' => 'Tabela para itens do CRMC',
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
        ],
        'indexes' => [
            'idx_category' => 'INDEX (category)',
            'idx_is_active' => 'INDEX (is_active)'
        ]
    ],
    'archive_items' => [
        'description' => 'Tabela para itens do acervo',
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
        ],
        'indexes' => [
            'idx_date' => 'INDEX (date)',
            'idx_item_type' => 'INDEX (item_type)',
            'idx_category' => 'INDEX (category)',
            'idx_is_active' => 'INDEX (is_active)'
        ]
    ]
];

echo "✓ Documentação do banco de dados gerada\n";

// 6. Gerar documentação em formato legível
echo "6. Gerando documentação em formato legível...\n";

$docsDir = 'docs';
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0755, true);
}

$timestamp = date('Y-m-d-H-i-s');
$docFile = "$docsDir/technical_documentation_$timestamp.md";

$docContent = "# DOCUMENTAÇÃO TÉCNICA DO SISTEMA CCCRJ\n";
$docContent .= "====================================\n\n";

$docContent .= "## 1. VISÃO GERAL DO SISTEMA\n";
$docContent .= "--------------------------\n\n";

$docContent .= "**Nome do Projeto**: " . $systemOverview['project_name'] . "\n";
$docContent .= "**Versão**: " . $systemOverview['version'] . "\n";
$docContent .= "**Data de Lançamento**: " . $systemOverview['release_date'] . "\n";
$docContent .= "**Arquitetura**: " . $systemOverview['architecture'] . "\n\n";

$docContent .= "### Tecnologias Utilizadas\n\n";
foreach ($systemOverview['technologies'] as $area => $tech) {
    $docContent .= "- **$area**: $tech\n";
}
$docContent .= "\n";

$docContent .= "### Equipe de Desenvolvimento\n\n";
foreach ($systemOverview['developers'] as $role => $developer) {
    $docContent .= "- **$role**: $developer\n";
}
$docContent .= "\n";

$docContent .= "## 2. ESTRUTURA DO PROJETO\n";
$docContent .= "------------------------\n\n";

foreach ($projectStructure as $dir => $info) {
    $docContent .= "### $dir\n";
    $docContent .= "**Descrição**: " . $info['description'] . "\n\n";
    $docContent .= "**Conteúdo**:\n";
    foreach ($info['contents'] as $item => $description) {
        $docContent .= "- `$item`: $description\n";
    }
    $docContent .= "\n";
}

$docContent .= "## 3. COMPONENTES DO SISTEMA\n";
$docContent .= "---------------------------\n\n";

foreach ($components as $category => $componentList) {
    $docContent .= "### $category\n\n";
    
    foreach ($componentList as $component => $info) {
        $docContent .= "#### $component\n";
        $docContent .= "**Descrição**: " . $info['description'] . "\n\n";
        
        $docContent .= "**Responsabilidades**:\n";
        foreach ($info['responsibilities'] as $responsibility) {
            $docContent .= "- $responsibility\n";
        }
        $docContent .= "\n";
        
        $docContent .= "**Dependências**:\n";
        foreach ($info['dependencies'] as $dependency) {
            $docContent .= "- $dependency\n";
        }
        $docContent .= "\n";
        
        if (isset($info['methods'])) {
            $docContent .= "**Métodos**:\n";
            foreach ($info['methods'] as $method => $description) {
                $docContent .= "- `$method`: $description\n";
            }
            $docContent .= "\n";
        }
        
        if (isset($info['events'])) {
            $docContent .= "**Eventos**:\n";
            foreach ($info['events'] as $event => $description) {
                $docContent .= "- `$event`: $description\n";
            }
            $docContent .= "\n";
        }
    }
}

$docContent .= "## 4. ENDPOINTS DA API\n";
$docContent .= "---------------------\n\n";

foreach ($apiEndpoints as $category => $endpoints) {
    $docContent .= "### $category\n\n";
    
    foreach ($endpoints as $endpoint => $info) {
        $docContent .= "#### `$info[method]` $endpoint\n";
        $docContent .= "**Descrição**: " . $info['description'] . "\n\n";
        
        if (!empty($info['parameters'])) {
            $docContent .= "**Parâmetros**:\n";
            foreach ($info['parameters'] as $param => $description) {
                $docContent .= "- `$param`: $description\n";
            }
            $docContent .= "\n";
        }
        
        if (!empty($info['response'])) {
            $docContent .= "**Resposta**:\n";
            foreach ($info['response'] as $field => $type) {
                $docContent .= "- `$field`: $type\n";
            }
            $docContent .= "\n";
        }
    }
}

$docContent .= "## 5. BANCO DE DADOS\n";
$docContent .= "-------------------\n\n";

foreach ($databaseSchema as $table => $info) {
    $docContent .= "### `$table`\n";
    $docContent .= "**Descrição**: " . $info['description'] . "\n\n";
    
    $docContent .= "**Colunas**:\n";
    foreach ($info['columns'] as $column => $definition) {
        $docContent .= "- `$column`: $definition\n";
    }
    $docContent .= "\n";
    
    if (!empty($info['indexes'])) {
        $docContent .= "**Índices**:\n";
        foreach ($info['indexes'] as $index => $definition) {
            $docContent .= "- `$index`: $definition\n";
        }
        $docContent .= "\n";
    }
}

file_put_contents($docFile, $docContent);

echo "✓ Documentação técnica salva em: $docFile\n\n";

echo "✅ Geração de documentação técnica concluída!\n";
echo "\nDocumentação gerada:\n";
echo "- $docFile (Markdown)\n";
?>