<?php
// generate_quality_report.php

// Script para gerar um relatório de qualidade de código

echo "Gerando relatório de qualidade de código...\n\n";

// 1. Verificar qualidade do código PHP
echo "1. Verificando qualidade do código PHP...\n";

function checkPhpQuality($directory) {
    $qualityReport = [
        'files' => 0,
        'errors' => 0,
        'warnings' => 0,
        'syntax_issues' => 0,
        'security_issues' => 0,
        'performance_issues' => 0,
        'style_issues' => 0,
        'complexity' => 0,
        'maintainability' => 0
    ];
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    $phpFiles = [];
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $phpFiles[] = $file->getPathname();
        }
    }
    
    foreach ($phpFiles as $phpFile) {
        $qualityReport['files']++;
        
        // Ler o conteúdo do arquivo
        $content = file_get_contents($phpFile);
        
        // Verificar sintaxe
        $syntaxErrors = [];
        $returnVar = 0;
        $output = [];
        exec("php -l \"$phpFile\" 2>&1", $output, $returnVar);
        
        if ($returnVar !== 0) {
            $qualityReport['syntax_issues']++;
            $qualityReport['errors']++;
        }
        
        // Verificar padrões de segurança
        $securityPatterns = [
            '/\$_GET\[/i' => 'Possible XSS vulnerability',
            '/\$_POST\[/i' => 'Direct input access',
            '/eval\(/i' => 'Dangerous eval function',
            '/shell_exec\(/i' => 'Shell execution',
            '/exec\(/i' => 'Shell execution',
            '/system\(/i' => 'System call',
            '/passthru\(/i' => 'Passthru call'
        ];
        
        foreach ($securityPatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $qualityReport['security_issues']++;
            }
        }
        
        // Verificar padrões de performance
        $performancePatterns = [
            '/SELECT \*/i' => 'Non-explicit field selection',
            '/ORDER BY RAND\(\)/i' => 'Inefficient random sorting',
            '/preg_match\(\'\*\*/i' => 'Greedy regex pattern'
        ];
        
        foreach ($performancePatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $qualityReport['performance_issues']++;
            }
        }
        
        // Verificar estilo e complexidade
        $lines = explode("\n", $content);
        $lineCount = count($lines);
        $functionCount = preg_match_all('/function\s+[a-zA-Z_][a-zA-Z0-9_]*\s*\(/', $content);
        $classCount = preg_match_all('/class\s+[a-zA-Z_][a-zA-Z0-9_]*\s*/', $content);
        
        // Calcular complexidade ciclomática
        $complexityPatterns = [
            '/\bif\s*\(/',
            '/\belse\b/',
            '/\belseif\s*\(/',
            '/\bfor\s*\(/',
            '/\bwhile\s*\(/',
            '/\bforeach\s*\(/',
            '/\bswitch\s*\(/',
            '/\bcase\s+/',
            '/\bcatch\s*\(/',
            '/\b\|\||\&\&/'
        ];
        
        $complexity = 0;
        foreach ($complexityPatterns as $pattern) {
            $complexity += preg_match_all($pattern, $content);
        }
        
        $qualityReport['complexity'] += $complexity;
        
        // Verificar indentação e estilo
        $styleIssues = 0;
        foreach ($lines as $line) {
            // Verificar tabulações misturadas com espaços
            if (preg_match('/[\t ]+[\t][ ]+/', $line)) {
                $styleIssues++;
            }
            
            // Verificar linhas muito longas (> 120 caracteres)
            if (strlen($line) > 120) {
                $styleIssues++;
            }
        }
        
        $qualityReport['style_issues'] += $styleIssues;
    }
    
    // Calcular manutenibilidade (simplificada)
    $maintainability = 100 - (
        ($qualityReport['syntax_issues'] * 10) +
        ($qualityReport['security_issues'] * 5) +
        ($qualityReport['performance_issues'] * 3) +
        ($qualityReport['style_issues'] * 1)
    );
    
    $qualityReport['maintainability'] = max(0, min(100, $maintainability));
    
    return $qualityReport;
}

// Verificar qualidade do código PHP nos principais diretórios
$phpDirectories = [
    'api' => 'API Directory',
    'api/config' => 'Configuration Files',
    'api/models' => 'Model Files',
    'api/utils' => 'Utility Files'
];

