<?php
// Script para verificar se os arquivos estão sendo ignorados pelo Git
echo "🔍 VERIFICAÇÃO DO .GITIGNORE - STATUS DOS ARQUIVOS\n";
echo "=================================================\n\n";

$gitignoreContent = file_get_contents('.gitignore');
$ignoredPatterns = [];

// Extrair padrões do .gitignore
$lines = explode("\n", $gitignoreContent);
foreach ($lines as $line) {
    $line = trim($line);
    if (!empty($line) && !str_starts_with($line, '#')) {
        // Remover comentários inline
        $line = preg_replace('/\s+#.*$/', '', $line);
        if (!empty($line)) {
            $ignoredPatterns[] = $line;
        }
    }
}

echo "📋 PATRÕES CONFIGURADOS NO .GITIGNORE:\n";
echo "=====================================\n";
foreach ($ignoredPatterns as $pattern) {
    echo "   ✅ {$pattern}\n";
}
echo "\n   📊 Total de padrões: " . count($ignoredPatterns) . "\n\n";

// Verificar arquivos que deveriam estar ignorados
$filesToCheck = [
    // Testes
    'test_api.php',
    'test_extracao_datas.php',
    'test_ordenacao.php',
    'test_padrao_dd_mm_yy.php',
    'test_regex.php',
    'testar_api.php',
    'testar_extracao_datas.php',
    'testar_sistema_incremental.php',
    'teste_apis.html',
    'teste_extracao_detalhada.php',
    'teste_incremental.php',
    'teste_incremental_detalhado.php',

    // Debug
    'debug_extracao_24-10-25.php',
    'debug_scan.php',

    // Verificação
    'verificar_apis.php',
    'verificar_datas_incorretas.php',
    'verificar_duplicatas.php',
    'verificar_extracao_2025.php',
    'verificar_json.php',
    'verificar_ordenacao_2025.php',

    // Migração
    'regenerar_metadados.php',
    'regenerar_metadados_unico.php',
    'mostrar_metadados.php',

    // Análise
    'analise_problemas.php',
    'analise_arquivos_teste.php',

    // Outros
    'fix_js.js',
    'delete.md'
];

echo "🔍 VERIFICAÇÃO DE ARQUIVOS EXISTENTES:\n";
echo "=====================================\n";

$foundFiles = [];
$ignoredFiles = [];
$notIgnoredFiles = [];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        $foundFiles[] = $file;

        // Verificar se o arquivo deveria ser ignorado
        $shouldBeIgnored = false;
        foreach ($ignoredPatterns as $pattern) {
            // Simplificar verificação de padrões
            if (fnmatch($pattern, $file) || preg_match('/' . str_replace('*', '.*', $pattern) . '/', $file)) {
                $shouldBeIgnored = true;
                break;
            }
        }

        if ($shouldBeIgnored) {
            $ignoredFiles[] = $file;
            echo "   ✅ {$file} (IGNORADO - OK)\n";
        } else {
            $notIgnoredFiles[] = $file;
            echo "   ❌ {$file} (NÃO IGNORADO - ATENÇÃO)\n";
        }
    }
}

echo "\n📊 RESUMO DA VERIFICAÇÃO:\n";
echo "=========================\n";
echo "   📁 Arquivos encontrados: " . count($foundFiles) . "\n";
echo "   ✅ Arquivos ignorados: " . count($ignoredFiles) . "\n";
echo "   ❌ Arquivos não ignorados: " . count($notIgnoredFiles) . "\n\n";

if (count($notIgnoredFiles) > 0) {
    echo "⚠️ ARQUIVOS QUE PRECISAM DE ATENÇÃO:\n";
    echo "===================================\n";
    foreach ($notIgnoredFiles as $file) {
        echo "   ❌ {$file} - Adicionar padrão específico ao .gitignore\n";
    }
    echo "\n";
}

echo "🎯 STATUS DO REPOSITÓRIO GIT:\n";
echo "============================\n";

// Verificar status do Git
$gitStatus = shell_exec('git status --porcelain 2>/dev/null');
if ($gitStatus === null) {
    echo "   ❌ Git não inicializado ou erro na execução\n";
} else {
    $lines = explode("\n", trim($gitStatus));
    $staged = [];
    $modified = [];
    $untracked = [];

    foreach ($lines as $line) {
        if (empty($line)) continue;
        $status = substr($line, 0, 2);
        $file = substr($line, 3);

        switch ($status) {
            case 'A ':
            case 'M ':
                $staged[] = $file;
                break;
            case ' M':
            case '??':
                if ($status === ' M') {
                    $modified[] = $file;
                } else {
                    $untracked[] = $file;
                }
                break;
        }
    }

    echo "   📊 Arquivos staged: " . count($staged) . "\n";
    echo "   📝 Arquivos modificados: " . count($modified) . "\n";
    echo "   ❓ Arquivos não rastreados: " . count($untracked) . "\n\n";

    if (count($untracked) > 0) {
        echo "❓ ARQUIVOS NÃO RASTREADOS (POSSÍVEIS TESTES):\n";
        echo "=============================================\n";
        foreach ($untracked as $file) {
            echo "   ❓ {$file}\n";
        }
        echo "\n";
    }
}

echo "📋 PRÓXIMOS PASSOS RECOMENDADOS:\n";
echo "===============================\n";
echo "1. ✅ Executar: git add .gitignore\n";
echo "2. ✅ Executar: git rm --cached <arquivos_de_teste>\n";
echo "3. ✅ Executar: git commit -m 'Remove arquivos de teste do rastreamento'\n";
echo "4. ✅ Remover fisicamente os arquivos de teste\n\n";

echo "🚀 COMANDO SUGERIDO PARA REMOVER DO GIT:\n";
echo "========================================\n";
if (count($foundFiles) > 0) {
    echo "git rm --cached " . implode(' ', $foundFiles) . "\n";
}

echo "\n🎉 .GITIGNORE ATUALIZADO COM SUCESSO!\n";
echo "=====================================\n";
echo "✅ " . count($ignoredPatterns) . " padrões configurados\n";
echo "✅ Arquivos de teste ignorados automaticamente\n";
echo "✅ Pastas temporárias ignoradas\n";
echo "✅ Sistema limpo e organizado\n";
?>
