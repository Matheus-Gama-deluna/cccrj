<?php
// verify_project_files.php

// Script para verificar se todos os arquivos do projeto foram criados corretamente

echo "Verificando arquivos do projeto CCCRJ...\n\n";

// 1. Verificar diretórios principais
echo "1. Verificando diretórios principais...\n";

$mainDirs = [
    'api' => 'API em PHP puro',
    'api/config' => 'Configurações da API',
    'api/models' => 'Modelos de dados',
    'api/utils' => 'Funções utilitárias',
    'api/clipping' => 'Endpoints para clipping',
    'api/publications' => 'Endpoints para publicações',
    'api/history' => 'Endpoints para história',
    'api/about' => 'Endpoints para seção sobre',
    'api/crmc' => 'Endpoints para CRMC',
    'api/archive' => 'Endpoints para acervo',
    'assets' => 'Recursos frontend',
    'assets/js' => 'Scripts JavaScript',
    'assets/js/components' => 'Componentes JavaScript',
    'assets/css' => 'Folhas de estilo',
    'scraping_cccrj' => 'Conteúdo raspado',
    'scraping_cccrj/cccrj_content' => 'Conteúdo HTML raspado',
    'uploads' => 'Arquivos enviados',
    'reports' => 'Relatórios gerados'
];

$missingDirs = [];
foreach ($mainDirs as $dir => $description) {
    if (!is_dir($dir)) {
        $missingDirs[$dir] = $description;
    }
}

if (empty($missingDirs)) {
    echo "✓ Todos os diretórios principais estão presentes\n";
} else {
    echo "✗ Diretórios principais faltando:\n";
    foreach ($missingDirs as $dir => $description) {
        echo "  - $description ($dir)\n";
    }
}

echo "\n";

// 2. Verificar arquivos de configuração
echo "2. Verificando arquivos de configuração...\n";

$configFiles = [
    'api/config/database.php' => 'Configuração do banco de dados',
    'api/config/auth.php' => 'Configuração de autenticação',
    'api/utils/functions.php' => 'Funções utilitárias'
];

$missingConfigFiles = [];
foreach ($configFiles as $file => $description) {
    if (!file_exists($file)) {
        $missingConfigFiles[$file] = $description;
    }
}

if (empty($missingConfigFiles)) {
    echo "✓ Todos os arquivos de configuração estão presentes\n";
} else {
    echo "✗ Arquivos de configuração faltando:\n";
    foreach ($missingConfigFiles as $file => $description) {
        echo "  - $description ($file)\n";
    }
}

echo "\n";

// 3. Verificar modelos de dados
echo "3. Verificando modelos de dados...\n";

$modelFiles = [
    'api/models/Clipping.php' => 'Modelo Clipping',
    'api/models/Publication.php' => 'Modelo Publication',
    'api/models/HistoricalEvent.php' => 'Modelo HistoricalEvent',
    'api/models/AboutSection.php' => 'Modelo AboutSection',
    'api/models/CrmcItem.php' => 'Modelo CrmcItem',
    'api/models/ArchiveItem.php' => 'Modelo ArchiveItem'
];

$missingModelFiles = [];
foreach ($modelFiles as $file => $description) {
    if (!file_exists($file)) {
        $missingModelFiles[$file] = $description;
    }
}

if (empty($missingModelFiles)) {
    echo "✓ Todos os modelos de dados estão presentes\n";
} else {
    echo "✗ Modelos de dados faltando:\n";
    foreach ($missingModelFiles as $file => $description) {
        echo "  - $description ($file)\n";
    }
}

echo "\n";

// 4. Verificar endpoints da API
echo "4. Verificando endpoints da API...\n";

$apiEndpoints = [
    'api/clipping/list.php' => 'Listagem de clipping',
    'api/clipping/details.php' => 'Detalhes de clipping',
    'api/publications/list.php' => 'Listagem de publicações',
    'api/publications/details.php' => 'Detalhes de publicação',
    'api/history/list.php' => 'Listagem de eventos históricos',
    'api/history/details.php' => 'Detalhes de evento histórico',
    'api/about/list.php' => 'Listagem de seções sobre',
    'api/about/details.php' => 'Detalhes de seção sobre',
    'api/crmc/list.php' => 'Listagem de itens do CRMC',
    'api/crmc/details.php' => 'Detalhes de item do CRMC',
    'api/archive/list.php' => 'Listagem de itens do acervo',
    'api/archive/details.php' => 'Detalhes de item do acervo'
];

$missingApiEndpoints = [];
foreach ($apiEndpoints as $file => $description) {
    if (!file_exists($file)) {
        $missingApiEndpoints[$file] = $description;
    }
}

if (empty($missingApiEndpoints)) {
    echo "✓ Todos os endpoints da API estão presentes\n";
} else {
    echo "✗ Endpoints da API faltando:\n";
    foreach ($missingApiEndpoints as $file => $description) {
        echo "  - $description ($file)\n";
    }
}

echo "\n";

// 5. Verificar componentes JavaScript
echo "5. Verificando componentes JavaScript...\n";