$overallQuality = [
    'files' => 0,
    'errors' => 0,
    'warnings' => 0,
    'syntax_issues' => 0,
    'security_issues' => 0,
    'performance_issues' => 0,
    'style_issues' => 0,
    'complexity' => 0,
    'maintainability' => 0,
    'directories' => []
];

foreach ($phpDirectories as $directory => $description) {
    if (is_dir($directory)) {
        echo "✓ Analisando $description...\n";
        $quality = checkPhpQuality($directory);
        $overallQuality['directories'][$directory] = $quality;
        
        $overallQuality['files'] += $quality['files'];
        $overallQuality['errors'] += $quality['errors'];
        $overallQuality['warnings'] += $quality['warnings'];
        $overallQuality['syntax_issues'] += $quality['syntax_issues'];
        $overallQuality['security_issues'] += $quality['security_issues'];
        $overallQuality['performance_issues'] += $quality['performance_issues'];
        $overallQuality['style_issues'] += $quality['style_issues'];
        $overallQuality['complexity'] += $quality['complexity'];
    }
}

// Calcular média de manutenibilidade
$dirCount = count($overallQuality['directories']);
if ($dirCount > 0) {
    $overallQuality['maintainability'] = array_sum(array_column($overallQuality['directories'], 'maintainability')) / $dirCount;
}

echo "\n✓ Análise de qualidade do código PHP concluída\n\n";

// 2. Verificar qualidade do código JavaScript
echo "2. Verificando qualidade do código JavaScript...\n";

function checkJsQuality($directory) {
    $qualityReport = [
        'files' => 0,
        'errors' => 0,
        'warnings' => 0,
        'syntax_issues' => 0,
        'security_issues' => 0,
        'performance_issues' => 0,
        'style_issues' => 0,
        'complexity' => 0,
        'maintainability' => 0
    ];
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    $jsFiles = [];
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'js') {
            $jsFiles[] = $file->getPathname();
        }
    }
    
    foreach ($jsFiles as $jsFile) {
        $qualityReport['files']++;
        
        // Ler o conteúdo do arquivo
        $content = file_get_contents($jsFile);
        
        // Verificar padrões de segurança
        $securityPatterns = [
            '/eval\(/i' => 'Dangerous eval function',
            '/document\.write\(/i' => 'Unsafe document.write',
            '/innerHTML\s*=/i' => 'Potential XSS risk',
            '/outerHTML\s*=/i' => 'Potential XSS risk'
        ];
        
        foreach ($securityPatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $qualityReport['security_issues']++;
            }
        }
        
        // Verificar padrões de performance
        $performancePatterns = [
            '/\.innerHTML\s*\+=/i' => 'Inefficient DOM manipulation',
            '/for\s*\(\s*var\s+i\s*=.*?i\s*<\s*[a-zA-Z0-9_]+\s*\.\s*length/i' => 'Inefficient loop',
            '/setTimeout\(["\']/i' => 'String-based timeout',
            '/setInterval\(["\']/i' => 'String-based interval'
        ];
        
        foreach ($performancePatterns as $pattern => $description) {
            if (preg_match($pattern, $content)) {
                $qualityReport['performance_issues']++;
            }
        }
        
        // Verificar estilo e complexidade
        $lines = explode("\n", $content);
        $lineCount = count($lines);
        
        // Verificar indentação e estilo
        $styleIssues = 0;
        foreach ($lines as $line) {
            // Verificar tabulações misturadas com espaços
            if (preg_match('/[\t ]+[\t][ ]+/', $line)) {
                $styleIssues++;
            }
            
            // Verificar linhas muito longas (> 120 caracteres)
            if (strlen($line) > 120) {
                $styleIssues++;
            }
        }
        
        $qualityReport['style_issues'] += $styleIssues;
        
        // Calcular complexidade ciclomática
        $complexityPatterns = [
            '/\bif\s*\(/',
            '/\belse\b/',
            '/\bfor\s*\(/',
            '/\bwhile\s*\(/',
            '/\bforEach\s*\(/',
            '/\bswitch\s*\(/',
            '/\bcatch\s*\(/',
            '/\b\|\||\&\&/'
        ];
        
        $complexity = 0;
        foreach ($complexityPatterns as $pattern) {
            $complexity += preg_match_all($pattern, $content);
        }
        
        $qualityReport['complexity'] += $complexity;
    }
    
    // Calcular manutenibilidade (simplificada)
    $maintainability = 100 - (
        ($qualityReport['syntax_issues'] * 10) +
        ($qualityReport['security_issues'] * 5) +
        ($qualityReport['performance_issues'] * 3) +
        ($qualityReport['style_issues'] * 1)
    );
    
    $qualityReport['maintainability'] = max(0, min(100, $maintainability));
    
    return $qualityReport;
}

