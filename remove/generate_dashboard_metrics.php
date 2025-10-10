<?php
// generate_dashboard_metrics.php

// Script para gerar um dashboard de métricas do sistema

require_once 'api/config/database.php';
require_once 'api/models/Clipping.php';
require_once 'api/models/Publication.php';
require_once 'api/models/HistoricalEvent.php';
require_once 'api/models/AboutSection.php';
require_once 'api/models/CrmcItem.php';
require_once 'api/models/ArchiveItem.php';

try {
    echo "Gerando dashboard de métricas do sistema...\n\n";
    
    $pdo = connectDatabase();
    
    // 1. Métricas gerais do sistema
    echo "1. Coletando métricas gerais do sistema...\n";
    
    $systemMetrics = [
        'timestamp' => date('Y-m-d H:i:s'),
        'total_records' => 0,
        'active_records' => 0,
        'inactive_records' => 0,
        'content_types' => [],
        'database_size' => 0,
        'last_updated' => date('Y-m-d H:i:s')
    ];
    
    // 2. Métricas por tipo de conteúdo
    echo "2. Coletando métricas por tipo de conteúdo...\n";
    
    $contentTypes = [
        'historical_events' => 'Eventos Históricos',
        'publications' => 'Publicações',
        'archive_items' => 'Itens do Acervo',
        'clippings' => 'Clippings',
        'about_sections' => 'Seções Sobre',
        'crmc_items' => 'Itens do CRMC'
    ];
    
    foreach ($contentTypes as $table => $description) {
        // Total de registros
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
        $totalCount = $stmt->fetch()['count'];
        
        // Registros ativos
        $stmt = $pdo->query("SELECT SUM(is_active = 1) as active FROM `$table`");
        $activeCount = $stmt->fetch()['active'];
        
        // Registros inativos
        $stmt = $pdo->query("SELECT SUM(is_active = 0) as inactive FROM `$table`");
        $inactiveCount = $stmt->fetch()['inactive'];
        
        // Tamanho da tabela
        $stmt = $pdo->query("SELECT ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = 'cccrj_db' AND table_name = '$table'");
        $tableSize = $stmt->fetch()['size_mb'];
        
        // Data da última atualização
        $stmt = $pdo->query("SELECT MAX(updated_at) as last_updated FROM `$table`");
        $lastUpdated = $stmt->fetch()['last_updated'];
        
        $systemMetrics['content_types'][$description] = [
            'total_records' => $totalCount,
            'active_records' => $activeCount,
            'inactive_records' => $inactiveCount,
            'size_mb' => $tableSize,
            'last_updated' => $lastUpdated
        ];
        
        $systemMetrics['total_records'] += $totalCount;
        $systemMetrics['active_records'] += $activeCount;
        $systemMetrics['inactive_records'] += $inactiveCount;
        $systemMetrics['database_size'] += $tableSize;
        
        echo "✓ Métricas coletadas para $description\n";
    }
    
    echo "\n";
    
    // 3. Métricas de crescimento
    echo "3. Coletando métricas de crescimento...\n";
    
    $growthMetrics = [];
    
    foreach ($contentTypes as $table => $description) {
        // Registros nos últimos 7 dias
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table` WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $last7Days = $stmt->fetch()['count'];
        
        // Registros nos últimos 30 dias
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table` WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $last30Days = $stmt->fetch()['count'];
        
        // Registros este mês
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table` WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
        $thisMonth = $stmt->fetch()['count'];
        
        // Registros este ano
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table` WHERE YEAR(created_at) = YEAR(NOW())");
        $thisYear = $stmt->fetch()['count'];
        
        $growthMetrics[$description] = [
            'last_7_days' => $last7Days,
            'last_30_days' => $last30Days,
            'this_month' => $thisMonth,
            'this_year' => $thisYear
        ];
        
        echo "✓ Métricas de crescimento coletadas para $description\n";
    }
    
    echo "\n";
    
    // 4. Métricas de conteúdo por categoria
    echo "4. Coletando métricas de conteúdo por categoria...\n";
    
    $categoryMetrics = [
        'event_types' => [],
        'publication_types' => [],
        'clipping_categories' => [],
        'crmc_categories' => [],
        'about_section_types' => []
    ];
    
    // Tipos de eventos históricos
    $stmt = $pdo->query("SELECT event_type, COUNT(*) as count FROM historical_events GROUP BY event_type ORDER BY count DESC");
    $eventTypes = $stmt->fetchAll();
    foreach ($eventTypes as $type) {
        $categoryMetrics['event_types'][$type['event_type']] = $type['count'];
    }
    
    // Tipos de publicações
    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM publications GROUP BY type ORDER BY count DESC");
    $publicationTypes = $stmt->fetchAll();
    foreach ($publicationTypes as $type) {
        $categoryMetrics['publication_types'][$type['type']] = $type['count'];
    }
    
    // Categorias de clipping
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM clippings GROUP BY category ORDER BY count DESC");
    $clippingCategories = $stmt->fetchAll();
    foreach ($clippingCategories as $category) {
        $categoryMetrics['clipping_categories'][$category['category']] = $category['count'];
    }
    
    // Categorias do CRMC
    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM crmc_items GROUP BY category ORDER BY count DESC");
    $crmcCategories = $stmt->fetchAll();
    foreach ($crmcCategories as $category) {
        $categoryMetrics['crmc_categories'][$category['category']] = $category['count'];
    }
    
    // Tipos de seções sobre
    $stmt = $pdo->query("SELECT section_type, COUNT(*) as count FROM about_sections GROUP BY section_type ORDER BY count DESC");
    $aboutSectionTypes = $stmt->fetchAll();
    foreach ($aboutSectionTypes as $type) {
        $categoryMetrics['about_section_types'][$type['section_type']] = $type['count'];
    }
    
    echo "✓ Métricas de conteúdo por categoria coletadas\n\n";
    
    // 5. Gerar relatório em formato legível
    echo "5. Gerando relatório em formato legível...\n";
    
    $reportsDir = 'reports';
    if (!is_dir($reportsDir)) {
        mkdir($reportsDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d-H-i-s');
    $reportFile = "$reportsDir/dashboard_metrics_$timestamp.txt";
    
    $reportContent = "DASHBOARD DE MÉTRICAS DO SISTEMA CCCRJ\n";
    $reportContent .= "=====================================\n\n";
    $reportContent .= "Data da geração: " . $systemMetrics['timestamp'] . "\n\n";
    
    $reportContent .= "MÉTRICAS GERAIS DO SISTEMA\n";
    $reportContent .= "--------------------------\n";
    $reportContent .= "Total de registros: " . number_format($systemMetrics['total_records']) . "\n";
    $reportContent .= "Registros ativos: " . number_format($systemMetrics['active_records']) . "\n";
    $reportContent .= "Registros inativos: " . number_format($systemMetrics['inactive_records']) . "\n";
    $reportContent .= "Tamanho total do banco de dados: " . number_format($systemMetrics['database_size'], 2) . " MB\n";
    $reportContent .= "Última atualização: " . $systemMetrics['last_updated'] . "\n\n";
    
    $reportContent .= "MÉTRICAS POR TIPO DE CONTEÚDO\n";
    $reportContent .= "-----------------------------\n";
    foreach ($systemMetrics['content_types'] as $type => $metrics) {
        $reportContent .= "$type:\n";
        $reportContent .= "  - Total de registros: " . number_format($metrics['total_records']) . "\n";
        $reportContent .= "  - Registros ativos: " . number_format($metrics['active_records']) . "\n";
        $reportContent .= "  - Registros inativos: " . number_format($metrics['inactive_records']) . "\n";
        $reportContent .= "  - Tamanho: " . number_format($metrics['size_mb'], 2) . " MB\n";
        $reportContent .= "  - Última atualização: " . ($metrics['last_updated'] ?: 'N/A') . "\n\n";
    }
    
    $reportContent .= "MÉTRICAS DE CRESCIMENTO\n";
    $reportContent .= "-----------------------\n";
    foreach ($growthMetrics as $type => $metrics) {
        $reportContent .= "$type:\n";
        $reportContent .= "  - Últimos 7 dias: " . number_format($metrics['last_7_days']) . " registros\n";
        $reportContent .= "  - Últimos 30 dias: " . number_format($metrics['last_30_days']) . " registros\n";
        $reportContent .= "  - Este mês: " . number_format($metrics['this_month']) . " registros\n";
        $reportContent .= "  - Este ano: " . number_format($metrics['this_year']) . " registros\n\n";
    }
    
    $reportContent .= "MÉTRICAS DE CONTEÚDO POR CATEGORIA\n";
    $reportContent .= "----------------------------------\n";
    
    $reportContent .= "Tipos de Eventos Históricos:\n";
    foreach ($categoryMetrics['event_types'] as $type => $count) {
        $reportContent .= "  - $type: " . number_format($count) . " registros\n";
    }
    $reportContent .= "\n";
    
    $reportContent .= "Tipos de Publicações:\n";
    foreach ($categoryMetrics['publication_types'] as $type => $count) {
        $reportContent .= "  - $type: " . number_format($count) . " registros\n";
    }
    $reportContent .= "\n";
    
    $reportContent .= "Categorias de Clipping:\n";
    foreach ($categoryMetrics['clipping_categories'] as $category => $count) {
        $reportContent .= "  - $category: " . number_format($count) . " registros\n";
    }
    $reportContent .= "\n";
    
    $reportContent .= "Categorias do CRMC:\n";
    foreach ($categoryMetrics['crmc_categories'] as $category => $count) {
        $reportContent .= "  - $category: " . number_format($count) . " registros\n";
    }
    $reportContent .= "\n";
    
    $reportContent .= "Tipos de Seções Sobre:\n";
    foreach ($categoryMetrics['about_section_types'] as $type => $count) {
        $reportContent .= "  - $type: " . number_format($count) . " registros\n";
    }
    $reportContent .= "\n";
    
    file_put_contents($reportFile, $reportContent);
    
    echo "✓ Relatório salvo em: $reportFile\n\n";
    
    // 6. Gerar dashboard em formato HTML
    echo "6. Gerando dashboard em formato HTML...\n";
    
    $dashboardFile = "$reportsDir/dashboard_metrics_$timestamp.html";
    
    $dashboardContent = "<!DOCTYPE html>
<html lang=\"pt-BR\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Dashboard de Métricas do Sistema CCCRJ</title>
    <script src=\"https://cdn.tailwindcss.com\"></script>
    <link href=\"https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap\" rel=\"stylesheet\">
    <link href=\"https://fonts.googleapis.com/icon?family=Material+Icons\" rel=\"stylesheet\">
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #8B2635 0%, #992D3D 50%, #A63545 100%);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(139, 38, 53, 0.25);
        }
        
        .price-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(245, 240, 232, 0.9);
            border: 1px solid rgba(139, 38, 53, 0.1);
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class=\"bg-[#F5F0E8] min-h-screen\">
    <div class=\"container mx-auto px-4 py-8\">
        <div class=\"text-center mb-12\">
            <h1 class=\"text-4xl font-bold text-[#6B4423] mb-4\">Dashboard de Métricas do Sistema CCCRJ</h1>
            <p class=\"text-xl text-[#8B2635]\">Visão geral do desempenho e estado do sistema</p>
            <p class=\"text-sm text-[#6B4423] mt-2\">Atualizado em: " . $systemMetrics['timestamp'] . "</p>
        </div>
        
        <!-- Cards de Métricas Principais -->
        <div class=\"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12\">
            <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover\">
                <div class=\"flex items-center justify-between mb-4\">
                    <div class=\"p-3 bg-[#8B2635] bg-opacity-10 rounded-lg\">
                        <span class=\"material-icons text-[#8B2635] text-2xl\">storage</span>
                    </div>
                    <div class=\"text-right\">
                        <p class=\"text-sm text-[#8B2635]\">Registros</p>
                        <p class=\"text-2xl font-bold text-[#6B4423]\">" . number_format($systemMetrics['total_records']) . "</p>
                    </div>
                </div>
                <div class=\"progress-bar bg-gray-200 h-2 rounded-full\">
                    <div class=\"progress-fill bg-[#8B2635] h-full rounded-full\" style=\"width: 100%\"></div>
                </div>
                <p class=\"text-xs text-[#6B4423] mt-2\">Total de registros no sistema</p>
            </div>
            
            <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover\">
                <div class=\"flex items-center justify-between mb-4\">
                    <div class=\"p-3 bg-[#4A6B8A] bg-opacity-10 rounded-lg\">
                        <span class=\"material-icons text-[#4A6B8A] text-2xl\">check_circle</span>
                    </div>
                    <div class=\"text-right\">
                        <p class=\"text-sm text-[#4A6B8A]\">Ativos</p>
                        <p class=\"text-2xl font-bold text-[#6B4423]\">" . number_format($systemMetrics['active_records']) . "</p>
                    </div>
                </div>
                <div class=\"progress-bar bg-gray-200 h-2 rounded-full\">
                    <div class=\"progress-fill bg-[#4A6B8A] h-full rounded-full\" style=\"width: " . round(($systemMetrics['active_records'] / $systemMetrics['total_records']) * 100) . "%\"></div>
                </div>
                <p class=\"text-xs text-[#6B4423] mt-2\">Registros ativos (" . round(($systemMetrics['active_records'] / $systemMetrics['total_records']) * 100) . "%)</p>
            </div>
            
            <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover\">
                <div class=\"flex items-center justify-between mb-4\">
                    <div class=\"p-3 bg-[#D4A574] bg-opacity-10 rounded-lg\">
                        <span class=\"material-icons text-[#D4A574] text-2xl\">database</span>
                    </div>
                    <div class=\"text-right\">
                        <p class=\"text-sm text-[#D4A574]\">Tamanho</p>
                        <p class=\"text-2xl font-bold text-[#6B4423]\">" . number_format($systemMetrics['database_size'], 2) . " MB</p>
                    </div>
                </div>
                <div class=\"progress-bar bg-gray-200 h-2 rounded-full\">
                    <div class=\"progress-fill bg-[#D4A574] h-full rounded-full\" style=\"width: " . min(100, round($systemMetrics['database_size'])) . "%\"></div>
                </div>
                <p class=\"text-xs text-[#6B4423] mt-2\">Tamanho total do banco de dados</p>
            </div>
            
            <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover\">
                <div class=\"flex items-center justify-between mb-4\">
                    <div class=\"p-3 bg-[#6B4423] bg-opacity-10 rounded-lg\">
                        <span class=\"material-icons text-[#6B4423] text-2xl\">update</span>
                    </div>
                    <div class=\"text-right\">
                        <p class=\"text-sm text-[#6B4423]\">Atualização</p>
                        <p class=\"text-2xl font-bold text-[#6B4423]\">" . date('d/m/Y', strtotime($systemMetrics['last_updated'])) . "</p>
                    </div>
                </div>
                <div class=\"progress-bar bg-gray-200 h-2 rounded-full\">
                    <div class=\"progress-fill bg-[#6B4423] h-full rounded-full\" style=\"width: 100%\"></div>
                </div>
                <p class=\"text-xs text-[#6B4423] mt-2\">Última atualização do sistema</p>
            </div>
        </div>
        
        <!-- Gráficos de Crescimento -->
        <div class=\"grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12\">
            <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover\">
                <h3 class=\"text-xl font-bold text-[#6B4423] mb-6\">Crescimento nos Últimos 30 Dias</h3>
                <div class=\"space-y-4\">
                    " . generateGrowthChart($growthMetrics) . "
                </div>
            </div>
            
            <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover\">
                <h3 class=\"text-xl font-bold text-[#6B4423] mb-6\">Distribuição por Tipo de Conteúdo</h3>
                <div class=\"space-y-4\">
                    " . generateContentTypeChart($systemMetrics['content_types']) . "
                </div>
            </div>
        </div>
        
        <!-- Métricas Detalhadas -->
        <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover mb-12\">
            <h3 class=\"text-xl font-bold text-[#6B4423] mb-6\">Métricas Detalhadas por Tipo de Conteúdo</h3>
            <div class=\"overflow-x-auto\">
                <table class=\"min-w-full divide-y divide-[#F5F0E8]\">
                    <thead class=\"bg-[#F5F0E8]\">
                        <tr>
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-[#6B4423] uppercase tracking-wider\">Tipo de Conteúdo</th>
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-[#6B4423] uppercase tracking-wider\">Total</th>
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-[#6B4423] uppercase tracking-wider\">Ativos</th>
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-[#6B4423] uppercase tracking-wider\">Inativos</th>
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-[#6B4423] uppercase tracking-wider\">Tamanho (MB)</th>
                            <th class=\"px-6 py-3 text-left text-xs font-medium text-[#6B4423] uppercase tracking-wider\">Última Atualização</th>
                        </tr>
                    </thead>
                    <tbody class=\"bg-white divide-y divide-[#F5F0E8]\">
                        " . generateContentDetailsTable($systemMetrics['content_types']) . "
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Métricas por Categoria -->
        <div class=\"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12\">
            <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover\">
                <h3 class=\"text-xl font-bold text-[#6B4423] mb-6\">Tipos de Eventos Históricos</h3>
                <div class=\"space-y-3\">
                    " . generateCategoryList($categoryMetrics['event_types']) . "
                </div>
            </div>
            
            <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover\">
                <h3 class=\"text-xl font-bold text-[#6B4423] mb-6\">Tipos de Publicações</h3>
                <div class=\"space-y-3\">
                    " . generateCategoryList($categoryMetrics['publication_types']) . "
                </div>
            </div>
            
            <div class=\"metric-card bg-white rounded-2xl shadow-lg p-6 border border-[#F5F0E8] card-hover\">
                <h3 class=\"text-xl font-bold text-[#6B4423] mb-6\">Categorias de Clipping</h3>
                <div class=\"space-y-3\">
                    " . generateCategoryList($categoryMetrics['clipping_categories']) . "
                </div>
            </div>
        </div>
        
        <!-- Rodapé -->
        <div class=\"text-center py-8 border-t border-[#F5F0E8]\">
            <p class=\"text-sm text-[#6B4423]\">Dashboard de Métricas do Sistema CCCRJ - Atualizado em " . $systemMetrics['timestamp'] . "</p>
            <p class=\"text-xs text-[#8B2635] mt-2\">© " . date('Y') . " Centro de Comércio do Café do Rio de Janeiro</p>
        </div>
    </div>
</body>
</html>";

    file_put_contents($dashboardFile, $dashboardContent);
    
    echo "✓ Dashboard salvo em: $dashboardFile\n\n";
    
    echo "✅ Geração de dashboard de métricas concluída!\n";
    echo "\nRelatórios gerados:\n";
    echo "- $reportFile (Texto)\n";
    echo "- $dashboardFile (HTML)\n";
    
} catch (Exception $e) {
    echo "✗ Erro ao gerar dashboard de métricas: " . $e->getMessage() . "\n";
}

// Funções auxiliares para gerar conteúdo do dashboard
function generateGrowthChart($growthMetrics) {
    $html = '';
    
    foreach ($growthMetrics as $type => $metrics) {
        $total = $metrics['last_30_days'];
        $last7DaysPercent = $total > 0 ? round(($metrics['last_7_days'] / $total) * 100) : 0;
        $thisMonthPercent = $total > 0 ? round(($metrics['this_month'] / $total) * 100) : 0;
        $thisYearPercent = $total > 0 ? round(($metrics['this_year'] / $total) * 100) : 0;
        
        $html .= "
            <div class=\"mb-4\">
                <div class=\"flex justify-between mb-1\">
                    <span class=\"text-sm text-[#6B4423]\">$type</span>
                    <span class=\"text-sm font-medium text-[#8B2635]\">" . number_format($total) . " registros</span>
                </div>
                <div class=\"flex space-x-1\">
                    <div class=\"flex-1 bg-gray-200 rounded-full h-2\">
                        <div class=\"bg-[#8B2635] h-2 rounded-full\" style=\"width: {$last7DaysPercent}%\"></div>
                    </div>
                    <div class=\"flex-1 bg-gray-200 rounded-full h-2\">
                        <div class=\"bg-[#4A6B8A] h-2 rounded-full\" style=\"width: {$thisMonthPercent}%\"></div>
                    </div>
                    <div class=\"flex-1 bg-gray-200 rounded-full h-2\">
                        <div class=\"bg-[#D4A574] h-2 rounded-full\" style=\"width: {$thisYearPercent}%\"></div>
                    </div>
                </div>
                <div class=\"flex justify-between text-xs text-[#8B2635] mt-1\">
                    <span>7 dias: " . number_format($metrics['last_7_days']) . "</span>
                    <span>Mês: " . number_format($metrics['this_month']) . "</span>
                    <span>Ano: " . number_format($metrics['this_year']) . "</span>
                </div>
            </div>
        ";
    }
    
    return $html;
}

function generateContentTypeChart($contentTypes) {
    $html = '';
    
    foreach ($contentTypes as $type => $metrics) {
        $percent = round(($metrics['total_records'] / array_sum(array_column($contentTypes, 'total_records'))) * 100);
        
        $html .= "
            <div class=\"mb-4\">
                <div class=\"flex justify-between mb-1\">
                    <span class=\"text-sm text-[#6B4423]\">$type</span>
                    <span class=\"text-sm font-medium text-[#8B2635]\">{$percent}%</span>
                </div>
                <div class=\"bg-gray-200 rounded-full h-2\">
                    <div class=\"bg-gradient-to-r from-[#8B2635] to-[#992D3D] h-2 rounded-full\" style=\"width: {$percent}%\"></div>
                </div>
                <p class=\"text-xs text-[#8B2635] mt-1\">" . number_format($metrics['total_records']) . " registros</p>
            </div>
        ";
    }
    
    return $html;
}

function generateContentDetailsTable($contentTypes) {
    $html = '';
    
    foreach ($contentTypes as $type => $metrics) {
        $html .= "
            <tr>
                <td class=\"px-6 py-4 whitespace-nowrap text-sm font-medium text-[#6B4423]\">$type</td>
                <td class=\"px-6 py-4 whitespace-nowrap text-sm text-[#8B2635]\">" . number_format($metrics['total_records']) . "</td>
                <td class=\"px-6 py-4 whitespace-nowrap text-sm text-[#4A6B8A]\">" . number_format($metrics['active_records']) . "</td>
                <td class=\"px-6 py-4 whitespace-nowrap text-sm text-[#D4A574]\">" . number_format($metrics['inactive_records']) . "</td>
                <td class=\"px-6 py-4 whitespace-nowrap text-sm text-[#6B4423]\">" . number_format($metrics['size_mb'], 2) . "</td>
                <td class=\"px-6 py-4 whitespace-nowrap text-sm text-[#8B2635]\">" . ($metrics['last_updated'] ? date('d/m/Y', strtotime($metrics['last_updated'])) : 'N/A') . "</td>
            </tr>
        ";
    }
    
    return $html;
}

function generateCategoryList($categories) {
    $html = '';
    
    // Ordenar por quantidade (descendente)
    arsort($categories);
    
    foreach ($categories as $category => $count) {
        $html .= "
            <div class=\"flex justify-between items-center p-2 bg-[#F5F0E8] bg-opacity-50 rounded-lg\">
                <span class=\"text-sm text-[#6B4423]\">$category</span>
                <span class=\"text-sm font-medium text-[#8B2635]\">" . number_format($count) . "</span>
            </div>
        ";
    }
    
    return $html;
}
?>