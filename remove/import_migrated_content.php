<?php
// import_migrated_content.php

// Script para importar conteúdo de backup

require_once 'api/config/database.php';

try {
    echo "Importando conteúdo de backup...\n\n";
    
    // Verificar se foi fornecido um diretório de importação
    if ($argc < 2) {
        echo "Uso: php import_migrated_content.php <diretorio_de_backup>\n";
        echo "Exemplo: php import_migrated_content.php exports/export-2023-01-01-12-00-00\n";
        exit(1);
    }
    
    $importDir = $argv[1];
    
    if (!is_dir($importDir)) {
        echo "✗ Diretório de backup não encontrado: $importDir\n";
        exit(1);
    }
    
    echo "Importando conteúdo de: $importDir\n\n";
    
    $pdo = connectDatabase();
    $pdo->exec("USE cccrj_db");
    
    // 1. Verificar manifesto da importação
    echo "1. Verificando manifesto da importação...\n";
    
    $manifestFile = "$importDir/manifest.json";
    if (!file_exists($manifestFile)) {
        echo "✗ Manifesto da importação não encontrado: $manifestFile\n";
        exit(1);
    }
    
    $manifest = json_decode(file_get_contents($manifestFile), true);
    if (!$manifest) {
        echo "✗ Erro ao ler manifesto da importação\n";
        exit(1);
    }
    
    echo "✓ Manifesto da importação válido\n";
    echo "  Data da exportação: " . $manifest['export_date'] . "\n";
    echo "  Total de registros: " . array_sum($manifest['total_records']) . "\n";
    
    echo "\n";
    
    // 2. Importar eventos históricos
    echo "2. Importando eventos históricos...\n";
    
    $eventsFile = "$importDir/historical_events.json";
    if (file_exists($eventsFile)) {
        $events = json_decode(file_get_contents($eventsFile), true);
        if ($events) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE historical_events");
            
            $stmt = $pdo->prepare("
                INSERT INTO historical_events 
                (title, description, content, date, event_type, image_url, is_featured, is_active, created_at, updated_at) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $importedCount = 0;
            foreach ($events as $event) {
                try {
                    $stmt->execute([
                        $event['title'],
                        $event['description'],
                        $event['content'],
                        $event['date'],
                        $event['event_type'],
                        $event['image_url'],
                        $event['is_featured'],
                        $event['is_active'],
                        $event['created_at'],
                        $event['updated_at']
                    ]);
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
    
    $publicationsFile = "$importDir/publications.json";
    if (file_exists($publicationsFile)) {
        $publications = json_decode(file_get_contents($publicationsFile), true);
        if ($publications) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE publications");
            
            $stmt = $pdo->prepare("
                INSERT INTO publications 
                (title, description, file_path, date, number, type, is_active, created_at, updated_at) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $importedCount = 0;
            foreach ($publications as $pub) {
                try {
                    $stmt->execute([
                        $pub['title'],
                        $pub['description'],
                        $pub['file_path'],
                        $pub['date'],
                        $pub['number'],
                        $pub['type'],
                        $pub['is_active'],
                        $pub['created_at'],
                        $pub['updated_at']
                    ]);
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
    
    $archiveFile = "$importDir/archive_items.json";
    if (file_exists($archiveFile)) {
        $archiveItems = json_decode(file_get_contents($archiveFile), true);
        if ($archiveItems) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE archive_items");
            
            $stmt = $pdo->prepare("
                INSERT INTO archive_items 
                (title, description, file_path, date, item_type, category, metadata, is_active, created_at, updated_at) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $importedCount = 0;
            foreach ($archiveItems as $item) {
                try {
                    $stmt->execute([
                        $item['title'],
                        $item['description'],
                        $item['file_path'],
                        $item['date'],
                        $item['item_type'],
                        $item['category'],
                        $item['metadata'],
                        $item['is_active'],
                        $item['created_at'],
                        $item['updated_at']
                    ]);
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
    
    $clippingsFile = "$importDir/clippings.json";
    if (file_exists($clippingsFile)) {
        $clippings = json_decode(file_get_contents($clippingsFile), true);
        if ($clippings) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE clippings");
            
            $stmt = $pdo->prepare("
                INSERT INTO clippings 
                (title, summary, content, source_url, date, category, is_active, created_at, updated_at) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $importedCount = 0;
            foreach ($clippings as $clip) {
                try {
                    $stmt->execute([
                        $clip['title'],
                        $clip['summary'],
                        $clip['content'],
                        $clip['source_url'],
                        $clip['date'],
                        $clip['category'],
                        $clip['is_active'],
                        $clip['created_at'],
                        $clip['updated_at']
                    ]);
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
    
    $aboutFile = "$importDir/about_sections.json";
    if (file_exists($aboutFile)) {
        $aboutSections = json_decode(file_get_contents($aboutFile), true);
        if ($aboutSections) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE about_sections");
            
            $stmt = $pdo->prepare("
                INSERT INTO about_sections 
                (title, content, section_type, `order`, is_active, image_url, created_at, updated_at) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $importedCount = 0;
            foreach ($aboutSections as $section) {
                try {
                    $stmt->execute([
                        $section['title'],
                        $section['content'],
                        $section['section_type'],
                        $section['order'],
                        $section['is_active'],
                        $section['image_url'],
                        $section['created_at'],
                        $section['updated_at']
                    ]);
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
    
    $crmcFile = "$importDir/crmc_items.json";
    if (file_exists($crmcFile)) {
        $crmcItems = json_decode(file_get_contents($crmcFile), true);
        if ($crmcItems) {
            // Limpar tabela antes de importar
            $pdo->exec("TRUNCATE TABLE crmc_items");
            
            $stmt = $pdo->prepare("
                INSERT INTO crmc_items 
                (title, description, content, category, image_url, file_path, is_active, created_at, updated_at) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $importedCount = 0;
            foreach ($crmcItems as $item) {
                try {
                    $stmt->execute([
                        $item['title'],
                        $item['description'],
                        $item['content'],
                        $item['category'],
                        $item['image_url'],
                        $item['file_path'],
                        $item['is_active'],
                        $item['created_at'],
                        $item['updated_at']
                    ]);
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