// Verificar qualidade do código JavaScript
$jsDirectories = [
    'assets/js' => 'JavaScript Files',
    'assets/js/components' => 'Component Files'
];

$overallJsQuality = [
    'files' => 0,
    'errors' => 0,
    'warnings' => 0,
    'syntax_issues' => 0,
    'security_issues' => 0,
    'performance_issues' => 0,
    'style_issues' => 0,
    'complexity' => 0,
    'maintainability' => 0,
    'directories' => []
];

foreach ($jsDirectories as $directory => $description) {
    if (is_dir($directory)) {
        echo "✓ Analisando $description...\n";
        $quality = checkJsQuality($directory);
        $overallJsQuality['directories'][$directory] = $quality;
        
        $overallJsQuality['files'] += $quality['files'];
        $overallJsQuality['errors'] += $quality['errors'];
        $overallJsQuality['warnings'] += $quality['warnings'];
        $overallJsQuality['syntax_issues'] += $quality['syntax_issues'];
        $overallJsQuality['security_issues'] += $quality['security_issues'];
        $overallJsQuality['performance_issues'] += $quality['performance_issues'];
        $overallJsQuality['style_issues'] += $quality['style_issues'];
        $overallJsQuality['complexity'] += $quality['complexity'];
    }
}

// Calcular média de manutenibilidade
$dirCount = count($overallJsQuality['directories']);
if ($dirCount > 0) {
    $overallJsQuality['maintainability'] = array_sum(array_column($overallJsQuality['directories'], 'maintainability')) / $dirCount;
}

echo "\n✓ Análise de qualidade do código JavaScript concluída\n\n";

// 3. Verificar qualidade do HTML/CSS
echo "3. Verificando qualidade do HTML/CSS...\n";

function checkHtmlCssQuality($directory) {
    $qualityReport = [
        'files' => 0,
        'errors' => 0,
        'warnings' => 0,
        'accessibility_issues' => 0,
        'semantic_issues' => 0,
        'style_issues' => 0,
        'responsive_issues' => 0,
        'maintainability' => 0
    ];
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    
    foreach ($iterator as $file) {
        if ($file->isFile() && ($file->getExtension() === 'html' || $file->getExtension() === 'htm')) {
            $qualityReport['files']++;
            
            // Ler o conteúdo do arquivo
            $content = file_get_contents($file->getPathname());
            
            // Verificar padrões de acessibilidade
            $accessibilityPatterns = [
                '/<img[^>]+src=.*?(?:alt=["\'][^"\']*["\'])?/i' => 'Possíveis imagens sem atributo alt',
                '/<a[^>]+href=.*?>(?:\s*|&nbsp;)<\/a>/i' => 'Links vazios',
                '/<a[^>]+>(?:<img[^>]+>)<\/a>/i' => 'Imagens sem texto alternativo em links'
            ];
            
            foreach ($accessibilityPatterns as $pattern => $description) {
                if (preg_match($pattern, $content)) {
                    $qualityReport['accessibility_issues']++;
                }
            }
            
            // Verificar padrões semânticos
            $semanticPatterns = [
                '/<div[^>]+class=.*?header.*?>/i' => 'Uso de div em vez de header semantic',
                '/<div[^>]+class=.*?nav.*?>/i' => 'Uso de div em vez de nav semantic',
                '/<div[^>]+class=.*?main.*?>/i' => 'Uso de div em vez de main semantic',
                '/<div[^>]+class=.*?footer.*?>/i' => 'Uso de div em vez de footer semantic'
            ];
            
            foreach ($semanticPatterns as $pattern => $description) {
                if (preg_match($pattern, $content)) {
                    $qualityReport['semantic_issues']++;
                }
            }
            
            // Verificar padrões de estilo
            $stylePatterns = [
                '/style=["\'][^"\']*["\']/i' => 'Estilos inline',
                '/font-size:.*?px/i' => 'Tamanhos fixos de fonte',
                '/width:.*?px/i' => 'Larguras fixas'
            ];
            
            foreach ($stylePatterns as $pattern => $description) {
                if (preg_match($pattern, $content)) {
                    $qualityReport['style_issues']++;
                }
            }
        }
    }
    
    // Calcular manutenibilidade (simplificada)
    $maintainability = 100 - (
        ($qualityReport['accessibility_issues'] * 3) +
        ($qualityReport['semantic_issues'] * 2) +
        ($qualityReport['style_issues'] * 1)
    );
    
    $qualityReport['maintainability'] = max(0, min(100, $maintainability));
    
    return $qualityReport;
}

