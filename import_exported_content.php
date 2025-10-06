<?php
// import_exported_content.php

// Script para importar conteúdo exportado de volta para o banco de dados

require_once 'api/config/database.php';
require_once 'api/models/Clipping.php';
require_once 'api/models/Publication.php';
require_once 'api/models/HistoricalEvent.php';
require_once 'api/models/AboutSection.php';
require_once 'api/models/CrmcItem.php';
require_once 'api/models/ArchiveItem.php';

try {
    echo "Importando conteúdo exportado para o banco de dados...\n\n";
    
    // Verificar se foi fornecido um diretório de exportação
    if ($argc < 2) {
        echo "Uso: php import_exported_content.php <diretorio_de_exportacao>\n";
        echo "Exemplo: php import_exported_content.php exports/export-2023-01-01-12-00-00\n";
        exit(1);
    }
    
    $exportDir = $argv[1];
    
    if (!is_dir($exportDir)) {
        echo "✗ Diretório de exportação não encontrado: $exportDir\n";
        exit(1);
    }
    
    echo "Importando conteúdo de: $exportDir\n\n";
    
    $pdo = connectDatabase();
    
    // 1. Verificar manifesto da exportação
    echo "1. Verificando manifesto da exportação...\n";
    
    $manifestFile = "$exportDir/manifest.json";
    if (!file_exists($manifestFile)) {
        echo "✗ Manifesto da exportação não encontrado: $manifestFile\n";
        exit(1);
    }
    
    $manifest = json_decode(file_get_contents($manifestFile), true);
    if (!$manifest) {
        echo "✗ Erro ao ler manifesto da exportação\n";
        exit(1);
    }
    
    echo "✓ Manifesto da exportação válido\n";
    echo "  Data da exportação: " . $manifest['export_date'] . "\n";
    echo "  Total de registros: " . array_sum($manifest['total_records']) . "\n";
    
    echo "\n";
    
    // 2. Importar eventos históricos
    echo "2. Importando eventos históricos...\n";
    
    $eventsFile = "$exportDir/historical_events.json";
    if (file_exists($eventsFile)) {
        $events = json_decode(file_get_contents($eventsFile), true);
        if ($events) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE historical_events");
            
            $historicalEventModel = new HistoricalEvent();
            $importedCount = 0;
            
            foreach ($events as $event) {
                try {
                    $historicalEventModel->create($event);
                    $importedCount++;
                } catch (Exception $e) {
                    echo "✗ Erro ao importar evento histórico: " . $e->getMessage() . "\n";
                }
            }
            
            echo "✓ $importedCount eventos históricos importados\n";
        } else {
            echo "✗ Erro ao ler arquivo de eventos históricos\n";
        }
    } else {
        echo "✗ Arquivo de eventos históricos não encontrado\n";
    }
    
    echo "\n";
    
    // 3. Importar publicações
    echo "3. Importando publicações...\n";
    
    $publicationsFile = "$exportDir/publications.json";
    if (file_exists($publicationsFile)) {
        $publications = json_decode(file_get_contents($publicationsFile), true);
        if ($publications) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE publications");
            
            $publicationModel = new Publication();
            $importedCount = 0;
            
            foreach ($publications as $pub) {
                try {
                    $publicationModel->create($pub);
                    $importedCount++;
                } catch (Exception $e) {
                    echo "✗ Erro ao importar publicação: " . $e->getMessage() . "\n";
                }
            }
            
            echo "✓ $importedCount publicações importadas\n";
        } else {
            echo "✗ Erro ao ler arquivo de publicações\n";
        }
    } else {
        echo "✗ Arquivo de publicações não encontrado\n";
    }
    
    echo "\n";
    
    // 4. Importar itens do acervo
    echo "4. Importando itens do acervo...\n";
    
    $archiveFile = "$exportDir/archive_items.json";
    if (file_exists($archiveFile)) {
        $archiveItems = json_decode(file_get_contents($archiveFile), true);
        if ($archiveItems) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE archive_items");
            
            $archiveItemModel = new ArchiveItem();
            $importedCount = 0;
            
            foreach ($archiveItems as $item) {
                try {
                    $archiveItemModel->create($item);
                    $importedCount++;
                } catch (Exception $e) {
                    echo "✗ Erro ao importar item do acervo: " . $e->getMessage() . "\n";
                }
            }
            
            echo "✓ $importedCount itens do acervo importados\n";
        } else {
            echo "✗ Erro ao ler arquivo de itens do acervo\n";
        }
    } else {
        echo "✗ Arquivo de itens do acervo não encontrado\n";
    }
    
    echo "\n";
    
    // 5. Importar clippings
    echo "5. Importando clippings...\n";
    
    $clippingsFile = "$exportDir/clippings.json";
    if (file_exists($clippingsFile)) {
        $clippings = json_decode(file_get_contents($clippingsFile), true);
        if ($clippings) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE clippings");
            
            $clippingModel = new Clipping();
            $importedCount = 0;
            
            foreach ($clippings as $clip) {
                try {
                    $clippingModel->create($clip);
                    $importedCount++;
                } catch (Exception $e) {
                    echo "✗ Erro ao importar clipping: " . $e->getMessage() . "\n";
                }
            }
            
            echo "✓ $importedCount clippings importados\n";
        } else {
            echo "✗ Erro ao ler arquivo de clippings\n";
        }
    } else {
        echo "✗ Arquivo de clippings não encontrado\n";
    }
    
    echo "\n";
    
    // 6. Importar seções sobre
    echo "6. Importando seções sobre...\n";
    
    $aboutFile = "$exportDir/about_sections.json";
    if (file_exists($aboutFile)) {
        $aboutSections = json_decode(file_get_contents($aboutFile), true);
        if ($aboutSections) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE about_sections");
            
            $aboutSectionModel = new AboutSection();
            $importedCount = 0;
            
            foreach ($aboutSections as $section) {
                try {
                    $aboutSectionModel->create($section);
                    $importedCount++;
                } catch (Exception $e) {
                    echo "✗ Erro ao importar seção sobre: " . $e->getMessage() . "\n";
                }
            }
            
            echo "✓ $importedCount seções sobre importadas\n";
        } else {
            echo "✗ Erro ao ler arquivo de seções sobre\n";
        }
    } else {
        echo "✗ Arquivo de seções sobre não encontrado\n";
    }
    
    echo "\n";
    
    // 7. Importar itens do CRMC
    echo "7. Importando itens do CRMC...\n";
    
    $crmcFile = "$exportDir/crmc_items.json";
    if (file_exists($crmcFile)) {
        $crmcItems = json_decode(file_get_contents($crmcFile), true);
        if ($crmcItems) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE crmc_items");
            
            $crmcItemModel = new CrmcItem();
            $importedCount = 0;
            
            foreach ($crmcItems as $item) {
                try {
                    $crmcItemModel->create($item);
                    $importedCount++;
                } catch (Exception $e) {
                    echo "✗ Erro ao importar item do CRMC: " . $e->getMessage() . "\n";
                }
            }
            
            echo "✓ $importedCount itens do CRMC importados\n";
        } else {
            echo "✗ Erro ao ler arquivo de itens do CRMC\n";
        }
    } else {
        echo "✗ Arquivo de itens do CRMC não encontrado\n";
    }
    
    echo "\n";
    
    echo "✅ Importação concluída!\n";
    echo "\nConteúdo importado com sucesso!\n";
    
} catch (Exception $e) {
    echo "✗ Erro durante a importação: " . $e->getMessage() . "\n";
}
?>