$jsComponents = [
    'assets/js/components/quotes.js' => 'Componente de Cotações',
    'assets/js/components/news.js' => 'Componente de Notícias',
    'assets/js/components/reports.js' => 'Componente de Relatórios',
    'assets/js/components/calculator.js' => 'Componente de Calculadora',
    'assets/js/components/clipping.js' => 'Componente de Clipping',
    'assets/js/components/publications.js' => 'Componente de Publicações',
    'assets/js/components/history.js' => 'Componente de História',
    'assets/js/components/about.js' => 'Componente Sobre Nós',
    'assets/js/components/crmc.js' => 'Componente CRMC',
    'assets/js/components/archive.js' => 'Componente de Acervo'
];

$missingJsComponents = [];
foreach ($jsComponents as $file => $description) {
    if (!file_exists($file)) {
        $missingJsComponents[$file] = $description;
    }
}

if (empty($missingJsComponents)) {
    echo "✓ Todos os componentes JavaScript estão presentes\n";
} else {
    echo "✗ Componentes JavaScript faltando:\n";
    foreach ($missingJsComponents as $file => $description) {
        echo "  - $description ($file)\n";
    }
}

echo "\n";

// 6. Verificar scripts de manutenção
echo "6. Verificando scripts de manutenção...\n";

$maintenanceScripts = [
    'db_setup.php' => 'Configuração do banco de dados',
    'populate_db.php' => 'População do banco de dados',
    'migrate_scraped_content.php' => 'Migração de conteúdo raspado',
    'generate_content_statistics.php' => 'Geração de estatísticas de conteúdo',
    'export_populated_content.php' => 'Exportação de conteúdo populado',
    'import_exported_content.php' => 'Importação de conteúdo exportado',
    'check_database.php' => 'Verificação do banco de dados',
    'test_database_connection.php' => 'Teste de conexão com o banco de dados',
    'create_database_tables.php' => 'Criação de tabelas do banco de dados',
    'populate_from_scraped_content.php' => 'População com conteúdo raspado',
    'verify_implementation.php' => 'Verificação da implementação',
    'generate_final_project_report.php' => 'Geração do relatório final do projeto',
    'verify_project_files.php' => 'Verificação de arquivos do projeto'
];

$missingMaintenanceScripts = [];
foreach ($maintenanceScripts as $file => $description) {
    if (!file_exists($file)) {
        $missingMaintenanceScripts[$file] = $description;
    }
}

if (empty($missingMaintenanceScripts)) {
    echo "✓ Todos os scripts de manutenção estão presentes\n";
} else {
    echo "✗ Scripts de manutenção faltando:\n";
    foreach ($missingMaintenanceScripts as $file => $description) {
        echo "  - $description ($file)\n";
    }
}

echo "\n";

// 7. Verificar arquivos principais
echo "7. Verificando arquivos principais...\n";

$mainFiles = [
    'index.html' => 'Página principal',
    'main.js' => 'Script principal',
    'style.css' => 'Folha de estilo principal',
    'README.md' => 'Documentação principal',
    'RESUMO_IMPLEMENTACAO.md' => 'Resumo da implementação',
    'implementacao_site_original.md' => 'Plano de implementação'
];

$missingMainFiles = [];
foreach ($mainFiles as $file => $description) {
    if (!file_exists($file)) {
        $missingMainFiles[$file] = $description;
    }
}

if (empty($missingMainFiles)) {
    echo "✓ Todos os arquivos principais estão presentes\n";
} else {
    echo "✗ Arquivos principais faltando:\n";
    foreach ($missingMainFiles as $file => $description) {
        echo "  - $description ($file)\n";
    }
}

echo "\n";

// 8. Resumo da verificação
echo "8. Resumo da verificação...\n";

$totalChecks = 7;
$passedChecks = 0;

if (empty($missingDirs)) $passedChecks++;
if (empty($missingConfigFiles)) $passedChecks++;
if (empty($missingModelFiles)) $passedChecks++;
if (empty($missingApiEndpoints)) $passedChecks++;
if (empty($missingJsComponents)) $passedChecks++;
if (empty($missingMaintenanceScripts)) $passedChecks++;
if (empty($missingMainFiles)) $passedChecks++;

$completionPercentage = round(($passedChecks / $totalChecks) * 100, 2);

echo "✅ Verificação concluída!\n";
echo "\nResultado da verificação:\n";
echo "- Verificações realizadas: $totalChecks\n";
echo "- Verificações aprovadas: $passedChecks\n";
echo "- Taxa de conclusão: $completionPercentage%\n";

if ($completionPercentage == 100) {
    echo "\n🎉 Parabéns! Todos os arquivos do projeto foram criados com sucesso!\n";
    echo "O sistema CCCRJ está pronto para uso.\n";
} else if ($completionPercentage >= 80) {
    echo "\n👍 Bom trabalho! A maioria dos arquivos do projeto foi criada com sucesso.\n";
    echo "Alguns arquivos ainda precisam ser criados.\n";
} else {
    echo "\n⚠️ A criação dos arquivos do projeto ainda está em andamento.\n";
    echo "Continue trabalhando nos arquivos faltantes.\n";
}

echo "\nPróximos passos:\n";
echo "1. Execute os scripts de configuração do banco de dados\n";
echo "2. Popule o banco de dados com conteúdo raspado\n";
echo "3. Teste a integração completa do sistema\n";
echo "4. Verifique a funcionalidade de todos os componentes\n";
echo "5. Otimize o desempenho e faça ajustes finos\n";
?>