// Verificar qualidade do HTML/CSS
$htmlDirectories = [
    '.' => 'HTML Files',
    'scraping_cccrj/cccrj_content' => 'Scraped HTML Content'
];

$overallHtmlQuality = [
    'files' => 0,
    'errors' => 0,
    'warnings' => 0,
    'accessibility_issues' => 0,
    'semantic_issues' => 0,
    'style_issues' => 0,
    'responsive_issues' => 0,
    'maintainability' => 0,
    'directories' => []
];

foreach ($htmlDirectories as $directory => $description) {
    if (is_dir($directory)) {
        echo "✓ Analisando $description...\n";
        $quality = checkHtmlCssQuality($directory);
        $overallHtmlQuality['directories'][$directory] = $quality;
        
        $overallHtmlQuality['files'] += $quality['files'];
        $overallHtmlQuality['accessibility_issues'] += $quality['accessibility_issues'];
        $overallHtmlQuality['semantic_issues'] += $quality['semantic_issues'];
        $overallHtmlQuality['style_issues'] += $quality['style_issues'];
    }
}

// Calcular média de manutenibilidade
$dirCount = count($overallHtmlQuality['directories']);
if ($dirCount > 0) {
    $overallHtmlQuality['maintainability'] = array_sum(array_column($overallHtmlQuality['directories'], 'maintainability')) / $dirCount;
}

echo "\n✓ Análise de qualidade do HTML/CSS concluída\n\n";

// 4. Gerar relatório de qualidade
echo "4. Gerando relatório de qualidade...\n";

$qualityReport = [
    'timestamp' => date('Y-m-d H:i:s'),
    'php_quality' => $overallQuality,
    'js_quality' => $overallJsQuality,
    'html_css_quality' => $overallHtmlQuality,
    'overall_score' => 0
];

// Calcular pontuação geral
$weights = [
    'php_quality' => 40,
    'js_quality' => 35,
    'html_css_quality' => 25
];

$overallScore = (
    ($overallQuality['maintainability'] * $weights['php_quality'] / 100) +
    ($overallJsQuality['maintainability'] * $weights['js_quality'] / 100) +
    ($overallHtmlQuality['maintainability'] * $weights['html_css_quality'] / 100)
);

$qualityReport['overall_score'] = $overallScore;

// Salvar relatório em arquivo JSON
$reportsDir = 'reports';
if (!is_dir($reportsDir)) {
    mkdir($reportsDir, 0755, true);
}

$timestamp = date('Y-m-d-H-i-s');
$reportFile = "$reportsDir/code_quality_report_$timestamp.json";
file_put_contents($reportFile, json_encode($qualityReport, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✓ Relatório de qualidade salvo em: $reportFile\n\n";

// 5. Gerar relatório em formato legível
echo "5. Gerando relatório em formato legível...\n";

$readableReport = "RELATÓRIO DE QUALIDADE DE CÓDIGO\n";
$readableReport .= "================================\n\n";
$readableReport .= "Data da geração: " . $qualityReport['timestamp'] . "\n\n";

$readableReport .= "PONTUAÇÃO GERAL\n";
$readableReport .= "-------------\n";
$readableReport .= "Pontuação: " . number_format($qualityReport['overall_score'], 2) . "/100\n\n";

if ($qualityReport['overall_score'] >= 90) {
    $readableReport .= "Classificação: Excelente\n";
} elseif ($qualityReport['overall_score'] >= 80) {
    $readableReport .= "Classificação: Boa\n";
} elseif ($qualityReport['overall_score'] >= 70) {
    $readableReport .= "Classificação: Regular\n";
} else {
    $readableReport .= "Classificação: Precisa de melhorias\n";
}
$readableReport .= "\n";

$readableReport .= "QUALIDADE DO CÓDIGO PHP\n";
$readableReport .= "---------------------\n";
$readableReport .= "Arquivos analisados: " . $overallQuality['files'] . "\n";
$readableReport .= "Erros de sintaxe: " . $overallQuality['syntax_issues'] . "\n";
$readableReport .= "Problemas de segurança: " . $overallQuality['security_issues'] . "\n";
$readableReport .= "Problemas de performance: " . $overallQuality['performance_issues'] . "\n";
$readableReport .= "Problemas de estilo: " . $overallQuality['style_issues'] . "\n";
$readableReport .= "Complexidade: " . $overallQuality['complexity'] . "\n";
$readableReport .= "Manutenibilidade: " . number_format($overallQuality['maintainability'], 2) . "/100\n\n";

foreach ($overallQuality['directories'] as $dir => $quality) {
    $readableReport .= "$dir: " . number_format($quality['maintainability'], 2) . "/100 (" . $quality['files'] . " arquivos)\n";
}
$readableReport .= "\n";

$readableReport .= "QUALIDADE DO CÓDIGO JAVASCRIPT\n";
$readableReport .= "----------------------------\n";
$readableReport .= "Arquivos analisados: " . $overallJsQuality['files'] . "\n";
$readableReport .= "Problemas de segurança: " . $overallJsQuality['security_issues'] . "\n";
$readableReport .= "Problemas de performance: " . $overallJsQuality['performance_issues'] . "\n";
$readableReport .= "Problemas de estilo: " . $overallJsQuality['style_issues'] . "\n";
$readableReport .= "Complexidade: " . $overallJsQuality['complexity'] . "\n";
$readableReport .= "Manutenibilidade: " . number_format($overallJsQuality['maintainability'], 2) . "/100\n\n";

foreach ($overallJsQuality['directories'] as $dir => $quality) {
    $readableReport .= "$dir: " . number_format($quality['maintainability'], 2) . "/100 (" . $quality['files'] . " arquivos)\n";
}
$readableReport .= "\n";

$readableReport .= "QUALIDADE DO HTML/CSS\n";
$readableReport .= "-------------------\n";
$readableReport .= "Arquivos analisados: " . $overallHtmlQuality['files'] . "\n";
$readableReport .= "Problemas de acessibilidade: " . $overallHtmlQuality['accessibility_issues'] . "\n";
$readableReport .= "Problemas semânticos: " . $overallHtmlQuality['semantic_issues'] . "\n";
$readableReport .= "Problemas de estilo: " . $overallHtmlQuality['style_issues'] . "\n";
$readableReport .= "Manutenibilidade: " . number_format($overallHtmlQuality['maintainability'], 2) . "/100\n\n";

foreach ($overallHtmlQuality['directories'] as $dir => $quality) {
    $readableReport .= "$dir: " . number_format($quality['maintainability'], 2) . "/100 (" . $quality['files'] . " arquivos)\n";
}
$readableReport .= "\n";

$readableReport .= "RECOMENDAÇÕES\n";
$readableReport .= "------------\n";

if ($qualityReport['overall_score'] < 80) {
    $readableReport .= "1. Revise os problemas de segurança identificados\n";
    $readableReport .= "2. Otimize problemas de performance\n";
    $readableReport .= "3. Aprimore a manutenibilidade do código\n";
    $readableReport .= "4. Padrões de estilo inconsistentes\n";
} else {
    $readableReport .= "1. O código está em bom estado geral\n";
    $readableReport .= "2. Continue mantendo boas práticas\n";
    $readableReport .= "3. Realize revisões periódicas\n";
}

$readableReportFile = "$reportsDir/code_quality_report_$timestamp.txt";
file_put_contents($readableReportFile, $readableReport);

echo "✓ Relatório legível salvo em: $readableReportFile\n\n";

echo "✅ Relatório de qualidade de código gerado com sucesso!\n";
echo "\nRelatórios gerados:\n";
echo "- $reportFile (JSON)\n";
echo "- $readableReportFile (Texto)\n";
echo "\nResultados resumidos:\n";
echo "- Pontuação geral: " . number_format($qualityReport['overall_score'], 2) . "/100\n";
echo "- PHP Manutenibilidade: " . number_format($overallQuality['maintainability'], 2) . "/100\n";
echo "- JavaScript Manutenibilidade: " . number_format($overallJsQuality['maintainability'], 2) . "/100\n";
echo "- HTML/CSS Manutenibilidade: " . number_format($overallHtmlQuality['maintainability'], 2) . "/100\n";

?>